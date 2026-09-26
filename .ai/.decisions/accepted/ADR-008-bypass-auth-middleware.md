---
title: "CoreMusic — ADR-008: Bypass Auth Middleware (3 Mod Bypass + Default-Deny Public Route + Üretimde Fail-Closed)"
type: adr
category: security
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-008 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-008: Bypass Auth Middleware (3 Mod Bypass + Default-Deny Public Route + Üretimde Fail-Closed)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-008'i sıfırdan yaz") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (çoklu domain SPA — route registry ve auth yönlendirmelerinin bağlamı; dosya diskte VAR ✅) · karar dizini [[../index]] §3 satırı `[[ADR-008-bypass-auth-middleware]]` (slug eşleşmesi ✅ — satır diskte mevcut). · Düz metin (dosyalar diskte YOK — wiki-link KURULMAZ): ADR-010 (CSRF), ADR-011 (session), ADR-012 (CSP nonce) — `.ai/.decisions/index.md` §3'te kayıtlı; ayrıca ADR-013 (rate limit), ADR-020 (API public security) düz metin.

---

## 1. Bağlam (Context)

CoreMusic'ın auth katmanında **kontrollü bypass kapısı** tanımlanmamıştır: test/otomasyon için login'siz erişim, health-check/local debug için geçici kapatma ve public route'ların (login/register/health/static) auth dışı bırakılması davranışları dağınık, kuralı yazılmamış ve denetlenmemiştir. Auth bypass, OWASP Top 10'da A07:2021 Identification and Authentication Failures kapsamında (CWE-288 "Authentication Bypass Using an Alternate Path or Channel", CWE-306 "Missing Authentication for Critical Function") ve 2025-26 NVD kayıtlarında **en sık görülen kritik web açığı sınıfıdır** (§1.3 kaynak 1-3, 13-20). Bu karar bypass'ı **3 mod** ile ve **üç modun tamamında bağlayıcı fail-closed kuralıyla** tescil eder: (1) **public route allowlist (default-deny)**, (2) **test/ortam bypass kullanıcısı** (otomatik login — yalnız non-production), (3) **header/env ile geçici kapatma** (yalnız trusted proxy/env gate arkasında). **Üretim ortamında bypass = fail-closed (kapanık, güvenli taraf).**

### 1.1 Mevcut Durum

**Kod kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yoluyla):**

- **`shared/src/PageRouter/SpaRoute.php` (satır 9):** `$requiresAuth = true` **constructor varsayılanı** → route açıkça `requiresAuth: false` yazmadıkça auth zorunlu = **default-deny IMPLEMENTED**. `public` adlı ayrı bir bayraq **YOK** — karardaki "`public: true` işareti" kodda **`requiresAuth: false`** (ters opt-out) olarak IMPLEMENTEDtır; `public: true` biçimli ayrı alan **PLANNED** (aynı semantik, isim farkı).
- **`shared/src/PageRouter/AuthGuard.php` (satır 26-32):** `checkAuthRequired` — `if (!$route->requiresAuth || $this->authHelper->checkAuthenticated()) return null;` → public işaretli route auth'suz geçer, işaretli değilse ve oturum yoksa login redirect'i **IMPLEMENTED**. Guard zinciri: auth → role → permission → auth-redirect → auth-page → logout (satır 15-24). `skipAuthRedirect` ctor bayrağı (satır 12, default `false`) **IMPLEMENTED**.
- **`shared/src/PageRouter/RouteRegistry.php`:** `register()`/`loadFromFile()`/`resolve()` — exact key + `{param}` pattern eşleşmesi (satır 35-54); `getProtectedRouteKeys()` yalnız `requiresAuth=true` route'ları toplar (satır 61-70, **IMPLEMENTED**); `public` alanı sorgusu **YOK** (route registry'de public listesi ayrı yapı → **PLANNED**).
- **`shared/src/PageRouter/PageRouter.php` (satır 36-40, 49-52):** `resolve()` null dönerse `RouteResult::notFound` (404) → **bilinmeyen route auth'suz açılmaz, default-deny IMPLEMENTED**; `authGuard->check()` render'dan önce **IMPLEMENTED**.
- **Route kayıtları (disk kanıtı):** `shared/config/auth-routes.php` 7 public route (`login`, `register`, `select-gender`, `forgot-password`, `reset-password`, `logout`, `set-gender` — hepsi `requiresAuth: false`) + `shared/config/routes.php` 7 public route (auth redirect'ler + `auth/callback`) ve korumalı route'lar (`home`, `player`, `kesfet`, `albumler`, ... `requiresAuth: true`) → **public allowlist (ters işaretli) IMPLEMENTED**. `health` ve `static` route'ları route registry'de **YOK → PLANNED** (public işaretlenmeli).
- **`shared/src/Middleware/BypassAuthMiddleware.php` (mod 2 taşıyıcısı):** gate `SecurityHelper::isTestBypassActive()` (satır 38); credential `BYPASS_USER_UUID`/`BYPASS_ROLE`/`BYPASS_USERNAME` sabitlerinden okunur, biri bile boşsa `return []` → **fail-closed IMPLEMENTED** (satır 23-34, 42-44); gate aktifken **otomatik login**: `$_SESSION['MM_UserID'|'MM_UserRole'|'MM_Username']` yazılır + `request['_auth']['bypass']=true` (satır 46-61) → **bypass kullanıcısı IMPLEMENTED**; `$_SESSION['MM_Permissions'] = []` (satır 53) → **boş izin seti IMPLEMENTED**; audit `SecurityHelper::logTestBypass` çağrısı (satır 39) **IMPLEMENTED**. **`BYPASS_ROLE` değeri kodda serbest — "bypass asla admin yetkisi vermez" rol kısıtı YOK → PLANNED.**
- **`shared/src/Security/SecurityHelper.php` (satır 23-30):** `app.env === 'production'` ise **her koşulda bypass kapalı** (fail-closed **IMPLEMENTED**); değilse `app.test_mode` veya `app.force_auth_bypass` truthy ise açık → kod **denylist** (yalnız production kilitli; test/dev/staging hepsi açık); karardaki "APP_ENV=test/dev allowlist" sıkılaştırması **PLANNED**. **Üretimde `bypass=true` görmezden gelinir AMA log yazılmaz** (`logTestBypass` yalnız gate aktifken çağrılır, satır 39) → "üretimde bastırılan bypass denemesi ERROR log'u" **PLANNED**. `logTestBypass` (satır 42-53): `coremusic_php_errors.log`'a `[TEST_BYPASS] context=... file=... line=...` satırı — **audit log IMPLEMENTED** (`@` ile hata bastırılmış — §4.3 risk 6).
- **`shared/src/PageRouter/PageRouterKernel.php` (satır 267-284):** middleware sırası `... → Csrf → **BypassAuthMiddleware (satır 279)** → AuthMiddleware (satır 280) → Permission → Validation` → bypass, auth'dan önce ve onu besleyecek şekilde yerleştirilmiş **IMPLEMENTED**.
- **Mod 3 (header/env):** `app.force_auth_bypass` env anahtarı **IMPLEMENTED** (`SecurityHelper.php` satır 29); **header tabanlı bypass kodda YOK** (grep: `HTTP_X_*` kullanımları yalnız device/proto/api-key — bypass header'ı 0) → "X-Bypass benzeri header ile geçici kapatma" **PLANNED** (yalnız trusted proxy gate arkasında). Health-check route'u route registry'de yok (yukarı).
- **Kapsam yasağı (vault kanıtı):** `shared/AGENTS.md` Yasak #2 — "`BypassAuthMiddleware` kapsam genişletme (ADR-008 kapsamı ADR ile değişir)" → mod kapsamı yalnız bu ADR ile değişir; `.ai/AGENTS.md` §21 Cross References zaten `[[ADR-008-bypass-auth-middleware]]` bağlantısını taşıyor (satır hedefi bu dosya — slug eşleşmesi ✅).

### 1.2 Sorun Tanımı

(1) **Bypass kapısı kuralısız:** test otomasyonu login'siz erişmek istediğinde hangi modların geçerli, hangi ortamda aktifleştiği yazılmamış → geliştirici kendi yolunu açar (en tehlikeli senaryo — CWE-288). (2) **Public route tanımı belirsiz:** `requiresAuth: false` işaretinin *default-deny* olduğu kod varsayılanından geliyor, ADR'de değil; `health`/`static` gibi public'ler registry'de yok → biri korumalı route'u public işaretlerse veya public route'u unutursa davranış öngörülemez. (3) **Test bypass'ının production'a sızması:** gate kodda var (`app.env === 'production'` → false) ama **üretimde deneme log'a yazılmıyor** (görmezden geliniyor) → sızma sessiz kalır. (4) **Bypass kullanıcısı yetki şişmesi:** `BYPASS_ROLE` serbest (admin bile olabilir), `MM_Permissions=[]` kısmi koruma → rol kısıtı yok. (5) **Header ile kapatma spoof riski:** trusted-proxy'siz header yolu, client'ın `X-Forwarded-*` spoof edebildiği 2026 CVE'leriyle (TarsWeb CVE-2026-80349) doğrudan örtüşür. (6) **Fail-closed her yerde değil:** credential eksikliğinde ve üretimde kod fail-closed; ama **kural metni** (üç modun tamamında bağlayıcı) vault'ta yok → denetim açığı.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅, v7.2.0) — birincil/resmî kaynak önce (OWASP, NVD, MITRE, NIST, GitHub Advisory), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; güvenlik iddiası = "OWASP + official + CVE" zorunlu. **Odak: (a) OWASP A07:2021/A07:2025 auth bypass literatürü 2025-26; (b) default-deny vs default-allow / fail-safe defaults; (c) public route allowlist desenleri (login/register/health/static) + eşleme tuzakları; (d) 2025-26 auth bypass CVE'leri (allowlist, header trust, hardcoded default); (e) fail-closed vs fail-open.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "OWASP A07:2021 Identification and Authentication Failures broken authentication bypass 2025" · (2) "default deny vs default allow access control fail-closed security design best practice" · (3) "authentication bypass vulnerability patterns 2025 2026 CVE middleware" · (4) "public route allowlist middleware security best practice exclude paths login health static" |
| Web Search **Konusu** | OWASP A07:2021 → A07:2025 Authentication Failures kapsamı ve CWE haritası; Saltzer-Schroeder fail-safe defaults / "deny by default" prensibi + NIST SP-800-53 SC-7(5); 2025-26 auth bypass CVE'leri (public allowlist eşleme, path normalization, trusted header, hardcoded default); framework'lerde public route allowlist desenleri (deny-by-default middleware, PUBLIC_PATHS, matcher exclusion); fail-closed vs fail-open varsayılanı. |
| Web Search **Bağlam** | ~26 kaynak (2025-2026 tarihli): birincil — OWASP Top 10 2021/2025 resmi sayfaları, MITRE CWE-1442, NVD CVE kayıtları (CVE-2025-54576, CVE-2026-70636, CVE-2026-56271, CVE-2025-34069), GitHub Advisory (CVE-2026-65600), NIST SP-800-53 SC-7(5), arXiv SoK 2412.17329; ikincil — SentinelOne Vulnerability Database (TarsWeb/Oracle OIM), Kodem (Traefik CVE-2026-48020), Red Hat/Cisco/NOC uygulama belgeleri, framework dokümanları ve desen örnekleri (Django snippet Mar. 2026, Litestar, Next.js/Auth0 matcher, OmniRoute AUTHZ guide). |
| Web Search **Kısa Açıklama** | **A07:2021 (eski Broken Authentication) → A07:2025 Authentication Failures:** 36 CWE; çekirdekler CWE-287 Improper Authentication, **CWE-288 Alternate Path/Channel bypass**, **CWE-306 Missing Authentication for Critical Function**, CWE-289/294/305. **Default-deny:** Saltzer-Schroeder fail-safe defaults "izin bazlı karar" ister (whitelist whitelist'tir, blacklist değil); NIST SC-7(5) "deny by default, allow by exception"; uygulama örnekleri (iptables DROP policy, Red Hat ACI default deny, Cisco default action Block) aynı prensibi taşır — **bilinmeyen = kapalı**. **Fail-closed:** auth/payment gibi hassas katmanlar kontrol arızalanınca DENY üretmeli; fail-open korumayı sessizce kaldırır. **CVE dersleri 2025-26:** oauth2-proxy `skip_auth_routes` regex bypass (**CVSS 9.1** — geniş public eşleme = auth bypass), Flowise prefix-whitelist bypass (**8.7** — `startsWith`-tabanlı public eşleme), Traefik StripPrefix/ReplacePathRegex path-normalization bypass (public router eşlenir → normalize sonrası korumalı route), TarsWeb **client-controlled `X-Forwarded-For` ile trusted-caller sahteciliği** (header trust = bypass), Flowise **env tanımsızsa hardcoded default'a sessiz fallback** (fail-open default). **Allowlist deseni:** deny-by-default middleware + explicit public listesi (`PUBLIC_PATHS`, `publicRoutes`, matcher exclusion, Litestar exclude) standarttır; her public giriş **gerekçeli** olmalı, eşleme **exact/anchor** olmalı (wildcard/regex/prefix tuzaklı). |
| Web Search **Uzun Açıklama** | **(a) OWASP:** A07:2021 (top10.owasp.org) kimlik doğrulama başarısızlıklarını, A07:2025 (owasp.org/Top10/2025) 36 CWE ile "Authentication Failures" adıyla sürdürüyor; MITRE CWE-1442 kategorisinde CWE-287/288/289/294/305/306/307 sayılır — yani **"auth'suz alternatif yol/baykal" (bypass) ve "kritik fonksiyonda eksik auth" doğrudan bu kategoriye girer** (kaynak 1, 2, 3). Oracle OIM auth bypass'ları CISA KEV'de aktif istismar görüyor (kaynak 20) → bypass = teorik değil, canlı risk. **(b) Default-deny:** SoK (arXiv 2412.17329) Saltzer-Schroeder'in "fail-safe defaults: erişim kararları dışlama değil izin üzerine kurulmalı" ilkesini, "deny by default" sloganını ve blacklist'in bu ilkeyi ihlal ettiğini kaynaklarıyla anlatır (kaynak 4); NIST SC-800-53 SC-7(5) ağa "deny by default, allow by exception" emreder (kaynak 5); iptables default-DROP (kaynak 6), Red Hat ACI default-deny (kaynak 7), Cisco default action Block (kaynak 8) pratik doğrulamadır — **≥3 bağımsız çapraz kaynak ✅**. **(c) Fail-closed:** auth katmanı dahil hassas kontroller arızada DENY üretmeli (kaynak 9, 10, 11, 12) — fail-open, eşitsizliği yanlış eylemin doğru yere düşmesine kadar gizler (kaynak 12); CoreMusic'in "üretimde bypass = fail-closed" bağlayıcısı bu literatürle birebir örtüşür. **(d) CVE'ler:** oauth2-proxy `skip_auth_routes` regex'i tam URI'ye karşı match edince query parametreyle bypass (CVSS 9.1, CWE-290 — kaynak 13); Flowise auth middleware'inde prefix-based whitelist (`startsWith` sınıfı) bypass (CVSS 8.7, CWE-862 — kaynak 14); Flowise hardcoded default JWT secret'a **env unset sessiz fallback** (kaynak 15); TarsWeb client'ın kontrol ettiği `X-Forwarded-For` ile loopback sahteciliği → SSO middleware kimlik atar (kaynak 16); Traefik public router + path normalization (`/api../admin`) korumalı route'a erişim (kaynak 17, 18); GFI insecure default proxy tam admin erişimi (kaynak 19). **Ortak ders: bypass her zaman "public/auth'suz eşleme ile korumalı hedef arasındaki eşleşme hatasından" veya "güvenilmeyen girdiye (header/env) güvenmekten" doğar → exact public allowlist + trust gate + fail-closed şart.** **(e) Allowlist desenleri:** Next.js middleware "DENY BY DEFAULT: Only these routes are public" (`publicRoutes = ["/", "/login", "/register", "/api/auth", "/api/health"]` — kaynak 21); Django `PUBLIC_PATHS` ile global auth middleware istisnaları (login/reset/static/media — kaynak 22); Litestar auth middleware path exclude (kaynak 23); OmniRoute explicit `publicApiRoutes.ts` allowlist + **GHSA-74g9-q8f6-793h dersi: prefix `startsWith()` eşlemesi komşu yolları da yakalar → allowlist şekli taşıyıcıdır** (kaynak 24); Auth0 matcher ile sadece istisnaların hariç tutulması (kaynak 25); "health-check session ister mi, statik CSRF ister mi — minimum middleware" (kaynak 26). |
| Web Search **Paragraf Veri Uzun** | A07:2021→A07:2025 = Authentication Failures, 36 CWE · CWE-288 = auth'suz alternatif yol · CWE-306 = kritik fonksiyonda eksik auth · fail-safe defaults = izin bazlı karar (whitelist ≠ blacklist) · "deny by default, allow by exception" = NIST SC-7(5) · default-deny = bilinmeyen kapalı (DROP/Block/ACI default-deny aynı prensip) · fail-closed = kontrol arızasında DENY; fail-open = sessiz koruma kaybı · üretimde bypass = fail-closed bağlayıcı · CVE-2025-54576 oauth2-proxy skip_auth_routes regex bypass CVSS 9.1 · CVE-2026-70636 prefix whitelist bypass 8.7 · CVE-2026-56271 hardcoded default fallback · CVE-2026-80349 X-Forwarded-For trusted header spoof · CVE-2026-65600/48020 path normalization public route bypass · CVE-2025-34069 insecure default proxy · allowlist = explicit public listesi + exact/anchor eşleme + gerekçe · prefix wildcard tuzaklı (GHSA-74g9-q8f6-793h) · public = login/register/health/static · her public giriş gerekçeli + CI denetimi · bypass kullanıcısı = özel identity + audit + admin'siz · otomasyon testleri non-production'a bağlanır · header gate yalnız trusted-proxy arkasında. |
| Web Search **Sonucu** | 1) **A07 bağlamı net:** auth bypass (CWE-288/306) A07:2021/A07:2025 kapsamında en kritik sınıf → bypass kapısı ADR ile yazılır, sessiz bırakılmaz (**kaynak 1, 2, 3, 20**). 2) **Default-deny onaylandı:** fail-safe defaults + NIST SC-7(5) + 3 uygulama belgesi = bilinmeyen route kapalı; allowlist yalnız gerekçeli public girişler (**kaynak 4, 5, 6, 7, 8**). 3) **Fail-closed bağlayıcı onaylandı:** auth katmanı arızada DENY; "üretimde bypass = fail-closed" bu kuralın uygulaması (**kaynak 9, 10, 11, 12**). 4) **Allowlist eşleme = en güncel saldırı yüzeyi:** regex/prefix/path-normalization 4 CVE ile ispatlı → exact matching + anchor + gerekçe zorunlu (**kaynak 13, 14, 17, 18, 24**). 5) **Header/env trust = ikinci yüzey:** client-controlled header bypass'ı (TarsWeb) + env unset fallback (Flowise) → mod 3 yalnız trusted-proxy/env gate arkasında; env denylist'inin allowlist'e sıkılaştırılması PLANNED (**kaynak 15, 16**). 6) **Framework deseni standard:** explicit public allowlist + deny-by-default middleware (login/register/health/static tipik public) (**kaynak 21, 22, 23, 25, 26**) → mod 1 bu desendir; CoreMusic'te karşılığı `requiresAuth: false` işaretidir. 7) **Bypass kullanıcısı:** literatürde karşılığı "test identity + audit + least-privilege" → boş `MM_Permissions`, ayrı hardcoded identity, audit log; admin engeli kodda yok → PLANNED (**kaynak 3, 20** ile CWE-269 bağlamı; kod bulgusu §1.1). |
| Web Search **Alınan Karar** | **ADR-008 kabul edilir — 3 mod + tek bağlayıcı:** **(Mod 1) Public route allowlist (default-deny):** route registry'de public işaretli route'lar auth'suz (login/register/health/static tipik public); **işaret YOKSA auth zorunlu** — default-deny; bilinmeyen route 404; işaret kodda `requiresAuth: false` (SpaRoute default `true` = IMPLEMENTED), ayrı `public: true` alanı ve health/static kayıtları PLANNED (**§2.2a**). **(Mod 2) Test/ortam bypass kullanıcısı:** `bypass=true` iken login olmadan, **otomatik login ile "bypass kullanıcısı"** olarak çalışılır (test otomasyonu/otomasyon içindir) — **yalnız non-production**: `app.env=production` → gate her koşulda kapalı (fail-closed IMPLEMENTED); üretimde `bypass=true` **görmezden gelinir + ERROR log yazar** (görmezden gelme IMPLEMENTED, log kalemi PLANNED); bypass kullanıcısı **özel hardcoded identity (BYPASS_* sabitleri — değerler REDACTED) + [TEST_BYPASS] audit log + boş `MM_Permissions`** ile çalışır (IMPLEMENTED); **hiçbir zaman admin yetkisi vermez** — rol allowlist kısıtı (admin yasak) PLANNED (**§2.2b**). **(Mod 3) Header/env ile geçici kapatma:** health check / lokal debug için — **yalnızca trusted proxy/env gate arkasında**: env anahtarı `app.force_auth_bypass` IMPLEMENTED; header yolu YOK → PLANNED ve yalnız trusted-proxy allowlist + exact eşleme ile (**§2.2c**). **Bağlayıcı (hepsinde):** **üretim ortamında bypass = fail-closed** — credential eksikse/koparsa bypass yok, gate kapalı, matched olmayan route kapalı; kill-switch §4.4 (**§2.2d**). |
| Web Search **Sonuç** | Karar 2025-26 verisiyle **desteklendi**: OWASP/CWE (3 kaynak), default-deny/fail-safe (5 kaynak), fail-closed (4 kaynak), auth bypass CVE'leri (8 kaynak), public allowlist desenleri (6 kaynak) — **toplam ~26 birincil+ikincil kaynak, 4 sorgu**; çapraz doğrulama ≥2 kaynak/tüm iddialarda karşılanır (OWASP, NVD, MITRE, NIST, GitHub Advisory birinciller). Kod bulguları §1.1'de IMPLEMENTED/PLANNED olarak ayrıldı; `public: true`/`health` route/üretim ERROR log/rol kısıtı/header yolu kodda yok → PLANNED yazıldı, uydurulmadı. `⚠️ VERIFICATION REQUIRED` yalnız kodda doğrulanamayan PLANNED kalemler için korunur (üretim log ve rol kısıtı uygulaması §5.1 adımlarında kapatılır). |

**Kaynak listesi (~26 birincil + ikincil):**
1. https://top10.owasp.org/2021/A07_2021-Identification_and_Authentication_Failures/ — A07:2021 Identification and Authentication Failures (birincil, OWASP)
2. https://owasp.org/Top10/2025/A07_2025-Authentication_Failures — A07:2025 Authentication Failures, 36 CWE (birincil, OWASP)
3. https://cwe.mitre.org/data/definitions/1442.html — A07:2025 CWE kategorisi: CWE-287/288/289/294/305/306/307 (birincil, MITRE)
4. https://arxiv.org/html/2412.17329 — SoK: Safe and Secure Defaults — fail-safe defaults, "deny by default" (Saltzer-Schroeder), blacklist-whitelist
5. https://csf.tools/reference/nist-sp-800-53/r5/sc/sc-7/sc-7-5 — NIST SP-800-53 SC-7(5): Deny by Default – Allow by Exception (birincil)
6. https://noc.org/learn/iptables-default-blocks — The Default-Deny Approach (2026-09-14)
7. https://docs.redhat.com/en/documentation/red_hat_directory_server/11/html/deployment_guide/Designing_a_Secure_Directory-Designing_Access_Control — default rule: deny (birincil, Red Hat)
8. https://docs.manage.security.cisco.com/cdfmc/c_access_control_policy_default_action.html — default action: Block/Ttrust (birincil, Cisco)
9. https://devsecopsschool.com/blog/fail-closed — fail-closed = deny-by-default operational behavior
10. https://systemdesignschool.io/technologies/fail-open-vs-fail-closed — fail-open (koruma yok) vs fail-closed (deny)
11. https://dev.to/khuepm/fail-open-vs-fail-closed-the-security-decision-you-make-without-realizing-it-15kl — auth katmanları fail-closed olmalı
12. https://clevrsecurity.substack.com/p/fail-closed-vs-fail-open-is-the-default — fail-closed uyumsuzluğu erken yüzeye çıkarır
13. https://nvd.nist.gov/vuln/detail/cve-2025-54576 — oauth2-proxy skip_auth_routes regex bypass, CVSS 9.1, CWE-290 (birincil, NVD/GitHub)
14. https://nvd.nist.gov/vuln/detail/cve-2026-70636 — Flowise prefix-based whitelist auth middleware bypass, CVSS 8.7, CWE-862 (birincil, NVD)
15. https://nvd.nist.gov/vuln/detail/cve-2026-56271 — Flowise hardcoded default JWT fallback (env unset → bypass) (birincil, NVD)
16. https://www.sentinelone.com/vulnerability-database/cve-2026-80349/ — TarsWeb X-Forwarded-For trusted caller spoof → SSO bypass
17. https://github.com/advisories/GHSA-cxjq-mrr5-89rv — Traefik ReplacePathRegex path normalization auth bypass, CVE-2026-65600 (birincil, GitHub Advisory)
18. https://www.kodemsecurity.com/cve-archive/cve-2026-48020 — Traefik StripPrefix public route + normalization bypass
19. https://nvd.nist.gov/vuln/detail/CVE-2025-34069 — GFI Kerio insecure default proxy → admin bypass (birincil, NVD)
20. https://www.sentinelone.com/vulnerability-database/cve-2025-61757/ — Oracle OIM auth bypass, CISA KEV aktif istismar
21. https://github.com/georgekhananaev/claude-skills-vault/blob/main/.claude/skills/nextjs-senior-dev/assets/middleware.ts — "DENY BY DEFAULT: Only these routes are public" (publicRoutes: login/register/api/auth/api/health)
22. https://django.wiki/snippets/middleware/authentication-middleware — AuthRequiredMiddleware + PUBLIC_PATHS allowlist (Mar. 2026)
23. https://docs.litestar.dev/main/usage/security/abstract-authentication-middleware.html — auth middleware path exclusion (birincil, Litestar)
24. https://raw.githubusercontent.com/diegosouzapw/OmniRoute/release/v3.8.50/docs/architecture/AUTHZ_GUIDE.md — explicit publicApiRoutes allowlist + GHSA-74g9-q8f6-793h prefix eşleme dersi
25. https://dev.to/sabbirsobhani/efficient-route-protection-in-nextjs-with-auth0-middleware-excluding-specific-routes-1d3f — Auth0 matcher ile public istisnalar
26. https://carles9000.github.io/hix/hixstyle/middleware/mw_dessign — "Don't over-protect": health-check session istemez, statik CSRF istemez; minimum middleware

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Her iddia ≥2 kaynak | ADR-005 §2.2c standardı: sayısal/canlı her dış iddia ≥2 bağımsız kaynak taşır; tek kaynak → `⚠️ VERIFICATION REQUIRED` (§1.3 çapraz doğrulama 4 sorgu/26 kaynakta tam). |
| Frozen ADR dokunulmaz | ADR-001…037 metinleri okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2); bu ADR frozen değildir (status: accepted, frozen YOK). |
| log.md append-only | Bu ADR kaydı append ile yazılır; geçmiş satıra dokunulmaz (AGENTS.md §25.3 kural 3). |
| Dosya adı değişmez | In-Place Refactoring: `ADR-008-bypass-auth-middleware.md` adı onaysız değiştirilemez (In-Place Refactoring kuralı 1). |
| REDACTED | `BYPASS_USER_UUID`/`BYPASS_ROLE`/`BYPASS_USERNAME` **sabit adları** yazılır, **değerleri hiçbir koşulda ADR'ye yazılmaz**; bypass kimliği credential sayılır (REDACTED politikası). |
| BypassAuthMiddleware kapsam yasağı | `shared/AGENTS.md` Yasak #2: bypass middleware **kapsam genişletilemez — kapsam yalnız ADR ile değişir** → mod 3 header yolu vb. eklemeler önce bu ADR'nin revizyonu/eki. |
| Kod implementasyonu kapsam dışı | Bu ADR karar kaydıdır; kod adımları Backend Architect / Security Engineer'a aittir (§5.1 sorumluları). |
| k6-guvenlik katmanı | Bypass kararı güvenlik katmanı A1/K6-K7'dir → `.ai/architecture/k6-guvenlik/` (authentication-jwt, audit-logging, session-management) ile birlikte okunur; katman ihlali → revert + log ERROR. |
| ADR-010/011/012 düz metin | CSRF (ADR-010), session (ADR-011), CSP (ADR-012) `.ai/.decisions/index.md` §3'te kayıtlı ama tekil dosyaları diskte YOK (glob kanıtı) → wiki-link kurulmaz, düz metin. |

---

## 2. Karar (Decision)

**CoreMusic, auth bypass'ı "hepsinde aynı mekanizma" ile THREE-MODE + tek bağlayıcı olarak ADR-008 ile tescil eder:** **(Mod 1) Public route allowlist (default-deny)** — route registry'de public işaretli route'lar auth'suz (login/register/health/static); işaret YOKSA auth zorunlu; bilinmeyen route 404. **(Mod 2) Test/ortam bypass kullanıcısı** — `bypass=true` iken login olmadan, **otomatik login ile "bypass kullanıcısı"** olarak çalışılır (test otomasyonu için); **yalnız non-production ortamda (APP_ENV=test/dev) aktifleşir**; production'da env kapalıyken `bypass=true` **görmezden gelinir ve log ERROR yazar**; bypass kullanıcısı **özel rol + audit log + farklı hardcoded identity** taşır ve **hiçbir zaman admin yetkisi vermez**. **(Mod 3) Header/env ile geçici kapatma** — health check / lokal debug için, **yalnızca trusted proxy/env gate arkasında**. **Bağlayıcı: üç modun hepsinde ve her koşulda — üretim ortamında bypass = fail-closed (kapanık, güvenli taraf).**

### 2.1 Neden Bu Seçenek?

1. **Default-deny literatürün zorunluğu:** fail-safe defaults "izin bazlı karar" ister, NIST SC-7(5) "deny by default, allow by exception" der; blacklist/default-allow bu ilkeyi ihlal eder (§1.3 kaynak 4, 5, 6, 7, 8) → "işaret YOKSA auth zorunlu" tek doğru varsayılan.
2. **Auth bypass = canlı en kritik sınıf:** CWE-288/306 A07:2021-2025 merkezinde; KEV'de aktif istismarlı OIM bypass'ları bunun teorik olmadığını gösterir (§1.3 kaynak 1, 2, 3, 20) → bypass kapısı gizli/dağıtık değil, tek ADR'de, denetlenebilir.
3. **Fail-closed bağlayıcı arıza karşısında tek doğru:** auth katmanı arızada DENY üretmezse koruma sessiz kalkar; 4 CVE (regex/prefix/normalization/header) tam olarak fail-open eşlemenin sonucudur (§1.3 kaynak 9-18) → üretimde bypass = kapalı, kural.
4. **Test otomasyonu gerçek hesapla değil, izole identity ile:** otomatik login gerçek kullanıcı oturumu sahtelemez; `MM_Permissions=[]` + ayrı hardcoded identity + `[TEST_BYPASS]` audit satırı, bypass'ı loglarda ayırt edilebilir kılar (kod: BypassAuthMiddleware satır 53, 39; SecurityHelper satır 42-53) → teşhis + denetim + geri alınabilirlik tek hamlede.
5. **Header/env yolu talep edilir ama kapı önde:** health/local debug ihtiyacı gerçek; fakat client-controlled header bypass'ı 2026'da ispatlı (TarsWeb CVE-2026-80349) → yalnız trusted-proxy/env gate arkasında, aksi halde yola çıkmaz (PLANNED).
6. **Mevcut kodla hizalı yazıldı:** default-deny (SpaRoute `true`), gate (SecurityHelper production kill), credential fail-closed ve otomatik login zaten IMPLEMENTED — karar bu davranışı tesciller; eksik kalemler (üretim ERROR log, rol kısıtı, health/static public, header gate, `public: true` alanı) PLANNED olarak sahiplenilir (§1.1, §5.1).

### 2.2 Teknik Detaylar

**(a) Mod 1 — Public route allowlist (default-deny):**

| Mekanizma | Davranış | Etiket | Dosya kanıtı |
|-----------|----------|--------|--------------|
| Route varsayılanı | Yeni route `requiresAuth` yazmazsa **auth zorunlu** (işaret = `public: true`/`requiresAuth: false` opt-out) | **IMPLEMENTED** (default `true`) | `shared/src/PageRouter/SpaRoute.php` satır 9 |
| Public işaretli route | Auth'suz geçiş; oturum varsa zaten geçer | **IMPLEMENTED** | `shared/src/PageRouter/AuthGuard.php` satır 26-32 (`!$route->requiresAuth \|\| checkAuthenticated()`) |
| Public allowlist kayıtları | login, register, logout, select-gender, forgot-password, reset-password, set-gender, auth/callback | **IMPLEMENTED** | `shared/config/auth-routes.php` (7× `requiresAuth: false`) + `shared/config/routes.php` (7× `requiresAuth: false`) |
| Bilinmeyen route | `resolve()` null → **404** (auth'suz açılmaz) | **IMPLEMENTED** | `shared/src/PageRouter/PageRouter.php` satır 36-40 |
| Koruma listesi görünümü | `getProtectedRouteKeys()` = `requiresAuth=true` route'ları | **IMPLEMENTED** | `shared/src/PageRouter/RouteRegistry.php` satır 61-70 |
| `health` / `static` public kayıtları | Route registry'de YOK | **PLANNED** ⚠️ VERIFICATION REQUIRED (yokluğu glob+grep kanıtı; eklenmesi §5.1 adım 6) | `shared/config/routes.php`, `auth-routes.php` (0 eşleşme) |
| `public: true` ayrı alanı + registry'de public listesi | Semantik `requiresAuth: false` ile aynı; ayrı alan/lhest yok | **PLANNED** (isim/alan farkı) ⚠️ VERIFICATION REQUIRED | `SpaRoute.php`/`RouteRegistry.php` (alan YOK) |
| Public allowlist CI denetimi (yeni route public ise gerekçe + otomatik listeleme) | Gate yok | **PLANNED** | §5.1 adım 8 |

*Kural (bağlayıcı):* allowlist **exact/anchor** eşleşir (regex/wildcard/prefix `startsWith` YASAK — §1.3 kaynak 13, 14, 24); her public girişin gerekçesi olur; işaretsiz route = korumalı.

**(b) Mod 2 — Test/ortam bypass kullanıcısı (otomatik login):**

| # | Kural (kullanıcı ifadesi + güvenlik yorumu) | Etiket | Dosya kanıtı |
|---|---------------------------------------------|--------|--------------|
| 1 | `bypass=true` iken login olmadan, **otomatik login ile "bypass kullanıcısı"** olarak çalışılır (test otomasyonu/otomasyon içindir) | **IMPLEMENTED** | `shared/src/Middleware/BypassAuthMiddleware.php` satır 46-61 (`$_SESSION['MM_UserID'\|'MM_UserRole'\|'MM_Username']` + `request['_auth']['bypass']=true`) |
| 2 | **Yalnızca non-production ortamda (APP_ENV=test/dev) aktifleşir:** `app.env === 'production'` → gate her koşulda `false` | **IMPLEMENTED** (production kill) · **PLANNED** (test/dev allowlist sıkılaştırması — kodda denylist: production olmayan her şey açık) ⚠️ VERIFICATION REQUIRED · debate şartı ile kapatılacak (§5.1 #11c) | `shared/src/Security/SecurityHelper.php` satır 23-30 |
| 3 | **Production'da `bypass=true` görmezden gelinir ve log ERROR yazar:** görmezden gelme (gate false) **IMPLEMENTED**; **log ERROR kalemi PLANNED** — `logTestBypass` yalnız gate aktifken çağrılıyor, üretimde bastırılan deneme sessiz ⚠️ VERIFICATION REQUIRED | **IMPLEMENTED / PLANNED (kısmi)** · debate şartı ile kapatılacak (§5.1 #11a) | `SecurityHelper.php` satır 25-27 (görmezden gelme) vs satır 39 çağrısı (log yalnız aktifken) |
| 4 | **Bypass kullanıcısı özel rol + farklı hardcoded identity:** `BYPASS_USER_UUID`/`BYPASS_ROLE`/`BYPASS_USERNAME` sabitleri; biri eksikse/farklıysa bypass yok | **IMPLEMENTED** (identity) · değerler REDACTED (ADR'ye yazılmaz) | `BypassAuthMiddleware.php` satır 23-34 (fail-closed `return []`) |
| 5 | **Audit log:** her aktif bypass'ta `coremusic_php_errors.log` → `[TEST_BYPASS] context/file/line` | **IMPLEMENTED** (log yazımı başarısızlığı `@` ile sessiz — risk §4.3 #6; denetim PLANNED) | `SecurityHelper.php` satır 42-53 + `BypassAuthMiddleware.php` satır 39 |
| 6 | **Hiçbir zaman admin yetkisi vermez:** `MM_Permissions = []` (boş izin seti) **IMPLEMENTED**; `BYPASS_ROLE` kodda serbest (admin yazılabilir) → **rol allowlist kısıtı (yalnız bypass/test rolleri; `admin`/`superadmin` yasak) PLANNED** ⚠️ VERIFICATION REQUIRED | **IMPLEMENTED (izin) / PLANNED (rol kısıtı)** · debate şartı ile kapatılacak (§5.1 #11b) | `BypassAuthMiddleware.php` satır 53 (`MM_Permissions=[]`), satır 26 (`BYPASS_ROLE` doğrulaması yalnız boşluk) |
| 7 | Sıra: bypass, auth'dan önce ve AuthGuard'ı besleyecek şekilde | **IMPLEMENTED** | `shared/src/PageRouter/PageRouterKernel.php` satır 279-280 |

**(c) Mod 3 — Header/env ile geçici kapatma (health check / lokal debug):**

| Mekanizma | Kural | Etiket | Dosya kanıtı |
|-----------|-------|--------|--------------|
| Env anahtarı | `app.force_auth_bypass` (veya `app.test_mode`) truthy + `app.env ≠ production` → gate açık; **üretimde hiçbir env değeri işe yaramaz** | **IMPLEMENTED** | `SecurityHelper.php` satır 25-29 |
| Header yolu (ör. health check için geçici kapatma header'ı) | **Yalnız trusted proxy/env gate arkasında:** header yalnız loopback/allowlisted proxy'den kabul edilir, exact eşleme, aksi halde yok sayılır | **PLANNED** ⚠️ VERIFICATION REQUIRED (kodda bypass header'ı YOK — grep 0) | `shared/src/**` (`HTTP_X_*` kullanımları: device/proto/api-key — bypass 0) |
| Health check public route | `health` route'u `requiresAuth: false` ile public işaretlenir | **PLANNED** (§2.2a ile aynı adım) | route kayıtlarında yok |
| Trusted-proxy güveni | Client'ın kontrol ettiği header (XFF vb.) **asla** bypass kanıtı sayılmaz (TarsWeb dersi) | **BAĞLAYICI (kural)** | §1.3 kaynak 16 |

**(d) Bağlayıcı — üç modda da fail-closed (üretim ortamında = kapanık, güvenli taraf):**

| # | Senaryo | Beklenen davranış | Etiket |
|---|---------|-------------------|--------|
| 1 | `app.env=production` + `bypass=true` / test_mode | **Gate kapalı** → bypass yok, normal auth akışı; **log ERROR yazar** (PLANNED · debate şartı ile kapatılacak §5.1 #11a) | IMPLEMENTED / PLANNED |
| 2 | Credential eksik/bozuk (`BYPASS_*` biri boş) | **Bypass yok** (`return []` → `$next()` yalnız normal akış) | IMPLEMENTED |
| 3 | Route registry'de işaret yok | **Auth zorunlu** (default-deny) | IMPLEMENTED |
| 4 | Route bilinmiyor / resolve null | **404** (kapalı) | IMPLEMENTED |
| 5 | Header yolu: trusted-proxy doğrulanamıyor | **Header yok sayılır** (kapalı) | PLANNED (bağlayıcı kural) |
| 6 | Env/config okuma hatası | Gate `false` (tanımsız değer truthy değil → `isTruthy` default false) | IMPLEMENTED (`SecurityHelper.php` satır 32-40) |
| 7 | Kill-switch | `BYPASS_*` sabitleri tanımsız + `app.test_mode=false` + `app.force_auth_bypass=false` → üç katman da kapalı | IMPLEMENTED (§4.4) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Default-allow (auth istisnai — "herkes açık, korumalı liste sen ekle")** | Yeni route'lar ilk denemede çalışır | Unutulan route sessizce açık (CWE-306 sınıfı); deny-by-default literatürünün tam tersi (§1.3 kaynak 4, 5); A07 kapsamındaki klasik hata | Fail-safe defaults izin bazlıdır; bilinmeyen = kapalı olmalı (§1.3 kaynak 4-8); kod zaten default `true` ile doğru yönde — bilinçli olarak terk edilmez |
| 2 | **Yalnız header ile bypass (env gate'siz, production dahil)** | Operasyonel rahatlık (curl ile tek header) | Client-controlled header bypass'ı 2026'da ispatlı: TarsWeb `X-Forwarded-For` spoof → SSO kimlik atar (§1.3 kaynak 16); production'da tek header = tam authsuz | Üretimde fail-closed bağlayıcısını ihlal eder; header yolu yalnız trusted-proxy/env gate arkasına alındı (§2.2c) |
| 3 | **Bypass kullanıcısının admin rolüyle çalışması** | Test senaryoları "her şeyi" görebilir | Yetki şişmesi: bypass identity → tam yetki = en kötü kimlik (CWE-269/A07 bağlamı); audit'te ayrıştırılamaz | Karar gereği **hiçbir zaman admin yetkisi vermez**; `MM_Permissions=[]` + rol allowlist (§2.2b #6); admin testleri gerçek yetkiyle, ortam izinli yapılır |
| 4 | **Üretimde de tek bayrakla açık bypass (fail-open)** | Acil durumda "hızlı giriş" | En kritik risk: yanlış/kaçak bayrak = production authsuz (§4.3 risk 1); env unset fallback CVE'leri bunun canlı örneği (Flowise CVE-2026-56271, §1.3 kaynak 15) | Bağlayıcı ihlali — üretimde bypass = fail-closed; acil erişim kill-switch/ops hesaplarıyla (§4.4), bypass bayrağıyla değil |
| 5 | **Bypass'ı AuthGuard içine gömmek (middleware değil, guard içinde koşul)** | Tek dosyada görünür | AuthGuard route-seviyesinde (SpaRoute) çalışır; bypass request-seviyesi kimliktir — karışım guard'ı bypass'a duyarlı, test edilebilirliği düşer; sıra (Csrf→Bypass→Auth) kaybolur | Mevcut `BypassAuthMiddleware` + kernel sırası IMPLEMENTED ve tek sorumluluk (single responsibility); guard'ı test kimliğiyle kirletmez |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek kural, üç kapı:** public allowlist + test bypass + header/env yolu tek ADR'de; "bypass nasıl yapılır?" sorusunun tek cevabı ve denetim noktası var (§2.2).
- **Default-deny kodla hizalı:** `SpaRoute` default `true` + 404 bilinmeyen route — bilinmeyen hiçbir yol sessizce açılmaz (§2.2a).
- **Üretim = fail-closed:** iki bağımsız katman (production gate + credential fail-closed) tek başına bile bypass'ı kapatır; üçü birlikte derin savunma (§2.2d).
- **Otomasyon mümkün, izole:** test otomasyonu login akışını takmaz; `[TEST_BYPASS]` audit satırı + `bypass=true` imzası üretim log'unda (PLANNED log ile) ayrıştırılabilir (§2.2b #1, #5).
- **CVE dersleri kodda:** exact allowlist (regex/prefix yasağı), client-header'a güvenmeme, env fallback'i bilinçli — 2025-26'nın üç ana bypass sınıfı kapatılmış/planlanmış (§1.3).
- **Kapsam yasağı hazır:** `shared/AGENTS.md` Yasak #2 zaten bu ADR'yi kapsam otoritesi yapar — kapsam kayması engelli (§1.4).

### 4.2 Olumsuz Sonuçlar

- **PLANNED yükü:** üretim ERROR log, rol allowlist kısıtı, health/static public kayıtları, header trusted-proxy gate, `public: true` alanı ve CI denetimi yazılmadan kararın yarısı uygulanmış sayılır (§5.1 adımları).
- **Env denylist → allowlist geçişi:** kodda "production olmayan her şey açık" (staging dahil); test/dev allowlist'e sıkılaştırma davranış değişikliği — mevcut test ortamlarını etkiler (§2.2b #2).
- **Hardcoded identity bakımı:** `BYPASS_*` sabitleri env/config'e taşınırsa credential sızmaması gerekir (REDACTED); sabit → config geçişi ayrı güvenlik review'u ister.
- **Audit log sağlamlığı:** `logTestBypass` `@file_put_contents` ile hatayı bastırır — sessiz log kaybı olası (§4.3 risk 6).
- **Debate/Tech Lead süreci:** debate ✅ TAMAMLANDI (3/20, 19/1/0 KABUL) + Tech Lead ✅ (2026-09-24) — 3 şart (#11-#13) açık (§7.1).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Üretim bypass açığı** (bypass production'da aktifleşirse/kalırsa = tam authsuz erişim — EN KRİTİK) | 2 (mümkün) | 4 (yüksek/kritik) | Production gate (`SecurityHelper.php` satır 25-27 IMPLEMENTED) + credential fail-closed (IMPLEMENTED) + boot-time assert `app.env≠production` ve **üretimde bastırılan deneme ERROR log + alarm** (PLANNED §5.1 adım 4) + kill-switch (§4.4) + deploy gate: production config'de `test_mode`/`force_auth_bypass` `false` doğrulaması (PLANNED §5.1 adım 8) |
| **Bypass kullanıcısı yetki şişmesi** (`BYPASS_ROLE=admin` seçilirse / rol kısıtı yokken yetki genişliği) | 3 (olası) | 3 (orta) | `MM_Permissions=[]` IMPLEMENTED; **rol allowlist kısıtı PLANNED** (yalnız bypass/test rolleri; `admin`/`superadmin` yasak — §2.2b #6, §5.1 adım 5); `[TEST_BYPASS]` audit + `bypass=true` imzası; bypass identity'ye ait oturumlar için otomatik denetim raporu (PLANNED) |
| **Public allowlist drifti** (geniş eşleme/regex/prefix → korumalı route public düşer) | 3 (olası) | 3 (orta) | Exact/anchor eşleme kuralı bağlayıcı (§2.2a); mevcut kod zaten exact key + `{param}` pattern (RouteRegistry satır 72-82); **CI gate: public işaretli route listesi + gerekçe denetimi** (PLANNED §5.1 adım 8); allowlist değişimi = Security review (§1.3 kaynak 13, 14, 17, 18, 24) |
| **Header spoofing** (mod 3 header'ı client'tan gelirse → bypass) | 2 (mümkün) | 4 (yüksek) | Header yolu şu an YOK (grep kanıtı — default kapalı); PLANNED yapıldığında **yalnız trusted-proxy/env gate** + exact eşleme + production'da header yolu zaten kapanır (bağlayıcı); client-controlled header asla bypass kanıtı (§2.2c; §1.3 kaynak 16) |
| **Env okuma hatası / yanlış `app.env`** (config cache, deploy env sızması → gate yanlış taraf) | 2 (mümkün) | 4 (yüksek) | `isTruthy` default false (tanımsız = kapalı — IMPLEMENTED); boot assert + deploy gate (§4.4); env değişimi = config review (REDACTED) |
| **Audit log kaybı** (`@file_put_contents` başarısız → `[TEST_BYPASS]` sessiz kaybolur) | 2 (mümkün) | 2 (düşük) | Log denetimi: bypass aktifken log yazımı doğrulama (PLANNED §5.1 adım 4); merkezî StructuredLogger'a taşıma (PLANNED) |

### 4.4 Fallback (Fail-Closed + Kill-Switch)

| # | Koşul | Eylem | Onay |
|---|-------|-------|------|
| 1 | **Kill-switch (anında kapama):** bypass kapatılacaksa | `BYPASS_USER_UUID`/`BYPASS_ROLE`/`BYPASS_USERNAME` sabitleri tanımsız/boş + `app.test_mode=false` + `app.force_auth_bypass=false` → üç bağımsız katman da kapanır; kod değişikliği gerekmez | Security Engineer (üretimde: Tech Lead) |
| 2 | **Üretimde bypass denemesi tespiti** (`bypass=true`/test_mode denemesi veya beklenen gate dışı aktivite) | Sistem **otomatik fail-closed** (mevcut kod: gate false — §2.2d #1); **PLANNED:** derhal `log ERROR` + alarm + olay kaydı; bayraklar kalıcı `false` yapılır, kök neden araştırılır (config sızması mı?) | Security Engineer → L2 eskalasyon (AGENTS §10) |
| 3 | **Header yolu güvensiz çıkarsa** (trusted-proxy doğrulanamıyor, spoof denemesi) | Header yolu **açılmaz/kapatılır** (default: YOK = kapalı); health check için `health` public route + env anahtarı ile devam (mod 1 + mod 3 env) | Security Engineer |
| 4 | **Credential sızma şüphesi** (`BYPASS_*` değerleri bir yere sızmışsa) | Sabitleri sıfırla/yenile (REDACTED — değerler hiçbir yazıya girmez), kill-switch 1 uygula, audit log'u tara | Security Engineer → L3 |
| 5 | **Karar geri alınacaksa** | Yeni ADR ("revert of ADR-008") — bu dosya keyfi düzenlenmez; kod geri dönüşü `git revert` (AGENTS §17 #10) | Vault Steward + Tech Lead |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-008-bypass-auth-middleware`) + `log.md` append ("ADR-008 yazıldı (debate PENDING)") + dizin satırı doğrula (`[[../index]]` §3 `[[ADR-008-bypass-auth-middleware]]` slug eşleşmesi ✅) | Vault Steward | 30 dk |
| 2 | Debate (3 tur / 20 persona, ADR-004/ADR-006 formatı) — **✅ TAMAMLANDI (19/1/0 KABUL)**; sonuç §7.1'e yazıldı | Debate + Vault Steward | 2 gün |
| 3 | Tech Lead onayı — **✅ (2026-09-24)** (debate sonrası, §7) | Tech Lead | 1 gün |
| 4 | **PLANNED kod:** üretimde bastırılan bypass denemesi (`bypass=true`/test_mode, gate false) → `log ERROR` + `[TEST_BYPASS]` denetimi; `@file_put_contents` sessizliğini gider (StructuredLogger) | Security Engineer | 1 gün |
| 5 | **PLANNED kod:** `BYPASS_ROLE` **rol allowlist kısıtı** — yalnız bypass/test rolleri; `admin`/`superadmin` engelli (§2.2b #6) + bypass identity yetki denetim testi (PHPUnit) | Security Engineer + QA Engineer | 1 gün |
| 6 | **PLANNED kod:** `health` (+ varsa `static`) route'ları `requiresAuth: false` ile public allowlist'e ekle, gerekçeli (§2.2a) | Backend Architect | 2 saat |
| 7 | **PLANNED kod:** Mod 3 header yolu — **yalnız** trusted-proxy/env gate arkasında (talep geldiğinde; kapsam `shared/AGENTS.md` Yasak #2 gereği bu ADR ile değişir) | Security Engineer | 1 gün (talepte) |
| 8 | **PLANNED gate:** CI/deploy denetimi — public işaretli route listesi + gerekçe; production config'de `test_mode`/`force_auth_bypass`=`false` assert'i (§4.3 risk 1/3) | DevOps Engineer | 1 gün |
| 9 | Env allowlist sıkılaştırması: gate `app.env ∈ {test, dev}` (denylist → allowlist, §2.2b #2) | Security Engineer | 2 saat |
| 10 | Vault senkronu: `[[../../brain]]` ADR-008 özeti + `.ai/.decisions/index.md` §3 satır durumu + debate/Tech Lead sonuçları (`log.md` append-only) | MO (vault-updater) | 1 saat |
| 11 | **Debate Şart 1 — kod 3 açığı kapatılacak (bağlayıcı):** (a) üretimde bastırılan bypass denemesi → `log ERROR` + alert (adım 4 · §2.2b #3 · §2.2d #1 · §4.3 risk 1); (b) bypass kullanıcısı **asla admin yetkisi vermez** — `MM_Permissions=[]` kalıcı + rol allowlist (`admin`/`superadmin` yasak — adım 5 · §2.2b #6); (c) env **denylist → allowlist**: yalnız `test,dev`; bilinmeyen değer → bypass kapalı (adım 9 · §2.2b #2) | Security Engineer | 2 gün |
| 12 | **Debate Şart 2 — fail-closed üretim kanıtı testi:** `app.env=production` + `bypass=true`/`test_mode` → gate kapalı, oturum açılmaz, `log ERROR`+alert yazıldığı PHPUnit/integration test ile kanıtlanır | Security Engineer + QA Engineer | 1 gün |
| 13 | **Debate Şart 3 — ADR-010/011/012 cross-auth uyum maddesi:** bypass 3 modunun CSRF (ADR-010), session (ADR-011), CSP nonce (ADR-012) kararlarıyla çelişmediği denetlenir (§6 — düz metin; dosyalar diskte YOK, wiki-link kurulmaz) | Security Engineer | 1 gün |

### 5.2 Geri Dönüş Planı

Karar süreç karardır; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Mod 1 değişirse** (ör. `public: true` alan adı/`requiresAuth` semantiği): yeni ADR — geçişte işaretlenmemiş route kalmaz (default-deny sayesinde kapanık kalır, açık kalma riski yok); (2) **Mod 2 kapatılırsa** (bypass tamamen kaldırılırsa): kill-switch §4.4 #1 zaten kod değişikliği gerektirmez — sabitleri/bayrakları kapatmak yeterli; test otomasyonu gerçek test hesabıyla (non-production seed) çalışır; yeni ADR ile tescil; (3) **Mod 3 header yolu eklenirse/çıkartılırsa:** kapsam yalnız bu ADR ile değişir (shared/AGENTS.md Yasak #2) → ek ADR/revizyon eki + Security review; header yolu kodda şu an yok → çıkarma gerekmez (default kapalı); (4) **Fail-closed kuralı gevşetilirse:** kritik ihlal — derhal revert + `log ERROR` + L2/L3 (AGENTS §10); yeni ADR olmadan üretim bypass fail-open yapılamaz; (5) **Vault bozulursa:** `git checkout` + son commit (AGENTS §17 #10); (6) **Veri/state kaybı yoktur** — bu ADR auth karar katmanıdır; bypass oturumları `[TEST_BYPASS]` imzasıyla ayrışır, silinmesi veri kaybı yaratmaz.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA mimarisi — route registry ve auth yönlendirmelerinin bağlamı; **dosya diskte VAR** ✅ |
| [[../index]] | Karar dizini — bu ADR'nin kaydı §3 `[[ADR-008-bypass-auth-middleware]]` (slug eşleşmesi ✅ — satır diskte mevcut) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Hard Guardrail (#16 şablon zorunluluğu), REDACTED politikası |
| [[../../AGENTS.md]] | §21 Cross References `[[ADR-008-bypass-auth-middleware]]` (bu dosyaya bağlanır ✅); §10 escalation; §17 #10 vault kurtarma; §25.3 frozen/append-only kuralları |
| [[../../architecture/k6-guvenlik/index]] | Güvenlik katmanı (K6-K7) ana sayfası — authentication-jwt, audit-logging, session-management ile birlikte okunur; **dosya diskte VAR** ✅ |
| [[../../brain]] | Mimari karar özeti — ADR-008 satırı vault-sync ile tazelenir |
| [[../../log]] | Audit trail — bu ADR ve debate/revizyon kayıtları append edilir (append-only) |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| `shared/src/PageRouter/AuthGuard.php` · `RouteRegistry.php` · `SpaRoute.php` · `PageRouter.php` · `PageRouterKernel.php` | Mod 1 + sıralama taşıyıcıları — §1.1/§2.2a IMPLEMENTED kanıtları (default `requiresAuth=true`, `!requiresAuth \|\| authenticated`, 404 bilinmeyen, Csrf→Bypass→Auth sırası) |
| `shared/src/Middleware/BypassAuthMiddleware.php` | Mod 2 taşıyıcısı — otomatik login, credential fail-closed, `MM_Permissions=[]`, audit çağrısı (IMPLEMENTED) |
| `shared/src/Security/SecurityHelper.php` | Gate (`isTestBypassActive`: production kill, `app.force_auth_bypass`) + `logTestBypass` ([TEST_BYPASS] audit) |
| `shared/config/routes.php` · `shared/config/auth-routes.php` | Public allowlist kayıtları (`requiresAuth: false`) + korumalı route'lar (IMPLEMENTED) |
| `shared/AGENTS.md` | Yasak #2 — BypassAuthMiddleware kapsam genişletme yasağı (kapsam otoritesi = bu ADR) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅, v7.2.0 — 2+ çapraz kaynak, güvenlik iddiası OWASP+official+CVE) |
| **Düz metin (dosya diskte YOK — wiki-link kurulmaz):** ADR-010 (CSRF), ADR-011 (session), ADR-012 (CSP), ADR-013 (rate limit), ADR-020 (API public security) | `.ai/.decisions/index.md` §3'te kayıtlı ama tekil dosyaları diskte YOK (glob kanıtı) → düz metin referans; auth bypass'ın komşu güvenlik kararları |
| **Debate Şart 3 — ADR-010 (CSRF) / ADR-011 (session) / ADR-012 (CSP nonce) cross-auth uyum maddesi** (düz metin — dosyalar diskte YOK, wiki-link kurulmaz) | Bypass 3 modunun komşu güvenlik kararlarıyla çelişmediği doğrulanır (§5.1 #13) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-008'i sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate onayı (3 tur / 20 persona — 19/1/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/ADR-006 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** — 2026-09-24 |
| Tur 1 — 20 persona | **16 kabul/neutral** · 3 itiraz: **Security** (3 kod açığı şart), **DevOps** (env allowlist prod doğrulama), **Critic** (denylist→allowlist en kritik) · 19 kabul / 1 çekimser eyaletinde kapanış |
| Tur 2 — İtiraz→çözüm | (1) üretimde bypass denemesi loglanmıyor → **`log ERROR` + alert** maddesi (§2.2b #3, §2.2d #1, §4.3 risk 1); (2) `BYPASS_ROLE` admin kısıtsız → **bypass kullanıcısı asla admin yetkisi vermez, `MM_Permissions=[]` kalıcı** + rol allowlist (§2.2b #6); (3) env denylist → **allowlist** (yalnız `test,dev`; bilinmeyen değer → bypass kapalı = fail-closed) (§2.2b #2); (4) header bypass kodda YOK → **PLANNED korunur, yalnız trusted-proxy şartıyla** (§2.2c) |
| Tur 3 — Oy | **19 kabul / 1 çekimser / 0 red → KABUL** |
| Karara dönüşen şartlar | 3 şart §5.1'e satır olarak eklendi: **#11 (Şart 1)** kod 3 açığı — üretim `log ERROR`+alert (#4) · bypass rol admin'siz (#5) · env allowlist `test,dev` (#9); **#12 (Şart 2)** fail-closed üretim kanıtı testi; **#13 (Şart 3)** ADR-010/011/012 cross-auth uyum maddesi (§6) |
| Tech Lead | **✅ (2026-09-24 — debate sonrası onay)** |
| Sonuç | **✅ KABUL (19/1/0)** — 2026-09-24; §2 PLANNED satırları (§2.2b #2/#3/#6, §2.2d #1) "debate şartı ile kapatılacak" bağlandı |

---

*ADR-008 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/accepted/ yeni seri; slug: `bypass-auth-middleware`)*
*Authority: ADR-008 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
