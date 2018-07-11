<?php

namespace App\Library\Crawler;

use DOMElement;
use DOMDocument;
use LogicException;
use App\Library\Crawler\Storage\Storage;
use App\Library\Crawler\Helper\UrlHelper;
use App\Library\Crawler\Helper\HttpHelper;

/**
 * Crawler
 *
 * TODO: решить вопрос с якорями (#)
 * TODO: дописать тесты
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
     * @var Storage
     */
    private $storage;

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
        if (isset($this->events[self::EVENT_BEFORE_CRAWL]) && is_callable($this->events[self::EVENT_BEFORE_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_BEFORE_CRAWL], [$url]);
        }

        $this->startUrl = $url;
        $this->maxDepth = $maxDepth ?: $this->maxDepth;
        $this->process($this->startUrl);

        if (isset($this->events[self::EVENT_AFTER_CRAWL]) && is_callable($this->events[self::EVENT_AFTER_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_AFTER_CRAWL], [$url]);
        }
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
    private function process(string $url, int $depth = 1): void
    {
        if ($this->storage->hasVisitedUrl($url) || $depth > $this->maxDepth || HttpHelper::isAvailablePage($url) === false) {
            return;
        }

        if (isset($this->events[self::EVENT_BEFORE_HIT_CRAWL]) && is_callable($this->events[self::EVENT_BEFORE_HIT_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_BEFORE_HIT_CRAWL], [$url, $depth]);
        }

        $urlDomDocument = $this->parseLink($url);

        if (isset($this->events[self::EVENT_HIT_CRAWL]) && is_callable($this->events[self::EVENT_HIT_CRAWL])) {
            call_user_func_array($this->events[self::EVENT_HIT_CRAWL], [$url, $depth, $urlDomDocument]);
        }

        if ($this->storage->hasDetectedUrl($url)) {
            $this->storage->setVisitedUrl($url);
        } else {
            ++$depth;
            $this->storage->addDetectedUrl($url, $depth, true);
        }

        $hrefList = $this->parseAttributeTagValues($urlDomDocument, 'a', 'href');

        $nextDepthUrlList = $this->convertHrefListToUrlListForParentUrl($url, $hrefList);

        array_map(function (string $url) use ($depth) {
            if ($this->storage->hasDetectedUrl($url) === false) {
                $this->storage->addDetectedUrl($url, $depth);
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
    private function isStartUrl(string $url): bool
    {
        return (rtrim($url, '/') . '/' === rtrim($this->startUrl, '/') . '/');
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
            $urlFromHref = UrlHelper::buildUrl($href, $url);
            if (
                is_null($urlFromHref) === false &&
                $this->isStartUrl($urlFromHref) === false &&
                UrlHelper::getDomain($urlFromHref) === UrlHelper::getDomain($this->startUrl) &&
                in_array($urlFromHref, $urlList) === false
            ) {
                array_push($urlList, $urlFromHref);
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
