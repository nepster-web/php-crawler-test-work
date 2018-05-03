<?php

namespace Tests;

require_once(__DIR__ . '/../vendor/autoload.php');

use App\Crawler;

/**
 * Class CrawlerTest
 *
 * @package Tests
 */
class CrawlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Crawler
     */
    public $crawler;

    /**
     * @var string
     */
    public $startUrl;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->crawler = new Crawler();
    }

    public function testFunctionBuildUrl()
    {
        $this->assertEquals($this->crawler->buildUrl('/', 'http://site.ru'), 'http://site.ru/');
        $this->assertEquals($this->crawler->buildUrl('http://site.ru/', 'http://site.ru/'), 'http://site.ru/');
        $this->assertEquals($this->crawler->buildUrl('http://site.ru', 'http://site.ru/'), 'http://site.ru/');
        $this->assertEquals($this->crawler->buildUrl('http://site.ru', 'http://site.ru'), 'http://site.ru/');
        $this->assertEquals($this->crawler->buildUrl('http://site.ru/', 'http://site.ru'), 'http://site.ru/');

        $this->assertEquals($this->crawler->buildUrl('https://site.ru', 'http://site.ru'), 'https://site.ru/');
        $this->assertEquals($this->crawler->buildUrl('https://site.ru/', 'https://site.ru'), 'https://site.ru/');

        $this->assertEquals($this->crawler->buildUrl('info.html', 'http://site.ru'), 'http://site.ru/info.html');

        $this->assertEquals($this->crawler->buildUrl('info.html', 'http://site.ru'), 'http://site.ru/info.html');
        $this->assertEquals($this->crawler->buildUrl('info', 'http://site.ru'), 'http://site.ru/info');
        $this->assertEquals($this->crawler->buildUrl('/info', 'http://site.ru/info/test'), 'http://site.ru/info');
        $this->assertEquals($this->crawler->buildUrl('./info', 'http://site.ru'), 'http://site.ru/info');
        $this->assertEquals($this->crawler->buildUrl('../../info', 'http://site.ru/s1/s2/s3/s4'), 'http://site.ru/s1/s2/info');

        $this->assertEquals($this->crawler->buildUrl('//info', 'http://site.ru'), '');

        $this->assertEquals($this->crawler->buildUrl('javascript://', 'http://site.ru'), '');
        $this->assertEquals($this->crawler->buildUrl('javascript:alert(\'Hello World!\');', 'http://site.ru'), '');
        $this->assertEquals($this->crawler->buildUrl('mailto:site@example.ru?Subject=Hello%20again', 'http://site.ru'), '');
        $this->assertEquals($this->crawler->buildUrl('skype:username?call', 'http://site.ru'), '');
    }

    public function testFunctionUnparseUrl()
    {
        $url = $this->crawler->unparseUrl(parse_url('http://site.ru'));
        $result = false;
        if (is_string($url) && !filter_var($url, FILTER_VALIDATE_URL) === false) {
            $result = true;
        }
        $this->assertTrue($result);
    }

    public function testEvents()
    {
        $events = [
            'eventHit' => false,
            'eventBefore' => false,
            'eventAfter' => false,
        ];

        $this->crawler->on(Crawler::EVENT_HIT_CRAWL, function ($href, $depth, \DOMDocument $dom) use (&$events) {
            $events['eventHit'] = true;
        });

        $this->crawler->on(Crawler::EVENT_BEFORE_CRAWL, function ($href, $depth) use (&$events)  {
            $events['eventBefore'] = true;
        });

        $this->crawler->on(Crawler::EVENT_AFTER_CRAWL, function ($href, $depth) use (&$events)  {
            $events['eventAfter'] = true;
        });

        $this->crawler->crawl('http://php.net', 1);

        $this->assertTrue($events['eventHit']);
        $this->assertTrue($events['eventBefore']);
        $this->assertTrue($events['eventAfter']);
    }

    public function testEventFail()
    {
        $error = false;
        try {
            $this->crawler->on('undefined', function ($href, $depth, \DOMDocument $dom) use (&$events) {

            });
        } catch (\Exception $e) {
            $error = true;
        }
        $this->assertTrue($error);
    }

    public function testFunctionGetDomain()
    {
        $this->assertEquals($this->crawler->getDomain('http://site.ru'), 'site.ru');
        $this->assertEquals($this->crawler->getDomain('http://www.site.ru'), 'site.ru');
        $this->assertEquals($this->crawler->getDomain('http://www.test.site.ru'), 'site.ru');
    }

    public function testFunctionIsCorrectPage()
    {
        $this->assertTrue($this->crawler->isCorrectPage('http://php.net'));
        $this->assertFalse($this->crawler->isCorrectPage('http://php.net/images/logo.php'));
    }

}
