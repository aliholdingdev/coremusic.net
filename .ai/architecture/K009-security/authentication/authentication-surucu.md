---
node_id: K009-security/authentication/authentication-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication/authentication-anayasa
  - K009-security/authentication/index
evidence:
  - "shared/src/PageRouter/PageRouterKernel.php:330-341 — web sürüş sırası"
  - "api.coremusic.net/index.php:147-171 — API sürüş sırası"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php:54-108 — API kimlik sürüşü"
  - "shared/src/Middleware/BypassAuthMiddleware.php:36-65 — bypass sürüş gövdesi"
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php:54-61 — parola doğrulama sürüşü"
  - "shared/src/PageRouter/AuthGuard.php:15-24 — web guard zinciri"
validation:
  - "2026-10-15 — 13 kaynak dosya okundu; sürüş adımları Dosya:Satır ile"
  - "2026-10-15 — URL tablosu EKLENMEDİ (mimari grup, URL tahsisi yok)"
open_questions:
  - "Login uç controller'ının hata sürüşü (AuthService) bu oturumda okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "401 redinin başlık seti (WWW-Authenticate) → UNKNOWN"
  - "Bypass testinin üretimde devre dışı kaldığını doğrulayan entegrasyon testi → UNKNOWN"
group: mimari
---

# K009 · authentication · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Adım adım akış / durum geçişi / hata kolu (K009-security/authentication).

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/authentication/authentication-surucu |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | istek → kimlik doğrulama sürüşü · durum geçişleri · hata kolları · sıra · bypass · JWT · parola |
| Kapsam dışı | yetki kararı (authorization-surucu) · oturum yenileme/timeout (session-security) |
| Bağlantı | Kurallar `[[authentication-anayasa]]` (A-01..A-10), kod `[[authentication-ornekler]]` |

## §2 Sürüş Sözlüğü

| Türkçe | English | Açıklama |
|---|---|---|
| Sürüş | Drive / execution path | Bir isteğin adım adım kimlik kontrolünden geçmesi |
| Hata kolu | Error branch | Red/yönlendirme yolunun sürülmesi |
| Durum geçişi | State transition | ANONIM → AUTHENTICATED → ... |
| Kapı | Gate | Public/bypass/fail-closed kontrol noktası |
| Sıra | Ordering | Middleware sırası ve sırasızlık riski |
| Kurtarma | Recovery | 401 sonrası istemcinin yeniden denemesi |
| Kanıt | Evidence | Her adımın Dosya:Satır karşılığı |

## §3 Web Ana Sürüş Akışı (Sayfa — Happy Path)

| Adım | Eylem | Sözleşme / Kanıt |
|---|---|---|
| W-1 | İstek `PageRouter`'a girer | `shared/src/PageRouter/PageRouterKernel.php:330-341` |
| W-2 | Origin/CORS/rate-limit/security-headers çalışır | `PageRouterKernel.php:331-334` |
| W-3 | `SessionManagerMiddleware` oturumu kurar | `PageRouterKernel.php:335` |
| W-4 | `CsrfMiddleware`: state-changing ise token eşitliği (`hash_equals`) | `shared/src/Middleware/CsrfMiddleware.php:22-46` |
| W-5 | `BypassAuthMiddleware`: gate kapalıysa **dokunmadan** geçer | `shared/src/Middleware/BypassAuthMiddleware.php:36-38` |
| W-6 | `AuthMiddleware`: session alanları okunur | `shared/src/Middleware/AuthMiddleware.php:15-21` |
| W-7 | `MM_UserID` doluysa `_auth` doldurulur (userId/role/username/permissions) | `AuthMiddleware.php:23-31` |
| W-8 | `PermissionMiddleware` çalışır (yetki — §4/authorization) | `shared/src/Middleware/PermissionMiddleware.php:17-45` |
| W-9 | `ValidationMiddleware` girdiyi doğrular | `PageRouterKernel.php:340` |
| W-10 | Route guard: `AuthGuard::check()` zinciri | `shared/src/PageRouter/AuthGuard.php:15-24` |
| W-11 | Kimlik yoksa login'e yönlendirme (SPA 403 / klasik 302) | `AuthGuard.php:26-32` → `AuthUrlBuilder.php:32-40` |
| W-12 | Handler çalışır | çekirdek (`$core`) |

| Durum | Tetik | Sonraki durum |
|---|---|---|
| ANONIM | — | SESSION_OKUMA (W-6) |
| SESSION_OKUMA | `MM_UserID` null/boş | ANONIM → W-11 |
| SESSION_OKUMA | `MM_UserID` dolu | AUTHENTICATED (W-7) |
| AUTHENTICATED | yetki/rol uyumu | PASS (W-10/W-12) |

## §4 API Ana Sürüş Akışı (JSON Gateway — Happy Path)

| Adım | Eylem | Kanıt |
|---|---|---|
| P-1 | `ResponseNormalizationMiddleware` | `api.coremusic.net/index.php:148` |
| P-2 | Origin + CORS | `index.php:149-150` |
| P-3 | `RateLimitMiddleware` (429/503 kapıları) | `index.php:151` |
| P-4 | `AuthenticationMiddleware`: 404/405 ise auth'u **kesmez** | `shared/src/Api/Middleware/AuthenticationMiddleware.php:56-64` |
| P-5 | Public rota ise geç | `AuthenticationMiddleware.php:67-69` (tanım `:33-38`) |
| P-6 | Session doğrulandı mı? → `_auth_user.method = 'session'` | `AuthenticationMiddleware.php:72-80` |
| P-7 | `Bearer` başlığı varsa `JwtService::validate()` | `AuthenticationMiddleware.php:84-85` |
| P-8 | Claims geçerli → `jti` revocation kontrolü (DB) | `AuthenticationMiddleware.php:87-90` |
| P-9 | Revocation yok → `_auth_user.method = 'jwt'` | `AuthenticationMiddleware.php:91-96` |
| P-10 | Hiçbiri değilse **401** | `AuthenticationMiddleware.php:103-107` |
| P-11 | CSRF — yalnız `method === 'session'` | `api.coremusic.net/index.php:164-169` |
| P-12 | `RequestValidationMiddleware` | `index.php:170` |
| P-13 | `AuthorizationMiddleware` (yetki) | `index.php:171` |
| P-14 | Gateway → controller | `index.php:173-182` |

| Durum | Sonraki |
|---|---|
| PUBLIC → PASS | P-11 |
| SESSION_OKUMA → AUTHENTICATED(session) | P-11 |
| TOKEN_VAR → JWT_GECERLI | P-11 (jwt) |
| JWT_GECERLI → REVOKED | P-10 (401) |
| JWT_GECERLI → DEGIL | P-7/P-10 |
| KANIT_YOK | P-10 (401) |

## §5 Bypass Sürüşü (Fail-closed)

| Adım | Koşul | Sonuç | Kanıt |
|---|---|---|---|
| B-1 | `SecurityHelper::isTestBypassActive()` | `env=production` → **false** (sürüş biter, dokunulmaz) | `shared/src/Security/SecurityHelper.php:23-30` |
| B-2 | Gate açıksa | `logTestBypass` yazılır (gözlemlenebilir) | `BypassAuthMiddleware.php:39` · `SecurityHelper.php:42-55` |
| B-3 | `bypassConfig` boş (`[]`) | `$next($request)` — **kimlik enjekte edilmez** | `BypassAuthMiddleware.php:41-44` |
| B-4 | Config dolu ve `$_SESSION['MM_UserID']` boşsa | session'a uuid/role/username + `permissions=[]` yazılır | `BypassAuthMiddleware.php:49-54` |
| B-5 | Config dolu | `_auth` doldurulur (`bypass: true`) | `BypassAuthMiddleware.php:56-61` |
| B-6 | Her durumda | `$next($request)` | `BypassAuthMiddleware.php:64` |

| Hata kolu | Sürüş |
|---|---|
| `BYPASS_USER_UUID` tanımsız/boş | `loadBypassConfig()` → `[]` (fail-closed) — `BypassAuthMiddleware.php:29-31` |
| Gate açık + config yok | bypass yok (B-3) — test: `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php:41-49` |
| Gate kapalı (production) | bypass hiç denenmez — `SecurityHelper.php:25-27` |

## §6 JWT Sürüşü (Issue / Validate)

**Üretim (`issue`):**

| Adım | Eylem | Kanıt |
|---|---|---|
| J-1 | `userId === ''` → `InvalidArgumentException` | `shared/src/Security/JwtService.php:46-48` |
| J-2 | `jti = bin2hex(random_bytes(16))` | `JwtService.php:51` |
| J-3 | `exp = now + ttlSeconds` (varsayılan 3600) | `JwtService.php:52`, `:34` |
| J-4 | `iss/aud/sub/iat/nbf/exp/jti` ek claims'i **ezmez** | `JwtService.php:54-62` |
| J-5 | `JWT::encode(..., RS256)` özel anahtarla | `JwtService.php:64`, `:24` |

**Doğrulama (`validate`) — fail-closed:**

| Adım | Eylem | Sonuç | Kanıt |
|---|---|---|---|
| V-1 | `$token === ''` | `null` | `JwtService.php:85-87` |
| V-2 | `JWT::decode` (imza/exp/nbf) | hata → `catch` → `null` (ayrıntı yok) | `JwtService.php:89-94` |
| V-3 | `iss` uyuşmuyor | `null` | `JwtService.php:96-98` |
| V-4 | `aud` uyuşmuyor | `null` | `JwtService.php:99-101` |
| V-5 | `sub`/`jti` yok veya boş | `null` | `JwtService.php:102-104` |
| V-6 | Geçerli | claims dizisi | `JwtService.php:106-108` |
| V-7 | Revocation (çağıran katmanda) | `jti` geçersizse 401 | `AuthenticationMiddleware.php:87-99` |

> **Sınır:** revocation DB sorgusu `JwtService`'te **değil** — çağıran katmandadır
> (`JwtService.php:78-79` yorumu). Sürüş böylece servis sınırını korur.

## §7 Parola Doğrulama Sürüşü (Login Adımı)

| Adım | Eylem | Sonuç | Kanıt |
|---|---|---|---|
| L-1 | Ham parola `Password::create()` | `< 8` → `ValidationException` | `auth.coremusic.net/include/Domain/ValueObject/Password.php:26-33` |
| L-2 | `hashWithPepper($pepper)` | pepper `''` → `ServerException` (fail-closed) | `Password.php:38-42` |
| L-3 | `hash_hmac('sha256', raw, pepper)` | peppered değer | `Password.php:43` |
| L-4 | `password_hash(..., PASSWORD_ARGON2ID, ...)` | hash (65536/4/2) | `Password.php:44-48` |
| L-5 | `verify($hash, $pepper)` | pepper `''` → `false` | `Password.php:54-58` |
| L-6 | `password_verify($peppered, $hash)` | bool | `Password.php:59-60` |
| L-7 | Başarısızlık | **tek** hata — ayrım yok (A-03) | anayasa §3 A-03; uygulama kanıtı ⚠️ §20 U-1 |
| L-8 | Başarı | kimlik bağlamı → §3/§4 | — |

> **Sürüş dışı adım (bilgi):** login uç controller'ının hata sürüşü
> (`auth.coremusic.net/include/Service/AuthService.php`) bu oturumda **okunmadı**
> → ⚠️ VERIFICATION REQUIRED.

## §8 Hata Kolları (Failure Branches)

| # | Tetikleyici | Hata kolu | Sonuç | Kanıt |
|---|---|---|---|---|
| H-1 | Kimlik kanıtı yok (API) | 401 döndür → `$next` çağrılmaz | `UNAUTHORIZED` | `AuthenticationMiddleware.php:103-107` |
| H-2 | JWT imza/süre hata | `null` → H-1 | 401 | `JwtService.php:91-94` |
| H-3 | `jti` revocation edilmiş | 401'e düşer (yorum: fail-closed) | 401 | `AuthenticationMiddleware.php:98-99` |
| H-4 | Kimliksiz sayfa (web) | `redirectAuth` → SPA `403` / klasik `302` | yönlendirme | `AuthGuard.php:31` · `AuthUrlBuilder.php:36-39` · `RouteResult.php:43-56` |
| H-5 | CSRF eşleşmiyor | `fail()` → `403` + `halt` + log | 403 | `CsrfMiddleware.php:42-44,49-63` |
| H-6 | Rate limit aşıldı | `429` + `Retry-After` + log | 429 | `RateLimiterMiddleware.php:86-99` |
| H-7 | Rate-limit kaynağı yok + auth yolu | `503` + log | 503 | `RateLimiterMiddleware.php:53-65` |
| H-8 | Rate-limit kaynağı yok + genel yol | `next()` (fail-open) + log | geç | `RateLimiterMiddleware.php:66-68` |
| H-9 | Bypass aktif + config yok | `next()` — injection yok | geç (kimliksiz) | `BypassAuthMiddleware.php:41-44` |
| H-10 | 404/405 | auth 401 ile kesilmez → Gateway üretir | 404/405 | `AuthenticationMiddleware.php:56-64` |
| H-11 | Pepper boş (hash) | `ServerException` fırlar | 500 sınıfı | `Password.php:40-42` |
| H-12 | JWT private key okunamaz | `RuntimeException` (üretim talimatıyla) | fail | `JwtService.php:114-120` |

## §9 Durum Makinesi (Kimlik Durumları)

| # | Durum | Tetikleyici | Sonraki Durum |
|---|---|---|---|
| 1 | ANONIM | — | kanıt arama (§3 W-6 / §4 P-6) |
| 2 | SESSION_ARANIYOR | `_session` okundu | AUTHENTICATED / ANONIM |
| 3 | AUTHENTICATED | `_auth` dolu | GUARD (W-10) / API devam |
| 4 | TOKEN_ARANIYOR | `Bearer` başlığı | JWT_DOGRULANIYOR |
| 5 | JWT_DOGRULANIYOR | `validate()` | AUTHENTICATED / 401 |
| 6 | REVOKED | `jti` geçersiz | 401 (H-3) |
| 7 | BYPASS_AKTIF | gate açık + config dolu | AUTHENTICATED(bypass) |
| 8 | BYPASS_KAPALI | gate kapalı veya config boş | ANONIM (dokunulmadı) |
| 9 | RED (401/403/302) | H-1..H-5 | istemci → yeniden login |
| 10 | KOTELI (429/503) | H-6/H-7 | bekle → yeniden dene |

| Geçiş | Koşul | Kanıt |
|---|---|---|
| 1 → 3 | `MM_UserID` dolu | `AuthMiddleware.php:23` |
| 1 → 2 → 5 | session yok + `Bearer` var | `AuthenticationMiddleware.php:84` |
| 5 → 6 → 9 | `accessTokenValidator` false | `AuthenticationMiddleware.php:88-99` |
| 1 → 8 → 1 | production | `SecurityHelper.php:25-27` |
| 3 → 9 (web) | `requiresAuth` + kimliksiz | `AuthGuard.php:28-31` |

## §10 Sıra Sürüşü ve Sırasızlık Riski

| # | Sıra denetimi | Beklenen | Kanıt | Durum |
|---|---|---|---|---|
| S-1 | Web: `Bypass → Auth → Permission` | 7 → 8 → 9 | `PageRouterKernel.php:337-339` | ✅ |
| S-2 | Web: CSRF, session'dan **önce** | 6 → 5 → 7 | `PageRouterKernel.php:335-336` | ✅ |
| S-3 | API: `Authentication → ... → Authorization` | 5 → 8 | `index.php:154,171` | ✅ |
| S-4 | API: CSRF yalnız `session` metodu | `index.php:164-169` | ✅ |
| S-5 | Sıra testi | frozen order assert | `ApiPipelineSecurityTest.php:161-184` | ✅ |

| Sırasızlık Riski | Etki | Savunma |
|---|---|---|
| `Auth` yerine `Permission` öne alınırsa | `_auth` boş → yetki **sessiz pas geçilir** (`PermissionMiddleware.php:23-25`) | dondurulmuş sıra (S-1) + A-08 |
| `Bypass` sonra alınırsa | bypass `_auth`'ı Auth'ta ezilebilir | sıra testi + ADR |
| API'de `Authorization` öne alınırsa | `_auth_user` yoksa her istek 401 | sıra testi (S-5) |
| CSRF, session öncesi | token session'dan okunamaz → her istek 403 | S-2 |
| Rate limit auth sonrası | brute-force yüzeyi açık kalır | S-4/§4 P-3 |

## §11 Zamanlama (Timing) Sürüşü

| Ölçüm | Kural | Kanıt/Durum |
|---|---|---|
| Parola karşılaştırması | `password_verify` üzerinden (kütüphane içi) | `Password.php:60` — kaynak ⚠️ U-8 |
| Token/secret eşitliği | `hash_equals` | `CsrfMiddleware.php:42` · `ApiKeyRepository.php:171` |
| Başarısızlık mesajı | ayrım yok (süre + metin sızıntısız) | anayasa A-03; uygulama ⚠️ L-7/U-1 |
| Middleware süresi ölçümü | `hrtime` + `timing()` logu | `MiddlewarePipeline.php:41-49` |
| Rate limit penceresi | `windowSeconds` (varsayılan 60) + `maxRequests` (60) | `RateLimiterMiddleware.php:39-40` |
| JWT TTL | `ttlSeconds = 3600` | `JwtService.php:34` |

## §12 Rate Limit / Brute-Force Sürüşü

| Adım | Eylem | Kanıt |
|---|---|---|
| R-1 | Cache çözülür mü? | `RateLimiterMiddleware.php:52` |
| R-2 | Yoksa yol `AUTH_FAIL_CLOSED_PATHS` içinde mi? (9 yol) | `:20-30`, kontrol `:55` |
| R-3 | Evet → `503` + `Retry-After` + log | `:57-64` |
| R-4 | Hayır → `next()` (fail-open) + log | `:66-68` |
| R-5 | Cache var → sayaç (`md5(ip)` anahtarı) | `:71-79` |
| R-6 | `count > max` → `429` + log | `:86-99` |
| R-7 | Trusted proxy ise ilk `X-Forwarded-For` IP'si | `:105-127` |

## §13 CSRF Sürüşü (Kimlik İlişkisi)

| Adım | Eylem | Kanıt |
|---|---|---|
| C-1 | Method `GET/HEAD/OPTIONS` değilse state-changing | `CsrfMiddleware.php:22-27` |
| C-2 | Bypass rota listesi **boş** (kaldırıldı — A01:2021 notu) | `CsrfMiddleware.php:13-18` |
| C-3 | Session token + gönderilen token (`x-csrf-token` veya body) | `:35-40` |
| C-4 | `hash_equals` eşleşmezse → `403` + log + `halt` | `:42-44,49-63` |
| C-5 | API'de yalnız `method === 'session'` tabi | `api.coremusic.net/index.php:164-169` |

## §14 Senaryo × Sonuç Matrisi

| # | Senaryo | Beklenen Sonuç | Kural/Kanıt |
|---|---|---|---|
| D-1 | Geçerli session, web sayfası | `_auth` dolu → geç | `AuthMiddleware.php:23-31` |
| D-2 | Session yok, auth'lu sayfa | SPA 403 / klasik 302 → login | `AuthGuard.php:31` · `RouteResult.php:43-56` |
| D-3 | API, kimlik yok, private rota | 401 | `AuthenticationMiddleware.php:103-107` |
| D-4 | API, public rota (`/api/v1/auth/login`) | auth atlanır | `:67-69` + `:33-38` |
| D-5 | Bozuk/imzalanmamış Bearer | 401 | `JwtService.php:91-94` |
| D-6 | Geçerli Bearer ama `jti` iptal | 401 | `AuthenticationMiddleware.php:87-99` |
| D-7 | `env=production` + bypass açık | bypass **kapalı** | `SecurityHelper.php:25-27` |
| D-8 | `env=development` + sabitler tanımsız | bypass yok (fail-closed) | `BypassAuthMiddleware.php:29-31,41-44` |
| D-9 | Parola < 8 karakter | `ValidationException` | `Password.php:28-30` |
| D-10 | Pepper boş (hash) | `ServerException` | `Password.php:40-42` |
| D-11 | Pepper boş (verify) | `false` | `Password.php:56-58` |
| D-12 | CSRF token yok/yanlış | 403 + log | `CsrfMiddleware.php:42-44` |
| D-13 | 61. istek (60/60) | 429 + log | `RateLimiterMiddleware.php:86-99` |
| D-14 | Auth yolu + cache yok | 503 | `RateLimiterMiddleware.php:55-64` |
| D-15 | 404 yolu + API | 401 değil, 404 | `AuthenticationMiddleware.php:59-64` |
| D-16 | Bearer + public rota | geç (auth yok) | `:67-69` |
| D-17 | JWT private key dosyası yok | `RuntimeException` | `JwtService.php:114-120` |
| D-18 | `_auth` boşken API | 401 (Authorization'da da 401) | `AuthorizationMiddleware.php:43-50` |

## §15 Çağrı Zincisi (Call Chain)

| Katman | Rol | Kanıt |
|---|---|---|
| Subdomain `index.php` | Pipeline kurulumu (API) | `api.coremusic.net/index.php:147-171` |
| `PageRouterKernel::buildDefaultMiddlewares()` | Web pipeline (10) | `PageRouterKernel.php:322-342` |
| `MiddlewarePipeline::run()` | Sarmalama + timing | `MiddlewarePipeline.php:23-53` |
| `AuthMiddleware` / `BypassAuthMiddleware` | Kimlik bağlamı | `AuthMiddleware.php:9-34` · `BypassAuthMiddleware.php:36-65` |
| `JwtService` | Bearer doğrulama | `JwtService.php:83-109` |
| `Password` (VO) | Parola hash/doğrulama | `Password.php:38-61` |
| `AuthGuard` | Web rota guard'ı | `AuthGuard.php:15-24` |
| `ISessionManager` / `ApiSessionManager` | API oturum durumu | `AuthenticationMiddleware.php:72` |

## §16 Test Sürüşleri (Mevcut)

| Test | Sürüş | Kanıt |
|---|---|---|
| AuthMiddleware session aktarımı | `_auth` dolu/boş | `shared/tests/Middleware/AuthMiddlewareTest.php:32,84` |
| Bypass normal senaryolar | 11 kurulum | `BypassAuthMiddlewareTest.php:62…230` (grep) |
| Bypass fail-closed | ayrı süreçte tanımsız sabit → injection yok | `BypassAuthMiddlewareFailClosedTest.php:39-49` |
| CSRF geç/red | 16 senaryo | `CsrfMiddlewareTest.php:34…333` (grep) |
| API kimliksiz private rota | `UNAUTHORIZED` | `ApiPipelineSecurityTest.php:143-151` |
| API public rota | geç | `ApiPipelineSecurityTest.php:127-141` |
| Frozen sıra | marker sırası | `ApiPipelineSecurityTest.php:161-184` |

**Eksik sürüş testleri (açık):**

| # | Test | Etiket |
|---|---|---|
| E-1 | `env=production` → bypass kapalı | UNKNOWN (§20 U-3) |
| E-2 | Revocation edilmiş `jti` → 401 | UNKNOWN |
| E-3 | Parola hata mesajı ayrımı (L-7) | ⚠️ VERIFICATION REQUIRED (U-1) |
| E-4 | Timing (yanlış/boş parola süre eşitliği) | UNKNOWN |

## §17 Gözlem Noktaları (Instrumentation)

| # | Nokta | Ne ölçülür | Durum |
|---|---|---|---|
| 1 | Middleware sarmalama | ad + süre (ms) | ✅ `MiddlewarePipeline.php:41-49` |
| 2 | Bypass tetiklenmesi | `[TEST_BYPASS]` satırı | ✅ `SecurityHelper.php:42-55` |
| 3 | Rate limit reddi | `rate_limited` olayı | ✅ `RateLimiterMiddleware.php:88-92` |
| 4 | Rate-limit kaynak yok | `rate_limiter_unavailable` | ✅ `:57,:67` |
| 5 | CSRF reddi | `csrf_invalid` olayı | ✅ `CsrfMiddleware.php:52-55` |
| 6 | 401 başına olay | — | ❌ eklenmedi → UNKNOWN (U-5) |
| 7 | Başarısız login sayacı | — | ⚠️ VERIFICATION REQUIRED (U-1) |
| 8 | JWT doğrulama hatası sınıfı | — | ❌ (`catch` sessiz `null`) — `JwtService.php:91-94` |

## §18 Sürüş Riskleri

| # | Risk | Olasılık | Etki | Savunma |
|---|---|---|---|---|
| R-1 | Sıra değişirse yetki sessiz pas geçilir | Düşük | Yüksek | §10 + frozen test |
| R-2 | Production'da bypass açılması | Düşük | Kritik | `SecurityHelper.php:25-27` + E-1 |
| R-3 | 401 gözlemi olmaması (sızıntı tespiti) | Yüksek | Orta | §17/6 |
| R-4 | `catch → null` ile hata sınıfı kaybı | Orta | Düşük | loglanabilir → backlog |
| R-5 | API rol boşluğu (§13 anayasa) | Orta | Yüksek | authorization ADR |
| R-6 | Public rota deseninin genişlemesi | Orta | Yüksek | `PUBLIC_ROUTE_PATTERNS` denetimi (P-5) |
| R-7 | Pepper sızmaz ama `raw()` taşınırsa | Düşük | Kritik | anayasa Y-06 |

## §19 Doğrulama Adımları (Verification Drive)

| # | Adım | Yöntem | Kriter |
|---|---|---|---|
| V-1 | Sıra eşleşmesi | `PageRouterKernel.php:330-341` vs §10 | 10/10 sıralı |
| V-2 | API sıra | `index.php:147-171` vs §4 | 8/8 sıralı |
| V-3 | Fail-closed taraması | `null/empty` dönen kapılar | §7'de 10 madde |
| V-4 | 401 konumu | `AuthenticationMiddleware.php:103-107` | tek 401 kaynağı (API) |
| V-5 | Timing | `hash_equals` grep | 3 dosya (anayasa §8) |
| V-6 | Test kapsamı | `shared/tests` envanteri | 7 sürüş testi (§16) |
| V-7 | Gözlem | `securityEvent` grep | 4 olay (§17) |

## §20 Bilinmeyenler (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | Login hata sürüşü + hata metni ayrımı | ⚠️ VERIFICATION REQUIRED |
| U-2 | 401 yanıtında `WWW-Authenticate` | UNKNOWN |
| U-3 | Production bypass kapalılığını doğrulayan test | UNKNOWN |
| U-4 | OAuth2/OIDC sürüş adımları (PKCE) | ⚠️ VERIFICATION REQUIRED |
| U-5 | 401/başarısız-login metrikleri | UNKNOWN |
| U-6 | `password_verify` timing kaynağı | ⚠️ VERIFICATION REQUIRED |
| U-7 | Session timeout / yenileme sürüşü | UNKNOWN (session-security) |

## §21 Sürüş Rolleri (Ownership)

| Görev | Sorumlu |
|---|---|
| Sürüş akışının tasarımı | Backend Architect |
| Fail-closed / bypass denetimi | Security Engineer |
| Parola VO sürüşü | Backend Architect |
| Test sürüşleri | QA Engineer |
| Sıra değişikliği (ADR) | Architect + MO |
| Gözlem metrikleri | DevOps Engineer |

## § BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[authentication-anayasa]] | yukarı | kurallar buradan gelir (A-01..A-10) |
| [[authentication-kapsam]] | kardeş | kapsam sınırı |
| [[authentication-ornekler]] | kardeş | gerçek kod alıntıları |
| [[authentication-dogrulama]] | kardeş | 18 kontrol |
| [[authentication-bilesenler]] | kardeş | bileşen planı |
| [[authorization-surucu]] | sonraki | yetki kontrolü sürüşü |
| [[authorization-ornekler]] | sonraki | Permission/Authorization kodu |
| [[rate-limiting-kapsam]] | kardeş | kota politikası (§12) |
| [[session-security-kapsam]] | kardeş | oturum yaşam döngüsü |
| [[identity-master]] | tek yönlü | kimlik modeli (MFA U-4) |
| [[security-master]] | yukarı | K009 anayasası |
| [[transactions-surucu]] | iskelet | bu dosyanın sürücü şablonu |

## §22 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk yazım — 13 okunan kaynak, 7 § kanıt tablosu, URL tablosu yok |

## §23 Sürüm

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-15 |
| Durum | draft (U-1..U-7 açık) |
| Erişim | `.ai/architecture/K009-security/authentication/authentication-surucu.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
