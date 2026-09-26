---
title: "CoreMusic — ADR-010: CSRF Protection Strategy (Üç Katmanlı Savunma: Synchronizer Token + SameSite + Origin/Referer)"
type: adr
category: security
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-010 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-010: CSRF Protection Strategy (Üç Katmanlı Savunma: Synchronizer Token + SameSite + Origin/Referer)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-010'u sıfırdan yaz") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL) · Tech Lead: ✅
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (çoklu domain SPA — token akışı bu haritanın domain'lerinde dolaşır, oturum cookie'si `.coremusic.net` domain'iyle tüm subdomainlere yayılır; dosya diskte VAR ✅) · **Düz metin (dosya diskte VAR — kullanıcı onayıyla wiki-link KURULMAZ): ADR-008** (`.ai/.decisions/accepted/ADR-008-bypass-auth-middleware.md` — auth bypass kapsamı; CSRF katmanları auth bypass'tan bağımsızdır ve bypass scope'u bu ADR ile değişmez) · **Düz metin (dosya diskte YOK — wiki-link kurulmaz): ADR-011** (Session Management — `henüz yazılmadı — vault: .ai/.decisions/index.md §3 [[ADR-011-session-management]]` kaydı var) · **Düz metin (dosya diskte YOK): ADR-012** (CSP Nonce Strict-Dynamic — `henüz yazılmadı — vault: .ai/.decisions/index.md §3 [[ADR-012-csp-nonce-strict-dynamic]]` kaydı var; XSS↔token kesişiminin hedefi) · karar dizini [[../index]] §3 satırı `[[ADR-010-csrf-protection-strategy]]` (slug eşleşmesi ✅ — satır diskte mevcut, satır 47).

---

## 1. Bağlam (Context)

CoreMusic'un **state-changing tüm istekleri cookie tabanlı oturumla** çalışır: oturum cookie'si `domain=.coremusic.net` (subdomainler arası paylaşım), `SameSite=Lax`, `HttpOnly` ve HTTPS'te `Secure` ile ayarlanır; tarayıcı bu cookie'yi aynı-site her isteğe otomatik ekler. CSRF (Cross-Site Request Forgery) yüzeyi burada doğar: saldırgan, mağdurun tarayıcısına güvenilen siteye **kendisi istemediği bir mutating istek** yaptırtır (para transferi, şifre/rol değişikliği, profil güncellemesi). Karar; **tek savunma katmanına bel bağlamak yerine ÜÇ KATMANLI derin savunma** kurar: **(1) Synchronizer token** (`csrf_token` — sunucu oturumuna bağlı, timing-safe karşılaştırma), **(2) SameSite cookie** (`Lax` varsayılan; mümkünse `Strict` — tarayıcı/UX uyumu §2.2d'de not edilir), **(3) Origin/Referer doğrulama** (POST/PUT/DELETE'te Origin host ile eşleşmeli; yoksa Referer; ikisi de yoksa **REDDET — fail-closed**). Ek **(4) yedek:** double-submit cookie — **yalnız Origin/Referer başlıklarının okunamadığı senaryolar için**. Üstüne **API kuralı** (cookie kullanan TÜM mutating endpoint'ler token ister — JSON dahil; **Bearer token kullanan endpoint CSRF'e muaf**) ve **SPA token akışı** (meta/gizli alandan JS okur → `X-CSRF-Token` header'ı; ADR-004 SPA uyumu) sabitlenir.

### 1.1 Mevcut Durum

**Kod kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yoluyla):**

- **`shared/src/Middleware/CsrfMiddleware.php` (59 satır) — KATMAN 1 ÇEKİRDEĞİ IMPLEMENTED:** synchronizer token doğrulaması — session `csrf_token` (satır 35: `$_SESSION['csrf_token']` / `$request['_session']['csrf_token']`) karşı `x-csrf-token` header'ı (satır 37) **veya** body `csrf_token` (satır 39); karşılaştırma **`hash_equals` = timing-safe IMPLEMENTED** (satır 42); korunan metotlar GET/HEAD/OPTIONS dışındaki **her şey** (satır 24 — POST/PUT/DELETE/PATCH); eksik/yanlış token → **403 `csrf_invalid`** (satır 49-58, `halt: true`); bypass route listesi **boş** — `set-gender` bypass'ı kaldırılmış (satır 15-17; gerekçe satır 16: "A01:2021 Broken Access Control riski"). **Origin/Referer doğrulaması YOK → PLANNED** (bu ADR §2 katman 3). **Double-submit cookie karşılaştırması YOK → PLANNED (yalnız yedek — §2 katman 4).** Pipeline kaydı: `shared/src/PageRouter/PageRouterKernel.php::buildDefaultMiddlewares()` **satır 278** — pipeline **#6** (sıra: OriginCheck #1 satır 273 → Cors #2 → RateLimiter #3 → SecurityHeaders #4 → SessionManager #5 → **Csrf #6**; `shared/CLAUDE.md` satır 114 ile uyumlu). Test: `shared/tests/Middleware/CsrfMiddlewareTest.php` — "ADR-010: csrf_token key (NOT _csrf_token)" (satır 11, 299-318) + GET/HEAD/OPTIONS atlama (satır 29-79) + yanlış/eksik token red (satır 82-111, 240-296) → **test altyapısı IMPLEMENTED**.
- **Token üretimi — `shared/src/Session/SessionLifecycle.php` (satır 47-49):** token yoksa/boşsa `bin2hex(random_bytes(32))` (64 hex karakter, CSPRNG) → **oturum başlangıcı üretimi IMPLEMENTED**. **Privilege change rotasyonu YOK:** session ID 30 dakikada bir `session_regenerate_id(true)` ile dönerken (satır 54-62, `SessionConfig::ROTATION_INTERVAL`) `csrf_token` **sabit kalır** (yalnız yoksa yeniden üretilir) → **"privilege change'de yenile" kuralı PLANNED** (§2.2c).
- **SameSite — `shared/src/Session/SessionInitializer.php` (satır 40-47):** `session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'domain' => '.coremusic.net', 'secure' => $isHttps, 'httponly' => true, 'samesite' => 'Lax'])` → **`SameSite=Lax` IMPLEMENTED** (+ HttpOnly, conditional Secure, **subdomain-geniş domain** — ADR-004/§4.3 risk 3 ile kesişir). Aynı parametre `shared/src/Session/SessionBootstrapper.php` satır 45 (`setcookie`) ve satır 80 (`session_set_cookie_params($config->cookieParams())` — SSOT `SessionConfig`) üzerinden de uygulanır. **`SameSite=Strict` YOK → PLANNED** (§2.2d). Doküman doğrulaması: `shared/src/Session/CLAUDE.md` satır 49 ve `shared/src/Middleware/CLAUDE.md` satır 102 "Cookie: HTTPOnly, Secure, SameSite=Lax" — **kodla uyumlu ✅** (çelişki yok).
- **`shared/src/Middleware/SecurityHeadersMiddleware.php` (satır 26-41):** başlık listesinde **`Set-Cookie`/SameSite YOK** — cookie katmanı bu middleware'de değil, Session katmanındadır (yukarıdaki satır 40-47). CSRF ile kesişen defanslar **IMPLEMENTED:** `Content-Security-Policy` nonce + `'strict-dynamic'` (satır 31, 53-55 — ADR-012 kesişimi), **`form-action 'self'`** (satır 71 — cross-site form submit'i keser), `frame-ancestors 'none'` (satır 69), **`Referrer-Policy: strict-origin-when-cross-origin`** (satır 30 — katman 3'ün Referer okumasını besler: same-origin istekte tam Referer gider), `X-Content-Type-Options: nosniff` (satır 27).
- **`shared/src/Security/ReturnUrlPolicy.php` (satır 7-80):** **`Set-Cookie`/SameSite ile ilgisi YOK** (görevi open-redirect allowlist'i: `javascript/data/vbscript` yasağı satır 65, user-info yasağı satır 69-71, host suffix allowlist satır 73-77) — redirect tabanlı token/istek taşıma yüzeyini kapatan **IMPLEMENTED** destek katmanıdır; `strtolower($host)` (satır 62) host karşılaştırmada kullanılır.
- **Origin kontrolü — `shared/src/Middleware/OriginCheckMiddleware.php`:** Origin başlığı allowlist doğrulaması **IMPLEMENTED** (satır 43-54 → izinsiz origin'de 403 `origin_not_allowed`; allowlist suffix eşlemesi satır 67-71 `str_ends_with($host, '.' . $allowedHost)`; **production'da dev fallback YOK** satır 74-76 = kısmen fail-closed). **AMA kritik boşluk: Origin başlığı YOKSA istek sessizce geçer — fail-OPEN** (satır 39-41 `if ($origin === '') return $next($request)`) ve **Referer fallback'i YOK** → bu ADR'nin fail-closed kuralıyla (Origin yoksa Referer, ikisi yoksa reddet) **çelişir → PLANNED değişiklik**. Kayıt/pipeline: **#1** sıra `PageRouterKernel.php` satır 273; doc comment satır 13: "ADR-010/022 uyumlu. Frozen sıra: OriginCheck → Cors → RateLimiter → ..." (kod ile tutarlı ✅).
- **SPA token akışı:** **gizli alan IMPLEMENTED** — `shared/src/PageRouter/HtmlShellRenderer.php` satır 129: `<input type="hidden" name="csrf_token" id="csrf-global" value="...">` (escape satır 107 `$h($csrfToken)`); **`<meta name="csrf-token">` YOK → PLANNED** (§2.2c). **JS header gönderimi IMPLEMENTED:** `assets.coremusic.net/js/router/config/headers.js` satır 3 (`X_CSRF_TOKEN: 'X-CSRF-Token'`), `assets.coremusic.net/js/router/AuthHandler.js` satır 33/47/61 (fetch `/login`, `/register`, `/set-gender` — `credentials: 'include'` + DOM'dan `querySelector('[name=csrf_token]').value` okuma), `auth.coremusic.net/pages/login.php` satır 147, `register.php` satır 233, `logout.php` satır 50, `set-gender.php` satır 105, `assets.coremusic.net/js/oauth-manager.js` satır 53/119, `assets.coremusic.net/js/auth/gender-select.js` satır 53. **CORS allowlist `X-CSRF-Token`:** `shared/src/Middleware/CorsMiddleware.php` satır 32 + `auth.coremusic.net/config/cors.php` satır 13 (`allowed_headers: ['Content-Type', 'X-CSRF-Token', 'X-Requested-With']`) **IMPLEMENTED**. **JSON yanıtından token akışı:** `assets.coremusic.net/js/router/ContentFetcher.js` satır 72 (`csrfToken: json.csrf_token`) **IMPLEMENTED**. Token dağıtımı `PageRouterKernel` satır 106-107 (`$req['_session']['csrf_token'] ?? $_SESSION['csrf_token']`) + `PageRouter.php` satır 128-129 (sayfa içi gizli alan) ile sağlanır.
- **API yüzeyi — `shared/src/Api/Middleware/` (6 dosya: Authentication, Authorization, RateLimit, RequestValidation, ResponseNormalization, ApiMiddlewarePipeline):** **`CsrfMiddleware` KAYDI YOK** (grep: `CsrfMiddleware` yalnız `PageRouterKernel.php` satır 278'de register ediliyor) → **cookie auth'lı API mutating endpoint'leri (JSON dahil) bugün CSRF katmanı DIŞINDA → PLANNED** (bu ADR §2 "API kuralı" ile kapatılır). Kimlik doğrulama **hybrid:** `AuthenticationMiddleware.php` **önce session/cookie** (satır 37-45, `method: 'session'`), sonra `Bearer ` prefix (satır 48-57, `HTTP_AUTHORIZATION`) — **Bearer yolu plumbing IMPLEMENTED ama `validateJwtToken` stub'ı hep `null` döner** (satır 92-106: "simplified implementation... return null (not validated)") → **JWT doğrulama PLANNED** ⚠️ VERIFICATION REQUIRED (üretim Bearer muafiyeti ancak JWT imza doğrulaması available olunca geçerli olur; o güne kadar API mutating için token katmanı varsayılandır).
- **OAuth flow state (tamamlayıcı katman):** `shared/database/migrations/oauth_states_migration.php` satır 4/18 ("oauth_states — CSRF state token yönetimi") + `shared/src/OAuth/Provider/BaseOAuthProvider.php` satır 64 + `OAuthManager.php` satır 192 → **harici OAuth akışında state-CSRF IMPLEMENTED** (bu ADR'nin oturum-içi katmanını tamamlar; login/register OAuth callback'leri kapsam içi).
- **Sıra kilitleri:** `shared/tests/Middleware/MiddlewarePipelineTest.php` satır 13: "Pipeline sırası değişirse CSP/CSRF bozulur" (test satır 195/212 `6_Csrf` sırasını zorlar) → **pipeline sırası test ile korumalı IMPLEMENTED**; `shared/AGENTS.md` Zorunlu #2: middleware ekleme/değişiklik → `MiddlewarePipeline` kaydı günceldir.

### 1.2 Sorun Tanımı

(1) **Tek katman bağımlılığı:** bugünkü koruma ağırlıklı olarak synchronizer token + Lax; **Origin/Referer katmanı yok** ve **OriginCheck fail-open** — Origin başlığı silinen/olmayan istekler doğrudan CsrfMiddleware'e düşer, o da yalnız token'a bakar (§1.1). (2) **SameSite=Lax tek başına yetersiz:** Lax top-level GET navigasyonlarında cookie taşır — GET ile state değiştiren bir endpoint varsa Lax durdurmaz; ayrıca SameSite yalnız **cross-site**'i kapsar, **cross-origin ama same-site** (subdomain) saldırılarını kapsamaz (§1.3 kaynak 6, 7, 8). (3) **API kuralı yazılı değil:** cookie auth'lı API mutating endpoint'leri CsrfMiddleware dışında (§1.1) — hangisinin token zorunluğuna tabi olduğu kararlaştırılmamış. (4) **Bearer muafiyeti gerekçesiz:** Bearer uçlar CSRF'e muaf mı, değil mi — gerekçe vault'ta yok (§1.3 kaynak 15-19). (5) **Token rotasyonu eksik:** privilege change (login, rol/izin değişikliği, email/şifre değişiminde oturum devri) sonrası eski token yaşamaya devam eder (§1.1 SessionLifecycle). (6) **SPA okuma yolu eksik:** meta tag yok; token yalnız gizli alandan okunuyor — SPA (ADR-004) derleme akışı için meta/cookie yolu PLANNED. (7) **Strict mümkün mü belirsiz:** `SameSite=Strict`'in oturum cookie'sindeki navigasyon etkisi ve yedek token cookie'sine uygulanabilirliği kararlı değil (§1.3 kaynak 3, 6). (8) **Double-submit yedeği yok:** Origin/Referer okunamayan senaryolar (privacy-proxy header strippi, non-browser client) için yedek mekanizma tanımsız; naive double-submit'in subdomain cookie-injection zaafı da yazılmamış (§1.3 kaynak 9-12). (9) **Login CSRF kapsamı net değil:** login POST'unun da token gerektirdiği gerekçesiyle birlikte vault'ta sabit değil (§1.3 kaynak 13, 14).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅, v7.2.0) — birincil/resmî kaynak önce (OWASP Cheat Sheet güncel sürüm, W3C Fetch Metadata, Chromium/web.dev, MDN/PortSwigger), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; **güvenlik iddiası = OWASP + official zorunlu**. **Odak: (a) OWASP CSRF Cheat Sheet güncel sürümü (synchronizer vs double-submit vs Origin/Referer vs Fetch Metadata); (b) SameSite 2025-26 tarayıcı durumu (Lax/Strict/None, Lax+POST istisnasının çöküşü, Lax-by-default); (c) double-submit eleştirileri (client-settable cookie, subdomain/DNS takeover); (d) login CSRF; (e) JSON POST'ta token + Bearer (Authorization header) muafiyet gerekçesi.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "OWASP CSRF Prevention Cheat Sheet 2025 synchronizer token double submit signed login CSRF JSON" · (2) "SameSite cookie 2025 2026 browser status Lax Strict None Lax+POST removal Chrome default" · (3) "double submit cookie CSRF weakness client-settable cookie subdomain DNS takeover" · (4) "login CSRF attack prevention OWASP SameSite Lax not enough" · (5) "JSON POST CSRF token required Bearer Authorization header not auto-sent immune" |
| Web Search **Konusu** | Güncel OWASP CSRF Prevention Cheat Sheet'in önerdiği savunma hiyerarşisi (stateful → synchronizer; stateless → signed double-submit; Origin/Referer fail-closed; custom header; Fetch Metadata); SameSite değerlerinin 2025-26 tarayıcı davranışı (Chrome 80+ Lax-by-default, Lax+POST 2 dk istisnasının düşüşü, None+Secure zorunluluğu) ve SameSite'ın tek başına yetersizliği; naive double-submit'in client-settable cookie / sibling-subdomain cookie-injection zaafı; login CSRF'in tanımı ve token'la korunması; JSON POST'ın simple-request olmayışı + Bearer (Authorization) header'ının tarayıcı tarafından otomatik gönderilmemesi → CSRF'e muafiyet gerekçesi. |
| Web Search **Bağlam** | ~22 kaynak (çoğu 2020-2026, birincil + ikincil): birincil — OWASP CSRF Prevention Cheat Sheet (cheatsheetseries — güncel, Fetch Metadata + Signed Double-Submit + Login Forms bölümleri), OWASP cheat-sheet GitHub aynası ("neither present → We recommend blocking"), W3C Fetch Metadata spec, Chromium SameSite Updates (Lax+POST), web.dev SameSite cookies explained, Google Search Security blog (SameSite=None; Secure), OWASP London slide (Johansson "Double Defeat"); ikincil — PortSwigger Web Security Academy (SameSite bypass), Security StackExchange (yüksek oylu: SameSite tek başına yetmez —66k; double-submit subdomain riski; REST custom header), Stack Overflow (JSON API custom header), Copenhagen Book (signed double-submit), Miguel Grinberg blog (2025-26 yorumlu Origin/Host zorluğu), Reddit r/node (form content-type / Bearer), Medium (Bearer vs cookie CSRF matrisi). |
| Web Search **Kısa Açıklama** | **(1) OWASP güncel:** stateful yazılım **synchronizer token** ister — token sunucuda oturuma özel üretilir, istekte yoksa/eşleşmezse **reddet + logla**; custom header (`X-CSRF-Token`) ile taşınması gizli form alanından **daha güvenlidir** (same-origin policy + CORS preflight); **naive double-submit DISCOURAGED** — sibling subdomain/DNS takeover/plaintext-HTTP cookie-injection ile atlatılır, **Signed (session-bound HMAC) double-submit** önerilir; Origin varsa host eşleşmeli, yoksa Referer, **"ikisi de yoksa blokla — önerimiz bloklamaktır"**; **XSS tüm CSRF savunmalarını yok eder** (ADR-012 kesişimi); GET ile state-change yasak. **(2) SameSite 2025-26:** attribute'suz cookie'ler Chrome 80+ (2020'den beri) **Lax-by-default**; **Lax+POST 2 dakika istisnası geçiciydi ve kaldırıldı** (artık attribute'suz cookie cross-site POST'a gitmez); `None` yalnız `Secure` ile kabul edilir; **Strict** hiçbir cross-site istekte cookie taşımaz (dış linkten ilk navigasyonda oturum "kaybolur"); **Lax üst-seviye GET navigasyonlarında cookie taşır → GET ile state-change varsa Lax durdurmaz** (OWASP cheat sheet'in SameSite Limitations bölümü); SameSite yalnız **cross-site**'i değil **cross-origin same-site** (subdomain) saldırılarını geçirir (jub0bs/66k oy). **(3) Double-submit:** cookie client tarafında yazılabilir → saldırgan subdomain'den eşleşen cookie basabilir (StackExchange 59470; Johansson "Double Defeat"; OWASP: DNS takeover, non-`__Host-` plaintext-HTTP injection); server-side session ile **signed** bile olsa synchronizer tercih edilir çünkü oturum zaten var. **(4) Login CSRF:** saldırgan kurbanı saldırganın hesabına logunutturabilir (veri/kart bilgisi sonra saldırgana düşer) — OWASP ayrı bölüm açar; **Lax login'i durdurmaz, login POST'u da token ister**. **(5) JSON/Bearer:** `application/json` CORS simple-request değil → preflight; custom header varlığı tek başına yeterli savunma sayılır (yüksek oylu SO/SE); **tarayıcı cookie'yi otomatik gönderir ama `Authorization` header'ını ASLA otomatik göndermez (aynı-origin bile)** → Bearer auth'lı uçta CSRF yüzeyi yok → muafiyet meşru; ama **hybrid (önce cookie!) auth kullanıyorsa token zorunlu** (AuthenticationMiddleware satır 37-45'in anlamı bu). |
| Web Search **Uzun Açıklama** | **(a) OWASP Cheat Sheet (güncel sürüm):** Synchronizer Token Pattern'i birincil öneridir; token sunucuda, oturuma özel, tahmin edilemez (CSPRNG) üretilir; istekte yoksa veya eşleşmezse istek reddedilir ve "olası CSRF saldırısı" olarak loglanır; token **cookie ile taşınmaz** (sync pattern'de) — HTML gizli alan veya **custom header** ile taşınır; custom header aynı-origin policy + preflight sayesinde saldırganca eklenemez; **Naive Double-Submit DISCOURAGED** uyarısı doğrudan sayfada: sibling subdomain, DNS takeover ve non-`__Host-` cookie injection bypass'ları + OWASP London (Johansson) kaynağına link; **Signed Double-Submit** önerilirse HMAC **session'a bağlanmalı** (session ID gizli kalmalı, email gibi statik değer kullanılmamalı), timestamp'i token'a expiration olarak koymak **mitostur**; **Origin/Referer:** Origin varsa hedef origin ile karşılaştır (HTTPS'te Origin her zaman gelir), Origin yoksa Referer'a bak, **ikisi de yoksa kabul edilebilir ama biz BLOKLAMAYI öneriyoruz** (fail-closed) — proxy/header-stripping için "önce log-only, güven kazanınca blokla" rollout'u; **Fetch Metadata (`Sec-Fetch-Site`)** modern tarayıcılarda (Mart 2023'ten beri tüm büyük tarayıcılar, %98+ destek) ek hafif katmandır ama **fallback olarak Origin/Referer kontrolü zorunludur**; **XSS tüm teknikleri yok eder** (token okunur) → XSS'in CST kleim: XSS önlemi ADR-012 CSP (nonce + strict-dynamic) ile verilmelidir; **GET ile state-change yasak**, yapılıyorsa korunmalı; **Login Forms** bölümü: login endpoint'i de CSRF'e açıktır. **(b) SameSite durumu:** web.dev ve Google blog: attribute'suz cookie artık Lax muamelesi görür ama **açıkça `SameSite=Lax` yazmak önerilir** (tarayıcılar arası tutarlılık), `None` + `Secure` zorunlu; Chromium SameSite Updates sayfası **Lax+POST 2 dk istisnasının geçici olduğunu ve kaldırıldığını** — yani eski "yeni set edilmiş cookie 2 dk POST'a gider" deliği artık kapalı, **explicit attribute şart**; PortSwigger akademisi Strict/Lax/None pratiklerini ve bypass'larını (window.open/top-level navigasyon, client-side redirect) anlatır; OWASP Limitations bölümü: **Lax yalnız güvenli metodlarda üst-seviye navigasyonda cookie taşır — GET state-change varsa yetersiz**; **SameSite cross-origin same-site saldırılarını geçirmez** (subdomain take-over senaryosu; 66k oy'lu SE cevabı: "Just the SameSite flag is not enough"); prerender/prefetch gibi specülatif navigasyonlar same-site context yaratabilir. **(c) Double-submit eleştirisi:** StackExchange 59470: non-host-only cookie subdomain'den okunabilir/yazılabilir; Johansson "Double Defeat": saldırgan kötü niyetli subdomain'den parent domain'e cookie basıp eşleşen payload üretebilir (MITM plaintext-HTTP ikinci vektör); OWASP uyarısı aynı + `__Host-` prefix ve **session-bound HMAC** telafisi; Copenhagen Book: imzalı double-submit'te HMAC session'a bağlanmalı. → CoreMusic'te server-side session **zaten var** (synchronizer mümkün) ve domain `.coremusic.net` tüm subdomainlere yayılıyor (§1.1) → **naive double-submit asla birincil olamaz; yalnız yedek + `__Host-`/Secure hedefi**. **(d) Login CSRF:** OWASP sayfası ayrı bölüm — login'i de koru; OWASP community tanımı: kurban saldırganın hesabına logunur, sonradan girdiği kişisel veriler/kartlar saldırgana akar; Lax login POST'una dokunmaz (cross-site form POST'ta cookie gider — Lax yalnız safe method üst-seviye navigasyonda taşır; form POST cross-site'te Lax cookie gitmez → login CSRF Lax'le büyük ölçüde kırılır **ama** aynı-site/subdomain ve login'in GET'e bağlı formlar için token şart). **(5) JSON/Bearer:** OWASP custom-header bölümü: modern JSON API'lar form kullanmaz → `X-CSRF-Token` varlık kontrolü yeterli savunmadır (preflight zorunlu kılar); simple content-type'lar (`text/plain` dahil) yasaklanmalı; StackOverflow 45202266 ve SE 23371 aynı sonuca varır (JSON API + zorunlu custom header = CSRF'e dayanıklı); Grinberg blogu Origin başlığının Host ile karşılaştırılmasının reverse-proxy altında zor olduğunu, `Sec-Fetch-Site`/`Origin`'in browser-client'ta forge edilemez olduğunu (forbidden header) anlatır; **Bearer tarafı:** Reddit r/node ve Medium matrisi aynı: **"CSRF token'ı cookie auth'da zorunlu; Bearer (Authorization header) auth'da gerekmez — çünkü tarayıcı Authorization'ı otomatik göndermez"** — cookie otomatik gider, header gitmez; form ile de header taşınamaz. → CoreMusic kuralı: **auth mekanizması = cookie ise token zorunlu (JSON dahil); auth mekanizması = Bearer ise muaf** (gerekçe kaynaklı §2.2b). |
| Web Search **Paragraf Veri Uzun** | OWASP güncel: stateful → synchronizer token (oturuma özel, CSPRNG, yoksa/eşleşmezse 403+log) · stateless → signed double-submit (session-bound HMAC) · naive double-submit DISCOURAGED (sibling subdomain / DNS takeover / non-`__Host-` injection) · Origin varsa host eşleşmesi, yoksa Referer, **ikisi yoksa blokla (fail-closed)** · log-only rollout · custom header `X-CSRF-Token` = preflight kilitli, form'la taşınamaz · Fetch Metadata `Sec-Fetch-Site` ek katman (%98+ Mart 2023+) fallback Origin zorunlu · **XSS tümünü yok eder → ADR-012 CSP** · GET state-change yasak · login de korunur (login CSRF: saldırgan hesabına logunma) · SameSite: Lax-by-default (Chrome 80+), Lax+POST 2 dk kaldırıldı, None+Secure, Strict dış navigasyonda oturumu gizler, **Lax GET state-change'i durdurmaz**, **cross-origin same-site (subdomain) geçer — tek başına yetersiz** · double-submit client-settable → parent-domain cookie basma (Johansson, `.coremusic.net` geniş domain!) · JSON: simple request değil → preflight; zorunlu custom header tek başına yeterli (SO 45202266, SE 23371) · **cookie auto-send VAR, Authorization header auto-send YOK (aynı-origin bile) → Bearer uç CSRF'e muaf, cookie auth'lı uç token zorunlu (hybrid auth'da önce cookie gelir — AuthenticationMiddleware satır 37)** · token rotasyonu: oturum başlangıcı + privilege change; per-request/s timestamp rotasyonu efsane + UX zararı · `hash_equals` timing-safe. |
| Web Search **Sonucu** | 1) **Synchronizer token birincil** doğrulandı (stateful CoreMusic oturumu var): OWASP güncel + StackExchange onayı; timing-safe `hash_equals` şart (kodda var §1.1) → **katman 1 = IMPLEMENTED, güçlendirilecek** (kaynak 1, 2, 17). 2) **Origin/Referer fail-closed** doğrulandı: OWASP "ikisi yoksa bloklamayı öneririz" → mevcut OriginCheck **fail-open** (satır 39-41) bu ADR ile **PLANNED** kapatılır (kaynak 1, 2, 20). 3) **SameSite tek başına YETERSİZ** 4 kaynakla sabit (Lax-GET, cross-origin same-site/subdomain, header-independent UX) → **yalnız katman, asla tek savunma** (kaynak 3, 4, 5, 6, 8) — karar 3 katmanı zorunlu kılar. 4) **Double-submit yedeği** — naive zaafı 4 kaynakla ispatlı → yalnız Origin/Referer okunamayan senaryoda, **signed + `__Host-`/Secure** ile (kaynak 9, 10, 11, 12). 5) **Login CSRF gerçek** → login/register POST'ları da token zorunlu (CsrfMiddleware'in metot-temelli kapsamı bunu zaten kapsar — gerekçe yazıldı) (kaynak 13, 14). 6) **JSON POST + Bearer muafiyet gerekçesi** netleşti: JSON preflight+custom header = korumalı; **Authorization header asla otomatik gönderilmez** → Bearer uçlar muaf, **cookie auth'lı (hybrid: önce cookie!) API uçları token zorunlu** (kaynak 15, 16, 17, 18, 19). 7) **Token rotasyonu:** oturum başlangıcı + privilege change (per-request rotasyon efsane — OWASP/SE: UX'i bozar, güvenlik katmaz) (kaynak 1, 17). 8) **XSS↔token köprüsü** → ADR-012 CSP ile kesişim bağlayıcı (kaynak 1). |
| Web Search **Alınan Karar** | **ADR-010 kabul edilir — ÜÇ KATMANLI CSRF + API kuralı + SPA akışı:** **Katman 1 — Synchronizer token** (`csrf_token`, oturuma bağlı, `hash_equals` timing-safe; header `X-CSRF-Token` veya body `csrf_token`; eksik/yanlış → 403) — birincil ve zorunlu. **Katman 2 — SameSite:** oturum cookie'si `Lax` (IMPLEMENTED) korunur; **Strict** oturum cookie'sinde UX (dış linkten dönüş) gerekçesiyle varsayılan değil, **yedek csrf_token cookie'sinde hedef** (tarayıcı uyumu: tüm büyüklar destekler; None yalnız Secure). **Katman 3 — Origin/Referer fail-closed:** POST/PUT/PATCH/DELETE'te Origin host ile eşleşmeli → yoksa Referer host eşleşmeli → **ikisi de yoksa 403 RED** (mevcut OriginCheck fail-open'ı PLANNED kapatılır). **Katman 4 (yedek) — double-submit cookie:** yalnız Origin/Referer'ın okunamadığı senaryolar için; **signed/session-bound + `__Host-` + Secure + SameSite=Strict** hedefi; naive asla. **API kuralı:** cookie kullanan TÜM mutating endpoint'ler (JSON dahil) token ister; **Bearer (`Authorization: Bearer`) kullanan endpoint CSRF'e MUAF** — gerekçe: tarayıcı Authorization header'ını otomatik göndermez (aynı-origin bile), form ile taşınamaz → otomatik kimlik eklenemez; muafiyet yalnız auth'ın **yalnızca** Bearer olduğu uçlarda geçerlidir (hybrid auth = cookie önce gelir → token zorunlu; §1.1 AuthenticationMiddleware satır 37-45). **SPA akışı:** token **meta tag (`<meta name="csrf-token">`, PLANNED) +/veya mevcut gizli alan (IMPLEMENTED)** → JS okur → **`X-CSRF-Token` header'ı** ile gönderir (ADR-004 SPA uyumu); header gönderimi **Origin/Referer kilidiyle** birlikte anlamlıdır; **rotasyon: oturum başlangıcı + privilege change'de yenile** (session ID rotation'ı token'ı taşımaz — PLANNED bağ). **Token cookie'ye sync-pattern'de koyulmaz** (OWASP) — cookie yolu yalnız katman 4 yedeğinde. |
| Web Search **Sonuç** | Karar 2025-26 verisiyle **desteklendi ve sıkılaştırıldı**: OWASP güncel cheat sheet (synchronizer + signed double-submit + fail-closed Origin/Referer + Fetch Metadata fallback + login forms + XSS uyarısı), SameSite tarayıcı durumu (Lax-by-default, Lax+POST çöküşü, Strict/Lax/None matrisi, "tek başına yetersiz" — 5 kaynak), double-submit eleştirisi (client-settable cookie / subdomain — 4 kaynak), login CSRF (2 kaynak), JSON+Bearer muafiyeti (5 kaynak) — **toplam ~22 birincil+ikincil kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak/tüm güvenlik iddialarında karşılanır (OWASP, W3C, Chromium, Google, PortSwigger birinciller). Kod bulguları §1.1'de IMPLEMENTED/PLANNED olarak ayrıldı: Origin/Referer fail-closed, meta tag, privilege-change rotasyonu, API CsrfMiddleware kaydı, JWT doğrulaması, `SameSite=Strict` kodda yok → PLANNED yazıldı, uydurulmadı. `⚠️ VERIFICATION REQUIRED` yalnız Bearer/JWT stub'u (AuthenticationMiddleware satır 92-106) ve PLANNED kalemler için korunur (§5.1 adımlarında kapatılır). |

**Kaynak listesi (~22 birincil + ikincil):**
1. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html — güncel OWASP CSRF Prevention Cheat Sheet: synchronizer pattern, signed vs naive double-submit (DISCOURAGED), Origin/Referer (fail-closed önerisi), custom header, Fetch Metadata, SameSite Limitations, Login Forms, "XSS defeats all" (birincil, OWASP)
2. https://github.com/nokia/OWASP-CheatSheetSeries/blob/master/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.md — "If neither of these headers are present, you can either accept or block the request. **We recommend blocking.**" (OWASP cheat-sheet kaynak aynası — fail-closed cümlesinin birebir kanıtı)
3. https://web.dev/articles/samesite-cookies-explained — Lax-by-default davranışı, açık `SameSite=Lax` yazımı önerisi, `None` + `Secure` zorunluluğu, Strict/Lax tanımı (birincil, Google/web.dev)
4. https://www.chromium.org/updates/same-site — **Lax+POST 2 dakika istisnasının geçici olduğu ve kaldırılması**; explicit SameSite'ın şartlığı (birincil, Chromium)
5. https://developers.google.com/search/blog/2020/01/get-ready-for-new-samesitenone-secure — Chrome 80+ attribute'suz cookie = Lax; "не все tarayıcılar default'ta korur → açıkça yazın" (birincil, Google)
6. https://portswigger.net/web-security/csrf/bypassing-samesite-restrictions — Strict/Lax/None davranışları + bypass vektörleri (window.open, redirect), Lax-by-default (ikincil, PortSwigger Web Security Academy)
7. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html#samesite-cookie-attribute — "Lax only blocks unsafe methods... If any state-changing operation is reachable via GET, SameSite=Lax will not stop it" + subdomain cookie paylaşım uyarısı (birincil, OWASP)
8. https://security.stackexchange.com/questions/234386/do-i-still-need-csrf-protection-when-samesite-is-set-to-lax — "SameSite yalnız cross-site'e uygulanır; cross-origin same-site (subdomain) saldırısına karşı güçsüz — tek başına yetmez" (66k puanlı uzman, jub0bs referanslı)
9. https://security.stackexchange.com/questions/59470/double-submit-cookies-vulnerabilities — subdomain'den cookie okuma/yazma riski (double-submit)
10. https://security.stackexchange.com/questions/65854195/csrf-double-submit-cookie-is-basically-not-secure — naive double-submit'in cookie-injection ile kırılabilirliği
11. https://owasp.org/www-chapter-london/assets/slides/David_Johansson-Double_Defeat_of_Double-Submit_Cookie.pdf — "Double Defeat of the Double-Submit Cookie": subdomain cookie fixation + MITM vektörleri (birincil, OWASP London)
12. https://thecopenhagenbook.com/csrf — signed double-submit: HMAC session'a bağlanmalı; imzasız double-submit subdomain erişiminde açık (ikincil)
13. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html#possible-csrf-vulnerabilities-in-login-forms — login formu CSRF zaafı bölümü (birincil, OWASP)
14. https://community.owasp.org/attacks/csrf — login CSRF tanımı: kurban saldırganın hesabına logunur, kişisel veriler saldırgana akar (birincil, OWASP community)
15. https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html#employing-custom-request-headers-for-ajaxapi — JSON/AJAX API'da custom header (preflight) savunması; simple content-type yasağı (birincil, OWASP)
16. https://stackoverflow.com/questions/45202266/is-it-safe-to-use-a-custom-required-http-header-as-a-protection-method-from-the — JSON SPA API'sında zorunlu custom header'ın güvenliği (yüksek oylu)
17. https://security.stackexchange.com/questions/23371/csrf-protection-with-custom-headers-and-without-validating-token — REST API'da custom header varlığının yeterliliği; timing-safe karşılaştırma bağlamı (yüksek oylu)
18. https://www.reddit.com/r/node/comments/1im7yj0/jwt_csrf_a_good_security_practice — form ile header taşınamaz; `application/json` preflight; cookie auth vs Bearer ayrımı (kanıt tartışmalı — 2. kaynakla çapraz)
19. https://rifatcse09.medium.com/csrf-cookies-and-bearer-tokens-what-to-use-when-and-why-most-apps-get-it-wrong-76ddd791946b — "Browsers auto-send cookies. Browsers do NOT auto-send Authorization headers." → Bearer muafiyet matrisi (ikincil — 18 ile çapraz, OWASP 1 ile hizalı)
20. https://blog.miguelgrinberg.com/post/csrf-protection-without-tokens-or-hidden-form-fields — Origin↔Host karşılaştırmasının reverse-proxy'de zorluğu; forbidden header'lar JS'de forge edilemez; `Sec-Fetch-Site` (2025-26 yorumlu)
21. https://www.w3.org/TR/fetch-metadata/ — `Sec-Fetch-Site` spesifikasyonu: `same-origin/same-site/cross-site/none` (birincil, W3C — OWASP'ın ek katmanı)
22. https://caniuse.com/mdn-http_headers_sec-fetch-site — Fetch Metadata başlıklarının %98+ tarayıcı desteği (Mart 2023+ tüm büyükler) — OWASP cheat sheet içi veriyle çapraz

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Her iddia ≥2 kaynak | ADR-005 §2.2c standardı: güvenlik/canlı her dış iddia ≥2 bağımsız kaynak taşır; tek kaynak → `⚠️ VERIFICATION REQUIRED` (§1.3 çapraz doğrulama 5 sorgu/22 kaynakta tam). |
| Frozen ADR dokunulmaz | ADR-001…037 metinleri okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2); bu ADR frozen değildir (status: accepted, frozen YOK). |
| log.md append-only | Bu ADR kaydı append ile yazılır; geçmiş satıra dokunulmaz (AGENTS.md §25.3 kural 3). |
| Dosya adı değişmez | In-Place Refactoring: `ADR-010-csrf-protection-strategy.md` adı onaysız değiştirilemez (In-Place Refactoring kuralı 1). |
| ADR-011/ADR-012 düz metin | Her iki dosya da diskte YOK → wiki-link kurulmaz; düz metin: `ADR-011 (henüz yazılmadı — vault: .ai/.decisions/index.md §3 [[ADR-011-session-management]])`, `ADR-012 (henüz yazılmadı — ... [[ADR-012-csp-nonce-strict-dynamic]])`. ADR-008 dosyası diskte VAR ama kullanıcı onayıyla düz metin tutulur. |
| Middleware pipeline sırası test korumalı | `MiddlewarePipelineTest` satır 13: sıra değişirse CSP/CSRF bozulur (#6_Csrf) — katman 3'ü CsrfMiddleware'e GÖMMEK veya sıra değiştirmek yalnız test + ADR güncellemesiyle yapılır (`shared/AGENTS.md` Zorunlu #2). |
| Kod implementasyonu kapsam dışı | Bu ADR karar kaydıdır; OriginCheck fail-closed değişikliği, API CsrfMiddleware kaydı, meta tag, Strict ve rotasyon kodu Backend/Security/QA'ya aittir (§5.1 sorumluları). |
| domain.php yasağı + REDACTED | `shared/config/domain.php` domain listesi onaysız değişmez (shared Yasak #4); oturum/JWT/HMAC secret'ları bu ADR'ye yazılmaz (REDACTED politikası). |
| Bearer muafiyeti JWT'ye bağlı ⚠️ | `AuthenticationMiddleware::validateJwtToken` stub (satır 92-106) hep `null` döner — Bearer doğrulaması PLANNED; muafiyet yalnız gerçek JWT imza doğrulaması devreye girdiğinde geçerli (§5.1 adım 8). |

---

## 2. Karar (Decision)

**CoreMusic, tüm state-changing istekleri ÜÇ KATMANLI savunmayla korur: (1) Synchronizer token — oturuma bağlı `csrf_token`, `hash_equals` ile timing-safe karşılaştırma, eksik/yanlış → 403; (2) SameSite cookie — oturum cookie'si `Lax` (varsayılan), mümkünse `Strict` (tarayıcı/UX uyumu §2.2d); (3) Origin/Referer doğrulama — POST/PUT/PATCH/DELETE'te Origin başlığı host ile eşleşmeli, yoksa Referer, İKİSİ DE YOKSA REDDET (fail-closed). Ek yedek (4): double-submit cookie — yalnız Origin/Referer'ın okunamadığı senaryolar için, signed/session-bound. API kuralı: cookie kullanan TÜM mutating endpoint'ler (JSON dahil) token ister; Bearer (Authorization header) kullanan endpoint CSRF'e muaf (gerekçe §2.2b). SPA akışı: token meta tag +/veya gizli alandan JS okunur → `X-CSRF-Token` header'ı ile gönderilir (ADR-004); rotasyon oturum başlangıcı + privilege change'de yapılır.**

### 2.1 Neden Bu Seçenek?

1. **Synchronizer token birincil çünkü oturum zaten server-side:** OWASP stateful yazılım için synchronizer'ı önerir; sunucu oturumunda token tutmak stateless zorunluluğunu ortadan kaldırır (§1.3 kaynak 1, 2). `hash_equals` timing-safe (kodda IMPLEMENTED — §1.1) kanal-zamanı saldırısını kapatır (kaynak 17).
2. **Aynı savunmaya 3 kez güvenmek yerine 3 farklı sinyale bakmak:** SameSite **cross-site**'i, Origin/Referer **kaynağı**, token **niyeti** doğrular — biri düşerse diğer iki ayakta kalır (derin savunma; OWASP "en az bir Defense in Depth katmanı" kuralı, kaynak 1).
3. **SameSite tek başına yetersiz kanıtlandı:** Lax GET ile state-change'i durdurmaz (kaynak 7); cross-origin same-site/subdomain saldırılarını geçirir (kaynak 8); Strict dış navigasyonda UX'i kırar (kaynak 3, 6) → Lax + token + Origin üçlüsü gerekçelidir.
4. **Fail-closed Origin/Referer OWASP birebir önerisi:** "ikisi de yoksa bloklamayı öneririz" (kaynak 1, 2) — mevcut fail-open (§1.1 satır 39-41) bu kararla kapatılır; `Referrer-Policy: strict-origin-when-cross-origin` aynı-origin'de Referer'ı zaten taşır (SecurityHeaders satır 30) → yanlış pozitif riski düşük.
5. **Double-submit yalnız yedek:** naive sürümü client-settable cookie zaafı taşıyor (sibling subdomain / DNS takeover — kaynak 9, 10, 11, 12) ve CoreMusic cookie'si `domain=.coremusic.net` (§1.1) → birincil yapılamaz; Origin/Referer okunamayan niş senaryo için signed + `__Host-` ile sınırlı tutulur (kaynak 1, 11).
6. **API kuralı mevcut hibrit auth'u birebir karşılıyor:** `AuthenticationMiddleware` önce cookie'ye bakar (satır 37-45) → cookie auth'lı API mutating uç **token ister** (JSON içerik biçimi preflight'e girer, koruma token/header ile tamamlanır — kaynak 15, 16); **Bearer uçta CSRF yüzeyi yoktur**: tarayıcı `Authorization` header'ını **asla otomatik göndermez — aynı-origin bile** (kaynak 18, 19) ve form ile header taşınamaz (kaynak 16, 17) → muafiyet kaynaklı gerekçelendirilir.
7. **Login CSRF dahil kapsama:** OWASP login formunu ayrı zaaf sayar (kaynak 13, 14); CsrfMiddleware'in metot-temelli kapsamı login/register/set-gender/logout POST'larını zaten içerir (kod testleri §1.1) → karar bunu **açıkça** bağlayıcı kılar.
8. **XSS köprüsü bilinçli devralınıyor:** XSS token'ı okuyup sahte istek atabilir (OWASP: XSS tümünü yok eder — kaynak 1) → bu ADR'nin sonu ADR-012 CSP'dir (nonce + `strict-dynamic` — SecurityHeaders satır 53-55 IMPLEMENTED); token süresiz değil, rotasyonlu ve oturum bağlı tutulur.

### 2.2 Teknik Detaylar

**(a) Katman tablosu (bağlayıcı):**

| Katman | Mekanizma | Kapsam | Başarısızlıkta | Etiket |
|--------|-----------|--------|----------------|--------|
| **1 — Synchronizer token** | `$_SESSION['csrf_token']` (64 hex, `random_bytes(32)`) vs `X-CSRF-Token` header **veya** body `csrf_token`; `hash_equals` | GET/HEAD/OPTIONS dışı **her istek** (PageRouter + API, cookie auth'lı) | Eksik/yanlış/oturumsuz token → **403 `csrf_invalid`**, `halt` (fail-closed) | **IMPLEMENTED** (`CsrfMiddleware.php` satır 24-58) + **PLANNED:** API pipeline kaydı (§5.1 #4) |
| **2 — SameSite cookie** | `session_set_cookie_params(samesite: 'Lax')` + HttpOnly + Secure(https) | Oturum cookie'si `.coremusic.net` (tüm subdomainler) | Tarayıcı cross-site POST'ta cookie'yi **zaten taşımaz**; taşsa bile katman 1/3 durdurur | **IMPLEMENTED Lax** (`SessionInitializer.php` satır 40-47) + **PLANNED Strict** (yalnız yedek csrf cookie'si — §2.2d) |
| **3 — Origin/Referer** | Origin host == hedef host → geç; Origin yoksa Referer host == hedef host → geç; **ikisi de yoksa 403** | POST/PUT/PATCH/DELETE | **Fail-closed: 403 `origin_not_allowed`** (OriginCheck mevcut fail-open'ı kapatılır) | **PLANNED** (`OriginCheckMiddleware.php` satır 39-41 fail-open → fail-closed; Referer fallback eklenir) — **debate şartı 1 ile kapatılacak (§5.3 · §7.1)** |
| **4 — Double-submit (yedek)** | Sunucu-set, `Secure`+`HttpOnly'siz`+`SameSite=Strict`+`__Host-` hedefli **signed** csrf cookie'si == header/body değeri (session-bound HMAC) | **Yalnız** Origin/Referer başlıklarının okunamadığı senaryo (privacy-proxy strippi, non-browser client) — katman 3'ün fail-closed'undan muaf akış listesi | Eşleşmezse 403; naive (imzasız) sürüm **yasak** | **PLANNED (yedek)** — OWASP signed kuralı (§1.3 kaynak 1, 11) |

*Kural (bağlayıcı):* katman 1 **asla kapatılmaz**; katman 4 yalnız katman 3'ün **açıkça muaf ilan ettiği** uçlarda devreye girer; naive double-submit birincil yapılamaz (§1.3 kaynak 9-12).

**(b) API kuralı + Bearer muafiyeti (gerekçeli):**

| Endpoint sınıfı | Auth mekanizması | CSRF zorunlu mu? | Gerekçe (kaynak) |
|-----------------|------------------|------------------|------------------|
| Cookie kullanan mutating uç (POST/PUT/PATCH/DELETE) — **JSON dahil** | Session cookie (hibrit uçlarda cookie **önce** gelir — `AuthenticationMiddleware` satır 37-45) | **EVET — `csrf_token` zorunlu** | Tarayıcı cookie'yi otomatik ekler → CSRF yüzeyi açık; JSON preflight'i tek başına yetmez, token header'ı tamamlar (kaynak 15, 16, 18) |
| Yalnız Bearer kullanan mutating uç | `Authorization: Bearer <JWT>` | **HAYIR — MUAF** | **Tarayıcı `Authorization` header'ını otomatik göndermez (aynı-origin bile); form ile header taşınamaz → saldırgan mağdurun kimliğini otomatik ekleyemez** (kaynak 16, 17, 18, 19); CSRF cookie-varlığına dayanır, cookie yoksa yüzey yok |
| Public/okuma uçları (GET) | — | Kapsam dışı | GET state-change yasak (kaynak 1) |
| OAuth callback (state) | Harici sağlayıcı + `oauth_states` | **EVET — state token** (IMPLEMENTED) | Flow-CSRF, oturum-içi token'dan bağımsız (§1.1) |

*Muafiyetin sınırı (bağlayıcı):* (i) muafiyet yalnız **auth mekanizması tekil Bearer** olan uçlarda geçerlidir; **hibrit/sessiz-cookie fallback'i varsa token zorunludur** (kaynak 19'ın matrisi: "CSRF token required **whenever** authentication relies on cookies"). (ii) Bearer muafiyeti, `validateJwtToken` **gerçek imza doğrulaması** yapıldığında geçerli olur (bugünkü stub `null` → Bearer uçlar fiilen 401 — §1.1 ⚠️). (iii) Muaf uçlar **açık allowlist**'te yazılıdır; liste dışında her uç token zorunlu (fail-closed varsayılan).

**(c) SPA token akışı + rotasyon (ADR-004 uyumu):**

```
Sunucu: SessionLifecycle → $_SESSION['csrf_token'] = bin2hex(random_bytes(32))   [oturum başlangıcı — IMPLEMENTED]
HTML:   <meta name="csrf-token" content="...">  [PLANNED]  +  <input name="csrf_token" id="csrf-global">  [IMPLEMENTED — HtmlShellRenderer satır 129]
JS:     token = document.querySelector('meta[name=csrf-token]')?.content
              ?? document.querySelector('[name=csrf_token]')?.value               [gizli yol IMPLEMENTED — AuthHandler satır 33/47/61]
POST:   fetch(url, { method:'POST', credentials:'include',
                     headers:{ 'X-CSRF-Token': token, 'Content-Type':'application/json' } })   [IMPLEMENTED — headers.js satır 3]
Kilit:  header gönderimi + Katman 3 Origin/Referer doğrulaması birlikte çalışır   [PLANNED — Katman 3]
Sunucu: CsrfMiddleware: hash_equals($_SESSION['csrf_token'], header/body)         [IMPLEMENTED]
```

| Kural | Değer | Etiket |
|-------|-------|--------|
| Okuma yolu | **meta tag +/veya gizli alan** (sync-pattern: token cookie'ye KONMAZ — OWASP, kaynak 1); yedek katman 4 cookie'si JS'den okunabilir | meta **PLANNED**, gizli alan **IMPLEMENTED** |
| Gönderim | **`X-CSRF-Token` header** (CORS allowlist'te — `CorsMiddleware` satır 32) veya form body `csrf_token` | **IMPLEMENTED** |
| **Rotasyon 1** | **Oturum başlangıcı** (login/destroy sonrası yeni token) | **IMPLEMENTED** (SessionLifecycle satır 47-49, destroy → yeni oturum) |
| **Rotasyon 2** | **Privilege change** (rol/izin, email, şifre değişikliği; yetki yükseltme) → token yenile + (tercihen) session devri | **PLANNED** (§5.1 #5) |
| Per-request / timestamp rotasyonu | **YAPILMAZ** — back-button UX'i bozar, güvenlik katmaz (OWASP: timestamp efsanesi — kaynak 1) | kararlı |
| Eski sekme | Rotation sonrası eski token → 403; istemci yeni token'ı meta/gizliden yeniden okur (silent retry 1 kez) | PLANNED (client) |

**(d) SameSite karar tablosu (tarayıcı uyumu notlu):**

| Değer | Karar | Gerekçe / Kaynak |
|-------|-------|------------------|
| Oturum cookie `SameSite=Lax` | **VARSAYILAN — korunur (IMPLEMENTED)** | Dış linkten dönüşte oturumun taşınması gerekir (UX); Chrome 80+ zaten Lax-by-default ama açıkça yazmak şart (kaynak 3, 5) |
| Oturum cookie `SameSite=Strict` | **Varsayılan DEĞİL** | Strict, dış siteden tıklamada **ilk istekte cookie'yi göndermez** → kullanıcı "çıkmış" görünür (navigasyon UX kırılır; kaynak 3, 6). Karar: Strict yalnız **oturumun taşındığı hassas mutating uçlar ayrılsaydı** düşünülürdü — tek session cookie'de uygulanamaz |
| Yedek csrf cookie (katman 4) `SameSite=Strict; Secure; __Host-` | **HEDEF (PLANNED)** | Cookie yalnız header okumada kullanılır, navigasyon taşımaz → Strict'in UX zararı yok; `__Host-` subdomain injection'ı kapatır (kaynak 1, 11) |
| `SameSite=None` | **YASAK oturum için** | Cross-site'e tam açılım = CSRF'e davet; `Secure` zorunlu (kaynak 3, 5) |
| Tarayıcı uyumu | **Lax/Strict/None tüm büyük tarayıcılarda destekli** (Chrome, Edge, Firefox, Safari 16.4+); attribute'suz cookie'lerde davranışı tarayıcıya BIRAKMAZ — **her zaman explicit yaz** (kaynak 3, 5, 6, 22) |

**(e) Origin/Referer fail-closed algoritması (Katman 3 — bağlayıcı):**

| # | Durum | Aksiyon |
|---|-------|---------|
| 1 | Yöntem GET/HEAD/OPTIONS | Katman 3 uygulanmaz (safe method — token da yeterlidir; GET state-change yasak, kaynak 1) |
| 2 | `Origin` başlığı **var** | Host == hedef host (allowlist suffix dahil, `strtolower`) → geç; **uymuyorsa 403** (bugünkü davranış IMPLEMENTED) |
| 3 | `Origin` **yok**, `Referer` var | Referer host == hedef host → geç; **uymuyorsa 403** |
| 4 | **İkisi de yok** | **403 — fail-closed** (OWASP: "We recommend blocking", kaynak 2); yalnız §2.2b allowlist'li (katman 4 ile korunan) uçlar bu reddi yedek cookie ile açar |
| 5 | Proxy header strippi şüphesi | **Log-only rollout** → OWASP önerisi: önce logla, yanlış pozitif gör, sonra blokla (kaynak 1); acil kill-switch §4.4 #1 |

**(f) Test kapısı (yayın öncesi zorunlu):**

| # | Test | Beklenen |
|---|------|----------|
| 1 | Token'lı/ token'sız / yanlış tokenlı POST | 200 / **403 `csrf_invalid`** / 403 (kod testi genişletilir — §5.1 #6) |
| 2 | Geçersiz Origin'li POST (cross-site) | **403 `origin_not_allowed`** |
| 3 | Origin yok + Referer yok POST | **403** (fail-closed — yeni davranış) |
| 4 | Origin yok + eşleşen Referer | 200 |
| 5 | Bearer-auth'lı API mutating uç (token'sız) | 200 (muafiyet) |
| 6 | Cookie-auth'lı API mutating uç (JSON, token'sız) | **403** |
| 7 | Login/Logout/set-gender/register POST | Token zorunlu (login CSRF kapsamı) |
| 8 | Privilege change sonrası eski token | 403 → yeni token'la 200 (rotasyon) |
| 9 | `SameSite` attribute'ü assert (Set-Cookie) | `Lax` (yedek cookie'de `Strict; Secure; __Host-`) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız SameSite cookie (token/Origin yok)** | Sıfır kod (Lax zaten IMPLEMENTED), UX'e dokunmaz | Lax üst-seviye GET'te cookie taşır → GET state-change açığı; **cross-origin same-site (subdomain) saldırılarını geçirir**; Strict dış navigasyonu kırar; attribute davranışı tarayıcıya bırakılmışsa tutarsız (§1.3 kaynak 3, 5, 6, 7, 8) | "SameSite tek başına yetersiz" 5 kaynakla sabit — OWASP bunu yalnız **defense in depth** sayar (kaynak 1) |
| 2 | **Yalnız double-submit cookie (stateless)** | Sunucu state'i gerekmez, ölçeklenir | **Naive sürüm client-settable** — sibling subdomain/DNS takeover/plaintext-HTTP cookie-injection ile bypass (kaynak 9, 10, 11); `.coremusic.net` geniş domain bunu besler; signed'ı kursa bile oturum zaten var → gereksiz karmaşa | OWASP: stateful yazılım synchronizer kullanır; naive **DISCOURAGED**, yalnız yedek (kaynak 1) — §2 katman 4 |
| 3 | **Yalnız Origin/Referer kontrolü (token yok)** | Hızlı, state gerektirmez | Header strippi/privacy-proxy riski (OWASP "önce log-only" diyor); Origin yoksa **hata fail-open ise zaaf** (bugünkü kod — §1.1); non-browser/non-Origin client'lar; OAuth/redirect akışlarında hedef origin hesabı zahmetli (kaynak 1, 2, 20) | OWASP bunu **katman** sayar, tek savunma değil; token olmadan form POST'u korumasız kalır |
| 4 | **Yalnız custom header (token değeri rastgele/sabit)** | JSON API'da preflight kilitli, state yok | **HTML form POST header ekleyemez** — login/register/gender gibi form akışları korumasız kalır; `text/plain` gibi simple content-type'lar unutulursa kırılır; sabit header değeri "gizlilik" taşımaz (kaynak 15, 16, 17) | CoreMusic'te form akışları var (§1.1) → header **kanal**, token **içerik** şart; OWASP custom header'ı token ile birlikte önerir |
| 5 | **Encrypted token (HMAC/timestamp'li) + per-request rotasyon** | Stateless'ta ekstra doğrulama, timestamp ile süre | Timestamp "expiration" OWASP'a göre **mitostur**; per-request token back-button'ı bozar (false-positive 403); anahtar yönetimi ek yük (kaynak 1) | Oturum zaten server-side (§1.1) → synchronizer daha basit ve güçlü; rotasyon yalnız başlangıç + privilege change (§2.2c) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Üç bağımsız sinyal:** token (niyet) + SameSite (site sınırı) + Origin/Referer (kaynak) — biri düşerse diğer ikisi ayakta; OWASP defense-in-depth şartı sağlanır (§1.3 kaynak 1).
- **Mevut kod çoğunlukla zaten uyumlu:** `CsrfMiddleware` (hash_equals, 403), `SessionInitializer` (Lax), `OriginCheckMiddleware` (allowlist 403), JS `X-CSRF-Token` gönderimi ve testler IMPLEMENTED (§1.1) → karar "yazma" değil "eksikleri kapatma" ağırlığında.
- **API yüzeyi netleşti:** cookie+JSON uçları token zorunlu, Bearer uçları gerekçeli muaf → hibrit auth'ın (`AuthenticationMiddleware` önce cookie) CSRF boşluğu kapanır.
- **Login CSRF dahil kapsandı:** login/register/logout/set-gender POST'ları açıkça token kapsamında (kaynak 13, 14).
- **Sınır/Aitor:</br> Sınır/AIT ... XSS→token riski bilinçli devralındı:** ADR-012 CSP (nonce + `strict-dynamic` IMPLEMENTED) ile kesişim yazılı; rotasyon (başlangıç + privilege change) token ömrünü daraltır.
- **Fail-closed kültürü:** Origin/Referer ikisi de yoksa red; allowlist dışı her şey varsayılan red → yeni uç eklemek "koruma unutma" riskini azaltır.

### 4.2 Olumsuz Sonuçlar

- **PLANNED yükü:** Katman 3 fail-closed, Referer fallback, API CsrfMiddleware kaydı, meta tag, privilege-change rotasyonu, `SameSite=Strict` yedek cookie, JWT doğrulaması kodda YOK (§1.1) → kararın gövdesi henüz tamamlanmamış (§5.1 adımları) ⚠️ VERIFICATION REQUIRED (yalnız Bearer/JWT stub).
- **Fail-closed = yanlış pozitif riski:** Origin/Referer'ı proxy/privacy-filter ile strip edilen legit istekler 403 yer → log-only rollout + allowlist gerekir (OWASP rollout — kaynak 1); kill-switch şart (§4.4 #1).
- **Çok katmanlı test yükü:** 9 testlik kapı (§2.2f) + pipeline sırası testi korunmalı; katman eklendikçe entegrasyon matrisi büyür (QA).
- **Strict UX dikkati:** oturum cookie'sine Strict basmak dış-link dönüşünü kırar → kararı "oturumda Lax, yedekte Strict" olarak daraltmak zorunda kaldık (§2.2d) — tek başına Strict hayali terk edildi.
- **Subdomain domain genişliği:** `domain=.coremusic.net` tüm subdomainlere cookie yaydığı için (ADR-004) double-submit/subdomain zaafı teorik olarak açık kalır → `__Host-` hedefi ve subdomain hijyeni bağımlılığı (§4.3 risk 3).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **XSS → token sızıntısı** (XSS tüm CSRF savunmalarını yok eder — OWASP; saldırgan `X-CSRF-Token`'ı okur) | 3 (olası) | 4 (yüksek) — hesap ele geçirme | **ADR-012 CSP** (nonce + `strict-dynamic` + `form-action 'self'` — SecurityHeaders IMPLEMENTED), token'ın sync-pattern'de cookie'ye konmaması, HttpOnly session, rotasyon (§2.2c); OWASP kaynak 1 — XSS önlenmeden CSRF tamamlanmaz |
| **SameSite'a tek başına güvenilmesi** (gelecekte bir refactor'da token/Origin "gereksiz" görülmesi) | 3 (olası) | 4 (yüksek) | Bu ADR **katman 1 zorunlu** der; §1.3 kaynak 3, 5, 6, 7, 8 (Lax-GET, cross-origin same-site, Strict UX) + §3 alternatif 1 red gerekçesi vault'ta yazılı |
| **Subdomain cookie-injection / sibling-subdomain zaafı** (`.coremusic.net` geniş domain; kötü niyetli/DNS-takeover subdomain basar — ADR-004 domain'leri) | 2 (mümkün) | 4 (yüksek) — double-submit bypass | Naive double-submit **yasak**; yedek cookie `__Host-` + `Secure` + signed session-bound HMAC (kaynak 1, 9, 11); `domain.php` hijyeni (shared Yasak #4); subdomain takeover taraması (ADR-004) |
| **Fail-closed yanlış pozitif** (Origin/Referer strip edilen legit istekler — privacy proxy, header normalizasyonu) | 3 (olası) | 3 (orta) — işlev kesintisi | OWASP log-only → enforce rollout'u (kaynak 1, 20); Referer-Policy same-origin'de tam taşır (SecurityHeaders satır 30); §4.4 #1 kill-switch (config ile log-only'ye dönüş); staging'de §2.2f testleri |
| **Bearer muafiyetinin yanlış uygulanması** (cookie auth'lı uç yanlışlıkla "Bearer muaf" sanılır — hybrid auth'da cookie önce gelir) | 2 (mümkün) | 4 (yüksek) | §2.2b: muafiyet **yalnızca tekil Bearer auth** ucu; allowlist yazılı, varsayılan token zorunlu; `validateJwtToken` stub kapanmadan muafiyet yürürlüğe girmez (§5.1 #8) ⚠️ |
| **Rotasyon sonrası eski sekme 403** (privilege change'de token değişir → açık formlar 403 alır) | 3 (olası) | 2 (düşük) — UX | İstemcide tek otomatik retry: 403 `csrf_invalid` → meta/gizliden yeni token oku → tekrar gönder (§5.1 #7); per-request rotasyon YAPILMAZ (UX — kaynak 1) |

### 4.4 Fallback (Fail-Closed / Muafiyet Kill-Switch)

| # | Koşul | Eylem | Onay |
|---|-------|-------|------|
| 1 | **Katman 3 yayında false-positive üretirse** (legit istekler 403) | Config bayrağı ile Origin/Referer katmanını **log-only**'ye al (OWASP rollout sırası: logla → gör → blokla); katman 1 token hâlâ zorunlu → güvenlik düşmez, yalnız 3. ayak gevşer; kök neden (proxy header strippi) çözülmeden enforce'e dönülmez | Security Engineer (üretimde: Tech Lead) |
| 2 | **Token sızıntısı / XSS ihlali şüphesi** | Oturumu destroy et (SessionLifecycle `destroy()` — token + session ID + nonce sıfırlanır), tüm aktif oturumları kapat, CSP'yi sıkılaştır (ADR-012), olayı log ERROR | Security Engineer → L2 (AGENTS §10.1) |
| 3 | **Cookie-auth'lı API'da toplu 403** (frontend token göndermeyi unuttu) | API CsrfMiddleware rollout'u **log-only**dan başlat (§5.1 #4 adımı 2 fazlı); frontend `X-CSRF-Token` eklenene kadar enforce ertelenir — ama **anonim/geç** yapılmaz | Backend Architect + Security |
| 4 | **Bearer muafiyeti kötüye kullanılırsa/yanlış allowlist** | Allowlist'i daralt (muafiyet yalnız tekil-Bearer uçları), hibrit fallback'i olan her ucu token'a geri al; JWT stub'ı doğru implemente etmeden muafiyeti KALDIR | Security Engineer |
| 5 | **Karar geri alınacaksa** | Yeni ADR ("revert of ADR-010") — bu dosya keyfi düzenlenmez; kod geri dönüşü `git revert` + `MiddlewarePipeline` sırası test ile korunur (AGENTS §17 #10; pipeline testi satır 13) | Vault Steward + Tech Lead |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-010-csrf-protection-strategy`) + `log.md` append ("ADR-010 yazıldı (debate PENDING)") + dizin satırı doğrula (`[[../index]]` §3 `[[ADR-010-csrf-protection-strategy]]` slug eşleşmesi ✅ satır 47) | Vault Steward | 30 dk |
| 2 | Debate (3 tur / 20 persona, ADR-004/ADR-008 formatı) — **✅ TAMAMLANDI (19/1/0 KABUL)**; sonuç §7.1'e yazıldı | Debate + Vault Steward | 2 gün |
| 3 | Tech Lead onayı — **✅** (debate sonrası, §7 — 2026-09-24) | Tech Lead | 1 gün |
| 4 | **PLANNED — Katman 3 fail-closed:** `OriginCheckMiddleware` → Origin yoksa **Referer'a bak**, Referer da yoksa **403** (§2.2e algoritması); §2.2b allowlist'li uçlar (katman 4) hariç; **2 fazlı:** önce log-only, staging testleri (§2.2f #2-4) geçince enforce | Security Engineer | 1 gün |
| 5 | **PLANNED — Privilege change rotasyonu:** rol/izin/email/şifre değişiminde `$_SESSION['csrf_token']` yenile (SessionLifecycle'e `rotateCsrfToken()`); oturum başlangıcı zaten IMPLEMENTED | Security Engineer | 3 saat |
| 6 | **PLANNED — API CSRF:** `ApiMiddlewarePipeline`'e CsrfMiddleware benzeri kontrol ekle (cookie auth'lı mutating uçlar — §2.2b); **log-only faz → enforce**; Bearer/allowlist uçlar muaf | Backend Architect + Security | 1 gün |
| 7 | **PLANNED — SPA meta tag + retry:** `<meta name="csrf-token">` (HtmlShellRenderer), JS'de okuma sırası meta → gizli alan; 403 `csrf_invalid` → token tazele + tek retry (UI Designer + Backend) | UI Designer | 4 saat |
| 8 | **PLANNED — Bearer/JWT:** `validateJwtToken` gerçek imza doğrulaması (RS256/JWT lib) — **muafiyet ancak bundan sonra yürürlükte**; muaf allowlist'ini yaz (§2.2b) ⚠️ VERIFICATION REQUIRED kalkar | Security Engineer | 1 gün |
| 9 | **PLANNED — Yedek katman 4 (yalnız gerekiyorsa):** signed + `__Host-` + `SameSite=Strict; Secure` csrf cookie'si; naive double-submit yasak (§2.2a kuralı) | Security Engineer | 4 saat |
| 10 | **Test kapısı:** §2.2f 9 test + `CsrfMiddlewareTest` genişletmesi (fail-closed satırı, API muafiyet, login kapsamı, rotasyon) — hepsi geçmeden adım 4/6/7 prod'a girmez | QA Engineer | 1 gün |
| 11 | **`SameSite` assert CI'da:** Set-Cookie `Lax` (yedekte `Strict; Secure; __Host-`) + pipeline sırası testi yeşil (`MiddlewarePipelineTest` #6_Csrf) | QA + DevOps | 2 saat |
| 12 | Cross-ref denetimi: ADR-004 (domain/SPA akışı), ADR-008 (bypass kapsamı değişmiyor — düz metin), ADR-011 (session — düz metin), ADR-012 (CSP kesişimi — düz metin) çelişmiyor | Security + Backend | 2 saat |
| 13 | Vault senkronu: `[[../../brain]]` ADR-010 özeti + `.ai/.decisions/index.md` §3 satır durumu + debate/Tech Lead sonuçları (`log.md` append-only) | MO (vault-updater) | 1 saat |

### 5.2 Geri Dönüş Planı

Karar süreç karardır; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Katman 3 yanlış pozitif üretirse:** config ile log-only'ye al (§4.4 #1) — katman 1 token zorunluluğu **kapatılmaz**, güvenlik katmanı 2'ye düşer ama sıfırlanmaz; (2) **Katman 3/6 geri alınırsa:** `git revert` + `MiddlewarePipeline` sırası test ile korunur (pipeline testi bozulursa derhal revert — AGENTS §17 #7); (3) **Bearer muafiyeti geri alınırsa:** allowlist'i sil, tüm mutating uçlar token zorunlu (fail-closed varsayılan zaten bu) — kesintisiz geri dönüş; (4) **Token sızıntısı/oturum ihlali:** `SessionLifecycle::destroy()` + toplu oturum kapatma (§4.4 #2) — veri kalıcı bozulmaz; (5) **Strict/Lax değişikliği geri alınacaksa:** `session_set_cookie_params` tek noktadır (SessionInitializer satır 40-47) → Lax'e dönmek tek satır (cookie nitelikleri oturum sonunda otomatik tazelenir); (6) **Yedek katman 4 kapatılırsa:** yalnız katman 3'ün muaf listesindeki uçlar etkilenir — acil durumda o uçlar da 403'e çekilir (fail-closed); (7) **Vault bozulursa:** `git checkout` + son commit (AGENTS §17 #10). **Veri/state kaybı yoktur** — bu ADR istek-doğrulama karar katmanıdır; geri dönüş kod revert + config ile dakikalar içinde uygulanır.

### 5.3 Debate Şartları (3 bağlayıcı şart — §7.1, 2026-09-24)

| # | Şart | Bağlantı | Durum |
|---|------|----------|-------|
| 1 | **Origin fail-closed + Referer fallback:** Origin yoksa Referer'a bak, ikisi de yoksa 403 — mevcut fail-open (`OriginCheckMiddleware.php` satır 39-41) **debate şartı ile kapatılacak** | §2.2a Katman 3 satırı · §2.2e algoritma satır 3-4 · §5.1 #4 | **ŞART — PLANNED (zorunlu)** |
| 2 | **Login CSRF + token rotasyon testi:** login/register POST'larında token zorunluluğu + privilege-change rotasyonu testi (eski token → 403, yeni token → 200) | §2.2f #7-8 · §5.1 #5, #10 | **ŞART — PLANNED (test kapısı)** |
| 3 | **JWT stub kapanana kadar cookie-auth tek yol:** `validateJwtToken` stub `null` dönerken (AuthenticationMiddleware satır 92-106) Bearer muafiyeti yürürlüğe girmez; cookie-auth tek geçerli yol, Bearer yalnız API | §1.4 "Bearer muafiyeti JWT'ye bağlı" · §2.2b sınır (ii) · §5.1 #8 | **ŞART — ⚠️ VERIFICATION REQUIRED (kapanışta kalkar)** |

*Bu 3 şart debate sonucunun bağlayıcı çıktısıdır (§7.1 Tur 3: 19 kabul / 1 çekimser / 0 red → KABUL, 2026-09-24); şartlar kapanmadan §5.1 #4/#6/#8 adımları prod'a girmez.*

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA — token akışı ADR-004'ün domain haritasında dolaşır; oturum cookie'si `domain=.coremusic.net` tüm subdomainlere yayılır (subdomain cookie zaafının yüzeyi); SPA `X-CSRF-Token` akışı bu kararın client ayağı; **dosya diskte VAR** ✅ |
| [[../index]] | Karar dizini — bu ADR'nin kaydı §3 `[[ADR-010-csrf-protection-strategy]]` (slug eşleşmesi ✅ — satır diskte mevcut, satır 47); ADR-011/012 satırları da burada (satır 48-49) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Hard Guardrail (#16 şablon zorunluluğu), REDACTED politikası |
| [[../../AGENTS.md]] | §16 (Security: "CSRF=`csrf_token`"), §10 escalation (CSRF/CSP uyumsuzluğu → L1 Security → L2), §17 #10 vault kurtarma, §25.2 (Middleware ×11 — `CsrfMiddleware` buna dahil), §25.3 frozen/append-only kuralları, §24.3 (Security okuma listesi: `.ai/.decisions/index.md (ADR-010)` + `shared/src/Middleware/`) |
| [[../../brain]] | Mimari karar özeti — ADR-010 satırı vault-sync ile tazelenir |
| [[../../log]] | Audit trail — bu ADR ve debate/revizyon kayıtları append edilir (append-only) |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| [[../../WORKFLOW.md]] | Süreçler — debate/onay akışı bağlamı |
| `shared/src/Middleware/CsrfMiddleware.php` | **Katman 1 — IMPLEMENTED** (satır 24-58: metot kapsamı, token, `hash_equals`, 403); Origin/Referer + double-submit eklemeleri PLANNED (§5.1 #4) |
| `shared/src/Middleware/OriginCheckMiddleware.php` | **Katman 3 — kısmen IMPLEMENTED / PLANNED:** allowlist 403 (satır 43-54) + pipeline #1 (Kernel satır 273); **fail-open (satır 39-41) + Referer fallback yok → fail-closed PLANNED** (§5.1 #4); doc comment satır 13 "ADR-010/022 uyumlu" |
| `shared/src/Session/SessionInitializer.php` | **Katman 2 — IMPLEMENTED** (satır 40-47: `samesite => 'Lax'`, HttpOnly, Secure, `domain .coremusic.net`); Strict yedek cookie PLANNED (§2.2d) |
| `shared/src/Session/SessionLifecycle.php` | Token üretimi IMPLEMENTED (satır 47-49) + session ID rotation (satır 54-62); **privilege-change rotasyonu PLANNED** (§5.1 #5) |
| `shared/src/Middleware/SecurityHeadersMiddleware.php` | Set-Cookie/SameSite **YOK** (cookie katmanı Session'da — §1.1); CSRF kesişen defanslar IMPLEMENTED: CSP nonce+strict-dynamic (ADR-012), `form-action 'self'`, `Referrer-Policy` (katman 3'ü besler) |
| `shared/src/Security/ReturnUrlPolicy.php` | Cookie/SameSite ile **ilgisi YOK**; open-redirect allowlist (IMPLEMENTED satır 7-80) — redirect ile token/istek taşıma yüzeyini kapatan destek katmanı |
| `shared/src/Api/Middleware/AuthenticationMiddleware.php` | Hibrit auth: **cookie önce (satır 37-45 → token zorunlu)**, Bearer sonra (satır 48-57 → muaf); **`validateJwtToken` stub `null` (satır 92-106) → Bearer muafiyeti PLANNED/⚠️** (§5.1 #8) |
| `shared/src/PageRouter/HtmlShellRenderer.php` · `PageRouterKernel.php` | Token dağıtımı: gizli alan satır 129 (IMPLEMENTED), meta tag PLANNED (§5.1 #7); pipeline #6 Csrf kaydı (Kernel satır 278, test #6_Csrf korumalı) |
| `shared/src/Middleware/CorsMiddleware.php` · `auth.coremusic.net/config/cors.php` | `X-CSRF-Token` allowlist (satır 32 / satır 13) — SPA header kanalı IMPLEMENTED |
| `shared/tests/Middleware/CsrfMiddlewareTest.php` · `MiddlewarePipelineTest.php` | ADR-010 testleri (csrf_token key, metot kapsam, redler) + sıra kilidi ("Pipeline sırası değişirse CSP/CSRF bozulur") |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅, v7.2.0 — 2+ çapraz kaynak) |
| **Düz metin (dosya diskte VAR — wiki-link KURULMAZ, kullanıcı onayı): ADR-008** | Auth bypass scope — CSRF katmanları bypass'tan bağımsız; `.ai/.decisions/accepted/ADR-008-bypass-auth-middleware.md` |
| **Düz metin (dosya diskte YOK — wiki-link kurulmaz): ADR-011** | Session Management — `henüz yazılmadı — vault: .ai/.decisions/index.md §3 [[ADR-011-session-management]]`; oturum politikaları (rotasyon, lifetime) bu ADR ile kesişir |
| **Düz metin (dosya diskte YOK): ADR-012** | CSP Nonce Strict-Dynamic — `henüz yazılmadı — vault: .ai/.decisions/index.md §3 [[ADR-012-csp-nonce-strict-dynamic]]`; **XSS↔token köprüsünün kapatıcısı** (§4.3 risk 1) |
| §5.3 · §7.1 debate kaydı | **3 bağlayıcı debate şartı** — (1) Origin fail-closed + Referer fallback · (2) login CSRF + token rotasyon testi · (3) JWT stub kapanana kadar cookie-auth tek yol (Bearer yalnız API); debate 3/20 → 19/1/0 KABUL (2026-09-24) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-010'u sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate sonucu onayı (19/1/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/ADR-008 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** |
| Sonuç | **KABUL** — Tur 3 oyu: 19 kabul / 1 çekimser / 0 red (2026-09-24) |
| Tech Lead | ✅ (debate sonrası onay — §7 onay tablosu) |
| Şartlar | 3 bağlayıcı şart → §5.3 (fail-closed · login CSRF+rotasyon testi · JWT stub/cookie-auth) |

**Tur 1 — Kanıt sunumu (20 persona).** Kod kanıtı: `CsrfMiddleware` VAR + `hash_equals` ✅ · `OriginCheckMiddleware.php:39-41` fail-open 🔴 · Referer fallback YOK · `SameSite=Lax` (`SessionInitializer.php:40-47`). Oy eğilimi: **15 kabul/neutral, 4 uyarı → 19/1 eyalet.** Uyarılar: **Security** — fail-open şart (Origin'siz istek geçemez); **QA** — login CSRF + rotasyon testi; **Critic** — JWT stub yarım → cookie-auth tek yol şart; (4. uyarının ismi kayda geçmedi — ⚠️ VERIFICATION REQUIRED).

**Tur 2 — İtiraz → Çözüm:**

| # | İtiraz | Çözüm | Karar |
|---|--------|-------|-------|
| 1 | Origin fail-open — Origin'siz istek sessizce geçer (`OriginCheckMiddleware.php:39-41`) | Origin yoksa Referer, ikisi yoksa **403** (fail-closed) | **ŞART 1** |
| 2 | Strict cookie subdomain riski (`.coremusic.net`) | **Lax kalır** (varsayılan), Strict opsiyonel + `__Host-` yedek cookie notu | Karar — §2.2d (şart değil) |
| 3 | Login CSRF — state-changing istek korumasız | POST + token zorunlu; GET state-change yasak maddesi | **ŞART 2** |
| 4 | JWT stub yarım (`AuthenticationMiddleware:92-106` hep `null`) | cookie-auth tek yol, **Bearer yalnız API** (muafiyet stub kapanana kadar pasif) | **ŞART 3** |

**Tur 3 — Oy:** **19 kabul / 1 çekimser / 0 red → KABUL** (2026-09-24).

**Bağlayıcı 3 şart:** (1) Origin fail-closed + Referer fallback · (2) login CSRF + token rotasyon testi · (3) JWT stub kapanana kadar cookie-auth tek yol → ayrıntı ve bağlantılar §5.3.

---

*1.0.0 | 2026-09-24 | Created*
*Authority: ADR-010 Karar Metni — CoreMusic Architecture Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
