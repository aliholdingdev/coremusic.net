---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K7 Middleware Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K7: Middleware Layer

**Katman:** K7 (Middleware)
**Kapsam:** OriginCheck, CORS, RateLimit, SecurityHeaders, Session, CSRF
**Sorumlu Agent:** Backend Architect
**Bileşen Sayısı:** 35

---

## 1. Genel Bakış

K7 katmanı, K6 güvenliğini ve K9 API'sini birbirine bağlayan middleware pipeline'ını içerir. **Sıra DEĞİŞTİRİLEMEZ** (ADR-010/011/012/013/022).

---

## 2. Pipeline (Sıra Değişmez)

```
1. OriginCheckMiddleware()      → Köken doğrulama (whitelist CORS)
2. CorsMiddleware()             → CORS header'ları (whitelist only)
3. RateLimiterMiddleware()      → APCu: 60 req/60s
4. SecurityHeadersMiddleware()  → CSP nonce üret, strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware()   → Session başlat, CSP nonce'u session'a kaydet
6. CsrfMiddleware()             → csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware()       → Test bypass (production'da devre dışı)
8. AuthMiddleware()             → Auth bilgisi inject (session'dan okur)
9. PermissionMiddleware()       → RBAC yetki kontrolü (regular/premium/studio/car/admin/system)
10. ValidationMiddleware()      → Request/DTO validasyonu
→ Controller
```

---

## 3. Middleware Detayları

### 3.1 OriginCheckMiddleware (#1)

```php
// Whitelist CORS origins
$allowedOrigins = [
    'https://coremusic.net',
    'https://music.coremusic.net',
    'https://admin.coremusic.net',
    'https://home.coremusic.net',
    'http://localhost:81', // Development
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (!in_array($origin, $allowedOrigins)) {
    http_response_code(403);
    exit;
}
```

### 3.2 SecurityHeadersMiddleware (#4)

```php
// CSP nonce üretimi
$nonce = base64_encode(random_bytes(32));

// CSP header
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'; style-src 'self' 'nonce-{$nonce}'; img-src 'self' data: https:; font-src 'self'; connect-src 'self' wss://api.coremusic.net; media-src 'self'; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");

// Other security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

### 3.3 SessionManagerMiddleware (#5)

```php
// Session başlat
session_start([
    'name' => 'COREMUSIC_SESS',
    'cookie_lifetime' => 3600,
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

// CSP nonce'u session'a kaydet
$_SESSION['csp_nonce'] = $nonce;
```

---

## 4. Sıra Uyumsuzluğu Riskleri

| Sıra Hatası | Sonuç |
|-------------|-------|
| SecurityHeaders → SessionManager önce | CSP nonce bozulur |
| CSRF → Auth önce | Token doğrulanamaz |
| RateLimit → SecurityHeaders sonra | Rate limit nonce alamaz |
| BypassAuth → Auth sonra | Auth bypass başarısız |

---

## 5. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-010 | csrf_token key zorunlu |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-022 | AES-256-GCM, Argon2id |

---

*K7 Middleware Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
