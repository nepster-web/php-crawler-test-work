<?php

namespace App\Library\Crawler\Helper;

/**
 * Class UrlHelper
 *
 * @package App\Library\Crawler\Helper
 */
class UrlHelper
{
    /**
     * @param string $href
     * @param string $currentUrl
     * @return null|string
     */
    public static function buildUrl(string $href, string $currentUrl): ?string
    {
        if (substr($href, 0, 2) === '//' || substr($href, 0, 1) === '#') {
            return null;
        }

        $parseCurrentUrl = parse_url($currentUrl);
        $domainURL = $parseCurrentUrl['scheme'] . '://' . $parseCurrentUrl['host'] . '/';
        $parts = parse_url($href);

        if (!is_array($parts)) {
            return null;
        }

        $isExternal = false;

        if (
            isset($parts['scheme']) &&
            (
                $parts['scheme'] === 'tel' ||
                $parts['scheme'] === 'mailto' ||
                $parts['scheme'] === 'skype' ||
                $parts['scheme'] === 'javascript'
            )
        ) {
            return null;
        }

        if (isset($parts['host'])) {
            $isExternal = true;
        }

        if (isset($parts['path'])) {
            if (!empty($parts['path']) && $parts['path'][0] === '/') {
                if (mb_strlen($parts['path']) > 1) {
                    $parts['path'] = substr($parts['path'], 1);
                } else {
                    return $domainURL;
                }
            } else {
                if (substr($parts['path'], 0, 2) === './') {
                    $parts['path'] = substr($parts['path'], 2);
                } else {
                    if (substr($parts['path'], 0, 3) === '../') {
                        $explodeParseCurrentUrl = explode('/', ltrim($parseCurrentUrl['path'], '/'));
                        $explodeParseCurrentUrl = array_filter(array_reverse($explodeParseCurrentUrl));
                        $countS = substr_count($parts['path'], '../');
                        array_splice($explodeParseCurrentUrl, 0, $countS);

                        return $domainURL . implode(
                                '/',
                                array_reverse($explodeParseCurrentUrl)
                            ) . '/' . str_replace('../', '', $parts['path']);
                    }
                }
            }
        }

        if ($isExternal === false) {
            if (isset($parts['host']) === false) {
                return $domainURL . self::unParseUrl($parts);
            }

            return $currentUrl . '/' . self::unParseUrl($parts);
        }

        if (isset($parts['host'])) {
            $parts['host'] = rtrim($parts['host'], '/') . '/';
        }

        return self::unParseUrl($parts);
    }

    /**
     * @param array $parsedUrl
     * @return string
     */
    public static function unParseUrl(array $parsedUrl): string
    {
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * @param string $url
     * @return null|string
     */
    public static function getDomain(string $url): ?string
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        return null;
    }
}