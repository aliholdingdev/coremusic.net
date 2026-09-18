---
type: index
category: security
title: "Güvenlik Katmanları (K6-K7)"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Güvenlik Katmanları (K6-K7)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    GÜVENLİK KATMANLARI                               │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ K7: MIDDLEWARE — OriginCheck • CORS • Session • CSRF        │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │ K6: GÜVENLİK — Auth • RBAC • CSP • Encryption • Audit      │   │
│  └─────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

## Dosya İndeksi

| # | Dosya | Boyut | İçerik |
|---|-------|-------|--------|
| 1 | [[k6-security]] | 16KB | Güvenlik katmanı (35 bileşen) |
| 2 | [[k7-middleware]] | 16KB | Middleware pipeline (28 bileşen) |
| 3 | [[k06-auth-layer]] | 15KB | Auth mimarisi (JWT, Session, RBAC, MFA) |
| 4 | [[k07-security-detail]] | 15KB | OWASP Top 10, middleware zinciri |

## Toplam Bileşen: 63

## OWASP Top 10 Uyumluluğu

| # | OWASP | Durum |
|---|-------|-------|
| A01 | Broken Access Control | RBAC |
| A02 | Cryptographic Failures | AES-256-GCM |
| A03 | Injection | PDO Prepared |
| A05 | Security Misconfiguration | CSP + HSTS |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
