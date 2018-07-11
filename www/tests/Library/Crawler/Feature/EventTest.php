<?php

namespace Tests\Library\Crawler\Feature;

use DOMDocument;
use App\Library\Crawler\Crawler;
use App\Library\Crawler\Storage\ArrayStorage;

/**
 * Class EventTest
 *
 * @package Tests\Library\Crawler\Feature
 */
class EventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->crawler = new Crawler(new ArrayStorage());
    }

    /** @test */
    public function testEventBeforeHitCall(): void
    {
        $eventCall = false;

        $this->crawler->on(Crawler::EVENT_BEFORE_HIT_CRAWL, function (string $href) use (&$eventCall) {
            $eventCall = true;
        });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($eventCall);
    }

    /** @test */
    public function testEventHitCrawlCall(): void
    {
        $eventCall = false;

        $this->crawler->on(Crawler::EVENT_HIT_CRAWL,
            function (string $href, string $depth, DOMDocument $dom) use (&$eventCall) {
                $eventCall = true;
            });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($eventCall);
    }

    /** @test */
    public function testEventBeforeCrawlCall(): void
    {
        $eventCall = false;

        $this->crawler->on(Crawler::EVENT_BEFORE_CRAWL, function (string $href) use (&$eventCall) {
            $eventCall = true;
        });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($eventCall);
    }

    /** @test */
    public function testEventAfterCrawlCall(): void
    {
        $eventCall = false;

        $this->crawler->on(Crawler::EVENT_AFTER_CRAWL, function (string $href) use (&$eventCall) {
            $eventCall = true;
        });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($eventCall);
    }

    /** @test */
    public function testEventCallFail(): void
    {
        $isError = false;
        try {
            $this->crawler->on('undefined', function () {

            });
        } catch (\Exception $e) {
            $isError = true;
        }
        $this->assertTrue($isError);
    }
}
