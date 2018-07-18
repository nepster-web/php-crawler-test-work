<?php

namespace Tests\Library\Crawler\Unit;

use App\Library\Crawler\Entity\Link;

/**
 * Class EntityTest
 *
 * @package Tests\Library\Crawler\Unit
 */
class EntityTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function testDefaultEntityData(): void
    {
        $link = new Link('http://example.com', 1);

        $this->assertEquals('http://example.com', $link->getUrl());
        $this->assertEquals(1, $link->getDepth());
        $this->assertFalse($link->isVisited());
        $this->assertFalse($link->isDetected());
    }

    /** @test */
    public function testChangeDepth(): void
    {
        $link = new Link('http://example.com', 1);
        $link->changeDepth(2);

        $this->assertEquals(2, $link->getDepth());
    }

    /** @test */
    public function testVisitedUrl(): void
    {
        $link = new Link('http://example.com', 1);
        $link->visited();

        $this->assertTrue($link->isVisited());
    }

    /** @test */
    public function testDetectedUrl(): void
    {
        $link = new Link('http://example.com', 1);
        $link->detected();

        $this->assertTrue($link->isDetected());
    }

    /** @test */
    public function testUnVisitedUrl(): void
    {
        $link = new Link('http://example.com', 1);
        $link->visited();
        $link->unVisited();

        $this->assertFalse($link->isVisited());
    }

    /** @test */
    public function testUnDetectedUrl(): void
    {
        $link = new Link('http://example.com', 1);
        $link->detected();
        $link->unDetected();

        $this->assertFalse($link->isDetected());
    }
}
