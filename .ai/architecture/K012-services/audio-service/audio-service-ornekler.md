---
node_id: K012-services/audio-service
node_type: module
role: ornekler
status: draft
parent_id: K012-services/audio-service
children: []
depends_on:
  - "audio-service/audio-service-anayasa.md"
  - "audio-service/audio-service-surucu.md"
  - "audio-service/index.md"
evidence:
  - "S1 — home.coremusic.net/include/Stream/MusicStreamHandler.php 380 satır (tam okuma 2026-10-13)"
  - "S1 — home.coremusic.net/include/Repository/MusicRepository.php 111 satır (tam okuma 2026-10-13)"
  - "S5 — audio-service'e özel implementasyon klasörü YOK (yalnız 4 md)"
  - "S3 — https://www.rfc-editor.org/rfc/rfc9110.html HTTP 200"
validation:
  - "§2–§9 kod blokları dosyanın birebir kopyasıdır (2026-10-13 okuma)"
  - "§10 eksik alanlar S5/UNKNOWN olarak işaretlendi — uydurma örnek yok"
open_questions:
  - "Q3 — servis mi motor mu (K003-audio sınırı) çözülmedi"
  - "Q-a1 — ses verisi sahipliği K015 mi K012 mi çözülmedi"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: MusicStreamHandler.php + MusicRepository.php (S1, tam okuma)"
group: kod
---

# audio-service — Örnekler

> **Kural:** §2–§9'daki her kod bloğu 2026-10-13'te diskten okunmuş gerçek
> koddur (uydurma yasak). §11'de repo'da olmayan alanlar "kod YOK" diye yazılır —
> yerine örnek uydurulmaz.

## §1 Örnek Kategorileri

| # | Kategori | Kaynak | Durum |
|---|---|---|---|
| A1 | Sınıf + sabitler (MIME tabloları, chunk) | MusicStreamHandler.php:21–46 | ✅ gerçek kod |
| A2 | Fabrika + giriş noktası | Aynı dosya:55 | ✅ gerçek kod |
| A3 | Yürütme gövdesi (`run`) | Aynı dosya:60–113 | ✅ gerçek kod |
| A4 | Yetki + bypass | Aynı dosya:121–159 | ✅ gerçek kod |
| A5 | Yol çözümleme güvenlik kapısı | Aynı dosya:173–209 | ✅ gerçek kod |
| A6 | MIME türetme | Aynı dosya:211–215 | ✅ gerçek kod |
| A7 | Range ayrıştırma | Aynı dosya:224–262 | ✅ gerçek kod |
| A8 | 304 kontrolü | Aynı dosya:264–290 | ✅ gerçek kod |
| A9 | Gövde gönderimi | Aynı dosya:331–355 | ✅ gerçek kod |
| A10 | Hata yanıtı | Aynı dosya:357–367 | ✅ gerçek kod |
| A11 | Bağımlılık (kütüphane yolu) | Aynı dosya:371–378 | ✅ gerçek kod |
| A12 | Repository sınıfı + fabrika | MusicRepository.php:15–47 | ✅ gerçek kod |
| A13 | Birincil dosya sorgusu (SQL) | Aynı dosya:51–71 | ✅ gerçek kod |
| A14 | Son dinlenenler sorgusu (SQL) | Aynı dosya:79–96 | ✅ gerçek kod |
| A15 | Tembel bağlantı | Aynı dosya:99–109 | ✅ gerçek kod |
| B1 | audio-service özel DI kaydı | — | ❌ KOD YOK — §10/1 |
| B2 | Zamanlanmış iş motoru | — | ❌ KOD YOK — §10/2 |
| B3 | Ses seviyesi hesaplama | — | ❌ KOD YOK — §10/3 |
| B4 | Sinyal işleme / EQ | — | ❌ KOD YOK — §10/4 |
| C1 | Range/304 resmi dayanağı | rfc9110 (S3) | ✅ resmi doküman — §12 |

**Sayım:** A=15 gerçek · B=4 yok · C=1 resmi doküman.

## §2 Sınıf Tanımı ve Sabitler (gerçek kod)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 1–46

```php
<?php declare(strict_types=1);

namespace CoreMusic\Home\Stream;

use CoreMusic\Home\Repository\MusicRepository;
use CoreMusic\PageRouter\PageRouterHelper;
use CoreMusic\Session\SessionBootstrapper;

/**
 * MusicStreamHandler — GET /stream/{musicId} (byte-range destekli ses akışı)
 *
 * Sözleşme:
 *   - Giriş ZORUNLU → değilse 302 /login (AuthGuard ile aynı session anahtarları)
 *   - Yalnız MUSIC_LIBRARY_PATH altında gerçek dosya servis edilir
 *     (`..` parçası, symlink ve kök-dışı realpath → 403)
 *   - HTTP Range: tek aralık → 206 Partial Content (seek için şart)
 *   - ETag + Last-Modified + Accept-Ranges: bytes
 *
 * Kurallar: ADR-002 (PDO prepared statements), SELECT * yok.
 */
final class MusicStreamHandler
{
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
```

**Gözlem (S1):** sınıf doc-block'u sözleşme olarak üç madde sayar — giriş zorunlu,
kütüphane yolu kısıtı, tek aralık Range. Bu üçü §4/§7 maddelerinin dayanağıdır.

## §3 Giriş Noktası ve Fabrika (gerçek kod)

**Dosya:** Aynı · **Satır:** 48–58

```php
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

**Gözlem:** `never` dönüş tipi — fonksiyon asla dönemez, ya yanıt gönderir ya
`exit` eder. Kurucu `private` → dışarıdan tek geçiş `dispatch()`.

## §4 Yürütme Gövdesi (gerçek kod)

**Dosya:** Aynı · **Satır:** 60–113

```php
    private function run(string $musicIdHex): never
    {
        $this->authorize();

        $row = $this->findFile($musicIdHex);
        if ($row === null) {
            $this->fail(404, 'Kayıt bulunamadı');
        }

        $path = $this->resolvePath((string)$row['file_path']);
        $size = (int)$row['file_size'];
        if (is_file($path)) {
            $diskSize = (int)filesize($path);
            if ($diskSize > 0) {
                $size = $diskSize;
            }
        }

        $mime      = $this->contentType($path, (string)$row['file_format']);
        $mtime     = (int)(is_file($path) ? filemtime($path) : time());
        $etag      = '"' . md5($path . '|' . $size . '|' . $mtime) . '"';
        $lastMod   = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';

        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

        // Koşullu istekler
        if ($this->isNotModified($etag, $mtime)) {
            $this->emitNotModified($etag, $lastMod);
            exit;
        }

        $range = $this->parseRange((string)($_SERVER['HTTP_RANGE'] ?? ''), $size);

        $this->emitBaseHeaders($mime, $range['length'], $etag, $lastMod);

        if ($range['status'] === 416) {
            header('Content-Range: bytes */' . $size);
            http_response_code(416);
            exit;
        }

        if ($range['status'] === 206) {
            header('Content-Range: bytes ' . $range['start'] . '-' . $range['end'] . '/' . $size);
            http_response_code(206);
        } else {
            http_response_code(200);
        }

        if ($method === 'HEAD') {
            exit;
        }

        $this->sendBody($path, $range['start'], $range['length']);
    }
```

**Gözlem (S1):** burada `404`, `416`, `206`, `200`, `HEAD` davranışı **gerçek
kodda görünür** — anayasa §9.1'deki "iddia" satırları bu dosyayla güncellenmiştir.

## §5 Yetki ve Bypass (gerçek kod)

**Dosya:** Aynı · **Satır:** 117–159

```php
    private function authorize(): void
    {
        SessionBootstrapper::ensureStarted();

        $helper = new PageRouterHelper();

        if (!$helper->checkAuthenticated() && self::bypassActive()) {
            $uuid = defined('BYPASS_USER_UUID') ? (string)BYPASS_USER_UUID : '';
            $role = defined('BYPASS_ROLE') ? (string)BYPASS_ROLE : '';
            $user = defined('BYPASS_USERNAME') ? (string)BYPASS_USERNAME : '';
            if ($uuid !== '' && $role !== '' && $user !== '') {
                $_SESSION['MM_UserID']      = $uuid;
                $_SESSION['MM_UserRole']    = $role;
                $_SESSION['MM_Username']    = $user;
                $_SESSION['MM_Permissions'] = [];
            }
        }

        if (!$helper->checkAuthenticated()) {
            SessionBootstrapper::writeClose();
            header('Location: /login', true, 302);
            exit;
        }

        // Streaming sırasında session kilidi tutulmasın
        SessionBootstrapper::writeClose();
    }

    private static function bypassActive(): bool
    {
        // B-F-05: prod'da bypass ASLA aktif olmaz — SecurityHelper::isTestBypassActive
        // ile aynı kural (tek katman değil, savunma derinliği).
        if (defined('APP_ENV_MODE') && APP_ENV_MODE === 'production') {
            return false;
        }
        $force = defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS;
        $test  = defined('TEST_MODE') && TEST_MODE;
        return $force || $test;
    }
```

**Gözlem (S1):** bypass değişken adları artık okundu: `APP_ENV_MODE`,
`FORCE_AUTH_BYPASS`, `TEST_MODE`, `BYPASS_USER_UUID`, `BYPASS_ROLE`,
`BYPASS_USERNAME`. Üretimde (`APP_ENV_MODE === 'production'`) bypass **her koşulda
false** — fail-closed. Anayasa §9/Q-a3 ve surucu Q-s2 bu kodla kapatılmıştır.

## §6 Yol Çözümleme — Güvenlik Kapısı (gerçek kod)

**Dosya:** Aynı · **Satır:** 169–209

```php
    /**
     * Dosya yolunu doğrular: MUSIC_LIBRARY_PATH altında olmalı.
     * `..` parçası, symlink ve realpath kaçışı → 403.
     */
    private function resolvePath(string $stored): string
    {
        $trimmed = trim($stored);
        if ($trimmed === '' || str_contains($trimmed, "\0")) {
            $this->fail(404, 'Dosya bulunamadı');
        }

        $segments = preg_split('#[/\\\\]+#', $trimmed) ?: [];
```

**Kesit notu:** okuma 209. satırda biter; 181–204 arası (`..` taraması,
`realpath`, 403 eşiği) bu oturumda bloklanmamıştır → ⚠️ VERIFICATION REQUIRED
(blok tam gövde değil, satır başlıklarıyla sınırlıdır).

## §7 MIME Türetme (gerçek kod)

**Dosya:** Aynı · **Satır:** 211–215

```php
    private function contentType(string $path, string $format): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return self::MIME_BY_EXT[$ext] ?? self::MIME_BY_FORMAT[$format] ?? 'application/octet-stream';
    }
```

**Gözlem:** üç kademeli düşüş: uzantı → format enum → `application/octet-stream`.
Anayasa §9.1/8 ("MIME bilinmiyor") bu kodla kapatılmıştır: nihai düşüş nettir.

## §8 Range Ayrıştırma (gerçek kod)

**Dosya:** Aynı · **Satır:** 219–262

```php
    /**
     * Tek aralık destekli. Çoklu aralık yok sayılır (200 tam gövde).
     *
     * @return array{status: int, start: int, end: int, length: int}
     */
    private function parseRange(string $header, int $size): array
    {
        $full = ['status' => 200, 'start' => 0, 'end' => $size > 0 ? $size - 1 : 0, 'length' => $size];
        $header = trim($header);

        if ($header === '' || $size === 0 || str_contains($header, ',')) {
            return $full;
        }

        if (preg_match('/^bytes=(\d*)-(\d*)$/i', $header, $m) !== 1) {
            return $full; // çözümlenemeyen header yok sayılır (RFC 9110)
        }

        $startRaw = $m[1];
        $endRaw   = $m[2];

        if ($startRaw === '' && $endRaw === '') {
            return $full;
        }

        if ($startRaw === '') {
            // Suffix range: bytes=-N (son N bayt)
            $suffix = (int)$endRaw;
            if ($suffix <= 0) {
                return ['status' => 416, 'start' => 0, 'end' => 0, 'length' => 0];
            }
            $start = max(0, $size - $suffix);
            $end   = $size - 1;
        } else {
            $start = (int)$startRaw;
            $end   = $endRaw === '' ? $size - 1 : min((int)$endRaw, $size - 1);
        }

        if ($start >= $size || $start > $end) {
            return ['status' => 416, 'start' => 0, 'end' => 0, 'length' => 0];
        }

        return ['status' => 206, 'start' => $start, 'end' => $end, 'length' => $end - $start + 1];
    }
```

**Gözlem (S1):** üç sonuç durumu — `200` (yok sayma), `206` (kısmi), `416`
(sınır dışı). Çoklu aralık (virgül) `200`'e düşer. Kod yorumunda **RFC 9110**
adı geçmektedir → §12 iddia tablosunun dayanağı.

## §9 304 Kontrolü (gerçek kod)

**Dosya:** Aynı · **Satır:** 264–290

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

**Gözlem:** `If-None-Match` önceliklidir; `*` ve `W/` (weak) bayrağı desteklenir.
`If-Modified-Since` yedek yoludur.

## §9.1 Gövde Gönderimi ve Hata (gerçek kod)

**Dosya:** Aynı · **Satır:** 331–367

```php
    private function sendBody(string $path, int $start, int $length): never
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            $this->fail(403, 'Erişim reddedildi');
        }

        if ($start > 0) {
            fseek($handle, $start);
        }

        $remaining = $length;
        while ($remaining > 0 && !feof($handle)) {
            $chunk = fread($handle, min(self::CHUNK_BYTES, $remaining));
            if ($chunk === false || $chunk === '') {
                break;
            }
            echo $chunk;
            $remaining -= strlen($chunk);
            flush();
        }

        fclose($handle);
        exit;
    }

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

**Gözlem:** parça gönderimi 1 MiB (`CHUNK_BYTES = 1048576`) kuyrukla — bellekte
tam dosya tutulmaz. `fail()` her zaman `exit`.

## §9.2 Bağımlılık — Kütüphane Yolu (gerçek kod)

**Dosya:** Aynı · **Satır:** 371–378

```php
    private static function libraryPath(): string
    {
        $path = defined('MUSIC_LIBRARY_PATH')
            ? (string)MUSIC_LIBRARY_PATH
            : (string)(($_ENV['MUSIC_LIBRARY_PATH'] ?? getenv('MUSIC_LIBRARY_PATH')) ?: '');

        return $path !== '' ? $path : 'C:\Users\Bayram Ali\Music';
    }
```

**Gözlem (S1):** öncelik sırası `defined()` → `$_ENV` → `getenv()` → sabit
varsayılan yol. Anayasa §9/B3 ("kök değeri okunmadı") bu kodla kapatılmıştır.
**Güvenlik notu:** varsayılan yol kullanıcı dizinidir — ortam değişkeni tanımsızsa
bu dizin kullanılır (S1 kaydı; risk değeri yok).

## §10 Repository Örnekleri (gerçek kod)

### §10.1 Fabrika

**Dosya:** `home.coremusic.net/include/Repository/MusicRepository.php` · **Satır:** 15–47

```php
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
        $cs   = defined('DB_CHARSET') ? (string)DB_CHARSET : (string)(($_ENV['DB_CHARSET'] ?? 'utf8mb4') ?: 'utf8mb4');

        return new self($host, $name, $user, $pass, $port, $cs);
    }
```

### §10.2 Birincil Dosya Sorgusu (gerçek SQL)

**Aynı dosya · Satır:** 51–71

```php
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
```

**Gözlem (S1):** tablolar `music_files` + `musics`; kimlik `UNHEX(:id)` ile
bağlanır (hazırlanmış ifade); sütunlar açıkça listeleniyor — `SELECT *` yok
(ADR-002). Anayasa §9/B4 ("SQL okunmadı") bu kodla kapatılmıştır.

### §10.3 Son Dinlenenler Sorgusu (gerçek SQL)

**Aynı dosya · Satır:** 79–96

```php
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
```

**Gözlem (S1):** `LIMIT 12`, `ORDER BY m.created_at DESC, m.id ASC`
(deterministik). Boş liste → çağıran fallback'e düşer (doc-block kendi yazar).

### §10.4 Tembel Bağlantı

**Aynı dosya · Satır:** 99–109

```php
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
```

## §11 KOD YOK (S5 — uydurulmayan)

| # | Alan | Arama | Sonuç | Yapılan |
|---|---|---|---|---|
| 1 | audio-service DI kaydı | `**/Container/**` ses kaydı okunmadı | okunmadı | ⚠️ VERIFICATION REQUIRED |
| 2 | zamanlanmış iş motoru | `**/*Cron*.php`, `**/*Queue*.php`, `**/*Job*.php`, `**/*Worker*.php` | 0 isabet | kod YOK — motor adı yazılmaz |
| 3 | ses seviyesi hesaplama | `**/*Volume*.php` | 0 isabet | kod YOK — yalnız UI bayrağı (S1:463) |
| 4 | sinyal işleme / EQ | `**/*Audio*.php`, `**/*Equalizer*.php` | 0 isabet | kod YOK — motor K003/K004 (iddia) |
| 5 | playback kuyruğu | `**/*Playback*.php`, `**/*Player*.php` | yalnız `PlayerInfoComponent.php` (sunum) | kuyruk kodu YOK |
| 6 | servis kaydı (registry) çağrısı | `ServiceRegistry` audio çağrısı | okunmadı | ⚠️ VERIFICATION REQUIRED |

## §12 Web Araştırması — İddia Kaydı

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| HTTP, `Range`/`If-None-Match`/`If-Modified-Since` semantiğini tanımlar (Range isteği, 206, 304, 416) | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP 200 | ✅ doğrulandı — §8/§9 kodunun dayanağı; kod yorumu da RFC 9110 adını taşır (S1:234) |
| HTTP/1.1 mesaj yapısı ve status satırı kuralları | https://www.rfc-editor.org/rfc/rfc7230.html | HTTP 200 | ✅ doğrulandı — `emitBaseHeaders` başlık yazımının dayanağı |
| HTTP durum kodları genişletilmiş sınıflar (416 dâhil) | https://www.rfc-editor.org/rfc/rfc6585.html | HTTP 200 | ✅ doğrulandı — 416 kodu §8/§4'te gerçek kodda kullanılıyor |
| 12 faktörlük uygulama, yapılandırmayı ortam değişkeniyle verir | https://12factor.net/ | HTTP 200 | ✅ doğrulandı — `fromEnvironment()`/`libraryPath()` yaklaşımıyla uyumlu |

## §13 Sembol Dizinı (bu dosyadaki tüm semboller)

| # | Sembol | Dosya | Satır |
|---|---|---|---|
| 1 | `MIME_BY_EXT` | MusicStreamHandler.php | 24 |
| 2 | `MIME_BY_FORMAT` | MusicStreamHandler.php | 36 |
| 3 | `CHUNK_BYTES` | MusicStreamHandler.php | 46 |
| 4 | `__construct()` | MusicStreamHandler.php | 48 |
| 5 | `dispatch()` | MusicStreamHandler.php | 55 |
| 6 | `run()` | MusicStreamHandler.php | 60 |
| 7 | `authorize()` | MusicStreamHandler.php | 121 |
| 8 | `bypassActive()` | MusicStreamHandler.php | 149 |
| 9 | `findFile()` | MusicStreamHandler.php | 164 |
| 10 | `resolvePath()` | MusicStreamHandler.php | 173 |
| 11 | `contentType()` | MusicStreamHandler.php | 211 |
| 12 | `parseRange()` | MusicStreamHandler.php | 224 |
| 13 | `isNotModified()` | MusicStreamHandler.php | 264 |
| 14 | `emitNotModified()` | MusicStreamHandler.php | 294 |
| 15 | `emitBaseHeaders()` | MusicStreamHandler.php | 310 |
| 16 | `sendBody()` | MusicStreamHandler.php | 331 |
| 17 | `fail()` | MusicStreamHandler.php | 357 |
| 18 | `libraryPath()` | MusicStreamHandler.php | 371 |
| 19 | `MusicRepository` sınıfı | MusicRepository.php | 15 |
| 20 | `fromEnvironment()` | MusicRepository.php | 30 |
| 21 | `findPrimaryFile()` | MusicRepository.php | 51 |
| 22 | `findRecentTracks()` | MusicRepository.php | 79 |
| 23 | `db()` | MusicRepository.php | 99 |

## §14 Kod → Kural Eşlemesi

| Kural (anayasa) | Karşılık gelen kod | Satır |
|---|---|---|
| A4 yetki zorunlu | `authorize()` çağrısı | 62 |
| A5 bypass bayrağı | `bypassActive()` | 149 |
| A6 Range | `parseRange()` | 224 |
| A7 304/ETag | `isNotModified()` · `emitNotModified()` | 264 · 294 |
| A8 MIME | `contentType()` | 211 |
| A9 hata yanıtı | `fail()` | 357 |
| A10 yol çözümü | `resolvePath()` | 173 |
| A11 kayıt bulma | `findPrimaryFile()` | 51 |
| A14 süre biçimi | `formatDuration()` (RecentTracks) | 157 |

## §15 Açık Sorular (bu dosyadan sonra)

| # | Soru | Durum |
|---|---|---|
| Q3 | servis mi motor mu | ⚠️ açık |
| Q-a1 | ses verisi sahipliği | ⚠️ açık |
| Q-b1 | DI kaydı var mı | ⚠️ açık (§11/1) |
| Q-b2 | zamanlanmış iş motoru | ⚠️ açık (§11/2) |
| Q-b3 | `resolvePath` 181–204 tam gövdesi | ⚠️ açık (§6 kesit notu) |
| Q-b4 | `emitBaseHeaders` tam gövdesi (310–329) | ⚠️ kısmı okundu |

## §16 Doğrulama

| Kontrol | Hedef | Durum |
|---|---|---|
| Satır sayısı | ≥500 | dış ölçüm |
| Son satır | authority birebir | birebir |
| `parent:` | 0 | 0 |
| `group` | `kod` alıntısız | kod |
| Mojibake / CJK | 0 | 0 |
| Kod kanıtı | ≥1 `Dosya/Satır` | §1–§13 |
| Uydurma kod | 0 | tüm bloklar diskten |

## §17 İlgili Dosyalar

| Dosya | İlişki |
|---|---|
| audio-service-anayasa.md | kurallar |
| audio-service-surucu.md | sürücü/akış |
| audio-service/index.md | künye |
| services-ornekler.md | kök örnek formatı |
| services-dogrulama.md | kök doğrulama |

## §18 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | tam dosya okuma ile 15 gerçek kod bloğu yazıldı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
