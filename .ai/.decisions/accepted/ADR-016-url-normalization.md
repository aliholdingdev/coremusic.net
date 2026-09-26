---
title: "CoreMusic — ADR-016: URL Normalization (Path+Query Tam Normalizasyon · RequestNormalizer Genişletme · Normalized Path = Routing Tek Girdisi · Bypass Red + ERROR)"
type: adr
category: routing
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-016 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-016: URL Normalization (Path+Query Tam Normalizasyon · RequestNormalizer Genişletme · Normalized Path = Routing Tek Girdisi · Bypass Red + ERROR)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-016'yı sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı/Önerilen) · debate: ✅ TAMAMLANDI (3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-009-clean-url-redirect]] (host/şema/slash/uzantı **sınırı** — bu ADR yalnız path+query; dosya diskte VAR ✅) · [[ADR-002-pdo-mandatory-no-orm]] (ham input asla SQL'e/routing'e gitmez + bağımlılıksız yazım; dosya diskte VAR ✅) · [[ADR-004-multi-domain-spa]] (RouteRegistry/PageRouter yol haritası; dosya diskte VAR ✅) · [[ADR-013-rate-limiting-apcu]] (red edilen istekler rate-limit sayımı + ERROR alert ruhu; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (doğrulama disiplini + REDACTED — red log'unda query değeri asla; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 53** `[[ADR-016-url-normalization]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in SPA router'ı (`shared/src/PageRouter/`) isteği `RequestNormalizer` üzerinden geçirir, ama normalizasyon yüzeyi **dar ve sessizdir**: yalnız `index.php` front-controller öneki, baştaki slash'lar, host/şema/port ve `FILTER_SANITIZE_URL` temizliği (§1.1). Path case, percent-encoding katmanları, Unicode NFC, dot-segment, path içi çift-slash ve query kanonikleştirme **hiç işlenmez**; bypass denemeleri (çift-encoding, `%2e%2e`, `/..%2f`) sessizce geçer, **red + alert yoktur**. Bu ADR; **path+query tam normalizasyonunu** tek kapıda (mevcut `RequestNormalizer`) toplar, **normalized path'i routing'in tek girdisi** kılar ve şüpheli kalıpları **RED + ERROR** ile durdurur. Kapsam sınırı bağlayıcıdır: **host/şema/slash/uzantı = ADR-009; path/query encoding/Unicode = bu ADR** (ADR-009 satır 27-29).

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

- **`shared/src/PageRouter/RequestNormalizer.php` — 118 satır, mevcut normalizasyon yüzeyi (IMPLEMENTED):**
  - host ayrıştırma + sanitize + `'localhost'` fallback: **satır 20-24** (`parse_url('http://'.$host)`, `FILTER_SANITIZE_FULL_SPECIAL_CHARS`) — **host lowercase YOK** (ADR-009'un konusu, bu ADR'ye girmez).
  - scheme tespiti (`X-Forwarded-Proto` ilk değer, `HTTPS`): **satır 26, 60-79**.
  - port 80↔443 eşlemesi: **satır 28-33**.
  - `index.php` front-controller öneki temizliği: **satır 36-38** (`preg_replace('#^(/index\.php)+#', '')`).
  - **yalnız BAŞTAKİ** slash tekilleme: **satır 39-41** (`preg_replace('#^/+#', '/')`) — **path içi `//` korunur** (`/a//b` değişmez).
  - `FILTER_SANITIZE_URL`: **satır 42** — geçersiz karakterleri siler, **percent-decode ETMEZ** (`%7E`, `%2f`, `%2e` aynen kalır).
  - path/query ayrımı: **satır 44-45** (`parse_url(...PHP_URL_PATH / PHP_URL_QUERY)`).
  - global geri yazımı (`REQUEST_URI`, `QUERY_STRING`, `PATH_INFO`, `REQUEST_PATH`, `PHP_SELF`, `SERVER_PORT`, `REQUEST_SCHEME`): **satır 81-99**.
  - `X-Forwarded-For` ilk IP → `REMOTE_ADDR`: **satır 94-99**.
- **YOK → PLANNED (aynı dosyada, bu ADR §2.2 ile doldurulacak):** (1) **path case folding** (satır 44 yalnız ayrıştırır, path'te case hiç değişmez; host'ta da lowercase yok — ADR-009 satır 40 bu maddeyi ADR-016'ya devretti), (2) **percent-encoding tekilleştirme** (`%7E`→`~`, `%41`→`A`, hex büyük harf, çift-encoding katmanı — hiç işlenmiyor), (3) **Unicode NFC** (hiçbir Unicode işleme yok), (4) **dot-segment temizliği** (`/a/../b`, `%2e%2e`, `/..%2f` — hiç işlenmiyor; satır 37-41 yalnız `index.php` + baştaki slash), (5) **path içi çift-slash** (yalnız `^/+`), (6) **query tekilleştirme + sırasız kanonikleştirme** (satır 45-47, 87 ham), (7) **bypass RED + ERROR alert** (yok — her şey sessiz normalize).
- **Routing teyidi — `shared/src/PageRouter/PageRouterKernel.php` (IMPLEMENTED):** `normalizeRequest()` **satır 199** `$this->normalizer->normalize()` çağırır; `populateGlobals` `$_SERVER['REQUEST_URI']`'i normalize edilmiş URI ile **değiştirir** (RequestNormalizer satır 85); kernel **satır 202-204** bu URI'den `parse_url(PHP_URL_PATH)` + `trim('/')` alır, **satır 211-213** query'yi atar, **satır 216-223** request'i döndürür. Sonuç: **routing kısmen normalize edilmiş path ile çalışır** (`index.php` öneki + baştaki slash + sanitize APPLY olmuştur) — **ham path routing'e gitmez**; **ama percent-encoding, Unicode, dot-segment ve query hâlâ hamdır** ve `'query' => $get` (**satır 222**) PHP'nin ham parse'udur. Route meta enjeksiyonu **satır 110-111** (`trim($req['uri'],'/')` → `RouteRegistry::resolve`).
- **Route eşleştirmesi case-sensitive (IMPLEMENTED):** `shared/src/PageRouter/RouteRegistry.php` `resolve()` **satır 37-51** — `trim` + `isset($this->routes[$normalized])` (**satır 43**, case-sensitive) + `matchesPattern` fallback (**satır 47-51**); `strtolower` **hiç yok** (PageRouter klasöründe 3 eşleşme: kernel satır 231 header, normalizer satır 68/74 — path'te değil).
- **Route anahtarları lowercase (IMPLEMENTED kanıt):** `shared/config/routes.php` anahtarları `'login'` (**satır 26**), `'register'` (**satır 32**), `'logout'` (**satır 38**); dosyada büyük harfli route anahtarı grep = **0** → **path case folding routing'i kırmaz**, `/Kesfet` → `/kesfet` eşleşmesini sağlar (§2.2b şartı).
- **Normalizasyon testi YOK (PLANNED):** `shared/tests/Unit/PageRouter/` = 4 dosya (`SpaRouteTest`, `RouteRegistryTest`, `AuthUrlBuilderTest`, `AuthGuardTest`) — `RequestNormalizer`/normalization testi **0**.
- **`ext-intl` declare YOK (PLANNED):** `shared/composer.json`, `auth.coremusic.net/composer.json`, `home.coremusic.net/composer.json` içinde `intl`/**`ext-intl` eşleşmesi 0** → `Normalizer::normalize()` (NFC) için çalışma-zamanı kontrolü + fallback gerekir (§2.2d, §4.3 risk 4).
- **Bypass/red altyapısı kısmi (IMPLEMENTED + PLANNED):** `StructuredLogger` (kernel satır 30, 58, 87 — `traceId`li) ve `MiddlewarePipeline` (kernel satır 241-248) var → **ERROR alert kanalı mevcut**; normalizasyon RED'i **yok** (PLANNED §2.2g).
- **Vault kanıtları:** `.ai/.decisions/index.md:53` `[[ADR-016-url-normalization]] | URL Normalization | Routing` ✅ (slug eşleşmesi) · `.ai/index.md:633` `[[decisions/accepted/ADR-016-url-normalization]] | URL normalization` ✅ · `.ai/keys.md:251` `ADR-016 | URL normalization | Routing` ✅ · **iddia-karar çelişkisi:** `.ai/brain.md:971` `ADR-016 | Subdomain routing` ve `.ai/.templates/adr/adr-index.md:87` `ADR-016 | Subdomain routing` → **karar metni = URL Normalization** (kullanıcı onayı + decisions/index + keys + index); düzeltme §5.1 adım 6.
- **Sınır bağlayıcılığı (ADR-009):** `ADR-009-clean-url-redirect.md` satır 27-29 ("ADR-009 = host/şema/slash/uzantı; ADR-016 = path/query encoding, Unicode NFC/NFD, `..`/`.` segment temizliği, çift-slash path içi, query sıralama"), satır 40 (path case ADR-016'ya devredildi), satır 102 ve 292 (ADR-016 o dosyada henüz **düz metindi** — bu dosya ile wiki-link'e dönüşür ✅).
- **Şablon/protokol kanıtları:** `.ai/.templates/adr/adr-template.md` (Guardrail #16, 7 bölüm + §1.3 9 alan) VAR ✅ · format referansı `.ai/.decisions/accepted/ADR-015-env-parser-strategy.md` VAR ✅ · `.claude/skills/prompt-maker/references/10-web-research-protocol.md` VAR ✅.

### 1.2 Sorun Tanımı

1. **Normalize yüzeyi dar:** yalnız `index.php` öneki + baştaki slash (satır 36-41); `/kesfet//konser`, `/a/../kesfet`, `/kesfet%2Fkonser`, `/Kesfet` hepsi ya 404 ya da farklı route'a düşer — **aynı sayfa birden çok URL varyantına** sahip (ADR-009 duplicate-content derdi path için de geçerli).
2. **İki katman, tek sahip belirsiz:** nginx zaten decode + dot-segment + `merge_slashes` uygular (§1.3 kaynak 20), PHP tarafı uygulamaz → **katmanlar farklı sonuç üretir** (§1.3 kaynak 21: ön/arka uç decode farkı = cache poisoning/ATO sınıfı). PHP'deki normalizasyon **idempotent** ve tam olmalı.
3. **Bypass red + alert yok:** `%252e%252e%252f`, `%2e%2e`, `/..%2f` istekleri sessizce geçer (satır 36-42 decode bile etmez) → ne red ne log (ADR-013/005 ruhu ihlali — saldırı denemesi görünmez kalır).
4. **Unicode iki eşleme:** aynı kelimenin NFC ve NFD yazımı **iki farklı path** olur; routing case-sensitive `isset` ile çalıştığı için (RouteRegistry satır 43) bu, kayıt/hesap kirliliği ve homograph yolu açar (§1.3 kaynak 7, 22-27).
5. **Query kanonik değil:** query ham gelir (kernel satır 222); `?b=2&a=1` ile `?a=1&b=2` aynı kaynak iki varyant; imzalı URL bozulmasın diye sıralama kuralı da yok (§1.3 kaynak 28).
6. **Path case + hassas routing:** route anahtarları lowercase ve `resolve` case-sensitive (§1.1) → kullanıcının `/Kesfet` yazması 404 üretir; folding kuralı yazılı değil.
7. **Test yok:** normalizasyon davranışını kilitleyen test 0 → regresyon sessiz (§1.1).
8. **Numara istisnası:** "yeni ADR ≥ 088" kuralına rağmen `ADR-016` numarası `.ai/.decisions/index.md:53`'te **çoktan rezerve edilmiş boş slottur** (ADR-015 satır 54'te aynı istisnayı kaydetti) → bu yazımda numara üretilmez, rezervasyon doldurulur.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (RFC Editor, Unicode, OWASP, PortSwigger, nginx.org, AWS), her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) URL normalization 2025-26, (b) path traversal / double-encoding saldırı vektörleri, (c) Unicode homograph / punycode, (d) HTTP parser farklılıkları (request smuggling — nginx vs PHP/frontend-backend), (e) RFC 3986/3987.** Erişim: **7 websearch + 1 doğrudan RFC sayfası (rfc-editor.org/info/rfc3987)**; sonuçlar başlık/özet + RFC tam metin düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "URL normalization percent-encoding Unicode NFC path canonicalization best practices 2025" · (2) "double URL encoding path traversal bypass %2e%2e ..%2f attack vectors web application" · (3) "Unicode homograph punycode IDN spoofing attack normalization 2025 RFC 3987 IRI" · (4) "HTTP request smuggling nginx PHP-FPM parser differences CL.TE transfer-encoding 2025" · (5) "RFC 3986 section 6 normalization remove dot segments decode unreserved percent-encoding comparison algorithm" · (6) "signed URL query parameter reordering canonicalization breaks signature AWS CloudFront presigned URL order sensitivity" · (7) "nginx merge_slashes on by default decodes percent-encoded URI before passing PHP-FPM REQUEST_URI discrepancy" |
| Web Search **Konusu** | (1) RFC 3986/Cloudflare/AdCP tarzı kanonik normalizasyon adımları (unreserved decode, hex büyük harf, NFC); (2) traversal'ın encode katmanlarıyla kaçışı (`%2e%2e%2f`, `%252e%252e%252f`, `..%c0%af`) ve 2025-26 CVE'leri; (3) homograph/punycode spoofing ve tarayıcı savunmaları (UTS #39, TLD whitelist); (4) front-end/back-end parser uyuşmazlığı = request smuggling (CL.TE/TE.CL, CVE-2025-55315); (5) normalizasyon algoritmasının standart tanımı (remove_dot_segments, percent-encoding normalization); (6) imzalı URL'nin query değişikliğine tahammülü; (7) nginx'in varsayılan normalize davranışı (`merge_slashes on`, %XX decode, dot-segment) |
| Web Search **Bağlam** | **~28 adlandırılmış kaynak / 7 sorgu + 1 RFC tam metin**: RFC Editor (RFC 3986, RFC 3987), Cloudflare Rules docs, AdCP URL Canonicalization, Google Search docs (URL structure), Unicode.org (UAX #15), whatwg/url GitHub issue #626, MERJ, thetexttool, OWASP Community (Double Encoding), PortSwigger Web Security Academy (path traversal + request smuggling), SentinelOne (CVE-2026-21726), PayloadsAllTheThings, APIsec, YesWeHack, Fastly, Imperva, Vulnsy (CVE-2025-55315), nginx.org (ngx_http_core_module), joshua.hu (proxy_pass), Wikipedia (IDN homograph), stingrai.io, Xudong Zheng, Huntress, jamf, HackTricks, AWS CloudFront docs. |
| Web Search **Kısa Açıklama** | **(1) Normalizasyon standartta:** RFC 3986 §6.2.2 üç adım sayar — case normalization, percent-encoding normalization (unreserved `ALPHA/DIGIT/-/./_/~` decode edilir, reserved encoded kalır), path segment normalization (remove_dot_segments) (kaynak 1, 3, 4); RFC 3987 §3.1 gelen IRI'nin **NFC** ile normalize edilmesini ister ve `%HH`'i **büyük harf** ister (kaynak 2); Unicode UAX #15 "önce check, sonra convert" der — NFC pahalıysa `Normalizer::isNormalized` (kaynak 6). **(2) Traversal encode ile kaçar:** PortSwigger `../` yerine `%2e%2e%2f` ve `%252e%252e%252f`'in filtreleri geçtiğini, `..%c0%af` gibi non-standartların da çalıştığını yazar (kaynak 11); OWASP çift-encoding'in "tek decode eden filtreyi" geçtiğini anlatır (kaynak 10); Grafana Loki **CVE-2026-21726** tek decode sonrası hâlâ `%2e%2e%2f` göründüğü için güvenlik kontrolünün atlandığını gösterir (kaynak 13). **(3) Homograph:** aynı Unicode-yolu iki yazım (NFC/NFD) iki path üretir; punycode `xn--` etiketleri tarayıcı politikasıyla (TLD whitelist + UTS #39) maskelenir — sunucu tarafında da kanonikleştirme şart (kaynak 22-27). **(4) Parser farkı:** CL.TE/TE.CL'de ön-arka uç aynı isteği farklı çerçeveler; 2025'te chunk-extension varyantı (CVE-2025-55315) çıktı (kaynak 12, 17, 18, 19); nginx `location` eşleşmesini **decode + dot-segment + slash birleştirme** yapılmış normalized URI üzerinde yapar ve `merge_slashes` **varsayılan on**'dur (kaynak 20) — PHP tarafı aynı normalize'u uygulamazsa katmanlar farklı görür (kaynak 21). **(5) İmzalı URL:** CloudFront/S3 imzası parametreye dokunulunca reddedilir → query sıralama/tekilleştirme imzalı uçlarda uygulanmaz (kaynak 28). |
| Web Search **Uzun Açıklama** | **(a) Kanonikleştirme boru hattı standartta net:** RFC 3986 §6.2.2.2 percent-encoding normalization — "decode unreserved, keep reserved encoded, hex'i büyük harf" (bkz. AdCP'nin birebir uygulaması: `%7E`→`~`, `%2F` kalır, `%25` içeren URL'ler reddedilir — kaynak 4); §6.2.2.3 ve §5.2.4 `remove_dot_segments` — "bazı implementasyonlar zaten mutlak path'te dot-segment silinmez diye yanlış varsayar, normalizer'lar uygulamalıdır" (kaynak 1); Cloudflare gelen isteğe önce backslash→slash, sonra `//`→`/`, sonra RFC 3986 normalizasyonu uygular ve percent-encoded temsili **büyük harfe** çevirir, path'i Remove Dot Segments ile normalize eder (kaynak 3) — yani endüstri uygulaması bu ADR'nin (b)(d)(e) maddeleriyle aynıdır; Google non-ASCII karakterin percent-encode edilmesini, unreserved'in düz bırakılabilmesini önerir (kaynak 5); "encode öncesi NFC" kuralı hem SEO/adım kaynaklarında (kaynak 9) hem WHATWG URL issue #626'da ("Unicode normalization URL'nin yapısını anlamlı biçimde değiştirebilir" — kaynak 7) tartışılır; NFKC'nin compatibility fold'unun fazla agresif olduğu, RFC 3987'in yalnız **NFC** istediğini ve mevcut percent-encode'ları ikinci kez encode etmemeyi (idempotent mapping) yazar (kaynak 2, 6). **(b) Saldırı yüzeyi 2025-26'da canlı:** OWASP çift-encoding'i "filtre tek decode eder, backend ikinci kez decode eder" diye tanımlar (kaynak 10); PortSwigger encode/nested/null-byte varyantlarını ve WAF'ların JSON gövdesine bakmadığı istismarları verir (kaynak 11); APIsec çift-encoded ve Unicode trick'lerinin WAF'ı geçtiğini, YesWeHack "Nginx → Node → Python gibi çok katmanlı decode zinciri"nin tehlikesini yazar (kaynak 15, 16); Loki CVE-2026-21726 bunun 2026'daki canlı örneğidir (kaynak 13); PayloadsAllTheThings `%252e/%252f/%255c` tablosunu standart payload listesi olarak tutar (kaynak 14). **Sonuç: "tek decode + tek kontrol" yeterli değildir; normalizer tam decode sonrası kontrol etmeli, red etmeli, sessizce temizlememelidir.** **(c) Homograph/punycode:** Wikipedia ve Xudong Zheng, Cyrilil `а/е/і/с` ile Latin harflerin aynı görünüp farklı code point olduğunu (kaynak 22, 24); stingrai tarayıcının iki savunmasını (TLD allow-list + UTS #39 Highly Restrictive) anlatır (kaynak 23); Huntress/jamf punycode'un DNS-ASCII dönüşümü ve `xn--` öneki olduğunu, phishing'in temel vektörü olduğunu yazar (kaynak 25, 26); HackTricks aynı kimliğin raw/NFC/NFKC/punycode akışlarında farklı eşlenmesinin **duplicate-account / password-reset / zero-interaction ATO** ürettiğini, tüm uçların **tek kanonikleştirme** uygulaması gerektiğini söyler (kaynak 27) → CoreMusic path'inin de tek kapıda NFC'den geçmesi bu sınıfı kapatır. **(d) nginx vs PHP:** nginx normalize edilmiş URI ile çalışır (decode %XX, `.`/`..` çözümle, ardışık slash'ları birleştir) ve `merge_slashes on` varsayılandır (kaynak 20); joshua.hu bu farkın "ön uç decode etmez / arka uç decode+normalize eder" şeklinde cache poisoning ve ChatGPT örneğinde hesap ele geçirmeye gittiğini, `proxy_pass`'in çoğu durumda decode+normalize yaptığını, tek çözümün `$request_uri` ile ham geçiş ya da **her iki katmanda aynı normalize** olduğunu yazar (kaynak 21); smuggling tarafında Fastly/Imperva CL.TE-TE.CL tanımı (kaynak 17, 18), Vulnsy 2025 chunk-extension CVE'sini (kaynak 19) ve PortSwigger'ın H2→H1.1 çeviri sınıfını (kaynak 12) ekler → CoreMusic için ders: **HTTP başlık çerçevesi WAF/nginx işi, ama path/query gövdesinin kanonik hâli uygulamada da garanti edilmeli**; PHP `parse_url` + normalizer bu gövdeyi tek sahiplenir. **(e) İmzalı URL:** AWS CloudFront docs, URL değiştirilince imzanın reddedildiğini ve parametre sırasının imzalı uçta özel politikaya tabi olduğunu yazar (kaynak 28) → query sıralama/tekilleştirme **imza taşıyan isteklerde uygulanmaz** (bu ADR §2.2f istisnası). |
| Web Search **Paragraf Veri Uzun** | RFC 3986 §6.2.2 = case + percent-encoding + dot-segment normalizasyonu; RFC 3987 §3.1 = NFC + `%HH` büyük harf + mevcut encode'ı ikileme (idempotent) · unreserved decode edilir (`%7E`→`~`), reserved (`%2F` `%3A`) encoded kalır, `%25` (=percent'in kendisi encode) şüpheyle karşılanır · Cloudflare sırası: backslash→slash, `//`→`/`, RFC 3986 normalizasyonu, uppercase %XX, remove dot segments · traversal encode ile kaçar: `%2e%2e%2f`, `%252e%252e%252f`, `..%c0%af`, `....//`, `%00` → tek decode yeterli değil, tam decode + RED + log · CVE-2026-21726 (Loki) ve CVE-2025-55315 (chunk-extension) 2025-26 kanıtı · nginx `merge_slashes on` + normalized URI eşleşmesi; PHP tarafı aynı normalize'u uygulamazsa katman desync (joshua.hu: cache poisoning, ATO) · homograph: NFC/NFD iki path, raw/NFC/punycode uçlar arası farklı eşleme = duplicate account / reset confusion → tek kanonikleştirme kapısı · imzalı URL'de query'ye dokunma (CloudFront imza reddi) · sonuc: **path+query tek kapıda normalize edilir; şüpheli kırmızı bayrak sessizce temizlenmez, RED + ERROR ile raporlanır.** |
| Web Search **Sonucu** | 1) **Normalizasyon standartları doğrulandı** (kaynak 1, 2, 3, 4, 5, 6, 7) → RFC 3986 §6.2.2 + RFC 3987 §3.1 (NFC) + Cloudflare/AdCP uygulaması = bu ADR'nin (b)(c)(d)(e) maddelerinin birebir karşılığı. 2) **Double-encoding/traversal vektörü doğrulandı** (kaynak 10, 11, 13, 14, 15, 16) → "tek decode ile temizlik" yanlış; tam decode sonrası **RED + ERROR** (sessiz temizleme yok). 3) **Homograph/Unicode riski doğrulandı** (kaynak 22, 23, 24, 25, 26, 27) → NFC zorunlu, NFKC opsiyonel/değil; tüm uçlar aynı kanonik formu görmeli. 4) **Parser/katman farkı doğrulandı** (kaynak 12, 17, 18, 19, 20, 21) → nginx normalize eder; PHP'de normalizasyon **idempotent ve tam** olmalı, aksi halde ön/arka uç farkı saldırı sınıfı açar. 5) **İmzalı URL istisnası doğrulandı** (kaynak 28) → query sıralama imzalı uçlarda uygulanmaz. 6) **Sıra/otoyetkinlik** (kaynak 6) → NFC önce kontrol (`isNormalized`) sonra dönüşüm. **Toplam ~28 adlandırılmış kaynak, 7 sorgu + 1 RFC tam metin**; her ana iddia ≥2 çapraz kaynakla karşılanır. |
| Web Search **Alınan Karar** | **ADR-016 KABUL EDİLİR — PATH+QUERY TAM NORMALİZASYONU, TEK KAPI, RED + ERROR:** **(A) Kapsam:** yalnız **path+query** (host/şema/slash/uzantı = ADR-009); boru hattı sırası: ayrıştır → RED taraması → kontrollü percent-decode (en fazla 2 katman, her katmandan sonra RED taraması) → **Unicode NFC** → ASCII-only path case folding (şartlı §2.2b) → backslash→slash → iç `//`→`/` → **remove_dot_segments** → unreserved tekilleştirme + `%HH` büyük harf → query tekilleştirme + sırasız kanonikleştirme (**imzalı URL hariç**). **(B) Uygulama:** `shared/src/PageRouter/RequestNormalizer.php` **genişletilir** (dosya adı değişmez — In-Place Refactoring); **normalized path = routing'in tek girdisi** (`PageRouterKernel::normalizeRequest` normalizer çıktısını kullanır; ham input asla routing/log/SQL'e gitmez — ADR-002). **(C) Güvenlik:** çift-encoding (`%252e`), `%2e%2e`, `/..%2f`, `..%5c`, `%00`, overlong UTF-8 (`%c0%af`) gibi **bypass denemeleri RED + ERROR log** (sessizce temizleme YOK — ADR-013/005 ruhu); red log'unda query **değerleri maskelenir** (REDACTED). **(D) Şartlı folding:** route anahtarları hepsi lowercase ise path küçük harfe katlanır (kanıt §1.1); aksi halde folding uygulanmaz, yalnız not + ADR-009 canonical'ına devredilir. **(E) İstisna:** imzalı query (`X-Amz-*`, `Signature`, `Policy`, `Key-Pair-Id`, `token`, `hash`) varsa query normalize edilmez (kaynak 28). **(F) Fallback:** nginx (birincil, ADR-009) → RequestNormalizer (uygulama) → 404; bypass şüphesi 400 + ERROR; beklenmeyen normalizasyon istisnası **fail-closed 400** (ham asla routing'e geçmez). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi ve netleşti**: RFC 3986/3987 (2), Cloudflare/AdCP/Google (3), Unicode/WHATWG (3), OWASP/PortSwigger/CVE 2025-26 (7), homograph/punycode (6), nginx/parser (6), imzalı URL (1) → **~28 adlandırılmış kaynak, 7 sorgu + 1 RFC tam metin**; çapraz doğrulama ≥2 kaynak tüm ana iddialarda karşılanır. Kod tarafı da aynı resmi verdi: normalize **var ama dar** (RequestNormalizer satır 16-58), routing **kısmen normalize** ile çalışıyor (kernel satır 199-204), **red/alert yok**, **test yok** → bu ADR sıfırdan yeni bir normalizer **kurmaz**, mevcut tek kapıyı standart + güvenlik ile genişletir. Vault bulguları ayrıldı: IMPLEMENTED (host/şema/port/index.php/baştaki slash, kernel bağlantısı, StructuredLogger kanalı), PLANNED (case fold, percent, NFC, dot-segment, iç `//`, query, RED+ERROR, test, ext-intl); `brain.md:971`/`adr-index.md:87` "Subdomain routing" etiketi **çelişki** olarak işaretlendi, uydurulmadı. **Kaynak listesi (~28):** 1) RFC Editor — RFC 3986 (§5.2.4, §6.2.2) · 2) RFC Editor — RFC 3987 (§3.1 NFC, §5) · 3) Cloudflare Rules docs — How URL normalization works · 4) AdCP — URL Canonicalization · 5) Google Search docs — URL Structure Best Practices · 6) Unicode.org — UAX #15 Normalization Forms · 7) whatwg/url — issue #626 (Unicode normalization could change URL structure) · 8) MERJ — URL Encoding Done Right · 9) thetexttool — Mastering URL Encoding (NFC before encode) · 10) OWASP Community — Double Encoding · 11) PortSwigger — Path Traversal (file-path-traversal) · 12) PortSwigger — HTTP Request Smuggling · 13) SentinelOne — CVE-2026-21726 (Grafana Loki double-encoding bypass) · 14) PayloadsAllTheThings — Directory Traversal · 15) APIsec — Path Traversal in APIs · 16) YesWeHack — Path Traversal guide · 17) Fastly — What is HTTP Request Smuggling · 18) Imperva — HTTP Request Smuggling · 19) Vulnsy — Smuggling Cheat Sheet (CVE-2025-55315) · 20) nginx.org — ngx_http_core_module (merge_slashes, normalized URI) · 21) joshua.hu — proxy_pass: nginx Dangerous URL Normalization · 22) Wikipedia — IDN homograph attack · 23) stingrai.io — Homoglyph Attacks Explained (UTS #39) · 24) Xudong Zheng — Phishing with Unicode Domains · 25) Huntress — What Is Punycode · 26) jamf — Punycode attacks · 27) HackTricks — Unicode Normalization · 28) AWS docs — CloudFront Signed URLs. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-009 (host/şema/slash/uzantı sınırı) | Bu ADR **yalnız path+query**'yi normalize eder; host lowercase, canonical 301, HSTS, trailing-slash/uzantı redirect'i ADR-009'un alanıdır — ikisi çakışırsa ADR-009 host'u, bu ADR path gövdesini alır (ADR-009 satır 27-29 bağlayıcı) |
| ADR-002 (no ORM / no framework / prepared statement) | Normalizasyon **yeni bağımlılık eklenmeden** yazılır; **ham path/query asla** routing, log veya SQL'e gitmez (SQL yalnız prepared statement + normalize edilmiş değer) |
| ADR-013 (rate limit + alert) | Red edilen istekler rate-limit sayaçlarına sayılır; ERROR alert kanalı `StructuredLogger` üzerinden (traceId korunur) |
| ADR-005 (doğrulama + REDACTED) | Red/ERROR log'unda path kalıbı yazılır, **query değerleri maskelenir**; token/imza hiçbir loga yazılmaz |
| ADR-004 (multi-domain SPA) | RouteRegistry anahtarları ve domain haritası değişmez; folding kuralı route anahtarlarına göredir (§1.1 kanıt) |
| In-Place Refactoring | `RequestNormalizer.php`, `PageRouterKernel.php`, `RouteRegistry.php` dosya adları **onaysız değiştirilemez**; davranış genişletilir, ad sabit |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (AGENTS.md §25.3 kural 2) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |
| Numara kuralı istisnası | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-016` `.ai/.decisions/index.md:53`'te rezerve boş slottur (doldurma, yeni numara tahsisi değil) |

---

## 2. Karar (Decision)

**Path+query tam normalizasyonu mevcut `RequestNormalizer` içinde tek kapıda uygulanır; routing bundan sonraki tek girdi olarak yalnız normalized path/query kullanır; bypass kalıpları sessizce temizlenmez — RED + ERROR ile raporlanır.**

### 2.1 Neden Bu Seçenek?

- **Tek kapı zaten var (YAGNI):** `RequestNormalizer` istek geçişinin tek taşıyıcısı ve `PageRouterKernel` onu zaten çağırıyor (§1.1) — normalizasyonu dağıtmak yerine mevcut sınıfı genişletmek en düşük riskli yol.
- **Katman uyumu zorunlu:** nginx normalize edip PHP etmezse ön/arka uç farkı doğrudan güvenlik sınıfıdır (§1.3 kaynak 20, 21); PHP tarafının normalize'u **idempotent** olunca nginx'in yaptığı normalize tekrar zararsız olur.
- **Standart hazır:** RFC 3986 §6.2.2 + RFC 3987 §3.1 algoritmayı zaten yazmış; Cloudflare/AdCP aynı adımları uyguluyor (§1.3 kaynak 1-5) — icat yok, uygulama var.
- **Red > sessiz temizleme:** çift-encoding/traversal'ı temizleyip geçmek saldırgana "filtreni aştım" der; red + ERROR hem engeller hem görünürlük verir (ADR-013/005 ruhu; §1.3 kaynak 10-16).
- **Tek girdi = tek denetim noktası:** routing/log/SQL yalnız normalize edilmiş değer görünce ADR-002 prepared statement savunması tamamlanır; ham varyantlar (duplicate content, cache parçalanması) da kapanır.

### 2.2 Teknik Detaylar

**a) Boru hattı (sıra bağlayıcı — `RequestNormalizer::normalizePath()/normalizeQuery()` yeni private metotlar):**

| # | Adım | Ayrıntı |
|---|------|---------|
| 1 | Ayrıştır | `REQUEST_URI` → path + query (`parse_url`, mevcut satır 44-45 korunur) |
| 2 | RED taraması (katman 0) | ham girdide bypass kalıpları → §2.2g (red, decode ÖNCESİ) |
| 3 | Kontrollü percent-decode | en fazla **2 katman**; her katmandan sonra tekrar RED taraması; `%2520` (boşluk) gibi masum çift-encoding çözülür, çözümü traversal üreten `%252e` üretime geçmez → red |
| 4 | Unicode NFC | `Normalizer::normalize($s, Normalizer::FORM_C)` — sınıf varsa; yoksa §2.2d fallback |
| 5 | Path case folding | ASCII-only `A-Z→a-z` (şartlı §2.2b; Türkçe `İ/ı` locale tuzağına düşmemek için `strtolower` locale'e değil ASCII tablosuna bağlanır) |
| 6 | backslash→slash + iç `//`→`/` | `\` → `/`, ardışık slash tekilleme (yalnız path gövdesi; `//host` `//`-formu zaten ayrıştırmada tüketilir) |
| 7 | remove_dot_segments | RFC 3986 §5.2.4 — `/a/../b` → `/b`, `/kesfet/./` → `/kesfet` |
| 8 | Percent tekilleştirme | unreserved (`A-Za-z0-9-._~`) decode (`%7E`→`~`), `%HH` büyük harf; **reserved (`%2F`, `%3A`) encoded kalır** (path segment ayrımı korunur) |
| 9 | Query normalizasyonu | §2.2f |
| 10 | Geri yaz + dön | `$_SERVER['REQUEST_URI']`, `PATH_INFO`, `REQUEST_PATH`, `QUERY_STRING` güncellenir; **idempotency**: `normalize(normalize(x)) = normalize(x)` (nginx tekrarı zararsız) |

**b) Path case folding — şartlı:** route anahtarları **hepsi lowercase** ise folding **uygulanır** (kanıt: `routes.php` `'login'`/`'register'`/`'logout'` — büyük harfli anahtar grep 0; `resolve()` case-sensitive `isset`, RouteRegistry satır 43) → `/Kesfet` artık `/kesfet` ile eşleşir, 404 kaybı kapanır. **Tek bir route anahtarı bile büyük harf içerirse folding uygulanmaz** (routing'i değiştirirdi); bu durumda yalnız **not düşülür** ve büyük harfli varyant canonical 301 konusu olarak ADR-009'a devredilir. Host lowercase **bu ADR'ye girmez** (ADR-009).

**c) Percent-encoding tekilleştirme + çift-encoding:** unreserved decode edilir, reserved encoded kalır, hex büyük harfe (`%2f`→`%2F`) (RFC 3986 §6.2.2.2, AdCP). **Çift-encoding ikiye ayrılır:** masum (`%2520`, `%253D` — çözülür/tekilleştirilir); **bypass** (çözüm sonucu `..`, `/`, `\`, NUL üreten: `%252e%252e`, `%252f`, `%255c`) → **RED + ERROR** (§2.2g). `%25` içeren path, decode sonrası kalıp üretmiyorsa düşülür; üretime geçmez.

**d) Unicode NFC:** path ve query değerleri NFC'ye katlanır (RFC 3987 §3.1; UAX #15 → önce `isNormalized(FORM_C)`, dönüşüm gerekmiyorsa atla — performans). **NFKC kullanılmaz** (compatibility fold fazla agresif → false-positive). `ext-intl` composer'da declare değil (§1.1) → **fallback:** `class_exists('\\Normalizer')` false ise NFC **atlanır + log WARNING** (fail-open yalnız NFC katmanı; RED taraması ASCII olduğu için çalışır), `ext-intl` kurulumu §5.1 adım 7'de şart koşulur.

**e) Double-slash + dot-segment:** path içi `//`→`/` ve `remove_dot_segments` uygulanır. **Ayrım:** literal `/a/../b` **sessiz normalize edilir** (`/b` — RFC 3986'nın öngördüğü davranış); **percent-encoded traversal** (`%2e%2e`, `/..%2f`, `..%5c`) ve **çözüm sonrası hâlâ `..` içeren çok katmanlı** girdiler **RED + ERROR** sayılır — çünkü `..`'yi yazmanın encode edilmiş tek meşru sebebi yoktur, kaçış niyetidir (§1.3 kaynak 10-16).

**f) Query tekilleştirme + sırasız kanonikleştirme (imzalı hariç):** parametreler `parse_str`-uyumlu ayrıştırılır; **aynı anahtar tekrarı tekilleştirilir** (son değer korunur — PHP davranışıyla hizalı), anahtar alfabetik **sıralanır** ve `http_build_query` ile yeniden üretilir → `?b=2&a=1` ≡ `?a=1&b=2`. **İstisna (imzalı URL):** query'de `X-Amz-Signature`, `X-Amz-Credential`, `X-Amz-Expires`, `Signature`, `Policy`, `Key-Pair-Id`, `sig`, `token`, `hash` anahtarlarından **herhangi biri varsa query hiç normalize edilmez** (ham kalır) — imza bozulmasın (§1.3 kaynak 28); path yine normalize edilir. İstisna yalnız query'yi durdurur, RED taramasını durdurmaz.

**g) Güvenlik — RED + ERROR (sessiz temizleme YASAK):**

| Kalıp (ham ve her decode katmanında) | Eylem |
|--------------------------------------|-------|
| `%25` çift-encoding, çözümü `../`, `..\`, NUL üreten | **RED 400 + ERROR** |
| `%2e%2e`, `%2E%2E`, `/..%2f`, `..%5c`, `.%2e`, `%c0%af` (overlong), `%ef%bc%8f` (fullwidth slash) | **RED 400 + ERROR** |
| `%00` (NUL), CR/LF enjeksiyonu (`%0d%0a`) path/query'de | **RED 400 + ERROR** |
| Normalize sonrası kök dışına çıkan dot-segment (`/../../etc`) | **RED 400 + ERROR** |
| literal `/a/../b` (içerikte zararsız, çözümü temiz yol) | sessiz normalize (§2.2e) |

Log biçimi: `StructuredLogger` → `ERROR` + `traceId` + **kalıp adı** (ör. `DOUBLE_ENCODING`, `DOT_SEGMENT_ENCODED`) + **maskelenmiş path** (query **değerleri `[REDACTED]`**, ADR-005) + IP. Red istekleri `RateLimiterMiddleware` sayaçlarına sayılır (ADR-013); alert tekrarlayan isteklerde **sıklık-limitli** yazılır (log taşkını yok).

**h) Normalized path = routing'in tek girdisi:** `PageRouterKernel::normalizeRequest()` (satır 197-224) yeniden düzenlenir: `$_SERVER`'dan yeniden okumak yerine `normalize()` dönüş dizisindeki `path`/`query` kullanılır; `$get` yerine **normalize edilmiş query** parse edilir; `'uri'` bu path'ten türetilir. **Kural:** ham `REQUEST_URI`/`QUERY_STRING` routing'e, `StructuredLogger`'a, route anahtarına veya SQL'e **ulaşamaz** (ADR-002). RouteRegistry `resolve()` aynen kalır (In-Place); yalnız girdisi normalize olur. `fallback`: normalizer **throw** ederse → **fail-closed 400** (ham ile devam YOK); bypass red'i → 400 + ERROR; normalize edilmiş ama eşleşmeyen path → mevcut 404 davranışı korunur.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Yalnız sunucu katmanı (nginx `merge_slashes` + normalize) — PHP'ye dokunma** | Ek kod yok, hızlı | nginx deploy'a bağlı; `proxy_pass`/PHP-FPM geçişinde `$request_uri` ham gelebilir; **red + alert yok** (saldırı denemesi görünmez); path folding/query kanonik üretilmez | §1.3 kaynak 20-21 tam da bu katman farkı tehlikesini anlatır; CoreMusic'in her ortamında (local/test) nginx garantisi yok → uygulama yedek katmanı şart (ADR-009 iki katman mantığıyla aynı) |
| 2 | **Minimal genişletme: yalnız dot-segment + iç `//`** | Küçük iş, düşük risk | percent/Unicode/case/query sorunları (%2e%2e, NFC, `/Kesfet`, varyant query) olduğu gibi kalır; bypass red'i yine yok | §1.2'nin 1, 3, 4, 5, 6'ncı maddelerinin **hiçbirini** çözmez — yarım karar |
| 3 | **Her route/controller'da ayrı doğrulama (parçalı)** | Yerel kontrol | Aynı kalıp 14 PageRouter dosyasında tekrar eder (DRY ihlali); bir yer unutulunca boşluk kalır; test edilemez | "Tek normalizasyon kapısı" fikrine aykırı; bugünkü hata sınıfı (sessiz normalize) parçalılıktan besleniyor |
| 4 | **Tam decode + yeniden encode (her şeyi çöz, sonra tekrar encode et)** | Basit tek fonksiyon | Reserved karakterler de çözülünce path segment ayrımı bozulur (`%2F`↔`/`), ACL/route yanlış eşleşir; ham `%2F` taşıyan legitimate path'ler kırılır | §1.3 kaynak 4 (reserved encoded KALIR) doğrudan karşı; güvenlik açığı yaratır (decode ≠ normalize) |
| 5 | **Canonical farkta 301 redirect (ADR-009 tarzı, path için de)** | SEO/önbellek avantajı | Bu ADR'nin amacı **sessiz normalizasyon** (yönlendirme hassasiyeti); 301 loop riski, auth 302'leriyle çakışma, imzalı URL'yi kırma riski | Redirect politikası ADR-009'un tekelinde (host/şema/slash); path redirect'i ancak §2.2b şartı sağlanmazsa ADR-009'a devredilir — bu ADR'de ayrılmaz |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek doğruluk kapısı:** path/query yalnız `RequestNormalizer`'dan geçer; routing/log/SQL ham göremez (ADR-002 tamamlanır) — denetim tek yerde toplanır.
- **Varyant kapanır:** `//`, `/./`, `/a/../b`, `%7E`, `/Kesfet`, `?b=2&a=1` hepsi aynı kanonik adrese iner → duplicate content, bölünmüş cache ve 404 kaybı azalır (ADR-009'un hedefiyle aynı, path katmanı).
- **Saldırı yüzeyi görünür:** çift-encoding/`%2e%2e`/`/..%2f` denemeleri **RED 400 + ERROR** ile hem engellenir hem `traceId`li log'a düşer (ADR-013/005) — bugünkü "sessizce geç" durumu kapanır.
- **Katman uyumu:** nginx'in normalize'u ile PHP'ninki aynı sonucu üretir (idempotent) → ön/arka uç desync sınıfı (§1.3 kaynak 21) kapanır.
- **Unicode tutarlılığı:** NFC tek kapıda uygulanınca aynı içerik iki path'te toplanır; homograph/duplicate-account sınıfı (§1.3 kaynak 27) daralır.
- **İmza korunur:** imzalı query normalize edilmez → mevcut entegrasyonlar (OAuth callback, AWS benzeri imzalar) kırılmaz.

### 4.2 Olumsuz Sonuçlar

- **Bariyer / yanlış red riski:** legitimate ama "şüpheli" görünen istekler (ör. `%2F` taşıyan eski link, çok katmanlı encode) 400 ile düşebilir → fallback/red matrisi test edilmeli (risk 1, 2).
- **İşçilik:** `normalizeRequest()` yeniden düzenlenir (kernel 197-224) + boru hattı 10 adım + test yoktan yazılır → 1-2 oturumluk refactor.
- **Bağımlılık ihtimali:** NFC için `ext-intl` gerekir; declare yok → ya composer'a `ext-intl` eklenir (ADR-002 ile gerilir, bağımlılık artar) ya NFC fallback'li kalır (§2.2d).
- **Performans:** her istekte NFC + decode + sıralama (kısa string, µs mertebesi ama sıcak yol) → `isNormalized` short-circuit şart (§1.3 kaynak 6).
- **Kapsam disiplini:** host/canonical-redirect bu ADR'de çözülmez (ADR-009 bekler) → kullanıcı büyük harfli host/gördüğünde yine 301 yok.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Legitimate encoded path kırma** — eski/üretici linkler `%2F`, çift-encode ile gelir, red/400 üretir | 3 (olası) | 3 (orta) | RED matrisi **daraltılmış** yalnız bypass kalıpları (traversal/NUL/CRLF); `%2F` path'te reserved olarak **korunur** (decode edilmez — §2.2c); test paketinde gerçekçi URL seti (§5.1 adım 4); üretimde ilk 2 hafta **red log'u gözlemi** + tek satırlık kill-switch (normalizasyon bayrağı) |
| **İmzalı URL bozma** — query sıralama/tekilleştirme imzayı geçersiz kılar | 2 (mümkün) | 4 (yüksek) | §2.2f imzalı anahtar listesi tespitinde query **ham bırakılır**; path imzalı uçlarda da test edilir; OAuth callback için ayrı regresyon testi (ADR-013/015 ruhu) |
| **Unicode fold false-positive** — NFKC/değişken genişlik iki farklı içeriği tek adrese düşürür | 2 (mümkün) | 3 (orta) | **NFKC yasak**, yalnız NFC (RFC 3987 §3.1); fold sonrası **içerik hash'i korunur** (route eşleşmesi path üzerinden, içerik üzerinden değil); Türkçe `İ/ı` için locale bağımsız ASCII-only folding |
| **Performans / NFC maliyeti** — sıcak yolda her istekte normalizasyon | 3 (olası) | 2 (düşük) | `Normalizer::isNormalized` önce check (UAX #15); decode tek geçişte; query sıralama yalnız imzasız GET'lerde; benchmark §5.1 adım 4 (ADR-006 bütçesi: normalizasyon TTFB'ye <1ms) |
| **nginx/PHP katman uyuşmazlığı** — nginx farklı normalize ederse iki sonuç | 2 (mümkün) | 3 (orta) | Boru hattı **idempotent** (§2.2a adım 10); nginx-out faydası olan local/test ortamında PHP katmanı tek kaynaktır; §5.1 adım 5'te nginx `$request_uri`/`$uri` davranışıyla **aynı sonuç** testi |
| **`ext-intl` yok** — NFC uygulanamaz | 3 (olası) | 2 (düşük) | §2.2d fallback: kırmızı bayrak kontrolü ASCII olduğu için **çalışır**, yalnız fold atlanır + log WARNING; composer'a `ext-intl` şartı adım 7'de |
| **Red log taşkını / yanlış alarm** — bot taraması ERROR'ları boğar | 3 (olası) | 2 (düşük) | Sıklık-limitli ERROR (aynı IP + kalıp: 1/sn), red sayaçları ADR-013 metriğine, log'a query değeri asla (REDACTED) |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | Ham input SQL/routing'e gitmez kuralının tamamlayıcısı; normalizasyon bağımlılıksız yazılır (§2.2h, §1.4) |
| [[ADR-004-multi-domain-spa]] | PageRouter/RouteRegistry yol haritası — bu ADR girdiyi besler, eşleştirme mantığı değişmez (§1.1) |
| [[ADR-009-clean-url-redirect]] | **Bağlayıcı sınır:** host/şema/port/lowercase-host/uzantı/trailing-slash 301 = ADR-009; path/query encoding/Unicode/dot-segment/query sıralama = ADR-016 (satır 27-29, 40, 65); ADR-009'daki "ADR-016 düz metin, dosya YOK" satırları (102, 292) bu dosya ile **wiki-link'e dönüşür** |
| [[ADR-013-rate-limiting-apcu]] | Red edilen isteklerin sayaçlara sayılması + ERROR alert sıklık limiti (§2.2g) |
| [[ADR-005-ultrathink-protocol]] | Red/ERROR log'unda kalıp adı yazılır, query değeri `[REDACTED]` (§2.2g, §1.4) |
| [[../index]] | Satır 53 `[[ADR-016-url-normalization]]` — slug eşleşmesi ✅ (bu dosya rezervasyonu doldurur) |
| [[../../index.md]] | Satır 633 `[[decisions/accepted/ADR-016-url-normalization]]` ✅ |
| [[../../keys.md]] | Satır 251 `ADR-016 | URL normalization | Routing` ✅ |
| [[../../brain.md]] | Satır 971 `ADR-016 | Subdomain routing` → **çelişki**, düzeltme §5.1 adım 6 |
| [[../../.templates/adr/adr-index.md]] | Satır 87 "ADR-016 | Subdomain routing" → **çelişki**, düzeltme §5.1 adım 6 |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| `shared/src/PageRouter/RequestNormalizer.php` · `PageRouterKernel.php` · `RouteRegistry.php` · `shared/config/routes.php` | Uygulanacak/hedef kod (§1.1 satır kanıtları) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Boru hattı (PLANNED → IMPLEMENTED):** `RequestNormalizer`'a `normalizePath()` + `normalizeQuery()` (§2.2a 10 adım: RED taraması → kontrollü decode → NFC → ASCII folding → backslash/`//` → remove_dot_segments → percent tekilleştirme → query sıralama); dosya adı değişmez | Backend Architect | 1 oturum |
| 2 | **Routing tek girdisi:** `PageRouterKernel::normalizeRequest()` (satır 197-224) normalizer dönüşündeki `path`/`query`'yi kullanır; `$get` normalize query parse'u ile değiştirilir; ham `REQUEST_URI` routing/log/SQL'e ulaştırılmaz (ADR-002) | Backend Architect | 0.5 oturum |
| 3 | **RED + ERROR:** §2.2g red matrisi + `StructuredLogger` ERROR (traceId, kalıp adı, maskelenmiş path, `[REDACTED]` query) + ADR-013 sayaçlarına sayım + sıklık limiti | Security Engineer + Backend Architect | 0.5 oturum |
| 4 | **Testler (yoktan):** `RequestNormalizerTest` — idempotency (`normalize² = normalize`), NFC/NFD eşlemesi, `/a/../b`→`/b`, `%2e%2e`/`%252e%252e`/`/..%2f`→RED 400, `%7E`→`~` ve `%2F` korunumu, `/Kesfet`→`/kesfet` (şartlı), imzalı query ham kalır, `//`→`/`; + TTFB normalizasyon bütçesi (ADR-006) | QA Engineer | 1 oturum |
| 5 | **Katman uyumu:** nginx lokal konfigürasyonunda aynı isteklerin `$uri` sonucuyla PHP normalize'unun **aynı** olduğu doğrulanır (idempotency saha testi); `$request_uri` ham geçişi kayda alınır | DevOps Engineer + QA Engineer | 0.5 oturum |
| 6 | **Vault düzeltmeleri (append-only, ayrı işlem):** `brain.md:971` + `adr-index.md:87` "Subdomain routing" → "URL Normalization"; ADR-009'daki "ADR-016 düz metin / dosya YOK" ifadeleri bu dosyaya wiki-link ile bağlanır; `.ai/.decisions/index.md:53` zaten hizalı ✅ | Vault Steward | 0.5 oturum |
| 7 | **`ext-intl` kararı:** `class_exists('\\Normalizer')` kontrolü + composer `platform.php.ext-intl` kaydı ya da yazılı fallback (§2.2d); route anahtarlarının tamamının lowercase olduğu CI denetimi (büyük harfli anahtar = folding kilitlenmesi) | Backend Architect + DevOps Engineer | 0.5 oturum |

### 5.2 Geri Dönüş Planı

1. **Boru hattı (adım 1):** `git revert` → `RequestNormalizer` eski davranışına (satır 16-58) döner; normalize edilmemiş path routing'e geri gelir — nginx katmanı (ADR-009) devrede kaldığı için site çalışmaya devam eder (bilinçli gerileme: red/kanonik yok).
2. **Kernel bağlantısı (adım 2):** ayrı commit → tek `git revert` ile `normalizeRequest()` yeniden `$_SERVER`'dan okur; veri/format değişmediği için kayıp yok.
3. **RED + ERROR (adım 3):** red bayrağı tek config anahtarı ile kapatılabilir (**kill-switch**: `security.url_normalize_strict = off` → istekler normalize edilip geçilir, log yalnız INFO) — yanlış alarm kırığına karşı ilk geri dönüş bu anahtar, kod revert'i değil.
4. **Testler (adım 4):** test dosyası silinmez; yalnız beklentiler eski davranışa çekilir (regresyon kapsamı korunur).
5. **`ext-intl` (adım 7):** composer değişikliği revert edilir; fallback zaten devrede (NFC atlanır, diğer adımlar durur).
6. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (eski satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → **KABUL** (2026-09-24) |
| Karar içeriği | Tümü **kullanıcı onaylı / Önerilen** (üst görev kapsamı) |
| Beklenen biçim | ADR-004/008/010/011/012/013/014/015 formatı — 3 tur / 20 persona (uygulandı — sonuç §7.1) |
| Tech Lead | **✅** — debate sonrası onay (2026-09-24) |
| Kural | Debate tamamlanmadan bu ADR `frozen` yapılmaz; sonuç §5.3/§5.4/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir |

### 5.4 Debate Şartları (Kabul Koşulları — 3/3)

| # | Şart | Kapsam | Sorumlu | Durum | Kanıt |
|---|------|--------|---------|-------|-------|
| 1a | **Bypass red'i: sessiz temizlik YASAK → RED + ERROR alert** | `%252e`, `%2e%2e`, `/..%2f`, `..%5c`, `%00`, overlong UTF-8 (`%c0%af`) gibi bypass denemeleri **sessizce normalize edilmez** → **RED 400 + `StructuredLogger` ERROR** (traceId, kalıp adı, maskelenmiş path, query `[REDACTED]`) + ADR-013 sayaçlarına sayım + sıklık limiti (§2.2g, §5.1 adım 3) | Security Engineer + Backend Architect | ⏳ PLANNED → §5.1 adım 3 | Debate Tur 2 itiraz 1 → §2.2g |
| 1b | **Query de normalize edilir (ham routing'e ulaşamaz)** | Ham routing'e ulaşan query için **tekilleştirme + kanonik sıralama** (`?b=2&a=1` ≡ `?a=1&b=2`, PHP hizalı son-değer koruma) uygulanır; **imzalı URL istisnası korunur** (`X-Amz-*`, `Signature`, `Policy`, `Key-Pair-Id`, `token`, `hash` varsa query ham kalır — §2.2f); istisna yalnız sıralamayı durdurur, RED taramasını durdurmaz (§5.1 adım 1-2) | Backend Architect | ⏳ PLANNED → §5.1 adım 1-2 | Debate Tur 2 itiraz 2 → §2.2f |
| 1c | **`ext-intl` yoksa NFC fallback** | 3 `composer.json`'da `intl` declare **0** (§1.1) → `class_exists('\Normalizer')` false ise **NFC katmanı atlanır + ASCII-fold + log WARNING** (RED taraması ASCII olduğu için çalışır), `ext-intl` kurulumu **PLANNED** olarak §5.1 adım 7'de şart koşulur; NFKC yasak (§2.2d, §4.3 risk 6) | Backend Architect + DevOps Engineer | ⏳ PLANNED → §5.1 adım 7 | Debate Tur 2 itiraz 4 → §2.2d |
| 2 | **Normalized path = routing/log/SQL tek girdisi** | `PageRouterKernel::normalizeRequest()` yalnız normalizer dönüşündeki `path`/`query`'yi kullanır; ham `REQUEST_URI`/`QUERY_STRING` routing'e, `StructuredLogger`'a, route anahtarına veya SQL'e **ulaşamaz** (ADR-002); normalizer throw → **fail-closed 400** (ham ile devam YOK) (§2.2h, §5.1 adım 2) | Backend Architect | ⏳ PLANNED → §5.1 adım 2 | Debate Tur 1/2 genel teyidi → §2.2h |
| 3 | **Normalizer test paketi (yoktan)** | `RequestNormalizerTest`: **double-encoding** (`%252e%252e`→RED, `%2520`→çözülür), **dot-segment** (`/a/../b`→`/b`, `%2e%2e`→RED), **NFC/NFD** eşlemesi, **bypass/red matrisi**, idempotency (`normalize² = normalize`), `%7E`→`~` + `%2F` korunumu, `/Kesfet`→`/kesfet`, imzalı query ham kalır, `//`→`/` + TTFB bütçesi (ADR-006 <1ms) — normalizasyon testi bugün **0** (§1.1) → §5.1 adım 4 | QA Engineer | ⏳ PLANNED → §5.1 adım 4 | Debate Tur 2 itiraz 3 → §5.1 adım 4 |

**Tur 2 teyitleri (şart sayılmadı):** nginx/PHP katman uyumu → boru hattı **idempotent** (§2.2a adım 10, §4.3 risk 5); yanlış red riski → RED matrisi yalnız bypass kalıplarına daraltıldı (`%2F` reserved korunur) + kill-switch (`security.url_normalize_strict`) teyit edildi (§4.3 risk 1, §5.2 madde 3); `brain.md:971`/`adr-index.md:87` "Subdomain routing" etiketi → **çelişki** olarak kaldı, §5.1 adım 6'ya bağlandı (şart sayılmadı).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 53** `[[ADR-016-url-normalization]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`URL normalization` → MO/vault), Backend Architect domaini |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | Mimari karar özeti — satır 971 `ADR-016 | Subdomain routing` (**çelişki → §5.1 adım 6**) |
| [[../../keys.md]] | Keyword haritası — satır 251 `ADR-016 | URL normalization | Routing` ✅ |
| [[../../index.md]] | Master katalog — satır 633 ADR-016 kaydı ✅ |
| [[../../glossary.md]] | Terimler (`normalization`, `NFC`, `double encoding`, `homograph`) — eklenecek |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate şartları (3/20 KABUL) | §5.4 — (1a) bypass → RED + ERROR alert · (1b) query normalize (imzalı hariç) · (1c) NFC fallback + `ext-intl` PLANNED · (2) normalized path = routing/log/SQL tek girdisi · (3) normalizer test paketi |
| Debate (✅ TAMAMLANDI) | §5.3/§5.4/§7.1 — 3 tur / 20 persona, 18/2/0 KABUL (2026-09-24); sonuç frontmatter `debate` alanına işlendi |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[../../.templates/adr/adr-index.md]] | Satır 87 "ADR-016 · Subdomain routing" (**çelişki → §5.1 adım 6**) |
| [[ADR-002-pdo-mandatory-no-orm]] | Ham input/routing/SQL sınırı + bağımlılıksız yazım (dosya diskte VAR ✅) |
| [[ADR-004-multi-domain-spa]] | PageRouter/RouteRegistry yol haritası (dosya diskte VAR ✅) |
| [[ADR-009-clean-url-redirect]] | Host/şema/slash/uzantı bağlayıcı sınırı (dosya diskte VAR ✅) |
| [[ADR-013-rate-limiting-apcu]] | Red isteği sayımı + ERROR alert (dosya diskte VAR ✅) |
| [[ADR-005-ultrathink-protocol]] | Doğrulama + REDACTED ruhu (dosya diskte VAR ✅) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| `shared/src/PageRouter/RequestNormalizer.php` · `PageRouterKernel.php` · `RouteRegistry.php` · `shared/config/routes.php` | Uygulanacak hedef kod (§1.1 satır kanıtları) |
| `shared/tests/Unit/PageRouter/` | Mevcut 4 test dosyası — `RequestNormalizerTest` YOK → §5.1 adım 4 |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-016'yı sıfırdan yaz"; karar içeriğinin tamamı Önerilen/onaylı) | 2026-09-24 | ✅ |
| Tech Lead | ✅ — debate KABUL (3 tur / 20 persona, 18/2/0) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010/011/012/013/014/015 formatı — 3 tur / 20 persona |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-24 (frontmatter `debate` alanı ile aynı) |
| Tur 1 | **20 persona** — kod bulguları: `RequestNormalizer` host/scheme/port/`index.php`/baştaki slash **VAR** (IMPLEMENTED); path case / percent-encoding / NFC / dot-segment / query dedup / **RED-YOK** (hepsi PLANNED) · routing **kısmen** normalize path ile çalışıyor (`PageRouterKernel` satır 199-204), query **ham** (satır 222) · `RouteRegistry::resolve` **case-sensitive** (satır 43) · normalizasyon testi **0** · `ext-intl` declare **0** (3 `composer.json`). Oy: **15 kabul/neutral, 4 uyarı** — **Security:** bypass için RED + alert şart · **QA:** normalizer test paketi · **Backend:** imzalı URL query sort istisnası · **Perf:** NFC için `ext-intl` yok. |
| Tur 2 | **İtiraz → çözüm:** (1) bypass denemeleri sessiz temizlik → **RED + ERROR alert** → **şart 1a**; (2) query ham routing'e ulaşıyor → **query de normalize edilir** (dedup + kanonik sort, imzalı URL hariç) → **şart 1b**; (3) normalizer testi 0 → **test paketi** (double-encoding, dot-segment, NFC, bypass) → **şart 3**; (4) `ext-intl` yok → **Unicode NFC fallback** (intl yoksa ASCII-fold + PLANNED `ext-intl`) → **şart 1c**. |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL — 3 şart (§5.4):** (1) path+query normalizasyonu tamamlama (RED+alert · query normalize · NFC/intl), (2) normalized path = routing/log/SQL tek girdisi (ham input yasak), (3) normalizer test paketi |
| Tech Lead | **✅** (2026-09-24) — debate sonrası onay |
| Durum | `accepted` (kullanılabilir — **frozen YOK**; §7 üç satırı ✅ olmadan `frozen` yapılmaz) |

---

**1.0.0 | 2026-09-24 | Created**

*ADR-016 — CoreMusic Architecture Decision Record*
*Authority: ADR-016 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
