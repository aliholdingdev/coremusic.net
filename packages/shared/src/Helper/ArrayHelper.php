<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Helper;

class ArrayHelper
{
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        return $array[$key] ?? $default;
    }

    public static function has(array $array, string $key): bool
    {
        return array_key_exists($key, $array);
    }

    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    public static function pluck(array $array, string $key): array
    {
        return array_map(fn(array $item) => $item[$key] ?? null, $array);
    }

    public static function flatten(array $array, string $delimiter = '.'): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach (self::flatten($value, $delimiter) as $nestedKey => $nestedValue) {
                    $result["{$key}{$delimiter}{$nestedKey}"] = $nestedValue;
                }
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function groupBy(array $array, string $key): array
    {
        $grouped = [];
        foreach ($array as $item) {
            $groupKey = $item[$key] ?? 'null';
            $grouped[$groupKey][] = $item;
        }
        return $grouped;
    }

    public static function unique(array $array, string $key): array
    {
        $seen = [];
        $result = [];
        foreach ($array as $item) {
            $value = $item[$key] ?? null;
            if (!in_array($value, $seen, true)) {
                $seen[] = $value;
                $result[] = $item;
            }
        }
        return $result;
    }
}
