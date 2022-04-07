<?php

declare(strict_types=1);

namespace App\Util;

class StrUtil
{
    public static function toCamelCase(string $str): string
    {
        $arr = explode('_', $str);
        $camelCase = "";
        foreach ($arr as $key => $value) {
            if ($key != 0) {
                $value[0] = strtoupper($value[0]);
            }
            $camelCase .= $value;
        }
        return $camelCase;
    }

    public static function withCamelCase($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = self::withCamelCase($value);
                }
                if (is_string($key)) {
                    $camelCaseKey = self::toCamelCase($key);
                    if ($key != $camelCaseKey) {
                        $data[$camelCaseKey] = $value;
                        unset($data[$key]);
                    } else {
                        $data[$key] = $value;
                    }
                } else {
                    $data[$key] = $value;
                }
            }
        } elseif (is_string($data)) {
            return self::toCamelCase($data);
        }
        return $data;
    }

    public static function toSnakeCase(string $str): string
    {
        $l = strlen($str);
        $snakeCase = "";
        for ($i = 0; $i < $l; $i++) {
            $ascii = ord($str[$i]);
            if ($ascii > 64 && $ascii < 91) {
                $snakeCase .= chr($ascii + 32);
                if ($l - 1 !== $i) {
                    $snakeCase .= '_';
                }
            } else {
                $snakeCase .= $str[$i];
            }
        }
        return $snakeCase;
    }

    public static function withSnakeCase($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = self::withSnakeCase($value);
                }
                if (is_string($key)) {
                    $snakeCaseKey = self::toSnakeCase($key);
                    if ($key != $snakeCaseKey) {
                        $data[$snakeCaseKey] = $value;
                        unset($data[$key]);
                    } else {
                        $data[$key] = $value;
                    }
                } else {
                    $data[$key] = $value;
                }
            }
        } elseif (is_string($data)) {
            return self::toSnakeCase($data);
        }
        return $data;
    }
}
