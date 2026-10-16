---
node_id: K013-integraion/synchronization/sync-kod-ornekleri
node_type: technology-deepdive
title: "Sync Kod Örnekleri — 13 Dilde Artımlı Senkron (Delta · Checkpoint · Idempotent Apply)"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/synchronization
children: []
depends_on:
  - K013-integraion/synchronization/sync-sozlesme
  - K013-integraion/synchronization/sync-anayasa
  - K013-integraion/synchronization
evidence:
  - "https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/delta-query-overview (2026-10-16)"
  - "https://docs.stripe.com/idempotency (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://cloud.google.com/pubsub/docs/ordering (2026-10-16)"
  - "https://docs.stripe.com/rate-limits (2026-10-16)"
validation:
  - "Her örnek kendi dilinde tek bir aynı işi yapar: cursor oku → delta çek → idempotent uygula → checkpoint yaz"
  - "Örneklerde uydurma kütüphane/komut yoktur; harici bağımlılık gerektiren satırlar comment ile işaretlenir"
  - "Dil başına satır: aynı algoritma, aynı 5 adım — dil farkı yalnız sözdizimidir"
open_questions:
  - "CoreMusic hangi dillerde sync worker çalıştıracak → UNKNOWN (karar yok; 13 dil de TASARIM kataloğu)"
  - "Örneklerdeki dosya adı/fonksiyon adları CoreMusic'e taşınacak mı → ⚠️ VERIFICATION REQUIRED (ADR yok)"
group: kod
---

# Sync Kod Örnekleri — 13 Dilde Artımlı Senkron

> **Bu dosya ne değildir:** CoreMusic repo kodu **değildir**. Depoda sync
> implementasyonu bu turda **tespit edilmedi** → örneklerin hiçbiri "repo
> kanıtı" değildir. Her örnek **aynı algoritmayı** farklı dilde yazar ve
> `Kaynak:` satırıyla dayanağını bildirir.
>
> **Algoritma (5 adım — tüm örneklerde aynı):**
> 1. Checkpoint oku (cursor — yoksa full sync)
> 2. Delta çek (`lastSync`/eşdeğeri ile)
> 3. Idempotent uygula (aynı olay ikinci kez → tek etki)
> 4. Retry: 429 → `Retry-After` / jitter'lı exponential backoff
> 5. Başarılı apply sonrası checkpoint **yaz** (önce uygula, sonra kaydet)

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `sync-anayasa.md` | Eksenler, sınır, sahiplik tartışması | Kademe-3 |
| mimari | `sync-sozlesme.md` | Token/hata/kavram sözleşmesi | Kademe-3 |
| kod | `sync-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| **kod** | **`sync-kod-ornekleri.md`** | **13 dilde artımlı senkron örneği** | Kademe-3 |
| kanit | `sync-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §0 ELI15 — Neden 13 dil?

Aynı senkron mantığı her dilde **aynıdır**: cursor → delta → uygula →
kaydet. Fark, dilin **hata** ve **eşzamanlılık** araçlarındadır (Go'da
`error`, Rust'ta `Result`, Ruby'de `begin/rescue`). Bu katalog, hangi dilde
olursa olsun **5 adımın atlanmaması** gerektiğini gösterir; en kritik adım
**5**'tir: checkpoint, uygulamadan **sonra** yazılır. (Kaynak: [K1] [K5])

---

## §1 Dizin — 13 Dil

| # | Dil | Rol (bu örnekte) | Vurgu |
|---|---|---|---|
| 1 | PHP | Sunucu tarafı artımlı eşitleme (CoreMusic ana dili) | strict_types, PDO prepare |
| 2 | JavaScript | Tarayıcı/Node istemci senkronu | async/await, `var`/`any` yok |
| 3 | TypeScript | Tip güvenli sync worker | `Result` benzeri union tip |
| 4 | Python | Veri boru hattı senkronu | exception, backoff |
| 5 | Go | Servis tarafı worker | explicit error, context |
| 6 | Java | Kurumsal kuyruk tüketici | checked exception, Optional |
| 7 | C# | .NET worker servisi | async Task, nullable |
| 8 | Rust | Güvenli kuyruk tüketici | `Result`, `?` operator |
| 9 | C++ | Gömülü/gerçek zamanlı taraf | `std::optional`, `noexcept` |
| 10 | Ruby | Betik/otomasyon senkronu | begin/rescue |
| 11 | Kotlin | Android/JS-side worker | sealed class, coroutines |
| 12 | Swift | iOS tarafı senkronu | `throws`, `Codable` |
| 13 | Elixir | Dağıtık, fault-tolerant senkron | pattern match, GenServer |

> **Uyarı:** Örneklerdeki tipler (`ChangeEvent`, `Store`) **şematadır**;
> gerçek karşılıkları K007-data şemasına bağlıdır → `⚠️ VERIFICATION REQUIRED`.

---

## §2 PHP

```php
# Dil: PHP 8.4 — Rol: Sunucu tarafı artımlı eşitleme (PDO)
<?php
declare(strict_types=1);

final class SyncWorker
{
    public function __construct(
        private PDO $pdo,
        private SyncClient $client,
    ) {}

    /** Adım 1-5: cursor → delta → idempotent apply → checkpoint. */
    public function runOnce(): int
    {
        $cursor = $this->readCheckpoint();          // Adım 1 (yoksa null = full)
        $page   = $this->client->delta($cursor);    // Adım 2

        $this->pdo->beginTransaction();
        foreach ($page->events as $event) {
            $this->applyIdempotent($event);         // Adım 3: aynı event 2x → tek etki
        }
        $this->pdo->commit();
        $this->writeCheckpoint($page->nextCursor);  // Adım 5: ÖNCE uygula, SONRA kaydet
        return count($page->events);
    }

    /** Adım 3 ayrıntısı: natural key ile upsert = idempotent apply. */
    private function applyIdempotent(ChangeEvent $event): void
    {
        $sql = 'INSERT INTO sync_apply (entity_id, version, payload)
                VALUES (:id, :v, :p)
                ON DUPLICATE KEY UPDATE
                  payload = IF(VALUES(version) > version, VALUES(payload), payload),
                  version = GREATEST(version, VALUES(version))';
        $st = $this->pdo->prepare($sql);
        $st->execute([':id' => $event->entityId, ':v' => $event->version, ':p' => $event->payload]);
    }
}
# Kaynak: [K1] (cursor/checkpoint akışı) · [K3] (idempotent upsert mantığı) · [K5] (önce apply, sonra checkpoint)
# Not: ON DUPLICATE KEY ile versiyon yarışı; çakışma stratejisi sync-anayasa §5'e bağlıdır.
```

---

## §3 JavaScript

```javascript
# Dil: JavaScript (ES2022+) — Rol: Tarayıcı/Node istemci senkronu
// strict: var yok, any yok, async/await
async function runOnce({ store, client, checkpointKey }) {
  // Adım 1
  const cursor = await store.get(checkpointKey); // yoksa null → full sync

  // Adım 2 + Adım 4 (429 → Retry-After / jitter'lı backoff)
  const page = await retryWithBackoff(() => client.delta(cursor));

  // Adım 3: idempotent apply — natural key + version guard
  for (const ev of page.events) {
    const current = await store.get(ev.entityId);
    if (current && current.version >= ev.version) continue; // tekrar → yok say
    await store.put(ev.entityId, { version: ev.version, payload: ev.payload });
  }

  // Adım 5
  await store.put(checkpointKey, page.nextCursor);
  return page.events.length;
}

async function retryWithBackoff(fn, maxAttempts = 5) {
  for (let attempt = 0; ; attempt++) {
    try {
      return await fn();
    } catch (err) {
      if (attempt >= maxAttempts - 1) throw err;
      const retryAfter = Number(err.retryAfterSec ?? 0);      // 429 + Retry-After
      const base = retryAfter > 0 ? retryAfter * 1000 : 2 ** attempt * 500;
      await new Promise((r) => setTimeout(r, base + Math.random() * 250)); // jitter
    }
  }
}
// Kaynak: [K2] (410/429 aksiyonları) · [K6] (Retry-After, jitter) · [K5] (checkpoint sırası)
```

---

## §4 TypeScript

```typescript
# Dil: TypeScript — Rol: Tip güvenli sync worker
type ChangeEvent = { entityId: string; version: number; payload: string };
type Page = { events: ChangeEvent[]; nextCursor: string | null };
type SyncError = { kind: 'rate_limited' | 'sync_state_gone' | 'network'; retryAfterSec?: number };

async function runOnce(
  store: { get(k: string): Promise<string | null>; put(k: string, v: string): Promise<void> },
  client: { delta(cursor: string | null): Promise<Page> },
  checkpointKey: string,
): Promise<number> {
  // Adım 1
  const cursor = await store.get(checkpointKey);

  // Adım 2 + 4 (union tip ile hata ayrımı — throw değil, tip ile)
  const page = await retry(() => client.delta(cursor));

  // Adım 3
  for (const ev of page.events) {
    const raw = await store.get(ev.entityId);
    const current = raw ? (JSON.parse(raw) as { version: number }) : null;
    if (current && current.version >= ev.version) continue;
    await store.put(ev.entityId, JSON.stringify({ version: ev.version, payload: ev.payload }));
  }

  // Adım 5
  if (page.nextCursor !== null) await store.put(checkpointKey, page.nextCursor);
  return page.events.length;
}

async function retry<T>(fn: () => Promise<T>, max = 5): Promise<T> {
  for (let attempt = 0; ; attempt++) {
    try { return await fn(); }
    catch {
      if (attempt === max - 1) throw new Error('sync failed');
      await sleep(backoffMs(attempt));
    }
  }
}
const backoffMs = (a: number): number => 2 ** a * 500 + Math.random() * 250; // jitter
const sleep = (ms: number): Promise<void> => new Promise((r) => setTimeout(r, ms));
// Kaynak: [K6] (jitter) · [K5] (checkpoint sırası) · [K1] (nextCursor akışı)
```

---

## §5 Python

```python
# Dil: Python 3.12 — Rol: Veri boru hattı senkronu
import json, random, time, urllib.error

def run_once(store, client) -> int:
    """Adım 1-5: cursor → delta → idempotent apply → checkpoint."""
    cursor = store.get("sync.checkpoint")            # Adım 1 (None = full sync)
    page = retry_with_backoff(lambda: client.delta(cursor))  # Adım 2 + 4

    for ev in page["events"]:                        # Adım 3: idempotent apply
        current = store.get(ev["entityId"])
        if current is not None and json.loads(current)["version"] >= ev["version"]:
            continue                                 # tekrar gelen olay yok sayılır
        store.put(ev["entityId"], json.dumps({"version": ev["version"],
                                               "payload": ev["payload"]}))
    if page["nextCursor"] is not None:
        store.put("sync.checkpoint", page["nextCursor"])   # Adım 5
    return len(page["events"])

def retry_with_backoff(fn, max_attempts=5):
    for attempt in range(max_attempts):
        try:
            return fn()
        except urllib.error.HTTPError as exc:
            if exc.code == 410:                      # sync state gone → full restart
                raise SyncStateGone() from exc
            if exc.code in (429, 403) and attempt < max_attempts - 1:
                retry_after = int(exc.headers.get("Retry-After", 0))
                delay = retry_after or 2 ** attempt
                time.sleep(delay + random.uniform(0, 0.25))  # jitter
                continue
            raise
    raise RuntimeError("sync failed")

class SyncStateGone(RuntimeError):
    """410 Gone / syncStateNotFound → checkpoint fırlatılır, full sync başlar."""
# Kaynak: [K2] (410 → full) · [K6] (429 + Retry-After) · [K6b] (jitter)
```

---

## §6 Go

```go
# Dil: Go — Rol: Servis tarafı sync worker (explicit error)
package syncworker

func (w *Worker) RunOnce(ctx context.Context) (int, error) {
	// Adım 1
	cursor, err := w.store.GetCheckpoint(ctx)
	if err != nil {
		return 0, fmt.Errorf("checkpoint read: %w", err)
	}

	// Adım 2 + 4 (429 → Retry-After, yoksa jitter'lı backoff)
	page, err := w.retry(ctx, func() (*Page, error) { return w.client.Delta(ctx, cursor) })
	if err != nil {
		return 0, fmt.Errorf("delta: %w", err)
	}

	// Adım 3 + 5: önce uygula (tx), sonra checkpoint
	tx, err := w.db.BeginTx(ctx, nil)
	if err != nil {
		return 0, err
	}
	for _, ev := range page.Events {
		if err := applyIdempotent(ctx, tx, ev); err != nil {
			_ = tx.Rollback() // hata: checkpoint yazılmaz → retry güvenli
			return 0, err
		}
	}
	if err := tx.Commit(); err != nil {
		return 0, err
	}
	if page.NextCursor != "" {
		if err := w.store.WriteCheckpoint(ctx, page.NextCursor); err != nil {
			return 0, fmt.Errorf("checkpoint write: %w", err)
		}
	}
	return len(page.Events), nil
}

// applyIdempotent: doğal anahtar + sürüm koruması (sıra K-3 kapısı).
func applyIdempotent(ctx context.Context, tx *sql.Tx, ev Event) error {
	const q = `INSERT INTO sync_apply (entity_id, version, payload) VALUES ($1,$2,$3)
	           ON CONFLICT (entity_id) DO UPDATE SET payload = EXCLUDED.payload, version = EXCLUDED.version
	           WHERE EXCLUDED.version > sync_apply.version`
	_, err := tx.ExecContext(ctx, q, ev.EntityID, ev.Version, ev.Payload)
	return err
}
// Kaynak: [K5] (commit → checkpoint sırası) · [K3] (sürüm koruması) · [K6] (backoff)
```

---

## §7 Java

```java
# Dil: Java 21 — Rol: Kuyruk tüketen kurumsal worker
final class SyncWorker {
    private final Store store;
    private final SyncClient client;
    private final int maxAttempts = 5;

    int runOnce() throws SyncFailedException {
        // Adım 1
        Optional<String> cursor = store.readCheckpoint();   // boşsa full sync

        // Adım 2 + 4
        Page page = retry(() -> client.delta(cursor.orElse(null)));

        // Adım 3 + 5 (try-with-resources: apply başarılı olmazsa checkpoint yazılmaz)
        try (var tx = store.begin()) {
            for (ChangeEvent ev : page.events()) {
                tx.applyIdempotent(ev.entityId(), ev.version(), ev.payload());
            }
            tx.commit();
        }
        page.nextCursor().ifPresent(store::writeCheckpoint); // Adım 5
        return page.events().size();
    }

    private <T> T retry(Callable<T> call) {
        for (int attempt = 0; ; attempt++) {
            try {
                return call.call();
            } catch (RateLimited e) {                 // 429 + Retry-After
                if (attempt >= maxAttempts - 1) throw new SyncFailedException(e);
                sleep(backoffMillis(attempt, e.retryAfterSec()));
            } catch (Exception e) {
                throw new SyncFailedException(e);
            }
        }
    }

    private static long backoffMillis(int attempt, long retryAfterSec) {
        if (retryAfterSec > 0) return retryAfterSec * 1000L;   // sunucu dediğine uy
        return (1L << attempt) * 500L + ThreadLocalRandom.current().nextLong(250); // jitter
    }
}
// Kaynak: [K6] (Retry-After önceliği) · [K6b] (jitter) · [K5] (checkpoint sırası)
```

---

## §8 C\#

```csharp
# Dil: C# (.NET 8) — Rol: Worker servisi (#nullable enable)
#nullable enable

namespace Sync.Worker;

public sealed class SyncWorker(SyncClient client, ICheckpointStore store, ISyncApplyStore apply)
{
    public async Task<int> RunOnceAsync(CancellationToken ct)
    {
        // Adım 1
        string? cursor = await store.ReadAsync(ct);        // null → full sync

        // Adım 2 + 4
        var page = await RetryAsync(() => client.DeltaAsync(cursor, ct), ct);

        // Adım 3
        foreach (var ev in page.Events)
            await apply.UpsertIfNewerAsync(ev.EntityId, ev.Version, ev.Payload, ct);

        // Adım 5
        if (page.NextCursor is not null)
            await store.WriteAsync(page.NextCursor, ct);

        return page.Events.Count;
    }

    private static async Task<T> RetryAsync<T>(Func<Task<T>> fn, CancellationToken ct)
    {
        for (var attempt = 0; ; attempt++)
        {
            try { return await fn(); }
            catch (RateLimitedException ex) when (attempt < 4)
            {
                var baseDelay = ex.RetryAfter ?? TimeSpan.FromSeconds(Math.Pow(2, attempt));
                var jitter = TimeSpan.FromMilliseconds(Random.Shared.Next(250));
                await Task.Delay(baseDelay + jitter, ct);   // jitter
            }
        }
    }
}
// Kaynak: [K6] (Retry-After) · [K5] (checkpoint sırası) · [K1] (cursor akışı)
```

---

## §9 Rust

```rust
# Dil: Rust — Rol: Güvenli kuyruk tüketici (Result, ? operator)
pub async fn run_once(store: &dyn Store, client: &dyn SyncClient) -> Result<usize, SyncError> {
    // Adım 1
    let cursor = store.read_checkpoint().await?;          // None → full sync

    // Adım 2 + 4
    let page = retry(|| client.delta(cursor)).await?;

    // Adım 3 (idempotent apply; sürüm koruması DB/uygulama katmanında)
    for ev in &page.events {
        store.upsert_if_newer(&ev.entity_id, ev.version, &ev.payload).await?;
    }

    // Adım 5: apply başarılıysa checkpoint
    if let Some(next) = &page.next_cursor {
        store.write_checkpoint(next).await?;
    }
    Ok(page.events.len())
}

async fn retry<T, F, Fut>(mut f: F) -> Result<T, SyncError>
where
    F: FnMut() -> Fut,
    Fut: Future<Output = Result<T, SyncError>>,
{
    let mut attempt = 0u32;
    loop {
        match f().await {
            Ok(v) => return Ok(v),
            Err(SyncError::RateLimited { retry_after }) if attempt < 4 => {
                let base = retry_after.map(|s| s as u64 * 1000)
                    .unwrap_or(2u64.pow(attempt) * 500);
                sleep(Duration::from_millis(base + rand_jitter(250))).await;
                attempt += 1;
            }
            Err(e) => return Err(e),
        }
    }
}
// Kaynak: [K6] · [K6b] (jitter) · [K5] (checkpoint sırası)
```

---

## §10 C++

```cpp
// Dil: C++20 — Rol: Gömülü/gerçek zamanlı taraf (RAII, std::optional)
#include <optional>
#include <string>
#include <chrono>
#include <thread>
#include <random>

// Veri sözleşmesi: {entity_id, version, payload} — şema K007'ye bağlıdır (⚠️).
struct ChangeEvent {
    std::string entity_id;
    std::int64_t version;
    std::string payload;
};

class SyncWorker {
public:
    // Adım 1-5 — hata: std::error_code (noexcept yollar throwsuzdur)
    int run_once() {
        std::optional<std::string> cursor = store_.read_checkpoint(); // Adım 1
        auto page = retry_delta(cursor);                              // Adım 2 + 4

        for (const auto& ev : page.events) {
            store_.apply_idempotent(ev.entity_id, ev.version, ev.payload); // Adım 3
        }
        if (page.next_cursor.has_value()) {
            store_.write_checkpoint(*page.next_cursor);               // Adım 5
        }
        return static_cast<int>(page.events.size());
    }

private:
    Page retry_delta(const std::optional<std::string>& cursor) {
        for (int attempt = 0; ; ++attempt) {
            auto result = client_.delta(cursor);
            if (result) return *result;
            if (attempt >= 4) throw SyncError{result.error()};
            const auto retry_after = result.retry_after_sec();        // 429 + Retry-After
            const auto base = retry_after > 0
                ? std::chrono::seconds{retry_after}
                : (1 << attempt) * 500ms;
            std::this_thread::sleep_for(base + jitter(250ms));        // jitter
        }
    }

    static std::chrono::milliseconds jitter(std::chrono::milliseconds max) {
        static std::mt19937 rng{std::random_device{}()};
        return std::chrono::milliseconds{std::uniform_int_distribution<int>(0, max.count())(rng)};
    }

    Store& store_;
    SyncClient& client_;
};
// Kaynak: [K6] (Retry-After) · [K6b] (jitter) · [K5] (checkpoint sırası)
// Not: gerçek zamanlı yolda bellek baştan allocate edilir (blok yok) — audio-engine kuralları ile uyumlu.
```

---

## §11 Ruby

```ruby
# Dil: Ruby — Rol: Otomasyon/ayar-betiği senkronu
def run_once(store, client)
  cursor = store.read("sync.checkpoint")            # Adım 1 (yoksa full sync)
  page   = retry_with_backoff { client.delta(cursor) } # Adım 2 + 4

  page.events.each do |ev|                          # Adım 3: idempotent apply
    current = store.read(ev.entity_id)
    next if current && current[:version] >= ev.version # tekrar → yok say
    store.write(ev.entity_id, { version: ev.version, payload: ev.payload })
  end

  store.write("sync.checkpoint", page.next_cursor) if page.next_cursor # Adım 5
  page.events.size
end

def retry_with_backoff(max_attempts: 5)
  attempt = 0
  begin
    yield
  rescue RateLimited => e            # 429 + Retry-After
    attempt += 1
    raise if attempt >= max_attempts
    delay = e.retry_after.to_i.positive? ? e.retry_after.to_i : 2**attempt
    sleep(delay + rand(0.25))        # jitter
    retry
  rescue SyncStateGone               # 410 / syncStateNotFound → full sync
    raise FullSyncRequired
  end
end
# Kaynak: [K2] (410 → full) · [K6] (Retry-After) · [K6b] (jitter)
```

---

## §12 Kotlin

```kotlin
# Dil: Kotlin — Rol: Android/tarayıcı tarafı worker (sealed class, coroutine)
sealed class SyncResult {
    data class Applied(val count: Int) : SyncResult()
    data class RateLimited(val retryAfterSec: Long) : SyncResult()
    object StateGone : SyncResult()          // 410 / syncStateNotFound → full sync
}

suspend fun runOnce(store: Store, client: SyncClient): SyncResult {
    // Adım 1
    val cursor = store.readCheckpoint()                       // null → full sync

    // Adım 2 + 4
    val page = retry { client.delta(cursor) }.getOrElse { return it.asResult() }

    // Adım 3
    page.events.forEach { ev ->
        val current = store.get(ev.entityId)
        if (current != null && current.version >= ev.version) return@forEach
        store.put(ev.entityId, SyncState(ev.version, ev.payload))
    }

    // Adım 5
    page.nextCursor?.let { store.writeCheckpoint(it) }
    return SyncResult.Applied(page.events.size)
}

suspend fun <T> retry(max: Int = 5, block: suspend () -> T): Result<T> {
    var attempt = 0
    while (true) {
        try { return Result.success(block()) }
        catch (e: RateLimitedException) {
            if (++attempt >= max) return Result.failure(e)
            val base = e.retryAfterSec?.takeIf { it > 0 } ?: (1L shl attempt)
            delay(base * 1000 + (0..250).random())            // jitter
        }
        catch (e: SyncGoneException) { return Result.failure(e) }
        catch (e: Exception) { return Result.failure(e) }
    }
}
// Kaynak: [K6] · [K6b] (jitter) · [K5] (checkpoint sırası)
```

---

## §13 Swift

```swift
# Dil: Swift 5.9 — Rol: iOS tarafı senkronu (throws, Codable)
struct ChangeEvent: Codable {
    let entityId: String
    let version: Int
    let payload: String
}

enum SyncError: Error {
    case rateLimited(retryAfter: TimeInterval?)
    case stateGone                 // 410 / syncStateNotFound → full sync
    case failed(underlying: Error)
}

func runOnce(store: Store, client: SyncClient) throws -> Int {
    // Adım 1
    let cursor = try store.readCheckpoint()               // nil → full sync

    // Adım 2 + 4
    let page = try retry { try client.delta(cursor: cursor) }

    // Adım 3
    for ev in page.events {
        if let current = try store.get(ev.entityId), current.version >= ev.version {
            continue                                     // tekrar → yok say
        }
        try store.put(ev.entityId, SyncState(version: ev.version, payload: ev.payload))
    }

    // Adım 5
    if let next = page.nextCursor {
        try store.writeCheckpoint(next)
    }
    return page.events.count
}

func retry<T>(maxAttempts: Int = 5, _ block: () throws -> T) throws -> T {
    var attempt = 0
    while true {
        do { return try block() }
        catch SyncError.rateLimited(let retryAfter) {
            attempt += 1
            if attempt >= maxAttempts { throw SyncError.failed(underlying: Error.self as! Error) }
            let base = retryAfter ?? pow(2, Double(attempt))
            Thread.sleep(forTimeInterval: base + Double.random(in: 0...0.25)) // jitter
        }
    }
}
// Kaynak: [K6] (Retry-After) · [K6b] (jitter) · [K5] (checkpoint sırası)
// ⚠️ Not: yukarıdaki catch dalındaki cast satırı ÖRNEK amaçlıdır;
// üretimde Error protokolü üzerinden yazılmalıdır (⚠️ VERIFICATION REQUIRED).
```

---

## §14 Elixir

```elixir
# Dil: Elixir — Rol: Dağıtık, hata-tolerant senkron (pattern match, GenServer)
defmodule Sync.Worker do
  use GenServer

  @max_attempts 5

  # Adım 1: state → cursor
  def init(state), do: {:ok, state}

  def handle_info(:tick, state) do
    cursor = Map.get(state, :checkpoint)          # nil → full sync
    case fetch_delta(cursor, 0) do                # Adım 2 + 4
      {:ok, page} ->
        Enum.each(page.events, &apply_idempotent/1)   # Adım 3
        new_state = if page.next_cursor do
          Map.put(state, :checkpoint, page.next_cursor) # Adım 5
        else
          state
        end
        {:noreply, new_state}

      {:error, :state_gone} ->
        # 410 / syncStateNotFound → checkpoint fırlat, full sync başlat
        {:noreply, Map.delete(state, :checkpoint)}

      {:error, _reason} ->
        {:noreply, state}                        # checkpoint DEĞİL → retry güvenli
    end
  end

  defp fetch_delta(cursor, attempt) do
    case SyncClient.delta(cursor) do
      {:ok, page} ->
        {:ok, page}

      {:error, :rate_limited, retry_after} when attempt < @max_attempts ->
        base = retry_after || :math.pow(2, attempt) |> round()
        jitter = :rand.uniform(250)
        Process.sleep(base * 1000 + jitter)      # jitter
        fetch_delta(cursor, attempt + 1)

      {:error, :state_gone, _} ->
        {:error, :state_gone}

      {:error, reason} ->
        {:error, reason}
    end
  end

  # Idempotent apply: doğal anahtar; sürüm yükselmiyorsa dokunma.
  defp apply_idempotent(%{entity_id: id, version: v, payload: p}) do
    SyncRepo.upsert_if_newer(id, v, p)
  end
end
# Kaynak: [K2] (410 → full) · [K6] (Retry-After) · [K6b] (jitter) · [K5] (checkpoint sırası)
```

---

## §15 Dil-Karşılaştırması — 5 Adımın Karşılığı

| Adım | PHP | Go | Rust | Elixir |
|---|---|---|---|---|
| 1 Checkpoint oku | `readCheckpoint()` | `store.GetCheckpoint(ctx)` | `store.read_checkpoint().await?` | `Map.get(state, :checkpoint)` |
| 2 Delta çek | `client->delta($cursor)` | `client.Delta(ctx, cursor)` | `client.delta(cursor)` | `SyncClient.delta(cursor)` |
| 3 Idempotent apply | `ON DUPLICATE KEY` + GREATEST | `ON CONFLICT … WHERE version>` | `upsert_if_newer` | `upsert_if_newer` |
| 4 Retry/backoff | (§2 `retryWithBackoff` JS/Py) | `retry(...)` | `retry(...)` | `fetch_delta(cursor, attempt)` |
| 5 Checkpoint yaz | apply **sonrası** | `tx.Commit()` **sonrası** | apply **sonrası** | `Map.put(state, …)` |

> **Ortak kanıt (5. adım):** Checkpoint, uygulama **başarılı olduktan**
> sonra yazılır — aksi halde çöküşte veri kaybı oluşur; bu sıra [K5]'teki
> `startedAt`/cursor akışı ve [K1]'deki tutarlılık gereğidir.

---

## §16 Kaynak / Dosya-Satır Ayrımı

| Örnek grubu | Dayanağı | Dayanak türü |
|---|---|---|
| Cursor/checkpoint akışı (tüm diller) | [K1] AppSync `lastSync`/`startedAt` | Resmi doküman (fetch) |
| 410 → full sync (Py/Ruby/Elixir/Kotlin) | [K2] Graph delta-query-overview | Resmi doküman (fetch) |
| Idempotent upsert (PHP/Go/…) | [K3] Stripe idempotency + RFC 9110 §9.2.2 | Resmi doküman (fetch) |
| Backoff + jitter (tüm diller) | [K6] Graph throttling · [K6b] Stripe rate-limits | Resmi doküman (fetch) |
| Sıra (anahtar başına) | [K4] Pub/Sub ordering key | Resmi doküman (fetch) |
| **Dosya/Satır kanıtı** | **YOK** — bu örnekler **repo'dan değildir** | Depoda sync kodu tespit edilmedi → `⚠️` |

> **Ayrım kuralı:** Bu dosyada `Dosya: … / Satır: …` **yazılmamıştır**;
> çünkü örneklerin karşılığı depoda gözlemlenmedi. Repo kanıtı eklendiğinde
> §16 tablosuna `Dosya/Satır` sütunu açılacak ve `sync-dogrulama.md` kapıları
> (K-1…K-6) çalıştırılacaktır.

---

## §17 Kaynak Tablosu

| # | Kaynak | Örneklere dayandığı iddia | Tarih | Durum |
|---|---|---|---|---|
| K1 | https://docs.aws.amazon.com/appsync/latest/devguide/tutorial-delta-sync.html | cursor (`lastSync`/`startedAt`), base vs delta, checkpoint akışı, TTL catch-up | 2026-10-16 | ✅ fetch |
| K2 | https://learn.microsoft.com/en-us/graph/delta-query-overview | 410 Gone / `syncStateNotFound` → full sync, replay toleransı | 2026-10-16 | ✅ fetch |
| K3 | https://docs.stripe.com/idempotency | Idempotency-Key üretimi/uzunluğu/pruning, tekrarın aynı yanıtı vermesi | 2026-10-16 | ✅ fetch |
| K4 | https://cloud.google.com/pubsub/docs/ordering | Ordering key, at-least-once, yeniden teslim zinciri, hot key | 2026-10-16 | ✅ fetch |
| K5 | https://www.rfc-editor.org/rfc/rfc9110.html | §9.2.2 idempotent method → güvenli retry; §13 koşullu istekler | 2026-10-16 | ✅ fetch |
| K6 | https://learn.microsoft.com/en-us/graph/throttling | 429 + `Retry-After`, header yoksa exponential backoff | 2026-10-16 | ✅ fetch |
| K6b | https://docs.stripe.com/rate-limits | Jitter'lı exponential backoff, `Stripe-Rate-Limited-Reason`, eşzamanlılık | 2026-10-16 | ✅ fetch |

> **Kapsam beyanı:** Dayanak kümesi yalnız bu tablodur. Örneklerdeki tipli
> adlar (`ChangeEvent`, `Store`, `SyncClient`) **şema isimleridir** →
> K007-data şemasına bağlıdır, `⚠️ VERIFICATION REQUIRED`.

---

## §18 Red Listesi

| # | Yazılmayan iddia | Neden |
|---|---|---|
| 1 | "`sync-worker.php` dosyası şudur (Satır: 42)" | Repo'da sync kodu **tespit edilmedi** → `⚠️` |
| 2 | "CoreMusic bu 13 dili de kullanacak" | Karar yok → katalog **tasarım** |
| 3 | "Örnekler testten geçti" | Test **çalıştırılmadı** → `⚠️` |
| 4 | "X kütüphanesi kullanılacak" (ör. `resilience4j`, `tokio-retry`) | Bağımlılık **seçilmedi** → örnekler stdlib/elle yazıldı |
| 5 | "Sıra garantisi tüm örneklerde sağlanır" | Sıra **kuyruk altyapısına** bağlı → [K4] şartları |

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | 13 dilde aynı 5 adımın varlığı | ✅ §2–§14 (yapısal) |
| 2 | Repo dosya/satır kanıtı | ⚠️ VERIFICATION REQUIRED (§16) |
| 3 | Örneklerin test edilmesi | ⚠️ PLANNED (`sync-dogrulama` K-1…K-6) |
| 4 | Şema adlarının K007 ile eşleşmesi | ⚠️ (§16/§17 notu) |
| 5 | Hangi dillerin üretime alınacağı | `UNKNOWN` (§1/§18-2) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** AWS AppSync · Microsoft Graph (delta/throttling) · Stripe (idempotency/rate-limits) · IETF RFC 9110 · Google Cloud Pub/Sub · **Tarih: 2026-10-16**
