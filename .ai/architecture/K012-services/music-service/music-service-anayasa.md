---
node_id: K012-services/music-service/music-service-anayasa
node_type: module
status: draft
parent_id: K012-services/music-service
role: anayasa
children: []
depends_on:
  - "K012-services/music-service/index.md"
  - "K012-services/service-contracts"
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php — 379 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/include/Repository/MusicRepository.php — 110 satır, 2026-10-13 tam okundu"
  - "home.coremusic.net/include/Component/RecentTracksComponent.php — 217 satır, 2026-10-13 tam okundu"
validation:
  - "Üç dosya diskten birebir okundu; method/sabit envanteri satır numarasıyla kaydedildi (2026-10-13)"
open_questions:
  - "Kapsamı K015/catalog ile örtüşüyor mu? → ⚠️ VERIFICATION REQUIRED (index §A)"
  - "playback sahipliği (K012 ↔ K015) → ⚠️ VERIFICATION REQUIRED — çözülmedi"
group: mimari
---

# music-service — Anayasa (kademe-3 · module · rol: anayasa)

**ELI15:** Bu dosya müzik servisinin gerçek kod kanıtını verir: `home.coremusic.net/` içinde üç dosya — byte-range destekli stream handler (379 satır), PDO repository (110 satır), son-dinlenenler bileşeni (217 satır). Anayasa, neyin var olduğunu ve nereye bağlı olduğunu tanımlar; sürüş sırası surucu dosyasındadır.

**Sınır (ne DEĞİL):** Slug/ULID medya kuralları K012/media-service · katalog DB şeması K007-data · çalma sırası ve player durumu playback-service (K015 ile sahiplik çatışması açık — §9).

**Kod kanıtı kuralı:** Her iddia `Dosya: … / Satır: …` künyesiz yazılmaz. Yokluk iddiası (`YOK`, `0`) repo taramasıyla verilir.

**Durum:** draft · PHP runtime bu oturumda yok — `php -l` kanıtı ⚠️ VERIFICATION REQUIRED · kademe-4 ADR-097 K4 faz (C) bekliyor.

## §1 Dosya Envanteri (music-service kod yüzeyi)

| # | Dosya | Satır | Namespace | Rol |
|---|---|---|---|---|
| 1 | include/Stream/MusicStreamHandler.php | 379 | CoreMusic\Home\Stream | GET /stream/{musicId} byte-range akışı |
| 2 | include/Repository/MusicRepository.php | 110 | CoreMusic\Home\Repository | coremusic_musics veri erişimi |
| 3 | include/Component/RecentTracksComponent.php | 217 | CoreMusic\Home\Component | En Son + Playlistler + Sıradaki bileşeni |

**Toplam:** 706 satır (379 + 110 + 217) · üçü de `final class` · üçü de `declare(strict_types=1)` tek satır (satır 1 her dosyada).

## §2 MusicStreamHandler — Sözleşme Satırları

| Sözleşme | Satır | Davranış |
|---|---|---|
| Auth zorunlu | 121-147 (`authorize`) | Oturum yoksa 302 `/login`; fail-closed |
| Bypass prod'da kapalı | 149-159 (`bypassActive`) | `APP_ENV_MODE === 'production'` → false (B-F-05) |
| Kütüphane sınırı | 173-209 (`resolvePath`) | `..` parçası → 403 · symlink → 403 · realpath kök-dışı → 403 |
| Range tek aralık | 224-262 (`parseRange`) | Tek aralık → 206 · çoklu aralık (`,` içeren) → yok sayılır, 200 |
| 416 sınırı | 257-259 | `start >= size` veya `start > end` → 416 |
| Koşullu GET | 264-290 (`isNotModified`) | ETag (W/ öneki temizlenir) veya If-Modified-Since → 304 |
| MIME iki katman | 211-215 (`contentType`) | uzantı → `MIME_BY_EXT` (satır 24-33) → `file_format` → `MIME_BY_FORMAT` (satır 36-44) → `application/octet-stream` |
| Chunk 1 MiB | 46 + 331-355 (`sendBody`) | `CHUNK_BYTES = 1048576`; `fread` döngüsü + `flush()`; `ignore_user_abort(true)` (satır 318) |
| HEAD | 108-110 | Başlıklar gönderilir, gövde yok |

**Sabit envanteri:** `MIME_BY_EXT` 8 uzantı (mp3/flac/wav/m4a/ogg/aac/dsd/mqa) · `MIME_BY_FORMAT` 7 enum · `CHUNK_BYTES` 1048576 · ctor `private readonly` (satır 48-52).

## §3 MusicStreamHandler — Auth Zinciri

| Adım | Satır | İşlem |
|---|---|---|
| 1 | 123 | `SessionBootstrapper::ensureStarted()` |
| 2 | 125 | `PageRouterHelper` — AuthGuard ile aynı session anahtarları |
| 3 | 127-137 | Auth yoksa + bypass aktifse + üç sabit tanımlıysa `$_SESSION['MM_*']` doldurulur (fail-closed: biri boşsa doldurma yok) |
| 4 | 139-143 | Hâlâ auth yoksa `writeClose()` + 302 `/login` + exit |
| 5 | 146 | `writeClose()` — "Streaming sırasında session kilidi tutulmasın" (satır 145 yorumu) |

**Gözlem:** Bypass katmanı tek katman değildir — `bypassActive()` prod kontrolü (satır 153) `SecurityHelper::isTestBypassActive` ile "aynı kural" olarak belgelenmiştir (satır 151-152 yorumu); savunma derinliği kasten.

## §4 MusicRepository — SQL ve Tembel Bağlantı

| Method | Satır | SQL / Davranış |
|---|---|---|
| `fromEnvironment()` | 30-43 | `DB_MUSIC_NAME` → default `coremusic_musics`; bağlantı AÇILMAZ (satır 29 yorumu) |
| `findPrimaryFile()` | 51-69 | 32-hex girdi denetimi (satır 53) → `SELECT f.file_path, f.file_format, f.file_size FROM music_files f JOIN musics m … WHERE f.music_id = UNHEX(:id) AND m.is_deleted = 0 AND f.is_deleted = 0 ORDER BY f.is_primary DESC, f.id ASC LIMIT 1` |
| `findRecentTracks()` | 79-96 | `SELECT HEX(m.id) AS music_hex, m.title, m.duration_sec, a.name AS artist_name FROM musics m JOIN artists a … JOIN music_files f … WHERE … AND f.is_primary = 1 ORDER BY m.created_at DESC, m.id ASC LIMIT 12` |
| `db()` | 99-109 | Tembel: `??=` ile ilk sorguda `DatabaseManager + DatabaseConfig` kurulur (satır 12-13 yorumu: "DB'siz ortamlarda bağlantı denemesi yapılmaz") |

**ADR-002 uyumu:** İki SQL de prepared statement (`:id` parametresi, satır 66) ve `SELECT *` yok — her iki sorguda da açık sütun listesi (satır 57-58, 81-84). Sınıf dokümanı bu kuralı satır 11'de kaynak gösterir.

## §5 RecentTracksComponent — Varyant Matrisi

| Bölüm | Wide/4K (1920) | Embedded (1024) | Kaynak |
|---|---|---|---|
| En Son | 1 sıra × 10 kart (`WIDE_CARDS = 10`, satır 34) | 2×2 grid × 4 kart (`EMBEDDED_CARDS = 4`, satır 37) | Figma 1639:9904 / PNG 1920 |
| Playlistler | 1 sıra × 6 kart (`WIDE_PLAYLIST_CARDS = 6`, satır 40) | 3 kart + link kartı (`EMBEDDED_PLAYLIST_CARDS = 3`, satır 43) | Figma 1639:9892/9893 |
| Sıradaki | `null` — "mockup'ta YOK" (satır 15, 101) | 1 kart 186×109 cam panel (satır 101-106) | Figma 1639:9910 (185.978×109) |

**Veri zinciri (satır 67):** `$tracks ?? $this->dbTracks($repository) ?? $this->defaultTracks()` — override → DB → PNG demo. DB yolu (satır 122-154): `DB_HOST` tanımlı değilse null · `\Throwable` yakalanır → null · boş liste → null · her satırda `music_hex` boşsa atlanır (satır 141-143) · `stream` = `/stream/{hex}` (satır 149).

**Süre biçimi (satır 157-164):** `formatDuration` — `is_numeric` değilse `00:00:00` (import `duration_sec` yazmaz, satır 156 yorumu); `intdiv` ile HH:MM:SS.

## §6 Tablolar ve Enum Referansları

| Tablo | Kullanım | Dosya/Satır |
|---|---|---|
| `musics` | `is_deleted = 0`, `created_at DESC`, `artist_id` | MusicRepository 59-61, 85-92 |
| `music_files` | `is_primary`, `is_deleted`, `file_path/format/size` | MusicRepository 58, 63, 87-91 |
| `artists` | `is_deleted = 0`, `name` | MusicRepository 86, 89 |
| `UNHEX(:id)` | 32 karakter hex → BLOB id | MusicRepository 60 |
| `HEX(m.id)` | BLOB id → hex (stream URL üretimi) | MusicRepository 81 |

**Sahiplik notu:** Şemanın sahibi K007-data'dır; bu dosyalardaki sorgular salt-okunur (`SELECT`) — INSERT/UPDATE/DELETE **YOK** (üç dosyanın tamamı 2026-10-13 okundu; yazma methodu bulunamadı).

## §7 Bağımlılık İçe Aktarmaları (use satırları)

| Dosya | `use` satırları | Hedef |
|---|---|---|
| MusicStreamHandler | 5-7 | `MusicRepository` (Home) · `PageRouterHelper` (shared PageRouter) · `SessionBootstrapper` (shared Session) |
| MusicRepository | 5-6 | `DatabaseConfig` · `DatabaseManager` (shared Database) |
| RecentTracksComponent | 5-8 | `AbstractComponent` · `HomeLayoutVariant` · `HomeSongButton` (Home) · `MusicRepository` (Home) |

**Katman gözlemi:** Üç dosya da `shared/` katmanına yalnız alttan bağımlıdır (PageRouter/Session/Database) — shared, home'a asla bağımlı değildir. DI yolu: repository ctor `private` + `fromEnvironment()` static factory (MusicRepository 19-27, 30); bileşen injection'ı `ComponentLoader::make` üzerinden (RecentTracksComponent 61 yorumu).

## §8 Kodlanmayanlar (YOK iddiaları — tarama kanıtı)

| İddia | Tarama | Sonuç |
|---|---|---|
| Yazma methodu (INSERT/UPDATE/DELETE) üç dosyada | 2026-10-13 tam dosya okuma | YOK |
| Çoklu-aralık Range desteği | `str_contains($header, ',')` → `$full` (satır 229) | Bilinçli YOK — "Çoklu aralık yok sayılır (200 tam gövde)" (satır 220 yorumu) |
| Playlist DB tablosu | bileşen dokümanı satır 29: "PNG sabit içerik (DB tablosu yok)" | YOK |
| Album art DB alanı | `dbTracks` `art => ''` (satır 148) | DB'den gelmez |
| `php -l` 3/3 kanıtı | PHP runtime bu ortamda yok | ⚠️ VERIFICATION REQUIRED |

## §9 Açık Sorular (bu dosyaya özgü)

| # | Soru | Durum |
|---|---|---|
| 1 | Kapsam K015/catalog ile örtüşüyor mu? | ⚠️ VERIFICATION REQUIRED — index §A'dan taşındı |
| 2 | Playback sahipliği (music stream vs player durumu) K012 ↔ K015 sınırı | ⚠️ VERIFICATION REQUIRED — asla uydurulmadı |
| 3 | `dsd`/`mqa` uzantıları hangi donanım zincirinde gerçekten servis edilir? | UNKNOWN — repo'da kod seviyesi kanıt YOK |
| 4 | `MUSIC_LIBRARY_PATH` default yolu (`C:\Users\Bayram Ali\Music`, satır 377) production'da hangi değer? | UNKNOWN — `.env` içeriği vault'a yazılmaz (Guardrail) |

## §10 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| RFC 9110 Range semantiği (satır 234 yorumu "RFC 9110" adı verir) | kullanılmadı — kod yorumu kaynaktır | 2026-10-13 | geçerli |
| B-F-05 bypass kuralı | URL yok — kaynak: kod yorumu satır 151 | 2026-10-13 | geçerli |
| ADR-002 / ADR-008 | vault ADR referansı — URL eklenmedi | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanterdeki 2.929 URL ile kesişim 0.

## §11 MusicStreamHandler — Method Ekseni (tam envanter)

| # | Method | İmza (satır) | Çıkış | Erişim |
|---|---|---|---|---|
| 1 | `__construct` | `private function __construct(private readonly string $libraryPath, private readonly MusicRepository $repository)` (48-52) | — | private (singleton-ish factory) |
| 2 | `dispatch` | `public static function dispatch(string $musicIdHex): never` (55-58) | yanıt + exit | public — bootstrap.php special-route girişi |
| 3 | `run` | `private function run(string $musicIdHex): never` (60-113) | akış orkestrasyonu | private |
| 4 | `authorize` | `private function authorize(): void` (121-147) | 302 veya devam | private |
| 5 | `bypassActive` | `private static function bypassActive(): bool` (149-159) | bool | private static |
| 6 | `findFile` | `private function findFile(string $musicIdHex): ?array` (164-167) | satır/null | private — repository delegasyonu |
| 7 | `resolvePath` | `private function resolvePath(string $stored): string` (173-209) | gerçek yol veya fail | private |
| 8 | `contentType` | `private function contentType(string $path, string $format): string` (211-215) | MIME | private |
| 9 | `parseRange` | `private function parseRange(string $header, int $size): array` (224-262) | `{status,start,end,length}` | private |
| 10 | `isNotModified` | `private function isNotModified(string $etag, int $mtime): bool` (264-290) | bool | private |
| 11 | `emitNotModified` | `private function emitNotModified(string $etag, string $lastMod): never` (294-308) | 304 + exit | private |
| 12 | `emitBaseHeaders` | `private function emitBaseHeaders(string $mime, int $length, string $etag, string $lastMod): void` (310-329) | başlıklar | private |
| 13 | `sendBody` | `private function sendBody(string $path, int $start, int $length): never` (331-355) | gövde + exit | private |
| 14 | `fail` | `private function fail(int $status, string $message): never` (357-367) | hata metni + exit | private |
| 15 | `libraryPath` | `private static function libraryPath(): string` (371-378) | yol | private static |

**Toplam:** 15 method · 1 public static · 2 private static · 12 private instance · hiçbiri `protected` değil (final sınıf, extend yok).

## §12 HTTP Başlık Matrisi (satır bazlı)

| Başlık | Değer kaynağı | Satır |
|---|---|---|
| `Accept-Ranges: bytes` | sabit | 321 |
| `Content-Length: {length}` | `$range['length']` | 322 |
| `ETag: "{md5}"` | `md5($path . '\|' . $size . '\|' . $mtime)` çaprazı, satır 80 | 323 |
| `Last-Modified: {gmdate} GMT` | `gmdate('D, d M Y H:i:s', $mtime)` (satır 81) | 324 |
| `Cache-Control: private, max-age=0, must-revalidate` | sabit | 325 |
| `Content-Disposition: inline` | sabit | 326 |
| `X-Content-Type-Options: nosniff` | sabit | 327 |
| `Content-Type: {mime}` | iki katmanlı harita (§2) | 328 |
| `Content-Range: bytes {s}-{e}/{size}` | 206 dalı | 102 |
| `Content-Range: bytes */{size}` | 416 dalı | 96 |
| 304 seti: ETag + Last-Modified + Cache-Control | `emitNotModified` | 303-305 |

**Ön temizlik (her iki emit de):** `zlib.output_compression = 0` (satır 297, 313) · tüm output buffer'lar temizlenir (satır 299-301, 315-317) · `header_remove('Content-Type')` yalnız `emitBaseHeaders` (satır 320).

## §13 Hata Kodu Matrisi (fail çağrıları)

| Kod | Mesaj | Tetikleyen satır | Koşul |
|---|---|---|---|
| 404 | `Kayıt bulunamadı` | 66 | `findFile` null (id yok / silinmiş) |
| 404 | `Dosya bulunamadı` | 177 | boş yol veya `\0` içerir |
| 404 | `Dosya bulunamadı` | 191 | realpath false veya dosya değil |
| 404 | `Kütüphane bulunamadı` | 196 | `MUSIC_LIBRARY_PATH` realpath'i alınamıyor |
| 403 | `Erişim reddedildi` | 182 | yol parçalarında `..` |
| 403 | `Erişim reddedildi` | 186 | `is_link` true (symlink) |
| 403 | `Erişim reddedildi` | 201 | gerçek yol kütüphane kökünün altında değil |
| 403 | `Erişim reddedildi` | 205 | `is_readable` false |
| 403 | `Erişim reddedildi` | 335 | `fopen` başarısız (sendBody) |

**Yanıt karakteri:** `fail` her durumda `text/plain; charset=utf-8` + `nosniff` + `no-store` (satır 359-364); `headers_sent()` koruması vardır.

## §14 Range Çözümleme Senaryoları (parseRange gerçek davranışı)

| # | Header | size | Sonuç | Satır gerekçesi |
|---|---|---|---|---|
| 1 | `bytes=0-499` | 1000 | 206, 0-499, length 500 | 253-255 normal aralık |
| 2 | `bytes=500-` | 1000 | 206, 500-999, length 500 | 254: boş `endRaw` → `size-1` |
| 3 | `bytes=-200` | 1000 | 206, 800-999, length 200 | 244-251 suffix range |
| 4 | `bytes=0-` | 1000 | 206, 0-999, length 1000 | 253-254 |
| 5 | `bytes=2000-3000` | 1000 | 416 | 257: `start >= size` |
| 6 | `bytes=500-200` | 1000 | 416 | 257: `start > end` |
| 7 | `bytes=-0` | 1000 | 416 | 247-248: `suffix <= 0` |
| 8 | `bytes=0-0` | 1000 | 206, 0-0, length 1 | 261: tek bayt |
| 9 | `bytes=0-999999` | 1000 | 206, 0-999, length 1000 | 254: `min(end, size-1)` — taşma kırpılır |
| 10 | `bytes=0-100,200-300` | 1000 | 200 tam gövde | 229: virgül → yok say |
| 11 | `items=0-100` | 1000 | 200 tam gövde | 233: desen uymuyor |
| 12 | (boş) | 1000 | 200 tam gövde | 229 |
| 13 | `bytes=-` | 1000 | 200 tam gövde | 240-242: ikisi de boş |
| 14 | `bytes=0-499` | 0 | 200 (length 0) | 229: `size === 0` erken çıkış |

**Not:** Senaryo 9 RFC 9110 `bytes-unit` taşma kırpmasıyla uyumludur (kod yorumu satır 234 kaynağı gösterir).

## §15 Verbatim Kanıt Blokları (kritik methodlar)

### §15.1 parseRange — Dosya: MusicStreamHandler.php / Satır: 219-262

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

### §15.2 resolvePath — Dosya: MusicStreamHandler.php / Satır: 169-209

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
        if (in_array('..', $segments, true)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        if (is_link($trimmed)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        $real = realpath($trimmed);
        if ($real === false || !is_file($real)) {
            $this->fail(404, 'Dosya bulunamadı');
        }

        $root = realpath($this->libraryPath);
        if ($root === false) {
            $this->fail(404, 'Kütüphane bulunamadı');
        }

        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
        if (!str_starts_with($real, $prefix)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        if (!is_readable($real)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        return $real;
    }
```

### §15.3 sendBody — Dosya: MusicStreamHandler.php / Satır: 331-355

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
```

## §16 Verbatim Kanıt Blokları (repository + bileşen)

### §16.1 findPrimaryFile SQL — Dosya: MusicRepository.php / Satır: 57-64

```php
        $sql = 'SELECT f.file_path, f.file_format, f.file_size
                  FROM music_files f
                  JOIN musics m ON m.id = f.music_id
                 WHERE f.music_id = UNHEX(:id)
                   AND m.is_deleted = 0
                   AND f.is_deleted = 0
                 ORDER BY f.is_primary DESC, f.id ASC
                 LIMIT 1';
```

### §16.2 findRecentTracks SQL — Dosya: MusicRepository.php / Satır: 81-93

```php
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
```

### §16.3 dbTracks — Dosya: RecentTracksComponent.php / Satır: 122-154

```php
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
```

## §17 Bileşen — Seed Verileri ve HTML Üretimi

**Seed tablosu (defaultTracks, satır 172-195):**

| # | `t` (başlık) | `a` (sanatçı) | `d` | `art` |
|---|---|---|---|---|
| 1 | Göksel - Sevil Neşelen | Göksel | 00:03:05 | album-goksel.png |
| 2 | Göksel - Kabahat Seni Se... | Göksel | 00:02:05 | album-goksel.png |
| 3 | Barış Manco - Gulpembe | Barış Manco | 00:04:01 | album-kursat.png |
| 4 | Kış Masalı Ensturmental | Org Dersleri | 00:01:10 | album-kursat.png |

**Playlist seed (satır 204-216):** 3 kayıt (`En Sevilen Supermix'im` / `Ruh haline Göre Günlük mix'im` / `Yeni Sevilen Türleri Keşfet mix'im` — hepsi `Sistem Tarafından Oluşturuldu`); wide için `array_merge($seed, $seed)` = 6 kart (satır 215).

**Döngü kuralı (satır 185-192):** `WIDE_CARDS = 10` tamamlanana kadar 4'lük seed tekrarlanır — 10 kart 4'ten döngüyle üretilir (2×4 + 2).

**HTML üretimi:** `HomeSongButton::html([...], 'home-song__mini-card__subtitle', $d)` — parametreler `t/s/art/stream` (satır 70-79); playlist akışında `stream` daima `''` (satır 93).

## §18 Güvenlik Matrisi

| Katman | Mekanizma | Satır |
|---|---|---|
| Auth | fail-closed 302 (§3) | 139-143 |
| Bypass | prod'da imkânsız | 153-155 |
| Path traversal | `..` segment kontrolü | 181-183 |
| Null byte | `\0` reddi | 176 |
| Symlink | `is_link` reddi | 185-187 |
| Kök kaçışı | realpath + `str_starts_with` prefix | 189-202 |
| Okunabilirlik | `is_readable` | 204-206 |
| MIME sniffing | `X-Content-Type-Options: nosniff` iki yerde | 327, 362 |
| Cache sızıntısı | `Cache-Control: private` + `no-store` hatalarda | 325, 363 |
| SQL injection | prepared statement (ADR-002) | Repository 66 |
| Session kilidi | streaming öncesi `writeClose()` | 146 |

## §19 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | `php -l` 3/3 kanıtı yok (PHP runtime yok) | ⚠️ VERIFICATION REQUIRED |
| 2 | `home.php` çağrısı ve `ComponentLoader::make` akışı okunmadı | ⚠️ VERIFICATION REQUIRED — ayrı okuma |
| 3 | Çoklu-range istemci uyumu test edilmedi | UNKNOWN |
| 4 | `mqa` → `audio/flac` eşlemesinin DAC zinciri etkisi | UNKNOWN — donanım testi yok |

## §20 Çağrı Grafiği (istek yaşam döngüsü)

| Adım | Method | Atla/Devam |
|---|---|---|
| 1 | `bootstrap.php` special-route → `dispatch()` (satır 54-58) | — |
| 2 | `libraryPath()` + `MusicRepository::fromEnvironment()` ctor enjeksiyonu (satır 57) | — |
| 3 | `authorize()` | 302 exit (139-143) |
| 4 | `findFile()` → `findPrimaryFile()` | 404 exit (65-67) |
| 5 | `resolvePath()` | 403/404 exit (173-209) |
| 6 | Disk boyutu düzeltmesi (`$diskSize > 0` → `$size`, satır 71-76) | — |
| 7 | `contentType()` + ETag + Last-Modified üretimi (78-81) | — |
| 8 | `isNotModified()` | 304 exit (86-89) |
| 9 | `parseRange()` | — |
| 10 | `emitBaseHeaders()` + status (93-106) | 416 exit (95-99) |
| 11 | HEAD mi? | exit (108-110) |
| 12 | `sendBody()` 1 MiB chunk döngüsü | exit (112, 331-355) |

**Zamanlama notu:** Adım 6'da disk boyutu DB boyutunu ezer — "dosya diske taşındıysa gerçek boyut esas alınır" davranışı (satır 70-76).

## §21 Ortak Yöntem Karşılaştırması (üç dosya)

| Davranış | StreamHandler | Repository | Component |
|---|---|---|---|
| `final class` | evet (satır 21) | evet (satır 15) | evet (satır 31) |
| Dosya satırı (2026-10-13) | 379 | 110 | 217 |
| `readonly` özellik | ctor promoted 2 özellik (satır 49-50) | ctor promoted 6 özellik (satır 20-25) | 4 public readonly array/nullable (satır 46-56) |
| Static factory | `dispatch` (satır 55) | `fromEnvironment` (satır 30) | yok — ctor + ComponentLoader |
| `\Throwable` yakalama | yok — hata 500 üretir (davranış korunur, satır 49 yorumu) | `@throws` beyanı (satır 49, 77) | `catch (\Throwable)` → null (satır 130) |
| exit kullanımı | 6 noktada (88, 98, 110, 307, 354, 366) | yok | yok |
| HTML üretimi | yok | yok | `HomeSongButton::html` (satır 70, 88) |

## §22 Değişiklik Kaydı

| Tarih | Değişiklik | Kanan |
|---|---|---|
| 2026-10-13 | İlk yazım — 3 dosya envanteri, sözleşme/SQL/varyant matrisleri, YOK taramaları | Vault Steward oturumu |
| 2026-10-13 | Genişletme §11-§19 — method ekseni, başlık/hata/Range matrisleri, verbatim bloklar, güvenlik matrisi | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
