<?php

namespace Tests\Library\Crawler\Unit;

use DOMDocument;
use App\Library\Crawler\Storage\ArrayStorage;
use Tests\Library\Crawler\_helper\Mock\CrawlerMock;

/**
 * Class EventTest
 *
 * @package Tests\Library\Crawler\Unit
 */
class EventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CrawlerMock
     */
    private $crawler;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->crawler = new CrawlerMock(new ArrayStorage());
    }

    /** @test */
    public function testEventBeforeHitCall(): void
    {
        $eventCall = false;

        $this->crawler->on(CrawlerMock::EVENT_BEFORE_HIT_CRAWL, function (string $href) use (&$eventCall) {
            $eventCall = true;
        });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($eventCall);
    }

    /** @test */
    public function testEventHitCrawlCall(): void
    {
        $eventCall = false;

        $this->crawler->on(CrawlerMock::EVENT_HIT_CRAWL,
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

        $this->crawler->on(CrawlerMock::EVENT_BEFORE_CRAWL, function (string $href) use (&$eventCall) {
            $eventCall = true;
        });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($eventCall);
    }

    /** @test */
    public function testEventAfterCrawlCall(): void
    {
        $eventCall = false;

        $this->crawler->on(CrawlerMock::EVENT_AFTER_CRAWL, function (string $href) use (&$eventCall) {
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
