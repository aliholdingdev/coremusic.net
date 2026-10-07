---
title: "l2-routing/middleware-pipeline — Eski L2 Middleware Pipeline Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-routing
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# middleware-pipeline — L2 Middleware Pipeline (stub)

**Durum:** `architecture/l2-routing/middleware-pipeline(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

- **Kod:** `shared/src/Middleware/MiddlewarePipeline.php` + 10 middleware (sıra: `.claude/CLAUDE.md` §6)
- **Kararlar:** ADR-010 (CSRF) · ADR-011 (Session) · ADR-012 (CSP nonce) · ADR-013 (RateLimit) · ADR-022 (DB) · ADR-094 (API extension)
- **Kernel sırası:** `shared/src/PageRouter/PageRouterKernel.php`
- **Stub-with-truth ayrıntısı:** [[architecture/07-security/middleware-security]]
- **Domain tablosu:** [[architecture/14-domain-d05-guvenlik-middleware]] (K200–K210)
