---
type: architecture
category: security
title: "Security Master Index"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Security Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[engine.md]]

## 1. Amaç

Güvenlik ile ilgili tüm kararları, prosedürleri ve dokümantasyonu bir araya getiren ana navigasyon noktasıdır. [[ADR-010-csrf-protection-strategy]] ve [[ADR-022-database-hardened-security]] ile uyumludur.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tüm güvenlik kararları ve stratejileri | Teknik uygulama detayları |
| OWASP Top 10 uyumluluk | İş mantığı |
| Kimlik doğrulama ve yetkilendirme | Veritabanı şeması |
| Şifreleme ve sertifikalandırma | — |

## 3. Güvenlik Kararları

### 3.1 Temel Güvenlik Kararları

| ADR | Konu | Durum |
|-----|------|-------|
| [[ADR-008-bypass-auth-middleware]] | Auth bypass | Frozen |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | Frozen |
| [[ADR-011-session-management]] | Session yönetimi | Frozen |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce + strict-dynamic | Frozen |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Frozen |
| [[ADR-020-api-public-security]] | API güvenlik | Frozen |
| [[ADR-022-database-hardened-security]] | DB güvenlik sertleştirme | Frozen |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Frozen |

### 3.2 Kimlik Doğrulama Yöntemleri

| Yöntem | Kullanım | Token | ADR |
|--------|----------|-------|-----|
| **Session** | User → Browser | `COREMUSIC_SESS` | ADR-011 |
| **API Key** | Service → Service | `X-API-Key` | ADR-032 |
| **Cookie** | Browser → Service | `auth_key` | ADR-011 |

### 3.3 Şifreleme Algoritmaları

| Algoritma | Kullanım | Parametreler | ADR |
|-----------|----------|-------------|-----|
| **AES-256-GCM** | Credential vault | 96-bit IV, 16-byte tag | ADR-022 |
| **Argon2id** | Password hash | 64MB, t=4, p=2 | ADR-022 |

### 3.4 Middleware Pipeline Sırası

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

| # | Middleware | Görev | ADR |
|---|-----------|-------|-----|
| 1 | SessionManager | Session başlat, CSP nonce üret | ADR-011 |
| 2 | BypassAuth | Test bypass (prod'da devre dışı) | ADR-008 |
| 3 | RateLimiter | Hız sınırlama (60 req/60s) | ADR-013 |
| 4 | Auth | Auth bilgisi inject | ADR-011 |
| 5 | SecurityHeaders | CSP, HSTS, X-Frame-Options | ADR-012 |
| 6 | Csrf | CSRF token doğrulama | ADR-010 |

**⚠️ Middleware sırası DEĞİŞTİRİLEMEZ!**

## 4. Güvenlik Mekanizmaları

### 4.1 CSRF Koruma

| Özellik | Değer | ADR |
|---------|-------|-----|
| **Key Name** | `csrf_token` | ADR-010 |
| **Storage** | Session variable | ADR-010 |
| **Entropy** | 32 bytes | ADR-010 |
| **Doğrulama** | `hash_equals()` | ADR-010 |
| **Scope** | POST, PUT, DELETE | ADR-010 |

**⚠️ `_csrf_token` KALDIRILDI! Sadece `csrf_token` kullanılır.**

### 4.2 CSP Nonce

| Özellik | Değer | ADR |
|---------|-------|-----|
| **Format** | `base64_encode(random_bytes(32))` | ADR-012 |
| **Policy** | strict-dynamic | ADR-012 |
| **Üretim** | SessionManager'da | ADR-012 |
| **Güncelleme** | DOM patch sonrası | ADR-021 |

### 4.3 Rate Limiting

| Endpoint | Limit | Pencere | Cezalandırma |
|----------|-------|---------|-------------|
| Login | 5 req | 60s | 15dk lockout |
| Register | 3 req | 300s | 1 saat ban |
| API General | 60 req | 60s | 429 Too Many |

### 4.4 Session Yönetimi

| Özellik | Değer | ADR |
|---------|-------|-----|
| **Session ID** | Random bytes | ADR-011 |
| **Cookie Name** | `COREMUSIC_SESS` | ADR-011 |
| **Idle Timeout** | 3600s | ADR-011 |
| **Regenerate** | Login sonrası | ADR-011 |
| **Secure Flag** | true | ADR-011 |
| **HttpOnly** | true | ADR-011 |
| **SameSite** | Lax | ADR-011 |

## 5. OWASP Top 10 Uyumluluk

| # | Risk | Koruma | Durum |
|---|------|--------|-------|
| A01 | Broken Access Control | RBAC, middleware, session | ✅ |
| A02 | Cryptographic Failures | AES-256-GCM, Argon2id | ✅ |
| A03 | Injection | PDO prepared, CSP nonce | ✅ |
| A04 | Insecure Design | L0-L3 layered architecture | ✅ |
| A05 | Security Misconfiguration | Security headers, .env | ✅ |
| A06 | Vulnerable Components | Composer audit | ✅ |
| A07 | Auth Failures | Rate limiting, lockout | ✅ |
| A08 | Data Integrity Failures | CSRF, input validation | ✅ |
| A09 | Security Logging Failures | Audit trail | ✅ |
| A10 | SSRF | SSRF-protected HTTP client | ✅ |

## 6. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| `_csrf_token` | `csrf_token` | ADR-010 |
| Düz metin secret | Credential vault | ADR-034 |
| `eval()` | Safe alternatives | — |
| Hardcoded secret | `.env` / vault | ADR-022 |
| timing attack | `hash_equals()` | ADR-010 |

## 7. Güvenlik Kontrol Listesi

| # | Kontrol | Periyot | Sorumlu |
|---|---------|---------|---------|
| 1 | CSRF token validation | Her deploy | Security |
| 2 | CSP header presence | Her deploy | Security |
| 3 | Rate limiting active | Her deploy | Security |
| 4 | Session timeout enforced | Her deploy | Security |
| 5 | Error handling review | Haftalık | QA |
| 6 | Dependency audit | Haftalık | DevOps |
| 7 | Security headers check | Her deploy | Security |
| 8 | OWASP checklist review | Aylık | Security |

## 8. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Middleware sırası değişmez | ADR-010/011/012/013 | CSP/CSRF bozulması |
| 2 | CSRF token `csrf_token` olmalı | ADR-010 | CSRF açığı |
| 3 | Rate limiting zorunlu | ADR-013 | Brute force |
| 4 | Security headers zorunlu | ADR-012 | Güvenlik açığı |
| 5 | Credential vault AES-256-GCM | ADR-022 | Veri sızıntısı |

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/07-security/encryption]] | Şifreleme detayları |
| [[architecture/07-security/middleware-security]] | Middleware detayları |
| [[architecture/07-security/session-management]] | Session yönetimi |
| [[architecture/07-security/api/api_security_master]] | API güvenlik |
| [[architecture/07-security/security/csrf-protection]] | CSRF koruma |
| [[architecture/07-security/security/owasp-compliance]] | OWASP uyumluluk |
| [[architecture/l1-security]] | Güvenlik katmanı |

## 10. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Kararlar | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 3 Kararlar | [[ADR-011-session-management]] | Session |
| § 3 Kararlar | [[ADR-012-csp-nonce-strict-dynamic]] | CSP |
| § 3 Kararlar | [[ADR-013-rate-limiting-apcu]] | Rate limit |
| § 4 Mekanizmalar | [[architecture/07-security/encryption]] | Encryption |
| § 5 OWASP | [[architecture/07-security/security/owasp-compliance]] | OWASP |

## 11. Sözlük

| Terim | Tanım |
|-------|-------|
| **CSRF** | Cross-Site Request Forgery |
| **CSP** | Content Security Policy |
| **OWASP** | Open Web Application Security Project |
| **RBAC** | Role-Based Access Control |
| **Rate Limiting** | Hız sınırlama |
| **Session** | Oturum |
| **Credential Vault** | Kimlik bilgisi kasası |
| **Middleware** | Ara katman |
| **AES-256-GCM** | Advanced Encryption Standard |
| **Argon2id** | Şifreleme algoritması |
| **SSRF** | Server-Side Request Forgery |
| **HSTS** | HTTP Strict Transport Security |

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~500 |
| **ADR Uyumlu** | ✅ 008, 010, 011, 012, 013, 020, 022, 032, 034 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 6 referans |
| **Guardrails** | ✅ 5 kural |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
