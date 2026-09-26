---
title: "CoreMusic — ADR-012: CSP Nonce Strict-Dynamic (Tek Politika · Kademeli Devreye Alma · unsafe-inline/eval Kalıcı Kapalı)"
type: adr
category: security
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-012 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ KABUL — 3 tur / 20 persona (18/2/0) · 2026-09-24"
---

# CoreMusic — ADR-012: CSP Nonce Strict-Dynamic (Tek Politika · Kademeli Devreye Alma · unsafe-inline/eval Kalıcı Kapalı)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-012'yi sıfırdan yaz") · debate: ✅ KABUL (2026-09-24) · Tech Lead: ✅
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (7 subdomain domain haritası — tek CSP bu domain listesinde yaşar; dosya diskte VAR ✅) · [[ADR-009-clean-url-redirect]] (HSTS bu ADR'de PLANNED olarak düzeltilmişti — §1.1'de Middleware/CLAUDE.md CSP iddiaları ayrıca denetlendi; dosya diskte VAR ✅) · [[ADR-010-csrf-protection-strategy]] (XSS↔token köprüsü: "XSS tüm CSRF savunmalarını yok eder" → bu ADR o köprünün kapatıcısı; dosya diskte VAR ✅) · [[ADR-011-session-management]] (nonce oturumda saklanır `$_SESSION['csp_nonce']` — oturum hijyeni bu ADR'nin nonce zincirini besler; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (Zero Hallucination hedefi — her iddia kod dosya yoluyla/§1.3'te kaynakla kanıtlanır; dosya diskte VAR ✅) · karar dizini [[../index]] §3 satırı `[[ADR-012-csp-nonce-strict-dynamic]]` (satır 49 — slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

XSS, CoreMusic'un en yüksek etkili saldırı sınıfıdır: ADR-010'un bulgusuna göre "XSS tüm CSRF savunmalarını yok eder" (synchronizer token okunur), ADR-011'e göre XSS oturum/JWT çalar (refresh cookie `HttpOnly` olsa bile token body'den okunabilir). Bu ADR, XSS'in **çalışma zamanı etkisini** Content Security Policy ile kapatır: script kaynaklarını nonce + `strict-dynamic` ile sınırlar, `unsafe-inline`/`unsafe-eval`'i **kalıcı** olarak kapar ve tüm CoreMusic domain'leri için **tek, domain listeli** politika belirler. Bugün kodda zaten çalışan bir CSP vardır (IMPLEMENTED — §1.1); karar bunu **tek politika + kademeli devreye alma + kalıcı yasaklar** ile tamamlar, yıkmaz. Kapsam: (a) strateji (nonce + strict-dynamic, unsafe kalıcı kapalı), (b) tek politika/domain listesi (ADR-004), (c) report-only → enforce kademelendirmesi, (d) nonce middleware üretimi + template enjeksiyonu, (e) iddia-kod çelişkilerinin işaretlenmesi.

### 1.1 Mevcut Durum

**Kod kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yoluyla):**

- **`shared/src/Middleware/SecurityHeadersMiddleware.php` (satır 16-19) — NONCE ÜRETİMİ IMPLEMENTED:** `bin2hex(random_bytes(32))` → 256-bit CSPRNG hex nonce, `$request['_csp_nonce']` alanına yazılır (SessionManager'dan önce çalışır). Güvenlik agentı kuralıyla ("CSP nonce per-request 256-bit random — ADR-012", `.opencode/opencode.json`) birebir uyumlu ✅.
- **`shared/src/Middleware/SecurityHeadersMiddleware.php` (satır 31, 48-72) — CSP BAŞLIĞI IMPLEMENTED (ENFORCE):** `Content-Security-Policy` başlığı **şu an `Content-Security-Policy-Report-Only` DEĞİL, doğrudan enforce** olarak gönderilir. `buildCsp()`: `default-src 'self'; script-src 'strict-dynamic' 'nonce-{nonce}' https:` (nonce boşsa `'self' https:`), `style-src '{nonce}' 'self' {assets} fonts.googleapis.com`, `img-src 'self' data: {assets}`, `font-src`, `connect-src 'self' {assets}`, `media-src`, `frame-ancestors 'none'`, `base-uri 'self'`, `form-action 'self'` (satır 62-71). Ek: `Require-Trusted-Types-For 'script'` (satır 32). **HSTS bu dosyada YOK** (ADR-009'da PLANNED olarak düzeltilmişti ✅) — ayrıca `object-src`/`report-uri`/`report-to`/`Reporting-Endpoints` **YOK** → PLANNED (§2.2).
- **Nonce zinciri IMPLEMENTED:** middleware kaydı `shared/src/PageRouter/PageRouterKernel.php:276` → `shared/src/Middleware/SessionManagerMiddleware.php:18/20` (`startOrExtend(nonce)` + `$_SESSION['csp_nonce']` geri okuma) → `shared/src/PageRouter/SessionInitializer.php:46` · `shared/src/Session/SessionLifecycle.php:52,79` (`$_SESSION['csp_nonce'] = bin2hex(random_bytes(32))`) → `shared/src/PageRouter/HtmlShellRenderer.php:58,108` (`<meta name="csp-nonce" content="...">` + script nonce enjeksiyonu). Testler: `shared/tests/Middleware/SessionManagerMiddlewareTest.php:82-130` (nonce üretimi/kaydı/esneklik) + `MiddlewarePipelineTest.php:225-227` (#4 SecurityHeaders → #5 SessionManager sırası).
- **Nonce'lu script basımı IMPLEMENTED (auth + home):** `auth.coremusic.net/pages/login.php:11,129` · `register.php:11,175` · `logout.php` · `set-gender.php:65` (`<script nonce="...">`), `home.coremusic.net/pages/home.php:101` — sayfalar `$_SESSION['csp_nonce']` okur. `assets.coremusic.net/js copy/router/ScriptInjector.js:1-18` meta `csp-nonce`'dan okuyup dinamik script'e nonce atar ⚠️ VERIFICATION REQUIRED (klasör adı "js copy" — canlı varlık mı doğrulanmalı).
- **⚠️ İKİNCİ, ÇELİŞKİLİ POLİTİKA — `auth.coremusic.net/include/Middleware/SecurityHeadersMiddleware.php`:** (1) satır 29-31: CSP **yalnız** `$request['server']['csp_nonce']` doluysa üretilir — bu anahtar auth kod tabanında **hiçbir yerde yazılmıyor** (grep `csp_nonce`: 1 okuma/0 yazma; yazmalar hep `$_SESSION`) → başlık fiilen hiç üretilmiyor; (2) satır 31: `style-src 'self' 'unsafe-inline'` — **karara aykırı** (`unsafe-inline` kalıcı kapalı); (3) satır 25: `Strict-Transport-Security` var — ama sınıf **kayıtsız/ölü kod şüpheli**: grep `SecurityHeaders` auth'da 3 eşleşme (sınıf tanımı + 2 doküman, örnekleme/`new`/pipeline kaydı YOK) ⚠️ VERIFICATION REQUIRED. Sonuç: auth sayfaları nonce'lu script basıyor ama orada **CSP başlığı üretilmiyor** → nonce uygulaması tek başına koruma sağlamaz. **İddia-kod çelişkisi: EVET (§2.2f).**
- **`shared/src/Middleware/CLAUDE.md` CSP iddiaları — ADR-009 DOKUNMADI, amaçoğunu çoğunu karşılıyor:** satır 21/42/58/68/93-97 "CSP nonce üretimi SecurityHeaders'da, SessionManager kaydeder, strict-dynamic, Sıra DEĞİŞTİRİLEMEZ" → **kodda IMPLEMENTED** (HSTS iddiası satır 42/58/96'da ADR-009 ile "PLANNED (kodda yok)"a düzeltilmiş ✅; CSP iddiaları düzeltilmedi çünkü çoğunlukla doğru). ⚠️ **Bir iddia-kod biçimsel çelişkisi vardı: satır 95 "`nonce-{base64}`" — kod HEX üretir (`bin2hex`), base64 değil** → debate şartı 1b ile düzeltildi (2026-09-24); satır 94 `base64_encode(random_bytes(32))` ifadesi ⚠️ VERIFICATION REQUIRED. Satır 144'teki wiki-link `[[../../.ai/decisions/accepted/ADR-012-csp-nonce-strict-dynamic]]` bu dosya oluşana kadar hedefsizdi → bu işlemle slug hedefi oluşur.
- **Report-only altyapısı YOK → PLANNED:** grep `report-only|report-uri|report-to|Reporting-Endpoints` → `shared/src` + `auth` içinde **0 kod eşleşmesi**; eşleşmeler yalnızca vault doküman örnekleri (`.ai/architecture/k7-middleware/security-headers-middleware.md:176`, `.ai/architecture/k6-guvenlik/csp-policy.md:84,200`, `security-headers.md:163`) — yani "development'ta report-only" hedefi dokümanlarda var, kodda yok.
- **Domain listesi YOK → PLANNED:** `connect-src 'self' {assetsOrigin}` (satır 67) — ADR-004'ün 7 subdomain'i (API fetch'ler dahil) politikada temsil edilmiyor; `https://*.coremusic.net` biçimli domain-scope varyantı yok.
- **İlgili doküman iddiaları:** `.opencode/CLAUDE.md` + `.claude/CLAUDE.md` satır 159-167 "SecurityHeaders → CSP strict-dynamic, X-Frame, **HSTS**" — CSP kısmı doğru, HSTS kısmı `shared` için PLANNED (ADR-009). `.cursorrules:152` "CSP | strict-dynamic with nonce" → kodla uyumlu ✅.

### 1.2 Sorun Tanımı

1. **Tek politika yok:** `shared` enforce CSP (canlı, PageRouterKernel kayıtlı) ile `auth`'daki ölü/kayıtsız ikinci CSP kodu var; hangi politikanın geçerli olduğu subdomain'e göre belirsiz → ADR-004'ün 7 domain'i tek politikada birleşmiyor.
2. **Report-only yok:** kod anında enforce ediyor; kararın "önce report-only → rapor topla → ihlal yoksa enforce" kademelendirmesi bugün uygulanamaz, rapor toplama uçları (report-uri/report-to/Reporting-Endpoints) kodda yok.
3. **Domain listesi yok:** `connect-src` yalnız `'self'` + assets origin; ADR-004 subdomain API'leri ve ortak img/frame/connect kaynakları politika dışında → cross-subdomain fetch ihlal raporlarıyla patlar.
4. **`unsafe-inline` kalıcı kapalı kararı kodda parçalı:** `shared` style-src'de yok ✅ ama `auth` ölü kodunda `style-src 'self' 'unsafe-inline'` var → kararı ihlal eden ikinci metin diskte duruyor.
5. **Fallback kararı yazılı değil:** `script-src ... 'strict-dynamic' 'nonce-...' https:` (satır 54) — eski (CSP2) tarayıcılarda `https:` her HTTPS script'e izin verir; `'self'`/`https:` fallbackinin bilinçli kapsamı vault'ta yok.
6. **İddia-kod biçimsel çelişki:** `Middleware/CLAUDE.md:95` `nonce-{base64}` ↔ kod hex (`bin2hex`); ADR-009 HSTS'yi düzeltirken CSP iddialarına dokunmadı — "CSP var mı kodda" sorusunun cevabı: **VAR (IMPLEMENTED)**, ama iki küçük çelişki (biçim + auth ölü politika) açıkça işaretlenmelidir.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅, v7.2.0) — birincil/resmî kaynak önce (OWASP CSP Cheat Sheet, MDN, Chrome for Developers, NDSS, PortSwigger Research), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; **güvenlik iddiası = OWASP + resmî zorunlu**. **Odak: (a) CSP strict-dynamic + nonce 2025-26 en iyi uygulama; (b) nonce vs hash; (c) CSP bypass literatürü (dangling markup, JSONP gadget, base-uri, form-action); (d) report-only → enforce geçiş pratiği; (e) tarayıcı desteği (CSP3/strict-dynamic/report-to).**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "Content-Security-Policy strict-dynamic nonce best practice 2025 report-only to enforce rollout" · (2) "CSP bypass techniques dangling markup JSONP gadget base-uri form-action strict-dynamic weakness" · (3) "CSP nonce vs hash 2025 browser support strict-dynamic caniuse CSP level 3 Safari Firefox" · (4) "CSP Reporting API report-uri deprecated reporting-endpoints report-to 2025 browser support" |
| Web Search **Konusu** | Strict CSP'nin nonce + `strict-dynamic` ile kurulumu; `unsafe-inline`/`unsafe-eval` yerine nonce/hash; raporlamalı (report-only) kademeli devreye alma ve enforce'e geçiş adımları; CSP'nin nonce'a rağmen bypass edilebildiği klasik yüzeyler (JSONP whitelist, eksik `base-uri`, dangling markup, `strict-dynamic` güven zinciri); raporlama başlıklarının (`report-uri` → `report-to`/`Reporting-Endpoints`) 2025-26 durumu; CSP3/`strict-dynamic` tarayıcı desteği. |
| Web Search **Bağlam** | **~19 adlandırılmış kaynak / 4 sorgu** (çoğu 2024-2026; birincil + ikincil): birincil — OWASP CSP Cheat Sheet, MDN CSP + `report-uri` (Web.dev/MDN), Chrome for Developers Reporting API, NDSS 2023 DiffCSP (tarayıcı CSP hataları), PortSwigger Research (dangling markup), Google/secappdev "From Zero to Hero with CSP" (Philippe Deryck); ikincil — content-security-policy.com (strict-dynamic destek tablosu), CentralCSP (report-to Baseline 2026), Netlify Blog (nonce entegrasyonu + kademeli dağıtım), Microsoft Learn Power Pages (report-only → enforce), tech-insider 2026 (CSP kurulum rehberi), DCHost (nonce/hash/report-to), Invicti (yanlış CSP'nin bedeli + nonce vs hash), BBLabs/Cobalt/InfoSec Writeups/Medium (bypass literatürü), Stack Overflow (strict-dynamic + unsafe-inline fallback tartışması). |
| Web Search **Kısa Açıklama** | **(1) Strict CSP 2025-26:** OWASP, Strict CSP'yi nonce **veya** hash + isteğe bağlı `strict-dynamic` ile kurar; Google rehberi `strict-dynamic`'i yükleyici (loader) tabanlı uygulamalar için önerir; `unsafe-inline`/`unsafe-eval` nonce varken CSP3 tarayıcılarda zaten yok sayılır — ama **bilerek kalıcı kapalı tutulur**. **(2) Bypass literatürü:** whitelist'teki JSONP endpoint'leri (Google API örnekleri), eksik `base-uri` (nonce bilinmese de `<base href=attacker>` ile göreli script'leri sızdırma), dangling markup (script'siz veri sızıntısı — CSP3'ün nonce'lu HTML enjeksiyonunu durduramadığı senaryo), `strict-dynamic`'in dar istisnası (nonce'lu yükleyici → attacker-controlled import zinciri). CoreMusic'te `base-uri 'self'` + `form-action 'self'` zaten kodda ✅. **(3) Raporlama:** `report-uri` deprecat — `report-to` + `Reporting-Endpoints` (Baseline 2026) ile değiştirilir; destekleyen tarayıcılar `report-uri`'yi yok sayar → **her ikisi birlikte** yazılır. **(4) Geçiş pratiği:** Microsoft Learn ve Netlify aynı adımları verir: report-only başlat → ihlalleri konsol/raporla incele → kaynakları ekle → kritik ihlal kalmayınca enforce. **(5) Destek:** `strict-dynamic` CSP3 — Chrome 52+/Edge 79+/Firefox 52+/Safari 15.4+; IE yok; NDSS: Firefox/Safari `default-src`'te nonce/hash/strict-dynamic'i desteklemez (fallback mekanizması yok) → politika `script-src`'te tutulmalı. |
| Web Search **Uzun Açıklama** | **(a) Strict CSP mimarisi:** OWASP Cheat Sheet, Strict CSP için nonce-tabanlı (dinamik sayfa — CoreMusic tipi) veya hash-tabanlı (statik içerik) yaklaşımı tarar; `strict-dynamic` nonce/hash'e eşlik ederek yükleyicinin yüklediği script'leri de güvenli kabul eder ve CSP3'te `'self'`/URL-tabanlı ifadeleri yok sayar (eski tarayıcılar için bunlar fallback kalır — Google'ın geri-uyumlu politikası `https: http:` içerir). Invicti ve Google el kitabı aynı uyarıyı yapar: **nonce'lu yükleyici, attacker'ın etkileyebildiği import'ları da yetkilendirir** → yükleyici kodu sade ve kullanıcı girdisinden arınmış tutulmalı. Nonce vs hash: nonce **her yanıtta** yeniden üretilir (dinamik sayfa, oturum bağlama ile güçlü); hash içerik değişince kırılır (formatting/whitespace) — statik build artifact'leri için uygundur (OWASP: "hash kullanmak riskli olabilir"; Invicti: nonce dinamik, hash sabit). **(b) Bypass literatürü:** PortSwigger Research — dangling markup, script çalıştırmasan da sayfa içeriğini `<img src=//attacker>` ile sızdırır; CSP nonce bunu tek başına durdurmaz (HTML enjeksiyonu yeterli) → `form-action`/`base-uri`/çıkış kısıtları tamamlar. BBLabs/Cobalt — (1) `script-src`'te JSONP whitelist'i → callback ile arbitrary JS; (2) eksik `base-uri` → göreli script yolları saldırgan origin'ine çözülür; (3) `strict-dynamic` + gadget kütüphaneleri (eski jQuery/Prototype/Handlebars) nonce'lu script'ten çalışır. Medium (Breaking CSPs) — `strict-dynamic` yalnızca "güvenli başlangıç" ister; başlangıç nonce/hash'tir, URL-based ifadeler CSP3'te yok sayılır. **Cobalt/InfoSec:** iyi politika `default-src 'none'` + nonce + `strict-dynamic` + `object-src 'none'` + `base-uri 'self'` + Trusted Types — CoreMusic'te `object-src 'none'` bugünkü kodda yok (default-src 'self' fallback'i devrede) → §2.2'de PLANNED. **(c) Raporlama & geçiş:** MDN — `report-uri` deprecat, `report-to` destekli tarayıcılarda yok sayılır; ikisini birlikte yaz. Chrome Reporting API — `Reporting-Endpoints` başlığı modern uç; Microsoft Learn — "önce report-only, konsolda ihlalleri incele, kaynakları kademeli ekle, kritik ihlal kalmayınca enforce" (aynı sıra Netlify/tech-insider/DCHost'ta). Netlify ayrıca **kademeli nonce dağıtımı** (yüzde oranlı trafik) ve "raporlar günlerce tahmin etmeyi önler" notu ile pratik enforce eşiği verir. **(d) Tarayıcı:** content-security-policy.com — `strict-dynamic` Chrome 52+/Firefox 52+/Safari 15.4+, IE'de yok; eski tarayıcıda nonce CSP2'de çalışır, `strict-dynamic` çalışmaz → URL fallback'i (`https:`/'self') bu yüzden politikada taşınır; NDSS DiffCSP — Safari/Firefox `default-src`'te strict-dynamic desteklemez → fallback'ler `script-src`'te ve bilinçli yazılır. |
| Web Search **Paragraf Veri Uzun** | OWASP Strict CSP: nonce (dinamik) veya hash (statik) + `strict-dynamic` · nonce = her yanıt CSPRNG, oturum bağlanabilir · hash = içerik değişince kırılır · `strict-dynamic` CSP3: nonce/hash güvenince URL-tabanlı/`'self'` yok sayılır, eski tarayıcıda fallback devrede (Chrome 52+/FF 52+/Safari 15.4+, IE yok) · NDSS: Safari/FF `default-src`'te strict-dynamic desteklemez → `script-src`'te tut **·** yükleyici attack yüzeyi (attacker import'u yetkilenir) · bypass: JSONP whitelist · eksik `base-uri` · dangling markup (nonce HTML'i durdurmaz) · gadget kütüphaneleri · `form-action`/`base-uri` tamamlayıcı · CoreMusic'te `base-uri 'self'`+`form-action 'self'`+`frame-ancestors 'none'`+Trusted Types başlığı IMPLEMENTED, `object-src 'none'` YOK · report-uri → report-to + Reporting-Endpoints (Baseline 2026), **ikisi birlikte** yazılır · geçiş: report-only → konsol+rapor incelemesi → kaynak ekle → kritik ihlal kalmayınca enforce (Microsoft Learn, Netlify, tech-insider, DCHost) · Netlify: yüzde bazlı nonce dağıtımı + ihlal ucu hazır tutma · false positive → enforce eşiği (raporlama döngüsü olmadan enforce erken patlar). |
| Web Search **Sonucu** | 1) **nonce + `strict-dynamic` birincil strateji** doğrulandı (OWASP + Google/secappdev + Invicti) → karar destekli. 2) **`unsafe-inline`/`unsafe-eval` kalıcı kapalı** — nonce varken CSP3'te zaten yok sayılır ama fallback yüzeyi ve stil güvenliği için **açıkça yazılır/ kalıcılaştırılır** (OWASP + Netlify default'larının aksine `unsafe-*` çıkar). 3) **report-only → enforce** geçişi 4 kaynakla (Microsoft Learn, Netlify, tech-insider, DCHost) doğrulandı → kademeli devreye alma kararın pratiğidir; **false positive için enforce öncesi eşik** (konsol+rapor temizliği) zorunlu. 4) **Raporlama:** `report-uri` deprecat → `report-to` + `Reporting-Endpoints` (Baseline 2026) + `report-uri` fallback **birlikte** (MDN + CentralCSP + Chrome). 5) **Bypass literatürü doğrulandı** (PortSwigger + BBLabs + Cobalt + InfoSec + Medium): JSONP whitelist, eksik `base-uri`, dangling markup, `strict-dynamic` gadget zinciri → CoreMusic politikasında `base-uri 'self'`/`form-action 'self'` zaten var (✅ IMPLEMENTED), JSONP/gadget denetimi + `object-src 'none'` PLANNED, rapor-only fazında dangling markup için `img-src`/`connect-src` daraltması izlenir. 6) **Tarayıcı desteği** (content-security-policy.com + NDSS + SO): `strict-dynamic` modern her yerde; eski tarayıcı fallback'i (`https:` vs `'self'`) bilinçli ve daraltılabilir → 2025-26 uyum notu §2.2e. 7) **Nonce vs hash:** dinamik sayfada nonce, statik inline için hash opsiyonu (OWASP + Invicti) → kararın "nonce yoksa hash opsiyonu" notu destekli. |
| Web Search **Alınan Karar** | **ADR-012 kabul edilir — DÖRT KATMANLI CSP KARARI:** **(A) Strateji:** birincil `nonce + 'strict-dynamic'`; `unsafe-inline`/`unsafe-eval` **kalıcı kapalı** (önemli sayfada yalnız nonce/hash); **kademeli devreye alma: `Content-Security-Policy-Report-Only` önce** → `report-to` + `Reporting-Endpoints` (modern uç) + `report-uri` (fallback) + noir raporları toplanır → **ihlal eşiği** altında kalınca enforce; OWASP ikiz başlık deseni (strict report-only + mevcut enforce) geçişte birlikte çalışır. **(B) Tek politika, domain listeli:** tüm CoreMusic domain'leri (ADR-004, 7 subdomain) **aynı** politika şablonunu kullanır; `connect-src`/`img-src`/`frame-src` ortak domain listeleri + `https://*.coremusic.net` biçimli domain-scope varyant; API fetch'leri `connect-src` içinde. **(C) Nonce middleware'de:** `SecurityHeadersMiddleware` (veya bu ADR ile adlandırılan CSP middleware'i) per-request **CSPRNG 256-bit** nonce üretir → template/meta'ya enjekte edilir (IMPLEMENTED zincir korunur; inline script/style = nonce, statik inline = hash opsiyonu). **(D) Fallback disiplini:** `strict-dynamic` ile parent/child zinciri yönetilir; eski tarayıcı için `'self'`/`https:` fallback **daraltılır ve yazılır** (2025-26 notu: CSP3 her yerde, fallback yüzeyi kasıtlı olarak dar tutulur). §2.2f'te işaretlenen **iddia-kod çelişkileri** (base64/hex iddiası, auth ölü politikası) düzeltmesi debate (⏳ PENDING) şartı olarak girer. |
| Web Search **Sonuç** | Karar 2024-2026 verisiyle **desteklendi ve netleşti**: Strict CSP (OWASP, Google/secappdev, Invicti), bypass literatürü (5 kaynak: PortSwigger, BBLabs, Cobalt, InfoSec Writeups, Medium), report-only→enforce geçişi (4 kaynak: Microsoft Learn, Netlify, tech-insider, DCHost), raporlama başlıkları (3 kaynak: MDN, CentralCSP, Chrome Developers), tarayıcı desteği (3 kaynak: content-security-policy.com, NDSS DiffCSP, Stack Overflow) — **toplam ~19 adlandırılmış kaynak, 4 sorgu**; çapraz doğrulama ≥2 kaynak tüm güvenlik iddialarında karşılanır. Kod bulguları §1.1'de IMPLEMENTED/PLANNED olarak ayrıldı: CSP + nonce zinciri **IMPLEMENTED**, report-only uçları + domain listesi + `object-src` + auth politikası **PLANNED** yazıldı, uydurulmadı. `⚠️ VERIFICATION REQUIRED` yalnız (i) auth SecurityHeadersMiddleware pipeline kaydı/ölü kod şüphesi, (ii) `ScriptInjector.js` canlı varlık durumu ("js copy" klasörü), (iii) `report-to` uç noktası/noir entegrasyonunun mevcut altyapıda karşılığı için korunur. **Kaynak listesi (19):** 1) OWASP CSP Cheat Sheet · 2) Google/secappdev — From Zero to Hero with CSP (Philippe Deryck) · 3) content-security-policy.com — strict-dynamic (destek tablosu) · 4) MDN — CSP rehberi · 5) MDN — `report-uri` · 6) Chrome for Developers — Reporting API · 7) CentralCSP — report-uri/report-to durumu · 8) PortSwigger Research — dangling markup · 9) BBLabs — CSP bypass arsenal (JSONP/base-uri/AngularJS) · 10) Cobalt — CSP and Bypasses · 11) InfoSec Writeups — Bug Hunter's Guide to CSP Bypasses · 12) Medium — Breaking CSPs (strict-dynamic dar istisna) · 13) Netlify Blog — CSP nonce entegrasyonu + kademeli dağıtım · 14) Microsoft Learn — Power Pages CSP (report-only → enforce) · 15) tech-insider 2026 — Set Up CSP · 16) DCHost — Nonces/Hashes/Report-To · 17) Invicti — Dangers of Incorrect CSP · 18) NDSS 2023 — DiffCSP (tarayıcı tutarsızlıkları) · 19) Stack Overflow — strict-dynamic + unsafe-inline fallback tartışması. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-004 domain haritası (7 subdomain) | Tek politika bu domain listesinin üzerinde yaşar; `config/domain.php` domain listesi onaysız değişmez (shared Yasak #4) — politika domain listeleri bu haritadan türetilir |
| ADR-009 sınırı (HSTS) | HSTS `shared/src/Middleware/SecurityHeadersMiddleware.php`'de hâlâ PLANNED (ADR-009 §1.1); **bu ADR HSTS'yi çözmez** — header eklemek ayrı iş/uygulama adımıdır |
| ADR-010 XSS köprüsü (bağlayıcı) | "XSS tüm CSRF savunmalarını yok eder" → bu ADR'nin CSP'si o köprünün kapatıcısı; CSP, CSRF token/zincirinin yerine geçmez, üstüne katmanıdır |
| ADR-011 nonce saklama | Nonce `$_SESSION['csp_nonce']` içinde oturumla taşınır; oturum rotasyonu/invalidasyonu (ADR-011) nonce yaşam döngüsünü de etkiler — middleware sırası (#4 SecurityHeaders → #5 SessionManager) DEĞİŞTİRİLEMEZ |
| Frozen ADR-001-037 dokunulmaz | Bu ADR yeni karar üretir; frozen metinler okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2) |
| REDACTED | Secret/credential/anahtar hiçbir koşulda bu ADR'ye yazılmaz |
| In-Place Refactoring | Dosya adı onay olmadan değiştirilemez; mevcut `SecurityHeadersMiddleware` adı korunur (yeni CSP middleware'i önerilirse ayrı onay) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |

---

## 2. Karar (Decision)

CoreMusic CSP'si **tek politika, domain listeli** olarak kabul edilir: **(A)** birincil strateji `nonce + 'strict-dynamic'`; `unsafe-inline`/`unsafe-eval` **kalıcı kapalı**; **(B)** kademeli devreye alma — **report-only önce** → raporlar (`report-to`/`Reporting-Endpoints` + `report-uri` fallback + noir) toplanır → ihlal eşiği altında kalınca **enforce**; **(C)** nonce per-request CSPRNG ile **middleware'de** üretilir ve template'e enjekte edilir; **(D)** `strict-dynamic` parent/child zinciri + eski tarayıcı `'self'` fallback'ine 2025-26 uyum notuyla dikkat edilir. §1.1'de işaretlenen iddia-kod çelişkilerinin düzeltmesi §2.2f'te debate şartı olarak girmiştir (debate ✅ KABUL — §7.1).

### 2.1 Neden Bu Seçenek?

- **XSS yüzeyi kapanır:** nonce'suz inline script çalışamaz; `strict-dynamic` ile yükleyici zinciri disipline edilir — ADR-010/011'in devraldığı "XSS her şeyi çökertir" riskinin ikinci kalkanı (§1.3-1: OWASP + Google strict CSP).
- **Kod bugün zaten doğru yönde:** shared CSP + nonce zinciri IMPLEMENTED (§1.1) — karar bu temeli **tamamlar** (tek politika, raporlama, fallback disiplini), yıkmaz; sıfırdan yazım YAGNI olurdu.
- **Kademeli geçiş = kanıtlanmış pratik:** report-only → enforce 4 bağımsız kaynakla (§1.3-3); anında sıkılaştırma kullanıcı kırılması üretir, rapor döngüsü false positive'i enforce öncesi ayıklar.
- **Tek politika = ADR-004'ün zorunluluğu:** 7 subdomain'de farklı politikalar (bugünkü shared/auth ikiliği) hem tutarsızlık hem bakış açısıdır; tek şablon + domain listeleri ihlal raporlarını anlamlı kılar.
- **Kalıcı `unsafe-*` yasağı:** geçici `unsafe-inline` asla geri gelmez (auth ölü kodundaki satır dahil silinir) — "kolay geçiş için unsafe ekle, sonra unut" tuzağı kapatılır (§1.3-2/3: OWASP + Netlify default'larının aksine bilinçli seçim).
- **Zero Hallucination (ADR-005):** her madde dosya yoluyla (IMPLEMENTED/PLANNED) veya §1.3 kaynağıyla kanıtlandı; doğrulanamayanlar `⚠️ VERIFICATION REQUIRED`.

### 2.2 Teknik Detaylar

#### 2.2a Tek Politika Şablonu (tüm domainler — ADR-004)

```text
default-src 'self';
script-src 'strict-dynamic' 'nonce-{CSPRNG256}' 'self' https:;   ; eski tarayıcı fallback'i §2.2e
style-src  'nonce-{CSPRNG256}' 'self' {assets} fonts.googleapis.com;
img-src    'self' data: {assets} {domain-list};
font-src   'self' {assets} fonts.gstatic.com;
connect-src 'self' {assets} {api-domain-list} https://*.coremusic.net;   ; API fetch'leri burada (PLANNED genişletme)
media-src  'self' {assets};
frame-src  {domain-list};
frame-ancestors 'none';
base-uri   'self';
form-action 'self';
object-src 'none';                    ; ek sıkılaştırma (bugün default-src 'self' fallback'i devrede — PLANNED)
upgrade-insecure-requests;            ; PLANNED (HTTPS-öncelikli, ADR-009 ile hizalı)
```

- **Ortak + domain-scope varyant:** `connect-src`/`img-src`/`frame-src` listeleri **ortaktır** (tek şablon) + `https://*.coremusic.net` biçimli domain-scope varyant ile subdomain içi kaynaklar; her subdomain yalnız `{domain-list}`/origin farkıyla aynı şablonu kullanır (kopya politika yasağı).
- **Tek kaynak:** şablon tek bir yerde (§2.2c middleware) üretilir; subdomain'lere elle header yazmak yasak.

#### 2.2b Nonce & Hash

| # | Madde | Değer | Durum |
|---|-------|-------|-------|
| 1 | Nonce üretimi | Per-request CSPRNG **256-bit** (`bin2hex(random_bytes(32))`) — `SecurityHeadersMiddleware` içinde, SessionManager'dan önce | **IMPLEMENTED** (`SecurityHeadersMiddleware.php:16-19`) — korunur |
| 2 | Nonce zinciri | Request `_csp_nonce` → `$_SESSION['csp_nonce']` → `<meta name="csp-nonce">` + `<script nonce=...>` enjeksiyonu | **IMPLEMENTED** (SessionManagerMiddleware:18/20, SessionInitializer:46, SessionLifecycle:52, HtmlShellRenderer:58/108) |
| 3 | Template enjeksiyonu | Inline script/style = `nonce` attr; **statik inline** (değişmeyen satır içi blok) = **hash opsiyonu** (SHA-256, OWASP: hash içerik değişince kırılır → yalnız sabit blok) | **PLANNED** (hash opsiyonu kodda yok) |
| 4 | Middleware sorumluluğu | Nonce üretimi + politika builder **tek middleware'de** (`SecurityHeadersMiddleware` veya onunla adlandırılan CSP middleware'i — ad değişikliği In-Place kuralı gereği onay ister) | **IMPLEMENTED** (üretim) / **PLANNED** (rapor başlıkları) |
| 5 | CDN/üçüncü parti script | `script-src` domain allowlist'i yok (`strict-dynamic` nedeniyle modern tarayıcıda zaten yok sayılır); harici köprü script'ler **nonce + `crossorigin` + SRI** ile | **PLANNED** (§4.3 risk 3) |
| 6 | Yoksayma | `unsafe-inline`/`unsafe-eval` politika metninde **asla yazılmaz**; auth'daki `style-src 'self' 'unsafe-inline'` satırı kaldırılır | **PLANNED** (§2.2f şart) |

#### 2.2c Nonce Middleware'de — Mekanik (ADR-004/009 PLANNED satırlarıyla uyum)

```text
[İstek] → SecurityHeadersMiddleware
            ├─ 1) nonce = bin2hex(random_bytes(32))   → $request['_csp_nonce']   (IMPLEMENTED)
            ├─ 2) $next($request)                      (SessionManager nonce'u oturuma kaydeder)
            └─ 3) header('Content-Security-Policy(-Report-Only): ' + buildCsp(nonce))
                     ├─ Aşama R (PLANNED): Report-Only + report-to + Reporting-Endpoints + report-uri
                     └─ Aşama E: enforce (bugünkü başlık korunur)
[Şablon] → HtmlShellRenderer: <meta name="csp-nonce"> + <script/Style nonce="..."> (IMPLEMENTED)
```

- **Geçiş deseni (OWASP ikiz başlık):** `Content-Security-Policy-Report-Only` (yeni tam politika) **+** mevcut `Content-Security-Policy` (temel) aynı yanıtta birlikte gönderilebilir — report-only'de sertleşirken temel enforce bozulmaz; eşik dolunca ikiz kapatılır.

#### 2.2d Kademeli Devreye Alma (report-only → enforce)

| Aşama | Ne | Kapı |
|-------|----|------|
| **R1 — Toplama** | `Content-Security-Policy-Report-Only` + `report-to` + `Reporting-Endpoints` (modern) + `report-uri` (eski fallback); noir raporları + tarayıcı konsolu | İhlal uçları kodda yoksa **önce uçlar yazılır** (PLANNED) |
| **R2 — Triage** | İhlal JSON'ları analiz: `blocked-uri`, `violated-directive`; false positive (ör. CDN geçişi) politikaya, gerçek ihlal koda düzeltilir | Kritik ihlal kalmadan geç yok |
| **E — Enforce** | Report-Only başlığı kapatılır, tek enforce politika; **enforce öncesi eşik** (debate şart 2): ≥14 gün (2 hafta) report-only temiz pencere + 0 ihlal + noir temiz | **Şart:** §4.3 risk 1 · §5.3 şart 2 |

#### 2.2e Fallback & 2025-26 Uyum Notu (eski tarayıcı)

- `strict-dynamic` **CSP3'tür** (Chrome 52+/Edge 79+/Firefox 52+/Safari 15.4+; IE yok — §1.3 kaynak 3); CSP3 tarayıcıda URL-tabanlılar/`'self'` **yok sayılır** → fallback yalnız **eski** tarayıcıda devreye girer.
- Bugünkü kod: `script-src 'strict-dynamic' 'nonce-...' https:` (satır 54) → CSP2 tarayıcısında **her HTTPS script** geçerli. Karar: fallback **daraltılır** — `'self'` + gerekli minimum origin'ler (`{assets}`); `https:` genişliği bilinçli olarak yeniden değerlendirilir (**debate şartı 2**).
- NDSS notu: Safari/Firefox `default-src`'te nonce/hash/strict-dynamic desteklemez → fallback ifadeleri **`script-src`'te** tutulur (mevcut yapı ✅), `default-src`'e taşınmaz.

#### 2.2f İddia-Kod Çelişkileri (işaretli — düzeltme debate şartı)

| # | Çelişki | Kanıt | Etiket |
|---|---------|-------|-------|
| 1 | `Middleware/CLAUDE.md:95` "nonce-**{base64}**" ↔ kod **hex** (`bin2hex`) | `SecurityHeadersMiddleware.php:18` | İddia-kod biçimsel çelişki — **debate şartı 1b ✅ düzeltildi (2026-09-24)** (ADR-009'da HSTS PLANNED'e düzeltilirken bu satıra dokunulmadı) |
| 2 | auth ikinci CSP: `style-src ... 'unsafe-inline'` + `$request['server']['csp_nonce']` hiç yazılmıyor + sınıf kaydı yok | `auth.coremusic.net/include/Middleware/SecurityHeadersMiddleware.php:25,29-31`; grep (0 yazma, 0 örneklem) | Çelişki + ölü kod şüphesi ⚠️ VERIFICATION REQUIRED — **debate şartı 1a** (belge notu eklendi 2026-09-24; kod değişikliği Backend Architect işi) |
| 3 | Report-only iddiası (`shared` doküman hedefi) kodda yok | grep report-* → src içinde 0 | PLANNED (zaten §2.2d) — şart değil, adım |
| 4 | "CSP var mı kodda?" | **EVET — IMPLEMENTED** (`Content-Security-Policy` satır 31, enforce) | HSTS'nin aksine iddia-kod **uyumlu**; yalnız 1-2 numaralı küçük çelişkiler açık |

> **Düzeltme tartışmaya şart olarak girer:** `Middleware/CLAUDE.md` CSP satırlarının (base64/hex) ve auth ölü politikasının düzeltilmesi bu ADR'nin debate şartı 1'idir (✅ KABUL 2026-09-24 — §7.1); 1b hex ✅ uygulandı, 1a (auth tek middleware) + 1c (object-src) kapanmadan §5.1 adım 5 kapatılmaz.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Mevcut durumu koru** (parçalı: shared enforce + auth ölü kod, raporlama yok, domain listesi yok) | Sıfır iş gücü; shared CSP zaten çalışıyor | 7 subdomain'de tutarsız; ihlaller görünmez (rapor yok); auth'da başlık yok; `unsafe-inline` metni diskte duruyor | §1.2 maddeleri 1-4 doğrudan reddetme gerekçesi; ADR-004/010/011 köprüleri açık kalır |
| 2 | **Yalnız hash tabanlı CSP** (nonce yok) | Statik içerikte kolay; nonce sızıntısı yüzeyi yok | Dinamik sayfalarda her inline blok hash taşır; içerik değişince kırılır (formatting/whitespace); oturum bağlamalı nonce elde edilmez | CoreMusic dinamik/oturumlu sayfalar (login/register/home) üretiyor → OWASP: hash sabit bloklar için; nonce dinamik için (§1.3-1) |
| 3 | **Geçici `unsafe-inline` + sonra sıkılaştırma** | En kolay geçiş; ilk gün kırılmaz | "Sonra unut" tuzağı; `unsafe-inline` nonce'u CSP3'te zaten yok sayar → kazanım yok, risk var; kalıcı hale gelirse tüm CSP anlamsız | Kararın **kalıcı kapalı** ilkesine aykırı; literatür `unsafe-*`'i Strict CSP'nin düşmanı sayar (OWASP/Invicti) |
| 4 | **CSP'siz savunma** (yalnız output encoding + Trusted Types + XFO) | Politika karmaşıklığı yok | XSS'in çalışma zamanı etkisi ölçülmez; ADR-010 "XSS her şeyi çökertir" köprüsü açık kalır; derin savunma tek katmana iner | Tek katman yetersiz; kodda zaten CSP+Trusted Types başlığı var → savmak anlamsız |
| 5 | **Domain allowlist'li `script-src` (strict-dynamic'siz nonce)** | Daha dar, eski tarayıcıda tutarlı | Yükleyici/dinamik import disiplini yok; her alan adı tek tek bakımı; JSONP whitelist yüzeyi büyür (bypass literatürü §1.3-2) | `strict-dynamic` zinciri bakım yükünü azaltırken güveni yükseltir (OWASP/Google); allowlist yüzeyi bypass literatürünün ana hedefi |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **XSS ikinci kalkanı:** nonce'suz inline script çalıştıramaz; `strict-dynamic` yükleyici zinciri disipline edilir — ADR-010 (CSRF) ve ADR-011 (oturum) köprüleri kapanır.
- **Tek politika = görünür güvenlik:** 7 subdomain aynı şablonla yaşar; ihlal raporları domain'ler arası karşılaştırılabilir olur.
- **Erken bozulma yakalanır:** report-only fazı, enforce'den önce CDN/üçüncü parti kırılmalarını raporlar (4 kaynaklı pratik §1.3-3).
- **Kalıcı disiplin:** `unsafe-inline`/`unsafe-eval` yasağı geri dönülemez yazılır; auth ölü politikası temizlenir.
- **Mevcut iyi temel korunur:** nonce üretimi/zinciri/`base-uri`/`form-action`/`frame-ancestors`/Trusted Types başlığı IMPLEMENTED ve aynen devam eder.
- **Denetlenebilirlik:** rapor uçları + noir ile CSP etkisi ölçülebilir hale gelir (ADR-005 kanıt disiplini).

### 4.2 Olumsuz Sonuçlar

- **Geliştirme sürtünmesi:** her yeni inline script/style ya nonce alır ya hash'e taşınır; eski habit (inline onclick) kırılır (`assets` footer.init.js:92 zaten bu yolda).
- **Geçiş iş yükü:** rapor ucu + endpoint + triage döngüsü kurulmadan enforce'e geçmek mümkün değil (2 ek oturum).
- **Eski tarayıcı riski:** `strict-dynamic`'in fallback'i daraltılırsa çok eski tarayıcıda script kırılabilir (kullanıcı kaybı riski — rapor-only fazı ölçer).
- **Ölü kod temizliği riski:** auth `SecurityHeadersMiddleware` silinirken HSTS/x-frame başlıklarının **canlı** bir yere taşıdığı doğrulanmazsa auth sayfaları korumasız kalabilir (⚠️ doğrulama zorunlu).
- **Rapor gürültüsü:** false positive'ler enforce'i yanlışlıkla tetikler/erteler → eşik disiplini şart (risk 1).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Report-only'de false positive → erken enforce, sayfa kırılması** | 3 (olası) | 3 (orta) | **Enforce öncesi eşik (debate şart 2):** ≥14 gün (2 hafta) report-only temiz pencere + 0 ihlal + noir temiz (§2.2d E); OWASP ikiz başlık ile temel enforce bozulmadan sertleşme |
| **`strict-dynamic` fallback (`https:`) eski tarayıcıda geniş yüzey** | 2 (mümkün) | 4 (yüksek) | Fallback `'self'` + minimum origin'lere daraltılır (§2.2e, debate şartı 2); CSP3 desteği 2025-26'da yaygın → eski kuyruk ölçülür (raporlar) |
| **CDN/üçüncü parti varlık nonce'suz → script engellenir (veya JSONP gadget whitelist'e girer)** | 3 (olası) | 3 (orta) | Harici script = nonce + `crossorigin` + SRI; whitelist domain'lerinde JSONP/gadget denetimi (§1.3-2); GTM/analytics entegrasyonu report-only fazında test edilir (Netlify notu §1.3-3) |
| **Tek politika geçişinde subdomain kırılması** (API fetch'ler `connect-src` dışı) | 3 (olası) | 3 (orta) | Domain listeleri ADR-004'ten türetilir; R1 fazı zaten bunu raporlar → enforce öncesi kapatılır |
| **Nonce sızıntısı / dangling markup** (XSS nonce'yu okur; nonce script'siz veri sızdırmasını durdurmaz) | 3 (olası) | 4 (yüksek) | Nonce tek savunma değildir: `base-uri`/`form-action 'self'` (IMPLEMENTED) + `img-src`/`connect-src` daraltma + Trusted Types başlığı + ADR-010 token katmanı; rapor fazında `blocked-uri` izlenir |
| **auth ölü kod temizliği header boşluğu yaratır** (sınıf kayıtlı değil ama başka katman header basıyor olabilir) | 2 (mümkün) | 3 (orta) | ⚠️ VERIFICATION REQUIRED: AuthContainer/pipeline + sunucu-config header taraması → sonra tek politikaya bağlanır (§5.1 adım 2) |

### 4.4 Vault Çapraz Referans

| ADR | İlişki |
|-----|--------|
| [[ADR-004-multi-domain-spa]] | 7 subdomain domain haritası — tek CSP'nin domain listeleri bu haritadan türetilir (`connect-src`/`img-src`/`frame-src` + `https://*.coremusic.net` varyantı) |
| [[ADR-009-clean-url-redirect]] | HSTS PLANNED düzeltmesinin yapıldığı dosya — bu ADR §1.1'de Middleware/CLAUDE.md **CSP** iddialarını ayrıca denetledi; HSTS bu kararın **kapsam dışı** (§1.4 sınır) |
| [[ADR-010-csrf-protection-strategy]] | **XSS↔token köprüsü:** "XSS tüm CSRF savunmalarını yok eder" → bu ADR CSP'si o köprünün kapatıcısı; `form-action 'self'` ortak katman |
| [[ADR-011-session-management]] | Nonce `$_SESSION['csp_nonce']` içinde oturumla taşınır; oturum rotasyonu nonce yaşam döngüsünü etkiler; ADR-011 risk 1/4 bu ADR ile kapatılır |
| [[ADR-005-ultrathink-protocol]] | Zero Hallucination: IMPLEMENTED/PLANNED etiketleri + `⚠️ VERIFICATION REQUIRED` disiplini bu ADR'nin kanıt standardı |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Tek politika şablonu: `buildCsp()`'ye domain listeleri (`connect-src` API + `https://*.coremusic.net`, `img-src`/`frame-src` ortak) + `object-src 'none'` + `upgrade-insecure-requests` (PLANNED satırlar) — ADR-004'ten türet | Security Engineer + Backend Architect | 1-2 oturum |
| 2 | **auth ikinci politika temizliği:** ölü `SecurityHeadersMiddleware` (kayıt/⚠️ doğrulama) → ortak CSP hattına bağla; `style-src 'unsafe-inline'` metnini kaldır; HSTS başlığının canlı katmanını doğrula | Security Engineer | 1 oturum |
| 3 | Raporlama döngüsü: `Content-Security-Policy-Report-Only` + `report-to` + `Reporting-Endpoints` + `report-uri` fallback + rapor ucu + noir entegrasyonu → **R1/R2 → Eşik → enforce** (§2.2d) | Security Engineer + DevOps Engineer | 2 oturum |
| 4 | Nonce/zincir teyidi + hash opsiyonu (statik inline) + harici script `crossorigin`/SRI kuralı; `ScriptInjector.js` canlı varlık doğrulaması (⚠️) | Backend Architect + UI Designer | 1 oturum |
| 5 | **İddia-kod düzeltmeleri:** `Middleware/CLAUDE.md:95` base64→hex ✅ (2026-09-24 — şart 1b) + auth politika kaydı (şart 1a) — satır 94 `base64_encode` ifadesi ⚠️ VERIFICATION REQUIRED (§2.2f) | Vault Steward | 0.5 oturum |
| 6 | Testler: header↔template nonce eşleşmesi, strict-dynamic zincir (yükleyici→çocuk), report-only rapor akışı, `unsafe-*` kalıcılık testi (politikada geçmez), auth başlık teyidi | QA Engineer | 1-2 oturum |
| 7 | Debate ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — §7.1) + Tech Lead onayı ✅ → Arch Lead akışı (⏳) | Vault Steward | 2026-09-24 |

### 5.2 Geri Dönüş Planı

1. **Adım 3 (raporlama):** `Content-Security-Policy-Report-Only` + `report-*` başlıkları kaldırılır → mevcut enforce politikasına (bugünkü satır 31) tam dönüş; sayfa davranışı değişmez (enforce zaten vardı).
2. **Adım 1 (domain listesi):** `buildCsp()` eski haline `git revert` → listeler kalkar; R fazı zaten kapanmışsa etki yok, enforce sürer.
3. **Adım 2 (auth):** ortak hattan auth geri alınabilir (eski ölü kod `git checkout` ile döner) — ama `unsafe-inline` metni **geri yazılmaz** (kalıcı yasak); doğrulanmamış header kaybı için sunucu-config katmanı yedektir.
4. **Adım 4-6:** test/hash opsiyonu geri alınabilir; davranışsal etkisi yoktur.
5. **Acil:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/` ile vault bütünlüğü doğrulanır; bozulma → `vault-utf8-writer.mjs repair` + `git checkout`.

### 5.3 Debate Şartları (✅ BAĞLAYICI — debate KABUL 2026-09-24)

| # | Şart | Bağlantı | Durum |
|---|------|----------|-------|
| 1 | **İddia-kod düzeltmeleri — 1a tek middleware · 1b hex · 1c object-src:** (1a) auth ölü ikinci middleware → tek kaynak `shared/src/Middleware/SecurityHeadersMiddleware.php`, instance kaldır/düzelt + `style-src 'unsafe-inline'` metni silinir; (1b) `Middleware/CLAUDE.md:95` base64↔hex → `bin2hex(random_bytes(32))` lehine; (1c) `object-src 'none'` → politika tamamlama | §1.1 · §2.2f · §5.1 #1-2, #5 | **✅ BAĞLAYICI — 1b UYGULANDI (2026-09-24, satır 94 `base64_encode` ⚠️); 1a belge notu eklendi / kod AÇIK; 1c AÇIK** |
| 2 | **Report-only geçiş eşiği + report-to:** report-only **2 hafta (14 gün)** → **0 ihlal** → enforce; `report-to` + `Reporting-Endpoints` (+ `report-uri` fallback); eski tarayıcı fallback kapsamı (`'self'` + minimum origin — §2.2e) bu eşikle birlikte kapatılır | §2.2d · §2.2e · §4.3 risk 1-2 | **✅ BAĞLAYICI (AÇIK — §5.1 #3)** |
| 3 | **strict-dynamic + statik asset nonce/hash testi:** loader→çocuk zinciri testi + statik inline/varlık için nonce↔hash davranışı doğrulaması | §2.2b · §5.1 #6 | **✅ BAĞLAYICI (AÇIK — §5.1 #6)** |

*Bu 3 madde debate sonucu **bağlayıcı** şartlardır (18/2/0 KABUL — §7.1); Tech Lead onayı ✅ (2026-09-24). Şart 1b uygulandı; 1a/1c, 2 ve 3 kapanmadan §5.1 adım 3'ün "E" aşaması prod'a girmez. Durum `accepted` (kullanılabilir); frozen YOK.*

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — §3 satır 49 `[[ADR-012-csp-nonce-strict-dynamic]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (CSP/XSS → Security Engineer) |
| [[../../WORKFLOW.md]] | Debate/onay akışı bağlamı |
| [[../../brain.md]] | Mimari karar özeti (bu ADR'den türetilir) |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| [[ADR-004-multi-domain-spa]] | 7 subdomain domain haritası — tek politika/domain listeleri bu haritada yaşar (dosya diskte VAR ✅) |
| [[ADR-005-ultrathink-protocol]] | Zero Hallucination — IMPLEMENTED/PLANNED/⚠️ kanıt standardı (dosya diskte VAR ✅) |
| [[ADR-009-clean-url-redirect]] | HSTS PLANNED düzeltmesi — CSP iddialarının çapraz denetim referansı; HSTS kapsam dışı (dosya diskte VAR ✅) |
| [[ADR-010-csrf-protection-strategy]] | XSS↔token köprüsü — bu ADR'nin hedefi; `form-action 'self'` ortak katman (dosya diskte VAR ✅) |
| [[ADR-011-session-management]] | `$_SESSION['csp_nonce']` saklama + middleware sırası (#4→#5) (dosya diskte VAR ✅) |
| `shared/src/Middleware/SecurityHeadersMiddleware.php` | Nonce üretimi (16-19) + enforce CSP (31, 48-72) — IMPLEMENTED çekirdek |
| `shared/src/Middleware/CLAUDE.md` | CSP iddiaları (21/42/58/95/97/144) — 95. satır base64/hex düzeltildi (şart 1b ✅ 2026-09-24) |
| `shared/src/Middleware/SessionManagerMiddleware.php` · `shared/src/PageRouter/HtmlShellRenderer.php` | Nonce oturum kaydı (18/20) + meta/script enjeksiyonu (58/108) |
| `auth.coremusic.net/include/Middleware/SecurityHeadersMiddleware.php` | Çelişkili/ölü ikinci politika (25, 29-31) — §2.2f şart 1a (belge notu eklendi 2026-09-24) |
| `shared/src/PageRouter/PageRouterKernel.php` | Middleware kaydı (276) — canlı zincir kanıtı |
| `shared/tests/Middleware/SessionManagerMiddlewareTest.php` · `MiddlewarePipelineTest.php` | Nonce zinciri + sıra testleri (82-130; 225-227) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte VAR ✅, v7.2.0) |
| §5.3 · §7.1 debate kaydı | **✅ KABUL (2026-09-24)** — 3 bağlayıcı şart (iddia-kod düzeltmeleri [hex · tek middleware · object-src] · report-only geçiş eşiği + report-to · strict-dynamic + statik asset nonce/hash testi) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-012'yi sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate 3/20 KABUL onayı | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/ADR-008/ADR-010/ADR-011 formatı — 3 tur / 20 persona (tamamlandı) |
| Durum | **✅ KABUL — 2026-09-24** |
| Tur 1 | 20 persona: CSP kodda enforce olarak doğrulandı (`SecurityHeadersMiddleware.php:16-19` nonce 256-bit, `:31` enforce, `:48-72` strict-dynamic); 2 çelişki — (i) `Middleware/CLAUDE.md:95` base64↔hex, (ii) auth subdomain ölü ikinci middleware (`server.csp_nonce` hiç yazılmıyor + `style-src 'unsafe-inline'` ihlali); dağılım: 15 kabul/neutral · 4 uyarı · 1 Critic (object-src 'none' eksik + iki politika yarışı) |
| Tur 2 (İtiraz→Çözüm) | (1) ölü auth middleware → tek kaynak shared SecurityHeaders, instance kaldır/düzelt → **şart 1a**; (2) `CLAUDE.md` base64↔hex düzeltme → **şart 1b**; (3) `object-src 'none'` politika tamamlama → **şart 1c**; (4) enforce eşiği → report-only 2 hafta → 0 ihlal → enforce + `report-to`/`Reporting-Endpoints` → **şart 2** |
| Tur 3 (Oy) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL** — 3 bağlayıcı şart §5.3'e işlendi |
| Tech Lead | ✅ 2026-09-24 (debate KABUL sonrası — §7 onay tablosu) |
| Bağlayıcı şartlar | 3 madde → §5.3 (iddia-kod düzeltmeleri [hex · tek middleware · object-src] · report-only geçiş eşiği + report-to · strict-dynamic + statik asset nonce/hash testi) |
| Not | Frontmatter + §7 + §5.3 birlikte güncellendi; kayıt `log.md` append ile alındı (Arch Lead ⏳ devam eder) |

---

*1.0.0 | 2026-09-24 | Created*
*Authority: ADR-012 Karar Metni — CoreMusic Architecture Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
