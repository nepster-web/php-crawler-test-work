<?php

namespace App\Infrastructure\Report;

use LogicException;

/**
 * Class HtmlReportSaver
 *
 * @package App\Infrastructure\Report
 */
class HtmlReportSaver implements \App\Infrastructure\Contract\ReportSaver
{
    /**
     * @var string
     */
    private $extension = '.html';

    /**
     * @var string
     */
    private $reportPath;

    /**
     * @var array
     */
    private $report = [];

    /**
     * @var string
     */
    private $reportName;

    /**
     * @var null|string
     */
    private $domain = null;

    /**
     * @var array
     */
    private $params = [
        'href' => 'URL',
        'depth' => 'Depth',
        'processTime' => 'Parsing time',
        'imgLength' => 'Images amount',
    ];

    /**
     * HtmlReportSaver constructor.
     *
     * @param array $report
     * @param string $reportPath
     * @param null|string $domain
     */
    public function __construct(
        array $report,
        string $reportPath,
        ?string $domain
    ) {
        $this->report = $report;
        $this->reportPath = $reportPath;
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getReportName(): string
    {
        return $this->reportName;
    }

    /**
     * @throws LogicException
     */
    public function save(): void
    {
        $this->generateReportName();

        $report = str_replace([
            '{title}',
            '{content}',
        ], [
            $this->reportName,
            $this->generateTable(),
        ], $this->generateTemplate());

        $file = $this->reportPath . DIRECTORY_SEPARATOR . $this->reportName;

        if (!file_put_contents($file, $report)) {
            throw new LogicException('Unable to write to a file: "' . $file . '"');
        }
    }

    /**
     * Generate report name
     */
    private function generateReportName(): void
    {
        $dateWithExtension = date('d.m.Y') . $this->extension;
        $this->reportName = 'report_' . $dateWithExtension;

        if (empty($this->domain) === false) {
            $domain = parse_url($this->domain, PHP_URL_HOST);
            if (empty($domain) === false) {
                $this->reportName = str_replace('report_', $domain . '_',  $this->reportName);
            }
        }
    }

    /**
     * Array sorting by keys
     * http://stackoverflow.com/a/348418
     *
     * @param array $array
     * @param array $orderArray
     * @return array
     */
    private function sortArrayByArray(array $array, array $orderArray): array
    {
        $ordered = [];
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

    /**
     * @return string
     */
    private function generateTable(): string
    {
        $thead = '';
        $tbody = '';

        $thead .= PHP_EOL . '<tr>' . PHP_EOL . $this->generateCell($this->params, 'th') . '</tr>';

        foreach ($this->report as $records) {
            $sortRecords = $this->sortArrayByArray($records, array_keys($this->params));
            $tbody .= PHP_EOL . '<tr>' . PHP_EOL . $this->generateCell($sortRecords, 'td') . '</tr>';
        }

        return '<table>' . PHP_EOL . '<thead>' . $thead . '</thead>' . PHP_EOL . '<tbody>' . $tbody . PHP_EOL . '</tbody>' . PHP_EOL . '</table>';
    }

    /**
     * @param array $records
     * @param string $type
     * @return string
     */
    private function generateCell(array $records, string $type = 'td'): string
    {
        $result = '';
        foreach ($records as $_param => &$record) {
            if ($_param === 'href' && $type !== 'th') {
                $record = '<a href="' . $record . '" target="_blank">' . $record . '</a>';
            }
            $result .= '<' . $type . '> ' . $record . '  </' . $type . '>' . PHP_EOL;
        }
        return $result;
    }

    /**
     * @return string
     */
    private function generateTemplate(): string
    {
        return <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{title}</title>
    <style type="text/css">
        * {margin: 0; padding: 0}
        html, body {width: 100%; height: 100%}
        body {font-size: 15px; display: flex; align-items: center; justify-content: center;}
        a {text-decoration: none}
        a:hover {text-decoration: underline}
        table {border: solid 1px silver; width: 800px; margin: auto;}
        table th {border: solid 1px silver; padding: 5px;}
        table thead tr  {background: rgba(0, 0, 0, 0.05)}
        table tbody tr:hover {background: rgba(0, 0, 0, 0.02)}
        table td:first-child {text-align: left;}
        table td {padding: 5px; border: solid 1px silver; text-align: center}
    </style>
</head>
<body>

    {content}

</body>
</html>
EOD;
    }

}
