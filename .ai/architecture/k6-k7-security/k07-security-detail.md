# K07: Güvenlik Detayı

## OWASP Top 10 Uyumluluğu

| # | OWASP | CoreMusic Karşılığı | Durum |
|---|-------|---------------------|-------|
| A01 | Broken Access Control | RBAC + Permission Middleware | ✅ |
| A02 | Cryptographic Failures | AES-256-GCM + Argon2id | ✅ |
| A03 | Injection | PDO Prepared Statement | ✅ |
| A04 | Insecure Design | Security-by-Design | ✅ |
| A05 | Security Misconfiguration | CSP + HSTS + X-Frame | ✅ |
| A06 | Vulnerable Components | Composer Audit + NPM Audit | ✅ |
| A07 | Auth Failures | JWT + Session + MFA | ✅ |
| A08 | Data Integrity | HMAC + Digital Signature | ✅ |
| A09 | Logging Failures | Audit Trail + Sentry | ✅ |
| A10 | SSRF | Origin Check + CORS | ✅ |

## Middleware Güvenlik Zinciri

```
İstek → OriginCheck → CORS → RateLimit → SecurityHeaders → Session
  │                                                      │
  ▼                                                      ▼
Csrf → BypassAuth → Auth → Permission → Validation → Controller
  │                                                      │
  ▼                                                      ▼
Response ← ErrorHandler ← Logging ← Serialization ← Response
```

## Şifreleme Standartları

| Parametre | Değer |
|-----------|-------|
| Symmetric | AES-256-GCM |
| IV | 96-bit (12 byte) |
| Tag | 16 byte |
| Key | 256-bit (32 byte) |
| Asymmetric | RSA-4096 |
| Password | Argon2id (64MB, 4 iterasyon) |
| CSP Nonce | base64_encode(random_bytes(32)) |

## API Güvenliği

```
┌─────────────────────────────────────────────────────────────────────┐
│                    API GÜVENLİK KATMANLARI                          │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ HTTPS Only   │────►│ API Gateway  │────►│ Rate Limiter │       │
│  │ TLS 1.3      │     │ (api.core.)  │     │ (60 req/60s) │       │
│  └──────────────┘     └──────────────┘     └──────────────┘       │
│                              │                     │                │
│                              ▼                     ▼                │
│                       ┌──────────────┐     ┌──────────────┐       │
│                       │ Auth Check   │     │ Input Valid. │       │
│                       │ (JWT/Session)│     │ (DTO/Schema) │       │
│                       └──────────────┘     └──────────────┘       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Güvenlik ADR'leri

| ADR | Konu | Durum |
|-----|------|-------|
| ADR-010 | CSRF Protection | Frozen |
| ADR-011 | Session Management | Frozen |
| ADR-012 | CSP Nonce | Frozen |
| ADR-013 | Rate Limiting | Frozen |
| ADR-022 | DB Hardened Security | Frozen |

## İlgili Dosyalar

- [[k6-security]] — Genel güvenlik katmanı
- [[k7-middleware]] — Middleware pipeline
- [[electronics/amplifier-classab-circuit]] — Donanım güvenlik koruması

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
