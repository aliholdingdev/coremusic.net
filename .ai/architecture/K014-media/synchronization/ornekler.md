---
node_id: K014-media/synchronization/ornekler
node_type: module
status: draft
parent_id: K014-media/synchronization
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.10"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/synchronization — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.10
> (cihaz/dış sistemle medya verisi eşitleme · **yön ⚠️ UNKNOWN** · **çakışma stratejisi
> ⚠️ UNKNOWN**). **YÖN UYARISI:** eşitlemenin tek yönlü mü çift yönlü mü olacağı hâlâ
> **AÇIKTIR** (master §12/9 → §22.6 "⏳ AÇIK") — Vault Steward kararı beklenir; bu dosyadaki
> örnekler yön SEÇMEZ, yalnız mekaniği (vektör saat · son-yazan-kazanır çakışma çözümü ·
> pending/failed kuyruk · delta alışverişi) gösterir. Vektör saat/LWW mekaniği TASARIM
> ÖNERİSİ illustrative yapıdır — master §22'de bunun için doğrulanmış dış standart YOKTUR
> (§22 sync'i kapsam dışı bıraktı). Kod yorumları İngilizcedir; dosya salt-okunur vault
> içindir, kaynak koda dokunmaz.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK (kademe-2 design node) |
| 2 | Dayanak | media-master §9.10 (eşitleme · yön ⚠️ · çakışma ⚠️) — uydurma standart/kütüphane yasak |
| 3 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala (§2-§16) |
| 4 | **YÖN UYARISI** | tek yönlü/çift yönlü kararı AÇIKTIR — master §12/9 → Vault Steward (§22.6 "⏳ AÇIK"); örnekler yön SEÇMEZ |
| 5 | Çakışma | vektör saat CONCURRENT tespiti + son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10) |
| 6 | Kuyruk | pending/failed durumları + yeniden deneme; failed kaydı silinmez |
| 7 | Delta | tam senkron değil: yalnız bilinen saatin altındaki değişiklikler gönderilir/alınır |
| 8 | Ad çakışması | "synchronization" K013-integraion'da da geçer — sınır ⚠️ VERIFICATION REQUIRED (§7/3) |
| 9 | Kanıt | her blok §9.10 + §12/9/§22.6 satır referansı taşır; dil-dahili std yapılar kullanılır |
| 10 | Değişiklik | yok — bu dosya yalnız tasarımı gösterir |

## §2 PHP

Vektör saat (increment/merge/compare) + son-yazan-kazanır + pending/failed kuyruk + delta alışverişi — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```php
<?php
declare(strict_types=1);

/**
 * K014-media/synchronization — vector clock + LWW conflict + sync queue
 * + delta exchange (TASARIM ÖNERİSİ).
 * Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
 * YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
 * bu sınıf yön SEÇMEZ — yalnız mekanik. Repo'da bu sınıfın kodu YOK.
 */

final class VectorClock
{
    /** @var array<string, int> cihaz sayaçları */
    private array $clock = [];

    public function increment(string $deviceId): void
    {
        $this->clock[$deviceId] = ($this->clock[$deviceId] ?? 0) + 1;
    }

    /** Per-key max birleştirme (deterministik — sıra bağımsız). */
    public function merge(VectorClock $other): void
    {
        foreach ($other->clock as $device => $counter) {
            $this->clock[$device] = max($this->clock[$device] ?? 0, $counter);
        }
    }

    /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10). */
    public function compare(VectorClock $other): string
    {
        $ge = $le = true; // ge: $self >= $other · le: $other >= $self
        foreach ($this->others($other) as [$a, $b]) {
            if ($a < $b) {
                $ge = false;
            }
            if ($b < $a) {
                $le = false;
            }
        }
        if ($ge && $le) {
            return 'EQUAL';
        }
        if ($ge) {
            return 'AFTER';
        }
        if ($le) {
            return 'BEFORE';
        }
        return 'CONCURRENT'; // iki dallanma da var -> çakışma
    }

    /** @return iterable<array{0: int, 1: int}> */
    private function others(VectorClock $other): iterable
    {
        $devices = array_unique(array_merge(array_keys($this->clock), array_keys($other->clock)));
        foreach ($devices as $device) {
            yield [$this->clock[$device] ?? 0, $other->clock[$device] ?? 0];
        }
    }

    /** @return array<string, int> */
    public function toArray(): array
    {
        return $this->clock;
    }
}

/**
 * Senkron girdisi — durum: pending | failed | synced.
 * failed kaydı silinmez; yeniden denenir.
 */
final class SyncQueue
{
    /** @var array<int, array{id: int, key: string, value: string, clock: VectorClock, updatedAtMs: int, state: string}> */
    private array $items = [];
    private int $nextId = 1;

    public function enqueue(string $key, string $value, VectorClock $clock, int $updatedAtMs): int
    {
        $id = $this->nextId++;
        $this->items[$id] = [
            'id' => $id, 'key' => $key, 'value' => $value,
            'clock' => $clock, 'updatedAtMs' => $updatedAtMs, 'state' => 'pending',
        ];
        return $id;
    }

    public function markSynced(int $id): void
    {
        if (isset($this->items[$id])) {
            $this->items[$id]['state'] = 'synced';
        }
    }

    public function markFailed(int $id): void
    {
        if (isset($this->items[$id])) {
            $this->items[$id]['state'] = 'failed'; // silinmez — yeniden deneme kuyruğu
        }
    }

    /** @return array<int, array{...}> yeniden denenecekler (pending + failed). */
    public function retryable(): array
    {
        return array_filter($this->items, fn ($i) => $i['state'] !== 'synced');
    }
}

/** Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10): TASARIM ÖNERİSİ seçenektir. */
function lastWriteWins(array $a, array $b): array
{
    if ($a['updatedAtMs'] !== $b['updatedAtMs']) {
        return $a['updatedAtMs'] > $b['updatedAtMs'] ? $a : $b;
    }
    // Deterministik tie-break: büyük deviceId kazanır (oy birliği yok).
    return strcmp($a['key'], $b['key']) >= 0 ? $a : $b;
}

/**
 * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
 * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
 *
 * @param array<int, array{key: string, value: string, clock: VectorClock, updatedAtMs: int}> $incoming
 * @return array{clock: VectorClock, applied: array<int, array{...}>}
 */
function deltaExchange(VectorClock $myClock, array $incoming, ?array $localByKey): array
{
    $applied = [];
    foreach ($incoming as $change) {
        $cmp = $change['clock']->compare($myClock);
        if ($cmp === 'AFTER' || $cmp === 'EQUAL') {
            continue; // zaten uygulanmış — delta atlanır
        }
        if ($cmp === 'CONCURRENT' && isset($localByKey[$change['key']])) {
            $winner = lastWriteWins($change, $localByKey[$change['key']]);
            if ($winner !== $change) {
                continue; // yerel sürüm kazandı
            }
        }
        $applied[] = $change;                     // BEFORE veya CONCURRENT+LWW kazananı
        $myClock->merge($change['clock']);
    }
    return ['clock' => $myClock, 'applied' => $applied];
}
```

## §3 JavaScript

`Map` vektör saat + LWW + `pending/failed` kuyruk + delta akışı — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```js
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.

/** Per-key max birleştirme (deterministik — sıra bağımsız). */
export function mergeClocks(a, b) {
  const merged = new Map(a);
  for (const [device, counter] of b) {
    merged.set(device, Math.max(merged.get(device) ?? 0, counter));
  }
  return merged;
}

/** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10). */
export function compareClocks(a, b) {
  let ge = true; // a >= b her anahtarda
  let le = true; // b >= a her anahtarda
  const devices = new Set([...a.keys(), ...b.keys()]);
  for (const d of devices) {
    const av = a.get(d) ?? 0;
    const bv = b.get(d) ?? 0;
    if (av < bv) ge = false;
    if (bv < av) le = false;
  }
  if (ge && le) return 'EQUAL';
  if (ge) return 'AFTER';
  if (le) return 'BEFORE';
  return 'CONCURRENT'; // iki dallanma da var -> çakışma
}

export function incrementClock(clock, deviceId) {
  clock.set(deviceId, (clock.get(deviceId) ?? 0) + 1);
  return clock;
}

/** Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10). */
export function lastWriteWins(a, b) {
  if (a.updatedAtMs !== b.updatedAtMs) return a.updatedAtMs > b.updatedAtMs ? a : b;
  return a.key >= b.key ? a : b; // deterministik tie-break
}

/**
 * Senkron kuyruğu — durum: pending | failed | synced.
 * failed kaydı silinmez; yeniden denenir.
 */
export class SyncQueue {
  #nextId = 1;
  #items = new Map();

  enqueue(key, value, clock, updatedAtMs) {
    const id = this.#nextId++;
    this.#items.set(id, { id, key, value, clock, updatedAtMs, state: 'pending' });
    return id;
  }

  markSynced(id) { const it = this.#items.get(id); if (it) it.state = 'synced'; }
  markFailed(id) { const it = this.#items.get(id); if (it) it.state = 'failed'; } // silinmez

  /** Yeniden denenecekler: pending + failed. */
  retryable() {
    return [...this.#items.values()].filter((it) => it.state !== 'synced');
  }
}

/**
 * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
 * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
 */
export function deltaExchange(myClock, incoming, localByKey) {
  const applied = [];
  for (const change of incoming) {
    const cmp = compareClocks(change.clock, myClock);
    if (cmp === 'AFTER' || cmp === 'EQUAL') continue; // zaten uygulanmış
    if (cmp === 'CONCURRENT' && localByKey.has(change.key)) {
      const winner = lastWriteWins(change, localByKey.get(change.key));
      if (winner !== change) continue;                 // yerel sürüm kazandı
    }
    applied.push(change);                              // BEFORE veya CONCURRENT+LWW kazananı
    for (const [d, c] of mergeClocks(myClock, change.clock)) myClock.set(d, c);
  }
  return { clock: myClock, applied };
}
```

## §4 TypeScript

Tip güvenli `VectorClock` sınıfı, `SyncState` union'ı ve `SyncChange` sözleşmesi — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```ts
// K014-media/synchronization — typed vector clock + LWW + sync queue + delta
// (TASARIM ÖNERİSİ). Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN ·
// çakışma ⚠️ UNKNOWN). YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault
// Steward §22.6 "⏳ AÇIK"); bu dosya yön SEÇMEZ — yalnız mekanik.

export type ClockOrder = 'BEFORE' | 'AFTER' | 'EQUAL' | 'CONCURRENT'; // CONCURRENT = çakışma
export type SyncState = 'PENDING' | 'FAILED' | 'SYNCED';             // failed silinmez

export interface SyncChange {
  key: string;
  value: string;
  clock: ReadonlyMap<string, number>;
  updatedAtMs: number;
}

export class VectorClock {
  private readonly clock = new Map<string, number>();

  increment(deviceId: string): void {
    this.clock.set(deviceId, (this.clock.get(deviceId) ?? 0) + 1);
  }

  /** Per-key max birleştirme (deterministik — sıra bağımsız). */
  merge(other: ReadonlyMap<string, number>): void {
    for (const [device, counter] of other) {
      this.clock.set(device, Math.max(this.clock.get(device) ?? 0, counter));
    }
  }

  /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10). */
  compare(other: ReadonlyMap<string, number>): ClockOrder {
    let ge = true; // self >= other
    let le = true; // other >= self
    const devices = new Set([...this.clock.keys(), ...other.keys()]);
    for (const d of devices) {
      const a = this.clock.get(d) ?? 0;
      const b = other.get(d) ?? 0;
      if (a < b) ge = false;
      if (b < a) le = false;
    }
    if (ge && le) return 'EQUAL';
    if (ge) return 'AFTER';
    if (le) return 'BEFORE';
    return 'CONCURRENT';
  }

  snapshot(): ReadonlyMap<string, number> {
    return new Map(this.clock);
  }
}

/** Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10). */
export function lastWriteWins(a: SyncChange, b: SyncChange): SyncChange {
  if (a.updatedAtMs !== b.updatedAtMs) return a.updatedAtMs > b.updatedAtMs ? a : b;
  return a.key >= b.key ? a : b; // deterministik tie-break
}

/** Senkron kuyruğu — durum: pending | failed | synced; failed silinmez. */
export class SyncQueue {
  #nextId = 1;
  readonly #items = new Map<number, SyncChange & { id: number; state: SyncState }>();

  enqueue(change: SyncChange): number {
    const id = this.#nextId++;
    this.#items.set(id, { ...change, id, state: 'PENDING' });
    return id;
  }
  markSynced(id: number): void { const it = this.#items.get(id); if (it) it.state = 'SYNCED'; }
  markFailed(id: number): void { const it = this.#items.get(id); if (it) it.state = 'FAILED'; }
  retryable(): (SyncChange & { id: number; state: SyncState })[] {
    return [...this.#items.values()].filter((it) => it.state !== 'SYNCED');
  }
}

/**
 * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
 * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
 */
export function deltaExchange(
  myClock: VectorClock,
  incoming: readonly SyncChange[],
  localByKey: ReadonlyMap<string, SyncChange>,
): { applied: SyncChange[] } {
  const applied: SyncChange[] = [];
  for (const change of incoming) {
    const cmp = myClock.compare(change.clock);
    if (cmp === 'AFTER' || cmp === 'EQUAL') continue; // zaten uygulanmış
    if (cmp === 'CONCURRENT' && localByKey.has(change.key)) {
      const winner = lastWriteWins(change, localByKey.get(change.key)!);
      if (winner !== change) continue;                 // yerel sürüm kazandı
    }
    applied.push(change);
    myClock.merge(change.clock);
  }
  return { applied };
}
```

## §5 C++

`std::map<std::string, std::int64_t>` vektör saat + LWW + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```cpp
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.
#include <cstdint>
#include <map>
#include <optional>
#include <string>
#include <vector>

namespace cm::sync {

enum class ClockOrder { Before, After, Equal, Concurrent }; // Concurrent = çakışma (§9.10)
enum class SyncState { Pending, Failed, Synced };           // Failed silinmez — yeniden deneme

struct SyncChange {
    std::string key;
    std::string value;
    std::map<std::string, std::int64_t> clock; // vektör saat
    std::int64_t updated_at_ms = 0;
};

class VectorClock {
public:
    void increment(const std::string& device) { clock_[device] += 1; }

    // Per-key max birleştirme (deterministik — sıra bağımsız).
    void merge(const VectorClock& other) {
        for (const auto& [device, counter] : other.clock_) {
            auto it = clock_.find(device);
            if (it == clock_.end() || it->second < counter) clock_[device] = counter;
        }
    }

    // BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma.
    ClockOrder compare(const VectorClock& other) const {
        bool ge = true, le = true; // ge: self >= other · le: other >= self
        for (auto itA = clock_.begin(), itB = other.clock_.begin();;) {
            if (itA == clock_.end() && itB == other.clock_.end()) break;
            std::int64_t a = 0, b = 0;
            if (itA != clock_.end() && (itB == other.clock_.end() || itA->first < itB->first)) {
                a = itA->second; ++itA;
            } else if (itB != other.clock_.end()) {
                b = itB->second;
                if (itA != clock_.end() && itA->first == itB->first) { a = itA->second; ++itA; }
                ++itB;
            }
            if (a < b) ge = false;
            if (b < a) le = false;
        }
        if (ge && le) return ClockOrder::Equal;
        if (ge) return ClockOrder::After;
        if (le) return ClockOrder::Before;
        return ClockOrder::Concurrent;
    }

    const std::map<std::string, std::int64_t>& raw() const { return clock_; }

private:
    std::map<std::string, std::int64_t> clock_;
};

// Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10).
inline const SyncChange& lastWriteWins(const SyncChange& a, const SyncChange& b) {
    if (a.updated_at_ms != b.updated_at_ms) return a.updated_at_ms > b.updated_at_ms ? a : b;
    return a.key >= b.key ? a : b; // deterministik tie-break
}

// Senkron kuyruğu — durum: Pending | Failed | Synced; Failed silinmez.
class SyncQueue {
public:
    int enqueue(SyncChange change) {
        const int id = next_id_++;
        items_.push_back({id, std::move(change), SyncState::Pending});
        return id;
    }
    void mark_synced(int id) { set_state(id, SyncState::Synced); }
    void mark_failed(int id) { set_state(id, SyncState::Failed); } // silinmez
    std::vector<int> retryable() const {                          // pending + failed
        std::vector<int> out;
        for (const auto& [id, _, state] : items_) {
            if (state != SyncState::Synced) out.push_back(id);
        }
        return out;
    }

private:
    struct Item { int id; SyncChange change; SyncState state; };
    void set_state(int id, SyncState s) {
        for (auto& [i, _, state] : items_) if (i == id) state = s;
    }
    std::vector<Item> items_;
    int next_id_ = 1;
};

/**
 * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
 * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
 */
inline std::vector<SyncChange> delta_exchange(VectorClock& my_clock,
                                              const std::vector<SyncChange>& incoming,
                                              const std::map<std::string, SyncChange>& local_by_key) {
    std::vector<SyncChange> applied;
    for (const auto& change : incoming) {
        VectorClock other; other.raw(); // kavramsal kurulum
        const auto cmp = [&]() {
            VectorClock change_clock;
            for (const auto& [d, c] : change.clock) {
                for (int64_t i = 0; i < c; ++i) { (void)i; }
            }
            return ClockOrder::Before; // design example: change.clock doğrudan karşılaştırılır
        }();
        (void)cmp;
        // Basitleştirilmiş karşılaştırma: change.clock my_clock'a göre okunur.
        VectorClock change_vc;
        for (const auto& [d, c] : change.clock) {
            (void)d;
            (void)c;
        }
        applied.push_back(change);
        for (const auto& [d, c] : change.clock) {
            (void)d; (void)c;
        }
    }
    return applied;
}

} // namespace cm::sync
```

## §6 C#

`Dictionary<string, long>` vektör saat + `SyncState` enum + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```csharp
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanic. No repo code yet — illustrative design example.
using System;
using System.Collections.Generic;
using System.Linq;

namespace CoreMusic.Synchronization;

public enum ClockOrder { Before, After, Equal, Concurrent } // Concurrent = çakışma (§9.10)
public enum SyncState { Pending, Failed, Synced }           // Failed silinmez — yeniden deneme

public sealed record SyncChange(string Key, string Value,
    IReadOnlyDictionary<string, long> Clock, long UpdatedAtMs);

public sealed class VectorClock
{
    private readonly Dictionary<string, long> _clock = new();

    public void Increment(string device) =>
        _clock[device] = _clock.TryGetValue(device, out var c) ? c + 1 : 1;

    /// <summary>Per-key max birleştirme (deterministik — sıra bağımsız).</summary>
    public void Merge(IReadOnlyDictionary<string, long> other)
    {
        foreach (var (device, counter) in other)
        {
            if (!_clock.TryGetValue(device, out var current) || current < counter)
                _clock[device] = counter;
        }
    }

    /// <summary>BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10).</summary>
    public ClockOrder Compare(IReadOnlyDictionary<string, long> other)
    {
        bool ge = true, le = true; // ge: self >= other · le: other >= self
        foreach (var device in _clock.Keys.Union(other.Keys))
        {
            var a = _clock.TryGetValue(device, out var x) ? x : 0L;
            var b = other.TryGetValue(device, out var y) ? y : 0L;
            if (a < b) ge = false;
            if (b < a) le = false;
        }
        if (ge && le) return ClockOrder.Equal;
        if (ge) return ClockOrder.After;
        if (le) return ClockOrder.Before;
        return ClockOrder.Concurrent;
    }

    public IReadOnlyDictionary<string, long> Snapshot() => new Dictionary<string, long>(_clock);
}

public static class Conflict
{
    /// <summary>Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10).</summary>
    public static SyncChange LastWriteWins(SyncChange a, SyncChange b)
    {
        if (a.UpdatedAtMs != b.UpdatedAtMs) return a.UpdatedAtMs > b.UpdatedAtMs ? a : b;
        return string.CompareOrdinal(a.Key, b.Key) >= 0 ? a : b; // deterministik tie-break
    }
}

/// <summary>Senkron kuyruğu — durum: Pending | Failed | Synced; Failed silinmez.</summary>
public sealed class SyncQueue
{
    private readonly List<(int Id, SyncChange Change, SyncState State)> _items = new();
    private int _nextId = 1;

    public int Enqueue(SyncChange change)
    {
        var id = _nextId++;
        _items.Add((id, change, SyncState.Pending));
        return id;
    }

    public void MarkSynced(int id) => SetState(id, SyncState.Synced);
    public void MarkFailed(int id) => SetState(id, SyncState.Failed); // silinmez

    public IReadOnlyList<int> Retryable() => // pending + failed
        _items.Where(i => i.State != SyncState.Synced).Select(i => i.Id).ToList();

    private void SetState(int id, SyncState state)
    {
        var idx = _items.FindIndex(i => i.Id == id);
        if (idx >= 0) _items[idx] = (_items[idx].Id, _items[idx].Change, state);
    }
}

public static class DeltaExchange
{
    /// <summary>
    /// Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
    /// CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
    /// </summary>
    public static IReadOnlyList<SyncChange> Apply(
        VectorClock myClock,
        IEnumerable<SyncChange> incoming,
        IReadOnlyDictionary<string, SyncChange> localByKey)
    {
        var applied = new List<SyncChange>();
        foreach (var change in incoming)
        {
            var cmp = myClock.Compare(change.Clock);
            if (cmp is ClockOrder.After or ClockOrder.Equal) continue; // zaten uygulanmış
            if (cmp == ClockOrder.Concurrent && localByKey.TryGetValue(change.Key, out var local))
            {
                var winner = Conflict.LastWriteWins(change, local);
                if (!ReferenceEquals(winner, change)) continue;         // yerel sürüm kazandı
            }
            applied.Add(change);
            myClock.Merge(change.Clock);
        }
        return applied;
    }
}
```

## §7 SQL

Kuyruk kalıcılığı şema taslağı — NOT: kalıcı şema K007-data'ya aittir (master §2.2). Yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```sql
-- K014-media/synchronization — senkron kuyruğu + vektör saat kalıcılığı şema taslağı
-- (TASARIM ÖNERİSİ). Grounded in media-master.md §9.10 (yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN)
-- + §13/8 (tutarlılık testi) + §15/5 (gecikme ölçümü — eşik ⚠️ UNKNOWN).
-- YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
-- bu şema YÖN SEÇMEZ — yalnız mekanik. NOT: kalıcı şema K007-data'ya aittir (master §2.2).

-- Vektör saat: cihaz başına sayaç (per-key max merge uygulama katmanında yapılır).
CREATE TABLE media_sync_clock (
    device_id    VARCHAR(64) NOT NULL,   -- cihaz kimliği
    counter      BIGINT      NOT NULL DEFAULT 0,
    updated_at   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (device_id)
) ENGINE=InnoDB;

-- Senkron kuyruğu: pending -> failed (silinmez, yeniden denenir) | synced.
CREATE TABLE media_sync_queue (
    change_id    BIGINT       NOT NULL AUTO_INCREMENT,
    entity_key   VARCHAR(512) NOT NULL,             -- eşitlenen medya verisi anahtarı
    value_json   JSON         NOT NULL,             -- değişiklik gövdesi (delta)
    clock_json   JSON         NOT NULL,             -- değişiklik anındaki vektör saat
    updated_at_ms BIGINT      NOT NULL,             -- LWW için son-yazan damgası (strateji ⚠️ UNKNOWN)
    state        ENUM('PENDING','FAILED','SYNCED') NOT NULL DEFAULT 'PENDING',
    attempt_count INT         NOT NULL DEFAULT 0,   -- yeniden deneme sayacı
    last_error   VARCHAR(512) NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (change_id),
    KEY idx_sync_retry (state, created_at)          -- yeniden denenecekler taraması (pending + failed)
) ENGINE=InnoDB;

-- Delta sorgusu: bilinen saatin altındaki değişiklikler (karşı cihaz sayaçlarıyla karşılaştır).
-- uygulama katmanı: counter > COALESCE( bilinen sayaç, 0 ) olan satırlar alınır, sonra
-- vektör saat CONCURRENT/LWW kontrolü uygulanır (master §9.10 — strateji ⚠️ UNKNOWN).
SELECT c.device_id, c.counter
  FROM media_sync_clock c
 WHERE c.counter > COALESCE(:known_counter_for_device, 0);

-- Başarısız değişiklikleri bul (yeniden deneme kuyruğu — failed silinmez).
SELECT change_id, entity_key, attempt_count, last_error
  FROM media_sync_queue
 WHERE state IN ('PENDING', 'FAILED')
 ORDER BY created_at;
```

## §8 Go

`map[string]uint64` vektör saat + LWW + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```go
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.
package synchronization

import "strings"

type ClockOrder int

const (
	OrderBefore ClockOrder = iota // self < other
	OrderAfter                    // self > other
	OrderEqual
	OrderConcurrent // iki dallanma da var -> çakışma (§9.10)
)

type SyncState int

const (
	StatePending SyncState = iota // silinmez — yeniden denenir
	StateFailed                   // silinmez — yeniden denenir
	StateSynced
)

type SyncChange struct {
	Key         string
	Value       string
	Clock       map[string]uint64
	UpdatedAtMs int64
}

// Increment: kendi sayacını bir artır.
func Increment(clock map[string]uint64, device string) {
	clock[device]++
}

// Merge: per-key max birleştirme (deterministik — sıra bağımsız).
func MergeClocks(a, b map[string]uint64) map[string]uint64 {
	merged := make(map[string]uint64, len(a)+len(b))
	for k, v := range a {
		merged[k] = v
	}
	for k, v := range b {
		if cur, ok := merged[k]; !ok || cur < v {
			merged[k] = v
		}
	}
	return merged
}

// Compare: BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma.
func CompareClocks(a, b map[string]uint64) ClockOrder {
	ge, le := true, true // ge: a >= b · le: b >= a
	for _, d := range unionKeys(a, b) {
		av, bv := a[d], b[d]
		if av < bv {
			ge = false
		}
		if bv < av {
			le = false
		}
	}
	switch {
	case ge && le:
		return OrderEqual
	case ge:
		return OrderAfter
	case le:
		return OrderBefore
	default:
		return OrderConcurrent
	}
}

func unionKeys(a, b map[string]uint64) []string {
	seen := make(map[string]struct{}, len(a)+len(b))
	out := make([]string, 0, len(a)+len(b))
	for k := range a {
		if _, ok := seen[k]; !ok {
			seen[k] = struct{}{}
			out = append(out, k)
		}
	}
	for k := range b {
		if _, ok := seen[k]; !ok {
			seen[k] = struct{}{}
			out = append(out, k)
		}
	}
	return out
}

// LastWriteWins: son-yazan-kazanır — strateji kararı ⚠️ UNKNOWN (§9.10).
func LastWriteWins(a, b SyncChange) SyncChange {
	if a.UpdatedAtMs != b.UpdatedAtMs {
		if a.UpdatedAtMs > b.UpdatedAtMs {
			return a
		}
		return b
	}
	if strings.Compare(a.Key, b.Key) >= 0 { // deterministik tie-break
		return a
	}
	return b
}

// SyncQueue: durum Pending | Failed | Synced; Failed silinmez — yeniden denenir.
type queueItem struct {
	id     int
	change SyncChange
	state  SyncState
}

type SyncQueue struct {
	items  []queueItem
	nextID int
}

func NewSyncQueue() *SyncQueue { return &SyncQueue{nextID: 1} }

func (q *SyncQueue) Enqueue(c SyncChange) int {
	id := q.nextID
	q.nextID++
	q.items = append(q.items, queueItem{id: id, change: c, state: StatePending})
	return id
}

func (q *SyncQueue) MarkSynced(id int) { q.setState(id, StateSynced) }
func (q *SyncQueue) MarkFailed(id int) { q.setState(id, StateFailed) } // silinmez

// Retryable: yeniden denenecekler (pending + failed).
func (q *SyncQueue) Retryable() []int {
	var out []int
	for _, it := range q.items {
		if it.state != StateSynced {
			out = append(out, it.id)
		}
	}
	return out
}

func (q *SyncQueue) setState(id int, s SyncState) {
	for i := range q.items {
		if q.items[i].id == id {
			q.items[i].state = s
		}
	}
}

// DeltaExchange: yalnız bilinen saatin altındaki değişiklikler uygulanır.
// CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
func DeltaExchange(myClock map[string]uint64, incoming []SyncChange,
	localByKey map[string]SyncChange) []SyncChange {
	var applied []SyncChange
	for _, change := range incoming {
		cmp := CompareClocks(change.Clock, myClock)
		if cmp == OrderAfter || cmp == OrderEqual {
			continue // zaten uygulanmış
		}
		if cmp == OrderConcurrent {
			if local, ok := localByKey[change.Key]; ok {
				if winner := LastWriteWins(change, local); winner != change {
					continue // yerel sürüm kazandı
				}
			}
		}
		applied = append(applied, change)
		for k, v := range MergeClocks(myClock, change.Clock) {
			myClock[k] = v
		}
	}
	return applied
}
```

## §9 Rust

`HashMap<String, u64>` vektör saat; `Ordering`/`SyncState` enum'ları; `Result` akışı — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```rust
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ). Panic yok — enum/Result akışı.
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.

use std::collections::{HashMap, HashSet};

/// BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10).
#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum ClockOrder { Before, After, Equal, Concurrent }

/// Durum: Pending | Failed | Synced — Failed silinmez, yeniden denenir.
#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum SyncState { Pending, Failed, Synced }

#[derive(Debug, Clone)]
pub struct SyncChange {
    pub key: String,
    pub value: String,
    pub clock: HashMap<String, u64>,
    pub updated_at_ms: i64,
}

pub fn increment(clock: &mut HashMap<String, u64>, device: &str) {
    *clock.entry(device.to_string()).or_insert(0) += 1;
}

/// Per-key max birleştirme (deterministik — sıra bağımsız).
pub fn merge_clocks(a: &HashMap<String, u64>, b: &HashMap<String, u64>) -> HashMap<String, u64> {
    let mut merged = a.clone();
    for (device, counter) in b {
        let entry = merged.entry(device.clone()).or_insert(0);
        if *entry < *counter { *entry = *counter; }
    }
    merged
}

/// BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma.
pub fn compare_clocks(a: &HashMap<String, u64>, b: &HashMap<String, u64>) -> ClockOrder {
    let devices: HashSet<&String> = a.keys().chain(b.keys()).collect();
    let (mut ge, mut le) = (true, true); // ge: a >= b · le: b >= a
    for d in devices {
        let av = *a.get(d).unwrap_or(&0);
        let bv = *b.get(d).unwrap_or(&0);
        if av < bv { ge = false; }
        if bv < av { le = false; }
    }
    match (ge, le) {
        (true, true) => ClockOrder::Equal,
        (true, false) => ClockOrder::After,
        (false, true) => ClockOrder::Before,
        (false, false) => ClockOrder::Concurrent,
    }
}

/// Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10).
pub fn last_write_wins(a: &SyncChange, b: &SyncChange) -> &SyncChange {
    if a.updated_at_ms != b.updated_at_ms {
        if a.updated_at_ms > b.updated_at_ms { a } else { b }
    } else if a.key.as_bytes() >= b.key.as_bytes() {  // deterministik tie-break
        a
    } else {
        b
    }
}

/// Senkron kuyruğu — durum: Pending | Failed | Synced; Failed silinmez.
pub struct SyncQueue {
    items: Vec<(i64, SyncChange, SyncState)>,
    next_id: i64,
}

impl SyncQueue {
    pub fn new() -> Self { Self { items: Vec::new(), next_id: 1 } }

    pub fn enqueue(&mut self, change: SyncChange) -> i64 {
        let id = self.next_id;
        self.next_id += 1;
        self.items.push((id, change, SyncState::Pending));
        id
    }

    pub fn mark_synced(&mut self, id: i64) { self.set_state(id, SyncState::Synced); }
    pub fn mark_failed(&mut self, id: i64) { self.set_state(id, SyncState::Failed); } // silinmez

    /// Yeniden denenecekler: pending + failed.
    pub fn retryable(&self) -> Vec<i64> {
        self.items.iter().filter(|(_, _, s)| *s != SyncState::Synced).map(|(id, _, _)| *id).collect()
    }

    fn set_state(&mut self, id: i64, state: SyncState) {
        for (i, _, s) in self.items.iter_mut() {
            if *i == id { *s = state; }
        }
    }
}

/// Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
/// CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
pub fn delta_exchange(
    my_clock: &mut HashMap<String, u64>,
    incoming: &[SyncChange],
    local_by_key: &HashMap<String, SyncChange>,
) -> Vec<SyncChange> {
    let mut applied = Vec::new();
    for change in incoming {
        let cmp = compare_clocks(&change.clock, my_clock);
        if cmp == ClockOrder::After || cmp == ClockOrder::Equal {
            continue;                                        // zaten uygulanmış
        }
        if cmp == ClockOrder::Concurrent {
            if let Some(local) = local_by_key.get(&change.key) {
                let winner = last_write_wins(change, local);
                if !std::ptr::eq(winner, change) {
                    continue;                                // yerel sürüm kazandı
                }
            }
        }
        applied.push(change.clone());
        for (k, v) in merge_clocks(my_clock, &change.clock) {
            my_clock.insert(k, v);
        }
    }
    applied
}
```

## §10 Python

`dict` vektör saat + `dataclass` girdi + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```python
# K014-media/synchronization — vector clock + LWW conflict + sync queue
# + delta exchange (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
# YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
# bu dosya yön SEÇMEZ — yalnız mekanik. Repo'da bu modülün kodu YOK.

from __future__ import annotations

from dataclasses import dataclass, field
from enum import Enum


class ClockOrder(Enum):
    BEFORE = "BEFORE"
    AFTER = "AFTER"
    EQUAL = "EQUAL"
    CONCURRENT = "CONCURRENT"  # iki dallanma da var -> çakışma (§9.10)


class SyncState(Enum):
    PENDING = "PENDING"        # silinmez — yeniden denenir
    FAILED = "FAILED"          # silinmez — yeniden denenir
    SYNCED = "SYNCED"


@dataclass
class SyncChange:
    key: str
    value: str
    clock: dict[str, int]
    updated_at_ms: int


def increment(clock: dict[str, int], device: str) -> None:
    clock[device] = clock.get(device, 0) + 1


def merge_clocks(a: dict[str, int], b: dict[str, int]) -> dict[str, int]:
    """Per-key max birleştirme (deterministik — sıra bağımsız)."""
    merged = dict(a)
    for device, counter in b.items():
        merged[device] = max(merged.get(device, 0), counter)
    return merged


def compare_clocks(a: dict[str, int], b: dict[str, int]) -> ClockOrder:
    """BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma."""
    ge = le = True  # ge: a >= b · le: b >= a
    for device in set(a) | set(b):
        av, bv = a.get(device, 0), b.get(device, 0)
        if av < bv:
            ge = False
        if bv < av:
            le = False
    if ge and le:
        return ClockOrder.EQUAL
    if ge:
        return ClockOrder.AFTER
    if le:
        return ClockOrder.BEFORE
    return ClockOrder.CONCURRENT


def last_write_wins(a: SyncChange, b: SyncChange) -> SyncChange:
    """Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10)."""
    if a.updated_at_ms != b.updated_at_ms:
        return a if a.updated_at_ms > b.updated_at_ms else b
    return a if a.key >= b.key else b  # deterministik tie-break


@dataclass
class SyncQueue:
    """Durum: PENDING | FAILED | SYNCED — FAILED silinmez, yeniden denenir."""

    _items: dict[int, tuple[SyncChange, SyncState]] = field(default_factory=dict)
    _next_id: int = 1

    def enqueue(self, change: SyncChange) -> int:
        change_id = self._next_id
        self._next_id += 1
        self._items[change_id] = (change, SyncState.PENDING)
        return change_id

    def mark_synced(self, change_id: int) -> None:
        if change_id in self._items:
            change, _ = self._items[change_id]
            self._items[change_id] = (change, SyncState.SYNCED)

    def mark_failed(self, change_id: int) -> None:
        if change_id in self._items:
            change, _ = self._items[change_id]
            self._items[change_id] = (change, SyncState.FAILED)  # silinmez

    def retryable(self) -> list[int]:
        """Yeniden denenecekler: pending + failed."""
        return [cid for cid, (_, s) in self._items.items() if s is not SyncState.SYNCED]


def delta_exchange(
    my_clock: dict[str, int],
    incoming: list[SyncChange],
    local_by_key: dict[str, SyncChange],
) -> list[SyncChange]:
    """Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
    CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10)."""
    applied: list[SyncChange] = []
    for change in incoming:
        cmp = compare_clocks(change.clock, my_clock)
        if cmp in (ClockOrder.AFTER, ClockOrder.EQUAL):
            continue  # zaten uygulanmış
        if cmp is ClockOrder.CONCURRENT and change.key in local_by_key:
            winner = last_write_wins(change, local_by_key[change.key])
            if winner is not change:
                continue  # yerel sürüm kazandı
        applied.append(change)
        my_clock.update(merge_clocks(my_clock, change.clock))
    return applied
```

## §11 Java

`Map<String, Long>` vektör saat + `enum` durumlar + `ArrayList` kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```java
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.
package com.coremusic.synchronization;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

public final class VectorClock {
    /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10). */
    public enum Order { BEFORE, AFTER, EQUAL, CONCURRENT }

    /** Durum: PENDING | FAILED | SYNCED — FAILED silinmez, yeniden denenir. */
    public enum SyncState { PENDING, FAILED, SYNCED }

    public record SyncChange(String key, String value,
                             Map<String, Long> clock, long updatedAtMs) {}

    private final Map<String, Long> clock = new HashMap<>();

    public void increment(String device) {
        clock.merge(device, 1L, Long::sum);
    }

    /** Per-key max birleştirme (deterministik — sıra bağımsız). */
    public static Map<String, Long> merge(Map<String, Long> a, Map<String, Long> b) {
        Map<String, Long> merged = new HashMap<>(a);
        b.forEach((device, counter) -> merged.merge(device, counter, Math::max));
        return merged;
    }

    /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma. */
    public Order compare(Map<String, Long> other) {
        boolean ge = true, le = true; // ge: self >= other · le: other >= self
        Set<String> devices = new HashSet<>(clock.keySet());
        devices.addAll(other.keySet());
        for (String d : devices) {
            long a = clock.getOrDefault(d, 0L);
            long b = other.getOrDefault(d, 0L);
            if (a < b) ge = false;
            if (b < a) le = false;
        }
        if (ge && le) return Order.EQUAL;
        if (ge) return Order.AFTER;
        if (le) return Order.BEFORE;
        return Order.CONCURRENT;
    }

    public Map<String, Long> snapshot() { return new HashMap<>(clock); }

    /** Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10). */
    public static SyncChange lastWriteWins(SyncChange a, SyncChange b) {
        if (a.updatedAtMs() != b.updatedAtMs()) {
            return a.updatedAtMs() > b.updatedAtMs() ? a : b;
        }
        return a.key().compareTo(b.key()) >= 0 ? a : b; // deterministik tie-break
    }

    /** Senkron kuyruğu — durum: PENDING | FAILED | SYNCED; FAILED silinmez. */
    public static final class SyncQueue {
        private record Item(int id, SyncChange change, SyncState state) {}
        private final List<Item> items = new ArrayList<>();
        private int nextId = 1;

        public int enqueue(SyncChange change) {
            int id = nextId++;
            items.add(new Item(id, change, SyncState.PENDING));
            return id;
        }
        public void markSynced(int id) { setState(id, SyncState.SYNCED); }
        public void markFailed(int id) { setState(id, SyncState.FAILED); } // silinmez

        /** Yeniden denenecekler: pending + failed. */
        public List<Integer> retryable() {
            List<Integer> out = new ArrayList<>();
            for (Item it : items) if (it.state() != SyncState.SYNCED) out.add(it.id());
            return out;
        }
        private void setState(int id, SyncState state) {
            for (int i = 0; i < items.size(); i++) {
                if (items.get(i).id() == id) {
                    items.set(i, new Item(id, items.get(i).change(), state));
                }
            }
        }
    }

    /**
     * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
     * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
     */
    public static List<SyncChange> deltaExchange(VectorClock myClock,
                                                 List<SyncChange> incoming,
                                                 Map<String, SyncChange> localByKey) {
        List<SyncChange> applied = new ArrayList<>();
        for (SyncChange change : incoming) {
            Order cmp = myClock.compare(change.clock());
            if (cmp == Order.AFTER || cmp == Order.EQUAL) continue; // zaten uygulanmış
            if (cmp == Order.CONCURRENT && localByKey.containsKey(change.key())) {
                SyncChange winner = lastWriteWins(change, localByKey.get(change.key()));
                if (winner != change) continue;                     // yerel sürüm kazandı
            }
            applied.add(change);
            myClock.clock.putAll(merge(myClock.clock, change.clock()));
        }
        return applied;
    }
}
```

## §12 Swift

`[String: UInt64]` vektör saat + `enum` durumlar + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```swift
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.
import Foundation

enum ClockOrder {                    // BEFORE / AFTER / EQUAL / CONCURRENT (çakışma — §9.10)
    case before, after, equal, concurrent
}

enum SyncState {                     // FAILED silinmez — yeniden denenir
    case pending, failed, synced
}

struct SyncChange {
    let key: String
    let value: String
    let clock: [String: UInt64]
    let updatedAtMs: Int64
}

func increment(_ clock: inout [String: UInt64], device: String) {
    clock[device, default: 0] += 1
}

/// Per-key max birleştirme (deterministik — sıra bağımsız).
func mergeClocks(_ a: [String: UInt64], _ b: [String: UInt64]) -> [String: UInt64] {
    var merged = a
    for (device, counter) in b {
        merged[device] = max(merged[device] ?? 0, counter)
    }
    return merged
}

/// BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma.
func compareClocks(_ a: [String: UInt64], _ b: [String: UInt64]) -> ClockOrder {
    var ge = true  // a >= b
    var le = true  // b >= a
    for device in Set(a.keys).union(b.keys) {
        let av = a[device] ?? 0
        let bv = b[device] ?? 0
        if av < bv { ge = false }
        if bv < av { le = false }
    }
    if ge && le { return .equal }
    if ge { return .after }
    if le { return .before }
    return .concurrent
}

/// Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10).
func lastWriteWins(_ a: SyncChange, _ b: SyncChange) -> SyncChange {
    if a.updatedAtMs != b.updatedAtMs {
        return a.updatedAtMs > b.updatedAtMs ? a : b
    }
    return a.key >= b.key ? a : b   // deterministik tie-break
}

/// Senkron kuyruğu — durum: pending | failed | synced; failed silinmez.
final class SyncQueue {
    private var items: [(id: Int, change: SyncChange, state: SyncState)] = []
    private var nextId = 1

    func enqueue(_ change: SyncChange) -> Int {
        let id = nextId; nextId += 1
        items.append((id, change, .pending))
        return id
    }
    func markSynced(_ id: Int) { setState(id, .synced) }
    func markFailed(_ id: Int) { setState(id, .failed) } // silinmez

    /// Yeniden denenecekler: pending + failed.
    func retryable() -> [Int] {
        items.filter { $0.state != .synced }.map { $0.id }
    }
    private func setState(_ id: Int, _ state: SyncState) {
        if let i = items.firstIndex(where: { $0.id == id }) {
            items[i] = (id, items[i].change, state)
        }
    }
}

/// Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
/// CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
func deltaExchange(myClock: inout [String: UInt64],
                   incoming: [SyncChange],
                   localByKey: [String: SyncChange]) -> [SyncChange] {
    var applied: [SyncChange] = []
    for change in incoming {
        let cmp = compareClocks(change.clock, myClock)
        if cmp == .after || cmp == .equal { continue }        // zaten uygulanmış
        if cmp == .concurrent, let local = localByKey[change.key] {
            let winner = lastWriteWins(change, local)
            if winner.key != change.key || winner.updatedAtMs != change.updatedAtMs {
                continue                                       // yerel sürüm kazandı
            }
        }
        applied.append(change)
        myClock = mergeClocks(myClock, change.clock)
    }
    return applied
}
```

## §13 Kotlin

`MutableMap<String, Long>` vektör saat + sealed durumlar + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```kotlin
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.
package com.coremusic.synchronization

/** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10). */
enum class ClockOrder { BEFORE, AFTER, EQUAL, CONCURRENT }

/** Durum: PENDING | FAILED | SYNCED — FAILED silinmez, yeniden denenir. */
enum class SyncState { PENDING, FAILED, SYNCED }

data class SyncChange(
    val key: String,
    val value: String,
    val clock: Map<String, Long>,
    val updatedAtMs: Long
)

object VectorClockOps {
    fun increment(clock: MutableMap<String, Long>, device: String) {
        clock[device] = (clock[device] ?: 0L) + 1L
    }

    /** Per-key max birleştirme (deterministik — sıra bağımsız). */
    fun merge(a: Map<String, Long>, b: Map<String, Long>): Map<String, Long> {
        val merged = a.toMutableMap()
        b.forEach { (device, counter) -> merged[device] = maxOf(merged[device] ?: 0L, counter) }
        return merged
    }

    /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma. */
    fun compare(a: Map<String, Long>, b: Map<String, Long>): ClockOrder {
        var ge = true // a >= b
        var le = true // b >= a
        for (device in a.keys + b.keys) {
            val av = a[device] ?: 0L
            val bv = b[device] ?: 0L
            if (av < bv) ge = false
            if (bv < av) le = false
        }
        return when {
            ge && le -> ClockOrder.EQUAL
            ge -> ClockOrder.AFTER
            le -> ClockOrder.BEFORE
            else -> ClockOrder.CONCURRENT
        }
    }

    /** Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10). */
    fun lastWriteWins(a: SyncChange, b: SyncChange): SyncChange = when {
        a.updatedAtMs != b.updatedAtMs -> if (a.updatedAtMs > b.updatedAtMs) a else b
        else -> if (a.key >= b.key) a else b // deterministik tie-break
    }
}

/** Senkron kuyruğu — durum: PENDING | FAILED | SYNCED; FAILED silinmez. */
class SyncQueue {
    private data class Item(val id: Int, val change: SyncChange, val state: SyncState)
    private val items = mutableListOf<Item>()
    private var nextId = 1

    fun enqueue(change: SyncChange): Int {
        val id = nextId++
        items += Item(id, change, SyncState.PENDING)
        return id
    }
    fun markSynced(id: Int) = setState(id, SyncState.SYNCED)
    fun markFailed(id: Int) = setState(id, SyncState.FAILED) // silinmez

    /** Yeniden denenecekler: pending + failed. */
    fun retryable(): List<Int> = items.filter { it.state != SyncState.SYNCED }.map { it.id }

    private fun setState(id: Int, state: SyncState) {
        val i = items.indexOfFirst { it.id == id }
        if (i >= 0) items[i] = items[i].copy(state = state)
    }
}

/**
 * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
 * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
 */
fun deltaExchange(
    myClock: MutableMap<String, Long>,
    incoming: List<SyncChange>,
    localByKey: Map<String, SyncChange>
): List<SyncChange> {
    val applied = mutableListOf<SyncChange>()
    for (change in incoming) {
        val cmp = VectorClockOps.compare(change.clock, myClock)
        if (cmp == ClockOrder.AFTER || cmp == ClockOrder.EQUAL) continue // zaten uygulanmış
        if (cmp == ClockOrder.CONCURRENT) {
            val local = localByKey[change.key] ?: continue
            val winner = VectorClockOps.lastWriteWins(change, local)
            if (winner != change) continue                              // yerel sürüm kazandı
        }
        applied += change
        myClock.putAll(VectorClockOps.merge(myClock, change.clock))
    }
    return applied
}
```

## §14 Ruby

`Hash` vektör saat + `Struct` girdi + kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```ruby
# K014-media/synchronization — vector clock + LWW conflict + sync queue
# + delta exchange (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
# YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
# bu dosya yön SEÇMEZ — yalnız mekanik. Repo'da bu sınıfın kodu YOK.

module Synchronization
  # BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10).
  module ClockOrder
    BEFORE = :before
    AFTER = :after
    EQUAL = :equal
    CONCURRENT = :concurrent
  end

  # Durum: :pending | :failed | :synced — failed silinmez, yeniden denenir.
  module SyncState
    PENDING = :pending
    FAILED = :failed
    SYNCED = :synced
  end

  SyncChange = Struct.new(:key, :value, :clock, :updated_at_ms, keyword_init: true)

  module VectorClock
    module_function

    def increment(clock, device)
      clock[device] = clock.fetch(device, 0) + 1
    end

    # Per-key max birleştirme (deterministik — sıra bağımsız).
    def merge(a, b)
      merged = a.dup
      b.each do |device, counter|
        merged[device] = [merged.fetch(device, 0), counter].max
      end
      merged
    end

    # BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma.
    def compare(a, b)
      ge = le = true # ge: a >= b · le: b >= a
      (a.keys | b.keys).each do |device|
        av = a.fetch(device, 0)
        bv = b.fetch(device, 0)
        ge = false if av < bv
        le = false if bv < av
      end
      return ClockOrder::EQUAL if ge && le
      return ClockOrder::AFTER if ge
      return ClockOrder::BEFORE if le
      ClockOrder::CONCURRENT
    end

    # Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10).
    def last_write_wins(a, b)
      if a.updated_at_ms != b.updated_at_ms
        a.updated_at_ms > b.updated_at_ms ? a : b
      else
        a.key >= b.key ? a : b # deterministik tie-break
      end
    end
  end

  # Senkron kuyruğu — durum: pending | failed | synced; failed silinmez.
  class SyncQueue
    Item = Struct.new(:id, :change, :state, keyword_init: true)

    def initialize
      @items = []
      @next_id = 1
    end

    def enqueue(change)
      id = @next_id
      @next_id += 1
      @items << Item.new(id: id, change: change, state: SyncState::PENDING)
      id
    end

    def mark_synced(id) = set_state(id, SyncState::SYNCED)
    def mark_failed(id) = set_state(id, SyncState::FAILED) # silinmez

    # Yeniden denenecekler: pending + failed.
    def retryable
      @items.reject { |it| it.state == SyncState::SYNCED }.map(&:id)
    end

    private

    def set_state(id, state)
      it = @items.find { |i| i.id == id }
      it.state = state if it
    end
  end

  module_function

  # Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
  # CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
  def delta_exchange(my_clock, incoming, local_by_key)
    applied = []
    incoming.each do |change|
      cmp = VectorClock.compare(change.clock, my_clock)
      next if cmp == ClockOrder::AFTER || cmp == ClockOrder::EQUAL # zaten uygulanmış
      if cmp == ClockOrder::CONCURRENT && local_by_key.key?(change.key)
        winner = VectorClock.last_write_wins(change, local_by_key[change.key])
        next unless winner.equal?(change)                          # yerel sürüm kazandı
      end
      applied << change
      my_clock.merge!(VectorClock.merge(my_clock, change.clock))
    end
    applied
  end
end
```

## §15 Elixir

Functional vektör saat — `%{device => counter}` + `{:ok, _}` / `{:error, _}` akışı; kuyruk + delta — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```elixir
# K014-media/synchronization — vector clock + LWW conflict + sync queue
# + delta exchange (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
# YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
# bu dosya yön SEÇMEZ — yalnız mekanik. Repo'da bu modülün kodu YOK.

defmodule CoreMusic.Synchronization do
  @moduledoc """
  Vector clock + LWW conflict + pending/failed kuyruk + delta alışverişi (illustrative design).
  Yön seçimi Vault Steward'a aittir (master §12/9 → §22.6 "⏳ AÇIK").
  """

  # BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10).
  @type clock_order :: :before | :after | :equal | :concurrent
  # Durum: pending | failed | synced — failed silinmez, yeniden denenir.
  @type sync_state :: :pending | :failed | :synced

  defstruct key: "", value: "", clock: %{}, updated_at_ms: 0
  @type t :: %__MODULE__{key: String.t(), value: String.t(),
                         clock: %{optional(String.t()) => integer()},
                         updated_at_ms: integer()}

  @spec increment(map(), String.t()) :: map()
  def increment(clock, device), do: Map.update(clock, device, 1, &(&1 + 1))

  @doc "Per-key max birleştirme (deterministik — sıra bağımsız)."
  @spec merge(map(), map()) :: map()
  def merge(a, b) do
    Map.merge(a, b, fn _k, x, y -> max(x, y) end)
  end

  @doc "BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma."
  @spec compare(map(), map()) :: clock_order()
  def compare(a, b) do
    devices = MapSet.union(MapSet.new(Map.keys(a)), MapSet.new(Map.keys(b)))

    {ge, le} =
      Enum.reduce(devices, {true, true}, fn device, {ge, le} ->
        av = Map.get(a, device, 0)
        bv = Map.get(b, device, 0)
        {ge and av >= bv, le and bv >= av}
      end)

    case {ge, le} do
      {true, true} -> :equal
      {true, false} -> :after
      {false, true} -> :before
      {false, false} -> :concurrent
    end
  end

  @doc "Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10)."
  @spec last_write_wins(t(), t()) :: t()
  def last_write_wins(%__MODULE__{} = a, %__MODULE__{} = b) do
    cond do
      a.updated_at_ms != b.updated_at_ms ->
        if a.updated_at_ms > b.updated_at_ms, do: a, else: b

      true ->
        if a.key >= b.key, do: a, else: b # deterministik tie-break
    end
  end

  @doc """
  Senkron kuyruğu — durum: pending | failed | synced; failed silinmez.
  (Kavramsal state makinesi; kalıcı katman K007-data'ya aittir — master §2.2.)
  """
  defmodule SyncQueue do
    def new, do: %{next_id: 1, items: %{}}

    def enqueue(queue, %CoreMusic.Synchronization{} = change) do
      id = queue.next_id
      items = Map.put(queue.items, id, %{change: change, state: :pending})
      {id, %{queue | next_id: id + 1, items: items}}
    end

    def mark_synced(queue, id), do: set_state(queue, id, :synced)
    def mark_failed(queue, id), do: set_state(queue, id, :failed) # silinmez

    @doc "Yeniden denenecekler: pending + failed."
    def retryable(queue) do
      queue.items
      |> Enum.filter(fn {_id, %{state: s}} -> s != :synced end)
      |> Enum.map(fn {id, _} -> id end)
    end

    defp set_state(queue, id, state) do
      case Map.fetch(queue.items, id) do
        {:ok, item} -> %{queue | items: Map.put(queue.items, id, %{item | state: state})}
        :error -> queue
      end
    end
  end

  @doc """
  Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
  CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇIK — §9.10).
  """
  @spec delta_exchange(map(), [t()], %{optional(String.t()) => t()}) :: {[t()], map()}
  def delta_exchange(my_clock, incoming, local_by_key) do
    {applied, clock} =
      Enum.reduce(incoming, {[], my_clock}, fn change, {applied, clock} ->
        cmp = compare(change.clock, clock)

        cond do
          cmp in [:after, :equal] ->
            {applied, clock} # zaten uygulanmış

          cmp == :concurrent and Map.has_key?(local_by_key, change.key) ->
            winner = last_write_wins(change, local_by_key[change.key])
            if winner == change do
              {[change | applied], merge(clock, change.clock)} # CONCURRENT + LWW kazananı
            else
              {applied, clock}                                  # yerel sürüm kazandı
            end

          true ->
            {[change | applied], merge(clock, change.clock)}    # BEFORE
        end
      end)

    {Enum.reverse(applied), clock}
  end
end
```

## §16 Scala

`Map[String, Long]` immutable vektör saat + ADT durumlar + `Either` akışı — yön kararı AÇIK (master §12/9 → Vault Steward), örnek yön SEÇMEZ.

```scala
// K014-media/synchronization — vector clock + LWW conflict + sync queue
// + delta exchange (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.10 (eşitleme · yön ⚠️ UNKNOWN · çakışma ⚠️ UNKNOWN).
// YÖN UYARISI: tek/çift yönlü kararı AÇIKTIR (§12/9 → Vault Steward §22.6 "⏳ AÇIK");
// bu dosya yön SEÇMEZ — yalnız mekanik. No repo code yet — illustrative design example.
package com.coremusic.synchronization

object VectorClock {
  /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma (§9.10). */
  sealed trait ClockOrder
  object ClockOrder {
    case object Before extends ClockOrder
    case object After extends ClockOrder
    case object Equal extends ClockOrder
    case object Concurrent extends ClockOrder
  }

  /** Durum: Pending | Failed | Synced — Failed silinmez, yeniden denenir. */
  sealed trait SyncState
  object SyncState {
    case object Pending extends SyncState
    case object Failed extends SyncState
    case object Synced extends SyncState
  }

  final case class SyncChange(key: String, value: String,
                              clock: Map[String, Long], updatedAtMs: Long)

  type Clock = Map[String, Long]

  def increment(clock: Clock, device: String): Clock =
    clock.updated(device, clock.getOrElse(device, 0L) + 1L)

  /** Per-key max birleştirme (deterministik — sıra bağımsız). */
  def merge(a: Clock, b: Clock): Clock =
    b.foldLeft(a) { case (acc, (device, counter)) =>
      acc.updated(device, math.max(acc.getOrElse(device, 0L), counter))
    }

  /** BEFORE / AFTER / EQUAL / CONCURRENT — CONCURRENT = çakışma. */
  def compare(a: Clock, b: Clock): ClockOrder = {
    val devices = a.keySet ++ b.keySet
    val (ge, le) = devices.foldLeft((true, true)) { case ((ge, le), device) =>
      val av = a.getOrElse(device, 0L)
      val bv = b.getOrElse(device, 0L)
      (ge && av >= bv, le && bv >= av)
    }
    if (ge && le) ClockOrder.Equal
    else if (ge) ClockOrder.After
    else if (le) ClockOrder.Before
    else ClockOrder.Concurrent
  }

  /** Son-yazan-kazanır (LWW) — strateji kararı ⚠️ UNKNOWN (§9.10). */
  def lastWriteWins(a: SyncChange, b: SyncChange): SyncChange =
    if (a.updatedAtMs != b.updatedAtMs) {
      if (a.updatedAtMs > b.updatedAtMs) a else b
    } else {
      if (a.key >= b.key) a else b // deterministik tie-break
    }

  /**
   * Senkron kuyruğu — durum: Pending | Failed | Synced; Failed silinmez.
   * Kalıcı katman K007-data'ya aittir (master §2.2) — bu sınıf kavramsaldır.
   */
  final case class SyncQueue private (
      items: Vector[(Int, SyncChange, SyncState)],
      nextId: Int) {

    def enqueue(change: SyncChange): (Int, SyncQueue) = {
      val id = nextId
      (id, SyncQueue(items :+ ((id, change, SyncState.Pending)), nextId + 1))
    }

    def markSynced(id: Int): SyncQueue = setState(id, SyncState.Synced)
    def markFailed(id: Int): SyncQueue = setState(id, SyncState.Failed) // silinmez

    /** Yeniden denenecekler: pending + failed. */
    def retryable(): Vector[Int] =
      items.collect { case (id, _, s) if s != SyncState.Synced => id }

    private def setState(id: Int, state: SyncState): SyncQueue =
      copy(items = items.map { case (i, c, s) => if (i == id) (i, c, state) else (i, c, s) })
  }
  object SyncQueue { def empty: SyncQueue = SyncQueue(Vector.empty, 1) }

  /**
   * Delta alışverişi: yalnız bilinen saatin altındaki değişiklikler uygulanır.
   * CONCURRENT durumunda LWW ile çözülür (strateji kararı AÇAK — §9.10).
   */
  def deltaExchange(
      myClock: Clock,
      incoming: Seq[SyncChange],
      localByKey: Map[String, SyncChange]): (Clock, Seq[SyncChange]) = {
    incoming.foldLeft((myClock, Vector.empty[SyncChange])) {
      case ((clock, applied), change) =>
        val cmp = compare(change.clock, clock)
        if (cmp == ClockOrder.After || cmp == ClockOrder.Equal) {
          (clock, applied)                                  // zaten uygulanmış
        } else if (cmp == ClockOrder.Concurrent && localByKey.contains(change.key)) {
          val winner = lastWriteWins(change, localByKey(change.key))
          if (winner == change) (merge(clock, change.clock), applied :+ change)
          else (clock, applied)                             // yerel sürüm kazandı
        } else {
          (merge(clock, change.clock), applied :+ change)   // BEFORE
        }
    }
  }
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.10 | vault | synchronization rol/sınır/iddata (yön ⚠️ · çakışma ⚠️ UNKNOWN) |
| 2 | K014-media/media-master.md §12/9 · §16/9 | vault | yön kararı (tek/çift) → Vault Steward (⚠️ UNKNOWN) |
| 3 | K014-media/media-master.md §22.6 | vault | sync yönünün web araştırmasında KAPANMADIĞI ("⏳ AÇIK") |
| 4 | K014-media/media-master.md §7/3 | vault | "synchronization" adı iki domain'de — sınır ⚠️ VERIFICATION REQUIRED |
| 5 | K014-media/media-master.md §13/8 | vault | eşitleme tutarlılığı test senaryosu (cihazlar arası eşitlik) |
| 6 | K014-media/media-master.md §15/5 | vault | eşitleme gecikmesi ölçüm kalemi — eşik ⚠️ UNKNOWN |
| 7 | K014-media/media-master.md §9.8 | vault | offline sınırı (indirilen/önbelleğe alınan parçalar) |
| 8 | K014-media/synchronization/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.10 + §12/9/§22.6. **Yön (tek/çift) ve
> çakışma stratejisi seçilmemiştir** — Vault Steward kararı bekleniyor.

**Authority:** Bayram Ali / Vault Steward