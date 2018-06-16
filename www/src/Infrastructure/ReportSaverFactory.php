<?php

namespace App\Infrastructure;

use App\Infrastructure\Contract\ReportSaver;
use App\Infrastructure\Report\HtmlReportSaver;

/**
 * Class ReportSaverFactory
 *
 * @package App\Infrastructure
 */
class ReportSaverFactory
{
    /**
     * @param array $report
     * @param null|string $url
     * @return ReportSaver
     */
    public function __invoke(array $report, ?string $url = null): ReportSaver
    {
        $reportDir = __DIR__ . '/../../reports';

        return (new HtmlReportSaver($report, $reportDir, $url));
    }
}
