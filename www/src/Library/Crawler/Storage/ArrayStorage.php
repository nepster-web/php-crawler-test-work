<?php

namespace App\Library\Crawler\Storage;

use LogicException;
use App\Library\Crawler\Entity\Link;

/**
 * Class ArrayStorage
 *
 * @package App\Library\Crawler\Storage
 */
class ArrayStorage implements Storage
{
    /**
     * Detected pages
     *
     * @var Link[]
     */
    private $detectedLinks = [];

    /**
     * {@inheritdoc}
     */
    public function findByUrl(string $url): ?Link
    {
        foreach ($this->detectedLinks as $detectedLink) {
            if ($detectedLink->getUrl() === $url) {
                return $detectedLink;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Link $link): void
    {
        foreach ($this->detectedLinks as $i => $detectedLink) {
            if ($detectedLink->getUrl() === $link->getUrl()) {
                $this->detectedLinks[$i] = $link;
                return ;
            }
        }

        throw new LogicException('Link with URL "' . $link->getUrl() . '" not found.');
    }

    /**
     * {@inheritdoc}
     */
    public function add(Link $link): void
    {
        foreach ($this->detectedLinks as $i => $detectedLink) {
            if ($detectedLink->getUrl() === $link->getUrl()) {
                throw new LogicException('Link with URL "' . $link->getUrl() . '" already exists.');
            }
        }

        $this->detectedLinks[] = $link;
    }

}
