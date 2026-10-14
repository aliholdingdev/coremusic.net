---
node_id: K012-services/music-service/music-service-ornekler
node_type: module
status: draft
parent_id: K012-services/music-service
role: ornekler
children: []
depends_on:
  - "K012-services/music-service/music-service-anayasa.md"
  - "K012-services/music-service/music-service-surucu.md"
evidence:
  - "home.coremusic.net/include/Repository/MusicRepository.php — 110 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/include/Component/RecentTracksComponent.php — 217 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php — parçalı bloklar (sabitler, dispatch, isNotModified, fail, libraryPath)"
validation:
  - "Tam dosya blokları satır atlanmadan kopyalandı; parçalı bloklarda künye satır aralığı verildi (2026-10-13)"
open_questions:
  - "HomeSongButton::html üretimi okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "home.php → ComponentLoader akışı okunmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# music-service — Örnekler (kademe-3 · module · rol: ornekler)

**ELI15:** Bu dosya müzik servisinin gerçek PHP kaynağını verir: `MusicRepository.php` (110 satır) ve `RecentTracksComponent.php` (217 satır) **tam dosya** olarak; `MusicStreamHandler.php`'den (379 satır) henüz anayasa/surucu bloklarında yer almayan beş parça (sabitler, dispatch/ctor, isNotModified, fail, libraryPath). Bloklar diskten birebir kopyalanmıştır.

**Sınır (ne DEĞİL):** `parseRange`/`resolvePath`/`sendBody`/`run`/`authorize`/`emitBaseHeaders` blokları anayasa §15-§16 ve surucu §22'de yayımlanmıştır — burada TEKRARLANMAZ (tekrar yerine referans verilir).

**Kod kanıtı kuralı:** Her blok `Dosya: … / Satır: …` künyesiyle verilir.

**Durum:** draft · PHP runtime yok — `php -l` kanıtı ⚠️ VERIFICATION REQUIRED.

## §1 Blok Envanteri (bu dosyadaki bloklar)

| # | Dosya | Blok | Satır | Tür |
|---|---|---|---|---|
| 1 | MusicRepository.php | tam dosya | 1-110 | verbatim |
| 2 | RecentTracksComponent.php | tam dosya | 1-217 | verbatim |
| 3 | MusicStreamHandler.php | sabitler + ctor + dispatch | 23-58 | parçalı |
| 4 | MusicStreamHandler.php | isNotModified | 264-290 | parçalı |
| 5 | MusicStreamHandler.php | fail | 357-367 | parçalı |
| 6 | MusicStreamHandler.php | libraryPath | 371-378 | parçalı |

**Toplam blok satırı:** 110 + 217 + 36 + 27 + 11 + 8 = 409 satır verbatim.

## §2 MusicRepository.php — Tam Dosya Bloğu

**Dosya:** `home.coremusic.net/include/Repository/MusicRepository.php` · **Satır:** 1-110 (tamamı) · **Ölçüm:** 2026-10-13

```php
<?php declare(strict_types=1);

namespace CoreMusic\Home\Repository;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Database\DatabaseManager;

/**
 * MusicRepository — coremusic_musics veri erişimi (stream + component ortak).
 *
 * ADR-002: yalnız DatabaseManager üzerinden prepared statement; SELECT * yok.
 * Bağluk tembel (lazy): PDO ilk sorguda açılır — DB'siz ortamlarda (testler)
 * bağlantı denemesi yapılmaz.
 */
final class MusicRepository
{
    private ?DatabaseManager $db = null;

    private function __construct(
        private readonly string $dbHost,
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPassword,
        private readonly int $dbPort,
        private readonly string $dbCharset,
    ) {
    }

    /** Ortam değişkenlerinden (defined/ENV) repository kurar — bağlantı henüz açılmaz. */
    public static function fromEnvironment(): self
    {
        $name = defined('DB_MUSIC_NAME')
            ? (string)DB_MUSIC_NAME
            : (string)(($_ENV['DB_MUSIC_NAME'] ?? getenv('DB_MUSIC_NAME')) ?: 'coremusic_musics');

        $host = defined('DB_HOST') ? (string)DB_HOST : (string)(($_ENV['DB_HOST'] ?? 'localhost') ?: 'localhost');
        $user = defined('DB_USER') ? (string)DB_USER : (string)(($_ENV['DB_USER'] ?? '') ?: '');
        $pass = defined('DB_PASSWORD') ? (string)DB_PASSWORD : (string)(($_ENV['DB_PASSWORD'] ?? '') ?: '');
        $port = defined('DB_PORT') ? (int)DB_PORT : (int)(($_ENV['DB_PORT'] ?? 3306) ?: 3306);
        $cs   = defined('DB_CHARSET') ? (string)DB_CHARSET : (string)(($_ENV['DB_CHARSET'] ?? '') ?: 'utf8mb4');

        return new self($host, $name, $user, $pass, $port, $cs);
    }

    /**
     * musicIdHex (32 karakter) için birincil dosya satırı.
     *
     * @return array{file_path: string, file_format: string, file_size: int}|null
     * @throws \Throwable bağlantı/sorgu hatası (stream handler 500 üretir — davranış korunur)
     */
    public function findPrimaryFile(string $musicIdHex): ?array
    {
        if (strlen($musicIdHex) !== 32 || !ctype_xdigit($musicIdHex)) {
            return null;
        }

        $sql = 'SELECT f.file_path, f.file_format, f.file_size
                  FROM music_files f
                  JOIN musics m ON m.id = f.music_id
                 WHERE f.music_id = UNHEX(:id)
                   AND m.is_deleted = 0
                   AND f.is_deleted = 0
                 ORDER BY f.is_primary DESC, f.id ASC
                 LIMIT 1';

        $rows = $this->db()->execute($sql, ['id' => $musicIdHex]);

        return $rows === [] ? null : $rows[0];
    }

    /**
     * En son dinlenen parçalar (deterministik sıralama).
     *
     * @return list<array{music_hex: string, title: string, duration_sec: mixed, artist_name: string}>
     *         boş liste = DB yok / hata / kayıt yok → çağıran demo fallback'e düşer
     *
     * @throws \Throwable bağlantı/sorgu hatası (çağıran \Throwable yakalar)
     */
    public function findRecentTracks(): array
    {
        $sql = 'SELECT HEX(m.id)      AS music_hex,
                       m.title         AS title,
                       m.duration_sec  AS duration_sec,
                       a.name          AS artist_name
                  FROM musics m
                  JOIN artists a   ON a.id = m.artist_id
                  JOIN music_files f ON f.music_id = m.id
                 WHERE m.is_deleted = 0
                   AND a.is_deleted = 0
                   AND f.is_deleted = 0
                   AND f.is_primary = 1
                 ORDER BY m.created_at DESC, m.id ASC
                 LIMIT 12';

        return $this->db()->execute($sql);
    }

    /** Tembel bağlantı: ilk sorguda DatabaseManager + PDO oluşturulur. */
    private function db(): DatabaseManager
    {
        return $this->db ??= new DatabaseManager(new DatabaseConfig(
            $this->dbHost,
            $this->dbName,
            $this->dbUser,
            $this->dbPassword,
            $this->dbPort,
            $this->dbCharset,
        ));
    }
}
```

## §3 MusicRepository — Blok Analizi

| Satır | Öğe | Not |
|---|---|---|
| 1 | `declare(strict_types=1)` tek satır + namespace aynı satırda | üç dosyanın ortak stili |
| 17 | `private ?DatabaseManager $db = null` | tek mutable alan; `??=` ile doldurulur |
| 19-27 | private ctor — 6 promoted readonly | factory dışına ctor kapalı |
| 32-34 | `DB_MUSIC_NAME` fallback `coremusic_musics` | tek sabit default |
| 53 | `strlen !== 32 || !ctype_xdigit` | SQL enjeksiyon kapısı — hex dışı girdi sorguya ULAŞMAZ |
| 60 | `UNHEX(:id)` | prepared parametre + dönüşüm SQL içinde |
| 63 | `is_primary DESC, id ASC` | deterministik birincil seçimi |
| 66 | `execute($sql, ['id' => …])` | tek parametre haritası |
| 92 | `created_at DESC, id ASC` | deterministik son-12 sırası |
| 101 | `??=` | kısa devre — ikinci sorguda bağlantı tekrar kurulmaz |

**Yokluk taraması (2026-10-13 tam okuma):** Yazma methodu YOK · `SELECT *` YOK · prepared dışı parametre YOK · `query()` ham çağrısı YOK.

## §4 RecentTracksComponent.php — Tam Dosya Bloğu

**Dosya:** `home.coremusic.net/include/Component/RecentTracksComponent.php` · **Satır:** 1-217 (tamamı) · **Ölçüm:** 2026-10-13

```php
<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\HomeSongButton;
use CoreMusic\Home\Repository\MusicRepository;

/**
 * RecentTracksComponent — Alt satır bölümleri: En Son + Playlistler + Sıradaki (v4.0.0)
 *
 * home.php her varyansta 'recent-tracks''i TEK ÇAĞIRIR; tek view tüm bölümleri üretir:
 *   Wide/4K (1920): En Son (1 sıra × 10 kart) + Playlistler (1 sıra × 6 kart)
 *                   [Sıradaki Şarkılar — mockup'ta YOK (aaa.md ASCII + Figma nodesuz)]
 *   Embedded (1024): .home-layout__bottom--embedded'e 3 doğrudan grid-çocuğu:
 *                   kolon1 En Son (2×2, 4 kart) · kolon2 Playlistler (2×2, 3 kart
 *                   + "Playlist listesini görüntüle" link kartı) · kolon3 Sıradaki
 *                   (1 kart, 186×109 cam panel)
 *
 * Figma/PNG SSOT:
 *   extracted-1024.md — 1639:9904 "Menu En Son Şarkılar" · 1639:9892 "Menu Oynatma Listesi"
 *                       (+1639:9893 link kartı) · 1639:9910 "Sıradaki Şarkı" (185.978×109)
 *   extracted-1920.md — 2856:22290 (En Son kartı, y−220) · 2850:21627 (playlist başlığı,
 *                       fs13 Avalon-Bold ls0.975) · 2890:7922-7927 (6 playlist kartı, y−89)
 *
 * Veri: En Son = coremusic_musics (musics ⋈ artists ⋈ music_files is_primary=1);
 *       DB yoksa PNG demo verisi (testler bu yolu kullanır).
 *       Playlist + Sıradaki = PNG sabit içerik (DB tablosu yok).
 */
final class RecentTracksComponent extends AbstractComponent
{
    /** En Son — wide/4K satırı (PNG 1920: 1 sıra × 10 kart, pitch 184) */
    private const WIDE_CARDS = 10;

    /** En Son — embedded 2×2 grid (Figma 1639:9904 = 4 kart) */
    private const EMBEDDED_CARDS = 4;

    /** Playlist — wide 1 sıra × 6 (PNG 1920: 2890:7922-7927) */
    private const WIDE_PLAYLIST_CARDS = 6;

    /** Playlist — embedded 2×2 = 3 kart + link kartı (Figma 1639:9892/9893) */
    private const EMBEDDED_PLAYLIST_CARDS = 3;

    /** @var list<string> "En Son Dinlenen Şarkılar" mini kart HTML'leri */
    public readonly array $cards;

    /** @var list<string> "Son Oluşturlan & Sistem Taraından Oluşturlan Playlistler" kart HTML'leri */
    public readonly array $playlistCards;

    /** @var array{t: string, album: string, a: string, art: string}|null
     *  Sıradaki Şarkı verisi — yalnız embedded (wide: null, mockup'ta bölüm yok) */
    public readonly ?array $nextCard;

    /** Link kartı ikonu (Figma 1639:9897 — 10×10 album thumb, radius 115) */
    public readonly string $playlistLinkIcon;

    /**
     * @param list<array{t: string, a: string, d: string, art: string, stream?: string}>|null $tracks
     *        override verisi (yalnız "En Son"u besler) — null ise DB, DB yoksa PNG varsayılanları
     * @param MusicRepository|null $repository veri erişimi (ComponentLoader::make injection'ı)
     */
    public function __construct(HomeLayoutVariant $variant, ?array $tracks = null, ?MusicRepository $repository = null)
    {
        parent::__construct($variant);

        $data = $tracks ?? $this->dbTracks($repository) ?? $this->defaultTracks();

        $cards = array_map(
            fn (array $t): string => HomeSongButton::html(
                [
                    't'      => (string)$t['t'],
                    's'      => (string)$t['a'],
                    'art'    => (string)($t['art'] ?? ''),
                    'stream' => (string)($t['stream'] ?? ''),
                ],
                'home-song__mini-card__subtitle',
                (string)$t['d']
            ),
            $data
        );

        $max = $variant->isWide() ? self::WIDE_CARDS : self::EMBEDDED_CARDS;
        $this->cards = array_slice($cards, 0, $max);

        $playlistMax = $variant->isWide() ? self::WIDE_PLAYLIST_CARDS : self::EMBEDDED_PLAYLIST_CARDS;
        $this->playlistCards = array_map(
            fn (array $p): string => HomeSongButton::html(
                [
                    't'      => (string)$p['t'],
                    's'      => (string)$p['a'],
                    'art'    => (string)$p['art'],
                    'stream' => '',
                ],
                'home-song__mini-card__subtitle',
                (string)$p['d']
            ),
            array_slice($this->defaultPlaylists(), 0, $playlistMax)
        );

        $this->nextCard = $variant->isWide() ? null : [
            't'     => 'Göksel - Sevil Neşelen',
            'album' => 'Hayat Rüya Gibi',
            'a'     => 'Göksel',
            'art'   => $this->asset('/Image/res-pink/album-goksel.png'),
        ];

        $this->playlistLinkIcon = $this->asset('/Image/res-pink/album-kursat.png');
    }

    public function key(): string
    {
        return 'recent-tracks';
    }

    /**
     * DB'den son eklenen parçalar (deterministik sıralama).
     *
     * @return list<array{t: string, a: string, d: string, art: string, stream: string}>|null
     *         null = DB yok / hata / boş liste → demo fallback
     */
    private function dbTracks(?MusicRepository $repository): ?array
    {
        if (!defined('DB_HOST')) {
            return null;
        }

        try {
            $rows = ($repository ?? MusicRepository::fromEnvironment())->findRecentTracks();
        } catch (\Throwable) {
            return null;
        }

        if ($rows === []) {
            return null;
        }

        $tracks = [];
        foreach ($rows as $row) {
            $hex = (string)($row['music_hex'] ?? '');
            if ($hex === '') {
                continue;
            }
            $tracks[] = [
                't'      => (string)$row['title'],
                'a'      => (string)$row['artist_name'],
                'd'      => self::formatDuration($row['duration_sec'] ?? null),
                'art'    => '',
                'stream' => '/stream/' . strtolower($hex),
            ];
        }

        return $tracks !== [] ? $tracks : null;
    }

    /** Saniye → HH:MM:SS (NULL → 00:00:00 — import duration_sec yazmaz). */
    private static function formatDuration(mixed $seconds): string
    {
        if (!is_numeric($seconds)) {
            return '00:00:00';
        }
        $s = max(0, (int)$seconds);
        return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
    }

    /**
     * PNG home-1024 node 1639:9904 demo içeriği (4 kart) — 1920 satırı bu
     * 4'lüğün döngüsüyle 10 karta tamamlanır (extracted-1024.md metinleri, birebir).
     *
     * @return list<array{t: string, a: string, d: string, art: string}>
     */
    private function defaultTracks(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        $seed = [
            ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05', 'art' => $art1],
            ['t' => 'Göksel - Kabahat Seni Se...', 'a' => 'Göksel', 'd' => '00:02:05', 'art' => $art1],
            ['t' => 'Barış Manco - Gulpembe', 'a' => 'Barış Manco', 'd' => '00:04:01', 'art' => $art2],
            ['t' => 'Kış Masalı Ensturmental', 'a' => 'Org Dersleri', 'd' => '00:01:10', 'art' => $art2],
        ];

        $tracks = [];
        while (count($tracks) < self::WIDE_CARDS) {
            foreach ($seed as $t) {
                $tracks[] = $t;
                if (count($tracks) >= self::WIDE_CARDS) {
                    break;
                }
            }
        }

        return $tracks;
    }

    /**
     * PNG playlist içeriği — embedded 3 kart (1639:9899/9900/9901) + wide 6 kart
     * (2890:7922-7927, sıra: Supermix → Ruh haline → Yeni Sevilen, sonra tekrar).
     * Metinler extracted-1024/-1920 'metin:' alanlarından birebir (sic).
     *
     * @return list<array{t: string, a: string, d: string, art: string}>
     */
    private function defaultPlaylists(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        $seed = [
            ['t' => "En Sevilen Supermix'im", 'a' => 'Sistem Tarafından Oluşturuldu', 'd' => '00:03:05', 'art' => $art2],
            ['t' => "Ruh haline Göre Günlük mix'im", 'a' => 'Sistem Tarafından Oluşturuldu', 'd' => '01:05:00', 'art' => $art1],
            ['t' => "Yeni Sevilen Türleri Keşfet mix'im", 'a' => 'Sistem Tarafından Oluşturuldu', 'd' => '00:50:15', 'art' => $art2],
        ];

        return array_merge($seed, $seed);
    }
}
```

## §5 MusicStreamHandler — Parça Blokları (tam dosya olmayanlar)

### §5.1 Sabitler + ctor + dispatch — Dosya: MusicStreamHandler.php / Satır: 23-58

```php
    /** Uzantı → Content-Type */
    private const MIME_BY_EXT = [
        'mp3'  => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav'  => 'audio/wav',
        'm4a'  => 'audio/mp4',
        'ogg'  => 'audio/ogg',
        'aac'  => 'audio/aac',
        'dsd'  => 'audio/dsd',
        'mqa'  => 'audio/flac',
    ];

    /** file_format enum → Content-Type (uzantı yoksa fallback) */
    private const MIME_BY_FORMAT = [
        'mp3'  => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav'  => 'audio/wav',
        'aac'  => 'audio/mp4',
        'ogg'  => 'audio/ogg',
        'dsd'  => 'audio/dsd',
        'mqa'  => 'audio/flac',
    ];

    private const CHUNK_BYTES = 1048576; // 1 MiB

    private function __construct(
        private readonly string $libraryPath,
        private readonly MusicRepository $repository,
    ) {
    }

    /** bootstrap.php special-route girişi — yanıt gönderir ve çıkar. */
    public static function dispatch(string $musicIdHex): never
    {
        (new self(self::libraryPath(), MusicRepository::fromEnvironment()))->run(strtolower($musicIdHex));
    }
```

### §5.2 isNotModified — Dosya: MusicStreamHandler.php / Satır: 264-290

```php
    private function isNotModified(string $etag, int $mtime): bool
    {
        $ifNoneMatch = trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
        if ($ifNoneMatch !== '') {
            if ($ifNoneMatch === '*') {
                return true;
            }
            foreach (explode(',', $ifNoneMatch) as $candidate) {
                $candidate = trim($candidate);
                $candidate = preg_replace('/^W\//', '', $candidate) ?? $candidate;
                if ($candidate === $etag) {
                    return true;
                }
            }
            return false;
        }

        $ifModifiedSince = (string)($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '');
        if ($ifModifiedSince !== '') {
            $since = strtotime($ifModifiedSince);
            if ($since !== false && $mtime <= $since) {
                return true;
            }
        }

        return false;
    }
```

### §5.3 fail — Dosya: MusicStreamHandler.php / Satır: 357-367

```php
    private function fail(int $status, string $message): never
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: text/plain; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-store');
        }
        echo $message;
        exit;
    }
```

### §5.4 libraryPath — Dosya: MusicStreamHandler.php / Satır: 371-378

```php
    private static function libraryPath(): string
    {
        $path = defined('MUSIC_LIBRARY_PATH')
            ? (string)MUSIC_LIBRARY_PATH
            : (string)(($_ENV['MUSIC_LIBRARY_PATH'] ?? getenv('MUSIC_LIBRARY_PATH')) ?: '');

        return $path !== '' ? $path : 'C:\\Users\\Bayram Ali\\Music';
    }
```

**Gözlem (§5.4):** Son fallback sabit yoldur (`C:\Users\Bayram Ali\Music`, satır 377) — development makinesi yolu; production değeri `.env` içindedir ve vault'a yazılmaz (Guardrail: Secret Yok). Bilinmeyen değer UNKNOWN olarak işaretlidir.

## §6 Blok Bütünlük Kaydı

| Dosya | Blok | Satır | Eksik/Yanlış |
|---|---|---|---|
| MusicRepository.php | §2 | 1-110 | yok — 110/110 |
| RecentTracksComponent.php | §4 | 1-217 | yok — 217/217 |
| MusicStreamHandler.php | §5.1-§5.4 | seçili 82 satır | yok — parçalar bitişik değil, aralık künyeli |

**Doğrulama yöntemi:** 2026-10-13 `read` çıktıları ile birebir karşılaştırma.

## §7 Referanslanan Ama Burada Olmayan Bloklar

| Blok | Nerede | Gerekçe |
|---|---|---|
| `run` (60-113) | surucu §22.1 | sürüş anlatımı |
| `authorize` + `bypassActive` (117-159) | surucu §22.2 | sürüş anlatımı |
| `emitBaseHeaders` (310-329) | surucu §22.3 | sürüş anlatımı |
| `parseRange` (219-262) | anayasa §15.1 | sözleşmeli method |
| `resolvePath` (169-209) | anayasa §15.2 | güvenlik methodu |
| `sendBody` (331-355) | anayasa §15.3 | gövde methodu |
| `findPrimaryFile` SQL (57-64) | anayasa §16.1 | SQL kanıtı |
| `findRecentTracks` SQL (81-93) | anayasa §16.2 | SQL kanıtı |
| `dbTracks` (122-154) | anayasa §16.3 | veri zinciri |

## §8 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| ADR-002 prepared statement kuralı | URL yok — kod dokümanı satır 11 kaynaktır | 2026-10-13 | geçerli |
| Figma node ID'leri (1639:9904 vb.) | URL yok — kod yorumu satır 22-25 kaynaktır | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §9 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | `php -l` 3/3 kanıtı yok | ⚠️ VERIFICATION REQUIRED |
| 2 | `HomeSongButton::html` tam dosyası okunmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | `AbstractComponent::asset` implementasyonu okunmadı | ⚠️ VERIFICATION REQUIRED |
| 4 | `DatabaseManager::execute` imzası okunmadı (shared katman) | ⚠️ VERIFICATION REQUIRED |
| 5 | `php -l` dışına çıkmayan çalıştırma kanıtı (CLI test) | UNKNOWN |

## §10 Değişiklik Kaydı

| Tarih | Değişiklik | Kanan |
|---|---|---|
| 2026-10-13 | İlk yazım — 2 tam dosya + 4 parçalı blok + bütünlük/eksiklik tabloları | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
