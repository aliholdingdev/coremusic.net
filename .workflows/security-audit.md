---
type: workflow
category: security-audit
title: "Security Audit Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Security Audit Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

OWASP Top 10:2025 uyumluluğunun sağlanmasını ve güvenlik açıklarının tespit edilmesini sağlayan iş akışı.

## 2. Adımlar

| # | Adım | Aksiyon | Sorumlu |
|---|------|---------|---------|
| 1 | OWASP Kontrolü | OWASP Top 10 kontrol listesi | [[security-engineer]] |
| 2 | Middleware | Sıra doğrulama | [[security-engineer]] |
| 3 | Şifreleme | Standart kontrolü | [[security-engineer]] |
| 4 | CSRF/CSP | Test etme | [[security-engineer]] |
| 5 | Session | Yönetim doğrulama | [[security-engineer]] |
| 6 | Credential | Vault kontrolü | [[security-engineer]] |
| 7 | Rapor | Güvenlik raporu | [[security-engineer]] |
| 8 | Düzeltme | Tespit edilen açıkları düzelt | İlgili ajan |

## 3. OWASP Top 10:2025 Kontrolleri

| # | Kategori | Kontrol |
|---|----------|---------|
| A01 | Broken Access Control | RBAC + Auth Middleware |
| A02 | Cryptographic Failures | Argon2id + AES-256-GCM |
| A03 | Injection | PDO Prepared Statement |
| A04 | Insecure Design | L0-L3 Architecture |
| A05 | Security Misconfiguration | CSP + Headers |
| A06 | Vulnerable Components | Dependency audit |
| A07 | Auth Failures | Session Management |
| A08 | Data Integrity Failures | CSRF Protection |
| A09 | Logging Failures | Audit Trail |
| A10 | SSRF | Input Validation |

## 4. Middleware Sıra Kontrolü

```text
1. SessionManagerMiddleware    → CSP nonce üretimi
2. BypassAuthMiddleware        → Test bypass (prod'da devre dışı)
3. RateLimiterMiddleware       → 60 req/60s
4. AuthMiddleware              → Auth bilgisi inject
5. SecurityHeadersMiddleware   → CSP strict-dynamic
6. CsrfMiddleware              → csrf_token doğrulama
```

**Bu sıra KATIDIR.**

## 5. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| OWASP Uyumluluğu | %100 |
| Güvenlik Açığı | 0 |
| Middleware Sırası | Doğru |
| Şifreleme | AES-256-GCM |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
