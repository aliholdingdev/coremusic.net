---
title: "middleware-security — Eski Middleware Güvenlik Dokümanı (stub-with-truth)"
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

# middleware-security — Middleware Güvenlik (stub)

**Durum:** `architecture/07-security/middleware-security` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

**10 adımlı pipeline (sıra DEĞİŞTİRİLEMEZ — `.claude/CLAUDE.md` §6):**

```text
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
→ BypassAuth → Auth → Permission → Validation → Controller
```

| Adım | Dosya | ADR |
|------|-------|-----|
| 1 OriginCheck | `shared/src/Middleware/OriginCheckMiddleware.php` | ADR-094 |
| 2 Cors | `shared/src/Middleware/CorsMiddleware.php` | — |
| 3 RateLimiter (APCu 60/60s) | `shared/src/Middleware/RateLimiterMiddleware.php` | ADR-013 |
| 4 SecurityHeaders (CSP nonce) | `shared/src/Middleware/SecurityHeadersMiddleware.php` | ADR-012 |
| 5 SessionManager | `shared/src/Middleware/SessionManagerMiddleware.php` | ADR-011 |
| 6 Csrf (`csrf_token`) | `shared/src/Middleware/CsrfMiddleware.php` | ADR-010 |
| 7 BypassAuth | `shared/src/Middleware/BypassAuthMiddleware.php` | ADR-008 |
| 8 Auth | `shared/src/Middleware/AuthMiddleware.php` | ADR-052 |
| 9 Permission (RBAC) | `shared/src/Middleware/PermissionMiddleware.php` | — |
| 10 Validation | `shared/src/Middleware/ValidationMiddleware.php` | — |

Pipeline sınıfı: `shared/src/Middleware/MiddlewarePipeline.php` · ADR-022 (DB güvenlik).
**İlgili domain:** d05 → [[architecture/14-domain-d05-guvenlik-middleware]] (K200–K210)
