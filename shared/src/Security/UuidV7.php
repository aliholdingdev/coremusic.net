<?php declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * UUID v7 Generator
 *
 * RFC 9562 uyumlu UUID v7 üretimi.
 * 48-bit timestamp (ms) + 2-bit variant + 4-bit version + 62-bit random.
 * DB: BINARY(16) olarak saklanır.
 */
final class UuidV7
{
    /**
     * UUID v7 binary(16) olarak üret.
     */
    public static function generateBinary(): string
    {
        $ms = (int)(microtime(true) * 1000);

        // 6-byte timestamp (48 bit)
        $time = pack('N', ($ms >> 16) & 0xFFFFFFFF)
              . pack('n', $ms & 0xFFFF);

        // 2-byte rand_a (version nibble = byte 6 üst 4 bit)
        $randA = random_bytes(2);
        $randA[0] = chr((ord($randA[0]) & 0x0F) | 0x70); // version 7

        // 8-byte rand_b (variant = byte 8 üst 2 bit, RFC 9562: 10xx)
        $randB = random_bytes(8);
        $randB[0] = chr((ord($randB[0]) & 0x3F) | 0x80); // variant 10xx

        return $time . $randA . $randB;
    }

    /**
     * UUID v7 hex string olarak üret (display/compare için).
     */
    public static function generateHex(): string
    {
        return bin2hex(self::generateBinary());
    }

    /**
     * UUID v7 formatted string (8-4-4-4-12) — varsayılan üretici.
     */
    public static function generate(): string
    {
        return self::generateFormatted();
    }

    /**
     * UUID v7 formatted string (8-4-4-4-12).
     */
    public static function generateFormatted(): string
    {
        $hex = self::generateHex();
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    /**
     * Binary UUID'yi hex string'e çevir.
     */
    public static function toHex(string $binary): string
    {
        return bin2hex($binary);
    }

    /**
     * Hex string'i binary UUID'ye çevir.
     *
     * @throws \InvalidArgumentException  Geçersiz hex string
     */
    public static function toBinary(string $hex): string
    {
        $clean = str_replace('-', '', $hex);
        if (!ctype_xdigit($clean) || strlen($clean) !== 32) {
            throw new \InvalidArgumentException('Invalid UUID hex: must be exactly 32 hex characters');
        }
        return hex2bin($clean);
    }

    /**
     * Geçerli bir UUID binary(16) mi?
     */
    public static function isValidBinary(string $value): bool
    {
        return strlen($value) === 16;
    }

    /**
     * Geçerli bir UUID hex string mi?
     */
    public static function isValidHex(string $value): bool
    {
        $clean = str_replace('-', '', $value);
        return strlen($clean) === 32 && ctype_xdigit($clean);
    }
}
