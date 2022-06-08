<?php

declare(strict_types=1);

namespace App\Util;

class ArrayUtil
{
    public static function array_unique_ext(array $array, int $sort_flags = \SORT_STRING, array $keys): array
    {
        foreach ($array as $item) {
            if (gettype($item) != 'array') {
                return array_unique($array, $sort_flags);
            }
        }
        $temp = [];
        $new = [];
        foreach ($array as $item) {
            $same = [];
            foreach ($keys as $key) {
                $same[$key] = $item[$key];
            }
            $k = serialize($same);

            if (!isset($temp[$k])) {
                $temp[$k] = true;
                $new[] = $same;
            }
        }

        return $new;
    }
}
