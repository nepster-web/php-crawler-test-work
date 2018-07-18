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
     * @param null|string $prefix
     * @return string
     */
    public static function generateName(?string $prefix = null): string
    {
        $reportName = date('d.m.Y') . self::$extension;

        if (empty($prefix) === false) {
            $domain = parse_url($prefix, PHP_URL_HOST);
            if (empty($domain) === false) {
                return $domain . '_' . $reportName;
            }
            return $prefix . '_' . $reportName;
        }

        return 'report_' . $reportName;
    }

}
