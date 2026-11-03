<?php

declare(strict_types=1);

namespace Media;

/**
 * CatalogValidator — Katalog verisi doğrulama kuralları.
 *
 * Tablo allowlist, hash deseni, unique constraint guard'ları gibi
 * şema/doğrulama kurallarını merkezi tutar.
 */
final class CatalogValidator
{
    /** MySQL'e yazılan tablolar (ADR-002: dinamik tanımlayıcı yalnız allowlist'ten). */
    public const TABLO_ALLOWLIST = ['asset_tag', 'variant', 'asset', 'album', 'artist', 'koleksiyon'];

    /** Küçük harf hex sha256 (media.schema.json $defs/sha256) — bölmeli (`~...~`). */
    private const HASH_DESEN = '~^[a-f0-9]{64}$~';

    /**
     * Tablo adının allowlist'te olup olmadığını kontrol et.
     *
     * @throws \InvalidArgumentException Tablo allowlist'te değilse
     */
    public static function validateTable(string $tablo): void
    {
        if (!in_array($tablo, self::TABLO_ALLOWLIST, true)) {
            throw new \InvalidArgumentException('Tablo allowlist disi: ' . $tablo);
        }
    }

    /**
     * SHA256 hash desenini doğrula (küçük harf hex, 64 karakter).
     */
    public static function isValidSha256(string $hash): bool
    {
        return preg_match(self::HASH_DESEN, $hash) === 1;
    }
}