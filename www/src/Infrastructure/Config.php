<?php

namespace App\Infrastructure;

use LogicException;

/**
 * Class Crawler
 *
 * @package App\Infrastructure
 */
class Config
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @return Config
     */
    public static function getInstance(): Config
    {
        if (null === self::$instance) {
            $config = require realpath(__DIR__ . '/../config.php');
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        throw new LogicException('Param "' . $key . '" doesn\'t exists.');
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * Config constructor.
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
    }
}