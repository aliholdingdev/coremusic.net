---
title: "CoreMusic — ADR-007: Cache Namespace Standard (Katmanlı Cache Hiyerarşisi + TTL + Invalidation)"
type: adr
category: architecture
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-007 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-007: Cache Namespace Standard (Katmanlı Cache Hiyerarşisi + TTL + Invalidation)

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "sıfırdan yaz") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅
**İlgili ADR'ler:** [[ADR-006-performance-targets]] (performans eşikleri — bu kararın hız hedefleri bağlamı; cache katmanları TTFB/p95 hedeflerine hizmet eder) · [[ADR-003-multi-db-bcnf]] (domain kavramı — namespace şemasındaki `{domain}` alanı 18 DB domain mantığıyla uyumludur) · [[ADR-081-multi-provider-data-sync]] (outbox event'leri — event-driven invalidation'ın olay kaynağı) · karar dizini [[../index]] §3 satırı `ADR-007-cache-namespace` (slug eşleşmesi ✅ — dizin satırı diskte mevcut). · Eski seri: ADR-013 (rate limiting APCu), ADR-011 (session 3600s) — `.ai/.decisions/index.md` §3'te kayıtlı ama tekil dosyaları diskte YOK → eski numaralara wiki-link KURULMAZ, düz metin.

---

## 1. Bağlam (Context)

CoreMusic'ın cache katmanı **standartsızdır**: key namespace şeması yok, TTL varsayılanları veri tipine göre yazılmamış, invalidation stratejisi (TTL / event / versiyon) seçilmemiş. `.ai/CLAUDE.md` satır 335 cache'i **Redis** olarak "IMPLEMENTED" diyor ama aynı dosya satır 750'te "Redis L0 hedef→PLANNED notu" düzeltme kalemi tutuyor; disk kanıtı `shared/src/Cache/` altında **Redis adapter olmadığını** gösteriyor (yalnız `ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php` + `CacheManager.php` zinciri — glob kanıtı; `.ai/ROLE.md` satır 624 aynı düzeltmeyi kaydetmiş: "Redis adapter yok — `CacheManager.php` zinciri"). namespace'siz key'ler domain'ler arası çarpışmaya, sabitsiz TTL'ler bayatlamaya, seçilmemiş invalidation ise **cache stampede**'e açık bırakır. Bu karar **katmanlı cache hiyerarşisini** (APCu → dosya → HTTP headers), **`app:v{version}:{domain}:{key}` namespace şemasını**, **veri tipi bazlı TTL varsayılan tablosunu** ve **3'lü invalidation stratejisini** (TTL + event-driven + versiyon bump) tescil eder.

### 1.1 Mevcut Durum

- **Kod (disk kanıtı):** `shared/src/Cache/` altında 7 dosya: `CacheManager.php`, `ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php`, `CacheInterface.php`, `PageCacheInterface.php`, `CLAUDE.md` — **Redis adapter YOK** (glob kanıtı). `ext-apcu` composer bağımlılığı IMPLEMENTED (`.ai/agents/backend-architect.md` satır 137 — ADR-013 altyapısı).
- **Vault çelişkisi (Truth Mode):** `.ai/CLAUDE.md` satır 335 `| **Cache** | Redis | IMPLEMENTED |` vs satır 750 `| 3 | Redis L0 hedef→PLANNED notu |` — kod lehine çözüm: Redis **hedef/PLANNED**, kullanılmıyor. `.ai/ROLE.md` satır 624 aynı düzeltmeyi yapmış ("Redis adapter yok"). Hedef stack `brain.md` satır 80-81'de: `symfony/cache` (PSR-6) + `predis/predis` — ikisi de bağımlılık kaydı, Redis adapter kodda yok.
- **Namespace/TTL/invalidation (vault kanıtı):** `app:v{version}:{domain}:{key}` şeması, TTL varsayılan tablosu ve invalidation seçimi vault kök dosyalarında YOKTUR (grep kanıtı: `namespace` eşleşmesi `.ai/CLAUDE.md` içinde cache bağlamında 0). `.ai/.decisions/index.md` satır 44 bu ADR'nin kaydını taşıyor (`[[ADR-007-cache-namespace]]` — slug eşleşmesi ✅).
- **HTTP cache (vault kanıtı):** `Cache-Control`/`ETag` başlık standardı vault'ta ve kodda yazılmamış — `.github/workflows/` var ama cache-header gate'i yok (ADR-006 CI gate'i bu başlıkları da kucaklayabilir).
- **Kapsam dışı:** kod implementasyonu (`CacheManager.php` genişletmesi ayrı iş); rate-limit cache ADR-013'ün tekelinde (bu ADR veri cache'i içindir — `.ai/.agents/data-engineer.md` satır 122 aynı ayrımı kaydetmiş: "veri cache ≠ rate-limit cache").

### 1.2 Sorun Tanımı

(1) **Namespace yokluğu:** düz key'ler (`user:1`) iki domain'in CacheManager'ında çarpışabilir; eski key'ler silinemez, hangi domain'in ne kadar bellek kullandığı ölçülemez. (2) **TTL sabitsizliği:** "her şeye 1 saat" ya da "TTL yok" (APCu'de TTL=0 = elle silinene kadar kalıcı — php.net) bayat veri veya gereksiz yeniden hesap üretir. (3) **Invalidation seçimsizliği:** yalnız TTL = event sonrası bayat pencere; TTL'siz = sonsuz bayat; versiyonsuz = yayılım (deploy) anında eski key'lere istek gitmeye devam eder. (4) **Stampede açıklığı:** hot key eşzamanlı yeniden hesaplanabilir (cache miss dalgası → DB'ye N kat yük). (5) **Redis statüsü belirsiz:** vault "IMPLEMENTED" der, kod adapter içermez → yanlış beklenti (PLANNED ile kullanılmıyor ayrımı yazılmazsa yeni biri Redis'e kod yazar ve kırılır).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅) — birincil/resmî kaynak önce, **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) cache-aside vs write-through 2025-26; (b) cache namespace/naming best practice; (c) cache stampede/thundering herd mitigations (mutex, jitter, probabilistic early expiration); (d) APCu PHP 8.4 user cache + TTL semantics; (e) HTTP cache headers (Cache-Control/ETag/SWR); (f) veri tipi bazlı TTL varsayılanları.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "cache-aside vs write-through pattern 2025 best practices when to use" · (2) "cache key namespace naming best practice colon separator version domain isolation" · (3) "cache stampede thundering herd mitigation probabilistic early expiration jitter mutex lock" · (4) "APCu user cache PHP 8.4 TTL semantics apcu_ttl apcu_entry TTL 0 expiration" · (5) "HTTP cache Control ETag Cache-Control best practice stale-while-revalidate max-age CDN 2025" · (6) "recommended TTL values cache session config data cache duration seconds best practice" (ilk istek HTTP 503 — yeniden denendi ✅) |
| Web Search **Konusu** | Cache-aside (lazy loading) vs write-through 2025-26 tercih gerekçeleri; Redis/eşdeğer key namespace + colon delimiter + version/domain prefix kuralları; cache stampede/thundering herd teknikleri (single-flight mutex, TTL jitter, probabilistic early expiration / XFetch); APCu'nun PHP 8.4 user-cache TTL semantiği (`apc.ttl`, `apc.gc_ttl`, TTL=0 davranışı); HTTP katmanı Cache-Control/ETag/stale-while-revalidate (RFC 5861) politikaları; veri tipi bazlı TTL varsayılan süreleri (session/config/hot-data/statik). |
| Web Search **Bağlam** | ~35 kaynak (2025-2026 tarihli): birincil — php.net apcu.configuration + apcu_entry manual, MDN Cache-Control, web.dev HTTP cache, RFC 5861 (SWR), AWS CloudFront expiration docs, redis.io blog (namespace), antirez cache-stampede sayfası; ikincil — HLD Handbook (cache strategies), Redisson glossary, Ensolvers, c-sharpcorner, redimo.dev, oneuptime (naming + stampede, Oca. 2026), dev.to, mojoauth (Haz. 2026 — XFetch/VLDB 2015), vergecloud, daily.dev (TTL invalidation), simonhearne (caching header best practices), jonoalderson (HTTP caching profilleri), blazingcdn (Nis. 2026 SWR ölçümü), greadme (long TTL), debugbear (SWR zaman çizelgesi), krakjoe/apcu issue #196, Drupal/SO APCu GC notları. |
| Web Search **Kısa Açıklama** | **Cache-aside 2025-26'da okuma-ağırlıklı işlerin varsayılanıdır** (>90% read-heavy; cache arızasında graceful degradation); write-through yalnız "read-your-writes" zorunlu (bakiye/feature flag) dar alanda kullanılır; write yolu her zaman `delete` (set değil) → stale-set yarışı önlenir. **Namespace:** colon (`:`) delimiter + service/domain prefix + version alanı standarttır (redis.io); `user:1001:profile` tarzı hiyerarşi SCAN/pattern-match'ü ve bellek dökümünü mümkün kılar. **Stampede:** üç ana teknik — (1) single-flight mutex (tek süreç yeniden hesaplar), (2) probabilistic early expiration / XFetch (Vattani-Chierichetti-Lowenstein, VLDB 2015 — `delta · beta · -log(random)` ile TTL dolmadan olasılıkla yenileme), (3) TTL jitter + request coalescing; CDN/HTTP katmanında `stale-while-revalidate` aynı işi tarayıcıda yapar. **APCu:** PHP 8.4 user cache, paylaşımlı bellek; TTL=0 = kalıcı (elle silinmeli), `apc.ttl` yalnız TTL'siz entry'lerde opportunistik eviction, `apc.gc_ttl` GC listesi süresi — dolu cache'te (`Cache full count`) önce süresi geçenler atılır. **HTTP:** hash'li statik için `max-age=31536000, immutable`; HTML için `no-cache`/60-300s; API için `s-maxage + stale-while-revalidate + stale-if-error + ETag`. **TTL:** her veri tipine ayrı süre — statik uzun, dinamik 1-5 dk, session kısa; "tek TTL herkese" anti-patterntir. |
| Web Search **Uzun Açıklama** | **(a) Cache-aside vs write-through:** HLD Handbook — cache-aside varsayılan, uygulama miss-and-populate'i yönetir, DB authoritative, cache outage = performans düşüşü veri kaybı değil; write-through "read-your-writes ürün gerekliliğinde" (hesap bakiyesi, feature flag) senkron çift yazım maliyetiyle kullanılır; write-behind yalnız geri kurulabilir yüksek-hacimli yazım için (aksi halde veri yutar); write yolunda `delete` + `set` yarışı eşzamanlılıkta stale-set üretir → silme zorunlu. Redisson: write-through senkron DB round-trip'i her yola koyar, cold read deseninde write'a rağmen entry doldurur (bellek israfı); cache-aside yalnız istenen entry'yi tutar. Ensolvers: cache-aside starting point, write-through yalnız tutarlılık pazarlık kabul etmeyen yerde. c-sharpcorner/designgurus aynı ayrımı teyit eder (≥2 kaynak/karar ✅). **(b) Namespace:** redis.io blog — colon delimiter Redis namespace best practice'i; migration/silme/toplu taşımada key gruplarını tanımlanabilir kılar; service bazlı namespace "hangi uygulama ne kullanıyor" evrakını verir. redimo.dev kuralları: colon separator, lowercase, **namespace önce**, `entity:id:attribute:version` (version alanını şemamıza dayanak yapar); oneuptime (2026) tutarlı colon + type prefix yoksa `user_1001`/`user-1001` dağınıklığı ve kaza çarpmaları; dev.to prefix = multi-tenant/collision savunması; medium namespace prefix ile veri tipi ayrımı anında. **(c) Stampede:** antirez — popüler key_expire'da N eşzamanlı miss → DB'ye bin sorgu → kaskad; çözümler probabilistic early refresh, mutex locking, request coalescing. mojoauth (2026) — XFetch kanonik yöntem: `delta · beta · -log(random)`, olasılık TTL'e yaklaştıkça artar, yenileme senkron "cliff" olmadan tepe dışına kayar; lock tek doğru düzeltme ise bekleme/yük dağılımı. oneuptime (2026) — distributed lock (SET NX), probabilistic expiration, singleflight, background refresh kombinasyonu; vergecloud — dogpile/thundering herd/stampede aynı olay, 5 teknik (lock, coalescing, SWR, randomized TTL, warming); systemdr — en kötü stampede'ler en kritik key'lerde olur (50.000 RPS ana sayfa expire → 20ms içinde 1.000 istek). Jitter/erken yenileme TTL'ler **eşzamanlı expire**'ı (senkron dalga) kırar. **(d) APCu PHP 8.4:** php.net apcu.configuration (birincil) — `apc.ttl`: TTL'siz entry'ler erişilmezse bu sürede sayılır, **fırsatçı** (opportunistik) temizlik; explicit TTL'yi etkilemez; `apc.gc_ttl`: GC listesinde kalma süresi (0 = time-based cleanup kapalı); dolu cache eviction'da önce süresi geçenler atılır, sonra `apc.ttl`'e uyanlar, sonra en eskiler — `Cache full count` sayacı bunu görünür. krakjoe/apcu #196: TTL verilmezse veya 0 ise değer elle silinene/depolama temizlenene kadar kalır (kalıcı!). onlinephp.io apcu_entry aynı davranışı teyit eder. reintech/plumislandmedia — APCu PHP-FPM worker'ları arası paylaşımlı bellek (request-üstü sıcak yol için ideal). reliablepenguin (2025) — tuning + eviction tuzakları. **(e) HTTP headers:** MDN Cache-Control (birincil) — `stale-while-revalidate` bayat yanıtı yeniden doğrulama sırasında sunmaya izin verir (RFC 5861), `immutable` hash'li varlıkta revalidation'ı portlar, `must-revalidate`/`no-cache`/`no-store` ayrımı. web.dev — hash'li varlık `max-age=31536000`; HTML `no-cache`; ETag/Last-Modified tekrar doğrulama validator'ıdır. simonhearne — statik `max-age=604800, stale-while-revalidate=86400 + ETag`, HTML `max-age=300, private`; gereksiz validator başlığı yayımlamama. jonoalderson — API tipik profil `public, s-maxage=30, stale-while-revalidate=30, stale-if-error=300 + ETag`; "updates event-driven ise uzun TTL + tag purge" (invalidation kararımızla hizalı). blazingcdn (2026) — SWR ile origin revalidasyonu 10x düşürür; `Age`/`Cache-Status` gözlemi. greadme — statik 31.536.000 sn + immutable; HTML istisna 60-300s. **(f) TTL varsayılanları:** daily.dev — tek-TTL anti-pattern ("5 dk homepage için makul, stok için felaket"); segment: statik uzun, dinamik 1-5 dk, güvenlik-kritik 0; CDN yaygın 5 dk (300 sn). zonewatcher — düşük TTL 60-300 sn hızlı değişim için. debugbear — sn↔süre çizelgesi (60=1 dk, 300=5 dk, 3600=1 saat, 86400=1 gün, 31536000=1 yıl). momento — session token TTL örneği (en fazla oturum süresi). AWS CloudFront — `max-age + stale-while-revalidate + stale-if-error` üçlüsü origin koruma modeli. |
| Web Search **Paragraf Veri Uzun** | Cache-aside = read-heavy varsayılan (>90%), graceful degradation · write path = delete (set değil) → stale-set yarışı yok · write-through = read-your-writes zorunluluğunda (bakiye/flag) · write-behind = geri kurulabilir yazım, flush-interval veri kaybı penceresi · namespace = colon delimiter + service/domain prefix + `entity:id:attr:version` · namespace önce / lowercase / id sonra (redimo) · pattern-match `user:*` ile bellek dökümü · stampede = hot key expire → N miss → DB N kat · 3 teknik: single-flight mutex (SET NX), probabilistic early expiration XFetch `delta·beta·-log(random)` (VLDB 2015), request coalescing + jitter/background refresh · SWR = bayat sun + arka planda doğrula (RFC 5861) · APCu TTL=0/kalıcı → elle sil · `apc.ttl` opportunistik, explicit TTL'yi etkilemez · `apc.gc_ttl` GC listesi (0 = kapalı) · dolu cache: önce expire → apc.ttl → en eski · `Cache full count` sayacı · statik `max-age=31536000, immutable` · HTML `no-cache`/60-300s · API `s-maxage=30, swr=30, sie=300 + ETag` · tek-TTL anti-pattern; segment: statik uzun / dinamik 1-5 dk / kritik 0 · yaygın CDN TTL 300 sn · session = en fazla oturum süresi · 60=1dk, 300=5dk, 3600=1sa, 86400=1g, 31536000=1y. |
| Web Search **Sonucu** | 1) **Cache-aside kabul** — 2025-26 literatürü okuma-ağırlıklı varsayılan olarak cache-aside'i önerir, write-through dar senaryoya indirger (**kaynak 1, 2, 3, 4, 5**); write yolunda `delete` zorunlu (**kaynak 1**). 2) **Namespace şeması onaylandı** — colon delimiter + domain/service prefix + version alanı standarttır ve SCAN/bellek-dökümü/çarpışma önlemini sağlar (**kaynak 7, 8, 9, 10, 11, 12**) → `app:v{version}:{domain}:{key}` bu şablondur; 18 domain izolasyonu `domain` alanında (**ADR-003** ile uyum). 3) **Invalidation 3'lüsü literatürle hizalı** — TTL (expiration) her yerde (**kaynak 16, 17**), event-driven = "updates event-driven ise uzun TTL + purge" (**kaynak 14**) outbox'a (ADR-081) bağlanır, versiyon bump = stampede/deploy koruması (**kaynak 13, 15** — version alanı namespace şemasında zaten var, **kaynak 8**). 4) **Stampede mitigasyonu üçlüsü** — mutex (single-flight), probabilistic early expiration (XFetch), jitter/coalescing + SWR (**kaynak 5, 6, 13, 15**) → versiyon bump tek başına yeterli değil; hot key'lerde jitter + lock/düz yenileme gerekir. 5) **APCu TTL semantiği netleşti** — TTL=0 kalıcı (**kaynak 18, 19**), `apc.ttl` opportunistik (**kaynak 20**), eviction sırası dolu-cache'te (**kaynak 20, 21, 22**) → tüm cache entry'lerinde açık TTL zorunlu; bellek baskısı riski `Cache full count` ile izlenir. 6) **HTTP katmanı üç profil** — statik `immutable` 1 yıl, HTML `no-cache`/300s, API `s-maxage+swr+sie+ETag` (**kaynak 11, 12, 14, 16, 23, 24**) → §2.2e tablosu bu profillerle yazıldı. 7) **TTL tablosu** — tek-TTL anti-pattern; segment: statik uzun / dinamik 60-300s / config 300s / session oturum süresi / rate-limit 60s (vault ADR-013) (**kaynak 16, 17, 25, 26, 27**). |
| Web Search **Alınan Karar** | **ADR-007 kabul edilir — 3 katman + 1 şema + 1 tablo + 3'lü invalidation:** **(1) Katmanlı hiyerarşi:** L1 **APCu** (request-üstü/hot path — paylaşımlı bellek, sıcak veri; TTL'siz entry YASAK), L2 **dosya cache** (kalıcı/soğuk veri — `PageCacheAdapter`/`MemoryAdapter` zinciri), L3 **HTTP cache headers** (`Cache-Control`/`ETag`/`stale-while-revalidate` — tarayıcı/CDN katmanı). **Redis kullanılmıyor** (vault bulgusu: `shared/src/Cache/` glob'unda adapter yok, `.ai/ROLE.md` satır 624, `.ai/CLAUDE.md` satır 750 PLANNED notu); yalnızca **opsiyonel not**: çok-process paylaşımı senaryosunda distributed adapter eklenebilir (predis + `CacheInterface`) — bu ADR Redis'i zorunlu kılmaz (**§2.2a**). **(2) Namespace şeması:** `app:v{version}:{domain}:{key}` — colon delimiter (redis.io), `v{version}` deploy/validity alanı, `{domain}` 18 DB domain izolasyonu (ADR-003), `{key}` veri tipi önekiyle (`cfg:`, `sess:`, `feed:` vb.) (**§2.2b**). **(3) TTL varsayılan tablosu:** session 3600sn (ADR-011 düz metin) · rate-limit 60sn (ADR-013 düz metin) · config 300sn · hot-data 60sn · cold-data 3600sn · statik varlık (HTTP) 31536000sn immutable · HTML (HTTP) `no-cache`/300sn · API (HTTP) `s-maxage=30, swr=30, sie=300 + ETag` — her entry'de açık TTL zorunlu, TTL=0 yalnız bilinçli istisna (**§2.2c**). **(4) Invalidation 3'lüsü:** (i) **TTL her zaman** (başlangıç katmanı), (ii) **event-driven** — outbox event publish → cache drop (`[[ADR-081-multi-provider-data-sync]]` uyumu: yazım transaction'ında outbox, relay publish → dinleyici cache invalidation), (iii) **versiyon bump** — `v{version}` alanı değişince eski key'ler istek almaz, yeni versiyon tek seferde dolar → deploy yayılımında stampede koruması; hot key'lerde jitter + single-flight tamamlayıcıdır (**§2.2d**). |
| Web Search **Sonuç** | Karar 2026 verisiyle **desteklendi**: cache-aside/write-through (5 kaynak), namespace/naming (6 kaynak), stampede/XFetch (6 kaynak), APCu TTL (5 kaynak), HTTP headers (6 kaynak), TTL varsayılanları (5 kaynak) — **toplam ~35 birincil+ikincil URL, 6 sorgu grubu**; çapraz doğrulama ≥2 kaynak/tüm iddialarda karşılanır (php.net, MDN, web.dev, redis.io, RFC 5861 birinciller). Tek kaynaklı iddia yazılmadı; Redis durumu disk glob'u + 2 vault satırıyla çaprazlandı (kod lehine PLANNED/kullanılmıyor — §1.1). Vault'ta karşılığı olmayan TTL sayısal değerleri **öneri** olarak konumlandırıldı ve dayanaklarıyla yazıldı (§2.2c); `⚠️ VERIFICATION REQUIRED` yalnız Redis "IMPLEMENTED" eski satırı için korunur (düzeltme üst görevin işi — §5.1 adım 7). |

**Kaynak listesi (~35 birincil + ikincil):**
1. https://hld.handbook.academy/trade-offs/cache-strategies — cache-aside >90% read-heavy varsayılan, graceful degradation; write yolunda `delete`; write-through read-your-writes'a kısıt
2. https://redisson.pro/glossary/cache-aside.html — cache-aside vs write-through ayrımı, senkron çift yazım maliyeti
3. https://www.ensolvers.com/post/distributed-performance-cache-aside-vs-write-through-patterns — cache-aside starting point; write-through yalnız tutarlılık pazarlık kabul etmeyince
4. https://www.c-sharpcorner.com/article/redis-cache-patterns-explained-cache-aside-vs-read-through-vs-write-through-vs — desen karşılaştırma + "cache asla ücretsiz değildir" sahiplik kuralı
5. https://www.designgurus.io/answers/detail/explain-cache-aside-vs-read-through-vs-write-through-vs-write-back — cache-aside read-heavy / write-through strong consistency / write-back write-heavy
6. https://redis.io/blog/5-key-takeaways-for-developing-with-redis — colon delimiter = Redis namespace best practice; service bazlı namespace
7. https://www.redimo.dev/blog/posts/redis-key-naming-conventions — colon, lowercase, namespace önce, `entity:id:attribute:version`
8. https://oneuptime.com/blog/post/2026-01-25-redis-key-naming-conventions/view — tutarlı colon hiyerarşisi; tutarsız separator kuralı
9. https://dev.to/rijultp/redis-naming-conventions-every-developer-should-know-1ip — prefix = çarpışma önleme + kullanım deseni takibi
10. https://codemia.io/knowledge-hub/path/distributed_cache_redis_prefix_keys — prefix/namespace gruplama + bellek analizi
11. https://www.redisson.pro/ (benzer: medium nerd-for-tech naming) — namespace prefix ile veri tipi ayrımı
12. https://www.jonoalderson.com/performance/http-caching — API `s-maxage+swr+sie+ETag`; event-driven update → uzun TTL + tag purge
13. https://redis.antirez.com/fundamental/cache-stampede-prevention.html — stampede tanımı + probabilistic early refresh / mutex / coalescing
14. https://simonhearne.com/2022/caching-header-best-practices — statik `max-age=604800, swr=86400 + ETag`; HTML `max-age=300, private`
15. https://mojoauth.com/blog/thundering-herd-distributed-auth-caching — XFetch `delta·beta·-log(random)` (VLDB 2015), single-flight mutex
16. https://daily.dev/blog/cache-invalidation-vs-expiration-best-practices — tek-TTL anti-pattern; segment TTL (statik/dinamik/kritik); yaygın CDN 5 dk
17. https://zonewatcher.com/blog/2026-04-06-dns-ttl-explained — düşük TTL 60-300 sn hızlı değişim; TTL düşür→bekle→değiştir prosedürü
18. https://www.php.net/manual/en/apcu.configuration.php — `apc.ttl` opportunistik, explicit TTL'yi etkilemez; `apc.gc_ttl`; eviction sırası; `Cache full count`
19. https://github.com/krakjoe/apcu/issues/196 — TTL 0/yok = kalıcı (elle silinene kadar)
20. https://onlinephp.io/apcu-entry/manual — apcu_entry TTL davranışı (0 = kalıcı) teyidi
21. https://reintech.io/blog/guide-php-apcu-library-caching-performance-optimization — APCu paylaşımlı bellek user cache (PHP-FPM arası)
22. https://www.plumislandmedia.net/programming/php/apcu-in-php-some-notes — APCu worker'lar arası paylaşımlı kalıcılık
23. https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control — SWR/must-revalidate/no-cache/no-store/immutable (birincil)
24. https://web.dev/articles/http-cache — hash'li `max-age=31536000`; HTML `no-cache`; ETag validator
25. https://www.greadme.com/blog/performance/what-is-uses-long-cache-ttl-complete-guide — statik 31.536.000 + immutable; HTML 60-300s istisna
26. https://www.debugbear.com/docs/stale-while-revalidate — sn↔süre çizelgesi (60/300/3600/86400/31536000)
27. https://docs.momentohq.com/cache/learn/courses/cache-concepts/time-to-live — session token TTL = en fazla oturum süresi
28. https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/Expiration.html — `max-age + stale-while-revalidate + stale-if-error` origin koruma (birincil, AWS)
29. https://blog.blazingcdn.com/en-us/cache-control-stale-while-revalidate-cdn-caching — SWR 2026 CDN ölçümü (10x origin düşüşü), RFC 5861
30. https://www.vergecloud.com/blog/what-is-cache-stampede — dogpile/herd/stampede aynı olay; 5 önleme tekniği
31. https://oneuptime.com/blog/post/2026-01-21-redis-cache-stampede/view — lock + XFetch + singleflight + background refresh kombinasyonu
32. https://www.coddykit.com/courses/caching/defending-against-the-thundering-herd-8186790 — jittered TTL + erken yenileme + coalescing
33. https://systemdr.systemdrd.com/p/the-thundering-herd-problem-mitigation — en kötü stampede en kritik key'lerde (50K RPS → 20ms'de 1000 istek)
34. https://blogs.reliablepenguin.com/2025/11/29/using-php-apcu-on-plesk-installation-tuning-and-pitfalls — APCu tuning/eviction tuzakları (2025)
35. https://www.drupal.org/project/drupal/issues/3586418 + https://stackoverflow.com/questions/34725669/apcu-configuration-gc-ttl-0 — apc.ttl/gc_ttl davranış notları (çapraz)

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Her iddia ≥2 kaynak | ADR-005 §2.2c standardı: sayısal her dış iddia ≥2 bağımsız kaynak taşır; tek kaynak → `⚠️ VERIFICATION REQUIRED` (§1.3 çapraz doğrulama 6 grupta tam). |
| Frozen ADR dokunulmaz | ADR-001…037 metinleri okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2); bu ADR frozen değildir (status: accepted, frozen YOK). |
| log.md append-only | Bu ADR kaydı append ile yazılır; geçmiş satıra dokunulmaz (AGENTS.md §25.3 kural 3). |
| Dosya adı değişmez | In-Place Refactoring: `ADR-007-cache-namespace.md` adı onaysız değiştirilemez (In-Place Refactoring kuralı 1). |
| REDACTED | Secret/credential hiçbir koşulda ADR'ye/cache entry'ye yazılmaz; cache value'su PII içerirse encrypted/değer-maskele (REDACTED politikası). |
| Rate-limit cache ayrımı | APCu rate-limiter ADR-013'ün tekelindedir (60 req/60s); bu ADR **veri cache**'ini düzenler — `.ai/.agents/data-engineer.md` satır 122'deki "veri cache ≠ rate-limit cache" ayrımı korunur; ADR-013 dosyası diskte YOK → düz metin. |
| ADR-003 domain izolasyonu | Namespace `{domain}` alanı 18 BCNF DB domain'iyle aynı adlandırma kriterini paylaşır (ADR-003 slug `multi-db-bcnf`, dosya diskte VAR → wiki-link). |
| Redis kullanılmıyor | `shared/src/Cache/` glob'unda Redis adapter yok; `.ai/CLAUDE.md` satır 335 "IMPLEMENTED" iddiası satır 750 + `.ai/ROLE.md` satır 624 ile düzeltilir (kod lehine) — bu ADR Redis'e bağımlı yazılamaz. |

---

## 2. Karar (Decision)

**CoreMusic, katmanlı cache hiyerarşisini (L1 APCu → L2 dosya cache → L3 HTTP cache headers), `app:v{version}:{domain}:{key}` namespace şemasını, veri tipi bazlı TTL varsayılan tablosunu ve 3'lü invalidation stratejisini (TTL + event-driven outbox + versiyon bump) ADR-007 olarak tescil eder.** Cache-aside (lazy loading) okuma modeli varsayılandır; write yolunda `delete` zorunludur. **Redis kullanılmıyor** — çok-process paylaşımı senaryosu için opsiyonel not olarak kalır; bu karar Redis'i zorunlu kılmaz. L3 HTTP başlıkları statik/HTML/API için üç profile ayrılır; her APCu entry'sinde açık TTL zorunludur (TTL=0 yalnız bilinçli istisna).

### 2.1 Neden Bu Seçenek?

1. **Okuma-ağırlıklı iş için varsayılan kanıtlanmış:** literatür cache-aside'i >%90 read-heavy varsayılan, cache arızasında graceful degradation olarak önerir; write-through dar senaryoya indirgenir (§1.3 kaynak 1-5) → CoreMusic feed/medya okuma ağırlıklı, cache-aside doğru başlangıç.
2. **Namespace domain izolasyonunu çözer:** 18 domain mimarisi (ADR-003) çarpışma/ölçüm/bellek dökümü ister; colon + domain + version şeması literatür standartıdır (§1.3 kaynak 6-10) → `app:v{version}:{domain}:{key}` üçünü de tek şemada verir.
3. **Invalidation 3'lüsü tek başına hiçbirinin yapamadığını yapar:** TTL bayat penceresini üstten kırpıştırır; event-driven (outbox — ADR-081) yazım anında tazeler; versiyon bump deploy anını temiz keser → üçü birlikte hem gündelik hem yayılım hem arıza senaryosunu kapsar (§1.3 kaynak 12, 13, 15).
4. **Stampede savunması versiyon alanına gömülüdür:** `v{version}` değişimi eski key'lere isteği keser, yeni key tek seferde dolar; hot key'de jitter + single-flight (§1.3 kaynak 13, 15, 30-33) tamamlayıcıdır.
5. **APCu TTL disiplini bellek baskısını yönetir:** TTL=0 kalıcıdır (§1.3 kaynak 19, 20), dolu cache eviction ile yaşar (§1.3 kaynak 18) → açık TTL + `Cache full count` izlemesi riski sahiplenir; mevcut `ApcuAdapter`/`CacheManager` zinciri bu disipline hazırdır (disk kanıtı §1.1).
6. **Redis'siz yazılır — vault doğru yönlendirir:** kodda adapter yok, ROLE.md düzeltmesi mevcut → karar mevcut duruma (kullanılan = APCu + dosya + HTTP) göre yazılır; Redis opsiyonel notu gelecek seçeneği kapatmaz (çok-process paylaşımı).

### 2.2 Teknik Detaylar

**(a) Katmanlı cache hiyerarşisi:**

| Katman | Teknoloji | Ne saklar | TTL/disiplin | Vault/Kod kanıtı |
|--------|-----------|-----------|--------------|------------------|
| **L1 — Request-üstü / hot path** | **APCu** (`ApcuAdapter`, `ext-apcu`) | Sıcak veri: sık okunan domain verisi, computed sonuç, hot key | **Açık TTL zorunlu** (§2.2c); TTL=0 yalnız bilinçli istisna + `log` notu | `shared/src/Cache/ApcuAdapter.php` · `ext-apcu` IMPLEMENTED (backend-architect satır 137) |
| **L2 — Kalıcı / soğuk veri** | **Dosya cache** (`PageCacheAdapter`/`MemoryAdapter` zinciri) | Soğuk veri: az okunan kalıcı çıktı, sayfa/rapor önbelleği | Uzun TTL (§2.2c cold-data) + event invalidation | `shared/src/Cache/{PageCacheAdapter,MemoryAdapter,CacheManager}.php` |
| **L3 — Tarayıcı / CDN** | **HTTP cache headers** (`Cache-Control`, `ETag`, `stale-while-revalidate`) | Statik varlık, HTML, API yanıtı | §2.2e üç profil | Standart yazılmamış (§1.1) → bu kararla yazılır |
| *(opsiyonel not)* | **Redis** — çok-process paylaşımı senaryosunda distributed adapter (`CacheInterface` → predis) | Paylaşımlı hot key (çok worker/çok düğüm) | Aynı şema + TTL | **Kullanılmıyor** — `shared/src/Cache/` glob'unda adapter YOK; `.ai/CLAUDE.md` L335 vs L750 + `.ai/ROLE.md` L624 (kod lehine PLANNED) |

*Akış (cache-aside):* oku → L1 miss → L2 miss → L3/DB → **value döner + L1/L2'ye yaz** (TTL ile). Yaz → DB → **cache'den `delete`** (set DEĞİL — §1.3 kaynak 1 stale-set yarışı). L3 yalnız statik/HTML/API response'una header ile uygulanır.

**(b) Namespace şeması — `app:v{version}:{domain}:{key}`:**

| Alan | Anlam | Örnek | Dayanak |
|------|-------|-------|---------|
| `app` | Uygulama öneki (CoreMusic tek uygulama; ileride multi-app izolasyonu) | `app:` | §1.3 kaynak 6-10 (service prefix) |
| `v{version}` | **Cache versiyonu** — deploy/schema değişiminde bump | `v12:` | §1.3 kaynak 7 (`entity:id:attr:version`) |
| `domain` | 18 DB domain adı (ADR-003 ile aynı adlandırma) | `music:`, `user:`, `order:` | ADR-003-multi-db-bcnf (wiki-link) |
| `key` | Veri tipi öneki + ayırt edici id | `cfg:feed:main`, `sess:8f3a…` | §1.3 kaynak 8-10 (type prefix) |

Örnek: `app:v12:music:cfg:feed:main` · `app:v12:user:sess:8f3a91c2` · `app:v12:media:track:meta:1042`.
**Kurallar:** colon delimiter zorunlu; lowercase; SCAN/pattern silme `app:v{N}:{domain}:*` ile domain bazlı toplu invalidation; version bump = tüm domain'lerde yeni alan (`v12` → `v13`), eski alan istek almaz ve TTL içinde kendiliğinden söner (stale-data riski §4.3 risk 2).

**(c) TTL varsayılan tablosu (veri tipi bazlı — her entry'de açık TTL zorunlu):**

| Veri tipi | Katman | Varsayılan TTL | Dayanak |
|-----------|--------|----------------|---------|
| Session / oturum verisi | L1 APCu | **3600 sn** (1 saat) | Vault: ADR-011 (COREMUSIC_SESS 3600s — `.ai/.templates/backend/php-template.md` satır 545, `.ai/brain.md`); Momento session TTL = oturum süresi (§1.3 kaynak 27) |
| Rate-limit sayacı | L1 APCu | **60 sn** | Vault: ADR-013 (60 req/60s — `.ai/CLAUDE.md` satır 193/330; dosya diskte YOK → düz metin) |
| Config / yapılandırma | L1 APCu | **300 sn** (5 dk) | §1.3 kaynak 16 (yaygın CDN 5 dk), 17 (düşük TTL 60-300 sn) |
| Hot-data (sık okunan feed/indeks) | L1 APCu | **60 sn** | §1.3 kaynak 16 (dinamik 1-5 dk), 17 |
| Cold-data (soğuk kalıcı çıktı) | L2 Dosya | **3600 sn** + event invalidation | §1.3 kaynak 16 (az değişen daha uzun), ADR-081 outbox |
| Statik varlık (hash'li JS/CSS/img) | L3 HTTP | **31536000 sn (1 yıl) `immutable`** | §1.3 kaynak 24, 25 (web.dev/greadme) |
| HTML dokümanı | L3 HTTP | **`no-cache`** veya `max-age=300` | §1.3 kaynak 24, 14 |
| API yanıtı | L3 HTTP | **`s-maxage=30, swr=30, sie=300` + ETag** | §1.3 kaynak 12, 28 (jonoalderson/AWS) |

*Not:* Süreler **öneridir** (dayanaklı — §1.3); revizyon §5.1 adım 6 (Tech Lead ilk ölçümde onaylar). TTL=0 yalnız: anlık sayaç (rate-limit — ADR-013'ün tekeli) veya bilinçli kalıcı entry (bilgi notu + temizlik sahibi ile).

**(d) Invalidation stratejisi — 3'lü (hepsi birlikte aktif):**

| # | Mekanizma | Ne zaman devrede | Akış | Dayanak |
|---|-----------|------------------|------|---------|
| 1 | **TTL (expiration)** | Her zaman — başlangıç katmanı | Entry açık TTL ile yazılır; süre dolunca söner → bayat pencere = TTL | §1.3 kaynak 16, 18 |
| 2 | **Event-driven invalidation** | Yazım anında (veri değişince) | Domain yazımı → **outbox event** (ADR-081: aynı transaction'da outbox satırı) → relay publish → dinleyici `delete(app:v{N}:{domain}:…)` → event sonrası bayat pencere ~0 | [[ADR-081-multi-provider-data-sync]] + §1.3 kaynak 12 (event-driven update → purge) |
| 3 | **Versiyon bump (`v{version}`)** | Deploy / schema / global kural değişiminde | `v12` → `v13`: eski alan `app:v12:*` istek almaz (router yeni versiyonu okur), yeni alan tek seferde dolar; **stampede koruması**: yayılım anında eski key'lere istek gitmez → N kat yeniden hesap yok | §1.3 kaynak 7 (version alanı), 13, 15 (stampede/deploy) |

*Tamamlayıcılar (hot key):* **TTL jitter** (±%10-20 rastgele ofset → senkron expire kırılır) + **single-flight/mutex** (eşzamanlı miss'te tek süreç yeniden hesaplar) + L3'te `stale-while-revalidate` (§1.3 kaynak 13, 15, 23, 30-33). Cache-aside yazım yolu: **`delete`** (set değil — §1.3 kaynak 1).

**(e) L3 HTTP başlık profilleri:**

| Kaynak tipi | Cache-Control | Validator | Örnek TTL |
|-------------|--------------|-----------|-----------|
| Statik hash'li varlık | `public, max-age=31536000, immutable` | gerekmez (hash = validator) | 1 yıl (§1.3 24, 25) |
| HTML | `no-cache` (veya `max-age=300, private`) | ETag opsiyonel | 0-300 sn (§1.3 14, 24) |
| API yanıtı | `public, s-maxage=30, stale-while-revalidate=30, stale-if-error=300` | **ETag zorunlu** | 30 sn s / 30 sn stale / 300 sn hata (§1.3 12, 28) |
| Kişiselleştirilmiş | `private, no-store` | — | cache yok (§1.3 14; REDACTED/PII) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız APCu (tek katman)** | En düşük gecikme, sıfır ek altyapı | Soğuk/kalıcı veri APCu'ye sığmaz (bellek pahalı, eviction §4.3 risk 3); tarayıcı/CDN avantajı hiç kullanılmaz → her istek origin'e | Okuma-ağırlıklı sitede L2/L3 ücretsiz yük azaltma sağlar (§1.3 kaynak 12, 24); tek katman TTFB/p95 hedeflerini (ADR-006) yalnız sunucu tarafında kucaklar |
| 2 | **Yalnız Redis (merkezi distributed cache)** | Çok-process/çok-düğüm paylaşımı, güçlü veri yapıları | `shared/src/Cache/` glob'unda adapter YOK (§1.1); ek altyapı + predis bağımlılığı; tek düğüm CoreMusic'te L1 APCu zaten aynı işi paylaşır | Karar mevcut durumla yazılır (kullanılan = APCu+dosya+HTTP); Redis **opsiyonel not** olarak çok-process senaryosuna bırakılır (§2.2a) — red değil,erteleme |
| 3 | **Write-through (cache-aside yerine)** | Read-your-writes, senkron tutarlılık | Her yola senkron DB round-trip; cold read deseninde bellek israfı; yazım gecikmesi | Literatür yalnız tutarlılık zorunlu dar alanda önerir (bakiye/flag) — feed/medya okuma için fazla maliyet (§1.3 kaynak 1, 2, 3); yazım yolu delete + outbox ile zorunlu tazelik sağlanır |
| 4 | **Namespace'siz düz key'ler (mevcut durum)** | Kısa key, şema yok | Domain çarpışması, toplu silme/pattern-match imkânsız, bellek dökümü ölçülemez, deploy'da eski key temizliği elle | 18 domain mimarisinde çarpışma/ölçüm kaçınılmaz (ADR-003); literatür colon+prefix'i standart kılar (§1.3 kaynak 6-10); versiyon bump stampede savunması namespace'siz imkânsız |
| 5 | **Yalnız TTL invalidation (event/versiyonsuz)** | En basit, tek mekanizma | Event sonrası bayat pencere (TTL'e kadar); deploy'da eski key'lere istek; hot key stampede | 3'lü invalidation bunların üçünü de kapatır (§2.2d); outbox zaten ADR-081'de kararlı — olayı çöpe atmak alternatifle çelişir |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek şema, üç katman:** `app:v{version}:{domain}:{key}` + L1/L2/L3 — çarpışma, toplu silme, ölçüme ve deploy temizliği tek yapıda çözülür (§2.2a-b).
- **Bayat pencere katmanlı daralır:** TTL üstten kırpar, outbox event yazım anında tazeler (ADR-081), versiyon bump deploy anını keser — üç senaryo da kapsanır (§2.2d).
- **Stampede savunması gömülü:** `v{version}` alanı yayılımda N kat yeniden hesabı önler; jitter + single-flight hot key'yi korur (§1.3 13, 15).
- **Bellek disiplini:** açık TTL zorunluluğu + `Cache full count` izlemesi APCu eviction'ını öngörülebilir yapar (§1.3 18).
- **HTTP katmanı ADR-006'ya hizmet eder:** `immutable`/SWR/ETag profilleri TTFB ve byte yükünü (ADR-006 §2.2b-e) doğrudan düşürür.
- **Redis belirsizliği kapanır:** vault çelişkisi (L335 vs L750) kod lehine yazılır; gelecekteki distributed adapter opsiyonel yolla saklı kalır.

### 4.2 Olumsuz Sonuçlar

- **Üç katman bakım maliyeti:** L1/L2 adapter davranışı + L3 başlık profilleri test edilmeli (CacheManager genişlemesi kod işi — kapsam dışı §1.1).
- **Namespace uzunluğu:** key başına ek bayt (`app:v12:music:` öneki) — APCu'de önemsiz ama ölçülmeli; şişme riski §4.3 risk 2.
- **Event-driven bağımlılık:** invalidation olayının hızı outbox relay gecikmesine bağlı (ADR-081 lag bütçesi kritik ≤60 sn'ye kadar bayat pencere bırakabilir).
- **TTL revizyonu olasılığı:** §2.2c süreleri öneridir; ilk ölçümden sonra Tech Lead revizyonu gerekebilir (§5.1 adım 6).
- **Redis opsiyonunun pasif kalması:** çok-process senaryosunda eklenecek adapter + test eforu şimdiden ödenmez → ihtiyaç anında yazım + ölçüm gerekir.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Stale data** (event gecikmesi/TTL penceresi → kullanıcı eski veri görür) | 3 (olası) | 3 (orta) | 3'lü invalidation (§2.2d): kritik yazım outbox event ile anında drop (ADR-081 lag bütçesi ≤60 sn); TTL üst sınır; hassas ekranlarda `no-store` + bypass-okuma; okuma yolu stale ise stale-while-revalidate yalnız L3'te (§2.2e) |
| **Namespace şişmesi** (eski `v{N}` alanları + atıl key'ler belleği yer) | 3 (olası) | 2 (düşük) | Version bump sonrası `app:v{eski}:*` pattern-silme (colon prefix SCAN — §1.3 6-8); TTL her key'de zorunlu (§2.2c); `Cache full count`/bellek dökümü domain bazlı rapor (namespace = domain ölçümü) |
| **APCu bellek baskısı → eviction** (dolu cache'te sıcak key atılır → miss dalgası) | 3 (olası) | 3 (orta) | Açık TTL (fırsatçı temizlik çalışır — §1.3 18); `apc.ttl`/`apc.gc_ttl` yapılandırması; `Cache full count` alarmı; hot key'de L2 dosya fallback'i (miss'te DB'ye değil L2'ye iner) + jitter/single-flight (§1.3 13, 15) |
| **Stampede (deploy/eski expire)** | 2 (mümkün) | 4 (yüksek) | Versiyon bump (§2.2d #3) + TTL jitter + single-flight + L3 SWR (§1.3 13, 15, 23, 30-33); yayılım öncesi warm-up |
| **Redis yanılgısı** (birisi vault L335'e güvenip adapter'sız Redis'e kod yazar) | 2 (mümkün) | 3 (orta) | §2.2a opsiyonel not + §1.1 çelişki kaydı (kod lehine); vault L335 düzeltmesi §5.1 adım 7'de (`⚠️ VERIFICATION REQUIRED` işaretli) |

### 4.4 Fallback (Zaruri İstisna Kapısı)

| # | Koşul | Onay |
|---|-------|------|
| 1 | APCu bellek kalıcı olarak yetersizse (ör. `Cache full count` sürekli artıyor, sıcak key eviction'ı gözleniyor) → sıcak veri **L2 dosya cache'e** indirgenir; TTL'ler DEĞİŞMEZ, katman önceliği değişir | Tech Lead |
| 2 | Outbox relay gecikmesi lag bütçesini (ADR-081: kritik ≤60 sn) aşarsa → kritik domain'lerde **event + kısa TTL (≤60 sn)** ikilisi zorunlu geçici kural olarak §2.2d'ye not düşülür; 3'lü yapının kendisi değişmez | Vault Steward + Data Engineer |
| 3 | Çok-process paylaşımı ihtiyacı doğarsa (çok düğüm/worker) → Redis adapter `CacheInterface` üzerinden eklenir (predis); **aynı namespace + TTL + invalidation şeması korunur** — bu ADR değişmez, §5.1 adım 8 akışı işletilir | Tech Lead |
| 4 | İlk ölçümde TTL önerisi gerçekçi değilse (ör. config 300 sn çok sık/dönük) → süre Tech Lead onayıyla revize edilir, kapı kalkmaz; kalıcı şema değişikliği (`app:v{version}:{domain}:{key}` yapısı) = yeni ADR | Tech Lead |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-007-cache-namespace`) + `log.md` append ("ADR-007 yazıldı (debate PENDING)") + dizin satırı doğrula (`[[../index]]` §3 satırı `ADR-007-cache-namespace` slug eşleşmesi ✅) | Vault Steward | 30 dk |
| 2 | Debate (3 tur / 20 persona, ADR-004/ADR-006 formatı) — **✅ TAMAMLANDI (18/2/0 KABUL)**; sonuç §7.1'e yazıldı | Debate + Vault Steward | 2 gün |
| 3 | Tech Lead onayı — **✅** (debate KABUL üzerine, 2026-09-24) | Tech Lead | 1 gün |
| 4 | **CacheManager genişletme (kod):** namespace builder (`app:v{version}:{domain}:{key}`) + TTL registry (§2.2c tablosu) + `delete`-yazım yolu + domain pattern-silme; `CacheInterface`'e dokunmadan adapter'lar üzerinde | Backend Architect | 2 gün |
| 5 | **L3 HTTP başlıkları:** `.htaccess`/PageRouter response filter'a §2.2e üç profil + ETag validator'ı | Backend Architect + DevOps Engineer | 1 gün |
| 6 | **TTL gerçekçilik ölçümü:** ilk production'da hit/miss + bayat şikâyeti + `Cache full count` ölçülür; §2.2c süreleri gerekirse §4.4 fallback 4 ile revize edilir | Tech Lead + QA Engineer | 1 hafta |
| 7 | **Vault düzeltmesi:** `.ai/CLAUDE.md` satır 335 `Cache \| Redis \| IMPLEMENTED` → PLANNED/kullanılmıyor düzeltmesi (satır 750 kalemi ile hizala; şu an `⚠️ VERIFICATION REQUIRED` — üst görevin işi) + `brain.md` cache satırı (`symfony/cache` notu) vault-sync ile tazelenir | MO (vault-updater) | 1 saat |
| 8 | *(opsiyonel, §4.4 fallback 3)* Çok-process senaryosunda Redis adapter (`CacheInterface` → predis) — **şu an değil**; ihtiyaç anında | Backend Architect | 1 hafta (ihtiyaçta) |
| 9 | `[[../../brain]]` özeti + `.ai/.decisions/index.md` §3 satır durumu vault-sync ile tazelenir (MO/vault-updater) | MO (vault-updater) | 1 saat |

### 5.2 Geri Dönüş Planı

Karar süreç karardır; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) **Namespace şeması değişirse** (ör. `app:` yerine multi-app `tenant:`): yeni ADR — şema değişimi TTL içinde eski alanın kendiliğinden sönmesiyle yapılır, çift-yazım/dual-read yok; (2) **Invalidation 3'lüsü basitleştirilirse** (yalnız TTL'e düşürülürse): yeni ADR şart + `log.md` ERROR — outbox drop kalkarsa bayat pencere açılır (ADR-081 ile çelişir); (3) **Redis adapter eklenirse:** §4.4 fallback 3 akışı — namespace/TTL/invalidation aynen taşınır, bu ADR `note added` ile güncellenir (revizyon madresiyle, metin silinmeden) + `log.md` append; (4) **TTL süreleri revize edilirse:** §4.4 fallback 4 (Tech Lead geçici onayı, §2.2c tablosuna revizyon satırı); kalıcı = yeni ADR; (5) **HTTP profilleri CDN/hosting ile uyumsuz çıkarsa:** L3 profili `warn`'e alınır (§4.4 kapısı), L1/L2 değişmez; (6) **Veri/state kaybı yoktur** — bu ADR şema/karar katmanıdır, cache entry'leri TTL ile kendiliğinden döner; vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10).

### 5.3 Debate Şartları (3 bağlayıcı şart — 2026-09-24 KABUL)

| # | Şart | Uygulama Maddesi | Durum |
|---|------|------------------|-------|
| 1 | `.ai/CLAUDE.md` satır 335 `Cache \| Redis \| IMPLEMENTED` düzeltmesi — Redis adapter kodda YOK (`shared/src/Cache/` = ApcuAdapter + MemoryAdapter + PageCacheAdapter) → satır kod lehine: APCu IMPLEMENTED / Redis PLANNED (L750 + ROLE.md L624 hizası; satır formatı korunarak tek-satır edit) | §5.1 adım 7 · §6 `[[../../CLAUDE.md]]` satırı | ✅ Uygulandı (2026-09-24) |
| 2 | APCu eviction alarmı + TTL üst sınırı fallback'i — DevOps dashboard + `log.md` eşik maddesi (Tur 2-1); event invalidation kaçarsa stale data'ya karşı TTL üst sınırı son savunma (Tur 2-2) | §4.3 risk 3 (eviction satırı `Cache full count` alarmı) + §4.4 fallback 1-2 + §2.2c TTL zorunluluğu | ✅ Metne bağlı (§4.3/§4.4/§2.2c) |
| 3 | Rate-limit key namespace standarda bağlanır: `app:v{ver}:ratelimit:{client}` (Tur 2-4 — Security itirazı) | §2.2b namespace şeması + §2.2c rate-limit 60sn satırı (ADR-013 düz metin) | ✅ Metne bağlı (§2.2b/c) |

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-006-performance-targets]] | TTFB/p95/p99 + byte bütçesi hedefleri — cache katmanları bu eşiklere hizmet eder (L3 `immutable`/SWR byte+TTFB düşürür); **dosya diskte VAR** ✅ |
| [[ADR-003-multi-db-bcnf]] | Domain kavramı — namespace `{domain}` alanı 18 DB domain adlandırmasıyla uyumlu; **dosya diskte VAR** ✅ |
| [[ADR-081-multi-provider-data-sync]] | Outbox event'leri — event-driven invalidation'ın olay kaynağı (yazım transaction'ında outbox → publish → cache drop); **dosya diskte VAR** ✅ |
| [[../index]] | Karar dizini — bu ADR'nin kaydı §3 `[[ADR-007-cache-namespace]]` (slug eşleşmesi ✅ — satır diskte mevcut) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme — 16 Hard Guardrail (#16 şablon zorunluluğu); satır 335 `Cache\|Redis\|IMPLEMENTED` vs satır 750 PLANNED düzeltme kalemi (§1.1 çelişki kaydı); satır 193/330 rate-limit 60 req/60s (TTL tablosu dayanağı) |
| [[../../AGENTS.md]] | §24.1 Ultrathink protokolü (bu ADR §1.3'ü bu protokolle üretti), §25.3 frozen/append-only kuralları, §16 quality standards |
| [[../../brain]] | `symfony/cache` (PSR-6) + `predis/predis` bağımlılık kayıtları (satır 80-81) — hedef stack; Redis adapter kodda yok |
| [[../../log]] | Audit trail — bu ADR ve revizyonlar append edilir (append-only) |
| [[../../index]] | Master katalog — decisions bölümü |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| `shared/src/Cache/CacheManager.php` · `ApcuAdapter.php` · `MemoryAdapter.php` · `PageCacheAdapter.php` | Uygulayıcı kod zinciri — glob kanıtı: **Redis adapter YOK** (§1.1, §2.2a); L1/L2 adapter'ları bu ADR'nin taşıyıcısı |
| `.ai/.agents/data-engineer.md` | "veri cache ≠ rate-limit cache" ayrımı (satır 122) — bu ADR'nin kapsam sınırı |
| Debate şartı 1 (§5.3) | `[[../../CLAUDE.md]]` satır 335 Redis→PLANNED düzeltmesi — 2026-09-24 uygulandı; L750 + `.ai/ROLE.md` L624 ile hizalı |
| Debate şartı 2 (§5.3) | APCu eviction alarm (`Cache full count` — §4.3 risk 3) + TTL üst sınırı fallback (§4.4 #1/#2) — DevOps dashboard + log.md eşik maddesi |
| Debate şartı 3 (§5.3) | Rate-limit key namespace `app:v{ver}:ratelimit:{client}` — §2.2b şema + §2.2c rate-limit satırına bağlandı (ADR-013 düz metin) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅ — 2+ çapraz kaynak kuralı) |
| **Düz metin (dosya diskte YOK — wiki-link kurulmaz):** ADR-013 (rate limiting APCu, 60 req/60s) · ADR-011 (COREMUSIC_SESS 3600s) | `[[../index]]` §3'te kayıtlı ama tekil dosyaları diskte YOK (glob kanıtı) → düz metin referans; TTL tablosu dayanakları |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Bayram Ali (debate KABUL üzerine onay) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/ADR-006 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur 1 | 20 persona — **15 kabul/neutral, 3 uyarı, 1 Critic düzeltme talebi**: DevOps (APCu eviction/OOM alarmı), QA (stale data testi), Security (rate-limit key namespace'i); Critic — `.ai/CLAUDE.md` L335 "Redis IMPLEMENTED" satırı kod lehine düzeltilmeden KABUL verilmez |
| Tur 2 (İtiraz→çözüm) | **4 itiraz→çözüm:** (1) APCu eviction alarmı → DevOps dashboard + `log.md` eşik maddesi (şart 2); (2) Event invalidation kaçarsa stale data → TTL üst sınırı = son savunma maddesi (şart 2); (3) Redis hedefi (`brain.md` symfony/cache+predis) → mevcut APCu ile devam; Redis geçiş **ayrı karar**, sıçrama yok; (4) Rate-limit key → `app:v{ver}:ratelimit:{client}` standarda bağlanır (şart 3) |
| Tur 3 (Oy) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Şartlar | **3 şart** (§5.3'e yazıldı, §6'ya bağlandı): (1) CLAUDE.md L335 Redis düzeltmesi ✅ uygulandı; (2) APCu eviction alarm + TTL üst sınırı fallback; (3) rate-limit key namespace standarda bağlanır |
| Tech Lead | **✅** (debate KABUL üzerine onaylandı — 2026-09-24) |
| Sonuç | **✅ KABUL** — ADR-007 debate 3/20 tamamlandı (18/2/0); 3 şart bağlayıcı |

---

*ADR-007 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/accepted/ yeni seri; slug: `cache-namespace`)*
*Authority: ADR-007 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
