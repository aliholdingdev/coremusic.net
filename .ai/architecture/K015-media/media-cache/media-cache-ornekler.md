---
node_id: "K015-media/media-cache/media-cache-ornekler"
node_type: "module"
title: "Media-Cache — Örnekler (tasarım iddiası)"
status: draft
group: kod
parent_id: "K015-media/media-cache"
children: []
depends_on: ["K015-media/media-cache/media-cache-kapsam", "K015-media/media-cache/media-cache-web-arastirma"]
evidence:
  - "media-cache-kapsam.md — §3 durum makinesi, §5 hata durumları, §6 açık sorular (2026-10-16)"
  - "media-cache-web-arastirma.md — MC-01..MC-13 URL havuzu (2026-10-16, HTTP 200)"
  - "shared/src/Api/Middleware/ResponseNormalizationMiddleware.php:34-46,68-96 (Cache-Control + ETag/304 setter) · shared/src/PageRouter/ResponseEmitter.php:10 (varsayılan no-store) · shared/src/Cache/ (6 dosya — medya-dışı önbellek katmanı) — 2026-10-16 okundu/glob"
validation: "TASARIM İDDİASI — üretim kodu değildir. Repo'da medya önbelleği yüzeyi YOK (2026-10-16 grep: media-cache|mediaCache|MediaCache → yalnız .ai/ vault dosyaları, kaynak kodda 0 isabet). Örnekler yalnız tasarım referansıdır."
open_questions: "Boyut/TTL politikası (UNKNOWN) · temizleme stratejisi LRU/LFU/ARC (UNKNOWN) · önbellek depolama yeri (sunucu mu tarayıcı mı — TASARIM KARARI) · offline sahipliği (§7/2 — kesinleştirilmez)"
updated: 2026-10-16
---

# Media-Cache — Örnekler (tasarım iddiası)

> **Etiket kuralı:** Bu dosyadaki tüm örnekler **TASARIM İDDİASI** etiketlidir; repo'da
> birebir karşılığı olan satırlar `Dosya:Satır` ile kanıtlanır. Üretim kodu değildir.
> **Tarih:** 2026-10-16 · **Kapsam:** K015-media / media-cache

---

## Ör-01 — Cache-Control setter (PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `MediaCacheHeaders.php` (tasarım) |
| Amaç | Önbelleklenen medya yanıtına Cache-Control + ETag yazmak; taze/bayat ayrımını (kapsam §3) başlık düzeyinde ifade etmek |
| Kanıt | `media-cache-kapsam.md:§1a-3,§1a-4,§3` · `media-cache-web-arastirma.md:MC-1,MC-3,MC-11` · repo: `ResponseNormalizationMiddleware.php:88-96` (setter deseni), `ResponseEmitter.php:10` (varsayılan no-store) |
| Durum | TASARIM İDDİASI — repo'daki setter API yanıtlarınadır; medya önbelleği yüzeyi grep 0 isabet |

```php
<?php
declare(strict_types=1);

// MediaCacheHeaders.php — TASARIM IDDIASI (uretim kodu degildir)
// TTL degerleri BURADA UYDURULMAZ (kapsam §6/1 — UNKNOWN).
final class MediaCacheHeaders
{
    private ?int $ttlSeconds;   // null = politika yok → no-store (repo: ResponseEmitter.php:10)

    public function __construct(?int $ttlSeconds)
    {
        $this->ttlSeconds = ($ttlSeconds !== null && $ttlSeconds > 0) ? $ttlSeconds : null;
    }

    /** Taze girdi (§3 S-3): public + max-age + ETag; bayat/güvensiz girdi: no-store. */
    public function emit(string $etag, bool $fresh): void
    {
        if ($fresh && $this->ttlSeconds !== null) {
            header(sprintf('Cache-Control: public, max-age=%d', $this->ttlSeconds));
        } else {                                        // §5/2-§5/3: kayıt doğrulanamıyorsa
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
        }
        header('ETag: "' . $etag . '"');
        header('Vary: Accept-Encoding');                // varyant anahtarı — §1a-2, MC-4
    }

    /** Doğrulama (§1a-4): If-None-Match eşleşirse gövdesiz 304. */
    public function checkRevalidate(string $etag): bool
    {
        if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') !== '"' . $etag . '"') return false;
        http_response_code(304);
        return true;
    }
}
```

---

## Ör-02 — PDO prepared önbellek kaydı (PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `MediaCacheRepository.php` (tasarım) |
| Amaç | Girdi okuma/yazma/dolum döngüsünü (kapsam §3 `empty→filling→fresh`) prepared statement ile taşımak |
| Kanıt | `media-cache-kapsam.md:§1a-1,§3,§5/1,§5/2` · `media-cache-web-arastirma.md:MC-1` · repo kuralı: PDO prepared yalnız (shared AGENTS.md §4 — ADR-002) |
| Durum | TASARIM İDDİASI — şema K007'dedir (kapsam §2/4); kolon uzunlukları **tasarım parametresidir, ölçülmüş değer değildir** |

```php
<?php
declare(strict_types=1);

// MediaCacheRepository.php — TASARIM IDDIASI (uretim kodu degildir)
// SELECT * YOK · prepared ZORUNLU (ADR-002) · expires_at BURAYA YAZILMAZ (§6/1 UNKNOWN).
final class MediaCacheRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /** §3 S-2 → S-3: girdi yaz (mevcutsa güncelle). */
    public function put(string $key, string $kind, string $payload, ?int $ttlSeconds): bool
    {
        // null = tazelik yok → doğrulama zorunlu (MC-1); süre değeri politikadan gelir (§6/1).
        $expiresAt = ($ttlSeconds !== null && $ttlSeconds > 0) ? date('Y-m-d H:i:s', time() + $ttlSeconds) : null;
        $sql = 'INSERT INTO media_cache (cache_key, kind, payload, expires_at, created_at)
                VALUES (:cache_key, :kind, :payload, :expires_at, NOW())
                ON DUPLICATE KEY UPDATE payload = VALUES(payload), expires_at = VALUES(expires_at), created_at = NOW()';
        $stmt = $this->pdo->prepare($sql);
        // kind: artwork | metadata | track (§1a-1)
        return $stmt->execute([':cache_key' => $key, ':kind' => $kind, ':payload' => $payload, ':expires_at' => $expiresAt]);
    }

    /** §3 S-1 → S-3: taze okuma (HIT); bayat/eksikse null → §5/3 doğrulama. */
    public function getFresh(string $key): ?string
    {
        $sql = 'SELECT payload, expires_at FROM media_cache
                WHERE cache_key = :cache_key
                  AND (expires_at IS NULL OR expires_at > NOW())
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cache_key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) && isset($row['payload']) ? (string) $row['payload'] : null;
    }

    /** §3 S-4/S-5: doğrulama başarısız/elle temizlik → tek girdiyi at. */
    public function evict(string $key): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM media_cache WHERE cache_key = :cache_key');
        return $stmt->execute([':cache_key' => $key]);
    }

    // Toplu temizlik (LRU/LFU) YOK — strateji seçilmedi (kapsam §6/2 · MC-5..MC-7).
}
```

---

## Ör-03 — Önbellekten byte-range okuma iskeleti (PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `MediaCacheRangeReader.php` (tasarım) |
| Amaç | Önbellekteki medya parçasından istenen aralığı okumak (206/416 ayrımı) — **sunum tarafı** |
| Kanıt | `media-cache-kapsam.md:§2/2,§5/5` · kardeş kanıt: Range/206/416 uygulaması repo'da `home.coremusic.net/include/Stream/MusicStreamHandler.php` üzerinden `downloads-ornekler.md` içinde kanıtlanmıştır — bu dosya **yeniden iddia üretmez** |
| Durum | TASARIM İDDİASI — indirme resume (kaldığı yerden devam) **downloads'ın işidir** (kapsam §2/2); burada yalnız **okuma** iskeleti vardır |

```php
<?php
declare(strict_types=1);

// MediaCacheRangeReader.php — TASARIM IDDIASI (uretim kodu degildir)
// Indirme resume DEGIL (downloads'ın işi — §2/2): önbellekteki kayıt üzerinden okuma.
final class MediaCacheRangeReader
{
    /** @return array{status:int,offset:int,length:int}|null null = geçersiz aralık → 416 */
    public function resolve(?string $rangeHeader, int $size): ?array
    {
        if ($rangeHeader === null || $size <= 0) {
            return ['status' => 200, 'offset' => 0, 'length' => $size];
        }
        if (!preg_match('/^bytes=(\d*)-(\d*)$/', $rangeHeader, $m)) return null;   // 416
        $start = $m[1] !== '' ? (int) $m[1] : null;
        $end   = $m[2] !== '' ? (int) $m[2] : null;
        if ($start === null && $end === null) return null;
        if ($start === null) { $start = max(0, $size - $end); $end = $size - 1; }   // son N bayt
        else { $end = $end ?? $size - 1; }
        if ($start >= $size || $end < $start) return null;
        return ['status' => 206, 'offset' => $start, 'length' => $end - $start + 1];
    }
}
```

---

## Ör-04 — Önbellek durum makinesi (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `media-cache-state.js` (tasarım) |
| Amaç | Kapsam §3'teki 5 durumu (`empty`·`filling`·`fresh`·`stale`·`evicted`) geçiş tablosuyla tutmak |
| Kanıt | `media-cache-kapsam.md:§3` · `media-cache-web-arastirma.md:MC-1,MC-3,MC-11` |
| Durum | TASARIM İDDİASI — repo'da medya önbelleği JS'i yok (grep 0 isabet) |

```javascript
// media-cache-state.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

const CacheState = Object.freeze({
  EMPTY: 'empty', FILLING: 'filling', FRESH: 'fresh', STALE: 'stale', EVICTED: 'evicted',
});

// Geçiş tablosu: kapsam §3 ile birebir. Olmayan geçiş = geçersiz.
const ALLOWED = Object.freeze({
  empty: ['filling'],
  filling: ['fresh', 'empty'],
  fresh: ['stale', 'evicted'],
  stale: ['fresh', 'filling', 'evicted'],
  evicted: ['empty'],
});

export class MediaCacheStateMachine {
  #state = CacheState.EMPTY;

  get state() { return this.#state; }

  /** @returns {boolean} geçersiz geçişte false — durum DEĞİŞMEZ */
  transition(to) {
    if (!ALLOWED[this.#state]?.includes(to)) return false;
    this.#state = to;
    return true;
  }

  // §3: stale → fresh YALNIZ doğrulama başarılıysa; başarısızsa yeniden dolum.
  onRevalidate(ok) { return this.transition(ok ? CacheState.FRESH : CacheState.FILLING); }
}
```

**Not:** `empty → fresh` geçişi tabloda yoktur (taze girdi her zaman `filling`'den geçer);
`evicted` yalnız `empty`'ye döner — kapsam §3 geçiş kuralıyla aynıdır.

---

## Ör-05 — Cache API sarmalayıcı (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `media-cache-store.js` (tasarım) |
| Amaç | Tarayıcı önbellek deposunu (Cache API) anahtar/varyant disiplinli sarmalayıcıya çevirmek |
| Kanıt | `media-cache-kapsam.md:§1a-2,§3,§2/3` · `media-cache-web-arastirma.md:MC-4,MC-8,MC-9` |
| Durum | TASARIM İDDİASI — sınır: **offline** çevrimdışı kullanılabilirliğin sahibidir (kapsam §2/3); bu sarmalayıcı yalnız **hızlandırma** içindir |

```javascript
// media-cache-store.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

export class MediaCacheStore {
  #cacheName;
  #stateMachine;   // Ör-04

  constructor(cacheName, stateMachine) {
    this.#cacheName = cacheName; this.#stateMachine = stateMachine;   // machine: Ör-04
  }

  // varyant anahtarı (MC-4): varyantlar arasında gövde paylaşılmaz.
  static #key(url, variant = '') {
    return new Request(`${url}${variant ? `?v=${encodeURIComponent(variant)}` : ''}`);
  }

  async get(url, variant = '') {
    if (!('caches' in globalThis)) return null;          // depo yok → MISS davranışı
    const store = await caches.open(this.#cacheName);
    const hit = await store.match(MediaCacheStore.#key(url, variant));
    if (hit) { this.#stateMachine?.transition('fresh'); return hit; }   // §3 HIT
    this.#stateMachine?.transition('filling');                          // §3 MISS → dolum
    return null;
  }

  async put(url, response, variant = '') {
    if (!('caches' in globalThis) || !response?.ok) return false;
    const store = await caches.open(this.#cacheName);
    await store.put(MediaCacheStore.#key(url, variant), response.clone());
    this.#stateMachine?.transition('fresh');             // §3 S-2 → S-3
    return true;
  }

  async evict(url, variant = '') {
    if (!('caches' in globalThis)) return false;
    const store = await caches.open(this.#cacheName);
    const gone = await store.delete(MediaCacheStore.#key(url, variant));
    this.#stateMachine?.transition('evicted');           // §3 S-5 → empty
    return gone;
  }

  // §5/1 kota ölçümü (MC-8, MC-9): değerler çağrıda okunur; eşik/sayı YAZILMAZ.
  async usage() {
    return navigator.storage?.estimate ? await navigator.storage.estimate() : null;
  }
}
```

---

## §3 — Özet

| # | Örnek | Kapsam karşılığı | Üretim mü? |
|---|---|---|---|
| 1 | Cache-Control setter + ETag/304 (PHP 8.4) | §1a-3 · §1a-4 · §3 (`fresh`/`stale`) | Hayır — TASARIM İDDİASI |
| 2 | PDO prepared önbellek kaydı (PHP 8.4) | §1a-1 · §3 · §5/1-§5/2 | Hayır — TASARIM İDDİASI |
| 3 | Byte-range okuma iskeleti (PHP 8.4) | §2/2 · §5/5 (sunum tarafı) | Hayır — TASARIM İDDİASI |
| 4 | 5 durumlu makine (Vanilla JS ES6+) | §3 (empty·filling·fresh·stale·evicted) | Hayır — TASARIM İDDİASI |
| 5 | Cache API sarmalayıcı (Vanilla JS ES6+) | §1a-2 · §2/3 · §5/1 | Hayır — TASARIM İDDİASI |

**Üretim öncesi çözülecekler:** §6/1 (boyut/TTL politikası) · §6/2 (temizleme stratejisi
LRU/LFU/ARC) · §6/4 (offline sahipliği — kesinleştirilmez).

## §4 — Revizyon

| Tarih | Revizyon | Authority |
|---|---|---|
| 2026-10-16 | İlk örnek seti — PHP 8.4 (strict_types · prepared · cache-control setter · range okuma) + Vanilla JS ES6+ (durum makinesi · Cache API sarmalayıcı); eşik/sayı uydurulmadı | Bayram Ali / Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
