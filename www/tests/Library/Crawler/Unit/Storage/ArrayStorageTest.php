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
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storage = new ArrayStorage();
    }

    /**
     *
     */
    public function testTest(): void
    {
        $this->assertTrue(false);
    }
}
