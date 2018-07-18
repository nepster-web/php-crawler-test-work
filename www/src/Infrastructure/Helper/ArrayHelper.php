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
    public static function sortAssociativeArrayByKey(array &$array, string $orderByKey, $orderByDesc = false): void
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

    /**
     * Array sorting by keys
     * http://stackoverflow.com/a/348418
     *
     * @param array $array
     * @param array $orderArray
     */
    public static function sortAssociativeArrayByArray(array &$array, array $orderArray): void
    {
        $result = $array;
        $ordered = [];
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $result)) {
                $ordered[$key] = $result[$key];
                unset($result[$key]);
            }
        }
        $array = $ordered + $result;
    }

}
