<?php

namespace Tests\Library\Crawler\Feature;

use App\Library\Crawler;

/**
 * Class IsAvailableUrlTest
 *
 * @package Tests\Library\Crawler\Feature
 */
class IsAvailableUrlTest extends \PHPUnit\Framework\TestCase
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
        $isCorrectPage = $this->crawler->isCorrectPage($url);

        $this->assertEquals($isCorrectPage, $expected);
    }

}
