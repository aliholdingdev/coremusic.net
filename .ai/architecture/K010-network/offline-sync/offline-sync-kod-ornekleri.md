---
node_id: K014-network/offline-sync/kod-ornekleri
node_type: module
status: draft
parent_id: K014-network/offline-sync
role: ornek
children: []
depends_on:
  - "K014-network/offline-sync/offline-sync-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans kaynaklar: offline-sync-standart-kanit.md §1–§2 (IndexedDB · Background Sync · SQLite)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Sahiplik (§7/2) + çakışma stratejisi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kod
---

# offline-sync — kod-ornekleri (kademe-2 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: offline kuyruğu
> (enqueue) + bağlantı gelince sıralı flush (FIFO). Çakışma çözümü YOKTUR (faz C).

## §1 PHP 8.4 — OfflineQueue (FIFO · basit kalıcılaştırma · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/offline-sync — offline kuyruk (TASARIM ÖRNEĞİ).
 * Sınır: girişim (retry) politikası basit; çakışma çözümü YOK (faz C),
 *        sahiplik §7/2 — bu sınıf yalnız kuyruk mekaniği.
 */
final class OfflineQueue
{
    /** @var list<array{id: string, payload: string, enqueuedAt: float}> */
    private array $items = [];

    public function enqueue(string $id, string $payload): void
    {
        foreach ($this->items as $it) {
            if ($it['id'] === $id) {
                return; // aynı id iki kez kuyruğa girmez (idempotency — basit)
            }
        }
        $this->items[] = ['id' => $id, 'payload' => $payload, 'enqueuedAt' => microtime(true)];
    }

    /**
     * FIFO sırayla gönderir; başarılıyı kuyruktan düşer, başarısızda durur (sıra korunur).
     *
     * @param \Closure(string): bool $send payload = başarılı mı?
     * @return array{sent: int, remaining: int}
     */
    public function flush(\Closure $send): array
    {
        $sent = 0;
        while ($this->items !== []) {
            $head = $this->items[0];
            if (!$send($head['payload'])) {
                break; // başarısızda dur — sıra korunur (strict ordering, faz C'de gevşenebilir)
            }
            array_shift($this->items);
            $sent++;
        }
        return ['sent' => $sent, 'remaining' => count($this->items)];
    }

    public function size(): int
    {
        return count($this->items);
    }
}
```

## §2 Vanilla JS (ES6+) — IndexedDB kuyruk + online flush (MDN §1/1, §1/3)

```js
/**
 * K014/offline-sync — IndexedDB tabanlı offline kuyruk (TASARIM ÖRNEĞİ).
 * Kanıt: IndexedDB (§1/1) + online olayı (connectivity §2/5) + Background Sync (§1/3, not).
 * Sınır: çakışma çözümü yok (faz C); sahiplik §7/2.
 */
const DB_NAME = 'cm-offline-queue';
const STORE = 'outbox';

export class OfflineQueue {
  #db = null;

  async #open() {
    if (this.#db) return this.#db;
    this.#db = await new Promise((resolve, reject) => {
      const req = indexedDB.open(DB_NAME, 1); // MDN §1/1
      req.onupgradeneeded = () => req.result.createObjectStore(STORE, { keyPath: 'id' });
      req.onsuccess = () => resolve(req.result);
      req.onerror = () => reject(req.error);
    });
    return this.#db;
  }

  /** @param {string} id @param {object} payload */
  async enqueue(id, payload) {
    const db = await this.#open();
    await new Promise((res, rej) => {
      const tx = db.transaction(STORE, 'readwrite');
      tx.objectStore(STORE).put({ id, payload, enqueuedAt: Date.now() });
      tx.oncomplete = res; tx.onerror = () => rej(tx.error);
    });
  }

  /** @param {(payload: object) => Promise<boolean>} send */
  async flush(send) {
    const db = await this.#open();
    const all = await new Promise((res, rej) => {
      const req = db.transaction(STORE).objectStore(STORE).getAll();
      req.onsuccess = () => res(req.result); req.onerror = () => rej(req.error);
    });
    all.sort((a, b) => a.enqueuedAt - b.enqueuedAt); // FIFO sıra
    for (const item of all) {
      const ok = await send(item.payload);
      if (!ok) break; // başarısızda dur — sıra korunur
      await new Promise((res, rej) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).delete(item.id);
        tx.oncomplete = res; tx.onerror = () => rej(tx.error);
      });
    }
  }
}

// Bağlantı gelince otomatik flush (connectivity online olayı ile birlikte).
window.addEventListener('online', () => { /* flush çağırıcıyı başlat — çağıran taraf */ });
```

## §3 C++20 — OfflineQueue (Neva Engine · deque · noexcept taşımalar)

```cpp
// K014/offline-sync — native offline kuyruk (TASARIM ÖRNEĞİ).
// Kanıt: SQLite yolu (offline-sync-standart-kanit.md §2/6) — burada bellek içi
//        sözleşmedir; kalıcılaştırma faz C'de (SQLite seçimi karar değil).
// Sınır: çakışma çözümü yok (faz C); sahiplik §7/2.
#pragma once
#include <deque>
#include <optional>
#include <string>
#include <string_view>
#include <unordered_set>

namespace cm::net {

class OfflineQueue {
public:
    // Aynı id ikinci kez girmez (idempotency — basit).
    void enqueue(std::string id, std::string payload) {
        if (seen_.insert(id).second)
            items_.push_back(Item{std::move(id), std::move(payload)});
    }

    // FIFO sırayla dener; başarısızda durur (sıra korunur).
    template <class SendFn>
    [[nodiscard]] std::size_t flush(SendFn&& send) {
        std::size_t sent = 0;
        while (!items_.empty()) {
            if (!send(items_.front().payload)) break; // başarısız → dur
            seen_.erase(items_.front().id);
            items_.pop_front();
            ++sent;
        }
        return sent;
    }

    [[nodiscard]] std::size_t size() const noexcept { return items_.size(); }

private:
    struct Item { std::string id; std::string payload; };
    std::deque<Item> items_;
    std::unordered_set<std::string> seen_;
};

} // namespace cm::net
```

## §4 C# — OfflineQueue (Channel tabanlı · async · ConfigureAwait(false))

```csharp
// K014/offline-sync — offline kuyruk (TASARIM ÖRNEĞİ).
// Sınır: çakışma çözümü yok (faz C); sahiplik §7/2; kalıcılaştırma (SQLite/IndexedDB) faz C.
#nullable enable
using System.Threading.Channels;

namespace CoreMusic.Network.Offline;

public sealed record OutboxItem(string Id, string Payload, DateTimeOffset EnqueuedAt);

public sealed class OfflineQueue(int capacity = 10_000)
{
    private readonly Channel<OutboxItem> _channel =
        Channel.CreateBounded<OutboxItem>(new BoundedChannelOptions(capacity)
        {
            SingleReader = true,
            SingleWriter = false,
        });
    private readonly HashSet<string> _seen = new();   // idempotency — basit
    private readonly object _gate = new();

    public bool Enqueue(string id, string payload)
    {
        lock (_gate)
        {
            if (!_seen.Add(id)) return false;         // aynı id iki kez girmez
        }
        return _channel.Writer.TryWrite(new OutboxItem(id, payload, DateTimeOffset.UtcNow));
    }

    /// <summary>FIFO sırayla gönderir; başarısızda durur (sıra korunur).</summary>
    public async Task<int> FlushAsync(Func<string, CancellationToken, Task<bool>> send,
                                      CancellationToken ct = default)
    {
        var sent = 0;
        while (_channel.Reader.TryPeek(out var item))
        {
            if (!await send(item.Payload, ct).ConfigureAwait(false)) break;
            await _channel.Reader.ReadAsync(ct).ConfigureAwait(false); // düş
            lock (_gate) { _seen.Remove(item.Id); }
            sent++;
        }
        return sent;
    }
}
```

## §5 Python 3.12+ — OfflineQueue (deque FIFO · async flush · sıra korumalı)

```python
# K014/offline-sync — offline kuyruk (TASARIM ÖRNEĞİ).
# Sınır: çakışma çözümü yok (faz C); sahiplik §7/2; kalıcılaştırma faz C.
from __future__ import annotations

from collections import deque
from collections.abc import Awaitable, Callable
from dataclasses import dataclass
from time import monotonic


@dataclass(frozen=True, slots=True)
class OutboxItem:
    id: str
    payload: str
    enqueued_at: float


class OfflineQueue:
    def __init__(self, maxsize: int = 10_000) -> None:
        self._maxsize = maxsize
        self._q: deque[OutboxItem] = deque()     # FIFO — asyncio.Queue peek yoktu (stdlib)
        self._seen: set[str] = set()             # idempotency — basit

    def enqueue(self, id: str, payload: str) -> bool:
        if id in self._seen:
            return False                          # aynı id iki kez girmez
        if len(self._q) >= self._maxsize:
            return False                          # kapasite doldu — bırak
        self._seen.add(id)
        self._q.append(OutboxItem(id, payload, monotonic()))
        return True

    async def flush(self, send: Callable[[str], Awaitable[bool]]) -> int:
        """FIFO sırayla dener; başarısızda durur (sıra korunur)."""
        sent = 0
        while self._q:
            item = self._q[0]                     # oku — düşme başarısızlıkta
            if not await send(item.payload):
                break
            self._q.popleft()                     # başarılı → düş
            self._seen.discard(item.id)
            sent += 1
        return sent

    def size(self) -> int:
        return len(self._q)
```

## §6 Go — OfflineQueue (slice FIFO · mutex)

```go
// K014/offline-sync — offline kuyruk (TASARIM ÖRNEĞİ).
// Sınır: çakışma çözümü yok (faz C); sahiplik §7/2; kalıcılaştırma faz C.
package offlinesync

import (
	"context"
	"sync"
	"time"
)

type Item struct {
	ID         string
	Payload    string
	EnqueuedAt time.Time
}

type Queue struct {
	mu   sync.Mutex
	q    []Item
	seen map[string]struct{}
}

func NewQueue() *Queue { return &Queue{seen: make(map[string]struct{})} }

// Enqueue aynı ID'yi ikinci kez eklemez (idempotency — basit).
func (q *Queue) Enqueue(id, payload string) bool {
	q.mu.Lock()
	defer q.mu.Unlock()
	if _, dup := q.seen[id]; dup {
		return false
	}
	q.seen[id] = struct{}{}
	q.q = append(q.q, Item{ID: id, Payload: payload, EnqueuedAt: time.Now()})
	return true
}

// Flush FIFO sırayla dener; başarısızda durur (sıra korunur).
func (q *Queue) Flush(ctx context.Context, send func(context.Context, string) (bool, error)) (int, error) {
	q.mu.Lock()
	defer q.mu.Unlock()
	sent := 0
	for len(q.q) > 0 {
		if err := ctx.Err(); err != nil {
			return sent, err
		}
		ok, err := send(ctx, q.q[0].Payload)
		if err != nil || !ok {
			return sent, err // başarısız → dur — sıra korunur
		}
		delete(q.seen, q.q[0].ID)
		q.q = q.q[1:]
		sent++
	}
	return sent, nil
}

func (q *Queue) Size() int {
	q.mu.Lock()
	defer q.mu.Unlock()
	return len(q.q)
}
```

## §7 Rust — OfflineQueue (VecDeque · idempotency kümesi)

```rust
// K014/offline-sync — offline kuyruk (TASARIM ÖRNEĞİ).
// Sınır: çakışma çözümü yok (faz C); sahiplik §7/2; kalıcılaştırma faz C.
use std::collections::{HashSet, VecDeque};
use std::future::Future;
use std::time::{SystemTime, UNIX_EPOCH};

#[derive(Clone, Debug)]
pub struct OutboxItem {
    pub id: String,
    pub payload: String,
    pub enqueued_at: u128, // unix ms — SystemTime::now kaynaklı
}

pub struct OfflineQueue {
    q: VecDeque<OutboxItem>,
    seen: HashSet<String>, // idempotency — basit
}

impl OfflineQueue {
    pub fn new() -> Self {
        Self { q: VecDeque::new(), seen: HashSet::new() }
    }

    /// Aynı id ikinci kez girmez.
    pub fn enqueue(&mut self, id: impl Into<String>, payload: impl Into<String>) -> bool {
        let id = id.into();
        if !self.seen.insert(id.clone()) {
            return false;
        }
        let ts = SystemTime::now().duration_since(UNIX_EPOCH).map(|d| d.as_millis()).unwrap_or(0);
        self.q.push_back(OutboxItem { id, payload: payload.into(), enqueued_at: ts });
        true
    }

    /// FIFO sırayla dener; başarısızda durur (sıra korunur).
    pub async fn flush<F, Fut>(&mut self, mut send: F) -> usize
    where
        F: FnMut(String) -> Fut,
        Fut: Future<Output = bool>,
    {
        let mut sent = 0usize;
        while let Some(front) = self.q.front() {
            if !send(front.payload.clone()).await {
                break; // başarısız → dur
            }
            let done = self.q.pop_front().expect("front varken pop");
            self.seen.remove(&done.id);
            sent += 1;
        }
        sent
    }

    pub fn size(&self) -> usize {
        self.q.len()
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | bellek içi FIFO kuyruk | kalıcılaştırma faz C |
| 2 | JS ES6+ | Web UI | IndexedDB outbox + online flush | Background Sync entegrasyonu faz C |
| 3 | C++20 | Neva Engine | deque + idempotency kümesi | SQLite yolu §2/6 — seçimi faz C |
| 4 | C# | Windows kolu | Channel tabanlı kuyruk | kalıcılaştırma faz C |
| 5 | Python | ağ testi/araç | deque FIFO + async flush | kalıcılaştırma faz C |
| 6 | Go | sunucu/tünel | slice FIFO + context | kalıcılaştırma faz C |
| 7 | Rust | sistem | VecDeque + HashSet | kalıcılaştırma faz C |

**Kapı:** 7/7 dil örneği üretildi; **çakışma çözümü YOKTUR** ve sahiplik §7/2
açık kalır — offline-sync-standart-kanit.md §4 (2 UNKNOWN).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
