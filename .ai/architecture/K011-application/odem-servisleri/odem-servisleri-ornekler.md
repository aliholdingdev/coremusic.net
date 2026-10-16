---
title: "K011 · odem-servisleri — ornekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: odem-servisleri
status: active
updated: 2026-10-15
group: kod
---

# K011 · odem-servisleri — Kod Örnekleri

**Amaç:** Ödeme entegrasyonunda **kullanılacak** güvenlik/entegrasyon iskeletinin 4 dilde (PHP · Vanilla JS · SQL · CSS) repo kanıtlı örnekleri.

> ⚠️ **Kritik uyarı:** Repo'da ödeme kodu **YOKTUR**. Aşağıdaki hiçbir blok ödeme kodu değildir — hepsi, repo'da **gerçekten var olan** güvenlik/entegrasyon yüzeyidir ve ödeme entegrasyonunda da **aynı şekilde** kullanılacaktır. Uydurma ödeme kodu yazılmamıştır.

## §1 — PHP: Güvenlik Başlığı Yığını (ödeme sayfası için de geçerli)

```php
// Dosya: shared\src\Middleware\SecurityHeadersMiddleware.php · Satır: 26-43
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

**Ödeme çerçevesi:** `X-Frame-Options: DENY` ödeme formunu **clickjacking**'e karşı korur; `Require-Trusted-Types-For: 'script'` DOM XSS'i kısıtlar.
**YAPMA:** Ödeme sayfasında bu başlıkları atlamak · `frame-ancestors`'ı açmak.
**YAP:** Aynı middleware'i ödeme rotalarına da uygulamak (ekstra kod gerekmez).

## §2 — PHP: Hassas Veri Maskeleme (PAN/token için)

```php
// Dosya: shared\src\Log\Redactor.php · Satır: 28, 31, 65
public const MASK = '[REDACTED]';

private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';

public static function redact(string $line): string
```

**Ödeme çerçevesi:** PCI DSS gereği log'a düşen kart/ödeme verisi maskelenir. `SENSITIVE_KEYS` listesine `card_number`/`pan`/`cvc` **henüz eklenmemiştir** (boşluk).
**YAPMA:** Log'a ham kart verisi / PAN / CVC yazmak.
**YAP:** `Redactor::redact()` her log yazımında · `SENSITIVE_KEYS`'e ödeme anahtarlarını ekleme **ADR gerektirir**.

## §3 — PHP: Rate Limit (ödeme denemeleri için)

```php
// Dosya: shared\src\Api\Middleware\RateLimitMiddleware.php · Satır: 34-49
public function __invoke(array $request, callable $next): array
{
    $key = $this->getRateLimitKey();

    // Check if rate limited
    if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {
        header('Retry-After: ' . $this->windowSeconds);
        return ApiResponse::error(
            'RATE_LIMIT_EXCEEDED',
            'Too many requests',
            429
        );
    }

    // Increment rate limit counter
    $this->rateLimiter->increment($key, $this->windowSeconds);
```

**Ödeme çerçevesi:** Brute-force ödeme denemelerini sınırlar (kötü niyetli kart denemesi).
`Retry-After` başlığı zaten üretiliyor (satır 40) — ödeme 429'u için ek kod gerekmez.
**YAPMA:** Ödeme rotasını rate limit dışında bırakmak.
**YAP:** `$maxRequests`/`$windowSeconds`'ı ödeme rotaları için **daha sıkı** eşikle vermek.

> **Not (2026-10-16):** Önceki sürümde bu blokta `// ...` placeholder'ı vardı — **gerçek kod
> satırlarıyla değiştirildi** (okuma: 34-49). Placeholder artık yoktur.

## §4 — Vanilla JS: İstek Başlığı Köprüsü

```js
// Dosya: assets.coremusic.net\js\router\FetchWrapper.js · Satır: 47-52
const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };

if (csrfHeader) Object.assign(headers, csrfHeader);

if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
```

**Ödeme çerçevesi:** Aynı `FetchWrapper`, ödeme API çağrısını da taşır; `X-Trace-Id` **ödeme izlenebilirliği** için kritik.
**YAPMA:** Ödeme isteklerini ayrı/farklı fetch ile yapmak (CSRF koruması kaybolur).
**YAP:** `FetchWrapper` üzerinden gitmek; CSRF + trace-id otomatik eklenir.

## §5 — Vanilla JS: CSRF Token Alımı

```js
// Dosya: assets.coremusic.net\js\router\CsrfSyncManager.js · Satır: 5-7
getToken() { const el = document.querySelector(CSRF_SELECTOR); return el?.value || null; }
update(token) { if (!token) return; const els = document.querySelectorAll(CSRF_SELECTOR); for (const el of els) el.value = token; }
getHeader() { const token = this.getToken(); return token ? { [HEADERS.X_CSRF_TOKEN]: token } : {}; }
```

**Ödeme çerçevesi:** Ödeme POST'unun CSRF token'ı zorunlu.
**YAPMA:** Token yokken isteği yine de göndermek (sunucu reddeder ama kullanıcı hata alır).
**YAP:** `getHeader()` ile token'ı koşullu ekle; boşsa isteği **engelle**.

## §6 — SQL: Ödeme Gözetim Tablosu İskeleti (mevcut API logu temel alınarak)

```sql
-- Dosya: .ai\.sql\mysql\coremusic_api.sql · Satır: 18-31, 39 (KEY satırları 32-38 bilinçli atlandı)
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
  PRIMARY KEY (`id`,`called_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

**Ödeme çerçevesi:** `api_calls` **mevcut** gözetim tablosudur; ödeme olayları için **aynı desen** izlenecektir (`payment_id`, `idempotency_key`, `amount`, `currency` sütunları **PLANNED**).
**UYARI:** `request_body` sütunu ödeme verisi alırsa **KVKK/PCI ihlali** doğar — başlıkta bilinçli veri yasağı ilan edilmiştir (`coremusic_api.sql:5`).
**YAPMA:** PAN/CVC'yi `request_body`'ye yazmak.
**YAP:** Yalnız `payment_intent_id` + `idempotency_key` + maskelenmiş özet.

## §7 — CSS: Ödeme Formu Token'ları (mevcut token altyapısı)

```css
/* Dosya: assets.coremusic.net\Css\01_Abstracts\a-primitive-tokens.css · Satır: 31-42 */
--cm-pink: #f200d0;
--cm-pink-mid: #bc04a3;
--cm-pink-deep: #92007d;
--cm-pink-primary-button: #FF00C8;
--cm-blue: #0a7fab;
--cm-green: #8dd429;
--cm-orange: #ff6e0d;
--cm-gray: #9d9d9d;
--cm-gray-light: #d9d9d9;
--cm-gray-dark: #424242;
```

**Ödeme çerçevesi:** Başarı (`--cm-green`) / hata (`--cm-orange`) durum token'ları ödeme geri bildirimi için hazır.
**YAPMA:** Ödeme formuna ham hex yazmak (token yasağı).
**YAP:** Semantic token üzerinden okumak (`a-semantic-token.css:170` deseni).

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

## §8 — PHP: CSP Üretimi (SecurityHeadersMiddleware tam okuma)

> Kanıt: `shared\src\Middleware\SecurityHeadersMiddleware.php` · **104 satır** · bu oturumda okundu (2026-10-16).

### §8.1 Nonce — session'dan önce, idempotent (Satır: 16-19)

- Kod: `if (empty($request['_csp_nonce'])) { $request['_csp_nonce'] = bin2hex(random_bytes(32)); }` (17-19).
- `random_bytes(32)` → **64 karakter** nonce; `empty()` sayesinde ikinci middleware
  geçişinde **yeniden üretilmez** (yorum 16: *"SessionManager'dan önce çalışır"*).
- Değer **request dizisinde** taşınır (header değil); `$response = $next($request)` (21)
  ile downstream'e gider, CSP ise **yanıt** üzerine yazılır (23, 43).

### §8.2 buildCsp — nonce var / yok iki dal (Satır: 48-60)

- `$nonce = $request['_csp_nonce'] ?? '';` (52) → `$scriptSrc` (54-56): nonce varsa
  `'strict-dynamic' 'nonce-{nonce}' https:`, **boşsa** fallback `'self' https:`.
- `$styleSrc` (58-60): nonce varsa `'nonce-{…}' 'self' {assets} fonts.googleapis.com`,
  boşsa aynı liste **nonce'suz** — `fonts.googleapis.com` **her iki dala da** yazılır.
- Yani nonce üretilemezse site çalışmaya devam eder (fail-safe; `⚠️ VERIFICATION REQUIRED`:
  bu dalın üretime girip girmediği bu dosyadan okunmaz).
- `'strict-dynamic'` + nonce birlikte: nonce'suz **3. taraf script girmez** (§16 W5).
- Kaynak: `resolveAssetsOrigin` (50) · `resolveApiOrigin` (51) · `buildCsp` gövdesi 48-74.

### §8.3 CSP — 11 direktif, birebir satırlar (Satır: 62-73)

```php
// Dosya: SecurityHeadersMiddleware.php · Satır: 62-73
return
    "default-src 'self'; " . "script-src {$scriptSrc}; " . "style-src {$styleSrc}; " .
    "style-src-attr 'unsafe-inline'; " . "img-src 'self' data: {$assetsOrigin}; " .
    "font-src 'self' {$assetsOrigin} fonts.gstatic.com; " .
    "connect-src 'self' {$assetsOrigin} {$apiOrigin}; " .
    "media-src 'self' {$assetsOrigin}; " . "frame-ancestors 'none'; " .
    "base-uri 'self'; " . "form-action 'self'";
```

| # | Direktif | Değer | Ödeme etkisi |
|---|---|---|---|
| 1 | `default-src` | `'self'` | fallback sıkı |
| 2 | `script-src` | nonce + `strict-dynamic` / `'self' https:` | 3. taraf ödeme script'i nonce'suz **girmez** |
| 3 | `style-src` | nonce + `'self'` + assets + `fonts.googleapis.com` | harici font CDN'e izin var |
| 4 | `style-src-attr` | `'unsafe-inline'` | satır-içi `style=` attr serbest (bilinçli) |
| 5 | `img-src` | `'self' data: {assets}` | **`https:` YOK** → dış CDN görseli engelli |
| 6 | `font-src` | `'self' {assets} fonts.gstatic.com` | 2 dış origin |
| 7 | `connect-src` | `'self' {assets} {api}` | **yalnız 3 origin** → ödeme sağlayıcı fetch'i engeli (§16 W6) |
| 8 | `media-src` | `'self' {assets}` | ses/görsel asset |
| 9 | `frame-ancestors` | `'none'` | clickjacking (§1 `X-Frame-Options: DENY` ile çift katman) |
| 10 | `base-uri` | `'self'` | `<base>` enjeksiyonu kapalı |
| 11 | `form-action` | `'self'` | ödeme formu yalnız kendi origin'ine gönderir |
| — | `object-src` | **direktif YOK** | `default-src 'self'` fallback'ine düşer (62-73'te yok) |

### §8.4 Origin çözümleme — 3 kademeli fallback (Satır: 76-103)

- `resolveAssetsOrigin` (76-79) → `resolveOrigin('assets', 'https://assets.coremusic.net')`.
- `resolveApiOrigin` (81-85, docblock: *"connect-src — API origin (auth sayfaları fetch -> api.coremusic.net)"*)
  → `resolveOrigin('api', 'https://api.coremusic.net')`.
- `resolveOrigin` (87-103): `DomainConfig::getUrl($name)` → boşsa **fallback**;
  `parse_url` `false`/bozuk → `?? 'http'` + `?? ''` → `$host === ''` → **fallback**.
  `$this->domainConfig` **null** ise (Satır: 90 `?->`) doğrudan fallback.
  Yani bozuk config `connect-src`'i **asla boş üretmez** (fail-safe).

## §9 — HSTS ve COOP/COEP: kanıtla tespit

### §9.1 HSTS başlığı BU dosyada YOK (arama kanıtlı)

- `SecurityHeadersMiddleware.php` üzerinde `HSTS` / `Strict-Transport` araması:
  **0 isabet** (okuma 2026-10-16; dosyanın 104 satırının tamamı okundu).
- `$headers` dizisi 9 anahtar (Satır: 26-36) + `$isHttps` dalı 2 başlık
  (Satır: 38-41) = **11 başlık**; hiçbiri HSTS değil.
- **Sonuç:** `Strict-Transport-Security` bu middleware'de üretilmiyor.
  Ödeme sayfası (HTTPS zorunlu domain) için HSTS **eksik yüzey** →
  etiket: `PLANNED` (ADR gerekir; `⚠️ VERIFICATION REQUIRED`: HSTS başka bir
  katmanda üretiliyor mu — `shared\src\` içinde ayrıca aranmadı).

### §9.2 COOP/COEP — yalnız HTTPS; config null ise kapalı (Satır: 24, 38-41)

- `$isHttps = $this->domainConfig?->isHttps() ?? false;` (24) → `domainConfig === null`
  → `false` → COOP/COEP **eklenmez** (fail-safe: kırık config `require-corp` ile
  kaynak yüklemesini kırmaz).
- `$isHttps` ise 2 başlık: `Cross-Origin-Opener-Policy: same-origin` (39) +
  `Cross-Origin-Embedder-Policy: require-corp` (40).
- `require-corp` ile birlikte okunur: `Cross-Origin-Resource-Policy: cross-origin` (33)
  → **gönderen** taraf geniş, **alan** taraf (`COEP`) sıkı.

### §9.3 Başlık envanteri — dosyanın 104 satırı okundu

- `$headers` dizisi **9 başlık** (Satır: 26-36): `X-Content-Type-Options: nosniff` (27) ·
  `X-Frame-Options: DENY` (28) · `X-XSS-Protection: 0` (29, eski tarayıcı filtresi
  **bilinçli kapalı**) · `Referrer-Policy` (30) · `Content-Security-Policy` (31) ·
  `Require-Trusted-Types-For: 'script'` (32) · `Cross-Origin-Resource-Policy` (33) ·
  `Permissions-Policy` camera/mic/geo/interest-cohort `()` (34) ·
  `X-Permitted-Cross-Domain-Policies: none` (35) + 2 HTTPS başlığı (39-41) = **11**.
- **Yok:** `Strict-Transport-Security` (§9.1) · `object-src` (§8.3) ·
  `Server`/`X-Powered-By` gizleme → `⚠️ VERIFICATION REQUIRED` (başka katmanda aranmadı).

## §10 — PHP: Redactor tam okuma (3 maskeleme adımı)

> Kanıt: `shared\src\Log\Redactor.php` · **106 satır** · bu oturumda okundu (2026-10-16).

### §10.1 Sabit ve pattern tablosu (dosya satır birebir)

| Sabit | Satır | Değer / desen |
|---|---|---|
| `MASK` | 28 | `'[REDACTED]'` |
| `SENSITIVE_KEYS` | 31 | **11 anahtar**: `auth_key, password, passwd, csrf_token, token, secret, api_key, session_id, session, authorization, cookie` |
| `KEY_VALUE_PATTERN` | 40 | `key=value` · `key: value` · `"key":"value"` · `Bearer/Basic/Digest <token>` |
| `LONG_HASH_PATTERN` | 49 | 64 char hex/base64, lookaround'lı sınır |
| `EMAIL_PATTERN` | 56 | yerel kısım maskelenir, alan adı kalır |
| `__construct` | 58-60 | `private` → tek örneksiz sınıf (static API) |

- `SENSITIVE_KEYS` **11** anahtar; `card_number` / `pan` / `cvc` **yok** → §2'deki
  "boşluk" tespiti dosya içi doğrulandı (Satır: 31 birebir okundu).

### §10.2 `redact(string)` — 3 adım, null-safe geri dönüşler (Satır: 65-93)

| Adım | Ne maskeler | Satır | Boşta kalma (`null`) durumu |
|---|---|---|---|
| 0 | `$line === ''` → olduğu gibi döner | 67-69 | — |
| 1 | `KEY_VALUE_PATTERN` (hassas anahtar + değer) → `maskValue($m[1], $m[2])` | 72-76 | `null` → **orijinal satır** (78-80) |
| 2 | `LONG_HASH_PATTERN` → `self::MASK` | 83 | `null` → 92'de `$replaced ?? $redacted` |
| 3 | `EMAIL_PATTERN` → `'***@' . $m[1]` | 86-90 | `null` → 92 zinciri |

- `static fn` (74, 88) — kapanışlar durum tutmaz; `Redactor` `private __construct`
  (58-60) ile **tek örneksiz**, tamamı static API.
- `maskValue` (98-105): değer `"`/`'` ile **başlıyorsa tırnak korunur**
  (`"password":"[REDACTED]"`, Satır: 100-104), aksi halde düz `[REDACTED]`.
- Vault kuralı ile kod **çapraz bağlı**: dosya yorumu `.ai/AGENTS.md §17 Edge Case 3`
  "Sensitive data log'da → [REDACTED]" (Satır: 8).

### §10.3 UUID korunur (hash deseninin bilinçli istisnası · Satır: 43-49)

- Docblock: *"Tam sınırıyla 64-char hex veya base64 hash … UUID tire içerdiği için bu
  kurala girmez"* (43-47) → `LONG_HASH_PATTERN` (49) `\b` yerine lookaround kullanır.
- Sonuç: `8-4-4-4-12` UUID → 64 char desenine girmez → **izlenebilirlik kimlikleri**
  (trace/order id) logda korunur; `⚠️ VERIFICATION REQUIRED`: ödeme idempotency
  anahtarının uzunluğu bu dosyadan okunmaz.

## §11 — PHP: RateLimit tam okuma (key üretimi + immutable override)

> Kanıt: `shared\src\Api\Middleware\RateLimitMiddleware.php` · **80 satır** · okundu (2026-10-16).

### §11.1 Varsayılan eşikler (Satır: 22-29)

- `DEFAULT_MAX_REQUESTS = 60` (22) · `DEFAULT_WINDOW_SECONDS = 60` (23) →
  **60 istek / 60 sn**; constructor Promoted property olarak `private int` (27-28),
  `$rateLimiter` `readonly` (26).
- §3'teki `$maxRequests`/`$windowSeconds` önerisi bu iki parametreye bağlanır →
  rotaya özel sıkılaştırma `withLimits()` ile (§11.3).

### §11.2 `__invoke` — §3 bloğunun devamı ve **düzeltme kaydı** (Satır: 48-52)

- `increment($key, $this->windowSeconds)` (49) → yalnız **izin verilen** istekte
  sayaç artar (`isLimited` true ise 49'a hiç gelinmez, 39-46'da 429 döner).
- `return $next($request);` (51) → middleware zincirine devam.
- **Düzeltme (2026-10-16):** bu dosyada `try`/`catch` **YOKTUR** (80 satırın tamamı
  okundu) → `CacheRateLimiter` istisnası **yukarıya taşınır**;
  `⚠️ VERIFICATION REQUIRED`: fail-safe davranışı `shared\src\Security\CacheRateLimiter.php`
  bu görevde okunmadı. Önceki taslakta yer alan "catch → allow" ifadesi bu dosyaya
  ait **değildir**, çıkarılmıştır.

### §11.3 Key üretimi + immutable override (Satır: 57-79)

| Satır | Kod | Not |
|---|---|---|
| 60-62 | `$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;` → `return 'api:' . hash('sha256', $apiKey);` | ham anahtar **asla** key'e girmez — log/istatistik sızıntısı önlenir |
| 66-67 | `$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';` → `return 'ip:' . $ip;` | header yoksa tek anonim kova (fail-safe) |
| 73-78 | `withLimits(int, int): self { $clone = clone $this; … return $clone; }` | **immutable** → mevcut instance bozulmaz; ödeme rotası aynı middleware'i kendi eşiğiyle kullanır |
| 40 | `header('Retry-After: ' . $this->windowSeconds);` | §3'teki başlık ile aynı satır (40 ↔ 40 ✓) |

**YAPMA:** Küresel 60/60 eşiğini düşürüp diğer API'yi yavaşlatmak.
**YAP:** `withLimits(5, 60)` gibi **rotaya özel** kova — küresel state yok.

## §12 — Vanilla JS: FetchWrapper tam okuma (retry + abort zinciri)

> Kanıt: `assets.coremusic.net\js\router\FetchWrapper.js` · **63 satır** · okundu (2026-10-16).

### §12.1 Donmuş konfigürasyon + private alanlar (Satır: 5-11)

| Satır | Kanıt | Not |
|---|---|---|
| 6 | `static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 })` | çalışma zamanında **değiştirilemez** |
| 7-9 | `#controller` / `#csrfSync` / `#logger` | `#` private → dışarıdan erişim yok |
| 11 | `constructor(logger, csrfSync = null)` | CSRF köprüsü **opsiyonel** (null ise §12.3'teki `?.` dalı devreye girer) |
| 5 | `export default class FetchWrapper` | tek ihracat |

**Ölçü:** en kötü durum ≈ 3 deneme × (10 sn timeout + 500 ms gecikme) — ödeme UI'sında
bekleme üst sınırı olarak okunur.

### §12.2 Retry yalnız 5xx; abort her dala öncelikli (Satır: 26-41)

| Kural | Satır | Ödeme etkisi |
|---|---|---|
| Döngü: `attempt <= MAX_RETRIES` (0,1,2 = 3 çağrı) | 26 | — |
| `if (signal.aborted) throw …` döngü **başında** | 27 | iptal, retry'dan önce gelir |
| `status >= 500` ve `attempt < MAX_RETRIES` → `continue` | 30-33 | yalnız sunucu hatası tekrarlanır |
| `4xx` / `402` / `409` / `422` | 30 | `>= 500` **false** → **tekrarlanmaz**, kullanıcıya anında gider |
| `429` | 30 | `429 >= 500` **false** → `Retry-After` bu wrapper'da **okunmaz** (`⚠️ VERIFICATION REQUIRED`: tüketimi başka katmanda aranmalı) |
| `err.name === ABORT_ERROR_NAME → throw err` | 37 | `AbortError` **retry sayılmaz** |
| `lastError` | 36, 41 | tüm denemeler bitince **ilk hata** değil, **son** hata fırlatılır |

### §12.3 `#doFetch` — §4 bloğunun tamamı (Satır: 46-53)

> Blok bu dosyanın **§4'ü ile birebirdir** (orada `Satır: 47-52`); burada yalnız
> okuma notları: `$csrfHeader = this.#csrfSync?.getHeader()` (48) ·
> `if (csrfHeader) Object.assign(headers, csrfHeader)` (49) ·
> `const traceId = this.#logger?.traceId; if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;` (50-51) ·
> `return fetch(url, { method: 'GET', headers, credentials: 'include', signal });` (52).

- `credentials: 'include'` (52) — **çapraz origin cookie**; `connect-src` §8.3'te
  yalnız 3 origin olduğu için etkisi `api.coremusic.net` ile sınırlı (çifte sınır).
- `Object.assign` (49) — caller header'ları **ezilmez**, CSRF **eklenir**;
  `csrfSync` null ise (48 `?.`) başlık hiç eklenmez.
- `attempt` parametresi (46) bu gövdede **kullanılmıyor** → `⚠️ VERIFICATION REQUIRED`:
  deneme sayısına göre idempotency başlığı **planlanmış** olabilir.
- **`method: 'GET'` sabittir (52)** → bu sınıfta `POST` **hiç yok**:
  **YAPMA** ödeme `POST`'unu buraya ekleyip `GET`'i unutmak ·
  **YAP** `method`/`body` parametresi ADR ile eklenmeden önce; `X-Trace-Id` (51)
  ödeme izinde korunur.

## §13 — Vanilla JS: timeout/abort (öz kanıt)

| Kanıt | Satır | Ne |
|---|---|---|
| `static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 })` | 6 | 10 sn timeout · 2 retry · 500 ms gecikme (değiştirilemez) |
| `this.abort()` + `new AbortController()` | 14-15 | yeni istek, öncekini iptal eder → yarım kalan ödeme isteği kapanır |
| `createTimeout(…)` + `combineSignals(…)` | 16-19 | caller sinyali + manuel iptal + timeout birleştirilir |
| `finally { clearTimeoutFn(); this.#controller = null; }` | 21 | timer/controller temizliği → sızıntı yok |
| `if (signal.aborted) throw …` (döngü başında) | 27 | iptal, retry'dan önce gelir |
| `err.name === ABORT_ERROR_NAME → throw` | 37 | `AbortError` retry **sayılmaz** |
| `#delayWithAbort`: `signal.addEventListener('abort', …, { once: true })` | 60 | listener tek sefer → bellek sızıntısı yok |
| `#delayWithAbort` erken `signal.aborted` reddi | 57 | timer dolmuşsa yeni listener takılmaz |
| `combineSignals` import'u | 3 | `⚠️ VERIFICATION REQUIRED`: üçlü sinyalin iptal öncelik sırası `config/signal-utils.js`'ten okunmadı |

## §14 — SQL: KEY/tanım satırları (§6 düzeltmesinin kaynağı)

```sql
-- Dosya: .ai\.sql\mysql\coremusic_api.sql · Satır: 31
  PRIMARY KEY (`id`),
-- Satır: 34
  KEY `idx_endpoint` (`endpoint`(64)),
-- Satır: 35
  UNIQUE KEY `uq_method_path` (`method`,`path`),
-- Satır: 36-38
  KEY `idx_is_active` (`is_active`),
  KEY `idx_created_at` (`created_at`),
  KEY `fk_api_routes_created_by` (`created_by`)
```

| Kanıt | Satır | Not |
|---|---|---|
| `CREATE TABLE api_routes` başlığı | 18 | bölüm başlangıcı |
| `PRIMARY KEY (`id`)` | 31 | §6'da `id` sütunu 18-31 aralığının **sonu** |
| 4 `KEY` + 1 `UNIQUE` + 1 `fk` | **32-38 (7 satır)** | eski belge "32-35" ifadesi **düzeltildi** (2026-10-16) |
| `endpoint`(64) prefix index | 34 | `varchar(500)` sütun için bilinçli 64 karakter prefix |
| `CREATE TABLE api_route_logs` başlığı | 39 | §6'daki ikinci tablo |

**YAPMA:** 500 karakterlik `endpoint` sütununa tam uzunluk index denemek.
**YAP:** `KEY idx_endpoint (endpoint(64))` desenini ödeme `path` sütununda da
kopyalamak (ödeme tablosu `PLANNED` — §6).

## §15 — Sayım envanteri (tekrarlanabilir glob sonuçları)

| Kümе | Sayı | Kanıt günü |
|---|---|---|
| `shared\src\**\*.php` | **165** | 2026-10-15 |
| `shared\tests\**\*.php` (dir 9) | **45** | 2026-10-15 |
| `auth\**\*.php` | **37** | 2026-10-15 |
| `assets.coremusic.net\js\**\*.js` | **95** (bunun 7'si `*.spec.js`) | 2026-10-15 |
| `shared\src\Router` yerine gerçek router: `shared\src\PageRouter\` + `config\routes.php` | 30 router ailesi dosyası | 2026-10-15 |
| DTO (`*Response.php`/`*Request.php`) | **11 dosya / 999 satır** (ort **90,8**; MusicResponse 116 · MusicSearch 94 · PlaylistResponse 94 · UserUpdate 72) | 2026-10-16 düzeltildi (eski "1.011 / 91,9" **yanlış**) |
| `for (const [k, v] of Object.entries(` | **62** (AdminFeature 16 · MusicSearch 15 · MusicCompare 12 · Channel 8 · Playlist 6 · Profile 4 · Dashboard 1) · `.reduce`/yürüteç varyantı **0** | 2026-10-15 |
| `JSON.parse`/`JSON.stringify` dosyası | **19 / 7** | 2026-10-15 |
| `serialize(`/`unserialize(` | **4 / 0** (`CacheKeySerializer.php` 18,46 · `RouteCacheService.php` 78,87) | 2026-10-16 |
| `fromArray` / `toArray` dosyası | **15 / 14** | 2026-10-15 |
| Bileşen CSS (§7'ye giren 7 dosya) | **477 satır** | 2026-10-15 |
| `\bPAN\b` (tam kelime) / `idempotency` / `api.stripe.com` / `unserialize(` repo geneli | **0 / 0 / 0 / 0** | 2026-10-15 |
| test: `auth\**\LoginRequestTest.php` | **73 satır · 5 `#[Test]`** | 2026-10-16 — "DTO testi 0" ifadesi **sınırlıdır** (`shared\tests` 0, auth 1 dosya) |

> **Düzeltme kaydı (2026-10-16):** "DTO toplam 1.011 satır / ort 91,9" ile
> "envanter dosyası diskte YOK" kayıtları **yanlış bulunup** bu belgede ve
> kardeş belgelerde düzeltildi; envanterin gerçek yolu `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt`
> (**4286 satır / 252847 bayt**) — eski `Temp\opencode\k011-urls.txt` yolu yanlıştır.

## §16 — Web-arastirma claim matrisi (kod tarafı çapraz okuma)

> Kesişim sonucu (her iki `*-web-arastirma.md` ile **birebir aynı satır**):
> `Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).`
> Dolayısıyla havuzdan çıkarılan URL: **0** · reddeden adaylara eklenen etiket: **0**.

| # | Claim (web-arastirma dosyası) | Kod karşılığı (bu görevde okundu) | Sonuç |
|---|---|---|---|
| W1 | CSP `strict-dynamic` + nonce kullanımı | `SecurityHeadersMiddleware.php:54-55` + `:17-19` | ✓ |
| W2 | `connect-src` dış origin engeli | `:69` yalnız `'self' {assets} {api}` | ✓ |
| W3 | `frame-ancestors 'none'` | `:71` | ✓ |
| W4 | HSTS başlığı | **0 isabet** (§9.1) | ✗ eksik → `PLANNED` |
| W5 | 3. taraf ödeme script'i (harici JS) | nonce'lu `script-src` harici kaynak **kabul etmez** (§8.2) | ⚠️ entegrasyon engeli — ADR gerekli |
| W6 | Harici ödeme sağlayıcısı fetch/XHR | `connect-src` 3 origin (§8.3 #7) | ⚠️ aynı engel |
| W7 | Log maskeleme `[REDACTED]` | `Redactor.php:28` + `:65-93` | ✓ |
| W8 | Hassas anahtar listesi | `Redactor.php:31` (11 anahtar; kart anahtarları **yok**) | ✓ kısmi |
| W9 | Rate limit `Retry-After` | `RateLimitMiddleware.php:40` | ✓ |
| W10 | Fetch retry politikası | `FetchWrapper.js:30` (yalnız ≥500) | ✓ |
| W11 | Ödeme kodu varlığı | `\bPAN\b`=0 · `idempotency`=0 · `api.stripe.com`=0 (§15) | ✓ "yok" iddiası |
| W12 | `unserialize` kullanıcı girdisi | repo **0** (§15) | ✓ |

**Açık sorular (üst karar gerektirir):**

1. Ödeme sağlayıcısı script/fetch'i CSP'ye **nasıl** eklenecek — nonce'lu `script-src`'e
   hedef origin mi, yoksa proxy/sunucu tarafı akış mı? (ADR) — §16 W5/W6 engeli.
2. HSTS hangi katmanda? (§9.1) — `PLANNED`, `⚠️ VERIFICATION REQUIRED`.
3. Ödeme `POST` retry'sinde idempotency başlığı `#doFetch`'e mi taşınacak? (§12.3 `attempt` kullanılmıyor)

## §17 — Edge-case ve YAPMA↔YAP özeti

| # | Edge-case | Davranış | Kanıt |
|---|---|---|---|
| 1 | `DomainConfig` null | origin fallback, COOP/COEP kapalı | `SecurityHeaders:24, 90` |
| 2 | `parse_url` başarısız | `$host === ''` → sabit fallback | `SecurityHeaders:92-102` |
| 3 | nonce zaten varsa | yeniden üretilmez (idempotent) | `SecurityHeaders:17` |
| 4 | regex `null` dönerse | orijinal satır döner | `Redactor:78-80, 92` |
| 5 | UUID logda | korunur (64 char desenine girmez) | `Redactor:49` docblock 43-47 |
| 6 | IP header'sız istek | `0.0.0.0` kovası | `RateLimit:66` |
| 7 | `429` yanıtı | retry **yapılmaz** | `FetchWrapper:30` |
| 8 | istek iptal edilirse | retry sayaçları artmaz, timer temizlenir | `FetchWrapper:27, 37, 57-60` |
| 9 | `redact('')` | olduğu gibi döner | `Redactor:67-69` |

| # | YAPMA | YAP | Kanıt |
|---|---|---|---|
| 1 | Log'a ham kart/PAN yazmak | `Redactor::redact()` + `SENSITIVE_KEYS` genişletme **ADR ile** | `Redactor:31, 65` |
| 2 | Ödeme sağlayıcısını doğrudan tarayıcıdan çağırmak | sunucu tarafı proxy (CSP `connect-src` 3 origin) | `SecurityHeaders:69` |
| 3 | Global rate limit'i kısma | `withLimits()` ile rotaya özel kova | `RateLimit:73-79` |
| 4 | 4xx'i retry'a sokmak | yalnız `>= 500` | `FetchWrapper:30` |
| 5 | HSTS'yi "var varsaymak" | kanıt yok → `PLANNED` + ADR | §9.1 |
| 6 | Ödeme `POST`'unu `method: 'GET'` ile göndermek | method/body parametresi eklenmeden önce ADR | `FetchWrapper:52` |
| 7 | `KEY`'leri "4 satır" sanmak | 32-38 (7 satır) saymak | §14 |
| 8 | `select *` / ham hex / `var` | proje yasakları (SQL · token · JS) | proje kuralları |

## §18 — Doğrulama adımları (tekrarlanabilir · okuma tabanlı)

```powershell
# 1) Belge satır sayısı (gate: >= 500)
[System.IO.File]::ReadAllLines("C:\www\coremusic.net\.ai\architecture\K011-application\odem-servisleri\odem-servisleri-ornekler.md").Length
# 2) HSTS (beklenen 0) · 3) kart anahtarları Redactor'da (beklenen 0)
Select-String -Path "C:\www\coremusic.net\shared\src\Middleware\SecurityHeadersMiddleware.php" -SimpleMatch "Strict-Transport"
Select-String -Path "C:\www\coremusic.net\shared\src\Log\Redactor.php" -SimpleMatch "card_number","pan","cvc"
# 4) Ödeme izi (beklenen 0)
(Get-ChildItem -Recurse -File -Include *.php,*.js -Path C:\www\coremusic.net\shared,C:\www\coremusic.net\assets.coremusic.net |
  Select-String -SimpleMatch "api.stripe.com","idempotency" -List | Measure-Object).Count
# 5) Kesişim (salt okuma; havuz URL'leri rapora kopyalanmaz)
([System.IO.File]::ReadAllLines("C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt")).Count   # 4286
```

- 2026-10-16 koşusu: (2) `0` · (3) `0` · (4) `0` · (5) `4286`.
- `.ai/scripts/*.ps1` **çalıştırılmadı** (yasak) — tüm ölçüm doğrudan okuma.

## §19 — Özet ve sonraki okuma

- §1-§7 **korunarak** (hiçbir blok silinmedi) §8-§19 eklendi: CSP üretimi (16-19 ·
  48-74 · 76-103) · HSTS tespiti (0 isabet) · 11 başlık envanteri · Redactor 3 adım
  (65-103) · RateLimit key/override (57-79) · FetchWrapper retry/abort (13-62) ·
  SQL KEY 32-38 · sayım envanteri · claim matrisi (W1-W12) · edge-case + YAPMA↔YAP.
- **Kanıt disiplini:** her iddia `Dosya: … · Satır: …` taşır; okunmayan gövdeler
  `⚠️ VERIFICATION REQUIRED`, ödeme kodu `PLANNED`.
- **Sonraki okuma:** `shared\src\Security\CacheRateLimiter.php` (§11.2 boşluk) →
  `assets.coremusic.net\js\router\config\signal-utils.js` (§13 sinyal sırası) →
  `shared\src\Api\ApiResponse.php` (429 gövdesi).

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
