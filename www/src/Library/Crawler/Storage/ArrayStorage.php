<?php

namespace App\Library\Crawler\Storage;

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
     * @var array
     */
    private $detectedUrls = [];


    // todo
    public function test(): array
    {
        return $this->detectedUrls;
    }

    /**
     * {@inheritdoc}
     */
    public function addDetectedUrl(string $url, int $depth, bool $isVisited = false): void
    {
        if (isset($this->detectedUrls[$depth])) {
            if (array_key_exists($url, $this->detectedUrls[$depth]) === false) {
                $this->detectedUrls[$depth][$url] = $isVisited;
            }
        } else {
            $this->detectedUrls[$depth] = [$url => $isVisited];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasDetectedUrl(string $url): bool
    {
        foreach ($this->detectedUrls as $depth => $detectedUrls) {
            if (array_key_exists($url, $detectedUrls)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVisitedUrl(string $url): bool
    {
        foreach ($this->detectedUrls as $depth => &$detectedUrls) {
            if (array_key_exists($url, $detectedUrls)) {
                return $detectedUrls[$url];
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setVisitedUrl(string $url): void
    {
        foreach ($this->detectedUrls as $depth => &$detectedUrls) {
            if (array_key_exists($url, $detectedUrls)) {
                $detectedUrls[$url] = true;
                break;
            }
        }
    }
}
