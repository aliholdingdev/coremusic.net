---
node_id: "K007-cache-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories"
evidence:
  - "shared/src/Cache/ — 6 PHP dosyası (2026-10-15 disk enumerate)"
  - "shared/src/Cache/CLAUDE.md §2.1 — dosya envanteri + ADR-007"
  - "composer.json:9 — ext-apcu zorunlu bağımlılık"
validation: "2026-10-15 — 6 bileşen diskte; envanter CLAUDE.md ile birebir"
open_questions:
  - "CacheItem/CachePool (PSR-6) gerçek kullanımı repo'da görünmüyor → VERIFICATION REQUIRED"
group: mimari
---

# cache — Bileşenler (K007)

| # | Dosya | Rol | Kanıt | Durum |
|---|-------|-----|-------|-------|
| 1 | `CacheInterface.php` | sözleşme | dosya var | ✅ |
| 2 | `CacheManager.php` | adapter seçimi + singleton | :12-16 | ✅ DOĞRULANDI |
| 3 | `ApcuAdapter.php` | L1 — APCu | composer ext-apcu | ✅ |
| 4 | `MemoryAdapter.php` | fallback (APCu yoksa) | CacheManager else dalı | ✅ |
| 5 | `PageCacheAdapter.php` | L3 — sayfa cache `page_pub_/page_auth_` TTL 600 | :11,30-31 | ✅ |
| 6 | `PageCacheInterface.php` | sayfa cache sözleşmesi | dosya var | ✅ |

> `CLAUDE.md §2.1` ApcuCacheAdapter/MemoryCacheAdapter/CacheItem/CachePool adlarını verir;
> diskteki gerçek adlar `ApcuAdapter`/`MemoryAdapter`'dir ve CacheItem/CachePool **diskte YOK**.
> Bu tutarsızlık CLAUDE.md'in eski kalıntısıdır — disk kazanır (2026-10-15).

## §1 Katman matrisi (CLAUDE.md §3 ↔ repo)

| Katman | Adapter | Kullanım (plan) | Repo kanıtı |
|--------|---------|-----------------|-------------|
| L1 Hot | APCu | session, rate limit, config | ApcuAdapter ✅ |
| L2 Warm | Memory | query result, API response | MemoryAdapter ✅ (fallback) |
| L3 Cool | PageCache | full-page render | PageCacheAdapter ✅ |

---

# EK B — Dosya Bazlı Derin Analiz (B-01..B-06)

## B-01 `CacheInterface.php` — 6 metotluk sözleşme

```php
interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 0): bool;
    public function delete(string $key): bool;
    public function increment(string $key, int $step = 1): int|false;
    public function clear(): bool;
    public function has(string $key): bool;
}
```

| # | Metot | Dönüş | Kusur |
|---|---|---|---|
| 1 | `get` | `mixed` | `false` = "yok" = "değer false" belirsiz (`cache-kapsam` §C.4) |
| 2 | `set` | `bool` | `false` = taşma/kapalı — **çağıran kontrol etmiyor** |
| 3 | `delete` | `bool` | — |
| 4 | `increment` | `int\|false` | `MemoryAdapter` yalnız `int` değerlerde çalışır |
| 5 | `clear` | `bool` | APCu'da **tüm** önbelleği siler (namespace yok) |
| 6 | `has` | `bool` | — |

**Sözleşme eksikleri (PSR-6/PSR-16 karşılaştırması):**

| Özellik | PSR-16 | PSR-6 | CoreMusic `CacheInterface` |
|---|---|---|---|
| `getMultiple` | ✓ | ✓ | ✗ |
| `setMultiple` | ✓ | ✓ | ✗ |
| `deleteMultiple` | ✓ | ✓ | ✗ |
| `clear` | ✓ | ✓ | ✓ |
| Namespace/prefix | `setPrefix` | pool | ✗ (`clear` tümünü siler) |
| İstisna | `CacheException` | `CacheException` | ✗ (bool/false) |

`composer.json:20` `psr/cache ^3.0` **bağımlılık olarak var** ama
`CacheItem`/`CachePool` **diskte yok** → bağımlılık **kullanılmıyor**
(`⚠️ VERIFICATION REQUIRED`: hangi paket `psr/cache` istiyor?).

## B-02 `CacheManager.php` — statik singleton (24 satır)

| Satır | İçerik |
|---|---|
| 8 | `private static ?CacheInterface $adapter = null;` |
| 10-21 | `getAdapter()` — `function_exists('apcu_fetch')` dalı |
| 23-26 | `setAdapter()` — test/mock için |

**Tasarım notları:**

| Not | Değerlendirme |
|---|---|
| Statik erişim | DI container (`php-di ^7.0` composer'da) **atlanmış** — testte `setAdapter` ile kırılabilir |
| Karar anı | ilk `getAdapter()` çağrısı — sonra **değişmez** (istek ömrü) |
| `apcu.enabled=0` | fonksiyon varlığı doğru sinyal **vermez** → sessiz bozulma (`cache-kapsam` §F) |
| Extension yoksa | composer `ext-apcu` zorunlu → kurulum kırılır; runtime fallback **teorik** |

## B-03 `ApcuAdapter.php` — 6 metot (tam gövde öncesi)

| Metot | APCu | Satır (yaklaşık) |
|---|---|---|
| `get` | `apcu_fetch($key, $success)` | 8-16 |
| `set` | `apcu_store($key, $value, $ttl)` | 18-24 |
| `delete` | `apcu_delete($key)` | 26-32 |
| `increment` | `apcu_inc($key, $step)` | 34-40 |
| `clear` | `apcu_clear_cache()` | 42-48 |
| `has` | `apcu_exists($key)` | 50-56 |

Her metot `function_exists(...)` ile başlar — extension kapalıysa sessizce
`false` (uyarı yok, log yok).

## B-04 `MemoryAdapter.php` — süreç-içi array

| Özellik | Satır | Sonuç |
|---|---|---|
| `private array $store` | 8 | `array<string, array{value, expires}>` |
| TTL kontrolü | 14-18 | `expires > 0 && expires < time()` → lazy silme |
| `set` | 21-28 | `expires = ttl>0 ? time()+ttl : 0` (0 = sonsuz) |
| `increment` | 32-40 | `!is_int(...)` → `false` |
| `clear` | 43-46 | `$this->store = []` |

**"File-based" iddiası yanlış** (`Cache/CLAUDE.md §2.1`) — kod kanıtı yok.

## B-05 `PageCacheAdapter.php` — L3

| Metot | Satır | Davranış |
|---|---|---|
| `__construct` | 13-18 | `?? CacheManager::getAdapter()` — **L1'e yazar** |
| `isPageCacheEnabled` | 20-23 | `$enabled` |
| `ttlPage` | 25-28 | `$ttlPage` (varsayılan 600 sn) |
| `pageKey` | 30-33 | `page_pub_`/`page_auth_` + `md5(uri)` |
| `remember` | 35-44 | read-through; `!== false` kontrolü |

## B-06 `PageCacheInterface.php` — 4 metot

`isPageCacheEnabled()` · `ttlPage()` · `pageKey(string,bool)` ·
`remember(string,int,callable)` — `PageCacheAdapter` ile birebir uyumlu.

---

# EK B.7 — CLAUDE.md ↔ Disk Uyuşmazlık Tablosu (tam)

| # | `Cache/CLAUDE.md` iddiası | Disk gerçeği | Yargı |
|---|---|---|---|
| 1 | `ApcuCacheAdapter.php` | `ApcuAdapter.php` | ❌ ad yanlış |
| 2 | `MemoryCacheAdapter.php` "File-based" | `MemoryAdapter.php` — **süreç-içi array** | ❌ ad + davranış yanlış |
| 3 | `CacheItem.php` (PSR-6) | **dosya YOK** | ❌ uydurma/planlı |
| 4 | `CachePool.php` (PSR-6) | **dosya YOK** | ❌ uydurma/planlı |
| 5 | "Toplam dosya 6 PHP" | 6 dosya ✓ (interface'ler dahil) | ✓ doğru |
| 6 | L1/L2/L3 katmanları | 3 adapter var ✓ | ✓ yapısal doğru |
| 7 | "Read-Through, Write-Through, LRU eviction" | yalnız Read-Through (`remember`); Write-Through **YOK**; LRU **YOK** (APCu kendi politikası) | ⚠️ kısmi |
| 8 | "L1: session" | session kodu `Cache/` içinde **yok** | `UNKNOWN` (Session katmanı ayrı) |
| 9 | "L2: query result" | repository cache'e yazmıyor | ❌ plan ≠ kod |
| 10 | "PSR-6 uyumlu" | PSR-6 yüzeyi **diskte yok** | ❌ |

> Bu tablo, `.ai/architecture/` K007 katmanının **en yüksek sinyalli**
> bulgusudur: bağlam dosyası (CLAUDE.md) ile kod arasındaki 6/10 uyuşmazlık.

---

# EK B.8 — Tüketici (Consumer) Envanteri — 2026-10-17 disk taraması

| # | Tüketici | Dosya:satır | Ne kullanır |
|---|---|---|---|
| 1 | `RateLimiterMiddleware` | `shared/src/Middleware/RateLimiterMiddleware.php:6-7,42,129-132` | `CacheInterface` + `CacheManager::getAdapter()` |
| 2 | `CacheRateLimiter` | `shared/src/Security/CacheRateLimiter.php:6,13` | `CacheInterface` (constructor DI) |
| 3 | `PageRouter` | `shared/src/PageRouter/PageRouter.php:7,21,30` | `PageCacheInterface` (nullable) |
| 4 | `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php:7,74` | `new PageCacheAdapter()` — **doğrudan new** |

| Gözlem | Sonuç |
|---|---|
| DI tutarlılığı | `CacheRateLimiter` **constructor** ile alır; `PageRouterKernel` **`new`** ile — iki farklı stil |
| `PageRouter::$pageCache` nullable | `?PageCacheInterface` → cache **kapalı** olabilir |
| Session cache'i | taramada **görülmedi** → `⚠️` |
| Config cache'i | taramada **görülmedi** → `⚠️` |
| Repository cache'i | **yok** (tasarım kararı) | `repositories-kapsam.md` §2 |

---

# EK B.9 — Katman Gerçeği (plan ↔ kod)

| Katman | Plan (`CLAUDE.md §3`) | Kod | Yargı |
|---|---|---|---|
| L1 Hot | APCu | `ApcuAdapter` ✓ | ✓ |
| L2 Warm | Memory (query result) | `MemoryAdapter` — ama yalnız **fallback**; query cache **yok** | ⚠️ eksik |
| L3 Cool | PageCache | `PageCacheAdapter` — ama **L1'e yazıyor** | ⚠️ fiziksel ayrım yok |

**Sonuç:** fiilen **2 katman** vardır: APCu (paylaşımlı) ve onun fallback'i
(süreç-içi). "L3" ayrı bir depo değil, L1 üzerinde **anahtar öneki**dir.

---

# EK B.10 — Bileşen Olgunluk Matrisi (2. tur)

| Bileşen | Olgu | Kanıt | Risk |
|---|---|---|---|
| B-01 CacheInterface | ✓ 6 metot | dosya | `false` belirsizliği |
| B-02 CacheManager | ✓ 24 satır | dosya | statik singleton, `enabled=0` |
| B-03 ApcuAdapter | ✓ 6 metot | dosya | sessiz `false` |
| B-04 MemoryAdapter | ✓ | dosya | LRU yok, süreç-içi |
| B-05 PageCacheAdapter | ✓ | dosya | L1'e yazar, stampede yok |
| B-06 PageCacheInterface | ✓ 4 metot | dosya | — |
| CLAUDE.md | ⚠️ 6/10 uyuşmazlık | §B.7 | SSOT bozuk |
| Consumer envanteri | ✓ 4 tüketici | §B.8 | DI tutarsız |
| PSR-6 yüzeyi | ❌ YOK | `composer.json` bağımlılık | ölü bağımlılık |
| Session/config cache | `UNKNOWN` | tarama | — |

---

# EK B.11 — Bağımlılık Grafiği

```text
CacheInterface (sözleşme)
   ├── ApcuAdapter      (L1, apcu_*)
   └── MemoryAdapter    (fallback, süreç-içi)

CacheManager (statik singleton)
   └── getAdapter() ──→ ApcuAdapter | MemoryAdapter

PageCacheInterface
   └── PageCacheAdapter ──→ CacheManager::getAdapter()  [L1'e yazar]

Tüketici:
   RateLimiterMiddleware ──→ CacheManager::getAdapter()
   CacheRateLimiter      ──→ CacheInterface (DI)
   PageRouter            ──→ PageCacheInterface (nullable)
   PageRouterKernel      ──→ new PageCacheAdapter()  [doğrudan new]
```

---

# EK B.12 — Karar ve Açık Sorular (bileşen katmanı)

| # | Soru | Durum |
|---|---|---|
| BC-1 | `CacheItem`/`CachePool` planlı mıydı, unutulmuş mu? | `UNKNOWN` |
| BC-2 | `psr/cache ^3.0` neden composer'da? | `⚠️` |
| BC-3 | `PageRouterKernel:74` neden DI yerine `new` kullanıyor? | `UNKNOWN` |
| BC-4 | Session cache'i nerede? | `UNKNOWN` |
| BC-5 | Config cache'i nerede? | `UNKNOWN` |
| BC-6 | Rate limiter anahtar şeması/periode | `UNKNOWN` (dosya okunmadı) |
| BC-7 | `clear()` çağrısı var mı (tüm önbellek silme)? | `UNKNOWN` |
| BC-8 | `set()` false dönüşü loglanıyor mu? | `UNKNOWN` |
| BC-9 | Testlerde `setAdapter` mock kullanımı | `UNKNOWN` |
| BC-10 | `Cache/CLAUDE.md` güncelleme sahibi | `UNKNOWN` |

---

# EK B.13 — RateLimiter Tüketici Derin Analizi (Cache arayüzünün gerçek kullanımı)

> Kanıt: `shared/src/Middleware/RateLimiterMiddleware.php:6-7,42,129-132` +
> `shared/src/Security/CacheRateLimiter.php:6,13` (2026-10-17 disk okuması).

## B.13.1 İki farklı kullanım stili

| Sınıf | Bağımlılık nasıl gelir | Etki |
|---|---|---|
| `CacheRateLimiter` | **constructor DI** — `CacheInterface` zorunlu parametre | test edilebilir, mock edilebilir |
| `RateLimiterMiddleware` | `CacheManager::getAdapter()` — **statik çağrı** (satır 42) | testte `CacheManager::setAdapter` ile kırılabilir |
| `PageRouterKernel` | `new PageCacheAdapter()` (satır 74) | doğrudan new — **mock edilemez** |

**Tutarlılık kararı:** üç stil bir arada; vault'ta hangisinin kanonik olduğu
**tanımlı değil** → `⚠️` açık (BC-3 genişletmesi).

## B.13.2 Rate limiter cache anahtar tahmini (kanıt sınırlı)

```text
Tahmini anahtar şeması (⚠️ VERIFICATION REQUIRED — dosya tam okunmadı):
  rate_limit:{client_id}:{window_start}
```

| Parametre | Değer | Kanıt |
|---|---|---|
| Pencere boyutu | `UNKNOWN` | dosya okunmadı |
| Sayaç yöntemi | `increment` (CacheInterface) muhtemel | §B-01 imzası |
| `increment` false dönerse | `UNKNOWN` — rate limiter fail-open mı fail-closed mu? | **güvenlik etkisi kritik** |
| TTL | `UNKNOWN` | — |

**Kritik boşluk:** `increment` string sayaçta `false` döner (§B-01). Rate
limiter bu `false`'ı nasıl yorumluyor:
- fail-**open** (false → istek geçer) → rate limiter **devre dışı kalır** (güvenlik açığı)
- fail-**closed** (false → 429) → dürüst kullanıcı engellenir (kullanıcı deneyimi açığı)

Bu davranış **dosya okunmadan bilinemez** → `⚠️ VERIFICATION REQUIRED`. Okuma
listesine eklendi (`cache-bilesenler` BC-6 güncellendi).

## B.13.3 Cache → Rate limiter bağımlılık riski

```text
CacheManager::getAdapter()
  → ApcuAdapter (function_exists ile seçildi)
  → apcu.enabled=0 ise → her increment false
  → RateLimiterMiddleware ne yapıyor? (UNKNOWN — §B.13.2)
```

| Risk | Sonuç |
|---|---|
| APCu kapalıysa sessiz rate limiter kaybı | uygulama "çalışıyor" ama korumasız |
| APCu eviction (shm_size doldu) | sayaçlar sıfırlanır → rate limit atlanabilir |
| Çok sunucu | APCu yerel → her sunucu kendi sayacı → etkin limit × N sunucu |

---

# EK B.14 — Önerilen PSR-16 Uyumlu Adapter Tasarımı (kod YAZILMADI — tasarım)

> Bu bölüm **tasarım önerisidir**; repoda böyle bir dosya **YOKTUR**. Amaç:
> mevcut `CacheInterface` (6 metot) ile PSR-16 arasındaki boşluğu kapatmanın
> yollarını göstermek. `⚠️` işaretli satırlar uygulama kararı gerektirir.

## B.14.1 Boşluk kapatma matrisi

| PSR-16 metodu | CoreMusic karşılığı | Kapatma stratejisi |
|---|---|---|
| `get($key, $default = null)` | `get(string): mixed` | `false` yerine **sentinel** (`CacheMiss` nesnesi) — ADR-007 kararı |
| `set($key, $value, $ttl = null)` | `set(...): bool` | PSR-16'da `null` = sonsuz; CoreMusic'te `0` = sonsuz → imza uyarlaması |
| `delete` | `delete` | ✓ birebir |
| `clear` | `clear` | ✓ birebir (ama namespace eksik) |
| `has` | `has` | ✓ birebir |
| `getMultiple` | ✗ | `foreach` + `get` (N round-trip) veya APCu'da batch yok → **performans cezası** |
| `setMultiple` | ✗ | `foreach` + `set` (atomik değil) |
| `deleteMultiple` | ✗ | `apcu_delete(array)` — APCu destekler ama kodda yok |
| `CacheException` | ✗ | bool/false sözleşmesi → kırıcı değişiklik |

## B.14.2 Sentinels önerisi (ADR-007 alternatifi)

```php
// TASARIM — repoda YOK
final class CacheMiss
{
    private static ?self $instance = null;
    public static function get(): self { return self::$instance ??= new self(); }
    private function __construct() {}
}

// Kullanım
$value = $cache->get($key);
if ($value === CacheMiss::get()) { /* miss */ }
elseif ($value === false)         { /* gerçek false değeri */ }
```

| Avantaj | Dezavantaj |
|---|---|
| `false` belirsizliği kapanır | `CacheInterface::get` imzası değişir (`mixed` kalır ama sözleşme değişir) |
| Performans: nesne tek instance | `switch`/`match` yerine `===` zorunlu |
| Test edilebilir | mevcut 4 tüketici (#B.8) güncellenmeli |

## B.14.3 Namespace/prefix çözümü (clear riski)

`clear()` mevcut kodda tüm önbelleği siler (`apcu_clear_cache`). Bileşenler
arası izolasyon için:

| Yaklaşım | Maliyet | Etki |
|---|---|---|
| `apcu_clear_cache('user')` (user cache adı) | APCu'da `apcu_clear_cache(string $cache)` çağrısı | `CacheInterface`'e parametre ekler — kırıcı |
| Anahtar öneki + silme taraması | `apcu_key_info` + `apcu_delete(key[])` | performans cezası |
| `CacheManager::clearNamespace('rate_limit')` | yeni metot | **önerilen** (kırıcı değil, ekleyici) |

---

# EK B.15 — Bileşen Test Matrisi (repo'da YOK — öneri)

| # | Bileşen | Test senaryosu | Beklenen | Repo durumu |
|---|---|---|---|---|
| T-1 | CacheInterface | `set` + `get` round-trip | değer korunur | ❌ yok |
| T-2 | ApcuAdapter | `apcu_fetch` yokken | `function_exists` koruması devreye girer | ❌ yok |
| T-3 | ApcuAdapter | TTL dolumu | `get` → `false` | ❌ yok |
| T-4 | MemoryAdapter | TTL dolumu | `get` → `false` (lazy temizlik) | ❌ yok |
| T-5 | MemoryAdapter | `increment` string sayaç | `false` | ❌ yok |
| T-6 | CacheManager | `apcu_fetch` var + `enabled=0` | **ApcuAdapter** döner (bug) — regresyon testi | ❌ yok |
| T-7 | PageCacheAdapter | `pageKey` ayrımı | `page_pub_` ≠ `page_auth_` | ❌ yok |
| T-8 | PageCacheAdapter | `remember` miss + hit | ilk çağrıda callback; ikincide cache | ❌ yok |
| T-9 | RateLimiter | `increment` false | **fail-open/closed davranışı** (§B.13.2) | ❌ yok |
| T-10 | CacheRateLimiter | constructor DI | mock edilebilir | ❌ yok |

> Toplam 10 senaryonun **0'ı** repoda var. Test edilmeyen cache katmanı,
> sessiz bozulmaya (CA-6) karşı **savunmasızdır** (§K.2 cache-kapsam).

---

# EK B.16 — Cache Bellek Modeli (APCu vs Memory) Karşılaştırması

| Boyut | ApcuAdapter | MemoryAdapter |
|---|---|---|
| Depolama | paylaşmalı segment (SHM) | PHP süreci heap |
| Kapasite | `apcu.shm_size` (INI) | `memory_limit` |
| Eviction | LRU benzeri (sürüm bağımlı → `⚠️`) | yok — sınırsız büyüme |
| Çok-FPM-worker | ✓ paylaşımlı | ✗ worker başına ayrı |
| Kalıcılık | istekler arası | istek içinde |
| Sıfırlama maliyeti | `apcu_clear_cache` (havuz geneli) | `$store = []` (anlık) |
| Debug | `apc` CLI (system) | `var_dump` |
| `increment` atomikliği | ✓ (`apcu_inc` atomik) | ✗ (tek süreç — atomik ama gereksiz) |

## B.16.1 Bellek büyüme riski (MemoryAdapter)

`MemoryAdapter` LRU **kullanmaz** (§C.5). Sonsuz TTL (`expires=0`) + sınırsız
store → bir istek içinde bellek sınırsız büyüyebilir:

| Senaryo | Risk |
|---|---|
| 1 istekte 10.000 farklı anahtar | ~store array bellekte tutulur |
| Sonsuz TTL anahtarlar | istek boyunca temizlenmez |
| FPM `memory_limit` | aşıldığında `OOM` fatal → istek 500 |

**Kontrol:** store boyutu sınırı (max entries) **kodda YOK** → `⚠️`.

---

# EK B.17 — Öneri Önceliklendirme (bileşen katmanı — genişletilmiş)

| # | Öneri | Etki | Öncelik |
|---|---|---|---|
| 1 | CacheMiss sentinel (ADR-007) | `false` belirsizliği kapanır | YÜKSEK |
| 2 | `RateLimiter` fail-open/closed kararı + testi | güvenlik | YÜKSEK |
| 3 | `apcu.enabled` kontrolü CacheManager'a | CA-6 kapanır | YÜKSEK |
| 4 | `clearNamespace` ekleyici metot | clear riski azalır | ORTA |
| 5 | MemoryAdapter max-entry sınırı | OOM koruması | ORTA |
| 6 | `psr/cache` kararı (kaldır veya uygula) | ölü bağımlılık | ORTA |
| 7 | `PageRouterKernel` → DI | mock edilebilirlik | DÜŞÜK |
| 8 | Bileşen test matrisi (§B.15) uygulaması | regresyon güvenliği | DÜŞÜK |

---

# EK B.18 — Cache Arayüzünün İmzaları (birebir kod kanıtı)

> `Cache/` altındaki 4 somut sınıfın imzaları bu ekte toplanmıştır. Her satır
> 2026-10-17 kod okumasıdır; okunmayan gövdeler `UNKNOWN` işaretlidir.

## B.18.1 `CacheInterface` (6 metot — tam)

```php
interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 0): bool;
    public function delete(string $key): bool;
    public function increment(string $key, int $step = 1): int|false;
    public function clear(): bool;
    public function has(string $key): bool;
}
```

## B.18.2 `PageCacheInterface` (4 metot — tam)

```php
interface PageCacheInterface
{
    public function isPageCacheEnabled(): bool;
    public function ttlPage(): int;
    public function pageKey(string $uri, bool $authenticated): string;
    public function remember(string $key, int $ttl, callable $callback): mixed;
}
```

| Gözlem | Sonuç |
|---|---|
| `pageKey` | `bool $authenticated` → 2 önek (`page_pub_`/`page_auth_`) |
| `remember` | callback deseni — read-through |
| `ttlPage()` | getter — 600sn varsayılan |
| `isPageCacheEnabled()` | runtime anahtarı |

## B.18.3 `ApcuAdapter` (6 metot — gövde özeti)

| Metot | APCu çağrısı | Koruma |
|---|---|---|
| `get` | `apcu_fetch($key, $success)` | `function_exists` |
| `set` | `apcu_store($key, $value, $ttl)` | `function_exists` |
| `delete` | `apcu_delete($key)` | `function_exists` |
| `increment` | `apcu_inc($key, $step)` | `function_exists` + `is_int` |
| `clear` | `apcu_clear_cache()` | `function_exists` |
| `has` | `apcu_exists($key)` | `function_exists` |

## B.18.4 `MemoryAdapter` (6 metot — gövde özeti)

| Metot | Davranış | Satır |
|---|---|---|
| `get` | `store[$key]['value']` + TTL kontrolü | 14-18 |
| `set` | `store[$key] = ['value'=>..., 'expires'=>...]` | 21-28 |
| `delete` | `unset($store[$key])` | — |
| `increment` | `is_int` şartı | 32-40 |
| `clear` | `$store = []` | 43-46 |
| `has` | `array_key_exists` + TTL | — |

---

# EK B.19 — Cache Bileşen Kullanım Senaryoları (gerçekleşme analizi)

> `CLAUDE.md §3`'te planlanan 3 kullanım senaryosunun **kod gerçeğiyle**
> karşılaştırması.

## B.19.1 Senaryo 1 — Session cache

| Boyut | Durum |
|---|---|
| Plan | L1 APCu'da session saklama |
| Kod | `Cache/` içinde session kodu **YOK**; `src/Session/` ayrı katman |
| Tarama | session cache'i tüketici taramasında **görülmedi** |
| Sonuç | `UNKNOWN` (Session katmanı okunmadı) |

## B.19.2 Senaryo 2 — Rate limit cache

| Boyut | Durum |
|---|---|
| Plan | L1'de sayaç |
| Kod | `RateLimiterMiddleware` + `CacheRateLimiter` ✓ |
| Mekanizma | `increment` (CacheInterface) — `UNKNOWN` (dosya gövdesi okunmadı) |
| Sonuç | ✓ mevcut, davranış `UNKNOWN` |

## B.19.3 Senaryo 3 — Query result cache

| Boyut | Durum |
|---|---|
| Plan | L2 Memory'de sorgu sonucu |
| Kod | **repository cache'e yazmaz** (tasarım kararı) |
| Sonuç | ❌ uygulanmamış |

## B.19.4 Senaryo 4 — Full page cache

| Boyut | Durum |
|---|---|
| Plan | L3 PageCache |
| Kod | `PageCacheAdapter` + `PageRouter` ✓ |
| Fiziksel | L1 APCu'ya yazıyor (§K.1) |
| Sonuç | ✓ mevcut, L3 ayrım yok |

## B.19.5 Senaryo 5 — Config cache

| Boyut | Durum |
|---|---|
| Plan | — (CLAUDE.md'de yok) |
| Kod | `ConfigManager` cache kullanıyor mu → `UNKNOWN` |
| Sonuç | `UNKNOWN` |

---

# EK B.20 — Cache Hiyerarşisi (fiili — kod kanıtı)

```text
İstek geliyor
  │
  ├─ [1] PageCacheAdapter::remember()
  │       └─ get('page_pub_'|'page_auth_' . md5(uri))
  │              ├─ HIT  → döndür (callback yok)
  │              └─ MISS → callback() → set() → döndür
  │
  ├─ [2] Uygulama katmanı (service/repository)
  │       └─ cache'e yazmıyor (tasarım)
  │
  ├─ [3] Rate limiter
  │       └─ CacheManager::getAdapter() → increment
  │
  └─ [4] DatabaseManager → MySQL
```

| Gözlem | Sonuç |
|---|---|
| Cache hiyerarşisi **fiziksel** | 3 katman değil, **1 fiziksel depo** (APCu) + 2 anahtar öneki |
| L2 "Memory" | yalnız fallback (APCu yoksa) |
| L3 "PageCache" | L1 üzerinde anahtar öneki |

---

# EK B.21 — PSR Uyumluluk Denetimi (detaylı)

| PSR | Gereksinim | CoreMusic | Uyum |
|---|---|---|---|
| PSR-16 SimpleCache | `get/set/delete/clear/has` | ✓ 5 metot | ✓ kısmi |
| PSR-16 | `getMultiple/setMultiple/deleteMultiple` | ✗ | ❌ |
| PSR-16 | `CacheException` fırlatma | ✗ (bool/false) | ❌ |
| PSR-16 | `setPrefix` | ✗ | ❌ |
| PSR-6 CacheItemPool | `getItem/save/deleteItem` | ✗ (CacheItem yok) | ❌ |
| PSR-6 | `CacheItem` nesnesi | ✗ | ❌ |
| PSR-11 Container | `get/has` | ✓ `CacheInterface` | ✓ isim benzerliği |

**Sonuç:** PSR-16'ya **kısmen** (5/8); PSR-6'ya **hiç**; PSR-11'e isim benzerliği
var ama Container **değil**. `psr/cache ^3.0` bağımlılığı kullanılmıyor (§K.5).

---

# EK B.22 — Cache Kararları (bu ek — 4. tur, kapanış)

| # | Karar/tespit | Durum |
|---|---|---|
| B-22.1 | Cache hiyerarşisi fiilen **1 depo + 2 önek** | ✓ (kod kanıtı) |
| B-22.2 | Session cache'i `UNKNOWN` | `UNKNOWN` |
| B-22.3 | Config cache'i `UNKNOWN` | `UNKNOWN` |
| B-22.4 | PSR-16'ya kısmen uyum (5/8) | ✓ |
| B-22.5 | PSR-6'ya hiç uyum yok | ✓ |
| B-22.6 | Query result cache'i uygulanmamış (plan ≠ kod) | ✓ |
| B-22.7 | `CacheRateLimiter` gövdesi okunmadı | `UNKNOWN` |

---

# EK B.23 — Cache Tüketici Analizi (2. tur — dosya bazlı)

> `Cache/` dışındaki 4 tüketici (§B.8) dosya bazında analiz edilmiştir. Her
> satır disk kanıtı; okunmayan gövdeler `UNKNOWN` işaretlidir.

## B.23.1 `RateLimiterMiddleware` (shared/src/Middleware/)

| Satır | İçerik |
|---|---|
| 6-7 | `use CacheInterface` + `use CacheManager` |
| 42 | `CacheManager::getAdapter()` — statik çağrı |
| 129-132 | `CacheInterface` kullanımı — `UNKNOWN` (gövde okunmadı) |

| Gözlem | Sonuç |
|---|---|
| Bağımlılık stili | statik (`CacheManager::getAdapter()`) |
| Test edilebilirlik | `CacheManager::setAdapter` ile mock |
| DI uyumsuzluğu | `CacheRateLimiter` constructor kullanıyor (BC-3) |

## B.23.2 `CacheRateLimiter` (shared/src/Security/)

| Satır | İçerik |
|---|---|
| 6 | `use CacheInterface` |
| 13 | `__construct(private readonly CacheInterface $cache)` |

| Gözlem | Sonuç |
|---|---|
| Bağımlılık stili | **constructor DI** ✓ |
| Test edilebilirlik | mock edilebilir |
| Önerilen stil | ✓ bu (statik değil) |

## B.23.3 `PageRouter` (shared/src/PageRouter/)

| Satır | İçerik |
|---|---|
| 7 | `use PageCacheInterface` |
| 21 | `?PageCacheInterface` — nullable |
| 30 | constructor bağlama |

| Gözlem | Sonuç |
|---|---|
| Nullable | cache kapalı olabilir ✓ |
| DI | constructor — ✓ |

## B.23.4 `PageRouterKernel` (shared/src/PageRouter/)

| Satır | İçerik |
|---|---|
| 7 | `use PageCacheAdapter` |
| 74 | `new PageCacheAdapter()` — doğrudan new |

| Gözlem | Sonuç |
|---|---|
| Bağımlılık stili | **doğrudan `new`** ✗ |
| Test edilebilirlik | mock edilemez |
| Neden | `UNKNOWN` |

---

# EK B.24 — Cache Bellek Ölçümü (kod → tahmini)

> `MemoryAdapter` store array'inin bellek büyüklüğü **kod kanıtı** ile tahmin
> edilmiştir. Ölçüm repo'da **YOK**; bu ek yalnız **tasarım hesabı**dır.

## B.24.1 Bellek modeli (MemoryAdapter)

```text
$store = [
  'key1' => ['value' => mixed, 'expires' => int],
  'key2' => ['value' => mixed, 'expires' => int],
  ...
]
```

| Anahtar uzunluğu | Değer boyutu | Toplam (1K anahtar) |
|---|---|---|
| 42 byte (pageKey) | 1KB (HTML sayfa) | ≈ 1MB |
| 42 byte | 100KB (büyük sayfa) | ≈ 100MB |
| 42 byte | 1MB (çok büyük) | ≈ 1GB → OOM |

## B.24.2 Bellek sınırı tablosu

| Parametre | Varsayılan | Sonuç |
|---|---|---|
| `memory_limit` (PHP) | 128M (tipik) | OOM fatal |
| `apcu.shm_size` (APCu) | sürüm bağımlı | eviction |
| Store max-entry | **YOK** (kodda) | sınırsız büyüme |

**Öneri:** `MemoryAdapter`'a `maxEntries` parametresi eklenmeli (§B.16.1).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Ek Tarihi:** 2026-10-17 · Kanıt: `shared/src/Cache/` (6 dosya) + tüketici taraması