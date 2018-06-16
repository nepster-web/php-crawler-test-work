<?php

namespace App\Infrastructure\Contract;

/**
 * Interface ReportSaver
 *
 * @package App\Infrastructure\Contract
 */
interface ReportSaver
{
    /**
     * @return string
     */
    public function getReportName(): string;

    /**
     * Report save
     */
    public function save(): void;
}
