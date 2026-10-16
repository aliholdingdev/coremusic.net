---
node_id: K009-security/policies/policies-ornekler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on: K009-security/policies/policies-anayasa
evidence: shared/src/Middleware/CsrfMiddleware.php · shared/src/Middleware/SecurityHeadersMiddleware.php · shared/src/Middleware/BypassAuthMiddleware.php · auth.coremusic.net/include/Domain/ValueObject/Password.php · .ai/CLAUDE.md:112-113,336,682,687,719,721
validation: pending
open_questions: parola min 8 vs OWASP 12+ ONAY BEKLİYOR · OriginCheck fail-open P-09f'ye aykırı
group: kod
---

# K009 · policies · policies-ornekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kod Örnekleri
> Politikaların gerçek kod yüzeyi. Her alıntı **bu oturumda okunmuş dosyadan**
> kopyalanmıştır; uydurma satır yoktur. Dosya·Satır referansları doğrulanabilir.
> P-numaraları `policies-anayasa.md` §4'e göredir.

## BAĞLANTILAR

- [[policies-anayasa]] — P-01..P-18 (bu örneklerin politika karşılığı)
- [[policies-surucu]] — politika → kod → test yönlendirme tablosu
- ADR-010 (csrf) — `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` (accepted)
- [[../encryption/index]] — kripto politikası (P-05/P-06)
- [[../authentication/index]] — parola/doğrulama (P-01..P-04)
- [[index]] — dizin indeksi

---

## 1. Ornek 1 — CSRF doğrulama (P-11, P-12)

Dosya: `shared/src/Middleware/CsrfMiddleware.php`

`CsrfMiddleware.php:15-17` (bypass route listesi boş — anayasa P-12d):

```php
// CSRF bypass route'ları kaldırıldı — tüm state-changing istekler CSRF token gerektirir
// Önceki: ['set-gender'] — A01:2021 Broken Access Control riski taşıyordu
$this->bypassRoutes = $bypassRoutes ?? [];
```

`CsrfMiddleware.php:22-27` (GET/HEAD/OPTIONS hariç — anayasa P-12a):

```php
$method = strtoupper($request['method'] ?? 'GET');

$stateChanging = !in_array($method, ['GET', 'HEAD', 'OPTIONS'], true);
if (!$stateChanging) {
    return $next($request);
}
```

`CsrfMiddleware.php:42-44` (timing-safe karşılaştırma — anayasa P-11/P-12b):

```php
if ($sessionToken === '' || $submitted === '' || !hash_equals($sessionToken, (string)$submitted)) {
    return $this->fail();
}
```

`CsrfMiddleware.php:51-63` (reddin gözlemlenirliği: 403 + securityEvent — P-12c):

```php
// P5-32: CSRF reddi gözlemlenir (öncekte sessizce 403 dönüyordu).
\CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('csrf_invalid', [
    'uri' => $_SERVER['REQUEST_URI'] ?? '-',
    'ip'  => $_SERVER['REMOTE_ADDR'] ?? '-',
]);

return [
    'httpStatus' => 403,
    'type'       => 'json',
    'body'       => ['error' => 'csrf_invalid'],
    'headers'    => [],
    'halt'       => true,
];
```

**Politika bağı**: P-11 (`csrf_token` adı) · P-12 (state-changing + hash_equals + 403).
**Test**: `shared/tests/Middleware/CsrfMiddlewareTest.php` → doğrulama AD-01/AD-02.

---

## 2. Ornek 2 — Güvenlik başlıkları + CSP nonce (P-14, P-15)

Dosya: `shared/src/Middleware/SecurityHeadersMiddleware.php`

`SecurityHeadersMiddleware.php:16-19` (nonce — anayasa P-14a; pipeline #3'te üretilir):

```php
// Nonce üret — SessionManager'dan önce çalışır, nonce'u request'e yaz
if (empty($request['_csp_nonce'])) {
    $request['_csp_nonce'] = bin2hex(random_bytes(32));
}
```

`SecurityHeadersMiddleware.php:26-36` (başlık seti — anayasa P-15/P-15b):

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
```

`SecurityHeadersMiddleware.php:54-56` (strict-dynamic + nonce — anayasa P-14a):

```php
$scriptSrc = $nonce !== ''
    ? "'strict-dynamic' 'nonce-{$nonce}' https:"
    : "'self' https:";
```

`SecurityHeadersMiddleware.php:71,73` (frame-ancestors / form-action — anayasa P-15a):

```php
"frame-ancestors 'none'; " .
"base-uri 'self'; " .
"form-action 'self'";
```

**Politika bağı**: P-14 (nonce + sıra `.ai/CLAUDE.md:719`) · P-15 (başlık seti).
**Sıra uyarısı**: başlık üretimi middleware #3; sıra değişirse nonce üretimi bozulur
(`.ai/CLAUDE.md:719`) → P-14b.
**Test**: başlık varlık testi YOK → doğrulama AD-08 `RED`.

---

## 3. Ornek 3 — Parola: min uzunluk + Argon2id + pepper fail-closed (P-01..P-03)

Dosya: `auth.coremusic.net/include/Domain/ValueObject/Password.php`

`Password.php:13-14` (min 8 — anayasa P-01a):

```php
/** Asgari şifre uzunluğu (AuthService::MIN_PASSWORD_LENGTH ile aynı değer). */
private const MIN_PASSWORD_LENGTH = 8;
```

`Password.php:28-30` (ihlal sessiz kabul edilmez — P-01b):

```php
if (strlen($rawPassword) < self::MIN_PASSWORD_LENGTH) {
    throw \CoreMusic\Exception\ValidationException::passwordTooShort(self::MIN_PASSWORD_LENGTH);
}
```

`Password.php:40-48` (pepper boşsa hash yok + Argon2id maliyeti — P-03a + P-02a/b):

```php
if ($pepper === '') {
    throw \CoreMusic\Exception\ServerException::configError('APP_PEPPER');
}
$peppered = hash_hmac('sha256', $this->raw, $pepper);
return password_hash($peppered, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost'   => 4,
    'threads'     => 2,
]);
```

`Password.php:56-58` (pepper boşsa verify false — P-03b fail-closed):

```php
if ($pepper === '') {
    return false;
}
```

`Password.php:8-9` (ham parola entity'ye gitmez — P-04):

```php
* Ham şifre ile oluşturulur, hash'lenmiş hali saklanır.
* Ham şifre asla entity'de saklanmaz.
```

**Politika bağı**: P-01 (min 8) · P-02 (Argon2id, `.ai/CLAUDE.md:336`) · P-03 (pepper) · P-04 (ham parola).
**ÖRNEK SINIRI**: OWASP 12+ farkı bu dosyada çözülmez → `⚠️ ONAY BEKLİYOR` (P-01c).

---

## 4. Ornek 4 — Fail-closed bypass (P-09, P-18)

Dosya: `shared/src/Middleware/BypassAuthMiddleware.php`

`BypassAuthMiddleware.php:11` (tip sözleşmesi = fail-closed — anayasa P-09b):

```php
/** @var array{uuid: string, role: string, username: string}|array{} Boş array = fail-closed */
private array $bypassConfig;
```

`BypassAuthMiddleware.php:29-31` (credential eksikse bypass devre dışı — P-09b/P-18):

```php
if ($uuid === '' || $role === '' || $username === '') {
    return []; // Fail-closed: credential eksikse bypass devre dışı
}
```

`BypassAuthMiddleware.php:41-44` (config yoksa bypass yapılmaz — P-09c; istek
bypass'sız, anonim olarak akar):

```php
$bypass = $this->bypassConfig;
if (empty($bypass)) {
    return $next($request); // Fail-closed: config yoksa bypass yapma
}
```

**Politika bağı**: P-09 (fail-closed) · P-18 (bypass kapsamı yalnız ADR ile).
**Test**: `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` → AD-03/AD-04.
**Not**: bypass aktifken log yazılır (`SecurityHelper::logTestBypass`, :39) — gözlemlenebilirlik.

---

## 5. Ornek 5 — Vault kural metinleri (P-07, P-08, P-10, P-16)

Alıntı: `.ai/CLAUDE.md:112` (K7 middleware — anayasa P-08b kaynağı):

```text
112 | K7 Middleware | OriginCheck, CORS, RateLimit, SecurityHeaders, Session, CSRF, PSR-15 | 35 | K6 güvenlik doğrulamasını atlayamaz
```

Alıntı: `.ai/CLAUDE.md:113` (K6 — P-08c kaynağı):

```text
113 | K6 Güvenlik | JWT, RBAC, CSRF, CSP, RateLimit, AES-256, Vault, Audit | 40 | Hard Guardrail: Asla bypass edilemez
```

Alıntı: `.ai/CLAUDE.md:336` (kripto — P-05/P-02 kaynağı):

```text
336 | Encryption | AES-256-GCM, Argon2id | NIST SP 800-38D
```

Alıntı: `.ai/CLAUDE.md:682` (token adı — P-11 kaynağı):

```text
682 | `_csrf_token` | `csrf_token` |
```

Alıntı: `.ai/CLAUDE.md:687` (hardcoded secret — P-07 kaynağı):

```text
687 | Hardcoded secrets | `.env` / credential vault |
```

Alıntı: `.ai/CLAUDE.md:719` (sıra — P-14b kaynağı):

```text
719 | 1 | Middleware sırası değiştirme | CSP nonce üretimi bozulur, güvenlik açığı
```

Alıntı: `.ai/CLAUDE.md:721` (sır sızıntısı — P-07 ihlal sonucu):

```text
721 | 3 | Hardcoded secret kodda/log'da | Veri sızıntısı
```

Alıntı: `auth.coremusic.net/AGENTS.md` §4 Yasak 1-3 (P-07c/d kaynağı):

```text
Yasak 1. `config/.env` içeriğini okuyup vault'a veya chat'e yazmak (Guardrail: Secret Yok)
Yasak 3. Session/cookie değerlerini log'a yazmak
Yasak 4. `pages/*.php` içine iş mantığı (business logic) gömmek — mantık Service'te
```

Alıntı: `auth.coremusic.net/AGENTS.md` §4 Zorunlu 3 (P-02 kaynağı):

```text
3. Argon2id şifre hash'i; `Password` ValueObject dışında hash üretimi yasak
```

Alıntı: `shared/AGENTS.md` §4 Yasak 2 (P-18 kaynağı):

```text
2. `BypassAuthMiddleware` kapsam genişletme (ADR-008 kapsamı ADR ile değişir)
```

Alıntı: `shared/AGENTS.md` §4 Zorunlu 2 (P-08f kaynağı):

```text
2. Middleware ekleme → `MiddlewarePipeline` kaydı + `shared/config/` güncelleme gerekirse
```

Alıntı: `.ai/AGENTS.md` §16 (P-10a kaynağı):

```text
| Security | OWASP Top 10, CSRF=`csrf_token`, Argon2id | %100 |
```

Alıntı: `.ai/AGENTS.md` §17 (P-16 kaynağı):

```text
| 3 | Sensitive data log'da | `[REDACTED]` ile maskeleme |
```

Alıntı: `.ai/AGENTS.md` §18 (P-08d kaynağı):

```text
| 6 | Layer violation | Kod revert edilir |
```

---

## 6. Ornek 6 — Pipeline kaydı (P-08f, P-14b)

Dosya: `shared/src/PageRouter/PageRouterKernel.php:322-341` (sıra —
`MiddlewarePipelineTest.php:13` ile mühürlü; detay `policies-surucu.md` §3):

```php
SessionCookieMiddleware::class,      // P-13a — SameSite=Lax + HttpOnly (ADR-010:124)
CorrelationIdMiddleware::class,      // P-16 — redaksiyonli telemetri
SecurityHeadersMiddleware::class,    // P-14/P-15 — nonce burada üretilir
LocaleMiddleware::class,             // güvenlik dışı
RateLimiterMiddleware::class,        // P-17/P-09
OriginCheckMiddleware::class,        // P-13b/P-09f — ⚠️ kod fail-open
CsrfMiddleware::class,               // P-11/P-12
BypassAuthMiddleware::class,         // P-09/P-18
MethodOverrideMiddleware::class,     // güvenlik dışı
ApiRouterMiddleware::class,          // P-08f
```

**Not (zero-hallucination)**: satır aralığı `PageRouterKernel.php:322-341`; sınıf
adları dosyadan, satır sonu yorumları bu belgeye **açıklama olarak** eklendi
(sıra doğrulandı — `MiddlewarePipelineTest.php`).

---

## 7. Ornek Denetim Ozeti

| # | Dosya | Politika | Satır | Kaynak türü | Okundu mu |
|---|---|---|---|---|---|
| 1 | CsrfMiddleware.php | P-11, P-12, P-12d | 15-17, 22-27, 42-44, 51-63 | PHP | ✅ |
| 2 | SecurityHeadersMiddleware.php | P-14, P-15 | 16-19, 26-36, 54-56, 71-73 | PHP | ✅ |
| 3 | Password.php | P-01..P-04 | 13-14, 28-30, 40-48, 56-58, 8-9 | PHP | ✅ |
| 4 | BypassAuthMiddleware.php | P-09, P-18 | 11, 29-31, 41-44, 39 | PHP | ✅ |
| 5 | vault metinler (CLAUDE/AGENTS) | P-02/05/07/08/10/11/14/16/18 | 112,113,336,682,687,719,721 + AGENTS §4/§16/§17/§18 | metin | ✅ |
| 6 | PageRouterKernel.php | P-08f, P-14b | 322-341 | PHP | ✅ |

**Uydurma kontrolü**: 6 örnek = okunmuş dosyalardan; uydurma satır 0.
`⚠️` işaretli iki sınır (min parola 8 vs 12+, OriginCheck fail-open) bilinçli
olarak çözüm iddiası içermiyor — karar üst mercide (`policies-dogrulama.md` §6).

**Eksik alıntılar (bilerek yok)**: `RateLimiterMiddleware` (P-17) ve
`OriginCheckMiddleware` (P-09f) bu oturumda dosya bazında okunmadı → alıntı
uydurulmadı, doğrulama AD-11/AD-12 RED/OPEN'da bekliyor.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
