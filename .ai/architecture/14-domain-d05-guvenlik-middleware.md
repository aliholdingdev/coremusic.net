---
title: "14-domain-d05-guvenlik-middleware — Mimari Domain Tablosu d05"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d05 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d05
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 14-domain-d05-guvenlik-middleware — Domain d05: Güvenlik & Middleware (K200–K249)

> **Kapsam:** 10 adımlı middleware pipeline (sıra DEĞİŞTİRİLEMEZ), auth (hybrid session+JWT RS256), RBAC, CSRF/CSP/rate-limit, yardımcı güvenlik sınıfları.
> **Eski dizin karşılıkları:** [[architecture/k6-guvenlik]] · [[architecture/k7-middleware]] · **Legacy K6 + K7** → bu domain.
> **Gerçeklik notu (2026-10-07):** Pipeline `shared/src/Middleware/` altında **10 middleware + MiddlewarePipeline** olarak gerçektir (ls 2026-10-07); JWT RS256 `shared/src/Security/JwtService.php` (ADR-095).

## Katman Tablosu (K200–K249 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K200 | #1 OriginCheck (köken doğrulama) | IMPLEMENTED | shared/src/Middleware/OriginCheckMiddleware.php · ADR-094 |
| K201 | #2 CORS | IMPLEMENTED | shared/src/Middleware/CorsMiddleware.php |
| K202 | #3 RateLimiter (APCu 60/60s) | IMPLEMENTED | shared/src/Middleware/RateLimiterMiddleware.php · ADR-013 |
| K203 | #4 SecurityHeaders (CSP nonce, HSTS) | IMPLEMENTED | shared/src/Middleware/SecurityHeadersMiddleware.php · ADR-012 |
| K204 | #5 SessionManager (3600s) | IMPLEMENTED | shared/src/Middleware/SessionManagerMiddleware.php · ADR-011 |
| K205 | #6 CSRF (`csrf_token`) | IMPLEMENTED | shared/src/Middleware/CsrfMiddleware.php · ADR-010 |
| K206 | #7 BypassAuth (test bypass) | IMPLEMENTED | shared/src/Middleware/BypassAuthMiddleware.php · ADR-008 |
| K207 | #8 Auth (session + JWT inject) | IMPLEMENTED | shared/src/Middleware/AuthMiddleware.php · ADR-052 |
| K208 | #9 Permission (RBAC roller) | IMPLEMENTED | shared/src/Middleware/PermissionMiddleware.php · .claude/CLAUDE.md §6 |
| K209 | #10 Validation (request/DTO) | IMPLEMENTED | shared/src/Middleware/ValidationMiddleware.php · shared/composer.json (respect/validation) |
| K210 | MiddlewarePipeline (sıra değişmez) | IMPLEMENTED | shared/src/Middleware/MiddlewarePipeline.php · .claude/CLAUDE.md §6 |
| K211 | JwtService (RS256, iss/aud fail-closed) | IMPLEMENTED | shared/src/Security/JwtService.php · ADR-095 |
| K212 | Access-token revocation (jti → user_tokens) | IMPLEMENTED | ADR-095 · .ai/log.md (P1-9, 2026-10-07) |
| K213 | SessionBootstrapper | IMPLEMENTED | shared/src/Session/SessionBootstrapper.php |
| K214 | SessionConfig / SessionLifecycle | IMPLEMENTED | shared/src/Session/SessionConfig.php · SessionLifecycle.php |
| K215 | CacheRateLimiter (APCu tabanlı) | IMPLEMENTED | shared/src/Security/CacheRateLimiter.php · ADR-013 |
| K216 | ReturnUrlPolicy (open-redirect koruması) | IMPLEMENTED | shared/src/Security/ReturnUrlPolicy.php |
| K217 | SecurityHelper | IMPLEMENTED | shared/src/Security/SecurityHelper.php |
| K218 | UuidV7 | IMPLEMENTED | shared/src/Security/UuidV7.php |
| K219 | SessionKeys + SessionInitializer | IMPLEMENTED | shared/src/Security/SessionKeys.php · shared/src/Session/SessionInitializer.php |
| K220 | Redactor (e-posta maskesi — P5) | IMPLEMENTED | shared/src/Log/Redactor.php · .ai/log.md (P5, 2026-10-07) |
| K221 | OAuth provider katmanı | IMPLEMENTED | shared/src/OAuth/ · shared/src/OAuth/Provider/ |
| K222 | API origin/CSRF genişletmesi | IMPLEMENTED | ADR-094 (.ai/.decisions/accepted/ADR-094-api-pipeline-origin-csrf.md) |
| K223 | Hybrid auth (session + JWT) | IMPLEMENTED | ADR-052 · ADR-058 · auth.coremusic.net/ |
| K224 | Public API güvenliği | IMPLEMENTED | ADR-020 · api.coremusic.net/config/cors.php |
| K225 | RBAC rol tabloları (regular…system) | IMPLEMENTED | .ai/.sql/mysql/coremusic_auth.sql |
| K226 | Security log stream (securityEvent) | IMPLEMENTED | coremusic_php_security.log (kök — 2026-10-07) |
| K227 | Şifreleme (AES-256-GCM / Argon2id) | PLANNED | .claude/CLAUDE.md §12 (iddia) · ⚠️ VERIFICATION REQUIRED (kod kanıtı doğrulanmadı) |
| K228 | Credential vault (secret saklama) | PLANNED | ADR-034 · ⚠️ VERIFICATION REQUIRED |
| K229 | OWASP compliance denetimi | PLANNED | ⚠️ VERIFICATION REQUIRED — denetim dosyası yok |
| K230 | MFA | PLANNED | ADR-059 (dosyası var; kod kanıtı ⚠️ VERIFICATION REQUIRED) |
| K231 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K232 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K233 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K234 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K235 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K236 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K237 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K238 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K239 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K240 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K241 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K242 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K243 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K244 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K245 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K246 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K247 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K248 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K249 | Rezerve — d05 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K200–K249) · IMPLEMENTED 26 · PLANNED 24 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-008 · ADR-010 · ADR-011 · ADR-012 · ADR-013 · ADR-020 · ADR-022 · ADR-034 · ADR-052 · ADR-058 · ADR-059 · ADR-094 · ADR-095 (`.ai/.decisions/accepted/`)
