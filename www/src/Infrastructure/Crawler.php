<?php

namespace App\Infrastructure;

use DOMDocument;
use App\Infrastructure\Helper\ArrayHelper;
use App\Infrastructure\Contract\ReportSaver;
use App\Infrastructure\Factory\CrawlerFactory;
use App\Infrastructure\Contract\ReportStorage;
use App\Library\Crawler\Crawler as VendorCrawler;
use App\Infrastructure\Factory\ReportSaverFactory;
use App\Infrastructure\Report\ReportNameGenerator;

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
     * @var ReportStorage
     */
    private $reportStorage;

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
     * @param ReportStorage $reportStorage
     */
    public function __construct(ReportStorage $reportStorage)
    {
        $this->reportStorage = $reportStorage;
        $this->crawler = (new CrawlerFactory())();
    }

    /**
     * {@inheritdoc}
     */
    public function crawl(string $url, int $depth = 5): void
    {
        $crawler = $this->crawler;

        $reportName = ReportNameGenerator::generateName($url);

        $crawler->on($crawler::EVENT_BEFORE_HIT_CRAWL, function (string $href, int $depth): void {
            $this->stepUrlLoadTime = microtime(true);
        });

        $crawler->on($crawler::EVENT_HIT_CRAWL, function (string $href, int $depth, DOMDocument $dom) use ($reportName): void {
            $imgLength = $dom->getElementsByTagName('img')->length;
            $processTime = sprintf('%.6F', microtime(true) - $this->stepUrlLoadTime);
            $this->reportStorage->add($reportName, [
                'href' => $href,
                'depth' => $depth,
                'imgLength' => $imgLength,
                'processTime' => $processTime
            ]);

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
        $this->saveReport($reportName, $url);
    }

    /**
     * @param string $message
     */
    private function show(string $message): void
    {
        echo $message;
    }

    /**
     * @param string $reportName
     * @param string $url
     */
    private function saveReport(string $reportName, string $url)
    {
        $report = $this->reportStorage->get($reportName);

        ArrayHelper::sortAssociativeArrayByKey(
            $report,
            $this->reportOrderByKey,
            $this->reportOrderByDesc
        );

        /** @var ReportSaver $reportSaver */
        $reportSaver = (new ReportSaverFactory())($report, $reportName, $url);
        $reportSaver->save();

        $this->show('Generate report file: ' . $reportName . PHP_EOL);
    }
}
