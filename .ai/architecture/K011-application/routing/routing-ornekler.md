---
title: "Routing — Örnekler (4 Dil · Gerçek Repo Kodu)"
type: dogrulama
kodeme: kademe-2
module: K011-application
parent: routing
status: active
updated: 2026-10-15
group: kod
---

# Routing — Örnekler

> **Amaç:** Routing modülünün 4 dilde (PHP · Vanilla JS · SQL · CSS) gerçek repo
> örneğini `Dosya:/Satır:` künyesiyle sunmak ve her örnek için YAPMA↔YAP karşıtlığı
> kurmak. Tüm kod blokları bu oturumda satır satır okunmuş dosyalardan **birebir**
> alınmıştır; uydurma satır yoktur.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| 1 | §1 Dil dağılımı ve envanter | mimari | kapsam | hangi dil, hangi dosya |
| 2 | §2 PHP — route kaydı (routes.php) | kod | bilesenler | server-side routing |
| 3 | §3 PHP — route resolve deseni (RouteRegistry) | kod | bilesenler | SPA server resolve |
| 4 | §4 Vanilla JS — URL parse (UrlUtils) | kod | bilesenler | client URL |
| 5 | §5 Vanilla JS — router guard (Router.js) | kod | bilesenler | client router |
| 6 | §6 SQL — routing telemetri (api_calls) | kod | bilesenler | rota logu |
| 7 | §7 CSS — breakpoint tokenı | kod | bilesenler | responsive route UI |
| 8 | §8 Standart referans (History/pushState) | kanit | dogrulama | pushState kaynağı |
| 9 | §9 YAPMA ↔ YAP (dil bazlı) | mimari | kapsam | anti-pattern |
| 10 | §10 Örnek↔iddia köprüsü | kanit | dogrulama | neyi kanıtlıyor |
| 11 | §11 Erişilebilirlik kanıt notu | kanit | dogrulama | fetch kaydı |

---

## §1 Dil dağılımı ve envanter

| Dil | Örnek rolü | Dosya (repo) | Satır | Okundu mu |
|-----|-----------|--------------|-------|-----------|
| PHP 8.x | route kaydı + `implemented` sözleşmesi | `api.coremusic.net/config/routes.php` | 1-120 | ✅ tamamı |
| PHP 8.x | SPA route resolve deseni | `shared/src/PageRouter/RouteRegistry.php` | 1-83 | ✅ tamamı |
| PHP 8.x | middleware ters sarma | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 1-46 | ✅ tamamı |
| PHP 8.x | yanıt normalizasyonu | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 1-97 | ✅ tamamı |
| Vanilla JS | URL parse + origin testi | `assets.coremusic.net/js/router/UrlUtils.js` | 1-39 | ✅ tamamı |
| Vanilla JS | pushState guard + init | `assets.coremusic.net/js/router/Router.js` | 1-81 | ✅ tamamı |
| SQL (MySQL) | `api_calls` rota logu | `.ai/.sql/mysql/coremusic_api.sql` | 18-53 | ✅ |
| SQL (MySQL) | `api_keys` | `.ai/.sql/mysql/coremusic_api.sql` | 55-76 | ✅ |
| SQL (MySQL) | `rate_limits` (endpoint pattern) | `.ai/.sql/mysql/coremusic_api.sql` | 78-90 | ✅ |
| CSS | cihaz breakpoint tokenları | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 1-57 | ✅ tamamı |

**Dil sayısı:** 4 (PHP · Vanilla JS · SQL · CSS) — kapı ≥4 ✓.

---

## §2 PHP — route kaydı (routes.php)

### 2.1 Dosya başlığı sözleşmesi (birebir)

```php
<?php declare(strict_types=1);

/**
 * CoreMusic API — Route Kayıtları (ADR-084 Contract First · ADR-020 §1.1-B.1)
 *
 * Method-aware tablo: `shared/src/Api/Routing/RouteTable`.
 *
 * Alanlar:
 *   service / handler : çözümleme anahtarı (Faz 1b AuthController — container resolver)
 *   action            : AuthController::handle() dispatch anahtarı (Faz 1b)
 *   public            : auth'sız erişim (Authentication/Authorization bunu okur)
 *   implemented       : false → route TANIMLI ama controller yok → 405 + Allow
 *                       (404 DEĞİL); Faz 1b'de 6 POST ucu true yapıldı
 *   validation        : RequestValidationMiddleware kural seti (422 + error.fields)
 *   requiredRole / requiredPermission : AuthorizationMiddleware RBAC
 *   cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control
```

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 1-16
```

### 2.2 GET prefix kayıtları (birebir)

```php
return [
    'GET' => [
        // Faz 2 hardcodedi GET prefix kayıtları (davranış değişmez)
        '/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],
        '/api/v1/user'     => ['service' => 'user',     'handler' => 'userController'],
        '/api/v1/music'    => ['service' => 'music',    'handler' => 'musicController'],
        '/api/v1/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],
        '/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
        '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],

        // Public auth uçları — exact kayıt, prefix `/api/v1/auth` ile çakışmasın
        // Bu 4 uç POST-only'dir: GET implemented=false → 405 + Allow: POST, OPTIONS
        '/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/register'       => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/forgot-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
        '/api/v1/auth/reset-password' => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
```

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 24-39
```

### 2.3 POST ucu + validation (birebir)

```php
    'POST' => [
        // Faz 1b: 6 uç implemented=true + AuthController action'ı + doğrulama kuralları
        '/api/v1/auth/login' => [
            'service'     => 'auth',
            'handler'     => 'authController',
            'action'      => 'login',
            'public'      => true,
            'implemented' => true,
            'validation'  => [
                // identity: e-posta VEYA kullanıcı adı (LoginRequest::fromArray)
                'email'    => ['required' => true, 'type' => 'string'],
                'password' => ['required' => true, 'type' => 'string', 'min' => 8],
                'gender'   => ['in' => ['male', 'female', 'neutral']],
            ],
        ],
```

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 51-65
```

**Yorum:** `implemented` bir rota **meta alanıdır**; false iken bile rota tanınır
(404 değil 405 döner). Bu, "yol var ama method/controller yok" bilgisini kasıtlı
söyler — client 404 ile kaybolmaz.

---

## §3 PHP — SPA route resolve deseni (RouteRegistry)

### 3.1 register + loadFromFile (birebir)

```php
public function register(SpaRoute $route): void
{
    $this->routes[trim($route->path, '/')] = $route;
}

public function loadFromFile(string $filePath): void
{
    if (!is_file($filePath)) {
        throw new \RuntimeException("Route config dosyası bulunamadı: {$filePath}");
    }

    $routes = include $filePath;

    if (!is_array($routes)) {
        throw new \RuntimeException('Route config bir array döndürmelidir.');
    }

    foreach ($routes as $key => $route) {
        if (!$route instanceof SpaRoute) {
            continue;
        }
        $this->routes[trim((string)$key, '/')] = $route;
    }
}
```

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 10-33
```

### 3.2 resolve + matchesPattern (birebir)

```php
public function resolve(string $uri): ?SpaRoute
{
    $normalized = trim($uri, '/');

    if ($normalized === '') {
        return $this->routes['home'] ?? null;
    }

    if (isset($this->routes[$normalized])) {
        return $this->routes[$normalized];
    }

    foreach ($this->routes as $pattern => $route) {
        if ($this->matchesPattern($pattern, $normalized)) {
            return $route;
        }
    }

    return null;
}

private function matchesPattern(string $pattern, string $uri): bool
{
    if (!str_contains($pattern, '{')) {
        return false;
    }

    $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '[^/]+', $pattern);
    $regex = '#^' . $regex . '$#';

    return (bool)preg_match($regex, $uri);
}
```

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 35-82
```

**Yorum:** `{param}` segmenti `[^/]+` ile değiştirilir — slash **yutar** (segment
sınırlı eşleşme). `#^...$#` anchor'ları tam eşleşmeyi zorlar; boş URI `home`'a
düşer. `{` içermeyen pattern desen döngüsüne girmez (performans + yanlış eşleşme
önleme, satır 74-76).

---

## §4 Vanilla JS — URL parse (UrlUtils)

### 4.1 isCrossOrigin + normalizeUrl (birebir)

```js
export function isCrossOrigin(url) {
    try { const target = new URL(url, window.location.href); return target.origin !== window.location.origin; }
    catch { return false; }
}

export function normalizeUrl(url, base) {
    try {
        const u = new URL(url, base || window.location.href);
        if (u.origin !== window.location.origin) return u.href;
        // C-F-14: query KORUNUR — SPA navigasyonunda query-driven özellikler
        // (arama/filtre/sayfalama) çalışmıyordu. Rota ANAHTARI pathname'dir;
        // guard eşleşmesi orchestrator'da path-only yapılır.
        return u.pathname + u.search;
    } catch {
        // Parse başarısız: hash düşer, query korunur (yukarıdaki politika ile aynı).
        const hIdx = url.indexOf('#');
        return hIdx !== -1 ? url.substring(0, hIdx) : url;
    }
}
```

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 3-21
```

### 4.2 normalizeCacheKey (birebir)

```js
export function normalizeCacheKey(url, base) {
    try {
        const u = new URL(url, base || window.location.origin);
        u.hash = '';
        const params = [...u.searchParams.entries()].sort(([a], [b]) => a.localeCompare(b));
        u.search = params.map(([k, v]) => `${k}=${v}`).join('&');
        return u.pathname + u.search;
    } catch { return url; }
}
```

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 31-39
```

**Yorum:** C-F-14 kararı (satır 12-14 yorumu) "rota anahtarı pathname'dir, query
değildir" der — böylece `?page=2` ile `?page=3` **aynı rota** sayılır ama farklı
cache anahtarına sahiptir (sıralı rebuild, satır 35-36). Parse hatası sessiz
fallback üretir (satır 16-20): hash düşer, query kalır.

---

## §5 Vanilla JS — router guard (Router.js)

### 5.1 init guard zinciri (birebir)

```js
init() {
    if (window.CoreMusic?.RouterConfig?.enabled === false) return;
    if (typeof history.pushState !== 'function') return;
    history.scrollRestoration = 'manual';
    this.#eventManager.bind({
        onNavigate: (url, pushState) => this.#nav.navigate(url, pushState),
        onPrefetch: (url) => this.#nav.prefetch(url),
        onOffline: () => this.#domPatcher.setErrorState('offline'),
        onOnline: () => this.#domPatcher.setErrorState(null),
        onError: (e) => this.#logger.error('Router', 'unhandled_error', { message: e.message }),
        onUnhandledRejection: (e) => this.#logger.error('Router', 'unhandled_error', { message: e.reason?.message || String(e.reason) }),
    });
    if (window.location.search?.includes('auth_key=')) return;
    if (window.location.search) history.replaceState(null, '', window.location.pathname);
    this.#nav.initUrl(window.location.pathname);
    const initialState = this.#config.initialState;
    if (initialState) this.#renderInitialContent(initialState);
}
```

```text
Dosya: assets.coremusic.net/js/router/Router.js · Satır: 49-66
```

**Yorum (guard zinciri sırası):**

| # | Satır | Guard | Ne engelliyor |
|---|-------|-------|---------------|
| G1 | 50 | `RouterConfig.enabled === false` | config ile tamamen kapatma |
| G2 | 51 | `typeof history.pushState !== 'function'` | eski tarayıcıda yarı-başlangıç (fallback: normal link) |
| G3 | 61 | `search?.includes('auth_key=')` | OAuth callback query'sinin silinmesi |
| G4 | 62 | `if (location.search) replaceState` | rota anahtarına query sızması (auth_key yoksa) |

G3, G4'ten **önce** gelir — auth_key varsa replaceState **yapılmaz** (return).
Sıra kritiktir; tersi olsaydı OAuth akışı bozulurdu.

### 5.2 destroy (birebir)

```js
async destroy() {
    this.#fetcher.abort?.(); this.#nav?.abortAll?.(); this.#lifecycle.unmount();
    this.#eventManager.unbind(); this.#memoryWatchdog?.destroy?.();
    this.#nav = null;
}
```

```text
Dosya: assets.coremusic.net/js/router/Router.js · Satır: 71-75
```

**Yorum:** in-flight istekler abort edilir, event dinleyicileri bırakılır — router
yeniden başlatılabilir (memory leak önleme).

---

## §6 SQL — routing telemetri (coremusic_api.sql)

### 6.1 `api_calls` — rota çağrısı logu (birebir, seçme)

```sql
CREATE TABLE `api_calls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_status` smallint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_ms` int unsigned DEFAULT NULL,
  `request_body` text COLLATE utf8mb4_unicode_ci,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `called_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`called_at`),
  KEY `idx_ac_key` (`api_key_id`),
  KEY `idx_ac_user` (`user_id`),
  KEY `idx_ac_endpoint` (`endpoint`(255)),
  KEY `idx_ac_status` (`http_status`),
  KEY `idx_ac_method` (`method`),
  KEY `idx_ac_response` (`response_time_ms`),
  KEY `idx_ac_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

```text
Dosya: .ai/.sql/mysql/coremusic_api.sql · Satır: 18-39
```

### 6.2 `rate_limits` — endpoint pattern (birebir, seçme)

```sql
CREATE TABLE `rate_limits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned NOT NULL,
  `endpoint_pattern` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_requests` int unsigned NOT NULL DEFAULT '60',
  `window_sec` int unsigned NOT NULL DEFAULT '60',
  ...
  UNIQUE KEY `uq_rl_key_endpoint` (`api_key_id`,`endpoint_pattern`(255)),
```

```text
Dosya: .ai/.sql/mysql/coremusic_api.sql · Satır: 78-90
```

**Yorum (routing perspektifi):** `api_calls.endpoint` + `method` her rota çağrısını
kaydeder; `http_status` (404/405 dahil) rota sağlığını izler. `rate_limits.
endpoint_pattern` rota **deseni** bazlı limit tanımlar — routing katmanının
telemetri + throttling ayağı. `idx_ac_status` ve `idx_ac_method` bu analiz için
var. `endpoint`(255) prefix index — 500 char kolonun tamamı indexlenmez.

---

## §7 CSS — breakpoint tokenı (a-breakpoint-tokens.css)

### 7.1 Cihaz breakpoint tokenları (birebir, seçme)

```css
:root {
  /* ============================================================
     DEVICE BREAKPOINTS (px cinsinden — media query için)
     device-loader.js ile senkronize
     ============================================================ */
  --bp-device-phone:      767px;
  --bp-device-tablet:     1024px;
  --bp-device-embedded:   1024px;   /* RPi5: 1024x600 */
  --bp-device-laptop:     1440px;
  --bp-device-medium:     1919px;   /* Medium desktop: 1441-1919px */
  --bp-device-desktop:    2560px;
  --bp-device-4k-tv:      3840px;
  --bp-device-4k-monitor: 3841px;
```

```css
  --bp-1024: 1024px;  /* = --bp-device-tablet = --bp-device-embedded (tablet/embedded üst sınırı) */
  --bp-1920: 1920px;  /* wide desktop alt sınırı — a-semantic-token: --bp-2xl, a-design-tokens: --bp-tv ile aynı */

  /* DEVICE BREAKPOINTS (integer — JS hesaplama için) */
  --bp-embedded:   1024;
  --bp-laptop:     1440;
  --bp-medium:     1919;
  --bp-desktop:    2560;
  --bp-4k-tv:      3840;
  --bp-4k-monitor: 3841;
}
```

```text
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 22-57
```

**Yorum (routing perspektifi):** SPA route'ları cihaz katmanına göre farklı
düzen sunar; bu tokenlar cihaz sınırlarını tek kaynaktan verir. Dosya başlığı
(satır 12-19) `device-loader.js` ile birebir senkronu şart koşar. `@media` içinde
`var()` **kullanılamaz** (satır 39-40 yorumu) — CSS spesifikasyon kısıtı; bu
yüzde `--bp-1024` (px) + `--bp-embedded` (integer) ikili varlığı zorunludur.

---

## §8 Standart referans — History/pushState

Bu dosya Ünite-3 routing havuzundan **tek URL** alır:

| URL | Kullanım | Erişim |
|-----|----------|--------|
| `https://developer.mozilla.org/en-US/docs/Web/API/History/pushState` | §5.1 G2 guard'ın (`typeof history.pushState !== 'function'`) standart kaynağı — pushState API'sinin varlık/kontrol semantiği | exa fetch OK → 200-eşdeğer; status kodu `⚠️ VERIFICATION REQUIRED` (shell/curl yok) |

**Klasör içi dağılım (tek-dosya kuralı):** pushState URL'si bu dosyada; History
URL'si `routing-dogrulama.md`'de; URL/URLSearchParams/Location `routing-
web-arastirma.md`'de; `$_GET` `routing-bilesenler.md`'de. Toplam 6/6, kesişim 0.

**Reddedilen adaylar (etiket):**

| Etiket | Neden |
|--------|-------|
| `MDN Window.history.replaceState` | Ayrı havuz URL'si yok; §5.1 G4 kanıtı repo'da (Router.js:62) |
| `MDN Web/API/History` | `routing-dogrulama.md` kullanıyor (tek-dosya kuralı) |
| `MDN Window.history.scrollRestoration` | Havuz dışı → etiket |
| `whatwg html.spec.whatwg.org` | web klasörü havuzuna ait → klasör dışı |

---

## §9 YAPMA ↔ YAP (dil bazlı)

### 9.1 PHP

| YAPMA (anti-pattern) | YAP (doğru) | Kanıt |
|----------------------|-------------|-------|
| Tanımsız uca GET → 404 döndür | Rota tanımla, `implemented=false` bırak → 405 + Allow | routes.php:12-13, 36-39 |
| Route config'de array kontrolü yapma | `is_array` + RuntimeException (fail-fast) | RouteRegistry.php:23-25 |
| `{param}` için `.*` deseni | `[^/]+` (segment sınırlı, slash yutmaz) | RouteRegistry.php:78 |
| Prefix altı her yolu prefix'e fallback'le | Exact → isDeclared → segment-sınırlı prefix; tanımsız alt yol 404 | RouteTable.php:197-221 (find) |
| Middleware'i kayıt sırasıyla çalıştır | `array_reverse` ile ters sarma (onion) | ApiMiddlewarePipeline.php:37-42 |

### 9.2 Vanilla JS

| YAPMA | YAP | Kanıt |
|-------|-----|-------|
| Query'yi rota anahtarına kat | pathname rota, query korunur (C-F-14) | UrlUtils.js:12-15 |
| Cache key için ham query | `sort()` + rebuild (deterministik) | UrlUtils.js:35-36 |
| Hash'i cache key'e dahil et | `u.hash = ''` | UrlUtils.js:34 |
| pushState guard'sız başla | `typeof history.pushState !== 'function'` → return | Router.js:51 |
| auth_key callback'inde query temizle | `includes('auth_key=')` guard → return (önce) | Router.js:61-62 |
| Parse fail'de sessiz yanlış rota | Deterministik fallback (hash-drop) | UrlUtils.js:16-20 |

### 9.3 SQL

| YAPMA | YAP | Kanıt |
|-------|-----|-------|
| endpoint'i tam uzunlukta indexle | `endpoint`(255) prefix index | coremusic_api.sql:34 |
| Status/method için ayrı tablo | `api_calls` kolonları + `idx_ac_status`/`idx_ac_method` | coremusic_api.sql:24, 35-36 |
| Rate limit'i sabit global say | `endpoint_pattern` + unique (key, endpoint) | coremusic_api.sql:81, 88 |
| Log'da ham IP/UA tutup indeksleme | `ip_address` kolonu + `idx_ac_ip` | coremusic_api.sql:25, 38 |

### 9.4 CSS

| YAPMA | YAP | Kanıt |
|-------|-----|-------|
| `@media (min-width: var(--bp-1024))` | `@media` içinde `var()` YASAK; sabit + üstte yorum köprüsü | a-breakpoint-tokens.css:39-43 |
| Breakpoint'i 03_Layout'a göm | 01_Abstracts'ta token üret | a-breakpoint-tokens.css:22-34 |
| JS için px token'ı parse et | Integer ikili (`--bp-embedded: 1024`) | a-breakpoint-tokens.css:51-56 |
| device-loader.js'ten bağımsız değer | Birebir senkron şartı (dosya başlığı) | a-breakpoint-tokens.css:8-19 |

---

## §10 Örnek↔iddia köprüsü

| Örnek | § | Beslediği iddia (dogrulama) |
|-------|---|------------------------------|
| routes.php GET/POST kaydı | §2 | A4, A12, A13, A14 |
| RouteRegistry register/resolve | §3 | A15, A16, A17, A18 |
| UrlUtils isCrossOrigin/normalizeUrl | §4 | A22, A23 (+ W-R1, R4, R5) |
| UrlUtils normalizeCacheKey | §4.2 | A24 (+ W-R2, R9) |
| Router.js init guard | §5 | A19, A20, A21 (+ W-R3) |
| coremusic_api.sql api_calls/rate_limits | §6 | §6 routing telemetri envanteri (KPI: loglanan uç/method/status) |
| a-breakpoint-tokens.css | §7 | §7 responsive route UI token kaynaklı |

**Köprü sayımı:** 7 örnek grubu · 7/7 dogrulama dosyasındaki iddiayla eşleşiyor ·
yetim (iddiyi beslemeyen) örnek: 0.

---

## §11 Erişilebilirlik kanıt notu

| URL | Araç | Sonuç |
|-----|------|-------|
| pushState (MDN) | exa.web_fetch_exa | içerik döndü (erişilebilir) |

Status kodu (200 iddiası): `⚠️ VERIFICATION REQUIRED` (curl yok). Erişilebilirlik
içerik dönüşü ile kanıtlanmıştır (bkz. routing-web-arastirma.md §11 metodolojisi).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
