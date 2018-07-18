<?php

namespace Tests\Library\Crawler\_helper\Mock;

use DOMDocument;

/**
 * Class CrawlerMock
 *
 * @package Tests\Library\Crawler\_helper\Mock
 */
class CrawlerMock extends \App\Library\Crawler\Crawler
{
    /**
     * {@inheritdoc}
     */
    protected function process(string $url, int $depth = 1): void
    {
        $this->callEvent(self::EVENT_BEFORE_HIT_CRAWL, [$url, $depth]);

        $urlDomDocument = $dom = new DOMDocument('1.0', 'utf-8');

        $this->callEvent(self::EVENT_HIT_CRAWL, [$url, $depth, $urlDomDocument]);
    }

}
