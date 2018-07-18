<?php

namespace Tests\Library\Crawler\Unit\Storage;

use App\Library\Crawler\Entity\Link;
use App\Library\Crawler\Storage\ArrayStorage;

/**
 * Class ArrayStorageTest
 *
 * @package Tests\Library\Crawler\Unit\Storage
 */
class ArrayStorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ArrayStorage
     */
    private $storage;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storage = new ArrayStorage();
    }

    /** @test */
    public function testAddLinkToStorage(): void
    {
        $newLink = new Link('https://example.com', 1);

        $this->storage->add($newLink);

        $link = $this->storage->findByUrl($newLink->getUrl());

        $this->assertEquals($newLink->getUrl(), $link->getUrl());
    }

    /** @test */
    public function testSaveLink(): void
    {
        $link = new Link('https://example.com', 1);
        $this->storage->add($link);

        $link->changeDepth(2);

        $this->storage->save($link);

        $link = $this->storage->findByUrl($link->getUrl());

        $this->assertEquals(2, $link->getDepth());
    }
}
