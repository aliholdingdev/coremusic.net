---
title: "Command-Query — Örnekler (PHP · JS · SQL · CSS)"
type: kod
kodeme: kademe-2
module: K011-application
parent: command-query
status: active
updated: 2026-10-15
group: kod
---

# K011-application/command-query — Örnekler

**Amaç:** Komut/sorgu ayrımının dört dilde (PHP, JS, SQL, CSS) gerçek repo
bloklarıyla gösterimi. Her kod bloğu `Dosya: … · Satır: …` künyesi taşır; künyedeki
dosya ve satır 2026-10-15'te diskten doğrulanmıştır. Her dilde bir `YAPMA ↔ YAP`
karması vardır; PLANNED yüzeyler `⚠️ VERIFICATION REQUIRED` etiketiyle ayrılır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | PHP — komut ucu (Gateway, RouteTable) | kod | 118 agent · 351 skill | Komutlar |
| §2 | PHP — veri doğrulama / aritmetik / bitwise | kod | 381 ADR · 43 C4 | Komutlar |
| §3 | PHP — alternative syntax (view katmanı) | kod | 694 prompt · 50 kural | Komutlar |
| §4 | JS — sorgu ucu (FetchWrapper, gender-select) | kod | 4 hook · 24 command | Komutlar |
| §5 | SQL — sorgu disiplini (SELECT * yasağı, LIMIT boşluğu) | kod | 24 workflow · 28 workflow | Komutlar |
| §6 | CSS — attribute sorgusu (data-* ile durum okuma) | kod | 6 domain prompt · 12 example | Sorgular |
| §7 | Diller arası omurga — bir isteğin 4 dili | kod | 151 ekran · 37 flow | Sorgular |
| §8 | Kesişim / havuz kaydı | kanıt | 227 wiki-link · 5 test | Sorgular |
| §9 | Genişletilmiş YAPMA↔YAP (pipeline & dispatch) | kod | Gateway · ApiMiddlewarePipeline | Komutlar |

---

## §1 PHP — Komut Ucu

### §1.1 Gateway::dispatch — komutun giriş kapısı (GERÇEK)

```php
// Dosya: shared/src/Api/Gateway.php · Satır: 50
public function dispatch(array $request): array
{
    // Create API request object
    $apiRequest = new ApiRequest();
    // Resolve API version
    $version = $this->versionResolver->resolve($apiRequest);
    $apiRequest = $apiRequest->withAttribute('version', $version);
    // ...
```

**Künye:** `Dosya: shared/src/Api/Gateway.php · Satır: 50` — tek halkalı komut girişi:
HTTP `$request` dizisi → `ApiRequest` → versiyon çözümleme → pipeline. Docblock
(`Satır: 44-49`) route eşleşmesinin pipeline **öncesinde** olduğunu ve eşleşmeyen
rotalarda bile CORS/rate-limit/auth'un atlanmadığını söyler (ADR-020 §1.1-B.1).

### §1.2 Handler çağrısı — komutun yürütülmesi (GERÇEK)

```php
// Dosya: shared/src/Api/Gateway.php · Satır: 152, 155
private function invokeHandler(array $route, array $request): array
{
    // ...
    $response = ($this->handlerResolver)($route, $request);
```

**Künye:** `Dosya: shared/src/Api/Gateway.php · Satır: 152, 155` — handlerResolver
**değişken fonksiyon** olarak çağrılır (php.net variable-functions claim'i ile
eşleşen repo örneği: bkz §8 claim matrisi).

### §1.3 RouteTable::defaults — komut envanteri (GERÇEK)

```php
// Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 92-104
public static function defaults(): self
{
    return self::fromArray([
        'GET' => [
            '/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],
            '/api/v1/user'     => ['service' => 'user',     'handler' => 'userController'],
            '/api/v1/music'    => ['service' => 'music',    'handler' => 'musicController'],
            '/api/v1/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],
            '/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
            '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],
        ],
    ]);
}
```

**Künye:** `Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 92-104` — 6 GET rotası,
service+handler eşlemesi. Dosya 253 satır: `match()` 111 · `normalize()` 242 ·
durum sabitleri 28-30 (`STATUS_OK`, `STATUS_METHOD_NOT_ALLOWED`, `STATUS_NOT_FOUND`).

### §1.4 Middleware zinciri — komutun aktarımı (GERÇEK)

```php
// Dosya: shared/src/Api/Middleware/ApiMiddlewarePipeline.php · Satır: 40, 44
return $middleware($request, $pipeline);
// ...
return $pipeline($request);
```

```php
// Dosya: shared/src/Api/Middleware/AuthenticationMiddleware.php · Satır: 63, 68, 79, 96
return $next($request);
```

**Künye:** `ApiMiddlewarePipeline.php · Satır: 40, 44` ve
`AuthenticationMiddleware.php · Satır: 63, 68, 79, 96` — `$middleware` / `$next`
**değişken fonksiyon** çağrısı; pipeline'ın komutu aktarma biçimi.
`AuthorizationMiddleware.php · Satır: 33, 39, 57, 86` aynı kalıptır.

### §1.5 YAPMA ↔ YAP (PHP komut)

```text
YAPMA                                         YAP (repo kanıtı)
──────                                        ──────────────────
Handler'ı doğrudan controller'da              Gateway::dispatch → middleware pipeline
çağırmak (routing'i atlamak)                  → invokeHandler (Gateway.php:50,152,155)
                                              — route eşleşmesi pipeline ÖNÇESİ
                                              (Gateway.php:47-48, ADR-020 §1.1-B.1)

Route'ları string concat ile                  RouteTable::defaults + fromArray
kurmak                                        (RouteTable.php:92-104), normalize()
                                              tek noktada (RouteTable.php:242)

Middleware'i elle zincirlemek                 ApiMiddlewarePipeline üzerinden
($a($b($c(...))))                             $middleware/$pipeline aktarımı
                                              (ApiMiddlewarePipeline.php:40,44)
```

---

## §2 PHP — Doğrulama, Aritmetik, Bitwise

### §2.1 Komut girdisi doğrulama — min/max klatz (GERÇEK)

```php
// Dosya: shared/src/Api/Dto/Request/MusicSearchRequest.php · Satır: 76-77
page:  max(1, $data['page']  ?? 1),
limit: min(100, max(1, $data['limit'] ?? 20))
```

**Künye:** `Dosya: shared/src/Api/Dto/Request/MusicSearchRequest.php · Satır: 76-77` —
sorgu parametresi komut verisine dönüşürken **aritmetik operatörler** (`max`, `min`)
ile sınırlandırılır: `page ≥ 1`, `1 ≤ limit ≤ 100`, varsayılan 20. Dosya 94 satır.

### §2.2 UuidV7 — bitwise versiyon/variant markalama (GERÇEK)

```php
// Dosya: shared/src/Security/UuidV7.php · Satır: 27-28
$clockSeq[0] = chr((ord($clockSeq[0]) & 0x0F) | 0x70); // version 7
$clockSeq[1] = chr((ord($clockSeq[1]) & 0x3F) | 0x80); // variant 1
```

**Künye:** `Dosya: shared/src/Security/UuidV7.php · Satır: 27-28` — bitwise AND/OR
ile UUIDv7'nin version nibble'ı (`0x70`) ve variant bitleri (`0x80`) zorlanır.
php.net bitwise-operators claim'inin repo karşılığı.

### §2.3 YAPMA ↔ YAP (PHP doğrulama/bitwise)

```text
YAPMA                                         YAP (repo kanıt)
──────                                        ──────────────────
$limit = $_GET['limit'];                      min(100, max(1, $data['limit'] ?? 20))
(sınırsız, tipli değil)                       (MusicSearchRequest.php:77)

Versiyon bitini                                ($b & 0x0F) | 0x70 — tek satırda
string replace ile set etmek                   maskele+birleştir (UuidV7.php:27-28)
```

---

## §3 PHP — Alternative Syntax (view/şablon katmanı)

### §3.1 foreach / if alternatif sözdizimi (GERÇEK)

```php
// Dosya: home.coremusic.net/pages/components/recent-tracks.php · Satır: 21, 25, 75
<?php if ($this->variant->isWide()): ?>
...
<?php foreach ($this->cards as $card): ?>
...
<?php endif; ?>
```

**Künye:** `Dosya: home.coremusic.net/pages/components/recent-tracks.php · Satır: 21, 25, 75`
— HTML içine gömülü PHP'de `if(): … endif;` ve `foreach(): … endforeach;` alternatif
sözdizimi kullanılır (php.net alternative-syntax claim'inin repo karşılığı; satır 75
`endif;` kapanışı, blok aralığı 21-75).

### §3.2 YAPMA ↔ YAP (alternative syntax)

```text
YAPMA                                         YAP (repo kanıt)
──────                                        ──────────────────
<?php foreach ($c as $x): ?>                  Aynı kalıp DOĞRU — ama kapamayı
… <?php } ?>  (karışık sözdizimi)             unutmamak: recent-tracks.php:21/25/75
                                              tek dosyada tutarlı `if():…endif;`
                                              ve `foreach():…endforeach;`
```

> **Not:** Bu klasör (components) **query/sunucu-render** yüzeyidir; komut (yazma)
> tarafı `shared/src/Api/` §1'dedir. Ayrım: §1 yazma/karar, §3 yalnız okuma/biçim.

---

## §4 JS — Sorgu Ucu

### §4.1 FetchWrapper — GET sorgusu, header birleştirme (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 46-52
async #doFetch(url, signal, attempt) {
    const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };
    const csrfHeader = this.#csrfSync?.getHeader();
    if (csrfHeader) Object.assign(headers, csrfHeader);
    const traceId = this.#logger?.traceId;
    if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
    return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
}
```

**Künye:** `Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 46-52` —
sorgu `method: 'GET'` (Satır 52), kimlik `credentials: 'include'`, iptal `signal`.
Dosya 63 satır; `#delayWithAbort` (55-62) `abort` event'ini `{ once: true }` dinler.

### §4.2 gender-select — komut (POST) gönderimi (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/auth/gender-select.js · Satır: 43-57
const params = {};
if (redirectUri) params['redirect_uri'] = redirectUri;
const qs = Object.keys(params).length ? '?' + new URLSearchParams(params).toString() : '';
const postUrl = '/set-gender' + qs;

fetch(postUrl, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': document.querySelector('[name=csrf_token]').value
    },
    body: JSON.stringify({ gender: gender }),
    credentials: 'include'
})
```

**Künye:** `Dosya: assets.coremusic.net/js/auth/gender-select.js · Satır: 43-57` (redirect
künyesi Satır 20: `URLSearchParams(…).get('redirect_uri')`; fetch Satır 48) — komut
yanıtı: `method: 'POST'` + `X-CSRF-Token` + JSON body. Dosya 86 satır.

### §4.3 YAPMA ↔ YAP (JS)

```text
YAPMA                                         YAP (repo kanıt)
──────                                        ──────────────────
fetch(url) — header'sız,                      #doFetch: X-Requested-With + Accept +
CSRF'siz, trace'siz                           CSRF + traceId tek noktada birleşir
                                              (FetchWrapper.js:47-52)

POST'ta CSRF token gövdeye                    X-CSRF-Token header'ında (JSON body ile)
gömme / hiç koymamak                          (gender-select.js:53)
```

---

## §5 SQL — Sorgu Disiplini

### §5.1 `SELECT *` yasağı — kural yazılmış (GERÇEK KANIT)

```sql
-- Dosya: .ai/.sql/mysql/media_catalog.sql · Satır: 18
-- ORM     : YOK (ADR-002) → SELECT * : YOK (ADR-002)
-- Dosya: .ai/.sql/mysql/media_catalog.sql · Satır: 431
--   gereği açıkça listelenir (SELECT * yok). donem/tur gibi kapalı sözlük
```

**Künye:** `Dosya: .ai/.sql/mysql/media_catalog.sql · Satır: 18, 431` — `SELECT *`
yasağı şemanın kendisinde yorum olarak kayıtlıdır (ADR-002).

### §5.2 View örneği — açık sütun listesi (GERÇEK)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_musics.sql · Satır: 508
CREATE ALGORITHM=UNDEFINED DEFINER=`Ali`@`%` SQL SECURITY DEFINER VIEW
`coremusic_musics`.`v_music_recording_map` AS
select `m`.`id` AS `music_id`,`r`.`id` AS `recording_id`,`m`.`title` AS `title`,
`r`.`isrc` AS `isrc` from (`coremusic_musics`.`musics` `m`
left join `coremusic_musics`.`recordings` `r` on((`r`.`legacy_music_id` = `m`.`id`)));
```

**Künye:** `Dosya: .ai/.sql/mysql/coremusic_musics.sql · Satır: 508` — view'da bile
sütunlar **tek tek** listelenir; `*` yok.

### §5.3 Ölçüm: LIMIT yok, prepared statement PHP'de (2026-10-15)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `SELECT ` | `.ai/.sql/mysql/*.sql` (20 dosya) | **3 isabet** (1 view + 2 yorum) |
| `LIMIT ` / `OFFSET ` | `.ai/.sql/mysql/*.sql` | **0** |
| `LIMIT ?` / `LIMIT $` / `OFFSET` | `shared/src`, `api.coremusic.net`, `home.coremusic.net` (vendor dışı PHP) | **0** |
| `->prepare(` | `shared/src/**` | **8+ isabet** — `Database/DatabaseManager.php:32,40` · `OAuth/OAuthRepository.php:46,59,73,90,101,117` |

**Sonuç:** `.sql` dosyaları **DDL/şema** katmanıdır (migration); çalıştırılabilir sorgu
PHP'de `prepare()` ile yaşar. Sayfalama (`LIMIT/OFFSET`) ne şemada ne de bu taramada
göründü — **`⚠️ VERIFICATION REQUIRED`**: pagination nerede uygulanıyor?
(`command-query-kapsam.md` §dolgusu ile ilişkili boşluk.)

### §5.4 YAPMA ↔ YAP (SQL)

```text
YAPMA                                         YAP (repo kanıt)
──────                                        ──────────────────
SELECT * FROM musics;                         View'da açık sütun listesi
                                              (coremusic_musics.sql:508);
                                              şemada yasak kaydı (media_catalog.sql:18)

Sorguyu string birleştirme ile               ->prepare() + bind (DatabaseManager.php:32,40;
kurmak                                       OAuthRepository.php:46,59,73,90,101,117)
```

---

## §6 CSS — Attribute Sorgusu (durum okuma)

### §6.1 `html[data-mode]` — en yüksek öncelikli override (GERÇEK)

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css · Satır: 143-150 */
html[data-mode="light"] {
  /* --- Background --- */
  --bg-base:        #f8f7fc;
  --bg-surface:     #ffffff;
  --bg-elevated:    #f0eef5;
  --bg-overlay:     rgba(0, 0, 0, 0.50);
  --bg-subtle:      rgba(0, 0, 0, 0.03);
  --bg-muted:       rgba(0, 0, 0, 0.06);
}
```

### §6.2 Fallback — attribute yoksa sistem tercihi (GERÇEK)

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css · Satır: 38-39 */
@media (prefers-color-scheme: light) {
  html:not([data-mode]) {
```

**Künye:** `Dosya: …/a-color-mode-tokens.css · Satır: 38-39, 143-150` — CSS burada
**sorgu** yapar: `html[data-mode="light"]` var mı? `html:not([data-mode])` var mı?
Öncelik yorumu dosyanın Satır 21-23'ünde yazılıdır: `[data-gender]` → `html[data-mode]`
(en yüksek). Diğer selector satırları: 217, 228, 239, 253, 259. Dosya 379 satır;
`Css/**` genelinde `[data-` isabeti **213**.

### §6.3 YAPMA ↔ YAP (CSS)

```text
YAPMA                                         YAP (repo kanıt)
──────                                        ──────────────────
body.light { … } — class sarma,               html[data-mode="light"] { --bg-*: … }
JS her yerde classList.toggle                (a-color-mode-tokens.css:143);
(className'ı string olarak                  JS yalnız attribute yazar
dağınık yazmak)                              (ThemeManager.js:87 data-gender, :166 data-mode)

Sistemi tamamen yok saymak                   html:not([data-mode]) fallback +
                                              @media (prefers-color-scheme: light)
                                              (a-color-mode-tokens.css:38-39)
```

---

## §7 Diller Arası Omurga — Bir İsteğin 4 Dili

Aynı kullanıcı eylemi (cinsiyet kaydı) dört dilde nasıl yansır:

| Katman | Dosya · Satır | Rol | Komut mu Sorgu mu |
|---|---|---|---|
| JS tetikleyici | `js/auth/gender-select.js:48-57` | `POST /set-gender` + CSRF | **Komut** |
| PHP eşleme | `shared/src/Api/Routing/RouteTable.php:92-104` | rota → service/handler | Yönlendirme |
| PHP yürütme | `shared/src/Api/Gateway.php:50,152,155` | pipeline + handlerResolver | **Komut** |
| CSS yansıması | `Css/01_Abstracts/a-color-mode-tokens.css:143` (+ `217,228,239,253,259`) | `html[data-mode]`/`[data-gender]` okuma | **Sorgu** |

> Bu tablo `command-query-kapsam.md` §5 (senaryo) ile uyumludur; bu dosya yalnız
> **kod bloğu** kanıtını taşır.

---

## §8 Kesişim / Havuz Kaydı

### §8.1 Havuz URL'leri (spec §9 — command-query, 4 URL) ve HTTP durumları

| # | URL | Komut | HTTP |
|---|-----|-------|------|
| 1 | variable-functions | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/functions.variable-functions.php` | 200 |
| 2 | alternative-syntax | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/control-structures.alternative-syntax.php` | 200 |
| 3 | arithmetic | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/language.operators.arithmetic.php` | 200 |
| 4 | bitwise | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/language.operators.bitwise.php` | 200 |

> **Toplam:** 4/4 **HTTP 200** · havuz dışı erişim **0**.

### §8.2 Claim ↔ repo özeti

| Claim (havuz) | Repo karşılığı | Kanıt |
|---|---|---|
| variable-functions | handlerResolver / `$middleware` / `$next` çağrıları | `Gateway.php:155` · `ApiMiddlewarePipeline.php:40,44` · `AuthenticationMiddleware.php:63` |
| alternative-syntax | `if():…endif;` / `foreach():…endforeach;` | `recent-tracks.php:21,25,75` |
| arithmetic | `max`/`min` clamp | `MusicSearchRequest.php:76-77` |
| bitwise | `& 0x0F \| 0x70`, `& 0x3F \| 0x80` | `UuidV7.php:27-28` |

### §8.3 Kesişim kontrolü (2026-10-15 — GERÇEK çalıştırma)

> **Envanter dosyası:** `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` (4286 satır)
> **Sonuç:** `HIT = 0` — 4 havuz URL'sinin hiçbiri envanterde yok → hepsi havuzda kalır.

```powershell
# --- K011 command-query KESİŞİM KONTROLÜ (2026-10-15, çalıştırıldı) ---
$envanter = "C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt"
$envanterUrls = Get-Content $envanter | Where-Object { $_ -match '^https?://' }
$havuz = @(
  "https://www.php.net/manual/en/functions.variable-functions.php",
  "https://www.php.net/manual/en/control-structures.alternative-syntax.php",
  "https://www.php.net/manual/en/language.operators.arithmetic.php",
  "https://www.php.net/manual/en/language.operators.bitwise.php"
)
$hit = 0
foreach ($u in $havuz) {
  if ($envanterUrls -contains $u) { $hit++; Write-Output "HIT  - $u" }
  else { Write-Output "MISS - $u" }
}
Write-Output "KESISEM: HIT=$hit / havuz=$($havuz.Count) / envanter_satir=$($envanterUrls.Count)"
# SONUÇ: KESISEM: HIT=0 / havuz=4 / envanter_satir=4286
```

---

## §9 Genişletilmiş YAPMA↔YAP Çiftleri (pipeline & dispatch)

> Bu bölüm, §1-§6'daki çiftlere ek olarak `Gateway::dispatch` ve pipeline desenlerinden
> gerçek kod kanıtıyla çıkarılmış YAPMA↔YAP çiftleridir. Her satır dosya:satır kanıtlıdır.

### §9.1 Rota eşlemesi nerede yapılır?

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | Rota eşlemesini middleware pipeline'ının **içine** koy (405/404 durumu middleware'e ulaşmaz) | Rota eşlemesini **pipeline dışında** yap, sonucu `_route` / `_route_status` / `_route_allow` olarak isteğe yaz → `Gateway.php:62-65` |

> **Kanıt (Gateway.php:59-61 yorumu):** "Rota eşlemesi pipeline'ın DIŞINDA yapılır ki
> `_route` (ve 405/404 durumu) Authentication / Authorization / RequestValidation /
> ResponseNormalization middleware'lerine ulaşsın."

### §9.2 404/405 üretimi — elle mi, tek fabrika ile mi?

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | Her yerde `['status' => 404, ...]` dizisi elle kur | Statik fabrika kullan → `ApiResponse::error('NOT_FOUND', 'Route not found', 404)` (`Gateway.php:79`) |
| 2 | 405 için ayrı ayrı `header('Allow: ...')` yaz | `methodNotAllowedResponse($allow)` tek noktada (`Gateway.php:125`, `allow` L65'ten gelir) |

### §9.3 Pipeline ters sıralama

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | Middleware'leri ters sarmalayıp sonra ters sırayla çalıştır (çift ters — sıra bozulur) | Kayıt sırası = çalışma sırası; `array_reverse` ile **tek ters** sarmala → `ApiMiddlewarePipeline.php:33-39` |

```php
// YAP — ApiMiddlewarePipeline.php:31-39 (gerçek kod)
$pipeline = $handler;
foreach (array_reverse($this->middlewares) as $middleware) {
    $pipeline = function (array $request) use ($middleware, $pipeline) {
        return $middleware($request, $pipeline);
    };
}
return $pipeline($request);
```

> **Not:** `$pipeline($request)` — **değişken-fonksiyon çağrısı** (havuz claim'i §1).
> İlk `pipe()` edilen middleware en dışta çalışır (Onion).

### §9.4 Middleware imzası — invokable + next callback

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | Middleware'i düz class olarak yaz, sırayı `if` ile yönet | `__invoke(array $request, callable $next): array` — her middleware zincirin bir halkası → `AuthenticationMiddleware.php:54`, `AuthorizationMiddleware.php:25`, `RateLimitMiddleware.php:34`, `RequestValidationMiddleware.php:46`, `ResponseNormalizationMiddleware.php:27` |

### §9.5 Hata yakalama + TraceId

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | Hata mesajını ham döndür (iç sızıntısı) | `catch (\Throwable $e)` içinde `UuidV7::generate()` ile **TraceId** üret, log'a yaz, istemciye genel mesaj dön → `Gateway.php:87-95` |

> **Bitwise bağlantısı:** `UuidV7::generate()` → `UuidV7.php:22,23,27,28` (shift `>>` +
> AND `&` + OR `|` — havuz claim'i §2.4/web-arastirma §9). TraceId üretimi bitwise
> operatörleriyle UUID v7 kurar.

### §9.6 DTO clamp — aritmetik

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | `limit` değerini doğrudan SQL'e geçir (sonsuz sayfa) | `min(100, max(1, $data['limit'] ?? 20))` — iç içe clamp → `MusicSearchRequest.php:77`; `page: max(1, ...)` → `:76` |

---

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
