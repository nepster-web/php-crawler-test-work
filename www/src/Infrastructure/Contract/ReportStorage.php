<?php

namespace App\Infrastructure\Contract;

/**
 * Interface ReportStorage
 *
 * @package App\Infrastructure\Contract
 */
interface ReportStorage
{
    /**
     * @param string $reportName
     * @param array $reportData
     */
    public function add(string $reportName, array $reportData): void;

    /**
     * @param string $reportName
     * @return array
     */
    public function get(string $reportName): array;
}
