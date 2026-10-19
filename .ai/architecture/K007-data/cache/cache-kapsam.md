---
node_id: "K007-cache-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories"
  - "K007-data/schema"
evidence:
  - "shared/src/Cache/CacheManager.php:12-16 — apcu_fetch varsa ApcuAdapter, yoksa MemoryAdapter (2026-10-15 okundu)"
  - "shared/src/Cache/CLAUDE.md §3 — L1 APCu / L2 Memory / L3 PageCache katmanları (2026-10-15 okundu)"
  - "shared/src/Cache/PageCacheAdapter.php:11,30-31 — page_pub_/page_auth_ + md5(uri) TTL 600"
validation: "2026-10-15 — kapsam yazıldı; 3 repo kanıtı satır bazlı"
open_questions:
  - "Write-through invalidation repo'da YOK → VERIFICATION REQUIRED"
  - "Stampede koruması repo'da YOK → VERIFICATION REQUIRED"
  - "APCu production'da enabled mı → php.ini yüzeyi K007 dışı"
group: mimari
---

# cache — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Katmanlı önbellek mimarisi (L1 APCu · L2 Memory · L3 PageCache) | `Cache/CLAUDE.md §3` |
| 2 | Adapter seçim zinciri (APCu → Memory fallback) | `CacheManager.php:12-16` |
| 3 | Sayfa önbelleği anahtar/TTL şeması | `PageCacheAdapter.php:11,30-31` |
| 4 | TTL politikası (`expires_in`/`expires_at`) | `Cache/CLAUDE.md` · APCu TTL |
| 5 | Invalidation stratejisi (tanımı) | ⚠️ kısmi — repo kanıtı yok |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| DB sorgu planı/indeks | indexing | hız ≠ önbellek |
| Redis/memcached dağıtık seçim | K-04 kararı (UNKNOWN) | araç seçimi onaya bağlı |
| php.ini/apcu.enable yapılandırması | K000-isletim-sistemi | OS/runtime yüzeyi |
| Sayfa içerik üretimi | K011-application | cache yalnız kopyalar |

## §3 Sınır cümlesi

cache, "aynı veri ikinci kez istenince ne kadar ucuza döner?" sorusunun sahibidir —
kopyalar, tazeler, kirletir; **doğru olanın kendisi değildir** (o schema'dır).

---

# EK C — Adapter Yüzeyi (birebir kod kanıtı)

## C.1 CacheInterface (tam — 6 metot)

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

**Sözleşme özelliği:** `get()` yanlışlıkla `false` döndürülebilir (APCu
başarısızlık ile "anahtar yok" aynı değeri verir) — bkz. §C.4.

## C.2 CacheManager (tam — 24 satır)

```php
final class CacheManager
{
    private static ?CacheInterface $adapter = null;

    public static function getAdapter(): CacheInterface
    {
        if (self::$adapter === null) {
            if (function_exists('apcu_fetch')) {
                self::$adapter = new ApcuAdapter();
            } else {
                self::$adapter = new MemoryAdapter();
            }
        }
        return self::$adapter;
    }

    public static function setAdapter(CacheInterface $adapter): void
    {
        self::$adapter = $adapter;
    }
}
```

| Gözlem | Sonuç | Önem |
|---|---|---|
| Seçim kriteri | `function_exists('apcu_fetch')` — **runtime** kontrolü | ✓ iyi |
| `apcu.enabled=0` durumu | fonksiyon var ama çalışmaz → `ApcuAdapter` yine seçilir, her çağrı `false` döner | **YÜKSEK** `⚠️` |
| `apcu.enable_cli` | CLI'da (migration/test) APCu genelde kapalıdır → test ortamı farklı davranır | ORTA |
| Singleton | `static $adapter` — istek ömrü boyunca sabit | test izolasyonu `setAdapter` ile |
| Lock | yok — iki istek aynı anda adapter kurabilir | önemsiz (idempotent) |
| Multi-process | APCu **paylaşımlı**; MemoryAdapter **paylaşımsız** | §C.5 |

## C.3 ApcuAdapter — 6 metot, her biri `function_exists` korumalı

| Metot | APCu çağrısı | Başarısızlık dönüşü |
|---|---|---|
| `get` | `apcu_fetch($key, $success)` | `false` |
| `set` | `apcu_store($key, $value, $ttl)` | `false` |
| `delete` | `apcu_delete($key)` | `false` |
| `increment` | `apcu_inc($key, $step)` | `false` |
| `clear` | `apcu_clear_cache()` | `false` |
| `has` | `apcu_exists($key)` | `false` |

## C.4 `false` belirsizliği — kritik sözleşme kusuru

```php
public function get(string $key): mixed
{
    if (!function_exists('apcu_fetch')) { return false; }
    $success = false;
    $value = apcu_fetch($key, $success);
    return $success ? $value : false;
}
```

| Durum | Dönüş | Ayrıştırılabilir mi? |
|---|---|---|
| Anahtar yok | `false` | ✗ |
| APCu kapalı | `false` | ✗ |
| Değer **gerçekten `false`** | `false` | ✗ |
| Değer `0` / `''` / `[]` | doğru değer | ✓ |

**Sonuç:** `CacheInterface::get()` `false` ile "boşluk" ve "yanlış değeri"
**ayırt edemez**. `PageCacheAdapter::remember()` bunu `!== false` ile karşılar
(`:36-39`) — bu, `false` depolanan gerçek değerlerin **her istekte yeniden
üretileceği** anlamına gelir.

**Öneri (kod yazmadan, tasarım notu):** `null` döndür + `has()` ayrı, ya da
`Result` benzeri çift dönüş. `⚠️ VERIFICATION REQUIRED` — ADR-007 kapsamı
değişikliği gerektirir.

## C.5 MemoryAdapter — süreç-içi (in-process) önbellek

```php
private array $store = [];   // array<string, array{value: mixed, expires: int}>
```

| Özellik | Sonuç |
|---|---|
| Kapsam | **tek PHP isteği/süreci** — FPM worker'lar arası **paylaşılmaz** |
| TTL | `expires > 0 && expires < time()` → lazy temizlik |
| `increment` | `is_int` şartı — string sayaç `false` döner |
| Bellek | `memory_limit` içinde; LRU **yok** → sınırsız büyüme riski |
| `clear` | tüm store'u boşaltır — süreç-içi |

**CLAUDE.md ile çelişki:** `shared/src/Cache/CLAUDE.md §2.1` bu dosyayı
"File-based memory adapter" diye betimliyor; kod **file tabanlı değil**,
süreç-içi array'dir. `CacheItem.php`/`CachePool.php` (PSR-6) da **diskte YOK**.
(Kayıt: `cache-bilesenler.md` §1 notu.)

## C.6 PageCacheAdapter — L3

```php
public function __construct(?CacheInterface $cache = null, bool $enabled = true, int $ttlPage = 600)
{
    $this->cache    = $cache ?? CacheManager::getAdapter();
    $this->enabled  = $enabled;
    $this->ttlPage  = $ttlPage;
}

public function pageKey(string $uri, bool $authenticated): string
{
    $prefix = $authenticated ? 'page_auth_' : 'page_pub_';
    return $prefix . md5($uri);
}

public function remember(string $key, int $ttl, callable $callback): mixed
{
    $cached = $this->cache->get($key);
    if ($cached !== false) {
        return $cached;
    }
    $value = $callback();
    $this->cache->set($key, $value, $ttl);
    return $value;
}
```

| Karar | Sonuç | Risk |
|---|---|---|
| `page_pub_` / `page_auth_` ayrımı | oturumlu/oturumsuz sayfa ayrımı | ✓ doğru |
| `md5($uri)` | 32 hex karakter | URL uzunluğu kaybolur (çarpışma teorik) |
| Cache'i nereye yazar? | `CacheManager::getAdapter()` → **L1 APCu** | L3, L1'in üstünde **fiziksel** olarak durmuyor — katmanlama iddiası `⚠️` |
| `remember` | read-through | stampede koruması **YOK** (§E.2) |
| `callback()` hatası | exception yayılır, cache'e yazılmaz | ✓ iyi |

---

# EK D — Cache Anahtar (Key) Şeması

## D.1 Bilinen anahtarlar (kod kanıtı)

| Anahtar | Üreten | Değer |
|---|---|---|
| `page_pub_ . md5(uri)` | `PageCacheAdapter::pageKey` (`:30-32`) | sayfa gövdesi (içerik `UNKNOWN`) |
| `page_auth_ . md5(uri)` | aynı | oturumlu sayfa |
| Rate limit sayaçları | `CacheRateLimiter` (dosya var, okunmadı `UNKNOWN`) | `increment()` sonucu |
| Session | `CLAUDE.md §3` "L1: session" — kod kanıtı **YOK** | `⚠️` |
| Config | aynı — kod kanıtı **YOK** | `⚠️` |

## D.2 Önerilen (henüz uygulanmamış) namespace şeması

| Domain | Önek | Örnek |
|---|---|---|
| Repository sonucu | `q:` | `q:apikeys:prefix:<8krk>` |
| Sayfa | `page_pub_`/`page_auth_` | mevcut (kanıt) |
| Sayaç | `cnt:` | `cnt:ratelimit:<ip>:<dk>` |
| Domain entity | `e:` | `e:track:<binary-hex>` |

> ⚠️ ADR-007 "cache namespace" başlığını taşıyor; içeriği okunmadı →
> mevcut namespace kuralı **`UNKNOWN`**. Yukarıdaki tablo **öneridir**, kanun değil.

## D.3 `binary(16)` + anahtar etkileşimi

PK'lar `binary(16)`; cache anahtarı string olduğu için **hex** kullanılmalı
(`UuidV7::toHex`). `ApiKeyRepository::toHexSafe()` bunu sağlar. Karışık
kullanım (binary ham string) `apcu_store` anahtarında bayt sorunu yaratabilir
→ `⚠️ VERIFICATION REQUIRED`.

---

# EK E — Invalidation Stratejileri (K007-cache'in gerçek sahipliği)

## E.1 Strateji matrisi

| # | Strateji | Mekanizma | Uygun CoreMusic senaryosu | Repo durumu |
|---|---|---|---|---|
| 1 | TTL (time-based) | `apcu_store(..., $ttl)` | sayfa, rate limit | ✓ **tek uygulanan** (`ttlPage=600`) |
| 2 | Write-through | yazma sonrası cache güncelle | repository write | ❌ yok |
| 3 | Write-behind | async flush | log/sayaç | ❌ yok |
| 4 | Cache-aside (lazy) | okuma miss'inde DB'den doldur | repo sorguları | ❌ yok |
| 5 | Read-through | `remember(callback)` | sayfa | ✓ (`PageCacheAdapter::remember`) |
| 6 | Tag-based invalidation | etiket → anahtar kümesi | playlist/album değişimi | ❌ yok (APCu native desteklemez) |
| 7 | Version-key | `v:<n>` öneki + glob invalidation | entity grupları | ❌ yok |
| 8 | Event-driven | domain event → flush | `Events/Domain/` mevcut | ❌ yok (event altyapısı var: 9 domain event) |

## E.2 Stampede (thundering herd) — koruma YOK

| Durum | Sonuç |
|---|---|
| `remember()` TTL dolduğunda | ilk N istek **hepsi** `callback()` çağırır |
| DB baskısı | TTL dolumu anında sorgu patlaması |
| Koruma seçenekleri | (a) `apcu_add()` ile lock, (b) jitter TTL, (c) stale-while-revalidate |
| Repo durumu | **hiçbiri yok** → `cache-dogrulama.md` testi yok |

## E.3 Cache invalidation ↔ transaction sırası (kritik)

| Sıra | Sonuç |
|---|---|
| transaction ÖNCESİ flush | flush sonrası rollback olursa **eski veri cache'te** (stale) |
| transaction SONRASI flush | ✓ doğru — commit edilmemiş veri cache'e sızmaz |
| commit başarısız | flush **atlanmalı** |

**Kanun:** `transactions-anayasa` AP-4 ("Transaction içinde mail/queue publish
— transaction sonrası kuyruğa al") cache için de geçerlidir. Repo'da uygulanır
durumda **DEĞİL** (cache flush zaten yok).

## E.4 Soft delete + cache

`is_deleted=1` olan kayıt cache'te hâlâ "var" görünebilir. Savunma:
anahtara `is_deleted` durumu **eklenmez**; invalidation kaydın **her durum
geçişinde** tetiklenir. Repo'da mekanizma yok → `⚠️`.

---

# EK F — APCu Yapılandırma Riskleri (K007 dışı yüzey, K007 etkisi)

| Ayar | Etki | K007 için |
|---|---|---|
| `apcu.enabled` | tüm L1 kapanır | `CacheManager` yine `ApcuAdapter` seçer (`function_exists` true kalır!) → **sessiz bozulma** |
| `apcu.enable_cli` | CLI/migration/test | MemoryAdapter'e düşer (farklı davranış) |
| `apcu.shm_size` | Bellek sınırı | taşan cache `apcu_store` `false` döner — **LRU yok** |
| `apcu.ttl` / `apcu.max_entry_size` | tek anahtar sınırı | — |
| `apcu.slam_defense` | eşzamanlı başlatma | — |
| `apcu.use_request_time` | TTL hesabı | yanlış TTL hesabına yol açabilir |

> Yapılandırma `.env`/php.ini'dedir (K000-isletim-sistemi); K007 yalnızca
> **sonucu** taşır. `composer.json:9` `ext-apcu` zorunlu — extension yoksa
> kurulum kırılır (ama `CacheManager` runtime'da tolerate eder → iki
> davranış arasında **tutarsızlık**: composer kurulumu zorunlu, runtime
> fallback var).

---

# EK G — Serileştirme ve Bellek

| Konu | APCu | MemoryAdapter |
|---|---|---|
| Serileştirme | APCu PHP değerlerini **doğrudan** saklar (şeffaf copy-on-write) | PHP array referans kopyası |
| Nesne tuzakları | `__sleep`/`__serialize` gerekmez | referans paylaşımı sürprizi |
| Büyük değer | `shm_size` + `max_entry_size` | `memory_limit` |
| `json` kolonları (113 adet) | string olarak saklanmak gerekirse decode maliyeti | aynı |

**Risk:** `PageCacheAdapter` hangi **tipi** cache'lediğini imza söylemiyor
(`mixed`). HTML gövde string mi, `ApiResponse` nesnesi mi → `UNKNOWN`
(`data-kod-ornekleri.md` açık sorusu ile aynı).

---

# EK H — Cache ↔ Komşu Alt Alanlar

| Komşu | İlişki | Kural |
|---|---|---|
| `repositories/` | repo cache'e **yazmaz** (şimdilik) | `repositories-kapsam.md` §2 |
| `transactions/` | invalidation commit **sonrası** | §E.3 |
| `schema/` | `binary(16)` ↔ hex anahtar | §D.3 |
| `search/` | arama sonucu cache'i | `UNKNOWN` |
| `synchronization/` | çok-DB'de cache tutarlılığı | dağıtık cache **YOK** (tek süreç APCu) |
| `backup/` | cache dump'ı alınmaz | — |
| `indexing/` | cache hit oranı DB yükünü azaltır → EXPLAIN gerekmez | ölçüm **YOK** |

## H.1 Çok-işlem/çok-sunucu gerçeği

| Ortam | APCu davranışı | Sonuç |
|---|---|---|
| Tek PHP-FPM sunucusu | APCu paylaşımlı (worker'lar arası) | ✓ tutarlı |
| 2+ sunucu / load balancer | APCu **yerel** — senkron değil | **stale cache riski** |
| Dağıtık seçenek | Redis/Memcached | K-04 kararı `UNKNOWN` |

CoreMusic topolojisi (1 sunucu mu, çok sunucu mu) **`UNKNOWN`** → `⚠️`.
Çok sunuculu ise `page_pub_` cache'i sunucu başına farklı içerik döndürebilir.

---

# EK I — Doğrulama Testleri (öneri seti — repo'da YOK)

| # | Test | Beklenen | Repo durumu |
|---|---|---|---|
| 1 | `apcu_fetch` yokken `CacheManager::getAdapter()` | `MemoryAdapter` | ❌ yok |
| 2 | `apcu.enabled=0` + fonksiyon var | `ApcuAdapter` seçilir ama her `get` `false` | ❌ yok (**tespit edilen çelişki**) |
| 3 | `remember()` + `false` değeri | her istekte callback çağrılır | ❌ yok (§C.4) |
| 4 | TTL dolumu | değer `false` döner | ❌ yok |
| 5 | `pageKey` ayırt ediciliği | `page_pub_` ≠ `page_auth_` | ❌ yok |
| 6 | `increment` (string sayaç) | `false` | ❌ yok |
| 7 | Transaction rollback sonrası cache | eski değer durur | ❌ yok |
| 8 | Stampede (eşzamanlı `remember`) | N çağrı | ❌ yok |

---

# EK J — Kararlar ve Açık Sorular (cache)

| # | Karar/tespit | Durum |
|---|---|---|
| CA-1 | Invalidation stratejisi **yalnız TTL** | ✓ kayıt (kanıt) |
| CA-2 | Write-through invalidation yok | `⚠️` açık |
| CA-3 | Stampede koruması yok | `⚠️` açık |
| CA-4 | `false` belirsizliği sözleşme kusuru | `⚠️` açık (ADR-007) |
| CA-5 | L3 PageCache fiilen L1 APCu'ya yazıyor | `⚠️` açık |
| CA-6 | `apcu.enabled=0` sessiz bozulma | `⚠️` açık |
| CA-7 | Çok sunucu topolojisi | `UNKNOWN` |
| CA-8 | Redis/Memcached seçimi (K-04) | `UNKNOWN` |
| CA-9 | Domain event → cache flush bağlantısı | `UNKNOWN` |
| CA-10 | `PageCacheAdapter` cache'lediği tip | `UNKNOWN` |
| CA-11 | Rate limiter cache anahtar şeması | `UNKNOWN` (dosya okunmadı) |
| CA-12 | ADR-007 namespace kuralı içeriği | `UNKNOWN` |

---

# EK K — Adaptör Davranış Matrisi ve Kod Düzeyi Denetim

> Bu ek, `shared/src/Cache/` altındaki 4 somut dosyanın (`CacheManager.php`,
> `ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php`) satır düzeyi
> gözlemlerinden türetilmiştir. Her satır kod kanıtına dayanır; kanıtsız hücre
> `UNKNOWN` işaretlenmiştir.

## K.1 Adaptör karşılaştırma matrisi

| Özellik | ApcuAdapter | MemoryAdapter | PageCacheAdapter |
|---|---|---|---|
| Kalıcılık | FPM havuzu boyunca (paylaşımlı) | Tek istek/süreç | L1 adaptörüne devredilmiş |
| `get` → bulunamazsa | `false` (APCu sözleşmesi) | `false` | `false` |
| `has()` uygulaması | `apcu_exists` | `array_key_exists` benzeri (kod kanıtı) | üst adaptöre devredilir |
| `increment` | `apcu_inc` + `is_int` kontrolü | sayısal mı? **`UNKNOWN`** (kod okunmadı) | yok (CacheInterface'e ek metot yok) |
| TTL kaydı | APCu'nun kendi TTL'si | `expires` alanı (§C.5 tablosu) | üst adaptör TTL'si |
| Sıfırlama maliyeti | `apcu_clear_cache` (havuz geneli) | store array'i boşaltılır | üst adaptöre devredilir |
| `function_exists` koruması | ✓ var (`apcu_*`) | gerekmez | gerekmez (saf delegasyon) |

## K.2 `CacheManager::getAdapter()` seçim ağacı (§C.2 kanıtından)

```text
CacheManager::getAdapter()
  ├─ [1] function_exists('apcu_fetch')?
  │     ├─ HAYIR → MemoryAdapter (süreç-içi)
  │     └─ EVET → [2] apcu.enabled INI değeri okunur
  │           ├─ 0/false → MemoryAdapter BEKLENİR ama §C.2 kanıtı:
  │           │            kod yalnız fonksiyon varlığını sorguluyor →
  │           │            ApcuAdapter döner, her çağrı sessiz false (CA-6)
  │           └─ 1/true  → ApcuAdapter
  └─ (PageCacheAdapter AYRI instantiate edilir — bu ağaçta YOK)
```

**Kritik sapma (CA-6):** Seçim ağacı [2] adımını **atlar**. Sonuç: CLI ortamda
veya `apcu.enabled=0` olan FPM'de uygulama "çalışıyor" görünür ama **hiçbir cache
hit'i üretilmez** — her `remember()` callback'i yeniden çağrılır. Tespit edilmesi
zor çünkü fonksiyonel hata yoktur, yalnız performans kaybı vardır.

## K.3 `remember()` çağrışım zinciri (PageCacheAdapter, §C.6)

| Adım | Kod davranışı | Hata modu |
|---|---|---|
| 1 | `get($key)` çağrılır | `false` dönerse [2]'ye |
| 2 | `false` → `$callback()` | İlk istekte her zaman callback |
| 3 | Sonuç `set($key, $value, $ttl)` | `false` değeri **set edilse bile** yeniden okunamaz (CA-4) |
| 4 | Dönen değer | `mixed` — tip sözleşmesi yok (CA-10) |

**Ölçülmüş risk:** Adım 3'te `false` `set` edilirse APCu `false`'ı saklar mı
yoksa `set` reddedilir mi → APCu sürümüne bağlı davranış, bu repo'da test edilmemiş
→ `⚠️ VERIFICATION REQUIRED`. Etki: `false` dönen bir hesaplama **her istekte**
yeniden hesaplanır (kayıp: yok; gecikme: vardır).

## K.4 Cache anahtar ömrü tablosu (§D.1 anahtarları ile)

| Anahtar | Üretildiği yer | Silindiği yer | TTL |
|---|---|---|---|
| `page_pub_{md5(uri)}` | `PageCacheAdapter::pageKey` | **yok** (yalnız TTL) | `ttlPage` (varsayılan 600sn) |
| `page_auth_{md5(uri)}` | aynı | **yok** | `ttlPage` |
| Rate limiter anahtarları | `UNKNOWN` (dosya okunmadı — CA-11) | `UNKNOWN` | `UNKNOWN` |
| Repository cache (varsa) | **yok** — repo'lar cache'e yazmaz (§H) | — | — |

**Gözlem:** Hiçbir anahtar **aktif olarak silinmiyor**. Yazma gerçekleştiğinde
ilgili `page_pub_` anahtarının TTL'i düşülmeden beklenir → maksimum 600sn
stale veri penceresi. `invalidate()` benzeri bir metodun varlığı kodda
**görülmedi** → `UNKNOWN`.

## K.5 PSR-6 boşluğu (composer.json kanıtı)

`shared/composer.json` `psr/cache: ^3.0` requirement'ını taşıyor; ancak
`Cache/` dizininde `CacheItem.php` ve `CachePool.php` **diskte yok** (envanter §C.1).
Yani:

| Beklenen PSR-6 yüzeyi | Disk durumu |
|---|---|
| `Psr\Cache\CacheItemInterface` | ❌ yok |
| `Psr\Cache\CacheItemPoolInterface` | ❌ yok |
| `CacheItem` somut sınıfı | ❌ yok |
| `CachePool` somut sınıfı | ❌ yok |
| `CacheInterface` (proje-içi, 6 metot) | ✓ var — **PSR-6 değil, PSR-16'ya da uymuyor** |

**Sonuç:** Bağımlılık `composer.json`'da tanımlı ama **kullanılmıyor** (ölü
bağımlılık sinyali). `CacheInterface` proje kendi 6 metotlu arayüzünü kullanıyor;
`get/set/delete/clear/has/increment` — `increment` PSR-16'da da PSR-6'da da yoktur.
`psr/cache ^3.0`'ın kaldırılması bir adaydır ama bu karar **verilmemiştir** → `⚠️`.

## K.6 INI etkileşim tablosu (APCu ile)

| INI anahtarı | Etki | Kod tarafı karşılığı |
|---|---|---|
| `apcu.enabled` | 0 → cache fiilen kapalı | seçim ağacında **sorgulanmıyor** (CA-6) |
| `apcu.enable_cli` | CLI'da APCu kapalıysa fonksiyon var ama çalışmaz | seçim ağacı bu durumu ayırt **edemez** |
| `apcu.ttl` | global üst sınır | `set` TTL'ini aşarsa APCu kendi değerini uygular → kodda okunmuyor |
| `apcu.shm_size` | havuz boyutu → dolunca eviction | kodda **hiçbir yerde sorgulanmıyor**; eviction sessiz veri kaybı |
| `apcu.entries_hint` | performans ipucu | etkisiz, kod tarafı yok |

**Tespit:** Kod yalnızca `apcu.enabled`'ı **değil**, hiçbir INI değerini okumuyor
(§C.2 seçim kodu tek kontrol). `shm_size` eviction'ı sessizdir → `remember()`
içindeki `false` dönen kayıt sayısı havuz doldukça artar, tespit edilemez.

## K.7 Ölçüm ve telemetri boşluğu

| Metrik | Toplanıyor mu? | Kanıt |
|---|---|---|
| Hit oranı | ❌ | kodda hit/miss sayacı yok |
| Hit süresi (latency) | ❌ | zamanlama ölçümü yok |
| Eviction sayısı | ❌ | APCu istatistiği okunmuyor |
| Havuz doluluk oranı | ❌ | `apcu_sma_info` çağrısı yok |
| Cache boyutu | ❌ | — |

**Sonuç:** Cache davranışına dair production'da **hiçbir gözlemlenebilirlik
yok**. CA-6 (`enabled=0` sessiz bozulma) ve `shm_size` eviction gibi hatalar
**yalnızca kod okunarak** tespit edilebilir; runtime telemetriyle değil.
`⚠️ VERIFICATION REQUIRED`: bir metrik/monitoring entegrasyonu varsa bile bu
`Cache/` klasöründe görünmüyor; üst katman (ör. middleware) mı ölçüyor → `UNKNOWN`.

## K.8 Erişilebilirlik ve concurrency notları

| Konu | Gözlem |
|---|---|
| Eşzamanlı `remember()` | stampede koruması **yok** (§E.2) — APCu atomicite sağlamaz |
| `increment` atomikliği | `apcu_inc` atomiktir ama `is_int` şartı sayacın string ise **hiç çalışmamasına** yol açar |
| MemoryAdapter `increment` | `UNKNOWN` — kod okunmadı; ama süreç-içi olduğundan eşzamanlılık riski yok |
| `clear()` yan etkisi | `apcu_clear_cache` **havuz geneli** — başka bir uygulamanın cache'ini de siler (paylaşımlı havuz) |

## K.9 Öneri önceliklendirme (kanıt → etki)

| # | Öneri | Etki | Kanıt gücü |
|---|---|---|---|
| 1 | `apcu.enabled` sorgusunu seçim ağacına ekle | CA-6 kapanır | kod kanıtı (§C.2) |
| 2 | `remember()`'da `null` sentinel (false yerine) | CA-4 kapanır | kod + sözleşmesel |
| 3 | Yazma sonrası `page_pub_` anahtarını aktif sil | 600sn stale penceresi kapanır | §K.4 kanıtı |
| 4 | Hit/miss + havuz doluluk metriği ekle | gözlemlenebilirlik | §K.7 boşluğu |
| 5 | `psr/cache` bağımlılığını karara bağla (kaldır veya uygula) | ölü bağımlılık temizlenir | §K.5 |
| 6 | Stampede: `apcu_add` kilidi veya singleflight | thundering herd kapanır | §E.2 |

# EK L — Sayfa Önbelleği Derinlemesine (PageCacheAdapter + TTL matematiği)

> Kanıt: `PageCacheAdapter.php:11,30-31` (pageKey + md5(uri) + TTL 600) ve
> `Cache/CLAUDE.md §3` (L3 katmanı). Bu ek, sayfa önbelleğinin uçtan uca
> davranışını kod kanıtı sınırları içinde inceler.

## L.1 Sayfa cache anahtarı türetme denetimi

```php
// PageCacheAdapter.php:30-31 (birebir kod kanıtı)
public function pageKey(string $uri, bool $authenticated): string
{
    $prefix = $authenticated ? 'page_auth_' : 'page_pub_';
    return $prefix . md5($uri);
}
```

| Özellik | Değer | Değerlendirme |
|---|---|---|
| Türetme | `md5($uri)` | MD5 → **çarpışma (collision) direnci zayıf** (2^64 birthday); cache poisoning senaryosu |
| Girdi kapsamı | `$uri` tek parametre | Query string dahil mi → kodda `parse_url` **yok**; `?page=2` → farklı anahtar ✓ |
| `Host` başlığı | anahtarda **yok** | 2 site aynı md5'i paylaşabilir → **yanlış içerik riski** (çok-site kurulumunda) |
| `Accept-Encoding` | anahtarda **yok** | gzip istemcisi, almadığı içeriği alabilir → `⚠️ VERIFICATION REQUIRED` (middleware bunu ele alıyor mu?) |
| `Vary` başlığı | kodda **görülmedi** | — |
| Uzunluk sınırı | md5 → 32 hex + 9-10 önek ≈ 42 byte | anahtar ≤ 64 byte (APCu anahtar sınırı) ✓ |

**Tespit:** md5 yerine BLAKE3/SHA-256-trunc önerilebilir; ama cache anahtarında
collision **kullanıcıya yanlış içerik göstermek** anlamına gelir — güvenlik
etkisi düşüktür ama doğruluk etkisi **yükseltdir**. `⚠️` işaretli: collision
bir vaka olarak raporlanmış mı → bilinmiyor (hiçbir log/monitoring yok, §K.7).

## L.2 TTL matematiği — 600 saniye varsayılanı

| Boyut | Değer | Sonuç |
|---|---|---|
| Varsayılan TTL | 600sn (10 dk) | `PageCacheAdapter.php:11` |
| Üst sınır | APCu `apcu.ttl` INI'ine bağlı | `⚠️ VERIFICATION REQUIRED` — sunucu ayarı |
| Düşük TTL (ör. 5sn) | — | DB yükü artar, kazanım azalır |
| TTL=0 | — | kod davranışı `UNKNOWN` (kod okunmadı — TTL=0'ın anlamı cache'lememek mi, süresiz mi) |

**İyileştirme:** `Cache-Control` başlığı ile senkronize edilmeli; kodda bunu
yapan bir middleware **görülmedi** → `⚠️ VERIFICATION REQUIRED`.

## L.3 Autentikasyon ayrımı (page_pub_ vs page_auth_)

| Anahtar | Kim | İçerik | Risk |
|---|---|---|---|
| `page_pub_{md5(uri)}` | anonim | genel sayfa | — |
| `page_auth_{md5(uri)}` | oturum açmış | kişiselleştirilmiş sayfa | **kişisel veri cache'de** → TTL 600sn boyunca erişilebilir |

**Güvenlik notu:** `page_auth_` cache'i oturum kapanınca **silinmez** (aktif
invalidation yok, §K.4). Aynı cihazdan başka kullanıcı giriş yaparsa **aynı
`md5(uri)`** ile cache'e yazılır → `page_auth_` anahtarı kullanıcı-özel değil,
**urlye-özel**. Yani:
- Kullanıcı A, `/profile` sayfasını görüntüler → `page_auth_{md5(/profile)}` = A'nın verisi.
- A çıkıp B girer, `/profile` açar → **A'nın verisi 600sn boyunca B'ye servis edilir** → **veri sızıntısı** (critical).
- `⚠️ VERIFICATION REQUIRED`: gerçek uygulamada `page_auth_` kullanılıyor mu (sorgu cache'i var mı)? Kod kanıtı yalnız adapter'ın ürettiği anahtar; kullanım sitesi okunmadı.

## L.4 PageCacheAdapter ↔ PSR-6/16 uyumsuzluğu

`remember()` bir **callback** desenidir; PSR-6'da `CacheItemPoolInterface::get()`
`CacheItem` döndürür; PSR-16'da `get()` doğrudan `mixed`. `remember()` üçünün
de parçası değildir → `CacheInterface`'in özel olduğu doğrulanır (§K.5).

## L.5 Sayfa cache'i — ne zaman invalid edilir? (boşluk tablosu)

| Olay | Beklenen invalidasyon | Kodda karşılığı |
|---|---|---|
| İçerik güncellendi (repo write) | ilgili `page_pub_` silinmeli | **YOK** (§K.4) |
| Kullanıcı oturum açtı | `page_auth_` | otomatik değil; kullanıcı-özel değil (§L.3) |
| Kullanıcı çıktı yaptı | `page_auth_` | **YOK** |
| RTL/tema değişimi | — (client-side) | — |
| Deploy / cache sürüm değişimi | tümü flush edilmeli | **YOK** (deployment hook cache'e dokunmuyor → `UNKNOWN`) |

## L.6 Ölçüm (2026-10-17, kod okuması)

| Metrik | Değer | Kaynak |
|---|---|---|
| Sayfa cache anahtar uzunluğu | ~42 byte | §L.1 hesabı |
| TTL | 600sn | kod kanıtı |
| Invalidasyon sayısı | 0 (kodda) | §K.4 |
| Collision direnci | MD5 (2^64) | §L.1 |
| Autentikasyon ayrımı | var (2 önek) | §L.3 |
| Kullanıcı-özel cache | **YOK** → veri sızıntısı riski | §L.3 |

## L.7 Kararlar (PageCache türevi)

| # | Karar/tespit | Durum |
|---|---|---|
| CP-1 | md5 cache anahtarı collision'a açık ama düşük olasılık | ✓ kayıt |
| CP-2 | `page_auth_` kullanıcı-özel değil → veri sızıntısı riski | `⚠️` kritik (ADR'ye taşınmalı) |
| CP-3 | Host başlığı anahtarda yok | `⚠️` |
| CP-4 | TTL=0 davranışı | `UNKNOWN` |
| CP-5 | Cache-Control senkronizasyonu | `UNKNOWN` (middleware okunmadı) |
| CP-6 | Deploy flush hook'u | `UNKNOWN` |

---

# EK M — Özet Karar Tablosu (cache alt alanı — bu üretim sonu)

> Tüm EK C..L'deki kararların **tek konsolide görünümü**. Aşağıdaki tablo
> yalnız bu üretimde üretilen yeni bulguları içerir; önceki üretim kararları
> (CA-1..CA-12) §J'de durur.

## M.1 Yeni bulgular (EK K + EK L)

| # | Bulgu | EK | Kritiklik |
|---|---|---|---|
| 1 | Seçim ağacı `apcu.enabled`'ı sorgulamıyor | §K.2 | YÜKSEK |
| 2 | `psr/cache ^3.0` ölü bağımlılık | §K.5 | ORTA |
| 3 | Hiçbir INI değeri okunmuyor (`shm_size` eviction sessiz) | §K.6 | ORTA |
| 4 | Sıfır gözlemlenebilirlik (hit/miss/eviction/metrik yok) | §K.7 | YÜKSEK |
| 5 | `page_auth_` kullanıcı-özel değil → veri sızıntısı riski | §L.3 | **KRİTİK** |
| 6 | `md5` cache anahtarı collision'a açık | §L.1 | DÜŞÜK |
| 7 | Host başlığı anahtarda yok | §L.1 | ORTA (çok-site) |
| 8 | Aktif invalidasyon: 0 (yalnız TTL) | §K.4 | YÜKSEK |

## M.2 Kritiklik tanımları

| Seviye | Tanım |
|---|---|
| KRİTİK | veri sızıntısı / güvenlik açığı |
| YÜKSEK | sessiz performans kaybı veya güvenlik zayıflığı |
| ORTA | bakım/ölçülebilirlik açığı |
| DÜŞÜK | kozmetik / düşük olasılık |

## M.3 Öneri → ADR bağımlılığı

| Öneri | ADR gerekli mi? | Not |
|---|---|---|
| `page_auth_` kullanıcı-özel anahtar | **✓ evet** | güvenlik etkisi |
| `apcu.enabled` sorgusu | hayır | düzeltme (bug fix) |
| CacheMiss sentinel | **✓ evet** | `CacheInterface` sözleşmesi değişir |
| `psr/cache` kaldırma | hayır | bağımlılık temizliği |
| Stampede kilidi | hayır ama tasarım | performans |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Ek Tarihi:** 2026-10-17 · Kanıt: `shared/src/Cache/` (6 dosya) + `composer.json`