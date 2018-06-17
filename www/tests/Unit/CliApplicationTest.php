<?php

namespace Tests\Unit;

use App\CliApplication;
use Tests\_helper\Mock\CrawlerMock;

/**
 * Class CliApplicationTest
 *
 * @package Tests\Unit
 */
class CliApplicationTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function testDefaultOptions(): void
    {
        $crawler = new CrawlerMock();

        $app = new CliApplication($crawler);

        $this->assertEquals($app->getParams(), [
            'url' => null,
            'depth' => 5,
        ]);
    }

    /** @test */
    public function testOptionPath(): void
    {
        $crawler = new CrawlerMock();

        $app = new CliApplication($crawler);
        $app->setParams([
            'depth' => 7
        ]);

        $this->assertEquals($app->getParams(), [
            'url' => null,
            'depth' => 7,
        ]);
    }

    /** @test */
    public function testOptionUrl(): void
    {
        $crawler = new CrawlerMock();

        $app = new CliApplication($crawler);
        $app->setParams([
            'url' => 'http://site.ru',
        ]);

        $this->assertEquals($app->getParams(), [
            'url' => 'http://site.ru',
            'depth' => 5,
        ]);
    }

    /** @test */
    public function testOptions(): void
    {
        $crawler = new CrawlerMock();

        $app = new CliApplication($crawler);
        $app->setParams([
            'url' => 'http://site.ru',
            'depth' => 7,
        ]);

        $this->assertEquals($app->getParams(), [
            'url' => 'http://site.ru',
            'depth' => 7,
        ]);
    }
}
