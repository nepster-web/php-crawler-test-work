<?php

namespace App\Infrastructure\Report;

use LogicException;

/**
 * Class ArrayReportStorage
 *
 * @package App\Infrastructure\Report
 */
class ArrayReportStorage implements \App\Infrastructure\Contract\ReportStorage
{
    /**
     * @var array
     */
    private $reports = [];

    /**
     * {@inheritdoc}
     */
    public function add(string $reportName, array $reportData): void
    {
        if (isset($this->reports[$reportName]) === false) {
            $this->reports[$reportName] = [];
        }
        $this->reports[$reportName][] = $reportData;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $reportName): array
    {
        if (isset($this->reports[$reportName]) === false) {
            throw new LogicException('Report "' . $reportName . '" doesn\'t exists.');
        }

        return $this->reports[$reportName];
    }

}
