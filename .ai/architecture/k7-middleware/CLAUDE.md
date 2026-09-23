---
title: "CoreMusic — K7 Middleware CLAUDE.md"
type: layer-guide
folder: "architecture/k7-middleware"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K7 Middleware — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Pipeline sırası değişmez | CSP/CSRF bozulması |
| 2 | CSP nonce SecurityHeaders'da üretilir | CSP hatası |
| 3 | BypassAuth prod'da devre dışı | Güvenlik açığı |
| 4 | Rate limit 60 req/60s | Abuse |

## 2. Sıra Hataları

| Sıra Hatası | Sonuç |
|-------------|-------|
| SecurityHeaders → SessionManager önce | CSP nonce bozulur |
| CSRF → Auth önce | Token doğrulanamaz |
| RateLimit → SecurityHeaders sonra | Rate limit nonce alamaz |

## 3. Middleware Kullanımı

```php
// Doğru pipeline kullanımı
$app->addMiddleware(new OriginCheckMiddleware());
$app->addMiddleware(new CorsMiddleware());
$app->addMiddleware(new RateLimiterMiddleware());
$app->addMiddleware(new SecurityHeadersMiddleware());
$app->addMiddleware(new SessionManagerMiddleware());
$app->addMiddleware(new CsrfMiddleware());
$app->addMiddleware(new BypassAuthMiddleware());
$app->addMiddleware(new AuthMiddleware());
$app->addMiddleware(new PermissionMiddleware());
$app->addMiddleware(new ValidationMiddleware());
```

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-010 | csrf_token key zorunlu |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |

---

*K7 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
