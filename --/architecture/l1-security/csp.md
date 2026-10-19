---
title: "l1-security/csp — Eski L1 CSP Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-l1-security
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# csp — L1 Content Security Policy (stub)

**Durum:** `architecture/l1-security/csp(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

- **Kod:** `shared/src/Middleware/SecurityHeadersMiddleware.php` — CSP strict-dynamic + nonce, HSTS, X-Frame-Options (IMPLEMENTED)
- **Karar:** ADR-012 (`csp-nonce-strict-dynamic`) — `.ai/.decisions/accepted/`
- **Nonce zinciri:** SecurityHeaders (#4) üretir → SessionManager (#5) session'a kaydeder — sıra DEĞİŞTİRİLEMEZ (`.claude/CLAUDE.md` §6 · `.ai/CLAUDE.md` §23)
- **İlgili kenar:** OriginCheck (#1) köken doğrulama — ADR-094

**Domain tablosu:** [[architecture/14-domain-d05-guvenlik-middleware]] (K203, K223)
