---
type: architecture
category: layer
title: "K006 — Güvenlik"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K006 — Güvenlik

## §1 Kimlik
- Katman: K006 · Alan: **A1** (K6-K7 — Güvenlik, AGENTS §5).
- Kapsam: OWASP savunma derinliği — kanonik istek sırası ve kod içi savunma katmanları.

## §2 Sorumluluk
1. İstek yaşam döngüsü sırası: Origin/CSRF (ADR-094) → rate-limit (ADR-013) → CSP (ADR-012) → auth (plan §4/§5.5 — tek kaynak).
2. Kod içi savunma zinciri: PDO prepared → input validator → CSRF (`csrf_token`) → output escape → yetki (RBAC) → hata gizleme (plan §5.5).
3. Kimlik: session + JWT RS256 hybrid (ADR-052/095), MFA TOTP (ADR-059), bypass listesi (ADR-008).
4. Yetkilendirme: RBAC + permission denetimi (ADR-056), merkezi auth servisi (ADR-058), auth subdomain (ADR-043).
5. "Tek katmana güvenmek yasak" — savunma katmanları birlikte çalışır (plan §5.5).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K005 (user_tokens/session verisi — ADR-095 `jti`→user_tokens) → K000.
- **Üst (çağıran):** K007 (middleware bu primitifleri çağırır) · K009 (API ucu).

## §4 ADR Bağlantıları
ADR-008 (bypass) · ADR-010 (CSRF) · ADR-011 (session) · ADR-012 (CSP nonce) · ADR-013 (rate-limit APCu) ·
ADR-020 (API public security) · ADR-022 (DB hardened) · ADR-034 (credential vault) · ADR-043/047 (auth subdomain, bridge) ·
ADR-052 (hybrid auth) · ADR-056 (RBAC) · ADR-058 (merkezi auth) · ADR-059 (JWT+MFA) · ADR-094 (Origin+CSRF) · ADR-095 (RS256).

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `shared/src/Security/{JwtService,SecurityHelper,CacheRateLimiter,ReturnUrlPolicy,UuidV7,SessionKeys}.php` · `shared/src/Middleware/{OriginCheck,Csrf,RateLimiter,SecurityHeaders,Permission,BypassAuth}Middleware.php` · `auth.coremusic.net/include/Service/AuthService.php`.

## §6 Risk / Not
- AÇIK KONU: rate-limit'in auth öncesi/sonrası nihai sırası security-engineer review'uyla sabitlenecek (plan §5.5) — bu dosyada iki farklı sıra taşınmadı.
- ADR-091 (TemplateEngine eval kaldırımı) dosyasız, brain-only (index §4 notu) → uygulama kanıtı **UNKNOWN**.
