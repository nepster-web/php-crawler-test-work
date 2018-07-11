<?php

namespace App\Infrastructure\Factory;

use App\Library\Crawler\Crawler;
use App\Library\Crawler\Storage\ArrayStorage;

/**
 * Class CrawlerFactory
 *
 * @package App\Infrastructure\Factory
 */
class CrawlerFactory
{
    /**
     * @return Crawler
     */
    public function __invoke(): Crawler
    {
        return (new Crawler(new ArrayStorage()));
    }
}
