<?php

namespace App\Infrastructure\Report;

use LogicException;
use App\Infrastructure\Helper\ArrayHelper;

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
        'processTime' => 'Page load time',
        'imgLength' => 'Amount images ',
    ];

    /**
     * HtmlReportSaver constructor.
     * @param array $report
     * @param string $reportPath
     * @param string $reportName
     * @param null|string $domain
     */
    public function __construct(
        array $report,
        string $reportPath,
        string $reportName,
        ?string $domain
    ) {
        $this->report = $report;
        $this->reportName = $reportName;
        $this->reportPath = $reportPath;
        $this->domain = $domain;
    }

    /**
     * @throws LogicException
     */
    public function save(): void
    {
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
     * @return string
     */
    private function generateTable(): string
    {
        $tHead = '';
        $tBody = '';

        $tHead .= PHP_EOL . '<tr>' . PHP_EOL . $this->generateCell($this->params, 'th') . '</tr>';

        foreach ($this->report as $records) {
            ArrayHelper::sortAssociativeArrayByArray($records, array_keys($this->params));
            $tBody .= PHP_EOL . '<tr>' . PHP_EOL . $this->generateCell($records, 'td') . '</tr>';
        }

        return
            '<table>' . PHP_EOL .
            '<thead>' . $tHead . '</thead>' . PHP_EOL .
            '<tbody>' . $tBody . '</tbody>' . PHP_EOL .
            '</table>';
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
