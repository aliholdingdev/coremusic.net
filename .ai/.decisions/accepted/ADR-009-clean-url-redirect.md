---
title: "CoreMusic — ADR-009: Clean URL Redirect (Tek Canonical Yöne 301 + İki Katmanlı Mekanizma + HSTS Kilit)"
type: adr
category: routing
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-009 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-009: Clean URL Redirect (Tek Canonical Yöne 301 + İki Katmanlı Mekanizma + HSTS Kilit)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-009'u sıfırdan yaz") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (çoklu domain SPA — canonical host tercihi bu domain haritasının apex'i ile hizalanır; dosya diskte VAR ✅) · [[ADR-006-performance-targets]] (TTFB/performans bütçesi — her redirect atlama bu bütçeyi yer; dosya diskte VAR ✅) · [[ADR-008-bypass-auth-middleware]] (auth/durum redirect'leri 302 sınıfı olarak bu ADR'nin dışındadır; dosya diskte VAR ✅) · karar dizini [[../index]] §3 satırı `[[ADR-009-clean-url-redirect]]` (slug eşleşmesi ✅ — satır diskte mevcut). · Düz metin (dosya diskte YOK — wiki-link KURULMAZ): **ADR-016** (URL/path normalizasyonu — `henüz yazılmadı — vault: brain.md ADR-016`; `.ai/.decisions/index.md` §3 `[[ADR-016-url-normalization]]` kaydı var).

---

## 1. Bağlam (Context)

CoreMusic'un **tek canonical URL yönü** tanımlı değil: aynı içerik `http`/`https`, `www.coremusic.net`/`coremusic.net`, `/kesfet`/`/kesfet/`, `/sayfa.php`/`/sayfa` gibi host/şema/slash/uzantı varyantlarıyla erişilebilir ve bu varyantların hangisinin "gerçek" olduğu kodda, config'de veya vault'ta yazılı değil. Karar; **TÜM host/şema normalizasyonlarını tek canonical yöne** bağlar: **https + apex domain (`coremusic.net`, www'sız) + tek trailing-slash biçimi + uzantısız path + lowercase host** — hepsi **301 (kalıcı)** ile, **iki katmanlı** mekanizmayla (sunucu birincil + uygulama yedek), **HSTS (max-age + preload)** ile kilitli.

**Sınır (ADR-009 vs ADR-016 — bu §1'de bağlayıcıdır):**
- **ADR-009 (bu karar) = host/şema/slash/uzantı dönüşümleri.** Girdi/çıktı her zaman **önceki host → sonraki host** eşleşmesidir (http→https, www→apex, slash'lı→slash'sız, `.php`→uzantısız, `KESFET`→`kesfet` host grubu). Path **içindeki** normalizasyon bu kararın konusu değildir.
- **ADR-016 = path/query encoding, Unicode/path normalizasyonu** (yüzde kodlama, Unicode NFC/NFD, `..`/`.` segment temizliği, çift-slash path içi, query sıralama). Düz metin referans: `ADR-016 (henüz yazılmadı — vault: brain.md ADR-016)`. İki karar çakışmaz: ADR-009 host'u ve iskeleti bir kez söyler, ADR-016 path gövdesini normalize eder.

### 1.1 Mevcut Durum

**Kod kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yoluyla):**

- **`shared/src/PageRouter/ResponseEmitter.php` (satır 80-89):** `redirect(string $location, int $statusCode = 302, ...)` — **yönlendirme varsayılanı 302**; `header("Location: $location")`. Kod tabanında **301 üretimi YOK** (PageRouter'da grep `301` = 0) → **canonical 301 = PLANNED**. (302'lerin bu ADR kapsamında olmayan kısmı auth/durum sınıfıdır — §1 sınırı.)
- **`shared/src/PageRouter/PageRouterKernel.php` (satır 157-165):** `wrapInHtmlShell` — `type === 'redirect'` veya `403 + body.redirect` sonuçları **her koşulda `['httpStatus' => 302, ...]`** olarak sabitlenir → mevcut tüm uygulama redirect'leri 302 **IMPLEMENTED**; canonical 301 yolu kernel'de yok → **PLANNED**.
- **`shared/src/PageRouter/RouteResult.php` (satır 43-60):** `redirect($location)` status kodu yazmaz (kernel/emitter 302'ye çevirir); `forbidden($redirect)` 403 + redirect taşır → 301 seçeneği sözleşmede yok → **PLANNED** (301 parametresi eklenecek).
- **`shared/src/PageRouter/PageRouter.php` (satır 65-78):** kök `/` → oturuma göre `/home` veya `login` redirect'i (`RouteResult::redirect`) — **302 IMPLEMENTED** (auth/durum sınıfı, bu ADR'nin canonical kapsamı dışı).
- **`shared/src/PageRouter/RequestNormalizer.php` (satır 16-58):** host/scheme/port/URI normalizasyonu **sessizce** yapılır — redirect **üretmez**: `index.php` öneki temizliği (satır 37: `preg_replace('#^(/index\.php)+#'...`), çift baştaki slash tekilleme (satır 40), scheme tespiti `X-Forwarded-Proto`/`HTTPS` (satır 60-79), port 80↔443 eşlemesi (satır 28-33), host `parse_url` + sanitize (satır 20-24) → **normalize (rewrite tarzı) IMPLEMENTED; canonical 301 redirect'i YOK → PLANNED.**
- **Büyük/küçük harf host eşleme:** `RequestNormalizer` host'u **lowercase'a çevirmez**; `strtolower($host)` yalnız `shared/src/Security/ReturnUrlPolicy.php` (satır 62) ve `shared/src/Middleware/OriginCheckMiddleware.php` (satır 64) içinde karşılaştırmada kullanılır → **canonical lowercase-host 301 = PLANNED** (path case dönüşümü ADR-016 kapsamı — bu ADR'ye girmez).
- **Trailing slash tekliği:** `resolveUri`/`normalizeRequest` slash'ları **sessizce trim** eder (`trim($val, '/')`, `PageRouter.php` satır 100, `PageRouterKernel.php` satır 204/214) — yani `/kesfet/` ve `/kesfet` aynı route'a düşer ama **hiçbiri diğerine 301 ile gitmez** (çift indeks riski crawl tarafında kalır) → **301 tekilleştirme = PLANNED**.
- **`.php`/uzantı temizliği:** yalnız `index.php` öneki temizlenir (yukarı); `/sayfa.php` → `/sayfa` gibi uzantı canonical'ı **YOK → PLANNED**.
- **HSTS:** `shared/src/Middleware/SecurityHeadersMiddleware.php` (satır 26-41) başlık listesinde **`Strict-Transport-Security` YOK** (grep `Strict-Transport`/`HSTS` → `shared/src/` içinde 0 kod eşleşmesi); `shared/src/Middleware/CLAUDE.md` satır 42/58/96 "SecurityHeadersMiddleware → HSTS" iddiası **kodda karşılıksız** → **doküman-kod çelişkisi; HSTS header'ı PLANNED** ⚠️ VERIFICATION REQUIRED (§5.1 adım 5'te kapatılır).
- **Sunucu katmanı (birincil):** depoda nginx/Apache config dosyası **YOK** (glob `**/*.conf` = 0, `**/.htaccess` = 0; yalnız `auth.coremusic.net/index.php` + `home.coremusic.net/index.php` front controller'lar var) → **sunucu canonical 301 bloğu = PLANNED**. Niyet mimari dokümanda mevcut: `.ai/architecture/k13-cicd/staging-environment.md` satır 127 ve `kubernetes-deploy.md` satır 154 → `nginx.ingress.kubernetes.io/ssl-redirect: "true"` (**https zorlama niyeti IMPLEMENTED-as-spec, canlı config PLANNED**).
- **Open-redirect kalkanı (ADR-004/ADR-008 kesişimi):** `shared/src/Security/ReturnUrlPolicy.php` — `ALLOWED_HOSTS` allowlist (satır 7-15: `coremusic.net`, `home/auth/music/admin.coremusic.net`, `localhost`, `127.0.0.1` — **`www.coremusic.net` YOK**) + `getSafeUrl` (satır 41-80: `javascript/data/vbscript` yasağı, user-info yasağı, host suffix allowlist) → **redirect hedefi doğrulaması IMPLEMENTED**; apex non-www canonical tercihi bu listeyle koddan da desteklenir.
- **`shared/config/domain.php` (9 subdomain haritası — `shared/AGENTS.md` envanteri):** apex `coremusic.net` + subdomainler HSTS `includeSubDomains` kapsamının hedefidir; domain listesi onaysız değişmez (shared Yasak #4).

### 1.2 Sorun Tanımı

(1) **Canonical yön yok:** hangi host/şema/slash biçimin "gerçek" olduğu yazılmadığı için arama motorları aynı içeriği 4-8 varyantta indeksler (duplicate content, crawl budget israfı — §1.3 kaynak 2, 4). (2) **Her redirect 302:** kalıcı(normalize) bir taşımayı 302 ile yapmak arama motoruna "eski URL hâlâ kanonik" der; sinyal yeni URL'ye konsolide olmaz (§1.3 kaynak 7, 8, 10). (3) **HSTS yok:** ilk HTTPS isteği henüz şifreli değil; `www`/subdomain varyantları preload dışı kalır (§1.3 kaynak 12, 14). (4) **Tek katman bağımlılığı:** sunucu config'i yokken (mevcut durum) kanal tamamen uygulamaya bağlı; uygulama düşerse normalizasyon da düşer. (5) **Loop/chain riski yazılmamış:** çakışan kural çifti (http force + https force) `ERR_TOO_MANY_REDIRECTS` üretir; browser ~20 hop'ta, pratik kabul 3-4 hop, SEO eşiği **1 hop güvenli** (§1.3 kaynak 2, 4, 5, 6) → "en fazla 1 atlama + loop testi" kuralı kodda/vault'ta yok. (6) **301'in tersi zor:** tarayıcı 301'i süresiz önbelleğe alır; yanlış 301 bir kez yayılınca server tarafında kuralı silmek tek başına yetmez (§1.3 kaynak 22, 23, 24) → "yanlış 301 riski" kayıtlı değil. (7) **Sınır belirsiz:** ADR-009 vs ADR-016 (host/şema vs path/query) ayrımı vault'ta yazılı değil → iki normalizasyon kararının çakışma riski.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅, v7.2.0) — birincil/resmî kaynak önce (hstspreload.org, MDN, Google Search Docs, OWASP, NDSS, Stack Overflow yüksek oylu kanıtlar), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; performans iddiası = "benchmark/kaynak + 2 doküman", güvenlik iddiası = "OWASP + official" zorunlu. **Odak: (a) redirect chain/loop 2025-26 performans ve CWV etkisi; (b) 301 vs 302 SEO (canonical + PageRank/link sinyali geçişi); (c) HSTS preload listesi kuralları; (d) redirect-based attacks (open redirect, OAuth token sızıntısı — ADR-004/ADR-008 ile kesişim); (e) 301 tarayıcı cache'i (ters dönüş riski).**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "redirect chains and redirect loops impact Core Web Vitals SEO performance 2025 2026" · (2) "301 vs 302 redirect SEO canonical PageRank signal transfer permanent temporary" · (3) "HSTS preload list submission requirements rules max-age includeSubDomains preload" · (4) "open redirect vulnerability OAuth token leakage redirect attacks OWASP 2025" · (5) "301 redirect browser cache permanent cannot undo" + "redirect loop limit ERR_TOO_MANY_REDIRECTS chain single hop nginx" |
| Web Search **Konusu** | Redirect chain/loop'un TTFB/LCP/CWV ve crawl budget'a etkisi (2025-26); 301'in sinyal konsolidasyonu vs 302'nin eski URL'yi kanonik tutması; hstspreload.org kabul kuralları (max-age ≥ 31536000, includeSubDomains, preload) + OWASP 2 yıl önerisi; open redirect → OAuth kod/token hırsızlığı saldırı zinciri; 301'in tarayıcıda süresiz cache'i ve tersine zorluğu; browser redirect limiti (Chrome/Edge ~20, pratikte 3-4, SEO'da 1 hop güvenli). |
| Web Search **Bağlam** | ~24 kaynak (çoğu 2024-2026, birincil + ikincil): birincil — hstspreload.org (submission + deployment + continued requirements), MDN Strict-Transport-Security, OWASP HSTS Cheat Sheet, OWASP Open Redirect, Google Search Core Web Vitals dokümanı, NDSS 2025 paper (open redirect escalation), HackerOne raporu #3099816 (Lichess OAuth redirect_uri); ikincil — Search Engine Land redirect guide, SE Ranking 301/302 araştırması (+ AI-citation verisi), urllo/redirect.pizza/Siteimprove 301-302 karşılaştırmaları, captaindns/Pulsetic loop limit tabloları, Stack Overflow 301-cache kanıtları, Moz 301 reverse, APNIC HSTS preload benimseme analizi. |
| Web Search **Kısa Açıklama** | **Chain/loop:** her hop 100-300ms ekler (Search Engine Land), Google ~10 URL hop'tan sonra indekslemeyi kesebilir, **1 hop = güvenli**; çakışan http/https force kuralı 3. hop'ta loop üretir (captaindns vakası); Chrome/Edge limiti 20 (`ERR_TOO_MANY_REDIRECTS`), Safari 16, pratik kabul 3-4 hop → chain TTFB'ye ve LCP/INP'ye doğrudan yazılır (CWV, Google resmi doküman). **301 vs 302:** 301 kalıcıdır → canonical sinyali hedefe taşınır, ranking sinyalleri konsolide olur; **302 geçici der → eski URL kanonik kalır, sinyal hedefe consolidate olmaz** (SE Ranking: 302'ler sinyal taşımaz/çok az taşır; urllo: eski URL indekste kalır); uzun süreli 302'yi Google 301 gibi işlese de bu **garanti değildir** (Screaming Frog/SE Ranking notu). **HSTS preload:** base domain'de header zorunlu — `max-age ≥ 31536000; includeSubDomains; preload`; kademeli ramp (5dk→1hafta→1ay) önerilir; preload sonrası devam şartı 10886400; OWASP önerisi `max-age=63072000; includeSubDomains` (2 yıl). **Saldırı:** open redirect, OAuth `redirect_uri` zincirine eklenince authorization code/token'ı saldırgana götürür (OWASP, HackerOne #3099816, StackHawk akışı); NDSS 2025 client-side open redirect'i escalation aracı olarak inceler; allowlist exact-match şart. **301 cache:** tarayıcı 301'i **süresiz** cache'ler (SO 9130422); kural kaldırılsa bile tarayıcı yönlendirmeye devam eder (dev.to); Moz: 301'i reverse etmek zordur → yanlış 301 = istemci tarafında kalıcı hasar riski. |
| Web Search **Uzun Açıklama** | **(a) Chain/loop performansı:** Search Engine Land, her redirect hop'unun tek isteğe 100-300ms eklediğini, chain'lerin crawl budget'ı her hop için ayrı istek olarak yaktığını ve Google'ın ~10 hop sonrasında sayfayı indekslemediğini belirtir; "1 hop = güvenli, geçici senaryolar dışında 1'den uzun chain kabul edilemez" (kaynak 2). Google'ın resmi CWV dokümanı LCP/INP/CLS'in doğrudan sıralama girdisi/UX sinyali olduğunu, redirect gecikmesinin LCP'yi besleyen TTFB'yi artırdığını (kaynak 1; 2026 odaklı redirect-CWV yazısı kaynak 3 ile çapraz). Loop tarafında captaindns, `.htaccess` içindeki Force-HTTPS + Force-non-www çiftinin `R=301` zincirini 3. hop'ta loop'a çevirdiğini (http→https→http) ve tarayıcı limitlerini tabloya bağlar (Chrome/Edge 20, Safari 16 — kaynak 4); Pulsetic aynı limiti ~20, curl'i 30 olarak doğrular (kaynak 5); Stack Overflow "browsers accept 3-4 hops" notu pratik eşiği verir (kaynak 6). **→ "max 1 atlama + zorunlu loop testi" kararı bu dört kaynağa dayanır.** **(b) 301 vs 302:** SE Ranking, 301'in destination'ı kanonik yapıp "most ranking signals"ı taşıdığını, 302'nin sinyal taşımadığını (veya çok az taşıdığını) araştırmasıyla yazar; ayrıca Google organik sonuçlarında %4.71 geçici redirect izi ve AI-citation'larda kalıcıların (301/308) geçiciye (302/307) oranla 1,6 kat daha sık atıf aldığını bildirir (kaynak 7). urllo, 302 kullanıldığında **eski URL'nin kanonik/index'te kaldığını**, yeni URL'nin "limbo"da olduğunu; sabit taşınmada 302'nin indekslemeyi yavaşlatıp link sinyalini zayıflattığını anlatır (kaynak 8). redirect.pizza, 301'in "rankings move + browsers cache it hard", 302'nin "nothing transfers" özeti verir (kaynak 9). Siteimprove, migration'da 302 kurulup 301'e çevrilmeyi unutmanın en yaygın hata olduğunu ve "declared intent" sorununu vurgular (kaynak 10); torresnicolas 301 sinyal konsolidasyonunu zamanlama boyutuyla (ekipman/süre) tamamlar (kaynak 11). **→ "kalıcı normalizasyon asla 302; SEO kanonikalite 301 ister" kararının dayanağı.** **(c) HSTS preload:** hstspreload.org kabul şartları: base domain'de HSTS header, `max-age ≥ 31536000`, `includeSubDomains`, `preload`; kademeli ramp adımları (300 → 604800 → 2592000 saniye) zorunlu pratik; 2016 sonrası girenler için devam max-age'i 10886400 (kaynak 12). MDN aynı kuralları (preload için min 1 yıl + includeSubDomains) ve subdomain kapsamını teyit eder (kaynak 13). OWASP Cheat Sheet `max-age=63072000; includeSubDomains` (2 yıl) önerir, `includeSubDomains`'siz örneği "tehlikeli" ilan eder (kaynak 14); Invicti preload 3 adımı ve 2 yıl değerini tekrarlar (kaynak 15); APNIC preload benimseme tablolarıyla üretim gerçeğini (ör. youtube/instagram `31536000; includeSubDomains; preload`) doğrular (kaynak 16). **→ `max-age=63072000; includeSubDomains; preload` + apex'te header + kademeli ramp kararı.** **(d) Redirect saldırıları:** OWASP açık redirect'in OAuth'a zincirlenince authorization code'unu çaldırdığını söyler (kaynak 17); StackHawk, `redirect_uri` allowlist exact-match'i olmadan token hijack oluşunu adım adım anlatır (kaynak 18); NDSS 2025 paper client-side open redirect'i üç kritik Web sınıfında escalation aracı olarak inceler (kaynak 19); HackerOne #3099816 Lichess OAuth `redirect_uri` open redirect'i (kaynak 20); Vulnsy cheat-sheet SAML RelayState ve implicit-flow fragment sızıntısıyla tamamlar (kaynak 21). **→ `ReturnUrlPolicy` allowlist + sadece relative canonical hedef (bu ADR) ADR-004/ADR-008 ile kesişimde open-redirect yüzeyini kapatır.** **(e) 301 cache riski:** Stack Overflow 9130422 — 301 "süresiz" tarayıcı cache'i alır, Cache-Control'a rağmen kenar durumları var (kaynak 22); Moz — 301 reverse edilebilir ama zordur ve istemci cache'i temizlemeden geri alınmaz (kaynak 23); dev.to — sunucudan kural kaldırılsa dahi tarayıcı önbellekteki 301 ile yönlendirmeye devam eder (kaynak 24); keyword.com — düzeltme genelde cache/CDN temizliği ister (kaynak 9/24 çapraz). **→ §4.3 risk 3: yanlış 301 = yüksek etki; staging doğrulaması + kurallı yayın şartı.** |
| Web Search **Paragraf Veri Uzun** | chain her hop +100-300ms · Google ~10 hop sonra indekslemez · **1 hop = güvenli** · crawl budget her hop = 1 istek · CWV: redirect gecikmesi TTFB→LCP/INP yazar · loop: Force-HTTPS + Force-non-www çatışması 3. hop · Chrome/Edge limit 20 `ERR_TOO_MANY_REDIRECTS` · Safari 16 · pratik 3-4 hop · 301 = kalıcı → sinyal hedefe konsolide · 302 = geçici → eski URL kanonik kalır, sinyal taşınmaz · uzun süreli 302→301 muamelesi garanti değil · Google organikte %4.71 302 izi · AI-citation 301/308 : 302/307 ≈ 1,6× · HSTS preload: `max-age≥31536000; includeSubDomains; preload` · OWASP: `max-age=63072000` (2 yıl) · ramp 300→604800→2592000 · devam şartı 10886400 · open redirect + OAuth `redirect_uri` = code/token hırsızlığı · exact-match allowlist şart · NDSS 2025 escalation · SAML RelayState / fragment sızıntısı · 301 tarayıcıda süresiz cache · kural silinse de tarayıcı yürür · reverse = cache/CDN temizliği + yeni URL · canonical hedefler relative olmalı (open-redirect yüzeyi) · apex `coremusic.net` + https tek yön · ReturnUrlPolicy `www` içermiyor. |
| Web Search **Sonucu** | 1) **Chain/loop net:** 1 hop güvenli, 3-4 pratik tavan, 20 tarayıcı limiti; her hop 100-300ms + crawl budget yakar → **"max 1 atlama + loop testi zorunlu"** bağlayıcı (kaynak 1, 2, 3, 4, 5, 6). 2) **301 zorunlu:** kalıcı normalizasyonda 302 = eski URL kanonik kalır + sinyal taşınmaz → **"asla 302 (canonical sınıfı)"** doğrulandı (kaynak 7, 8, 9, 10, 11). 3) **HSTS preload kuralları resmi:** apex'te header, 1 yıl min / OWASP 2 yıl, includeSubDomains + preload, kademeli ramp → **`max-age=63072000; includeSubDomains; preload` + ramp PLANI** (kaynak 12, 13, 14, 15, 16). 4) **Open redirect/token yüzeyi gerçek:** OAuth `redirect_uri` zinciri 5 kaynakla ispatlı → **yalnız relative canonical hedef + `ReturnUrlPolicy` allowlist** (ADR-004/008 kesişimi) (kaynak 17-21). 5) **Yanlış 301 = istemcide kalıcı:** süresiz cache + zor reverse → **staging doğrulama + yayın gate + fallback** (kaynak 22, 23, 24 + 9). 6) **İki katman gerekçeli:** sunucu katmanı uygulama öncesi/en hızlı/en güvenli (loop çifti kaynağı da sunucu kurallarıdır → tek kaynakta topla), uygulama katmanı sunucu config'siz ortam + SPA route içi temizlik için yedek (kaynak 4, 6 sunucu-uygulama çatışması dersleri). |
| Web Search **Alınan Karar** | **ADR-009 kabul edilir — tek canonical yön + 301 + iki katman + HSTS:** **Canonical yön:** `https://coremusic.net` (**apex, www'sız** — standart apex+https seçimi: HSTS `includeSubDomains` apex'ten tüm subdomainleri kilitler, `ReturnUrlPolicy`'de `www` zaten yok, apex marka adıdır) + **trailing slash yok** (root `/` hariç — route registry anahtarları slash'sızdır, `trim('/')` ile hizalı) + **uzantı yok** (`.php`/`.html` → uzantısız; `index.php` front controller öneki hariç) + **host lowercase** (DNS case-insensitive; path case'i ADR-016'ya bırakılır). **Mekanizma — İKİ KATMAN, ikisi de 301:** (1) **Sunucu birincil** (nginx/Apache canonical bloğu — uygulama ayağa kalkmadan önce, en hızlı, en güvenli); (2) **Uygulama yedek** (PageRouter fallback — sunucu config'i olmayan ortamda veya SPA route içi temizlik). **Asla 302** (SEO kalıcılık sinyali — canonical sınıfı); auth/durum redirect'leri (ADR-008, 302) bu kararın dışındadır. **Zincir ≤ 1 atlama, loop testi yayın öncesi şart.** **HSTS:** apex + tüm subdomainlerde `Strict-Transport-Security: max-age=63072000; includeSubDomains; preload` ile kilitli (kademeli ramp ile yayılır). **Sınır:** host/şema/slash/uzantı = ADR-009; path/query encoding/Unicode = ADR-016. |
| Web Search **Sonuç** | Karar 2025-26 verisiyle **desteklendi**: chain/loop+CWV (6 kaynak), 301 vs 302 SEO (5 kaynak), HSTS preload (5 kaynak), redirect saldırıları (5 kaynak), 301 cache/ters dönüş (4 kaynak) — **toplam ~24 birincil+ikincil kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak/tüm iddialarda karşılanır (hstspreload.org, MDN, OWASP, Google, NDSS, HackerOne birinciller). Kod bulguları §1.1'de IMPLEMENTED/PLANNED olarak ayrıldı; sunucu config'i, 301 sözleşmesi, HSTS header'ı, lowercase/trailing/uzantı 301'leri kodda yok → PLANNED yazıldı, uydurulmadı. `⚠️ VERIFICATION REQUIRED` yalnız doküman-kod çelişkisinde (Middleware/CLAUDE.md HSTS iddiası) ve PLANNED kalemler için korunur (§5.1 adımlarında kapatılır). |

**Kaynak listesi (~24 birincil + ikincil):**
1. https://developers.google.com/search/docs/appearance/core-web-vitals — CWV resmi tanım ve sıralama bağlamı (birincil, Google)
2. https://searchengineland.com/guide/too-many-redirects — chain/loop rehberi: hop başına 100-300ms, ~10 hop indeksleme limiti, **1 hop = güvenli**, crawl budget + CWV etkisi (2025-26)
3. https://www.redirections.app/blog/optimizing-redirects-for-core-web-vitals-compliance-in-2026 — redirect gecikmesi → TTFB/LCP, 2026 CWV uyumu
4. https://www.captaindns.com/en/blog/detect-redirect-loops-chains-suspicious-links — browser limit tablosu (Chrome/Edge 20, Safari 16), `.htaccess` Force-HTTPS + Force-non-www loop vakası
5. https://pulsetic.com/errors/err-too-many-redirects/ — ~20 redirect limiti, curl 30, loop vs chain ayrımı
6. https://stackoverflow.com/questions/76981000/nginx-cause-too-many-redirects — "browsers accept 3-4 hops" pratik eşiği (yüksek oylu)
7. https://seranking.com/blog/301-vs-302-redirects/ — 301 sinyal konsolidasyonu, 302 sinyal taşımaz; Google organikte %4.71 302; AI-citation 1,6× kalıcı lehine
8. https://www.urllo.com/resources/learn/redirect-webpage-301-vs-302-redirect — 302 eski URL'yi kanonik/index'te tutar; kalıcı taşımda indeksleme/link sinyali zayıflar
9. https://redirect.pizza/resources/redirects/301-vs-302-redirects — 301: rankings taşınır + tarayıcı sert cache'ler; 302: transfer yok
10. https://www.siteimprove.com/blog/temporary-vs-permanent-redirects/ — yanlış 302 kalıcılaşır (migration dersi); redirect = declared intent
11. https://torresnicolas.com/en/blog/redirecciones-301-seo-senales-tiempo — 301 sinyal geçişi ve zamanlama boyutu
12. https://hstspreload.org/ — submission requirements (`max-age ≥ 31536000`, `includeSubDomains`, `preload`), kademeli ramp (300→604800→2592000), continued requirement 10886400 (birincil)
13. https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Strict-Transport-Security — HSTS direktifleri, preload için min 1 yıl + includeSubDomains, subdomain kapsamı (birincil, MDN)
14. https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Strict_Transport_Security_Cheat_Sheet.html — OWASP: `max-age=63072000; includeSubDomains`; `includeSubDomains`'iz örnekte tehlikeli (birincil, OWASP)
15. https://www.invicti.com/learn/http-strict-transport-security-hsts — preload 3 adımı + 2 yıl max-age önerisi
16. https://blog.apnic.net/2023/07/26/hsts-preload-adoption-and-challenges/ — preload benimseme tabloları (youtube/instagram/reddit `...; includeSubDomains; preload`), google.com'un apex preload'sızlığı
17. https://community.owasp.org/attacks/open_redirect — open redirect OAuth'a zincirlenince authorization code hırsızlığı (birincil, OWASP)
18. https://www.stackhawk.com/blog/what-is-open-redirect/ — OAuth `redirect_uri` token hijack akışı + exact-match allowlist
19. https://www.ndss-symposium.org/wp-content/uploads/2025-523-paper.pdf — NDSS 2025: "Challenging the Myth of Harmless Open Redirection" (birincil, akademik)
20. https://hackerone.com/reports/3099816 — Lichess OAuth `redirect_uri` open redirect raporu (birincil, HackerOne)
21. https://www.vulnsy.com/cheat-sheets/open-redirect — SAML RelayState + implicit-flow fragment token sızıntısı
22. https://stackoverflow.com/questions/9130422/how-long-do-browsers-cache-http-301s — 301 tarayıcıda süresiz cache'lenir (yüksek oylu kanıt)
23. https://moz.com/blog/can-you-reverse-a-301-redirect — 301'i tersine çevirmek mümkün ama zor; istemci cache'i şart
24. https://dev.to/epranka/clear-the-301-302-redirection-cache-chrome-4dio — sunucudan 301 kaldırılsa tarayıcı önbelleğiyle yönlendirmeye devam eder

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Her iddia ≥2 kaynak | ADR-005 §2.2c standardı: sayısal/canlı her dış iddia ≥2 bağımsız kaynak taşır; tek kaynak → `⚠️ VERIFICATION REQUIRED` (§1.3 çapraz doğrulama 5 sorgu/24 kaynakta tam). |
| Frozen ADR dokunulmaz | ADR-001…037 metinleri okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2); bu ADR frozen değildir (status: accepted, frozen YOK). |
| log.md append-only | Bu ADR kaydı append ile yazılır; geçmiş satıra dokunulmaz (AGENTS.md §25.3 kural 3). |
| Dosya adı değişmez | In-Place Refactoring: `ADR-009-clean-url-redirect.md` adı onaysız değiştirilemez (In-Place Refactoring kuralı 1). |
| ADR-016 düz metin | ADR-016 dosyası diskte YOK → wiki-link kurulmaz; düz metin: `ADR-016 (henüz yazılmadı — vault: brain.md ADR-016)`. Sınır §1'de bağlayıcı: path/query encoding bu ADR'ye girmez. |
| Auth/durum redirect'leri kapsam dışı | ADR-008 auth redirect'leri 302 sınıfıdır; "asla 302" kuralı yalnız **canonical normalizasyon** (host/şema/slash/uzantı) içindir — auth akışları bu ADR ile değiştirilemez. |
| Canonical ≤ 1 atlama + loop testi | Zincir en fazla 1 hop; yayın öncesi loop testi zorunlu (§1.3 kaynak 2, 4, 5, 6) — test olmadan kural yayına girmez. |
| Kod implementasyonu kapsam dışı | Bu ADR karar kaydıdır; nginx/Apache bloğu, PageRouter 301 sözleşmesi ve HSTS header kodu Backend/Security/DevOps'a aittir (§5.1 sorumluları). |
| domain.php yasağı | `shared/config/domain.php` domain listesi onaysız değişmez (shared Yasak #4) — canonical host seti bu haritayla sabitlenir; www eklenmeyecek. |
| REDACTED | Sunucu/CDN credential'ları (varsa) bu ADR'ye yazılmaz (REDACTED politikası). |

---

## 2. Karar (Decision)

**CoreMusic, TÜM host/şema/slash/uzantı normalizasyonlarını tek canonical yöne ve kalıcı 301'e bağlar: her istek `https://coremusic.net/<uzantısız-path-lowercase-host>` biçimine EN FAZLA TEK (1) 301 atlamasıyla düşer; asla 302 üretmez; HSTS (max-age + includeSubDomains + preload) ile host seti kilitlenir. Mekanizma İKİ KATMANDIR: (1) Sunucu birincil — nginx/Apache redirect (uygulama ayağa kalkmadan önce çalışır, en hızlı, en güvenli); (2) Uygulama yedek — PageRouter fallback (sunucu config'i olmayan ortamda veya SPA route içi temizlik). İkisi de 301.**

**Canonical yönün seçimi (standart gerekçe):**
- **Şema = https:** HSTS + preload ile zorunlu kılınan yön; http→https tek yönlü 301 (tersi yasak).
- **Host = apex `coremusic.net` (www'sız):** (a) HSTS `includeSubDomains` apex'ten tüm 9 subdomaini kapsar — preload başvurusu apex üzerinden yapılır (§1.3 kaynak 12, 13); (b) mevcut `ReturnUrlPolicy::ALLOWED_HOSTS` allowlist'inde `www.coremusic.net` zaten YOK — kod apex yönünü destekler (§1.1); (c) www ayrı bir DNS/konfigürasyon yüzeyi ekler, apex marka adıdır (ADR-004 domain haritasıyla hizalı).
- **Trailing slash = YOK (root `/` hariç):** route registry anahtarları slash'sızdır (`home`, `kesfet` — `trim('/')` davranışı); `/kesfet/` → `/kesfet` tek 301.
- **Uzantı = YOK:** `/sayfa.php`, `/sayfa.html` → `/sayfa` tek 301; `index.php` front controller öneki istisnadır (RequestNormalizer mevcut temizliği korur).
- **Host = lowercase:** `coremusic.net` vs `COREMUSIC.NET` aynı hosttur (DNS case-insensitive) → büyük harfli host istekleri lowercase canonical'a 301. **Path case'i bu ADR'ye girmez** (ADR-016).

### 2.1 Neden Bu Seçenek?

1. **301 tek doğru sinyal:** kalıcı normalizasyonda 302 eski URL'yi kanonik tutar ve sinyali hedefe taşımaz; 301 destination'ı kanonik yapıp ranking sinyallerini konsolide eder (§1.3 kaynak 7, 8, 9, 10, 11) → "asla 302 (canonical)" bağlayıcı.
2. **1 atlama = performans ve SEO eşiği:** her hop 100-300ms + 1 crawl isteği yakar; 1 hop güvenli, 3-4 pratik tavan, 20 tarayıcı limiti (§1.3 kaynak 1, 2, 3, 4, 5, 6) → "max 1 hop + loop testi" bu kararın motorudur.
3. **İki katman derin savunma:** sunucu katmanı uygulama öncesi çalışır (TTFB'ye neredeyse sıfır ek, uygulama çökse de kanal yaşar); uygulama katmanı sunucu config'siz ortamda (mevcut depoda `.conf`/`.htaccess` YOK — §1.1) ve SPA route içi temizlikte yedektir. Çakışan kural riski tek disipline (canonical matrisi) indirgenir (§1.3 kaynak 4, 6).
4. **HSTS kilit yönü:** `max-age=63072000; includeSubDomains; preload` (OWASP 2 yıl, preload min 1 yıl — §1.3 kaynak 12, 13, 14) http varyantını istemci tarafında daha ilk byte'tan önce keser → redirect bile gerekmeyen istekler doğrudan https'e gider.
5. **Apex tercihi koddan destekli:** `ReturnUrlPolicy` allowlist'inde www yok (§1.1); apex+https+www'sız üçlü tek canonical matrisi temizler, ADR-004 domain haritasıyla çakışmaz.
6. **Güvenlik yüzeyi daraltılır:** yalnız relative canonical hedef + allowlist, open-redirect/OAuth token zinciri yüzeyini kapatır (§1.3 kaynak 17-21) — `ReturnUrlPolicy::getSafeUrl` zaten IMPLEMENTED, bu ADR onu canonical hedefler için de bağlar.

### 2.2 Teknik Detaylar

**(a) Canonical matrisi (hepsi 301 — tek atlama):**

| # | Girdi (varyant) | Canonical (hedef) | Etiket | Kanıt |
|---|-----------------|-------------------|--------|-------|
| 1 | `http://…` (https dışı her şey) | `https://…` | **PLANNED** (sunucu + uygulama) | Kodda 301 yok: `ResponseEmitter.php` satır 80 varsayılan 302; sunucu config'i depoda YOK (glob 0) |
| 2 | `www.coremusic.net/…` | `coremusic.net/…` | **PLANNED** | `ReturnUrlPolicy.php` satır 7-15'te www YOK (apex allowlist — destekleyici kanıt) |
| 3 | `/kesfet/` (kök hariç slash'lı) | `/kesfet` | **PLANNED** | Sessiz trim: `PageRouter.php` satır 100, `PageRouterKernel.php` satır 204/214 (301 üretmiyor) |
| 4 | `/sayfa.php`, `/sayfa.html` | `/sayfa` | **PLANNED** | Uzantı canonical'ı kodda YOK; yalnız `index.php` öneki temiz: `RequestNormalizer.php` satır 37 |
| 5 | `HTTP://KESFET.COREMUSIC.NET/…` (büyük harfli host) | `https://kesfet.coremusic.net/…` | **PLANNED** | `RequestNormalizer.php` satır 20-24'te lowercase YOK; `strtolower` yalnız karşılaştırmada (ReturnUrlPolicy satır 62) |
| 6 | `/index.php/…` front controller öneki | öneksiz path | **IMPLEMENTED** (sessiz normalize — redirect değil) | `RequestNormalizer.php` satır 37 (`preg_replace('#^(/index\.php)+#'…)`) |
| 7 | Host'a göre scheme/port tespiti (X-Forwarded-Proto, 80↔443) | doğru scheme/port | **IMPLEMENTED** (normalize) | `RequestNormalizer.php` satır 28-33, 60-79 |
| 8 | Kök `/` → `/home` veya `login` | auth/durum hedefi (**302 — kapsam dışı**, ADR-008) | **IMPLEMENTED** | `PageRouter.php` satır 65-78; `PageRouterKernel.php` satır 157-165 |

*Kural (bağlayıcı):* matris **tek kaynaktan** (sunucu + uygulama aynı liste) okunur; çifte kural (http force + https force gibi) yasak — kaynak §1.3 kaynak 4.

**(b) İki katmanlı mekanizma:**

| Katman | Konum | Ne yapar | Çalışma anı | Etiket |
|--------|-------|----------|-------------|--------|
| **1 — Sunucu birincil** | nginx/Apache canonical bloğu (apex + https + slash + uzantı + lowercase, tek `return 301`) | Matrisin tamamı, 301, uygulama öncesi | Uygulama ayağa kalkmadan önce — en hızlı, en güvenli | **PLANNED** (depoda `.conf`/`.htaccess` = 0; `ssl-redirect: "true"` niyeti `.ai/architecture/k13-cicd/staging-environment.md` satır 127 + `kubernetes-deploy.md` satır 154 IMPLEMENTED-as-spec) ⚠️ VERIFICATION REQUIRED |
| **2 — Uygulama yedek** | PageRouter fallback (`RequestNormalizer` + `RouteResult::redirect(..., 301)` + `ResponseEmitter` 301 desteği) | Sunucu config'i olmayan ortamda matrisin uygulanabilen kısmı + SPA route içi temizlik | Uygulama içinde, kernel öncesi normalizasyon + guard aşamasında | **PLANNED** (normalize IMPLEMENTED satır 6-7 amaçlı; 301 sözleşmesi yok) ⚠️ VERIFICATION REQUIRED |

*Kural (bağlayıcı):* her iki katman da **yalnız 301** üretir (canonical sınıfı); ikisi birden aynı isteği görmemeli (sunucu geçtiyse uygulama canonical'ı zaten eşleşir) → chain yine ≤ 1 hop.

**(c) Durum kodu disiplini:**

| Sınıf | Kod | Kapsam |
|-------|-----|--------|
| Canonical normalizasyon (host/şema/slash/uzantı/lowercase-host) | **301 — asla 302** | Bu ADR |
| Auth/durum redirect (login/logout/home yönlendirme, 403→redirect) | 302 | ADR-008 (kapsam dışı — dokunulmaz) |
| Retry/TTL'li geçici yönlendirme (varsa gelecekte) | 302/307 | Bu ADR dışı; canonical sınıfına sokulmaz |

**(d) HSTS kilit tablosu:**

| Değer | Karar | Gerekçe / Kaynak |
|-------|-------|------------------|
| Header | `Strict-Transport-Security: max-age=63072000; includeSubDomains; preload` | OWASP 2 yıl (kaynak 14); preload min 1 yıl (kaynak 12, 13) |
| Kapsam | **apex `coremusic.net` + tüm subdomainler** | preload başvurusu base domain'de yapılır (kaynak 12); `includeSubDomains` 9 subdomaini kilitler (`domain.php`) |
| Yayılım | Kademeli ramp: `300` → `604800` → `2592000` → `63072000` (+`preload` son adım) | hstspreload.org deployment recommendations (kaynak 12) |
| Uygulama yeri | nginx/Apache **+** `SecurityHeadersMiddleware` (yedek) | Mevcut kodda header **YOK** → PLANNED (§1.1; `Middleware/CLAUDE.md` iddiası kodla çelişiyor ⚠️ VERIFICATION REQUIRED) |
| Devam şartı | preload girişinden sonra max-age ≥ 10886400 kalıcı | hstspreload continued requirements (kaynak 12) |

**(e) Zincir/loop test kapısı (yayın öncesi zorunlu):**

| # | Test | Beklenen |
|---|------|----------|
| 1 | Her varyant (matris satırı 1-5) `curl -I` | Tek `301` → canonical → `200`; **hop = 1** |
| 2 | Canonical URL'den tekrar istek | `200` — **0 hop (loop yok)** |
| 3 | İki katman birlikte (sunucu + uygulama aktif) | Yine tek 301 (çifte atlama YOK) |
| 4 | Host döngüsü (`www`↔`apex` karşı yönlü kural çifti) | Loop üretilmez; `ERR_TOO_MANY_REDIRECTS` = 0 (limit 20 — kaynak 4, 5) |
| 5 | Auth akışı (login→redirect→callback) | 302'ler korunur, canonical matrisine girmez (ADR-008 uyumu) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız uygulama katmanında canonical 301 (sunucu config'siz)** | Deploy basit, tek kod tabanı | Her varyant PHP'ye kadar yol alır (ekstra TTFB — §1.3 kaynak 2, 3); uygulama çökmesiyle kanal da çöker; sunucu loglarında gizlenen loop kaynağı (`.htaccess`/nginx çatışması) teşhis edilemez (kaynak 4, 6) | Yedek katman olabilir ama **birincil** olamaz: karar iki katman ister — sunucu katmanı en hızlı/en güvenli olduğu için birincildir |
| 2 | **Normalizasyonu 302 ile yapmak ("sonra 301'e çeviririz")** | İstemcide cache riski yok, tersi kolay | 302 eski URL'yi kanonik tutar, sinyali hedefe taşımaz; kalıcı taşımda indeksleme/link sinyali zayıflar; "302→301 çevirme" unutulması en yaygın kalıcı hata (§1.3 kaynak 7, 8, 10) | Kararın çekirdeği SEO kalıcılığıdır — canonical sınıfına 302 giremez (§2.2c) |
| 3 | **www apex tercihi (`www.coremusic.net` canonical)** | Eski web alışkanlığı, bazı CDN'lerde cookie kolaylığı | `includeSubDomains` hâlâ apex'te olmalı → apex'i yine yönetmek gerekir; `ReturnUrlPolicy` allowlist'inde www YOK (kod itiraz ediyor); ekstra DNS/konfigürasyon yüzeyi + `www` varyantı zaten HSTS kapsamı dışında kalma riski (§1.3 kaynak 12, 13) | Apex + https + www'sız üçlü tek kanal, kilit apex'te, kod ve ADR-004 domain haritasıyla hizalı — standart ve daha ucuz |
| 4 | **Yalnız `<link rel=canonical>` meta (redirect'siz)** | Server config gerektirmez, tersi kolay | Meta HTML parse'dan sonra gelir (gecikme), crawl hâlda çoklu host/slash varyantını gezer → crawl budget israfı devam eder; 301'ün sinyal konsolidasyonunu vermez (§1.3 kaynak 2, 7) | Yönlendirme kararı "etiketle" ile çözülemez; meta canonical opsiyonel katman olarak kalabilir ama bu ADR'nin mekanizması değildir |
| 5 | **Tek katman = yalnız CDN/edge redirect** | Global düşük gecikme | Edge yoksa/düşerse kanal yok; depoda edge config'i de yok (glob 0); edge + origin kural çatışması loop kaynağı (kaynak 4) | İki katman (sunucu + uygulama) zorunlu; edge varsa **üçüncü** katman olarak eklenir, ikamesi değil |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek kanal, tek gerçek:** tüm host/şema/slash/uzantı varyantları tek canonical'a ≤1 hop'ta düşer; duplicate content ve crawl budget israfı kapanır (§1.3 kaynak 2, 7, 8).
- **SEO sinyali konsolide:** 301 ile ranking sinyalleri apex+https hedefinde birleşir; 302'nin "eski URL kanonik kalması" tuzağına düşülmez (§1.3 kaynak 7, 9).
- **TTFB dostu:** sunucu birincil katman uygulama öncesi 301 üretir (nginx `return 301` neredeyse sıfır maliyet); chain ≤1 hop CWV/LCP bütçesini korur (ADR-006 — §1.3 kaynak 1, 2, 3).
- **İlk istek bile şifreli:** HSTS ramp + preload ile http ve www varyantı istemcide kesilir; redirect sayfasına gerek kalmayan istekler doğrudan https'e gider (§1.3 kaynak 12, 14).
- **Yedek kanal:** sunucu config'i olmayan ortamda (mevcut depoda config yok — §1.1) PageRouter fallback devreye girer; iki katman aynı matristen beslenir → çelişkisiz.
- **Sınır temiz:** host/şema (ADR-009) vs path/query (ADR-016) ayrımı §1'de yazılı → iki normalizasyon kararı birbirine girmez.

### 4.2 Olumsuz Sonuçlar

- **PLANNED yükü:** sunucu canonical bloğu, PageRouter 301 sözleşmesi, HSTS header'ı ve lowercase/slash/uzantı 301'leri kodda yok — kararın gövdesi henüz uygulanmamış (§5.1 adımları; §1.1 kanıtları).
- **Doküman-kod çelişkisi:** `shared/src/Middleware/CLAUDE.md` HSTS iddiası kodda karşılıksız → ya kod eklenir ya doküman düzeltilir (§5.1 adım 5) ⚠️ VERIFICATION REQUIRED.
- **İki katman senkronu:** canonical matrisi iki yerde yaşar (nginx + PHP) → drift olursa loop/çifte atlama riski; tek kaynak listesi + test kapısı (§2.2e) sürdürülebilirlik maliyetidir.
- **301 geri alımı pahalı:** tarayıcı 301'i süresiz cache'ler; yanlış yayın istemcide kalıcıdır (§4.3 risk 3, §1.3 kaynak 22, 23, 24) → staging + yayın gate zorunluluğu ek yük.
- **HSTS geri alımı yavaş:** preload listesinden çıkış haftalar/alır ve zaten kilitli istemciler yeni kurallara uymaya devam eder (§1.3 kaynak 12, 16) → ramp disiplini ek süreç yükü.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Redirect loop** (çakışan kural çifti — http force + https force, www↔apex karşı kural; sunucu+uygulama kuralı birbirine girerse) | 2 (mümkün) | 4 (yüksek) — site erişilemez | Tek canonical matrisi (§2.2a) + tek kaynak liste + **loop test kapısı §2.2e (yayın öncesi zorunlu)**; limit: browser ~20 hop'ta keser (`ERR_TOO_MANY_REDIRECTS` — §1.3 kaynak 4, 5); acil: sunucu bloğu devre dışı bırak (§4.4 #1) |
| **Chain TTFB'si** (matris uygulanmazsa çoklu istek; veya iki katman art arda atlarsa çift hop) | 3 (olası) | 3 (orta) | ≤1 hop kuralı + §2.2e test 1/3; sunucu katmanı birincil (uygulama öncesi tek atlar); ADR-006 TTFB bütçesiyle birlikte ölç (§1.3 kaynak 1, 2, 3) |
| **Yanlış 301 cache'i** (yanlış canonical yayına girerse — tarayıcı 301'i **süresiz** cache'ler, kural silinse dahi istemci yürür; ters dönüş zor) | 2 (mümkün) | 4 (yüksek) | Staging doğrulaması + §5.1 yayın gate; çıkış yolu: **yeni hedefi ters 301 ile değil**, farklı path/host + CDN/Cache-Control + istemci cache temizliği ile al (§1.3 kaynak 22, 23, 24); yayın öncesi matrisin 10 farklı host×path ile imzalanması |
| **HSTS kilidi** (`includeSubDomains; preload` ile tüm subdomainler HTTPS'e kilitlenir; http-only test/local servis varsa kırılır; preload çıkışı yavaş) | 2 (mümkün) | 4 (yüksek) | Kademeli ramp (§2.2d — 300→604800→2592000→63072000); preload **en son** adım; tüm subdomainlerin https servis ettiği doğrulanır (`domain.php` 9 subdomain); local http istisnası: HSTS yalnız public host'ta (§1.3 kaynak 12, 13, 16) |
| **Open redirect / token sızıntısı** (canonical hedefine absolute URL kabul edilirse veya `redirect` query'si cross-domain taşınırsa — ADR-004 logout `redirect=` paramı, ADR-008 auth hedefleri) | 2 (mümkün) | 4 (yüksek) | Canonical hedefler **yalnız relative path** olabilir (matriste host her zaman sabit); `ReturnUrlPolicy::getSafeUrl` (IMPLEMENTED — §1.1) her redirect hedefinde bağlayıcı; `javascript/data/vbscript` + user-info yasağı + exact host allowlist korunur (§1.3 kaynak 17-21) |
| **Kural drifti (sunucu ↔ uygulama)** (nginx bloğu güncellenir, PHP listesi unutulur — veya tersi) | 3 (olası) | 3 (orta) | Canonical listesi tek dosyada (paylaşılan config) + §2.2e test 3 (iki katman birlikte); CI'da curl matris testi (PLANNED §5.1 adım 8) |

### 4.4 Fallback (Loop / 301 / HSTS Kill-Switch)

| # | Koşul | Eylem | Onay |
|---|-------|-------|------|
| 1 | **Loop tespiti** (`ERR_TOO_MANY_REDIRECTS` / test 2-4 kırmızı) | Sunucu canonical bloğu anında devre dışı (nginx reload) → kanal uygulama yedek katmanına düşer (301, tek hop); kök neden (çift kural) araştırılır, düzeltilmeden geri açılmaz | DevOps Engineer (üretimde: Tech Lead) |
| 2 | **Yanlış 301 yayına girdi** | Sunucu kuralı düzelt + CDN cache purge; istemci tarafında: ters yöne 301 **koyma** (cache'i büyütür) — yeni kanonik hedefe geçiş + `Cache-Control` disiplini ile eski 301'in süresi dolması beklenir; kritikse geçici 302 ile "yama" yalnız müdahalenin süresince (canonical sınıfı 302 kalıcı dönüşmez) | Backend Architect + DevOps |
| 3 | **HSTS uyumsuzluğu** (bir subdomain http-only kaldı / kırılma) | Ramp'i bir adım geri al (`max-age` düşür); **preload'a ZATEN girildiyse** çıkış için hstspreload.org removal + üst-domain `max-age=0` (yalnız apex'te, subdomain override) — süreç haftalar sürer → preload'a erken girme, ramp'i tamamla | Security Engineer → Tech Lead |
| 4 | **Uygulama yedek katmanı hata üretirse** (301 döngüsü uygulama içinde) | Fallback'i kapat (normalizasyon sessiz normalize'a döner — mevcut `RequestNormalizer` davranışı, kanal zaten sunucuda) | Backend Architect |
| 5 | **Karar geri alınacaksa** | Yeni ADR ("revert of ADR-009") — bu dosya keyfi düzenlenmez; kod/sunucu geri dönüşü `git revert` + config revert (AGENTS §17 #10) | Vault Steward + Tech Lead |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-009-clean-url-redirect`) + `log.md` append ("ADR-009 yazıldı (debate PENDING)") + dizin satırı doğrula (`[[../index]]` §3 `[[ADR-009-clean-url-redirect]]` slug eşleşmesi ✅) | Vault Steward | 30 dk |
| 2 | Debate (3 tur / 20 persona, ADR-004/ADR-008 formatı) — **✅ TAMAMLANDI (18/2/0 KABUL)**; sonuç §7.1'e yazıldı | Debate + Vault Steward | 2 gün |
| 3 | Tech Lead onayı — **✅ (2026-09-24)** (debate sonrası, §7) | Tech Lead | 1 gün |
| 4 | **PLANNED — sunucu birincil katman:** nginx/Apache canonical bloğu (matris §2.2a satır 1-5: https force + apex force + slash + uzantı + lowercase host, tek `return 301`, tek kaynak liste) + `.ai/architecture/k13-cicd/` deploy config'e taşı (`ssl-redirect: "true"` niyetiyle hizalı) | DevOps Engineer | 1 gün |
| 5 | **PLANNED — HSTS:** `SecurityHeadersMiddleware` + sunucu header `Strict-Transport-Security: max-age=63072000; includeSubDomains; preload` (§2.2d ramp'ı ile); `Middleware/CLAUDE.md` HSTS iddiası kodla eşleşir → ⚠️ VERIFICATION REQUIRED kalkar | Security Engineer | 4 saat |
| 6 | **PLANNED — uygulama yedek katman:** `RouteResult::redirect($location, int $code = 301)` + `ResponseEmitter`/`PageRouterKernel` 301 desteği; `RequestNormalizer` sonrası canonical kontrolü (host/şema/slash/uzantı/lowercase) → uyuşmuyorsa **tek 301**; auth/durum 302'leri (ADR-008) korunur | Backend Architect | 1 gün |
| 7 | **Loop/chain test kapısı:** §2.2e 5 testin tamamı (curl -I matrisi, 0 hop canonical, çift katman, host döngüsü, auth 302 ayrımı) — hepsi geçmeden adım 4/6 prod'a girmez | QA Engineer | 4 saat |
| 8 | **CI gate:** curl matrisi + HSTS header assert + "canonical yanıtta 301 dışında redirect yok" kontrolü (drift riski §4.3 #6) | DevOps Engineer + QA | 4 saat |
| 9 | HSTS preload başvurusu (ramp tamamlandıktan **sonra**, apex üzerinden) + hstspreload.org doğrulaması | Security Engineer | 2 saat (+ form onayı beklemesi) |
| 10 | Cross-ref denetimi: ADR-004 (domain haritası + `ReturnUrlPolicy`), ADR-006 (TTFB ölçümü), ADR-008 (302 auth sınıfı korunuyor), ADR-016 (path/query sınırı — düz metin) çelişmiyor | Security + Backend | 2 saat |
| 11 | Vault senkronu: `[[../../brain]]` ADR-009 özeti + `.ai/.decisions/index.md` §3 satır durumu + debate/Tech Lead sonuçları (`log.md` append-only) | MO (vault-updater) | 1 saat |
| 12 | **Debate Şart 1 — 301 geçişi + staging loop testi:** 302→301 geçişi (adım 6) staging'de §2.2e testleriyle doğrulanır — 301 tarayıcıda süresiz cache'lenir, yanlış geçiş istemcide kalıcıdır (§1.3 kaynak 22, 23, 24) → staging loop testi geçmeden prod'a girmez | Backend Architect + QA Engineer | 1 gün |
| 13 | **Debate Şart 2 — HSTS çelişkisi kod lehine düzeltme:** `shared/src/Middleware/CLAUDE.md` (satır 42/58/96) HSTS iddiası **PLANNED (kodda yok)** olarak düzeltildi (`SecurityHeadersMiddleware.php` satır 26-41'te `Strict-Transport-Security` YOK) — kod eklenince iddia tekrar IMPLEMENTED olur (adım 5 ile birlikte) | Security Engineer | 2 saat (uygulandı 2026-09-24) |
| 14 | **Debate Şart 3 — CI redirect-loop testi:** CI gate'e (adım 8) redirect-loop assert'i eklenir — matris satırı 1-5'te loop (`ERR_TOO_MANY_REDIRECTS`) ve çift-atlama üretilmediği zorunlu kontrol | DevOps Engineer + QA Engineer | 4 saat |

### 5.2 Geri Dönüş Planı

Karar süreç karardır; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Canonical yön değişirse** (ör. www'ya geçiş): yeni ADR + eski matristeki 301'lerin tersine çevrilmemesi (istemci cache'i nedeniyle ters 301 döngüsü üretilmez, yeni hedefe 301 ile gidilir — §1.3 kaynak 22, 23); (2) **Sunucu katmanı kapanırsa/koparsa:** uygulama yedek katman devrede kalır (§4.4 #1) — kanal hiç kapanmaz; (3) **HSTS geri alınacaksa:** ramp geri alma + varsa preload removal (§4.4 #3 — süreç haftalar, preload'a geç girme disiplini bu yüzden var); (4) **302'ye dönüş** (canonical sınıfında) kritik ihlal — derhal revert + `log ERROR`; yalnız auth/durum sınıfı (ADR-008) 302 kalır; (5) **Vault bozulursa:** `git checkout` + son commit (AGENTS §17 #10); (6) **Veri/state kaybı yoktur** — bu ADR URL/Host karar katmanıdır; geri dönüş sunucu reload + kod revert ile dakikalar içinde uygulanır (HSTS preload hariç — §4.4 #3).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA — canonical host tercihi apex'in bu domain haritasıyla hizalanması; `ReturnUrlPolicy` allowlist'i (apex + 4 subdomain) bu kararın kod temeli; **dosya diskte VAR** ✅ |
| [[ADR-006-performance-targets]] | TTFB/CWV bütçesi — chain atlama sayısının bu bütçeye etkisi; §1.3 performans gerekçeleri bu ADR'nin ölçüm bağlamı; **dosya diskte VAR** ✅ |
| [[ADR-008-bypass-auth-middleware]] | Auth/durum redirect'lerinin 302 sınıfı olarak bu ADR'nin dışında olduğunu sabitler (§2.2c); **dosya diskte VAR** ✅ |
| [[../index]] | Karar dizini — bu ADR'nin kaydı §3 `[[ADR-009-clean-url-redirect]]` (slug eşleşmesi ✅ — satır diskte mevcut) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Hard Guardrail (#16 şablon zorunluluğu), REDACTED politikası |
| [[../../AGENTS.md]] | §21 Cross References; §10 escalation; §17 #10 vault kurtarma; §25.3 frozen/append-only kuralları; §24.1 Ultrathink |
| [[../../brain]] | Mimari karar özeti — ADR-009 satırı (`| ADR-009 | Clean URL redirect |`) vault-sync ile tazelenir; **ADR-016 düz metin kaynağı** (`vault: brain.md ADR-016`) |
| [[../../log]] | Audit trail — bu ADR ve debate/revizyon kayıtları append edilir (append-only) |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| `shared/src/PageRouter/RequestNormalizer.php` | Host/scheme/port/URI normalize (IMPLEMENTED, sessiz) — uygulama yedek katmanının mevcut taşıyıcısı; 301 canonical kontrolü buraya eklenir (PLANNED §5.1 #6) |
| `shared/src/PageRouter/ResponseEmitter.php` · `RouteResult.php` · `PageRouterKernel.php` | 302 varsayılan/zorlaması (IMPLEMENTED satır 80-89 / 43-60 / 157-165) — 301 sözleşmesi PLANNED |
| `shared/src/PageRouter/PageRouter.php` | Kök `/` auth redirect (302 IMPLEMENTED satır 65-78 — kapsam dışı sınıf) + `trim('/')` davranışı (trailing-slash gerekçesi) |
| `shared/src/Middleware/SecurityHeadersMiddleware.php` | HSTS header'ı **YOK** (satır 26-41) → PLANNED (§5.1 #5) |
| `shared/src/Security/ReturnUrlPolicy.php` | Redirect hedefi allowlist (IMPLEMENTED satır 7-80) — open-redirect kalkanı; `www` YOK → apex tercihini destekler |
| `shared/src/Middleware/CLAUDE.md` | HSTS iddiası (satır 42/58/96) kodla çelişiyordu → **✅ düzeltildi 2026-09-24 (Debate Şart 2 — §5.1 #13): iddia → PLANNED (kodda yok)**; kod eklenince tekrar IMPLEMENTED (§5.1 #5) |
| **Debate Şart 1 — 301 geçişi + staging loop testi (§5.1 #12)** | Yayın kapısı: 301 geçişi staging'de §2.2e loop testleriyle doğrulanmadan prod'a girmez (301 süresiz cache'lenir) |
| **Debate Şart 3 — CI redirect-loop testi (§5.1 #14)** | CI gate: redirect-loop/drift assert'i — matris satırı 1-5'te loop ve çift-atlama üretilmez (§2.2e, §4.3 risk 1/6) |
| `.ai/architecture/k13-cicd/staging-environment.md` · `kubernetes-deploy.md` | `nginx.ingress.kubernetes.io/ssl-redirect: "true"` (https zorlama niyeti IMPLEMENTED-as-spec — sunucu katmanının mimari gerekçesi) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅, v7.2.0 — 2+ çapraz kaynak) |
| **Düz metin (dosya diskte YOK — wiki-link kurulmaz):** ADR-016 (URL Normalization) | `henüz yazılmadı — vault: brain.md ADR-016`; `.ai/.decisions/index.md` §3 `[[ADR-016-url-normalization]]` kayıtlı — **path/query encoding, Unicode/path normalizasyonu sınırı** (§1 bağlayıcı) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-009'u sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate onayı (3 tur / 20 persona — 18/2/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/ADR-008 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-24 |
| Tur 1 — 20 persona | **15 kabul/neutral · 4 uyarı** — kod kanıtı: `ResponseEmitter.php` satır 80-89 + `PageRouterKernel.php` satır 157-165 her yerde 302; 301 sözleşmesi yok → şart. Uyarılar: **DevOps** (nginx conf = 0 → sunucu katmanı PLANNED), **QA** (loop testi), **Security** (www canonical eksiği), **Critic** (HSTS iddia-kod çelişkisi) |
| Tur 2 — İtiraz→çözüm | (1) 302→301 geçişi — **301 süresiz cache'lenir → staging'de doğrula** → şart; (2) sunucu katmanı PLANNED (conf yok) → **uygulama katmanı geçici birincil** notu; (3) HSTS Middleware/CLAUDE.md iddiası kodda yok → **düzeltme şart** (Şart 2); (4) apex+https canonical **tek tercih** — tersine çevirme = yeni karar |
| Tur 3 — Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **✅ KABUL (18/2/0)** — 2026-09-24 (≥2/3 karşılandı) |
| Karara dönüşen şartlar | 3 şart §5.1'e satır olarak eklendi: **#12 (Şart 1)** 301 geçişi + staging loop testi; **#13 (Şart 2)** HSTS çelişkisi kod lehine düzeltme; **#14 (Şart 3)** CI redirect-loop testi (§6'da çapraz kayıt) |
| Tech Lead | **✅ (2026-09-24 — debate sonrası onay)** |
| Şartlar | **3 şart §5.1'e eklendi (revizyon — frozen değil):** #12 301 geçişi + staging loop testi · #13 HSTS iddia düzeltmesi (uygulandı 2026-09-24) · #14 CI redirect-loop testi |

---

*ADR-009 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/ yeni seri; slug: `clean-url-redirect`)*
*Authority: ADR-009 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
