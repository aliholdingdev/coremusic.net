---
title: "K011 · Server-Runtime — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: server-runtime
status: active
updated: 2026-10-15
group: mimari
---

# K011 · Server-Runtime — Kapsam (Mimari)

> **Amaç:** `server-runtime` alt-modülünün ne kapsadığını, sınırını (ne DEĞİL),
> runtime yaşam döngüsünü ve CoreMusic repo'sundaki gerçek sunucu/zamanı-işleyiş
> bileşenlerini ELI15 düzeyinden kanıt satırlarına kadar tanımlamak.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 ELI15 & tanım | mimari | kapsam | Okuma sırası başı |
| 2 | §2 Sınır — ne DEĞİL | mimari | sinir | Kapsam dışı netleştirme |
| 3 | §3 Katman & bağımlılık | mimari | katman | Sunucu → boot → pipeline → emit |
| 4 | §4 Runtime yaşam döngüsü | mimari | akış | Uçtan uca akış |
| 5 | §5 Çekirdek bileşenler | mimari | bilesen | Envanter + kod kanıtı |
| 6 | §6 Sunucu katmanı desenleri | mimari | desen | Tekrar eden kalıplar |
| 7 | §7 Kurallar & yasaklar | mimari | kural | Bağlayıcı kısıtlar |
| 8 | §8 Kapsam dışı | mimari | sinir | Ne yapılmaz |
| 9 | §9 Güvenlik etkileşimi | mimari | security | CSP/header/rewrite |
| 10 | §10 Açık sorular | mimari | verification | ⚠️ işaretli boşluklar |
| 11 | §A Ek — terimler | mimari | referans | Sözlük |

---

## §1 ELI15 & Tanım

### §1.1 ELI15

`server-runtime` = **bir isteğin sunucuya düştüğü andan yanıtın pusetlenip
kapanışına kadar olan yolun sahibi**. Tarayıcı bir adres yazdığında önce sunucu
katmanı (Apache `.htaccess` veya IIS `web.config`) rewrite ile isteği
`index.php`'ye yönlendirir; PHP çalışmaya başlayınca `RuntimeBootstrap` saat/
hata ayarlarını kurar; API girişinde middleware zinciri sırayla çalışır; en sonda
`ResponseEmitter` gövdeyi + başlıkları gönderip `exit` eder. Hata olursa
`ErrorHandler`/catch bloğu devreye girer. Bu modül yolu, kuralı ve kapanışı
anlatır — sorguların kendisini değil (o `queries` modülünün işi).

### §1.2 Tanım tablosu

| Terim | Tanım | Repo karşılığı |
|---|---|---|
| Runtime bootstrap | İstek başında PHP ortamını (timezone, error_reporting) kuran tek çağrı | `RuntimeBootstrap::boot()` — `RuntimeBootstrap.php` L7 |
| Front controller | Tüm isteklerin düştüğü tek PHP dosyası | `api.coremusic.net/index.php` (223 satır) |
| Rewrite | Sunucu düzeyinde isteği front controller'a yönlendirme | `.htaccess` L26-37 (Apache) · `web.config` L14-41 (IIS) |
| Emitter | HTTP durum + başlık + gövdeyi yazıp süreci bitiren sınıf | `ResponseEmitter` (163 satır) |
| Fallback header | Pipeline dışında üretilen yanıt için yedek güvenlik başlıkları | `ResponseEmitter::applyFallbackSecurityHeaders()` L132 |
| Trace-Id | İsteği log'lar arasında bağlayan kimlik başlığı | `X-Trace-Id` — L97 · JS tarafı `FetchWrapper.js` L50-51 |
| ETag/304 | Gövdenin xxh64 hash'i ile koşullu GET önbelleği | `ResponseEmitter.php` L60-66 |

### §1.3 Kapsam beyanı

| # | Kapsam içi | Kanıt |
|---|---|---|
| 1 | Boot/boot-time ortam kurulumu | `Dosya: shared/src/Bootstrap/RuntimeBootstrap.php · Satır: 7-20` |
| 2 | Yanıt emit (JSON/HTML/redirect) | `Dosya: shared/src/PageRouter/ResponseEmitter.php · Satır: 16-92` |
| 3 | Hata sayfası üretimi | `Dosya: shared/src/PageRouter/ErrorHandler.php · Satır: 11-76` |
| 4 | Yapılandırılmış log | `Dosya: shared/src/PageRouter/StructuredLogger.php · Satır: 55-73` |
| 5 | Log fabrikası (singleton) | `Dosya: shared/src/Log/LoggerFactory.php · Satır: 15-22` |
| 6 | APCu cache adaptörü | `dosya: shared/src/Cache/ApcuAdapter.php · Satır: 7-55` |
| 7 | API runtime pipeline girişi | `Dosya: api.coremusic.net/index.php · Satır: 44, 129-171` |
| 8 | Apache sunucu kuralları | `Dosya: home.coremusic.net/.htaccess · Satır: 1-37` |
| 9 | IIS sunucu kuralları | `Dosya: home.coremusic.net/web.config · Satır: 1-43` |

---

## §2 Sınır — ne DEĞİL

| # | Kapsam dışı | Nereye ait | Kanıt |
|---|---|---|---|
| 1 | SQL sorguları ve repository'ler | `queries` modülü | Kardeş modül kapsamı |
| 2 | Middleware'lerin iç mantığı (CSRF/CORS doğrulama) | K011 middleware/modül | Burada yalnız **sıra ve yerleşim** anlatılır |
| 3 | Route tablosu içeriği | `config/routes.php` sahipliği (Backend Architect) | `api.coremusic.net/AGENTS.md` §3.2 |
| 4 | nginx üretim yapılandırması | PLANNED — diskte nginx conf dosyası yok | ⚠️ VERIFICATION REQUIRED (§10.4) |
| 5 | PHP-FPM pool ayarları | Sunucu ortamı — vault'a yazılmadı | ⚠️ VERIFICATION REQUIRED (§10.5) |

---

## §3 Katman & Bağımlılık

### §3.1 Katman şeması

```text
[İstemci / SPA · FetchWrapper.js]
        │ HTTPS
        ▼
[Sunucu katmanı — DOKUNULMAZ]
  Apache: .htaccess   ·  IIS: web.config
  (rewrite · header · dosya engelleme)
        │ index.php
        ▼
[Boot] RuntimeBootstrap::boot(DEBUG_MODE)          index.php L44
        │ timezone L9 · error_reporting L11-17 · log_errors L19
        ▼
[Pipeline] (sabit sıra — ADR-020 §1.1-B.1)        index.php L129-171
  ResponseNormalization → OriginCheck → Cors → RateLimit
    → Authentication → CSRF (yalnız session) → RequestValidation → Authorization
        │
        ▼
[Gateway dispatch]                                  index.php L173-182
        │
        ▼
[Emit] ResponseEmitter::sendJson/sendHtml/redirect → exit
```

### §3.2 Bağımlılık tablosu (kanıtlı)

| Kenar | Yön | Kanıt |
|---|---|---|
| `index.php` → `RuntimeBootstrap` | aşağı | `api.coremusic.net/index.php` L44 |
| `index.php` → `LoggerFactory` | aşağı | L47 |
| `index.php` → `ApcuAdapter` / `MemoryAdapter` | aşağı (koşullu) | L143-145 — `apcu_enabled()` kontrolü |
| `index.php` → middleware pipeline | aşağı | L147-171 |
| `index.php` → `Gateway` | aşağı | L173-182 |
| `ErrorHandler` → `HtmlShellRenderer` | aşağı | `ErrorHandler.php` L7-9 |
| `StructuredLogger` → `Redactor` | aşağı | `StructuredLogger.php` L5, L72 |
| `ResponseEmitter` → (bağımlılık yok) | — | final sınıf, yalnız `header()`/`exit` (L43-163) |

---

## §4 Runtime Yaşam Döngüsü

### §4.1 API isteği — adım adım

| # | Adım | Kod | Kanıt |
|---|---|---|---|
| 1 | autoload + config yükleme | `require_once` + `RouteTable::fromArray` | `index.php` L14, L38-42 |
| 2 | boot | `RuntimeBootstrap::boot(DEBUG_MODE)` | L44 |
| 3 | logger kur | `LoggerFactory::getInstance(...)` | L47 |
| 4 | HTTPS tespiti (X-Forwarded-Proto dahil) | `$isHttps` | L50-53 |
| 5 | `/v1/*` → `/api/v1/*` normalizasyonu | alias bloğu | L87-94 |
| 6 | health kısa devre | `/health` → 200 JSON | L101-108 |
| 7 | pipelineRequest paketleme | server/method/uri/headers/body | L121-127 |
| 8 | pipeline kur + dispatch | `->pipe(...)` + `dispatch` | L147-182 |
| 9 | header'ları yaz | `foreach ... header()` | L184-187 |
| 10 | preflight 204 veya JSON yanıt | `halt` → 204 · `$jsonResponse` | L193-200 |
| 11 | yakalanmayan hata | `catch \Throwable` → 500 | L201-214 |
| 12 | bilinmeyen uç | 404 `NOT_FOUND` | L217-223 |

### §4.2 Hata akışı

| Durum | Davranış | Kanıt |
|---|---|---|
| Pipeline içi yakalanan hata | `catch \Throwable` — log'a `file:line` + trace, istemciye yalnız `{error:{code:SERVER_INTERNAL_ERROR}}` | `index.php` L201-214 |
| Exception mesajı sızıntısı YOK | Dosya dokümanı: "exception mesajı ASLA istemciye dönmez" | `index.php` L11 |
| 403/404/429 HTML (SPA) | `ErrorHandler::buildErrorHtmlResult` match 403/404/429 | `ErrorHandler.php` L55-76 |
| 404/403/410/500 kabuklu sayfa | `buildErrorPage` match + `HtmlShellRenderer` | L11-37 |

---

## §5 Çekirdek Bileşenler

| # | Bileşen | Dosya | Boyut | Rol | Kanıt |
|---|---|---|---|---|---|
| 1 | `RuntimeBootstrap` | `shared/src/Bootstrap/RuntimeBootstrap.php` | 21 satır | Boot: timezone + error ayarları | L7-20 |
| 2 | `ResponseEmitter` | `shared/src/PageRouter/ResponseEmitter.php` | 163 satır | Emit JSON/HTML/redirect + fallback CSP | L16-163 |
| 3 | `ErrorHandler` | `shared/src/PageRouter/ErrorHandler.php` | 77 satır | Hata sayfası/HTML sonucu | L11-76 |
| 4 | `StructuredLogger` | `shared/src/PageRouter/StructuredLogger.php` | 81 satır | JSON log + redaction + traceId | L55-73 |
| 5 | `LoggerFactory` | `shared/src/Log/LoggerFactory.php` | 34 satır | Singleton `FileHandler` | L15-22 |
| 6 | `ApcuAdapter` | `shared/src/Cache/ApcuAdapter.php` | 56 satır | APCu cache (yoksa `false`) | L7-55 |
| 7 | API front controller | `api.coremusic.net/index.php` | 223 satır | Boot → pipeline → emit zinciri | L44-L223 |
| 8 | Apache kuralları | `home.coremusic.net/.htaccess` | 37 satır | Rewrite + header + engel | L1-37 |
| 9 | IIS kuralları | `home.coremusic.net/web.config` | 43 satır | Rewrite + header + engel | L1-43 |

---

## §6 Sunucu Katmanı Desenleri

### §6.1 Sabit sıra deseni (ADR-020 §1.1-B.1)

Pipeline sırası `index.php` L130-133 yorum satırında dondurulmuştur:
`ResponseNormalization → Cors → RateLimit → Authentication → RequestValidation → Authorization`
(B-F-02/B-F-07 ile `OriginCheck(#1) → Cors(#2)` frozen sırası L135-137).
Sıra değişikliği ADR + MO kapısı gerektirir (`api.coremusic.net/AGENTS.md` §4 #4).

### §6.2 Fallback güvenlik başlığı deseni

`ResponseEmitter::applyFallbackSecurityHeaders()` (L132-150) yalnızca başlık
gönderilmemişse (`hasSentHeader` L152-162) CSP/nosniff/X-Frame-Options/
Referrer-Policy yazar. `SecurityHeadersMiddleware` (ADR-012) zaten yazdıysa
**dokunmaz** — nonce zinciri değişmez (L127-131 yorumu).

### §6.3 ETag/304 deseni

`sendHtml` L60: `$etag = '"' . hash('xxh64', $html) . '"'`; L62-66:
`If-None-Match` eşitse gövdesiz 304 + `exit`.

### §6.4 Apache/IIS parite deseni

`.htaccess` (Apache) ve `web.config` (IIS) aynı üç kuralı parite taşır:
statik uzantı passthrough, `config|include|pages` engeli, catch-all → `index.php`.
Karşılaştırma §6.5 tablosunda.

### §6.5 Rewrite parite tablosu

| Kural | Apache (`.htaccess`) | IIS (`web.config`) |
|---|---|---|
| Statik uzantı passthrough | L34 (`RewriteRule \.(css\|js\|...) - [L]`) | L16-19 (`Static files`, `action None`) |
| `config|include|pages` engeli | L35 (`[F,L]` → 403) | L20-23 (CustomResponse 403) |
| Hassas dosya uzantıları | L18-20 (`Require all denied`) | L24-27 (403) |
| Mevcut dosya/dizin passthrough | L30-32 (`-f/-d` → `[L]`) | L28-35 (`SPA Router`, IsFile/IsDirectory) |
| Catch-all | L36 (`index.php [QSA,L]`) | L36-39 (`Rewrite index.php`, appendQueryString) |
| Extra header | L9-16 (mod_headers) | L4-13 (customHeaders + Permissions-Policy) |
| Extra sertleştirme | L2-3 `Options -Indexes` · `ServerSignature Off` · L5-7 `php_flag` | — (IIS'de PHP ayarı web.config'de yok → §10.6) |

---

## §7 Kurallar & Yasaklar

| # | Kural | Kaynak | Kanıt |
|---|---|---|---|
| 1 | Middleware sırası ADR'siz değişmez | ADR-020 | `index.php` L130-133 |
| 2 | Hata mesajı istemciye asla dönmez | API hata sözleşmesi | `index.php` L11, L208-213 |
| 3 | Fallback header nonce zincirini bozmaz | ADR-012 | `ResponseEmitter.php` L127-131 |
| 4 | `config|include|pages` sunucu düzeyinde engelli | `.htaccess` L35 · `web.config` L20-23 |
| 5 | `X-Powered-By`/`Server` başlıkları kaldırılır | `.htaccess` L14-15 · `web.config` L6 |
| 6 | Boot yalnız `RuntimeBootstrap` üzerinden | shared AGENTS §4 #1 (paralel kural: DB'de DatabaseManager tekeli) | `RuntimeBootstrap.php` L7 |

---

## §8 Kapsam Dışı (Yapılmaz)

| # | Yapılmaz | Nereye ait |
|---|---|---|
| 1 | Sorgu yazmak / repository değiştirmek | `queries` modülü |
| 2 | Middleware iç doğrulamasını değiştirmek | Middleware modülü + Security Engineer |
| 3 | `web.config`/`.htaccess` onaysız düzenlemek | DevOps Engineer (`api.coremusic.net/AGENTS.md` §3.2) |
| 4 | nginx conf üretmek (üretimde yok) | PLANNED — §10.4 |

---

## §9 Güvenlik Etkileşimi

| # | Yüzey | Mekanizma | Kanıt |
|---|---|---|---|
| 1 | CSP (fallback) | `default-src 'self'` varyantı; `https:` genişliği kaldırıldı (C-F-02) | `ResponseEmitter.php` L13-14 |
| 2 | CSP (API JSON) | `default-src 'none'; frame-ancestors 'none'; base-uri 'none'` | `index.php` L76 |
| 3 | Frame engeli | `X-Frame-Options: DENY` (fallback + Apache + IIS) | L141 · `.htaccess` L10 · `web.config` L8 |
| 4 | MIME sniffing | `X-Content-Type-Options: nosniff` (4 ayrı yerde) | L9, L140 · `index.php` L75 · `.htaccess` L11 · `web.config` L7 |
| 5 | Referrer sızıntısı | `strict-origin-when-cross-origin` (3 yerde) | L142 · `.htaccess` L13 · `web.config` L10 |
| 6 | Hassas uzantı engeli | env/log/json/sql/git/ini... 403 | `.htaccess` L18-20 · `web.config` L24-27 |
| 7 | PHP dosyası doğrudan erişim | `index.php`/`debug.php` dışındaki `.php` engelli | `.htaccess` L22-24 |
| 8 | Log redaction | `Redactor::redact` — `[REDACTED]` (§17 Edge Case 3) | `StructuredLogger.php` L71-72 |
| 9 | Server banner sızıntısı | `unset Server` · `unset X-Powered-By` · `ServerSignature Off` | `.htaccess` L3, L14-15 · `web.config` L6 |
| 10 | Permissions-Policy | `camera=(), microphone=(), geolocation=()` | `web.config` L11 |

---

## §10 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Durum |
|---|---|---|
| 1 | `html_shell`/`HtmlShellRenderer` CSP nonce üretimi nasıl bağlanıyor? | ⚠️ VERIFICATION REQUIRED — renderer dosyası okunmadı |
| 2 | `FileHandler` log döndürme/dosya boyutu sınırı var mı? | ⚠️ VERIFICATION REQUIRED — dosya okunmadı |
| 3 | `MemoryAdapter` davranışı (APCu yokken) | ⚠️ VERIFICATION REQUIRED — dosya okunmadı |
| 4 | Üretimde nginx var mı (havuzdaki nginx dokümanları)? | ⚠️ VERIFICATION REQUIRED — repo'da nginx conf yok |
| 5 | `ATTR_PERSISTENT`/FPM production ayarı | ⚠️ VERIFICATION REQUIRED (queries modülüyle ortak boşluk) |
| 6 | IIS'de `php_flag` karşılığı (display_errors log)? | ⚠️ VERIFICATION REQUIRED — web.config'de PHP ayarı yok |
| 7 | `StructuredLogger` çıktı hedefi (`error_log`) production'da nereye akıyor? | ⚠️ VERIFICATION REQUIRED — `.htaccess` L7 yalnız Apache home log yolu veriyor |
| 8 | `X-XSS-Protection: "0"` bilinçli mi (modern tarayıcıda devre dışı)? | ⚠️ VERIFICATION REQUIRED — ADR kaydı okunmadı |

---

## §A Ek — Terimler

| Terim | Tanım |
|---|---|
| Front controller | Tüm isteklerin tek dosyaya düştüğü desen |
| Pipeline | Sırası sabit PSR-15 benzeri middleware zinciri |
| Fallback header | Pipeline dışı yanıt için son savunma başlıkları |
| Rewrite parite | Apache/IIS kurallarının birebir karşılıklılığı |
| ETag | Gövde hash'i (xxh64) — koşullu GET |
| Trace-Id | Log bağlama anahtarı (`X-Trace-Id`) |
| Halt | Preflight için gövdesiz kısa devre yanıtı (`index.php` L193-197) |

---

## §11 Boot Senaryoları

| # | Senaryo | Boot davranışı | Kanıt |
|---|---|---|---|
| 1 | API isteği (production) | `DEBUG_MODE=false` → `E_ERROR\|E_WARNING\|E_PARSE` + `display_errors=0` + `log_errors=1` | `RuntimeBootstrap.php` L14-19 · `index.php` L44 |
| 2 | API isteği (debug) | `DEBUG_MODE=true` → `E_ALL` + `display_errors=1` | L11-13 |
| 3 | `APP_TIMEZONE` tanımsız | `Europe/Istanbul` fallback | L9 |
| 4 | `/health` | Boot yine çalışır (L44), sonra kısa devre L101-108 | `index.php` L44, L101 |
| 5 | `/v1/*` alias | Boot sonrası normalizasyon L87-94 | `index.php` L87-94 |
| 6 | Apache'de `.php` direkt erişim | `index.php` dışındaki `.php` 403 → boot hiç çalışmaz | `.htaccess` L22-24 |

> Boot çağrısı her istekte tekrarlanır (persistent SAPI'de state korunur —
> `ATTR_PERSISTENT` davranışı ⚠️ VERIFICATION REQUIRED).

---

## §12 HTTP Durum Kodu Matrisi

| Kod | Üreten yer | Şekil | Kanıt |
|---|---|---|---|
| 200 | `/health` JSON | `{status:ok,...}` | `index.php` L101-108 |
| 200/4xx/5xx | Gateway sonucu | `$jsonResponse($result, $status >= 400 ? $status : 200)` | L199-200 |
| 204 | Preflight halt | gövdesiz | L193-197 |
| 302 | `redirect()` (varsayılan) | `Location` header | `ResponseEmitter.php` L83-92 |
| 304 | ETag eşleşmesi | gövdesiz | L62-66 |
| 403 | `buildErrorHtmlResult` / IIS block | minimal HTML / CustomResponse | `ErrorHandler.php` L58 · `web.config` L22, L26 |
| 404 | Bilinmeyen uç | `{error:{code:NOT_FOUND}}` | `index.php` L217-223 |
| 404/403/410/500 | SPA hata sayfası (kabuk) | `buildErrorPage` match | `ErrorHandler.php` L21-27 |
| 429 | Rate limit (mesaj eşlemesi) | `buildErrorHtmlResult` 429 | `ErrorHandler.php` L60 |
| 500 | Yakalanmayan `\Throwable` | `{error:{code:SERVER_INTERNAL_ERROR}}` | `index.php` L201-214 |

---

## §13 Başlık Envanteri (Üretici Bazında)

| Başlık | Üretici (kod) | Koşul | Kanıt |
|---|---|---|---|
| `Content-Type: application/json; charset=utf-8` | `JSON_HEADERS` | Tüm JSON yanıtlar | `ResponseEmitter.php` L8 |
| `Cache-Control: no-store, no-cache, must-revalidate, max-age=0` | `JSON_HEADERS` | Tüm JSON yanıtlar | L10 |
| `Content-Type: text/html; charset=UTF-8` | `sendHtml` | HTML | L68 |
| `ETag` (xxh64) | `sendHtml` | HTML | L60, L69 |
| `Vary: Accept-Encoding, Cookie` | `sendHtml` | HTML | L70 |
| `Content-Length` | `sendHtml` | HTML | L71 |
| `X-Trace-Id` | `emitTraceAndExtras` | `$traceId !== null` | L96-98 |
| `Location` | `redirect` | redirect | L86 |
| `Content-Security-Policy` (fallback) | `applyFallbackSecurityHeaders` | yalnız gönderilmemişse | L139, L145-148 |
| `X-Frame-Options: DENY` | fallback + Apache + IIS | her yanıt | L141 · `.htaccess` L10 · `web.config` L8 |
| `Referrer-Policy` | fallback + Apache + IIS | her yanıt | L142 · `.htaccess` L13 · `web.config` L10 |
| `Permissions-Policy` | IIS (tek taraf) | her IIS yanıtı | `web.config` L11 |
| `X-XSS-Protection: "0"` | Apache + IIS | her yanıt | `.htaccess` L12 · `web.config` L9 |

---

## §14 Vary Birleştirme Akışı

| # | Adım | Kanıt |
|---|---|---|
| 1 | Ek `Vary` istenen yanıtta `mergeVary`'ye yönlendirilir | `ResponseEmitter.php` L100-102 |
| 2 | `headers_list()` taranır, `Vary:` ön eki aranır | L111-114 |
| 3 | Mevcut token'lar tek tek karşılaştırılır (case-insensitive) | L116-119 |
| 4 | Eksik token eklenir; `Cookie` vary böyle korunur | L118-119, L108 yorumu |

---

## §15 Trace-Id Senaryosu

| # | Adım | Katman | Kanıt |
|---|---|---|---|
| 1 | İstemci log'u başlatır, `traceId` üretir | JS | `FetchWrapper.js` L50-51 |
| 2 | İstek `X-Trace-Id` başlığıyla gider | HTTP | JS L51 |
| 3 | Pipeline/log çağrısında kullanılır | PHP | `StructuredLogger` L66 (`traceId` alanı) |
| 4 | Yanıt `X-Trace-Id` ile döner | HTTP | `ResponseEmitter.php` L97 |
| 5 | Hata yakalanır, log traceId ile bağlanır | log | `index.php` L202-207 (logger error) |

> Sunucu tarafında istek başlığından traceId okuma kodu **okunmadı** —
> `⚠️ VERIFICATION REQUIRED` (pipeline içi logger beslemesi).

---

## §16 Edge Case'ler

| # | Senaryo | Davranış | Kanıt |
|---|---|---|---|
| 1 | `headers_sent()` true | Fallback başlıklar **yazılmaz** | `ResponseEmitter.php` L134-136 |
| 2 | Session aktifken emit | `session_write_close()` her emit sonunda | L51-53, L77-79, L88-90 |
| 3 | Gövde array değil (SPA/JSON) | `['error' => 'internal_error']`'e düşer | L33 |
| 4 | APCu kapalı | `MemoryAdapter`'a geçilir | `index.php` L143-145 |
| 5 | Host header'da port | `explode(':', $host, 2)` ayrıştırılır | L58-61 |
| 6 | 429/403/500 pipeline dışı | fallback başlık seti devreye girer | `ResponseEmitter.php` L127-131 yorumu |
| 7 | IIS'de `php_flag` | web.config'de karşılık YOK — IIS PHP handler ayarı başka yerde (⚠️) | `web.config` L1-43 |

---

## §17 ADR Etkileşimleri (Kanıtlı)

| ADR | İlgili karar | Kanıt |
|---|---|---|
| ADR-020 (§1.1-B.1) | Middleware sabit sırası | `index.php` L130-133 |
| ADR-012 | CSP/nonce zinciri; fallback yalnız boşlukları doldurur | `ResponseEmitter.php` L13-14, L127-131 |
| ADR-020 (key yaşam döngüsü) | `key_type enum('user','service','server')` | `coremusic_auth.sql` L71 |
| B-F-02/B-F-07 | OriginCheck(#1) → Cors(#2) frozen; CSRF yalnız session | `index.php` L135-137, L160-169 |
| P1-9 | Hybrid auth (session + RS256 + jti revocation) | `index.php` L152-159 |
| ADR-021/083 | SPA router (server-runtime yalnız bootstrap/emit yüzeyi) | Kardeş modül kapsamı |

---

## §18 Örnek İstek Yolculuğu (Adım Adım)

```text
GET https://api.coremusic.net/v1/library/songs  (Bearer)
[1] Apache/IIS: statik değil → catch-all → index.php
      .htaccess L36 · web.config L36-39
[2] config + RouteTable            index.php L38-42
[3] RuntimeBootstrap::boot(false)  L44  → E_WARNING/E_ERROR, display_errors=0
[4] LoggerFactory (seviye error)   L47
[5] HTTPS=true (proxy header)      L50-53
[6] /v1/... → /api/v1/...          L87-94
[7] pipelineRequest (Bearer header L125 yalnız x-csrf-token — auth header $_SERVER'dan)
[8] ResponseNormalization → OriginCheck → Cors → RateLimit
      → Authentication (Bearer + jti) → (CSRF atlanır: method=bearer)
      → RequestValidation → Authorization        L147-171
[9] Gateway::dispatch → controller action        L173-182
[10] headers yaz (CORS dahil) → 200 JSON        L184-200
[11] exit — istek bitti; error.log'a istek satırı (varsa)  L96-98
```

---

## §19 Sık Sorular

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| 1 | Neden iki sunucu dosyası (.htaccess + web.config)? | Apache ve IIS paritesi — aynı 5 kural | §6.5 |
| 2 | Emitter neden `exit` eder? | `never` dönüş — pipeline sonrası son nokta | `ResponseEmitter.php` L16, L43, L57, L83 |
| 3 | Fallback CSP neden 'self' varyantı? | Nonce yokken ADR-012 kararı; `https:` genişliği C-F-02 ile kaldırıldı | L13-14 |
| 4 | 5xx'te kim retry eder? | İstemci (`FetchWrapper` MAX_RETRIES=2); sunucu etmez | `FetchWrapper.js` L6, L30 |
| 5 | Log nereye yazılır? | `error_log` → SAPI hedefi; Apache'de `../../coremusic_php_errors.log` (home) | `StructuredLogger.php` L72 · `.htaccess` L7 |

---

## §20 Sürüm & Kapsam Notu

| # | Not |
|---|---|
| 1 | Bu dosya kademe-2 dokümandır; kademe-3 bounded-context genişletmesi ADR-097 K4 fazı (C) bekler (index.md `open_questions`). |
| 2 | `queries` modülüyle kesişim: yalnız ortak boşluklar (`ATTR_PERSISTENT`, `api_usage` yazıcısı) — URL havuzları kesişmez (web-arastirma §5). |
| 3 | Tüm satır kanıtları 2026-10-15 read ölçümleridir; kod değişirse yeniden ölçülmelidir. |

---

## §21 Karşılaştırmalı Özet Tablosu (Sunucu vs Uygulama)

| Soru | Sunucu katmanı (Apache/IIS) | Uygulama katmanı (PHP) |
|---|---|---|
| Kimsin? | `.htaccess` · `web.config` | `index.php` · `RuntimeBootstrap` · pipeline |
| Ne yapar? | Rewrite · header · dosya engeli | Boot · auth · dispatch · emit |
| CSP kimde? | Yok (başlık yazmaz) | Fallback (`ResponseEmitter` L139) + ADR-012 middleware |
| Hata kodu kimde? | 403 (engel) | 404/429/500 (uygulama) |
| Cache kimde? | Statik dosya önbelleği (sunucu) | APCu (`ApcuAdapter`) |
| Log kimde? | Access log (nginx/Apache — vault dışı) | `error_log` JSON (`StructuredLogger` L72) |
| Değişme hızı | Yavaş (onay: DevOps) | Kod ile (Backend Architect) |
| Kanıt | `.htaccess` L1-37 · `web.config` L1-43 | `index.php` L1-223 |

### §21.1 Kesişim noktaları (nerede birleşir)

| # | Kesişim | Kanıt |
|---|---|---|
| 1 | `index.php` adı | `.htaccess` L36 · `web.config` L38 |
| 2 | Statik uzantı listesi | `.htaccess` L34 ↔ `web.config` L17 |
| 3 | Engelli uzantı listesi | `.htaccess` L18 ↔ `web.config` L25 |
| 4 | `X-Forwarded-Proto` (proxy → PHP) | `index.php` L52 |
| 5 | PHP hata yolu (Apache `php_flag`) → PHP `log_errors` | `.htaccess` L5-7 ↔ `RuntimeBootstrap.php` L19 |

> Bu tablo modülün iki yakasını tek çerçevede toplar: sunucu kuralları ile
> PHP runtime'ı ancak bu 5 noktada temas eder; gerisi bağımsız hızda değişir.

---

## §22 Referans Kataloğu (Bu Dosyada Kullanılan Kaynaklar)

| # | Dosya | Satır aralığı | Kullanıldığı bölüm |
|---|---|---|---|
| 1 | `shared/src/Bootstrap/RuntimeBootstrap.php` | L7-20 | §1, §5, §11 |
| 2 | `shared/src/PageRouter/ResponseEmitter.php` | L7-163 | §1, §3, §6.2-6.3, §9, §13-14 |
| 3 | `shared/src/PageRouter/ErrorHandler.php` | L7-76 | §4, §5, §12 |
| 4 | `shared/src/PageRouter/StructuredLogger.php` | L9-80 | §5, §15 |
| 5 | `shared/src/Log/LoggerFactory.php` | L11-33 | §5 |
| 6 | `shared/src/Cache/ApcuAdapter.php` | L5-56 | §3.2, §5 |
| 7 | `api.coremusic.net/index.php` | L11-223 | §3-§4, §11-12, §17-18 |
| 8 | `home.coremusic.net/.htaccess` | L1-37 | §3, §6.5, §9 |
| 9 | `home.coremusic.net/web.config` | L1-43 | §3, §6.5, §9 |
| 10 | `assets.coremusic.net/js/router/FetchWrapper.js` | L6, L46-53 | §15, §18 |
| 11 | `.ai/.sql/mysql/coremusic_auth.sql` | L56-98 | §17 (ADR-020 key döngüsü) |

> Toplam: 11 kaynak dosya, 2026-10-15 read ölçümleri. Yeni iddia ancak bu
> dosyalardan birinin satırıyla eklenebilir (Zero-Hallucination §3).

---

## §23 Değişiklik Günlüğü (Modül)

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-15 | Kademe-2 dosya seti üretildi (5 içerik + index güncellemesi) | Bu revizyon |
| 2026-10-15 | Havuz doğrulaması: 6 URL HTTP 200, kesişim 0 | `server-runtime-web-arastirma.md` §3, §5 |
| 2026-10-15 | Ölçüm: 9 bileşen / 735 satır toplamı | `server-runtime-bilesenler.md` §7 |

> Geçmiş revizyon kayıtları bu modülde tutulmaz; modül doğduğu tarihte
> kademe-2 ile başlar. Commit bu dosya setinin **orkestratör** yetkisindedir
> (subagent commit atmaz — AGENTS §16.5).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
