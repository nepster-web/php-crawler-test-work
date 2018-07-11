<?php

namespace Tests\Library\Crawler\Feature;

use App\Library\Crawler\Helper\HttpHelper;

/**
 * Class HttpHelperTest
 *
 * @package Tests\Library\Crawler\Feature
 */
class HttpHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @inheritdoc
     */
    public function additionProvider(): array
    {
        return [
            ['http://php.net', true],
            ['https://secure.php.net', true],

            ['http://php.net/', true],
            ['https://secure.php.net/', true],

            ['http://php.net/logo.php', false],
        ];
    }

    /**
     * @dataProvider additionProvider
     * @param string $url
     * @param string $expected
     */
    public function testIsCorrectPage(string $url, string $expected): void
    {
        $isCorrectPage = HttpHelper::isAvailablePage($url);

        $this->assertEquals($isCorrectPage, $expected);
    }

}
