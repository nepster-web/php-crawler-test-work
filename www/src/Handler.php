<?php

namespace App;

use \Exception as Exception;
use \DOMDocument as DOMDocument;
use  App\Contract as interfaces;

/**
 * Handler
 *
 * @package App
 */
class Handler
{
    /**
     * @var array
     */
    protected $_options = [];

    /**
     * @var null|array
     */
    protected $_report = null;

    /**
     * @var null|array
     */
    protected $_reportOrderByKey = 'imgLength';

    /**
     * @var interfaces\CrawlerInterface
     */
    private $_crawler;

    /**
     * @var interfaces\WriterInterface
     */
    private $_writer;

    /**
     * Handler constructor.
     *
     * @param array $options
     * @param interfaces\CrawlerInterface $crawler
     * @param interfaces\WriterInterface $writer
     */
    public function __construct(array $options, interfaces\CrawlerInterface $crawler, interfaces\WriterInterface $writer)
    {
        $this->validate($options);

        $this->_options = $options;
        $this->_crawler = $crawler;
        $this->_writer = $writer;
    }

    /**
     * @return interfaces\CrawlerInterface
     */
    public function getCrawler()
    {
        return $this->_crawler;
    }

    /**
     * @return interfaces\WriterInterface
     */
    public function getWriter()
    {
        return $this->_writer;
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

        $crawler = $this->_crawler;

        $crawler->on($crawler::EVENT_HIT_CRAWL, function ($href, $depth, DOMDocument $dom) {
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

        $crawler->on($crawler::EVENT_BEFORE_CRAWL, function ($href, $depth) {
            $this->show('Start crawl' . PHP_EOL);
        });

        $crawler->on($crawler::EVENT_AFTER_CRAWL, function ($href, $depth) {
            $this->show('Finish crawl' . PHP_EOL);
        });

        $crawler->crawl($this->_options['url'], $this->_options['depth']);
    }

    /**
     * Генерация отчета
     *
     * @throws Exception
     */
    public function report()
    {
        if (!is_array($this->_report)) {
            throw new Exception('The process "Handler->crawl()" is not running');
        }

        $this->arrayOrderBy();

        $this->_writer->setReport($this->_report);
        $this->_writer->save();

        $this->show('Generate report file: ' . $this->_writer->getReportName() . PHP_EOL);
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
    protected function arrayOrderBy()
    {
        usort($this->_report, function ($item1, $item2) {
            if ($item1[$this->_reportOrderByKey] == $item2[$this->_reportOrderByKey]) return 0;
            return $item1[$this->_reportOrderByKey] < $item2[$this->_reportOrderByKey] ? 1 : -1;
        });
    }

}