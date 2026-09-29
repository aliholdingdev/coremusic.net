<?php

declare(strict_types=1);

namespace Media;

/**
 * `catalog\catalog.jsonl` yazıcısı — yeniden üretilebilir türetilmiş indeks.
 *
 * SSOT yan JSON dosyalarıdır (`config\media.schema.json` + `meta.json`lar);
 * bu indeks her zaman `bin\scan.php` ile baştan üretilebilir (ADR-092 §5.2
 * adım 6 + `media_catalog.sql` IG notu). DB çökse arşiv yaşar.
 *
 * MySQL (`media_catalog`) OPSİYONELDİR: `pdo_mysql` yoksa veya bağlanılamazsa
 * tek bir uyarı ile JSONL ile devam edilir — çökme YOK.
 *
 * Kayıt şeması (her satır 1 JSON): tip · id · slug · yollar[] · baslik ·
 * sanatci · album · yil · tag · durum · sha256.
 */
final class CatalogWriter
{
    /** JSONL satır bayrakları: Türkçe UTF-8 korunur, `/` kaçışlanmaz. */
    private const JSON_BAYRAK = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    /** Küçük harf hex sha256 (media.schema.json $defs/sha256). */
    private const HASH_DESEN = '^[a-f0-9]{64}$';

    /** @var list<array<string, mixed>> */
    private array $kayitlar = [];

    private bool $dryRun;

    private string $yol;

    public function __construct(string $yol, bool $dryRun = false)
    {
        $this->yol = $yol;
        $this->dryRun = $dryRun;
    }

    /** Proje kökünden `catalog/catalog.jsonl` hedefi. */
    public static function fromProject(?string $kok = null, bool $dryRun = false): self
    {
        $kok ??= dirname(__DIR__, 2);

        return new self($kok . '/catalog/catalog.jsonl', $dryRun);
    }

    public function yol(): string
    {
        return $this->yol;
    }

    public function dryRun(): bool
    {
        return $this->dryRun;
    }

    /** Bir katalog kaydını tampona ekler. */
    public function ekle(array $kayit): void
    {
        $this->kayitlar[] = $kayit;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function kayitlar(): array
    {
        return $this->kayitlar;
    }

    public function satirSayisi(): int
    {
        return count($this->kayitlar);
    }

    /**
     * Tampondaki kayıtları JSONL'e yazar.
     *
     * dry-run → STDOUT'a basar, dosyaya dokunmaz. `--rebuild` → mevcut dosyayı
     * önce siler (eski indeks kalmasın).
     *
     * @return array{yazildi: int, bayt: int, hata: string|null}
     */
    public function yaz(bool $rebuild = false): array
    {
        $satirlar = [];
        foreach ($this->kayitlar as $kayit) {
            $satir = json_encode($kayit, self::JSON_BAYRAK);
            if ($satir === false) {
                continue; // JSON'a çevrilemeyen kayıt atlanır (sayaç düşer, çökme yok)
            }
            $satirlar[] = $satir;
        }
        $metin = $satirlar === [] ? '' : implode("\n", $satirlar) . "\n";

        if ($this->dryRun) {
            if ($metin !== '') {
                echo $metin;
            }

            return ['yazildi' => count($satirlar), 'bayt' => strlen($metin), 'hata' => null];
        }

        $klasor = dirname($this->yol);
        if (!is_dir($klasor) && !@mkdir($klasor, 0775, true) && !is_dir($klasor)) {
            return ['yazildi' => 0, 'bayt' => 0, 'hata' => 'klasor olusturulamadi: ' . $klasor];
        }
        if ($rebuild && is_file($this->yol)) {
            @unlink($this->yol); // --rebuild: eskisini sil, baştan üret
        }
        $bayt = @file_put_contents($this->yol, $metin, LOCK_EX);
        if ($bayt === false) {
            return ['yazildi' => 0, 'bayt' => 0, 'hata' => 'yazilamadi: ' . $this->yol];
        }

        return ['yazildi' => count($satirlar), 'bayt' => (int) $bayt, 'hata' => null];
    }

    /**
     * Mevcut katalogdan `sha256 → yol` kümesi okur (ingest dedupe kaynağı).
     *
     * @return array<string, string> hash => ilk geçtiği katalog yolu
     */
    public static function hashSetiOku(string $dosya): array
    {
        $cikti = [];
        if ($dosya === '' || !is_file($dosya)) {
            return $cikti;
        }
        $satirlar = @file($dosya, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($satirlar === false) {
            return $cikti;
        }
        foreach ($satirlar as $satir) {
            $kayit = json_decode($satir, true);
            if (!is_array($kayit)) {
                continue;
            }
            $hash = $kayit['sha256'] ?? null;
            if (!is_string($hash) || preg_match(self::HASH_DESEN, $hash) !== 1) {
                continue;
            }
            $yollar = is_array($kayit['yollar'] ?? null) ? $kayit['yollar'] : [];
            $cikti[$hash] = (string) ($yollar[0] ?? '');
        }

        return $cikti;
    }

    /**
     * MySQL `media_catalog` türetilmiş indeksine yazar (opsiyonel).
     *
     * ADR-002: yalnız PDO prepared statement · `SELECT *` YOK · ORM YOK.
     * Herhangi bir satır hatası (FK, teklik) o satırı atlar ve uyarı döner —
     * tarama JSONL ile zaten başarılı sayılır.
     *
     * @param list<array{tip: string, veri: array<string, mixed>, yol: string}> $hucreler
     * @param bool                                                                $rebuild true → varlık tablolarını boşaltıp baştan doldurur
     *
     * @return array{baglandi: bool, yazildi: int, uyari: list<string>}
     */
    public static function mysqlYaz(array $hucreler, bool $rebuild = false): array
    {
        if (!class_exists(\PDO::class)) {
            return ['baglandi' => false, 'yazildi' => 0, 'uyari' => ['PDO yok — MySQL atlandi, JSONL ile devam']];
        }
        if (!in_array('mysql', \PDO::getAvailableDrivers(), true)) {
            return ['baglandi' => false, 'yazildi' => 0, 'uyari' => ['pdo_mysql yok — MySQL atlandi, JSONL ile devam']];
        }

        try {
            $pdo = self::baglanti();
        } catch (\Throwable $e) {
            return ['baglandi' => false, 'yazildi' => 0,
                'uyari' => ['MySQL baglanilamadi (' . $e->getMessage() . ') — JSONL ile devam']];
        }

        $uyari = [];
        $yazildi = 0;
        try {
            $pdo->beginTransaction();
            if ($rebuild) {
                // FK sırası: çocuklar önce. Tablo adları sabit listeden gelir (kullanıcı girdisi DEĞİL).
                foreach (['asset_tag', 'variant', 'asset', 'album', 'artist', 'koleksiyon'] as $tablo) {
                    $pdo->prepare('DELETE FROM ' . $tablo)->execute();
                }
            }

            foreach ($hucreler as $hucre) {
                $tip = (string) ($hucre['tip'] ?? '');
                $veri = is_array($hucre['veri'] ?? null) ? $hucre['veri'] : [];
                $yol = (string) ($hucre['yol'] ?? '');

                [$tablo, $sutunlar, $pk] = match ($tip) {
                    'artist' => ['artist', self::artistSatiri($veri), ['id']],
                    'album' => ['album', self::albumSatiri($veri), ['id']],
                    'koleksiyon' => ['koleksiyon', self::koleksiyonSatiri($veri), ['id']],
                    'meta' => ['asset', self::assetSatiri($veri, $yol), ['id']],
                    default => [null, null, []],
                };
                if ($tablo === null || $sutunlar === null) {
                    $uyari[] = $yol . ' => DB satiri hazirlanamadi (alan eksik), atlandi';
                    continue;
                }

                try {
                    $st = $pdo->prepare(self::upsertSql($tablo, array_keys($sutunlar), $pk));
                    $st->execute($sutunlar);
                    $yazildi++;
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
     * Ortam değişkenlerinden bağlantı (repo geleneği: DB_HOST/DB_PORT/DB_USER/DB_PASSWORD).
     *
     * @throws \Throwable bağlantı hatası (çağıran yakalar → uyarı)
     */
    private static function baglanti(): \PDO
    {
        $ortam = static fn (string $k, string $varsayilan = ''): string
            => trim((string) (($_ENV[$k] ?? getenv($k)) ?: $varsayilan));

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $ortam('DB_HOST', 'localhost'),
            (int) $ortam('DB_PORT', '3306'),
            $ortam('DB_MEDIA_NAME', 'media_catalog'),
            $ortam('DB_CHARSET', 'utf8mb4'),
        );

        return new \PDO($dsn, $ortam('DB_USER', ''), $ortam('DB_PASSWORD', ''), [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
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
        $liste = implode(', ', $sutunlar);
        $yerTutucu = implode(', ', array_map(static fn (string $k): string => ':' . $k, $sutunlar));
        $guncelle = array_values(array_filter($sutunlar, static fn (string $k): bool => !in_array($k, $pk, true)));
        $atamalar = array_map(static fn (string $k): string => $k . ' = new.' . $k, $guncelle);

        return 'INSERT INTO ' . $tablo . ' (' . $liste . ') VALUES (' . $yerTutucu . ') AS new'
            . ($atamalar === [] ? '' : ' ON DUPLICATE KEY UPDATE ' . implode(', ', $atamalar));
    }

    /* ------------------------------------------------------------------ *
     * Kayıt → satır dönüşümleri (null = kayıt atlanır)
     * ------------------------------------------------------------------ */

    /** @return array<string, mixed>|null */
    private static function artistSatiri(array $v): ?array
    {
        $k = is_array($v['kimlik'] ?? null) ? $v['kimlik'] : [];
        $temel = [
            'id' => self::s($v['id'] ?? null),
            'slug' => self::s($v['slug'] ?? null),
            'sanatci' => self::s($k['sanatci'] ?? null),
            'tip' => self::s($k['tip'] ?? null),
            'durum' => self::s($v['durum'] ?? null),
            'eklenme' => self::dt($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + ['varsayilan_tag' => self::j($v['varsayilan_tag'] ?? [])];
    }

    /** @return array<string, mixed>|null */
    private static function albumSatiri(array $v): ?array
    {
        $b = static fn (string $alan): array => (is_array($alan) && false) ? [] : []; // okunabilirlik sabitleyici (kullanılmaz)
        unset($b);

        $kimlik = is_array($v['kimlik'] ?? null) ? $v['kimlik'] : [];
        $ust = is_array($v['ust'] ?? null) ? $v['ust'] : [];
        $tarih = is_array($v['tarih'] ?? null) ? $v['tarih'] : [];
        $bicim = is_array($v['bicim'] ?? null) ? $v['bicim'] : [];
        $kapsam = is_array($v['kapsam'] ?? null) ? $v['kapsam'] : [];
        $gorsel = is_array($v['gorsel'] ?? null) ? $v['gorsel'] : [];
        $kredi = is_array($v['kredi'] ?? null) ? $v['kredi'] : [];

        $temel = [
            'id' => self::s($v['id'] ?? null),
            'slug' => self::s($v['slug'] ?? null),
            'album' => self::s($kimlik['album'] ?? null),
            'album_tip' => self::s($kimlik['album_tip'] ?? null),
            'fiziksel' => self::s($bicim['fiziksel'] ?? null),
            'durum' => self::s($v['durum'] ?? null),
            'eklenme' => self::dt($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + [
            'sanatci_id' => self::s($ust['sanatci_id'] ?? null),
            'yil' => self::i($tarih['yil'] ?? null),
            'ay' => self::i($tarih['ay'] ?? null),
            'ulke' => self::s($tarih['ulke'] ?? null),
            'sarki_sayisi' => self::i($kapsam['sarki_sayisi'] ?? null),
            'sure_sn' => self::i($kapsam['sure_sn'] ?? null),
            'cover' => self::s($gorsel['cover'] ?? null),
            'etiket_sirketi' => self::s($kredi['etiket_sirketi'] ?? null),
            'katalog_no' => self::s($kredi['katalog_no'] ?? null),
            'tag' => self::j($v['tag'] ?? []),
            'guncelleme' => self::dt($v['guncelleme'] ?? null),
        ];
    }

    /** @return array<string, mixed>|null */
    private static function koleksiyonSatiri(array $v): ?array
    {
        $temel = [
            'id' => self::s($v['id'] ?? null),
            'slug' => self::s($v['slug'] ?? null),
            'koleksiyon' => self::s($v['koleksiyon'] ?? null),
            'kaynak_tur' => self::s($v['kaynak_tur'] ?? null),
            'durum' => self::s($v['durum'] ?? null),
            'eklenme' => self::dt($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + [
            'sarki_sayisi' => self::i($v['sarki_sayisi'] ?? null),
            'tag' => self::j($v['tag'] ?? []),
        ];
    }

    /**
     * meta.json → `asset` satırı. `$jsonYolu` repo-göreli JSON yoludur;
     * `dosya_yolu` = aynı klasördeki medya dosyası (UNIQUE, DDL tanımı).
     *
     * @return array<string, mixed>|null
     */
    private static function assetSatiri(array $v, string $jsonYolu): ?array
    {
        $kimlik = is_array($v['kimlik'] ?? null) ? $v['kimlik'] : [];
        $ust = is_array($v['ust'] ?? null) ? $v['ust'] : [];
        $teknik = is_array($v['teknik'] ?? null) ? $v['teknik'] : [];
        $gorsel = is_array($v['gorsel'] ?? null) ? $v['gorsel'] : [];
        $muzikal = is_array($v['muzikal'] ?? null) ? $v['muzikal'] : [];

        $dosya = self::s($teknik['dosya'] ?? null);
        $klasor = str_replace('\\', '/', dirname($jsonYolu));
        $temel = [
            'id' => self::s($v['id'] ?? null),
            'tip' => self::s($v['tip'] ?? null),
            'slug' => self::s($kimlik['slug'] ?? null),
            'baslik' => self::s($kimlik['baslik'] ?? null),
            'sira' => self::i($kimlik['sira'] ?? null),
            'dosya_yolu' => $dosya === null ? null : $klasor . '/' . $dosya,
            'dosya' => $dosya,
            'format' => self::s($teknik['format'] ?? null),
            'boyut_bayt' => self::i($teknik['boyut_bayt'] ?? null),
            'sha256' => self::s($teknik['sha256'] ?? null),
            'durum' => self::s($v['durum'] ?? null),
            'eklenme' => self::dt($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + [
            'disk' => self::i($kimlik['disk'] ?? null),
            'album_id' => self::s($ust['album_id'] ?? null),
            'sanatci_id' => self::s($ust['sanatci_id'] ?? null),
            'koleksiyon_id' => null,
            'guncelleme' => self::dt($v['guncelleme'] ?? null),
            'ekleyen' => self::s($v['ekleyen'] ?? null),
            'kapsayici' => self::s($teknik['kapsayici'] ?? null),
            'sure_sn' => self::i($teknik['sure_sn'] ?? null),
            'bitrate' => self::i($teknik['bitrate'] ?? null),
            'kanal' => self::i($teknik['kanal'] ?? null),
            'orneklem' => self::i($teknik['orneklem'] ?? null),
            'derinlik' => self::i($teknik['derinlik'] ?? null),
            'lufs' => self::f($teknik['lufs'] ?? null),
            'peak_db' => self::f($teknik['peak_db'] ?? null),
            'cozunurluk' => self::s($teknik['cozunurluk'] ?? null),
            'fps' => self::f($teknik['fps'] ?? null),
            'video_kodec' => self::s($teknik['video_kodec'] ?? null),
            'ses_kodec' => self::s($teknik['ses_kodec'] ?? null),
            'bitrate_kbps' => self::i($teknik['bitrate_kbps'] ?? null),
            'bpm' => self::i($muzikal['bpm'] ?? null),
            'ton' => self::s($muzikal['ton'] ?? null),
            'enerji' => self::i($muzikal['enerji'] ?? null),
            'poster' => self::s($gorsel['poster'] ?? null),
            'poster_zaman' => self::f($gorsel['poster_zaman'] ?? null),
        ];
    }

    /* ------------------------------------------------------------------ *
     * Ölçü/çeviriciler
     * ------------------------------------------------------------------ */

    private static function s(mixed $d): ?string
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

    private static function i(mixed $d): ?int
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

    private static function f(mixed $d): ?float
    {
        if (is_int($d) || is_float($d)) {
            return (float) $d;
        }
        if (is_string($d) && is_numeric($d)) {
            return (float) $d;
        }

        return null;
    }

    /** JSON sütunu (DDL: `JSON NOT NULL`) — geçersiz UTF-8替补 ile bozulmaz. */
    private static function j(mixed $d): string
    {
        $j = json_encode(is_array($d) ? $d : new \stdClass(), self::JSON_BAYRAK);

        return $j === false ? '{}' : $j;
    }

    /** `2026-09-29T12:34:56+03:00` → `2026-09-29 12:34:56` (MySQL DATETIME). */
    private static function dt(mixed $d): ?string
    {
        if (!is_string($d)) {
            return null;
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})[+-]\d{2}:\d{2}$/', $d, $m) === 1) {
            return $m[1] . ' ' . $m[2];
        }

        return null;
    }
}
