---
title: "CoreMusic — ADR-004: Multi-Domain SPA Mimarisi"
type: adr
category: architecture
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-004 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-004: Multi-Domain SPA Mimarisi

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "sen karar") + mimar kararı · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-001-vanilla-js-itcss]] (frontend temeli — tek build, framework yasak; bu kararın bağlayıcı ön koşulu) · [[ADR-003-multi-db-bcnf]] (domain kavramının veri ayağı) · [[ADR-081-multi-provider-data-sync]] (domain'ler arası veri akışı) · karar dizini [[../index]] §3/§4 · Eski seri kayıtları düz metin: ADR-009 (clean URL redirect), ADR-021 (SPA router immutable contract), ADR-044 (dynamic theme engine), ADR-045 (multi-domain view mode), ADR-046 (cross-view state preservation), ADR-083 (SPA router), ADR-085 (modular composer packages) — `(henüz yazılmadı — vault: brain.md)`; eski numaralara wiki-link (ADR-0xx biçimi, dosyası olmayanlara) KURULMAZ.

---

## 1. Bağlam (Context)

CoreMusic, `shared/config/domain.php` ile tanımlı **7 subdomain** (auth, home, assets, music, admin, media, api) + primary `coremusic.net` üzerine kurulu çok-origindir. Her domain'in kendi route ağacı, teması ve görünüm modu vardır; buna karşılık frontend ADR-001 ile **tek vanilla JS + ITCSS** yığınına, tek build'e bağlanmıştır. Bu karar, **Multi-Domain SPA mimarisini** tanımlar: tek SPA çekirdeği mi, domain başına ayrı bundle mı; yönlendirme (routing) kimin işi — sunucunun mu istemcinin mi; router kodu nerede yaşar ve hangi güvenlik sınırları multi-domain SPA'da zorunludur. Karar üç bağımsız baskıyı aynı anda karşılamalıdır: (1) SEO/ilk-boyama (first paint) için sunucu tarafı HTML, (2) ADR-001 ile kütüphane/framework yasağı ve tek build disiplini, (3) çok origin'in güvenlik yüzeyi (origin izolasyonu, path/redirect enjeksiyonu, frame/iletişim saldırıları).

### 1.1 Mevcut Durum

- **Sunucu router (disk kanıtı — IMPLEMENTED):** `shared/src/PageRouter/` altında 14 dosya: `PageRouter.php`, `PageRouterKernel.php`, `RouteRegistry.php`, `RouteResult.php`, `SpaRoute.php`, `RequestNormalizer.php`, `ResponseEmitter.php`, `HtmlShellRenderer.php`, `AuthGuard.php`, `AuthUrlBuilder.php`, `ErrorHandler.php`, `StructuredLogger.php`, `PageRouterHelper.php`, `SessionInitializer.php` + `templates/`. Kendi `CLAUDE.md`'si "SPA sayfa router çekirdeği (ADR-083/021) … Server-side hybrid rendering'in merkezi" der.
- **İstemci router (disk kanıtı — IMPLEMENTED):** `assets.coremusic.net/js/router/` altında 28 dosya (kendi `CLAUDE.md` sayımı): `Router.js`, `NavigationOrchestrator.js`, `HistoryManager.js`, `RouterEventManager.js`, `ContentFetcher.js`, `DomPatcher.js`, `GuardPipeline.js`, `CsrfSyncManager.js`, `AuthBoundaryDetector.js`, `ScrollRestorer.js`, `PrefetchManager.js`, `UrlUtils.js`, `guards.js`, `config/*` … Saf History API kullanımı diskte: `history.pushState` (`HistoryManager.js` L24, `NavigationOrchestrator.js` L65), `popstate` dinleyicisi (`RouterEventManager.js` L10, `main.js` L142) — hiçbirinde framework import'u yok (ADR-001 uyumu).
- **Composer/PSR-4 (disk kanıtı — IMPLEMENTED):** `shared/composer.json` → `"name": "coremusic/shared-infrastructure"`, PSR-4 `"CoreMusic\\": "src/"`, tip `library` — hybrid tek `shared/` yapısı (ADR-085 konsepti) fiziksel olarak diskte.
- **Domain + görünüm + tema (disk kanıtı — IMPLEMENTED):** `shared/config/domain.php` (7 subdomain + primary + portlar), `shared/src/Config/DomainConfig.php`, `shared/src/ViewMode/ViewModeManager.php` (ADR-045 konsepti), `shared/src/Theme/ThemeManager.php` (ADR-044 konsepti), `assets.coremusic.net/Css/09_ViewModes/` (v-home, v-pro, v-studio, v-car).
- **Güvenlik ortak katmanı (disk kanıtı — IMPLEMENTED):** `shared/src/Middleware/SecurityHeadersMiddleware.php` (L28 `X-Frame-Options: DENY`, L69 `frame-ancestors 'none'`, CSP nonce L58), `shared/src/Middleware/OriginCheckMiddleware.php` (origin whitelist), `CorsMiddleware.php` (frozen sıra: OriginCheck → Cors → …).
- **Kapsam dışı/mimari değil:** Domain'ler arası veri senkronu → [[ADR-081-multi-provider-data-sync]]; DB bölmesi → [[ADR-003-multi-db-bcnf]].

### 1.2 Sorun Tanımı

(1) **Tek çekirdek mi, domain başına ayrı bundle mı?** Ayrı bundle; build, cache-busting ve kod tekrarı maliyeti getirir ve ADR-001'in tek vanilla yığını ile R-004 (webpack — "over-engineering", [[../index]] §5) reddiyle çelişir. Tek çekirdek seçilirse domain'ler arası farklılık **route + konfigürasyon** olarak nasıl ifade edilir? (2) **İkili router sorumluluğu:** İlk yüklemede (cold load, deep link, bot isteği) HTML'i kim üretir — sunucu mu, istemci mi? Navigasyon sonrası DOM'u kim değiştirir? Sınır yazılmazsa iki router aynı route'u farklı yorumlar ve adres çubuğu iki farklı SSOT'a bölünür. (3) **Router nerede yaşar:** İstemci router `assets.coremusic.net/js/router/` içinde ama her web projesi tarafından kurulabilir olmalı — CoreMusic'e kilitli olmayan taşınabilirlik ADR-085'in (hybrid tek shared/ + PSR-4) konseptidir, kararı bu ADR'de tescil edilir. (4) **Güvenlik:** History API ile istemci tarafında üretilen yollar (path) ve yönlendirmeler (redirect/back) doğrulanmazsa open redirect ve open-redirect-ötesi (SSRF/OAuth token sızıntısı zinciri) kapıları açılır; `postMessage('*')` wildcard'ı ve framing (clickjacking) multi-origin'de doğrudan tehlikedir.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — ⚠️ VERIFICATION REQUIRED: `references/` dizini diskte mevcut, dosya adı glob ile doğrulanamadı) — resmi/birincil kaynak önce (MDN, OWASP, web.dev, AWS), her iddiaya kaynak, 2+ bağımsız çapraz doğrulama. **Odak KAPSAM İKİSİ: (a) SPA mimarisi + SEO/SSR trade-off 2025-2026 + subdomain routing; (b) GÜVENLİK: origin izolasyonu, History API/path enjeksiyonu, postMessage/CORS, clickjacking.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "SPA SEO SSR trade-offs 2025 2026 server-side vs client-side rendering crawlability" · (2) "subdomain routing multi-tenant origin isolation SPA cookie security" · (3) "History API pushState path injection security risk open redirect same-origin validation" · (4) "postMessage CORS origin validation security best practices iframe targetOrigin wildcard" · (5) "clickjacking prevention CSP frame-ancestors X-Frame-Options OWASP 2025" · (6) "SPA deep link browser back button popstate best practices History API" |
| Web Search **Konusu** | SPA'da CSR/SSR/hibrit render dengelemesi ve SEO/AI-crawler etkisi (2025-26); subdomain-tabanlı çok-origind izolasyon ve cookie/origin sınırları; History API ile üretilen path/redirect'lerin enjeksiyon-open-redirect riski; postMessage targetOrigin wildcard'ının token sızıntısı vakası; clickjacking savunmasında frame-ancestors vs X-Frame-Options katmanı; popstate/replaceState ile SPA geçmiş yönetimi ve deep-link edge case'leri. |
| Web Search **Bağlam** | 2026 tarihli render-verdict derlemeleri (Jasmine Directory 2026 verdict, Strapi 2025 rehberi, Screpy 2026 SEO, Magnolia "To SSR or not to SSR"), AWS tenant-routing dokümanı (2025), multi-tenant Next.js/cookie SameSite incelemeleri (Kavanagh, Ramgattie), MDN History API + web.dev Navigation API (Baseline 2025) + SPA routing history zorlukları yazısı, OWASP Clickjacking Cheat Sheet + MDN/centralcsp frame-ancestors karşılaştırması, Microsoft MSRC Ağustos 2025 postMessage vakası + SecureIdeas/CyberCX/Intigriti postMessage hardening, Invicti/Snyk/BrightSec open-redirect rehberleri. |
| Web Search **Kısa Açıklama** | SSR/CSR 2026'da "kazanan yok": SEO-kritik/halka açık route'lar sunucu-rendered HTML ister, oturumlu etkileşim route'ları CSR için yeterlidir — hibrit çoğunluk. Subdomain = tarayıcıda ayrı origin (yalnız cookie scope'u ile izole SANILIR; oturum/üye doğrulaması sunucuda yine zorunlu); SameSite+subdomain CSRF tuzağı gerçek. History API path'leri doğrulanmazsa open redirect (OWASP A03) ve OAuth token sızıntısı zinciri doğar. postMessage'da wildcard `'*'` yasak (MSRC 2025 vakası: access token sızdı). Clickjacking: `frame-ancestors` birincil, `X-Frame-Options` legacy fallback, ikisi birlikte gönderilir. SPA geçmişinde `popstate` programatik `pushState`/`replaceState` çağrısında tetiklenmez ve ilk giriş state'sizdir → `replaceState` ile initial state yazmak şart. |
| Web Search **Uzun Açıklama** | **(a) SPA/SEO/SSR 2025-26:** Jasmine Directory "2026 Verdict": evrensel kazanan yok — içerik-ağır, SEO-kritik route'lar SSR; karmaşık etkileşimli route'lar CSR; hibrit mimariler çoğunluğu besler; "derecelendirilecek her sayfayı SSR yap" kuralı. Strapi: SSR = indexlenebilir tam HTML + hızlı TTFB, CSR = CDN/statik basitlik ama ilk HTML boş. Screpy (2026): yalnız Google değil — AI content fetcher'ları ve denetim botları ham HTML'i tercih eder; CSR siteler kullanıcıya görünür ama botlara "boş" görünür; SEO-kritik route'lar SSR/SSG/ISR'a taşınır, dinamik render köprüdür. Magnolia: SSG bayatlayabilir, SSR backend ister, CSR basit ama ilk yük yavaş — "SSR'i gerçekten SEO/freshness ihtiyacı olmadan ekleme". **Subdomain/origin:** AWS tenant-routing — wildcard subdomain DNS bir kez kurulur, tenant header/cookie/request'ten çıkarılır; domain-driven vs data-driven routing ayrımı. Kavanagh: subdomain tarayıcıya ayrı origin verir **ama sunucu tarafı oturum/tenant üyeliğini otomatik izole etmez** — uygulama yine doğrular; cookie'ler subdomain'ler arası paylaşımda explicit scope şart. Ramgattie: `SameSite=Lax` + subdomain'lerde cookie scope dışında kalan alt-domain'den dahi CSRF POST başarılı olabildi (gerçekleştirilmiş test). **(b) GÜVENLİK — History API/path:** Invicti: open redirect = doğrulanmış kullanıcı girdisiyle hedefe yönlendirme; phishing'e geçit + SSRF kapısı; OWASP Unvalidated Redirects cheat sheet'i esas. Snyk: open redirect A03:2021 Injection kapsamındadır; regex yerine göreli path + whitelist doğrulaması önerilir (`url.startsWith('/')` + hedef whitelist). BrightSec: open redirect OAuth/SSO akışlarında token hijack zincirine girer; basit parametre filtresi yetmez, doğru URL parse şart. **postMessage/CORS:** MSRC (Ağu 2025): gerçek olay — `postMessage(message, '*')` ile Azure Marketplace iframe'ine access token + clusterUrl sızdı; mitigasyon = tam trusted origin. CyberCX: alıcıda `event.origin` doğrulaması + wildcard yasak; gönderici/alan tarafı ikisi de. SecureIdeas: targetOrigin'i her zaman tam yaz, gelen mesajı sanitize et, CSP/CORS ile hizala. Intigriti: `'*'` genelde test amaçlı başlar, OAuth callback token sızıntısı klasik istismar örneğidir. **Clickjacking:** OWASP Cheat Sheet: üç bağımsız mekanizma (X-Frame-Options/CSP `frame-ancestors` başlıkları, SameSite cookie, frame-buster JS) ve mümkünse birden fazlası — savunma derinliği. centralcsp: `frame-ancestors` modern ve esnek (origin listesi + report-only), `X-Frame-Options ALLOW-FROM` deprecated; CSP spec'i `frame-ancestors` uygulayan tarayıcıya `X-Frame-Options`'ı yok saymayı zorunlu kılar → ikisi çatışmaz, ikisi birden gönderilir (legacy IE için XFO). ZeriFlow/Barry: `DENY` + `frame-ancestors 'none'` en yüksek-ROI iki başlık. **SPA geçmiş/deep-link:** MDN: SPA'da ilk sayfa yükü history entry'sine state yazılmadığı için `replaceState` ile initial state zorunlu; `popstate` back/forward'da state'i geri yükler. web.dev (Navigation API, Baseline 2025): History API "onlarca yıldır SPA router'ın temeli ama tasarım olarak SPA için değildi" — global `<a>` click yakalama, manuel `pushState`, manuel DOM, ayrı `popstate` … bir edge case unutulursa kullanıcı yanlış görürümde kalır. yiou.me: `popstate`'te back mi forward mı ayrımı yapılamaz, programatik push/replace'te `popstate` TETİKLENMEZ, anchor hash tıklaması bile `popstate` üretir → history stack yönetimi kırılgandır ve sınır/sorumluluk yazmak şart. |
| Web Search **Paragraf Veri Uzun** | SSR 2026 verdict: evrensel kazanan yok · SEO-kritik route = SSR/SSG/ISR, oturumlu etkileşim = CSR · CSR HTML botlara/AI-crawler'lara boş görünür · SSR = tam HTML + hızlı ilk boyama, CSR = basit CDN ama yavaş ilk yük · Hibrit çoğunluk · Subdomain = ayrı origin ama sunucu oturumu otomatik izole etmez · Tenant doğrulama sunucuda zorunlu · Cookie subdomain scope'u explicit yazılır · SameSite=Lax subdomain CSRF test edilmiş (Ramgattie) · AWS: domain-driven vs data-driven tenant routing · Open redirect = A03 Injection · Open redirect → phishing + SSRF + OAuth token hijack · Doğrulama: göreli path + whitelist + doğru URL parse (regex yetmez) · postMessage `'*'` = token sızıntısı (MSRC Ağu 2025 gerçek vaka) · Alıcıda `event.origin` doğrulaması zorunlu · Hedefte tam `targetOrigin` yazılır · Gelen mesaj sanitize edilir · Clickjacking: `frame-ancestors 'none'` birincil + `X-Frame-Options: DENY` legacy · CSP spec: frame-ancestors varsa XFO yok sayılır (çatışma yok) · SameSite + frame-buster = savunma derinliği · History API SPA için tasarlanmadı (web.dev) · `popstate` programatik push/replace'te tetiklenmez · İlk entry state'siz → `replaceState` şart · popstate back/forward ayrımı yapılamaz · Anchor tıklaması popstate üretir · İstemci router edge-case unutulursa yanlış görünüm. |
| Web Search **Sonucu** | 1) **Hibrit render kararı desteklendi:** ilk yük + SEO-kritik route sunucu-rendered HTML (PageRouter HtmlShell), sonrası istemci navigasyonu — literatür 2026'da bunu "hibrit çoğunluk" der (kaynak: Jasmine Directory, Strapi, Screpy, Magnolia — 4 kaynak). 2) **Subdomain origin izolasyonu tek başına yeterli değil:** origin ayrımı tarayıcıda var, oturum/tenant doğrulaması sunucuda zorunlu + cookie scope explicit (kaynak: AWS, Kavanagh, Ramgattie — 3 kaynak) → AuthGuard/OriginCheck ile kararlaştırılır. 3) **History API path/redirect enjeksiyonu gerçek risk:** open redirect A03 kapsamındadır, OAuth zincirine girer; göreli-path + whitelist doğrulaması şart (kaynak: Invicti, Snyk, BrightSec — 3 kaynak) → ReturnUrlPolicy/AuthGuard'a bağlanır. 4) **postMessage wildcard yasak, frame başlıkları zorunlu:** MSRC 2025 token sızıntısı + OWASP clickjacking katmanı (kaynak: MSRC, CyberCX, SecureIdeas, Intigriti, OWASP, centralcsp — 6 kaynak) → mevcut SecurityHeadersMiddleware (`frame-ancestors 'none'` + `X-Frame-Options: DENY`) ile diskte zaten karşılanır, karar bunu multi-domain SPA'ya şart koşar. 5) **Saf History API yeterli ama kırılgan:** `replaceState` initial-state, `popstate` edge case'leri ve back/forward ayrımı yazılı sınır ister (kaynak: MDN, web.dev, yiou.me — 3 kaynak) → istemci router'ın HistoryManager/RouterEventManager sorumluluğu tescillenir. 6) **Çapraz doğrulama:** her iddia ≥2 bağımsız kaynakla örtüştü (render 4, origin 3, redirect 3, postMessage+clickjacking 6, history 3). |
| Web Search **Alınan Karar** | **Multi-Domain SPA mimarisi kabul edilir:** (i) **Tek SPA çekirdeği** — domain'ler origin/route bazlı **görünüm + konfigürasyon** değiştirir, ayrı bundle YOK (ADR-001 tek build ile uyumlu; R-004 webpack reddi korunur); her domain kendi route ağacını ve temasını tanımlar, aynı uygulama kabuğu çalışır. (ii) **İstemci router, `shared/` altındaki Composer paketinde yaşar** (PSR-4 namespace, hybrid tek `shared/` yapısı — ADR-085 konsepti); **CoreMusic'e özel değil, herhangi bir web projesi Composer ile kurabilir.** (iii) **İkili router sorumluluğu:** (1) Sunucu `PageRouter` (PHP, `shared/src/PageRouter/`) — temiz URL, ilk yük, fallback, auth korumalı rotalar → server-rendered first paint; (2) İstemci SPA router (History API, saf — kütüphane yok, ADR-001) — client-side navigasyon. Sınır: **sunucu bilir → yönlendirir; istemci bilir → değiştirir.** Adres çubuğu her iki dünya için SSOT. (iv) **Güvenlik kapıları şart:** path/redirect yalnız göreli + whitelist (ReturnUrlPolicy/AuthGuard), `postMessage` tam origin + `event.origin` doğrulaması, `frame-ancestors 'none'` + `X-Frame-Options: DENY` (SecurityHeadersMiddleware — mevcut), cookie scope explicit. |
| Web Search **Sonuç** | Karar 2026 verisiyle **desteklendi**: SPA/SEO/SSR hibrit (4 kaynak), subdomain/origin izolasyonu sınırları (3 kaynak), open-redirect/path enjeksiyonu (3 kaynak), postMessage wildcard + clickjacking (6 kaynak), History API kırılganlığı (3 kaynak) — toplam **19 kaynak**, 6 sorgu, çapraz doğrulama tam. Kaynaksız iddia yok; tek ⚠️: araştırma protokol dosyasının tam adı glob ile doğrulanamadı (`references/` dizini var) → `⚠️ VERIFICATION REQUIRED`. |

**Kaynak listesi (19):**
1. https://www.jasminedirectory.com/blog/server-side-rendering-ssr-vs-client-side-the-2026-verdict — SSR vs CSR "2026 Verdict": evrensel kazanan yok, hibrit çoğunluk, SEO-kritik sayfa SSR
2. https://strapi.io/blog/client-side-rendering-vs-server-side-rendering — SSR: indexlenebilir tam HTML + hızlı TTFB; CSR: CDN basitliği + ilk HTML boş
3. https://screpy.com/blog/server-side-rendering-vs-client-side-rendering-for-seo — 2026'da yalnız Google değil; AI crawler'lar ham HTML ister; SEO-kritik route → SSR/SSG/ISR
4. https://www.magnolia-cms.com/blog/to-ssr-or-not-to-ssr-the-developer-s-guide-to-rendering-in-the-age-of-ai.html — SSG bayatlar / SSR backend ister / CSR ilk yük yavaş; gereksiz SSR ekleme
5. https://aws.amazon.com/blogs/networking-and-content-delivery/tenant-routing-strategies-for-saas-applications-on-aws — wildcard subdomain, domain-driven vs data-driven tenant routing
6. https://johnkavanagh.co.uk/articles/building-a-multi-tenant-application-with-next-js — subdomain = ayrı origin; sunucu oturumu otomatik izole etmez; cookie scope explicit
7. https://medium.com/@rramgattie/samesite-and-subdomains-08870bbdd62c — SameSite=Lax + subdomain'den CSRF POST gerçekleştirmesi
8. https://developer.mozilla.org/en-US/docs/Web/API/History_API/Working_with_the_History_API — ilk entry state'siz → `replaceState`; `popstate` back/forward state'i
9. https://web.dev/blog/baseline-navigation-api — History API SPA için tasarlanmadı; global click + manuel pushState + manuel DOM + popstate; edge case → yanlış görünüm
10. https://yiou.me/blog/posts/spa-routing — `popstate` back/forward ayrımı yapılamaz; programatik push/replace'te tetiklenmez; anchor tıklaması popstate üretir
11. https://www.invicti.com/blog/web-security/open-redirect-vulnerabilities-invicti-pauls-security-weekly — open redirect: doğrulanmış girdi → phishing + SSRF geçidi
12. https://learn.snyk.io/lesson/open-redirect — open redirect A03:2021 Injection; göreli path + whitelist doğrulaması (regex yetmez)
13. https://brightsec.com/blog/open-redirect-vulnerabilities — open redirect OAuth/SSO token hijack zinciri; doğru URL parse şart
14. https://www.microsoft.com/en-us/msrc/blog/2025/08/postmessaged-and-compromised — MSRC Ağu 2025: `postMessage('*')` wildcard ile access token sızıntısı + mitigasyon
15. https://cybercx.com.au/blog/post-message-vulnerabilities — `event.origin` doğrulaması zorunlu; wildcard hedef origin yasak (gönderici + alıcı)
16. https://www.secureideas.com/blog/being-safe-and-secure-with-cross-origin-messaging — tam targetOrigin, gelen mesaj sanitize, CSP/CORS hizası
17. https://www.intigriti.com/researchers/blog/hacking-tools/exploiting-postmessage-vulnerabilities — `'*'` test'ten gerçeğe; OAuth callback token sızıntısı klasik istismar
18. https://cheatsheetseries.owasp.org/cheatsheets/Clickjacking_Defense_Cheat_Sheet.html — üç bağımsız mekanizma (header + SameSite + frame-buster), savunma derinliği
19. https://centralcsp.com/en/blog/x-frame-options-vs-frame-ancestors — `frame-ancestors` modern; CSP spec'i XFO'yu yok saymaya zorlar; ikisi birden gönderilir (legacy için)

*(Ek çapraz kaynaklar — 19 çekirdek dışı: ZeriFlow/Barry clickjacking rehberleri, MDN X-Frame-Options — ana iddialar 19'luk çekirdek listesinde kaynaklıdır.)*

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Tek build / framework yasaki (ADR-001) | Vanilla JS ES6+, ITCSS; ayrı domain bundle'ı, webpack/vite/build sistemi, React/Vue/jQuery YASAK ([[ADR-001-vanilla-js-itcss]]; R-004 webpack reddi — [[../index]] §5). Domain farklılığı yalnız route + konfigürasyon + tema/görünüm katmanında ifade edilir. |
| Kütüphane bağımlılığı yasağı (istemci router) | İstemci SPA router saf History API kullanır (`pushState`/`popstate`/`replaceState`); router kütüphanesi (React Router, Vue Router, Navigo vb.) YASAK. Mevcut 28 dosyalık çekirdek `assets.coremusic.net/js/router/` kanıtıdır. |
| Taşınabilirlik (ADR-085 hybrid) | Router, CoreMusic'e özel olmayacak: `shared/` altındaki Composer paketinde PSR-4 namespace ile yaşar; herhangi bir web projesi `composer require` ile kurabilir. Uygulamaya özel route'lar paketin içine gömülmez (paket = mekanizma, route = uygulama). |
| İki router sınırı | Sunucu (`PageRouter`) ve istemci (SPA router) aynı sorumluluğu paylaşmaz: sunucu temiz URL/ilk yük/fallback/auth rotasını **yönlendirir** ve server-rendered HTML üretir; istemci mevcut DOM'da navigasyonu **değiştirir**. Çift yorumlama (aynı route iki farklı anlamda) yasaktır. |
| Adres çubuğu = SSOT | `location` her iki dünyanın da tek doğruluk kaynağıdır; istemci router URL'siz (yalnız DOM) gezinmez, sunucu istemci history'sine dokunmaz. |
| Güvenlik kapıları (çok origin) | Yalnız göreli path + whitelist yönlendirme (ReturnUrlPolicy/AuthGuard hattı), `postMessage` tam origin + `event.origin` doğrulaması, `frame-ancestors 'none'` + `X-Frame-Options: DENY` (SecurityHeadersMiddleware — diskte IMPLEMENTED), origin whitelist (OriginCheckMiddleware), cookie scope explicit — §1.3 kaynak 5-19. |
| Şablon zorunluluğu | Yeni route/görünüm dosyaları Guardrail #16 ile ilgili şablondan türetilir; bu ADR kod üretmez, yalnız neden-sonuç zincirini belgeler. |

---

## 2. Karar (Decision)

**CoreMusic, Multi-Domain SPA mimarisi kullanır: tek SPA çekirdeği; domain'ler origin/route bazlı görünüm + konfigürasyon değiştirir — ayrı bundle YOK.** Her domain kendi route ağacını ve temasını tanımlar; aynı uygulama kabuğu (app shell) çalışır. **İstemci SPA router `shared/` altındaki Composer paketinde yaşar** (PSR-4 namespace, hybrid tek `shared/` yapısı — ADR-085 konsepti) ve **CoreMusic'e özel değildir; herhangi bir web projesi Composer ile kurabilir.** Yönlendirme **ikili sorumlulukla** yürütülür: **(1) Sunucu: `PageRouter`** (PHP, `shared/src/PageRouter/`) — temiz URL, ilk yük, fallback, auth korumalı rotalar → server-rendered first paint; **(2) İstemci: SPA router** (History API, saf — kütüphane yok, ADR-001 uyumu) — client-side navigasyon. **Sınır: sunucu bilir → yönlendirir; istemci bilir → değiştirir. Adres çubuğu her iki dünya için SSOT'tur.**

### 2.1 Neden Bu Seçenek?

1. **Tek build disiplini korunur:** Ayrı bundle; cache-busting, kod tekrarı ve build zinciri maliyeti ADR-001'in tek vanilla yığını ile R-004 (webpack — over-engineering) reddine aykırıdır. Route + konfigürasyon ile domain farklılığı, bundle ile değil (§1.3 Sonucu madde 1; R-004 — [[../index]] §5).
2. **SEO/ilk yük garanti altına alınır:** 2026 literatürü "hibrit çoğunluk" der — SEO-kritik/halka açık route sunucu-rendered HTML ister, oturumlu etkileşim CSR için yeterlidir (§1.3 kaynak 1-4). `PageRouter::HtmlShellRenderer` diskte bu ilk boyamayı üretir (IMPLEMENTED).
3. **Sorumluluk sınırı yazıldı → çift anlamsız route engellenir:** web.dev/MDN/yiou.me üçü History API'nin kırılganlığını (popstate edge case'leri, initial state) gösterir (§1.3 kaynak 8-10); sınır yazılmazsa iki router aynı URL'i farklı yorumlar ve adres çubuğu SSOT'luğunu kaybeder.
4. **Taşınabilirlik + paylaşımlı altyapı:** Router Composer/PSR-4 paketi olarak `shared/` içinde yaşar → her web projesi kurabilir; sunucu tarafı zaten `shared/composer.json` (PSR-4 `CoreMusic\`) ile hybrid yapıda (ADR-085 — düz metin, henüz yazılmadı).
5. **Güvenlik kapıları karara gömülür:** çok origin = çok yüzey; path/redirect whitelist'i, postMessage tam-origin, frame başlıkları §1.4'te kısıt olarak karara bağlanır — mevcut `SecurityHeadersMiddleware` (`frame-ancestors 'none'` + `X-Frame-Options: DENY`) ve `OriginCheckMiddleware` diskte zaten IMPLEMENTED.

### 2.2 Teknik Detaylar

**(a) Mimari çizim (akış):**

```
[İstek] → origin (domain.php: auth/home/assets/music/admin/media/api)
   │
   ├─ İlk yük / deep-link / bot / auth rotası
   │    → PageRouter (shared/src/PageRouter/) — RequestNormalizer → RouteRegistry(SpaRoute)
   │      → AuthGuard (auth korumalı) → MiddlewareResolver → HtmlShellRenderer (app shell + domain konfig)
   │      → ResponseEmitter → server-rendered first paint (SSOT: location)
   │
   └─ Sonrası tüm navigasyon (aynı kabuk içinde)
        → SPA router (History API, saf) — guard → ContentFetcher → DomPatcher
          → history.pushState / popstate (HistoryManager, RouterEventManager)
          → domain route ağacı + tema/görünüm konfig'i ile render (SSOT: location)
```

**(b) İkili router sorumluluk tablosu (sınır sözleşmesi):**

| Soru | Sunucu — `PageRouter` (PHP) | İstemci — SPA router (vanilla JS) |
|------|-----------------------------|-----------------------------------|
| Ne iş yapar? | **Yönlendirir:** temiz URL çözümü, ilk yük (cold load), 404/fallback, auth korumalı rota erişim kontrolü | **Değiştirir:** mevcut DOM'da navigasyon, görünüm/görünüm-modu geçişi, scroll/focus geri yükleme |
| Kod nerede? | `shared/src/PageRouter/` (14 dosya — IMPLEMENTED) | `assets.coremusic.net/js/router/` (28 dosya — IMPLEMENTED); taşınabilir çekirdek `shared/` Composer paketine (§5.1 #2) |
| Tarihçe/durum | Yok — history istemcinin tekelinde | `history.pushState` + `popstate` + initial `replaceState` (HistoryManager/RouterEventManager — IMPLEMENTED) |
| HTML | Server-rendered first paint üretir (`HtmlShellRenderer`) | Üretmez; patch'ler (`DomPatcher`/`ContentPatcher`) |
| Auth | `AuthGuard` + `AuthUrlBuilder` — engeller/yönlendirir (`shared/src/PageRouter/AuthGuard.php`) | Yorum yapmaz; 401/oturum sınırını algılar (`AuthBoundaryDetector.js`) ve sunucuya döner |
| SSOT | `location` (adres çubuğu) | `location` (adres çubuğu) |

**(c) Domain → görünüm + konfigürasyon (ayrı bundle YOK):** her domain tanımlar: (1) route ağacı (hangi path hangi view), (2) tema/görünüm modu (`ThemeManager.php` + `ViewModeManager.php` — ADR-044/045 konseptleri, IMPLEMENTED; `Css/09_ViewModes/` v-home/v-pro/v-studio/v-car), (3) domain konfig'i (`shared/config/domain.php` + `DomainConfig.php` — IMPLEMENTED). Uygulama kabuğu (app shell, router, guard, CSRF, EventBus) tüm domain'lerde **aynıdır**.

**(d) Composer/taşınabilirlik (ADR-085 konsepti):** istemci router paketi `shared/` altında, PSR-4 namespace ile; `shared/composer.json` (tip `library`, `"CoreMusic\\": "src/"`) mevcut paket altyapısıdır (IMPLEMENTED). Paket = router mekanizması (matcher, guard pipeline, history, patcher); route tanımları ve domain konfig'i uygulamanın işidir — pakete gömülmez.

**(e) Kod kanıtı tablosu:**

| İddia | Etiket | Disk kanıtı (dosya yolu) |
|-------|--------|--------------------------|
| Sunucu router (PageRouter) | **IMPLEMENTED** | `shared/src/PageRouter/` — `PageRouter.php`, `PageRouterKernel.php`, `RouteRegistry.php`, `SpaRoute.php`, `HtmlShellRenderer.php`, `AuthGuard.php`, `RequestNormalizer.php`, `ResponseEmitter.php`, `ErrorHandler.php` + `CLAUDE.md` (14 dosya) |
| İstemci SPA router (saf History API) | **IMPLEMENTED** | `assets.coremusic.net/js/router/` — `Router.js`, `NavigationOrchestrator.js`, `HistoryManager.js` (L24 `pushState`), `RouterEventManager.js` (L10 `popstate`), `GuardPipeline.js`, `CsrfSyncManager.js`, `AuthBoundaryDetector.js`, `DomPatcher.js`, `config/*` (28 dosya) |
| Composer / PSR-4 / hybrid tek shared/ | **IMPLEMENTED** | `shared/composer.json` (`psr-4: CoreMusic\ → src/`, tip `library`), `shared/src/` alt yapısı (ADR-085 konsepti) |
| Domain haritası (multi-origin) | **IMPLEMENTED** | `shared/config/domain.php` (7 subdomain + primary), `shared/src/Config/DomainConfig.php` |
| Tema + görünüm modu (domain konfig'i) | **IMPLEMENTED** | `shared/src/Theme/ThemeManager.php`, `shared/src/ViewMode/ViewModeManager.php`, `assets.coremusic.net/Css/09_ViewModes/` (v-home, v-pro, v-studio, v-car) — ADR-044/045 konseptleri |
| Güvenlik başlıkları (clickjacking) | **IMPLEMENTED** | `shared/src/Middleware/SecurityHeadersMiddleware.php` (L28 `X-Frame-Options: DENY`, L69 `frame-ancestors 'none'`, CSP nonce), `OriginCheckMiddleware.php` (whitelist), `CorsMiddleware.php` |
| ui-design konseptleri (ADR-045/046) | **IMPLEMENTED (doküman)** | `.ai/ui-design/05-responsive-architecture.md` §09_ViewModes (v-home/v-pro/v-studio/v-car), `.ai/ui-design/01-mockup-index.md` |
| İstemci router'ın `shared/` Composer paketine taşınması | **PLANNED** | Paket dosyası diskte YOK — `shared/src/` altında router JS paketi henüz yok (taşıma adımı: §5.1 #2) |
| Domain route ağacı konfig dosyası (domain başına route listesi) | **PLANNED** | Parçalı: `js/router/config/auth-routes` var; 7 domain'in eksiksiz route ağacı konfig dosyası diskte YOK → §5.1 #3 |
| Paketin üçüncü taraf web projesinde kurulumu (composer require kanıtı) | **PLANNED** | Harici kullanım örneği/CI kanıtı diskte YOK → §5.1 #4 |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Domain başına ayrı bundle (ayrı build + domain-bazlı micro-frontend paketleri)** | Domain'ler tam bağımsız deploy; hata izolasyonu bundle seviyesinde | Her domain için ayrı build + cache-busting + kod tekrarı; ADR-001'in tek vanilla yığınıyla çelişir; R-004 (webpack — over-engineering) reddi bu alternatifi doğrudan yasaklar; multi-origin'de bundle sürüm uyumsuzluğu riski | ADR-001 tek build disiplini + [[../index]] §5 R-004 — build zinciri yasağı; §1.4 kısıt 1 |
| 2 | **Framework tabanlı SPA router (React Router / Vue Router / Next.js App Router vb.)** | Olgun, belgelenmiş, edge case'leri çözülmüş | ADR-001 framework yasağına (React/Vue/jQuery yasak) doğrudan ihlal; bundle şişirir; `var`/framework çalışma zamanı CSP/TrustedTypes disiplinini kırar | [[ADR-001-vanilla-js-itcss]] yasağı; R-001 (Redux — framework bağımlılığı) ile aynı gerekçe ailesi; §1.3 kaynak 8-10 saf History API'nin yeter (ama sınır yazılarak) der |
| 3 | **Tüm route'larda sunucu tarafı render (MPA / her navigasyon tam sayfa yükü — SPA router yok)** | SEO en basit haliyle; state yönetimi yok | Her navigasyon tam sayfa = footer player/view-mode durumu kopar (ADR-045/046 cross-view state hedefiyle çelişir); TTFB + asset yeniden yükü; mevcut 28 dosyalık istemci router çöp olur | Kayıp: etkileşimli player/scroll/focus sürekliliği; §1.3 kaynak 1-4 (CSR etkileşim route'ları için yeterli + hibrit çoğunluk); sunucu first paint'i zaten korunuyor — tam MPA gereksiz |
| 4 | **Tek origin + path-tabanlı domain ayırma (`coremusic.net/auth`, `/music` …)** | Cookie/scope tek origin = en basit güvenlik modeli | Mevcut subdomain altyapısı (domain.php 7 subdomain, ayrı statik servis, ayrı portlar) yıkılışı; tenant/domain kimliği URL path'e sızar; statik asset CDN'liği (assets.coremusic.net) path'e taşınır | Mimari mevcut durum (IMPLEMENTED domain.php + 3 subdomain klasörü) — yeniden yazım maliyeti **çok yüksek** (7 subdomain + 3 fiziksel subdomain klasörünün yıkımı); ADR-043 auth subdomain konsolidasyon yönüyle ters; §1.3 kaynak 5-7 subdomain routing'in olgunluğunu doğrular |
| 5 | **İstemci tarafında tam yorum (JS) ile ilk yük — sunucusuz SPA (PageRouter bypass)** | Sunucu en basit (statik shell yeter) | 2026 literatürü: CSR ilk HTML'i botlara/AI crawler'lara boş görünür, TTFB/k ilk boyama yavaş (§1.3 kaynak 2-4); auth korumalı rotaların ilk erişim kontrolü istemciye kalır → güvenliksiz | §1.3 Sonucu madde 1 (SEO-kritik route SSR ister) + güvenlik: auth ilk yükü sunucu işi (AuthGuard IMPLEMENTED); geri döndürülmesi pahalı (SEO kaybı) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek build, tek yığın:** ADR-001 disiplini korunur; domain başına bundle/build/test zinciri yok (§2.1 m.1).
- **SEO + ilk yük güvence altında:** server-rendered first paint (`HtmlShellRenderer`) ile 2026 literatürüne uygun hibrit denge; bot/AI-crawler ham HTML görür (§1.3 kaynak 1-4).
- **Tek uygulama kabuğu, domain esnekliği:** route + konfigürasyon + tema/görünüm ile domain farklılığı; yeni domain eklemek bundle değil, route/konfig satırı (domain.php + route konfig'i).
- **Taşınabilir router:** `shared/` Composer/PSR-4 paketi — CoreMusic'e özel değil, her web projesi kurabilir (ADR-085 konsepti).
- **Sorumluluk sınırı yazılı:** iki router aynı route'u yorumlamaz; adres çubuğu tek SSOT (§2.2 b).
- **Güvenlik temeli diskte hazır:** `SecurityHeadersMiddleware` (`frame-ancestors 'none'` + `X-Frame-Options: DENY`), `OriginCheckMiddleware`, `CorsMiddleware` — karar bu kapıları şart koşar, yeni kod yazmaz (IMPLEMENTED).

### 4.2 Olumsuz Sonuçlar

- **History API kırılganlığı devrede:** `popstate` edge case'leri, initial state (`replaceState` zorunluluğu), back/forward ayrımı yapılamaması — hepsi kodda elle yönetilir; kütüphane yok (§1.3 kaynak 8-10).
- **İkili router = iki katmanlı debug:** ilk yük hatası (sunucu) vs navigasyon hatası (istemci) ayrı ayrı izlenir; sınır ihlali sessiz çift-anlam üretir.
- **Multi-origin güvenlik yüzeyi:** 7 subdomain = 7 origin; cookie scope, postMessage, redirect doğrulaması her domain'de aynı disiplin ister — ihlal bir domain'e sıçrayabilir (§1.3 kaynak 5-7).
- **Hibrit render karmaşıklığı:** sunucu shell + istemci patch eşleşmeli; DOM selector/patch uyumsuzluğu runtime hatası üretir (DomPatcher/ContentPatcher sorumluluğu).
- **Route konfig sürükleme:** domain route ağacı konfig dosyaları güncel tutulmazsa sunucu-istemci route uyuşmazlığı (PLANNED kanıt boşluğu — §2.2 e).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Sunucu-istemci route uyuşmazlığı (aynı URL iki farklı render) | 3 (olası) | 4 (yüksek) | Tek route konfigi paylaşım sözleşmesi (SpaRoute ↔ route konfigi); §5.1 #3 domain route ağacı konfigi + senkron testi (QA) |
| İlk yük `replaceState` unutulması / popstate edge case → back butonu bozuk | 3 (olası) | 3 (orta) | HistoryManager/RouterEventManager'da initial-state + edge case regresyon testi (§1.3 kaynak 8-10; QA Engineer) |
| Path/redirect enjeksiyonu → open redirect (OAuth zinciri dahil) | 2 (mümkün) | 4 (yüksek) | Yalnız göreli path + whitelist (ReturnUrlPolicy/AuthGuard — `shared/src/Security/ReturnUrlPolicy.php` IMPLEMENTED); §1.3 kaynak 11-13; code review + statik kapı |
| `postMessage('*')` wildcard'ı → token/veri sızıntısı (MSRC 2025 sınıfı) | 2 (mümkün) | 4 (yüksek) | Tam `targetOrigin` + alıcıda `event.origin` doğrulaması; CSP/CORS hizası (§1.3 kaynak 14-17); security-audit workflow |
| Clickjacking (framing) — frame başlığı eksik domain | 2 (mümkün) | 3 (orta) | `frame-ancestors 'none'` + `X-Frame-Options: DENY` tüm response'larda (SecurityHeadersMiddleware — IMPLEMENTED, L28/L69); SameSite cookie katmanı (§1.3 kaynak 18-19) |
| Ayrı bundle baskısı (yeni domain geldiğinde "ayrı build yapalım" itirazı) | 3 (olası) | 3 (orta) | §2.2 c: domain farklılık = route + konfig + tema; bu ADR frozen olmasa da değiştirilmesi yeni ADR ister (§5.2) |
| SEO kaybı (bir domain'in ilk yükü CSR'a düşerse) | 2 (mümkün) | 4 (yüksek) | İlk yük/fallback her domain'de PageRouter'dan geçer (AuthGuard/RouteRegistry zorunlu); QA E2E'de bot-benzeri ilk-yük testi (§1.3 kaynak 1-4) |

### 4.4 Fallback (Zaruri İstisna Kapısı)

| # | Koşul (hepsi) | Onay |
|---|---------------|------|
| 1 | Performans/SEO metriği (LCP/first paint + crawl doğrulaması) 3 sprint boyunca hibrit modelle karşılanamamıştır — kanıt: ölçüm raporu | Backend Architect + QA Engineer |
| 2 | **Yeni ADR** yazılır; bu dosya düzenlenmez (`superseded by` bağı yeni ADR'nin §6'sına konur) | Vault Steward → Tech Lead → Arch Lead |
| 3 | Kapsam dar: tek domain çifti (ör. yalnız auth akışı) — tüm mimari değil | Arch Lead |
| 4 | ADR-001 (vanilla JS/tek build) ve istemci kütüphane yasağı **değişmez** | UI Designer + QA Engineer |
| 5 | Güvenlik kapıları (whitelist redirect, frame başlıkları, postMessage origin) fallback ile **kaldırılamaz** — yalnız genişletilebilir | Security Engineer |

*Geçici istisna en fazla 5 iş günü sürer; sonunda `git revert` + `log.md` ERROR satırı. "Domain başına ayrı bundle" hiçbir geçici istisnada başlatılamaz — route + konfigürasyon yolu korunur.*

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-004-multi-domain-spa`) + `log.md` append ("ADR-004 yazıldı (debate PENDING)") + dizin satırı doğrula ([[../index]] §3 satırındaki `[[ADR-004-multi-domain-spa]]` artık dosyaya ulaşır — slug eşleşmesi) | Vault Steward | 30 dk |
| 2 | İstemci SPA router'ı taşınabilir Composer paketine al: `shared/` altında PSR-4 namespace'li paket iskeleti (router mekanizması: matcher/guard/history/patcher — route'lar UYGULAMA konfigi olarak paket dışı) — mevcut `assets.coremusic.net/js/router/` (28 dosya) kaynağı, silinme DEĞİL taşıma/sarma | Backend Architect + UI Designer | 3 gün |
| 3 | **Domain route ağacı konfigi:** 7 domain (auth/home/assets/music/admin/media/api) için route listesi + tema/görünüm eşlemesi tek konfig sözleşmesinde — `SpaRoute` (sunucu) ↔ istemci route konfigi senkronu + senkron testi | Backend Architect + QA Engineer | 2 gün |
| 4 | Taşınabilirlik doğrulaması: paketin harici (CoreMusic dışı) minimal bir HTML projesinde `composer require` + örnek route ile kurulumu — kanıt log'a + bu ADR §2.2 e tablosuna (PLANNED → IMPLEMENTED geçişi **yeni log append** ile, bu dosya düzenlenmez) | Backend Architect | 1 gün |
| 5 | Güvenlik kapıları denetimi: (a) redirect/path whitelist (ReturnUrlPolicy/AuthGuard) tüm route'larda, (b) `frame-ancestors` + `X-Frame-Options` tüm response'larda (SecurityHeadersMiddleware), (c) `postMessage` çağrılarında tam origin + `event.origin` doğrulaması — §4.3 risk satırlarıyla aynı kapılar | Security Engineer + QA Engineer | 1 gün |
| 6 | History API edge case regresyonu: initial `replaceState`, popstate back/forward, anchor tıklaması, 404 fallback — E2E (Playwright) senaryoları | QA Engineer | 1 gün |
| 7 | Debate (✅ TAMAMLANDI — 3 tur / 20 persona, 18/2/0 KABUL) + Tech Lead (✅ 2026-09-24) onayı: sonuç §7.1'e yazıldı; şartlar §5.1 #8-#10'a satır olarak eklendi | Vault Steward + Tech Lead | 1 gün |
| 8 | **Debate şartı 1:** `shared/` altında taşınabilir Composer router paketi iskeleti + `composer.json` PSR-4 kaydı (paket = mekanizma, route = uygulama konfigi) — adım #2'nin ön koşulu; iskelet diskte görünmeden taşıma tamamlanmaz (§2.2 d, §2.2 e PLANNED satırı) | Backend Architect + UI Designer | 1 gün |
| 9 | **Debate şartı 2:** 7 domain (auth/home/assets/music/admin/media/api) route ağacı konfigi diskte tamamlanır — §2.2 e'deki PLANNED satırının gerçekleşme kanıtı; `SpaRoute` ↔ istemci route konfigi senkron testi adım #3 ile birlikte koşulur | Backend Architect + QA Engineer | adım #3 ile |
| 10 | **Debate şartı 3:** 4 güvenlik kapısı testi (Playwright E2E): (a) postMessage tam `targetOrigin` + `event.origin` doğrulaması, (b) path/redirect doğrulama (göreli + whitelist — ReturnUrlPolicy/AuthGuard), (c) `frame-ancestors 'none'` + `X-Frame-Options: DENY` başlığı, (d) open-redirect reddi | QA Engineer + Security Engineer | 1 gün |

### 5.2 Geri Dönüş Planı

Karar mimaridir; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Ayrı bundle'a geçiş** gerekirse (ör. bir domain'in bağımsız deploy'u): §4.4 koşulları + **yeni ADR** — bu ADR `superseded by` ile bağlanır; mevcut tek build korunur, bundle pilot domain ile sınırlı başlar; (2) **İkili router sınırı değişirse** (ör. tümü sunucu DOM'a geçsin veya tümü istemciye): yeni ADR + §5.1 #3 route konfig senkron testi ön koşul; (3) **Router paketleme geri alınırsa** (taşınabilirlikten vazgeçilirse): `git revert` + `log.md` ERROR — `assets.coremusic.net/js/router/` kaynağı hiç silinmediği için geri dönüş dosya bazında güvenli; (4) **Güvenlik fallback'i yoktur:** §4.4 madde 5 — whitelist/frame/post kapıları hiçbir senaryoda kaldırılamaz; (5) **Veri/state kaybı riski yoktur** — bu ADR katmandır, veri değiştirmez (veri: [[ADR-003-multi-db-bcnf]], [[ADR-081-multi-provider-data-sync]]); (6) Vault bozulursa standart kurtarma `git checkout` + son commit ([[../../AGENTS.md]] §17 #10).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-001-vanilla-js-itcss]] | Bağlayıcı ön koşul: tek vanilla yığın + framework/build yasağı — bu ADR'nin "ayrı bundle YOK" kararının temeli |
| [[ADR-003-multi-db-bcnf]] | Domain kavramının veri ayağı: domain bölmesi DB'de BCNF, SPA'da route+konfig (çapraz: domain tanımı tek yerde, uygulama iki katman) |
| [[ADR-081-multi-provider-data-sync]] | Domain'ler arası veri akışı (outbox) — SPA katmanının fetch/patch'i bu akışın tüketicisidir |
| [[../index]] | Karar dizini — bu ADR'nin kaydı (`[[ADR-004-multi-domain-spa]]` §3) + R-001/R-004 reddi §5 (alternatif gerekçeleri) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — guardrails (özellikle Guardrail #16), K katmanları |
| [[../../AGENTS.md]] | Backend (PageRouter)/UI (router JS)/Security (middleware) domain sorumlulukları §5; escalation §10; edge case §17 |
| [[../../brain]] | Mimari karar özeti — ADR-044/045/046/083/085 özetleri (düz metin: hepsi `(henüz yazılmadı — vault: brain.md)`) |
| [[../../index]] | Master katalog — subdomain mimarisi |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| [[../../ui-design/01-mockup-index]] | Görsel mockup indeksi — domain route/görünüm tasarımı (Guardrail #11) |
| [[../../ui-design/05-responsive-architecture]] | §09_ViewModes (v-home/v-pro/v-studio/v-car) — domain görünüm modu kanıtı (ADR-045/046 konsepti) |
| `shared/src/PageRouter/` | Sunucu router — IMPLEMENTED kanıt (§2.2 e) |
| `assets.coremusic.net/js/router/` | İstemci SPA router (28 dosya, saf History API) — IMPLEMENTED kanıt (§2.2 e) |
| `shared/composer.json` | PSR-4 Composer altyapısı — ADR-085 hybrid kanıtı (§2.2 d) |
| `shared/config/domain.php` | 7 subdomain + primary domain haritası — multi-origin kanıtı (§1.1) |
| `shared/src/Middleware/SecurityHeadersMiddleware.php` · `OriginCheckMiddleware.php` · `CorsMiddleware.php` | Clickjacking/origin/CORS kapıları — IMPLEMENTED (§4.1, §4.3) |
| `shared/src/Security/ReturnUrlPolicy.php` | Redirect/path whitelist — open-redirect mitigasyonu (§4.3) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (`references/` dizini var; dosya adı ⚠️ VERIFICATION REQUIRED) |
| Eski seri (düz metin — dosya YOK): ADR-009 (clean URL), ADR-021 (SPA router contract), ADR-044/045/046 (tema/view/state), ADR-083 (SPA router), ADR-085 (composer paket) | İlişkili kararlar — `(henüz yazılmadı — vault: brain.md)`; eski numaralara wiki-link KURULMAZ |
| [[../../log]] | Audit trail (bu kaydın append satırı) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı karar — "sen karar") + mimar kararı | 2026-09-24 | ✅ |
| Tech Lead | Debate onayı (3 tur / 20 persona — 18/2/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) — 2026-09-24 |
| Tur 1 — 20 persona | 15 kabul/neutral · 3 uyarı: **SEO** (CSR deep-link → hibrit render notu), **QA** (popstate/geçmiş testi), **PM** (bilgi notu) · Critic en sert: shared/ Composer paketi diskte yok → şart |
| Tur 2 — İtiraz→çözüm | (1) subdomain ≠ oturum izolasyonu → **cookie scope/domain ayarı** maddesi (§1.4 kısıt 6, §1.3 kaynak 5-7); (2) 7 domain route ağacı konfigi yok (§2.2 e — PLANNED 3 satır) → **gerçekleşme şartı** (§5.1 #9); (3) `replaceState` throttle → tarayıcı history kotası notu (§4.2) |
| Tur 3 — Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Karara dönüşen şartlar | 3 şart §5.1'e satır olarak eklendi: **#8** shared/ Composer router paketi iskeleti + composer.json PSR-4 kaydı · **#9** 7 domain route ağacı konfigi · **#10** 4 güvenlik kapısı testi (Playwright: postMessage targetOrigin, path doğrulama, frame-ancestors, open-redirect) |
| Tech Lead | ✅ (2026-09-24 — debate sonrası onay) |
| Sonuç | **KABUL** (18/2/0) — 2026-09-24 |
| Çözüm | Şart 1 → taşınabilirlik kapısı kapatıldı (shared/ Composer iskeleti + composer.json PSR-4 disk kanıtı şart); şart 2 → 7 domain route ağacı konfigi (§2.2 e PLANNED → gerçekleşme) kapatıldı; şart 3 → 4 güvenlik kapısı Playwright E2E ile zorlandı (§1.4 kısıt 6, §4.3). Tech Lead: ✅ |

---

*ADR-004 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/ yeni seri; slug: `multi-domain-spa`)*
*Authority: ADR-004 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
