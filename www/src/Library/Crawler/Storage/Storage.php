<?php

namespace App\Library\Crawler\Storage;

/**
 * Interface Storage
 *
 * @package App\Library\Crawler\Storage
 */
interface Storage
{
    /**
     * @param string $url
     * @param int $depth
     * @param bool $isVisited
     */
    public function addDetectedUrl(string $url, int $depth, bool $isVisited = false): void;

    /**
     * @param string $url
     * @return bool
     */
    public function hasDetectedUrl(string $url): bool;

    /**
     * @param string $url
     * @return bool
     */
    public function hasVisitedUrl(string $url): bool;

    /**
     * @param string $url
     */
    public function setVisitedUrl(string $url): void;
}
