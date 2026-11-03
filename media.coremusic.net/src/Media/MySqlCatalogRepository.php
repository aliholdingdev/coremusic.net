<?php

declare(strict_types=1);

namespace Media;

use CoreMusic\Database\ConnectionFactory;
use CoreMusic\Database\DatabaseException;
use CoreMusic\Contracts\Database\ISqlDatabaseManager;

/**
 * MySqlCatalogRepository — MySQL `media_catalog` türetilmiş indeks yazıcı.
 *
 * ADR-002: yalnız PDO prepared statement · `SELECT *` YOK · ORM YOK.
 * Herhangi bir satır hatası (FK, teklik) o satırı atlar ve uyarı döner —
 * tarama JSONL ile zaten başarılı sayılır. `asset` tablosunda D-7 UK'ları
 * (uq_asset_album_sira · uk_asset_slug) için "ilk kazanır" guard'ı çalışır:
 * çakışan hücre yazılmaz, `slug-tekillik`/`sira-tekillik` uyarısı döner.
 *
 * MySQL (`media_catalog`) OPSİYONELDİR: `pdo_mysql` yoksa veya bağlanılamazsa
 * tek bir uyarı ile JSONL ile devam edilir — çökme YOK.
 */
final class MySqlCatalogRepository
{
    /**
     * MySQL `media_catalog` türetilmiş indeksine yazar (opsiyonel).
     *
     * Hücre anahtarları (`tip`·`veri`·`yol`) üretim tarafında garanti edilir;
     * yine de tek tek defansif okunur (bozuk tek satır tüm yığını yürümez —
     * satır atlanır, JSONL yazımı sürer).
     *
     * @param list<array<string, mixed>> $hucreler
     * @param bool                       $rebuild true → varlık tablolarını boşaltıp baştan doldurur
     *
     * @return array{baglandi: bool, yazildi: int, uyari: list<string>}
     */
    public static function yaz(array $hucreler, bool $rebuild = false): array
    {
        if (!class_exists(\PDO::class)) {
            return ['baglandi' => false, 'yazildi' => 0, 'uyari' => ['PDO yok — MySQL atlandi, JSONL ile devam']];
        }
        if (!in_array('mysql', \PDO::getAvailableDrivers(), true)) {
            return ['baglandi' => false, 'yazildi' => 0, 'uyari' => ['pdo_mysql yok — MySQL atlandi, JSONL ile devam']];
        }

        // Use shared ConnectionFactory for connection management
        try {
            $factory = ConnectionFactory::getInstance();
            $key = 'media_catalog';

            $manager = $factory->getOrRegister(
                $key,
                $factory->providerFor($key),
                self::getEnv('DB_HOST', 'localhost'),
                self::getEnv('DB_MEDIA_NAME', 'media_catalog'),
                self::getEnv('DB_USER', ''),
                self::getEnv('DB_PASSWORD', ''),
                (int) self::getEnv('DB_PORT', '3306'),
                self::getEnv('DB_CHARSET', 'utf8mb4')
            );

            // getOrRegister birleşik döner (IDatabaseManager|ISql|INoSql);
            // PDO yalnız SQL manager'da → fail-closed: PDO olmayan yonetici
            // bu yuzeyden reddedilir, JSONL ile devam edilir.
            if (!$manager instanceof ISqlDatabaseManager) {
                return ['baglandi' => false, 'yazildi' => 0,
                    'uyari' => ['Yonetici SQL degil (PDO yok) — MySQL atlandi, JSONL ile devam']];
            }
            $pdo = $manager->getPdo();

        } catch (DatabaseException $e) {
            return ['baglandi' => false, 'yazildi' => 0,
                'uyari' => ['MySQL baglanilamadi (' . $e->getMessage() . ') — JSONL ile devam']];
        } catch (\Throwable $e) {
            return ['baglandi' => false, 'yazildi' => 0,
                'uyari' => ['MySQL baglanilamadi (' . $e->getMessage() . ') — JSONL ile devam']];
        }

        $uyari = [];
        $yazildi = 0;
        try {
            $pdo->beginTransaction();
            if ($rebuild) {
                // FK sırası: çocuklar önce. Tablo adları CatalogValidator::TABLO_ALLOWLIST'ten gelir
                // (kullanıcı girdisi DEĞİL — ADR-002: dinamik tanımlayıcı allowlist).
                foreach (CatalogValidator::TABLO_ALLOWLIST as $tablo) {
                    $pdo->prepare('DELETE FROM ' . $tablo)->execute();
                }
            }

            // D-7 UK guard tabanı: asset tablosundaki mevcut anahtarlar (rebuild'de
            // DELETE sonrası boş → yalnız toplu-içi çakışma; incremental'de mevcut
            // satırlar da haritada → eski kaydın üzerine yazma (takeover) engellenir).
            $slugHarita = [];
            $siraHarita = [];
            if (!$rebuild) {
                // ADR-002: prepared statement — ham query() YOK.
                $st = $pdo->prepare('SELECT slug, dosya_yolu, album_id, sira FROM asset');
                $st->execute();
                foreach ($st->fetchAll(\PDO::FETCH_ASSOC) as $mevcut) {
                    $slugHarita[(string) $mevcut['slug']] = (string) $mevcut['dosya_yolu'];
                    if ($mevcut['album_id'] !== null) {
                        $siraHarita[$mevcut['album_id'] . '|' . $mevcut['sira']] = (string) $mevcut['dosya_yolu'];
                    }
                }
            }

            foreach ($hucreler as $hucre) {
                $tip = (string) ($hucre['tip'] ?? '');
                $veri = is_array($hucre['veri'] ?? null) ? $hucre['veri'] : [];
                $yol = (string) ($hucre['yol'] ?? '');

                [$tablo, $sutunlar, $pk] = match ($tip) {
                    'artist' => ['artist', CatalogTransformer::transformArtist($veri), ['id']],
                    'album' => ['album', CatalogTransformer::transformAlbum($veri), ['id']],
                    'koleksiyon' => ['koleksiyon', CatalogTransformer::transformKoleksiyon($veri), ['id']],
                    'meta' => ['asset', CatalogTransformer::transformAsset($veri, $yol), ['id']],
                    default => [null, null, []],
                };
                if ($tablo === null || $sutunlar === null) {
                    $uyari[] = $yol . ' => DB satiri hazirlanamadi (alan eksik), atlandi';
                    continue;
                }

                // D-7 guard: slug / (album_id, sira) tekilliği — ilk kazanır, çakışan
                // hücre yazılmaz (ODKU sessiz birleştirme yapmasın diye).
                if ($tablo === 'asset') {
                    $slugDeger = (string) ($sutunlar['slug'] ?? '');
                    if (isset($slugHarita[$slugDeger]) && $slugHarita[$slugDeger] !== $yol) {
                        $uyari[] = 'slug-tekillik: "' . $slugDeger . '" zaten "'
                            . $slugHarita[$slugDeger] . '" kaydinda, atlandi';
                        continue;
                    }
                    $albumDeger = $sutunlar['album_id'] ?? null;
                    if ($albumDeger !== null && $albumDeger !== '') {
                        $siraAnahtar = $albumDeger . '|' . ($sutunlar['sira'] ?? '');
                        if (isset($siraHarita[$siraAnahtar]) && $siraHarita[$siraAnahtar] !== $yol) {
                            $uyari[] = 'sira-tekillik: "' . $siraAnahtar . '" zaten "'
                                . $siraHarita[$siraAnahtar] . '" kaydinda, atlandi';
                            continue;
                        }
                    }
                }

                try {
                    $st = $pdo->prepare(self::upsertSql($tablo, array_keys($sutunlar), $pk));
                    $st->execute($sutunlar);
                    $yazildi++;
                    if ($tablo === 'asset') {
                        $slugHarita[(string) ($sutunlar['slug'] ?? '')] = $yol;
                        if (($sutunlar['album_id'] ?? null) !== null && $sutunlar['album_id'] !== '') {
                            $siraHarita[$sutunlar['album_id'] . '|' . ($sutunlar['sira'] ?? '')] = $yol;
                        }
                    }
                } catch (\Throwable $e) {
                    $uyari[] = $yol . ' => MySQL satir hatasi: ' . $e->getMessage();
                }
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            try {
                $pdo->rollBack();
            } catch (\Throwable) {
                // geri alma da başarısızsa sessiz geç — asıl uyarı aşağıda döner
            }
            $uyari[] = 'MySQL islemi geri alindi: ' . $e->getMessage() . ' — JSONL ile devam';
        }

        return ['baglandi' => true, 'yazildi' => $yazildi, 'uyari' => $uyari];
    }

    /* ------------------------------------------------------------------ *
     * Bağlantı + SQL üretimi
     * ------------------------------------------------------------------ */

    /**
     * Ortam değişkeninden güvenli okuma (repo geleneği: DB_HOST/DB_PORT/DB_USER/DB_PASSWORD).
     */
    private static function getEnv(string $k, string $varsayilan = ''): string
    {
        $value = $_ENV[$k] ?? getenv($k);
        return trim((string) ($value ?: $varsayilan));
    }

    /**
     * `INSERT … AS new ON DUPLICATE KEY UPDATE` (MySQL 8.0.19+ satır takma adı).
     * Sütun adları sabit haritadan gelir; değerler yalnız bind edilir (ADR-002).
     *
     * @param list<string> $sutunlar
     * @param list<string> $pk
     */
    private static function upsertSql(string $tablo, array $sutunlar, array $pk): string
    {
        // ADR-002: dinamik tablo tanımlayıcı yalnız allowlist'ten — savunma katmanı.
        CatalogValidator::validateTable($tablo);

        $liste = implode(', ', $sutunlar);
        $yerTutucu = implode(', ', array_map(static fn (string $k): string => ':' . $k, $sutunlar));
        $guncelle = array_values(array_filter($sutunlar, static fn (string $k): bool => !in_array($k, $pk, true)));
        $atamalar = array_map(static fn (string $k): string => $k . ' = new.' . $k, $guncelle);

        return 'INSERT INTO ' . $tablo . ' (' . $liste . ') VALUES (' . $yerTutucu . ') AS new'
            . ($atamalar === [] ? '' : ' ON DUPLICATE KEY UPDATE ' . implode(', ', $atamalar));
    }
}