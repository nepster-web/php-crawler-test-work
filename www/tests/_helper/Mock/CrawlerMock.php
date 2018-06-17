<?php

namespace Tests\_helper\Mock;

/**
 * Class CrawlerMock
 *
 * @package Tests\_helper\Mock
 */
class CrawlerMock implements \App\Infrastructure\Contract\Crawler
{
    /**
     * @inheritdoc
     */
    public function crawl(string $url, int $depth = 5): void
    {

    }

}
