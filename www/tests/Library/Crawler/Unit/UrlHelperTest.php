<?php

namespace Tests\Library\Crawler\Unit;

use App\Library\Crawler\Helper\UrlHelper;

/**
 * Class UrlHelperTest
 *
 * @package Tests\Library\Crawler\Unit
 */
class UrlHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @inheritdoc
     */
    public function additionProviderForBuildUrl(): array
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
     * @inheritdoc
     */
    public function additionProviderForGetDomain(): array
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
     * @inheritdoc
     */
    public function additionProviderForUnParseUrl(): array
    {
        return [
            ['http://site.ru', 'http://site.ru'],
            ['https://site.ru', 'https://site.ru'],

            ['http://site.ru/info', 'http://site.ru/info'],
        ];
    }

    /**
     * @dataProvider additionProviderForBuildUrl
     * @param array $data
     * @param string $expected
     */
    public function testBuildUrl(array $data, string $expected): void
    {
        list($url, $currentUrl) = $data;
        $buildUrl = UrlHelper::buildUrl($url, $currentUrl);

        $this->assertEquals($expected, $buildUrl);
    }

    /**
     * @dataProvider additionProviderForGetDomain
     * @param string $url
     * @param string $expected
     */
    public function testGetDomainFromUrl(string $url, string $expected): void
    {
        $domain = UrlHelper::getDomain($url);

        $this->assertEquals($expected, $domain);
    }

    /**
     * @dataProvider additionProviderForUnParseUrl
     * @param string $urlForPars
     * @param string $expected
     */
    public function testUnParseUrl(string $urlForPars, string $expected): void
    {
        $url = UrlHelper::unParseUrl(parse_url($urlForPars));

        $this->assertEquals($expected, $url);
    }
}
