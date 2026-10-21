---
title: "php-backend-standards — Middleware Pipeline"
type: reference
category: skills
version: 4.0.0
status: active
date: 2026-10-21
updated: 2026-10-21
---

# Middleware Pipeline — Derin Referans

> **Kaynaklar (disk, 2026-10-21):** `.ai/CLAUDE.md` §6 (sayfa zinciri) ·
> `api.coremusic.net/index.php:130-171` (API zinciri, ADR-020 §1.1-B.1) ·
> `shared/src/Middleware/*.php` (11 dosya) · `shared/src/Api/Middleware/*.php` (7 dosya) ·
> `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` ·
> `references/sources.md` #1 (PSR-15), #4 (PSR-7).

## §0 Kritik Ayrım — Çift Pipeline VARDIR

CoreMusic'te **tek bir middleware zinciri yoktur.** İkisi de array tabanlıdır
(PSR-15 nesne imzalı `process()` **değildir** — bkz. §4).

| | Sayfa/SPA pipeline | API pipeline |
|---|---|---|
| Tanım yeri | `.ai/CLAUDE.md` §6 (Immutable) | `api.coremusic.net/index.php` (ADR-020 §1.1-B.1) |
| Sınıf/İmza | `IMiddleware::handle(array $request, callable $next): array` | `__invoke(array $request, callable $next): array` (callable) |
| Runner | `shared/src/Middleware/MiddlewarePipeline.php` (`pipe()` / `run()`) | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` (`pipe()` / `process()`) |
| Dosyalar | `shared/src/Middleware/` (11) | `shared/src/Api/Middleware/` (7) |
| Çekirdek | `Controller` | `Gateway::dispatch(array)` |

**Kural:** Yeni bir endpoint hangi zincire giriyorsa o zincirin sırasına uyar;
iki zincirin kapılarını karıştırmak (ör. API ucuna `SessionManagerMiddleware`
eklemek) ADR-020/ADR-010 ihlalidir.

## §1 Sayfa/SPA Pipeline — Frozen 10 Kapı (DEĞİŞTİRİLEMEZ)

```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
→ BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Timeout | Dosya (disk) |
|---|-----------|-------|---------|--------------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS) | — | `OriginCheckMiddleware.php` |
| 2 | **Cors** | CORS header yönetimi | — | `CorsMiddleware.php` |
| 3 | **RateLimiter** | APCu tabanlı, **60 req/60s** | 60s | `RateLimiterMiddleware.php` |
| 4 | **SecurityHeaders** | CSP `strict-dynamic`, X-Frame-Options, HSTS | — | `SecurityHeadersMiddleware.php` |
| 5 | **SessionManager** | Session başlatır, **CSP nonce'unu session'a kaydeder** | 3600s idle | `SessionManagerMiddleware.php` |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) | — | `CsrfMiddleware.php` |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`) — **prod'da devre dışı** | — | `BypassAuthMiddleware.php` |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session) | — | `AuthMiddleware.php` |
| 9 | **Permission** | RBAC (`regular/premium/studio/car/admin/system`) | — | `PermissionMiddleware.php` |
| 10 | **Validation** | Request/DTO validasyonu | — | `ValidationMiddleware.php` |

**Neden sıra değiştirilemez (2 bağımsız mekanizma):**

1. **CSP nonce bağımlılığı** — nonce üretimi #4 `SecurityHeaders` içindedir;
   #5 `SessionManager` bunu session'a yazar. #4→#5 terslenirse nonce
   yazılmadan okunur → CSP `strict-dynamic` her istekte kırılır.
2. **Test korumalı** — `MiddlewarePipelineTest` (ADR-010 §1.4: "sıra değişirse
   CSP/CSRF bozulur") sırayı assert eder; değişiklik yalnız test + ADR
   güncellemesiyle yapılır (`shared/AGENTS.md` Zorunlu #2).

Ek nedenler: #6 CSRF, #8 Auth'tan **önce** gelmelidir ki kimliksiz istek
token'dan önce reddedilsin; #9 Permission #8'siz anlamsızdır; #10 Validation
en sonda — çünkü önce kimlik, yetki ve origin doğrulanır.

## §2 API Pipeline — ADR-020 §1.1-B.1 (8 Kapı)

`api.coremusic.net/index.php:147-171` (birebir sıra):

```
ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication
→ Csrf (yalnız session-auth) → RequestValidation → Authorization → Gateway
```

| # | Kapı | Sınıf | Kritik davranış |
|---|------|-------|-----------------|
| 1 | ResponseNormalization | `ResponseNormalizationMiddleware` | ETag / `Cache-Control` (`route['cacheable']`, `cacheTtl`) |
| 2 | OriginCheck | `OriginCheckMiddleware` (adapter ile pipe) | İzin yoksa **403**; Origin yoksa (curl/Bearer) geçer |
| 3 | Cors | `CorsMiddleware` (adapter ile pipe) | **Yalnız header yayıcıdır** — reddetmez (B-F-03) |
| 4 | RateLimit | `RateLimitMiddleware` + `CacheRateLimiter` | Aşım → `Retry-After` + **429 `RATE_LIMIT_EXCEEDED`** |
| 5 | Authentication | `AuthenticationMiddleware` | Hybrid: **session önce**, sonra RS256 Bearer + jti revocation; fail-closed **401 `UNAUTHORIZED`** |
| 6 | Csrf | `CsrfMiddleware` — **şartlı pipe** | `$_auth_user.method === 'session'` **ise** çalışır; Bearer/API-key/public uçlar muaf |
| 7 | RequestValidation | `RequestValidationMiddleware` + `Validator` | 415 / 400 / **422 + `error.fields`** |
| 8 | Authorization | `AuthorizationMiddleware` | `requiredRole` / `requiredPermission` → **403 `FORBIDDEN`** |

**Not (doküman↔kod farkı):** dosya içindeki üst yorum satırı
`ResponseNormalization → Cors → RateLimit → …` yazar; **asıl `.pipe()`
zinciri #2 olarak OriginCheck içerir** (satır 149). Kod eylemi üstündür.

### §2.1 Kapı mantığı — neden bu sıra?

- **#1 en dışta:** tüm yanıtları (hata dahil) normalize eder — 404/405/429
  gövdeleri de tek biçime girer.
- **#2 OriginCheck, #3 Cors'tan önce:** OriginCheck reddeder, Cors yalnız
  yayınlar. Terslenirse reddedilen istek CORS başlığı alır (bilgi sızıntısı).
- **#4 RateLimit, auth'dan önce:** kimlik doğrulama işleminden önce sayar —
  brute-force yüzeyi ucuzlatılır.
- **#6 CSRF, #5'ten sonra:** kimlik methodu bilinmeden "session mı?" kararı verilemez.
- **#7 Validation, #8'den önce:** yapısal olmayan istek RBAC'a tabi tutulmaz
  (gereksiz DB/rol okuması yok); ama #7 **salt-veri** doğrulamasıdır —
  yetki #8'dedir.
- **#8 en içeride:** yalnız doğru role sahip istek `Gateway`'e ulaşır.

## §3 Her Kapı İçin Başarısızlık Tablosu

| Kapı | Başarısızlık | Yanıt | Log |
|------|-------------|-------|-----|
| OriginCheck | İzin dışı Origin | 403 | origin reddi |
| Cors | — (başlık yazar) | devam | — |
| RateLimit | `isLimited()` true | 429 + `Retry-After` | throttle |
| SessionManager | session başlatılamaz | 500 | session fatal |
| Csrf | token yok/eşleşmiyor | 403 `csrf_invalid` (ADR-010 §2.2c) | "olası CSRF saldırısı" |
| BypassAuth | prod'da etkinse | **CRITICAL bulgu** | güvenlik olayı |
| Auth | kimlik yok (protected uç) | 401 | auth red |
| Permission | rol/izinsiz | 403 | yetki red |
| Validation | kural ihlali | 422 + `error.fields` | — |
| RequestValidation | JSON bozuk / tip yanlış | 400 `INVALID_JSON` / 415 `UNSUPPORTED_MEDIA_TYPE` | — |
| Authorization | rol/izin uyuşmuyor | 403 `FORBIDDEN` | RBAC red |
| Gateway | yol yok | 404 `NOT_FOUND` | — |
| Gateway | method uyuşmuyor | **405 + `Allow:`** | — |
| Gateway | yakalanmayan istisna | 500 `INTERNAL_ERROR` | `API Gateway unhandled exception` |

## §4 PSR İlişkisi — Aynılama Yapma

| PSR | Ne der | Repo durumu (disk) |
|-----|--------|--------------------|
| **PSR-15** | `MiddlewareInterface::process(ServerRequestInterface, RequestHandlerInterface): ResponseInterface`; exception middleware **ilk/en dışta** (SHOULD) | **Doğrudan kullanılmıyor:** `psr/http-server-middleware` bağımlılığı composer.json'larda **yok** (2026-10-21 ölçümü). Sıra felsefesiyle uyumlu, imza **array/callable**. `⚠️ VERIFICATION REQUIRED`: PSR-15'e geçiş kararı diskte yok |
| **PSR-7** | Değiştirilemez HTTP mesajı; `getParsedBody(): null\|array\|object`; `with*()` yeni örnek döner | **Mevcut:** `nyholm/psr7 ^1.8` + `nyholm/psr7-server ^1.1` (shared/admin/auth/api composer.json) — buna rağmen pipeline request'i **array** |
| **PSR-14** | `EventDispatcherInterface::dispatch(object)`; listener'lar **senkron**, provider sırasıyla; listener hatası yayını durdurur ve yayar | **IMPLEMENTED:** `shared/src/Events/EventDispatcher.php` + `psr/event-dispatcher ^1.0` + 12 event (9 domain + 3 integration) — **wiring PLANNED** (ADR-086) |
| **PSR-12** | `declare(strict_types=1);` (boşluksuz), 4 boşluk, 120 char yumuşak, küçük harf anahtar, görünürlük belirteci, kapanış `?>` yok | **Bağlayıcı** — her PHP dosyası |
| **PSR-3** | Logger | `psr/log ^3.0` + `shared/src/Log/` |

**Exception middleware kuralı (PSR-15):** yakalama/en dış katman ilk sarmalayıcıdır.
CoreMusic karşılığı: **#1 ResponseNormalization** (API) ve `ErrorHandler`
(`shared/src/PageRouter/ErrorHandler.php`) — istisnayı en dışta yakalar.

## §5 Yeni Middleware Ekleme — Kapı Listesi

| Adım | İş | Yasak |
|------|----|-------|
| 1 | Hangi pipeline? Sayfa (§1) mi API (§2) mi | İkisine birden eklemek |
| 2 | Sıra: `.ai/CLAUDE.md` §6 veya `index.php` `.pipe()` konumu | Sıra değişimi = ADR + test |
| 3 | Dosya: `shared/src/Middleware/` → `IMiddleware` (interface zorunlu) **veya** `shared/src/Api/Middleware/` → `__invoke` | `shared/src/` içine subdomain'e özel iş kuralı gömmek (Yasak #1) |
| 4 | Kayıt: pipeline `pipe()` + gerekirse `shared/config/` | Kayıtsız middleware çalışmaz |
| 5 | Test: `shared/tests/Middleware/` — sıra assert'i dahil | Sıra testi olmadan merge |
| 6 | Şablon: `.ai/.templates/backend/php-template.md` §6 (Guardrail #16) | Şablonsuz dosya |

`BypassAuthMiddleware` **kapsam genişletilemez** (ADR-008; `shared/AGENTS.md` Yasak #2).

## §6 Sık Yapılan 5 Hata

| # | Hata | Sonuç | Düzeltme |
|---|------|-------|----------|
| 1 | API ucuna `SessionManager`/`SecurityHeaders` eklemek | Kapı çakışması, iki CSP kaynağı | Zincir §2'de kalır; ihtiyaç varsa ADR-020 revizyonu |
| 2 | Validation'ı auth'dan önce koymak | Kimliksiz veri sızıntısı, gereksiz iş | #7/#10 en sonda |
| 3 | CSRF'yi her mutating uçta zorunlu kılmak | Bearer API client'ları 403 | ADR-010: **yalnız cookie/session auth** token ister; Bearer muaf |
| 4 | OriginCheck'i kapatmak ("test için") | fail-open → Katman 3 kırılır | `log-only rollout` (ADR-010 §2.2c #5), asla tam kapalı |
| 5 | Pipeline'a `echo`/`exit` koymak | ResponseNormalization by-pass | Her kapı `$next($request)` döndürür; erken dönüş **yalnız** `{error: ...}` gövdesiyle |

## §7 Denetim Checklist'i

- [ ] Zincir sırası `.ai/CLAUDE.md` §6 / `index.php:147-171` ile birebir mi?
- [ ] Kapı, doğru pipeline'da mı (sayfa vs API)?
- [ ] Başarısızlıkta doğru HTTP kodu + error code (§3)?
- [ ] Yeni kapı için sıra testi eklendi mi (`MiddlewarePipelineTest`)?
- [ ] `csrf_token` adı korundu mu (ADR-010 — `_csrf_token` YASAK)?
- [ ] BypassAuth kapsamı değişmedi mi (ADR-008)?
- [ ] `declare(strict_types=1);` + PSR-12 var mı?
- [ ] Sırlar/hata metinleri log'a, istemciye ham hata değil mi (CWE-209)?
