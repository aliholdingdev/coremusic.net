---
title: "CoreMusic — shared/src/Middleware Bağlam"
type: context
folder: "shared/src/Middleware"
category: layer1-security
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Middleware — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k6-guvenlik]] · [[../../.ai/architecture/k7-middleware]]

---

## 1. Bağlam

10-adımlı middleware pipeline'ın uygulandığı kritik güvenlik katmanı. Her HTTP isteği bu zincirden geçer. **Sıra DEĞİŞTİRİLEMEZ** — CSP nonce üretimi SecurityHeaders'da, SessionManager'da kaydedilir.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 11 PHP dosyası |
| HTTP Middleware | 10 (pipeline'da aktif) |
| Interface | 1 (MiddlewareInterface) |
| ADRReferansları | ADR-010, ADR-011, ADR-012, ADR-013, ADR-022 |

### 2.1 Dosya Envanteri

| Dosya | Amaç | ADR |
|-------|------|-----|
| `MiddlewareInterface.php` | Tüm middleware'lerin implemente ettiği arayüz | — |
| `OriginCheckMiddleware.php` | CORS origin whitelist doğrulaması | ADR-012 |
| `CorsMiddleware.php` | CORS header yönetimi (whitelist only) | ADR-012 |
| `RateLimiterMiddleware.php` | APCu tabanlı rate limiting (60 req/60s) | ADR-013 |
| `SecurityHeadersMiddleware.php` | CSP nonce üretimi, HSTS, X-Frame-Options | ADR-012 |
| `SessionManagerMiddleware.php` | Session başlatır, CSP nonce'u session'a kaydeder | ADR-011 |
| `CsrfMiddleware.php` | `csrf_token` doğrulama (POST/PUT/DELETE) | ADR-010 |
| `BypassAuthMiddleware.php` | Test bypass (`?_bypass=1`), prod'da devre dışı | ADR-008 |
| `AuthMiddleware.php` | Auth bilgisi inject (JWT + Session) | ADR-011 |
| `PermissionMiddleware.php` | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | — |
| `ValidationMiddleware.php` | Request/DTO validasyonu | — |

---

## 3. Pipeline Sırası (DEĞİŞTİRİLEMEZ)

```
1. OriginCheckMiddleware() → Köken doğrulama (whitelist CORS)
2. CorsMiddleware() → CORS header'ları (whitelist only)
3. RateLimiterMiddleware() → APCu: 60 req/60s
4. SecurityHeadersMiddleware() → CSP nonce üret, strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware() → Session başlat, CSP nonce'u session'a kaydet
6. CsrfMiddleware() → csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware() → Test bypass (production'da devre dışı)
8. AuthMiddleware() → Auth bilgisi inject (session'dan okur)
9. PermissionMiddleware() → RBAC yetki kontrolü
10. ValidationMiddleware() → Request/DTO validasyonu
→ Controller
```

**⚠️ KRİTİK:** CSP nonce üretimi SecurityHeaders (#4) içindedir. SessionManager (#5) bu nonce'u session'a kaydeder. **Sıra değiştirilirse CSP bozulur.**

---

## 4. Her Middleware Detaylı

### 4.1 OriginCheckMiddleware
- **Görev:** İstek kaynağını whitelist ile doğrular
- **Yöntem:** `$_SERVER['HTTP_ORIGIN']` kontrolü
- **Whitelist:** `.env` dosyasından okunur
- **Hata:** 403 Forbidden

### 4.2 CorsMiddleware
- **Görev:** CORS header'larını ekler
- **Header'lar:** `Access-Control-Allow-Origin`, `Access-Control-Allow-Methods`, `Access-Control-Allow-Headers`
- **Kural:** Yalnızca whitelist'teki origin'ler

### 4.3 RateLimiterMiddleware
- **Görev:** IP bazlı istek sınırlaması
- **Depolama:** APCu (in-memory)
- **Limit:** 60 istek/60 saniye
- **Header:** `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- **Hata:** 429 Too Many Requests

### 4.4 SecurityHeadersMiddleware
- **Görev:** Güvenlik header'ları + CSP nonce üretimi
- **Nonce:** `base64_encode(random_bytes(32))`
- **CSP:** `strict-dynamic`, `nonce-{base64}`
- **Header'lar:** HSTS, X-Frame-Options, X-Content-Type-Options, X-XSS-Protection
- **⚠️** Nonce'u `$_SESSION['csp_nonce']`'a kaydeder

### 4.5 SessionManagerMiddleware
- **Görev:** Session başlatır, CSP nonce'u session'a kaydeder
- **Timeout:** 3600s idle (ADR-011)
- **Cookie:** HTTPOnly, Secure, SameSite=Lax
- **Session key:** `COREMUSIC_SESS`

### 4.6 CsrfMiddleware
- **Görev:** CSRF token doğrulaması
- **Token key:** `csrf_token` (NOT `_csrf_token`)
- **Doğrulama:** `hash_equals()` (timing-safe)
- **Etkilenen methodlar:** POST, PUT, DELETE

### 4.7 BypassAuthMiddleware
- **Görev:** Test ortamında auth bypass
- **Parametre:** `?_bypass=1`
- **Prod'da:** Devre dışı (ADR-008)

### 4.8 AuthMiddleware
- **Görev:** Auth bilgisi inject
- **Kaynak:** Session + JWT
- **Inject:** `$_REQUEST['_auth']` dizisi

### 4.9 PermissionMiddleware
- **Görev:** RBAC yetki kontrolü
- **Roller:** regular, premium, studio, car, admin, system
- **Kontrol:** `$_REQUEST['_auth']['role']` vs required role

### 4.10 ValidationMiddleware
- **Görev:** Request/DTO validasyonu
- **Kütüphane:** respect/validation
- **Kontrol:** DTO validasyon kuralları

---

## 5. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Tüketen | [[../../auth.coremusic.net/CLAUDE.md]] | Auth middleware kullanımı |
| Tüketen | [[../../home.coremusic.net/CLAUDE.md]] | Session middleware kullanımı |
| Referans | [[../../.ai/architecture/k6-guvenlik]] | Güvenlik mimarisi |
| Referans | [[../../.ai/architecture/k7-middleware]] | Middleware mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-010-csrf-protection-strategy]] | CSRF |
| ADR | [[../../.ai/decisions/accepted/ADR-011-session-management]] | Session |
| ADR | [[../../.ai/decisions/accepted/ADR-012-csp-nonce-strict-dynamic]] | CSP |
| ADR | [[../../.ai/decisions/accepted/ADR-013-rate-limiting-apcu]] | Rate limit |

---

## 6. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Middleware sırasını değiştirme | CSP nonce bozulur |
| 2 | CSRF token key'i değiştirme (`_csrf_token`) | ADR-010 ihlali |
| 3 | Rate limit'i devre dışı bırakma | Güvenlik açığı |
| 4 | BypassAuth'i prod'da aktif etme | Auth bypass |
| 5 | Session timeout'u kaldırma | Session hijacking |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
