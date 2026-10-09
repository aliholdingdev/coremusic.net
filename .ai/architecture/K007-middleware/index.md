---
type: architecture
category: layer
title: "K007 — Middleware"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K007 — Middleware

## §1 Kimlik
- Katman: K007 · Alan: **A1** (K6-K7).
- Kapsam: middleware pipeline — kanonik istek sırasının yürütücüsü (`shared/src/Middleware/`).

## §2 Sorumluluk
1. Kanonik sıra (plan §4): OriginCheck+CSRF (ADR-094) → RateLimit APCu (ADR-013) → CSP nonce strict-dynamic (ADR-012) → Auth (ADR-052/095; bypass ADR-008) → PageRouter.
2. Pipeline yürütme ve kısa-devre (`MiddlewarePipeline.php`).
3. Validation denetimi (`ValidationMiddleware`) ve RBAC permission denetimi (ADR-056, `PermissionMiddleware`).
4. Güvenlik başlıkları (`SecurityHeadersMiddleware`) ve CORS (`CorsMiddleware`).
5. Sıra/katman ihlali tespitinde revert + log ERROR (plan §4).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K006 (güvenlik primitifleri) → K000 (APCu/barındırma zemini).
- **Üst (çağıran):** K009 API · K010 uygulama (HTTP isteği pipeline'a girer).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-094 | API pipeline OriginCheck + koşullu CSRF |
| ADR-013 | Rate Limiting APCu |
| ADR-012 | CSP Nonce Strict-Dynamic |
| ADR-010 / ADR-008 | CSRF Protection · Bypass Auth Middleware |
| ADR-056 / ADR-020 | RBAC permission middleware · API Public Security |

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `shared/src/Middleware/` = 11 `.php` (10 `*Middleware.php` + `MiddlewarePipeline.php`); AGENTS §25.2 "Middleware ×11 (PSR-15)".

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: PSR-15 doğrudan implement edilmiyor — 10 middleware `IMiddleware` implement eder (`shared/src/Contracts/Middleware/IMiddleware.php:5`); `Psr\Http\Server\MiddlewareInterface` grep = 0 eşleşme (2026-10-09). PSR-15 uyumu **UNKNOWN** (IMiddleware'in PSR-15'i extend edip etmediği okunmadı).
- Sayım notu: "×11" = dosya sayısı; sınıf bazında middleware 10, pipeline 1'dir.
