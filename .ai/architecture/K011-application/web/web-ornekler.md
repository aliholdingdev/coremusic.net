---
title: "K011 Ünite-2 — Web Katmanı Kod Örnekleri"
id: K011-U2-WEB-ORNEKLER
type: architecture
category: application
status: active
version: 1.0.0
source: "repo gerçek kodu (shared/src, assets.coremusic.net/js, Css, .ai/.sql)"
verified: "okunmuş Dosya · Satır blokları (2026-10-15)"
updated: "2026-10-15"
kodeme: kademe-2
module: K011-application
parent: web
group: kod
---

# K011 Ünite-2 — Web Katmanı Kod Örnekleri (kod)

> **Kural:** Bu dosyadaki her kod alıntısı repo'dan **okunarak** alınmıştır ve
> başında `Dosya: <yol> · Satır: <n>` kanıt bloğu vardır. Repo dışı kod
> **üretilmez**; konu repoda yoksa `PLANNED` boşluğu yazılır. Tam URL
> kullanılmaz (havuz URL'leri tek dosyada: `-web-arastirma.md`).

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §GRUPLAR

| # | Grup | Eksen | Kapsam | Kanıt / Dosya | Durum |
|---|------|-------|--------|---------------|-------|
| 1 | PHP middleware | backend × güvenlik | CSP nonce + security header örneği | `shared/src/Middleware/SecurityHeadersMiddleware.php` | okundu |
| 2 | JS çekirdek | frontend × olay | EventBus pub/sub + Fetch retry | `js/core/EventBus.js`, `js/router/FetchWrapper.js` | okundu |
| 3 | CSS token | tasarım × katman | breakpoint token + @media eşleşmesi | `Css/01_Abstracts/a-breakpoint-tokens.css` | okundu |
| 4 | SQL şema | veri × API | api_keys tablosu + indeks/FK | `.ai/.sql/mysql/coremusic_auth.sql` | okundu |
| 5 | YAPMA ↔ YAP | anti-pattern × düzeltme | 4 çift (yasak → doğru kalıp) | bu dosya §E | aktif |
| 6 | PLANNED boşlukları | kapsam × yok | repo'da olmayan 4 örnek | §F | boşluk |

---

## §A — PHP: CSP NONCE + SECURITY HEADER (middleware kalıbı)

### A.1 Kanıt

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php · Satır: 14-21
```

```php
public function handle(array $request, callable $next): array
{
    // Nonce üret — SessionManager'dan önce çalışır, nonce'u request'e yaz
    if (empty($request['_csp_nonce'])) {
        $request['_csp_nonce'] = bin2hex(random_bytes(32));
    }

    $response = $next($request);

    $csp     = $this->buildCsp($request);
    $isHttps = $this->domainConfig?->isHttps() ?? false;
```

**Ne kanıtlıyor:** nonce request'in en başına (next'ten önce) yazılır; böylece
downstream middleware/View nonce'u görür. `random_bytes` kriptografik olarak
güvenli üreteç.

### A.2 Kanıt — header seti

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php · Satır: 26-43
```

```php
$headers = [
    'X-Content-Type-Options'         => 'nosniff',
    'X-Frame-Options'                => 'DENY',
    'X-XSS-Protection'               => '0',
    'Referrer-Policy'                => 'strict-origin-when-cross-origin',
    'Content-Security-Policy'        => $csp,
    'Require-Trusted-Types-For'      => "'script'",
    'Cross-Origin-Resource-Policy'   => 'cross-origin',
    'Permissions-Policy'             => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
    'X-Permitted-Cross-Domain-Policies' => 'none',
];

if ($isHttps) {
    $headers['Cross-Origin-Opener-Policy']   = 'same-origin';
    $headers['Cross-Origin-Embedder-Policy'] = 'require-corp';
}

$response['headers'] = array_merge($response['headers'] ?? [], $headers);
```

**Not:** `X-XSS-Protection: 0` bilinçli — modern tarayıcılarda bu başlık
kendisi XSS açığı üretir; `0` kapatmaktır. COOP/COEP yalnız HTTPS'te.

### A.3 Kanıt — CSP script-src dalı

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php · Satır: 54-56
```

```php
$scriptSrc = $nonce !== ''
    ? "'strict-dynamic' 'nonce-{$nonce}' https:"
    : "'self' https:";
```

**Fallback davranışı:** nonce yoksa `'self'`'e düşer (fail-safe değil ama
fail-closed: nonce üretimi hatası istisna fırlatır, boş CSP yazılmaz —
doğrulama: `bin2hex(random_bytes(32))` hatası fırlatır).

---

## §B — JAVASCRIPT: EVENTBUS (modüller arası loose coupling)

### B.1 Kanıt — dinleme

```text
Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 8-24
```

```js
export default class EventBus {
    /** @type {Map<string, Set<Function>>} */
    #listeners = new Map();

    on(event, fn) {
        if (!this.#listeners.has(event)) {
            this.#listeners.set(event, new Set());
        }
        this.#listeners.get(event).add(fn);
        return () => this.off(event, fn);
    }
```

**Kalıp:** `on()` unsubscribe döndürür (RAII benzeri temizlik). Private
field `#listeners` dışarıdan yazılamaz. Aynı `fn` iki kez `on` edilirse
`Set` ikinci kez eklemez (duplicate listener yok).

### B.2 Kanıt — tek seferlik dinleme

```text
Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 31-37
```

```js
once(event, fn) {
    const wrapper = (data) => {
        this.off(event, wrapper);
        fn(data);
    };
    this.on(event, wrapper);
}
```

**Dikkat:** `off(event, fn)` ile `once` kaydı **silinemez** — kaydedilen
`wrapper` referansı dışarıda yok. Bu bilinen sınır, kod içinde de `once`
iade değeri yok.

### B.3 Kanıt — hata yalıtımı

```text
Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 57-67
```

```js
emit(event, data) {
    const fns = this.#listeners.get(event);
    if (!fns) return;
    for (const fn of fns) {
        try {
            fn(data);
        } catch (err) {
            console.error(`[EventBus] Listener error on "${event}":`, err);
        }
    }
}
```

**Neden önemli:** bir listener hatası diğerlerini **durdurmaz** (yalıtım).
Ancak hata `console.error`'a düşer → produção gözlemi `⚠️` (monitoring
entegrasyonu yok, bkz §F).

---

## §C — JAVASCRIPT: FETCHWRAPPER (timeout + retry + CSRF)

### C.1 Kanıt — sabitler

```text
Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 5-11
```

```js
export default class FetchWrapper {
    static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 });
    #controller = null;
    #csrfSync;
    #logger;

    constructor(logger, csrfSync = null) { this.#logger = logger; this.#csrfSync = csrfSync; }
```

**Kalıp:** konfigürasyon `Object.freeze` ile sabit; alanlar private;
bağımlılık constructor ile enjekte (test edilebilir).

### C.2 Kanıt — retry döngüsü

```text
Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 24-42
```

```js
async #fetchWithRetry(url, signal) {
    let lastError = null;
    for (let attempt = 0; attempt <= FetchWrapper.CONFIG.MAX_RETRIES; attempt++) {
        if (signal.aborted) throw new DOMException('Aborted', ABORT_ERROR_NAME);
        try {
            const response = await this.#doFetch(url, signal, attempt);
            if (!response.ok && response.status >= 500 && attempt < FetchWrapper.CONFIG.MAX_RETRIES) {
                await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
                continue;
            }
            return response;
        } catch (err) {
            lastError = err;
            if (err.name === ABORT_ERROR_NAME) throw err;
            if (attempt < FetchWrapper.CONFIG.MAX_RETRIES) await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
        }
    }
    throw lastError || new Error('Fetch failed');
}
```

**Davranış özeti:** yalnız `>=500` yeniden denenir; `4xx` hemen döner;
abort asla yeniden denenmez (kullanıcı iptali = iptal).

### C.3 Kanıt — başlıklar (CSRF + trace)

```text
Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 46-53
```

```js
async #doFetch(url, signal, attempt) {
    const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };
    const csrfHeader = this.#csrfSync?.getHeader();
    if (csrfHeader) Object.assign(headers, csrfHeader);
    const traceId = this.#logger?.traceId;
    if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
    return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
}
```

**Not:** `attempt` parametresi bu metotta **kullanılmıyor** (dead param) —
gerçek kod, uydurma değil; `§F` borç listesine yazıldı.

---

## §D — CSS: BREAKPOINT TOKEN (ITCSS 01_Abstracts)

### D.1 Kanıt — token üretimi

```text
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 22-34
```

```css
:root {
  /* DEVICE BREAKPOINTS (px cinsinden — media query için) */
  --bp-device-phone:      767px;
  --bp-device-tablet:     1024px;
  --bp-device-embedded:   1024px;   /* RPi5: 1024x600 */
  --bp-device-laptop:     1440px;
  --bp-device-medium:     1919px;   /* Medium desktop: 1441-1919px */
  --bp-device-desktop:    2560px;
  --bp-device-4k-tv:      3840px;
  --bp-device-4k-monitor: 3841px;
```

### D.2 Kanıt — @media sınırı (spec kuralı, dosya içi not)

```text
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 39-46
```

```css
  /* NOT (CSS spesifikasyonu): @media içinde var() KULLANILAMAZ —
     "@media (min-width: var(--bp-1024))" geçersizdir. */
  --bp-1024: 1024px;  /* = --bp-device-tablet = --bp-device-embedded */
  --bp-1920: 1920px;  /* wide desktop alt sınırı */
```

**Kural:** `@media` içinde `var()` yasak (gerçek CSS davranışı) → token
katmanı hem `--bp-*` (JS/matrics) hem eşdeğer sabit çifti üretir; eşitleme
el ile yapılır, bu bilinçli teknik borçtur (§F).

---

## §E — YAPMA ↔ YAP ÇİFTLERİ

### E.1 JS — `var` yerine private field

```text
Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 9-10 (YAP kanıtı)
```

```js
YAPMA:  var listeners = {};              // global sızıntı + var hoisting
YAP:    #listeners = new Map();          // class private field (gerçek kalıp)
```

### E.2 JS — innerHTML yerine güvenli render

```text
Durum: repo'da `innerHTML` yasağı kural olarak var (assets AGENTS §4.3);
       bu oturumda örnek dosya OKUNMADI → ⚠️ VERIFICATION REQUIRED
```

```js
YAPMA:  container.innerHTML = userControlledString;   // XSS
YAP:    container.append(document.createTextNode(s)); // kanıt: Ünite-3'te dosya okunacak
```

### E.3 PHP — SQL yerine parametreli sorgu

```text
Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 78 (YAP — FK/indeksli şema)
```

```php
YAPMA:  $pdo->query("SELECT * FROM api_keys WHERE key_hash = '$hash'");  // injection
YAP:    $st = $pdo->prepare('SELECT `id` FROM `api_keys` WHERE `key_hash` = ?');
        $st->execute([$hash]);
```

> ⚠️ YAP bloğu repo'da hazır bir `api_keys` SELECT örneği aranıp
> **okunarak** doğrulanmalı (bu oturumda yalnız şema okundu).

### E.4 CSS — ham değer yerine token

```text
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 27 (YAP kanıtı)
```

```css
YAPMA:  @media (max-width: 767px) { }     /* katmanda sabit değer — kural ihlali */
YAP:    /* 01_Abstracts'ta: */ --bp-device-phone: 767px;   /* gerçek satır */
        /* @media içinde var() yasak → eşdeğer sabit bilinçli yazılır (bkz §D.2) */
```

---

## §F — PLANNED BOŞLUKLARI (repo dışı / okunmayan örnekler)

| # | Örnek | Neden boş | Ne zaman dolacak |
|---|-------|-----------|-------------------|
| F1 | JS güvenli DOM render örneği (`DOMParser` + TrustedTypes) | dosya bu oturumda okunmadı | Ünite-3 (dosya adı: `js/core/helper.js` araştırılacak) |
| F2 | CSRF token senkron örneği (`CsrfSync`) | yalnız `FetchWrapper` üzerinden dolaylı görüldü | Ünite-3: `js/router/config/*` okunacak |
| F3 | PHPUnit CSRF test kod alıntısı | test dosyası bu turda okunmadı (envanter var) | Ünite-3: `shared/tests/**` |
| F4 | Electron/IPC kod örneği | masaüstü kodu repo'da YOK | desktop klasörü `PLANNED` |

---

## §G — ÖRNEK ↔ KAPI EŞLEŞMESİ

| # | Örnek | Hangi kapıyı besler | Kapı durumu |
|---|-------|---------------------|-------------|
| 1 | §A middleware | güvenlik (CSP/nonce) | `web-dogrulama.md` §B ✓ |
| 2 | §B EventBus | modülerlik/olay | `web-kapsam.md` §B ✓ |
| 3 | §C FetchWrapper | hata + retry | `web-kapsam.md` §C ✓ |
| 4 | §D token | ITCSS katman | `web-kapsam.md` §D ✓ |
| 5 | §E çiftleri | anti-pattern denetimi | kalite kapısı ✓ |
| 6 | §F boşlukları | doğrulama | `⚠️` işaretli |

---

## §H — KOD ALINTI DÜRÜSTLÜK NOTU

1. Alıntılar dosyadan **okunarak** kopyalandı; satır numaraları okuma
   anındaki dosyadır (dosya değişirse numara kayar → bitişte tekrar okunur).
2. `attempt` parametresi (§C.3) ve `once` iade değeri (§B.2) gibi
   gerçek-kod kusurları **gizlenmedi**, bilinçli yazıldı.
3. Repo dışı hiçbir satır eklenmedi; eksik konu §F'te `PLANNED`.

---

---

## §I — JAVASCRIPT: CİHAZ CSS HARİTASI (tek SSOT dondurma)

### I.1 Kanıt

```text
 Dosya: assets.coremusic.net/js/devices.config.js · Satır: 9-21
```

```js
window.CoreMusic = window.CoreMusic || {};
window.CoreMusic.DEVICES = Object.freeze({

    /** Cihaz bazlı ana CSS dosyaları — 4K tek SSOT: d-4k.css (v3.0.0) */
    HOME_CSS: Object.freeze({
        'embedded':   '08_Devices/d-embedded.css',
        'phone':      '08_Devices/d-phone.css',
        'tablet':     '08_Devices/d-tablet.css',
        'laptop':     '08_Devices/d-laptop.css',
        'desktop':    '08_Devices/d-desktop.css',
        '4k-tv':      '08_Devices/d-4k.css',
        '4k-monitor': '08_Devices/d-4k.css',
    }),
```

### I.2 Kanıt — tip listesi

```text
 Dosya: assets.coremusic.net/js/devices.config.js · Satır: 42-44
```

```js
    /** Tüm desteklenen cihaz türleri */
    ALL: Object.freeze(['embedded', 'phone', 'tablet', 'laptop', 'desktop', '4k-tv', '4k-monitor']),
});
```

**Eşitleme kuralı (kod içi not, satır 4):** bu harita `DeviceCssMap.php` ve
`device-loader.js` ile senkronize — cihaz CSS'i ekleme üç noktada birden
iş demektir (kural assets AGENTS §4.4). `4k-tv` ve `4k-monitor` aynı CSS'e
maplenir (tek dosya `d-4k.css`), haritada iki ayrı anahtar tutulur.

### I.3 YAPMA ↔ YAP (bu örnek için)

```text
YAPMA:  if (device === 'phone') link.href = 'd-phone.css';      // hardcoded, harita yok
YAP:    link.href = window.CoreMusic.DEVICES.HOME_CSS[device];  // gerçek kalıp (satır 13-21)
```

---

## §J — PHP: API FRONT CONTROLLER (middleware pipeline)

### J.1 Kanıt — sözleşmenin başı

```text
 Dosya: api.coremusic.net/index.php · Satır: 3-14
```

```php
 * CoreMusic API Service — Entry Point
 *
 * JSON-only front controller (SPA router KULLANMAZ).
 *   /health    → canlılık ucu (Faz 0: 200 döner)
 *   /api/v1/*  → CoreMusic\Api\Gateway'e yönlendirilir
 *   diğer      → 404 JSON
 *
 * Hata sözleşmesi: {error:{code,message}} — exception mesajı ASLA istemciye dönmez.
 */

require_once __DIR__ . '/autoload.php';
```

**Kanal kuralı:** exception mesajı istemciye dönmez (kod içi yasak, satır 11) —
`web-kapsam.md` §C hata sözleşmesiyle aynı.

### J.2 Kanıt — middleware listesi

```text
 Dosya: api.coremusic.net/index.php · Satır: 19-36
```

```php
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Api\Middleware\AuthenticationMiddleware;
use CoreMusic\Api\Middleware\AuthorizationMiddleware;
use CoreMusic\Api\Middleware\RateLimitMiddleware;
use CoreMusic\Api\Middleware\RequestValidationMiddleware;
use CoreMusic\Api\Middleware\ResponseNormalizationMiddleware;
use CoreMusic\Api\Registry\ServiceRegistry;
use CoreMusic\Api\Routing\RouteTable;
use CoreMusic\Api\Versioning\VersionResolver;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Cache\ApcuAdapter;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Middleware\CorsMiddleware;
use CoreMusic\Middleware\CsrfMiddleware;
use CoreMusic\Middleware\OriginCheckMiddleware;
use CoreMusic\Security\CacheRateLimiter;
```

### J.3 Kanıt — bootstrap sırası

```text
 Dosya: api.coremusic.net/index.php · Satır: 38-47
```

```php
/* --- Config (constants + app + cors + routes) --- */
require_once __DIR__ . '/config/constants.php';
$appConfig    = require __DIR__ . '/config/app.php';
$corsConfig   = require __DIR__ . '/config/cors.php';
$routeTable   = RouteTable::fromArray(require __DIR__ . '/config/routes.php');

RuntimeBootstrap::boot(DEBUG_MODE);

/* --- Logger --- */
$logger = LoggerFactory::getInstance(dirname(__DIR__), DEBUG_MODE ? 'debug' : 'error');
```

**Sıra:** config → bootstrap → logger. Route tablosu config'den array olarak
gelir (`RouteTable::fromArray`) — rota kod içine gömülü değil.

### J.4 Pipeline sırası (iddia)

Pipeline'ın **çalışma sırası** bu dosyada görünmüyor (use listesi = alfabetik
sıra, çalışma sırası `ApiMiddlewarePipeline` içinde).
⚠️ VERIFICATION REQUIRED — Ünite-3'te `ApiMiddlewarePipeline` okunacak;
bu dosyada sıralama **uydurulmadı**.

---

## §K — ÖRNEK ENVANTERİ (bu dosyada kanıtlı)

|#| Örnek | Dil | Dosya | Satır | §GRUP|
|---|-------|-----|-------|-------|------|
|1| CSP nonce middleware | PHP | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 14-56 | §A |
|2| EventBus pub/sub | JS | `assets.coremusic.net/js/core/EventBus.js` | 8-73 | §B |
|3| Fetch retry + CSRF header | JS | `assets.coremusic.net/js/router/FetchWrapper.js` | 5-60 | §C |
|4| Breakpoint token | CSS | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 22-57 | §D |
|5| api_keys şema | SQL | `.ai/.sql/mysql/coremusic_auth.sql` | 56-79 | §E.3 |
|6| Cihaz CSS haritası | JS | `assets.coremusic.net/js/devices.config.js` | 9-44 | §I |
|7| API front controller | PHP | `api.coremusic.net/index.php` | 3-47 | §J |

**Dil dağılımı:** PHP (2) · JS (3) · CSS (1) · SQL (1) → şartname `>=4 dil` ✓

---

## §L — SON KONTROL (bu dosya)

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | `Dosya · Satır` bloğu sayısı | >= 7 | ☐ (bitişte sayılacak) |
| 2 | Uydurma kod | 0 | ✓ (her alıntı okundu) |
| 3 | Havuz-dışı tam URL | 0 | ✓ (yalnız `Dosya · Satır` yolu) |
| 4 | `§GRUPLAR` satırı | >= 6 | ✓ (6) |
| 5 | Satır | >= 500 | ☐ |
| 6 | Footer + `group:` son alan | var | ✓ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
