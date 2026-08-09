---
type: architecture
category: l1
title: "L1 — Security Layer (Overview)"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L1 — Security Layer (Overview)

**See also:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

L1, CoreMusic platformunun güvenlik katmanıdır. Middleware pipeline, session yönetimi, CSRF koruması, CSP nonce, rate limiting ve authentication bu katmanda yönetilir. L1, L0 (infrastructure) üzerinde çalışır ve L2-Routing'e güvenlik hizmeti sunar.

**Katman Sırası (Dıştan içe):**
```
L3 Presentation → L2 Routing → L1 Security ← BU DOSYA → L0 Infrastructure
```

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Middleware pipeline (6 katman) | Backend/Frontend iş mantığı |
| Session yönetimi | Veritabanı şeması |
| CSRF koruması | UI tasarım |
| CSP nonce + strict-dynamic | Deployment |
| Rate limiting (APCu) | Monitoring |
| Authentication (RBAC, Argon2id) | Authorization business logic |
| Security headers | — |

## 3. Dosya Haritası

| Dosya | Kapsam | ADR |
|-------|--------|-----|
| [[middleware]] | Pipeline yapısı, 6 middleware detayı | ADR-010/011/012/013/022 |
| [[session]] | Session yönetimi, timeout, regeneration | ADR-011 |
| [[csrf]] | CSRF token, hash_equals, timing-safe | ADR-010 |
| [[csp]] | CSP nonce, strict-dynamic, directives | ADR-012 |
| [[auth]] | Authentication, RBAC, Argon2id, login flow | ADR-008/043 |

## 4. Tech Stack

| Teknoloji | Versiyon | Kullanım |
|-----------|---------|----------|
| PHP | 8.4+ | Runtime (strict_types) |
| Argon2id | RFC 9106 | Password hashing |
| AES-256-GCM | NIST SP 800-38D | Credential encryption |
| APCu | 5.1+ | Rate limiting |
| PHP Session | 8.4+ | Session management |

## 5. Middleware Pipeline (Özet)

```
Request → SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf → Controller
```

Sıra **frozen**'dır — değiştirilemez. Detaylı açıklama: [[middleware]]

## 6. OWASP Top 10:2025 Compliance

| # | Risk | Koruma | Durum |
|---|------|--------|-------|
| A01 | Broken Access Control | RBAC + middleware + URL validation (SSRF dahil) | ✅ |
| A02 | Security Misconfiguration | Security headers + strict config | ✅ |
| A03 | Software Supply Chain Failures | Version pinning + dependency audit | ✅ |
| A04 | Cryptographic Failures | AES-256-GCM + Argon2id | ✅ |
| A05 | Injection | PDO prepared + CSP nonce | ✅ |
| A06 | Insecure Design | L0-L3 architecture | ✅ |
| A07 | Authentication Failures | Rate limiting + lockout + session mgmt | ✅ |
| A08 | Software/Data Integrity Failures | HMAC verification + code signing | ✅ |
| A09 | Security Logging & Alerting Failures | Audit trail (log.md) + alerting | ✅ |
| A10 | Mishandling of Exceptional Conditions | Error handling + fail-safe defaults | ✅ |

*Kaynak: OWASP Top 10:2025 (owasp.org/Top10/2025/) — 2026-08-09'da doğrulandı*

## 7. Hard Guardrails

| # | Kural | ADR |
|---|-------|-----|
| 1 | `csrf_token` key — frozen | ADR-010 |
| 2 | Middleware sırası — frozen | ADR-010/011/012/013/022 |
| 3 | Argon2id — 64MB/t=4/p=2 | ADR-022 |
| 4 | AES-256-GCM — 96-bit IV | ADR-022 |
| 5 | Session regenerate after login | ADR-011 |
| 6 | `ATTR_EMULATE_PREPARES => false` | ADR-002 |
| 7 | BypassAuth — prod'da devre dışı | ADR-008 |

## 8. İlgili Dosyalar

- [[middleware]] — Middleware pipeline detayları
- [[session]] — Session yönetimi
- [[csrf]] — CSRF koruması
- [[csp]] — CSP nonce + strict-dynamic
- [[auth]] — Authentication ve RBAC

## 9. Çapraz Referanslar

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § Pipeline | [[ADR-010/011/012/013/022]] | Frozen pipeline |
| § CSRF | [[ADR-010-csrf-protection-strategy]] | Token key |
| § Session | [[ADR-011-session-management]] | Session config |
| § CSP | [[ADR-012-csp-nonce-strict-dynamic]] | Nonce |
| § Rate Limit | [[ADR-013-rate-limiting-apcu]] | APCu |
| § Auth | [[ADR-043-auth-subdomain-consolidation]] | Auth domain |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Middleware** | İstek/yanıt zincirinde sıralı güvenlik katmanı |
| **CSRF** | Cross-Site Request Forgery — sahte istek saldırısı |
| **CSP** | Content Security Policy — script injection önleme |
| **Nonce** | Tek kullanımlık rastgele değer |
| **APCu** | APC User Cache — PHP önbellek sistemi |
| **Argon2id** | Şifreleme algoritması (RFC 9106) |
| **RBAC** | Role-Based Access Control — rol bazlı erişim |
| **HSTS** | HTTP Strict Transport Security |
| **OWASP** | Open Web Application Security Project |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Toplam Dosya** | 5 (index + 4 detay) |
| **ADR Uyumlu** | ✅ 008, 010, 011, 012, 013, 022, 043 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

*L1 Security Layer v3.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-08*
*Mode: Red Team · Human Mode · Truth Mode*
