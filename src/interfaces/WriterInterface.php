<?php

namespace src\interfaces;

/**
 * Writer Interface
 * @package src\interfaces
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