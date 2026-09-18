---
title: "K7 — Middleware Pipeline Katmanı (Middleware Pipeline)"
type: architecture
category: layer-definition
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k7-middleware.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K7
  component_count: 35
  adr:
    - "[[ADR-010-csrf-protection-strategy]]"
    - "[[ADR-011-session-management]]"
    - "[[ADR-012-csp-nonce-strict-dynamic]]"
    - "[[ADR-013-rate-limiting-apcu]]"
  github:
    - name: "PSR-15 HTTP Server Request Handlers"
      url: "https://www.php-fig.org/psr/psr-15/"
    - name: "Symfony HttpFoundation"
      url: "https://github.com/symfony/http-foundation"
    - name: "Nyholm PSR-7"
      url: "https://github.com/Nyholm/psr7"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K7 — Middleware Pipeline Katmanı (Middleware Pipeline)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic ekosistemi için middleware pipeline altyapısı. PSR-15 uyumlu, sırası değişmez pipeline. 35 bileşen.

---

## 1. Genel Bakış

K7 katmanı, tüm HTTP isteklerinin işlendiği middleware pipeline'ı tanımlar. Bu katman, K6 (Security) bileşenlerini kullanarak istekleri doğrular ve K2 (Routing) katmanına yönlendirir.

### 1.1 Pipeline Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    K7 — MIDDLEWARE PIPELINE (35)                    │
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│  CORE (10)   │  REQUEST (8) │  RESPONSE (7)│  UTILITY (10)          │
│              │              │              │                        │
│  OriginCheck │  JsonBody    │  Compress    │  ErrorHandler          │
│  Cors        │  FormBody    │  Cache       │  Logging               │
│  RateLimiter │  Multipart   │  ETag        │  Correlation           │
│  SecHeaders  │  ContentNeg  │  Serializ    │  RequestTimer          │
│  Session     │  Validatn    │  Deprecation │  MemoryLimit           │
│  Csrf        │              │  HealthCheck │  MaxExecTime           │
│  BypassAuth  │              │              │  FileUpload            │
│  Auth        │              │              │  RateLimitHeader       │
│  Permission  │              │              │                        │
│  Validation  │              │              │                        │
└──────────────┴──────────────┴──────────────┴────────────────────────┘
```

### 1.2 Bağımlılık Haritası

```
HTTP Request
  → K7 (Middleware Pipeline)
    → K6 (Security) — auth, CSRF, CSP
    → K2 (Routing) — PageRouter dispatch
    → K4 (Application) — Controller
```

---

## 2. Immutable Pipeline Sırası (ADR-010/011/012/013/022)

**⚠️ KRİTİK:** Middleware sırası DEĞİŞTİRİLEMEZ. CSP nonce üretimi SecurityHeaders (#4) içindedir. SessionManager (#5) bu nonce'u session'a kaydeder. Sıra değiştirilirse CSP bozulur.

```
1.  OriginCheckMiddleware()      — Köken doğrulama (whitelist CORS)
2.  CorsMiddleware()             — CORS header'ları (whitelist only)
3.  RateLimiterMiddleware()      — APCu: 60 req/60s
4.  SecurityHeadersMiddleware()  — CSP nonce üret, strict-dynamic, HSTS, X-Frame
5.  SessionManagerMiddleware()   — Session başlat, CSP nonce'u session'a kaydet
6.  CsrfMiddleware()             — csrf_token doğrulama (POST/PUT/DELETE)
7.  BypassAuthMiddleware()       — Test bypass (production'da devre dışı)
8.  AuthMiddleware()             — Auth bilgisi inject (session'dan okur)
9.  PermissionMiddleware()       — RBAC yetki kontrolü (6 rol)
10. ValidationMiddleware()       — Request/DTO validasyonu
→ Controller
```

---

## 3. Core Middleware Bileşenleri (10 Bileşen)

| # | Middleware | Görev | Timeout | ADR |
|---|-----------|-------|---------|-----|
| 1 | OriginCheckMiddleware | İstek kökeni doğrulama (whitelist tabanlı) | — | — |
| 2 | CorsMiddleware | CORS header yönetimi (Access-Control-Allow-Origin) | — | [[ADR-010-csrf-protection-strategy]] |
| 3 | RateLimiterMiddleware | APCu tabanlı rate limiting (60 req/60s) | 60s | [[ADR-013-rate-limiting-apcu]] |
| 4 | SecurityHeadersMiddleware | CSP nonce üretimi, strict-dynamic, HSTS, X-Frame-Options | — | [[ADR-012-csp-nonce-strict-dynamic]] |
| 5 | SessionManagerMiddleware | Session başlatır, CSP nonce'u session'a kaydeder | 3600s idle | [[ADR-011-session-management]] |
| 6 | CsrfMiddleware | `csrf_token` doğrulama (POST/PUT/DELETE) | — | [[ADR-010-csrf-protection-strategy]] |
| 7 | BypassAuthMiddleware | Test bypass (`?_bypass=1`), prod'da devre dışı | — | [[ADR-008-bypass-auth-middleware]] |
| 8 | AuthMiddleware | Auth bilgisi inject (JWT + Session) | — | — |
| 9 | PermissionMiddleware | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | — | — |
| 10 | ValidationMiddleware | Request/DTO validasyonu | — | — |

---

## 4. Request Middleware Bileşenleri (8 Bileşen)

| # | Middleware | Görev | Teknoloji |
|---|-----------|-------|-----------|
| 11 | JsonBodyMiddleware | `application/json` gövde çözümleme | `json_decode()` |
| 12 | FormBodyMiddleware | `application/x-www-form-urlencoded` çözümleme | `parse_str()` |
| 13 | MultipartMiddleware | `multipart/form-data` çözümleme (dosya yükleme) | `$_FILES` |
| 14 | ContentNegotiation | Accept header'a göre response formatı seçimi | PSR-7 |
| 15 | SerializationMiddleware | DTO ↔ JSON/XML dönüşümleri | Symfony Serializer |
| 16 | CompressionMiddleware | gzip/br sıkıştırma (Content-Encoding) | `zlib` |
| 17 | CacheMiddleware | HTTP cache (ETag, Last-Modified, Cache-Control) | PSR-6 |
| 18 | ETagMiddleware | ETag üretimi ve If-None-Match doğrulama | `md5()` |

---

## 5. Response Middleware Bileşenleri (7 Bileşen)

| # | Middleware | Görev | Teknoloji |
|---|-----------|-------|-----------|
| 19 | DeprecationMiddleware | API deprecated endpoint uyarısı (Sunset header) | PSR-7 |
| 20 | HealthCheckMiddleware | `/health` endpoint respond (200 OK) | — |
| 21 | RateLimitHeaderMiddleware | `X-RateLimit-Remaining` header ekleme | — |
| 22 | FileUploadMiddleware | Dosya yükleme validasyonu ve depolama | Flysystem |
| 23 | MaxExecutionTimeMiddleware | Maksimum çalıştırma süresi kontrolü | `set_time_limit()` |
| 24 | MemoryLimitMiddleware | Bellek kullanımı kontrolü | `memory_get_usage()` |
| 25 | RequestTimerMiddleware | İstek süresi ölçümü ve loglama | PSR-3 |

---

## 6. Utility Middleware Bileşenleri (10 Bileşen)

| # | Middleware | Görev | Teknoloji |
|---|-----------|-------|-----------|
| 26 | ErrorHandlerMiddleware | Merkezi hata yönetimi (try-catch) | PSR-3 |
| 27 | LoggingMiddleware | İstek/yanıt loglama (PSR-3 Monolog) | Monolog |
| 28 | CorrelationMiddleware | Correlation ID üretimi ve propagation | `ramsey/uuid` |
| 29 | RequestIdMiddleware | Her istek için benzersiz ID | `uniqid()` |
| 30 | TrustProxyMiddleware | Reverse proxy arkasında gerçek IP | `$_SERVER['HTTP_X_FORWARDED_FOR']` |
| 31 | MaintenanceMiddleware | Bakım modu kontrolü (503 Service Unavailable) | Config |
| 32 | ApiVersionMiddleware | URL veya header'dan API versiyonu çıkarma | PSR-7 |
| 33 | RequestTimerMiddleware | İstek başlangıç/bitiş zamanı ölçümü | `microtime()` |
| 34 | ResponseTimeMiddleware | `X-Response-Time` header ekleme | PSR-7 |
| 35 | SecurityAuditMiddleware | Güvenlik olaylarını audit log'a yazma | PSR-3 |

---

## 7. Middleware Akış Diyagramı

### 7.1 Tam İstek Yaşam Döngüsü

```
HTTP Request
  │
  ▼
┌─────────────────────────────────────────────────┐
│  1. ErrorHandlerMiddleware (try-catch wrapper)  │
│  2. CorrelationMiddleware (X-Request-ID)        │
│  3. TrustProxyMiddleware (real IP)              │
│  4. LoggingMiddleware (request log)             │
│  5. RequestTimerMiddleware (start timer)        │
├─────────────────────────────────────────────────┤
│  6. OriginCheckMiddleware (whitelist check)     │
│  7. CorsMiddleware (CORS headers)               │
│  8. RateLimiterMiddleware (60 req/60s)          │
│  9. SecurityHeadersMiddleware (CSP nonce)       │
│  10. SessionManagerMiddleware (session start)   │
├─────────────────────────────────────────────────┤
│  11. CsrfMiddleware (POST/PUT/DELETE)           │
│  12. BypassAuthMiddleware (test only)           │
│  13. AuthMiddleware (auth inject)               │
│  14. PermissionMiddleware (RBAC)                │
│  15. ValidationMiddleware (DTO validate)        │
├─────────────────────────────────────────────────┤
│  16. ContentNegotiation (response format)       │
│  17. Controller (use case execution)            │
├─────────────────────────────────────────────────┤
│  18. SerializationMiddleware (DTO → JSON)       │
│  19. CompressionMiddleware (gzip/br)            │
│  20. CacheMiddleware (ETag/Cache-Control)       │
│  21. RateLimitHeaderMiddleware (remaining)      │
│  22. ResponseTimeMiddleware (X-Response-Time)   │
│  23. LoggingMiddleware (response log)           │
└─────────────────────────────────────────────────┘
  │
  ▼
HTTP Response
```

### 7.2 CSP Nonce Akışı (Kritik — Sıra Değişmez)

```
Request → SecurityHeadersMiddleware (#4)
  → $nonce = base64_encode(random_bytes(32))
  → CSP Header: script-src 'nonce-{$nonce}' 'strict-dynamic'
  → $request->withAttribute('csp_nonce', $nonce)

Request → SessionManagerMiddleware (#5)
  → $_SESSION['csp_nonce'] = $request->getAttribute('csp_nonce')
  → Template: <script nonce="<?php echo $_SESSION['csp_nonce']; ?>">
```

**Sıra değiştirilirse:** CSP nonce üretilmez → tüm inline script'ler bloke edilir → uygulama bozulur.

---

## 8. PSR-15 Uyumluluk

K7 middleware bileşenleri PSR-15 (HTTP Server Request Handlers) standardına uygundur:

```php
// PSR-15 Middleware Interface
interface MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}

// PSR-15 RequestHandler Interface
interface RequestHandlerInterface {
    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface;
}
```

### 8.1 Middleware Implementasyon Kalıbı

```php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // 1. Nonce üret
        $nonce = base64_encode(random_bytes(32));

        // 2. Request'e ekle
        $request = $request->withAttribute('csp_nonce', $nonce);

        // 3. Handler'ı çağır
        $response = $handler->handle($request);

        // 4. Response'a header ekle
        return $response
            ->withHeader('Content-Security-Policy', "script-src 'nonce-{$nonce}' 'strict-dynamic'")
            ->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-Content-Type-Options', 'nosniff');
    }
}
```

---

## 9. Middleware Konfigürasyonu

### 9.1 Pipeline Konfigürasyonu (Dependency Injection)

```php
// shared/src/Middleware/PipelineConfig.php
return [
    'middleware_pipeline' => [
        // Core security
        OriginCheckMiddleware::class,
        CorsMiddleware::class,
        RateLimiterMiddleware::class,
        SecurityHeadersMiddleware::class,
        SessionManagerMiddleware::class,
        CsrfMiddleware::class,
        BypassAuthMiddleware::class,
        AuthMiddleware::class,
        PermissionMiddleware::class,
        ValidationMiddleware::class,
    ],
];
```

### 9.2 Middleware Bağımlılıkları

| Middleware | Bağımlılık | Provider |
|-----------|------------|----------|
| RateLimiterMiddleware | APCu / Redis | `apcu` / `predis` |
| SessionManagerMiddleware | PHP session | native |
| CsrfMiddleware | Session | SessionManager |
| AuthMiddleware | JWT + Session | `lcobucci/jwt` |
| PermissionMiddleware | RBAC config | DB |
| ValidationMiddleware | DTO schemas | Respect Validation |
| CompressionMiddleware | zlib | PHP extension |
| CacheMiddleware | PSR-6 Cache | Symfony Cache |
| LoggingMiddleware | PSR-3 Logger | Monolog |

---

## 10. Hata Yönetimi

### 10.1 Middleware Hata Senaryoları

| Middleware | Hata Durumu | HTTP Kodu | Davranış |
|-----------|-------------|-----------|----------|
| OriginCheck | Geçersiz köken | 403 | Reddet |
| Cors | CORS ihlali | 403 | Header ekleme |
| RateLimiter | Limit aşıldı | 429 | Retry-After header |
| SecurityHeaders | — | — | Header ekleme |
| Session | Session başlatılamadı | 500 | Hata log |
| Csrf | Token eşleşmedi | 403 | Reddet |
| BypassAuth | — | — | Test ortamında bypass |
| Auth | Token geçersiz | 401 | Reddet |
| Permission | Yetki yok | 403 | Reddet |
| Validation | DTO geçersiz | 422 | Hata listesi |

### 10.2 Error Handler Middleware

```
Her middleware'de exception fırlatılabilir:
  → ErrorHandlerMiddleware (try-capper)
    → PSR-3 logger'a yaz
    → JSON error response döndür
    → stack trace (prod'da gizli)
```

---

## 11. Performance Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Middleware pipeline süresi | <5ms toplam | Request timer |
| Tek middleware süresi | <1ms | Individual timer |
| Rate limiter lookup | <1ms | APCu get |
| Session start | <2ms | File-based |
| CSRF token doğrulama | <0.5ms | hash_equals |
| Gzip sıkıştırma | <10ms | Compression ratio |

---

## 12. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| K7 Middleware | K6 Security | Auth, CSRF, CSP bileşenleri |
| K7 Middleware | K2 Routing | PageRouter dispatch |
| K7 Middleware | K4 Application | Controller çağrısı |
| K7 → PSR-15 | php-fig.org | PSR-15 standardı |
| K7 → ADR-010 | CSRF | csrf_token key |
| K7 → ADR-011 | Session | Session yönetimi |
| K7 → ADR-012 | CSP | Nonce + strict-dynamic |
| K7 → ADR-013 | Rate Limit | APCu 60 req/60s |
| K7 → brain.md §6 | Pipeline | Middleware sırası |

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | draft |
| Total Components | 35 |
| Core Middleware | 10 |
| Request Middleware | 8 |
| Response Middleware | 7 |
| Utility Middleware | 10 |
| ADR Coverage | 4 ADR referansı |
| PSR Standards | PSR-15, PSR-7, PSR-3 |
| GitHub References | PSR-15, Symfony, Nyholm |

---

## 14. Class AB Middleware Entegrasyonu

- [[electronics/power-supply-classab]] — Gu yonetimi middleware'i
- [[electronics/thermal-design-classab]] — Termal izleme middleware'i

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
