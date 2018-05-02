<?php

namespace tests;

require_once(__DIR__ . '/../vendor/autoload.php');

use src\Crawler;
use src\Handler;
use src\writers\HtmlWriter;

/**
 * Handler Test
 * @package tests
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public $crawler;
    public $writer;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->crawler = new Crawler();
        $this->writer = new HtmlWriter();
    }

    public function testValidateUrl()
    {
        $error = false;
        try {
            $handler = new Handler([
                'url' => 'http://site.ru',
            ], $this->crawler, $this->writer);
        } catch (\Exception $e) {
            $error = true;
        }
        $this->assertFalse($error);
    }

    public function testValidateDepth()
    {
        $error = false;
        try {
            $handler = new Handler([
                'url' => 'http://site.ru',
                'depth' => 7,
            ], $this->crawler, $this->writer);
        } catch (\Exception $e) {
            $error = true;
        }
        $this->assertFalse($error);
    }

    public function testErrorValidateUrl()
    {
        $error = false;
        try {
            $handler = new Handler([
                'url' => 'test',
            ], $this->crawler, $this->writer);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'Param "url" is not a valid URL. Please enter valid value: --url=http://site.ru');
    }

    public function testErrorValidateDepth()
    {
        $error = false;
        try {
            $handler = new Handler([
                'url' => 'http://site.ru',
                'depth' => 'test',
            ], $this->crawler, $this->writer);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $this->assertEquals($error, 'Param "depth" is not a valid integer. Please enter valid value: --depth=5');
    }
}
