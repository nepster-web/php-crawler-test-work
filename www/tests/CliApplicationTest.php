<?php

namespace Tests;

use App\CliApplication;

/**
 * Class CliApplicationTest
 *
 * @package Tests
 */
class CliApplicationTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultOptions()
    {
        $app = new CliApplication();
        $this->assertEquals($app->getOptions(), ['url' => null, 'depth' => 5]);
    }

    public function testOptionPath()
    {
        $app = new CliApplication([
            'depth' => 23
        ]);
        $this->assertEquals($app->getOptions(), ['url' => null, 'depth' => 23]);
    }

    public function testOptionUrl()
    {
        $app = new CliApplication([
            'url' => 'http://site.ru'
        ]);
        $this->assertEquals($app->getOptions(), ['url' => 'http://site.ru', 'depth' => 5]);
    }

    public function testOptions()
    {
        $app = new CliApplication([
            'url' => 'http://site.ru',
            'depth' => 7
        ]);
        $this->assertEquals($app->getOptions(), ['url' => 'http://site.ru', 'depth' => 7]);
    }
}
