---
title: "l2-routing/guard-pipeline — Eski Guard Pipeline Dokümanı (stub-with-truth)"
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

# guard-pipeline — Guard Pipeline (stub)

**Durum:** `architecture/l2-routing/guard-pipeline(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Guard | Dosya | Durum |
|-------|-------|-------|
| AuthGuard (sayfa erişim koruması) | `shared/src/PageRouter/AuthGuard.php` | IMPLEMENTED |
| Permission guard (RBAC) | `shared/src/Middleware/PermissionMiddleware.php` | IMPLEMENTED |
| CSRF guard | `shared/src/Middleware/CsrfMiddleware.php` | IMPLEMENTED |
| Return-URL policy (redirect koruması) | `shared/src/Security/ReturnUrlPolicy.php` | IMPLEMENTED |
| Router auth boundary (JS) | `assets.coremusic.net/js/router/AuthBoundaryDetector.js` | IMPLEMENTED |

**Domain tablosu:** [[architecture/14-domain-d05-guvenlik-middleware]] (K205–K208) · [[architecture/15-domain-d06-servis-api]] (K286)
