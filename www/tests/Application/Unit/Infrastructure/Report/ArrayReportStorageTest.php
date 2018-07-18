<?php

namespace Tests\Application\Unit\Infrastructure\Report;

use LogicException;
use App\Infrastructure\Report\ArrayReportStorage;

/**
 * Class ArrayReportStorageTest
 *
 * @package Tests\Application\Unit\Infrastructure\Report
 */
class ArrayReportStorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ArrayReportStorage
     */
    private $storage;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storage = new ArrayReportStorage();
    }

    /** @test */
    public function testAddToStorage(): void
    {
        $this->storage->add('reportName', ['key' => 'value']);

        $this->assertEquals('value', $this->storage->get('reportName')[0]['key']);
    }

    /** @test */
    public function testGetNotExistentReportFromStorage(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Report "not-existent" doesn\'t exists.');

        $this->storage->get('not-existent');
    }
}
