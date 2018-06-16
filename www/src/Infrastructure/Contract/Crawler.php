<?php

namespace App\Infrastructure\Contract;

/**
 * Interface Crawler
 *
 * @package App\Infrastructure\Contract
 */
interface Crawler
{
    /**
     * Process starting
     *
     * @param string $url
     * @param int $depth
     */
    public function crawl(string $url, int $depth = 5): void;

}
