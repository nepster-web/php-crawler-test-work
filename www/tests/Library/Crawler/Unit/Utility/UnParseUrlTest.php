<?php

namespace Tests\Library\Crawler\Unit\Utility;

use App\Library\Crawler;

/**
 * Class UnParseUrlTest
 *
 * @package Tests\Library\Crawler\Unit\Utility
 */
class UnParseUrlTest extends \PHPUnit\Framework\TestCase
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

        $this->crawler = new Crawler();
    }

    /**
     * @inheritdoc
     */
    public function additionProvider(): array
    {
        return [
            ['http://site.ru', 'http://site.ru'],
            ['https://site.ru', 'https://site.ru'],

            ['http://site.ru/info', 'http://site.ru/info'],
        ];
    }

    /**
     * @dataProvider additionProvider
     * @param string $urlForPars
     * @param string $expected
     */
    public function testUnParseUrl(string $urlForPars, string $expected): void
    {
        $url = $this->crawler->unparseUrl(parse_url($urlForPars));

        $this->assertEquals($expected, $url);
    }
}
