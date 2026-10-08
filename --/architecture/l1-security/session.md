---
title: "l1-security/session — Eski L1 Session Dokümanı (stub-with-truth)"
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

# session — L1 Session (stub)

**Durum:** `architecture/l1-security/session(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Bileşen | Dosya | Karar |
|---------|-------|-------|
| Session middleware (3600s idle) | `shared/src/Middleware/SessionManagerMiddleware.php` | ADR-011 |
| Session bootstrap | `shared/src/Session/SessionBootstrapper.php` | IMPLEMENTED |
| Session config / lifecycle / initializer | `shared/src/Session/{SessionConfig,SessionLifecycle,SessionInitializer}.php` | IMPLEMENTED |
| Session anahtar şeması | `shared/src/Security/SessionKeys.php` | IMPLEMENTED |
| Multi-tab CSRF (session-bound token) | `shared/src/Middleware/CsrfMiddleware.php` | ADR-010 |
| Login redirect session bridge | — (kod kanıtı yok) | ADR-047 · ⚠️ VERIFICATION REQUIRED |

**Domain tablosu:** [[architecture/14-domain-d05-guvenlik-middleware]] (K204, K213–K215)
