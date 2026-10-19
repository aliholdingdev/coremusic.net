---
node_id: K014-media/media-cache/ornekler
node_type: module
status: draft
parent_id: K014-media/media-cache
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.9"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/media-cache — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.9
> (medya önbelleği · boyut/TTL politikası ⚠️) + §22 (doğrulanmış standartlar: strateji
> tablosu LRU/LFU/ARC/TTL §22.5 · `Cache-Control: public, max-age=31536000, immutable` +
> ETag §22.5 · RFC 9110 §22.7). **Dikkat:** saf LRU, ses akışında (tekil dinleme) isabet
> oranı ≈ 0'dır (§22.5) — bu yüzden katalog/artwork için ARC (scan-resistant) önerilir;
> parça önbelleği TTL ile ele alınır. ARC blokları kavramsal basitleştirmedir (gölge
> listeler B1/B2 yalnız anahtar tutar; üretim implementasyonu O(1) liste indeksi ister).
> Kod yorumları İngilizcedir; dosya salt-okunur vault içindir, kaynak koda dokunmaz.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK (kademe-2 design node) |
| 2 | Dayanak | media-master §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag) — uydurma standart/kütüphane yasak |
| 3 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala (§2-§16) |
| 4 | Politika | ARC (T1 recency · T2 frequency · B1/B2 hayalet) — scan-resistant; katalog/artwork için tercih (§22.5) |
| 5 | Kontrast | saf LRU — audio stream'de isabet ≈ 0 (§22.5); parça önbelleğinde ölçüt DEĞİL |
| 6 | TTL | zamanlayıcı süpürme — süre eşiği ⚠️ UNKNOWN (Vault Steward §16/3); örneklerde uydurma sabit eşik YOK |
| 7 | Revalidasyon | `If-None-Match` → 304 (ETag §22.5 · RFC 9110 §22.7); 304'te gövde yok, süre tazelenir |
| 8 | Kanıt | her blok §9.9 + §22.5/§22.7 satır referansı taşır; dil-dahili std yapılar kullanılır |
| 9 | Değişiklik | yok — bu dosya yalnız tasarımı gösterir |

## §2 PHP

ARC (kavramsal): T1/T2 + B1/B2 hayalet admit/evict kararı; yanında LRU kontrast, TTL süpürme ve ETag revalidasyonu.

```php
<?php
declare(strict_types=1);

/**
 * K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
 * + ETag revalidation (TASARIM ÖNERİSİ).
 * Grounded in media-master.md §9.9 (media-cache · boyut/TTL ⚠️)
 * + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag · LRU audio-stream hit ≈ 0)
 * + §22.7 (RFC 9110 — If-None-Match → 304 temeli).
 * Repo'da bu sınıfın kodu YOK — illustrative design example.
 */

final class ArcCache
{
    /** @var array<string, array{value: mixed, expiresAt: int}> recency list (T1). */
    private array $t1 = [];
    /** @var array<string, array{value: mixed, expiresAt: int}> frequency list (T2). */
    private array $t2 = [];
    /** @var array<string, true> ghost recency (B1) — yalnız anahtar (hayalet). */
    private array $b1 = [];
    /** @var array<string, true> ghost frequency (B2) — yalnız anahtar (hayalet). */
    private array $b2 = [];
    /** T1 hedef boyutu — adaptasyon parametresi (ARC çekirdeği). */
    private int $p = 0;

    public function __construct(private int $c, private \Closure $loader)
    {
    }

    /** Oku/admit kararı — scan-resistant: tarama T2'yi kirletmez (§22.5). */
    public function get(string $key): mixed
    {
        if (isset($this->t1[$key])) {            // CASE 1: recency hit -> T2'ye terfi
            $e = $this->t1[$key];
            unset($this->t1[$key]);
            $this->t2[$key] = $e;
            return $e['value'];
        }
        if (isset($this->t2[$key])) {            // CASE 1: frequency hit -> tazele
            $e = $this->t2[$key];
            unset($this->t2[$key]);
            $this->t2[$key] = $e;
            return $e['value'];
        }
        if (isset($this->b1[$key])) {            // CASE 2: hayalet isabet (recency) -> frekansa kay
            $this->p = min($this->c, $this->p + max(1, intdiv(count($this->b2), max(1, count($this->b1)))));
            unset($this->b1[$key]);
            $this->replace();
            $this->t2[$key] = ($this->loader)($key);
            return $this->t2[$key]['value'];
        }
        if (isset($this->b2[$key])) {            // CASE 3: hayalet isabet (frequency) -> recency'ye kay
            $this->p = max(0, $this->p - max(1, intdiv(count($this->b1), max(1, count($this->b2)))));
            unset($this->b2[$key]);
            $this->replace();
            $this->t1[$key] = ($this->loader)($key);
            return $this->t1[$key]['value'];
        }
        // CASE 4: tam isabetsizlik -> admit
        if (count($this->t1) + count($this->b1) === $this->c) {
            if (count($this->t1) < $this->c) {   // T1'de yer aç (hayalet B1'i kırp + L1 kurban)
                $this->evictFirst($this->b1);
                $this->replace();
            } else {                             // |T1| = c: yalnız hayalet B2 kırpılır
                $this->evictFirst($this->b2);
            }
        } elseif (count($this->t1) + count($this->b2) >= $this->c) {
            $this->evictFirst($this->b2);        // hayalet + L2 kurban
            $this->replace();
        }
        $this->t1[$key] = ($this->loader)($key); // tek geçişlik dolaşım T1'de ölür (scan-resistant)
        return $this->t1[$key]['value'];
    }

    /** REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2. */
    private function replace(): void
    {
        if (count($this->t1) >= max(1, $this->p) && $this->t1 !== []) {
            $this->evictVictim($this->t1, $this->b1);
        } elseif ($this->t2 !== []) {
            $this->evictVictim($this->t2, $this->b2);
        }
    }

    /** @param array<string, mixed> $map @param array<string, true> $ghost */
    private function evictVictim(array &$map, array &$ghost): void
    {
        $victim = array_key_first($map);          // LRU = en eski eklenen (PHP dizi sırası)
        $ghost[$victim] = true;                   // hayalet: yalnız anahtar kalır
        unset($map[$victim]);
    }

    /** @param array<string, true> $ghost */
    private function evictFirst(array &$ghost): void
    {
        $first = array_key_first($ghost);
        if ($first !== null) {
            unset($ghost[$first]);
        }
    }

    /** TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward). */
    public function ttlSweep(int $now): void
    {
        foreach (['t1', 't2'] as $list) {
            foreach ($this->$list as $k => $e) {
                if ($e['expiresAt'] <= $now) {
                    unset($this->$list[$k]);
                }
            }
        }
    }
}

/**
 * ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — master §22.5 + §22.7).
 * 304'te gövde YOK, önbellek süresi tazelenir.
 */
function revalidate(string $currentEtag, ?string $ifNoneMatch): int
{
    if ($ifNoneMatch === null || $ifNoneMatch === '') {
        return 200;
    }
    foreach (explode(',', $ifNoneMatch) as $candidate) {
        $candidate = trim(preg_replace('/^W\//', '', $candidate));
        if ($candidate === '*' || hash_equals($currentEtag, $candidate)) {
            return 304; // içerik değişmemiş — gövde yok
        }
    }
    return 200;
}

/**
 * LRU kontrast — basit ama scan'e dayanıksız. Ses akışında (tekil dinleme)
 * isbet oranı ≈ 0 (§22.5) -> parça önbelleğinde ölçüt olarak KULLANILMAZ.
 */
final class LruCache
{
    /** @var array<string, mixed> */
    private array $map = [];

    public function __construct(private int $c)
    {
    }

    public function get(string $key): mixed
    {
        if (!isset($this->map[$key])) {
            return null;
        }
        $v = $this->map[$key];
        unset($this->map[$key]);
        $this->map[$key] = $v; // en başa al (tazele)
        return $v;
    }

    public function put(string $key, mixed $value): void
    {
        unset($this->map[$key]);
        $this->map[$key] = $value;
        if (count($this->map) > $this->c) {
            unset($this->map[array_key_first($this->map)]); // LRU kurban
        }
    }
}
```

## §3 JavaScript

`Map` (ekleme sıralı) ile ARC çekirdeği; LRU kontrast, TTL süpürme ve `If-None-Match` revalidasyonu.

```js
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Map insertion order = LRU order.

class ArcCache {
  constructor(capacity, load) {
    this.c = capacity; this.p = 0; this.load = load;
    this.t1 = new Map();  // recency (value + expiresAt)
    this.t2 = new Map();  // frequency
    this.b1 = new Set();  // ghost recency — key only
    this.b2 = new Set();  // ghost frequency — key only
  }

  // Scan-resistant admit/evict kararı — tarama T2'yi kirletmez (§22.5).
  get(key) {
    if (this.t1.has(key)) {                     // CASE 1: recency hit -> T2'ye terfi
      const e = this.t1.get(key); this.t1.delete(key); this.t2.set(key, e); return e.value;
    }
    if (this.t2.has(key)) {                     // CASE 1: frequency hit -> tazele
      const e = this.t2.get(key); this.t2.delete(key); this.t2.set(key, e); return e.value;
    }
    if (this.b1.has(key)) {                     // CASE 2: hayalet isabet -> frekansa kay
      this.p = Math.min(this.c, this.p + Math.max(1, Math.floor(this.b2.size / Math.max(1, this.b1.size))));
      this.b1.delete(key); this.replace();
      const e = this.load(key); this.t2.set(key, e); return e.value;
    }
    if (this.b2.has(key)) {                     // CASE 3: hayalet isabet -> recency'ye kay
      this.p = Math.max(0, this.p - Math.max(1, Math.floor(this.b1.size / Math.max(1, this.b2.size))));
      this.b2.delete(key); this.replace();
      const e = this.load(key); this.t1.set(key, e); return e.value;
    }
    // CASE 4: tam isabetsizlik -> admit
    if (this.t1.size + this.b1.size === this.c) {
      if (this.t1.size < this.c) { this.evictFirst(this.b1); this.replace(); }
      else { this.evictFirst(this.b2); }
    } else if (this.t1.size + this.b2.size >= this.c) {
      this.evictFirst(this.b2); this.replace();
    }
    const e = this.load(key); this.t1.set(key, e); return e.value; // tek geçişlik dolaşım T1'de ölür
  }

  // REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
  replace() {
    if (this.t1.size >= Math.max(1, Math.floor(this.p)) && this.t1.size > 0) this.evictVictim(this.t1, this.b1);
    else if (this.t2.size > 0) this.evictVictim(this.t2, this.b2);
  }

  evictVictim(map, ghost) {          // LRU = ilk anahtar (Map ekleme sırası)
    const victim = map.keys().next().value;
    ghost.add(victim); map.delete(victim);
  }

  evictFirst(set) { const k = set.values().next().value; if (k !== undefined) set.delete(k); }

  // TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
  ttlSweep(nowMs) {
    for (const map of [this.t1, this.t2]) {
      for (const [k, e] of map) if (e.expiresAt <= nowMs) map.delete(k);
    }
  }
}

// ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — master §22.5 + §22.7).
// 304'te gövde YOK, önbellek süresi tazelenir.
function revalidate(currentEtag, ifNoneMatch) {
  if (!ifNoneMatch) return 200;
  for (const raw of ifNoneMatch.split(',')) {
    const candidate = raw.trim().replace(/^W\//, '');
    if (candidate === '*' || candidate === currentEtag) return 304;
  }
  return 200;
}

// LRU kontrast — basit ama scan'e dayanıksız: ses akışında isabet ≈ 0 (§22.5),
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
class LruCache {
  constructor(capacity) { this.c = capacity; this.map = new Map(); }
  get(key) {
    if (!this.map.has(key)) return undefined;
    const v = this.map.get(key); this.map.delete(key); this.map.set(key, v); return v;
  }
  put(key, value) {
    this.map.delete(key); this.map.set(key, value);
    if (this.map.size > this.c) this.map.delete(this.map.keys().next().value); // LRU kurban
  }
}
```

## §4 TypeScript

Tip güvenli ARC sözleşmesi: `CacheEntry`, `ArcLists` ve `RevalResult` union'ları + admit/evict mantığı.

```ts
// K014-media/media-cache — typed ARC contract + TTL sweep + ETag revalidation
// (TASARIM ÖNERİSİ). Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL ·
// immutable/ETag; LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example.

export interface CacheEntry<V> {
  value: V;
  expiresAt: number; // epoch ms — TTL süresi (eşik ⚠️ UNKNOWN — Vault Steward §16/3)
}

export type RevalResult =
  | { tag: 'NOT_MODIFIED' }   // 304: gövde yok, süre tazelenir
  | { tag: 'FRESH'; etag: string }; // 200: gövde ile

export class ArcCache<V> {
  private readonly t1 = new Map<string, CacheEntry<V>>(); // recency
  private readonly t2 = new Map<string, CacheEntry<V>>(); // frequency
  private readonly b1 = new Set<string>();                // ghost recency (key only)
  private readonly b2 = new Set<string>();                // ghost frequency (key only)
  private p = 0;                                          // T1 hedef boyutu (adaptasyon)

  constructor(private readonly c: number, private readonly load: (k: string) => CacheEntry<V>) {}

  /** Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez. */
  get(key: string): V {
    const t1hit = this.t1.get(key);
    if (t1hit) { this.t1.delete(key); this.t2.set(key, t1hit); return t1hit.value; } // CASE 1 -> terfi
    const t2hit = this.t2.get(key);
    if (t2hit) { this.t2.delete(key); this.t2.set(key, t2hit); return t2hit.value; } // CASE 1 -> tazele

    if (this.b1.has(key)) { // CASE 2: hayalet isabet -> frekansa kay
      this.p = Math.min(this.c, this.p + Math.max(1, Math.floor(this.b2.size / Math.max(1, this.b1.size))));
      this.b1.delete(key); this.replace();
      const e = this.load(key); this.t2.set(key, e); return e.value;
    }
    if (this.b2.has(key)) { // CASE 3: hayalet isabet -> recency'ye kay
      this.p = Math.max(0, this.p - Math.max(1, Math.floor(this.b1.size / Math.max(1, this.b2.size))));
      this.b2.delete(key); this.replace();
      const e = this.load(key); this.t1.set(key, e); return e.value;
    }

    // CASE 4: tam isabetsizlik -> admit
    if (this.t1.size + this.b1.size === this.c) {
      if (this.t1.size < this.c) { this.evictFirst(this.b1); this.replace(); }
      else { this.evictFirst(this.b2); }
    } else if (this.t1.size + this.b2.size >= this.c) {
      this.evictFirst(this.b2); this.replace();
    }
    const e = this.load(key); this.t1.set(key, e); return e.value; // tek geçişlik dolaşım T1'de ölür
  }

  /** REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2. */
  private replace(): void {
    if (this.t1.size > 0 && this.t1.size >= Math.max(1, Math.floor(this.p))) this.evictVictim(this.t1, this.b1);
    else if (this.t2.size > 0) this.evictVictim(this.t2, this.b2);
  }

  private evictVictim(map: Map<string, CacheEntry<V>>, ghost: Set<string>): void {
    const victim = map.keys().next().value as string; // LRU = ilk anahtar (ekleme sırası)
    ghost.add(victim); map.delete(victim);
  }

  private evictFirst(set: Set<string>): void {
    const k = set.values().next().value;
    if (k !== undefined) set.delete(k);
  }

  /** TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward). */
  ttlSweep(nowMs: number): void {
    for (const map of [this.t1, this.t2]) {
      for (const [k, e] of [...map]) if (e.expiresAt <= nowMs) map.delete(k);
    }
  }
}

/** ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7). */
export function revalidate(currentEtag: string, ifNoneMatch?: string): RevalResult {
  if (!ifNoneMatch) return { tag: 'FRESH', etag: currentEtag };
  for (const raw of ifNoneMatch.split(',')) {
    const candidate = raw.trim().replace(/^W\//, '');
    if (candidate === '*' || candidate === currentEtag) return { tag: 'NOT_MODIFIED' }; // gövde yok
  }
  return { tag: 'FRESH', etag: currentEtag };
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5): parça önbelleğinde KULLANILMAZ.
export class LruCache<V> {
  private readonly map = new Map<string, V>();
  constructor(private readonly c: number) {}
  get(key: string): V | undefined {
    const v = this.map.get(key);
    if (v === undefined) return undefined;
    this.map.delete(key); this.map.set(key, v); return v;
  }
  put(key: string, value: V): void {
    this.map.delete(key); this.map.set(key, value);
    if (this.map.size > this.c) this.map.delete(this.map.keys().next().value as string); // LRU kurban
  }
}
```

## §5 C++

STL ile ARC: `std::list` (sıra) + `std::unordered_map` (indeks); hayaletler `std::unordered_set`; TTL/ETag yardımcıları.

```cpp
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. HTTP istemcisi: taşıma K010-network.
#include <cstdint>
#include <list>
#include <string>
#include <unordered_map>
#include <unordered_set>

namespace cm::media_cache {

struct Entry {
    std::string value;
    std::int64_t expires_at_ms = 0; // TTL — eşik ⚠️ UNKNOWN (master §16/3)
};

// Tek yönlü bağlı liste indeksi: O(1) LRU terfi/kurban (ARC çekirdeği).
template <typename V>
class ArcCache {
public:
    using Loader = std::function<Entry(const std::string&)>;

    ArcCache(std::size_t c, Loader load)
        : c_(c), load_(std::move(load)) {}

    // Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez.
    std::string get(const std::string& key) {
        if (auto it = idx1_.find(key); it != idx1_.end()) {        // CASE 1: recency hit -> T2'ye terfi
            t2_.splice(t2_.end(), t1_, it->second); idx1_.erase(it);
            return tail2_.back()->second.value;
        }
        if (auto it = idx2_.find(key); it != idx2_.end()) {        // CASE 1: frequency hit -> tazele
            t2_.splice(t2_.end(), t2_, it->second);
            return tail2_.back()->second.value;
        }
        if (b1_.erase(key) > 0) {                                  // CASE 2: hayalet isabet -> frekansa kay
            p_ = std::min(c_, p_ + std::max<std::size_t>(1, b2_.size() / std::max<std::size_t>(1, b1_.size() + 1)));
            replace();
            auto e = load_(key); t2_.emplace_front(key, e); idx2_[key] = t2_.begin();
            return e.value;
        }
        if (b2_.erase(key) > 0) {                                  // CASE 3: hayalet isabet -> recency'ye kay
            p_ = p_ > std::max<std::size_t>(1, b1_.size() / std::max<std::size_t>(1, b2_.size() + 1))
                     ? p_ - 1 : 0;
            replace();
            auto e = load_(key); t1_.emplace_front(key, e); idx1_[key] = t1_.begin();
            return e.value;
        }
        // CASE 4: tam isabetsizlik -> admit
        if (t1_.size() + b1_.size() == c_) {
            if (t1_.size() < c_) { if (!b1_.empty()) b1_.erase(b1_.begin()); replace(); }
            else { if (!b2_.empty()) b2_.erase(b2_.begin()); }
        } else if (t1_.size() + b2_.size() >= c_) {
            if (!b2_.empty()) b2_.erase(b2_.begin()); replace();
        }
        auto e = load_(key); t1_.emplace_front(key, e); idx1_[key] = t1_.begin(); // tek geçişlik dolaşım T1'de ölür
        return e.value;
    }

    // REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
    void replace() {
        if (!t1_.empty() && t1_.size() >= std::max<std::size_t>(1, p_)) {
            b1_.insert(t1_.back().first); idx1_.erase(t1_.back().first); t1_.pop_back();
        } else if (!t2_.empty()) {
            b2_.insert(t2_.back().first); idx2_.erase(t2_.back().first); t2_.pop_back();
        }
    }

    // TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
    void ttl_sweep(std::int64_t now_ms) {
        sweep_list(t1_, idx1_, now_ms);
        sweep_list(t2_, idx2_, now_ms);
    }

private:
    using List = std::list<std::pair<std::string, Entry>>;
    using Index = std::unordered_map<std::string, typename List::iterator>;

    void sweep_list(List& l, Index& idx, std::int64_t now_ms) {
        for (auto it = l.begin(); it != l.end();) {
            if (it->second.expires_at_ms <= now_ms) { idx.erase(it->first); it = l.erase(it); }
            else { ++it; }
        }
    }

    std::size_t c_;
    std::size_t p_ = 0;
    Loader load_;
    List t1_, t2_;                 // recency / frequency
    Index idx1_, idx2_;            // anahtar -> liste düğümü (O(1) terfi)
    std::unordered_set<std::string> b1_, b2_; // hayalet: yalnız anahtar
};

// ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
// 304'te gövde YOK, önbellek süresi tazelenir.
inline int revalidate(const std::string& current_etag, const std::string& if_none_match) {
    if (if_none_match.empty()) return 200;
    std::size_t pos = 0;
    while (pos <= if_none_match.size()) {
        auto comma = if_none_match.find(',', pos);
        auto token = if_none_match.substr(pos, comma == std::string::npos ? std::string::npos : comma - pos);
        // trim + "W/" zayıf etiket ön eki
        while (!token.empty() && token.front() == ' ') token.erase(token.begin());
        while (!token.empty() && token.back() == ' ')  token.pop_back();
        if (token.rfind("W/", 0) == 0) token = token.substr(2);
        if (token == "*" || token == current_etag) return 304;
        if (comma == std::string::npos) break;
        pos = comma + 1;
    }
    return 200;
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
template <typename V>
class LruCache {
public:
    explicit LruCache(std::size_t c) : c_(c) {}
    void put(const std::string& k, V v) {
        if (idx_.count(k)) { order_.erase(idx_[k]); idx_.erase(k); }
        order_.emplace_front(k, std::move(v)); idx_[k] = order_.begin();
        if (order_.size() > c_) { idx_.erase(order_.back().first); order_.pop_back(); } // LRU kurban
    }
private:
    std::size_t c_;
    std::list<std::pair<std::string, V>> order_;
    std::unordered_map<std::string, typename std::list<std::pair<std::string, V>>::iterator> idx_;
};

} // namespace cm::media_cache
```

## §6 C#

`Dictionary` + `LinkedList` ile ARC; `HttpResponseMessage` başlığı üzerinden `If-None-Match` → 304 akışı.

```csharp
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Taşıma: K010-network.
using System;
using System.Collections.Generic;
using System.Net;
using System.Net.Http;

namespace CoreMusic.MediaCache;

public sealed record CacheEntry(string Value, DateTimeOffset ExpiresAt); // TTL: eşik ⚠️ UNKNOWN (§16/3)

public sealed class ArcCache
{
    private readonly int _c;
    private readonly Func<string, CacheEntry> _load;
    private readonly LinkedList<string> _t1 = new();   // recency (LRU = First)
    private readonly LinkedList<string> _t2 = new();   // frequency
    private readonly Dictionary<string, LinkedListNode<string>> _idx1 = new();
    private readonly Dictionary<string, LinkedListNode<string>> _idx2 = new();
    private readonly HashSet<string> _b1 = new();      // ghost recency (key only)
    private readonly HashSet<string> _b2 = new();      // ghost frequency (key only)
    private readonly Dictionary<string, CacheEntry> _values = new();
    private int _p;                                    // T1 hedef boyutu (adaptasyon)

    public ArcCache(int capacity, Func<string, CacheEntry> loader)
    {
        _c = capacity; _load = loader;
    }

    /// <summary>Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez.</summary>
    public string Get(string key)
    {
        if (_idx1.TryGetValue(key, out var n1))       // CASE 1: recency hit -> T2'ye terfi
        {
            _t1.Remove(n1); _idx1.Remove(key);
            _idx2[key] = _t2.AddLast(n1.Value);
            return _values[key].Value;
        }
        if (_idx2.TryGetValue(key, out var n2))       // CASE 1: frequency hit -> tazele
        {
            _t2.Remove(n2); _idx2[key] = _t2.AddLast(n2.Value);
            return _values[key].Value;
        }
        if (_b1.Remove(key))                          // CASE 2: hayalet isabet -> frekansa kay
        {
            _p = Math.Min(_c, _p + Math.Max(1, _b2.Count / Math.Max(1, _b1.Count)));
            Replace();
            var e = _load(key); _values[key] = e; _idx2[key] = _t2.AddFirst(key);
            return e.Value;
        }
        if (_b2.Remove(key))                          // CASE 3: hayalet isabet -> recency'ye kay
        {
            _p = Math.Max(0, _p - Math.Max(1, _b1.Count / Math.Max(1, _b2.Count)));
            Replace();
            var e = _load(key); _values[key] = e; _idx1[key] = _t1.AddFirst(key);
            return e.Value;
        }
        // CASE 4: tam isabetsizlik -> admit
        if (_t1.Count + _b1.Count == _c)
        {
            if (_t1.Count < _c) { if (_b1.Count > 0) _b1.Remove(FirstOf(_b1)); Replace(); }
            else { if (_b2.Count > 0) _b2.Remove(FirstOf(_b2)); }
        }
        else if (_t1.Count + _b2.Count >= _c)
        {
            if (_b2.Count > 0) _b2.Remove(FirstOf(_b2)); Replace();
        }
        var entry = _load(key); _values[key] = entry; _idx1[key] = _t1.AddFirst(key); // tek geçişlik dolaşım T1'de ölür
        return entry.Value;
    }

    /// <summary>REPLACE: T1 payını (&gt;= p) tutuyorsa LRU T1 → B1; değilse LRU T2 → B2.</summary>
    private void Replace()
    {
        if (_t1.Count > 0 && _t1.Count >= Math.Max(1, _p)) EvictVictim(_t1, _idx1, _b1);
        else if (_t2.Count > 0) EvictVictim(_t2, _idx2, _b2);
    }

    private void EvictVictim(LinkedList<string> list, Dictionary<string, LinkedListNode<string>> idx, HashSet<string> ghost)
    {
        var victim = list.Last!.Value;                 // LRU = son düğüm
        ghost.Add(victim); list.RemoveLast(); idx.Remove(victim);
    }

    private static string FirstOf(HashSet<string> set)
    {
        using var e = set.GetEnumerator(); e.MoveNext(); return e.Current; // hayalet kuyruğu: ekleme sırası (kavramsal)
    }

    /// <summary>TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).</summary>
    public void TtlSweep(DateTimeOffset now)
    {
        Sweep(_t1, _idx1, now); Sweep(_t2, _idx2, now);
    }

    private void Sweep(LinkedList<string> list, Dictionary<string, LinkedListNode<string>> idx, DateTimeOffset now)
    {
        for (var n = list.First; n is not null;)
        {
            var next = n.Next;
            if (_values[n.Value].ExpiresAt <= now) { _values.Remove(n.Value); idx.Remove(n.Value); list.Remove(n); }
            n = next;
        }
    }
}

public static class ETagRevalidation
{
    /// <summary>
    /// ETag revalidation: If-None-Match → 304 (RFC 9110 semantics — §22.5 + §22.7).
    /// 304'te gövde YOK, önbellek süresi tazelenir.
    /// </summary>
    public static HttpStatusCode Revalidate(string currentEtag, HttpRequestMessage req)
    {
        if (!req.Headers.TryGetValues("If-None-Match", out var values)) return HttpStatusCode.OK;
        foreach (var header in values)
        {
            foreach (var raw in header.Split(','))
            {
                var candidate = raw.Trim();
                if (candidate.StartsWith("W/", StringComparison.Ordinal)) candidate = candidate[2..];
                if (candidate == "*" || candidate == currentEtag) return HttpStatusCode.NotModified; // gövde yok
            }
        }
        return HttpStatusCode.OK;
    }
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
public sealed class LruCache<T>
{
    private readonly int _c;
    private readonly Dictionary<string, LinkedListNode<KeyValuePair<string, T>>> _map = new();
    private readonly LinkedList<KeyValuePair<string, T>> _order = new();
    public LruCache(int capacity) { _c = capacity; }
    public T? Get(string key)
    {
        if (!_map.TryGetValue(key, out var n)) return default;
        _order.Remove(n); _order.AddLast(n); return n.Value.Value; // tazele
    }
    public void Put(string key, T value)
    {
        if (_map.TryGetValue(key, out var old)) _order.Remove(old);
        var n = _order.AddLast(new KeyValuePair<string, T>(key, value));
        _map[key] = n;
        if (_map.Count > _c)
        {
            var victim = _order.First!;                    // LRU kurban = kuyruk başı
            _map.Remove(victim.Value.Key);
            _order.RemoveFirst();
        }
    }
}
```

## §7 SQL

Kalıcılık tarafı şema taslağı — NOT: kalıcı şema K007-data'ya aittir (master §2.2); ARC'nin kendisi bellek içidir (TASARIM ÖNERİSİ). Bu blok TTL süpürme sorgusu + ETag revalidasyon sorgusu + LRU kontrast indeksini gösterir.

```sql
-- K014-media/media-cache — önbellek kalıcılığı şema taslağı + TTL sweep + ETag revalidation
-- (TASARIM ÖNERİSİ). Grounded in media-master.md §9.9 (boyut/TTL ⚠️ UNKNOWN)
-- + §22.5 (TTL · ETag · immutable; LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — 304 temeli).
-- NOT: kalıcı şema K007-data'ya aittir (master §2.2) — bu blok taslak/örnek niteliğindedir.
-- ARC (T1/T2/B1/B2) bellek içi politikadır; kalıcı katman yalnız girdi + ETag + TTL taşır.

CREATE TABLE media_cache_entry (
    cache_key    VARCHAR(512)  NOT NULL,           -- "artwork:1200:release-42" gibi kümelenmiş anahtar
    kind         ENUM('CATALOG','ARTWORK','TRACK') NOT NULL,
    content_ref  VARCHAR(1024) NOT NULL,           -- içerik kaynağı (dosya yolu / K013 URL)
    etag         CHAR(64)      NOT NULL,           -- içerik hash (master §22.5)
    expires_at   TIMESTAMP     NOT NULL,           -- TTL — süre eşiği ⚠️ UNKNOWN (Vault Steward §16/3)
    last_access  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cache_key),
    -- LRU kontrast indeksi: parça önbelleğinde LRU ölçüt DEĞİL (hit ≈ 0 — §22.5); yalnız telemetri
    KEY idx_lru_contrast (kind, last_access)
) ENGINE=InnoDB;

-- ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
-- İstemci "If-None-Match: <etag>" gönderir; satır varsa ve etag eşitse 304: gövde YOK, süre tazelenir.
SELECT expires_at
  FROM media_cache_entry
 WHERE cache_key = :cache_key
   AND etag      = :if_none_match;   -- eşleşme => 304 Not Modified (gövde yok)

-- TTL süpürme (zamanlayıcı temizlik — master §22.5 "TTL"): süresi dolan girdileri sil.
DELETE FROM media_cache_entry
 WHERE expires_at <= CURRENT_TIMESTAMP;

-- Süresi dolan sanat eseri kademesini bul (raporlama/monitoring — ölçüm master §15/4).
SELECT kind, COUNT(*) AS expired_count
  FROM media_cache_entry
 WHERE expires_at <= CURRENT_TIMESTAMP
 GROUP BY kind;
```

## §8 Go

`container/list` + `map` indeksi ile ARC; `net/http` başlığı üzerinden `If-None-Match` → 304 akışı.

```go
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package mediacache

import (
	"container/list"
	"net/http"
	"strings"
	"time"
)

type Entry struct {
	Value     string
	ExpiresAt time.Time // TTL — eşik ⚠️ UNKNOWN (master §16/3)
}

type arcCache struct {
	c    int
	p    int // T1 hedef boyutu (adaptasyon)
	load func(string) Entry

	t1, t2     *list.List               // recency / frequency: front = MRU, back = LRU
	idx1, idx2 map[string]*list.Element // anahtar -> düğüm (O(1) terfi)
	b1, b2     map[string]struct{}      // hayalet: yalnız anahtar
	values     map[string]Entry
}

func newArc(capacity int, load func(string) Entry) *arcCache {
	return &arcCache{
		c: capacity, load: load,
		t1: list.New(), t2: list.New(),
		idx1: map[string]*list.Element{}, idx2: map[string]*list.Element{},
		b1: map[string]struct{}{}, b2: map[string]struct{}{},
		values: map[string]Entry{},
	}
}

// Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez.
func (a *arcCache) Get(key string) string {
	if el, ok := a.idx1[key]; ok { // CASE 1: recency hit -> T2'ye terfi
		a.t1.Remove(el); delete(a.idx1, key)
		a.idx2[key] = a.t2.PushBack(key)
		return a.values[key].Value
	}
	if el, ok := a.idx2[key]; ok { // CASE 1: frequency hit -> tazele
		a.t2.Remove(el); a.idx2[key] = a.t2.PushBack(key)
		return a.values[key].Value
	}
	if _, ok := a.b1[key]; ok { // CASE 2: hayalet isabet -> frekansa kay
		delete(a.b1, key)
		a.p = min(a.c, a.p+max(1, len(a.b2)/max(1, len(a.b1)+1)))
		a.replace()
		e := a.load(key); a.values[key] = e; a.idx2[key] = a.t2.PushFront(key)
		return e.Value
	}
	if _, ok := a.b2[key]; ok { // CASE 3: hayalet isabet -> recency'ye kay
		delete(a.b2, key)
		if d := max(1, len(a.b1)/max(1, len(a.b2)+1)); a.p > d { a.p -= d } else { a.p = 0 }
		a.replace()
		e := a.load(key); a.values[key] = e; a.idx1[key] = a.t1.PushFront(key)
		return e.Value
	}
	// CASE 4: tam isabetsizlik -> admit
	if a.t1.Len()+len(a.b1) == a.c {
		if a.t1.Len() < a.c { a.popGhost(a.b1); a.replace() } else { a.popGhost(a.b2) }
	} else if a.t1.Len()+len(a.b2) >= a.c {
		a.popGhost(a.b2); a.replace()
	}
	e := a.load(key); a.values[key] = e; a.idx1[key] = a.t1.PushFront(key) // tek geçişlik dolaşım T1'de ölür
	return e.Value
}

// REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
func (a *arcCache) replace() {
	if a.t1.Len() > 0 && a.t1.Len() >= max(1, a.p) {
		a.evictVictim(a.t1, a.idx1, a.b1)
	} else if a.t2.Len() > 0 {
		a.evictVictim(a.t2, a.idx2, a.b2)
	}
}

func (a *arcCache) evictVictim(l *list.List, idx map[string]*list.Element, ghost map[string]struct{}) {
	victim := l.Back().Value.(string) // LRU = arka düğüm
	ghost[victim] = struct{}{}; l.RemoveBack(); delete(idx, victim)
}

func (a *arcCache) popGhost(ghost map[string]struct{}) {
	for k := range ghost { delete(ghost, k); return } // hayalet kuyruğu: kavramsal FIFO
}

// TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
func (a *arcCache) TtlSweep(now time.Time) {
	sweep := func(l *list.List, idx map[string]*list.Element) {
		for el := l.Front(); el != nil; {
			next := el.Next(); key := el.Value.(string)
			if a.values[key].ExpiresAt.Before(now) || a.values[key].ExpiresAt.Equal(now) {
				delete(a.values, key); delete(idx, key); l.Remove(el)
			}
			el = next
		}
	}
	sweep(a.t1, a.idx1); sweep(a.t2, a.idx2)
}

// ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
// 304'te gövde YOK, önbellek süresi tazelenir.
func revalidate(currentETag string, r *http.Request) int {
	header := r.Header.Get("If-None-Match")
	if header == "" { return http.StatusOK }
	for _, raw := range strings.Split(header, ",") {
		candidate := strings.TrimSpace(raw)
		candidate = strings.TrimPrefix(candidate, "W/")
		if candidate == "*" || candidate == currentETag { return http.StatusNotModified }
	}
	return http.StatusOK
}

func min(a, b int) int { if a < b { return a }; return b }
func max(a, b int) int { if a > b { return a }; return b }

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
type lruCache struct {
	c int; m map[string]*list.Element; l *list.List
}

func newLRU(c int) *lruCache { return &lruCache{c: c, m: map[string]*list.Element{}, l: list.New()} }

func (c *lruCache) Get(k string) (string, bool) {
	el, ok := c.m[k]; if !ok { return "", false }
	c.l.MoveToFront(el); return el.Value.(string), true
}

func (c *lruCache) Put(k, v string) {
	if el, ok := c.m[k]; ok { c.l.Remove(el) }
	c.m[k] = c.l.PushFront(k + "=" + v)
	if c.l.Len() > c.c { // LRU kurban
		victim := c.l.Back().Value.(string); c.l.RemoveBack(); delete(c.m, strings.SplitN(victim, "=", 2)[0])
	}
}
```

## §9 Rust

`Result`/tip-güvenli ARC — `HashMap` + `VecDeque` sıra; `Option` ile hayalet/kurban seçimi; panic yok.

```rust
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ). Panic yok — Result/Option akışı.
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example.

use std::collections::{HashMap, VecDeque};
use std::time::{SystemTime, UNIX_EPOCH};

#[derive(Clone)]
pub struct Entry {
    pub value: String,
    pub expires_at_ms: u64, // TTL — eşik ⚠️ UNKNOWN (master §16/3)
}

pub struct ArcCache {
    c: usize,
    p: usize, // T1 hedef boyutu (adaptasyon)
    t1: VecDeque<String>,            // recency: back = LRU
    t2: VecDeque<String>,            // frequency: back = LRU
    b1: VecDeque<String>,            // ghost recency (key only)
    b2: VecDeque<String>,            // ghost frequency (key only)
    values: HashMap<String, Entry>,
    load: Box<dyn Fn(&str) -> Entry>,
}

impl ArcCache {
    pub fn new(c: usize, load: Box<dyn Fn(&str) -> Entry>) -> Self {
        Self { c, p: 0,
               t1: VecDeque::new(), t2: VecDeque::new(),
               b1: VecDeque::new(), b2: VecDeque::new(),
               values: HashMap::new(), load }
    }

    /// Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez.
    pub fn get(&mut self, key: &str) -> Option<String> {
        if let Some(pos) = self.t1.iter().position(|k| k == key) {   // CASE 1: recency hit -> T2'ye terfi
            let k = self.t1.remove(pos)?;
            self.t2.push_back(k.clone());
            return self.values.get(&k).map(|e| e.value.clone());
        }
        if let Some(pos) = self.t2.iter().position(|k| k == key) {   // CASE 1: frequency hit -> tazele
            let k = self.t2.remove(pos)?;
            self.t2.push_back(k.clone());
            return self.values.get(&k).map(|e| e.value.clone());
        }
        if let Some(pos) = self.b1.iter().position(|k| k == key) {   // CASE 2: hayalet isabet -> frekansa kay
            let _ = self.b1.remove(pos);
            self.p = self.c.min(self.p + 1.max(self.b2.len() / 1.max(self.b1.len() + 1)));
            self.replace();
            let e = (self.load)(key);
            self.values.insert(key.to_string(), e.clone());
            self.t2.push_back(key.to_string());
            return Some(e.value);
        }
        if let Some(pos) = self.b2.iter().position(|k| k == key) {   // CASE 3: hayalet isabet -> recency'ye kay
            let _ = self.b2.remove(pos);
            let d = 1.max(self.b1.len() / 1.max(self.b2.len() + 1));
            self.p = self.p.saturating_sub(d);
            self.replace();
            let e = (self.load)(key);
            self.values.insert(key.to_string(), e.clone());
            self.t1.push_back(key.to_string());
            return Some(e.value);
        }
        // CASE 4: tam isabetsizlik -> admit
        if self.t1.len() + self.b1.len() == self.c {
            if self.t1.len() < self.c { self.pop_ghost(&mut self.b1.clone()); self.replace(); }
            else { self.pop_ghost(&mut self.b2.clone()); }
        } else if self.t1.len() + self.b2.len() >= self.c {
            self.pop_ghost(&mut self.b2.clone());
            self.replace();
        }
        let e = (self.load)(key);                                    // tek geçişlik dolaşım T1'de ölür
        self.values.insert(key.to_string(), e.clone());
        self.t1.push_back(key.to_string());
        Some(e.value)
    }

    /// REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
    fn replace(&mut self) {
        if !self.t1.is_empty() && self.t1.len() >= 1.max(self.p) {
            if let Some(victim) = self.t1.pop_back() {               // LRU = back
                self.b1.push_back(victim);
            }
        } else if let Some(victim) = self.t2.pop_back() {
            self.b2.push_back(victim);
        }
    }

    fn pop_ghost(&mut self, ghost: &mut VecDeque<String>) {
        ghost.pop_front(); // hayalet kuyruğu: kavramsal FIFO
    }

    /// TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
    pub fn ttl_sweep(&mut self, now_ms: u64) {
        let expired: Vec<String> = self.values.iter()
            .filter(|(_, e)| e.expires_at_ms <= now_ms)
            .map(|(k, _)| k.clone()).collect();
        for k in expired {
            self.values.remove(&k);
            self.t1.retain(|x| x != &k);
            self.t2.retain(|x| x != &k);
        }
    }
}

/// ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
/// 304'te gövde YOK, önbellek süresi tazelenir. `None` = başlık yok (200).
pub fn revalidate(current_etag: &str, if_none_match: Option<&str>) -> Option<u16> {
    let header = if_none_match?;
    for raw in header.split(',') {
        let candidate = raw.trim().trim_start_matches("W/");
        if candidate == "*" || candidate == current_etag {
            return Some(304);                                        // gövde yok
        }
    }
    Some(200)
}

pub fn now_ms() -> u64 {
    SystemTime::now().duration_since(UNIX_EPOCH).map(|d| d.as_millis() as u64).unwrap_or(0)
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
pub struct LruCache {
    c: usize,
    order: VecDeque<String>,
    values: HashMap<String, String>,
}

impl LruCache {
    pub fn new(c: usize) -> Self {
        Self { c, order: VecDeque::new(), values: HashMap::new() }
    }
    pub fn put(&mut self, k: &str, v: &str) {
        self.values.insert(k.to_string(), v.to_string());
        self.order.push_back(k.to_string());
        if self.order.len() > self.c {
            if let Some(victim) = self.order.pop_front() {           // LRU kurban
                self.values.remove(&victim);
            }
        }
    }
}
```

## §10 Python

`dict` (ekleme sıralı) ile ARC; `time`/`hashlib` yardımı yok — ETag revalidasyonu basit eşitlik; std kütüphane yeterli.

```python
# K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
# + ETag revalidation (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
# LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
# Repo'da bu modülün kodu YOK — illustrative design example. Taşıma: K010-network.

from __future__ import annotations

from typing import Callable


class ArcCache:
    """ARC: T1 (recency) + T2 (frequency) + B1/B2 hayalet listeleri."""

    def __init__(self, capacity: int, loader: Callable[[str], dict]) -> None:
        self.c = capacity
        self.p = 0                      # T1 hedef boyutu (adaptasyon)
        self.t1: dict[str, dict] = {}   # recency (ekleme sıralı — LRU = ilk anahtar)
        self.t2: dict[str, dict] = {}   # frequency
        self.b1: set[str] = set()       # ghost recency (key only)
        self.b2: set[str] = set()       # ghost frequency (key only)
        self._load = loader

    def get(self, key: str):
        """Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez."""
        if key in self.t1:                                  # CASE 1: recency hit -> T2'ye terfi
            entry = self.t1.pop(key)
            self.t2[key] = entry
            return entry["value"]
        if key in self.t2:                                  # CASE 1: frequency hit -> tazele
            entry = self.t2.pop(key)
            self.t2[key] = entry
            return entry["value"]
        if key in self.b1:                                  # CASE 2: hayalet isabet -> frekansa kay
            self.p = min(self.c, self.p + max(1, len(self.b2) // max(1, len(self.b1))))
            self.b1.discard(key)
            self.replace()
            entry = self._load(key)
            self.t2[key] = entry
            return entry["value"]
        if key in self.b2:                                  # CASE 3: hayalet isabet -> recency'ye kay
            self.p = max(0, self.p - max(1, len(self.b1) // max(1, len(self.b2))))
            self.b2.discard(key)
            self.replace()
            entry = self._load(key)
            self.t1[key] = entry
            return entry["value"]
        # CASE 4: tam isabetsizlik -> admit
        if len(self.t1) + len(self.b1) == self.c:
            if len(self.t1) < self.c:
                self.pop_ghost(self.b1)
                self.replace()
            else:
                self.pop_ghost(self.b2)
        elif len(self.t1) + len(self.b2) >= self.c:
            self.pop_ghost(self.b2)
            self.replace()
        entry = self._load(key)                             # tek geçişlik dolaşım T1'de ölür
        self.t1[key] = entry
        return entry["value"]

    def replace(self) -> None:
        """REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2."""
        if self.t1 and len(self.t1) >= max(1, self.p):
            victim = next(iter(self.t1))                    # LRU = ilk anahtar
            del self.t1[victim]
            self.b1.add(victim)
        elif self.t2:
            victim = next(iter(self.t2))
            del self.t2[victim]
            self.b2.add(victim)

    @staticmethod
    def pop_ghost(ghost: set) -> None:
        if ghost:
            ghost.pop()                                     # hayalet kuyruğu: kavramsal FIFO

    def ttl_sweep(self, now: float) -> None:
        """TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward)."""
        for table in (self.t1, self.t2):
            for key in [k for k, e in table.items() if e["expires_at"] <= now]:
                del table[key]


def revalidate(current_etag: str, if_none_match: str | None) -> int:
    """ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
    304'te gövde YOK, önbellek süresi tazelenir."""
    if not if_none_match:
        return 200
    for raw in if_none_match.split(","):
        candidate = raw.strip()
        if candidate.startswith("W/"):
            candidate = candidate[2:]                        # zayıf etiket
        if candidate == "*" or candidate == current_etag:
            return 304                                       # gövde yok
    return 200


class LruCache:
    """LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
    parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC)."""

    def __init__(self, capacity: int) -> None:
        self.c = capacity
        self.map: dict = {}

    def put(self, key, value) -> None:
        self.map.pop(key, None)
        self.map[key] = value
        if len(self.map) > self.c:
            del self.map[next(iter(self.map))]               # LRU kurban
```

## §11 Java

`LinkedHashMap` (sıralı) tabanlı ARC; `HttpURLConnection` başlığı üzerinden `If-None-Match` → 304.

```java
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package com.coremusic.mediacache;

import java.net.HttpURLConnection;
import java.util.Iterator;
import java.util.LinkedHashMap;
import java.util.LinkedHashSet;
import java.util.Map;

public final class ArcCache {
    /** TTL — eşik ⚠️ UNKNOWN (master §16/3 → Vault Steward). */
    record Entry(String value, long expiresAtMs) {}

    private final int c;
    private int p;                                       // T1 hedef boyutu (adaptasyon)
    private final LinkedHashMap<String, Entry> t1 = new LinkedHashMap<>(); // recency (LRU = ilk)
    private final LinkedHashMap<String, Entry> t2 = new LinkedHashMap<>(); // frequency
    private final LinkedHashSet<String> b1 = new LinkedHashSet<>();        // ghost recency (key only)
    private final LinkedHashSet<String> b2 = new LinkedHashSet<>();        // ghost frequency (key only)
    private final Loader loader;

    public interface Loader { Entry load(String key); }

    public ArcCache(int capacity, Loader loader) { this.c = capacity; this.loader = loader; }

    /** Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez. */
    public String get(String key) {
        if (t1.containsKey(key)) {                        // CASE 1: recency hit -> T2'ye terfi
            Entry e = t1.remove(key); t2.put(key, e); return e.value();
        }
        if (t2.containsKey(key)) {                        // CASE 1: frequency hit -> tazele
            Entry e = t2.remove(key); t2.put(key, e); return e.value();
        }
        if (b1.remove(key)) {                             // CASE 2: hayalet isabet -> frekansa kay
            p = Math.min(c, p + Math.max(1, b2.size() / Math.max(1, b1.size())));
            replace();
            Entry e = loader.load(key); t2.put(key, e); return e.value();
        }
        if (b2.remove(key)) {                             // CASE 3: hayalet isabet -> recency'ye kay
            p = Math.max(0, p - Math.max(1, b1.size() / Math.max(1, b2.size())));
            replace();
            Entry e = loader.load(key); t1.put(key, e); return e.value();
        }
        // CASE 4: tam isabetsizlik -> admit
        if (t1.size() + b1.size() == c) {
            if (t1.size() < c) { popGhost(b1); replace(); } else { popGhost(b2); }
        } else if (t1.size() + b2.size() >= c) {
            popGhost(b2); replace();
        }
        Entry e = loader.load(key);                       // tek geçişlik dolaşım T1'de ölür
        t1.put(key, e);
        return e.value();
    }

    /** REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2. */
    private void replace() {
        if (!t1.isEmpty() && t1.size() >= Math.max(1, p)) evictVictim(t1, b1);
        else if (!t2.isEmpty()) evictVictim(t2, b2);
    }

    private void evictVictim(LinkedHashMap<String, Entry> map, LinkedHashSet<String> ghost) {
        Iterator<String> it = map.keySet().iterator();
        String victim = it.next();                        // LRU = ilk anahtar
        it.remove();
        ghost.add(victim);                                // hayalet: yalnız anahtar
    }

    private static void popGhost(LinkedHashSet<String> ghost) {
        Iterator<String> it = ghost.iterator();
        if (it.hasNext()) { it.next(); it.remove(); }     // hayalet kuyruğu: kavramsal FIFO
    }

    /** TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward). */
    public void ttlSweep(long nowMs) {
        sweep(t1, nowMs); sweep(t2, nowMs);
    }

    private void sweep(LinkedHashMap<String, Entry> map, long nowMs) {
        Iterator<Map.Entry<String, Entry>> it = map.entrySet().iterator();
        while (it.hasNext()) {
            if (it.next().getValue().expiresAtMs() <= nowMs) it.remove();
        }
    }

    /** ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
     *  304'te gövde YOK, önbellek süresi tazelenir. */
    public static int revalidate(String currentEtag, HttpURLConnection conn) throws java.io.IOException {
        String header = conn.getRequestProperty("If-None-Match");
        if (header == null || header.isEmpty()) return 200;
        for (String raw : header.split(",")) {
            String candidate = raw.trim();
            if (candidate.startsWith("W/")) candidate = candidate.substring(2);
            if (candidate.equals("*") || candidate.equals(currentEtag)) return 304;
        }
        return 200;
    }
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
final class LruCache {
    private final int c;
    private final LinkedHashMap<String, String> map = new LinkedHashMap<>();
    LruCache(int capacity) { this.c = capacity; }
    String get(String key) {
        String v = map.remove(key);
        if (v == null) return null;
        map.put(key, v);                                  // tazele
        return v;
    }
    void put(String key, String value) {
        map.remove(key); map.put(key, value);
        if (map.size() > c) {
            Iterator<String> it = map.keySet().iterator();
            it.next(); it.remove();                       // LRU kurban
        }
    }
}
```

## §12 Swift

`Dictionary` + `Array` sıra kuyruğu ile ARC; `URLSession` başlığı üzerinden `If-None-Match` → 304.

```swift
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Taşıma: K010-network.
import Foundation

struct CacheEntry {
    let value: String
    let expiresAtMs: Int64 // TTL — eşik ⚠️ UNKNOWN (master §16/3)
}

final class ArcCache {
    private let c: Int
    private var p = 0                                     // T1 hedef boyutu (adaptasyon)
    private var t1: [String: CacheEntry] = [:]            // recency
    private var t2: [String: CacheEntry] = [:]            // frequency
    private var orderT1: [String] = []                    // LRU sırası (ilk = LRU)
    private var orderT2: [String] = []
    private var b1: Set<String> = []                      // ghost recency (key only)
    private var b2: Set<String> = []                      // ghost frequency (key only)
    private let load: (String) -> CacheEntry

    init(capacity: Int, load: @escaping (String) -> CacheEntry) {
        self.c = capacity; self.load = load
    }

    /// Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez.
    func get(_ key: String) -> String {
        if let e = t1.removeValue(forKey: key) {          // CASE 1: recency hit -> T2'ye terfi
            dropFromOrder(&orderT1, key); t2[key] = e; orderT2.append(key); return e.value
        }
        if let e = t2.removeValue(forKey: key) {          // CASE 1: frequency hit -> tazele
            dropFromOrder(&orderT2, key); t2[key] = e; orderT2.append(key); return e.value
        }
        if b1.remove(key) != nil {                        // CASE 2: hayalet isabet -> frekansa kay
            p = min(c, p + max(1, b2.count / max(1, b1.count)))
            replace()
            let e = load(key); t2[key] = e; orderT2.append(key); return e.value
        }
        if b2.remove(key) != nil {                        // CASE 3: hayalet isabet -> recency'ye kay
            p = max(0, p - max(1, b1.count / max(1, b2.count)))
            replace()
            let e = load(key); t1[key] = e; orderT1.append(key); return e.value
        }
        // CASE 4: tam isabetsizlik -> admit
        if t1.count + b1.count == c {
            if t1.count < c { popGhost(&b1); replace() } else { popGhost(&b2) }
        } else if t1.count + b2.count >= c {
            popGhost(&b2); replace()
        }
        let e = load(key)                                 // tek geçişlik dolaşım T1'de ölür
        t1[key] = e; orderT1.append(key)
        return e.value
    }

    /// REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
    private func replace() {
        if !orderT1.isEmpty && orderT1.count >= max(1, p) { evictVictim(&t1, &orderT1, &b1) }
        else if !orderT2.isEmpty { evictVictim(&t2, &orderT2, &b2) }
    }

    private func evictVictim(_ map: inout [String: CacheEntry], _ order: inout [String], _ ghost: inout Set<String>) {
        let victim = order.removeFirst()                  // LRU = kuyruk başı
        map.removeValue(forKey: victim)
        ghost.insert(victim)                              // hayalet: yalnız anahtar
    }

    private func popGhost(_ ghost: inout Set<String>) {
        if let first = ghost.first { ghost.remove(first) } // hayalet kuyruğu: kavramsal FIFO
    }

    private func dropFromOrder(_ order: inout [String], _ key: String) {
        order.removeAll { $0 == key }
    }

    /// TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
    func ttlSweep(nowMs: Int64) {
        let expired = (t1.filter { $0.value.expiresAtMs <= nowMs }).map { $0.key }
        for k in expired { t1.removeValue(forKey: k); dropFromOrder(&orderT1, k) }
        let expiredT2 = (t2.filter { $0.value.expiresAtMs <= nowMs }).map { $0.key }
        for k in expiredT2 { t2.removeValue(forKey: k); dropFromOrder(&orderT2, k) }
    }
}

/// ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
/// 304'te gövde YOK, önbellek süresi tazelenir.
func revalidate(currentEtag: String, request: URLRequest) -> Int {
    guard let header = request.value(forHTTPHeaderField: "If-None-Match"), !header.isEmpty else { return 200 }
    for raw in header.split(separator: ",") {
        var candidate = raw.trimmingCharacters(in: .whitespaces)
        if candidate.hasPrefix("W/") { candidate = String(candidate.dropFirst(2)) }
        if candidate == "*" || candidate == currentEtag { return 304 }
    }
    return 200
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
final class LruCache {
    private let c: Int
    private var map: [String: String] = [:]
    private var order: [String] = []
    init(capacity: Int) { self.c = capacity }
    func put(_ key: String, _ value: String) {
        map.removeValue(forKey: key); order.removeAll { $0 == key }
        map[key] = value; order.append(key)
        if order.count > c {                              // LRU kurban
            let victim = order.removeFirst(); map.removeValue(forKey: victim)
        }
    }
}
```

## §13 Kotlin

`LinkedHashMap` + `LinkedHashSet` ile ARC; sealed sonuç yerine `Int` durum kodu (200/304) reval akışı.

```kotlin
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package com.coremusic.mediacache

data class Entry(val value: String, val expiresAtMs: Long) // TTL — eşik ⚠️ UNKNOWN (§16/3)

class ArcCache(private val c: Int, private val loader: (String) -> Entry) {
    private var p = 0                                      // T1 hedef boyutu (adaptasyon)
    private val t1 = LinkedHashMap<String, Entry>()        // recency (LRU = ilk)
    private val t2 = LinkedHashMap<String, Entry>()        // frequency
    private val b1 = LinkedHashSet<String>()               // ghost recency (key only)
    private val b2 = LinkedHashSet<String>()               // ghost frequency (key only)

    /** Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez. */
    fun get(key: String): String {
        t1.remove(key)?.let { e -> t2[key] = e; return e.value }   // CASE 1: recency hit -> terfi
        t2.remove(key)?.let { e -> t2[key] = e; return e.value }   // CASE 1: freq hit -> tazele
        if (b1.remove(key)) {                                       // CASE 2: hayalet isabet -> frekansa kay
            p = minOf(c, p + maxOf(1, b2.size / maxOf(1, b1.size)))
            replace(); val e = loader(key); t2[key] = e; return e.value
        }
        if (b2.remove(key)) {                                       // CASE 3: hayalet isabet -> recency'ye kay
            p = maxOf(0, p - maxOf(1, b1.size / maxOf(1, b2.size)))
            replace(); val e = loader(key); t1[key] = e; return e.value
        }
        // CASE 4: tam isabetsizlik -> admit
        if (t1.size + b1.size == c) {
            if (t1.size < c) { popGhost(b1); replace() } else { popGhost(b2) }
        } else if (t1.size + b2.size >= c) {
            popGhost(b2); replace()
        }
        val e = loader(key)                                          // tek geçişlik dolaşım T1'de ölür
        t1[key] = e
        return e.value
    }

    /** REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2. */
    private fun replace() {
        if (t1.isNotEmpty() && t1.size >= maxOf(1, p)) evictVictim(t1, b1)
        else if (t2.isNotEmpty()) evictVictim(t2, b2)
    }

    private fun evictVictim(map: LinkedHashMap<String, Entry>, ghost: LinkedHashSet<String>) {
        val victim = map.keys.first()                               // LRU = ilk anahtar
        map.remove(victim); ghost.add(victim)                       // hayalet: yalnız anahtar
    }

    private fun popGhost(ghost: LinkedHashSet<String>) {
        ghost.firstOrNull()?.let { ghost.remove(it) }               // hayalet kuyruğu: kavramsal FIFO
    }

    /** TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward). */
    fun ttlSweep(nowMs: Long) {
        listOf(t1, t2).forEach { map ->
            map.entries.removeAll { it.value.expiresAtMs <= nowMs }
        }
    }

    companion object {
        /** ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
         *  304'te gövde YOK, önbellek süresi tazelenir. */
        fun revalidate(currentEtag: String, ifNoneMatch: String?): Int {
            if (ifNoneMatch.isNullOrBlank()) return 200
            for (raw in ifNoneMatch.split(',')) {
                val candidate = raw.trim().removePrefix("W/")
                if (candidate == "*" || candidate == currentEtag) return 304
            }
            return 200
        }
    }
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
class LruCache(private val c: Int) {
    private val map = LinkedHashMap<String, String>()
    fun get(key: String): String? = map.remove(key)?.also { map[key] = it }
    fun put(key: String, value: String) {
        map.remove(key); map[key] = value
        if (map.size > c) map.remove(map.keys.first())              // LRU kurban
    }
}
```

## §14 Ruby

`Hash` (ekleme sıralı) ile ARC; `Net::HTTP` başlığı üzerinden `If-None-Match` → 304.

```ruby
# K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
# + ETag revalidation (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
# LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
# Repo'da bu sınıfın kodu YOK — illustrative design example. Taşıma: K010-network.

require 'set'

class ArcCache
  # TTL — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
  Entry = Struct.new(:value, :expires_at, keyword_init: true)

  def initialize(capacity, &loader)
    @c = capacity
    @p = 0                    # T1 hedef boyutu (adaptasyon)
    @t1 = {}                  # recency (Ruby Hash = ekleme sıralı; LRU = ilk anahtar)
    @t2 = {}                  # frequency
    @b1 = Set.new              # ghost recency (key only)
    @b2 = Set.new              # ghost frequency (key only)
    @loader = loader
  end

  # Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez.
  def get(key)
    if (e = @t1.delete(key))     # CASE 1: recency hit -> T2'ye terfi
      @t2[key] = e
      return e.value
    end
    if (e = @t2.delete(key))     # CASE 1: frequency hit -> tazele
      @t2[key] = e
      return e.value
    end
    if @b1.delete?(key)          # CASE 2: hayalet isabet -> frekansa kay
      @p = [@c, @p + [@b2.size / [@b1.size, 1].max, 1].max].min
      replace
      e = @loader.call(key); @t2[key] = e; return e.value
    end
    if @b2.delete?(key)          # CASE 3: hayalet isabet -> recency'ye kay
      @p = [@p - [@b1.size / [@b2.size, 1].max, 1].max, 0].max
      replace
      e = @loader.call(key); @t1[key] = e; return e.value
    end
    # CASE 4: tam isabetsizlik -> admit
    if @t1.size + @b1.size == @c
      if @t1.size < @c
        pop_ghost(@b1); replace
      else
        pop_ghost(@b2)
      end
    elsif @t1.size + @b2.size >= @c
      pop_ghost(@b2); replace
    end
    e = @loader.call(key)       # tek geçişlik dolaşım T1'de ölür
    @t1[key] = e
    e.value
  end

  private

  # REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
  def replace
    if !@t1.empty? && @t1.size >= [@p, 1].max
      victim = @t1.keys.first   # LRU = ilk anahtar
      @t1.delete(victim); @b1.add(victim)   # hayalet: yalnız anahtar
    elsif !@t2.empty?
      victim = @t2.keys.first
      @t2.delete(victim); @b2.add(victim)
    end
  end

  def pop_ghost(ghost)
    ghost.delete(ghost.first) unless ghost.empty? # hayalet kuyruğu: kavramsal FIFO
  end

  public

  # TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward).
  def ttl_sweep(now)
    [@t1, @t2].each do |table|
      table.delete_if { |_, e| e.expires_at <= now }
    end
  end

  # ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
  # 304'te gövde YOK, önbellek süresi tazelenir.
  def self.revalidate(current_etag, request)
    header = request['If-None-Match']
    return 200 if header.nil? || header.empty?
    header.split(',').each do |raw|
      candidate = raw.strip.sub(%r{\AW/}, '')
      return 304 if candidate == '*' || candidate == current_etag
    end
    200
  end
end

# LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
# parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
class LruCache
  def initialize(capacity)
    @c = capacity
    @map = {}
  end

  def get(key)
    return nil unless @map.key?(key)
    value = @map.delete(key)
    @map[key] = value           # tazele
  end

  def put(key, value)
    @map.delete(key)
    @map[key] = value
    @map.shift if @map.size > @c # LRU kurban (Hash sıralı: ilk eleman)
  end
end
```

## §15 Elixir

Functional ARC — `%{}` + `:queue` sıra hayaletleri; `{:ok, _}` / `{:error, _}` yerine basit sonuç atomları; `:crypto` yok, ETag eşitliği `==`.

```elixir
# K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
# + ETag revalidation (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
# LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
# Repo'da bu modülün kodu YOK — illustrative design example. Taşıma: K010-network.

defmodule CoreMusic.MediaCache.Arc do
  @moduledoc """
  ARC: T1 (recency) + T2 (frequency) + B1/B2 hayalet listeleri — scan-resistant (§22.5).
  Yalnız illustrative design: O(1) üretim indeksi yerine kavramsal listeler.
  """

  defstruct c: 0, p: 0, t1: %{}, t2: %{}, b1: [], b2: [], loader: nil
  # t1/t2: %{key => %{value:, expires_at:}} · b1/b2: anahtar listeleri (hayalet, kavramsal sıra)

  @type t :: %__MODULE__{}

  @spec new(non_neg_integer, (String.t() -> map())) :: t()
  def new(capacity, loader), do: %__MODULE__{c: capacity, loader: loader}

  @doc "Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez."
  @spec get(t(), String.t()) :: {t(), String.t()}
  def get(%__MODULE__{} = s, key) do
    cond do
      Map.has_key?(s.t1, key) ->                               # CASE 1: recency hit -> T2'ye terfi
        {entry, t1} = Map.pop(s.t1, key)
        {%{s | t1: t1, t2: Map.put(s.t2, key, entry)}, entry.value}

      Map.has_key?(s.t2, key) ->                               # CASE 1: frequency hit -> tazele
        {entry, t2} = Map.pop(s.t2, key)
        {%{s | t2: Map.put(t2, key, entry)}, entry.value}

      key in s.b1 ->                                           # CASE 2: hayalet isabet -> frekansa kay
        p = min(s.c, s.p + max(1, div(length(s.b2), max(1, length(s.b1)))))
        s = %{s | p: p, b1: List.delete(s.b1, key)}
        s = replace(s)
        entry = s.loader.(key)
        {%{s | t2: Map.put(s.t2, key, entry)}, entry.value}

      key in s.b2 ->                                           # CASE 3: hayalet isabet -> recency'ye kay
        p = max(0, s.p - max(1, div(length(s.b1), max(1, length(s.b2)))))
        s = %{s | p: p, b2: List.delete(s.b2, key)}
        s = replace(s)
        entry = s.loader.(key)
        {%{s | t1: Map.put(s.t1, key, entry)}, entry.value}

      true ->                                                  # CASE 4: tam isabetsizlik -> admit
        s =
          cond do
            map_size(s.t1) + length(s.b1) == s.c ->
              if map_size(s.t1) < s.c do
                s |> pop_ghost(:b1) |> replace()
              else
                pop_ghost(s, :b2)
              end

            map_size(s.t1) + length(s.b2) >= s.c ->
              s |> pop_ghost(:b2) |> replace()

            true -> s
          end

        entry = s.loader.(key)                                 # tek geçişlik dolaşım T1'de ölür
        {%{s | t1: Map.put(s.t1, key, entry)}, entry.value}
    end
  end

  # REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2.
  defp replace(%__MODULE__{} = s) do
    cond do
      map_size(s.t1) > 0 and map_size(s.t1) >= max(1, s.p) ->
        {victim, t1} = Map.pop(s.t1)              # kavramsal LRU: arbitrary ama deterministik
        %{s | t1: t1, b1: s.b1 ++ [victim]}       # hayalet: yalnız anahtar

      map_size(s.t2) > 0 ->
        {victim, t2} = Map.pop(s.t2)
        %{s | t2: t2, b2: s.b2 ++ [victim]}

      true -> s
    end
  end

  defp pop_ghost(%__MODULE__{} = s, :b1), do: %{s | b1: tl_or_empty(s.b1)}
  defp pop_ghost(%__MODULE__{} = s, :b2), do: %{s | b2: tl_or_empty(s.b2)}
  defp tl_or_empty([]), do: []
  defp tl_or_empty([_ | rest]), do: rest          # hayalet kuyruğu: kavramsal FIFO

  @doc "TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward)."
  @spec ttl_sweep(t(), integer()) :: t()
  def ttl_sweep(%__MODULE__{} = s, now) do
    drop = fn table -> Map.reject(table, fn {_k, e} -> e.expires_at <= now end) end
    %{s | t1: drop.(s.t1), t2: drop.(s.t2)}
  end

  @doc """
  ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
  304'te gövde YOK, önbellek süresi tazelenir.
  """
  @spec revalidate(String.t(), String.t() | nil) :: 200 | 304
  def revalidate(current_etag, if_none_match) when if_none_match in [nil, ""], do: 200
  def revalidate(current_etag, if_none_match) do
    if_none_match
    |> String.split(",")
    |> Enum.any?(fn raw ->
      candidate = raw |> String.trim() |> String.replace_prefix("W/", "")
      candidate == "*" or candidate == current_etag
    end)
    |> if(do: 304, else: 200)
  end
end

# LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
# parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
defmodule CoreMusic.MediaCache.Lru do
  defstruct c: 0, map: %{}, order: []

  def new(capacity), do: %__MODULE__{c: capacity}

  def put(%__MODULE__{} = s, key, value) do
    order = List.delete(s.order, key)
    map = Map.put(s.map, key, value)

    if length(order) >= s.c do               # LRU kurban: kuyruk başı
      [victim | rest] = order
      %__MODULE__{s | map: Map.delete(map, victim), order: rest ++ [key]}
    else
      %__MODULE__{s | map: map, order: order ++ [key]}
    end
  end
end
```

## §16 Scala

`mutable.LinkedHashMap` + `LinkedHashSet` ile ARC; `Either` tabanlı reval sonucu; `java.time` epoch-ms TTL.

```scala
// K014-media/media-cache — ARC (scan-resistant) + LRU kontrast + TTL sweep
// + ETag revalidation (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.9 + §22.5 (LRU/LFU/ARC/TTL · immutable/ETag;
// LRU audio-stream hit ≈ 0) + §22.7 (RFC 9110 — If-None-Match → 304).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package com.coremusic.mediacache

import scala.collection.mutable

object ArcCache {
  final case class Entry(value: String, expiresAtMs: Long) // TTL — eşik ⚠️ UNKNOWN (§16/3)

  sealed trait RevalResult
  object RevalResult {
    case object NotModified extends RevalResult            // 304: gövde yok, süre tazelenir
    case class Fresh(etag: String) extends RevalResult     // 200: gövde ile
  }

  final class Arc(capacity: Int, loader: String => Entry) {
    private var p: Int = 0                                 // T1 hedef boyutu (adaptasyon)
    private val t1 = new mutable.LinkedHashMap[String, Entry]() // recency (LRU = ilk)
    private val t2 = new mutable.LinkedHashMap[String, Entry]() // frequency
    private val b1 = new mutable.LinkedHashSet[String]()        // ghost recency (key only)
    private val b2 = new mutable.LinkedHashSet[String]()        // ghost frequency (key only)

    /** Scan-resistant admit/evict kararı (§22.5): tarama T2'yi kirletmez. */
    def get(key: String): String =
      t1.remove(key) match {
        case Some(e) =>                                 // CASE 1: recency hit -> T2'ye terfi
          t2.put(key, e); e.value
        case None =>
          t2.remove(key) match {
            case Some(e) =>                             // CASE 1: frequency hit -> tazele
              t2.put(key, e); e.value
            case None =>
              if (b1.remove(key)) {                     // CASE 2: hayalet isabet -> frekansa kay
                p = math.min(capacity, p + math.max(1, b2.size / math.max(1, b1.size)))
                replace()
                val e = loader(key); t2.put(key, e); e.value
              } else if (b2.remove(key)) {              // CASE 3: hayalet isabet -> recency'ye kay
                p = math.max(0, p - math.max(1, b1.size / math.max(1, b2.size)))
                replace()
                val e = loader(key); t1.put(key, e); e.value
              } else {                                  // CASE 4: tam isabetsizlik -> admit
                if (t1.size + b1.size == capacity) {
                  if (t1.size < capacity) { popGhost(b1); replace() } else popGhost(b2)
                } else if (t1.size + b2.size >= capacity) {
                  popGhost(b2); replace()
                }
                val e = loader(key)                     // tek geçişlik dolaşım T1'de ölür
                t1.put(key, e)
                e.value
              }
          }
      }

    /** REPLACE: T1 payını (>= p) tutuyorsa LRU T1 -> B1; değilse LRU T2 -> B2. */
    private def replace(): Unit = {
      if (t1.nonEmpty && t1.size >= math.max(1, p)) evictVictim(t1, b1)
      else if (t2.nonEmpty) evictVictim(t2, b2)
    }

    private def evictVictim(map: mutable.LinkedHashMap[String, Entry],
                            ghost: mutable.LinkedHashSet[String]): Unit = {
      val victim = map.head._1                              // LRU = ilk anahtar
      map.remove(victim); ghost.add(victim)                 // hayalet: yalnız anahtar
    }

    private def popGhost(ghost: mutable.LinkedHashSet[String]): Unit = {
      ghost.headOption.foreach(ghost.remove)                // hayalet kuyruğu: kavramsal FIFO
    }

    /** TTL süpürme — süre eşiği ⚠️ UNKNOWN (master §16/3 → Vault Steward). */
    def ttlSweep(nowMs: Long): Unit = {
      t1.filterInPlace((_, e) => e.expiresAtMs > nowMs)
      t2.filterInPlace((_, e) => e.expiresAtMs > nowMs)
    }
  }

  /**
   * ETag revalidation: If-None-Match -> 304 (RFC 9110 semantics — §22.5 + §22.7).
   * 304'te gövde YOK, önbellek süresi tazelenir.
   */
  def revalidate(currentEtag: String, ifNoneMatch: Option[String]): RevalResult =
    ifNoneMatch match {
      case None => RevalResult.Fresh(currentEtag)
      case Some(header) =>
        header.split(',').map(_.trim.stripPrefix("W/")).find(c => c == "*" || c == currentEtag) match {
          case Some(_) => RevalResult.NotModified            // gövde yok
          case None    => RevalResult.Fresh(currentEtag)
        }
    }
}

// LRU kontrast — scan'e dayanıksız; ses akışında isabet ≈ 0 (§22.5):
// parça önbelleğinde ölçüt olarak KULLANILMAZ (katalog/artwork → ARC).
final class LruCache(capacity: Int) {
  private val map = new mutable.LinkedHashMap[String, String]()
  def get(key: String): Option[String] =
    map.remove(key).map { v => map.put(key, v); v }         // tazele
  def put(key: String, value: String): Unit = {
    map.remove(key); map.put(key, value)
    if (map.size > capacity) map.remove(map.head._1)        // LRU kurban
  }
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.9 | vault | media-cache rol/sınır/iddata (boyut/TTL ⚠️ UNKNOWN) |
| 2 | K014-media/media-master.md §22.5 | vault | strateji tablosu LRU/LFU/ARC/TTL + `Cache-Control: immutable` + ETag başlıkları |
| 3 | K014-media/media-master.md §22.4 | vault | üç kademe artwork (250/500/1200 px CAA) — `immutable` cache sunumu |
| 4 | K014-media/media-master.md §12/3 · §16/3 | vault | boyut/TTL eşik kararı → Vault Steward (⚠️ UNKNOWN) |
| 5 | K014-media/media-master.md §13/5 | vault | önbellek TTL test senaryosu |
| 6 | K014-media/media-master.md §15/4 | vault | önbellek isabet oranı (hit/miss) ölçüm kalemi — eşik ⚠️ UNKNOWN |
| 7 | RFC 9110 (STD 97) | master §22.7 kaynak listesi | `If-None-Match` → `304 Not Modified` revalidasyon temeli |
| 8 | K014-media/media-cache/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.9 + §22.5/§22.7. ARC blokları kavramsal
> basitleştirmedir; boyut/TTL eşikleri uydurulmamıştır (⚠️ UNKNOWN — Vault Steward §16/3).

**Authority:** Bayram Ali / Vault Steward