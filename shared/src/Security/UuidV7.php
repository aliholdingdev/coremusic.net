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

        // 2-byte clock_seq (version + variant)
        $clockSeq = random_bytes(2);
        $clockSeq[0] = chr((ord($clockSeq[0]) & 0x0F) | 0x70); // version 7
        $clockSeq[1] = chr((ord($clockSeq[1]) & 0x3F) | 0x80); // variant 1

        // 8-byte random
        return $time . $clockSeq . random_bytes(8);
    }

    /**
     * UUID v7 hex string olarak üret (display/compare için).
     */
    public static function generateHex(): string
    {
        return bin2hex(self::generateBinary());
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
     */
    public static function toBinary(string $hex): string
    {
        return hex2bin(str_replace('-', '', $hex));
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
