<?php

namespace App;

use App\Writer\HtmlWriter;
use \Exception as Exception;

/**
 * Application
 *
 * Пример запуска приложения:
 * php cwr --url=http://site.ru --depth=5
 * php cwr -u=http://site.ru -d=5
 *
 * @package App
 */
class Application
{
    /**
     * Входящие параметры
     *
     * @var array
     */
    protected $_options = [
        'url' => null,
        'depth' => 5
    ];

    /**
     * @var string
     */
    private $_errorMessage;

    /**
     * Application constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $_options = [];
        if (php_sapi_name() == "cli") {
            $_options = $this->cliRequest();
        }
        $options = array_merge($_options, $options);
        $this->_options = $this->setOptions($options);
    }

    /**
     * Запуск
     *
     * @return bool
     */
    public function run()
    {
        try {

            $crawler = new Crawler();
            $writer = new HtmlWriter();

            $handler = new Handler($this->_options, $crawler, $writer);
            $handler->crawl();
            $handler->report();

            return true;

        } catch (Exception $e) {
            $this->_errorMessage = 'ERROR: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Возвращает ошибку
     *
     * @return string
     */
    public function getError()
    {
        return $this->_errorMessage;
    }

    /**
     * Возвращает входящие параметры
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Обработка запроса
     *
     * @return array
     * @throws Exception
     */
    public function cliRequest()
    {
        $shortopts = "";
        $longopts = [];

        foreach ($this->_options as $option => &$value) {
            $shortopts .= $option[0] . '::';
            $longopts[] = $option . '::';
        }

        $options = getopt($shortopts, $longopts);

        if (!is_array($options)) {
            throw new Exception('Function "getopt()" is not array');
        }

        return $options;
    }

    /**
     * Устанавливаем опции
     *
     * @param array $options
     * @return array
     */
    public function setOptions(array $options)
    {
        $result = [];

        foreach ($this->_options as $option => &$defaultValue) {
            if (empty($result[$option])) {
                $result[$option] = $defaultValue ? $defaultValue : null;
            }
            foreach ($options as $_option => &$_value) {
                if ($option == $_option) {
                    $result[$option] = $_value;
                    continue;
                }
                if ($option[0] == $_option) {
                    $result[$option] = $_value;
                    continue;
                }
            }
        }

        return $result;
    }
}