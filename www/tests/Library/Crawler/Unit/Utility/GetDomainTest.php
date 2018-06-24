<?php

namespace Tests\Library\Crawler\Unit\Utility;

use App\Library\Crawler;

/**
 * Class GetDomainTest
 *
 * @package Tests\Library\Crawler\Unit\Utility
 */
class GetDomainTest extends \PHPUnit\Framework\TestCase
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
            ['http://site.ru', 'site.ru'],
            ['http://www.site.ru', 'site.ru'],
            ['http://www.test.site.ru', 'site.ru'],

            ['https://site.ru', 'site.ru'],
            ['https://www.site.ru', 'site.ru'],
            ['https://www.test.site.ru', 'site.ru'],
        ];
    }

    /**
     * @dataProvider additionProvider
     * @param string $url
     * @param string $expected
     */
    public function testGetDomainFromUrl(string $url, string $expected): void
    {
        $domain = $this->crawler->getDomain($url);

        $this->assertEquals($expected, $domain);
    }
}
