---
title: "CoreMusic — ADR-058: Merkezi Auth Servisi (auth.coremusic.net TEK auth noktası + servisler-arası güven + yüksek erişilebilirlik + kademeli migrasyon)"
type: "architecture-decision"
category: "security"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic merkezi auth kararı: (a) `auth.coremusic.net` TEK auth noktası — tüm subdomain'ler kimlik için oraya gider, (b) servisler-arası güven = server→server `POST /validate-key` + (kilit açıldığında) imzalı JWT/JWKS — servis kendi session'ını KURMAZ, (c) erişilebilirlik = auth down ise **fail-closed** (yeni kimlik doğrulama reddedilir), mevcut oturum için **sınırlı servis-içi son-şans okuma** — fail-open YOK, (d) migrasyon = mevcut dağınık auth'dan tek noktaya 5 fazlı kademeli geçiş (ADR-043 planı ile), (e) sınır = ADR-043/047/052/056/039/011'i uygular, yeniden karar vermez"
kaynak: "Disk/kod kanıtı taraması (2026-09-29: auth.coremusic.net vendor-dışı 69 dosya/42 PHP/343.848 B → OriginCheckMiddleware.php:35 validate-key origin muafiyeti → HomeAuthBridge.php:128 doğrudan HTTP POST → shared/src/Config/CLAUDE.md:47-55 9 subdomain servis tablosu → .coremusic.net cookie literal 13 PHP dosyası + 1 JS dosyası 3 satır → SessionBootstrapper::ensureStarted 4 ayrı noktada → auth 13 / home 2 / api 3 / shared 48 auth-session dosyası) + web araştırması (17 sorgu / ~30 adlandırılmış kaynak)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-058: Merkezi Auth Servisi (tek auth noktası + servisler-arası güven + HA + migrasyon)

> **Durum:** ✅ **ACCEPTED** — **Tarih:** 2026-09-29 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-058-centralized-auth-service`
> **İlgili kararlar:** [[ADR-043-auth-subdomain-consolidation]] (subdomain/cookie konsolidasyonu — bu ADR onun **kapsam (a)+(b)** kararını yazar) · [[ADR-047-login-redirect-session-bridge]] (imzalı köprü token'ı + `/validate-key` akışı) · [[ADR-052-hybrid-auth-session-jwt]] (hibrit session/JWT zemini) · [[ADR-056-auth-module-implementation]] (RBAC + Permission middleware — authz bu ADR'nin **kapsamı değil**) · [[ADR-039-7-service-platform-architecture]] (11 servis + **servis↔servis doğrudan HTTP yasağı**) · [[ADR-011-session-management]] (session yaşam döngüsü + cookie domain) · [[ADR-020-api-public-security]] (API auth üçlüsü + Bearer kilidi) · [[ADR-010-csrf-protection-strategy]] · [[ADR-012-csp-nonce-strict-dynamic]] · [[ADR-013-rate-limiting-apcu]] · [[ADR-008-bypass-auth-middleware]] · [[ADR-004-multi-domain-spa]] · [[ADR-007-cache-namespace]] · [[../index.md]] · [[../../CLAUDE.md]] · [[../../brain.md]]
> **Index durumu:** `.ai/.decisions/index.md` **ADR-051–ADR-060 satırlarını İÇERMEZ** — dizin 050 (satır 91) → 061 (satır 92) arasında **atlıyor** (bu işlemde yeniden doğrulandı). Bu işlemde index.md'ye **yeni satır eklenmedi** (report-only — In-Place Refactoring + SRP); satır ekleme **bir sonraki vault reset'ine ertelenmiştir** (§5.1 adım 8).
> **Kaynaksız numara boşlukları:** **ADR-051, ADR-053, ADR-054, ADR-055, ADR-057, ADR-059, ADR-060** diskte dosya olarak **YOK** (`Test-Path = False`). Bu ADR o numaraları **doldurmaz**; yalnızca sınır koyar. **ADR-059 (MFA)** bu ADR'nin **kapsam dışıdır**.
> **⚠️ VERIFICATION REQUIRED:** (i) `brain.md`'de ADR-058 slotu **YOK** (grep = 0) — özeti MO (vault-updater) ekleyecektir. (ii) `.ai/architecture/08-auth/auth-domain.md` ve `auth-flow.md` **diskte YOK** (`Test-Path = False`) — `auth.coremusic.net/AGENTS.md` §5 bu iki dosyayı referanslıyor (kırık referans, bu ADR düzeltemez). (iii) ADR-059 için disk kanıtı yok.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; bu dosya **yeni** bir accepted ADR'dir (numara, arşiv/üst görevin atadığı ADR-058 slotunu doldurur — kural 4'teki ADR-088+ aralığı bu seriyle çelişir; **numara çakışması raporlanır, düzeltilmez**, §5.1 adım 7).

---

## 1. Bağlam (Context)

CoreMusic'te kimlik zaten **tek fiziksel serviste** toplanmış durumda: `auth.coremusic.net/` (vendor-dışı **69 dosya / 42 PHP / 343.848 bayt**) login, register, reset, gender, logout ve `validate-key` uçlarını üretiyor. Buna rağmen **"tek auth noktası" iddiası kodla birebir örtüşmüyor**: oturum başlatma yetkisi tek yerde değil, **dört ayrı noktada** dağıtılıyor; servisler-arası doğrulama tek yol değil; ve `HomeAuthBridge` sunucu tarafından auth'a **doğrudan HTTP** atarak ADR-039'un "servis↔servis doğrudan HTTP yasak" kuralını **çözülmemiş bir gerilim** olarak taşıyor. Bu karar, (a) tek auth noktası, (b) servisler-arası güven, (c) yüksek erişilebilirlik, (d) kademeli migrasyon dört başlığı **tek yerde** bağlayarak bu gerilimleri kapatır — veya kapatılamayanı dürüstçe işaretler.

### 1.1 Mevcut Durum

**Tablo A — `auth.coremusic.net` envanteri (IMPLEMENTED, ölçüm 2026-09-29)**

| Katman | Kanıt | Ölçü | Durum |
|--------|-------|------|-------|
| Envanter (vendor dışı) | `Get-ChildItem -Recurse -File` (`\vendor\` hariç) | **69 dosya · 42 PHP · 343.848 bayt** | ✅ IMPLEMENTED |
| Composer | `auth.coremusic.net/composer.json` | `php >=8.4`, `php-di ^7.0`, `nikic/fast-route ^1.3`, `nyholm/psr7(-server)`, `vlucas/phpdotenv ^5.7`, `coremusic/shared-infrastructure ^2.0` (**path repo `../shared`, symlink**), `phpunit ^10.5` (dev) | ✅ IMPLEMENTED |
| PSR-4 | `composer.json` → `CoreMusic\Auth\` = `include/` | tek namespace | ✅ IMPLEMENTED |
| Front controller | `auth.coremusic.net/index.php` (9.763 B) | `RuntimeBootstrap::boot` + `PageRouterKernel` (shared) + `AuthController` + 3 handler + `SessionBootstrapper` | ✅ IMPLEMENTED |
| Rotalar | `index.php:68` → `/health`, `/session`, `/validate-key` **middleware zinciri dışına alınıyor**; `:96` → `'/validate-key' => $controller->handleValidateKey(...)` | 3 uç + controller rota | ✅ IMPLEMENTED |
| Ek rota dosyaları | `routes/oauth.php` (1.395 B), `shared/config/auth-routes.php` (1.687 B) | — | ✅ IMPLEMENTED |
| Hexagonal katman | `include/Container/` · `Controller/` · `Domain/{Entity,ValueObject,DTO}` · `Repository/` · `Service/` · `Middleware/` · `Handler/` | Entity 1 · VO 4 · DTO 3 · Service 2 · Middleware 6 · Handler 3 | ✅ IMPLEMENTED |
| Sayfalar | `pages/` 7 dosya (login 13.881 B · register 21.555 B · forgot 6.861 B · reset 7.906 B · logout 5.419 B · select-gender 10.351 B · set-gender 9.023 B) | 7 | ✅ IMPLEMENTED |
| Test | `tests/Unit/` 4 dosya (DTO/Entity/VO) + `phpunit.xml` | **4 test dosyası** — service/controller/middleware testi **YOK** | ⚠️ **PARÇALI** |
| CORS | `config/cors.php:11` → `$_ENV['CORS_ALLOWED_ORIGINS']` | boş env → **boş allowlist** | ⚠️ **AÇIK** (ADR-043 E2) |

**Tablo B — `/validate-key` origin muafiyeti (ADR-047/043 bulgusu — yeniden doğrulandı)**

| İddia | Kanıt | Durum |
|-------|-------|-------|
| `/validate-key` Origin denetiminden **MUAF** | `auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php:35-37` → `in_array($uri, ['/health','/session','/validate-key'], true)` → **`return $next($request)`** | ✅ **DOĞRULANDI (AÇIK — E4)** |
| Aynı muafiyet `index.php`'de de var | `auth.coremusic.net/index.php:68` — 3 uç `PageRouterKernel`'in origin adımının **dışında** | ✅ **DOĞRULANDI** |
| Muafiyet birim testi `RateLimiterMiddleware`'te de tekrarlanıyor | `shared/src/Middleware/RateLimiterMiddleware.php:29` → `'/validate-key'` | ✅ DOĞRULANDI (rate limit açısından da ayrı yol) |
| **Yeni bulgu:** `isAllowed()` tanımsız sabit kullanıyor | `OriginCheckMiddleware.php:66` → `foreach (self::ALLOWED_ORIGINS as ...)` — **88 satırlık dosyada `ALLOWED_ORIGINS` sabit tanımı grep = 0**; tanımlı olan `$this->allowedOrigins` (:14, :18) **kullanılmıyor** | ⚠️ **YENİ BULGU** — origin/referer **varsa** `isAllowed` çağrısında PHP `Error` (tanımsız sınıf sabiti) → 500; **muafiyet sayesinde `/validate-key` bu yola hiç girmiyor** |
| **Boş-Origin fail-open (ADR-043 E1)** | `OriginCheckMiddleware.php:40` → `if ($origin !== '')` yalnızca origin **varsa** kontrol eder; `:51` referer **varsa**; ikisi de yoksa `:60` → **`return $next($request)`** (reddetme yok) | ✅ **DOĞRULANDI (AÇIK)** |

**Tablo C — `HomeAuthBridge` ↔ ADR-039 doğrudan-HTTP gerilimi (yeniden doğrulandı)**

| İddia | Kanıt | Durum |
|-------|-------|-------|
| **Adres** | `home.coremusic.net/include/Auth/HomeAuthBridge.php` — `final class HomeAuthBridge` (:20), DI kaydı `home.coremusic.net/include/Container/HomeContainer.php:42`, kullanım `home.coremusic.net/config/bootstrap.php:73` | ✅ IMPLEMENTED |
| **Doğrudan çağrı var mı?** | **VAR.** `HomeAuthBridge.php:128` → `$url = $this->authUrl . '/validate-key';` · `:132` → `curl_init($url)` · `:130` → `['auth_key' => $authKey]` · `:16,47` doküman yorumu "auth.coremusic.net/validate-key'e POST atar" | ✅ **DOĞRULANDI — doğrudan HTTP POST mevcut** |
| ADR-039 kuralı | `ADR-039-7-service-platform-architecture.md` §2.2-b → "**Servis↔servis HTTP yasak**; circuit breaker + max 3 retry" (kural IMPLEMENTED doküman, uygulama PLANNED) | ⚠️ **AÇIK GERİLİM** (ADR-043 §2.4'te de kayıtlı; ADR-043 §5.1 adım 7 çözüm yolu istemişti — **o adım bu ADR'de ele alınır**, §2.2-e) |
| Retry davranışı | `:111-117` → sabit `usleep(self::RETRY_DELAY_US)` döngüsü, `MAX_RETRIES` (2); **exponential backoff / jitter YOK** (ADR-028 bulgusu) | ✅ IMPLEMENTED (sabit gecikme) |

**Tablo D — Subdomain envanteri ve auth bağımlılığı (`shared/src/Config/CLAUDE.md:47-55` servis tablosu + disk ölçümü)**

| # | Subdomain | Port / Stack (servis tablosu :47-55) | Fiziksel dizin | Auth bağımlılığı | Durum |
|---|-----------|--------------------------------------|----------------|------------------|-------|
| 1 | `coremusic.net` | 80 · Vanilla JS | kök (`index.html` **YOK**) | — (giriş dosyası yok) | ⚠️ **PLANNED** (ADR-039 §2.1-1) |
| 2 | `music.coremusic.net` | 81 · PHP 8.4 + JS | **YOK** | — | ⚠️ PLANNED |
| 3 | `admin.coremusic.net` | 80 · PHP 8.4 | **YOK** | — | ⚠️ PLANNED |
| 4 | `download.coremusic.net` | 3001 · Node.js+TS — **tabloda "⚠️ PLANNED (dizin yok — ADR-026 şart 1a)"** | **YOK** | — | ⚠️ PLANNED |
| 5 | `media.coremusic.net` | 5000/6000 · PHP + FFmpeg | **VAR** (18 dosya: `bin/config/docs/media/src`, `composer.json`) | kendi `src/` kodu var; **auth bağımlılığı ölçülmeli** | ⚠️ **ADR-039 §2.1-4 ile ÇELİŞİYOR** ("dizin YOK") → §5.1 adım 6 |
| 6 | **`auth.coremusic.net`** | `—` · PHP 8.4 | **VAR** (69 dosya/42 PHP) | **kimlik üretir** | ✅ **IMPLEMENTED — TEK AUTH NOKTASI** |
| 7 | `home.coremusic.net` | 81 · Vanilla JS | **VAR** (31 PHP) | **`HomeAuthBridge` → `POST auth/validate-key`** | ✅ IMPLEMENTED (tek doğrulayan istemci) |
| 8 | `car.coremusic.net` | `—` · Vanilla JS | **YOK** | — | ⚠️ PLANNED |
| 9 | `studio.coremusic.net` | 81 · Vanilla JS | **YOK** | — | ⚠️ PLANNED |
| + | `api.coremusic.net` | (servis tablosunda **YOK** — ADR-039 C2: `domain.php`'de var, 11 listesinde yok) | **VAR** (12 PHP) | `ApiAuthContainer` + `Authentication/AuthorizationMiddleware` → **kendi oturum kurulumu var** | ⚠️ **SINIR-ÖTESİ** |
| + | `assets.coremusic.net` | statik | **VAR** (550 dosya, 0 PHP) | yok | ✅ IMPLEMENTED |

**Tablo E — Cross-subdomain cookie (ADR-043 "8 nokta" bulgusu — yeniden doğrulama)**

| Ölçüm | Sonuç |
|-------|-------|
| `'.coremusic.net'` / `SESSION_COOKIE_DOMAIN` **literal yazan PHP dosyası** | **13 dosya**: `auth` 6 (`constants.php:48`, `app.php:23`, `AuthController.php:249`, `AuthContainer.php:55`, `SessionManager.php:12,161`, `SessionMiddleware.php:71`) · `api` 3 (`constants.php:51`, `app.php:22`, `ApiAuthContainer.php:38`) · `home` 2 (`HomeSessionManager.php:19,159`, `HomeContainer.php:38`) · `shared` 2 (`SessionInitializer.php:43`, `SessionConfig.php:45`) |
| JS tarafı | `assets.coremusic.net/js/managers/ThemeManager.js` **3 satır** (`:113`, `:166`, `:168` — `cm_gender` / `cm_color_mode`) |
| `setcookie` çağrı noktası | **6** (`SessionBootstrapper.php:45`, `PageRouter/SessionInitializer.php:73`, `auth/SessionManager.php:161`, `home/HomeSessionManager.php:159`, `auth/SessionMiddleware.php:86`, `auth/AuthController.php:246`) |
| **Ölçüm farkı (dürüst kayıt)** | ADR-043/ADR-052 **"8 nokta"** iddiası bu taramayla **birebir eşleşmiyor** (benim sayım **13 PHP dosyası + 1 JS dosyası**). Sayım yöntemi farklı olabilir → **⚠️ VERIFICATION REQUIRED**: "8" hangi ölçüte dayandığı vault'ta yazılmamış. **Sonuç değişmiyor:** cookie domain **tek değer** `.coremusic.net` ve **tüm alt alanlara yayılıyor** (SSO ön koşulu + ortak XSS yüzeyi). |

**Tablo F — Mevcut auth dağıtımı / duplicasyon seviyesi (ADR-039 servis listesi hizası)**

| Ölçüm | Sonuç |
|-------|-------|
| `SessionBootstrapper::ensureStarted()` **çağrıyan dosyalar** | **4 ayrı nokta**: `auth.coremusic.net/index.php:23,91,146` (+ `Handler/*:11,12,41,44`, `Service/SessionManager.php:6,60`) · `home.coremusic.net/config/bootstrap.php:7,53,69,70` (+ `HomeSessionManager.php:24,63`, `MusicStreamHandler.php:7,123,140,146`) · `api.coremusic.net/index.php:111,113` · `shared/src/Middleware/SessionManagerMiddleware.php:6,17` |
| **Oturum KURMA Yetkisi** | **DAĞILIK** — ADR-043 "oturum yalnız auth'ta" kararı **PLANNED**, bugün **3 servis + 1 shared middleware** kendi oturumunu kuruyor |
| Auth/Session dosyası dağılımı | `auth` **13** · `home` **2** · `api` **3** · `shared` **48** · `assets` **0** |
| Kendi auth kodu olan servis | **3 / 6 fiziksel servis** (`auth`, `home`, `api`) — ADR-039'un 11 servis listesiyle hizalı: 8'i **PLANNED (dizin YOK)**, `media` **dizin VAR ama auth bağımlılığı ölçülmüş değil** |
| Duplicasyon seviyesi | **ORTA-YÜKSEK**: 3 servis kendi oturum/auth konteynerini kuruyor (`AuthContainer` / `HomeContainer` / `ApiAuthContainer` — **3 ayrı DI kaydı**); `auth` **kendi** `SessionMiddleware` + `SessionManager`'ını tutuyor (`shared`'tekinden ayrı); **çizim:** yetki/oturum verisi 3 serviste farklı anahtarlarda (ADR-056 §1.2-2 ile aynı kök) |
| ADR-039 ayrılma sırası | `download → media → auth` — **auth en sonda**; bu ADR bu sırayı **değiştirmez** |

### 1.2 Sorun Tanımı

Dört sorun üst üste binmektedir:

1. **"Tek auth noktası" iddiası kodda 4 noktaya dağılmış.** Kimlik *üretimi* gerçekten tek (`auth.coremusic.net`), ama **oturum kurma** auth + home + api + `SessionManagerMiddleware` = **4 noktada**. Sonuç: ADR-043'ün "diğer alt alanlar session KURMAZ" kararı **henüz kod gerçeği değil**; `FORCE_AUTH_BYPASS` açıkken `home` `validate-key`'siz doğrudan `$_SESSION` yazabiliyor (`.ai/reports/auth-bypass-audit.md` C1).
2. **Servisler-arası güven tek yolla sınırlı ve denetimsiz.** Tek yol `POST auth/validate-key` — ama bu uç **Origin denetimi dışında** (E4) ve **rate limit'ten de ayrı** (`RateLimiterMiddleware.php:29` muaf). Yani "güvenin kapısı" **kilitli değil, denetimsiz**.
3. **Auth aşağı düşünce ne olacağı yazılmamış.** Bugün `HomeAuthBridge` **fail-open değil fail-null**: curl hata/401 → `null` → session kurulmaz (doğru). Ama **mevcut oturumlu kullanıcının** auth yokken ne olacağı, ve **hiçbir doğrulama kopyası bulunmadığı için** auth'ın tüm siteyi kilitlenme eşiği tanımsız. SSO literatürü bunu "tek nokta arıza + break-glass zorunluluğu" olarak adlandırır.
4. **ADR-039 ile gerilim çözülmedi.** `HomeAuthBridge` sunucu→sunucu doğrudan HTTP POST atıyor; ADR-043 §5.1 adım 7 bu sorunu "olay/IPC'ye taşıma ya da ADR-039'a istisna kaydı" diye açmıştı — **karar verilmedi**.

### 1.3 Web'den Araştırma Raporu & Sonuçları

Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte mevcut) · **17 sorgu** (exa 13 + web search 4) · **~30 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "centralized authentication service architecture 2025 single auth server subdomain SSO best practices" · (2) "authentication service availability high availability fail closed vs fail open auth outage identity provider break glass" · (3) "service-to-service authentication mTLS internal token vs JWT validation endpoint zero trust microservices 2025" · (4) "zero downtime migration to centralized identity provider phased rollout dual session validation" · (5) "authorization service fail closed deny by default availability cache stale session degradation" · (6) "SSO identity provider outage business impact SLA authentication service redundancy" · (7) "shared parent domain cookie SSO security risk subdomain OWASP session" |
| Web Search **Konusu** | Merkezi auth servisinin mimari yeri (IdP/SP ayrımı), tek auth noktasının **arızalık/kesilebilirlik** bedeli ve fail-closed/fail-open kararı, servisler-arası kimlik (mTLS / SPIFFE / imzalı JWT / validate endpoint), **flag-day'siz** IdP migrasyonu, üstalan cookie'sinin güvenliği |
| Web Search **Bağlamı** | CoreMusic'te `auth.coremusic.net` tek kimlik üretir; `home` **doğrudan HTTP** ile `validate-key` çağırır; cookie `domain=.coremusic.net` tüm alt alanlara yayılır; `/validate-key` origin denetimi dışındadır; auth down ise site ne olur **yazılı değildir** |
| Web Search **Kısa Açıklama** | Merkezi auth **güvenlik öncelikli bir mimaridir**: SSOJet/Verizon 2025 DBIR verisi (temel web saldırılarının **%88'i** çalınmış kimlik bilgisi), NIST SP 800-63B federatif modeli ve SOC 2 CC6, kimlik yüzeyini uygulama başına **tek yerde** toplamayı şart koşar. Aynı literatür **bedeli de açıkça yazar**: IdP "tek nokta hem arıza hem saldırı"dır — RSA Conference ("çoğu uygulama güvenli yedek auth'a geri düşecek şekilde tasarlanmamış"), Duo/IdP concentration risk, SCW IdP dependency mapping (izleme sistemi bile aynı IdP'den auth alır → kaskad), AWS break-glass (2026-08) federatif kimlik **arızalandığında kümelere erişimi kilitleyen** döngüyü kıran acil yol tanımlar. Servisler-arası güvende 2025-26 literatürü **katmanlı**:model = ağ katmanı **mTLS/SPIFFE (NIST SP 800-207 zero-trust)**, token katmanı **imzalı JWT + JWKS + `exp/aud/scope`** (authlayer/codelit/bmf-tech), **tek başına API key yeterli değil**; `mTLS varsayılan, JWT kimlik taşır` uzlaşımı (learnixo, josephraymund, ContentWave). Migrasyonda **flag day yasak**: WorkOS 4 fazlı playbook (2026-06) ve IdP Migration Planning 2026 → **çift doğrulama penceresi**, ölçülebilir çıkış ölçütü, geri alma. |
| Web Search **Uzun Açıklama** | **(i) Merkezileştirme gerekçesi:** SSOJet, kimlik tek noktada toplanınca MFA politikasının, denetim kaydının ve **deprovisioning'in tek yol** hâline geldiğini; "uygulama artık parola görmüyor" (NIST SP 800-63B relying party modeli) ile credential stuffing'in **yapısal** olarak kapandığını yazıyor. DevOps Consulting'in SSO mimarisi (2025-08) IdP → SP redirect + JWT doğrulama akışını **tam olarak CoreMusic'in `auth → home` köprüsünün** genel şeklini verir; babble-open-source authentication-as-a-service ise **alt-alan SSO** için `COOKIE_DOMAIN=.company.com` + `/auth/validate` ucu ile **`auth.coremusic.net`'in yapısıyla eşleşen** bir referans uygulamadır (exa.ai multi-product identity paper, 2025-10-30: gateway = ana token doğrulama noktası + **önbellekli** doğrulama + key rotation). **(ii) Tek nokta arıza bedeli:** nhimg "IdP SPOF" (misyon operasyonları aynı anda durur), Hive Security (2026) "üç AZ'de çalışan servis tek regional control plane + tek IdP'ye bağlıysa **şema redundant, arıza yolu değil**"), Rack2Cloud (break-glass, **session survivability**, bağımsız trust authority), RSA Conference (merkezi auth'a rağmen SPOF — **uygulamalar fallback'e tasarlanmamış**). Bu kaynakların ortak reçetesi: **(a) mevcut oturumlar yaşasın (session survivability), (b) yeni doğrulama fail-closed, (c) break-glass/acil yol test edilmiş olsun.** ArchMan "Complete Mediation + Fail Securely": her erişim isteği her seferinde kontrol edilir; hata durumunda **izin verme, reddet** — fail-closed'un kaynağı. **(iii) Servisler-arası güven:** bmf-tech üç katman (network/token/identity; RFC 8693 token exchange, SPIFFE, NIST SP 800-207), codelit/learnixo (mTLS + service account + JWT propagation + API key rotation), authlayer (**Servis B, auth sunucusunun public key'leriyle JWKS üzerinden doğrular — kullanıcı token'ıyla aynı işlem**), josephraymund (**mTLS servis kimliği + imzalı JWT attestation ile identity propagation**), zta-internal-api-prototype (mTLS + RFC 8705 certificate-bound token referans uygulaması), Medium (2026) "zero-trust'ta **mTLS varsayılanınız olsun**". **(iv) Migrasyon:** WorkOS "without a flag day" **4 faz** (paralel çalıştır → kademeli yönlendirme → ölç → kesme), softwaremodernization IdP Migration Planning 2026 (çift-write/dual-validation penceresi, rollback), idmanagement.gov Cloud Identity Playbook (fazlı benimseme). **(v) Cookie/SSO:** dev.to forceki ve medium jsmmmkt123 **paylaşımlı cookie'nin SSO'nun standard yolu** olduğunu; Okta devforum/next-auth #2414 aynı sonuca varır; auth0 SSO yazısı redirect + cookie akışını anlatır — hepsi `domain=.coremusic.net` kararını **destekler**, ADR-043'ün parent-domain cookie riski bulgusuyla **çelişmez** (aynı kaynaklar riski de kabul eder). |
| Web Search **Paragraf Veri Uzun** | 17 sorgu / ~30 adlandırılmış kaynak; her iddia en az 2 bağımsız kaynakla çaprazlandı. **Birincil standart/çerçeve:** NIST SP 800-63B (federatif authentication), NIST SP 800-207 (zero trust), RFC 8725 (JWT BCP), RFC 8693 (token exchange), RFC 8705 (certificate-bound token), SOC 2 CC6. **Sektör verisi:** Verizon 2025 DBIR (%88 credential). **Analiz/kılavuz:** AWS break-glass (2026-08-26), Duo IdP concentration, SCW IdP dependency mapping, RSA Conference SPOF, Rack2Cloud, Hive Security (2026), nhimg ×2, ArchMan fail-securely, Rack2Cloud. **Ürün/uygulama:** WorkOS zero-downtime playbook (2026-06-03), authlayer, codelit, learnixo, bmf-tech, josephraymund, ContentWave, Medium mTLS-vs-JWT (2026), zta-internal-api-prototype, babble-open-source authentication-as-a-service, exa.ai identity paper (2025-10-30), dev.to forceki, medium jsmmkt123, auth0, Okta devforum, next-auth #2414, group107 CAS (2026), devgenius multi-tenant, paddo.dev subdomain isolation, softwaremodernization IdP 2026, idmanagement.gov playbook. **Zafiyet/olay:** IdP SPOF kaskad senaryoları (SCW/hivesecurity). |
| Web Search **Sonucu** | (1) **`auth.coremusic.net`'i TEK auth noktası yapmak destekleniyor** — merkezileştirme NIST/DBIR/SOC2 gerekçesiyle savunulur; ama kaynaklar **aynı anda** "IdP = tek nokta arıza + en yüksek değerli hedef" uyarısını da yapar → §4.3 riskleri. (2) **Erişilebilirlik kararı için kaynak oyu: mevcut oturum yaşar + yeni doğrulama fail-closed + test edilmiş break-glass.** Fail-open (her şeyi geçir) **hiçbir** kaynakta önerilmiyor; RSA Conference "fallback yok" eleştirisi **fail-open değil, güvenli acil yol** istiyor. (3) **Servisler-arası güven: JWKS ile imzalı JWT + (ağ katmanında) mTLS/SPIFFE hedefi; `validate-key` bugünün IMPLEMENTED yolu, JWKS/rotasyon ADR-043/052 PLANNED'i.** Tek başına API key yeterli değil. (4) **Migrasyon: flag day yasak, 4-5 faz + çift doğrulama penceresi + çıkış ölçütü + rollback** → ADR-043'ün 5 faz planı literatürle uyumlu, bu ADR onu uygular. (5) **`domain=.coremusic.net` korunur** (SSO ön koşulu — paylaşımlı cookie kalıbı), savunma Origin + nonce + CSRF + kısa oturum ile katmanlanır (ADR-043 R1 aynı sonuca varmıştı). **Karşıt bulgu: yazarların hiçbiri "merkezileştirmeyi yapma" demiyor** — tek itiraz concentration risk'i ve bu, §4.3 + §5.1 ile mitige ediliyor. |
| Web Search **Alınan Karar** | **(a) Tek auth noktası:** kimlik üretimi + login/logout + oturum **yalnız `auth.coremusic.net`**; diğer alt alanlar session **KURMAZ**, yalnızca doğrular (bugünkü `HomeAuthBridge` yolu korunur, API/JWT yolu eklenir). Cookie `domain=.coremusic.net` **korunur**. **(b) Servisler-arası güven:** bugün = server→server `POST /validate-key` (imza/nonce + Origin denetimi **kapatılır**, rate limit **kapsama alınır**); hedef = imzalı JWT + JWKS/`kid` rotasyonu (ADR-043/052 PLANNED), ağ katmanı mTLS/SPIFFE **PLANNED** (deploy'a bağlı, `⚠️ VERIFICATION REQUIRED`). Her servis **kendi** oturumunu kuramaz. **(c) Yüksek erişilebilirlik:** **fail-closed** (yeni doğrulama reddedilir) + **mevcut oturum için servis-içi son-şans okuma** (son bilinen doğrulanmış durum, TTL'li APCu — ADR-007 namespace'i) + **break-glass** (acil yol, test edilir). **Fail-open YOK.** **(d) Migrasyon:** 5 faz, big-bang yok, her fazda çıkış ölçütü + rollback; **çift doğrulama penceresi** faz 2-3'te kapanır. |
| Web Search **Sonuç** | Karar **destekleniyor**: merkezileştirme, katmanlı s2s güveni, fail-closed + session survivability ve fazlı migrasyon **bağımsız kaynaklarda oybirliğiyle** var. **Tek gerilim** = tek nokta arıza; bu, bu ADR'nin §2.2-c (HA) + §4.3 (riskler) + §5.1 (adım 5 break-glass) ile **koşullu** kabul edildi. **⚠️ VERIFICATION REQUIRED:** mTLS/SPIFFE'nin CoreMusic deploy'unda uygulanabilirliği ve JWKS yayım yolu **kod kanıtıyla doğrulanmadı** (yalnız dış kaynak + ADR-043 PLANNED). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnızca okur ve atıf yapar |
| ADR-039 servis sınırı | **Servis↔servis doğrudan HTTP yasak** (olay/IPC); `HomeAuthBridge` bu kurala **gerilim** taşır → §2.2-e bu ADR'de karara bağlanır |
| ADR-039 ayrılma sırası | `download → media → auth` — **auth en sonda**; bu ADR ayrılma sırasını **değiştirmez** |
| In-Place Refactoring | Dosya adı değişikliği **yok**; `index.md`'ye satır eklenmedi (report-only) |
| UTF-8 yazım protokolü | Tüm vault yazımları `vault-utf8-writer.mjs` üzerinden; log.md yalnız `append` |
| Hallucination sweep | Diskte olmayan dosyaya wiki-link **yok**; ADR-051/053-055/057/059/060 = düz metin + ⚠️ |
| Kanıt = kod | Yalnız `Test-Path`/satır numarası ile doğrulanan iddialar; uygulanmamış şey **PLANNED** |
| Debate | `debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — §7.1'e debate kaydı girildi; **3 bağlayıcı şart** §5.1 adımlar 10-12'ye işlendi; **Tech Lead ✅** (debate sonrası) |
| REDACTED | `.env`, JWT imzalama anahtarı, DB parolası vb. **hiçbir** ADR'ye yazılmaz |

---

## 2. Karar (Decision)

Merkezi auth servisinin dört başlığı **aşağıdaki gibi** sabitlenir:

### (a) Tek auth sunucusu — `auth.coremusic.net` TEK auth noktası

1. **Kimlik üretimi tek:** kullanıcı oluşturma, giriş, çıkış, şifre sıfırlama, cinsiyet seçimi ve **oturum kurma yetkisi** yalnız `auth.coremusic.net`'tedir. Diğer alt alanlar **session KURMAZ**.
2. **Doğrulayan taraf tek yoldan geçer:** `home` (ve gelecek tüm servisler) kimliği yalnızca **`POST auth.coremusic.net/validate-key`** (bugün IMPLEMENTED) veya **imzalı JWT + JWKS doğrulama** (ADR-043/052 PLANNED) ile alır. `HomeAuthBridge` bu kararın **uygulayıcısıdır**, ayrı bir kimlik kaynağı değil.
3. **Cookie tek değer:** `domain=.coremusic.net`, `HttpOnly`, `Secure`, `SameSite=Lax`, `path=/`, `lifetime=0` (ADR-011/043 değeri **korunur** — bu ADR **değiştirmez**).
4. **Otomatik oturum kurtarma:** `FORCE_AUTH_BYPASS` (audit C1) **production'da kapatılır**; bypass varsa `validate-key` **atlanamaz**. `SessionBootstrapper::ensureStarted()` yalnız `auth.coremusic.net/index.php`'de zorunlu; `home`/`api`/`SessionManagerMiddleware` oturum **kurmak yerine doğrular**.
5. **Bu ADR authz'yi (yetki) KONU ALMAZ:** rol/permission dağıtımı [[ADR-056-auth-module-implementation]]'ındır; API anahtarı/OAuth [[ADR-020-api-public-security]]'dedir; CSRF [[ADR-010-csrf-protection-strategy]]'dedir.

### (b) Servisler arası güven — internal token / validate endpoint / hangi servis nasıl doğrular

| # | Yol | Kim kullanır | Mekanizma | Durum |
|---|-----|--------------|-----------|-------|
| 1 | **Server→server validate** | `home`, `api` (ve gelecek servisler) | `POST auth/validate-key` `{auth_key}` → auth DB'de `validateSessionKey()` → `{success, user}` | ✅ **IMPLEMENTED** (`HomeAuthBridge.php:128`) |
| 2 | **Token doğrulama (hedef)** | tüm servisler | İmzalı JWT (`exp`/`iss`/`aud`/`alg` allowlist RFC 8725) + **JWKS/`kid`** ile doğrulama; imzalama anahtarı **yalnız auth'ta** | ⏳ PLANNED (ADR-043 §2.3, ADR-052) |
| 3 | **Ağ katmanı (hedef)** | servis↔servis | mTLS / SPIFFE (NIST SP 800-207) | ⏳ PLANNED — **⚠️ VERIFICATION REQUIRED** (deploy bağımlı, kod kanıtı yok) |
| 4 | **Yol 1'in güvenliği kapatılır** | — | `/validate-key` **Origin muafiyeti KALKAR** (`OriginCheckMiddleware.php:35`) · boş-Origin **fail-closed** (`:40,51,60`) · `self::ALLOWED_ORIGINS` tanımsız sabit **düzeltılır** (`:66`) · boş CORS allowlist (`config/cors.php:11`) **doldurulur** · rate limit **muafiyetten çıkar** (`RateLimiterMiddleware.php:29`) | ⏳ PLANNED (ADR-043 Şart 1b ile **aynı kapı**) |
| 5 | **Önbellek** | — | Doğrulama sonucu APCu'da **kısa TTL** (ADR-007 namespace; rbac-authorization.md:241 → 30 sn authz TTL) + invalidasyon | ⏳ PLANNED (§2.2-c ile birlikte) |

**Kural (bağlayıcı):** Bir servis, 1-2 yollarından **hiçbiri** ile doğrulayamıyorsa **erişim reddedilir (401/403)**. Servis kendi `$_SESSION`'ına **bakarak** "girişli" kararı veremez; yalnızca **auth'tan gelen kanıta** güvenir.

### (c) Yüksek erişilebilirlik — auth aşağıysa site ne olur

**Karar: fail-closed + servis-içi son-şans okuma + break-glass. Fail-open YOK.**

| Katman | Davranış | Ayrıntı |
|--------|----------|---------|
| **L1 — yeni kimlik doğrulama** | **FAIL-CLOSED** | `validate-key` / JWKS erişilemiyorsa **yeni** oturum doğrulaması, login, yetki yükseltmesi **reddedilir** (401). Kaynak: ArchMan fail-securely, RSA Conference "fallback tasarımı", OWASP deny-by-default |
| **L2 — mevcut oturum (session survivability)** | **Sınırlı son-şans okuma** | Servis, **en son doğrulanmış** kimlik durumunu APCu'da (ADR-007 namespace) **TTL'li** tutar; auth down ise bu pencere içinde okuma **devam eder**, pencere dolunca **kapanır (fail-closed)**. TTL süresi **PLANNED sabitlenecek** — `⚠️ VERIFICATION REQUIRED` (bugün böyle bir cache **kodda YOK**). **Asla** TTL'siz kalıcı fallback yok |
| **L3 — break-glass** | **Test edilmiş acil yol** | Üretimde kimlik bağımlılığını kırabilen **acil erişim** (AWS break-glass modeli); **periyodik test zorunlu**. İçerik deploy öncesi Security Engineer + Tech Lead tarafından sabitlenir — `⚠️ VERIFICATION REQUIRED` |
| **L4 — gözlemlenebilirlik** | auth sağlık kontrolü | `/health` (zaten `index.php:68`'de) → servisler **circuit breaker** (ADR-039 §2.2-b: max 3 retry) ile `validate-key` çağrılarını **keser** ve L2'ye düşer; authz cache TTL'i (rbac-authorization.md:241) bu pencereleri sınırlar |
| **Site ne olur (açık cevap)** | **Okuma çoğu yerde yaşar, kimlik işlemi durur** | Mevcut oturumlu kullanıcılar L2 TTL'i boyunca devam eder; **yeni login / logout / reset / yetki değişimi durur**; TTL dolunca korumalı yüzeyler **401 → auth'a yönlendirme** (AuthGuard). **Fail-open (her şeyi geçir) hiçbir koşulda yok** |

**Gerekçe:** Literatür, IdP arızasında **uygulamaların güvenli yedeğe sahip olmadığını** (RSA Conference) ve **izleme/kurtarma yollarının bile aynı IdP'ye bağlı olduğunu** (SCW) gösteriyor → yani "auth down = her şey durur" hem operasyonel hem güvenlik açısından kabul edilebilir; buna karşılık **fail-open**, tek bir curl hatasının **tüm siteyi girişsiz** bırakmasına izin verir ve ADR-008 bypass'ının üretimde fail-closed olmasını **bozar**.

### (d) Migrasyon — dağınık auth'dan tek noktaya kademeli geçiş (ADR-043 planı ile)

Flag day **YOK**; WorkOS/IdP-2026 literatürüyle hizalı **5 faz**, her fazda **çıkış ölçütü + rollback**:

| Faz | İçerik | Çıkış ölçütü | Geri dönüş |
|-----|--------|--------------|------------|
| **1** | **Origin/CORS kilidi:** boş-Origin fail-closed, `/validate-key` muafiyeti kalkar, `self::ALLOWED_ORIGINS` sabiti düzeltilir, allowlist doldurulur, rate limit muafiyeti kalkar | `OriginCheck` boş-Origin'de 403 (test yeşil); E1/E4 kodda 0 | allowlist env ile geri alınır |
| **2** | **Oturum tekliği:** `SessionBootstrapper` yalnız auth'ta; `home`/`api` yalnız validate; **bypass prod kapatılır**; **çift doğrulama penceresi AÇILIR** (eski yol + yeni yol paralel) | E2E: login → `music/home` geçişi **tek cookie**; `FORCE_AUTH_BYPASS=false` | eski yol bayrakla geri açılır |
| **3** | **Çift doğrulama penceresi KAPANIR** + JWT/JWKS üretimi devreye girer (imzalama anahtarı yalnız auth'ta) | servisler JWKS ile doğruluyor; `validate-key` yalnız fallback | JWKS bayrağı ile JWT kapatılır, validate-key kalır |
| **4** | **HA:** health + circuit breaker + TTL'li doğrulama cache'i + **break-glass testi** | simüle auth-kesintisinde L1 fail-closed / L2 TTL davranışı **test ile kanıtlanır** | cache TTL=0 (her istekte doğrulama) |
| **5** | **Konsolidasyon:** 3 servisteki ayrı oturum/contayner kodu **tek akışa** bağlanır (In-Place, dosya adı değişmez) | duplicasyon: auth 13 / home 2 / api 3 → tek sorumluluk noktası | her dosya ayrı revert |

**ADR-043 ile hizalama:** ADR-043 §2.5'in 5 fazı + §5.1 adımları bu ADR'nin (d) maddesinin **kaynağıdır**; ADR-043'ün açık bıraktığı **§5.1 adım 7 (HomeAuthBridge ↔ ADR-039 gerilimi)** → bu ADR §2.2-e'de karara bağlanır.

### 2.1 Neden Bu Seçenek?

Merkezileştirme zaten **fiziksel gerçek** (`auth.coremusic.net` = tek kimlik üreticisi); bu karar onu **hukuksal** hâle getirir. Üç alternatif de ya mevcut durumu (3 servis + 4 oturum noktası) kalıcılaştırır, ya ADR-039/011'i zayıflatır (cookie daraltma = SSO'yu kırar), ya da operasyonel olgunluk gerektiren bir altyapıyı (mTLS mesh) bugünden zorunlu kılar. Servisler-arası güven için **sadece `validate-key`'ye** bağlanmak, ADR-043/052'nin JWKS hedefini **bozmadan** bugünün kodunu güvenceye alır; **HA için fail-closed + TTL'li son-şans** kombinasyonu, literatürün üç şartını (session survivability · deny-by-default · break-glass) **tek planda** karşılar.

### 2.2 Teknik Detaylar

1. **Doğrulama yolu (bugün):** `home` → `HomeAuthBridge::doValidateKey` (`:126-152`) → `POST auth/validate-key` → `AuthController` (`:306`) → `AuthService::validateSessionKey` (`:283`) → `UserRepository::findValidAuthKey` → `{success, user}` → `HomeSessionManager` session kurar. **Bu zincir tek doğrulama yoludur.**
2. **Güvenlik açıkları faz 1'de kapanır:** `OriginCheckMiddleware.php:35` (E4 muafiyet) · `:40,51,60` (E1 boş-Origin fail-open) · `:66` (`self::ALLOWED_ORIGINS` tanımsız sabit — **yeni bulgu**) · `config/cors.php:11` (boş allowlist — E2) · `RateLimiterMiddleware.php:29` (rate limit muafiyeti) · `index.php:68` (kernel dışı uçlar).
3. **HA mekanizması:** `auth/health` → servis **circuit breaker** (max 3 retry, ADR-039) → **kapalı** ise validate-key çağrıları durur → L2 APCu TTL penceresi (ADR-007 namespace) → pencere dolunca **401** → `AuthGuard` kullanıcısı `auth.coremusic.net/login`'e yönlendirir. **L2 TTL bugün kodda YOK** → §5.1 adım 5.
4. **Migrasyon penceresi:** faz 2-3'te eski (`validate-key`) + yeni (JWKS) yollar **paralel** çalışır; çift doğrulama **aynı kullanıcı için iki farklı sonuç üretemez** — uyuşmazlık **fail-closed** (reddet) ve log'a yazılır. Pencere faz 3 sonunda kapanır.
5. **Oturum tekliği kuralı:** `SessionBootstrapper::ensureStarted()` çağrı noktaları **4 → 1'e** iner (yalnız `auth.coremusic.net/index.php`); `home.coremusic.net/config/bootstrap.php:53,69,70` ve `api.coremusic.net/index.php:111,113` **doğrulamaya** çevrilir; `shared/src/Middleware/SessionManagerMiddleware.php:17` **auth dışında oturum KURMAZ**.
6. **ADR-039 gerilim çözümü (§2.4 → bu ADR):** `HomeAuthBridge`'in sunucu→sunucu `validate-key` çağrısı **ADR-039'a ISTİSNA DEĞİL, kuralın kendi istisnası olarak yazılır**: "servis↔servis doğrudan HTTP yasağı **kullanıcıya ait iş akışı için** değil, **servis kimliği paylaşımı** içindir; kimlik kanıtı taşımak (identity propagation) IPC/olay kanalının **dışında, imzalı ve denetlenebilir** tek yol olduğu için `/validate-key` sunucu→sunucu çağrısı **izinli tek uç**tur." Bu metin ADR-039'a **ek satır olarak** eklenir (ADR-039 frozen **değil**, ama In-Place Refactoring kuralıyla **satır ekleme** yapılır — §5.1 adım 4). **Alternatif (olay/outbox'a taşıma) reddedilir:** doğrulama **senkron ve idempotent** olmalıdır; olay tabanlı asenkron doğrulama, auth anahtarının **tek kullanımlık / TTL'li** doğasıyla (ADR-047) çelişir ve kullanıcı login'inde gecikme üretir.
7. **Deneme:** `media.coremusic.net` (dizin VAR, ADR-039'da "YOK") → auth bağımlılığı **ölçülür** ve tabloya işlenir; `api.coremusic.net` → ADR-039 11 listesinde **yok** (C2) → bu ADR'de "sinir-ötesi" olarak işaretlenir, sayıma girmez.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Statüko: her servis kendi oturumunu kurmaya devam etsin** (bugünkü 4 nokta) | Sıfır geçiş maliyeti; `home`/`api` bağımsız çalışır | "Tek auth" iddiası kod gerçeğiyle **çelişmeye** devam eder; bypass (audit C1) açık kalır; yetki/oturum verisi 3 serviste farklı anahtarlarda (ADR-056 §1.2-2); IdP-SPOF literatürünün **değişkeni** bile yok | ADR-043 **kullanıcı onaylı** konsolidasyon kararıyla doğrudan çelişir; güvenliği değil **karmaşıklığı** artırır |
| 2 | **Fail-open: auth aşağıysa tüm doğrulamayı atla (site yaşasın)** | Kesintide kullanıcı deneyimi **kesintisiz**; implementasyon ucuz | Tek curl hatası → **herkes girişsiz/ya da herkes girişli** ikilemi yerine doğrudan **herkes girişli** = ADR-008 bypass'ının üretimde fail-closed olmasını **bozar**; A01/A07 (OWASP) tetiklenir | **Hiçbir** dış kaynak fail-open önermiyor (ArchMan fail-securely; OWASP deny-by-default); ADR-056'daki Permission middleware fail-closed'u ile **çelişir** |
| 3 | **Cookie domain'i daralt (`host-only` / `__Host-`), her servis ayrı cookie tutsun** | XSS yüzeyi daralır (ADR-043 R1'in kaynağı); `__Host-` avantajı | **SSO kırılır** — her alt alan ayrı login ister; ADR-011'in `domain=.coremusic.net` kararı ve `ThemeManager.js` çerezleri geçersiz olur | Paylaşımlı cookie **SSO'nun standard yolu** (dev.to/medium/Okta/next-auth — §1.3-v); daraltma ayrı bir **yeni ADR** ister, bu ADR'nin kapsamı değil |
| 4 | **mTLS / SPIFFE mesh'i bugünden zorunlu kıl** | Servis kimliği ağ katmanında; en güçlü s2s modeli (NIST SP 800-207) | CoreMusic'te **servis sayısı bugün 3 fiziksel**; mesh kurulumu/sertifika operasyonu büyük; kod kanıtı **0** | Erken: ADR-039 "big-bang yasak" + kademeli ayrılma sırası (`download → media → auth`) buna karşı; **PLANNED hedef** olarak §2.2-b/3'te tutulur |
| 5 | **IdP dışarı taşınsın (Auth0/Keycloak vb.)** | Yönetilen HA, hazır MFA/federasyon; dış literatürle birebir örtüşür | 18 BCNF DB + `credential_keys`/`user_tokens` şeması **koda gömülü**; OAuth provider zaten `shared/src/OAuth/`'ta; veri egemenliği ADR-040/003 ihlali riski | Kapsam aşımı + büyük migrasyon; bu ADR **mevcut** `auth.coremusic.net`'i merkezîleştirir (ADR-043 hattı). Gelecekte ayrı ADR ile değerlendirilebilir |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **"Tek auth noktası" iddiası kodla hizalanır:** oturum kurma 4 noktadan **1'e** iner; bypass (audit C1) kapanır.
- **Güvenin kapısı kilitlenir:** `/validate-key` Origin + rate limit kapsamına alınır; tanımsız sabit (`:66`) düzeltilir → 500 hatası da ortadan kalkar.
- **Kesinti davranışı tanımlı:** fail-closed + TTL'li son-şans + test edilmiş break-glass → "auth down = tüm site kilit" **kontrollü** hâle gelir (kayıp: yeni login; kazanç: güvenlik).
- **Literatürle tam hizalı:** merkezileştirme gerekçesi (NIST/DBIR/SOC2), s2s katman modeli (mTLS+JWKS), fazlı migrasyon (WorkOS) — hepsi bağımsız kaynaklarla çaprazlandı (§1.3, ~30 kaynak).
- **ADR-043 ile ADR-039 arasındaki açık gerilim** (HomeAuthBridge doğrudan HTTP) **ilk kez karara bağlanır** (§2.2-e).
- **Duplicasyon ölçümü sayısal:** auth 13 / home 2 / api 3 / shared 48 → faz 5'te hedef net.

### 4.2 Olumsuz Sonuçlar

- **Tek nokta arıza kalıcı olarak kabul edilir** — hafifletilir (L2/L3), ortadan kaldırılmaz.
- **`index.md` hâlâ 051–060 satırlarını içermiyor**; bu ADR dizinde **görünmez** (report-only, §5.1 adım 8'e ertelendi).
- **`brain.md`'de ADR-058 slotu YOK** (grep = 0) → özet MO tarafından eklenmeyi bekliyor (**⚠️ VERIFICATION REQUIRED**).
- **Debate ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — Tech Lead ✅ (§7.1); ama **3 bağlayıcı şart** (§5.1 adımlar 10-12 + §6 şart notu) kapanana kadar **Frozen'a geçiş YOK**.
- **PLANNED yükü yüksek:** JWKS, mTLS, L2 cache TTL, break-glass içeriği, Origin kilit — hiçbiri bugün kodda yok (`⚠️ VERIFICATION REQUIRED`).
- **Numara serisi riski:** ADR-058 arşiv tarafından **daha önce ADR-056'da "diskte YOK" olarak anılmıştı**; bu dosya o numarayı doldurur. **ADR-059 (MFA) hâlâ diskte yok.**
- **ADR-039 §2.1-4 (media "dizin YOK")** ile disk gerçeği (**dizin VAR, 18 dosya**) **çelişiyor** — bu ADR düzeltmez, raporlar (§5.1 adım 6).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Tek nokta arıza** — auth.coremusic.net aşağı = yeni login/yetki durur | **Yüksek** (>70) | Yüksek | L2 TTL'li son-şans + L3 break-glass + `/health` izleme + circuit breaker (§2.2-c); faz 4'te **simülasyon testi** |
| **authDomain aşağı = tüm site kilit** (TTL dolunca) | Orta (%40-70) | Yüksek | Bilinçli **fail-closed** kararı; okuma yüzeyleri L2 ile yaşar; **fail-open YOK** (§3 alt.2); break-glass periyodik test |
| **Validate bypass** — muafiyet/boş-Origin/boş allowlist ile sahte doğrulama | **Yüksek** | **Kritik** | Faz 1: E1 + E4 + E2 + `:66` sabiti + rate limit muafiyeti **hepsi aynı fazda** kapanır (ADR-043 Şart 1b ile aynı kapı); kalıcı test yazılır |
| **Migrasyon sırasında çift doğrulama** — eski+yeni yol farklı sonuç üretir | Orta (%40-70) | Orta | Faz 2-3 penceresi **sınırlı**; uyuşmazlık **fail-closed + log**; pencere faz 3'te kapanır, çıkış ölçütü zorunlu (§2.2-d) |
| **ADR-039 gerilimi çözülmezse** — `HomeAuthBridge` yasağı ihlal eder | Orta (%40-70) | Orta | §2.2-e **istisna metni** ADR-039'a satır eklenerek yazılır (§5.1 adım 4); alternatif = olay/outbox (reddedildi, gerekçe §2.2-6) |
| **`self::ALLOWED_ORIGINS` tanımsız sabit** → origin kontrolünde **500** | **Yüksek** (>70) | Orta | Faz 1 düzeltmesi (`:66` → `$this->allowedOrigins`); regresyon testi (origin var → 403/200, 500 **yok**) |
| **L2 cache → stale kimlik** (kaldırılmış oturum TTL içinde yaşar) | Orta (%40-70) | Yüksek | TTL **kısa** sabitlenir + logout/invalidasyon cache'e **yazılır** (ADR-007 namespace); OWASP/CVE kanıtı invalidasyonu zorunlu kılar (ADR-056 §1.3-5) |
| **`media.coremusic.net` auth bağımlılığı bilinmiyor** → migrasyonda unutulur | Orta (%40-70) | Orta | §5.1 adım 6: envanter ölçümü + ADR-039 C1/C2 çelişki defterine kayıt |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Dokümante et (bu ADR):** tek auth noktası · validate yolu · HA katmanları (L1-L4) · 5 faz. Kod **değiştirilmez**. | Vault Steward | 0.5 gün |
| 2 | **PLANNED — Faz 1 Origin/CORS kilidi:** `OriginCheckMiddleware.php:35` muafiyeti kaldır · `:40,51,60` boş-Origin fail-**closed** · `:66` tanımsız `self::ALLOWED_ORIGINS` → `$this->allowedOrigins` düzelt · `config/cors.php:11` allowlist doldur · `RateLimiterMiddleware.php:29` muafiyeti kaldır · kalıcı test | Security + Backend | 2 gün |
| 3 | **PLANNED — Faz 2 oturum tekliği:** `SessionBootstrapper::ensureStarted()` → yalnız `auth.coremusic.net/index.php`; `home`/`api` bootstrap'leri doğrulamaya çevrilir; `FORCE_AUTH_BYPASS` prod `false` (audit C1); **çift doğrulama penceresi AÇILIR** | Backend | 3 gün |
| 4 | **PLANNED — ADR-039 istisna kaydı:** `ADR-039-7-service-platform-architecture.md` §2.2-b'ye **satır ekleme** (In-Place, dosya adı değişmez): `HomeAuthBridge` sunucu→sunucu `validate-key` çağrısının **izinli tek uç** olduğu + gerekçe (§2.2-6). **Frozen değil** ama metin **ekleme** olarak yazılır. | Tech Lead + Backend | 1 gün |
| 5 | **PLANNED — Faz 4 HA:** `auth/health` izleme + circuit breaker (max 3 retry, ADR-039) + **TTL'li doğrulama cache'i** (ADR-007 namespace; rbac-authorization.md:241 30 sn hizası) + **L2/L3 break-glass içeriğini Security Engineer + Tech Lead ile sabitle** + **simüle auth-kesinti testi** (L1 fail-closed / L2 TTL / 401→AuthGuard) | Security + DevOps | 3 gün |
| 6 | **Ölçüm — `media.coremusic.net` envanteri + ADR-039 çelişkisi:** dizin VAR (18 dosya) ama ADR-039 §2.1-4 "dizin YOK" diyor → auth bağımlılığı ölçülür, ADR-039 çelişki defterine (C1/C2) kayıt; `api.coremusic.net` C2 durumu teyit edilir | Data + Backend | 1 gün |
| 7 | **ERTELENEN — index/numara:** `.ai/.decisions/index.md`'ye ADR-058 satırı **eklenmedi**; 051–060 boşluğu + ADR-088+ kuralı ile numara serisi çelişkisi **raporlandı** (düzeltme YAPILMAZ) | Vault Steward | sonraki reset |
| 8 | **ERTELENEN — `.ai/.decisions/index.md` ADR-056/058 satırı + `brain.md` ADR-058 slotu** bir sonraki vault reset'ine ertelendi (report-only; In-Place Refactoring + SRP; brain slotu **⚠️ VERIFICATION REQUIRED** — MO işi) | Vault Steward / MO | sonraki reset |
| 9 | **Debate TAMAMLANDI (2026-09-29):** 3 tur / 20 persona → **18 kabul / 2 çekimser / 0 red → KABUL**; `debate` alanı ✅, **Tech Lead ✅** (§7.1) | Vault Steward + Tech Lead | 1 gün |
| 10 | **Debate Şart 1 (bağlayıcı):** (1a) **SPOF savunması** — L2 TTL cache + break-glass + **fail-closed (fail-open YOK)**; (1b) **bypass kapısı** — JWKS+mTLS hedef yolu + origin doğrulama kapısı (ADR-043 Şart 1b ile aynı kapı) | Security + Backend | 2 gün |
| 11 | **Debate Şart 2 (bağlayıcı):** `media.coremusic.net` envanter düzeltmesi + ADR-039 §2.1-4 çelişkisine satır ekleme (adım 4/6 ile birlikte) | Vault Steward + Data | 1 gün |
| 12 | **Debate Şart 3 (bağlayıcı):** migrasyon **çift doğrulama penceresi** (faz 2-3) + **geri alma testi** — çıkış ölçütü olarak kanıtlanır | QA + Backend | 1 gün |

### 5.2 Geri Dönüş Planı

Bu ADR **kod üretmez** (karar kaydıdır) → doğrudan geri dönüş riski yoktur. Uygulama adımları için:

1. **Adım 2 (Origin kilidi) geri dönüşü:** allowlist + muafiyet env bayrağı ile geri alınabilir (`ORIGIN_ALLOWED_HOSTS` boşaltılır) → davranış **E1/E4 açığına döner**. **Önerilmez**; ama acil durumda tek bayrakla geri alınabilir. Düzeltme (`:66` sabiti) **geri alınmaz** — o bir hatadır, kapatılması kayıp üretmez.
2. **Adım 3 (oturum tekliği) geri dönüşü:** `home`/`api` bootstrap'leri eski haline döner (`git revert` tek commit); çift doğrulama penceresi **açık kalır** → migrasyon faz 2 durdurulur, faz 1 korunur (güvenlik kilidi açık kalır).
3. **Adım 4 (ADR-039 satır ekleme) geri dönüşü:** eklenen satır `git revert` ile kalkar; ADR-039 metni **eski haline döner** (gerilim yeniden işaretlenir). ADR-039 **frozen değil** → geri dönüş güvenli.
4. **Adım 5 (HA) geri dönüşü:** cache `TTL = 0` → her istekte doğrulama (**güvenlik düşmez**, yalnız gecikme artar); circuit breaker bayrakla devre dışı (`CB_ENABLED=false`) → retry davranışı bugüne döner; break-glass **kapatılmaz** (test kaydı silinmez).
5. **Adım 9 (debate) geri dönüşü:** debate olumsuz sonuçlanırsa ADR `status: rejected` veya **yeni ADR** ile `superseded by` bağlanır (`[[../../.templates/adr/adr-template]]` §6.3) — **bu metin düzenlenmez**.
6. **Tam geri dönüş:** tüm adımlar tek commit serisinde; ADR-011 cookie değeri ve ADR-052 pipeline sırası **hiçbir adımda değiştirilmez** (rollback = o iki karara hiç dokunulmadığının kanıtı).
7. **`vault-utf8-writer` yedeği** (`<file>.bak`) bozulma halinde eski içeriği verir; `.ai/log.md`'ye tek satır revert append edilir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme (vault kuralları, UTF-8 protokolü, 16 Hard Guardrail) |
| [[../../brain.md]] | Mimari kararlar — **ADR-058 slotu YOK (⚠️ VERIFICATION REQUIRED)** |
| [[../../WORKFLOW.md]] | Süreçler |
| [[../index.md]] | ADR dizini — **051–060 satırları eksik, bu ADR satırı eklenmedi (report-only)** |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 zorunlu iskelet (§1.3 web araştırması + 19 doğrulama kapısı) |
| [[ADR-043-auth-subdomain-consolidation]] | **Subdomain/cookie/CORS/JWT konsolidasyonu** — bu ADR onun (a)+(b)+(e) başlıklarını **yazar**, (c)/(d)'yi referanslar |
| [[ADR-047-login-redirect-session-bridge]] | İmzalı tek kullanımlık köprü token'ı + `validateSessionKey` akışı — **bu ADR o akışın güvenliğini kilitler** |
| [[ADR-052-hybrid-auth-session-jwt]] | Hibrit session/JWT zemini (session = SSOT, JWT access ≤15 dk) — **servisler-arası güvenin (b) kaleminin kaynağı** |
| [[ADR-056-auth-module-implementation]] | RBAC + Permission middleware (fail-closed) — **bu ADR authz'yi KONU ALMAZ**, fail-closed kararını **destekler** |
| [[ADR-039-7-service-platform-architecture]] | 11 servis + **servis↔servis doğrudan HTTP yasağı** + kademeli ayrılma (`download → media → auth`) — §2.2-e istisna kaydı bu ADR'ye ait |
| [[ADR-011-session-management]] | Cookie parametreleri + oturum yaşam döngüsü — bu ADR **değiştirmez** |
| [[ADR-010-csrf-protection-strategy]] | CSRF üç katmanı — cookie paylaşımının karşı ayağı |
| [[ADR-012-csp-nonce-strict-dynamic]] | Nonce üretici tek — çapraz subdomain'de kopyalanmaz (ADR-043 (b)) |
| [[ADR-013-rate-limiting-apcu]] | Per-account rate limit — `/validate-key` muafiyeti bu ADR ile **kapsama alınır** |
| [[ADR-020-api-public-security]] | API auth üçlüsü + Bearer kilidi — servisler-arası API erişimi |
| [[ADR-008-bypass-auth-middleware]] | Bypass üretimde fail-closed — §2.2-a/4 (audit C1) ile aynı yön |
| [[ADR-004-multi-domain-spa]] | Cookie domain haritası + subdomain SPA iskeleti |
| [[ADR-007-cache-namespace]] | L2 doğrulama cache'inin namespace standardı |
| [[../../architecture/k6-guvenlik/rbac-authorization.md]] | Yetki cache TTL (`:241` → 30 sn) + implementasyon checklist (`:253-262`) |
| [[../../architecture/k8-servis/README.md]] | K8 servis katmanı — `:5` doğrudan çağrının yasağı (ADR-039 kaynağı) |
| [[../../glossary.md]] | `validate-key` (:155), hibrit JWT+session (:431), HomeAuthBridge (:432) |
| [[../../.decisions/index]] | Karar dizini (051–060 boşluğu) |

**Kod kanıtı (düz metin, wiki-link değil):** `auth.coremusic.net/index.php:68,96,146` · `auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php:35,40,51,60,66` · `auth.coremusic.net/config/cors.php:11` · `auth.coremusic.net/include/Service/AuthService.php:283` · `auth.coremusic.net/include/Controller/AuthController.php:306` · `auth.coremusic.net/include/Service/SessionManager.php:12,161` · `auth.coremusic.net/composer.json` · `home.coremusic.net/include/Auth/HomeAuthBridge.php:20,28,47,111-117,126-152` · `home.coremusic.net/config/bootstrap.php:7,53,69,70,73` · `home.coremusic.net/include/Container/HomeContainer.php:42` · `home.coremusic.net/include/Session/HomeSessionManager.php:19,159` · `api.coremusic.net/index.php:111,113` · `api.coremusic.net/include/Container/ApiAuthContainer.php:38` · `api.coremusic.net/config/constants.php:51` · `shared/src/Middleware/SessionManagerMiddleware.php:6,17` · `shared/src/Middleware/RateLimiterMiddleware.php:29` · `shared/src/Session/SessionBootstrapper.php:8,15,45` · `shared/src/Session/SessionConfig.php:24,45` · `shared/src/PageRouter/SessionInitializer.php:43,73` · `shared/src/Config/CLAUDE.md:47-55` · `assets.coremusic.net/js/managers/ThemeManager.js:113,166,168` · `.ai/reports/auth-bypass-audit.md` · `.ai/.decisions/index.md:91,92`

**Düz metin referanslar (diskte YOK → wiki-link KURULMAZ, `⚠️ VERIFICATION REQUIRED`):** ADR-051 · ADR-053 · ADR-054 · ADR-055 · ADR-057 · **ADR-059 (`ADR-059-mfa`)** · ADR-060 · `.ai/architecture/08-auth/auth-domain.md` · `.ai/architecture/08-auth/auth-flow.md`

**Debate şartları (§7.1 — bağlayıcı; §5.1 adımlar 10-12 ile eşleştirilmiş):** (1) **SPOF savunması + bypass kapısı** → §5.1 adım 10 · [[ADR-043-auth-subdomain-consolidation]] Şart 1b · (2) **envanter/ADR-039 düzeltmesi** → §5.1 adım 11 · [[ADR-039-7-service-platform-architecture]] §2.1-4 / §2.2-b · (3) **migrasyon çift-doğrulama + geri alma testi** → §5.1 adım 12 · §2.2-d faz 2-3.

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | CoreMusic Vault Documentation Specialist | 2026-09-29 | ✅ |
| Tech Lead | CoreMusic Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Debate durumu | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | **3** — Tur 1 bulgu/oy · Tur 2 itiraz→çözüm · Tur 3 oylama |
| Persona | **20** (Tur 1: 16 kabul/neutral + 4 uyarı — Cloud: SPOF şart · QA: migrasyon testi · Critic: bypass + media çelişkisi şart) |
| Tur 1 — bulgu/oy | home → `/validate-key` doğrudan çağırıyor (origin muaf) · `OriginCheckMiddleware:66` tanımsız `self::ALLOWED_ORIGINS` (latent 500) + `:40,51,60` boş-Origin fail-open · `media.coremusic.net` dizin VAR (18 dosya) ↔ ADR-039 "YOK" çelişkisi (raporlandı, düzeltilmedi) · ADR-043 "8 setcookie" iddiası ≠ yöntem bulgusu → **V.R.** · karar: tek auth noktası + validate-key bugün / JWKS+mTLS hedef + fail-closed + L2 TTL + break-glass (**fail-open YOK**) + 5 fazlı migrasyon · ~30 kaynak / 17 sorgu (NIST 800-63B/800-207, RFC 8725/8693/8705) · E4 yeni bulgu · ADR-039 gerilimi (`/validate-key` izinli tek uç — In-Place satır §5.1 adım 4) · index 051-060 satırı YOK (reset'e) · 051/053-055/057/060 atlanan boşluk notu · 16 kabul/neutral + 4 uyarı |
| Tur 2 — itiraz→çözüm | (1) SPOF → L2 TTL cache + break-glass + fail-closed (**fail-open YOK**) → **Şart 1a** · (2) validate-key bypass yüzeyi → JWKS+mTLS hedef yolu + origin doğrulama kapısı (ADR-043 şartı) → **Şart 1b** · (3) media.coremusic.net çelişkisi + ADR-039 gerilimi → envanter düzeltme + ADR-039'a satır ekleme → **Şart 2** · (4) migrasyon testi yok → çift doğrulama penceresi + geri alma testi → **Şart 3** |
| Oy dağılımı (Tur 3) | **18 kabul / 2 çekimser / 0 red** |
| Sonuç | ✅ **KABUL** — **3 bağlayıcı şart:** (1) SPOF savunması + bypass kapısı (1a-1b) · (2) envanter/ADR-039 düzeltmesi · (3) migrasyon çift-doğrulama + geri alma testi → §5.1 adımlar 10-12 + §6 şart notu |
| Tech Lead | ✅ **(2026-09-29 — debate sonrası)** |
| Arch Lead | ⏳ (ayrı onay — bu işlem kapsamı dışında) |

> **Bu ADR persona debate'den GEÇMİŞTİR** (`debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` — §7.1); **Tech Lead ✅ (2026-09-29)**. `status: accepted` kapsamı (§2 a-d) + **3 bağlayıcı şart** (§5.1 adımlar 10-12) uygulanana kadar **Frozen'a geçiş YOK**.
> **Kaynaksız numara boşlukları (not):** ADR-051, ADR-053, ADR-054, ADR-055, ADR-057, **ADR-059**, ADR-060 diskte dosya olarak **yok** ve bu ADR tarafından **atlandı** (kaynak = `.ai/.decisions/accepted/` glob taraması, 2026-09-29). Bu numaralar bu ADR'nin **kapsamı dışındadır**; her biri kendi kanıtıyla doldurulmalıdır.
> **Index boşluğu (not):** `.ai/.decisions/index.md` **051–060 satırlarını içermez** (050 → 061 atlıyor, satır 91/92); bu ADR'nin dizin satırı **eklenmedi** — §5.1 adım 8'de sonraki reset'e ertelendi.

---

*ADR-058 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*
*Mode: Red Team → Human Mode → Truth Mode*
