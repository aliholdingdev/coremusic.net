<?php

declare(strict_types=1);

namespace Media;

/**
 * MediaEnv — medya HTTP yüzeyinin env anahtarları (fail-safe okuma).
 *
 * Kural (Faz 2, tasarım maddesi 8): env anahtarı `getenv` ile okunur; eksikse
 * fail-safe. **Sessiz/varsayılan SECRET YOK** — hiçbir kimlik bilgisi, şema adı
 * veya yol kod gömmez; eksik yapılandırma erişimi reddeder (fail-closed).
 *
 * Okuma sırası repo geleneğidir (`MySqlCatalogRepository::getEnv:169-173`):
 * `$_ENV` (EnvParser::loadIntoEnv yazar) → `getenv` → varsayılan.
 *
 * | Anahtar | Varsayılan | Kullanım |
 * |---|---|---|
 * | `MEDIA_ROOT` | yok → `null` | medya kökü; `null` → yol çözümü imkânsız → 403/500 (asla 200) |
 * | `MEDIA_PORT` | `5000` | ilan edilen servis portu (ADR-039; 6000 HLS/transcode kapsam dışı) |
 * | `DB_MEDIACATALOG_NAME` | yok | ConnectionFactory `mediacatalog` şeması (`media_catalog` indeksi) |
 * | `DB_MEDIA_NAME` | yok | ConnectionFactory `media` şeması (`coremusic_media` → media_access/media_audit) |
 *
 * ⚠️ VERIFICATION REQUIRED (raporlandı): `DB_MEDIA_NAME` aynı zamanda
 * `MySqlCatalogRepository:51`'de katalog yazıcısının şema varsayılanını besliyor
 * (`getEnv('DB_MEDIA_NAME', 'media_catalog')`) — iki farklı anlam, koordinasyon
 * maddesi olarak raporlanır; bu dosya tasarımdaki bağlamı izler.
 */
final class MediaEnv
{
    /** ADR-039: bu yüzeyin ilan edilen portu. */
    public const VARSAYILAN_PORT = 5000;

    /** ADR-039: HLS/transcode portu — Faz 2 kapsamı DIŞI (6000). */
    public const HLS_PORT = 6000;

    /** ConnectionFactory kayıt anahtarı: `media` → env `DB_MEDIA_NAME`. */
    public const MEDIA_DB = 'media';

    /** ConnectionFactory kayıt anahtarı: `mediacatalog` → env `DB_MEDIACATALOG_NAME`. */
    public const CATALOG_DB = 'mediacatalog';

    /**
     * Medya kökü — yoksa/bos ise `null` (fail-closed; sessiz varsayılan YOK).
     */
    public static function root(): ?string
    {
        $deger = self::oku('MEDIA_ROOT');
        if ($deger === null || $deger === '') {
            return null;
        }

        return $deger;
    }

    /**
     * İlan edilen servis portu — sayısal değilse/grenk dışıysa fail-safe 5000.
     */
    public static function port(): int
    {
        $deger = self::oku('MEDIA_PORT');
        if ($deger === null || $deger === '' || !ctype_digit($deger)) {
            return self::VARSAYILAN_PORT;
        }

        $port = (int) $deger;
        if ($port < 1 || $port > 65535) {
            return self::VARSAYILAN_PORT;
        }

        return $port;
    }

    /**
     * `media_catalog` (varlık indeksi) bağlantı anahtarı.
     *
     * `DB_MEDIACATALOG_NAME` tanımlı değilse `media` anahtarına düşer
     * (fallback); o şemada `asset` tablosu yoksa sorgu hatası → fail-closed.
     */
    public static function catalogDbKey(): string
    {
        $ad = self::oku('DB_MEDIACATALOG_NAME');

        return ($ad !== null && $ad !== '') ? self::CATALOG_DB : self::MEDIA_DB;
    }

    /**
     * `getenv` öncelikli ortam okuma: `$_ENV` → `getenv` → `null`.
     * Değişken yoksa `null` (fail-safe; boş string de `null` sayılmaz — çağrı
     * kendisi boş kontrolü yapar).
     */
    private static function oku(string $anahtar): ?string
    {
        if (isset($_ENV[$anahtar])) {
            return (string) $_ENV[$anahtar];
        }
        $deger = getenv($anahtar);

        return $deger !== false ? (string) $deger : null;
    }
}