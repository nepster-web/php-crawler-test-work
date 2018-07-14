<?php

namespace Tests\Application\Unit\Infrastructure;

use LogicException;
use App\Infrastructure\Config;

/**
 * Class ConfigTest
 *
 * @package Tests\Application\Unit\Infrastructure
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $configArray = [];

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->config = Config::getInstance();
        $this->configArray = require realpath(__DIR__ . '/../../../../src/config.php');
    }

    /** @test */
    public function testGetConfigValueByKey(): void
    {
        $key = array_keys($this->configArray)[0];
        $value = $this->configArray[$key];

        $this->assertEquals($value, $this->config->get($key));
    }

    /** @test */
    public function testGetConfigValueByNotExistentKey(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Param "not-existent" doesn\'t exists.');

        $this->config->get('not-existent');
    }

}
