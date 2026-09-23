---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Security Audit Template"
type: security-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Denetim Tarihi:** {{DATE}}
**Denetçi:** {{AUTHOR}}
**Kapsam:** OWASP Top 10:2025

---

## 1. OWASP Top 10:2025 Kontrol Listesi

| # | Risk | Durum | Not |
|---|------|-------|-----|
| A01 | Broken Access Control | ✅/⚠️/❌ | RBAC uygulanmış |
| A02 | Cryptographic Failures | ✅/⚠️/❌ | AES-256-GCM + Argon2id |
| A03 | Injection | ✅/⚠️/❌ | Prepared statement |
| A04 | Insecure Design | ✅/⚠️/❌ | Threat modeling yapılmış |
| A05 | Security Misconfiguration | ✅/⚠️/❌ | CSP nonce-based |
| A06 | Vulnerable Components | ✅/⚠️/❌ | `composer audit` |
| A07 | Auth Failures | ✅/⚠️/❌ | JWT + Session hybrid |
| A08 | Data Integrity Failures | ✅/⚠️/❌ | CSRF token |
| A09 | Logging Failures | ✅/⚠️/❌ | Structured logging |
| A10 | SSRF | ✅/⚠️/❌ | Whitelist only |

---

## 2. Middleware Pipeline Doğrulaması

| # | Middleware | Sıra | Durum |
|---|-----------|------|-------|
| 1 | OriginCheckMiddleware | 1 | ✅/❌ |
| 2 | CorsMiddleware | 2 | ✅/❌ |
| 3 | RateLimiterMiddleware | 3 | ✅/❌ |
| 4 | SecurityHeadersMiddleware | 4 | ✅/❌ |
| 5 | SessionManagerMiddleware | 5 | ✅/❌ |
| 6 | CsrfMiddleware | 6 | ✅/❌ |
| 7 | BypassAuthMiddleware | 7 | ✅/❌ |
| 8 | AuthMiddleware | 8 | ✅/❌ |
| 9 | PermissionMiddleware | 9 | ✅/❌ |
| 10 | ValidationMiddleware | 10 | ✅/❌ |

---

## 3. Güvenlik Parametreleri

| Parametre | Beklenen Değer | Gerçek Değer | Durum |
|-----------|---------------|-------------|-------|
| CSRF Token Key | `csrf_token` | {{CSRF_KEY}} | ✅/❌ |
| CSP | strict-dynamic, nonce | {{CSP_VALUE}} | ✅/❌ |
| HSTS | max-age=31536000 | {{HSTS_VALUE}} | ✅/❌ |
| Argon2id Memory | 64MB | {{ARGON2_MEMORY}} | ✅/❌ |
| Argon2id Time | 4 iterations | {{ARGON2_TIME}} | ✅/❌ |
| AES-256-GCM IV | 96-bit (12 byte) | {{AES_IV}} | ✅/❌ |
| Session Timeout | 3600s | {{SESSION_TIMEOUT}} | ✅/❌ |
| Rate Limit | 60 req/60s | {{RATE_LIMIT}} | ✅/❌ |

---

## 4. Sensitive Data Kontrolü

| Kontrol | Durum |
|---------|-------|
| Hardcoded secret kodda yok | ✅/❌ |
| `.env` dosyası gitignore'da | ✅/❌ |
| Log'da sensitive data yok | ✅/❌ |
| Error message'da stack trace yok | ✅/❌ |
| API key'ler hashlenmiş | ✅/❌ |

---

## 5. Tespit Edilen Açık Listesi

| # | Açıklama | Severity | Dosya | Öneri |
|---|----------|----------|-------|-------|
| 1 | {{FINDING_1}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_1}} | {{FIX_1}} |
| 2 | {{FINDING_2}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_2}} | {{FIX_2}} |

---

## 6. Sonuç

**Genel Durum:** ✅ GÜVENLİ / ⚠️ İYİLEŞTİRME GEREKLİ / ❌ KRİTİK AÇIK

**Öneriler:**
1. {{RECOMMENDATION_1}}
2. {{RECOMMENDATION_2}}

---

*Security Audit Template v1.0.0 — CoreMusic Security Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
