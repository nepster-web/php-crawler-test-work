<?php

namespace Tests\Library\Crawler\Unit\Utility;

use App\Library\Crawler;

/**
 * Class BuildUrlTest
 *
 * @package Tests\Library\Crawler\Unit\Utility
 */
class BuildUrlTest extends \PHPUnit\Framework\TestCase
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
            [['/', 'http://site.ru'], 'http://site.ru/'],

            [['http://site.ru/', 'http://site.ru/'], 'http://site.ru/'],
            [['http://site.ru', 'http://site.ru/'], 'http://site.ru/'],
            [['http://site.ru', 'http://site.ru'], 'http://site.ru/'],
            [['http://site.ru/', 'http://site.ru'], 'http://site.ru/'],

            [['https://site.ru', 'http://site.ru'], 'https://site.ru/'],
            [['https://site.ru/', 'https://site.ru'], 'https://site.ru/'],

            [['info.html', 'http://site.ru'], 'http://site.ru/info.html'],

            [['info.html', 'http://site.ru'], 'http://site.ru/info.html'],
            [['info', 'http://site.ru'], 'http://site.ru/info'],
            [['/info', 'http://site.ru/info/test'], 'http://site.ru/info'],
            [['./info', 'http://site.ru'], 'http://site.ru/info'],
            [['../../info', 'http://site.ru/s1/s2/s3/s4'], 'http://site.ru/s1/s2/info'],

            [['//info', 'http://site.ru'], ''],
            [['#info', 'http://site.ru'], ''],

            [['tel:00000000000', 'http://site.ru'], ''],
            [['javascript://', 'http://site.ru'], ''],
            [['javascript:alert(\'Hello World!\');', 'http://site.ru'], ''],
            [['mailto:site@example.ru?Subject=Hello%20again', 'http://site.ru'], ''],
            [['skype:username?call', 'http://site.ru'], ''],
            [['http://yandex.ru/cy?base=0&host=site.ru', 'http://site.ru'], '']
        ];
    }

    /**
     * @dataProvider additionProvider
     * @param array $data
     * @param string $expected
     */
    public function testBuildUrl(array $data, string $expected): void
    {
        list($url, $currentUrl) = $data;
        $buildUrl = $this->crawler->buildUrl($url, $currentUrl);

        $this->assertEquals($expected, $buildUrl);
    }
}
