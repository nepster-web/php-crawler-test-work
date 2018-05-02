<?php

namespace tests;

require_once(__DIR__ . '/../vendor/autoload.php');

use src\Application;

/**
 * Application Test
 * @package tests
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOptions()
    {
        $app = new Application();
        $this->assertEquals($app->getOptions(), ['url' => null, 'depth' => 5]);
    }

    public function testOptionPath()
    {
        $app = new Application([
            'depth' => 23
        ]);
        $this->assertEquals($app->getOptions(), ['url' => null, 'depth' => 23]);
    }

    public function testOptionUrl()
    {
        $app = new Application([
            'url' => 'http://site.ru'
        ]);
        $this->assertEquals($app->getOptions(), ['url' => 'http://site.ru', 'depth' => 5]);
    }

    public function testOptions()
    {
        $app = new Application([
            'url' => 'http://site.ru',
            'depth' => 7
        ]);
        $this->assertEquals($app->getOptions(), ['url' => 'http://site.ru', 'depth' => 7]);
    }
}
