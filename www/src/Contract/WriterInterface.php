<?php

namespace App\Contract;

/**
 * Interface WriterInterface
 *
 * @package App\Contract
 */
interface WriterInterface
{
    /**
     * Получить имя отчета
     *
     * @return mixed
     */
    public function getReportName();

    /**
     * Сохранить отчет
     *
     * @return mixed
     */
    public function save();
}