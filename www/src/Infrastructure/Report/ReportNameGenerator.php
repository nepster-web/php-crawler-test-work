<?php

namespace App\Infrastructure\Report;

/**
 * Class ReportNameGenerator
 *
 * @package App\Infrastructure\Report
 */
class ReportNameGenerator
{
    /**
     * @var string
     */
    private static $extension = '.html';

    /**
     * @param null|string $domain
     * @return string
     */
    public static function generateName(?string $domain = null): string
    {
        $dateWithExtension = date('d.m.Y') . self::$extension;
        $reportName = 'report_' . $dateWithExtension;

        if (empty($domain) === false) {
            $domain = parse_url($domain, PHP_URL_HOST);
            if (empty($domain) === false) {
                $reportName = str_replace('report_', $domain . '_', $reportName);
            }
        }

        return $reportName;
    }

}
