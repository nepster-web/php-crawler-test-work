<?php

namespace App\Library\Crawler;

use DOMElement;
use DOMDocument;
use LogicException;
use App\Library\Crawler\Entity\Link;
use App\Library\Crawler\Storage\Storage;
use App\Library\Crawler\Helper\UrlHelper;
use App\Library\Crawler\Helper\HttpHelper;

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
 * $crawler->on(Crawler::EVENT_BEFORE_HIT_CRAWL, function(Link $link): void {
 *   echo 'before url parse';
 * });
 *
 * $crawler->on(Crawler::EVENT_HIT_CRAWL, function(Link $link, DOMDocument $dom): void {
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
 * @package App\Library\Crawler
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
     * Registered events
     *
     * @var array
     */
    protected $events = [];

    /**
     * Start page for parsing
     *
     * @var string
     */
    protected $startUrl;

    /**
     * Max depth for stopping this script
     *
     * @var int
     */
    protected $maxDepth = 5;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Crawler constructor.
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Starts parsing process
     *
     * @param string $url
     * @param int|null $maxDepth
     */
    public function crawl(string $url, ?int $maxDepth = null): void
    {
        $this->callEvent(self::EVENT_BEFORE_CRAWL, [$url]);

        $this->startUrl = $url;
        $this->maxDepth = $maxDepth ?: $this->maxDepth;

        $this->process($this->startUrl);

        $this->callEvent(self::EVENT_AFTER_CRAWL, [$url]);
    }

    /**
     * List of supported events
     *
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
     * Recursively get around web pages
     *
     * @param string $url
     * @param int $depth
     */
    protected function process(string $url, int $depth = 1): void
    {
        if ($depth > $this->maxDepth) {
            return;
        }

        $link = $this->storage->findByUrl($url);
        if (is_null($link) === false && $link->isVisited()) {
            return;
        }

        if (HttpHelper::isAvailablePage($url) === false) {
            return;
        }

        if (is_null($link)) {
            $link = new Link($url, $depth);
        } else {
            $depth = $link->getDepth();
        }

        $this->callEvent(self::EVENT_BEFORE_HIT_CRAWL, [$link]);

        $urlDomDocument = $this->parseLink($url);

        $link->visited();

        $this->callEvent(self::EVENT_HIT_CRAWL, [$link, $urlDomDocument]);

        ++$depth;

        if ($depth > $this->maxDepth) {
            return;
        }

        $hrefList = $this->parseAttributeTagValues($urlDomDocument, 'a', 'href');

        $nextDepthUrlList = $this->convertHrefListToUrlListForParentUrl($url, $hrefList);

        array_map(function (string $url) use ($depth) {
            if (
                is_null($this->storage->findByUrl($url)) &&
                is_null($this->storage->findByUrl(rtrim($url, '/')))
            ) {
                $link = new Link($url, $depth);
                $link->detected();
                $this->storage->add($link);
            }
        }, $nextDepthUrlList);

        foreach ($nextDepthUrlList as $nextDepthUrl) {
            $this->process($nextDepthUrl, $depth);
        }
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function isStartUrl(string $url): bool
    {
        return (rtrim($url, '/') . '/' === rtrim($this->startUrl, '/') . '/');
    }

    /**
     * @param string $url
     * @param array $hrefList
     * @return array
     */
    protected function convertHrefListToUrlListForParentUrl(string $url, array $hrefList): array
    {
        $urlList = [];

        foreach ($hrefList as $href) {
            $urlFromHref = UrlHelper::buildUrl($href, $url);
            if (
                is_null($urlFromHref) === false &&
                $this->isStartUrl($urlFromHref) === false &&
                UrlHelper::getDomain($urlFromHref) === UrlHelper::getDomain($this->startUrl) &&
                in_array($urlFromHref, $urlList) === false
            ) {
                $urlFromHref = strtok($urlFromHref, '#');
                array_push($urlList, $urlFromHref);
            }
        }

        foreach ($urlList as $i => $url) {
            if (substr($url, -1, 1) === '/' && in_array(rtrim($url, '/'), $urlList)) {
                unset($urlList[$i]);
            }
        }

        return array_values(array_unique(array_filter($urlList)));
    }

    /**
     * @param string $url
     * @return DOMDocument|null
     */
    protected function parseLink(string $url): ?DOMDocument
    {
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);

        if (
            is_object($dom) &&
            isset($dom->documentURI) &&
            empty($dom->documentURI) == false
        ) {
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
    protected function parseAttributeTagValues(DOMDocument $dom, string $tag, string $attribute): array
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

    /**
     * @param string $event
     * @param array $params
     */
    protected function callEvent(string $event, array $params): void
    {
        if (isset($this->events[$event]) && is_callable($this->events[$event])) {
            call_user_func_array($this->events[$event], $params);
        }
    }
}
