<?php

namespace App\Library\Crawler\Entity;

/**
 * Class Links
 *
 * @package App\Library\Crawler\Entity
 */
class Links
{
    /**
     * @var array
     */
    private $links = [];

    /**
     * @param Link $link
     */
    public function add(Link $link): void
    {
        $this->links[] = $link;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->links;
    }

}