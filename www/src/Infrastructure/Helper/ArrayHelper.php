<?php

namespace App\Infrastructure\Helper;

/**
 * Class ArrayHelper
 *
 * @package App\Infrastructure\Helper
 */
class ArrayHelper
{
    /**
     * Algorithm for sorting a multidimensional array
     * http://stackoverflow.com/a/19454643
     *
     * @param array $array
     * @param string $orderByKey
     * @param bool $orderByDesc
     */
    public static function arrayOrderByKey(array &$array, string $orderByKey, $orderByDesc = false): void
    {
        usort($array, function (array $item1, array $item2) use ($orderByKey, $orderByDesc): int {
            if ($item1[$orderByKey] === $item2[$orderByKey]) {
                return 0;
            }
            if ($orderByDesc) {
                return $item1[$orderByKey] < $item2[$orderByKey] ? 1 : -1;
            }
            return $item1[$orderByKey] > $item2[$orderByKey] ? 1 : -1;
        });
    }
}
