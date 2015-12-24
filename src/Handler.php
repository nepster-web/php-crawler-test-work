<?php

namespace src;

use \DOMDocument as DOMDocument;
use \Exception as Exception;

/**
 * Handler
 *
 * @package src
 */
class Handler
{
    /**
     * @var array
     */
    private $_options = [];

    /**
     * @var null|array
     */
    private $_report = null;

    /**
     * Handler constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->validate($options);
        $this->_options = $options;
    }

    /**
     * Вывести на экран
     *
     * @param $message
     */
    public function show($message)
    {
        echo $message;
    }

    /**
     * Процесс парсинга
     *
     * @throws Exception
     */
    public function crawl()
    {
        $this->_report = [];

        $crawler = new Crawler();

        $crawler->on(Crawler::EVENT_HIT_CRAWL, function ($href, $depth, DOMDocument $dom) {
            $start = microtime(true);
            $imgLength = $dom->getElementsByTagName('img')->length;
            $time = microtime(true) - $start;
            $processTime = sprintf('%.6F', $time);
            $this->_report[] = [
                'href' => $href,
                'depth' => $depth,
                'imgLength' => $imgLength,
                'processTime' => $processTime
            ];
            $this->show('  - ' . $href . ' [img: ' . $imgLength . ']' . PHP_EOL);
        });

        $crawler->on(Crawler::EVENT_BEFORE_CRAWL, function ($href, $depth) {
            $this->show('Start crawl' . PHP_EOL);
        });

        $crawler->on(Crawler::EVENT_AFTER_CRAWL, function ($href, $depth) {
            $this->show('Finish crawl' . PHP_EOL);
        });

        $crawler->crawl($this->_options['url'], $this->_options['depth']);
    }

    /**
     * Генерация отчета
     * @throws Exception
     */
    public function report()
    {
        if (!is_array($this->_report)) {
            throw new Exception('The process "Handler->crawl()" is not running');
        }

        $this->arrayOrderBy();

        $writer = new HtmlWriter();
        $writer->setReport($this->_report);
        $writer->save();

        $this->show('Generate report file: ' . $writer->getReportName() . PHP_EOL);
    }

    /**
     * Валидация параметров
     *
     * @param $options
     * @throws Exception
     */
    public function validate($options)
    {
        foreach ($options as $option => &$value) {
            switch ($option) {
                case 'url':
                    if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                        throw new Exception('Param "' . $option . '" is not a valid URL. Please enter valid value: --' . $option . '=http://site.ru');
                    }
                    break;

                case 'depth':
                    if (ctype_digit(strval($value)) === false) {
                        throw new Exception('Param "' . $option . '" is not a valid integer. Please enter valid value: --' . $option . '=5');
                    }
                    break;

                default:
                    continue;
            }
        }
    }

    /**
     * Алгорит сортировки многомерного массива
     * http://stackoverflow.com/a/19454643
     *
     * @return mixed
     */
    private function arrayOrderBy()
    {
        usort($this->_report, function ($item1, $item2) {
            if ($item1['imgLength'] == $item2['imgLength']) return 0;
            return $item1['imgLength'] < $item2['imgLength'] ? 1 : -1;
        });
    }

}