<?php

namespace App\Library;

use DOMXPath;
use DOMElement;
use DOMDocument;
use LogicException;

/**
 * Crawler
 *
 * Note:
 * Uses the DOMDocument extension.
 *
 * Example:
 *
 * $crawler = new Crawler();
 *
 * $crawler->on(Crawler::EVENT_BEFORE_HIT_CRAWL, function(string $href, string $depth): void {
 *   echo 'before url parse';
 * });
 *
 * $crawler->on(Crawler::EVENT_HIT_CRAWL, function(string $href, string $depth, DOMDocument $dom): void {
 *   echo 'after url parse';
 * });
 *
 * $crawler->on(Crawler::EVENT_BEFORE_CRAWL, function(string $href): void {
 *   echo 'before crawl';
 * });
 *
 * $crawler->on(Crawler::EVENT_AFTER_CRAWL, function(string $href): void {
 *   echo 'after crawl';
 * });
 *
 * $crawler->crawl('http://example.com', 5);
 *
 * @package App\Library
 */
class Crawler
{
    /**
     * Events
     *
     * - event before hit crawl
     * - event hit crawl
     * - event before crawl
     * - event after crawl
     */
    const EVENT_BEFORE_HIT_CRAWL = 'event_before_hit_crawl';
    const EVENT_HIT_CRAWL = 'event_hit_crawl';
    const EVENT_BEFORE_CRAWL = 'event_before_crawl';
    const EVENT_AFTER_CRAWL = 'event_after_crawl';

    /**
     * Detected pages for next depth
     *
     * @var array
     */
    private $detectedUrls = [];

    /**
     * Viewed urls
     *
     * @var array
     */
    private $visitedUrls = [];

    /**
     * Registered events
     *
     * @var array
     */
    private $events = [];

    /**
     * Start page for parsing
     *
     * @var string
     */
    private $startUrl;

    /**
     * Max depth for stopping this script
     *
     * @var int
     */
    private $maxDepth = 5;

    /**
     * Starts parsing process
     *
     * @param string $url
     * @param int $maxDepth
     */
    public function crawl(string $url, int $maxDepth = 5): void
    {
        if (isset($this->events[self::EVENT_BEFORE_CRAWL]) && is_callable($this->events[self::EVENT_BEFORE_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_BEFORE_CRAWL], [$url]);
        }

        $this->startUrl = rtrim($url, '/') . '/';
        $this->maxDepth = $maxDepth;
        $this->process($this->startUrl);


        print_r($this->visitedUrls);


        if (isset($this->events[self::EVENT_AFTER_CRAWL]) && is_callable($this->events[self::EVENT_AFTER_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_AFTER_CRAWL], [$url]);
        }
    }

    /**
     * @return array
     */
    public function getVisitedUrls(): array
    {
        return $this->visitedUrls;
    }

    /**
     * @param string $event
     * @param callable $cb
     */
    public function on(string $event, callable $cb): void
    {
        switch ($event) {
            case self::EVENT_BEFORE_HIT_CRAWL:
            case self::EVENT_HIT_CRAWL:
            case self::EVENT_BEFORE_CRAWL:
            case self::EVENT_AFTER_CRAWL:
                // supported events
                break;
            default:
                throw new LogicException('Event "' . $event . '" does\'t not support.');
        }

        $this->events[$event] = $cb;
    }

    /**
     * @param string $href
     * @param string $currentUrl
     * @return null|string
     */
    public function buildUrl(string $href, string $currentUrl): ?string
    {
        if (substr($href, 0, 2) === '//') {
            return null;
        }

        $currentUrl = rtrim($currentUrl, '/');
        $parseCurrentUrl = parse_url($currentUrl);
        $domainURL = $parseCurrentUrl['scheme'] . '://' . $parseCurrentUrl['host'] . '/';
        $parts = parse_url($href);

        if (!is_array($parts)) {
            return null;
        }

        $isExternal = false;

        if (isset($parts['scheme']) && (
                $parts['scheme'] == 'mailto' ||
                $parts['scheme'] == 'skype' ||
                $parts['scheme'] == 'javascript'
            )
        ) {
            return null;
        }

        if (isset($parts['host'])) {
            $isExternal = true;
        }

        if (isset($parts['path'])) {
            if (!empty($parts['path']) && $parts['path'][0] === '/') {
                if (mb_strlen($parts['path']) > 1) {
                    $parts['path'] = substr($parts['path'], 1);
                } else {
                    return $domainURL;
                }
            } else {
                if (substr($parts['path'], 0, 2) === './') {
                    $parts['path'] = substr($parts['path'], 2);
                } else {
                    if (substr($parts['path'], 0, 3) === '../') {
                        $explodeParseCurrentUrl = explode('/', ltrim($parseCurrentUrl['path'], '/'));
                        $explodeParseCurrentUrl = array_filter(array_reverse($explodeParseCurrentUrl));
                        $countS = substr_count($parts['path'], '../');
                        array_splice($explodeParseCurrentUrl, 0, $countS);

                        return $domainURL . implode(
                                '/',
                                array_reverse($explodeParseCurrentUrl)
                            ) . '/' . str_replace('../', '', $parts['path']);
                    }
                }
            }
        }

        if ($isExternal === false) {
            if (isset($parts['host']) === false) {
                return $domainURL . $this->unparseUrl($parts);
            }

            return $currentUrl . '/' . $this->unparseUrl($parts);
        }

        if (isset($parts['host'])) {
            $parts['host'] = rtrim($parts['host'], '/') . '/';
        }

        return $this->unparseUrl($parts);
    }

    /**
     * @param array $parsedUrl
     * @return string
     */
    public function unparseUrl(array $parsedUrl): string
    {
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * @param string $url
     * @return null|string
     */
    public function getDomain(string $url): ?string
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        return null;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isAvailablePage(string $url): bool
    {
        $headers = @get_headers($url, 1);
        if ($headers && is_array($headers)) {

            if (isset($headers[0])) {
                if (trim(substr($headers[0], -6)) !== '200 OK') {
                    return false;
                }
            }

            if (isset($headers["Content-Type"])) {
                if (stristr($headers["Content-Type"], 'text/html') === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * TODO: отрабатывает некорректно на 3+ вложенности.
     *
     * TODO: возможно отработка и парсинг корректны.
     * TODO: Однако, когда мы переходим на 2 url, все ссылки на нем,
     * TODO: будут уже текущей вложенности.
     *
     * TODO: исходя из этого необходимо перебрать алгоритм паринга
     * TODO: вначале парсить все ссылки текущей страницы, а только потом
     * TODO: переходить дальше.
     *
     *
     * The solution for recursive site crawling.
     * http://stackoverflow.com/a/2313270
     *
     * Note:
     * The solution is good, but the definition of URL is inaccurate.
     * This function does't consider many of href variations.
     *
     * @param string $url
     * @param int $depth
     *//*
    private function process(string $url, int $depth = 1): void
    {
        if (isset($this->visitedUrls[$url]) || $depth > $this->maxDepth) {
            return;
        }

        if ($this->isCorrectPage($url) === false || $this->getDomain($url) !== $this->getDomain($this->startUrl)) {
            return;
        }

        if (isset($this->events[self::EVENT_BEFORE_HIT_CRAWL]) && is_callable($this->events[self::EVENT_BEFORE_HIT_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_BEFORE_HIT_CRAWL], [$url, $depth]);
        }

        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);

        $xpath = new DOMXPath($dom);
        $body = $xpath->query('/html/body');
        $pageHash = md5($dom->saveXml($body->item(0)));

        if (in_array($pageHash, $this->visitedUrls)) {
            return;
        }

        $this->visitedUrls[$url] = $pageHash;

        if (isset($this->events[self::EVENT_HIT_CRAWL]) && is_callable($this->events[self::EVENT_HIT_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_HIT_CRAWL], [$url, $depth, $dom]);
        }

        $anchors = $dom->getElementsByTagName('a');

        /** @var DOMElement $element *//*
        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            $href = $this->buildUrl($href, $url);
            if ($href) {
                $this->process($href, $depth + 1);
            }
        }
    }*/







    private function process(string $url, int $depth = 1): void
    {
        if (isset($this->visitedUrls[$url]) || $depth > $this->maxDepth) {
            return;
        }

        if ($this->isAvailablePage($url) === false) {
            return;
        }

        if ($this->hasDetectedUrl($url) === false) {
            $this->expandDetectedUrlList($depth, [$url => null]);
        }

        if (isset($this->events[self::EVENT_BEFORE_HIT_CRAWL]) && is_callable($this->events[self::EVENT_BEFORE_HIT_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_BEFORE_HIT_CRAWL], [$url, $depth]);
        }

        $linkDom = $this->parseLink($url);
        $hrefList = $this->parseAttributeTagValues($linkDom, 'a', 'href');

        $urlList = $this->convertHrefListToUrlListForParentUrl($url, $hrefList);

        $linkHash = $this->generateUniqueHashByDOMDocument($linkDom);


        $this->setVisitedUrl($url, $linkHash);

        if (isset($this->events[self::EVENT_HIT_CRAWL]) && is_callable($this->events[self::EVENT_HIT_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_HIT_CRAWL], [$url, $depth, $linkDom]);
        }

        $this->expandDetectedUrlList($depth + 1, $urlList);


        // TODO: избавиться от якоря, но только, если такая ссылка уже (без якоря) уже есть.

        print_r($this->detectedUrls);
        die();


        foreach ($urlList as $url) {
            $this->process($url, $depth);
        }
    }















    /**
     * @param int $depth
     * @param array $urlList
     */
    private function expandDetectedUrlList(int $depth, array $urlList): void
    {
        $result = [];
        foreach ($urlList as $url => $visitHash) {
            if (is_string($url) === false) {
                list($visitHash, $url) = [null, $visitHash];
            }
            if ($this->hasDetectedUrl($url) === false) {
                $result[$url] = $visitHash;
            }
        }

        if (isset($this->detectedUrls[$depth]) === false) {
            $this->detectedUrls[$depth] = [];
        }

        $this->detectedUrls[$depth] = array_merge($this->detectedUrls[$depth], $result);
    }

    /**
     * @param string $url
     * @return array|null
     */
    private function getDetectedUrl(string $url): ?array
    {
        foreach ($this->detectedUrls as $depth => $detectedUrls) {
            if (isset($detectedUrls[$url])) {
                return [$url => $detectedUrls[$url]];
            }
        }

        return null;
    }

    /**
     * @param string $url
     * @return bool
     */
    private function hasDetectedUrl(string $url): bool
    {
        return $this->getDetectedUrl($url) ? true : false;
    }

    /**
     * @param string $url
     * @return bool
     */
    private function hasVisitedUrl(string $url): bool
    {
        if ($detectedUrl = $this->getDetectedUrl($url)) {
            $key = key($detectedUrl);
            return !empty($detectedUrl[$key]);
        }

        return false;
    }

    /**
     * @param string $url
     * @param string $hash
     */
    private function setVisitedUrl(string $url, string $hash): void
    {
        foreach ($this->detectedUrls as $depth => &$detectedUrls) {
            if (array_key_exists($url, $detectedUrls)) {
                $detectedUrls[$url] = $hash;
                break;
            }
        }
    }























    /**
     * @param string $url
     * @return bool
     */
    private function isStartUrl(string $url): bool
    {
        $url = rtrim($url, '/') . '/';

        return ($url === $this->startUrl);
    }


    /**
     * @param DOMDocument $dom
     * @return string
     */
    private function generateUniqueHashByDOMDocument(DOMDocument $dom): string
    {
        $xpath = new DOMXPath($dom);
        $body = $xpath->query('/html/body');

        return md5($dom->saveXml($body->item(0)));
    }

    /**
     * @param string $url
     * @param array $hrefList
     * @return array
     */
    private function convertHrefListToUrlListForParentUrl(string $url, array $hrefList): array
    {
        $urlList = [];
        foreach ($hrefList as $href) {
            $url = $this->buildUrl($href, $url);
            if (
                is_null($url) === false &&
                $this->isStartUrl($url) === false &&
                $this->getDomain($url) === $this->getDomain($this->startUrl) &&
                in_array($url, $urlList) === false
            ) {
                array_push($urlList, $url);
            }
        }

        return array_values($urlList);
    }

    /**
     * @param string $url
     * @return DOMDocument|null
     */
    private function parseLink(string $url): ?DOMDocument
    {
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);

        if (is_object($dom) && isset($dom->documentURI) && empty($dom->documentURI) == false) {
            return $dom;
        }

        return null;
    }

    /**
     * @param DOMDocument $dom
     * @param string $tag
     * @param string $attribute
     * @return array
     */
    private function parseAttributeTagValues(DOMDocument $dom, string $tag, string $attribute): array
    {
        $anchors = $dom->getElementsByTagName($tag);
        $attributeValues = [];

        /** @var DOMElement $element * */
        foreach ($anchors as $element) {
            $value = $element->getAttribute($attribute);
            array_push($attributeValues, $value);
        }

        return $attributeValues;
    }
}