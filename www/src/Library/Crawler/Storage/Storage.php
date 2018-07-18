<?php

namespace App\Library\Crawler\Storage;

use App\Library\Crawler\Entity\Link;

/**
 * Interface Storage
 *
 * @package App\Library\Crawler\Storage
 */
interface Storage
{
    /**
     * @param string $url
     * @return Link|null
     */
    public function findByUrl(string $url): ?Link;

    /**
     * @param Link $link
     */
    public function save(Link $link): void;

    /**
     * @param Link $link
     */
    public function add(Link $link): void;

}
