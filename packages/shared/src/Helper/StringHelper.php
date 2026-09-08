<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Helper;

class StringHelper
{
    public static function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }

    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
    }

    public static function camelCase(string $text): string
    {
        return lcfirst(self::studlyCase($text));
    }

    public static function studlyCase(string $text): string
    {
        $text = str_replace(['-', '_'], ' ', $text);
        $text = ucwords($text);
        return str_replace(' ', '', $text);
    }

    public static function snakeCase(string $text): string
    {
        $text = preg_replace('/[A-Z]/', '_$0', $text);
        $text = strtolower(trim($text, '_'));
        return str_replace('-', '_', $text);
    }

    public static function kebabCase(string $text): string
    {
        return str_replace('_', '-', self::snakeCase($text));
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return mb_strpos($haystack, $needle, 0, 'UTF-8') !== false;
    }

    public static function startWith(string $text, string $prefix): bool
    {
        return str_starts_with($text, $prefix);
    }

    public static function endWith(string $text, string $suffix): bool
    {
        return str_ends_with($text, $suffix);
    }

    public static function random(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($chars) - 1;
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }
        return $result;
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
