<?php

namespace App;

use Exception;
use InvalidArgumentException;
use App\Infrastructure\Contract\Crawler;

/**
 * CliApplication
 *
 * @package App
 */
class CliApplication
{
    /**
     * Incoming parameters
     *
     * @var array
     */
    private $params = [
        'url' => null,
        'depth' => 5
    ];

    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * CliApplication constructor.
     *
     * @param Crawler $crawler
     * @throws Exception
     */
    public function __construct(Crawler $crawler)
    {
        if (php_sapi_name() !== "cli") {
            throw new Exception('The application must be running in CLI mode.');
        }

        $params = $this->getParams();
        $this->params = $this->setParams($params);

        $this->crawler = $crawler;
    }

    /**
     * Run
     *
     * @throws Exception
     */
    public function run(): void
    {
        if (empty(trim($this->params['url']))) {
            throw new InvalidArgumentException('Param "URL" must be set for the parsing running.');
        }

        if (filter_var($this->params['url'], FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Param "URL" with value "' . $this->params['url'] . '" does\'t a valid URL.');
        }

        if (ctype_digit(strval($this->params['depth'])) === false) {
            throw new InvalidArgumentException('Param "DEPTH" must be integer.');
        }

        $this->crawler->crawl(
            $this->params['url'],
            $this->params['depth']
        );
    }

    /**
     * @return array
     */
    private function getParams(): array
    {
        $shortOpts = "";
        $longOpts = [];

        foreach ($this->params as $param => &$value) {
            $shortOpts .= $param[0] . '::';
            $longOpts[] = $param . '::';
        }

        return getopt($shortOpts, $longOpts);
    }

    /**
     * @param array $params
     * @return array
     */
    private function setParams(array $params): array
    {
        $result = [];

        foreach ($this->params as $param => &$defaultValue) {
            if (empty($result[$param])) {
                $result[$param] = $defaultValue ? $defaultValue : null;
            }
            foreach ($params as $_param => &$_value) {
                if ($param === $_param) {
                    $result[$param] = $_value;
                    continue;
                }
                if ($param[0] === $_param) {
                    $result[$param] = $_value;
                    continue;
                }
            }
        }

        return $result;
    }
}