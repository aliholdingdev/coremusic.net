---
title: "CoreMusic — ADR-013: Rate Limiting APCu (İki Katman · Sabit Pencere → Sliding Window + Token Bucket · Brute Force Backoff)"
type: adr
category: security
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-013 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-013: Rate Limiting APCu (İki Katman · Sabit Pencere → Sliding Window + Token Bucket · Brute Force Backoff)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-013'ü sıfırdan yaz"; kapsam onayı "1&3 + 1&2") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-007-cache-namespace]] (APCu = L1 cache katmanı + TTL tablosunda "rate-limit 60sn" bu ADR'nin düz metniyle hizalı; dosya diskte VAR ✅) · [[ADR-008-bypass-auth-middleware]] (auth pipeline'ı ve bypass sınırı — brute force kalkanının çalıştığı kapı; dosya diskte VAR ✅) · [[ADR-009-clean-url-redirect]] (sunucu/edge katmanı PLANNED bulgusu `**/*.conf` = 0 — bu ADR'de edge rate limit de aynı gerçekliğe dayanır; dosya diskte VAR ✅) · [[ADR-010-csrf-protection-strategy]] (oturum-içi token katmanı — rate limit üstüne ikinci hat; dosya diskte VAR ✅) · [[ADR-011-session-management]] (başarılı login'de sayaç reset'i + oturum hijyeni — backoff'un hedeflediği edinim; dosya diskte VAR ✅) · karar dizini [[../index]] §3 satırı `[[ADR-013-rate-limiting-apcu]]` (satır 50 — slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'un brute force ve kaynak istismarı savunması bugün **tek katmanlı ve sabit pencere sayaclıdır**: `shared` middleware'i her IP'ye 60 istek/60 sn verir, auth servisi login'i 5 başarısız deneme/900 sn ile sınırlar — ama **algoritma ikisi de sabit pencere**, yani pencere sınırında istek yığını (boundary burst) geçer; **per-account kota, exponential backoff, geçici lockout, OTP kotası, per-route kota ve edge (WAF/DDoS) katmanı yoktur**. Bu ADR, onaylı kapsamla (iki katman + üç algoritma + kota çerçevesi) rate limiting'i **iki hatlı** hâle getirir: (1) **WAF/DDoS edge katmanı** — uygulama ayağa kalkmadan önce ilk savunma (nginx `limit_req`/`limit_conn` veya CDN/WAF) — depoda conf yok → **PLANNED**; (2) **uygulama içi APCu katmanı** — per-IP + per-account kotalar, brute force backoff/lockout, per-route kota, burst poliçesi — kodda **kısmen IMPLEMENTED** (§1.1), tamamlanır ve algoritma olarak yükseltilir (sabit pencere → sliding window log + token bucket + backoff).

Kapsam: (a) iki katman mimarisi ve katman görev dağılımı, (b) algoritma ↔ katman eşlemesi, (c) OWASP temelli kota çerçevesi (varsayılan — ölçümle kalibre edilir), (d) APCu tek depo + çok-FPM atomikliği mekaniği, (e) mevcut kodun IMPLEMENTED/PLANNED kırılımı ve iddia-kod çelişkileri, (f) edge katmanının deployment artefaktı olarak şartlanması.

### 1.1 Mevcut Durum

**Kod kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yolu + satır ile):**

- **`shared/src/Middleware/RateLimiterMiddleware.php:9-59` — PER-IP LIMITER IMPLEMENTED (algoritma = sabit pencere sayacı):** key = `rl:` + `md5($ip)` (satır 35); ilk istekte `set($key, 1, window)` (satır 37), sonraki isteklerde `increment` (satır 42); sayaç `> maxRequests` ise **429** + `Retry-After: {window}` (satır 48-55). Varsayılanlar `maxRequests = 60`, `windowSeconds = 60` (satır 18-19). Güvenilir proxy şartıyla `X-Forwarded-For`/`X-Real-IP` okunur (satır 61-78; `TRUSTED_PROXIES`, varsayılan `127.0.0.1`/`::1`). Cache çözülemezse **fail-open** (`return $next($request)`, satır 30-32). **Sliding window log / token bucket / backoff YOK.**
- **`shared/src/PageRouter/PageRouterKernel.php:23-24, 273-282` — PIPELINE KAYDI IMPLEMENTED:** `RATE_LIMIT_MAX = 60`, `RATE_LIMIT_WINDOW = 60` (satır 23-24); sıra `OriginCheck → Cors → **RateLimiter (satır 275)** → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation` — yani limit, oturum/CSRF/auth'dan **önce** çalışır (doğru sıra).
- **`shared/src/Api/Middleware/RateLimitMiddleware.php:20-79` — API KATMANI IMPLEMENTED (sınıf):** key = `api:` + `sha256(API key)` ya da `ip:` + `REMOTE_ADDR` (satır 57-68); 60/60 varsayılan (satır 22-23); 429 + `Retry-After` (satır 39-46); **`withLimits(int $maxRequests, int $windowSeconds)` (satır 73-79) = per-route kota iskeleti**. ⚠️ VERIFICATION REQUIRED: bu sınıfın ApiGateway/pipeline **kaydı grep'te bulunamadı** (yalnız tanım + `use`) → muhtemel pasif kod, teyit edilmeden "çalışıyor" sayılmaz.
- **`shared/src/Security/CacheRateLimiter.php:8-40` — SAYAÇ ÇEKİRDEĞİ IMPLEMENTED (atomik değil):** `isLimited` = `get` → karşılaştırma (satır 16-24), `increment` = `get` → yoksa `set(1)` / varsa `increment` (satır 26-35). **Check-then-act yarışı:** eşzamanlı isteklerde `get` ile `increment/set` arasında kilitsiz boşluk var → APCu `apcu_inc` atomik olsa bile bu sarmalayıcı atomikliği kullanmıyor (§2.2d).
- **Depo zinciri IMPLEMENTED:** `shared/src/Cache/CacheManager.php:9-19` — `function_exists('apcu_fetch')` ise **`ApcuAdapter`**, değilse **`MemoryAdapter`** (süreç-başına izole → sayaçlar paylaşılmaz, kota gevşer); `shared/src/Cache/ApcuAdapter.php:33-39` `apcu_inc` kullanır (atomik artış), `set` → `apcu_store` (TTL ile) — ADR-007 L1 katmanının kendisi.
- **Auth brute force kalkanı IMPLEMENTED (kısmi — per-IP, per-account DEĞİL):** `auth.coremusic.net/include/Service/AuthService.php:31-41` sabitleri: login `MAX_LOGIN_ATTEMPTS = 5` / `LOGIN_WINDOW_SECONDS = 900` (900 sn = 15 dk sabit pencere), register `3 / 3600`, password reset `3 / 3600`. Key'ler **yalnız IP bazlı**: `rate_limit:login:` . `clientIp` (satır 57), `rate_limit:register:` . `clientIp` (satır 117), `rate_limit:password_reset:` . `clientIp` (satır 229) → **per-account sayaç YOK** (saldırgan tek IP'den çok hesabı yoklayabilir / tek hesap IP değiştirerek yoklanabilir). Başarısızlıkta `increment` (satır 69, 81, 126-145, 236), **başarıda `reset`** (satır 96). `RateLimitException::loginRateLimited()` (satır 60) ile 429'a bağlanır.
- **APCu bağlantısı IMPLEMENTED:** `auth.coremusic.net/include/Container/AuthContainer.php:61-69` — `IRateLimiter => new CacheRateLimiter(CacheManager::getAdapter())` → auth sayaçları APCu'ya gider.
- **Exponential backoff + geçici lockout YOK → PLANNED:** repo geneli grep `brute|lockout|backoff|failed_attempt|too_many` → **0 eşleşme** (yalnız sabit isimleri ve `login_attempt` log satırı). Kota aşımında anında 429 var, **katlanarak gecikme / süreli hesap kilidi yok**.
- **OTP kotası YOK → PLANNED:** grep `otp|totp|2fa|verifyCode` → **0 eşleşme**; OTP akışı kodda yok → "OTP 3/dk/hesap" kotası bu ADR'de şart olarak yazılır, akış gelince uygulanır.
- **auth kendi limiter sınıfı PASİF/⚠️:** `auth.coremusic.net/include/Middleware/RateLimitMiddleware.php:11-57` — APCu ile pencere tutan (`['count','start']` dizisi, satır 24-53) per-IP 60/60 sınıfı **var**, ama auth içinde `MiddlewarePipeline`/`new *Middleware(` kaydı grep'te **bulunamadı** → ⚠️ VERIFICATION REQUIRED (ADR-012 §2.2f'teki "ölü/kayıtsız kod" deseninin tekrarı).
- **Audit/log IMPLEMENTED:** `shared/src/Log/FileHandler.php:84` — `rate_limited` olayı `warning` seviyesine bağlı; `auth.coremusic.net/include/Controller/AuthController.php:133-134` — `login_attempt` authEvent (email + IP); `shared/src/PageRouter/RouteResult.php:65-70` — `rateLimitExceeded()` yanıt fabrikası.
- **İddia-kod çelişkisi:** `shared/src/Middleware/CLAUDE.md:41,57,88-90` — "APCu tabanlı rate limiting (60 req/60s)" ✅ kodla uyumlu; ama **satır 89 `X-RateLimit-Remaining`, `X-RateLimit-Reset` başlıkları kodda YOK** (yalnız `Retry-After`) → §2.2f işaretli çelişki.
- **Edge/WAF katmanı YOK → PLANNED:** glob `**/*.conf` = **0** ve `**/.htaccess` = **0** (ADR-009 §1.1 ile aynı bulgu) → nginx `limit_req`/`limit_conn` bloğu depoda yok; `.ai/architecture/k13-cicd/*` içinde niyet (`ssl-redirect` benzeri) IMPLEMENTED-as-spec'tir, canlı conf PLANNED ⚠️ VERIFICATION REQUIRED.

### 1.2 Sorun Tanımı

1. **Tek algoritma (sabit pencere) = sınır yığını:** pencere bitiminde 2× kota istek atılabilir (boundary burst); token burst poliçesi ve hassas per-route sayım yok.
2. **Per-account kota yok:** login/register/reset sayaçları yalnız IP'ye bağlı → çok hesaplı deneme (credential stuffing) ve IP değiştirerek tek hesabı yoklama serbest.
3. **Backoff/lockout yok:** 5 başarısızdan sonra 900 sn sabit pencere dolunca sayaç sıfır — saldırgan 15 dk'da bir temiz kota alır; katlanarak gecikme/uzatma yok (OWASP WSTG: kilit genelde 3-5 denemede; burada kilit **yok**, yalnız pencere var).
4. **Per-route kota fiilen uygulanmaz:** `withLimits()` iskeleti var ama API middleware kaydı ⚠️; admin/ölü uçlar için özel kota tanımsız (admin 30/dk önerisi bu ADR ile çerçevelenir).
5. **Edge katmanı yok:** tüm trafik PHP'ye kadar iner — volumetrik DDoS/scraper'da uygulama katmanı tek savunma hattıdır; `**/*.conf` = 0 (ADR-009 bulgusu).
6. **Atomiklik boşluğu:** `CacheRateLimiter` get→set/increment yarışlı; APCu çok-FPM'de paylaşılır ama sayaç güncellemesi tek adıma indirgenmedi → eşzamanlı isteklerde sayaç kaçabilir.
7. **IDdia-kod çelişkisi:** `Middleware/CLAUDE.md:89` `X-RateLimit-*` başlıkları kodda yok → vault iddiası gerçeği yansıtmıyor.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — birincil/resmî kaynak önce (OWASP Cheat Sheets + ASVS + WSTG, php.net, NGINX resmî dokümanı, Stripe/Cloudflare mühendislik), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; **güvenlik iddiası = OWASP + resmî zorunlu**. **Odak: (a) rate limiting 2025-26 en iyi uygulama; (b) OWASP brute force/ASVS kuralı; (c) sliding window vs token bucket vs fixed window; (d) APCu çok-process atomikliği (apcu_inc/lock); (e) WAF/edge rate limiting.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "rate limiting best practices 2025 sliding window log vs token bucket vs fixed window comparison" · (2) "OWASP ASVS rate limiting brute force prevention login throttling requirements" · (3) "APCu apcu_inc atomic across PHP-FPM processes locking concurrency" · (4) "nginx limit_req limit_conn edge WAF rate limiting before application DDoS defense layered" · (5) "Stripe Cloudflare rate limiting algorithm token bucket sliding window documentation" · (6) "OWASP authentication cheat sheet throttle consecutive failed login attempts per IP account backoff" |
| Web Search **Konusu** | Dört algoritmanın (sabit pencere, kayan pencere log, kayan pencere sayacı, token bucket) davranış/sembol/maliyet karşılaştırması; OWASP ASVS + Authentication Cheat Sheet + WSTG'in brute force/anti-automation/lockout kuralı; APCu'nun PHP-FPM worker'ları arası paylaşımlı bellekte atomik artış/kilit semantiği (`apcu_inc`, `apcu_store` yarışı); nginx `limit_req`/`limit_conn` ile uygulama öncesi katman ve CDN/WAF (Cloudflare) kenar rate limiting'i. |
| Web Search **Bağlam** | **~18 adlandırılmış kaynak / 6 sorgu** (çoğu 2024-2026): birincil — OWASP Authentication Cheat Sheet, OWASP ASVS 5.0 (V6) + ASVS 4.0 (V2), OWASP WSTG "Weak Lock Out Mechanism", OWASP Credential Stuffing + Logging Cheat Sheets, php.net `apcu_inc`, NGINX resmî "Limiting Access to Proxied HTTP Resources"; mühendislik — Stripe Engineering (rate limiters), Cloudflare Blog (edge rate limiting), Kong, API7, ByteByteGo; ikincil — getpagespeed 2026, edgeservers 2026 (katmanlı nginx), reintech/PLC APCu paylaşımlı bellek, krakjoe/apcu issue #333, mojoauth (IP+hesap katmanlı throttle), security.stackexchange (exponential timeout tartışması). |
| Web Search **Kısa Açıklama** | **(1) Algoritmalar:** sabit pencere en ucuz ama sınırda 2× burst üretir; **kayan pencere log** her isteğin zaman damgasını tutar → en hassas, bellek/en pahalı; **token bucket** kısa burst'a izin verip sonra kota uygular → burst poliçesinin doğal biçimi (Stripe API bunu kullanır); kayan pencere sayacı (weighted) hâl-iyiliğidir. **(2) OWASP:** ASVS 4.0 V2 "anti-automation controls … brute force ve account lockout saldırılarını etkisiz kılmalı"; ASVS 5.0 V6 out-of-band kodun **rate limiting ile** korunmasını ister; Authentication Cheat Sheet **login throttling** ister; WSTG "hesaplar tipik olarak **3-5 başarısız denemede** kilitlenir"; Logging Cheat Sheet tekrarlı hataları **izlemeyi** şart koşar. **(3) APCu:** `apcu_inc` paylaşımlı bellekte atomik artışı sağlar (php.net), ama `apcu_store` yoğun eşzamanlılıkta lock darboğazı üretir (krakjoe #333) → get→set yarışına düşmeden tek adımlı artış kullanılmalı; APCu tüm FPM worker'ları **aynı önbelleği** paylaşır. **(4) Edge:** nginx `limit_req_zone`/`limit_req` + `limit_conn_zone`/`limit_conn` anahtar (IP/URI) başına bağlantı ve istek hızını uygulama **öncesinde** kısar; katmanlı mimari edge (CDN/WAF) → origin/uygulama önerilir; whitelist/NAT notu ve `dry_run` ile kalibrasyon yapılır. |
| Web Search **Uzun Açıklama** | **(a) Algoritma karşılaştırması:** ByteByteGo/systemdesignsandbox/codesmith aynı matrisi verir — sabit pencere: O(1) bellek, sayaç+TTL, **pencere sınırında iki kat istek**; kayan pencere log: her istek için zaman damgası listesi (ZSET/dizi) → **en hassas** ama bellek maliyeti ve temizliği var; kayan pencere sayacı: önceki pencere ağırlıklı üstel ortama dönüşümle O(1) bellek + hassasiyet dengesi; token bucket: bucket kapasitesi + dolum oranı iki parametreli, **burst kontrollü** (Stripe "token bucket kullanıyoruz, merkezî bucket host'unda her istek token alır"); leaky bucket/nginx `limit_req` sabit çıkış hızı verir. Kong, API7 ve itnext kayan pencereyi "esnek ve ölçeklenebilir" önerir; Cloudflare edge'de **dağıtık sayaç** (edge'de hesapla) kurar — yani sayım yerel, sonuç global. **(b) OWASP katmanlaması:** mojoauth açık tarif eder: **IP başına 5-10 deneme/dk + hesap bazlı N başarısız sonrası lockout + exponential backoff**; security.stackexchange tartışması backoff'u "kullanıcı başına sayaç + izin verilen zaman damgası" (IP değil **kullanıcı**) olarak savunur; OWASP Credential Stuffing Prevention, dökülmüş kimlik bilgisi denemelerini IP **ve** hesap/alt ağ bazında kısar; Logging Cheat Sheet tekrarlı başarısızlıkları izleme (rate_limited log'ları) ile korelasyon ister. **(c) APCu mekaniği:** reintech/php-dictionary APCu'yu "PHP süreçleri arası paylaşımlı bellek" olarak tanımlar → çok-FPM'de **tek depo**; `apcu_inc` (PECL apcu ≥ 4.0.0) sayısal artışı tek atomik adımda yapar; `apcu_store` high-concurrency'de lock/self-lock sorunları raporlanmıştır (issue #333) → get→set yarışı yerine `apcu_inc` + TTL zorunlu; PHP-FPM havuzları arası paylaşım konusu tartışmalıdır (serverfault/SO) → **tek FPM havuzu varsayımı yazılır, çok havuz ayrımı ⚠️**. **(d) Edge/WAF:** NGINX resmî dokümanı `limit_req` (istek hızı, saniye/dakika) + `limit_conn` (IP başına bağlantı) + indirme hızı kontrolünü sayar ve **NAT arkasındaki IP paylaşımına dikkat** çeker; getpagespeed `burst`/`nodelay`/`dry_run`/fail2ban kombinasyonunu, edgeservers 2026 ise "edge → perimeter → origin" üç katmanlı yığını (Cloudflare + `limit_req_zone` birlikte) anlatır; KX/virtua aynı anda brute force ve volumetrik DDoS'a karşı "ilk savunma hattı" tanımını yapar. |
| Web Search **Paragraf Veri Uzun** | Sabit pencere = O(1) bellek ama sınır burst (2×) · kayan pencere log = en hassas, pahalı (per-route API için) · token bucket = burst poliçesi (kapasite + dolum; Stripe) · kayan pencere sayacı = hibrit (ağırlıklı) · nginx `limit_req` içsel leaky bucket + `limit_conn` bağlantı sayacı · OWASP: ASVS 4.0 V2 anti-automation/brute-force · ASVS 5.0 V6 out-of-band rate limit · Cheat Sheet login throttling · WSTG 3-5 denemede kilit · Logging Cheat Sheet tekrar izleme · mojoauth: IP 5-10/dk + hesap lockout + exponential backoff · APCu = FPM worker arası paylaşımlı bellek, `apcu_inc` atomik, `apcu_store` high-concurrency lock riski (krakjoe #333), get→set yarışına düşme, TTL zorunlu · tek FPM havuzu varsayımı, çok havuz/FPM cluster = APCu node-yerel → dağıtık sayaç yok · edge: CDN/WAF → `limit_req/limit_conn` → uygulama (ikinci hat) · whitelist + NAT uyarısı + `dry_run` kalibrasyon · fail-open (hizmet sürekliliği) vs fail-closed (güvenlik) seçimi açık yazılır. |
| Web Search **Sonucu** | 1) **Algoritma eşlemesi doğrulandı:** hassas genel/per-route kota → **kayan pencere log**; burst → **token bucket**; brute force → **sayaç + exponential backoff/lockout**; kenar/nginx → `limit_req` (leaky bucket) + `limit_conn` (kaynak 1, 5, 10, 12) → kararın üç algoritmalı çerçevesi literatürle uyumlu. 2) **OWASP dayanağı netleşti:** ASVS 4.0 V2 + 5.0 V6 + Authentication Cheat Sheet + WSTG (3-5 deneme) + Logging Cheat Sheet (kaynak 2, 3, 4, 5) → kota değerleri OWASP tabanlı **önerme** olarak, "varsayılan — ölçümle kalibre edilir" etiketiyle yazılır (kesinlik iddiası yok). 3) **APCu atomikliği:** `apcu_inc` atomik; `get→set/increment` sarmalayıcısı yarışlı; `apcu_store` lock riski → CacheRateLimiter'ın tek adıma indirgenmesi + TTL zorunluluğu (kaynak 6, 7, 8) → §2.2d şartı. 4) **Edge katmanı şartı:** `limit_req`/`limit_conn` uygulama öncesi ilk hat, katmanlı yığın önerisi (kaynak 9-13); depoda conf = 0 (ADR-009 bulgusu) → **PLANNED + deployment artefaktı**. 5) **Kalibrasyon:** whitelist/NAT/`dry_run` uyarısı + OWASP 3-5 deneme → kota değerleri ölçümle revize edilir (risk 4). |
| Web Search **Alınan Karar** | **ADR-013 kabul edilir — İKİ KATMAN + ÜÇ ALGORİTMA + KOTA ÇERÇEVESİ:** **(A) Katman 1 — Edge/WAF (PLANNED):** nginx `limit_req` (leaky bucket) + `limit_conn` veya CDN/WAF; uygulama ayağa kalkmadan önce volumetrik/scraper trafiğini keser; depoda conf yok → `.ai/architecture/k13-cicd/` altında deployment artefaktı olarak şart. **(B) Katman 2 — Uygulama (APCu, ADR-007 L1):** per-IP + **per-account** kotalar, per-route kota, burst poliçesi, brute force **exponential backoff + geçici lockout** (+ audit log). **(C) Algoritma eşlemesi:** hassas kotalar → **sliding window log**; burst → **token bucket**; auth/OTP/reset → **backoff + lockout**; mevcut sabit pencere (60/60 + 5/900) **korunur ve bu üçünün altına oturtulur**, anında silinmez. **(D) Kota çerçevesi (OWASP temelli, varsayılan — ölçümle kalibre edilir):** genel API 60/dk/IP · auth POST 10/dk/IP + 5 başarısız → backoff · OTP 3/dk/hesap · şifre sıfırlama 3/saat · admin 30/dk/IP (register/reset mevcut kodla hizalı: 3/3600). **(E) APCu mekaniği:** tek depo (FPM worker arası paylaşımlı), sayaç güncellemesi `apcu_inc` tek adım, lockout için `apcu_add`-tabanlı atomik kilit, her key'de açık TTL (ADR-007); APCu yoksa **fail-open + edge yedeği**; çok-node'da APCu node-yerel → dağıtık sayaç yok (ADR-007 distributed opsiyonu) → ilk üretimde tek node varsayımı yazılır. |
| Web Search **Sonuç** | Karar 2024-2026 verisiyle **desteklendi ve netleşti**: algoritma karşılaştırması (4 kaynak: ByteByteGo, API7, Kong, systemdesignsandbox/codesmith), OWASP zorunlulukları (5 kaynak: ASVS 4.0/5.0, Authentication Cheat Sheet, WSTG, Logging + Credential Stuffing), APCu atomikliği (4 kaynak: php.net `apcu_inc`, reintech, krakjoe #333, serverfault/SO), edge/nginx katmanı (5 kaynak: NGINX resmî, getpagespeed 2026, edgeservers 2026, KX, virtua), Stripe/Cloudflare mühendislik (2 kaynak) — **toplam ~18 adlandırılmış kaynak, 6 sorgu**; çapraz doğrulama ≥2 kaynak tüm iddialarda karşılanır. Kod bulguları §1.1'de ayrıldı: limiter + auth sayaçları + APCu zinciri **IMPLEMENTED**, sliding window/token bucket/backoff/per-account/OTP/edge **PLANNED** yazıldı, uydurulmadı. `⚠️ VERIFICATION REQUIRED` yalnız (i) API `RateLimitMiddleware` pipeline kaydı, (ii) auth `RateLimitMiddleware` pasif/ölü kod şüphesi, (iii) nginx conf/`k13-cicd` niyet belgeleri, (iv) çok-FPM havuzu paylaşım varsayımı için korunur. **Kaynak listesi (18):** 1) ByteByteGo — Design a Rate Limiter · 2) OWASP ASVS 4.0 V2 (anti-automation/brute force) · 3) OWASP ASVS 5.0 V6 (out-of-band rate limiting) · 4) OWASP WSTG — Testing for Weak Lock Out Mechanism (3-5 deneme) · 5) OWASP Authentication Cheat Sheet (login throttling) · 6) php.net — `apcu_inc` · 7) reintech — APCu paylaşımlı bellek (FPM arası) · 8) krakjoe/apcu issue #333 (`apcu_store` lock) · 9) NGINX Docs — Limiting Access to Proxied HTTP Resources (`limit_req`/`limit_conn`) · 10) getpagespeed 2026 — NGINX Rate Limiting Guide · 11) edgeservers 2026 — Layered rate limiting in Nginx · 12) KX Cloudingenium — Nginx Rate Limiting to Prevent DDoS · 13) virtua.cloud — Nginx Rate Limiting DDoS Protection · 14) Stripe Engineering — Scaling your API with rate limiters (token bucket) · 15) Cloudflare Blog — rate limiting at the edge · 16) Kong — Design a Scalable Rate Limiting Algorithm (sliding window) · 17) API7 — Token Bucket to Sliding Window guide · 18) mojoauth — katmanlı throttle (IP + hesap + exponential backoff). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-007 APCu depo + TTL tablosu | Sayaçlar ADR-007'in L1 APCu'sunda yaşar; `app:v{version}:{domain}:{key}` namespace ve "rate-limit 60sn" TTL satırıyla uyum; TTL=0 (kalıcı key) YASAK; Redis yok (PLANNED opsiyon) |
| ADR-008 bypass + ADR-010/011 auth akışı | Rate limit, bypass/Auth/CSRF/Session middleware'lerinin **öncesinde** çalışır (PageRouterKernel sırası DEĞİŞTİRİLEMEZ); kota aşımı login'i, CSRF token'ını veya oturum işini değiştirmez — üstüne katmandır |
| ADR-009 sunucu katmanı sınırı | Edge (`limit_req`/`limit_conn`) bu ADR'de **PLANNED** olarak kararlaştırılır; conf üretimi deployment artefaktıdır, kod değildir (`.conf` = 0) |
| OWASP temelli kota değerleri | Kesin ölçülmüş değer **DEĞİL** — "varsayılan — ölçümle kalibre edilir"; üretim verisiyle (429 oranı, 403/429 legit şikâyeti) revize edilir |
| Frozen ADR-001-037 dokunulmaz | Bu ADR yeni karar üretir; frozen metinler okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2) |
| REDACTED | Secret/credential/API anahtarı/pepper hiçbir koşulda bu ADR'ye yazılmaz |
| In-Place Refactoring | Dosya adı onay olmadan değiştirilemez; mevcut `RateLimiterMiddleware`/`CacheRateLimiter` adları korunur |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |

---

## 2. Karar (Decision)

CoreMusic rate limiting **İKİ KATMANLI** kabul edilir: **Katman 1 — Edge/WAF (PLANNED):** nginx `limit_req`/`limit_conn` veya CDN/WAF, uygulama ayağa kalkmadan önce volumetrik/scraper trafiğini keser; **Katman 2 — Uygulama (APCu, IMPLEMENTED temel):** per-IP + **per-account** kotalar, per-route kota, burst poliçesi ve login/OTP/şifre sıfırlama için **exponential backoff + geçici lockout** (+ audit log). **Algoritma eşlemesi:** hassas genel/per-route kota → **sliding window log**; burst poliçesi → **token bucket**; brute force → **sayaç + exponential backoff + lockout**; kenar → nginx `limit_req` (leaky bucket) + `limit_conn`. Mevcut sabit pencere (60/60, 5/900) **kaldırılmaz** — üç algoritmanın altındaki ucuz genel hat olarak korunur. Kota değerleri OWASP temelli **önermedir: varsayılan — ölçümle kalibre edilir.**

### 2.1 Neden Bu Seçenek?

- **Savunma derinliği tek katmana bırakılmaz:** tüm trafik PHP'ye inerse uygulama çökmesi rate limit'i de çökertir; edge katmanı uygulama öncesi çalışır (§1.3 kaynak 9-13; ADR-009'un "sunucu birincil" mantığının aynısı).
- **Sabit pencere tek başına yetersiz:** sınır yığını (boundary burst) ve per-route hassasiyeti yok (§1.3 kaynak 1, 16, 17); kayan pencere log + token bucket ikisini de kapatır.
- **Brute force = zaman işi:** sabit 900 sn pencere saldırgana düzenli kota döndürür; OWASP WSTG 3-5 deneme + katlanarak gecikme, deneme hızını saldırgan lehine değil **lehimize** büyütür (§1.3 kaynak 4, 5, 18).
- **APCu zaten elde:** `CacheManager → ApcuAdapter` ve `IRateLimiter` DI kaydı IMPLEMENTED (§1.1) — yeni depo/altyapı gerekmez; sıfırdan Redis/getirme YAGNI (ADR-007: Redis adapter yok).
- **Per-account + per-account olmayan birlikte:** yalnız IP (bugün) NAT/dağıtık saldırganı kaçırır; yalnız hesap (bugün yok) IP kaydırma ile yoklanır → **ikisi birden** (§1.3 kaynak 18, 4).
- **Zero Hallucination (ADR-005):** her madde dosya yolu/satır ile (IMPLEMENTED/PLANNED) veya §1.3 kaynağıyla kanıtlandı; doğrulanamayanlar `⚠️ VERIFICATION REQUIRED`.

### 2.2 Teknik Detaylar

#### 2.2a Katman Mimarisi

```text
[İstemci]
   │
   ▼
KATMAN 1 — EDGE / WAF  (PLANNED — depoda .conf = 0)
   ├─ nginx limit_req_zone (leaky bucket)  → istek hızı / IP+URI anahtarı
   ├─ nginx limit_conn_zone                → IP başına bağlantı sayısı
   └─ (veya) CDN/WAF kuralı                → volumetrik DDoS + bilinen kötü niyetli IP
   │  429 / 503 burada üretilir; PHP'ye inmez
   ▼
KATMAN 2 — UYGULAMA (APCu, ADR-007 L1)  [PageRouterKernel:273-282]
   ├─ RateLimiterMiddleware   : genel per-IP (bugün 60/60 sabit pencere)
   ├─ API RateLimitMiddleware : per-route / API-key kotası (withLimits — kaydı ⚠️) → debate şartı 1d
   ├─ AuthService sayaçları   : login/register/reset (per-IP) + YENİ per-account sayaç → debate şartı 1b
   └─ Backoff/Lockout katmanı : başarısızlık sayacı → katlanan gecikme → geçici lock + audit
   │  429 + Retry-After (+ opsiyonel X-RateLimit-*)
   ▼
[Auth/CSRF/Session/Permission — ADR-008/010/011 (değiştirilmez sıra)]
```

- **Katman görevleri ayrışır:** edge = hacim + bağlantı; uygulama = kimlik/uisle/hassas kota. İkisi de aynı anda açık olur (ikinci hat her zaman devrede).
- **Sıra kutsaldır:** `OriginCheck → Cors → RateLimiter → SecurityHeaders → Session → Csrf → BypassAuth → Auth → Permission → Validation` (`PageRouterKernel.php:273-282`) — rate limit oturum/CSRF'ten önce çalışır, oturum maliyetine girmeden reddeder.

#### 2.2b Algoritma ↔ Katman Eşlemesi

| # | Katman / Kota | Algoritma | Neden bu algoritma | Durum |
|---|---------------|-----------|--------------------|-------|
| 1 | Edge genel trafik (`limit_req`) | **Leaky bucket** (nginx iç) + `limit_conn` | Uygulama öncesi sabit çıkış hızı; bağlantı sayısını da keser (volumetrik) | **PLANNED** (conf yok) — debate şartı 2 ile bağlandı |
| 2 | Uygulama genel per-IP | **Sabit pencere** (bugün) → kayan pencere sayacına geçiş | Ucuz O(1); tek başına sınır burst üretir → hassasiyet 3 numaraya devredilir | **IMPLEMENTED** (`RateLimiterMiddleware.php:37-48`) |
| 3 | Hassas genel kota + **per-route API** | **Sliding window log** (zaman damgalı kayıt) | En hassas sayım; pencere sınırında yığın yok; route başına ayrı kota (`withLimits`) | **PLANNED** |
| 4 | **Burst poliçesi** (kısa sıçrayışa izin) | **Token bucket** (kapasite + dolum oranı) | Burst kontrollü: kısa süre izin, sonra kota — Stripe pratikleri (§1.3 kaynak 14) | **PLANNED** |
| 5 | **Login / OTP / şifre sıfırlama** brute force | **Exponential backoff + geçici lockout** (sayaç + `lock_until`) | Zamanı saldıran aleyhine büyütür; ASVS/WSTG kilit gerekçesi (§1.3 kaynak 2-5) | **PLANNED** (bugün: sabit 5/900s) — debate şartı 1b ile kapatılacak |
| 6 | Denetim | Audit olayı (`login_attempt`, `rate_limited` → warning) | OWASP Logging: tekrarlı başarısızlık izlemesi (§1.3 kaynak 5) | **IMPLEMENTED** (`FileHandler.php:84`, `AuthController.php:134`) |

**Backoff tablosu (önerilen — varsayılan, ölçümle kalibre edilir):**

| Başarısızlık | Bekleme | Sonrası |
|--------------|---------|---------|
| 1-4 | yok (serbest) | sayaç + audit |
| 5. | **30 sn** geçici lock | `lock_until = now + 30s` |
| 6-7. | **60 sn → 120 sn** (katlanır) | her başarısızlıkta ikiye katlar |
| 8. ve sonrası | **15 dk lock** + audit `warning` | `lock_until = now + 900s` |
| Başarı | sayaç **reset** (bugünkü `AuthService:96` davranışı korunur) | — |

#### 2.2c Kota Çerçevesi (OWASP temelli — **varsayılan: ölçümle kalibre edilir**)

| # | Uç / Kapsam | Kota | Pencere | Katman | Algoritma | Dayanak / Kod |
|---|-------------|------|---------|--------|-----------|---------------|
| 1 | Genel API (per-IP) | **60** | 60 sn (1 dk) | Uygulama | sabit pencere (→ sliding) | `RateLimiterMiddleware` 60/60 ✅ hizalı |
| 2 | Auth POST (per-IP) | **10** | 60 sn | Uygulama | sabit pencere + backoff | bugün 5/900 sn var (AuthService:31-32); OWASP katmanlı 5-10/dk (§1.3 kaynak 18) |
| 3 | Auth başarısız deneme (per-IP **+ per-hesap**) | **5** başarısız | backoff'a geçiş | Uygulama | exponential backoff + lockout | WSTG 3-5 deneme (§1.3 kaynak 4) |
| 4 | OTP (per-**hesap**) | **3** | 60 sn | Uygulama | sliding window | ASVS 5.0 V6 out-of-band rate limit (§1.3 kaynak 3) — akış yok → PLANNED |
| 5 | Şifre sıfırlama (per-IP) | **3** | 3600 sn (1 saat) | Uygulama | sabit pencere | `AuthService:40-41` 3/3600 ✅ hizalı |
| 6 | Kayıt (per-IP) | **3** | 3600 sn | Uygulama | sabit pencere | `AuthService:33-34` 3/3600 ✅ hizalı |
| 7 | Admin uçlar (per-IP) | **30** | 60 sn | Uygulama | sliding window | ADR-013 önermesi (yeni — kodda yok) |
| 8 | Edge genel (per-IP) | **300** | 60 sn + `burst` | Edge | `limit_req`/`limit_conn` | PLANNED (conf yok) — `dry_run` ile kalibre |

> **Kalibrasyon kuralı:** bu 8 satır üretimde **ölçümle** revize edilir (429 oranı, legit kullanıcı şikâyeti, NAT/ortak IP); kesin değer iddiası taşımaz. Whitelist (crawler/health-check) edge'de tutulur; NAT arkasındaki paylaşımlı IP'ler için kota üst sınırı gözden geçirilir (§1.3 NGINX resmî uyarısı).

#### 2.2d APCu Mekaniği (çok-process atomikliği — ADR-007 ile entegrasyon)

| # | Konu | Karar / Gerçek | Kanıt |
|---|------|----------------|-------|
| 1 | Tek depo | Tüm FPM worker'ları **aynı APCu önbelleğini** paylaşır → sayaçlar süreçler arası ortak | `CacheManager.php:9-19` → `ApcuAdapter`; §1.3 kaynak 7 |
| 2 | Atomik artış | Sayaç güncellemesi **tek atomik adım**: `apcu_inc($key, $step, $ttl)` — `get→set/increment` yarışı kapatılır | `apcu_inc` php.net (§1.3 kaynak 6); bugünkü yarış `CacheRateLimiter.php:26-35`; debate şartı 1c ile kapatılacak |
| 3 | Lockout kilidi | `lock_until` bayrağı **`apcu_add`** ile yazılır (mevcutsa ekleme yok = kilit korunur), sonra `apcu_store`/`apcu_inc` ile TTL güncellenir | CAS benzeri tek-adım deseni; `apcu_store` high-concurrency lock riski (§1.3 kaynak 8) |
| 4 | TTL zorunlu | Her sayaç/lockout key'inde açık TTL (rate-limit 60 sn — ADR-007 TTL tablosu); TTL=0 YASAK | ADR-007 §2.2c; `ApcuAdapter.php:17-23` |
| 5 | FPM havuzu varsayımı | **Tek FPM havuzu** varsayımı; birden çok havuz/pool veya ayrı CLI-only süreçte APCu paylaşımı ⚠️ VERIFICATION REQUIRED | serverfault/SO tartışması (§1.3) |
| 6 | Çok-node (cluster) | APCu **node-yerel** → dağıtık sayaç yok; ilk üretim **tek node** varsayımıyla yazılır; dağıtım gerekirse ADR-007'in opsiyonel distributed adapter (CacheInterface + predis) hattı açılır | ADR-007 §2.2a opsiyonu |
| 7 | APCu yoksa | `CacheManager` → `MemoryAdapter` (süreç-başına) + `RateLimiterMiddleware` fail-open (`:30-32`) → kota **gevşer**, hizmet ayakta kalır; **edge katmanı (Katman 1) yedektir** | `CacheManager.php:14-15`, `RateLimiterMiddleware.php:30-32`; debate şartı 1a ile kapatılacak |
| 8 | DoS / eviction | DoS APCu'yu doldurabilir → `apc.ttl`/GC eviction sayaçları yutabilir → kota **şişer (fail-open)**; izleme: `Cache full count` + 429 oranı; TTL kısa tutulur | ADR-007 §1.3 APCu eviction sırası; php.net `apc.ttl` |

#### 2.2e Edge / WAF Katmanı (PLANNED — deployment artefaktı)

```nginx
# PLANNED — depoda yok (**/*.conf = 0); .ai/architecture/k13-cicd/ altında üretilir
limit_req_zone  $binary_remote_addr zone=cm_general:10m rate=300r/m;   # genel (kota 8)
limit_conn_zone $binary_remote_addr zone=cm_conn:10m;
server {
    limit_req zone=cm_general burst=60 nodelay;   # burst + nodelay = kısa sıçrayışa izin
    limit_conn cm_conn 20;
    # dry_run ile önce gözlemle, sonra enforce (yanlış kota legit kullanıcı bloklar)
}
```

- **İlk savunma = edge** (uygulama ayağa kalkmadan): volumetrik/scraper/brute-force ilk dalga burada kesilir; uygulama katmanı **ikinci hat** (kimlik/hassas kota).
- `dry_run` → log → kalibrasyon → enforce sırası zorunlu (§2.2c kalibrasyon kuralı).
- CDN/WAF (Cloudflare benzeri) varsa `limit_req` ile **katmanlı** çalışır — ikisi yerine biri değil (§1.3 kaynak 11).

#### 2.2f İddia-Kod Çelişkileri (işaretli — debate ✅ TAMAMLANDI; satır 2-3 şart 1d ile kapatılacak)

| # | Çelişki | Kanıt | Etiket |
|---|---------|-------|-------|
| 1 | `Middleware/CLAUDE.md:89` "`X-RateLimit-Remaining`, `X-RateLimit-Reset`" ↔ kod yalnız `Retry-After` gönderiyor | `RateLimiterMiddleware.php:48-55` | İddia-kod çelişkisi — düzeltme/ekleme debate kararı (ekleme tercih: başlıklar standarda uygun) |
| 2 | API `RateLimitMiddleware` sınıfla var, pipeline kaydı grep'te yok | grep: yalnız tanım + `use` | ⚠️ VERIFICATION REQUIRED (pasif kod olabilir) — debate şartı 1d ile kapatılacak |
| 3 | auth `RateLimitMiddleware` (APCu pencereli) sınıfla var, `MiddlewarePipeline` kaydı yok | grep auth: yalnız tanım | ⚠️ VERIFICATION REQUIRED (ölü kod şüphesi — ADR-012 §2.2f deseni) — debate şartı 1d ile kapatılacak |
| 4 | "Rate limit APCu'da mı?" | **EVET — IMPLEMENTED** (`CacheManager→ApcuAdapter`, auth DI `AuthContainer:61-69`) | İddia-kod **uyumlu** ✅ |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız uygulama katmanı** (edge'siz, bugünkü hâlin genişlemesi) | Tek kod tabanı, deploy basit | Volumetrik DDoS/scraper PHP'ye kadar iner; uygulama çökünce rate limit de çöker; CPU/conn maliyeti saldırıda ödenir | §1.2 madde 5 + ADR-009 iki-katman dersi: en hızlı/en güvenli hat uygulama öncesi olmalı (§1.3 kaynak 9-13) |
| 2 | **Yalnız edge** (uygulama kotası yok) | Uygulama koduna dokunulmaz, hacmi keser | Kimlik/hesap seviyesinde hassas kota yok: 5 denemelik brute force edge'i aşar; rate limit auth'un içine girmez | Edge anahtarları IP'dir — per-account/backoff edge'de yapılamaz (OWASP ASVS hesap bazlı kilit); ADR-008/010/011 katmanı boş kalır |
| 3 | **Redis tabanlı dağıtık sayaç** | Çok-node'da tutarlı; atomik operasyonlar hazır | Depoda Redis adapter **yok** (ADR-007 glob bulgusu) → yeni altyapı + bağımlılık; tek node için gereksiz karmaşa | YAGNI: APCu zaten IMPLEMENTED ve FPM'ler arası paylaşımlı; ADR-007 distributed adapter **opsiyonel** not olarak kalır (çok-node'a geçişte açılır) |
| 4 | **Yalnız sabit pencere** (bugün ne varsa onu koru, algoritma ekleme) | Sıfır iş gücü; kod zaten çalışıyor | Sınır burst; brute force pencere sonunda temiz kota alır; per-route/burst yok | §1.2 madde 1-4 doğrudan ret gerekçesi; OWASP 3-5 deneme + backoff literatürü (§1.3 kaynak 4, 18) sabit pencereyi tek başına yetersiz kılar |
| 5 | **Fail-closed** (APCu yoksa her şeyi reddet) | Güvenlik "katı" görünür | Cache/uptime arızasında tam servis kesintisi; rate limit birincil hizmet değil | Mevcut kod fail-open (`RateLimiterMiddleware:30-32`) — hizmet sürekliliği esastır; güven ikinci hat (edge + auth sayaçları) ile sağlanır |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **İki hat:** edge (hacim/bağlantı) + uygulama (kimlik/hassas kota) — uygulama ayağa kalkmadan ilk savunma, düştüğünde ikinci hat ayakta.
- **Brute force bedeli artar:** exponential backoff + 15 dk lockout ile deneme hızı saldırgan aleyhine büyür; per-account sayaç IP kaydırma ile hesabı yoklamayı da kapatır.
- **Hassasiyet:** sliding window log ile per-route kotalarda pencere sınırı yığını kapanır; burst poliçesi (token bucket) legit kısa sıçrayışı boğmadan kota korur.
- **Sıfır yeni altyapı:** APCu + `CacheInterface` + `IRateLimiter` zaten IMPLEMENTED (ADR-007 L1) — Redis gerekmez (YAGNI).
- **Denetlenebilirlik:** `login_attempt` + `rate_limited` (warning) olayları OWASP Logging izleme şartını karşılar; 429 oranı kalibrasyon metriği olur.
- **Standart uyum:** OWASP ASVS/Cheat Sheet/WSTG gerekçeleri §1.3'te kaynaklı.

### 4.2 Olumsuz Sonuçlar

- **İki yerde kota yaşamı:** edge conf + uygulama kotası drift edebilir (ADR-009'daki kural drifti dersi) → tek kaynak kota tablosu (§2.2c) + `dry_run` gerekir.
- **Bellek/eviction baskısı:** her IP/hesap/route için key → APCu'da satır üretir; DoS altında `Cache full count` yükselir, sayaçlar yutulabilir (fail-open).
- **APCu node-yerel:** çok-node'a geçişte sayaçlar bölünür → kota fiilen katlanır; distributed adapter işi (ADR-007 opsiyonu) sonraya kalır.
- **NAT/ortak IP:** paylaşımlı IP'lerde 60/dk ortak kota legit kullanıcıları tehdit eder (edge whitelist + üst sınır gözden geçirmesi şart).
- **Kalibrasyon işi:** 8 satırlık kota tablosu ölçülmeden prod'a alınırsa 429 şikâyeti üretir → `dry_run`/monitoring oturumu ekler.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **APCu yoksa `MemoryAdapter` + fail-open → sayaçlar süreç-başına, kota fiilen gevşer** | 3 (olası) | 3 (orta) | Deployment kontrolü: `ext-apcu` zorunlu (composer/ops); edge katmanı (Katman 1) ikinci yedek; APCu durumu health-check'e girer |
| **Çok-FPM'de get→set yarışı → sayaç kaçar** (bugünkü `CacheRateLimiter`) | 3 (olası) | 3 (orta) | Sayaç güncellemesi `apcu_inc` tek atomik adıma indirgenir + lockout `apcu_add` (§2.2d satır 2-3) |
| **DoS → APCu dolar, eviction sayaçları yutar → kota şişer (fail-open)** | 3 (olası) | 4 (yüksek) | Edge `limit_conn`/`limit_req` hacmi keser; key TTL kısa (60 sn); `Cache full count` + 429 oranı alarmı (ADR-007 APCu izleme) |
| **Yanlış/tight kota → legit kullanıcı bloğu** (NAT, crawler, health-check) | 3 (olası) | 4 (yüksek) | "Varsayılan — ölçümle kalibre edilir" + `dry_run` fazı + whitelist + 429'da `Retry-After` + kota tablosu tek kaynak (§2.2c) |
| **Edge conf yok (PLANNED) → ilk hat hiç kurulmaz** | 4 (çok olası) | 4 (yüksek) | `.ai/architecture/k13-cicd/` altında deployment artefaktı olarak şart; ADR-009 adım 4 ile birlikte üretilir; `**/*.conf = 0` bulgusu ⚠️ |
| **Pasif/ölü limiter sınıfları (API + auth) → iki limiter yarışı veya hiç kayıt yok** | 3 (olası) | 3 (orta) | ⚠️ VERIFICATION REQUIRED: pipeline kaydı doğrulanır; gerekirse tek middleware'de birleştirilir (ADR-012 §2.2f deseni) |
| **XFF/istemci IP sahteceliği** (trusted proxy listesi dar/geniş) | 2 (mümkün) | 3 (orta) | `TRUSTED_PROXIES` yalnız gerçek proxy'ler; XFF yalnız trusted proxy'den okunur (bugünkü davranış korunur, `RateLimiterMiddleware:66-75`) |

### 4.4 Vault Çapraz Referans

| ADR | İlişki |
|-----|--------|
| [[ADR-007-cache-namespace]] | APCu = L1 depo; `app:v{ver}:{domain}:{key}` namespace + TTL tablosu "rate-limit 60sn" bu ADR'nin satırıdır; çok-node dağıtık adapter opsiyonu bu ADR'nin fallback'i |
| [[ADR-008-bypass-auth-middleware]] | Brute force kalkanının çalıştığı kapı; bypass kapsamı bu ADR ile **genişlemez** — rate limit bypass'ın üstünde ayrı katman |
| [[ADR-009-clean-url-redirect]] | Sunucu/edge katmanı PLANNED bulgusu (`**/*.conf` = 0) buradan devralınır; edge conf'i ADR-009 adım 4 ile birlikte üretilir |
| [[ADR-010-csrf-protection-strategy]] | Token katmanı rate limit'in **üstündeki** ikinci hat; rate limit 429 üretir ama CSRF doğrulamaz (sorumluluk ayrımı) |
| [[ADR-011-session-management]] | Başarılı login'de sayaç `reset` (`AuthService:96`) + oturum rotasyonu; backoff'un hedeflediği edinim oturum güvenliğidir |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Atomik sayaç:** `CacheRateLimiter` → `apcu_inc` tek adım (+ TTL), lockout `apcu_add` ile `lock_until`; `get→set` yarışı kapatılır (§2.2d 2-3) | Security Engineer + Backend Architect | 1 oturum |
| 2 | **Backoff + lockout + per-account sayaç:** AuthService'e `rate_limit:login:{ip}` **+** `rate_limit:login:{account}` anahtarları, 5 başarısız → 30s → katlanır → 15 dk lock + audit; başarıda reset korunur (§2.2b tablo) | Security Engineer | 1-2 oturum |
| 3 | **Sliding window log + token bucket:** per-route API kotası (`withLimits`) ve burst poliçesi; API middleware pipeline kaydı ⚠️ doğrulanır (§2.2f 2) | Backend Architect | 2 oturum |
| 4 | **Edge katmanı (PLANNED):** nginx `limit_req`/`limit_conn` conf'i `.ai/architecture/k13-cicd/` altında artefakt olarak + `dry_run` → log → kalibrasyon → enforce (§2.2e) — debate şartı 2 | DevOps Engineer + Security Engineer | 1-2 oturum |
| 5 | **İddia-kod düzeltmeleri:** `Middleware/CLAUDE.md:89` `X-RateLimit-*` başlıkları ↔ kod (ekle veya düzelt) + auth/API pasif limiter sınıflarının pipeline kaydı (§2.2f 1-3) | Vault Steward + Security Engineer | 0.5 oturum |
| 6 | **Testler:** 429 + `Retry-After`, sınır burst (pencere bitimi), per-account sayaç, backoff zaman çizelgesi, lockout TTL, APCu yoksa fail-open, edge `dry_run` logları + debate şartı 3 (NAT/VPN yanlış-kota + 429/Retry-After test paketi) | QA Engineer | 1-2 oturum |
| 7 | **Kalibrasyon penceresi:** 429 oranı + legit şikâyet izleme → §2.2c kota tablosunu ölçümle revize et ("varsayılan — ölçümle kalibre edilir" kapanışı) | Security Engineer + DevOps Engineer | sürekli (2 hafta ilk pencere) |

### 5.2 Geri Dönüş Planı

1. **Adım 2 (backoff/per-account):** yeni sayaç key'leri silinir (`rl:`/`rate_limit:*` TTL 60-900 sn ile zaten kendiliğinden söner) → `AuthService` `git revert` → bugünkü 5/900 sabit pencereye tam dönüş; oturumlar etkilenmez.
2. **Adım 1 (atomik sayaç):** `git revert` → `CacheRateLimiter` eski get/set hâline döner (yarış geri gelir ama davranış değişmez); APCu'ya bağımlılık değişmez.
3. **Adım 3 (sliding/token bucket):** `withLimits`/yeni limiter devre dışı → `RateLimiterMiddleware` 60/60 sabit pencereye düşer (bugünkü hâl).
4. **Adım 4 (edge):** nginx `limit_req`/`limit_conn` satırları `dry_run`'a alınır veya `nginx reload` ile kaldırılır → trafik doğrudan uygulamaya iner (bugünkü hâl); conf yokken zaten kayıp yoktur.
5. **Acil (kota legit blokluyorsa):** §2.2c tablosunda kota ↑ + whitelist; APCu'da sayaçlar `apcu_delete`/TTL ile temizlenir. Her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/` ile vault bütünlüğü; bozulma → `vault-utf8-writer.mjs repair` + `git checkout`.

### 5.3 Debate Şartları

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona, 19 kabul / 1 çekimser / 0 red → **KABUL** (2026-09-24; §7.1) |
| Bağlayıcı şart | **3 bağlayıcı şart** (§5.4) — §5.1 adımları bu şartlarla bağlayıcı listeye dönüşür |
| Öneri havuzu | (1) atomik sayaç + lockout (`apcu_add`) · (2) per-account kota · (3) edge conf artefaktı + `dry_run` kalibrasyon · (4) `X-RateLimit-*` iddia-kod düzeltmesi · (5) pasif limiter sınıflarının kaydı → tamamı §5.4 şartlarına bağlandı |

### 5.4 Debate Bağlayıcı Şartları (3 şart — KABUL)

| # | Şart | Kapsam | Bağlandığı §2/§5 satırı | Sorumlu |
|---|------|--------|--------------------------|---------|
| 1 | **4 kod açığı** | **(1a)** auth uçlarında **fail-closed**, genel uçlar fail-open · **(1b)** per-account + IP **ikili sayaç** (`rate_limit:login:{account}` + `rate_limit:login:{ip}`) · **(1c)** atomik `apcu_inc` zinciri + **TTL zorunlu** (get→set yarışı kapanır) · **(1d)** **tek kaynak** `RateLimiterMiddleware` + API middleware pipeline'a kayıt | 1a → §2.2d satır 7 · 1b → §2.2b satır 5 + §2.2a · 1c → §2.2d satır 2 · 1d → §2.2a + §2.2f satır 2-3 (hepsi "debate şartı ile kapatılacak" etiketli) | Security Engineer + Backend Architect |
| 2 | **Edge deployment artefaktı** | nginx `limit_req`/`limit_conn` conf'i `.ai/architecture/k13-cicd/` altında artefakt (**PLANNED → şart**); `dry_run` → log → kalibrasyon → enforce | §2.2b satır 1 · §2.2e · §5.1 adım 4 | DevOps Engineer |
| 3 | **NAT/VPN + yanıt test paketi** | NAT/VPN yanlış-kota senaryoları (paylaşımlı IP'de legit blok riski) + 429/`Retry-After` testleri (sınır burst, per-account sayaç, backoff zaman çizelgesi, lockout TTL) | §2.2c kalibrasyon kuralı · §4.3 NAT riski · §5.1 adım 6 | QA Engineer + Security Engineer |

> **Kural:** bir şart kapatılmadan ilgili §2 satırı "çözüldü" sayılmaz; her şart kapandığında `.ai/log.md` append ile kaydedilir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — §3 satır 50 `[[ADR-013-rate-limiting-apcu]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`rate limit` → Security Engineer) |
| [[../../WORKFLOW.md]] | Debate/onay akışı bağlamı |
| [[../../brain.md]] | Mimari karar özeti (bu ADR'den türetilir) |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| [[ADR-007-cache-namespace]] | APCu L1 depo + TTL tablosu + distributed adapter opsiyonu (dosya diskte VAR ✅) |
| [[ADR-008-bypass-auth-middleware]] | Auth bypass sınırı — kalkan çalıştığı kapı (dosya diskte VAR ✅) |
| [[ADR-009-clean-url-redirect]] | Edge/sunucu katmanı PLANNED bulgusu (`**/*.conf` = 0) (dosya diskte VAR ✅) |
| [[ADR-010-csrf-protection-strategy]] | Token katmanı üstteki ikinci hat (dosya diskte VAR ✅) |
| [[ADR-011-session-management]] | Oturum hijyeni + sayaç reset (dosya diskte VAR ✅) |
| `shared/src/Middleware/RateLimiterMiddleware.php` | Per-IP sabit pencere 60/60 + 429/`Retry-After` — IMPLEMENTED çekirdek (9-59) |
| `shared/src/PageRouter/PageRouterKernel.php` | Pipeline kaydı + sabitler (23-24, 273-282) — sıra kanıtı |
| `shared/src/Security/CacheRateLimiter.php` · `shared/src/Api/Middleware/RateLimitMiddleware.php` | sayaç çekirdeği (check-then-act ⚠️) + API per-route iskeleti (`withLimits`) |
| `shared/src/Cache/CacheManager.php` · `ApcuAdapter.php` | APCu → `apcu_inc`/`apcu_store` zinciri (ADR-007 L1) |
| `auth.coremusic.net/include/Service/AuthService.php` · `include/Container/AuthContainer.php` | login 5/900, register 3/3600, reset 3/3600, `reset` (31-41, 57-96, 117-145, 229-236) + `IRateLimiter` DI (61-69) |
| `auth.coremusic.net/include/Middleware/RateLimitMiddleware.php` | Pasif/⚠️ ikinci limiter sınıfı (kayıt yok — §2.2f 3) |
| `shared/src/Middleware/CLAUDE.md` | Rate limit iddiaları (41, 57, 88-90) — `X-RateLimit-*` çelişkisi §2.2f 1 |
| `shared/src/Log/FileHandler.php` · `auth/.../AuthController.php` | `rate_limited` warning + `login_attempt` audit (84; 134) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte VAR ✅) |
| §5.3 · §5.4 · §7.1 debate kaydı | **✅ TAMAMLANDI** — 3 tur / 20 persona (19/1/0 KABUL) + 3 bağlayıcı şart (§5.4) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-013'ü sıfırdan yaz"; kapsam 1&3 + 1&2) | 2026-09-24 | ✅ |
| Tech Lead | (debate 3/20 KABUL — 19/1/0) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010/011/012 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)** |
| Tur 1 | 20 persona — kod kanıtları: `RateLimiterMiddleware` fail-open (satır 30-32) + sabit pencere; `CacheRateLimiter` check-then-act yarışlı; auth limitleri yalnız IP (`rate_limit:login:{ip}`); auth `RateLimitMiddleware` pipeline'a kayıtlı değil; `X-RateLimit-*` başlıkları kodda yok → **14 kabul/neutral, 5 uyarı**; Critic: iki limiter yarışı |
| Tur 2 (İtiraz→Çözüm) | 4 itiraz → 4 çözüm → şart 1a-1d: (1) fail-open → auth uçlarında fail-closed, genel uçlar fail-open (**1a**); (2) IP-only lockout → per-account + IP ikili sayaç (**1b**); (3) check-then-act yarış → atomik `apcu_inc` zinciri + TTL zorunlu (**1c**); (4) iki rakip limiter → tek kaynak `RateLimiterMiddleware`, API middleware pipeline'a kayıt (**1d**) |
| Tur 3 (Oy) | **19 kabul / 1 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL** — 3 bağlayıcı şart (§5.4): (1) 4 kod açığı (fail-closed auth · per-account · atomik · tek limiter), (2) nginx edge `limit_req`/`limit_conn` deployment artefaktı (PLANNED→şart), (3) NAT/VPN yanlış-kota + 429/`Retry-After` test paketi |
| Tech Lead | ✅ (2026-09-24) |
| Öneri havuzu | §5.3 — 5 öneri tamamı şartlara bağlandı (şart 1a-1d · şart 2 · şart 3) |
| Not | Durum `accepted` (kullanılabilir, frozen YOK); debate kaydı bu tablo + §5.3/§5.4 + frontmatter `debate` alanına işlendi; `log.md` append ile alındı |

---

*1.0.0 | 2026-09-24 | Created*
*Authority: ADR-013 Karar Metni — CoreMusic Architecture Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
