<?php

namespace src\writers;

use \DOMDocument as DOMDocument;
use \Exception as Exception;

/**
 * HtmlWriter
 *
 * @package src
 */
class HtmlWriter implements \src\interfaces\WriterInterface
{
    /**
     * @var string
     */
    protected $_extension = '.html';

    /**
     * @var string
     */
    protected $_reportsDir = __DIR__ . '/../../reports';

    /**
     * @var array
     */
    protected $_report = [];

    /**
     * @var string
     */
    protected $_reportName;

    /**
     * @var array
     */
    protected $_params = [
        'href' => 'URL Адрес',
        'depth' => 'Уровень вложенности',
        'processTime' => 'Время парсинга',
        'imgLength' => 'Кол-во изображений',
    ];

    /**
     * @return string
     * @param array $report
     */
    public function setReport(array $report)
    {
        $this->_report = $report;
    }

    /**
     * @inheritdoc
     */
    public function getReportName()
    {
        return $this->_reportName;
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $this->generateReportName();

        $report = str_replace([
            '{title}',
            '{content}',
        ], [
            $this->_reportName,
            $this->generateTable(),
        ], $this->generateTemplate());

        $file = $this->_reportsDir . DIRECTORY_SEPARATOR . $this->_reportName;

        if (!file_put_contents($file, $report)) {
            throw new Exception('Unable to write to a file: "' . $file . '"');
        }

        return true;
    }

    /**
     * Сгенерировать имя для файла отчета
     */
    protected function generateReportName()
    {
        $this->_reportName = 'report_' . date('d.m.Y') . $this->_extension;
    }

    /**
     * Сортировка массива по ключам
     * http://stackoverflow.com/a/348418
     *
     * @param array $array
     * @param array $orderArray
     * @return array
     */
    protected function sortArrayByArray(array $array, array $orderArray)
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
    protected function generateTable()
    {
        $thead = '';
        $tbody = '';

        $thead .= PHP_EOL . '<tr>' . PHP_EOL . $this->generateCell($this->_params, 'th') . '</tr>';

        foreach ($this->_report as $records) {
            $sortRecords = $this->sortArrayByArray($records, array_keys($this->_params));
            $tbody .= PHP_EOL . '<tr>' . PHP_EOL . $this->generateCell($sortRecords, 'td') . '</tr>';
        }

        return '<table>' . PHP_EOL . '<thead>' . $thead . '</thead>' . PHP_EOL . '<tbody>' . $tbody . PHP_EOL . '</tbody>' . PHP_EOL . '</table>';
    }

    /**
     * @param array $records
     * @param string $type
     * @return string
     */
    protected function generateCell(array $records, $type = 'td')
    {
        $result = '';
        foreach ($records as $_param => &$record) {
            if ($_param == 'href' && $type != 'th') {
                $record = '<a href="' . $record . '" target="_blank">' . $record . '</a>';
            }
            $result .= '<' . $type . '> ' . $record . '  </' . $type . '>' . PHP_EOL;
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function generateTemplate()
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
        table thead tr  {background: rgba(0, 0, 0, 0.1)}
        table tbody tr:hover {background: rgba(0, 0, 0, 0.1)}
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