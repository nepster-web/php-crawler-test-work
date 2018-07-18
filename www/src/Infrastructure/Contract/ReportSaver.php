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
     * Report save
     */
    public function save(): void;
}
