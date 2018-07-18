<?php

namespace App\Library\Crawler\Helper;

/**
 * Class HttpHelper
 *
 * @package App\Library\Crawler\Helper
 */
class HttpHelper
{
    /**
     * @param string $url
     * @return bool
     */
    public static function isAvailablePage(string $url): bool
    {
        $headers = @get_headers($url, 1);
        if ($headers && is_array($headers)) {

            if (isset($headers[0])) {
                if (trim(substr($headers[0], -6)) !== '200 OK') {
                    return false;
                }
            }

            if (isset($headers["Content-Type"])) {
                if (stristr($headers["Content-Type"], 'text/html') === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
