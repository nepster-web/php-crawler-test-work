<?php

namespace App\Infrastructure;

use Exception;
use DOMDocument;
use App\Library\Crawler as VendorCrawler;
use App\Infrastructure\Helper\ArrayHelper;
use App\Infrastructure\Contract\ReportSaver;
use App\Infrastructure\Factory\ReportSaverFactory;

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
     * @var bool
     */
    private $reportOrderByDesc = true;

    /**
     * @var null|int
     */
    private $stepUrlLoadTime = null;

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

        $crawler->on($crawler::EVENT_BEFORE_HIT_CRAWL, function (string $href, int $depth): void {
            $this->stepUrlLoadTime = microtime(true);
        });

        $crawler->on($crawler::EVENT_HIT_CRAWL, function (string $href, int $depth, DOMDocument $dom): void {
            $imgLength = $dom->getElementsByTagName('img')->length;
            $processTime = sprintf('%.6F', microtime(true) - $this->stepUrlLoadTime);
            $this->report[] = [
                'href' => $href,
                'depth' => $depth,
                'imgLength' => $imgLength,
                'processTime' => $processTime
            ];

            $this->stepUrlLoadTime = null;
            $this->show('  - ' . $href . ' [depth: ' . $depth . '] [img: ' . $imgLength . ']' . PHP_EOL);
        });

        $crawler->on($crawler::EVENT_BEFORE_CRAWL, function (string $href): void {
            $this->show('Start crawl' . PHP_EOL);
        });

        $crawler->on($crawler::EVENT_AFTER_CRAWL, function (string $href): void {
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
        ArrayHelper::sortAssociativeArrayByKey(
            $this->report,
            $this->reportOrderByKey,
            $this->reportOrderByDesc
        );

        /** @var ReportSaver $reportSaver */
        $reportSaver = (new ReportSaverFactory())($this->report, $url);
        $reportSaver->save();

        $this->show('Generate report file: ' . $reportSaver->getReportName() . PHP_EOL);
    }
}
