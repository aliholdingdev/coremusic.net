---
title: "CoreMusic — ADR-095: Hybrid JWT (RS256 Access Token) — Issue/Validate/Revocation"
type: "architecture-decision"
category: "security"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "accepted"
authority: "API Bearer auth artık gerçek RS256 JWT ile çalışır; oturum (session cookie) tarayıcıya, JWT API istemcisine aittir. İmza firebase/php-jwt ^7.2 ile yapılır (kendi kripto implementasyonu YOK); revocation jti → user_tokens (sha256) ile sağlanır; private.pem gitignore'lanır."
kaynak: "B-F-01 bulgusu (validateJwtToken stub'ı hep null → Bearer=401) + kullanıcı kararı 2026-10-07 ('Gerçek JWT'yi şimdi implement et') + disk kanıtı: shared/src/Security/JwtService.php, AuthenticationMiddleware.php, api AuthController/ApiAuthContainer, user_tokens DDL · şablon .ai/.templates/adr/adr-template.md"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-095: Hybrid JWT (RS256 Access Token)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-10-07 · **Ağırlık:** 1 · **İlgili ADR:** 010, 011, 020, 022, 094
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `hybrid-jwt-rs256-access-token` · **Dosya:** `ADR-095-hybrid-jwt-rs256-access-token.md`
> **Numara gerekçesi:** `accepted/` içinde en yüksek numara **ADR-094** (2026-10-07) → **ilk boş numara 095**. Frozen 001-037'ye dokunulmadı.

---

## §1 Bağlam (Context)

### §1.1 Bulgu Kanıtları (disk — 2026-10-07)

| # | Kaynak | Kanıt | Etiket |
|---|--------|-------|--------|
| 1 | `shared/src/Api/Middleware/AuthenticationMiddleware.php` (değişim öncesi) | `validateJwtToken(): null` stub — "real implementation would…" yorumu; Bearer dalı **her zaman 401** | B-F-01 |
| 2 | Anayasa `.ai/CLAUDE.md` §7 (kod) + `.ai/CLAUDE.md:198` | "hybrid JWT+session" iddiası — kod implement edilmemiş | iddia-gerçek uçurumu |
| 3 | `coremusic_auth.sql` `user_tokens` | `token_type ENUM('password_reset','email_verify','api_key','refresh','access')` — **'access' hâlihazırda var**, revocation için migration GEREKMEZ | kanıt |
| 4 | composer | JWT kütüphanesi yoktu → `firebase/php-jwt ^7.2` eklendi (shared) | kanıt |

### §1.2 Kısıtlamalar

- Kendi kripto implementasyonu yasak (imza/süre/claim doğrulaması **kanıtlanmış kütüphaneye** ait — security kritik bileşen kuralı).
- `private.pem` asla git'e girmez; `public.pem` girer. Secret'lar vault'a/log'a yazılmaz (ADR-015/022).
- Mevcut web (session) akışı ve `auth_key` bridge'i DEĞİŞMEZ — yalnız API Bearer yüzeyi.
- Revocation'sız JWT = logout sonrası TTL'e kadar geçerlilik kabul edilemez (B-F-020 sınıfı).

### §1.3 Web Araştırması

⚠️ **VERIFICATION REQUIRED** — RFC 7519/7515 iddiaları bu oturumda doğrulanmadı; uygulama, kütüphane (firebase/php-jwt ^7.2, composer.lock) ve disk koduna dayanır.

---

## §2 Karar (Decision)

### §2.1 Karar

```text
Browser  → session cookie (HttpOnly, SameSite=Lax)     → değişmez (ADR-011)
API      → POST /api/v1/auth/login|register → access_token (RS256 JWT, TTL 3600s)
API      → Authorization: Bearer <jwt> → JwtService::validate → jti revocation → _auth_user(method=jwt)
logout   → session destroy + auth_key revoke + access-token revoke (user_tokens used_at)
```

1. **`CoreMusic\Security\JwtService`** (shared): `issue()` (RS256, iss/aud/sub/iat/nbf/exp/jti) + `validate()` (imza/exp kütüphanece; iss/aud/sub/jti **elle fail-closed**).
2. **Key yönetimi:** `shared/config/jwt/{private,public}.pem`; yollar/env (`JWT_*`, `.env.example` 3 dosya); üretem: `openssl genpkey` CLI.
3. **Revocation:** jti `user_tokens`'a `token_hash=sha256(jti)`, `token_type='access'` ile yazılır; validate sonrası `isValidAccessToken(jti)` (kayıt + `used_at IS NULL` + `expires_at > NOW()`) — **0 migration**.
4. **Wiring:** issue = API `AuthController::authPayload` (login/register yanıtı `access_token/token_type/expires_in` ekler — additive sözleşme); validate = API pipeline `AuthenticationMiddleware` (optional bağımlılıklar, web'e dokunmaz).

### §2.2 Gerekçe

Anayasa §7 hybrid iddiası kodla hizalandı; fail-closed stub yerine gerçek doğrulama; mevcut tablo (`access` enum) sayesinde revocation ek maliyetsiz; web oturumu/bridge değişmediği için regresyon yüzeyi API ile sınırlı.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Neden reddedildi |
|---|-----------|------------------|
| 1 | HS256 (paylaşımlı secret) | Secret her iki tarafta da bulunur; RS256'da validate eden yalnız public key tutar — sızıntida tek yönlü risk. MEMORY.md Q&A zaten RS256 kararlı. |
| 2 | JWT'yi Redis'te tut (revocation için) | Redis PLANNED (yok); `user_tokens` zaten var ve auth akçesi aynı DB'de. |
| 3 | Stub'u sil + Bearer'ı kaldır | Kullanıcı 2026-10-07'de "gerçek JWT'yi şimdi implement et" kararını verdi; API istemcisi kimlik yolunu kaybederdi. |
| 4 | Kendi JWT kodunu yazmak | Kripto implementasyonu yasak; kanıtlanmış kütüphane şartı. |

---

## §4 Sonuçlar (Consequences)

**Olumlu:** Bearer artık çalışır ve logout ile iptal edilebilir · fail-closed validate · 0 migration · web akışı değişmedi · 9 test (imza/exp/iss/aud/tamper/eksik-claim/yabancı-imza/eksik-key).

**Olumsuz / Risk:**
- Her Bearer isteği **1 indexed SELECT** (revocation) — DB erişimi olmayan uçlarda çalıştırılmaz (yalnız API'de bağlandı).
- Contract genişlemesi (login yanıtına `access_token`) OpenAPI'a işlenmeli — ⚠️ VERIFICATION REQUIRED: OpenAPI spec dosyası diskte yok (P9).
- `user_tokens` oturum başına satır üretir; `expires_at` geçen satırların temizliği için bakım politikası gerekir (şu an PASSIVE — süre dolunca isValidAccessToken zaten false döner).
- private.pem olan makine = imzalayıcı (yalnız API login); key rotasyonu politikası bu ADR'de tanımlı değil → gelecek ADR.

**Risk:** Orta-düşük — yeni kod yüzeyi testli; mevcut session akışı dokunulmadı.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar (2026-10-07)

1. `firebase/php-jwt ^7.2` → `shared/composer.json` (+ api lock `composer update coremusic/shared-infrastructure`).
2. Keypair: `openssl genpkey` → `shared/config/jwt/private.pem` (gitignore eklendi) + `public.pem`.
3. `shared/src/Security/JwtService.php` (issue/validate) · `IUserRepository` + `UserRepository`: `saveAccessToken`/`isValidAccessToken`/`revokeAccessTokensForUser`.
4. `ApiAuthContainer::{jwtService,userRepository}` (tekil registry) · `AuthController::authPayload` issue · `AuthService::logout` access revoke.
5. `AuthenticationMiddleware` Bearer dalı: validate + revocation → `_auth_user.method='jwt'`; ölü stub silindi.
6. `.env.example` ×3: `JWT_*` anahtar adları.
7. Test: `shared/tests/Unit/Security/JwtServiceTest` (9 test, fixture keypair `tests/Fixtures/jwt/`).

### §5.2 Rollback

`AuthenticationMiddleware` Bearer dalı + `authPayload` issue bloğu + container factory'leri kaldırılır; composer değişikliği `composer remove firebase/php-jwt` ile geri alınır. Tablo verisi (`token_type='access'` satırları) zararsızdır, migration GEREKMEZ.

---

## §6 İlgili Dokümanlar

`shared/src/Security/JwtService.php` · `shared/src/Api/Middleware/AuthenticationMiddleware.php` · `api.coremusic.net/include/{Controller/AuthController,Container/ApiAuthContainer}.php` · `auth.coremusic.net/include/Service/AuthService.php` · `coremusic_auth.sql (user_tokens)` · [[ADR-010-csrf-protection-strategy]] · [[ADR-011-session-management]] · [[ADR-020-api-public-security]] · [[ADR-022-database-hardened-security]] · [[ADR-094-api-pipeline-origin-csrf]]

---

## §7 Onay

| Tarih | Karar Veren | Durum | Not |
|-------|-------------|-------|-----|
| 2026-10-07 | Bayram Ali (Vault Steward) · Claude Code (uygulama) | ACCEPTED | Refactor planı P1-9 — kullanıcı kararı: "Gerçek JWT'yi şimdi implement et" |
