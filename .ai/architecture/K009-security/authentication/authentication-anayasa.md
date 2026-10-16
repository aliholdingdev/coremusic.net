---
node_id: K009-security/authentication/authentication-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication/index
  - K009-security/authentication/authentication-kapsam
  - K009-security/security-master
evidence:
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php:38-61 — Argon2id + pepper + password_verify"
  - "shared/src/Middleware/BypassAuthMiddleware.php:23-44 — fail-closed bypass"
  - "shared/src/Security/JwtService.php:83-109 — validate() fail-closed"
  - "shared/src/Api/Middleware/AuthenticationMiddleware.php:103-107 — 401 red"
  - "shared/src/Middleware/CsrfMiddleware.php:42 — hash_equals (sabit-zamanlı)"
  - "shared/src/PageRouter/PageRouterKernel.php:330-341 — dondurulmuş pipeline sırası"
validation:
  - "2026-10-15 — 14 kaynak dosya okundu; her madde Dosya:Satır kanıtıyla"
  - "2026-10-15 — URL tablosu EKLENMEDİ (mimari grup, URL tahsisi yok)"
open_questions:
  - "MFA/2FA kanıtı yok → K0100-identitiy sorumluluğu ⚠️ VERIFICATION REQUIRED"
  - "401 + WWW-Authenticate başlığı yayını kanıtı yok → ⚠️ VERIFICATION REQUIRED"
  - "password_needs_rehash akışı repo'da görülmedi → UNKNOWN"
  - "API _auth_user içinde rol/izin alanı boş (bkz §13) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · authentication · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Bağlayıcı ilke / yasak / karar kapısı (K009-security/authentication).

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/authentication/authentication-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | kimlik kanıtı doğrulama · parola hash · fail-closed · sabit-zamanlı karşılaştırma · red semantiği (401) · sıra bağı |
| Kapsam dışı | kimlik modeli/deposu (K0100-identitiy) · yetki kararı (authorization) · oturum yaşam döngüsü (session-security) · kota politikası (rate-limiting) |
| Karar sırası | Bu dosya **bağlayıcıdır**; sürüş akışı `authentication-surucu.md`'de, gerçek kod `authentication-ornekler.md`'dedir |

## §2 Terimler (Türkçe / English)

| Türkçe | English | Tanım |
|---|---|---|
| Kimlik doğrulama | Authentication | Gelen kanıtın (session/bearer/API key) gerçek olup olmadığının kontrolü |
| Kimlik kanıtı | Credential / authenticator | Session değişkeni, JWT, API key, OAuth kodu |
| Parola hash | Password hash | Argon2id ile saklanan tek yönlü temsil |
| Peppers | Pepper (HMAC-SHA256) | Hash öncesi uygulanan gizli anahtar |
| Fail-closed | Fail-closed | Kontrol kaynağı erişilemezse **reddet** |
| Fail-open | Fail-open | Kontrol kaynağı erişilemezse **geç** — auth yüzeyinde yasak |
| Bypass | Auth bypass | Test amaçlı kimlik enjeksiyonu (ADR-008) |
| Sabit-zamanlı karşılaştırma | Constant-time comparison | `hash_equals()` — süre sızıntısını kapatan eşitlik kontrolü |
| Revocation | Token revocation | `jti`'nin user_tokens'ta geçersizleşmesi |
| Red semantiği | Failure semantics | 401 / 302 / 403 / 429 / 503 ne zaman döner |
| Doğrulanmış kimlik | Authenticated identity | `_auth` (web) veya `_auth_user` (API) bağlamı |

## §3 Bağlayıcı İlkeler (Anayasa Maddeleri)

| Madde | Kural | Yaptırım |
|---|---|---|
| A-01 | Parola **yalnız** `Password` VO üzerinden Argon2id ile hash'lenir; başka yerde `password_hash` çağrısı yapılmaz | Code review RED |
| A-02 | Parola doğrulaması **yalnız** `password_verify` ile yapılır; elle `==`/`===` karşılaştırması yasak | RED |
| A-03 | Doğrulama başarısızlığı **tek** hata olarak döner (kullanıcı adı/parola ayrımı sızdırılmaz) | BLOCK |
| A-04 | Kimlik doğrulama kaynağı (session, JWT, cache, bypass config) erişilemezse **reddet** — fail-open yasak | RED |
| A-05 | Token/secret karşılaştırmaları `hash_equals()` ile yapılır (sabit-zamanlı) | RED |
| A-06 | Kimliksiz veya geçersiz kanıt → **401** (API) · web'de login'e yönlendirme (§9) | BLOCK |
| A-07 | `production` ortamında bypass kapısı **kapalıdır**; bypass yalnız test/development'ta açılabilir | RED |
| A-08 | Kimlik doğrulama, yetki kontrolünden **önce** gelir; sıra `index.php`/`PageRouterKernel`'de dondurulmuştur | RED — ADR gerektirir |
| A-09 | Kimlik kanıtı (parola, token, cookie) log'a, vault'a, hataya **yazılmaz** | CRITICAL — revert |
| A-10 | Doğrulama hataları gözlemlenir (`securityEvent`); sessiz başarısızlık yasak | BLOCK |

## §4 Karar Kapıları (Decision Gates)

| # | Kapı | Soru | Evet | Hayır |
|---|---|---|---|---|
| K-1 | Hash kapısı | Bu kod parola üretiyor/saklıyor mu? | `Password::hashWithPepper` (Password.php:38-49) zorunlu | YASAK — A-01 |
| K-2 | Doğrulama kapısı | Karşılaştırma parola mı? | `password_verify` (Password.php:60) | `hash_equals` (A-05) |
| K-3 | Fail-closed kapısı | Kontrol kaynağı yok/muşluk mu? | Reddet (A-04) | Geç — ama log (A-10) |
| K-4 | Bypass kapısı | `app.env === 'production'` mı? | Bypass **kapalı** (SecurityHelper.php:25-27) | Test gate kontrolü (SecurityHelper.php:28-29) |
| K-5 | Public rota kapısı | Rota public mi? (`_route['public']` veya `PUBLIC_ROUTE_PATTERNS`) | Auth atlanır (AuthenticationMiddleware.php:67-69) | Tam doğrulama |
| K-6 | Red kapısı | Kimlik yok/mu? | 401 (API) / login redirect (web) | Yetki sorusu → authorization |
| K-7 | Sıra kapısı | Pipeline'a yeni middleware ekleniyor mu? | Dondurulmuş sıra korunur (§12) | ADR-020/ADR-010 gerekli |

## §5 Doğrulama Sorusu Seti (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Yeni bir kimlik doğrulama yolu eklendi mi? | §3 A-04/A-06 test edilmeli | — |
| 2 | Karşılaştırma yapıldı mı? | `password_verify` / `hash_equals` (A-02/A-05) | RED |
| 3 | Hata mesajı ayrıştırıyor mu? | Kullanıcı adı mı parola mı ayrımı → BLOCK (A-03) | Devam |
| 4 | Kontrol kaynağı `null`/boş dönebiliyor mu? | fail-closed yazılmış mı? (A-04) | BLOCK |
| 5 | `production`'da açılabilir bir bayrak mı? | SecurityHelper gate + log (A-07/A-10) | Devam |
| 6 | Doğrulama yetki kontrolünden sonra mı? | Sıra ihlali → §12 (A-08) | Devam |
| 7 | Hata/log'a kanıt sızıyor mu? | RED (A-09) | Devam |

## §6 Parola Hash Zorunluluğu (Madde Detayı)

| Kural | Kanıt (Dosya:Satır) | Durum |
|---|---|---|
| Min uzunluk 8 | `auth.coremusic.net/include/Domain/ValueObject/Password.php:14` (`MIN_PASSWORD_LENGTH = 8`) + `:26-33` (`create()` ValidationException) | ✅ KANITLI |
| Pepper yoksa hash **üretilmez** (fail-closed) | `Password.php:40-42` → `ServerException::configError('APP_PEPPER')` | ✅ KANITLI |
| HMAC-SHA256 pepper | `Password.php:43` (`hash_hmac('sha256', $this->raw, $pepper)`) | ✅ KANITLI |
| Argon2id parametreleri | `Password.php:44-48` — `memory_cost 65536 / time_cost 4 / threads 2` | ✅ KANITLI |
| Doğrulama = `password_verify` | `Password.php:60` | ✅ KANITLI |
| Pepper yoksa doğrulama **false** (fail-closed) | `Password.php:56-58` | ✅ KANITLI |
| Ham parola entity/DB'ye gitmez | `Password.php:8-9` (dosya docblock: "Ham şifre asla entity'de saklanmaz") + `Password.php:63-66` (`raw()` yalnız VO içi) | ✅ KANITLI (doküman kanıtı) |
| VO dışında hash üretimi yasak | `auth.coremusic.net/AGENTS.md §4 Zorunlu 3` (kural metni) | ✅ KURAL |
| Min uzunluk 8 vs OWASP 12+ önerisi | `Password.php:14` (8) — çelişki kayıtlı → politika kararı `policies` dalında | ⚠️ açık karar |
| `password_needs_rehash` ile parametre yükseltme | repo taramasında **görülmedi** | UNKNOWN |

| Yasak | Sınıf |
|---|---|
| Y-01 | MD5/SHA-1 ile parola hash (açık kırılabilir) | parola güvenliği |
| Y-02 | Parolayı düz (plaintext) veya base64 saklamak | parola güvenliği |
| Y-03 | Elle `===` ile parola karşılaştırması | timing + mantık |
| Y-04 | Parola/pepper/token'ı log'a veya hataya yazmak | sızıntı (A-09) |
| Y-05 | Aynı hash'i iki farklı pepper ile doğrulamak | fail-closed ihlali |
| Y-06 | `raw()` değerini API yanıtına/DTO'ya taşımak | sızıntı |

## §7 Fail-closed Maddeleri (Kaynak Erşilemezse Reddet)

| # | Yüzey | Fail-closed davranışı | Kanıt (Dosya:Satır) |
|---|---|---|---|
| F-1 | Bypass credential eksik | `loadBypassConfig()` `[]` döner → bypass yapılmaz | `shared/src/Middleware/BypassAuthMiddleware.php:23-34` (boş kontrol `:29-31`) |
| F-2 | Bypass aktif + config boş | `$next()` ile **kimlik enjekte edilmez** | `BypassAuthMiddleware.php:41-44` |
| F-3 | Production bypass gate | `app.env === 'production'` → `false` (bypass kapalı) | `shared/src/Security/SecurityHelper.php:25-27` |
| F-4 | JWT doğrulama hatası | `catch` → `null` (ayrıntı sızdırılmaz) | `shared/src/Security/JwtService.php:89-94` |
| F-5 | JWT `iss`/`aud`/`sub`/`jti` uyuşmazlığı | `null` → 401 | `JwtService.php:96-104` |
| F-6 | Bearer reddedilmiş `jti` (revocation) | 401'e düşer | `shared/src/Api/Middleware/AuthenticationMiddleware.php:88-99` |
| F-7 | Kimliksiz API isteği | `401 UNAUTHORIZED` | `AuthenticationMiddleware.php:103-107` |
| F-8 | Rate-limit cache çözülemezse (auth uçları) | `503 rate_limiter_unavailable` + `Retry-After` | `shared/src/Middleware/RateLimiterMiddleware.php:53-65` + yol listesi `:20-30` |
| F-9 | CSRF token eşleşmezse | `403 csrf_invalid` + `halt` | `shared/src/Middleware/CsrfMiddleware.php:42-44, 49-63` |
| F-10 | Pepper boş (hash) | Exception — hash üretilmez | `Password.php:40-42` |

> **Fail-open yalnız genel uçlarda ve ADR-013 ile serbesttir:** cache çözülemezse
> genel yollar `next()` ile geçer ama loglanır — `RateLimiterMiddleware.php:66-68`.

## §8 Sabit-Zamanlı Karşılaştırma (Timing-Safe)

| # | Yüzey | Yöntem | Kanıt (Dosya:Satır) |
|---|---|---|---|
| T-1 | CSRF token eşitliği | `hash_equals($sessionToken, (string)$submitted)` | `CsrfMiddleware.php:42` |
| T-2 | API key hash eşitliği | `hash_equals($stored, $rawHash)` | `shared/src/Repository/ApiKeyRepository.php:171` (grep kanıtı; ön yorum `:22`) |
| T-3 | Tema modu token eşitliği | `hash_equals($valid, $lower)` ×2 | `shared/src/Theme/ThemeManager.php:253, :278` (grep kanıtı — ADR-010 notu `:239`) |
| T-4 | Parola karşılaştırması | `password_verify()` (kütüphane içi sabit-zamanlı davranış) | `Password.php:60` — **kütüphane düzeyi iddia**: ⚠️ VERIFICATION REQUIRED (bu dosyada web kaynağı okunmadı) |
| T-5 | JWT imza eşitliği | kütüphane (`firebase/php-jwt` `JWT::decode`) | `JwtService.php:90` — kütüphane sınırı (zero-hallucination: davranış iddiası ⚠️) |

| Yasak | Neden |
|---|---|
| `==` / `===` ile secret/token/parola karşılaştırması | Zamanlama sızıntısı + tip juggling |
| `str_replace`/`strlen` ile ön eleme (parola, token) | Süre kanalı açar |
| Hata mesajını karşılaştırılan değere göre ayırmak | Yanlışlık bilgisi sızıntısı (A-03) |

## §9 Red Semantiği (Ne Zaman 401 / 302 / 403 / 429 / 503)

| Durum | Web (sayfa) | API (JSON) | Kanıt |
|---|---|---|---|
| Kimlik kanıtı yok (rota auth ister) | Login'e yönlendirme: SPA → `403`, klasik → `302` | `401 UNAUTHORIZED` | `shared/src/PageRouter/AuthUrlBuilder.php:32-40` · `RouteResult.php:43-46,53-56` · `AuthenticationMiddleware.php:103-107` |
| Kanıt geçersiz (imza/süre/revocation) | Login'e yönlendirme (aynı kapı) | `401` | `JwtService.php:83-109` → `AuthenticationMiddleware.php:103-107` |
| Kimlik doğru, yetki yetersiz | **Bu dosyanın konusu değil** → authorization: `403` | `403 FORBIDDEN` | `shared/src/Api/Middleware/AuthorizationMiddleware.php:61-84` |
| Rate limit aşıldı | `429` + `Retry-After` | `429` + `Retry-After` | `RateLimiterMiddleware.php:93-99` |
| Rate-limit kaynağı yok (auth yolu) | `503` + `Retry-After` | `503` | `RateLimiterMiddleware.php:58-64` |
| CSRF başarısız | `403` + `halt` | `403` (yalnız `method === 'session'` — index.php:164-169) | `CsrfMiddleware.php:57-63` |
| Bilinmeyen yol / method | router akışı | `404` / `405` — auth **401 ile kesmez** | `AuthenticationMiddleware.php:56-64` |

> **Sınır notu:** "kimlik doğrulanmadı" ile "yetkisiz" **karıştırılmaz** (A-06).
> Web'de 401 başlığı yerine yönlendirme kullanılır; API'de `WWW-Authenticate`
> başlığı yayını kanıtı **yok** → ⚠️ VERIFICATION REQUIRED.

## §10 Kimlik Kanıtı Türleri (Kapsam İçi Yüzeyler)

| # | Kanıt türü | Doğrulayıcı | Kanıt (Dosya:Satır) | Durum |
|---|---|---|---|---|
| C-1 | Session (tarayıcı) | `AuthMiddleware` session → `_auth` | `shared/src/Middleware/AuthMiddleware.php:15-31` | ✅ KANITLI |
| C-2 | Session (API) | `ISessionManager::isAuthenticated()` | `AuthenticationMiddleware.php:72-80` | ✅ KANITLI |
| C-3 | Bearer JWT (RS256) | `JwtService::validate()` + `jti` revocation | `JwtService.php:24,83-109` · `AuthenticationMiddleware.php:84-100` | ✅ KANITLI |
| C-4 | API key | `hash_equals` (repo) | `ApiKeyRepository.php:171` (grep) | ✅ KANITLI |
| C-5 | Bypass credential | `BypassAuthMiddleware` fail-closed | `BypassAuthMiddleware.php:23-61` | ✅ KANITLI (riskli yüzey — §11) |
| C-6 | OAuth2/OIDC kodu | `OAuthManager` | shared envanteri (`shared/AGENTS.md §2`) — bu oturumda **okunmadı** | ⚠️ VERIFICATION REQUIRED |
| C-7 | MFA/2FA faktörü | — | kanıt yok | UNKNOWN (K0100-identitiy) |

## §11 Yasaklar (Forbidden — authentication)

| # | Yasak | Sınıf |
|---|---|---|
| Y-10 | `production`'da bypass açılabilir hale getirmek | kimlik sahteciliği |
| Y-11 | Bypass kapsamını ADR'siz genişletmek (kapsam değişimi) | ADR-008 |
| Y-12 | Doğrulama kontrolünü `try/catch` ile yutup geçmek | fail-open |
| Y-13 | Public rota listesini kod içi string ile büyütüp `public` işaretini atlamak | erişim |
| Y-14 | Aynı istekte auth → authz sırasını değiştirmek | §12 |
| Y-15 | 401/403/302 durumunu tek "hata" kodunda birleştirmek | yanlış semantik (A-06) |
| Y-16 | Rate limit fail-closed yolunu `fail-open` yapmak | ADR-013 ihlali |
| Y-17 | Token/parola değerini `error`/`message` alanına koymak | sızıntı (A-09) |

## §12 Sıra Bağlayıcılığı (Pipeline Order)

**Web (sayfa) — dondurulmuş sıra** (`shared/src/PageRouter/PageRouterKernel.php:330-341`):

| # | Middleware | Sıra | Not |
|---|---|---|---|
| 1 | `OriginCheckMiddleware` | :331 | origin allowlist |
| 2 | `CorsMiddleware` | :332 | header yayımı |
| 3 | `RateLimiterMiddleware` | :333 | 429/503 (§9) |
| 4 | `SecurityHeadersMiddleware` | :334 | başlıklar |
| 5 | `SessionManagerMiddleware` | :335 | oturum kurulumu |
| 6 | `CsrfMiddleware` | :336 | state-changing token |
| 7 | `BypassAuthMiddleware` | :337 | fail-closed (F-1/F-2) |
| 8 | `AuthMiddleware` | :338 | session → `_auth` |
| 9 | `PermissionMiddleware` | :339 | **yetki** (authorization dosyası) |
| 10 | `ValidationMiddleware` | :340 | girdi doğrulama |

**API — dondurulmuş sıra** (`api.coremusic.net/index.php:147-171`):

| # | Pipe | Satır |
|---|---|---|
| 1 | `ResponseNormalizationMiddleware` | :148 |
| 2 | Origin check | :149 |
| 3 | CORS | :150 |
| 4 | `RateLimitMiddleware` | :151 |
| 5 | `AuthenticationMiddleware` (+ revocation) | :154-159 |
| 6 | CSRF — **yalnız** `method === 'session'` | :164-169 |
| 7 | `RequestValidationMiddleware` | :170 |
| 8 | `AuthorizationMiddleware` | :171 |

**Sıra testi:** `shared/tests/Api/ApiPipelineSecurityTest.php:161-184` —
`testIndexRegistersPipelineInFrozenOrder` sıra işaretlerini sırayla doğrular
(`assertGreaterThan($last, $position)`).

**Sıra kuralı:** 7 → 8 → 9 (`Bypass → Auth → Permission`) **değiştirilemez**;
değişiklik ADR + MO onayı gerektirir (`api.coremusic.net/AGENTS.md §4/4`).

## §13 Kayıtlı Zayıf Nokta (API rol/izin boşluğu)

| İddia | Kanıt | Durum |
|---|---|---|
| `AuthenticationMiddleware` `_auth_user`'a yalnız `id/authenticated/method` yazar | `AuthenticationMiddleware.php:74-78` (session) ve `:91-95` (jwt) | ✅ KANITLI |
| `AuthorizationMiddleware` `$authUser['roles']` / `$authUser['permissions']` okur | `AuthorizationMiddleware.php:63, :76` | ✅ KANITLI |
| `_auth_user` içinde `roles`/`permissions` **hiçbir yerde yazılmıyor** | repo taraması: `_auth_user` atamaları yalnız AuthenticationMiddleware.php:74, :91 | ✅ KANITLI (negatif kanıt) |
| Üretim rotalarında `requiredRole`/`requiredPermission` atanmış | tarama: yalnız test (`SpaRouteTest.php:31`, `AuthGuardTest.php:92,101`) + API routes.php:15 yorumu → **üretim rotasında yok** | ✅ KANITLI (negatif kanıt) |
| Sonuç: API'de RBAC meta'sı atanırsa rol listesi boş kalır → `403` | `AuthorizationMiddleware.php:64, :77` (`in_array(..., true)` boşta false) | ⚠️ VERIFICATION REQUIRED (davranış testi yok) |

> Bu madde **kayıt amaçlıdır**; düzeltme yetkisi `authorization` + Backend Architect
> üzerindedir (ADR gerekir). Sessizce "geçer" sayılmaz.

## §14 Gözlemlenebilirlik Maddeleri

| # | Olay | Kayıt | Kanıt |
|---|---|---|---|
| O-1 | Bypass aktifse | `logTestBypass` → dosya (`[TEST_BYPASS]`) | `BypassAuthMiddleware.php:39` · `SecurityHelper.php:42-55` |
| O-2 | Rate limit reddi | `securityEvent('rate_limited')` (429) | `RateLimiterMiddleware.php:88-92` |
| O-3 | Rate-limit kaynağı yok | `securityEvent('rate_limiter_unavailable')` (fail-closed/open) | `RateLimiterMiddleware.php:57, :67` |
| O-4 | CSRF reddi | `securityEvent('csrf_invalid')` | `CsrfMiddleware.php:52-55` |
| O-5 | Middleware süresi | `StructuredLogger::timing` | `shared/src/Middleware/MiddlewarePipeline.php:41-49` |
| O-6 | Kimlik doğrulama **başarısızlık** olayı (login fail) | — | ⚠️ VERIFICATION REQUIRED (bu oturumda okunmadı) |
| O-7 | 401 yanıtı başına log | — | UNKNOWN |

## §15 Test Zorunlulukları (Mevcut Envanter)

| Test dosyası | Ne kapsıyor | Kanıt |
|---|---|---|
| `shared/tests/Middleware/AuthMiddlewareTest.php` | session → `_auth`; user yoksa `_auth` boş (PermissionMiddleware pasif) | `:32`, `:84` |
| `shared/tests/Middleware/BypassAuthMiddlewareTest.php` | bypass senaryoları (11 `new BypassAuthMiddleware`) | grep: satır 62…230 |
| `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` | tanımsız sabit → bypass yok (ayrı süreç) | `:39-46`, `:48-49` |
| `shared/tests/Middleware/CsrfMiddlewareTest.php` | CSRF geç/red (16 `new CsrfMiddleware`) | grep: satır 34…333 |
| `shared/tests/Unit/Security/JwtServiceTest.php` | JWT servisi | dosya envanteri (içerik okunmadı) → ⚠️ |
| `shared/tests/Api/ApiPipelineSecurityTest.php` | public rota geçişi, kimliksiz 401, dondurulmuş sıra | `:127-151`, `:161-184` |

**Zorunlu yeni testler (açık):**

| # | Test | Sahip |
|---|---|---|
| Z-1 | Üretim ortamında bypass kapalı (`env=production` → no inject) | QA + Security |
| Z-2 | Pepper boşken hash üretiminin exception fırlatması | QA |
| Z-3 | Revocation edilmiş `jti` → 401 | QA |
| Z-4 | Parola hata mesajının ayrım yapmaması (A-03) | QA |
| Z-5 | API `roles` boşluğu (§13) davranış testi | QA + Backend |

## §16 Kanıt Envanteri (Evidence Inventory)

| # | Dosya:Satır | Ne gösteriyor |
|---|---|---|
| 1 | `auth.coremusic.net/include/Domain/ValueObject/Password.php:14,26-33` | min 8 + ValidationException |
| 2 | `Password.php:38-49` | pepper fail-closed + Argon2id parametreleri |
| 3 | `Password.php:54-61` | verify: boş pepper false + `password_verify` |
| 4 | `shared/src/Middleware/AuthMiddleware.php:11-33` | session → `_auth` (userId/role/username/permissions) |
| 5 | `AuthMiddleware.php:23-31` | kimlik yoksa `_auth` dolmaz |
| 6 | `shared/src/Middleware/BypassAuthMiddleware.php:23-34` | fail-closed config yükleme |
| 7 | `BypassAuthMiddleware.php:36-44` | aktif + boş config → `next()` (injection yok) |
| 8 | `BypassAuthMiddleware.php:49-54` | session'a bypass yazımı (ADR-008, `:46-48` yorum) |
| 9 | `shared/src/Security/SecurityHelper.php:23-30` | production → bypass kapalı |
| 10 | `shared/src/Security/JwtService.php:24` | `ALGORITHM = 'RS256'` |
| 11 | `JwtService.php:44-72` | `issue()`: `jti` `:51`, `exp` `:52` |
| 12 | `JwtService.php:83-109` | `validate()`: catch→null `:91-94`, iss/aud `:96-101`, sub/jti `:102-104` |
| 13 | `shared/src/Api/Middleware/AuthenticationMiddleware.php:33-38` | `PUBLIC_ROUTE_PATTERNS` (4 uç) |
| 14 | `AuthenticationMiddleware.php:56-69` | 404/405 + public rota geçişi |
| 15 | `AuthenticationMiddleware.php:72-100` | session → jwt → revocation sırası |
| 16 | `AuthenticationMiddleware.php:103-107` | `401 UNAUTHORIZED` |
| 17 | `shared/src/Middleware/CsrfMiddleware.php:15-17,42` | bypass route yok + `hash_equals` |
| 18 | `shared/src/Middleware/RateLimiterMiddleware.php:20-30,53-69,93-99` | fail-closed yol listesi, 503, 429 |
| 19 | `shared/src/PageRouter/PageRouterKernel.php:330-341` | web pipeline sırası (10 middleware) |
| 20 | `api.coremusic.net/index.php:147-171` | API pipeline sırası (8 pipe) |
| 21 | `shared/src/PageRouter/AuthUrlBuilder.php:32-40` + `RouteResult.php:43-56` | SPA 403 / klasik 302 |
| 22 | `shared/tests/Api/ApiPipelineSecurityTest.php:161-184` | frozen sıra testi |

## §17 Uyum Matrisi (Standart / ADR)

| Kural | Kaynak | Uyum |
|---|---|---|
| Argon2id + VO dışında hash yasak | `auth.coremusic.net/AGENTS.md §4.3` | ✅ `Password.php:44` |
| Fail-safe, fail-open yasak (auth) | `.ai/AGENTS.md §18` (fail-open → veto) | ✅ §7 F-1..F-10 |
| Bypass kapsam değişimi ADR gerektirir | `shared/AGENTS.md §4 Yasak 2 (ADR-008)` | ✅ §11 Y-11 |
| Pipeline sırası ADR'siz değişmez | `api.coremusic.net/AGENTS.md §4.4 (ADR-020)` | ✅ §12 + test |
| Secret dosyaya/vault'a yazılmaz | kök `AGENTS.md` Zero-Hallucination | ✅ A-09 |
| OWASP / NIST referansları | `[[authentication-web-arastirma]]` (HTTP 200 kayıtları) | ✅ bu dosyada URL yok (mimari grup) |

## §18 Bilinmeyenler Defteri (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | MFA/2FA uygulaması var mı? | ⚠️ VERIFICATION REQUIRED (K0100-identitiy) |
| U-2 | 401 + `WWW-Authenticate` başlığı yayılıyor mu? | ⚠️ VERIFICATION REQUIRED |
| U-3 | `password_needs_rehash` akışı var mı? | UNKNOWN |
| U-4 | OAuth2/OIDC doğrulama adımları (PKCE dahil) | ⚠️ VERIFICATION REQUIRED (C-6) |
| U-5 | Login başarısızlık olayı loglanıyor mu? (ad/no-pattern ayrımı) | UNKNOWN (O-6) |
| U-6 | API `roles`/`permissions` boşluğu (§13) canlı davranışta nasıl sonuçlanır? | ⚠️ VERIFICATION REQUIRED |
| U-7 | Account lockout (kilitli hesap) mekanizması | UNKNOWN (rate-limiting) |
| U-8 | `password_verify` sabit-zamanlılık iddiasının kaynağı | ⚠️ VERIFICATION REQUIRED (T-4) |

## §19 İlişkili Düğümler (Relations)

| Düğüm | İlişki |
|---|---|
| `K009-security/authentication/index` | Bu çocuğun künyesi ve 8 dosyalık şema |
| `K009-security/authorization` | Doğrulama **sonrası** yetki |
| `K009-security/session-security` | Oturum yaşam döngüsü ayrı çocuk |
| `K009-security/rate-limiting` | Brute-force kotası (F-8, U-7) |
| `K0100-identitiy` | Kimlik modeli/deposu — tek yönlü sınır |
| `K009-security/security-master` | Domain anayasası (çelişkide üst otorite) |

## § BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[authentication-kapsam]] | kardeş | kapsam içi/dışı sınırı |
| [[authentication-ornekler]] | kardeş | gerçek PHP alıntıları |
| [[authentication-dogrulama]] | kardeş | 18 kontrol kanıtı (AD-01..AD-18) |
| [[authentication-bilesenler]] | kardeş | kademe-3 bileşen planı |
| [[authentication-web-arastirma]] | kardeş | OWASP / NIST kayıtları |
| [[authorization-anayasa]] | sonraki | yetki karar kapıları |
| [[authorization-ornekler]] | sonraki | Permission/Authorization kodu |
| [[security-master]] | yukarı | K009 domain anayasası |
| [[security-kararlar]] | yukarı | ADR kararları |
| [[rate-limiting-kapsam]] | kardeş | kota sınırı |
| [[session-security-kapsam]] | kardeş | oturum kapsamı |
| [[identity-master]] | tek yönlü | K0100-identitiy kimlik modeli |
| [[transactions-anayasa]] | iskelet | bu dosyanın anayasa şablonu |

## §20 Revizyon Günlüğü

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 22 kanıt satırı, URL tablosu yok (mimari grup), §13 zayıf nokta kaydı | Technical Writer (tek yazar) |
| 2026-10-15 | `parent:` yok, `parent_id: K009-security` · `group: mimari` | Technical Writer |

## §21 Sürüm ve Otorite

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Otorite | Bayram Ali / Vault Steward |
| Mod | Red Team · Human Mode · Truth Mode |
| Durum | draft (U-1..U-8 açık) |
| Erişim | `.ai/architecture/K009-security/authentication/authentication-anayasa.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
