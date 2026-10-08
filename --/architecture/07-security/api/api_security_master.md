---
title: "api_security_master — Eski API Güvenlik Master Dokümanı (stub-with-truth)"
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

# api_security_master — API Güvenlik Master (stub)

**Durum:** `architecture/07-security/api/api_security_master` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Katman | Dosya / Kaynak |
|--------|----------------|
| Public API güvenliği kararı | `.ai/.decisions/accepted/ADR-020-api-public-security.md` |
| API origin + CSRF pipeline genişletmesi | `.ai/.decisions/accepted/ADR-094-api-pipeline-origin-csrf.md` |
| Gateway CORS | `api.coremusic.net/config/cors.php` |
| Bearer JWT doğrulama | `shared/src/Middleware/AuthMiddleware.php` · ADR-095 |
| Rate limit (429) | `shared/src/Security/CacheRateLimiter.php` · ADR-013 |
| API key üretimi | `bin/api-key-create.php` · `coremusic_auth.sql` (api_keys) |
| Rate limit / log tabloları | `.ai/.sql/mysql/coremusic_api.sql` |
| API DTO doğrulama | `shared/src/Api/Dto/` · `shared/src/Middleware/ValidationMiddleware.php` |

**İlgili:** d05 (güvenlik) + d06 (API) → [[architecture/14-domain-d05-guvenlik-middleware]] · [[architecture/15-domain-d06-servis-api]]
