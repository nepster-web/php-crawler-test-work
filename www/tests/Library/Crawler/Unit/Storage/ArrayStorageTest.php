<?php

namespace Tests\Library\Crawler\Unit\Storage;

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
    public function testDetectedUrl(): void
    {
        $this->storage->addDetectedUrl('https://example.com', 1, false);

        $this->assertTrue($this->storage->hasDetectedUrl('https://example.com'));
        $this->assertFalse($this->storage->hasVisitedUrl('https://example.com'));
    }

    /** @test */
    public function testVisitedUrl(): void
    {
        $this->storage->addDetectedUrl('https://example.com', 1, true);

        $this->assertTrue($this->storage->hasDetectedUrl('https://example.com'));
        $this->assertTrue($this->storage->hasVisitedUrl('https://example.com'));
    }

    /** @test */
    public function testSetVisitedUrl(): void
    {
        $this->storage->addDetectedUrl('https://example.com', 1, false);
        $this->storage->setVisitedUrl('https://example.com');

        $this->assertTrue($this->storage->hasVisitedUrl('https://example.com'));
    }

    /** @test */
    public function testSetNotExistentVisitedUrl(): void
    {
        $this->storage->setVisitedUrl('https://example.com');

        $this->assertFalse($this->storage->hasVisitedUrl('https://example.com'));
    }

    /** @test */
    public function testNotExistentDetectedUrl(): void
    {
        $this->assertFalse($this->storage->hasDetectedUrl('https://example.com'));
        $this->assertFalse($this->storage->hasVisitedUrl('https://example.com'));
    }
}
