<?php

namespace App\Infrastructure\Factory;

use App\Infrastructure\Config;
use App\Infrastructure\Contract\ReportSaver;
use App\Infrastructure\Report\HtmlReportSaver;

/**
 * Class ReportSaverFactory
 *
 * @package App\Infrastructure\Factory
 */
class ReportSaverFactory
{
    /**
     * @param array $report
     * @param string $reportName
     * @param null|string $url
     * @return ReportSaver
     */
    public function __invoke(array $report, string $reportName, ?string $url = null): ReportSaver
    {
        $reportDir = Config::getInstance()->get('reportsPath');

        return (new HtmlReportSaver($report, $reportDir, $reportName, $url));
    }
}
