---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K6 Güvenlik Layer"
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

# K6: Güvenlik Layer

**Katman:** K6 (Güvenlik)
**Kapsam:** Auth, RBAC, CSRF, CSP, RateLimit, Encryption, Vault, Audit
**Sorumlu Agent:** Security Engineer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K6 katmanı, CoreMusic'in güvenlik altyapısını içerir. OWASP Top 10:2025 uyumlu, katmanlı güvenlik mimarisi sunar.

---

## 2. Bileşen Haritası

| # | Bileşen | Amaç | ADR |
|---|---------|------|-----|
| K6-01 | Auth System | JWT + Session hybrid | ADR-043 |
| K6-02 | RBAC | Rol bazlı erişim | — |
| K6-03 | CSRF | Cross-Site Request Forgery | ADR-010 |
| K6-04 | CSP | Content Security Policy | ADR-012 |
| K6-05 | Rate Limit | APCu tabanlı | ADR-013 |
| K6-06 | Encryption | AES-256-GCM | ADR-022 |
| K6-07 | Credential Vault | Güvenli depolama | ADR-034 |
| K6-08 | Audit Trail | Günlük kaydı | — |

---

## 3. Authentication (K6-01)

### 3.1 JWT + Session Hybrid

```
Login → Validate Credentials → Create Session → Generate JWT → Set HTTPOnly Cookie
                                                                      ↓
Session Data: user_id, role, permissions, device_info
JWT Claims: sub, exp, iat, jti, role
```

### 3.2 Token Struct

```json
{
    "header": {
        "alg": "RS256",
        "typ": "JWT",
        "kid": "key-id-2026"
    },
    "payload": {
        "sub": "user-123",
        "role": "premium",
        "permissions": ["play", "download", "create_playlist"],
        "iat": 1726838400,
        "exp": 1726842000,
        "jti": "unique-token-id"
    }
}
```

---

## 4. CSRF (K6-03)

### 4.1 Token Kuralları

| Kural | Değer |
|-------|-------|
| Token key | `csrf_token` (NOT `_csrf_token`) |
| Doğrulama | `hash_equals()` (timing-safe) |
| Storage | HTTPOnly cookie |
| Yenileme | Her 3600s |

---

## 5. CSP (K6-04)

### 5.1 Policy

```
default-src 'self';
script-src 'self' 'nonce-{{NONCE}}' 'strict-dynamic';
style-src 'self' 'nonce-{{NONCE}}';
img-src 'self' data: https:;
font-src 'self';
connect-src 'self' wss://api.coremusic.net;
media-src 'self';
object-src 'none';
frame-ancestors 'none';
base-uri 'self';
form-action 'self';
```

---

## 6. Encryption (K6-06)

### 6.1 Parametreler

| Parametre | Değer |
|-----------|-------|
| Algorithm | AES-256-GCM |
| Key Size | 256-bit (32 byte) |
| IV Size | 96-bit (12 byte) |
| Tag Size | 16 byte |
| Password Hash | Argon2id |
| Argon2 Memory | 64MB |
| Argon2 Time | 4 iterations |
| Argon2 Threads | 2 |

---

## 7. Rate Limiting (K6-05)

### 7.1 Kurallar

| Kural | Değer |
|-------|-------|
| Storage | APCu |
| Window | 60 saniye |
| Max Request | 60 |
| Eşik | 80% → Warning |
| Aşım | 429 Too Many Requests |

---

## 8. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-010 | csrf_token key zorunlu |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-022 | AES-256-GCM, Argon2id |
| ADR-034 | AES-256-GCM credential vault |
| ADR-043 | Auth subdomain konsolidasyonu |

---

*K6 Güvenlik Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
