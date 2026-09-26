---
title: "CoreMusic — ADR-020: API Public Security (API Key Hash+Scope · JWT Bearer Kilidi · OAuth2 PKCE · Rate Limit · Girdi/Yanıt Doğrulama · CORS · Versiyonlama · Hata Sızıntısı · Audit Log)"
type: adr
category: security
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-020 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-020: API Public Security (API Key Hash+Scope · JWT Bearer Kilidi · OAuth2 PKCE · Rate Limit · Girdi/Yanıt Doğrulama · CORS · Versiyonlama · Hata Sızıntısı · Audit Log)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-020'yi sıfırdan yaz"; karar içeriği kullanıcı onaylı: **kapsam = genel API güvenlik seti · auth üçlüsü = API key (hash+scope+rotasyon) + JWT Bearer kilidi + OAuth2 PKCE (PLANNED dürüst etiketle)**) · debate: **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** · Tech Lead: **✅**
**İlgili ADR'ler:** [[ADR-010-csrf-protection-strategy]] (**şart 3** — JWT stub kapanana kadar cookie-auth tek yol; Bearer muafiyeti bu ADR'deki kilit ile birebir aynı kapı; dosya diskte VAR ✅) · [[ADR-011-session-management]] (hibrit oturum — §4.4 "Bearer ile oturum açma AÇILMAZ"; bu ADR API ucunu bağlar; dosya diskte VAR ✅) · [[ADR-013-rate-limiting-apcu]] (**§1.1 bulgusu: API `RateLimitMiddleware` sınıfı var ama pipeline kaydı yok** — bu ADR o kaydı şart koşar; dosya diskte VAR ✅) · [[ADR-015-env-parser-strategy]] (`getenv` yasağı — `OAuthManager.php:77-78` ihlali bu ADR'nin risk tablosunda; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı; dosya diskte VAR ✅) · [[ADR-002-pdo-mandatory-no-orm]] (API key/audit lookup'ı yalnız PDO prepared statement; dosya diskte VAR ✅) · [[ADR-009-clean-url-redirect]] (edge/.conf = 0 bulgusu — edge rate limit/WAF bu ADR'de PLANNED olarak teyit edilir; dosya diskte VAR ✅) · [[ADR-016-url-normalization]] (API yolu `/api/v1/*` normalizasyonu ile çakışma kontrolü; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 57** `[[ADR-020-api-public-security]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in **public API yüzeyi** (`api.coremusic.net` + Gateway `/api/v1/*` uçları) üç ayrı dosyada dağınık duruyor: 6 API middleware sınıfı tanımlı ama **hiçbiri runtime pipeline'a kaydedilmemiş**, JWT doğrulama stub'ı `null` döndürüyor, API key doğrulama kodu **yok** (yalnız rate-limit key'i olarak okunuyor), Gateway 500'de **exception mesajını istemciye geri veriyor**, audit log'un **PHP yazıcısı yok** (SQL tabloları hazır). Bu ADR; kimlik doğrulama (API key / JWT Bearer / OAuth2 PKCE), rate limit, girdi-yanıt doğrulama, CORS/Origin, versiyonlama, hata sızıntısı ve audit log'u **tek bir "API public security seti"** altında bağlayıcı hale getirir — kod kanıtı olmayan her kalemi dürüstçe **PLANNED** etiketleyerek.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) SPEC/SQL/VAULT KATMANI — IMPLEMENTED (şema ve kayıt var; PHP kodu B bölümünde ayrı etiketli):**

- **`shared/` API anahtar şeması — `.ai/.sql/mysql/coremusic_api.sql:31-57` (IMPLEMENTED şema):** `api_keys` tablosu → `api_key_hash CHAR(64)` (**SHA-256 hex — "Ham anahtar ASLA DB'de saklanmaz", satır 27, 34**), `api_key_prefix` = `cm_live_` | `cm_test_` + ilk 8 karakter (satır 28, 35), `scope VARCHAR(1024)` virgülle ayrılmış yetki alanları (ör. `music.read,music.write,user.read`, satır 29, 37), `allowed_ips` JSON array (satır 38), `is_active`/`expires_at`/`last_used_at` (satır 39-41), `UNIQUE KEY uq_ak_hash` (satır 48) → **prefix lookup + hash unique index şemada hazır**. Aynı dosya: `rate_limits` (per-key endpoint bazlı kota, satır 63-82), `api_calls` (aylık partition'lı çağrı logu, satır 92-119), `webhooks` (satır 148-169).
- **İkinci API key şeması — `.ai/.sql/mysql/coremusic_auth.sql:284-311` (IMPLEMENTED şema):** `api_keys` tablosu + `idx_apikeys_hash` unique + `idx_apikeys_prefix` — `coremusic_auth` DB'sinde de aynı kavram var → **iki DB'de iki `api_keys` tanımı (çapraz kaynak: coremusic_api.sql vs coremusic_auth.sql) → hangisinin SSOT olduğu ⚠️ VERIFICATION REQUIRED** (§4.3 risk 6).
- **Token/credential şeması — `coremusic_auth.sql:146` (`token_type ENUM(...,'api_key','refresh','access')`), `:178` (`credential_type ENUM('api_key',...)`)** → API key/refresh kavramı şemada.
- **Audit şeması — `.ai/.sql/mysql/coremusic_logs.sql` (IMPLEMENTED şema):** `audit_logs` (satır 19-40), `rate_limit_logs` — "Rate limiting tracking for API security" (`identifier_type ENUM('ip','user','api_key')`, satır 124-142), `log_security` (`event_type ENUM` + index, satır 483-528) → **tablolar var**.
- **Domain haritası — `shared/config/domain.php:7-14`:** 7 subdomain (`auth`, `home`, `assets`, `music`, `admin`, `media`, **`api` → `api.coremusic.net` satır 14**). Repo kökünde `api.coremusic.net/` dizini **YOK** (yalnız `assets/`, `auth/`, `home/` dizinleri var) → API host henüz deployment'ta değil; **`public.coremusic.net` hiçbir dosyada geçmiyor (grep 0)** → public API yüzeyi `api.coremusic.net`'dir, ayrı "public" host'u **yok**.
- **Dizin kayıtları (önceden rezerve — bu dosya ile canlanır):** `.ai/.decisions/index.md:57` · `.ai/index.md:637` · `.ai/keys.md:255` · `.ai/brain.md:975` ("API güvenlik stratejisi") · `.ai/.templates/adr/adr-index.md:91` (`🔵 backend`, `adr-security-template.md ⚠️`) · `adr-security-template.md:388,393,396` (SEC-04/SEC-09/SEC-12 ADR-020'ye bağlı: ownership/403, hata gizleme, RBAC matrisi).

**B) KOD KATMANI:**

*IMPLEMENTED (sınıf/behavior kodda var):*

1. **Rate limit sınıfı — `shared/src/Api/Middleware/RateLimitMiddleware.php`:** key = `api:` + `sha256(HTTP_X_API_KEY)` **ya da** `ip:` + `REMOTE_ADDR` (**satır 57-68**); 60 istek/60 sn varsayılan (**satır 22-23**); 429 + `Retry-After` header'ı (**satır 39-46**); `withLimits(int, int)` = per-route kota iskeleti (**satır 73-79**).
2. **Hibrit auth + JWT stub — `shared/src/Middleware` değil, `shared/src/Api/Middleware/AuthenticationMiddleware.php`:** önce session/cookie (**satır 37-45**, `method: 'session'`), sonra `Authorization: Bearer ` prefix (**satır 48-57**); public route allowlist'i `/api/v1/auth/login|register|forgot-password`, `/api/v1/public` (**satır 73-78**); **`validateJwtToken()` her zaman `null` döner (satır 92-105: "For now, return null (not validated)") → Bearer yolu fiilen ÖLÜ**.
3. **İstek doğrulama — `RequestValidationMiddleware.php`:** Respect/Validation (**satır 15-16**), doğrulama başarısız → **422** (**satır 51**), kural eşleme `map rule configuration` (**satır 75**).
4. **Yanıt normalizasyonu — `ResponseNormalizationMiddleware.php`:** standart header'lar `Content-Type`/`X-Content-Type-Options: nosniff`/`X-Frame-Options: DENY` (**satır 45-47**), ETag (**satır 53-59**), Cache-Control (**satır 67-79**) → **yalnız header/ETag; yanıt ŞEMA doğrulaması YOK**.
5. **Versiyonlama — `shared/src/Api/Versioning/` (3 dosya):** `ApiVersion` enum `V1|V2|INTERNAL|PUBLIC|ADMIN` (**ApiVersion.php:17-23**), `VersionResolver` — path (`/api/v1`) + `Accept-Version` header (**VersionResolver.php:34-35, 70-72**), `VersionRegistry` — 6 servis için standart `/api/v1/{service}` ve `/{id}` rotaları (**VersionRegistry.php:96-115**) + **test: `shared/tests/Api/VersioningTest.php` 6 test (satır 12-53)** → **versiyonlama IMPLEMENTED + testli (tek testli API bileşeni)**.
6. **CORS + Origin — `shared/src/Middleware/CorsMiddleware.php` + `OriginCheckMiddleware.php`:** izinli header'lar **`['Content-Type','X-CSRF-Token','X-Requested-With']` (CorsMiddleware.php:32) → `Authorization` ve `X-Api-Key` YOK (preflight'te Bearer/API key'i engeller)**; `allow_credentials` varsayılan `true` + origin echo (**satır 33, 48-52**), `Max-Age 86400` (**satır 52**); OriginCheck whitelist + **suffix-match** `str_ends_with($host, '.'.$allowedHost)` (**OriginCheckMiddleware.php:68**), izinsiz → **403 `origin_not_allowed`** (**satır 47-54**), **production'da dev fallback YOK = fail-closed (satır 73-76)**, dev'de fallback listesi (**satır 78-83**).
7. **Sosyal OAuth + PKCE mekaniği — `shared/src/OAuth/OAuthManager.php`:** 10 sosyal sağlayıcı `pinterest|instagram|tiktok|snapchat|discord|reddit|x|linkedin|youtube|facebook` (**satır 90-102**), **PKCE `code_verifier` kaydı `saveState()` (satır 194-205) + doğrulama `validateState()` (satır 212-238)**, token **AES-256-GCM** şifreleme (**satır 242-261**), **ama `getenv()` doğrudan çağrı — `satır 77-78` (ADR-015 ihlali)** → sosyal login IMPLEMENTED, **üçüncü taraf API entegrasyonu (resource owner) PLANNED**.
8. **Gateway — `shared/src/Api/Gateway.php`:** versiyon resolve → route match → `middlewarePipeline->process()` (**satır 33-59**); **6 `/api/v1/*` rotası hardcoded (satır 80-89)**; **HATA SIZINTISI: `catch (\Throwable)` → `['exception' => $e->getMessage()]` (satır 60-67) — exception mesajı istemciye JSON içinde dönüyor**; handler placeholder "Handler not implemented yet" (**satır 105-114**); dosya başlığı `@see ADR-084-api-gateway-architecture` (**satır 9** — **ADR-084 dosyası diskte YOK, glob boş → düz metin, ⚠️ VERIFICATION REQUIRED**).
9. **Pipeline iskeleti — `ApiMiddlewarePipeline.php`:** `pipe(callable)` (**satır 24-26**) + `process()` zinciri (**satır 33-40**) **tanımlı**.

*PLANNED (kod yok / kayıt yok — uydurulmadı):*

1. **API middleware pipeline KAYDI YOK → kodlamadan önce bağlanmalı:** repo geneli grep `new RateLimitMiddleware|new AuthenticationMiddleware|new AuthorizationMiddleware|new RequestValidationMiddleware` → **src'de 0 sonuç** (yalnız `.ai/architecture/k7-middleware/*` doküman örnekleri `psr15-pipeline.md:139,144`, `index.md:75,80`); `ApiMiddlewarePipeline->pipe()` çağrısi **0**; `new Gateway` / `Gateway::class` **0** (yalnız sınıf tanımı + `use`); **`PageRouterKernel.php:267-282` web middleware stack'i (OriginCheck, Cors, RateLimiter, Session, CSRF, BypassAuth, Auth, Permission, Validation) API middleware'lerini içermiyor, `pipe()` yalnız orada çalışıyor (satır 241-247)** → **6 API middleware + Gateway = TANIMLI, KAYITSIZ** (ADR-013 §1.1 bulgusu bu ADR ile teyit ve tekrar kayıtlı).
2. **API key doğrulama KODU YOK:** grep `HTTP_X_API_KEY` → **tek eşleşme, rate-limit key'i (`RateLimitMiddleware.php:60`)**; `validateApiKey|api_keys|ApiKey` PHP'de **0** → key doğrulaması, scope denetimi, hash lookup, rotasyon **PHP katmanında PLANNED** (şema A bölümünde hazır).
3. **JWT doğrulama → stub `null` (B.2)** → `⚠️ VERIFICATION REQUIRED` + kilit (§2.2c).
4. **OAuth2 PKCE (third-party API auth) PLANNED:** mevcut PKCE yalnız sosyal login akışında; **resource-owner / authorization-code ile harici API erişimi kodda yok** → dürüst PLANNED.
5. **Audit log PHP yazıcı YOK:** repo geneli PHP grep `audit_logs|log_security|logSecurityEvent|auditLog` → **0 eşleşme** (yalnız SQL şeması + `shared/src/Log/LoggerFactory.php`+`FileHandler.php` genel log) → **güvenlik olayı yazımı PLANNED**.
6. **CORS preflight genişlemesi PLANNED:** `Authorization`/`X-Api-Key` header listesinde yok (B.6) → Bearer/API key'li tarayıcı istekleri preflight'te düşer.
7. **Yanıt şema doğrulaması PLANNED:** `ResponseNormalizationMiddleware` yalnız header/ETag (B.4).
8. **Hata sızıntısı düzeltmesi PLANNED:** Gateway `exception` alanı bugün aktif (B.8) → §2.2h ile kapatılır.
9. **Edge/WAF rate limit PLANNED:** `.conf`/`.htaccess` = 0 (ADR-009 bulgusu — teyit) → uygulama katmanı tek hat.

**Sonuç etiketi:** **şema/vault katmanı IMPLEMENTED** (3 SQL dosyası + 5 dizin kaydı + domain haritası); **kod katmanında IMPLEMENTED: 6 middleware sınıfı + Gateway + versiyonlama (testli) + sosyal OAuth/PKCE**; **PLANNED: pipeline kaydı, API key doğrulama, JWT, OAuth2 third-party, audit yazıcı, CORS header genişlemesi, yanıt şeması, hata gizleme**. Bu ADR **kod sözü değil, güvenlik sözleşmesidir** — bağlayıcı, uygulaması §5.1 adımlarına bağlıdır.

### 1.2 Sorun Tanımı

1. **Middleware'ler tanımlı, kayıtlı değil:** 6 API sınıfı + `ApiMiddlewarePipeline::pipe` var ama **tek bir `pipe()` çağrısı yok**; `Gateway` hiç instantiate edilmiyor → rate limit/auth/validation **bugün public API'de çalışmıyor** (ADR-013'in "pasif kod" bulgusu burada genelleşir).
2. **API key doğrulaması yok:** şemada hash+prefix+scope+expiry hazır, kodda tek satır `HTTP_X_API_KEY` (rate-limit key'i) → **ham anahtar istemciden gelir, hiçbir yerde hash'lenip DB ile karşılaştırılmıyor** → API key = süs.
3. **Bearer kilidi açık değil ama kapı da yazılı değil:** `validateJwtToken` stub `null` → ADR-010 şart 3 / ADR-011 §4.4 gereği **Bearer oturum AÇILMAZ**; buna karşılık **API key yolunun kendisi de yok** → third-party erişiminin tek meşru yolu tanımsız.
4. **Hata sızıntısı:** Gateway 500'de `exception => $e->getMessage()` (B.8) → SQL/dosya yolu/sınıf adı istemciye sızar (OWASP API3/API10 ile çelişir).
5. **CORS Bearer/API key'i engelliyor:** preflight izinli header'larında `Authorization`/`X-Api-Key` yok (B.6) → cross-origin API çağrısı 401/403'e düşer; credentials `true` + origin echo birlikte dikkat ister.
6. **Audit log yalnız SQL:** `log_security`/`audit_logs`/`rate_limit_logs` tabloları var, **PHP'den yazan kod yok** → brute force, origin ihlali, key rotasyonu olayları kayda geçmez (ADR-013 "audit log" şartı burada karşılanmamış).
7. **Çelişkili ikinci şema:** `coremusic_api.api_keys` vs `coremusic_auth.api_keys` — SSOT belirsiz (⚠️ VERIFICATION REQUIRED) → doğrulama kodu hangi tabloya bakacak?
8. **`public.coremusic.net` hayalet:** hiçbir dosyada geçmiyor; `api.coremusic.net` dizini yok → **"public API" nerede yaşayacak"** sorusu cevapsız → host/kapsam kararı bu ADR'de sabitlenir (§2.2a).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (owasp.org, ietf.org, oauth.net, google cloud, microsoft), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) OWASP API Security Top 10 2023 (BOLA/API3/API4/API10), (b) API key hash+scope+rotasyon uygulamaları, (c) JWT 2025-26 (RFC 8725, kısa ömür, refresh rotation + reuse detection, JWKS/kid), (d) OAuth 2.1 PKCE (implicit kaldırma, exact redirect, RTR), (e) URL versiyonlama + Deprecation/Sunset header'ları.** Erişim: **5 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "OWASP API Security Top 10 2023 API1 BOLA API3 property authorization API4 resource consumption rate limiting API10 unsafe consumption" · (2) "API key best practices SHA-256 hash prefix lookup constant-time comparison scoped read-only default key rotation overlap 2025" · (3) "JWT best practices 2025 2026 RFC 8725 algorithm allowlist short-lived access token refresh rotation reuse detection JWKS kid rotation" · (4) "OAuth 2.1 PKCE mandatory all clients implicit flow removed exact redirect URI match RFC 9744 resource owner 2025" · (5) "REST API versioning URL path /v1/ major only breaking change definition Deprecation header RFC 9745 Sunset RFC 8594 410 Gone six months" |
| Web Search **Konusu** | (1) OWASP API Security Top 10 2023: API1 BOLA (nesne erişim yetkisi), API3 Broken Object Property Level Authorization (yanıt/özellik sızıntısı — response schema doğrulama gerekçesi), API4 Unrestricted Resource Consumption (rate limit + kota gerekçesi), API10 Unsafe Consumption of APIs (dış API'den gelen veriye güvenme/hata temizleme); (2) API key saklama: SHA-256 (düz ama yüksek entropi anahtar için doğru) vs bcrypt (düşük entropi için), prefix ile lookup + hash ile doğrulama, `hash_equals` sabit-zamanlı karşılaştırma, scope ile en-az-ayrıcalık (varsayılan read-only), rotasyonda overlap (eski anahtar kısa süre geçerli) / multi-key; (3) JWT: RFC 8725 BCP (alg allowlist, `none` yasağı, RS256/ES256), access token 5-15 dk, rotasyonlu refresh + reuse detection → aile iptali, JWKS + `kid` rotasyonu, `exp/iss/aud` doğrulama; (4) OAuth 2.1: PKCE tüm istemcilerde zorunlu, implicit grant kaldırıldı, redirect URI birebir eşleşme, Resource Token Revocation; (5) versiyonlama: URL path `/v1/` = major-only, breaking change tanımı, `Deprecation` (RFC 9745) + `Sunset` (RFC 8594) header'ları, ~6 aylık geçiş süresi, `410 Gone` |
| Web Search **Bağlam** | **~36 adlandırılmış kaynak / 5 sorgu**: owasp.org ×4 + paloaltonetworks + OWASP API Security slides (6) · AWS Secrets Manager docs, Stripe API keys, apikeys.guide, Zuplo ×2, webtoolkit, yas.sh, security.stackexchange (8) · ietf.org RFC 8725, APIsec, Jsonic, Radware, Codelit, env.dev, AI Wisdom, Aptori (8) · oauth.net/2.1, WorkOS ×2, Spring docs, Curity, startwithidentity, Matthew Palma (7) · Google Cloud, Zalando, Microsoft ×2, VitalSentinel, ABP, sota-api-design (7) |
| Web Search **Kısa Açıklama** | **(1) OWASP:** API1 BOLA = "işlem nesnesine erişim yetkisi kontrolü yok" (owasp.org); API3 = istenmeyen özelliklerin/yanıt şemasının aşırı dönüşü (hata mesajı dahil — **Gateway exception sızıntısı buna girer**); API4 = kota/rate limit yokluğunun kaynak istismarı (Palo Alto aynı sıralamayı tekrarlar) → **rate limit + yanıt temizliği OWASP zorunluluğu**. **(2) API key:** AWS/Stripe **prefix ile kayıt bulma + hash ile doğrulama** deseni; apikeys.guide/yas.sh **yüksek entropi anahtar → SHA-256 makul, bcrypt gerekmez** (anahtar zaten tahmin edilemez); stackexchange **`hash_equals` sabit-zamanlı**; Zuplo/Webtoolkit **scope varsayılan en-az-ayrıcalık (read-only) + rotasyonda overlap penceresi**. **(3) JWT:** RFC 8725 "`none` ve zayıf alg yasak, allowlist" + "access kısa ömür"; Jsonic/Radware/Aptori **refresh rotation + reuse detection → aile iptali**; APIsec/Codelit **JWKS + `kid` ile anahtar rotasyonu**; env.dev/AI Wisdom **`exp/iss/aud` zorunlu**. **(4) OAuth 2.1:** oauth.net/2.1 + WorkOS **PKCE tüm istemcilere zorunlu, implicit kaldırıldı**; Spring/Curity **birebir redirect eşleşmesi**; startwithidentity/Matthew Palma **state + PKCE birlikte**. **(5) Versiyonlama:** Google Cloud/Zalando **URL path = major-only, breaking change tanımı yazılır**; Microsoft ×2 + VitalSentinel **`Deprecation`/`Sunset` header + 410**; ABP/sota-api-design **6 aylık emeklilik penceresi**. |
| Web Search **Uzun Açıklama** | **(a) OWASP bağlamı (kaynak 1-6):** 2023 listesinin ilk sıraları nesne/özellik yetkisi ve kaynak tüketime ayrılır; API3 özellikle **"dönen verinin fazlasını"** (özellik + hata detayı) kapsar → Gateway'in `exception` mesajını dönmesi ve `ResponseNormalization`'ın şema denetimi olmaması doğrudan API3 ihlali adayıdır; API4 ise **kotasız uç** demektir → `RateLimitMiddleware`'in kaydı yoksa tüm `/api/v1/*` uçları API4'te kalır. API10, dış API tüketiminde gelen verinin doğrulanmasını ve **hata bilgisinin temizlenmesini** ister (Bootstrap OAuthManager decode zinciri ile kesişir). **(b) API key tasarımı (kaynak 7-14):** doğru desen **üretimde tek sefer görülen ham anahtar → SHA-256(hex) ile hash → DB'de `key_hash` unique index → istekte `X-Api-Key` gelir, prefix ile kayıt bulunur, `hash_equals` ile hash karşılaştırılır**; bcrypt/sha256 tartışmasında anahtar entropisi belirleyicidir (anahtar 32+ bayt rastgele ise düz hash uygundur; kullanıcı-girilen secret için bcrypt); **scope varsayılanı read-only** (Stripe'ın `sk_live`/`rk_` okuma-anahtarı ayrımı, AWS'in ayrı okuma/yazma politikaları örnek); **rotasyon = overlap**: yeni anahtar üretilir, eski anahtar `expires_at` ile kısa süre (ör. 24-72 sa) yaşar, sonra `is_active=0` → kesintisiz geçiş. **(c) JWT 2025-26 (kaynak 15-22):** RFC 8725 hâlâ omurga: allowlist algoritma, `none` yasağı, kısa access ömrü; literatür 2025-26'da **refresh rotation + reuse detection'ı standart**, tek kullanımlık refresh çalınırsa **aynı family'deki tüm token'ları iptal** ediyor; JWKS + `kid` ile imzalayıcı rotasyonu operasyonel gereklilik; `exp` yanında `iss`/`aud` doğrulanmazsa token başka serviste geçerli kalır. **(d) OAuth 2.1 (kaynak 23-29):** OAuth 2.1, PKCE'yi **public AND confidential** tüm istemcilere zorunlu kılar, implicit'i kaldırır, redirect URI'de **birebir eşleşme** (prefix match yok), state hâlâ CSRF için gerekli; bu üçü CoreMusic sosyal login'in `saveState/validateState` mekaniğiyle uyumlu → **aynı mekaniği third-party API auth'a taşımak mümkün, yeniden yazmak gerekmez**. **(e) Versiyonlama (kaynak 30-36):** URL path sürümlemesi **yalnız major** (`/v1/`) taşır, minor değişiklik URL'de görünmez (header/mediatype opsiyonel); **breaking change tanımı yazılmazsa** her değişiklik tartışmalı olur; emeklilik **`Deprecation` + `Sunset` header'ları ile duyurulur, ~6 ay ranway, sonra `410 Gone`** → `VersionResolver` (path + `Accept-Version`) zaten IMPLEMENTED; eksik olan **politika (major-only kuralı + header'lar)**. |
| Web Search **Paragraf Veri Uzun** | OWASP 2023: API1 BOLA · API3 property/yanıt aşırılığı (hata detayı dahil) · API4 resource consumption → rate limit · API10 dış API doğrulama (owasp.org ×4, Palo Alto, slides) · API key: prefix lookup + SHA-256 hash + unique index (AWS, Stripe, apikeys.guide, yas.sh) · `hash_equals` sabit-zamanlı (stackexchange) · scope varsayılan read-only + en-az-ayrıcalık (Zuplo, Webtoolkit) · rotasyon overlap / multi-key (Zuplo, AWS) · JWT: RFC 8725 allowlist + `none` yasak + 5-15 dk access (ietf.org, APIsec) · refresh rotation + reuse → family revoke (Jsonic, Radware, Aptori) · JWKS + `kid` (Codelit, APIsec) · `exp/iss/aud` (env.dev, AI Wisdom) · OAuth 2.1: PKCE zorunlu tüm istemciler, implicit yok (oauth.net/2.1, WorkOS ×2) · exact redirect (Spring, Curity) · state+PKCE (startwithidentity, Matthew Palma) · versiyon: URL major-only + breaking change tanımı (Google Cloud, Zalando) · `Deprecation` RFC 9745 + `Sunset` RFC 8594 + `410 Gone` (Microsoft ×2, VitalSentinel) · ~6 ay runway (ABP, sota-api-design). **Sonuç: key = hash+prefix+scope+rotasyon; JWT = kısa ömür+rotation+JWKS; OAuth2 = PKCE+exact redirect; versiyon = major-only+header duyurusu; OWASP = rate limit + yanıt temizliği zorunlu.** |
| Web Search **Sonucu** | 1) **OWASP zorunlulukları doğrulandı** (kaynak 1-6): rate limit (API4), yanıt/hata temizliği (API3), dış veri doğrulama (API10) → ≥2 çapraz kaynak; **"Gateway exception sızıntısı API3'e girer"** çıkarımı bizim değerlendirme → `⚠️ VERIFICATION REQUIRED` (OWASP metni hata sızıntısını API3/API10 ile ilişkiler, dosyada birebir test edilmedi). 2) **API key deseni doğrulandı** (kaynak 7-14): prefix lookup + SHA-256 + `hash_equals` + scope read-only + rotasyon overlap → ≥2 çapraz kaynak; **bcrypt tercihi yalnız düşük-entropi sır için** (çapraz: stackexchange + apikeys.guide). 3) **JWT doğrulandı** (kaynak 15-22): RFC 8725 + rotation/reuse + JWKS → ≥2 çapraz kaynak. 4) **OAuth 2.1 doğrulandı** (kaynak 23-29): PKCE zorunlu + implicit yok + exact redirect → ≥2 çapraz kaynak. 5) **Versiyonlama politikası doğrulandı** (kaynak 30-36): major-only + Deprecation/Sunset + 410 + 6 ay → ≥2 çapraz kaynak; **`VersionResolver` kodda zaten var → yalnız politika/header eklenir**. **Toplam ~36 adlandırılmış kaynak, 5 sorgu**; sayfa-içi derin tur yapılmadığı için RFC madde numaraları ve kesin süreler başlık/özet düzeyindedir (açıkça işaretli). |
| Web Search **Alınan Karar** | **ADR-020 KABUL EDİLİR — API PUBLIC SECURITY SETİ (tek karar, 7 bileşen):** **(A) Kapsam:** public API yüzeyi = `api.coremusic.net` + Gateway `/api/v1/*` (6 hardcoded rota); `public.coremusic.net` **yoktur** (grep 0) — ayrı host açılmaz. **(B) Auth üçlüsü:** **1) API key** — üretimde tek sefer görülen ham anahtar, DB'de **SHA-256 hash + `cm_live_/cm_test_` prefix lookup** (şema `coremusic_api.sql:31-57` ile birebir), **`hash_equals` sabit-zamanlı**, **scope varsayılan read-only** (`*.read`), `allowed_ips` + `expires_at` destekli, **rotasyon = overlap penceresi** (eski key `expires_at` ile ölür) → *kod PLANNED*; **2) JWT Bearer** — **ADR-010 şart 3 / ADR-011 §4.4 kilidi: `validateJwtToken` stub `null` döndüğü sürece Bearer ile oturum/API erişimi AÇILMAZ** (API uçları da bu kurala tabidir) → RFC 8725 uyumlu implementasyon (allowlist alg, `exp/iss/aud`, kısa access + rotasyonlu refresh + reuse→aile iptali, JWKS/kid) **ancak stub kapanınca**; **3) OAuth2 PKCE** — sosyal login'deki `saveState/validateState` mekaniğinin aynısı **third-party API erişimi için PLANNED** (authorization code + PKCE, exact redirect). **(C) Rate limit:** API `RateLimitMiddleware` **`ApiMiddlewarePipeline`'e kaydedilir** (ADR-013 bulgusu bağlayıcı: sınıf var, kayıt yok), key = `api:sha256` | `ip:`, 60/60 varsayılan, `withLimits` ile per-route kota (şema `rate_limits`), edge/WAF **PLANNED** (`.conf`=0). **(D) Doğrulama:** istek → Respect/Validation **422 (IMPLEMENTED)**; yanıt → **şema doğrulaması PLANNED** (API3). **(E) CORS/Origin:** preflight izinli header'larına **`Authorization` + `X-Api-Key` eklenir**; OriginCheck suffix-match + **prod fail-closed (IMPLEMENTED)** korunur. **(F) Versiyonlama:** URL `/api/v1/` = **major-only**, `Accept-Version` header (IMPLEMENTED); **`Deprecation` (RFC 9745) + `Sunset` (RFC 8594) + `410 Gone` + ~6 ay runway politikası PLANNED**. **(G) Hata temizliği + audit:** Gateway `exception` alanı **kaldırılır** (yalnız `error code` + genel mesaj; detay sunucu log'una), güvenlik olayları (auth fail, 429, 422, origin 403, key rotasyonu) **`log_security`/`audit_logs`'a PHP yazıcı ile PLANNED**. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: OWASP API Top 10 (6 kaynak), API key uygulamaları (8), JWT/RFC 8725 (8), OAuth 2.1 PKCE (7), versiyonlama/Deprecation (7) → **~36 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak beş ana iddiada da karşılanır, iki çıkarım (`⚠️` API3 eşlemesi, bcrypt yalnız düşük-entropi) açıkça işaretlendi. Kod tarafı aynı resmi verdi: **şema IMPLEMENTED** (api_keys hash+prefix+scope+expiry, rate_limits, api_calls, audit_logs/log_security/rate_limit_logs), **sınıf IMPLEMENTED** (6 middleware + Gateway + versiyonlama testli), **kayıt/ doğrulama/ audit/ header/ politika PLANNED** → bu ADR **sözleşme, kod taahhüdü değil**; uygulaması §5.1'e bağlıdır. **Kaynak listesi (~36):** 1) owasp.org — API Security Top 10 2023 (genel) · 2) owasp.org — API4 Unrestricted Resource Consumption · 3) owasp.org — API3 Broken Object Property Level Authorization · 4) owasp.org — API10 Unsafe Consumption of APIs · 5) paloaltonetworks.com — OWASP API Security Top 10 · 6) OWASP API Security slides · 7) AWS — Secrets Manager best practices (anahtar rotation) · 8) Stripe — API keys dokümanı (prefix/live-test ayrımı) · 9) apikeys.guide — saklama/hash rehberi · 10) Zuplo — API key authentication best practices · 11) Zuplo — API key rotation · 12) webtoolkit — API key güvenliği · 13) yas.sh — API key hashing/scoping · 14) Security StackExchange — API key saklama (SHA-256 vs bcrypt, `hash_equals`) · 15) ietf.org — RFC 8725 JWT Best Current Practices · 16) APIsec — JWT security · 17) Jsonic — JWT 2026 (rotation/reuse) · 18) Radware — JWT savunması · 19) Codelit — JWT best practices (JWKS/kid) · 20) env.dev — JWT doğrulama · 21) AI Wisdom — JWT 2025/26 · 22) Aptori — JWT security · 23) oauth.net — OAuth 2.1 · 24) WorkOS — OAuth 2.1 PKCE · 25) WorkOS — implicit flow kaldırılması · 26) Spring Docs — PKCE · 27) Curity — OAuth 2.1 · 28) startwithidentity — OAuth 2.1 · 29) Matthew Palma — OAuth 2.1 · 30) Google Cloud — API versioning · 31) Zalando — RESTful API guidelines (breaking change) · 32) Microsoft — API versioning · 33) Microsoft — breaking changes + Deprecation · 34) VitalSentinel — Deprecation/Sunset + 410 · 35) ABP — API versioning runway · 36) sota-api-design — URL versioning |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-010 şart 3 + ADR-011 §4.4 (bağlayıcı kilit) | `AuthenticationMiddleware::validateJwtToken` stub'ı (`:92-105`) `null` döndükçe **Bearer ile oturum/erişim AÇILMAZ** — **API uçları da bu kurala tabidir**; kilit yalnız stub gerçek imza doğrulaması yapınca ve ayrı onayla kalkar |
| ADR-013 (rate limit) | API `RateLimitMiddleware` sınıfı var ama **pipeline kaydı yok** (bu ADR §1.1-B.1 ile teyit) → kayıt şart; 60/60 sabit pencere korunur, algoritma/kota çerçevesi ADR-013'ün işi (tekrar edilmez) |
| ADR-015 (env) | `OAuthManager.php:77-78` `getenv()` ihlali → API key/JWT/OAuth secret'ları DI `Config` servisinden okunur; **secret bu ADR'ye yazılmaz (REDACTED)** |
| ADR-002 (PDO) | API key lookup, rotasyon, audit yazımı **yalnız PDO prepared statement** — ORM/select* yasak |
| ADR-009 (edge sınırı) | `.conf`/`.htaccess` = 0 → edge rate limit/WAF **PLANNED deployment artefaktıdır**, kod değildir |
| ADR-005 (doğrulama) | Kod kanıtı olmayan her iddia etiketli: pipeline kaydı 0, API key doğrulama 0, audit PHP 0, ADR-084 dosyası yok, `public.coremusic.net` grep 0 → `⚠️ VERIFICATION REQUIRED` |
| Domain boundary (`shared/AGENTS.md` §3-4) | `src/Api/**` + `src/Middleware/**` → Backend Architect; **middleware değişikliği = `MiddlewarePipeline` kaydı + güvenlik audit** (Security Engineer); `config/domain.php` listesi onaysız değişmez (Yasak #4) |
| In-Place Refactoring | Dosya adları (`Gateway.php`, 6 middleware, `OAuthManager.php`, `coremusic_api.sql`, `.ai/.decisions/index.md` vb.) **onaysız değiştirilemez**; bu ADR yalnız karar yazar |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme |
| REDACTED | API anahtarı, JWT signing key/secret, OAuth client secret, DB şifresi hiçbir koşulda bu ADR'ye yazılmaz |
| Numara kuralı | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-020` `.ai/.decisions/index.md:57`'de **rezerve boş slottur** (doldurma, yeni numara tahsisi değil — ADR-019 aynı istisnayı kaydetmişti) |

---

## 2. Karar (Decision)

**CoreMusic'in public API yüzeyi (`api.coremusic.net` + Gateway `/api/v1/*`) TEK güvenlik setiyle kurulur: (A) kimlik doğrulama ÜÇLÜSÜ — 1) API key: üretimde tek sefer görülen ham anahtar, DB'de SHA-256 hash + `cm_live_/cm_test_` prefix lookup, `hash_equals` sabit-zamanlı karşılaştırma, scope tabanlı ve varsayılan read-only, `allowed_ips`/`expires_at` destekli, rotasyon overlap penceresiyle (şema hazır, kod PLANNED); 2) JWT Bearer: ADR-010 şart 3 / ADR-011 §4.4 kilidi — `validateJwtToken` stub `null` döndüğü sürece Bearer ile oturum/erişim AÇILMAZ, API uçları da bu kurala tabidir; kalkış yalnız RFC 8725 uyumlu gerçek doğrulamadan sonra; 3) OAuth2 PKCE (authorization code + exact redirect): sosyal login'deki `saveState/validateState` mekaniğinin aynısı third-party API erişimi için PLANNED; (B) rate limit: API `RateLimitMiddleware` `ApiMiddlewarePipeline`'e KAYDEDİLİR (ADR-013 bulgusu bağlayıcı — sınıf var, kayıt yok), key = `api:sha256` | `ip:`, 60/60 varsayılan, `withLimits` ile per-route kota, edge/WAF PLANNED; (C) doğrulama: istek Respect/Validation 422 (IMPLEMENTED) + yanıt şema doğrulaması (PLANNED — OWASP API3); (D) CORS/Origin: preflight izinli header'larına `Authorization` + `X-Api-Key` eklenir, OriginCheck suffix-match + prod fail-closed korunur; (E) versiyonlama: URL `/api/v1/` = major-only + `Accept-Version` (IMPLEMENTED), `Deprecation` (RFC 9745) + `Sunset` (RFC 8594) + `410 Gone` + ~6 ay runway politikası PLANNED; (F) hata temizliği: Gateway `exception => $e->getMessage()` alanı KALDIRILIR — istemciye yalnız genel hata kodu/mesajı, detay sunucu log'una; (G) audit log: auth fail, 429, 422, origin 403, key rotasyonu olayları `log_security`/`audit_logs` tablolarına PHP yazıcıyla yazılır (şema var, kod PLANNED).**

### 2.1 Neden Bu Seçenek?

- **OWASP zorunluluğu üç kalemi birden istiyor:** API4 rate limit, API3 yanıt/hata temizliği, API1 BOLA yetki (scope) — tek bir "güvenlik seti" ADR'si olmazsa bu üçü ayrı dosyalarda dağılır ve biri diğerini ezer (§1.3 kaynak 1-6).
- **API key tek başına yetmiyor, üçlü gerçek kullanım matrisini karşılıyor:** makine/entegrasyon erişimi = API key (read-only scope), kullanıcı-temsili kısa ömür = JWT (kilidi ADR-010 şart 3 ile korumalı), üçüncü taraf uygulama girişi = OAuth2 PKCE (sosyal login mekaniği zaten kodda → yeniden kullanım).
- **Kod iskeleti zaten var, yeniden yazmak israf:** 6 middleware + pipeline + versioning (testli) + PKCE state makinesi diskte → bu ADR onları **birbirine ve Gateway'e bağlar**; alternatif "sıfırdan gateway" (§3.3) bu varlıkları çöpe atardı.
- **Bearer kilidi pazarlık dışı:** `validateJwtToken` stub `null` iken Bearer açmak = imzasız token kabulü riski; ADR-010 şart 3 ve ADR-011 §4.4 zaten kilitli — bu ADR kilidi **API yüzeyine de yazar** (kullanıcı onayı: "stub kapanana kadar Bearer oturum AÇILMAZ — API uçları da bu kurala tabi").
- **Şema-hazır-kod-yok boşluğu kapanmalı:** `api_keys` (hash+prefix+scope+expiry), `rate_limits`, `audit_logs`/`log_security` SQL'de bekliyor; PLANNED etiketi olmadan "var" sanılır → dürüst ayrım bu ADR'nin kanıt disiplini (ADR-005).

### 2.2 Teknik Detaylar

**a) Kapsam + host kararı:**

| Kalem | Karar | Kanıt |
|-------|-------|-------|
| Public API host | **`api.coremusic.net`** (tek public API host) | `shared/config/domain.php:14` |
| `public.coremusic.net` | **YOKTUR — açılmaz** (ghost host) | grep 0 (§1.1-A) |
| Uç kapsamı | Gateway `/api/v1/*`: `auth, user, music, playlist, media, download` | `Gateway.php:80-89` + `VersionRegistry.php:96-115` |
| `api.coremusic.net/` dizini | Repo kökünde YOK → deployment PLANNED | dizin listesi (yalnız assets/auth/home) |
| ADR-084 (gateway mimarisi) | Dosya diskte YOK → **düz metin, ⚠️ VERIFICATION REQUIRED** (wiki-link kurulmaz) | glob `**/ADR-084*.md` boş |

**b) Auth üçlüsü (bağlayıcı):**

| Yol | Kim kullanır | Mekanizma | Durum |
|-----|--------------|-----------|-------|
| **1) API key** | Makine/entegrasyon (sunucu→sunucu) | `X-Api-Key` → prefix ile kayıt bul → **SHA-256 hash `hash_equals`** → scope denetimi (**varsayılan `*.read` = read-only**) → `allowed_ips` + `expires_at` kontrolü | **Şema IMPLEMENTED** (`coremusic_api.sql:31-57`), **kod PLANNED** |
| **2) JWT Bearer** | Kullanıcı-temsili API erişimi | `Authorization: Bearer` → `validateJwtToken`: RFC 8725 (alg allowlist, `none` yasak, `exp/iss/aud`) + kısa access (≤15 dk) + rotasyonlu refresh + reuse→aile iptali + JWKS/kid | **KİLİTLİ** (stub `null`, `AuthenticationMiddleware.php:92-105`) — **API uçları ADR-010 şart 3'e tabi: AÇILMAZ** |
| **3) OAuth2 PKCE** | Üçüncü taraf uygulamalar (resource owner) | authorization code + PKCE (`code_verifier`/`code_challenge`) + **exact redirect** + state | Sosyal login'de **IMPLEMENTED** (`OAuthManager.php:194-238`); **third-party API erişimi PLANNED** |

**API key yaşam döngüsü (bağlayıcı):**

| Aşama | Kural |
|-------|-------|
| Üretim | Sunucuda CSPRNG ile tek sefer üretilir (`cm_live_`|`cm_test_` + 32 bayt); **ham anahtar yalnız o anda döner, DB'ye yazılmaz** |
| Saklama | `api_key_hash CHAR(64)` = SHA-256(hex), `UNIQUE` index (`coremusic_api.sql:34,48`); `api_key_prefix` UI listesi için (`:35`) |
| Doğrulama | İstekte `X-Api-Key` → prefix lookup → **`hash_equals` ile hash karşılaştırma** (sabit-zamanlı) → scope + `allowed_ips` + `is_active`/`expires_at` |
| Scope | Virgülle ayrılmış (`music.read,user.read`); **varsayılan yalnız `*.read`**; write scope'u açık onayla verilir; scope dışı uç → **403** |
| Rotasyon | Yeni key üretilir → eski key `expires_at` ile **overlap penceresi** yaşar (kesintisiz geçiş) → `is_active=0`; olay audit'e yazılır |
| Reddedilen | `md5`/düşük entropi hash, ham anahtar log'u, tek key ile sonsuz ömür |

**c) Bearer kilidi (bağlayıcı — kopyalanamaz):**

| Madde | Değer |
|-------|-------|
| Kilitten çıkan kural | **`validateJwtToken` stub `null` döndüğü sürece Bearer ile oturum/erişim AÇILMAZ — API uçları dahil** (ADR-010 şart 3 + ADR-011 §4.4 ile birebir aynı kapı) |
| Kilidin açılış koşulu | (i) gerçek imza doğrulaması (RFC 8725 allowlist) · (ii) `exp/iss/aud` kontrolü · (iii) QA bypass testi (`shared/tests` Bearer 401) · (iv) **ayrı onay** (Tech Lead) |
| Kilitten önce geçerli olan | API key yolu (1) ve sosyal OAuth (3) — yani erişim boş kalmaz, **yalnız imzasız Bearer kapanık** |
| Kilit ihlali | Layer/security violation → revert + log ERROR (`AGENTS.md` §17) |

**d) Güvenlik seti bileşen tablosu:**

| # | Bileşen | Karar | Durum (kod) |
|---|---------|-------|-------------|
| 1 | **Rate limit** | `RateLimitMiddleware` → `ApiMiddlewarePipeline->pipe()` **kaydı zorunlu**; key `api:sha256` \| `ip:`; 60/60; `withLimits` per-route (şema `rate_limits`); 429 + `Retry-After` | Sınıf **IMPLEMENTED**, **kayıt PLANNED** (ADR-013 şartı) |
| 2 | **İstek doğrulama** | Respect/Validation kuralları route bazlı; başarısız → **422** + alan hataları | **IMPLEMENTED** (`RequestValidationMiddleware.php:15-16,51`) |
| 3 | **Yanıt doğrulama** | Yanıt şeması doğrulaması (OWASP API3) — aksi ispatlanana kadar veri sızdırmaz | **PLANNED** (bugün yalnız header/ETag) |
| 4 | **CORS** | Preflight izinli header'larına **`Authorization` + `X-Api-Key` eklenir**; `allow_credentials` + origin echo korunur ama allowlist suffix-match'e güvenir | Header genişlemesi **PLANNED**, OriginCheck **IMPLEMENTED** (prod fail-closed `:73-76`) |
| 5 | **Versiyonlama** | URL `/api/v1/` **major-only** (minor URL'de görünmez); `Accept-Version` header opsiyonel; **`Deprecation` (RFC 9745) + `Sunset` (RFC 8594) + `410 Gone` + ~6 ay runway** | Resolver/Registry **IMPLEMENTED + testli**; **politika/header PLANNED** |
| 6 | **Hata temizliği** | `Gateway.php:60-67` **`exception` alanı kaldırılır**: istemciye `INTERNAL_ERROR` + genel mesaj + hata ID'si; `$e->getMessage()` yalnız sunucu log'una | **PLANNED** (bugün sızıyor) |
| 7 | **Audit log** | Olay kümesi: auth fail · 429 · 422 · origin 403 · key üretimi/rotasyonu/iptali · scope ihlali → `log_security`/`audit_logs` (PHP yazıcı) | Şema **IMPLEMENTED**, **yazıcı PLANNED** |

**e) Hata + log akışı (bağlayıcı):**

```
İstemci → Gateway::dispatch → middleware zinciri (kayıt §d-1)
  ├─ 4xx (401/403/409/422/429): genel mesaj + kod + Retry-After (429)  → audit olayı (§d-7)
  └─ 5xx: INTERNAL_ERROR + "hata ID" (istemci)  ⎫
          $e->getMessage() yalnız sunucu log'una (Log\FileHandler)      ⎭ Gateway.php:60-67 düzeltmesi
```

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Yalnız API key** (JWT/OAuth2 yok) | Tek mekanizma, en az kod; şema hazır | Kullanıcı-temsili erişim ve üçüncü taraf giriisi karşılanmaz; key sızınca tek başına tam yetki; kısa ömür/ölçüm (kime ait) yok | Üç gerçek erişim senaryosu var (makine/kullanıcı/3. taraf — §2.2b); scope read-only bile olsa rotasyon+audit olmadan key = kalıcı sır |
| 2 | **Tam stateless JWT** (API key yok, cookie/session API'den tamamen çıkar) | Homojen API kimliği; ölçek | **ADR-010 synchronizer + ADR-011 hibrit ile çelişir** (revocation, "hepsini sonlandır" imkânsız); **bugün stub `null` → kapı bile değil**; entegrasyonculara token verme yolu olmaz | ADR-011 §alternatif 2 ile aynı ret gerekçesi + JWT kilidi bu ADR'de pazarlık dışı |
| 3 | **Hazır API gateway** (Kong / Tyk / APISIX) | Rate limit/auth/audit hazır; operasyonel olgunluk | Yeni altyapı bağımlılığı (deployment + öğrenme maliyeti); ADR-009 ile `.conf`/deploy artefaktı 0 gerçekliği; mevcut 6 middleware + testli versioning çöpe | Mevcut iskelet kodda (§1.1-B) ve `shared/AGENTS.md` "framework bağımlılığı" yasağı yönünde; edge katmanı zaten ADR-013'te PLANNED olarak ayrı hat |
| 4 | **Mevcut hâliyle bırak** (kayıtsız middleware, stub, sızan hata) | Sıfır efor | `/api/v1/*` uçları korumasız + OWASP API3/API4 ihlali + audit yok | **Kabul edilemez** — bu ADR'nin doğuş sebebi; ADR-013 "pasif kod" bulgusu tek başına yeterli gerekçe |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek karar, yedi bileşen:** auth/rate limit/doğrulama/CORS/versiyon/hata/audit tek sözleşmede → ADR-010/011/013/015 ile çatışma noktaları §1.4'te yazılı, yeniden tartışma yok.
- **Şema-hazır kod-bağlı:** `api_keys`+`rate_limits`+`audit_logs` SQL'i beklemekten çıkar; prefix+hash+scope deseni literatürle (§1.3) birebir.
- **Erişim hiç kapanmaz:** Bearer kilidi açıkken API key (1) ve OAuth2 PKCE (3) yolları devrede → "her şey kilitli" değil, **yalnız imzasız token kilitli**.
- **OWASP hizası:** API4 (kayıtlı rate limit), API3 (temiz hata + yanıt şeması), API1 (scope yetkisi) tek hamlede hedeflenir.
- **Test kapısı hazır:** versioning zaten testli (`VersioningTest` 6 test); doğrulama/audit testleri §5.1'e eklenebilir.

### 4.2 Olumsuz Sonuçlar

- **PLANNED yükü büyük:** API key doğrulama, pipeline kaydı, JWT, audit yazıcı, CORS header, hata gizleme, yanıt şeması = **7 kod işi** (§5.1); yapılmazsa ADR kâğıtta kalır.
- **İki `api_keys` şeması:** `coremusic_api` vs `coremusic_auth` — SSOT kararı gecikirse doğrulama kodu yanlış tabloya yazılabilir (⚠️ §4.3 risk 6).
- **Overlap rotasyon = geçici iki geçerli key:** hırsızlık anında pencere içinde eski key hâlâ yaşar (bilinçli kabul, süre sınırlı).
- **Suffix-match origin:** `str_ends_with` genişlemesi (`.coremusic.net`) alt-alan adı yüzeyini büyütür — devralınan risk, test ister.
- **Manuel onay bekliyor:** debate ✅ TAMAMLANDI (19/1/0) + Tech Lead ✅ → **Arch Lead ⏳** olmadan frozen yapılmaz (§7); 3 şart §5.4 tamamlanmadan frozen yok.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Pasif kod yanılgısı** — middleware'ler "var" sanılıp kaydedilmez, public API korumasız açılır | 4 (çok olası) | 4 (yüksek) | §d-1 kayıt zorunlu + test: pipeline'da auth/rate-limit yoksa test kırmızı; deployment gate (§5.1 adım 6) |
| **Bearer kilidi erken açma** — stub `null` iken Bearer devreye alınır | 3 (olası) | 5 (kritik) | §2.2c dört koşul + QA bypass testi; kilidi ihlal → revert + log ERROR (ADR-010 şart 3) |
| **Hata sızıntısı sürerse** sınıf/yol/DB bilgisi istemciye sızar | 4 (çok olası) | 3 (orta) | §d-6 Gateway düzeltmesi tek noktada (`:60-67`); derleme-review'da `getMessage()` grep kapısı |
| **API key tek başına yetki** (scope write verilirse / `hash_equals` yoksa) | 3 (olası) | 4 (yüksek) | Varsayılan read-only + `hash_equals` zorunlu (§b); write scope açık onay + audit |
| **İki `api_keys` şeması (SSOT belirsiz)** | 3 (olası) | 3 (orta) | §5.1 adım 0: SSOT kararı (⚠️ VERIFICATION REQUIRED) → doğrulama kodu tek tabloya bağlanır |
| **Audit yazıcı yoksa** brute force/origin ihlali iz bırakmaz | 4 (çok olası) | 3 (orta) | §d-7 olay kümesi + §5.1 adım 7; şema hazır, kod için süre |
| **CORS header eksikliği** → legaytim Bearer/API key istekleri preflight'te düşer | 3 (olası) | 2 (düşük) | §d-4 header listesi genişlemesi + preflight testi |
| **`getenv` ihlali (`OAuthManager:77-78`)** secret'ı mantık katmanında tutar | 3 (olası) | 4 (yüksek) | ADR-015 tek kapı kuralı → DI `Config`'e taşıma (§5.1 adım 8) |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-010-csrf-protection-strategy]] | **Şart 3** — JWT stub kapanana kadar cookie-auth tek yol; Bearer muafiyeti yalnız gerçek imza doğrulamasından sonra → bu ADR §2.2c kilidi |
| [[ADR-011-session-management]] | §4.4 "Bearer ile oturum açma AÇILMAZ" + hibrit saklama → API yüzeyinde aynı kapı |
| [[ADR-013-rate-limiting-apcu]] | §1.1 "API RateLimitMiddleware pipeline kaydı bulunamadı" → bu ADR §d-1 kaydı şart koşar; algoritma/kota çerçevesi ADR-013'ün (tekrar yok) |
| [[ADR-015-env-parser-strategy]] | `getenv` yasağı → `OAuthManager:77-78` ihlali §4.3 risk 8; secret'lar DI Config'den |
| [[ADR-002-pdo-mandatory-no-orm]] | API key lookup/rotasyon/audit yazımı yalnız PDO prepared statement (§1.4) |
| [[ADR-005-ultrathink-protocol]] | `⚠️ VERIFICATION REQUIRED` standardı — pipeline kaydı 0, API key kodu 0, audit PHP 0, ADR-084 yok, `public.coremusic.net` 0 (§1.1-B) |
| [[ADR-009-clean-url-redirect]] | `.conf`=0 → edge rate limit/WAF PLANNED (§d-1) |
| [[ADR-016-url-normalization]] | `/api/v1/*` yolu normalizasyonu ile çakışma kontrolü (§1.4) |
| [[../index]] | Satır 57 `[[ADR-020-api-public-security]]` — slug eşleşmesi ✅ (bu dosya rezervasyonu doldurur) |
| [[../../index.md]] | Satır 637 `decisions/accepted/ADR-020-api-public-security` kaydı ✅ |
| [[../../keys.md]] | Satır 255 `ADR-020 \| API public, guvenlik \| Security` ✅ |
| [[../../brain.md]] | Satır 975 `ADR-020 \| API güvenlik stratejisi` ✅ · §6 middleware pipeline sırası (satır 256, 438) |
| [[../../.templates/adr/adr-index.md]] | Satır 91 `20 \| ADR-020 \| API güvenlik stratejisi \| 🔵 backend \| adr-security-template.md ⚠️` ✅ |
| [[../../.templates/adr/adr-security-template.md]] | SEC-04/SEC-09/SEC-12 (ownership/403, hata gizleme, RBAC) ADR-020'ye bağlı (`:388,393,396`) |
| `.ai/.sql/mysql/coremusic_api.sql` | `api_keys:31-57` (hash+prefix+scope+expiry) · `rate_limits:63-82` · `api_calls:92-119` (§1.1-A, §2.2b) |
| `.ai/.sql/mysql/coremusic_auth.sql` | İkinci `api_keys:284-311` + token/credential enum `:146,178` → **SSOT çelişkisi ⚠️** (§4.3 risk 6) |
| `.ai/.sql/mysql/coremusic_logs.sql` | `audit_logs:19-40` · `rate_limit_logs:124-142` · `log_security:483-528` (§d-7) |
| `shared/src/Api/Gateway.php` | `:60-67` hata sızıntısı · `:80-89` 6 rota · `:105-114` placeholder · `:9` ADR-084 (dosya yok ⚠️) |
| `shared/src/Api/Middleware/*` (6 dosya) | RateLimit `:22-23,39-46,57-68,73-79` · Auth stub `:37-57,73-78,92-105` · Validation `:15-16,51` · Response `:45-47,53-79` · Pipeline `:24-40` · Authorization (kayıtsız) |
| `shared/src/Middleware/CorsMiddleware.php` · `OriginCheckMiddleware.php` | `:32` header listesi (Authorization/X-Api-Key yok) · `:48-52` echo+credentials · OriginCheck `:47-54` 403, `:68` suffix-match, `:73-76` prod fail-closed |
| `shared/src/Api/Versioning/*` + `shared/tests/Api/VersioningTest.php` | Enum `:17-23` · Resolver `:34-35,70-72` · Registry `:96-115` · **6 test** (§d-5) |
| `shared/src/OAuth/OAuthManager.php` | `:77-78` getenv ihlali · `:90-102` 10 sağlayıcı · `:194-238` PKCE state · `:242-261` AES-256-GCM (§2.2b-3) |
| `shared/config/domain.php` | `:7-14` 7 subdomain, `api.coremusic.net` `:14`; `public.coremusic.net` grep 0 (§2.2a) |
| `shared/src/PageRouter/PageRouterKernel.php` | `:241-247` yalnız web stack `pipe()` · `:267-282` web middleware listesi (API middleware yok) → §1.1-B.1 kanıtı |
| [[../../AGENTS.md]] | §6 routing (`CSRF, CSP, XSS, OWASP, auth, security, rate limit → Security Engineer`), §5 `Security middleware → Security Engineer`, §17.7 layer violation, §25.3 frozen |
| [[../../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16, REDACTED |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 0 | **SSOT kararı:** `coremusic_api.api_keys` vs `coremusic_auth.api_keys` — hangisi tek doğrulama tablosu (⚠️ §4.3 risk 6); karar `log.md`'ye append | Data Engineer + Vault Steward | 0.5 oturum |
| 1 | **Pipeline kaydı (kritik):** `Gateway` + `ApiMiddlewarePipeline`'e sıra ile `Authentication → Authorization → RateLimit → RequestValidation → ResponseNormalization` pipe edilir (sıra `brain.md:256` §6 ile hizalı); runtime giriş noktası (`new Gateway`) bağlanır; **kayıtsız uç = test kırmızı** | Backend Architect + Security Engineer | 1.5 oturum |
| 2 | **API key doğrulama:** `X-Api-Key` → prefix lookup → SHA-256 `hash_equals` → scope (varsayılan read-only) → `allowed_ips`/`expires_at`; rotasyon (overlap) uçları + audit olayı | Security Engineer + Backend Architect | 2 oturum |
| 3 | **Hata temizliği:** `Gateway.php:60-67` `exception` alanı kaldırılır → `INTERNAL_ERROR` + genel mesaj + hata ID; `$e->getMessage()` yalnız `Log\FileHandler`'a | Backend Architect | 0.5 oturum |
| 4 | **CORS header genişlemesi:** `allowed_headers`'a `Authorization` + `X-Api-Key` (`CorsMiddleware.php:32`); preflight testi (Bearer/API key'li OPTIONS) | Security Engineer | 0.5 oturum |
| 5 | **Versiyon politikası:** major-only kuralı + breaking change tanımı + `Deprecation`/`Sunset` header'ları + `410 Gone` (path zaten IMPLEMENTED; `VersionResolver`'a header eşlemesi genişletilir) | Backend Architect | 1 oturum |
| 6 | **Deployment gate:** `api.coremusic.net` yok → host hazır olmadan public uç **açılmaz**; edge rate limit (`.conf`) ADR-013/009 PLANNED artefaktı olarak üretilir | DevOps Engineer | 1 oturum |
| 7 | **Audit yazıcı:** §d-7 olay kümesi → `log_security`/`audit_logs` (PDO prepared); olay tipi enum'u şema ile eşleşir | Security Engineer + Data Engineer | 1.5 oturum |
| 8 | **`getenv` temizliği:** `OAuthManager.php:77-78` → DI `Config` servisi (ADR-015); secret testte sahte config ile | Security Engineer | 0.5 oturum |
| 9 | **Yanıt şema doğrulaması (PLANNED son adım):** route bazlı yanıt şeması kontrolü (API3); başarısız şema → 5xx + log (asla veri dönmez) | Backend Architect + QA Engineer | 1.5 oturum |
| 10 | **Test paketi:** pipeline'da auth/rate-limit yoksa kırmızı · API key (geçersiz hash, süresi dolmuş, scope dışı → 401/403) · Bearer stub iken **401** (kilidi doğrular) · 429 + `Retry-After` · 422 · origin 403 · 500'de `exception` alanı **YOK** · audit satırı yazıldı | QA Engineer | 2 oturum |
| 11 | **Vault senkronu:** `.ai/.decisions/index.md:57` satırı bu dosya ile canlanır; debate tamamlanınca §5.3/§7.1 + frontmatter `debate` güncellenir; broken-link varsa `broken-links-report.md` append | Vault Steward | 0.5 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** yazılır (`ADR-088+` serisi), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı).
2. **Pipeline kaydı (adım 1):** `pipe()` listesinden çıkarma tek commit → API uçları **fail-closed**: auth yoksa public uçlar zaten host'a kapalı (adım 6 gate'i) → en kötü durum "uç yok", "korumasız uç yok".
3. **API key (adım 2):** anahtarlar DB'de kalır; kod geri alınırsa key doğrulama devre dışı, **kalan tek katman = rate limit + OriginCheck** → yalnız yeni ADR ile tamamen kaldırılabilir (güvenlik düşüşü → L2/L3 eskalasyonu).
4. **Bearer kilidi (§2.2c):** kilit **kaldırılamaz** — yalnız dört koşul tamamlanınca ve Tech Lead onayıyla *açılır*; acil durumda geri dönüş = stub'ı `null`'a döndürmek (tek satır) → her şey 401.
5. **Hata temizliği (adım 3):** `exception` alanı geri eklenemez (güvenlik gerilemesi); log'da detay zaten var → geri dönüş yalnız yeni ADR.
6. **CORS header (adım 4):** header listesi daraltılabilir (Bearer API key'i preflight'te düşer → mobil/native istemciler etkilenmez, tarayıcı etkilenir) → anında geri dönüş.
7. **Audit yazıcı (adım 7):** yazıcı kapanırsa olay kaybı geri alınamaz → önce log-only faz, sonra enforce (AYNI ADR-010 rollout mantığı).
8. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (geçmiş satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona / 19 kabul · 1 çekimser · 0 red → **KABUL** (frontmatter `debate` alanıyla aynı) |
| Karar içeriği | Kullanıcı onaylı kapsam: **genel API güvenlik seti · auth üçlüsü (API key hash+scope+rotasyon · JWT Bearer kilidi · OAuth2 PKCE PLANNED)** · ADR-013 pipeline-kayıt bulgusu bağlayıcı · "PLANNED ise dürüst yaz" |
| Biçim | ADR-004/008/010-019 formatı — 3 tur / 20 persona ✅ uygulandı |
| Tur 1 (kanıt sunumu) | 20 persona §1.1 A-B + §1.3 üzerinden oy kullandı: **şema IMPLEMENTED** (`coremusic_api.sql:31-57` hash+prefix+scope+expiry), 6 middleware + Gateway + `VersioningTest` 6 test, CORS/OriginCheck fail-closed; **PLANNED: pipeline kaydı 0 (`pipe()` 0), API key doğrulama PHP'de 0, audit 0, CORS header'larında `Authorization`/`X-Api-Key` yok (`CorsMiddleware:32`), Gateway hata sızıntısı (`Gateway.php:60-67`)**; kilit: `validateJwtToken` stub `null` → Bearer AÇILMAZ (ADR-010 şart 3); ⚠️ çift `api_keys` şeması (`coremusic_api.sql` vs `coremusic_auth.sql:284-311`) SSOT çelişkisi. **Sonuç: 16 kabul/neutral, 4 uyarı**; Critic: CORS eksik başlıklar + Gateway sızıntı. |
| Tur 2 (itiraz → çözüm) | 4 itiraz, 4 çözüm → §5.4 şart 1a-1d: (1) çift `api_keys` şeması → tek SSOT `coremusic_auth.sql`, `coremusic_api.sql` superseded → **1a**; (2) Gateway hata sızıntısı → 500'de stack trace yasak, şema hatası [[ADR-005-ultrathink-protocol]] redaction → **1b**; (3) pipeline kaydı 0 → 6 middleware + Gateway pipeline'a kaydedilir → **1c**; (4) CORS eksik başlıklar → `Authorization`/`X-Api-Key` expose + allowed headers → **1d**. |
| Tur 3 (oy) | **19 kabul / 1 çekimser / 0 red → KABUL**; 3 şart bağlayıcı eklendi (§5.4) |
| Kural | Debate **tamamlandı**; sonuç §5.4/§7.1'e ve frontmatter `debate` alanına işlendi, `.ai/log.md` append ile kaydedildi |

### 5.4 Debate Şartları (bağlayıcı — 3 şart)

| # | Şart | Kapsam / Bağlantı | Sorumlu |
|---|------|-------------------|---------|
| **1** | **4 kod açığı kapatılır (1a-1d)** | Aşağıdaki alt maddeler | Security Engineer + Backend Architect |
| 1a | Çift `api_keys` şeması → **tek SSOT: `coremusic_auth.sql`** (`:284-311`); `coremusic_api.sql:31-57` `superseded` işaretlenir; doğrulama kodu tek tabloya bağlanır | §5.1 adım 0 · §4.3 risk 6 | Data Engineer + Vault Steward |
| 1b | **Gateway hata sızıntısı:** 500'de stack trace / `getMessage()` yasak — istemciye yalnız `INTERNAL_ERROR` + genel mesaj + hata ID; şema hatası dahil detay [[ADR-005-ultrathink-protocol]] redaction ile sunucu log'una | §5.1 adım 3 · `Gateway.php:60-67` | Backend Architect |
| 1c | **Pipeline kaydı 0 →** 6 middleware + `Gateway` `ApiMiddlewarePipeline`'e `pipe()` ile kaydedilir; kayıtsız uç testi kırmızı | §5.1 adım 1 · ADR-013 bulgusu | Backend Architect + Security Engineer |
| 1d | **CORS eksik başlıklar →** `CorsMiddleware.php:32` allowed-headers'a `Authorization` + `X-Api-Key` eklenir + `Access-Control-Expose-Headers` ile yanıt başlıkları açılır; preflight testi (Bearer/API key'li OPTIONS) | §5.1 adım 4 | Security Engineer |
| **2** | **API key doğrulama + audit PHP implementasyonu** (şema hazır: `api_keys` hash+prefix+scope+expiry · `audit_logs`/`log_security`/`rate_limit_logs`) — prefix lookup → SHA-256 `hash_equals` → scope (varsayılan read-only) → `allowed_ips`/`expires_at`; olaylar PHP yazıcıyla audit tablolarına | §5.1 adım 2 + adım 7 · §2.2b · §d-7 | Security Engineer + Backend Architect |
| **3** | **OWASP API Top 10 test paketi** — BOLA (nesne erişim yetkisi/scope ihlali) · auth (API key geçersiz/süresi dolmuş/scope dışı → 401/403 · Bearer stub iken 401) · rate limit (429 + `Retry-After`) senaryoları | §5.1 adım 10 | QA Engineer |

**Şartların bağlayıcılığı:** 3 şart da tamamlanmadan bu ADR **frozen yapılmaz**; her şart §5.1 ilgili adımıyla eşleştirilmiştir (adım 0/1/2/3/4/7/10).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 57** `[[ADR-020-api-public-security]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`OWASP, auth, security, rate limit → Security Engineer`), §5 domain boundary, §17.5/§17.7 edge case |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | Satır 975 ADR-020 kaydı ✅ · §6 middleware pipeline sırası (256, 438) · ADR-013 60/60 (968) · ADR-011 timeout (870) |
| [[../../keys.md]] | Satır 255 `ADR-020 \| API public, guvenlik \| Security` ✅ |
| [[../../index.md]] | Satır 637 ADR-020 kaydı ✅ |
| [[../../glossary.md]] | Terim sözlüğü (API key, scope, PKCE, JWKS, Deprecation/Sunset — ekleme ADR-020 uygulamasıyla) |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate sonucu | §5.3/§5.4 — **✅ KABUL (3 tur / 20 persona, 19/1/0)** + 3 bağlayıcı şart |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[../../.templates/adr/adr-security-template.md]] | SEC-04/09/12 bu ADR'ye bağlı (`:388,393,396`) |
| [[ADR-010-csrf-protection-strategy]] · [[ADR-011-session-management]] | Bearer/Bearer kilidi kapıları (dosyalar diskte VAR ✅) |
| [[ADR-013-rate-limiting-apcu]] | Pipeline kaydı bulgusu + kota çerçevesi (dosya diskte VAR ✅) |
| [[ADR-015-env-parser-strategy]] | `getenv` yasağı (dosya diskte VAR ✅) |
| [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-005-ultrathink-protocol]] · [[ADR-009-clean-url-redirect]] · [[ADR-016-url-normalization]] | PDO / doğrulama / edge sınırı / URL normalizasyonu (dosyalar diskte VAR ✅) |
| Düz metin (dosyalar diskte YOK — wiki-link KURULMAZ): ADR-084 | `@see ADR-084-api-gateway-architecture` (`Gateway.php:9`, `RateLimitMiddleware.php:9`) — glob boş → **⚠️ VERIFICATION REQUIRED** |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-020'yi sıfırdan yaz"; karar içeriği onaylı: kapsam = genel API güvenlik seti, auth üçlüsü, ADR-013 bulgusu, dürüst PLANNED) | 2026-09-25 | ✅ |
| Tech Lead | Debate onaylı (3 tur / 20 persona — 19 kabul / 1 çekimser / 0 red → KABUL, 3 şart §5.4) | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010-019 formatı — 3 tur / 20 persona ✅ uygulandı |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** — frontmatter `debate` ile aynı |
| Karar içeriği onayı | Kullanıcı onaylı: **genel API güvenlik seti · API key (hash + scope read-only + rotasyon) · JWT Bearer kilidi (ADR-010 şart 3 — API uçları da tabi) · OAuth2 PKCE PLANNED · ADR-013 pipeline-kayıt bulgusu bağlayıcı · "PLANNED ise dürüst yaz"** |
| Kanıt durumu | §1.1: şema/vault **IMPLEMENTED**, 6 sınıf + Gateway + versioning (testli) **IMPLEMENTED**, pipeline kaydı/API key doğrulama/JWT/audit yazıcı/CORS header/hata gizleme/yanıt şeması **PLANNED**; `⚠️ VERIFICATION REQUIRED`: iki `api_keys` SSOT (şart 1a ile çözüldü), ADR-084 dosyası yok, `public.coremusic.net` 0 |
| Şartlar | **3 şart §5.4'e işlendi:** (1) 4 kod açığı 1a-1d (SSOT tekilleştirme · Gateway hata temizliği · pipeline kaydı · CORS başlıkları), (2) API key doğrulama + audit PHP implementasyonu, (3) OWASP API Top 10 test paketi (BOLA/auth/rate limit) |
| Frozen | Debate ✅ + Tech Lead ✅ · **Arch Lead ⏳** → §7'nin üç satırı da ✅ olmadan Active/Frozen olmaz (şablon §4.2) |
| Kural | Debate tamamlandı; sonuç §5.3/§5.4/§7.1'e ve frontmatter `debate` alanına işlendi, `.ai/log.md` append ile kaydedildi |

---

*ADR-020 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-020 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
*Last Updated: 2026-09-25*

*ADR-020 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL) · Tech Lead ✅ · Arch Lead ⏳ · frozen YOK*
