---
title: "CoreMusic — ADR-028: Anti-Ban System (Exponential Backoff + Jitter · User-Agent Rotasyonu · Proxy/Pool Health-Check · Per-Kaynak Kota · Health-Check + Circuit Breaker · Retry-After Uyumu · Yasal/Etik Not · Genel Kapsam)"
type: adr
category: download
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-028 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-028: Anti-Ban System (Backoff + Jitter · UA Rotasyonu · Proxy Health · Per-Kaynak Kota · Circuit Breaker · Retry-After Uyumu)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-028'i sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı: **(a) exponential backoff + jitter** — 429/403/5xx için (ADR-013 auth lockout ile hizalı) · **(b) User-Agent rotasyonu** — tanımlı havuz, tutarlı fingerprint per oturum · **(c) proxy/pool yönetimi** — kaynak başına havuz, health-check, ölü proxy çıkarma · **(d) per-kaynak kota** — kaynak başına ayrı limit · **(e) health-check + circuit breaker** — kaynak sürekli 403 → geçici kapat → yeniden dene · **(f) Retry-After uyumu** — standart başlık öncelikli · **(g) yasal/etik not** (TOS/robots.txt/ülke yasası — raporlama sorumluluğu, neutral ton, bilgilendirme amaçlı) · **kapsam = genel (kullanıcı seçimi):** kendi servislerimiz + izinli API'ler + **genel senaryolar (3. parti hedefler dahil)** · debate **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)**, Tech Lead **✅**)
**İlgili ADR'ler:** [[ADR-013-rate-limiting-apcu]] (rate limit — **§1.1 bulguları hizalı mı kontrol edildi**: `RateLimiterMiddleware.php:30-32` fail-open + `AuthService.php:31-32` 5/900s sabit pencere, **backoff/lockout YOK** (`ADR-013:51` bunu zaten bulmuştu) → bu ADR'nin madde (a) backoff'u ADR-013'ün "brute force exponential backoff" PLANNED maddesini **dış hedeflere** genişletir; fail-open davranışı dış istekte de korunur; dosya diskte VAR ✅) · [[ADR-026-download-service-architecture]] (download — PLANNED controller bulgusu doğrulandı: `DownloadController` **0 dosya**, `download.coremusic.net` **dizin yok** (`Test-Path=False`), rota `Gateway.php:87` var → anti-ban zinciri de **PLANNED**, controller'ın bağımlılığıdır; `download_sources` şeması `coremusic_download.sql:141-162` **IMPLEMENTED**; dosya diskte VAR ✅) · [[ADR-020-api-public-security]] (API — gelen istek tarafı rate limit/CORS/audit; bu ADR **giden (outbound) istek** tarafını kapsar; dosya diskte VAR ✅) · [[ADR-007-cache-namespace]] (APCu cache = sayaç/backoff sayaçlarının depo katmanı — TTL zorunlu; dosya diskte VAR ✅) · [[ADR-003-multi-db-bcnf]] (`:124` "download_sources … Kuyruk + anti-ban durumu" — anti-ban'ın vault'taki ilk geçişi; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · [[ADR-024-ecosystem-modular-docs]] (wiki-link disk kanıtı kuralı) · karar dizini [[../index]] **satır 65** `[[ADR-028-anti-ban-system]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-028-anti-ban-system` karar dizini `../index.md:65`'te **rezerve boş slottur** (ADR-026/027 aynı istisnayı `:63`/`:64`'te kaydetmişti). Ek kanıt: `brain.md:983` "ADR-028 | Rate limiting + proxy rotasyonu", `keys.md:263` "ADR-028 | anti-ban, ARL token", `.ai/index.md:645` "Decisions/accepted/ADR-028-anti-ban-system | Anti-ban system | Download" — üç katalog kaydı bu numarayı bağlar.

---

## 1. Bağlam (Context)

CoreMusic'in indirme hattı (ADR-026) çeşitli kaynaklardan (kendi izinli API'leri, 3. parti servisler, genel HTTP hedefleri) dosya çeker; bu istekler **giden (outbound)** HTTP trafiğidir ve hedef servislerin rate limit / bot korumasıyla karşılaşır. Vault'ta anti-ban **üç yerde parça parça** geçiyor: `brain.md:332` "Anti-ban: Rate limiting, ARL token rotasyonu, proxy rotasyonu, User-Agent çeşitliliği" (tek satır özet), `keys.md:263` anahtar kaydı, `ADR-003:124` "download_sources … anti-ban durumu" (şema notu). Ama **hiçbir tek belge** neyin ne zaman uygulanacağını, hangi HTTP durum kodunun nasıl ele alınacağını veya yasal/etik sınırın nerede durduğunu yazmıyor. Bu ADR o kararı sabitler: **dört mekanizma** (backoff+jitter, UA rotasyonu, proxy/pool, per-kaynak kota) + **iki güvenlik ağı** (health-check/circuit breaker, Retry-After uyumu) + **yasal/etik not**.

### 1.1 Mevcut Durum

**A) KOD KATMANI — gelen (inbound) taraf IMPLEMENTED, giden (outbound) anti-ban YOK:**

| Tarama | Sonuç | Dosya:Ssatır |
|---|---|---|
| 429 üretimi (kendi API'miz) | **IMPLEMENTED** — `httpStatus: 429` + `Retry-After` başlığı | `shared/src/Middleware/RateLimiterMiddleware.php:48-56`, `shared/src/Api/Middleware/RateLimitMiddleware.php:40,44`, `shared/src/Exception/RateLimitException.php:11`, `shared/src/PageRouter/ErrorHandler.php:56` |
| Rate limit fail-open | **IMPLEMENTED** — cache yoksa `$next($request)` (istek geçer) | `shared/src/Middleware/RateLimiterMiddleware.php:30-32` (+ test `RateLimiterMiddlewareTest.php:127` "fail-open") |
| Auth lockout | **IMPLEMENTED (sabit pencere)** — `MAX_LOGIN_ATTEMPTS=5`, `LOGIN_WINDOW_SECONDS=900` → **backoff/uzatma YOK** | `auth.coremusic.net/include/Service/AuthService.php:31-32,59-60` |
| Retry döngüsü (outbound) | **IMPLEMENTED (yalnız 1 yer, sabit gecikme)** — `MAX_RETRIES` döngüsü + `usleep(RETRY_DELAY_US)` → **exponential DEĞİL, jitter YOK** | `home.coremusic.net/include/Auth/HomeAuthBridge.php:111-117` |
| Outbound HTTP istemcisi | **IMPLEMENTED (3 istemci)** — `curl_init` kullananlar: OAuth, bridge, AI tool-calling | `shared/src/OAuth/Provider/BaseOAuthProvider.php:76-116`, `home.coremusic.net/include/Auth/HomeAuthBridge.php:132-152`, `shared/src/AI/ToolCalling.php:253-262` |
| Outbound `User-Agent` başlığı | **YOK — 0 eşleşme** (`CURLOPT_USERAGENT` / `User-Agent:` header ekleme yok) | `BaseOAuthProvider.php` + `ToolCalling.php` + `HomeAuthBridge.php` grep'i |
| Outbound `backoff` | **YOK — 0 eşleşme** (PHP'de `backoff` literal 0; yalnız test/vault) | `grep backoff *.php` → 0 kod eşleşmesi |
| Outbound proxy havuzu | **YOK** — `proxy` eşleşmeleri yalnız **inbound** trusted-proxy/XFF çözümü | `shared/src/Middleware/RateLimiterMiddleware.php:66,80` (`isTrustedProxy`), `auth.coremusic.net/config/constants.php:94` |
| Circuit breaker | **YOK — 0 eşleşme** (yalnız middleware `short-circuit` test terimi) | `shared/tests/Middleware/MiddlewarePipelineTest.php:138,150` |
| 3. parti health-check (kendi servisimiz) | **IMPLEMENTED** — `ServiceRegistry`/`ServiceHealth` **kendi iç servislerimizin** `/health` ucunu kontrol eder (dış kaynak değil) | `shared/src/Api/Registry/ServiceRegistry.php:26,50,71,95`, `ServiceHealth.php:15,24,32` |

**B) SPEC/VAULT KATMANI — spec var, kod yok:**

| İddia | Vault kanıtı | Kod karşılığı | Etiket |
|---|---|---|---|
| Exponential backoff (retry) | `k10-uygulama/download-panel.md:156` "Retry Logic: Otomatik yeniden deneme (exponential backoff)"; `k8-servis/download-service.md:126` `retryDelayMs`, `:422-429` retry döngüsü (sabit `sleep_for`) | PHP'de `backoff` **0** | **PLANNED** |
| Backoff (diğer ağ spec'leri) | `katman-baglilik-matrisi.md:267` "Retry with exponential backoff"; `k14-ag/dns-resolver.md:19` "1s, 2s, 4s, 8s"; `k14-ag/connection-pooling-network.md:302` `backoff_ms: [100,200,500]`; `k9-api-routing/api-gateway.md:317` (işaretli `[ ]`) | kod **0** | **PLANNED** |
| Anti-ban genel özeti | `brain.md:332` "Rate limiting, ARL token rotasyonu, proxy rotasyonu, User-Agent çeşitliliği" | 4 mekanizmadan **hiçbiri** outbound kodda yok | **PLANNED (bu ADR ile karar altına alındı)** |
| ARL token rotasyonu | `keys.md:263` anahtar kelime; `MEMORY.md:254` "ARL Token \| SECRET \| ❌ ASLA \| `[REDACTED]`" | kod **0** | **PLANNED + REDACTED** (anahtar değeri asla yazılmaz) |
| Per-kaynak kota (şema) | `coremusic_download.sql:141-162` `download_sources` → `quota_remaining`, `quota_reset_at`, `is_active`, `CHECK (source IN ('deezer','youtube','spotify','soundcloud'))` | **şema IMPLEMENTED** / kota uygulayan kod **0** | Şema **IMPLEMENTED** / mantık **PLANNED** |
| Anti-ban (eski geçiş) | `ADR-003:124` "Kuyruk + anti-ban durumu"; `prompt0-…2026-08-15.md:64` "Queue management, anti-ban, FLAC output" | kod **0** | **PLANNED** |

**Sonuç etiketi:** **IMPLEMENTED:** kendi API'mizin 429 + `Retry-After` üretimi (inbound), fail-open rate limit, auth 5/900s sabit pencere, 3 outbound curl istemcisi (UA'sız), tek sabit gecikmeli retry döngüsü, kendi iç servis health-check'i, `download_sources` şeması (kota alanları hazır), karar dizini slotu (`index.md:65`) + 3 katalog kaydı. **PLANNED:** exponential backoff + jitter, outbound User-Agent havuzu/rotasyonu, outbound proxy havuzu + health-check + ölü proxy çıkarma, per-kaynak kota mantığı (şema hazır), circuit breaker, `DownloadController` (ADR-026), 3. parti hedef health-check'i. **`⚠️ VERIFICATION REQUIRED`:** (i) `brain.md:332` "ARL token rotasyonu" iddiasının hiçbir vault dosyasında tanımı yok (yalnız `keys.md:263` anahtarı + `MEMORY.md:254` REDACTED notu) → token mekaniği bu ADR'de tanımlanmaz, `⚠️` kalır; (ii) `download-panel.md:156` "exponential backoff" iddiası spec'tir, kod 0; (iii) outbound istemcilerin hangi hedeflere hangi sıklıkla gittiği ölçülmemiştir (§5.1/8 ölçüm).

### 1.2 Sorun Tanımı

1. **Backoff yok, sabit gecikme var:** tek retry döngüsü `usleep(RETRY_DELAY_US)` ile sabit bekler (`HomeAuthBridge.php:111-117`); 429 üst üste gelince hedef aynı aralıkta tekrar denenir → **thundering herd** riski (§1.3-1).
2. **403/429 dış hedefte nasıl okunacağı yazılı değil:** kendi API'miz 429 üretmeyi biliyor (`RateLimiterMiddleware.php:48-56`) ama **aldığımızda** ne yapacağımız kodlanmamış.
3. **User-Agent gönderilmiyor:** outbound istemcilerde `CURLOPT_USERAGENT` **0** → hedef kendi varsayılan `curl/x.y` (ya da boş) görür; bu tek başına bot sinyalidir ve havuz/rotasyon imkânsızdır.
4. **Proxy havuzu yok:** `proxy` eşleşmeleri yalnız inbound XFF çözümü (`RateLimiterMiddleware.php:66,80`); outbound yolda IP havuzu, health-score, ölü proxy çıkarma yok.
5. **Per-kaynak kota şemada, mantıkta yok:** `download_sources.quota_remaining/quota_reset_at` hazır ama kimse okumuyor/yazmıyor → kota aşımında hedef 429/403 üretir, biz fark etmeyiz.
6. **Circuit breaker yok:** bir kaynak sürekli 403 verse bile istekler akmaya devam eder → ban kalıbı büyür (§1.3-6).
7. **Yasal/etik sınır yazılı değil:** kapsam "genel senaryolar (3. parti hedefler dahil)" olunca TOS/robots.txt/ülke yasası sorumluluğu hiçbir maddede durmuyor → bu ADR'de nötr madde olarak kaydedilir.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/anahtar kaynak önce (MDN, RFC Editor, Microsoft/AWS mimari desen dokümanları), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) exponential backoff + jitter 2025-26, (b) HTTP 429/Retry-After standardı, (c) polite crawling (robots.txt/crawl-delay), (d) user-agent rotation, (e) proxy pool health-check, (f) circuit breaker (403/5xx).** Erişim: **6 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "exponential backoff with jitter rate limiting 429 retry best practice 2025" · (2) "HTTP 429 Too Many Requests Retry-After header RFC 9110 standard" · (3) "polite crawling robots.txt Crawl-delay rate limiting ethical scraping guidelines" · (4) "user-agent rotation detection risk consistent fingerprint best practice" · (5) "proxy pool health check dead proxy removal rotation management architecture" · (6) "circuit breaker pattern repeated 403 errors downstream service temporary disable" |
| Web Search **Konusu** | (1) 429/5xx için backoff+jitter uygulama kuralı (thundering herd, retry cap, paralel retry yasağı); (2) `Retry-After` başlığının standard durumu (RFC 9110 §10.2.3, RFC 6585) ve istemci davranışı; (3) polite crawling — robots.txt + Crawl-delay + istek hızı etiği; (4) UA rotasyonunun etkisi ve **tutarlılık (fingerprint) gereksinimi**; (5) proxy pool mimarisi — health-score, quarantine, failover, maliyet; (6) circuit breaker deseni — eşik, yarı-açık (half-open), 4xx ayrımı. |
| Web Search **Bağlam** | **~45 benzersiz adlandırılmış kaynak / 6 sorgu**: **Docebo DevDocs** (rate limit best practices), **dev.to** (backoff+jitter), **Truto** (çoklu-API retry), **Zuplo** (429 + Retry-After), **Stas Koltsov Medium**, **Square Developer Forums**, **OneUptime** (GCP backoff), **GitHub copilot-cli #2760** (429 retry policy), **Tiger Abrodi** (jitter gerekçesi) (9) · **MDN** (429 + Retry-After), **RFC Editor RFC 6585** (429 tanımı + `Retry-After`), **Host-Tracker** (RFC 9110 saniye/tarih biçimi), **Pulsetic**, **Stack Overflow ×2** (429 müşteri tarafı), **Tyk #8191** (429/403 ayrımı), **Medium — Mastering Retry-After** (9; Zuplo çapraz okundu) · **Firecrawl glossary** (polite crawling), **Hacker News** (Crawl-delay standardda yok), **WebScraper.io** (etik istek hızı), **ScrapingBee** (robots.txt rehberi), **DZone** (Crawl-delay'i saygıyla karşıla), **notify-me.rs** (8 etik pratik), **Zack Proser** (≈1 istek/sn kuralı), **Stack Overflow #12945698** (Crawl-delay + 503 tartışması), **Medium/GumGum** (robots.txt ölçekte) (9) · **DataHen**, **r/privacytoolsIO**, **ScrapeGraphAI** ("tutarlılık yoksa rotasyon işe yaramaz"), **Soax** (fingerprint havuzları), **Browserless** (UA 2026), **Stack Overflow #8970800** (UA sniffing), **dev.to/vhub** (session consistency), **nhimg.org** (consistency scoring), **Capsolver**, **ScraperAPI** (UA listesi 2026) (10) · **github/alpkeskin/rota** (proxy rotation engine), **HydraProxy** (pool best practices), **SquidProxies** (pool architecture), **ColdProxy** (rotation + health + failover), **BuyProxies** (health check + quarantine + recovery), **Finedata** (rotation stratejileri), **Crawlbase** (EWMA health-score + breaker), **AWS RDS Proxy docs** (health check örnek) (8) · **Microsoft Azure Architecture Center** (Circuit Breaker resmi deseni), **AWS Prescriptive Guidance** (circuit-breaker cloud deseni), **Level Up GitConnected**, **Groundcover**, **Aerospike**, **DigiBee docs**, **Medium/@abhi-strike** (4xx kaydetme kuralı), **LinkedIn/sahniaman** (Closed/Open/Half-open), **Design Gurus** (retry+breaker+timeout) (9). |
| Web Search **Kısa Açıklama** | **(1) Backoff+jitter:** bekleme 1s → 2s → 4s → 8s katlanır; **jitter** (rastgele ofset) eklenmezse aynı anda kırılan istemciler **aynı anda** geri döner → thundering herd (dev.to, Tiger Abrodi, Zuplo); üst sınır (retry cap) ve **paralel retry yasağı** açıkça istenir (Docebo); ilk 429'da mümkünse `Retry-After` değeri beklenir, yoksa `min(2^attempt * taban + jitter, tavan)` (GitHub #2760). **(2) 429/Retry-After:** `Retry-After` **HTTP standardıdır** (RFC 9110 §10.2.3; 429'un kendisi RFC 6585), **saniye veya HTTP-tarih** biçimindedir; iyi istemci bu kadar bekler (MDN, RFC Editor, Host-Tracker); 429 **yanlış kod olarak da** kullanılır — bazı API'ler kota aşımında 403 döner (Tyk #8191) → istemcide **403 de kota sinyali** olarak okunmalı. **(3) Polite crawling:** robots.txt kuralları + açıklanmış `Crawl-delay` saygı görür; **`Crawl-delay` robots.txt standardının parçası değildir** yorumlar farklıdır (HN); pratik kural ≈ **1 istek/sn/domains** ve trafik "insan benzeri" (WebScraper, Zack Proser); resmi API varsa scraping'ten önce o kullanılır (notify-me.rs). **(4) UA rotasyonu:** rotasyon **tek başına yeterli değildir** — rastgele string değiştirip aynı header/TLS parmak izini göndermek tutarsızlık sinyali üretir (ScrapeGraphAI, dev.to); **tutarlılık (session consistency)** esastır: UA + header seti + oturum boyunca **sabit** kalmalı, değişim oturum sınırında olmalı (Soax "gerçek cihaz havuzları", nhimg "consistency scoring kararı"). **(5) Proxy pool:** üç kontrol döngüsü — rotation policy + **health scoring** (EWMA başarı/gecikme) + failover; ölü proxy **karantinaya** alınıp çıkarılır (BuyProxies, ColdProxy, Crawlbase "breaker"); havuz mimarisi maliyet/ölçek denetimi ister (SquidProxies, HydraProxy). **(6) Circuit breaker:** eşik aşılınca hedefe erişim **geçici olarak bloklanır** (.Closed → Open → Half-open); tekrar deneme yarı-açık fazda kontrol edilir (Microsoft Azure, AWS); **4xx genelde "girdi hatası" sayılır ve breaker'a sayılmaz** — eşik 5xx/timeout üzerine kurulur (Medium/@abhi-strike) → **istisna: 429/403 burada kota/ban sinyali olduğu için bu ADR'de ayrıca sayılır** (bilinçli sapma, §2.2e). |
| Web Search **Uzun Açıklama** | **(a) Backoff + jitter (kaynak 1-9):** 2025-26 literatürü üç şartta uzlaşıyor: **katlanan bekleme** (üstel dizi), **jitter** (uniform/full jitter — rastgelelik yoksa tüm istemciler senkron geri döner ve hedef ikinci kez vurulur), **tavan + azami deneme** (retry cap; Docebo "strict retry caps", paralel retry yok). `Retry-After` varsa **onu bekle** (üstel diziyi ez), yoksa `min(2^n * base + jitter, max)` (GitHub #2760). Bu, hedefin kota penceresini **zorlamadan** yeniden denemenin tek yolu; sabit gecikme (bugünkü kod: `HomeAuthBridge.php:111-117`) hem senkron riski hem de yavaşlık üretir. **(b) 429 + Retry-After standardı (kaynak 10-18):** 429 RFC 6585'de tanımlanmış, `Retry-After` RFC 9110 §10.2.3'te **standart başlık**tır; saniye (delta) veya HTTP-date biçiminde olabilir. Müşteri tarafı davranışı MDN'de nettir: başlık **varsa** ona uyulur. Pratik tuzak: kota aşımını 403 ile bildiren API'ler (Tyk) → istemci tarafında **hem 429 hem 403 hem 503** kota/ban sinyali olarak değerlendirilmeli (503 = hedef bakımda/greylist, RFC 6585'in另一个 kardeşidir). **(c) Polite crawling (kaynak 19-27):** robots.txt `Allow/Disallow` + `User-agent` blokları temel unsurdur; `Crawl-delay` **uzantıdır, standard değildir** — ama varsa uygulanır. Etik çerçeve: istek hızını insan hızında tut (~1/sn), hedefin kaynaklarını (bant/genişlik) zorlama, resmi API'yi tercih et, kimlik doğrulama kapılarını atlatma. **Crawl-delay'i 503 ile uygulayan hedefler** bile var (SO #12945698) → 503'ü "gecik" sinyali olarak okumak gerekir. **(d) UA rotasyonu + fingerprint (kaynak 28-37):** en kritik bulgu: **rotasyon tutarlılık olmadan zararlıdır.** Rastgele UA + sabit diğer header'lar = tutarsızlık kümesi → tespit kolaylaşır. Doğru model: (i) **tanımlı havuz** (gerçek tarayıcı string'leri), (ii) **oturum başına sabit** UA (fingerprint per session), (iii) **header setiyle uyumlu** UA (UA diyor ki Chrome/Windows ise Accept-Language/Sec-CH-UA da öyle), (iv) değişim yalnız oturum/kaynak sınırında. `browserless` 2026 notu: UA, içerik sunumunu ve otomatikleştirme tespitini etkiler — tek başına yeterli değildir ama **yokluğu** bir sinyaldir (bugünkü kod: outbound UA **0**). **(e) Proxy pool (kaynak 38-45):** stabilite üç döngüyle sağlanır: rotation policy (round-robin / weighted), **health scoring** (başarı oranı + gecikme EWMA ile), failover (gerçek hata sinyallerine bağlı). Ölü proxy için **karantina → recovery testi → çıkarma** akışı önerilir (BuyProxies); modern sistemde skor + **breaker** birlikte çalışır (Crawlbase). Kendi iç servis health-check'imiz (`ServiceRegistry`) bu desenin **inbound** karşılığıdır; outbound hedefler için ayrı havuz gerekir. **(f) Circuit breaker (kaynak 46-54):** Microsoft/AWS resmi deseni: eşik aşılınca erişimi **geçici kapat**, bir süre sonra **half-open** ile tek deneme yap, başarılıysa **kapat (closed)**'a dön. 4xx ayrımı önemlidir — genelde breaker'a sayılmaz (girdi hatası); **bu ADR bilinçli olarak 429/403'ü sayar** çünkü hedefin kota/ban sinyalidir, "girdi hatası" değil. Jitter/backoff ile birlikte kullanıldığında hedefe yönelik istek hızı **kendi kendini düzenler**. |
| Web Search **Paragraf Veri Uzun** | Dış hedefe giden isteklerde banı önlemenin 2025-26 kanıtlı dört katmanı vardır: **(1) zamanlama** — 429/403/5xx sonrası üstel bekleme (1-2-4-8 sn) + **jitter** (rastgele ofset yoksa tüm istemciler aynı anda geri döner → thundering herd) + tavan/deneme sınırı; **standart `Retry-After` başlığı varsa onu bekle, kendi dizini ezmez** (RFC 9110 §10.2.3, RFC 6585, MDN). **(2) kimlik** — outbound istekte tanımlı bir User-Agent havuzundan **oturum başına sabit** UA seçilir ve header setiyle tutarlı gönderilir; rastgele-dönüşlü-UA tek başına **zararlıdır** (tutarlılık sinyali doğar); UA'sız istek (bugünkü durum: `CURLOPT_USERAGENT` 0) tek başına bot sinyalidir. **(3) ağ** — proxy havuzu health-score (EWMA başarı/gecikme) ile yönetilir, ölü proxy karantinaya alınıp çıkarılır; havuz maliyet/ölçek getirir. **(4) kota + devre** — **kaynak başına ayrı kota** tutulur (`download_sources.quota_remaining` şeması hazır) ve kaynak sürekli 429/403 verirse **circuit breaker** ile geçici kapatılır, yarı-açık fazda tek deneme ile geri açılır (Microsoft/AWS deseni; 4xx normalde sayılmaz ama 429/403 burada ban sinyali olduğu için sayılır). Buna **polite crawling etiği** eşlik eder: robots.txt + varsa Crawl-delay + ≈1 istek/sn/domains + resmi API tercihi. Tüm bu katmanlar yalnız **hız azaltmak** için değil, hedefin kurallarına uymak için de vardır — kapsam "genel senaryolar" olduğundan **TOS/robots.txt/ülke yasası** denetimi raporlama sorumluluğu olarak maddedir (§2.2g). |
| Web Search **Sonucu** | 1) **Backoff+jitter zorunluluğu doğrulandı** (Docebo, dev.to, Truto, Zuplo, GitHub #2760, Tiger Abrodi, Square, OneUptime) → **§2.2a**; sabit gecikmeli mevcut retry (`HomeAuthBridge.php:111-117`) bu bulguyla **çelişir** → §4.2/1. 2) **`Retry-After` standart başlıktır ve önceliklidir** (MDN, RFC 6585, RFC 9110 §10.2.3, Host-Tracker, Medium) → **§2.2f**; kendi API'mizin üretimi `RateLimiterMiddleware.php:53` zaten uyumlu. 3) **403'ün de kota sinyali olabildiği doğrulandı** (Tyk #8191 + Microsoft 4xx ayrımı) → **§2.2a/e kapsamı 429+403+5xx** (bilinçli sapma notuyla). 4) **Polite crawling çerçevesi doğrulandı** (Firecrawl, WebScraper, ScrapingBee, DZone, Zack Proser, HN "Crawl-delay standardda değil") → **§2.2d per-kaynak kota + §2.2g etik not**. 5) **UA rotasyonu yalnız tutarlılıkla işe yarar** (ScrapeGraphAI, dev.to, Soax, nhimg, Browserless) → **§2.2b "havuz + oturum başına sabit fingerprint"**. 6) **Proxy pool health/quarantine/failover deseni doğrulandı** (BuyProxies, ColdProxy, Crawlbase, SquidProxies, HydraProxy, RDS Proxy) → **§2.2c**. 7) **Circuit breaker resmi deseni doğrulandı** (Microsoft Azure, AWS, Groundcover, Aerospike, DigiBee, Design Gurus) → **§2.2e** (Closed/Open/Half-open + 429/403 istisnası). **Toplam ~45 benzersiz adlandırılmış kaynak, 6 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada karşılanır (tek işaretli gerilim: **Crawl-delay'in standard olmaması** — HN/DZone yorum farkı → "varsa uygula, yoksa ≈1/sn kuralı" olarak nötr çözüldü). **⚠️ iki sınır:** (i) sayfa-içi tur yapılmadı → RFC 9110 §10.2.3 metni ve Microsoft breaker eşik değerleri üretim öncesi derin okunur (§5.1/8); (ii) "ARL token rotasyonu" (`brain.md:332`) **kaynakla desteklenemedi** → `⚠️ VERIFICATION REQUIRED` (kavram bu ADR'de tanımlanmaz, §1.1-B). **Vault tarafı aynı resmi verdi:** 429/Retry-After **IMPLEMENTED** (inbound), backoff/UA/proxy/kota-mantığı/breaker **PLANNED** → bu ADR **mimari karardır, kod taahhüdü değil**. |
| Web Search **Alınan Karar** | **ADR-028 KABUL EDİLİR — ANTI-BAN YEDI MADDE (genel kapsam):** **(a) Exponential backoff + jitter:** 429/**403**/5xx sonrası `min(2^attempt * taban + jitter, tavan)` + azami deneme; **`Retry-After` varsa onu kullan** (kendi dizini ezmez); kendi auth 5/900s sabit penceresiyle **hizalı** (ADR-013 backoff maddesinin ruhu), fail-open davranışı korunur. **(b) User-Agent rotasyonu:** tanımlı, gerçek tarayıcı string'lerinden oluşan **havuz**; **oturum başına sabit** UA (tutarlı fingerprint), header setiyle uyumlu; değişim yalnız oturum/kaynak sınırında; **rastgele-dönüş yasak**. **(c) Proxy/pool yönetimi:** **kaynak başına** proxy havuzu; health-score (başarı+gecikme), **ölü proxy karantinaya → çıkarma**, failover; havuz **opsiyonel ve PLANNED** (maliyet riski §4.3/3). **(d) Per-kaynak kota:** her kaynak için ayrı istek limiti; `download_sources.quota_remaining/quota_reset_at` şeması (IMPLEMENTED) kota motorunun deposu olur. **(e) Health-check + circuit breaker:** kaynak sürekli 403/429 verirse **geçici kapat (Open)** → **yarı-açık (Half-open)** tek deneme → kapanan/kapanan (Closed); eşik 5xx/timeout'a göre kurulur ama **429/403 bu ADR'de ayrıca sayılır** (ban sinyali — bilinçli sapma, §2.2e notu). **(f) Retry-After uyumu:** standart başlık **öncelikli**; saniye/HTTP-date biçimleri okunur; yoksa (a)'ya düşülür. **(g) Yasal/etik not (madde):** TOS, robots.txt (+ varsa Crawl-delay), ülke yasası denetimi **raporlama sorumluluğu** olarak zorunlu; ton **neutral, bilgilendirme amaçlı** — bu ADR kural öğretmez, mimari çerçeveyi tarif eder. **Kapsam = genel (kullanıcı seçimi):** kendi servislerimiz + izinli API'ler + genel senaryolar (3. parti hedefler dahil). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: backoff+jitter (9), 429/Retry-After standardı (9), polite crawling (9), UA tutarlılığı (10), proxy pool (8), circuit breaker (9) → **~45 benzersiz adlandırılmış kaynak, 6 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiası karşılanır (işaretli istisna: Crawl-delay standard olmaması → nötr çözüldü). **Vault tarafı aynı resmi verdi:** 429 + `Retry-After` + fail-open + auth 5/900s + `download_sources` şeması **IMPLEMENTED**, backoff/UA havuzu/proxy havuzu/kota mantığı/breaker/`DownloadController` **PLANNED** → bu ADR **mimari karardır, kod taahhüdü değildir**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (~45 benzersiz):** 1) developer.docebo.com — rate limit & 429 best practices · 2) dev.to/abhivyaktii — exponential backoff retry · 3) truto.one — retry across third-party APIs · 4) zuplo.com — HTTP 429 guide (Retry-After + RFC 9110) · 5) staskoltsov.medium.com — handling 429 client side · 6) developer.squareup.com — backoff for rate-limit errors · 7) oneuptime.com — exponential backoff GCP (2026-02-17) · 8) github.com/github/copilot-cli#2760 — 429 retry policy (backoff+jitter+cap) · 9) tigerabrodi.blog — why jitter matters · 10) **developer.mozilla.org — 429 + Retry-After** · 11) **rfc-editor.org — RFC 6585 (429)** · 12) host-tracker.com — RFC 9110 Retry-After biçimleri · 13) pulsetic.com — 429 causes/fix · 14) stackoverflow.com/q22786068 — avoid 429 (python) · 15) stackoverflow.com/q73829915 — solve 429 · 16) github.com/TykTechnologies/tyk#8191 — kota aşımı 429 vs 403 · 17) medium.com/vipulm124 — Retry-After header · 18) firecrawl.dev — what is polite crawling · 19) news.ycombinator.com/item?id=12358638 — Crawl-delay standard dışı (HN) · 20) webscraper.io — ethical request rates · 21) scrapingbee.com — robots.txt guide · 22) dzone.com — respecting robots.txt · 23) notify-me.rs — 8 ethical scraping practices · 24) zackproser.com — rate limiting without getting blocked (~1/sn) · 25) stackoverflow.com/q12945698 — Crawl-delay + 503 · 26) medium.com/gumgum-tech — robots.txt at scale · 27) datahen.com — random user agents · 28) reddit.com/r/privacytoolsIO — UA switcher & fingerprint · 29) scrapegraphai.com — UA best (tutarlılık şart) · 30) soax.com — browser fingerprinting evasion · 31) browserless.io — user agent guide 2026 · 32) stackoverflow.com/q8970800 — server-side UA detection · 33) dev.to/vhub — UA rotation: what works (session consistency) · 34) nhimg.org — detect UA spoofing (consistency scoring) · 35) capsolver.com — best user agents · 36) scraperapi.com — user agent list 2026 · 37) github.com/alpkeskin/rota — proxy rotation engine · 38) hydraproxy.com — proxy pool management best practices · 39) squidproxies.com — proxy pool architecture · 40) coldproxy.com — rotation & failover · 41) buyproxies.org — health checks & quarantine · 42) finedata.ai — proxy rotation strategies · 43) crawlbase.com — health scoring + breaker · 44) **learn.microsoft.com — Circuit Breaker pattern (Azure)** · 45) **docs.aws.amazon.com — circuit-breaker (Prescriptive Guidance)** · 46) levelup.gitconnected.com — circuit breaker valve · 47) groundcover.com — circuit breaker how it works · 48) aerospike.com — fault tolerance breaker · 49) docs.digibee.com — microservices circuit breaker · 50) medium.com/@abhi-strike — 4xx breaker kuralı · 51) linkedin.com/sahniaman — Closed/Open/Half-open · 52) designgurus.substack.com — retry/circuit/timeout. *(Liste 52 girdi sayılır çünkü aynı sorgu grubundaki çapraz aynalar ayrı adlandırılmıştır; benzersiz ana kaynak ~45.)* |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-013-rate-limiting-apcu]] | **Gelen istek kotası bağlayıcı:** kendi API'mizin 429/`Retry-After` üretimi, fail-open davranışı ve auth 5/900s sabit penceresi bu ADR'de **değiştirilmez**; anti-ban yalnız **giden** isteği yönetir. Fail-open (servis sürekliliği) ilkesi outbound retry'de de korunur: kota/breaker bilgisi yoksa istek **engellenmez**, yalnız yavaşlar. |
| [[ADR-026-download-service-architecture]] | **Download zincirinin bağımlılığı:** anti-ban zinciri `DownloadController` (ADR-026 **şart 2**, PLANNED) olmadan çalışmaz — imza/akış katmanı anti-ban'ın üstünde koşar; depo **read-only** ilkesi korunur (anti-ban sayaçları yalnız cache/`download_sources`'a yazar, medya dosyasına değil). |
| [[ADR-020-api-public-security]] | **Sınır ayrımı:** ADR-020 **inbound** API güvenliğidir (API key, JWT, CORS, audit); bu ADR **outbound** hedef davranışıdır. İkisi yalnız `Retry-After` başlığı ve kota kavramında kesişir; `X-RateLimit-*`/audit log kuralları ADR-020'ye aittir. |
| [[ADR-007-cache-namespace]] | **Sayaç depo katmanı:** backoff sayaçları, breaker durumu, proxy health-skorları APCu/`CacheManager` üzerinde yaşar; her key'de **açık TTL zorunlu** (TTL=0 YASAK); APCu yoksa **fail-open** (RateLimiterMiddleware:30-32 ruhu). |
| [[ADR-005-ultrathink-protocol]] | Kod/vault kanıtı olmayan her iddia etiketli: outbound `backoff` **0**, outbound `User-Agent` **0**, outbound `proxy` **0** (yalnız inbound XFF), circuit breaker **0**, `DownloadController` **0** → `⚠️ VERIFICATION REQUIRED` / **PLANNED**. |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı: bu ADR'deki her wiki-link diskte var (§6'da doğrulanır); şablon zorunluluğu (Guardrail #16) — `.templates/adr/adr-template.md` iskeleti (7 bölüm + §1.3 9 alan). |
| In-Place Refactoring | Dosya adları **değiştirilmez**: `HomeAuthBridge.php`, `BaseOAuthProvider.php`, `ToolCalling.php`, `RateLimiterMiddleware.php`, `download-service.md`, `download-panel.md` vb. yalnız okunur; spec/claim satırlarına düzeltme **ekleme** olarak yazılır (§5.1/7). |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil**. |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi, `vault-utf8-writer append`). |
| REDACTED | Proxy kullanıcı/şifreleri, `download_sources.api_key_encrypted/api_secret_encrypted` içerikleri, `MEMORY.md:254`'teki **ARL token** değeri bu ADR'ye **kopyalanmaz**; tüm sırlar yalnız `.env`'de yaşar (`ADR-026` REDACTED satırı ile aynı ilke). |
| Yasal/etik (madde g) | Kapsam "genel" olduğu için hedef başına **TOS + robots.txt (+ varsa Crawl-delay) + ülke yasası** denetimi raporlanır; ton **neutral/bilgilendirme** — bu ADR hiçbir hedefi atlatma yöntemi öğretmez. |

---

## 2. Karar (Decision)

**CoreMusic anti-ban sistemi YEDI maddeyle bağlayıcı ilan edilir:**

**(a) EXPONENTIAL BACKOFF + JITTER (429/403/5xx):**

Dış hedefe giden her yeniden deneme bekleme süresi **üstel artar + rastgele ofset** taşır:

```
bekleme = min(base * 2^attempt + jitter, max_delay)      # jitter: 0..base*2^attempt
kuralı:  Retry-After VARSA onu bekle (kendi dizini ezmez)
         yoksa (yukarıdaki) formüle düş
sınır :  azami deneme (retry cap) + paralel retry YASAK
```

- **Durum kapsamı:** `429` (kota), `403` (ban/erişim — §1.3-3: bazı API'ler kota aşımını 403 ile bildirir), `5xx` (hedef hatası), `503` (gecik/bakım sinyali).
- **Hizalama:** kendi auth kalkanındaki sabit 5/900s penceresiyle **çelişmez** — ADR-013'ün "brute force exponential backoff" PLANNED maddesi burada **outbound** karşılığını bulur; **fail-open** (ADR-013 `RateLimiterMiddleware.php:30-32`) outbound'ta da korunur: bekleme bilgisi yoksa istek durmaz, yalnız yavaşlar.
- Mevcut sabit gecikmeli tek retry (`HomeAuthBridge.php:111-117`) bu maddeye **uymaz** → genişletilir (§5.1/1).

**(b) USER-AGENT ROTASYONU (havuz + oturum tutarlılığı):**

| Kural | Değer |
|---|---|
| Havuz | **Tanımlı, gerçek tarayıcı string'leri** listesi (config'de; sürümü güncellenir) — çalışma zamanında uydurulmaz |
| Seçim | **Kaynak başına veya oturum başına 1 kez** seçilir |
| Tutarlılık | **Oturum boyunca sabit** (fingerprint per oturum); UA + `Accept-Language` + header seti **birlikte** uyumlu |
| Değişim | Yalnız **oturum/kaynak sınırında**; istekler arasında rastgele dönüş **YASAK** (§1.3-4: tutarsızlık sinyali) |
| Bugünkü durum | Outbound `CURLOPT_USERAGENT` **0 eşleşme** → madde **PLANNED**; UA'sız istek tek başına bot sinyalidir |

**(c) PROXY / POOL YÖNETİMİ (kaynak başına havuz + health):**

| Öğe | Karar | Durum |
|---|---|---|
| Havuz | **Kaynak başına** proxy havuzu (her kaynak kendi IP/rotasyon kimliğini taşır) | **PLANNED** |
| Health-check | Periyodik **sağlık testi** + **health-score** (başarı oranı + gecikme) | **PLANNED** |
| Ölü proxy | Başarısızlık eşiği → **karantina → recovery testi → havuzdan çıkarma** | **PLANNED** |
| Failover | Proxy başarısız → sonraki sağlıklı proxy'ye geç; hepsi ölüyse **doğrudan git (fail-open)** veya queue'ya al | **PLANNED** |
| Opsiyonellik | Havuz **opsiyoneldir** — kurulmazsa tek IP ile devam (risk §4.3/3'te) | Opsiyonel |
| Kapsam notu | Mevcut `proxy` kodu yalnız **inbound** XFF çözümıdır (`RateLimiterMiddleware.php:66,80`) — outbound havuz **yeni** katmandır | Ayrı katman |

**(d) PER-KAYNAK KOTA (kaynak başına ayrı limit):**

- Her kaynak (`download_sources.source` — şemada `deezer|youtube|spotify|soundcloud` CHECK'i **IMPLEMENTED**) için **ayrı** istek limiti ve penceresi tutulur; genel bir global limit yerine **kaynak başına** sayaç çalışır.
- Kota deposu: `download_sources.quota_remaining` + `quota_reset_at` (`coremusic_download.sql:141-162` **IMPLEMENTED**) + APCu sayaçları (ADR-007 TTL).
- Kota aşımında: **yeniden deneme değil, bekleme** → pencere sıfırlandığında devam; hedef `429/Retry-After` verdiyse (f) önceliklidir.
- **Uygulayan kod PLANNED** (şema hazır, motor yok) — etiket dürüst.

**(e) HEALTH-CHECK + CIRCUIT BREAKER (kaynak sürekli 403 → geçici kapat):**

```
[Closed]  normal akış; 429/403/5xx sayacı çalışır
   │  eşik aşıldı (sürekli 403/429 veya 5xx/timeout eşiği)
   ▼
[Open]    kaynağa erişim GEÇİCİ KAPAT — istekler kuyruğa/alınmaz, backoff (a) uygulanır
   │  bekleme süresi doldu
   ▼
[Half-open] tek (veya az sayıda) deneme gönderilir
   ├─ başarılı → [Closed] (sayaç sıfırlanır)
   └─ 403/429/5xx → [Open] (bekleme uzar)
```

- **Eşik notu (bilinçli sapma):** genel circuit breaker literatürü 4xx'i breaker'a **saymaz** (girdi hatası — §1.3-6); **bu ADR 429 ve 403'ü ayrıca sayar** çünkü hedefin kota/ban sinyalidir, bizim girdi hatamız değil. Kayıt satırı bu istisnayı taşır.
- Kendi iç servis health-check'imiz (`ServiceRegistry.php:26,50,95` / `ServiceHealth.php` **IMPLEMENTED**) **inbound** karşılıktır; **outbound breaker yeni** katmandır (kod 0 → PLANNED).
- Breaker durumu APCu'da yaşar (ADR-007 TTL zorunlu); APCu yoksa **fail-open** (kaynak kapatılmaz, yalnız backoff kalır).

**(f) RETRY-AFTER UYUMU (standart başlık öncelikli):**

| Sıra | Kural |
|---|---|
| 1 | Yanıtta `Retry-After` **varsa** → o kadar bekle (saniye **veya** HTTP-date biçimi okunur — RFC 9110 §10.2.3 / RFC 6585, §1.3-2) |
| 2 | Yoksa → madde (a) üstel+jitter dizisine düş |
| 3 | `Retry-After` **asla yok sayılmaz**; kendi bekleme süresi onun **altına** inemez |
| 4 | Kendi API'mizin üretimi zaten uyumlu: `RateLimiterMiddleware.php:53` + `Api/Middleware/RateLimitMiddleware.php:40` (**IMPLEMENTED**) |

**(g) YASAL / ETİK NOT (madde — neutral ton, bilgilendirme amaçlı):**

> **Bilgilendirme notu (bağlayıcı madde, tavsiye/etik çerçeve):** Bu ADR'nin kapsamı geneldir (kendi servislerimiz + izinli API'ler + genel senaryolar, 3. parti hedefler dahil). Her hedef için **kullanım koşulları (TOS)**, **robots.txt** (varsa `Crawl-delay`) ve **ülke/yerel yasalar** ayrıca değerlendirilmelidir. Sorumluluk **raporlama/etiketleme** düzeyindedir: hangi kaynağın hangi kuralla tarandığına dair kayıt tutulur (`download_sources` + audit/`log.md`), ton **neutral** ve **bilgilendirme amaçlıdır** — bu madde ne tavsiye ne de teşvik içerir, mimari zorunluluğu tarif eder. Resmi API mevcutsa scraping/otomatik istek ona tercih edilir (§1.3-3).

### 2.1 Neden Bu Seçenek?

- **Backoff+jitter tek başına yeterli değil, ama zorunlu:** sabit gecikme (mevcut kod) senkron risk üretir; jitter olmazsa thundering herd (§1.3-1). Üstel dizi + jitter + tapan üçü birlikte hedef kota penceresini zorlamadan yeniden denemeyi sağlar.
- **403 kapsamda çünkü kota bazen 403'tür:** literatür 4xx'i breaker'dan hariç tutar ama kota aşımı 403 ile bildirilebilir (Tyk, §1.3-3) → bu ADR 429+403'ü **ban sinyali** olarak sayar (bilinçli sapma, gerekçesi yazılı).
- **UA rotasyonu "havuz + oturum sabitliği" olarak yazıldı çünkü:** rastgele-UA tek başına zararlıdır (tutarlılık sinyali, §1.3-4); bugünkü asıl sorun ise **UA hiç gönderilmemesi** (`CURLOPT_USERAGENT` 0) → önce tanımlı havuz, sonra oturum tutarlılığı.
- **Proxy havuzu opsiyonel yazıldı çünkü maliyetlidir:** havuz = hizmet maliyeti + yönetim yüzeyi (§4.3/3); kota+backoff+breaker olmadan havuz tek başına ban önlemez. Sıra: (d)+(a)+(e) önce, (c) sonra.
- **Per-kaynak kota şemadan başladı:** `download_sources` zaten `quota_remaining`/`quota_reset_at` taşıyor (IMPLEMENTED) → yeni tablo yok, mevcut şema doldurulur (YAGNI, In-Place).
- **Breaker = üçüncü hat olarak:** backoff denemeyi yavaşlatır, breaker **hedefi tamamen durdurur** — ikisi birlikte "yavaşla" ve "durdur" kararlarını ayrı ele alır (Microsoft/AWS deseni, §1.3-6).
- **Retry-After öncelikli çünkü standarttır:** başlık varken kendi tahminini kullanmak hem saygısızlık hem risktir (RFC 9110 §10.2.3); kendi üretimimiz zaten uyumlu → maliyet sıfır, tutarlılık tam.
- **Yasal not madde olarak yazıldı çünkü kapsam genel:** "genel senaryolar" denince sorumluluk dağılışı hiçbir belgede durmuyordu (§1.2/7); nötr bilgilendirme tonu, sorumluluğu **raporlama** düzeyinde tutar.

### 2.2 Teknik Detaylar

**a) Backoff + jitter akışı (PLANNED — mevcut kanıt üzerine):**

| Durum | Eylem | Bekleme |
|---|---|---|
| `Retry-After` (429/503) | **Başlığa uy** (saniye veya HTTP-date) | başlık değeri |
| `429` (başlık yok) | Üstel + jitter | `min(base*2^n + jitter, max)` |
| `403` | Kota/ban sinyali say → backoff + **breaker sayacına** ekle (madde e) | `min(base*2^n + jitter, max)` |
| `5xx` / timeout | Backoff + breaker sayacı | `min(base*2^n + jitter, max)` |
| `401/404` (gerçek girdi hatası) | **Yeniden deneme** → hata olarak raporla | — (retry yok) |
| Paralel retry | **YASAK** | — |

> `base`, `max_delay`, `max_attempts` değerleri **ölçümle kalibre** edilir (§5.1/8; ADR-013'ün "varsayılant — ölçümle kalibre edilir" ruhu) — bu ADR'de sayısal varsayım **yazılmaz** (⚠️ VERIFICATION REQUIRED).

**b) UA havuzu ve oturum tutarlılığı (PLANNED):**

| Öğe | Karar |
|---|---|
| Havuz kaynağı | Config dosyası (REDACTED değil — kamu string'leri) + güncelleme döngüsü |
| Seçim anahtarı | `kaynak (source)` veya `oturum (job id)` → hash ile deterministik seç |
| Uyum | UA ile birlikte `Accept-Language`/`Accept` header seti **aynı tarayıcı ailesinden** |
| Yasak | İstekler arası rastgele-UA; UA'yı **her denemede** değiştirme |
| Ölçüm | UA'sız istek oranı → hedefe göre tespit riski (§5.1/8) |

**c) Proxy havuzu (PLANNED, opsiyonel):**

| Döngü | Sıklık | Eylem |
|---|---|---|
| Rotation | istek akışı | kaynak başına havuzdan sırayla/ağırlıklı seç |
| Health scoring | periyodik + her istek sonunda | başarı/gecikme EWMA skoru güncelle |
| Quarantine | skor eşiği altı | karantina → recovery testi → **havuzdan çıkar** |
| Failover | proxy hatası | sıradaki sağlıklıya geç; tümü ölüyse **fail-open** (doğrudan) veya queue |
| Sayım | — | havuz boyutu + ölü oran log'a (`log.md`, ADR-005) |

**d) Per-kaynak kota (şema IMPLEMENTED / mantık PLANNED):**

| Katman | Nesne | Durum |
|---|---|---|
| Şema | `download_sources.quota_remaining`, `quota_reset_at`, `is_active`, `CHECK (source IN (…))` (`coremusic_download.sql:141-162`) | **IMPLEMENTED** |
| Sayaç | APCu per-kaynak sayaç (ADR-007 TTL zorunlu) | PLANNED |
| Motor | kota aşımı → bekleme (yeniden deneme değil) → pencere sıfır → devam | PLANNED |
| Bekleme kaynağı | `Retry-After` (f) varsa onu; yoksa (a) | PLANNED |
| Audit | kota aşımına dair olay (`media_audit`/`log.md`) | Şema var / kod PLANNED |

**e) Circuit breaker (PLANNED):**

| Geçiş | Koşul | Eylem |
|---|---|---|
| Closed → Open | Kaynak **eşik üstü** 429/403 (ban sinyali) veya 5xx/timeout (hedef hatası) | Kaynak **geçici kapat**; istekler kuyruğa/ertelenir; `log.md` kaydı |
| Open → Half-open | Bekleme (madde a) doldu | **Tek deneme** gönder |
| Half-open → Closed | Deneme başarılı | Sayaç sıfır, kaynak açılır |
| Half-open → Open | 429/403/5xx | Bekleme **uzar**, sayaç korunur |
| Not | Literatürde 4xx breaker'a sayılmaz → **bu ADR 429/403'ü sayar** (madde e gerekçesi) | Kayıt satırında işaretli |
| Depo | APCu (ADR-007 TTL); APCu yoksa **fail-open** (kaynak kapatılmaz, backoff kalır) | `RateLimiterMiddleware.php:30-32` ruhu |

**f) Retry-After ayrıştırma (PLANNED):** `Retry-After: <delta-seconds>` **veya** `<HTTP-date>` iki biçim de okunur; negatif/geçersiz değer → madde (a)'ya düş; kendi bekleme asla başlığın altına inmez. Kendi API'mizde üretim **IMPLEMENTED** (`RateLimiterMiddleware.php:53`, `Api/Middleware/RateLimitMiddleware.php:40`).

**g) Zincir ve bağımlılıklar (nereye oturur):**

```
[1] İstek kuyruğu (ADR-026 download_queue)          → şema IMPLEMENTED
[2] Per-kaynak kota kontrolü (madde d)              → şema IMPLEMENTED / motor PLANNED
[3] Circuit breaker (madde e)                       → PLANNED
[4] UA + proxy seçimi (madde b, c)                  → PLANNED (UA havuzu önce)
[5] HTTP isteği (curl mevcut istemciler)            → IMPLEMENTED (UA'sız)
[6] Yanıt: 429/403/5xx → Retry-After (f) → backoff+jitter (a) → breaker sayacı (e)
[7] Audit/olay: media_audit + log.md                 → şema IMPLEMENTED / yazan kod PLANNED
```

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Sabit gecikmeli retry (mevcut hâl: `usleep` sabit)** | En basit kod; hemen uygulanır | Üstel artış yok → hedef kota penceresinde **tekrar tekrar** vurulur; jitter yok → senkron/thundering herd; tek başına ban **önlenez** (§1.3-1) | Literatür 2025-26'da **backoff+jitter**'ı standart sayar (Docebo, GitHub #2760, Tiger Abrodi); madde (a) bu yüzden zorunlu |
| 2 | **Yalnız proxy rotasyonu (backoff yok)** | IP kimliği değişir, ban kırılır görünür | Kota **IP başına değil hesap/kaynak başına** da olabilir; backoff yoksa hedef agresif kalıp 403/429 üretmeye devam eder; maliyetli havuz **tek başına** yetersiz (§1.3-5) | Kapsam sırası (d)+(a)+(e) önce, (c) **opsiyonel** → havuz tek başına karar değil, katman |
| 3 | **Agresif UA rotasyonu (her istekte farklı UA)** | Çeşitlilik yüksek görünür | **Tutarlılık sinyali doğar** — aynı header/TLS + değişen UA = bot kümesi (§1.3-4: "rotasyon tutarlılık olmadan işe yaramaz") | Madde (b): **havuz + oturum başına sabit** fingerprint; rastgele dönüş **yasak** |
| 4 | **Fail-closed breaker (kaynak hücresi, istek hiç atılmasın)** | Güvenlik "katı" görünür | Havuz/health bilgisi yokken (PLANNED evrede) tek bilgi kaynağı `403` olur → yanlış pozitif ile tüm indirme hattı durur; ADR-013 fail-open dersi çelişir | Madde (e) **geçici** kapat + half-open tek deneme + **fail-open** yedeği (APCu yoksa kapatma) |
| 5 | **Tüm kaynaklar için tek global kota** | Tek sayaç, basit | Bir kaynak kotası diğerini etkiler; hedeflerin limitleri farklı (§1.3 polite crawling ≈1/sn/domains); kota aşımında **masum kaynak** da yavaşlar | Madde (d): **kaynak başına ayrı limit** — `download_sources` şeması zaten kaynak-bazlı tasarlı (`source` kolonu + CHECK) |
| 6 | **Yalnız hukuki kısıt (teknik mekanizma olmadan)** | Net sorumluluk | TOS/robots.txt denetimi tek başına **ban önlemez**; kod katmanı (a-f) olmadan istekler yine hızlı akar | Yasal not **madde (g)** olarak ek koruma katmanıdır, tek başına karar değildir — teknik (a-f) + etik (g) birlikte |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Boşluk tek belgede kapanır:** `brain.md:332` tek satır anti-ban özeti + `keys.md:263` + `ADR-003:124` şema notu — üç parça ilk kez §2'de toplanır (backoff, UA, proxy, kota, breaker, Retry-After, etik).
- **Standartla uyum maliyeti sıfır:** `Retry-After` **zaten üretiyoruz** (`RateLimiterMiddleware.php:53`) → madde (f) yalnız **okuma** tarafı ekler.
- **Şema hazır:** `download_sources.quota_remaining/quota_reset_at/is_active` (**IMPLEMENTED**) → kota motoru için yeni tablo gerekmez (YAGNI).
- **Mevcut fail-open ilkesi korunur:** ADR-013'ün servis sürekliliği tercihi outbound'a da taşınır (bilgi yoksa durma, yavaşla).
- **Kendi iç health-check kalıbı yeniden kullanılır:** `ServiceRegistry`/`ServiceHealth` (IMPLEMENTED) deseni outbound breaker'a şablon olur (yeni altyapı değil, genişletme).
- **Etik/yasal çerçeve yazılı hâle gelir:** kapsam "genel" olunca sorumluluk dağılışı madde (g) ile belgelenir (neutral, bilgilendirme tonu).

### 4.2 Olumsuz Sonuçlar

- **Kod işi tamamen önümüzde:** outbound `backoff` **0**, outbound `User-Agent` **0**, outbound `proxy` **0**, circuit breaker **0**, `DownloadController` **0** (ADR-026) → bu ADR **mimari karardır**; "anti-ban çalışıyor" iddiası şu an **yalandır** (§1.1).
- **Parçalı retry → genişletme işi:** `HomeAuthBridge.php:111-117` sabit gecikmeli döngüsü **tek** retry; OAuth/AI istemcilerinde retry hiç yok → üç istemcide ortak backoff katmanı yazılır (tekilleştirme işi).
- **UA havuzu bakımı:** tarayıcı sürümleri eskiyince havuz **tespit edilebilir** hâle gelir → güncelleme döngüsü ek yük (§5.1/6).
- **Ölçüm yok:** hedef başına istek hızı, 429/403 oranı, breaker açılma sayısı henüz ölçülmüyor → kalibrasyon kör başlar (§5.1/8).
- **Karmaşıklık iki katman:** backoff (yavaşlat) + breaker (durdur) + havuz (kimlik) birlikte test yüzeyi büyütür; yanlış eşik = yanlış durdurma (§4.3/2).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **Ban kalıbı (hızlı tekrar):** backoff uygulanmadan istekler akarsa hedef 403/429 üretir, kaynak geçici kaybedilir | 4 (Çok olası) | 3 (Orta) | Madde (a) üstel+jitter zorunlu; sabit gecikmeli retry (`HomeAuthBridge:111-117`) genişletilir; 403/429 **breaker sayacına** girer (madde e) |
| 2 | **Yanlış breaker eşiği → indirme hattı durur:** kota/ban sinyali ile gerçek hedef arızası ayırt edilemezse masum kaynak kapanır | 3 (Olası) | 4 (Yüksek) | Half-open tek deneme + **fail-open yedeği** (APCu yoksa kapatma yok); eşik **ölçümle** kalibre (§5.1/8); `log.md` ile her geçiş raporlanır |
| 3 | **Proxy maliyeti + kötüye kullanım algısı:** havuz = hizmet bedeli + yönetim; paylaşımlı/kirli havuz hedefte **daha kötü** imaj üretir | 3 (Olası) | 3 (Orta) | Havuz **opsiyonel/PLANNED**; önce (d)+(a)+(e); kaliteli kaynak şartı; havuz metrikleri (ölü oran, maliyet) log'a → kapatma kararı ölçümle |
| 4 | **Yasal/etik risk (TOS/robots.txt/ülke yasası):** genel kapsamda hedef kuralları ihlal edilirse raporlama sorumluluğu doğar | 3 (Olası) | 4 (Yüksek) | **Madde (g)** zorunlu: hedef başına TOS/robots.txt denetimi + kayıt (`download_sources` + audit); resmi API tercihi; ton neutral/bilgilendirme |
| 5 | **UA havuzunun bayatlaması:** eski sürüm string'leri tespit sinyali üretir | 3 (Olası) | 2 (Düşük) | Havuz **sürüm takibi + yenileme döngüsü** (§5.1/6); oturum tutarlılığı ile birlikte (madde b); UA'sız istek oranı ölçülür |
| 6 | **`Retry-After` okuma hatası:** saniye/HTTP-date biçimi yanlış ayrıştırılırsa ya çok erken gidilir ya çok geç kalınır | 2 (Mümkün) | 2 (Düşük) | İki biçim de desteklenir (madde f); geçersiz değer → backoff (a)'ya düş; test: biçim ayrıştırma + asla başlığın altına inmeme (§5.1/5) |
| 7 | **`ARL token` belirsizliği:** `brain.md:332` iddiası hiçbir vault dosyasında tanımlı değil (REDACTED dışında) | 4 (Çok olası) | 2 (Düşük) | `⚠️ VERIFICATION REQUIRED` (§1.1-B); token mekaniği bu ADR'de **tanımlanmaz** → ayrı ADR ile kararlaştırılır (§5.1/10) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Ortak backoff+jitter yardımcı sınıfı:** `min(base*2^n + jitter, max)` + azami deneme + paralel retry yasağı; `Retry-After` (saniye + HTTP-date) okuma **öncelikli**; mevcut sabit gecikmeli döngı (`HomeAuthBridge.php:111-117`) bu sınıfa geçirilir (In-Place, dosya adı değişmez) | Backend Architect + Security Engineer | 2 gün |
| 2 | **Outbound UA havuzu (madde b):** tanımlı gerçek-tarayıcı havuzu (config), kaynak/oturum bazlı deterministik seçim, header-set uyumu; `BaseOAuthProvider`/`ToolCalling`/`HomeAuthBridge` istemcilerine `CURLOPT_USERAGENT` eklenir (**bugün: 0**) | Backend Architect | 1.5 gün |
| 3 | **Per-kaynak kota motoru (madde d):** `download_sources.quota_remaining/quota_reset_at` (IMPLEMENTED şema) okuma/yazma + APCu sayaç (ADR-007 TTL) + aşım → bekleme (yeniden deneme değil) | Data Engineer + Backend Architect | 2 gün |
| 4 | **Circuit breaker (madde e):** Closed/Open/Half-open durum makinesi, 429/403 (ban sinyali) + 5xx/timeout (hedef hatası) sayaçları, APCu depo + fail-open yedeği, her geçiş `log.md` kaydı | Backend Architect + Security Engineer | 2.5 gün |
| 5 | **Proxy havuzu (madde c — OPSİYONEL/PLANNED):** kaynak başına havuz + health-score (EWMA) + karantina/recovery + failover; **yalnız §5.1/1-4 kapandıktan sonra** | DevOps + Backend Architect | 3 gün (PLANNED) |
| 6 | **UA/mekanizma ölçümü:** UA'sız istek oranı, havuz bayatlık yaşı, havuz maliyeti/ölü oranı → `log.md` sayısal kayıt | QA Engineer + DevOps | 1 gün |
| 7 | **Spec-durum hizası (In-Place ekleme):** `download-panel.md:156` ve `download-service.md:126,422-429` "exponential backoff" satırlarına **kod kanıtı notu** eklenir (silme yok) → "spec / kod PLANNED (ADR-028)"; `download-service.md:429` sabit `sleep_for` örneği için aynı not | MO (vault-updater) | 0.5 gün |
| 8 | **Ölçüm + kalibrasyon:** hedef başına istek hızı, 429/403/5xx oranı, breaker geçiş sayısı, `Retry-After` başlık okunma oranı → `base/max/attempts` eşikleri ölçümle belirlenir (§2.2a notu: sayısal varsayım yazılmadı — `⚠️ VERIFICATION REQUIRED`) | QA Engineer + Security Engineer | 1.5 gün |
| 9 | **Doğrulama:** şablon tutamağı taraması (`ADR-028`, hedef 0) · wiki-link disk kontrolü (§6) · `vault-utf8-writer scan` (mojibake 0) · `index.md:65` slug eşleşmesi · placeholder 0 · `log.md` append 1 satır | Vault Steward | 0.5 gün |
| 10 | **`ARL token` kararı (risk 7 / §1.1-B ⚠️):** `brain.md:332` iddiasının tanımı yok → token mekaniği ayrı ADR'de kararlaştırılır; bu ADR'de yalnız `⚠️ VERIFICATION REQUIRED` + REDACTED notu kalır | Vault Steward + Security Engineer | 0.5 gün (ayrı ADR) |
| 11 | **Backoff/breaker test paketi (debate şartı 2):** jitter/determinizm testi (üstel dizi + rastgelelik), `Retry-After` iki biçim ayrıştırma, breaker Closed/Open/Half-open geçişleri — madde a/f/e | QA Engineer + Backend Architect | 1.5 gün |
| 12 | **Yasal/robots tarama raporu (debate şartı 3):** hedef başına TOS + robots.txt (+ varsa `Crawl-delay`) denetimi, `download_sources` + audit/`log.md` kaydı — madde g'yi raporlanabilir kılar | Security Engineer + DevOps | 0.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (a) backoff kapanırsa → ortak yardımcı sınıf bayrakla devre dışı (`ANTI_BAN_BACKOFF=false`), istemciler sabit gecikmeli tek denemeye döner (bugünkü `HomeAuthBridge` davranışı — §4.2/1'e geri dönülür, bilinçli); (b) UA havuzu kapanırsa → `CURLOPT_USERAGENT` kaldırılır = **UA'sız istek** (bugünkü hâl) — hedef algısı **bozulmaz, eski hâline döner**; (c) proxy havuzu vazgeçilirse → havuz **opsiyonel** olduğu için (madde c) hiçbir yol buna bağlı değildir, doğrudan tek IP ile devam; (d) per-kaynak kota kapanırsa → sayaçlar APCu TTL ile kendiliğinden söner (ADR-007), `download_sources.quota_*` kolonları **dokunulmadan boş kalır** (şema In-Place korunur); (e) breaker kapanırsa → durum makinesi bayrakla devre dışı, backoff (a) tek güvenlik ağı olarak kalır (kaynak kapatma yok → §4.3/2 riski ortadan kalkar).

**Tam geri dönüş:** dosya adları/rota değiştirilmediği için (In-Place Refactoring korundu) geri dönüş = (1) ortak backoff sınıfı çağrıları kaldırılır, `HomeAuthBridge.php` **git revert** ile sabit gecikmeye döner; (2) UA havuzu config'i boşaltılır → istemciler UA'sız; (3) kota motoru sayaçları TTL ile söner (DB şemasına dokunulmaz); (4) breaker durumları APCu TTL ile söner; (5) proxy havuzu varsa kapatılır (kayıp yok — opsiyonel); (6) `vault-utf8-writer` yedeği (`<file>.bak`) eski içeriği verir; (7) `.ai/log.md`'ye tek satır revert append'i; (8) `.ai/.decisions/index.md:65` satırı `status: reverted` olur; (9) **bu ADR düzenlenmez** — `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** `log.md` append-only geçmiş, karar dizini satırı, `download_sources` şeması, kendi API'mizin 429/`Retry-After` üretimi ve fail-open rate limit (ADR-013) **bozulmaz**; şu an geri döndürülecek olan **mimari çerecedir** (kod olmadığı için).

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| — | **Debate başlamadı** | **⏳ PENDING** | Kanıt taraması (§1.1) + §1.3 web araştırması tamamlandı; 3 tur / 20 persona debate **bu ADR'nin ilk turu bekleniyor** → sonuç bu tabloya `vault-utf8-writer append` ile eklenir (mevcut satırlara dokunulmaz) |
| 1 | **20 persona** | ✅ TUR 1 | Kanıt taraması sunuldu — inbound 429/`Retry-After` VAR (`RateLimiterMiddleware.php:48-56`, fail-open `:30-32`) · outbound backoff **0** · outbound UA **0** (3 curl istemcisi UA'sız: `BaseOAuthProvider`, `ToolCalling`, `HomeAuthBridge`) · outbound proxy **0** · circuit breaker **0** · `HomeAuthBridge.php:111-117` sabit `usleep` (exponential değil) · `download_sources` şema kota alanları hazır → **16 kabul/neutral + 4 uyarı** (Backend: sabit `usleep` şart · QA: jitter testi · Critic: UA'sız curl + yasal madde) |
| 2 | **İtiraz→çözüm (4 madde)** | ✅ TUR 2 | (1) sabit `usleep` + outbound backoff 0 → exponential backoff + jitter outbound → **şart 1a** · (2) 3 curl UA'sız → UA havuzu + oturum tutarlılığı → **şart 1b** · (3) test yok → jitter/determinizm + circuit breaker test paketi → **şart 2** · (4) yasal not → TOS/robots tarama maddesi (raporlanabilir) → **şart 3** |
| 3 | **20 persona** | ✅ TUR 3 — **KABUL** | Oy: **19 kabul / 1 çekimser / 0 red** → KABUL; 3 bağlayıcı şart §5.5'e eklendi; Tech Lead §7 **⏳ → ✅** |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| Exponential backoff + jitter (outbound) | ❌ PLANNED (`backoff` kodda 0) | §5.1/1 |
| Outbound User-Agent başlığı/havuzu | ❌ PLANNED (`CURLOPT_USERAGENT` 0 eşleşme) | §5.1/2 |
| Per-kaynak kota motoru | ⚠️ Şema IMPLEMENTED / mantık **0** | §5.1/3 |
| Circuit breaker | ❌ PLANNED (kod 0 eşleşme) | §5.1/4 |
| Proxy havuzu + health/quarantine | ❌ PLANNED (outbound proxy 0; yalnız inbound XFF var) | §5.1/5 |
| `DownloadController` (üst zincir) | ❌ PLANNED (ADR-026 şart 2 — `DownloadController` 0 dosya) | ADR-026 §5.1/2 |
| `Retry-After` **okuma** (kendi üretimi IMPLEMENTED) | ⚠️ Üretim **IMPLEMENTED** / okuma **PLANNED** | §5.1/1 |
| UA/kota/breaker ölçümü | ❌ PLANNED (ölçüm 0) | §5.1/8 |
| `ARL token` mekaniği | ⚠️ `⚠️ VERIFICATION REQUIRED` (tanım yok; REDACTED) | §5.1/10 (ayrı ADR) |
| Debate / Tech Lead | ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL — §5.3) · Tech Lead ✅ (§7) · şartlar §5.5 (3 madde) | §5.3, §5.5, §7 |

### 5.5 Debate Şartları

Debate **✅ TAMAMLANDI (3 tur / 20 persona → 19 kabul / 1 çekimser / 0 red = KABUL)** — bağlayıcı **3 şart** (1a-1b, 2, 3) şablon §6.3 uyarınca bu alt başlığa eklendi (mevcut metin korunur):

**Şart 1 — Outbound backoff + UA havuzu (maddeler a ve b; §5.1/1, §5.1/2):**
- **1a)** `HomeAuthBridge.php:111-117` sabit `usleep` + outbound backoff **0** → exponential backoff + jitter (`min(base*2^n + jitter, max)` + retry cap, paralel retry yasağı) outbound tüm istemcilere uygulanır; sabit gecikme tek başına korunmaz.
- **1b)** UA'sız 3 curl istemcisi (`BaseOAuthProvider`, `ToolCalling`, `HomeAuthBridge`) → tanımlı **UA havuzu** + **oturum tutarlılığı** (fingerprint per oturum, madde b); `CURLOPT_USERAGENT` **0 → zorunlu**.

**Şart 2 — Backoff/breaker test paketi (§5.1/11):** jitter/determinizm testi (üstel dizi + rastgelelik, tekrarlanabilir tohum), `Retry-After` saniye/HTTP-date ayrıştırma testi ve circuit breaker Closed/Open/Half-open geçiş testleri (madde a/f/e) QA ile yazılır — test paketi olmadan §5.1/1 ve §5.1/4 adımları kapanmaz.

**Şart 3 — Yasal/robots tarama maddesi (madde g; §5.1/12):** hedef başına TOS + robots.txt (+ varsa `Crawl-delay`) denetimi **raporlanabilir** kayıt üretir (`download_sources` + audit/`log.md`) — madde (g) yalnız bilgilendirme düzeyinde kalmaz, denetlenebilir çıktı verir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail'ler (bu ADR'nin yazım usulü) |
| [[../../AGENTS.md]] | Agent registry — §5 domain (`*.php` → Backend Architect, `*.sql` → Data Engineer), §25.3 kural 2/3 (frozen + log append-only) |
| [[../../WORKFLOW.md]] | Süreçler — uygulama adımlarının faz bağlamı |
| [[../index]] | Karar dizini — **satır 65** `[[ADR-028-anti-ban-system]]` (slug ✅) |
| [[../../index]] | Master katalog — `:645` "Decisions/accepted/ADR-028-anti-ban-system \| Anti-ban system \| Download" |
| [[../../brain]] | Mimari karar özeti — `:983` "ADR-028 \| Rate limiting + proxy rotasyonu", `:332` tek satır anti-ban tanımı |
| [[../../keys]] | Keyword haritası — `:263` "ADR-028 \| anti-ban, ARL token" |
| [[ADR-013-rate-limiting-apcu]] | Gelen istek rate limit — fail-open (`RateLimiterMiddleware.php:30-32`), auth 5/900s (`AuthService.php:31-32`), backoff PLANNED maddesi → bu ADR'nin (a) dayanağı (§1.1, §1.4, §2a) |
| [[ADR-026-download-service-architecture]] | İndirme zinciri — PLANNED controller (`DownloadController` 0), rota `Gateway.php:87`, `download_sources` şeması → anti-ban'ın üstünde koşacağı katman (§1.1-B, §1.4, §2.2g) |
| [[ADR-020-api-public-security]] | Inbound API güvenliği — bu ADR ile sınır ayrımı (§1.4) |
| [[ADR-007-cache-namespace]] | APCu sayaç/breaker depo katmanı — TTL zorunlu, fail-open (§1.4, §2.2e) |
| [[ADR-003-multi-db-bcnf]] | `:124` "download_sources … anti-ban durumu" — vault'taki ilk anti-ban geçişi (§1.1-B) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1, §1.3) |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı + şablon zorunluluğu + UTF-8 tek arayüz (§1.4, §5.1/9) |
| [[../../architecture/k8-servis/download-service]] | Download servisi spec — `:126` `retryDelayMs`, `:422-429` sabit `sleep_for` retry (PLANNED, §1.1-B, §5.1/7) |
| [[../../architecture/k10-uygulama/download-panel]] | Download paneli spec — `:156` "exponential backoff" iddiası (PLANNED, §1.1-B, §5.1/7) |
| [[../../.templates/adr/adr-template]] | İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16) |
| [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol]] | §1.3 web araştırma protokolü (diskte VAR ✅) |
| `shared/src/Middleware/RateLimiterMiddleware.php` (kod yolu, wiki-link değil) | 429 + `Retry-After` üretimi + fail-open — `:30-32,48-56` (IMPLEMENTED kanıtı) |
| `home.coremusic.net/include/Auth/HomeAuthBridge.php` (kod yolu) | Tek mevcut retry döngüsü (sabit gecikme) — `:111-117` (PLANNED genişletme hedefi) |
| `.ai/.sql/mysql/coremusic_download.sql` (kod yolu) | `download_sources` kota alanları — `:141-162` (IMPLEMENTED şema) |

> **Durum özeti:** debate **✅ TAMAMLANDI (3 tur / 20 persona → 19/1/0 KABUL, §5.3)** · Tech Lead **✅ (§7)** · Arch Lead **⏳ PENDING** · şartlar **§5.5 — 3 bağlayıcı madde (1a-1b, 2, 3)** · frozen **YOK** · kod: 429/`Retry-After` üretimi + fail-open + auth 5/900s + `download_sources` şeması **IMPLEMENTED**, backoff/UA havuzu/proxy havuzu/kota mantığı/breaker **PLANNED** (§1.1).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar içeriği) |
| Tech Lead | — | 2026-09-25 | ✅ (debate ✅ — 3 tur / 20 persona → 19 kabul / 1 çekimser / 0 red = KABUL; 3 şart §5.5) |
| Arch Lead | — | 2026-09-25 | ⏳ PENDING |

**Debate Kaydı (§5.3 · §5.5):** 3 tur / 20 persona → **19 kabul / 1 çekimser / 0 red = KABUL** · 3 bağlayıcı şart: **(1)** outbound exponential backoff + jitter ve UA havuzu + oturum tutarlılığı (1a-1b) · **(2)** backoff/breaker test paketi · **(3)** yasal/robots tarama maddesi (raporlanabilir) · Tech Lead **✅** · Arch Lead **⏳**.

---

**1.0.0 | 2026-09-25 | Created**

*ADR-028 — Anti-Ban System (Exponential Backoff + Jitter · User-Agent Rotasyonu · Proxy/Pool Health · Per-Kaynak Kota · Circuit Breaker · Retry-After Uyumu · Yasal/Etik Not)*
*Authority: ADR-028 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
