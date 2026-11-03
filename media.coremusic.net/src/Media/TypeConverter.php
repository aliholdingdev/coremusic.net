<?php

declare(strict_types=1);

namespace Media;

/**
 * TypeConverter — Veri tipi dönüşüm yardımcıları.
 *
 * JSON/veri kaynaklarından gelen ham değerleri MySQL sütun tiplerine
 * güvenli bir şekilde dönüştürür. Null-güvenli (null in → null out).
 */
final class TypeConverter
{
    /** JSONL satır bayrakları: Türkçe UTF-8 korunur, `/` kaçışlanmaz. */
    private const JSON_BAYRAK = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    /** Küçük harf hex sha256 (media.schema.json $defs/sha256) — bölmeli (`~...~`). */
    private const HASH_DESEN = '~^[a-f0-9]{64}$~';

    /**
     * Değeri string'e dönüştür.
     *
     * @return string|null null gelirse null döner
     */
    public static function toString(mixed $d): ?string
    {
        if (is_string($d)) {
            return $d;
        }
        if (is_int($d) || is_float($d)) {
            return (string) $d;
        }
        if (is_bool($d)) {
            return $d ? '1' : '0';
        }

        return null;
    }

    /**
     * Değeri integer'a dönüştür.
     *
     * @return int|null null veya çevrilemez değer gelirse null döner
     */
    public static function toInt(mixed $d): ?int
    {
        if (is_int($d)) {
            return $d;
        }
        if (is_float($d)) {
            return (int) $d;
        }
        if (is_string($d) && preg_match('/^-?\d+$/', $d) === 1) {
            return (int) $d;
        }

        return null;
    }

    /**
     * Değeri float'a dönüştür.
     *
     * @return float|null null veya çevrilemez değer gelirse null döner
     */
    public static function toFloat(mixed $d): ?float
    {
        if (is_int($d) || is_float($d)) {
            return (float) $d;
        }
        if (is_string($d) && is_numeric($d)) {
            return (float) $d;
        }

        return null;
    }

    /**
     * Değeri JSON string'e dönüştür.
     *
     * Dizi gelirse JSON object/array, null/scalar gelirse '{}'.
     * Geçersiz UTF-8 yerine U+FFFD koyar (bozulmaz).
     */
    public static function toJson(mixed $d): string
    {
        $j = json_encode(is_array($d) ? $d : new \stdClass(), self::JSON_BAYRAK);

        return $j === false ? '{}' : $j;
    }

    /**
     * ISO 8601 datetime string'ini MySQL DATETIME formatına dönüştür.
     *
     * `2026-09-29T12:34:56+03:00` → `2026-09-29 12:34:56`
     *
     * @return string|null Geçersiz format gelirse null döner
     */
    public static function toDateTime(mixed $d): ?string
    {
        if (!is_string($d)) {
            return null;
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})[+-]\d{2}:\d{2}$/', $d, $m) === 1) {
            return $m[1] . ' ' . $m[2];
        }

        return null;
    }

    /**
     * SHA256 hash desenini doğrula (küçük harf hex, 64 karakter).
     */
    public static function isValidSha256(string $hash): bool
    {
        return preg_match(self::HASH_DESEN, $hash) === 1;
    }
}