<?php

namespace App\Library\Crawler\Entity;

/**
 * Class Link
 *
 * @package App\Library\Crawler\Entity
 */
class Link
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $isDetected = false;

    /**
     * @var bool
     */
    private $isVisited = false;

    /**
     * @var int
     */
    private $depth = 1;

    /**
     * Link constructor.
     * @param string $url
     * @param int $depth
     */
    public function __construct(string $url, int $depth = 1)
    {
        $this->url = $url;
        $this->depth = $depth;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isDetected(): bool
    {
        return $this->isDetected;
    }

    /**
     * @return bool
     */
    public function isVisited(): bool
    {
        return $this->isVisited;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     */
    public function changeDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    /**
     *
     */
    public function visited(): void
    {
        $this->isVisited = true;
    }

    /**
     *
     */
    public function unVisited(): void
    {
        $this->isVisited = false;
    }

    /**
     *
     */
    public function detected(): void
    {
        $this->isDetected = true;
    }

    /**
     *
     */
    public function unDetected(): void
    {
        $this->isDetected = false;
    }

}
