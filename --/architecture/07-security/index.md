---
title: "07-security index — Eski Güvenlik Klasörü Girişi (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-security
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 07-security index — Eski Güvenlik Klasörü (stub)

**Durum:** `architecture/07-security/*` altındaki eski güvenlik dokümanları **diskte YOK**
(eski ağaç 2026-10-06 silindi — 6 link hedefi: deep-logging-system, encryption,
middleware-security, owasp-compliance, electronics-security, api/api_security_master).

## Bugünkü Karşılığı (gerçek kanıt)

| Konu | Nerede |
|------|--------|
| Güvenlik + middleware katman tablosu (K200–K249) | [[architecture/14-domain-d05-guvenlik-middleware]] |
| Middleware kodu (10 adım) | `shared/src/Middleware/` (11 PHP dosyası + pipeline) |
| Güvenlik sınıfları (JWT, rate-limit, return-url) | `shared/src/Security/` (JwtService, CacheRateLimiter, ReturnUrlPolicy, SecurityHelper, UuidV7) |
| Güvenlik kararları | `.ai/.decisions/accepted/` ADR-010/011/012/013/022/094/095 |
| Security hardening skill | `.claude/skills/security-hardening/SKILL.md` |
| Güvenlik denetim akışı | `.workflows/security-audit.md` |

Alt hedeflerin her biri bu dizinde stub olarak üretilmiştir (deep-logging-system, encryption,
middleware-security, owasp-compliance, electronics-security, api/api_security_master).
