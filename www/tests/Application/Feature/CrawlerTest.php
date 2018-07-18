<?php

namespace Tests\Application\Feature;

use App\Infrastructure\Config;

/**
 * Class CrawlerTest
 *
 * @package Tests\Application\Feature
 */
class CrawlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $reportDir;

    /**
     * @var string
     */
    private static $reportName;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        self::$reportDir = rtrim(Config::getInstance()->get('reportsPath'), '/') . '/';
        self::$reportName = 'php.net_' . date('d.m.Y') . '.html';
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass(): void
    {
        unlink(self::$reportDir . self::$reportName);
    }

    /** @test */
    public function testCrawl(): void
    {
        shell_exec('php cwr -u=http://php.net -d=1');

        $this->assertTrue(file_exists(self::$reportDir . self::$reportName));
    }
}
