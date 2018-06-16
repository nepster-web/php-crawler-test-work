<?php

namespace App\Infrastructure;

use Exception;
use DOMDocument;
use App\Library\Crawler as VendorCrawler;
use App\Infrastructure\Contract\ReportSaver;

/**
 * Class Crawler
 *
 * @package App\Infrastructure
 */
class Crawler implements \App\Infrastructure\Contract\Crawler
{
    /**
     * @var VendorCrawler
     */
    private $crawler;

    /**
     * @var array
     */
    private $report = [];

    /**
     * @var string
     */
    private $reportOrderByKey = 'imgLength';

    /**
     * Crawler constructor.
     */
    public function __construct()
    {
        $this->crawler = new VendorCrawler();
    }

    /**
     * @inheritdoc
     */
    public function crawl(string $url, int $depth = 5): void
    {
        $crawler = $this->crawler;

        $crawler->on($crawler::EVENT_HIT_CRAWL, function (string $href, int $depth, DOMDocument $dom): void {
            $start = microtime(true);
            $imgLength = $dom->getElementsByTagName('img')->length;
            $time = microtime(true) - $start;
            $processTime = sprintf('%.6F', $time);
            $this->report[] = [
                'href' => $href,
                'depth' => $depth,
                'imgLength' => $imgLength,
                'processTime' => $processTime
            ];
            $this->show('  - ' . $href . ' [depth: ' . $depth . '] [img: ' . $imgLength . ']' . PHP_EOL);
        });

        $crawler->on($crawler::EVENT_BEFORE_CRAWL, function (string $href, int $depth): void {
            $this->show('Start crawl' . PHP_EOL);
        });

        $crawler->on($crawler::EVENT_AFTER_CRAWL, function (string $href, int $depth): void {
            $this->show('Finish crawl' . PHP_EOL);
        });

        $crawler->crawl($url, $depth);
        $this->report($url);
    }

    /**
     * @param string $message
     */
    private function show(string $message): void
    {
        echo $message;
    }

    /**
     * @param string $url
     * @throws Exception
     */
    private function report(string $url)
    {
        $this->arrayOrderBy();

        /** @var ReportSaver $reportSaver */
        $reportSaver = (new ReportSaverFactory())($this->report, $url);
        $reportSaver->save();

        $this->show('Generate report file: ' . $reportSaver->getReportName() . PHP_EOL);
    }

    /**
     * Algorithm for sorting a multidimensional array
     * http://stackoverflow.com/a/19454643
     */
    private function arrayOrderBy(): void
    {
        usort($this->report, function ($item1, $item2) {
            if ($item1[$this->reportOrderByKey] === $item2[$this->reportOrderByKey]) {
                return 0;
            }
            return $item1[$this->reportOrderByKey] < $item2[$this->reportOrderByKey] ? 1 : -1;
        });
    }
}
