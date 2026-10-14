---
node_id: K012-services/music-service/music-service-surucu
node_type: module
status: draft
parent_id: K012-services/music-service
role: surucu
children: []
depends_on:
  - "K012-services/music-service/music-service-anayasa.md"
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php — run() satır 60-113, 2026-10-13 okundu"
  - "home.coremusic.net/include/Repository/MusicRepository.php — db() satır 99-109, 2026-10-13 okundu"
  - "home.coremusic.net/include/Component/RecentTracksComponent.php — ctor satır 63-109, 2026-10-13 okundu"
validation:
  - "Sürüş adımları anayasa §2/§4/§5 matrisleriyle çapraz doğrulandı (2026-10-13)"
open_questions:
  - "home.php ComponentLoader akışı okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "playback sahipliği (K012 ↔ K015) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# music-service — Surucu (kademe-3 · module · rol: surucu)

**ELI15:** Bu dosya müzik servisinin **sürüş sırasını** verir: bir istek hangi adımlardan geçer, hangi noktada hangi HTTP kodu ile çıkar, bileşen verisi nasıl zincirlenir. Anayasa "neyin var" der; surucu "nasıl çalışır" der.

**Sınır (ne DEĞİL):** Satır-içi kod blokları anayasa dosyasındadır (§15-§16) · dosya envanteri anayasa §1'dedir · kural sabitleri anayasa §2'dedir.

**Sürüş kuralı:** Her adım `Dosya: … / Satır: …` ile bağlanır; adım sırası dosyadaki gerçek kod sırasıdır (yeniden düzenleme yok).

**Durum:** draft · PHP runtime yok — canlı doğrulama ⚠️ VERIFICATION REQUIRED.

## §1 Ana Akış — Stream İsteği (run)

| # | Adım | Satır | Devam koşulu | Ayrılma |
|---|---|---|---|---|
| 1 | `authorize()` | 62 | auth var | 302 `/login` exit |
| 2 | `findFile($musicIdHex)` | 64 | satır bulundu | 404 `Kayıt bulunamadı` exit |
| 3 | `resolvePath(file_path)` | 69 | gerçek yol doğrulandı | 403/404 exit (§2) |
| 4 | Disk boyutu düzeltmesi | 71-76 | `$diskSize > 0` ise `$size = $diskSize` | — |
| 5 | MIME + ETag + Last-Modified | 78-81 | — | — |
| 6 | `isNotModified()` | 86-89 | 304 değilse devam | 304 exit |
| 7 | `parseRange()` | 91 | — | — |
| 8 | `emitBaseHeaders()` | 93 | — | — |
| 9 | 416 kontrolü | 95-99 | 416 değilse devam | 416 exit (`Content-Range: bytes */size`) |
| 10 | 206/200 seçimi | 101-106 | — | — |
| 11 | HEAD kontrolü | 108-110 | GET ise devam | exit (başlıklar gitti, gövde yok) |
| 12 | `sendBody()` | 112 | — | exit (döngü sonu, satır 354) |

**Giriş noktası:** `dispatch($musicIdHex)` (satır 55-58) `strtolower` uygular (satır 57) — hex büyük/küçük harf duyarsız eşleşir; repository ayrıca `ctype_xdigit` + `strlen === 32` denetler (Repository satır 53).

## §2 Alt Akış — resolvePath Karar Ağacı

| # | Kontrol | Satır | Başarısızlık | Kod |
|---|---|---|---|---|
| 1 | trim + boş/`\0` | 175-178 | `Dosya bulunamadı` | 404 |
| 2 | `..` segmenti (ayırıcı: `/` ve `\`) | 180-183 | `Erişim reddedildi` | 403 |
| 3 | `is_link` | 185-187 | `Erişim reddedildi` | 403 |
| 4 | `realpath` + `is_file` | 189-192 | `Dosya bulunamadı` | 404 |
| 5 | Kütüphane `realpath` | 194-197 | `Kütüphane bulunamadı` | 404 |
| 6 | prefix `str_starts_with` | 199-202 | `Erişim reddedildi` | 403 |
| 7 | `is_readable` | 204-206 | `Erişim reddedildi` | 403 |
| 8 | — | 208 | `$real` döner | — |

**Sıra kuralı:** Kontroller **dosyalanma sırasıyla** uygulanır — `..` kontrolü symlink'ten önce, symlink kontrolü realpath'ten önce. Bu sıralama kasıtlıdır: en ucuz reddi en önce.

## §3 Alt Akış — Range + Yanıt Karar Ağacı

| Durum | `range['status']` | HTTP | Ek başlık | Gövde |
|---|---|---|---|---|
| Range yok / çoklu / çözümsüz | 200 | 200 | — | tam (`length = size`) |
| Geçerli tek aralık | 206 | 206 | `Content-Range: bytes start-end/size` | kısmi |
| Sınır dışı / ters aralık / `-0` | 416 | 416 | `Content-Range: bytes */size` | metin (`fail` değil — doğrudan exit, satır 96-98) |
| 304 (ETag/IMS eşleşmesi) | — | 304 | ETag + Last-Modified + Cache-Control | yok |
| HEAD | 206/200 | 206/200 | tam başlık seti | yok (exit, satır 109) |

**416 yolu ayrıdır:** `parseRange` 416 döndüğünde `fail()` DEĞİL doğrudan `header('Content-Range: bytes */' . $size)` + `http_response_code(416)` + `exit` (satır 95-99) — bu nedenle 416 yanıtında `fail`'in text/plain başlıkları yoktur.

## §4 Alt Akış — Bileşen Veri Zinciri (RecentTracksComponent ctor)

| # | Adım | Satır | Koşul | Sonuç |
|---|---|---|---|---|
| 1 | `$tracks` verilmiş mi? | 67 | `null değilse` | override verisi kullanılır (yalnız "En Son" beslenir, satır 59 yorumu) |
| 2 | `dbTracks($repository)` | 67 | `DB_HOST` tanımlı + satır var | DB verisi (§5) |
| 3 | `defaultTracks()` | 67 | — | PNG seed döngüsü (anayasa §17) |
| 4 | `HomeSongButton::html` map | 69-81 | — | `$cards` üretilir |
| 5 | Varyant kesimi | 83-84 | `isWide()` → 10 / embedded → 4 | `array_slice` |
| 6 | Playlist map | 86-99 | her iki varyantta da PNG verisi | DB yok |
| 7 | `nextCard` | 101-106 | wide → `null` / embedded → 1 kart | sabit metin |
| 8 | `playlistLinkIcon` | 108 | — | asset yolu |

**Zincir semantiği:** `??` kısa devre sırasıdır — ilk `null olmayan` kazanır; `dbTracks` hem `DB_HOST` yokluğunda hem `\Throwable` hem boş listede `null` döndürür (satır 124-136), yani DB sorunluysa sistem sessizce demo verisine düşer.

## §5 Alt Akış — dbTracks Ayrıştırma

| # | Adım | Satır | Ayrılma |
|---|---|---|---|
| 1 | `defined('DB_HOST')` | 124-126 | null |
| 2 | `findRecentTracks()` çağrısı | 129 | `\Throwable` → null (130-132) |
| 3 | `$rows === []` | 134-136 | null |
| 4 | Satır döngüsü | 139-151 | — |
| 5 | `music_hex` boş mu? | 140-143 | satır atlanır |
| 6 | Alan eşlemesi `t/a/d/art/stream` | 144-150 | `art` daima `''` |
| 7 | `$tracks !== []` | 153 | null (tüm satırlar atlandıysa) |

**Biçimlendirme:** `d = formatDuration(duration_sec)` — `is_numeric` değilse `00:00:00` (satır 157-164); `stream = '/stream/' . strtolower(hex)` (satır 149) — stream handler'daki `strtolower` (anayasa §1) ile aynı büyük/küçük harf politikası.

## §6 Bağlantı Yönetimi Sürüşü (Repository)

| Olay | Satır | Davranış |
|---|---|---|
| `fromEnvironment()` çağrısı | 30-43 | **Bağlantı yok** — yalnız 6 parametre saklanır |
| İlk `execute` | 66 veya 95 → `db()` | `??=` ile `DatabaseManager` + `DatabaseConfig` kurulur (99-109) |
| Sonraki her çağrı | — | Aynı `DatabaseManager` (`??=` kısa devre) |
| DB'siz ortam | — | İlk sorguda `\Throwable` → bileşen null zincirine düşer (§4 adım 2) |

**Tembel kuralı:** "PDO ilk sorguda açılır — DB'siz ortamlarda (testler) bağlantı denemesi yapılmaz" (Repository satır 12-13) — sürüş bunu doğrular: `fromEnvironment` tek başına bağlantı açmaz.

## §7 Uç Nokta → Sürüş Eşlemesi

| Uç nokta | Giriş | Sürüş | Kod |
|---|---|---|---|
| `GET /stream/{hex}` | bootstrap special-route → `dispatch` (satır 55) | §1 akışı | MusicStreamHandler |
| `HEAD /stream/{hex}` | aynı | §1 adım 1-11 (gövde yok) | MusicStreamHandler |
| Bileşen render | `home.php` → ComponentLoader → ctor (satır 63) | §4 akışı | RecentTracksComponent |
| Son 12 parça sorgusu | `findRecentTracks` | §6 tembel bağlantı | MusicRepository |

**`home.php` çağrısı:** Dosya bu oturumda OKUNMADI — "home.php her varyansta 'recent-tracks''i TEK ÇAĞIRIR" iddiası yalnızca bileşen dokümanından gelir (RecentTracksComponent satır 13) · ⚠️ VERIFICATION REQUIRED.

## §8 Hız/Sınır Sürüşü

| Parametre | Değer | Satır | Etki |
|---|---|---|---|
| Chunk | 1 MiB (1048576) | 46, 344 | `fread` başına maks okuma |
| Son parça | `min(CHUNK_BYTES, $remaining)` | 344 | `Content-Length` aşımı imkânsız |
| `flush()` | her chunk sonrası | 350 | akış canlılığı |
| `ignore_user_abort(true)` | gövde başında | 318 | istemci koparsa döngü devam (kısmi) |
| Range parça sayısı | 1 | 229 | çoklu aralık reddi — CPU/bellek koruması |
| Son 12 | `LIMIT 12` | Repository 93 | bileşen veri sınırı |
| Kart sayısı | 10 / 4 / 6 / 3 | bileşen 34-43 | varyant kesimi |

## §9 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | `bootstrap.php` special-route kayıt satırı okunmadı (dispatch'in çağrıldığı yer) | ⚠️ VERIFICATION REQUIRED |
| 2 | `ComponentLoader::make` injection akışı okunmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | `HomeSongButton::html` üretimi okunmadı | ⚠️ VERIFICATION REQUIRED |
| 4 | 416 yolunun `fail` olmadan exit yapması test edilmedi | UNKNOWN — test kanıtı yok |
| 5 | `ignore_user_abort` + `flush` davranışı PHP-FPM/apache altında canlı doğrulanmadı | UNKNOWN |

## §10 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Range çoklu aralık reddi RFC ile uyumlu | URL yok — kod yorumu satır 220/234 kaynaktır | 2026-10-13 | geçerli |
| Tembel bağlantı test dostu davranışı | URL yok — kod yorumu Repository satır 12-13 | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §11 Senaryo Matrisleri (uçtan uca)

### §11.1 Oturumsuz istemci senaryosu

| # | İstemci eylemi | Sunucu yanıtı | Kapanış |
|---|---|---|---|
| 1 | `GET /stream/abc…` (session yok) | 302 `Location: /login` | exit (satır 141-142) |
| 2 | 302 sonrası `/login` | login sayfası (home) | stream handler dışı |
| 3 | Bypass tanımlı + prod dışı + üç sabit dolu | `$_SESSION['MM_*']` doldurulur → akış devam | satır 127-137 |
| 4 | Bypass tanımlı + prod | bypass yok sayılır → 302 | satır 153-155 |

### §11.2 Seek (oynatıcı zıplama) senaryosu

| # | İstemci eylemi | Yanıt | Byte |
|---|---|---|---|
| 1 | İlk yükleme `bytes=0-` | 206, 0-size-1 | tam |
| 2 | 00:30'a seek `bytes=X-` | 206, X-end | kısmi |
| 3 | Son 2 saniye `bytes=-N` | 206, size-N..size-1 | suffix |
| 4 | ETag değişmiş dosyada eski `If-Range` | (If-Range işlenmiyor — kodda yok) | tam gövde 200 |

**If-Range boşluğu:** `isNotModified` yalnız `If-None-Match` / `If-Modified-Since` okur (satır 264-290); `If-Range` başlığı kodda **hiç geçmez** (2026-10-13 tam dosya okuma) — istemci eski ETag ile range isterse sunucu dosyanın güncel boyutunu esas alır, tutarsızlık 416/erken EOF ile sonuçlanabilir → UNKNOWN (test yok).

### §11.3 Kütüphane dışı erişim senaryosu

| # | DB `file_path` değeri | Sonuç | Satır |
|---|---|---|---|
| 1 | `..\..\secret.txt` | 403 (segment) | 181-183 |
| 2 | `C:\other\file.mp3` | 403 (prefix) | 200-202 |
| 3 | Kütüphane içinde symlink | 403 (is_link) | 185-187 |
| 4 | Silinmiş dosya | 404 (realpath false) | 190-192 |
| 5 | Boş string | 404 | 176-178 |
| 6 | `file\0.mp3` | 404 | 176 |

### §11.4 DB erişilemez senaryosu (bileşen)

| # | Durum | `dbTracks` | Bileşen çıktısı |
|---|---|---|---|
| 1 | `DB_HOST` tanımsız | null (satır 124) | PNG demo verisi |
| 2 | Bağlantı reddi (`\Throwable`) | null (satır 130) | PNG demo verisi |
| 3 | Boş sonuç (`[]`) | null (satır 134) | PNG demo verisi |
| 4 | Tüm satırlarda `music_hex` boş | null (satır 153) | PNG demo verisi |
| 5 | Kısmi geçerli satırlar | kalanlar döner | DB verisi (geçersizler atlanır) |

**Gözlem:** Dört ayrı null yolu vardır — hepsi sessiz fallback üretir; kullanıcıya hata gösterilmez. Loglama **YOK** (üç dosyanın 2026-10-13 okumasında `error_log`/`trigger_error` çağrısı bulunamadı) → gözlemlenebilirlik boşluğu, §9.5'e işlendi.

## §12 Sınır Durumları (edge cases)

| # | Durum | Beklenen | Kod tarafı |
|---|---|---|---|
| 1 | `file_size` DB'de 0, disk'te gerçek boyut | disk boyutu (satır 71-76) | `$diskSize > 0` şartı |
| 2 | `file_size` DB'de büyük, disk'te küçük | disk boyutu | aynı |
| 3 | Dosya boyutu 0 (gerçekten boş) | `$size` DB'den; range `size === 0` → 200 (satır 229) | length 0 gövde |
| 4 | `duration_sec` NULL | `00:00:00` (satır 159-161) | `is_numeric` reddi |
| 5 | `duration_sec` negatif | `max(0, …)` → `00:00:00` (satır 162) | clamp |
| 6 | `music_hex` DB'de büyük harf | `strtolower` → URL küçük harf (satır 149) | handler da lowercase (satır 57) |
| 7 | Aynı hex iki kez (çakışma) | repository `findPrimaryFile` tek satır döner (LIMIT 1, satır 64) | — |
| 8 | `If-None-Match: *` | 304 (satır 268-270) | her koşulda eşleşme |
| 9 | Birden fazla ETag adayı | herhangi biri eşleşirse 304 (satır 271-277) | döngü |
| 10 | `W/` zayıf ETag öneki | temizlenir, karşılaştırma sürer (satır 273) | — |

## §13 Zamanlama ve Sıra Kısıtları

| Kısıt | Değer | Kaynak |
|---|---|---|
| ETag girdisi | `path|size|mtime` üçlüsü (satır 80) | boyut veya mtime değişirse ETag değişir |
| Last-Modified | `gmdate` UTC (satır 81) | yerel saat yok |
| If-Modified-Since karşılaştırması | `$mtime <= $since` (satır 284) | saniye hassasiyeti |
| Range regex | `/^bytes=(\d*)-(\d*)$/i` (satır 233) | büyük/küçük harf duyarsız `bytes=` |
| Hex girdi | 32 karakter `ctype_xdigit` (Repository 53) | olmayan girdi null → 404 |
| Kart sırası (DB) | `created_at DESC, id ASC` (satır 92) | deterministik — "davranış korunur" |
| Dosya sırası (birincil) | `is_primary DESC, id ASC` (satır 63) | — |

## §14 Sürüş Güvenlik Zinciri (sıra önemlidir)

| # | Sıra | Katman | Gerekçe |
|---|---|---|---|
| 1 | Auth önce | authorize (satır 62) | Kimlik doğrulanmadan dosya adı bile okunmaz |
| 2 | DB sorgusu sonra | findFile (satır 64) | Yol, yetkili oturumdan gelir |
| 3 | Yol doğrulama sonra | resolvePath (satır 69) | DB değeri güvenilir sayılmaz — yeniden doğrulanır |
| 4 | Range sonra | parseRange (satır 91) | Geçersiz range'de dosya zaten doğrulanmıştır |
| 5 | Gövde en son | sendBody (satır 112) | Başlıklar gönderilmeden write yok |

**İhlal testi:** Adım sırası ters çevrilseydi (ör. path doğrulama auth'dan önce) auth'siz kullanıcı dosya varlığını 403/404 ile ölçebilirdi — mevcut sıra bu sızıntıyı engeller (bilgi sızıntısı yok: auth yoksa yalnız 302).

## §15 Surucu Test Senaryoları (PHP runtime yok — kanıt ⚠️)

| # | Senaryo | Beklenen | Durum |
|---|---|---|---|
| 1 | `parseRange('bytes=0-499', 1000)` | `{206,0,499,500}` | ⚠️ VERIFICATION REQUIRED |
| 2 | `parseRange('bytes=0-100,200-300', 1000)` | `{200,…}` | ⚠️ VERIFICATION REQUIRED |
| 3 | `resolvePath('..\\x')` | 403 | ⚠️ VERIFICATION REQUIRED |
| 4 | `formatDuration(null)` | `00:00:00` | ⚠️ VERIFICATION REQUIRED |
| 5 | `Ulid::isValid` 27 karakter | false | ⚠️ VERIFICATION REQUIRED (anayasa verisi) |
| 6 | `Slugger::slug('Göksel', ['göksel'])` | `göksel-2` beklenir | ⚠️ VERIFICATION REQUIRED (K012/media verisi) |

**Kural:** Bu tablo beklentiyi taşır; çalıştırma kanıtı olmadan "geçti" yazılmaz.

## §16 Bilinen Boşluklar (genişletilmiş)

| # | Boşluk | Durum |
|---|---|---|
| 1 | `bootstrap.php` special-route kayıt satırı okunmadı | ⚠️ VERIFICATION REQUIRED |
| 2 | `ComponentLoader::make` injection akışı okunmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | `HomeSongButton::html` üretimi okunmadı | ⚠️ VERIFICATION REQUIRED |
| 4 | 416 yolunun `fail` olmadan exit yapması test edilmedi | UNKNOWN |
| 5 | `ignore_user_abort` + `flush` davranışı canlı doğrulanmadı | UNKNOWN |
| 6 | Loglama yokluğu — hatalar sessiz (error_log çağrısı YOK) | Gözlemlenebilirlik boşluğu (2026-10-13 okuma) |
| 7 | `If-Range` başlığı işlenmiyor (§11.2) | Bilinçli/bilinçsiz ayrımı UNKNOWN |
| 8 | `php -l` 3/3 kanıtı yok | ⚠️ VERIFICATION REQUIRED |

## §17 İddia Kayıt Defteri (genişletilmiş · yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Range çoklu aralık reddi RFC ile uyumlu | URL yok — kod yorumu satır 220/234 | 2026-10-13 | geçerli |
| Tembel bağlantı test dostu | URL yok — Repository satır 12-13 | 2026-10-13 | geçerli |
| If-Range işleme yokluğu | URL yok — 2026-10-13 tam dosya taraması (negatif iddia) | 2026-10-13 | geçerli |
| Loglama yokluğu | URL yok — üç dosya taraması (negatif iddia) | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §18 Bağımlılık Sürüşü (use → gerçekleşme)

| İçe aktarma | Ne zaman gerçekleşir | Atlanabilir mi? |
|---|---|---|
| `MusicRepository` (satır 5) | `dispatch` ctor'unda (satır 57) | Hayır — ctor zorunlu |
| `PageRouterHelper` (satır 6) | `authorize` içinde `new` (satır 125) | Hayır |
| `SessionBootstrapper` (satır 7) | `authorize` ilk satır (satır 123) | Hayır |
| `DatabaseConfig` + `DatabaseManager` (Repository 5-6) | İlk sorgu (`db()`, satır 99-109) | Evet — DB yoksa hiç gerçekleşmez |
| `AbstractComponent` (bileşen 5) | ctor `parent::__construct` (satır 65) | Hayır |
| `HomeLayoutVariant` (bileşen 6) | ctor parametresi (satır 63) | Hayır |
| `HomeSongButton` (bileşen 7) | kart map'leri (satır 70, 88) | Hayır — render |

**Kritik yol:** `dispatch` → `libraryPath()` + `MusicRepository::fromEnvironment()` → `run` → `authorize` (session) → `findFile` (İLK DB sorgusu — burada bağlantı açılır) → devam. Yani bağlantı, auth'dan SONRA açılır; oturumsuz istemci DB'ye hiç dokundurmaz (sıra §14 kuralı 1-2).

## §19 Kod Dışı Yüzeyler (bu servis için)

| Yüzey | Durum | Kanıt |
|---|---|---|
| HTTP header dışına çıkış | Yalnız `header()` + `echo` (satır 96, 102, 303-305, 320-328, 348, 365) | 2026-10-13 okuma |
| Dosya yazma | YOK — üç dosyada `fwrite`/`file_put_contents` çağrısı bulunamadı | negatif tarama |
| DB yazma | YOK — yalnız `SELECT` (anayasa §6) | negatif tarama |
| Ağ çağrısı (curl/fsockopen) | YOK | negatif tarama |
| Zamanlayıcı/cron | YOK | negatif tarama |
| Global state mutation | `$_SESSION['MM_*']` yazımı yalnız bypass dalında (satır 132-135) | tek nokta |

## §20 Sürüş Hesapları (byte matematiği)

| Hesap | Formül | Örnek (size=10 MiB) |
|---|---|---|
| Chunk sayısı | `ceil(length / CHUNK_BYTES)` | 10 chunk |
| Son chunk boyutu | `length - (chunks-1) * CHUNK_BYTES` | 0 (tam bölünme) veya kalan |
| 206 `length` | `end - start + 1` (satır 261) | 1 MiB aralık → 1048576 |
| Suffix `start` | `max(0, size - suffix)` (satır 250) | `bytes=-1` → size-1 |
| ETag girdisi uzunluğu | `strlen(path) + 1 + strlen(size) + 1 + strlen(mtime)` | değişken |
| `findRecentTracks` maks satır | 12 (LIMIT, satır 93) | 12 |
| Wide kart döngüsü | `ceil(10 / 4) = 3` seed geçişi, son geçişte 2 kart (anayasa §17) | 10 kart |

## §21 Sürüş-Denetim Matrisi (hangi bölüm hangi denetimi veriyor)

| Denetim | Surucu bölümü | Anayasa karşılığı |
|---|---|---|
| Auth sırası | §1, §14 | §3 |
| Yol karar ağacı | §2 | anayasa §2 (sözleşme satırları) |
| Range/yanıt matrisi | §3, §12 | anayasa §2 (Range tek aralık) |
| Bileşen veri zinciri | §4, §5 | anayasa §5 (varyant matrisi) |
| Bağlantı tembelliği | §6, §18 | anayasa §4 (`db()`) |
| Hız/sınır | §8, §20 | anayasa §2 (CHUNK sabiti) |
| Test beklentileri | §15 | — (kanıt yok, ⚠️) |

## §22 Verbatim Sürüş Blokları

### §22.1 run — Dosya: MusicStreamHandler.php / Satır: 60-113

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

### §22.2 authorize — Dosya: MusicStreamHandler.php / Satır: 117-159

```php
    /**
     * home'un auth guard'ı (PageRouterHelper = AuthGuard'ın baktığı session anahtarları).
     * ADR-008 bypass davranışı BypassAuthMiddleware ile birebir — fail-closed.
     */
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

### §22.3 emitBaseHeaders — Dosya: MusicStreamHandler.php / Satır: 310-329

```php
    private function emitBaseHeaders(string $mime, int $length, string $etag, string $lastMod): void
    {
        if (function_exists('ini_set')) {
            @ini_set('zlib.output_compression', '0');
        }
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
        @ignore_user_abort(true);

        header_remove('Content-Type');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $length);
        header('ETag: ' . $etag);
        header('Last-Modified: ' . $lastMod);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Content-Disposition: inline');
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . $mime);
    }
```

**Blok bütünlüğü:** Üç blok da 2026-10-13 tam dosya okumasının birebir devamıdır; kırpma yoktur.

## §23 Sürüş Özeti Tablosu (tek bakış)

| Soru | Cevap | Satır |
|---|---|---|
| Auth olmadan dosya varlığı öğrenilebilir mi? | Hayır — 302, dosya adı işlenmeden | 62, 139-143 |
| Range olmadan seek mümkün mü? | Hayır — tam gövde 200 | 226-231 |
| Çoklu-range istemci desteklenir mi? | Hayır — 200 tam gövde | 229 |
| DB yoksa bileşen boş mu görünür? | Hayır — PNG demo verisi | bileşen 67 |
| Bağlantı ne zaman açılır? | İlk sorguda (findFile) | 99-109 |
| Gövde ne zaman yazılır? | Tüm başlıklar + status sonrası | 112 |
| HEAD isteği gövde yazar mı? | Hayır — başlık sonrası exit | 108-110 |
| 416'da `fail` metni gider mi? | Hayır — boş gövde + Content-Range | 95-99 |

## §24 Sürüş Kıyaslama (stream vs bileşen)

| Kriter | StreamHandler | RecentTracksComponent |
|---|---|---|
| Çalışma anı | HTTP isteği (special-route) | Sayfa render (home.php) |
| Çıkış | byte akışı / HTTP kodu | HTML kart listesi |
| DB kullanımı | 1 sorgu (findPrimaryFile) | 1 sorgu (findRecentTracks) veya 0 |
| Hata stratejisi | fail-closed (403/404/302) | sessiz fallback (null → demo) |
| Auth | zorunlu | sayfa düzeyinde (handler dışında) |
| Range | destekli | ilgisiz |
| `exit` | 6 noktada | yok |

**Ortak payday:** İkisi de `MusicRepository` üzerinden tek repository instance'ı kullanır; bağlantı tembelliği her iki yolu da korur.

## §25 Değişiklik Kaydı

| Tarih | Değişiklik | Kanan |
|---|---|---|
| 2026-10-13 | İlk yazım — 11 sürüş bölümü: ana akış, resolvePath ağacı, Range/yanıt, bileşen zinciri, bağlantı, uç nokta eşlemesi, hız | Vault Steward oturumu |
| 2026-10-13 | Genişletme §11-§24 — senaryo matrisleri, edge case'ler, zamanlama, güvenlik sırası, test beklentileri, verbatim bloklar, kıyaslama | Vault Steward oturumu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
