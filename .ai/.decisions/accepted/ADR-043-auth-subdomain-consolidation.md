---
title: "CoreMusic — ADR-043: Auth Subdomain Consolidation (tek kimlik otoritesi · cookie domain · CORS/CSRF/nonce · JWT anahtar · OAuth2 PKCE federasyon · kademeli geçiş)"
type: "architecture-decision"
category: "security"
date: "2026-09-26"
updated: "2026-09-26"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic kimlik konsolidasyonu: oturum/cookie domaini tek auth.coremusic.net, diğer alt alanlar yalnızca token doğrular; cross-subdomain Origin/CSRF/nonce kuralları; imzalama anahtarı tek kaynak + rotasyon; OAuth2/PKCE federasyonu ve kademeli geçiş fazları"
kaynak: "Kullanıcı onaylı tam kapsam (a-e) + disk/kod kanıtı taraması (2026-09-26: auth 70 dosya/46 PHP, cookie domain 8 nokta, OriginCheck/JWT doğrulaması) + web araştırması (6 sorgu / 35 kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-043: Auth Subdomain Consolidation (Kimlik Konsolidasyonu)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam a-e) · **Tarih:** 2026-09-26 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-043-auth-subdomain-consolidation`
> **İlgili kararlar:** [[ADR-011-session-management]] (cookie `domain=.coremusic.net` + hibrit saklama — bu ADR onu **alan genişletir**) · [[ADR-012-csp-nonce-strict-dynamic]] (tek politika + nonce zinciri — bu ADR cross-subdomain kuralını yazar) · [[ADR-013-rate-limiting-apcu]] (per-account kota — geçiş fazı 5) · [[ADR-020-api-public-security]] (auth üçlüsü: API key / JWT / OAuth2 PKCE + Bearer kilidi) · [[ADR-010-csrf-protection-strategy]] (şart 3: cookie-auth tek yol — bu ADR'de **korunur**) · [[ADR-004-multi-domain-spa]] (subdomain iskeleti — korunur) · [[ADR-039-7-service-platform-architecture]] (11 servis + servis sınırı) · [[ADR-040-database-authority]] (tek sahip/tek yazıcı mantığı — kimlik verisi için de geçerli) · [[../index.md]] (`:85` slug satırı) · [[../../brain.md]] (`:1002` ADR-043 slotu)
> **Ad gerekçesi:** slug `ADR-043-auth-subdomain-consolidation` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:85`) — uydurulmadı; kök `CLAUDE.md:16` kuralı "yeni ADR'ler 088+" derken bu numara **çoktan rezerve** (aynı durum ADR-041/042'de kayıtlı) → numara boş değil, boşluk dolduruldu.
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket, 2026-09-26 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **ÇELİŞKİ** = iki kayıt birbiriyle uyuşmuyor (hiçbiri yumuşatılmadı).

#### A) auth servisi — IMPLEMENTED (dizin doğrulandı)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `auth.coremusic.net/` | **70 dosya / 46 `.php`** (2026-09-26 recursive sayım) — ADR-039 §2.1 satır 2 ile **aynı sayı** → "auth IMPLEMENTED 70 dosya" iddiası **doğrulandı**; klasör: `config/ handler/ include/ pages/ routes/ tests/` + `index.php` + `composer.json` | **IMPLEMENTED** |
| `auth.coremusic.net/include/` | Hexagonal: `Container/ Controller/ Domain(DTO·Entity·ValueObject)/ Handler/ Middleware/ Repository/ Service/` — glossary §DDD/CQRS kanıtı | **IMPLEMENTED** |
| `auth.coremusic.net/index.php:65,74` | `/health`, `/session`, `/validate-key`, `/bypass-status` uçları + `handleValidateKey` | **IMPLEMENTED** |
| `home.coremusic.net/include/Auth/HomeAuthBridge.php:16,47,128` | auth'a **POST `/validate-key`** (TTL 300 sn, 2 retry — `.ai/ROLE.md:150`) → cross-domain doğrulama iskeleti | **IMPLEMENTED** |
| `auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php:35` | `/health`, `/session`, **`/validate-key` origin denetiminden MUAF** | **IMPLEMENTED (ÇELİŞKİ — §1.1-E4)** |
| `.ai/reports/auth-bypass-audit.md` (2026-09-23) | 10 bulgu (3 CRITICAL / 2 HIGH / 3 MEDIUM / 2 LOW); **C1: `FORCE_AUTH_BYPASS=true`**; `home.coremusic.net/config/bootstrap.php:36-43` → bypass aktifse `validate-key` **atlanır**, doğrudan `$_SESSION` yazılır | **IMPLEMENTED (ÇELİŞKİ)** |
| Oturum başlatma yetkisi | `SessionBootstrapper::ensureStarted()` **auth** (`index.php:69,130`) **+ home** (`bootstrap.php:38,55`) **+** middleware (`shared/src/Middleware/SessionManagerMiddleware.php:17`) → oturum kuran **birden fazla nokta** var | **IMPLEMENTED (dağınık kimlik)** |

#### B) Cookie domain kanıtı — tek değer `.coremusic.net` (8 nokta, hepsi IMPLEMENTED)

| Dosya: satır | Kod |
|---|---|
| `auth.coremusic.net/config/constants.php:48` | `define('SESSION_COOKIE_DOMAIN', $env('SESSION_COOKIE_DOMAIN', '.coremusic.net'))` |
| `auth.coremusic.net/config/app.php:23` | `'cookie_domain' => SESSION_COOKIE_DOMAIN` |
| `auth.coremusic.net/include/Container/AuthContainer.php:55` | `SESSION_COOKIE_DOMAIN ?? '.coremusic.net'` |
| `auth.coremusic.net/include/Service/SessionManager.php:161` | `setcookie($name, '', time() - 3600, '/', '.coremusic.net')` (logout temizliği) |
| `shared/src/Session/SessionConfig.php:45,57` | `cookieDomain: '.coremusic.net'` + `cookieParams()['domain']` |
| `shared/src/Session/SessionInitializer.php:43` | `'domain' => '.coremusic.net'` |
| `home.coremusic.net/include/Session/HomeSessionManager.php:19` | `private readonly string $cookieDomain = '.coremusic.net'` |
| `home.coremusic.net/include/Container/HomeContainer.php:38` | `'.coremusic.net'` |

→ **Bulgu:** cookie domaini **zaten tek ve `.coremusic.net`** (ADR-011 §2.2a-1 ile birebir). Sorun **domain genişliği değil**; sorun *kimin oturum kurduğu* ve *diğer alt alanların neyle doğruladığı* — bu ikisi hiçbir ADR'de bağlayıcı yazılmamıştı.

#### C) Subdomain envanteri (3 vault kaynağı — sayılar ADR-039 §2.1'de bağlandı, bu ADR ikinci sayı üretmez)

| Kaynak | İçerik |
|---|---|
| `shared/src/Config/CLAUDE.md:45-55` (9 satır) | `coremusic.net:80` Vanilla JS · `music:81` PHP 8.4+JS · `admin:80` PHP 8.4 · **`download:3001` Node+TS → ⚠️ PLANNED (dizin yok, ADR-026 şart 1a)** · `media:5000/6000` PHP+FFmpeg · **`auth` port `—` PHP 8.4 (satır 52)** · `home:81` Vanilla JS · `car` port `—` Vanilla JS · `studio:81` Vanilla JS |
| `shared/config/domain.php:4,7-14` | `primary = coremusic.net` + **7 subdomain**: auth, home, assets, music, admin, media, **api** (`api.coremusic.net` — 11 servis listesine girmez, ADR-039 §2.1 notu) |
| [[ADR-039-7-service-platform-architecture]] §2.1 | **11 servis**: main, auth, music, media, download, admin, studio, car, home, assets, dev — `assets`/`dev` eklenir, `api` girmez |
| Fiziksel kök dizinleri (2026-09-26) | `assets.coremusic.net/` (550 dosya), `auth.coremusic.net/` (70), `home.coremusic.net/` (29) → **music, admin, media, download, studio, car, dev = dizin YOK → PLANNED** |

> **Dürüst not:** `shared/src/Config/CLAUDE.md` içinde `auth` satırı **`:52`**'dir (`:53` = `home`); görev notundaki `:53` kayması düzeltildi. **assets** bu tabloda yok, `domain.php` + ADR-039 listelerinde var → 3 farklı kesit, ADR-039'da tek tabloya bağlandı.

#### D) İlgili güvenlik ADR bulguları (ADR-011/012/013/020 yeniden tarandı — hepsi GEÇERLİ)

| ADR | Geçerli bulgu (özet) | Bu ADR'ye etkisi |
|---|---|---|
| [[ADR-011-session-management]] | Cookie `HttpOnly+SameSite=Lax+Secure+domain=.coremusic.net` **IMPLEMENTED** (`SessionInitializer.php:40-47`); **ÇELİŞKİ** `MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600`; **iki rotasyon değeri** (`shared` 1800 vs `auth` 900, `SessionMiddleware.php:13-14`); `session.use_strict_mode` **YOK**; JWT stub → hibrit saklama PLANNED | §2.1 cookie kararı bu değerleri **değiştirmez**; yalnız sahiplik/doğrulama rolünü sabitler |
| [[ADR-012-csp-nonce-strict-dynamic]] | shared enforce CSP + 256-bit nonce zinciri **IMPLEMENTED** (`SecurityHeadersMiddleware:16-19,48-72` → `SessionManagerMiddleware` → `HtmlShellRenderer:58,108`); **auth'da ikinci/ölü politika** (nonce anahtarı hiçbir yerde yazılmıyor, `style-src 'unsafe-inline'`); domain listesi **YOK**; report-only **YOK** | §2.2 nonce paylaşımı kuralı bu iki boşluğu kapatır |
| [[ADR-013-rate-limiting-apcu]] | Per-IP 60/60 sabit pencere **IMPLEMENTED** (`RateLimiterMiddleware.php:9-59`); auth login 5/900 sn **IMPLEMENTED ama yalnız IP** (`AuthService.php:57,117,229` → **per-account YOK**); backoff/lockout **YOK**; `CacheRateLimiter` check-then-act yarışı; API limiter kaydı ⚠️ | Geçiş fazı 5'te per-account sayaç auth'ta toplanır |
| [[ADR-020-api-public-security]] | 6 API middleware **tanımlı, KAYITSIZ**; API key doğrulama kodu **YOK** (şema hazır: `coremusic_api.sql:31-57`); **JWT stub `null`**; audit yazıcı **YOK**; CORS allowed_headers'ta `Authorization`/`X-Api-Key` **YOK**; **Bearer kilidi** (§2.2c) | §2.3 + §2.4 doğrudan bu ADR'nin 1 (API key) ve 3 (OAuth2 PKCE) kalemlerini devralır |

#### E) CORS/Origin ve JWT — bu ADR'nin kilit doğrulamaları

| # | Bulgu | Kanıt | Etiket |
|---|---|---|---|
| E1 | **OriginCheck boş-Origin'de geçer (fail-open)** — `HTTP_ORIGIN` yoksa whitelist'e hiç bakılmadan devam eder | `shared/src/Middleware/OriginCheckMiddleware.php:36-41` | **IMPLEMENTED (açık)** → ADR-020 §1.1-B.6'daki "prod fail-closed" ifadesi yalnız `isProduction` dalını (`:73-76`) kapsar; boş-Origin dalı **her koşulda açık** |
| E2 | **auth CORS allowlist'i env'e bağlı, varsayılan BOŞ** → dolu Origin prod'da 403, dev fallback'i de boş | `auth.coremusic.net/config/cors.php:11,15-18` (`CORS_ALLOWED_ORIGINS` yoksa `array_filter(explode(',', ''))` = `[]`) | **IMPLEMENTED (ÇELİŞKİ: ya hepsi kapalı ya Origin'siz açık)** |
| E3 | **Suffix-match**: whitelist'e `coremusic.net` yazılınca **tüm alt alanlar** otomatik geçer | `OriginCheckMiddleware.php:68` `str_ends_with($host, '.'.$allowedHost)` | **IMPLEMENTED** (bilinçli domain-scope; tek kelimeye bağlı) |
| E4 | **`/validate-key` origin muaf** — cross-domain'in en kritik ucu denetim dışı | `auth.../include/Middleware/OriginCheckMiddleware.php:35` | **IMPLEMENTED (açık)** |
| E5 | **Preflight'te `Authorization`/`X-Api-Key` yok** → Bearer/API key'li tarayıcı isteği preflight'te düşer | `auth.coremusic.net/config/cors.php:13` + `shared/src/Middleware/CorsMiddleware.php:32` | **IMPLEMENTED (eksik)** — ADR-020 §2.2d-4 ile aynı bulgu |
| E6 | **JWT stub `null` DOĞRULANDI** — `validateJwtToken()` her koşulda `return null` → Bearer yolu fiilen ölü | `shared/src/Api/Middleware/AuthenticationMiddleware.php:92-106` | **IMPLEMENTED (stub)** → ADR-010 şart 3 + ADR-011 §4.4 + ADR-020 §2.2c kilidi **devrede** |
| E7 | Suffix-match + boş allowlist birlikte: sistem bugün **tek origin'e bile izin vermiyor**, ama Origin'siz isteği geçiriyor | E1 + E2 birleşimi | **ÇELİŞKİ** |

### §1.2 Sorun Tanımı (Problem)

1. **Kimlik üretimi tek, oturum kurma çok:** `auth` + `home` + `SessionManagerMiddleware` üçü de `SessionBootstrapper::ensureStarted()` çağırıyor; bypass açıkken `home` `validate-key`'siz session yazıyor → "tek auth otoritesi" iddiası kodla tam örtüşmüyor.
2. **"Diğer alt alanlar yalnızca token doğrular" kuralı hiçbir yerde yazılı değil:** cookie domaini tek (`§1.1-B`) ama *doğrulama zorunluluğu* tanımsız → her yeni servis kendi login'ini yazabilir (ADR-039 §2.1'deki 8 PLANNED servis kapıda).
3. **CORS iki uçlu:** Origin'siz istek geçiyor (E1), dolu Origin için liste genelde boş (E2) → ya her şey kırılıyor ya denetimsiz geçiş.
4. **Federasyon kapısında denetimsiz yol:** `/validate-key` muaf (E4) + `FORCE_AUTH_BYPASS` (C1 audit) → cross-domain doğrulama ucu politika dışında.
5. **İmzalama anahtarı tek kaynak değil:** JWT stub `null` (E6); hangi anahtar, algoritma, `kid`, rotasyon ve `iss/aud` hiçbir yerde sabitlenmemiş → ADR-020 kilidi açık ama kapı yazılmamış.
6. **Nonce paylaşımı tanımsız:** nonce `$_SESSION['csp_nonce']` içinde (ADR-012); session'ı olmayan alt alan nonce üretip CSP'de kullanamaz; auth'daki ölü politika (`unsafe-inline`) bunu görünmez kılıyor.
7. **Cross-domain rate limit per-IP:** aynı kullanıcı birden fazla serviste ayrı sayılır; **per-account kota yok** (ADR-013) → geçişte kimlik tekliği olmadan kota da tek değil.
8. **Geçişte çift kimlik riski:** eski dağınık session + yeni tek auth aynı cookie'yi (`.coremusic.net`) paylaşırsa **faz arası yarım durum** tanımsız, çıkış ölçütü olmayan geçiş = kalıcı ikilik.

### §1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/üretici kaynak önce, **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; güvenlik iddiası OWASP + resmî zorunlu. **Erişim: 6 websearch sorgusu (2026-09-26).**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "centralized authentication subdomain architecture SSO shared cookie domain parent domain best practices 2025" · (2) "cookie Domain attribute scope security risk parent domain subdomain XSS cookie tossing" · (3) "cross-subdomain CSRF protection SameSite cookie sibling subdomains nonce CSP sharing" · (4) "zero trust service to service authentication mTLS JWT key rotation JWKS best practice 2025" · (5) "OAuth 2.1 PKCE required exact redirect URI authorization server 2025" · (6) "consolidating multiple auth systems into single identity provider migration phased rollout dual identity risk" |
| Web Search **Konusu** | Merkezi auth subdomain mimarisi ve shared-cookie SSO; `Domain` özniteliği kapsamı (üst alan/cookie tossing); sibling subdomain'lerin same-site sayılması + CSRF/CORS/nonce etkisi; servis↔servis güven (mTLS, JWT propagation, JWKS/anahtar rotasyonu); OAuth 2.1'in PKCE + exact-redirect zorunlulukları; IdP birleştirmenin fazlı geçiş usulü |
| Web Search **Bağlam** | **6 sorgu / 35 adlandırılmış kaynak** (çoğu 2024-2026): birincil — OWASP CSRF Prevention Cheat Sheet, W3C Fetch Metadata bağlamı (ADR-010'dan), oauth.net/2.1, IETF `draft-ietf-oauth-v2-1-10`, PortSwigger, Acunetix; ikincil — Xebia (cookie domain pitfalls), canitakeyoursubdomain.name (same-site saldırı ölçümü), centralcsp/andrewlock/lirantal (SameSite+CORS+CSRF), learnixo/codelit/authlayer/gitguardian (zero-trust s2s), WorkOS/ORy/Duo/Strata (IdP geçiş fazları) |
| Web Search **Kısa Açıklama** | **(1)** Merkezi auth + paylaşımlı cookie, subdomain SSO'nun standart yolu: `Domain=.root` + `HttpOnly+Secure+SameSite` ve login/logout tek Identity Provider'da — better-auth/rehberler "kök domain + leading dot" der. **(2)** Aynı öznitelik asimetrik risk taşır: cookie **üstalan'a yazılabilir** (subdomain → parent), **altalanlar tarafından gölgelenebilir** (shadow/tossing) → Acunetix/PortSkigger parent-domain cookie'yi açık, canitakeyoursubdomain "confidentiality + integrity" der. **(3)** SameSite kardeş alt alanları **same-site sayar** → CSRF'te koruma sağlamaz; OWASP "sibling subdomain'i threat modeline güvenmiyorsan same-site'ı izin sayma" der; CORS ve CSP nonce cookie/oturumla birlikte anlam kazanır. **(4)** Zero-trust s2s: kullanıcı cookie'si makine trafiği için geçersiz — mTLS/SPIFFE veya imzalı service-JWT + **JWKS/`kid` ile anahtar rotasyonu** şart. **(5)** OAuth 2.1: PKCE **her** istemci için zorunlu, redirect URI **birebir** karşılaştırılır, Implicit kaldırılır. **(6)** IdP birleştirme tek "go-live" ile değil **fazlı** yapılır (paralel entegrasyon, sıfır kesinti), çift kimlik penceresi bilinçli yönetilir. |
| Web Search **Uzun Açıklama** | **(a) Paylaşımlı cookie SSO:** dev.to (forceki, logicverse) ve medium (jsmmkt123) aynı şemayı çizer — merkezi auth sunucusu oturumu `Domain=.coremusic.net` ile set eder, kardeş servisler aynı cookie'yi okur; cross-subdomain redirect'te cookie yaşar, `SameSite=None` yalnız gerçekten cross-site kurguda gerekir. **(b) Cookie domain kusurları:** Xebia, parent-domain cookie'nin güvenilirlik+sükûnet sorunlarını (bazı tarayıcılar/uzantılar `Domain`'i reddeder, `.example.com` vs `example.com` yazımı) anlatır; Acunetix ve PortSwigger bunu "session cookie parent scope" **zafiyeti** sayar; canitakeyoursubdomain.alt alan saldırısı literatürü, üstalan cookie'sinin **sızdırıldığını (confidentiality)** ve alt alan tarafından **gölgelendiğini (integrity)** ölçümlerle gösterir; Medium vaka analizi XSS + yanlış cookie scope'unun oturum çalınmasına nasıl gittiğini gösterir → yani `domain=.coremusic.net` kararı **bir taraftan SSO'nun ön koşulu, diğer taraftan tüm alt alanların XSS yüzeyinin ortaklaşmasıdır.** **(c) SameSite/CSRF/CORS üçlüsü:** OWASP CSRF cheat sheet sibling-subdomain güvenini açıkça sınırlar; lirantal CORS+SameSite+CSRF'yi ayrı boyutlar (origin ↔ site ↔ token) olarak ayırır; centralcsp "sibling subdomains count as same-site → SameSite compromised subdomain'e karşı korumaz" der; andrewlock/stackexchange aynı sonuca varır → **aynı sitenin alt alanlarında koruma, SameSite değil Origin+token+nonce katmanıdır.** **(d) Zero-trust s2s:** learnixo/codelit/authlayer/gitguardian aynı hiyerarşiyi verir: kullanıcı kimliği servis kimliği değildir; API key (rotate + scope), mTLS/SPIFFE ve **imzalı JWT + JWKS/`kid` rotasyonu** ile servisler birbirini doğrular; josephraymund JWT attestation ile kimlik yayılımını (identity propagation) şart koşar. **(e) OAuth 2.1:** oauth.net, IETF draft, Stytch, WorkOS, auth.wiki, aembit aynı üç değişikliği tekrarlar: PKCE zorunlu, exact redirect, Implicit yok → CoreMusic'te `OAuthManager` zaten PKCE üretiyor (IMPLEMENTED) ama third-party API erişimi yok (PLANNED). **(f) Fazlı geçiş:** ORy "single-phase = tüm kullanıcılar aynı anda taşınır, riskli" der; WorkOS 4 fazlık "flag day yok" playbook'u (paralel IdP → uygulama uygulama → cutover → temizlik), Duo/Strata aynı sırayı doğrular → big-bang kimlik taşıma reddedilir. |
| Web Search **Paragraf Veri Uzun** | Altı sorgu tek bir şeyi zorunlu kılıyor: **kimlik tek noktaya toplanır ama risk tek noktada toplanmaz.** `Domain=.coremusic.net` cookie'si SSO'nun ön koşuludur (dev.to, better-auth) ve kodda zaten IMPLEMENTED (`§1.1-B`); ama aynı öznitelik XSS'i kardeş alt alanlara yayar, cookie tossing/gölgelemeye açılır (Acunetix, PortSwigger, canitakeyoursubdomain, Xebia) → bu yüzden **her alt alanın kendi session kurması yasaklanır, doğrulama auth'a taşınır** (karar §2.1). SameSite kardeş alt alanları korumadığından (OWASP, centralcsp, lirantal) CSRF/CORS savunması **Origin listesi + csrf_token + nonce** ile kurulur (§2.2) ve bugünün boş-Origin fail-open'ı (`§1.1-E1`) kapatılır. Servisler arası ve üçüncü taraf erişimi kullanıcı cookie'siyle yapılmaz: **imzalama anahtarı auth'ta tek kaynaktır**, diğerleri JWKS/`kid` ile doğrular ve rotasyon overlap'li yapılır (§2.3); üçüncü taraf **OAuth 2.1 PKCE + exact redirect** ile bağlanır (§2.4). Geçiş, IdP literatüründeki gibi **fazlı** yürür ve her fazın **çıkış ölçütü** vardır (§2.5) — tek deploy'da çift kimlik penceresi açılmaz. |
| Web Search **Sonucu** | **35 kaynak / 6 sorgu**; her ana iddia ≥2 bağımsız kaynakla çaprazlandı: (i) shared-cookie SSO standart (**4**: dev.to×2, medium, better-auth) · (ii) parent-domain cookie riski (**6**: Acunetix, PortSwigger, Xebia, canitakeyoursubdomain, Medium vaka, stackexchange) · (iii) sibling subdomain = same-site → SameSite CSRF'te yetersiz (**4**: OWASP, centralcsp, lirantal, andrewlock) · (iv) zero-trust s2s + JWKS rotasyon (**5**: learnixo, codelit, authlayer, gitguardian, josephraymund) · (v) OAuth 2.1 PKCE/exact-redirect (**6**: oauth.net, IETF draft, Stytch, WorkOS, auth.wiki, aembit) · (vi) fazlı IdP geçişi (**4**: ORy, WorkOS, Duo, Strata). **Dış kaynakla doğrulanamayan tek konu = CoreMusic'in subdomain listesi ve mevcut kod durumu** → iç kanıt (§1.1) ile sabitlendi, `⚠️ VERIFICATION REQUIRED` değil (iç karardır). |
| Web Search **Alınan Karar** | **(a) Oturum/cookie:** kimlik üretimi + login/logout **tek `auth.coremusic.net`**; cookie `domain=.coremusic.net` (ADR-011 değeri korunur); **diğer alt alanlar session KURMAZ, yalnızca token doğrular** (sunucu→sunucu `validate-key`, kilit açıldığında imzalı JWT). **(b) CORS/CSRF/nonce:** Origin allowlist tek kaynaktan, **boş-Origin fail-closed**, `/validate-key` muafiyeti kalkar, preflight'e `Authorization`+`X-Api-Key`; CSRF `csrf_token` + `SameSite=Lax` korunur; nonce **üretici tek** (auth/shared `SecurityHeadersMiddleware`), **çapraz subdomain'de nonce/token KOPYALANMAZ**. **(c) JWT/key:** imzalama anahtarı **yalnız auth'ta**, servisler **JWKS/`kid`** ile doğrular; rotasyon overlap'li + audit; RFC 8725 `alg` allowlist + `exp/iss/aud`; ADR-020 Bearer kilidi **kapanık kalır**. **(d) Federasyon:** **OAuth 2.1 PKCE zorunlu + exact redirect + state**, `OAuthManager` genişletilir; makine erişimi ADR-020 kalem 1 (API key, SHA-256 hash + scope). **(e) Geçiş:** 5 faz, big-bang yok, her fazda **çıkış ölçütü**. |
| Web Search **Sonuç** | Dış kaynaklar **karar lehine** oybirliğiyle konuştu: shared-cookie SSO standart (destek), parent-domain cookie riski (doğrulanmış → R1/R2 mitigasyonu §4.3), same-site'ın kardeş alt alanlarda yetmemesi (doğrulanmış → Origin+nonce zorunluluğu), OAuth 2.1 PKCE/exact-redirect (doğrulanmış → §2.4), fazlı IdP geçişi (doğrulanmış → §2.5). **Karşıt bulgu yok**; tek gerilim `domain=.coremusic.net`'in XSS yüzeyini büyütmek olduğu için risk tablosuna (R1) ve fallback'e (§4.4-2) işlendi. |

**Kaynak listesi (35):** 1) dev.to/forceki — shared cookie SSO system design · 2) dev.to/logicverse_2025 — centralized auth service for multi-domain apps · 3) medium/@jsmmkt123 — authentication across subdomains · 4) ashishsrivastav.com — browser cookies in SSO deep dive · 5) github.com/better-auth/discussions/5670 — cross-subdomain cookies · 6) acunetix.com — session cookies scoped to parent domain · 7) portswigger.net/kb/issues/00500300 — cookie scoped to parent domain · 8) xebia.com — caveats and pitfalls of cookie domains · 9) canitakeyoursubdomain.name — same-site attacks (leak/shadow) · 10) medium/@Kuber19 — XSS + misconfigured cookies case study · 11) security.stackexchange.com/questions/231735 — cookie domain security · 12) stackoverflow.com/questions/18492576 — share cookies subdomain↔domain · 13) appcheck-ng.com — domain hijacking & subdomain takeover · 14) cheatsheetseries.owasp.org — CSRF Prevention Cheat Sheet (sibling subdomain trust) · 15) lirantal.com — CORS, SameSite and CSRF: 3 dimensions · 16) centralcsp.com — cookie security attributes (same-site kardeş alt alanlar) · 17) andrewlock.net — understanding SameSite cookies · 18) security.stackexchange.com/questions/223473 — SameSite with subdomains · 19) learnixo.io — microservices security (mTLS, JWT propagation, zero trust) · 20) codelit.io — service-to-service authentication · 21) authlayer.dev — S2S: API keys, mTLS, JWTs · 22) gitguardian.com — secure service-to-service communication · 23) josephraymund.net — zero-trust mTLS + JWT attestation · 24) ashishsrivastav.com — microservices security OAuth2/JWT/mTLS · 25) oauth.net/2.1/ — OAuth 2.1 · 26) datatracker.ietf.org — draft-ietf-oauth-v2-1-10 · 27) stytch.com — OAuth 2.1 vs 2.0 · 28) workos.com — OAuth 2.1 what's new · 29) auth.wiki — what is OAuth 2.1 · 30) aembit.io — OAuth 2.0 vs 2.1 migration · 31) workos.com — migrating identity providers without a flag day · 32) ory.com — IAM migration strategies (single vs phased) · 33) duo.com — identity orchestration & consolidating IdPs · 34) strata.io — transitioning from an EOL identity provider · 35) softwaremodernizationservices.com — identity provider migration planning 2026.

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR-001-037 immutabel | Yalnız atıf; metin değiştirilmez (AGENTS.md §25.3 kural 2) |
| ADR-010 şart 3 + ADR-011 §4.4 + ADR-020 §2.2c (bağlayıcı) | `validateJwtToken` stub `null` sürece **Bearer ile oturum/erişim AÇILMAZ** — bu ADR o kilidi **açmaz**, yalnız açılış koşullarını §2.3'te tekrarlar |
| ADR-004 domain haritası + `shared/AGENTS.md` Yasak #4 | `shared/config/domain.php` domain listesi onaysız değişmez; bu ADR yeni subdomain **eklemez** |
| ADR-039 servis sınırı | Servis↔servis **doğrudan HTTP yasak** (olay/IPC); `HomeAuthBridge`'in sunucu tarafı `validate-key` çağrısı bu kurala **gerilim** taşır → §2.4 + §5.1 adım 7'de açıkça ele alınır (uydurulmadı) |
| ADR-040 tek sahip/tek yazıcı | Kimlik verisi (`coremusic_auth`) tek yazar = auth servisi; başka servis bu şemaya **SELECT/UPDATE yapmaz** |
| REDACTED | Secret/credential/anahtar hiçbir koşulda bu ADR'ye yazılmaz (`.env` içeriği okunmaz/yazılmaz) |
| In-Place Refactoring | Dosya adı onaysız değişmez; `SessionConfig`/`OriginCheckMiddleware` sınıfları **ad olarak korunur**, yalnız davranış değişir |
| Tek yazma kanalı + log append-only | Tüm vault yazımı `.ai/scripts/vault-utf8-writer.mjs`; `log.md` yalnız append |

---

## §2 Karar (Decision)

**CoreMusic'te kimlik TEK noktada toplanır: `auth.coremusic.net` kimlik üretir ve oturumu kurar; cookie `domain=.coremusic.net` (ADR-011 değeri) ile tüm alt alanlara ulaşır, ancak diğer alt alanlar oturum KURMAZ — yalnızca doğrulanmış token/oturum kanıtı ile çalışır. Cross-subdomain Origin/CSRF/nonce kuralları, imzalama anahtarının tek kaynaktan yönetimi ve OAuth2/PKCE federasyonu bağlayıcı olarak sabitlenir; geçiş 5 fazda, big-bang olmadan, her fazda tanımlı çıkış ölçütüyle yapılır.**

### §2.1 (a) Oturum / Cookie Domaini

| Kalem | Karar | Durum |
|---|---|---|
| Kimlik otoritesi | **Tek: `auth.coremusic.net`** — login, register, şifre sıfırlama, cinsiyet seçimi, logout, oturum rotasyonu **yalnız burada** | IMPLEMENTED (dizin 70 dosya) + **karar olarak tescil** |
| Cookie | `domain=.coremusic.net`, `path=/`, `HttpOnly`, `Secure` (HTTPS), `SameSite=Lax`, `lifetime=0` | **IMPLEMENTED korunur** (`SessionInitializer.php:40-47`, `SessionConfig.php:45`) — ADR-011 §2.2a-1 ile birebir |
| Diğer alt alanların görevi | **Session KURMAZ**; yalnız iki doğrulama yolu: **(1)** sunucu→sunucu `POST auth/validate-key` (IMPLEMENTED), **(2)** imzalı JWT (`§2.3`, kilit açıldığında) | (1) IMPLEMENTED · (2) PLANNED (ADR-020 kilidi) |
| Oturum başlatma yetkisi | `SessionBootstrapper::ensureStarted()` **yalnız auth** girişinde zorunlu; `home` (ve gelecek 8 PLANNED servis) `HomeAuthBridge` üzerinden geçer → `bootstrap.php:36-43` bypass dalı **kaldırılır** | PLANNED (§5.1 adım 3-4) |
| Logout | auth'ta `restartFresh()` + cookie temizliği `domain=.coremusic.net` → **tüm alt alanlarda oturum kapanır** (tek cookie = tek kapı) | IMPLEMENTED (temizlik kodu var) + **zorunluluk kararı** |
| Dağınık kimlik izi | `home`'ın kendi `HomeSessionManager` oturum yazımı yalnız **auth'tan gelen doğrulama sonrası** kullanılır; `MM_*` anahtarları tek kaynaktan (`shared` `SessionKeys`) | IMPLEMENTED (anahtarlar ortak) + PLANNED (yazım yetkisi daraltma) |
| Geçiş koruması | ADR-011 çelişkileri (`1800 < 3600`, iki rotasyon) **bu ADR'de çözülmez** — ADR-011'in debate şartıdır; bu ADR yalnız **kimin kuracağı** sorusunu çözer | ÇELİŞKİ açık (atıf) |

### §2.2 (b) CORS / CSRF / Nonce (ADR-010/012/013 hizası)

| # | Kural | Karar | Bugünkü durum |
|---|---|---|---|
| 1 | **Origin allowlist tek kaynak** | Subdomain listesi tek config'den türetilir (`coremusic.net` + alt alanlar); `CORS_ALLOWED_ORIGINS` **boşsa üretimde fail-closed** | E2: env boş → `[]` (**ÇELİŞKİ**) |
| 2 | **Boş-Origin fail-open KAPATILIR** | Origin başlığı yoksa istek **reddedilir** (ya da same-origin ispatı zorunlu); whitelist'e bakılmadan geçiş yasağı | E1: `OriginCheckMiddleware.php:36-41` **açık** → PLANNED |
| 3 | **Suffix-match korunur, bilinçli yazılır** | `str_ends_with($host, '.'.$allowedHost)` domain-scope olarak **kalır**; whitelist'e yalnız `coremusic.net` girilir, tek kelime = tüm alt alanlar | E3 IMPLEMENTED (kural olarak yazıldı) |
| 4 | **`/validate-key` muafiyeti KALKAR** | Cross-domain doğrulama ucu da Origin denetiminden geçer; sadece `localhost/127.0.0.1` dev fallback'i kalır | E4: muaf (**açık**) → PLANNED |
| 5 | **Preflight genişlemesi** | `allowed_headers` → `Content-Type, X-CSRF-Token, X-Requested-With, Authorization, X-Api-Key` | E5: eksik → ADR-020 §2.2d-4 ile ortak adım |
| 6 | **CSRF** | `csrf_token` (synchronizer) + `SameSite=Lax` **korunur**; kardeş alt alanlar same-site sayıldığından **SameSite tek başına yeterli DEĞİL** → token zorunlu (OWASP, §1.3-3) | ADR-010 uygulanıyor (CsrfMiddleware) |
| 7 | **Nonce paylaşımı** | Nonce **tek üretici** (`SecurityHeadersMiddleware`, 256-bit CSPRNG, `$_SESSION['csp_nonce']`); **çapraz subdomain'de nonce kopyalanmaz/taşınmaz** — alt alan ya auth'un ürettiği HTML'i alır ya kendi isteğinde kendi nonce'unu üretir | shared zincir IMPLEMENTED; **auth'daki ölü politika temizlenir** (ADR-012 §2.2f) |
| 8 | **Tek CSP şablonu** | ADR-012 §2.2a şablonu tüm domainlerde; `connect-src`/`img-src` subdomain listesi eklenir; `unsafe-inline/unsafe-eval` **kalıcı kapalı** | domain listesi YOK (ADR-012 PLANNED) |

### §2.3 (c) JWT / Anahtar Yönetimi (ADR-020 hizası)

| Kalem | Karar |
|---|---|
| **Tek kaynak** | İmzalama anahtarı (özel) **yalnız `auth.coremusic.net`'de** üretilir ve saklanır (env — REDACTED); **hiçbir diğer servise kopyalanmaz** |
| **Doğrulama** | Diğer alt alanlar/servisler **public anahtar + JWKS ucu (`kid`)** ile doğrular; özel anahtar dışarı çıkmaz → ADR-040 mantığı (tek yazar) kimlik anahtarına da uygulanır |
| **Rotasyon** | Yeni anahtar (`kid` yeni) → eski `kid` **overlap penceresi** yaşar → eski kapatılır + **audit olayı**; ADR-020 API key rotasyon satırıyla aynı mekanik |
| **Token profili** | RFC 8725: `alg` allowlist (`none` yasak), **`exp` + `iss` + `aud` zorunlu**; `iss = auth.coremusic.net`, `aud = *.coremusic.net` (arşiv `prompt2-auth` notu — **kodda yok → ⚠️ VERIFICATION REQUIRED**) |
| **Ömür** | access **≤15 dk** + rotasyonlu **tek kullanımlık refresh** (sunucu deposu, reuse → aile iptali) — ADR-011 §2.2b ile birebir; **localStorage yasak** |
| **Bearer kilidi** | `AuthenticationMiddleware.php:92-106` stub `null` sürece Bearer **AÇILMAZ** (ADR-010 şart 3); açılış koşulları ADR-020 §2.2c maddeleri (gerçek imza + `exp/iss/aud` + QA bypass testi + **ayrı onay**) |
| **Makine erişimi** | ADR-020 kalem 1 — **API key**: SHA-256 hash + `hash_equals` + scope (varsayılan `*.read`) + `allowed_ips` + `expires_at`; şema `coremusic_api.sql:31-57` IMPLEMENTED, **kod PLANNED** |

### §2.4 (d) Federasyon — OAuth2 / PKCE (ADR-020 auth üçlüsü kalem 3 + kalem 1)

| Kalem | Karar | Durum |
|---|---|---|
| Kullanıcı yönü (kalem 3) | **OAuth 2.1**: PKCE (`code_verifier`/`code_challenge`) **her istemci için zorunlu**, **exact redirect URI** (birebir, joker yok), `state` zorunlu, **Implicit yasak** | PKCE+state **IMPLEMENTED** (`OAuthManager.php:194-238`, 10 sağlayıcı, token AES-256-GCM); **third-party API erişimi PLANNED** (ADR-020 §1.1-B.4) |
| Sosyal bağlantı verisi | `oauth_connections` / `oauth_states` migration'ı tek yazıcı = auth (ADR-040) | IMPLEMENTED (migration dosyaları `shared/database/migrations/`) |
| Makine yönü (kalem 1) | API key (`X-Api-Key`) veya client_credentials — **kullanıcı cookie'si servis trafiğinde kullanılmaz** (§1.3-4) | Şema IMPLEMENTED / kod PLANNED |
| **Gerilim (dürüst kayıt)** | `HomeAuthBridge` sunucu tarafından auth'a **doğrudan HTTP** POST atar → ADR-039 §2.2-b "servis↔servis doğrudan HTTP yasak" kuralıyla **çatışır** | ⚠️ **AÇIK GERİLİM** → çözüm seçeneği §5.1 adım 7 (olay/IPC'ye taşınma ya da "istemci-güdümlü doğrulama" istisnasının ADR-039'a yazılması) |
| `ADR-088-gender-based-social-oauth` | `[[../index.md]]:110` kaydı var, **dosya diskte YOK** → wiki-link kurulmaz | ⚠️ VERIFICATION REQUIRED |

### §2.5 (e) Kademeli Geçiş (5 faz — her fazda çıkış ölçütü; big-bang YOK)

| Faz | Kapsam | **Çıkış ölçütü (ölçülür)** | Durum |
|---|---|---|---|
| **1** | **Envanter + Origin kilit:** CORS allowlist tek kaynağa taşınır; boş-Origin fail-open kapatılır; `/validate-key` muafiyeti kaldırılır | `OriginCheck` boş-Origin'de **403 döner** (test yeşil); env boşken üretim **fail-closed**; E1/E4 bulguları kodda 0 | ⏳ PLANNED |
| **2** | **Oturum tekliği:** oturum başlatma **yalnız auth**; `home` ve gelecek servisler yalnız `validate-key`/JWT ile geçer; bypass prod'da kapatılır | Repo geneli `SessionBootstrapper::ensureStarted()` yalnız `auth.coremusic.net/index.php`'de (grep=1); E2E: login → `music/home`'a geçiş **tek cookie**; audit C1 kapalı (`FORCE_AUTH_BYPASS=false`) | ⏳ PLANNED |
| **3** | **Doğrulama sertleştirme:** `use_strict_mode=1`, iki rotasyon değeri tek `SessionConfig`'e bağlanır, idle/absolute düzeltilir (ADR-011 şart 1) | `grep use_strict_mode` = 1; rotasyon tek değer; ADR-011 çelişki satırı 0 | ⏳ PLANNED (ADR-011'e bağlı) |
| **4** | **Anahtar + JWT:** JWKS/`kid` ucu, rotasyon overlap + audit, gerçek `validateJwtToken` (RFC 8725) | Bearer **401** (imzasız) + **200** (geçerli) testleri yeşil; stub `return null` **0 eşleşme**; rotasyon olayı audit'te | ⏳ PLANNED (ADR-020 kilidi — ayrı onay şart) |
| **5** | **Federasyon + kota + audit:** OAuth 2.1 third-party erişimi, API key doğrulama, **per-account** rate limit (ADR-013), audit PHP yazıcısı | scope ihlali → 403; rotasyon testi yeşil; `log_security`'ye satır düşüyor; per-account sayaç görünür | ⏳ PLANNED |

**Geçiş kuralı:** fazlar **sıralıdır** — faz 2 tamamlanmadan 4-5 başlamaz; her faz tek başına geri alınabilir (§5.2); çift kimlik penceresi **faz 2 içinde kapatılır** (eski dağınık oturumlar faz sonunda sonlandırılır).

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Statu quo — her alt alan kendi session'ını kursun** | En az iş | 8 PLANNED serviste 8 login; bypass/audit C1 gibi ikinci kapılar çoğalır; logout tek noktadan kapanmaz; per-account kota imkânsız | §1.3-1 (shared-cookie SSO'nun tam tersi); ADR-039 "auth en son ayrışır" hedefiyle çelişir |
| 2 | **Cookie'yi daralt (yalnız `auth.coremusic.net`)** | Cookie yüzeyi minimal | SSO kayar: her servis geçişinde yeniden login/redirect; redirect zincirinde cookie kaybı (§1.3-1: kök domain + dot rehberleri bunu tersini önerir) | Kullanıcı onayı "tüm kimlik tek auth, cookie `.coremusic.net`" — SSO vazgeçilmez |
| 3 | **Tam stateless JWT (session kaldır)** | Ölçek + basitlik | logout/iptal anlık değil; refresh reuse'da aile iptali yine sunucu ister; ADR-011 §2.2b "SSOT sunucu tarafı" ile ters | ADR-011 §3 alt.3'te zaten reddedildi; ADR-010 şart 3 da cookie'yi şart koşar |
| 4 | **Path-tabanlı tek origin (`coremusic.net/auth`, `/music`…)** | Cookie/scope = en basit model | ADR-004 §3-alt.4'te **red kayıtlı**: 7 subdomain + ayrı statik servis + ayrı portlar yıkılışı; `assets.coremusic.net` CDN'liği kayar | ADR-004 kararı (korunur); yeniden yazım maliyeti çok yüksek |
| 5 | **Big-bang geçiş (tek deploy'da 5 faz)** | Tek seferde "hedef mimari" | Kimlik taşıma literatüründe en riskli model (§1.3-6: ORy "single-phase riskli", WorkOS "flag day yok"); yarım durum = çift kimlik | §2.5 sıralı faz + çıkış ölçütü; ADR-039 big-bang yasağıyla aynı ilke |
| 6 | **mTLS/SPIFFE ile zero-trust servis kimliği** | Servis↔servis için en güçlü model | Altyapı yok (`.conf` = 0, ADR-009/013 bulgusu); PHP 8.4 uygulama katmanında sertifika yönetimi ek yük | **Faz 5'te değerlendirilecek** (açık bırakıldı); bugün API key + JWKS yeterli (YAGNI) |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **Tek kapı:** login/logout/rotasyon tek `auth.coremusic.net`'de → ADR-011 hijyen kuralları (login rotate, logout invalidation, privilege change) **tek yerde** uygulanır, 8 servise kopyalanmaz.
- **Cookie kararı zaten doğru yerde:** `.coremusic.net` + `HttpOnly` + `SameSite=Lax` IMPLEMENTED (§1.1-B) → bu ADR **ekstra değişiklik değil, yetki kısıtı** getirir (düşük uygulama maliyeti).
- **Kapılar kapanır:** boş-Origin fail-open (E1), `/validate-key` muafiyeti (E4), bypass (audit C1) → üç bilinen açık faz 1-2'de kapanır.
- **Ölçülebilir geçiş:** her fazın çıkışı **ölçülebilir eşik** (grep/test/audit satırı) ile tanımlı → yarım iş "tamamlandı" sayılmaz.
- **Dış destek:** 6 sorgu / 35 kaynak, üç ana iddiayı (shared-cookie SSO, same-site'ın yetmemesi, fazlı IdP geçişi) ≥2 kaynakla çaprazladı (§1.3).
- **Diğer ADR'lerle çakışma yok:** ADR-010 şart 3, ADR-011 §2.2a, ADR-012 §2.2a, ADR-020 §2.2c **korunur** — bu ADR onları genişletir, kaldırmaz.

### §4.2 Olumsuz Sonuçlar

- **Cookie domaini geniş kalır:** `domain=.coremusic.net` → her alt alan XSS'i **ortak** cookie yüzeyine taşır (R1); daraltmak SSO'yu kırar (§3 alt.2).
- **Çift kimlik penceresi:** faz 1-2 boyunca eski dağınık oturum + yeni tek auth birlikte var olabilir → faz çıkışı olmadan bırakılırsa **kalıcı ikilik** (R3).
- **Geçiş bağımlılığı:** faz 4, ADR-020'nin Bearer kilidini ve QA testini; faz 3, ADR-011 debate şartını bekler → **bu ADR tek başına tamamlanamaz**.
- **Gerilim taşındı, çözülmedi:** `HomeAuthBridge` ↔ ADR-039 "doğrudan HTTP yasak" çatışması (§2.4) çözüm kararı olmadan işaretli kalır.
- **Env değişkeni hassasiyeti:** CORS allowlist'i `.env`'den okunuyor (REDACTED) → yanlış/kısmi değer **ya her şeyi kırar ya her şeyi açar** (E2).
- **`status: accepted` ≠ yürürlükte uygulama:** onay kararın bağlayıcı olduğunu söyler; §5.1 adımlarının 9'u PLANNED'dır.

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 Cookie domaini genişliği → cross-subdomain XSS oturum çalar** (§1.3-2: Acunetix/PortSwigger/canitakeyoursubdomain) | Yüksek (>%70) | Yüksek | Cookie `HttpOnly+Secure` korunur; **CSP nonce + strict-dynamic** (ADR-012) her alt alan politikasında zorunlu; `domain` yalnız bu ADR'nin onayıyla değişir; XSS yüzeyi izlenir |
| **R2 Cookie tossing / alt alan tarafından gölgelenme** | Orta (%40-70) | Orta | `use_strict_mode` + oturum kimliği CSPRNG (faz 3, ADR-011); şüpheli cookie'ler logout'ta `domain=.coremusic.net` ile temizlenir |
| **R3 Geçişte çift kimlik — yarım durum, kırık logout** | Orta (%40-70) | Yüksek | Faz 2 çıkış ölçütü (grep tek `ensureStarted` + E2E tek cookie); eski oturumlar faz sonunda **sonlandırılır**; faz 4-5 kapalı |
| **R4 CORS kilitlenme — fail-closed sonrası legit istek 403** | Orta (%40-70) | Orta | Allowlist tek kaynaktan üretilir; dev fallback `localhost` ile sınırlı; faz 1'de preflight testi + rapor-only gözlem (ADR-012 kademelendirme mantığı) |
| **R5 Anahtar sızıntısı / rotasyonsuz eski anahtar** | Düşük (%10-40) | Kritik | Özel anahtar **yalnız auth** (REDACTED env); JWKS ile yayım; overlap + audit (§2.3); REDACTED denetimi her vault yazımında |
| **R6 Bearer kilidi erken açılırsa imzasız erişim** | Düşük (%10-40) | Kilit ihlali → CRITICAL | Kilitten çıkış koşulları **değişmez** (ADR-020 §2.2c): gerçek imza + `exp/iss/aud` + QA testi + **ayrı Tech Lead onayı**; ihlal = revert + log ERROR |
| **R7 `validate-key`/bypass geri gelir** | Orta (%40-70) | Yüksek | Faz 1-2 çıkış ölçütleri kalıcı test olarak yazılır; `FORCE_AUTH_BYPASS` prod false → regresyon testi |

### §4.4 Fallback (geri birleşim / geri dönüş)

1. **Faz 1 geri alınırsa:** boş-Origin fail-open geri açılabilir (eski davranış) — ama yalnız **development**; üretim `fail-closed` kalır ve `log.md`'ye gerekçe append edilir.
2. **Oturum tekliği taşınamazsa:** `home` gibi servisler **kendi session'ını kurmaya döner** → §2.1 satır 3 `PLANNED (geçici)` işaretlenir, **cookie domaini `.coremusic.net` DEĞİŞMEZ**, R1 açık risk olarak izlenir.
3. **JWKS/rotasyon kurulamazsa:** imzalama anahtarı **tek kaynaktan statik yayım** (dosya paylaşımı) ile geçici çözülür — özel anahtar yine kopyalanmaz; ADR-020 Bearer kilidi **açılmaz** (yalnız `validate-key` yolu hizmet eder).
4. **Federasyon ertelenirse:** OAuth 2.1 third-party erişimi **PLANNED** olarak kalır; sosyal login (IMPLEMENTED) olduğu gibi çalışır; API key yolu da kapalı kalabilir → üçüncü taraf erişimi **yok** demektir (kabul edilebilir, hizmet durmaz).
5. **`HomeAuthBridge` ↔ ADR-039 gerilimi çözülemezse:** doğrulama **istemci-güdümlü** (tarayıcı auth'a yönlenir, servis sunucusu aradan kalkar) olarak yeniden çizilir → yeni ADR (NNN+1) + bu dosya `superseded-by` ile bağlanır; metin silinmez.
6. **Tam geri birleşim:** tüm fazlar tek tek geri alınabilir (adım bazlı); `log.md` append-only olduğu için geri dönüş de **yeni satır**dır (silme yok).

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Bu ADR'yi şablondan üret + künye/§1-§7 dolu (Guardrail #16) | Vault Steward | 2 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | `.ai/log.md`'ye 1 satır append: `ADR-043 yazıldı (debate PENDING)` | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | **Faz 1 — Origin/CORS kilidi:** boş-Origin fail-closed (`OriginCheckMiddleware.php:36-41`) + allowlist tek kaynak + `/validate-key` muafiyetinin kaldırılması (`auth.../OriginCheckMiddleware.php:35`) + preflight header genişlemesi | Security + Backend | 2 gün | ⏳ PLANNED |
| 4 | **Faz 2 — oturum tekliği:** `ensureStarted()` yalnız auth'a; `home/config/bootstrap.php:36-43` bypass dalının kaldırılması + `FORCE_AUTH_BYPASS` prod kapatma (audit C1); E2E tek-cookie testi | Security + QA | 3 gün | ⏳ PLANNED |
| 5 | **Faz 3 — oturum sertleştirme:** `use_strict_mode`, tek rotasyon değeri, idle 30 dk / absolute 8 saat (ADR-011 debate şartı 1) | Security | 2 gün | ⏳ PLANNED (ADR-011'e bağlı) |
| 6 | **Faz 4 — anahtar/JWKS + gerçek JWT:** `kid`/JWKS ucu, rotasyon overlap + audit, `validateJwtToken` RFC 8725 impl. + QA bypass testi + **ayrı onay** | Security + Tech Lead | 5 gün | ⏳ PLANNED (ADR-020 kilidi) |
| 7 | **Gerilim çözümü:** `HomeAuthBridge` sunucu→sunucu `validate-key` çağrısının ADR-039 §2.2-b ("doğrudan HTTP yasak") ile ilişkisi — istisna mı, IPC/olay mı → **karar + gerekirse yeni ADR** | Tech Lead + Backend | 1 gün | ⏳ PLANNED (açık gerilim §2.4) |
| 8 | **Faz 5 — federasyon + kota + audit:** OAuth 2.1 third-party (exact redirect), API key doğrulama (SHA-256 + scope), per-account rate limit (ADR-013), audit PHP yazıcısı | Backend + Security | 5 gün | ⏳ PLANNED |
| 9 | **index.md düzeltmesi (raporlandı):** `.ai/.decisions/index.md:85` satırındaki `[[../brain.md]] ADR-043-auth-subdomain-consolidation` → **`[[accepted/ADR-043-auth-subdomain-consolidation]]`** (§4 satırlarındaki `[[accepted/ADR-040-database-authority]]` biçimiyle aynı); **uygulama son sıfırmaya erteledi** | MO (vault-updater) | 1 dk | ⏳ ERTELENDİ (rapor: §7.1 son satır) |
| 10 | Debate (3 tur / 20 persona) + Tech Lead onayı | MO + Tech Lead | 2 gün | ✅ UYGULANDI (2026-09-26) |
| 11 | `brain.md:1002` ADR-043 slotuna karar özeti + `keys.md:278` karşılığı (vault sync) | MO (vault-updater) | 1 dk | ⏳ PLANNED |

### §5.2 Geri Dönüş Planı

1. **Karar seviyesi:** `status: accepted` ama **frozen değil** → vazgeçiş = yeni ADR (NNN+1, 088+ kuralı) ve bu dosya `superseded-by` ile bağlanır; metin **silinmez**.
2. **Faz seviyesi:** fazlar bağımsız geri alınır (§4.4-1..4); faz 2 geri alınırsa cookie domaini **yine `.coremusic.net`** kalır — geri dönüş yalnız "kimin session kurduğu" satırını etkiler.
3. **Kod seviyesi:** `OriginCheckMiddleware`/`SessionConfig` davranışı eski hâline döndürülür → adlar değişmediği için wiki-link/`@see` referansları kırılmaz (In-Place Refactoring).
4. **Test kapısı:** her fazın çıkış ölçütü regresyon testi olarak kalır; geri dönüş = testin kırmızıya dönmesi + `log.md`'ye yeni satır.
5. **Bozulmada:** `node .ai/scripts/vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout` (AGENTS §18 #5).

---

### §5.3 Debate Şartları (bağlayıcı — 2026-09-26, 3 tur / 20 persona · 19/1/0 KABUL)

> Debate (§7.1) **19/1/0 KABUL** ile sonuçlanmıştır; kabul **3 şartla** bağlanmıştır. Şartlar bu ADR'nin bağlayıcı parçasıdır — §5.1 adımları 3, 4 ve 7 bu şartlar **olmadan** kapanmaz.

| # | Şart | Kapsam | Bağlantı | Durum |
|---|------|--------|----------|-------|
| **1a** | **HomeAuthBridge çözüm yolu** | `HomeAuthBridge` sunucu→sunucu `validate-key` çağrısının ADR-039 "doğrudan HTTP yasak" çelişkisi (§2.4) için **çözüm yolu** seçilir: **outbox / ADR-032 IPC ya da ADR-039'a istisna kaydı** — karar yoksa adım 7 açık kalır | §2.4, §1.4 (ADR-039 kısıtı), §5.1 adım 7 | ⏳ PLANNED (Tech Lead) |
| **1b** | **Origin + allowlist kapatma fazı** | `/validate-key` origin muafiyeti (`auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php:35`, E4) + boş CORS allowlist (`auth.coremusic.net/config/cors.php:11`, E2) kapatılır → **allowlist + muafiyet kapatma fazı**; boş-Origin fail-open (E1) de aynı fazda kapanır | §2.2-1/2/4, §5.1 adım 3, §2.5 faz 1 | ⏳ PLANNED (Security) |
| **2** | **Geçiş faz ölçütleri** | Çift kimlik riski (§1.2-8, R3) → her fazın **çıkış ölçütü + rollback** koşulu bağlayıcı kalır; faz 2 çıkmadan faz 4-5 başlamaz; çift kimlik penceresi faz 2 içinde kapanır | §2.5, §5.2, §4.3-R3, §4.4 | ⏳ PLANNED (bağlayıcı) |
| **3** | **Oturum / CSRF / rotation testi** | **(a)** cross-subdomain oturum testi (login → `music/home`'a **tek cookie** geçişi) · **(b)** CSRF testi (`csrf_token` + `SameSite=Lax`) · **(c)** JWT rotasyon testi (overlap + audit) — üçü de yazılmadan debate şartı **sayılmaz** | §5.1 adım 4, §2.2-6, §2.3, §2.5 faz 2/4 | ⏳ PLANNED (QA) |

**Şart kuralı:** 3 şart da kapanmadan §5.1 adımları 3, 4, 7 **"tamamlandı" sayılmaz**; her kapanış `log.md`'ye ayrı satır olarak append edilir (append-only).

---

## §6 İlgili Dokümanlar

**Wiki-link hedefleri tek tek `Test-Path` ile doğrulandı: bu dosyanın dizininden çözümlenen **28/28 benzersiz hedef diskte** (tablodaki 28 satır); `[[../index.md]]`:85 düzeltme taslağındaki 3 alıntı hedef ise `../index.md`'in dizini (`.ai/.decisions/`) üzerinden **3/3 doğrulandı** — toplam **31/31 benzersiz hedef diskte** (50 link / 31 benzersiz).**

| Dosya (wiki-link) | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme; ADR serisi kuralı (001-037 frozen, yeni 088+), `csrf_token` kuralı |
| [[../../brain.md]] | `:1002` ADR-043 slotu · `:848` prompt2/auth k6-k7 atfı |
| [[../../AGENTS.md]] | Routing (Security), escalation §10, frozen kuralı §25.3, §17 edge case "Bilinmeyen class → VERIFICATION REQUIRED" |
| [[../../WORKFLOW.md]] | Süreç/fazlar — §5.1 adımlarının bağlandığı akış |
| [[../../index.md]] | Master katalog |
| [[../../log.md]] | Audit trail — bu ADR'nin append kaydı |
| [[../../glossary.md]] | `validate-key` (:155), hexagonal/CQRS (:234), hibrit JWT+session (:431) |
| [[../../keys.md]] | `:278` ADR-043 anahtar kaydı |
| [[../../MEMORY.md]] | Session hafızası + prompt2/auth kaydı (:325) |
| [[../index.md]] | `:85` slug satırı (**düzeltme §5.1 adım 9**) |
| [[../CLAUDE.md]] | Karar dizini kuralı |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 şablonu (bu dosyanın iskeleti) |
| [[../../.templates/adr/adr-security-template.md]] | Güvenlik ADR alan kontrolü |
| [[../../architecture/k6-guvenlik/README.md]] | K6-01 Auth System (JWT+Session hybrid) + `:154` ADR-043 atfı |
| [[../../architecture/k8-servis/README.md]] | Servis sınırı + olay/IPC kuralları (§2.4 geriliminin kaynağı) |
| [[../../reports/auth-bypass-audit.md]] | 10 bulgu (3 CRITICAL) — C1 `FORCE_AUTH_BYPASS` (§1.1-A) |
| [[ADR-004-multi-domain-spa]] | Subdomain iskeleti — bu ADR onu **korur**; path-tabanlı alternatif orada reddedilmiş |
| [[ADR-008-bypass-auth-middleware]] | Bypass kapsamı — yalnız onayla değişir (faz 2 bypass kapatması bu kurala tabi) |
| [[ADR-010-csrf-protection-strategy]] | **Şart 3** (cookie-auth tek yol) + `csrf_token` + SameSite=Lax — §2.2-6 |
| [[ADR-011-session-management]] | Cookie parametreleri + hibrit saklama + idle/absolute çelişkisi — §2.1/§5.1 adım 5 |
| [[ADR-012-csp-nonce-strict-dynamic]] | Tek politika + nonce zinciri + auth ölü politikası — §2.2-7/8 |
| [[ADR-013-rate-limiting-apcu]] | Per-account kota + APCu — faz 5 |
| [[ADR-020-api-public-security]] | Auth üçlüsü (1 API key / 2 JWT / 3 OAuth2 PKCE) + Bearer kilidi + CORS header — §2.3/§2.4 |
| [[ADR-034-credential-vault-normalization]] | Credential saklama normalizasyonu — anahtar yönetim hattı |
| [[ADR-039-7-service-platform-architecture]] | 11 servis + servis sınırı + "doğrudan HTTP yasak" — §1.1-C, §2.4 gerilimi |
| [[ADR-040-database-authority]] | Tek yazar/tek sahip — kimlik verisi ve anahtar için de geçerli (§2.3) |
| [[ADR-041-database-normalization-supplementary]] | Aynı seferin debate/şablon format referansı |
| [[ADR-042-vault-restructuring-2026-08-03]] | **Format referansı** (künye + §1-§7 + numara notu) |

**Wiki-link KURULMAYAN (diskte dosya YOK → düz metin + `⚠️ VERIFICATION REQUIRED`):**

| Referans | Durum |
|---|---|
| **ADR-084-api-gateway-architecture** | `AuthenticationMiddleware.php:9` `@see` veriyor, `index.md:106` kaydı var, **dosya diskte YOK** → wiki-link kurulmadı |
| **ADR-088-gender-based-social-oauth** | `index.md:110` kaydı var, **dosya diskte YOK** (draft/rejected dahil glob boş) → §2.4'te düz metin |
| `architecture/k6-k7-security/k06-auth-layer/auth-cross-domain.md` | `brain.md:848` bu yolu veriyor, **diskte YOK** → wiki-link kurulmadı |
| `ADR-016`, `ADR-047`, `ADR-056` (arşiv prompt'larında ADR-043'le anılan) | Dosyalar diskte YOK → yalnız atıf, link yok |

**Debate şartları (§5.3 — 3 şart, bağlayıcı):** (1) **HomeAuthBridge + origin kapatma** — 1a: `HomeAuthBridge` ↔ ADR-039 doğrudan-HTTP çelişkisinin outbox/ADR-032 IPC ya da istisna kaydı ile çözülmesi (§5.1 adım 7); 1b: `/validate-key` origin muafiyeti + boş CORS allowlist'in kapatılması (faz 1, §5.1 adım 3); (2) **geçiş faz ölçütleri** — her fazda çıkış ölçütü + rollback, çift kimlik penceresi faz 2'de kapanır (§2.5, §5.2); (3) **oturum/CSRF/rotation testi** — cross-subdomain tek-cookie oturum + `csrf_token` + JWT rotasyon testleri (§5.1 adım 4).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-09-26 | ✅ |
| Tech Lead | — | 2026-09-26 | ✅ |
| Arch Lead | — | — | ⏳ |

### §7.1 Tartışma Kaydı (Debate) — ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)

| Alan | Değer |
|---|---|
| **Durum** | ✅ **TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** — frontmatter `debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"` · **Tech Lead ✅ (2026-09-26)** · Arch Lead ⏳ |
| **Karar kapsama kaynağı** | Kullanıcı onaylı tam kapsam: (a) oturum/cookie domaini (tek auth, `domain=.coremusic.net`, diğer alt alanlar yalnız token doğrular) · (b) CORS/CSRF/nonce (ADR-012/013 hizası — Origin listesi + nonce paylaşımı) · (c) JWT/key yönetimi (tek kaynak + rotasyon) · (d) federasyon OAuth2/PKCE (ADR-020 kalem 1 ve 3) · (e) kademeli geçiş (§2.5, 5 faz + çıkış ölçütü) |
| **Bekleyenler** | Debate **✅ 3/20 (19/1/0 KABUL)** · Tech Lead **✅ (2026-09-26)** · **Arch Lead ⏳** · **Şartlar 1a/1b/2/3 (§5.3)** · §5.1 adımları 3-9, 11 · **Açık gerilim → şart 1a:** `HomeAuthBridge` ↔ ADR-039 doğrudan-HTTP yasağı (§2.4, adım 7) · **ERTELENEN:** `[[../index.md]]:85` wiki-link düzeltmesi (§5.1 adım 9) |
| **Statü özeti** | `status: accepted` (kullanıcı onaylı kapsam) · debate **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** · **Tech Lead ✅** · **Arch Lead ⏳** · **frozen YOK** (ADR-001-037 dokunulmaz; bu dosya Active aralığı) · wiki-link **31/31 benzersiz hedef diskte (50 link; 28 dosya dizini + 3 index.md bağlamı)** · §5.1 adımlarının **7'si PLANNED + 1 ERTELENDİ (adım 9) + adım 10 ✅** · §1.3 **6 sorgu / 35 kaynak** · **Şartlar: §5.3 (3 madde — 4 kalem)** |

#### §7.1.1 Tur 1 — Bulgu turu (20 persona)

**Katılım:** 20 persona · **Dağılım:** 16 kabul/neutral · 3 uyarı · **Kaynak:** 35 kaynak / 6 sorgu (§1.3).

**Bulgu listesi:**

1. **auth IMPLEMENTED** — `auth.coremusic.net/` **70 dosya / 46 `.php`** (§1.1-A) → "auth IMPLEMENTED" iddiası doğrulandı.
2. **Cookie domain `.coremusic.net` 8 noktada zaten mevcut** (§1.1-B) → bu ADR'nin katkısı **alan genişliği değil, yetki kısıtı**.
3. **OriginCheck fail-open** — boş `HTTP_ORIGIN` whitelist'e bakılmadan geçer (`shared/src/Middleware/OriginCheckMiddleware.php:36-41`, E1).
4. **`cors.php:11` allowlist boş** — `CORS_ALLOWED_ORIGINS` yoksa `[]` (E2) → dolu Origin prod'da 403, Origin'siz istek açık.
5. **`/validate-key` origin muafiyeti** (`auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php:35`, E4) — cross-domain'in en kritik ucu denetim dışı.
6. **JWT stub `return null` DOĞRULANDI** (`shared/src/Api/Middleware/AuthenticationMiddleware.php:92-106`, E6) → Bearer yolu fiilen ölü.
7. **`HomeAuthBridge` doğrudan HTTP ↔ ADR-039 "doğrudan HTTP yasak"** çelişkisi (§2.4, §5.1 adım 7).
8. **35 kaynak / 6 sorgu** (§1.3) doğrulandı; **`index.md:85` düzeltmesi ertelendi** (§5.1 adım 9).

**Uyarılar (3):**

| Persona | Uyarı |
|---|---|
| QA | CORS şart (allowlist + preflight genişlemesi) |
| Critic | HomeAuthBridge ↔ ADR-039 çelişkisi şartı |
| Critic | `/validate-key` origin muafiyeti şartı |

#### §7.1.2 Tur 2 — İtiraz → çözüm

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | `HomeAuthBridge` ↔ ADR-039 "doğrudan HTTP yasak" çelişkisi (§2.4) | **Çözüm yolu** — outbox / ADR-032 IPC **ya da** ADR-039'a istisna kaydı (§5.1 adım 7) | **Şart 1a** |
| 2 | `/validate-key` origin muafiyeti (E4) + `cors.php:11` boş allowlist (E2) | **Allowlist + muafiyet kapatma fazı** (faz 1, §5.1 adım 3) | **Şart 1b** |
| 3 | Geçiş riski — çift kimlik yarım durumu (§1.2-8, R3) | **Faz çıkış ölçütleri + rollback** (§2.5, §5.2) | **Şart 2** |
| 4 | Test yok — oturum/CSRF/rotation doğrulanmadı | **Cross-subdomain oturum + CSRF + JWT rotation testi** (§5.1 adım 4) | **Şart 3** |

> Şartların tam metni ve bağlantıları: **§5.3** (bağlayıcı) + §6 son satır.

#### §7.1.3 Tur 3 — Oy

| Seçenek | Oy |
|---|---|
| **KABUL** | **19** |
| Çekimser | 1 |
| Red | 0 |

**Sonuç: KABUL (19/1/0) — 3 şartla** (§5.3: **1a** HomeAuthBridge çözüm yolu · **1b** allowlist + `/validate-key` origin muafiyeti kapatma · **2** geçiş faz ölçütleri + rollback · **3** oturum/CSRF/JWT-rotation testi). Tech Lead 2026-09-26 ✅.

---

*ADR-043 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-043 Karar Metni (SSOT)*
*Last Updated: 2026-09-26*
*Mode: Red Team · Human Mode · Truth Mode*
*Debate: ✅ TAMAMLANDI (3/20, 19/1/0 KABUL) · Tech Lead: ✅ · Arch Lead: ⏳*
