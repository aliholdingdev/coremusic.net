<?php

declare(strict_types=1);

namespace Media;

/**
 * ULID üretimi ve doğrulama — Crockford base32 (ADR-092 §6.1 kural 2).
 *
 * Alfabe: `0123456789ABCDEFGHJKMNPQRSTVWXYZ` (I, L, O, U yok) · 26 karakter ·
 * ilk 10 karakter 48-bit milisaniye zaman damgası, son 16 karakter rastgele.
 */
final class Ulid
{
    /** Crockford base32 alfabe (I, L, O, U hariç). */
    public const ALFABE = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    /** Doğrulama deseni (ADR-092 §6.1 kural 2). */
    public const DESEN = '^[0-9A-HJKMNP-TV-Z]{26}$';

    /** Sabit uzunluk. */
    public const UZUNLUK = 26;

    /**
     * Yeni ULID üretir (zaman damgası + rastgele).
     */
    public static function uret(): string
    {
        $zaman = (int) floor(microtime(true) * 1000);
        if ($zaman < 0) {
            $zaman = 0;
        }

        return self::zamanKodla($zaman, 10) . self::rastgeleKodla(16);
    }

    /**
     * `^[0-9A-HJKMNP-TV-Z]{26}$` denetimi (I, L, O, U harfleri yasak).
     */
    public static function isValid(mixed $aday): bool
    {
        if (!is_string($aday) || mb_strlen($aday, 'UTF-8') !== self::UZUNLUK) {
            return false;
        }

        return preg_match(self::DESEN, $aday) === 1;
    }

    /** 48-bit zaman damgasını 10 karaktere kodlar. */
    private static function zamanKodla(int $zaman, int $uzunluk): string
    {
        $cikti = '';
        for ($i = $uzunluk - 1; $i >= 0; $i--) {
            $mod = $zaman % 32;
            $cikti = self::ALFABE[$mod] . $cikti;
            $zaman = intdiv($zaman, 32);
        }

        return $cikti;
    }

    /**
     * $karakter adet ULID karakteri rastgele üretir (16 karakter = 80 bit = 10 bayt).
     */
    private static function rastgeleKodla(int $karakter): string
    {
        $bayt = intdiv($karakter * 5 + 7, 8);
        $ham = random_bytes($bayt);
        $cikti = '';
        $tamppon = 0;
        $bit = 0;
        // strlen bilinçli: $ham ikili dize, bayt uzunluğu ölçülüyor (UTF-8 metin değil).
        for ($i = 0, $n = strlen($ham); $i < $n; $i++) {
            $tamppon = ($tamppon << 8) | ord($ham[$i]);
            $bit += 8;
            while ($bit >= 5) {
                $bit -= 5;
                $cikti .= self::ALFABE[($tamppon >> $bit) & 31];
            }
        }

        return mb_substr($cikti, 0, $karakter, 'UTF-8');
    }
}
