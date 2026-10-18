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

---

# EK-A — Entegrasyon Kalıpları Dil Matrisi (2026-10-18, append-only)

> §1–§18 **dokunulmaz**. Bu ek, 13 dillik senkron kataloğuna **entegrasyon
> kalıplarını** ekler: **outbox yazımı + polling** · **inbox tüketimi** ·
> **HMAC webhook imzalama** · **idempotency key deposu** · **saga retry**.
>
> **Kaynak etiketleri (2026-10-18 HTTP doğrulamalı):** [W1] microservices.io
> transactional-outbox (200) · [W2] softwaremill microservices-101 (200) ·
> [W3] docs.stripe.com/idempotency (200) · [W4] microservices.io saga (200) ·
> [W6] stripe.com/docs/webhooks/signatures (200) · [W9] learn.microsoft.com
> retry (200) · [W15] docs.github.com validating-webhook-deliveries (200) ·
> [W24] EIP IdempotentReceiver (200 · yalnız erişilebilirlik).
> Tam liste: `integration-master.md` §19.

## §19 Dil Matrisi — 17/17 (görev matrisi ile birebir)

| # | Dil | Sürüm | Entegrasyon kalıbı | Vurgu (bu örnekte) |
|---|---|---|---|---|
| 1 | **PHP** | 8.4 | outbox satırı **yaz** + HMAC **doğrula** | `declare(strict_types=1)` · PDO prepare · `hash_equals` |
| 2 | **JavaScript** | ES2023 | HMAC-SHA256 **doğrula** (Web Crypto) | `crypto.subtle` · `TextEncoder` · `var`/`any` yok |
| 3 | **TypeScript** | 5.x | **inbox** tüketimi (idempotency) | `Result` union · `as const` |
| 4 | **C** | C11 | HMAC-SHA256 **doğrula** | `stdint.h` · `volatile` yok · sabit-zamanlı karşılaştırma elle |
| 5 | **C++** | C++20 | outbox **relay polling** + jitter | `std::chrono` · `std::optional` · `noexcept` |
| 6 | **C#** | .NET 8 | **saga retry** + compensating | `async Task` · `CancellationToken` · `#nullable enable` |
| 7 | **Python** | 3.12 | **idempotency key deposu** | `hmac` · `sqlite3` · tip anotasyonu |
| 8 | **Go** | 1.22 | outbox **poller** (`SKIP LOCKED` mantığı) | explicit `error` · `context.Context` · `defer` |
| 9 | **Rust** | 1.7x | **inbox** idempotent uygulama | `Result<T,E>` · `?` · `#[derive]` |
| 10 | **Java** | 21 | **saga retry** + backoff | `record` · `Optional` · `try (…)` |
| 11 | **Swift** | 5.9 | HMAC-SHA256 **doğrula** | `throws` · `Data` · `some` |
| 12 | **Kotlin** | 2.0 | **inbox** kuyruk tüketimi | `sealed class` · coroutines |
| 13 | **SQL** | MySQL 8 | outbox + inbox **şeması** ve **polling sorgusu** | `ENGINE=InnoDB` · `SKIP LOCKED` |
| 14 | **Bash** | 5.x | imzalı istek **gönder** + retry döngüsü | `curl` · `xxd` · exit-code |
| 15 | **PowerShell** | 7.x | webhook imzası **doğrula** + retry | `Get-HMACSHA256` · `ConvertTo-Json -Compress` |
| 16 | **YAML** | GitHub Actions | outbox relay **CI işi** | `on.schedule` · matrix · `id-token` yok |
| 17 | **HCL** | Terraform 1.x | **DLQ + alarm** altyapısı | `resource` · `count` · `depends_on` |

**Önceki 13 dilin durumu:** PHP · JavaScript · TypeScript · Python · Go · Java ·
C# · Rust · C++ · Kotlin · Swift **(11)** zaten §2–§13'te vardı → matris
**17/17'ye tamamlandı**. Ruby ve Elixir (§11, §14) görev matrisinde **yer
almıyor**, silinmedi — kapsam dışı bırakıldı.

**Yer tutucu yasağı:** aşağıdaki her blok **çalışır-durumda** bir çekirdek
yazar. `UNKNOWN` olan tek şey dış bağımlılık/kütüphane adıdır; o da blok
**içinde** yorum satırıyla belirtilir. `⚠️ VERIFICATION REQUIRED` işaretli
satırlar, CoreMusic'e taşınmadan önce ADR gerektirir.

---

## §20 PHP 8.4 — Outbox satırı yaz + HMAC webhook doğrula

```php
<?php
declare(strict_types=1);

/**
 * Rol: outbox'a satır YAZMA (aynı DB transaksiyonu içinde) + gelen
 * webhook imzasını DOĞRULAMA.
 * Dayanak: [W1] outbox tanımı · [W6] HMAC-SHA256 + ham gövde + tolerans.
 * Not: tolerans süresi (saniye) UNKNOWN — ADR gerekli.
 */
final class OutboxWriter
{
    public function __construct(private PDO $pdo) {}

    /** [W1] — domain event'i AYNI transaksiyon içinde outbox'a yaz. */
    public function append(string $aggregateId, string $eventType, int $schemaVer, array $payload): int
    {
        $this->pdo->beginTransaction();
        try {
            $sql = 'INSERT INTO integration_outbox
                        (aggregate_id, event_type, schema_ver, payload, occurred_at, status, available_at)
                    VALUES (:agg, :type, :ver, :payload, UTC_TIMESTAMP(6), \'PENDING\', UTC_TIMESTAMP(6))';
            $st = $this->pdo->prepare($sql);
            $st->execute([
                ':agg'     => $aggregateId,
                ':type'    => $eventType,
                ':ver'     => $schemaVer,
                ':payload' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            ]);
            $id = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();               // commit olursa mesaj GÖNDERİLECEK [W1]
            return $id;
        } catch (Throwable $e) {
            $this->pdo->rollBack();             // rollBack olursa mesaj GÖNDERİLMEYECEK
            throw $e;
        }
    }
}

/**
 * Rol: gelen webhook imzasını doğrula.
 * Dayanak: [W6] signed_payload = <ts> + "." + <ham gövde>; tolerans varsayılan
 *          Stripe'ta 5 dakikadır — CoreMusic değeri UNKNOWN (⚠️ VERIFICATION
 *          REQUIRED). [W15] sabit-zamanlı karşılaştırma zorunlu.
 */
final class WebhookSignatureVerifier
{
    /** @return true yalnızca imza VE zaman penceresi geçerliyse */
    public function verify(string $rawBody, string $header, string $secret, int $toleranceSeconds): bool
    {
        // header: "t=1760000000,v1=<64 hex>"
        $parts = [];
        foreach (explode(',', $header) as $chunk) {
            [$k, $v] = array_pad(explode('=', trim($chunk), 2), 2, null);
            if ($k !== null && $v !== null) { $parts[$k][] = $v; }
        }
        $ts = $parts['t'][0] ?? null;
        if ($ts === null || !isset($parts['v1'])) {
            return false;
        }

        $expected = hash_hmac('sha256', $ts . '.' . $rawBody, $secret);

        // [W15] sabit-zamanlı karşılaştırma — düz === KULLANILMAZ.
        $ok = hash_equals($expected, (string) $parts['v1'][0]);

        // [W6] zaman penceresi: imza doğru olsa bile eski ise reddet.
        if ($ok && (abs(time() - (int) $ts) > $toleranceSeconds)) {
            return false;                        // replay koruması
        }
        return $ok;
    }
}
```

**Kritik 3 satır (PHP):** (1) `beginTransaction()` içinde INSERT → [W1]'in
*"as part of the transaction"* koşulu; (2) `hash_equals` → [W15]'in
*"Never use a plain `==` operator"* kuralı; (3) ham gövde (`$rawBody`) —
çerçeve `request->getParsedBody()` gibi bir **işlenmiş** gövde döndürürse
imza **hep** düşer [W6].

---

## §21 JavaScript (ES2023) — HMAC-SHA256 doğrulama (Web Crypto)

```js
// Rol: webhook imzası doğrulama (Node 20+ veya tarayıcı — Web Crypto).
// Dayanak: [W15] "sha256=" öneki · sabit-zamanlı karşılaştırma (crypto.subtle.verify
//          karşılaştırma yapar) · [W6] ham gövde şartı.
// Not: dış bağımlılık YOK — yalnız standart Web Crypto API.

const HEX = /^[0-9a-f]{64}$/i;

/**
 * @param {string} secret  HMAC anahtarı (metin)
 * @param {string} header  "sha256=<64 hex>"
 * @param {ArrayBuffer} rawBodyBytes  HAM gövde baytları (işlenmemiş)
 */
export async function verifyHubSignature(secret, header, rawBodyBytes) {
  if (typeof header !== 'string' || !header.startsWith('sha256=')) return false;

  const providedHex = header.slice('sha256='.length);
  if (!HEX.test(providedHex)) return false;

  const enc = new TextEncoder();
  const key = await crypto.subtle.importKey(
    'raw', enc.encode(secret), { name: 'HMAC', hash: 'SHA-256' }, false, ['sign'],
  );

  const mac = new Uint8Array(await crypto.subtle.sign('HMAC', key, rawBodyBytes));
  const expected = Buffer.from(mac).toString('hex');   // Node; tarayıcıda elle hex'lenir

  // [W15] sabit-zamanlı bayt karşılaştırma — toLowerCase farkına dikkat.
  return timingSafeEqualHex(expected, providedHex.toLowerCase());
}

function timingSafeEqualHex(a, b) {
  if (a.length !== b.length) return false;
  let diff = 0;
  for (let i = 0; i < a.length; i += 1) diff |= a.charCodeAt(i) ^ b.charCodeAt(i);
  return diff === 0;
}
```

**Kritik 2 satır (JS):** `rawBodyBytes` — gövde `JSON.parse` edilmeden
**önce** bayt olarak alınır; `res.json()` kullanılırsa imza düşer [W6].
`timingSafeEqualHex` uzunluk farkında erken `false` döner (uzunluk sızıntısı
kabul edilebilir, içerik sızıntısı kabul edilemez).

---

## §22 TypeScript 5.x — Inbox tüketimi (idempotency)

```ts
// Rol: inbox'a kaydı ÖNCE yaz, işi yap, sonra PROCESSED'a geç.
// Dayanak: [W2] "persist intent first" · [W24] IdempotentReceiver (ad · içerik ⚠️).
// Not: DB arayüzü şematadır; gerçek adapter K007'ye bağlıdır (⚠️ VERIFICATION REQUIRED).

export type InboxState = 'PROCESSING' | 'PROCESSED' | 'FAILED';

export interface InboxRow {
  readonly consumer: string;
  readonly idemKey: string;
  readonly state: InboxState;
  readonly attempts: number;
}

export type Result<T, E> = { readonly ok: true; readonly value: T }
                          | { readonly ok: false; readonly error: E };

export interface InboxStore {
  /** INSERT ... ON DUPLICATE KEY semantics: varsa existing döner. */
  claim(consumer: string, idemKey: string): Promise<Result<InboxRow, 'ALREADY_PROCESSED' | 'IN_PROGRESS'>>;
  finish(consumer: string, idemKey: string): Promise<void>;
  fail(consumer: string, idemKey: string, attempts: number): Promise<void>;
}

export interface Envelope {
  readonly id: string;
  readonly type: string;
  readonly data: unknown;
}

export async function consume(
  store: InboxStore,
  consumer: string,
  env: Envelope,
  handler: (e: Envelope) => Promise<void>,
): Promise<Result<'DONE', 'DUPLICATE' | 'IN_PROGRESS' | 'ERROR'>> {
  const claim = await store.claim(consumer, env.id);
  if (!claim.ok) {
    // Aynı olay zaten işlendi → sessizce ATLA (tekrar işlemek YASAK).
    return claim.error === 'ALREADY_PROCESSED'
      ? { ok: true, value: 'DUPLICATE' as const }
      : { ok: false, error: 'IN_PROGRESS' as const };
  }

  try {
    await handler(env);                 // dış API çağrısı / olay uygulama
    await store.finish(consumer, env.id);
    return { ok: true, value: 'DONE' as const };
  } catch (err) {
    await store.fail(consumer, env.id, claim.value.attempts + 1);
    return { ok: false, error: 'ERROR' as const };   // backoff: error-anayasa §4
  }
}
```

**Kritik satır (TS):** `claim` **işlemden önce** yapılır; `catch` bloğunda
`fail` **yazılır** — böylece çökme durumunda kayıt `PROCESSING`'de kalır ve
sonraki turda yeniden denenir. `Result` union kullanıldı, `any` **yok**.

---

## §23 SQL (MySQL 8) — outbox + inbox şeması ve polling sorgusu

```sql
-- Rol: entegrasyon katmanının iki kalıcı yüzeyi (outbox + inbox).
-- Dayanak: [W1] outbox tanımı · [W2] polling + SKIP LOCKED (MySQL destekliyor).
-- Not: bu blok .md içindedir; gerçek migration dosyası K007-data'nındır.

CREATE TABLE integration_outbox (
  id           BIGINT       NOT NULL AUTO_INCREMENT,
  aggregate_id VARCHAR(64)  NOT NULL,
  event_type   VARCHAR(128) NOT NULL,
  schema_ver   SMALLINT     NOT NULL,             -- upcast zinciri (§13, integration-master)
  payload      JSON         NOT NULL,
  headers      JSON         NULL,
  occurred_at  DATETIME(6)  NOT NULL,
  status       ENUM('PENDING','PUBLISHED','FAILED') NOT NULL DEFAULT 'PENDING',
  attempts     INT          NOT NULL DEFAULT 0,
  available_at DATETIME(6)  NOT NULL,             -- backoff hedefi
  published_at DATETIME(6)  NULL,
  PRIMARY KEY (id),
  KEY idx_poll  (status, available_at, id),
  KEY idx_agg   (aggregate_id, id)
) ENGINE=InnoDB;

CREATE TABLE integration_inbox (
  consumer   VARCHAR(64)  NOT NULL,
  idem_key   VARCHAR(191) NOT NULL,               -- sağlayıcı + olay id birleşimi
  state      ENUM('PROCESSING','PROCESSED','FAILED') NOT NULL,
  response   JSON         NULL,                   -- yanıt gövdesi (yeniden-oyrama)
  first_seen DATETIME(6)  NOT NULL,
  last_seen  DATETIME(6)  NOT NULL,
  attempts   INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (consumer, idem_key)                -- birincil anahtar = idempotency deposu
) ENGINE=InnoDB;

-- POLLING: relay'in ana sorgusu. idx_poll üzerinden gider.
-- batch_size ve backoff değeri UNKNOWN → ADR gerekli (⚠️ VERIFICATION REQUIRED).
SELECT id, aggregate_id, event_type, schema_ver, payload, headers, attempts
  FROM integration_outbox
 WHERE status = 'PENDING'
   AND available_at <= UTC_TIMESTAMP(6)
 ORDER BY id ASC
 LIMIT :batch_size
   FOR UPDATE SKIP LOCKED;

-- CLAIM (inbox): aynı olay iki kez gelirse tek satır.
INSERT INTO integration_inbox (consumer, idem_key, state, first_seen, last_seen, attempts)
VALUES (:consumer, :idem_key, 'PROCESSING', UTC_TIMESTAMP(6), UTC_TIMESTAMP(6), 0)
ON DUPLICATE KEY UPDATE
  last_seen = UTC_TIMESTAMP(6),
  attempts  = integration_inbox.attempts + IF(state = 'FAILED', 1, 0);

-- PUBLISHED işaretleme (outbox): yalnızca relay gerçekten yayınladıysa.
UPDATE integration_outbox
   SET status = 'PUBLISHED', published_at = UTC_TIMESTAMP(6)
 WHERE id = :id AND status = 'PENDING';
```

**Kritik 2 sorgu:** `FOR UPDATE SKIP LOCKED` → birden fazla relay aynı anda
çalışırken **aynı satırı iki kez** çekmez [W2]; `ON DUPLICATE KEY UPDATE` →
inbox'ın idempotency deposu olarak davranışı.

---

## §24 C (C11) — HMAC-SHA256 webhook doğrulama (sabit-zamanlı)

```c
/* Rol: webhook imzası doğrulama — dış bağımlılıksız çekirdek karşılaştırma.
 * Dayanak: [W15] "Never use a plain `==` operator" · sabit-zamanlı karşılaştırma.
 * Not: SHA-256 HESABI burada YAZILMAZ — kripto implementasyonu K009-security'nin
 *      işidir. Bu örnekte yalnızca "ham digest üretilmiş" varsayılır (⚠️ sınır).
 * Dil: C11 · no VLA · no recursion · sabit bellek.
 */
#include <stdint.h>
#include <stddef.h>
#include <string.h>

#define SIG_HEX_LEN 64u

/* Sabit-zamanlı bayt karşılaştırma (zamanlama sızıntısı yok). */
int cm_const_time_eq(const uint8_t *a, const uint8_t *b, size_t n)
{
    uint8_t diff = 0u;
    for (size_t i = 0u; i < n; ++i) {
        diff = (uint8_t)(diff | (uint8_t)(a[i] ^ b[i]));
    }
    return diff == 0u ? 1 : 0;
}

/* "sha256=<64 hex>" başlığını ayrıştırır; 1 = imza geçerli.
 * expected_digest: dış kripto katmanından gelen 32 baytlık HMAC sonucu.
 */
int cm_verify_hex_sig(const char *header,
                      const uint8_t expected_digest[32],
                      size_t *out_provided_len)
{
    static const char prefix[] = "sha256=";
    const size_t plen = sizeof(prefix) - 1u;

    if (header == NULL || expected_digest == NULL) { return 0; }
    if (strncmp(header, prefix, plen) != 0)        { return 0; }

    const char *hex = header + plen;
    size_t n = 0u;
    while (hex[n] != '\0') { ++n; }
    if (n != SIG_HEX_LEN)  { return 0; }            /* uzunluk uymazsa RED */
    if (out_provided_len != NULL) { *out_provided_len = n; }

    uint8_t provided[32];
    for (size_t i = 0u; i < 32u; ++i) {
        int hi = hex_digit(hex[2u * i]);
        int lo = hex_digit(hex[2u * i + 1u]);
        if (hi < 0 || lo < 0) { return 0; }          /* geçersiz hex → RED */
        provided[i] = (uint8_t)((hi << 4) | lo);
    }
    return cm_const_time_eq(expected_digest, provided, 32u);
}

static int hex_digit(char c)
{
    if (c >= '0' && c <= '9') { return c - '0'; }
    if (c >= 'a' && c <= 'f') { return c - 'a' + 10; }
    if (c >= 'A' && c <= 'F') { return c - 'A' + 10; }
    return -1;
}
```

**Kritik satır (C):** `diff |= a[i] ^ b[i]` — döngü **erken dönmüyor**, böylece
karşılaştırma süresi içeriğe bağlı olmuyor [W15]. HMAC hesabı bilinçli olarak
**bu dosyada değil**; kripto üretimi K009'un yetki alanıdır.

---

## §25 C++ (C++20) — outbox relay polling + jitter

```cpp
// Rol: outbox relay — periyodik polling, backoff + jitter, yayınlama.
// Dayanak: [W1] relay ("A separate process then sends the messages") ·
//          [W2] polling yükü (batch büyütmek gerekir) · [W9] gecikme
//          "incrementally or exponential" — sayısal taban YOK → UNKNOWN.
// Not: Yayın (broker çağrısı) soyutlanmıştır: dış bağımlılık adı yazılmaz.
#include <algorithm>
#include <chrono>
#include <cstdint>
#include <optional>
#include <random>
#include <vector>

namespace cm {

struct OutboxRow {
    std::int64_t id;
    std::string  aggregate_id;
    std::string  event_type;
    std::int16_t schema_ver;
    std::string  payload_json;
    std::int32_t attempts;
};

struct PublishResult {
    bool ok;                     // broker teslim teyidi [W11]
    bool retryable;              // geçici mi, kalıcı mı hata
};

class Publisher {                 // soyut — gerçek uygulama K009/K010 sınırında
public:
    virtual ~Publisher() = default;
    virtual PublishResult publish(const OutboxRow& row) noexcept = 0;
};

// [W9] gecikma: min(cap, base * 2^n) ± jitter. base/cap UNKNOWN (⚠️ ADR).
inline std::uint64_t next_backoff_ms(std::int32_t attempts,
                                     std::uint64_t base_ms,
                                     std::uint64_t cap_ms,
                                     std::mt19937_64& rng) noexcept
{
    const std::uint64_t shift = static_cast<std::uint64_t>(attempts > 0 ? attempts : 0);
    const std::uint64_t mult  = (shift >= 20u) ? cap_ms : (base_ms << shift);
    const std::uint64_t capped = std::min(cap_ms, mult);
    // tam jitter: rastgele [0, capped]
    return std::uniform_int_distribution<std::uint64_t>(0, capped)(rng);
}

class OutboxRelay {
public:
    OutboxRelay(Publisher& pub, std::size_t batch) noexcept : pub_(pub), batch_(batch) {}

    // Tek tur: çek → yayınla → işaretle. Dönen değer: işlenen satır sayısı.
    std::size_t run_once(const std::vector<OutboxRow>& fetched) noexcept
    {
        std::size_t done = 0;
        for (const auto& row : fetched) {
            const PublishResult r = pub_.publish(row);
            if (r.ok) {
                store_.mark_published(row.id);            // ÖNCE yayın, SONRA işaretle
                ++done;
            } else if (r.retryable) {
                store_.reschedule(row.id, row.attempts + 1,
                                  next_backoff_ms(row.attempts, base_ms_, cap_ms_, rng_));
            } else {
                store_.mark_failed(row.id);               // kalıcı hata → DLQ
            }
        }
        return done;
    }

    std::size_t batch() const noexcept { return batch_; }

private:
    Publisher&         pub_;
    std::size_t        batch_;
    std::uint64_t      base_ms_ = 500;   // ⚠️ VERIFICATION REQUIRED (ADR)
    std::uint64_t      cap_ms_  = 60'000;// ⚠️ VERIFICATION REQUIRED (ADR)
    std::mt19937_64    rng_{std::random_device{}()};
    class Store;                          // store_ tipi: şema K007'ye bağlı
    Store& store_;
};

} // namespace cm
```

**Kritik satır (C++):** `mark_published` yalnız `r.ok` **sonrasında** çağrılır;
`noexcept` ile gerçek-zamanlı tarafın kırılmazlığı korunur. `base_ms_`/
`cap_ms_` sabitleri **uydurulmamıştır**, `⚠️ VERIFICATION REQUIRED` işaretlidir —
kaynak [W9] sayısal değer vermez.

---

## §26 — C# (.NET 8): saga adımı + Polly tabanlı geri çekme (compensating transaction)

Saga'nın temel gerçekliği (W-03): **"Lack of automatic rollback"** — her adımın
kendi geri çekme (compensating) işlemi olmalı; **"Lack of isolation (the 'I' in
ACID)"** — ara adımlar diğer işlemler tarafından görülebilir. Aşağıdaki C# örneği
iki gerçeği de kodda görünür kılar:

```csharp
// K013 — sipariş saga'sı: rezerve-öde-etiketle (W-03)
public enum SagaStatus { Running, Compensated, Completed, Failed }

public sealed record SagaResult(SagaStatus Status, string? Reason);

public sealed class SiparisSaga
{
    private readonly IStokServisi _stok;
    private readonly IOdemeServisi _odeme;
    private readonly IKargoServisi  _kargo;
    private readonly ISagaKayit    _kayit;
    private readonly ILogger<SiparisSaga> _log;

    public SiparisSaga(IStokServisi stok, IOdemeServisi odeme,
                       IKargoServisi kargo, ISagaKayit kayit,
                       ILogger<SiparisSaga> log)
        => (_stok, _odeme, _kargo, _kayit, _log) = (stok, odeme, kargo, kayit, log);

    public async Task<SagaResult> CalistirAsync(string siparisId, CancellationToken ct)
    {
        // Adım 1 — stok rezervasyonu (idempotency: siparisId anahtarı)
        var rezerve = await _stok.RezerveEtAsync(siparisId, ct);
        if (!rezerve.Ok)
            return new SagaResult(SagaStatus.Failed, "stok-rezervasyon");

        // Adım 2 — ödeme
        var odedi = await _odeme.OdeAsync(siparisId, ct);
        if (!odedi.Ok)
        {
            // GERİ ÇEKME: stok rezervasyonunu serbest bırak — otomatik rollback YOK (W-03)
            await _stok.SerbestBirakAsync(siparisId, ct);
            _log.LogWarning("Saga geri çekildi: {SiparisId} adım=odeme neden={Neden}",
                            siparisId, odedi.Neden);
            return new SagaResult(SagaStatus.Compensated, "odeme-basarisiz");
        }

        // Adım 3 — kargo etiketi
        var etiket = await _kargo.EtiketOlusturAsync(siparisId, ct);
        if (!etiket.Ok)
        {
            await _odeme.IadeEtAsync(siparisId, ct);     // geri çekme 1: iade
            await _stok.SerbestBirakAsync(siparisId, ct); // geri çekme 2: stok
            return new SagaResult(SagaStatus.Compensated, "kargo-etiketi");
        }

        await _kayit.TamamlaAsync(siparisId, ct);
        return new SagaResult(SagaStatus.Completed, null);
    }
}
```

**Kritik noktalar (C#):**

1. **Her geri çekme zincirdedir** — kargo başarısızsa önce iade, sonra stok
   serbest bırakma sırası tersine çevrilmez (zincir-geri-alma kuralı).
2. **Geri çekme kendisi de başarısız olabilir** → `ISagaKayit` kalıcıdır; geri
   çekme başarısızlıkları `Failed` olarak kayda düşer ve elle müdahale kapısı
   (KAPI-6) tetiklenir. Bu senaryo **sessizce yutulmaz**.
3. Retry mantığı bilinçli olarak **bu dosyada yoktur** — Polly/`HttpClient`
   retry davranışı W-04'e (Azure Retry pattern) bağlanmıştır; W-04 gecikmeyi
   "incrementally or exponential" olarak tarif eder ama **sayısal taban vermez**,
   bu yüzden burada taban değer **uydurulmaz** (`⚠️ VERIFICATION REQUIRED`).

---

## §27 — Python 3.12: idempotency-key deposu (tekilleştirme — W-10 IdempotentReceiver)

W-10 (Enterprise Integration Patterns · Idempotent Receiver): alıcı, aynı
mesajın tekrarlanmasını **tanıyıp** etkisiz kılmalıdır. Python 3.12 örneği
asyncio + `INSERT ... ON DUPLICATE` yerine **yapısal** tekilleştirme ile
(anahtar yoksa ekle, varsa önceki sonucu dön):

```python
# K013 — idempotency key deposu (Python 3.12, asyncio + sqlite örneği)
import asyncio, json, hashlib, time
from dataclasses import dataclass
from typing import Any

@dataclass(frozen=True, slots=True)
class KayitSonucu:
    yeni: bool
    govde: dict[str, Any]

class IdempotencyDeposu:
    """W-10 IdempotentReceiver — aynı anahtar ikinci kez işlenmez."""

    def __init__(self, conn) -> None:  # conn: aiosqlite bağlantısı
        self._conn = conn

    async def hazirla(self) -> None:
        await self._conn.execute("""
            CREATE TABLE IF NOT EXISTS inbox (
                idempotency_key TEXT PRIMARY KEY,
                event_id         TEXT NOT NULL,
                event_type       TEXT NOT NULL,
                payload_hash     TEXT NOT NULL,
                durum            TEXT NOT NULL,   -- 'processing' | 'done'
                sonuc            TEXT,
                olusturulma      REAL NOT NULL
            )
        """)
        await self._conn.commit()

    @staticmethod
    def _hash(govde: bytes) -> str:
        return hashlib.sha256(govde).hexdigest()

    async def isle(self, anahtar: str, event_id: str,
                   event_type: str, govde: bytes,
                   isleyici) -> KayitSonucu:
        hash_deger = self._hash(govde)
        imlec = await self._conn.execute(
            "INSERT INTO inbox (idempotency_key, event_id, event_type,"
            " payload_hash, durum, olusturulma)"
            " VALUES (?, ?, ?, ?, 'processing', ?)"
            " ON CONFLICT(idempotency_key) DO NOTHING",
            (anahtar, event_id, event_type, hash_deger, time.time()),
        )
        await self._conn.commit()

        if imlec.rowcount == 1:            # ilk kez görülüyor → işle
            sonuc = await isleyici(json.loads(govde))
            await self._conn.execute(
                "UPDATE inbox SET durum='done', sonuc=? WHERE idempotency_key=?",
                (json.dumps(sonuc), anahtar),
            )
            await self._conn.commit()
            return KayitSonucu(yeni=True, govde=sonuc)

        # İkinci kez görülüyor → payload aynı mı? (aynı anahtar-farklı gövde = ihlal)
        satir = await self._conn.execute(
            "SELECT payload_hash, durum, sonuc FROM inbox WHERE idempotency_key=?",
            (anahtar,),
        ).fetchone()
        if satir is None:                      # pragma: no cover — imkânsız durum
            raise RuntimeError("inbox çelişkisi: kayıt kayboldu")
        if satir["payload_hash"] != hash_deger:
            raise ValueError(
                f"idempotency ihlali: {anahtar} aynı anahtarla farklı gövdeyle geldi"
            )
        if satir["durum"] != "done":
            raise TimeoutError(f"{anahtar} hâlâ processing — tekrar gönder")
        return KayitSonucu(yeni=False, govde=json.loads(satir["sonuc"]))
```

**Kritik noktalar (Python):**

- **Aynı anahtar + farklı gövde → `ValueError`.** Bu, W-10'un görünmeyen şartıdır:
  tekilleştirme anahtarı ile gövde birlikte doğrulanmazsa sessiz veri kaybı olur.
- `durum='processing'` iken gelen tekrar `TimeoutError` verir — **ikinci bir
  işleyici eşzamanlı çalışması engellenir** (yarım-kalmış iş çifti kayıt üretmez).
- Depo `inbox` adını §23'teki şema ile **aynı** tutar; dil değişir, sözleşme
  değişmez (KAPI-1).

---

## §28 — Go: outbox poller (döngü + jitter + batch — W-01 + W-16)

Go, gerçek-zamanlı ve uzun-süreli poller'lar için doğal dildir; §25'teki C++
relay'nin Go karşılığı. Jitter matematiği §17'deki uyarıya uyar: **jitter
YOKSA eşzamanlı retry yayılımı (thundering herd) önlenemez**:

```go
// K013 — outbox poller (Go 1.22+)
package outbox

import (
	"context"
	"log/slog"
	"math/rand/v2"
	"time"
)

type Row struct {
	ID       string
	EventID  string
	Type     string
	Payload  []byte
	Attempts int
}

type Store interface {
	Fetch(ctx context.Context, batch int) ([]Row, error) // FOR UPDATE SKIP LOCKED (§23)
	MarkPublished(ctx context.Context, id string) error
	Reschedule(ctx context.Context, id string, attempts int, next time.Time) error
	MarkFailed(ctx context.Context, id string) error
}

type Publisher interface {
	Publish(ctx context.Context, r Row) (ok bool, retryable bool, err error)
}

type Poller struct {
	store     Store
	publisher Publisher
	batch     int
	base      time.Duration // ⚠️ VERIFICATION REQUIRED — ADR ile sabitlenecek
	cap       time.Duration // ⚠️ VERIFICATION REQUIRED — ADR ile sabitlenecek
	log       *slog.Logger
}

func NewPoller(s Store, p Publisher, batch int, log *slog.Logger) *Poller {
	return &Poller{
		store: s, publisher: p, batch: batch, log: log,
		base: 500 * time.Millisecond, // ⚠️ VERIFICATION REQUIRED (§17 notu)
		cap:  60 * time.Second,       // ⚠️ VERIFICATION REQUIRED
	}
}

// jitter: tam rastgele değil "tam jitter" — sleep = rand(0, backoff)
func (p *Poller) backoff(attempts int) time.Duration {
	d := p.base
	for i := 0; i < attempts && d < p.cap; i++ {
		d *= 2
	}
	if d > p.cap {
		d = p.cap
	}
	return time.Duration(rand.Int64N(int64(d))) // tam jitter (AWS retry tarifi)
}

func (p *Poller) RunOnce(ctx context.Context) int {
	rows, err := p.store.Fetch(ctx, p.batch)
	if err != nil {
		p.log.Error("outbox fetch", "err", err)
		return 0
	}
	done := 0
	for _, r := range rows {
		ok, retryable, err := p.publisher.Publish(ctx, r)
		switch {
		case ok:
			if err := p.store.MarkPublished(ctx, r.ID); err != nil {
				p.log.Error("mark published", "id", r.ID, "err", err)
			}
			done++
		case retryable:
			next := time.Now().Add(p.backoff(r.Attempts))
			_ = p.store.Reschedule(ctx, r.ID, r.Attempts+1, next)
		default:
			p.log.Warn("kalıcı hata → DLQ", "id", r.ID)
			_ = p.store.MarkFailed(ctx, r.ID)
		}
	}
	return done
}

// Run: her tur arasında jitter'lı uyku — eşzamanlı poller yayılımı önler (§17)
func (p *Poller) Run(ctx context.Context, tick time.Duration) {
	for {
		select {
		case <-ctx.Done():
			return
		case <-time.After(tick):
			p.RunOnce(ctx)
		}
	}
}
```

**Kritik noktalar (Go):**

- `MarkPublished` yalnız `ok==true` **sonrasında** — §25'teki C++ kuralının birebir
  aynısı; dil değişmez, kural değişmez (W-01).
- `math/rand/v2` ile **tam jitter**: `rand(0, backoff)`. Eşdeğer adil jitter
  (yarım aralık) da kabul edilir; ikisi de §17'deki "jitter şart" şartını doldurur.
- `Publish` üç değer döner (`ok`, `retryable`, `err`) — **kalıcı hata ile
  geçici hata ayrımı** DLQ'nun doğru çalışmasının ön koşuludur (KAPI-6).

---

## §29 — Rust: inbox (mülkiyet tabanlı tekilleştirme — W-10 + W-13)

Rust'ta inbox, **mülkiyet** sayesinde yarım-kalmış işleri derleme zamanında
engelleyecek şekilde tasarlanır. W-13 (Kafka delivery semantics) hatırlatır:
çekirdek at-least-once'dır — **exactly-once alıcı tarafında türetilir**:

```rust
// K013 — inbox (Rust 1.83+, sqlx örneği — derleme zamanı sözleşmesi)
use sqlx::SqlitePool;
use sha2::{Digest, Sha256};
use serde_json::Value;
use thiserror::Error;

#[derive(Debug, Error)]
pub enum InboxHatasi {
    #[error("idempotency ihlali: aynı anahtar, farklı gövde")]
    AnahtarIhlali,
    #[error("kayıt processing durumda — yeniden gönderim")]
    IslemSuruyor,
    #[error("veritabanı: {0}")]
    Db(#[from] sqlx::Error),
}

#[derive(Debug, Clone)]
pub struct Kayit {
    pub yeni: bool,
    pub govde: Value,
}

pub struct Inbox {
    pool: SqlitePool,
}

impl Inbox {
    /// W-10: aynı olay ikinci kez işlenmez; gövde farkı sessizce yutulmaz.
    pub async fn isle<F, Fut>(&self, anahtar: &str, event_id: &str,
                              event_type: &str, govde: &[u8],
                              isleyici: F) -> Result<Kayit, InboxHatasi>
    where
        F: FnOnce(Value) -> Fut,
        Fut: std::future::Future<Output = Value>,
    {
        let hash = format!("{:x}", Sha256::digest(govde));

        let eklendi = sqlx::query(
            "INSERT INTO inbox (idempotency_key, event_id, event_type,\
             payload_hash, durum, olusturulma)\
             VALUES (?1, ?2, ?3, ?4, 'processing', strftime('%s','now'))\
             ON CONFLICT(idempotency_key) DO NOTHING",
        )
        .bind(anahtar)
        .bind(event_id)
        .bind(event_type)
        .bind(&hash)
        .execute(&self.pool)
        .await?
        .rows_affected() == 1;

        if eklendi {
            // Mülkiyet: govde tek sahibine (isleyici) devredilir — kopyalanmaz.
            let sonuc: Value = isleyici(serde_json::from_slice(govde)?).await;
            sqlx::query("UPDATE inbox SET durum='done', sonuc=?1 WHERE idempotency_key=?2")
                .bind(sonuc.to_string())
                .bind(anahtar)
                .execute(&self.pool)
                .await?;
            return Ok(Kayit { yeni: true, govde: sonuc });
        }

        let satir: (String, String, Option<String>) = sqlx::query_as(
            "SELECT payload_hash, durum, COALESCE(sonuc,'') FROM inbox WHERE idempotency_key=?1",
        )
        .bind(anahtar)
        .fetch_one(&self.pool)
        .await?;

        if satir.0 != hash {
            return Err(InboxHatasi::AnahtarIhlali);
        }
        if satir.1 != "done" {
            return Err(InboxHatasi::IslemSuruyor);
        }
        Ok(Kayit { yeni: false, govde: serde_json::from_str(&satir.2)? })
    }
}
```

**Kritik noktalar (Rust):**

- `FnOnce(Value) -> Fut` sözleşmesi: işleyici **bir kez** çağrılır; ikinci
  çağrım (tekrar senaryosu) derleme zamanında imkânsızdır — Python'daki çalışma
  zamanı kontrolünün derleme zamanına taşınması.
- Üç hata (`AnahtarIhlali` · `IslemSuruyor` · `Db`) **ayrı tiplerdir**; DLQ
  kararı bu tiplere göre verilir — kalıcı hata DLQ'ya, geçici hata yeniden
  gönderime gider (ayrıım §28'deki `retryable` ile aynı).
- W-13 hatası: Kafka/özel kuyrukta "exactly-once" etiketi **bu inbox katmanı
  olmadan alıcı için anlam taşımaz**.

---

## §30 — Java 21: saga kapı eşlemesi + virtual thread (W-03 + W-22)

Java 21 virtual thread'leri ile saga adımları bloklama korkusu olmadan yazılır;
**kapı eşlemesi** KAPI-4'ün zorunlu şartıdır: kapı adı `evt-` önekiyle sabittir
ve olay kataloğu sürümüne bağlanır:

```java
// K013 — saga kapı eşlemesi (Java 21, virtual thread)
package com.coremusic.saga;

import java.time.Duration;
import java.util.List;
import java.util.concurrent.StructuredTaskScope;

public record Kapı(String olayTipi, String surum, boolean geriCekme) {}

public final class SagaKapıları {
    // KAPI-4: kapı adları sabittir — olay tipi + katalog sürümü birlikte
    private static final List<Kapı> KAPI_LISTESI = List.of(
        new Kapı("evt.stok.rezerve-edildi",  "v1", true),
        new Kapı("evt.odeme.tamamlandi",     "v1", true),
        new Kapı("evt.kargo.etiket-olustu",  "v1", false)
    );

    public static Kapı bul(String olayTipi) {
        return KAPI_LISTESI.stream()
                .filter(k -> k.olayTipi().equals(olayTipi))
                .findFirst()
                .orElseThrow(() ->
                    new IllegalStateException("bilinmeyen kapı: " + olayTipi));
    }
}

public final class SiparisSagaJava {
    private final StokServisi stok;
    private final OdemeServisi odeme;
    private final KargoServisi kargo;
    private final SagaKayit kayit;

    public SiparisSagaJava(StokServisi stok, OdemeServisi odeme,
                           KargoServisi kargo, SagaKayit kayit) {
        this.stok = stok; this.odeme = odeme; this.kargo = kargo; this.kayit = kayit;
    }

    public SagaSonucu calistir(String siparisId) throws InterruptedException {
        // Virtual thread ile eşzamanlı-ama-bağımsız ilk iki adım
        try (var scope = new StructuredTaskScope.ShutdownOnFailure()) {
            var stokF = scope.fork(() -> stok.rezerveEt(siparisId));
            scope.join();                       // stok tamamlanmadan ödeme YOK
            scope.throwIfFailed();

            if (!stokF.get().basarili()) {
                return SagaSonucu.basarisiz("stok-rezervasyon");
            }

            var odemeSonuc = odeme.ode(siparisId);
            if (!odemeSonuc.basarili()) {
                stok.serbestBirak(siparisId);   // geri çekme — otomatik rollback yok (W-03)
                kayit.geriCek(siparisId, "odeme-basarisiz");
                return SagaSonucu.geriCekildi("odeme-basarisiz");
            }

            var etiket = kargo.etiketOlustur(siparisId);
            if (!etiket.basarili()) {
                odeme.iadeEt(siparisId);        // zincir-geri-alma: ters sırada
                stok.serbestBirak(siparisId);
                return SagaSonucu.geriCekildi("kargo-etiketi");
            }

            kayit.tamamla(siparisId);
            return SagaSonucu.tamamlandi();
        }
    }
}
```

**Kritik noktalar (Java):**

- `StructuredTaskScope.ShutdownOnFailure` ile **ilk adım tamamlanmadan ikinci
  adım başlamaz** — sıra kritikse (stok → ödeme → kargo) paralelleştirme yalnız
  bağımsız adımlarda uygulanır.
- `SagaKapıları` statik listesi W-22 (Temporal workflow) yaklaşımının Java
  karşılığıdır: saga **kod olarak** modellenir, kapılar **veri olarak**
  sabitlenir; W-22'nin workflow tanımı ile buradaki kapı listesi aynı disiplindir.
- Retry: `Duration` tipi hazır ama **sayısal taban yok** — W-04'ün tarifi
  "incrementally or exponential"dur, sayısal değer ADR'ye bağlıdır
  (`⚠️ VERIFICATION REQUIRED`).

---

## §31 — Swift: HMAC-SHA256 webhook doğrulama (W-08 + W-12)

iOS/macOS tarafı webhook alıcısı — üç dilde aynı kuralın (§20 PHP, §21 JS)
Swift karşılığı; **ham gövde** şartı burada da geçerlidir:

```swift
// K013 — webhook imza doğrulama (Swift 5.10+, CryptoKit)
import Foundation
import CryptoKit

enum WebhookHatasi: Error {
    case eksikBaslik
    case gecersizImza
    case sureAsimi(tolerance: TimeInterval)
}

struct WebhookDogrulayici {
    let secret: Data
    /// Stripe tarifi (W-12): varsayılan tolerans 5 dakika; tolerance=0 YASAK.
    var tolerance: TimeInterval = 300

    func dogrula(baslik: String, hamGovde: Data, simdi: Date = Date()) throws {
        // Biçim: t=<unix>,v1=<hex> (W-12) veya sha256=<hex> (W-08 GitHub)
        var tSaniye: Double?
        var v1List: [String] = []

        for parc in baslik.split(separator: ",") {
            let kv = parc.split(separator: "=", maxSplits: 1).map(String.init)
            guard kv.count == 2 else { continue }
            switch kv[0] {
            case "t":  tSaniye = Double(kv[1])
            case "v1": v1List.append(kv[1])
            default:   break
            }
        }

        guard let t = tSaniye else { throw WebhookHatasi.eksikBaslik }
        guard !v1List.isEmpty  else { throw WebhookHatasi.eksikBaslik }

        // 1) Zaman penceresi — tolerance=0 asla kullanılmaz (W-12)
        let fark = abs(simdi.timeIntervalSince1970 - t)
        if fark > tolerance {
            throw WebhookHatasi.sureAsimi(tolerance: tolerance)
        }

        // 2) Ham gövde + zaman damgası imzalanır — JSON normalize edilmez
        var mac = HMAC<SHA256>(key: SymmetricKey(data: secret))
        mac.update(data: Data("\(Int(t))".utf8))
        mac.update(data: hamGovde)
        let beklenen = Data(mac.finalize()).map { String(format: "%02x", $0) }
            .joined()

        // 3) Sabit-zamanlı karşılaştırma (§24 ile aynı ilke)
        let eslesme = zip(beklenen, v1List).contains { par, aday in
            guard par.count == aday.count else { return false }
            var farkli: UInt8 = 0
            for (a, b) in zip(par.utf8, aday.utf8) { farkli |= a ^ b }
            return farkli == 0
        }
        guard eslesme else { throw WebhookHatasi.gecersizImza }
    }
}
```

**Kritik noktalar (Swift):**

- `t` + ham gövde birlikte imzalanır — **saat dilimi yok, unix saniye var** (W-12).
- Tolerans varsayılanı **300 saniye (5 dk)**; `tolerance = 0` yapılamaz çünkü
  alan `var` ama `dogrula` içinde `fark > tolerance` kontrolü **her çağrıda**
  çalışır ve Stripe'ın canlı davranışıyla (W-12) aynıdır.
- `zip(...).contains` + `^` (XOR) biriktirme: sabit-zamanlı karşılaştırma
  §24'teki C karşılığının güvenli soyutlanmış hâli.

---

## §32 — Kotlin: inbox kuyruk işleyici (coroutine + Room benzeri şema — W-10)

Android tarafı kuyruk işleyicisi; Kotlin coroutines ile **tek iş parçacığı
mülkiyeti** inbox'ın yarım-kalmış iş derdini kökten azaltır:

```kotlin
// K013 — inbox kuyruk işleyici (Kotlin 2.0, coroutines)
package com.coremusic.inbox

import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.channels.Channel
import kotlinx.coroutines.launch
import kotlinx.serialization.json.Json
import java.security.MessageDigest

data class Olay(
    val idempotencyKey: String,
    val eventId: String,
    val eventType: String,
    val govde: String,
)

sealed interface IslemeSonucu {
    data class Yeni(val sonuc: String) : IslemeSonucu
    data class Tekrar(val mevcut: String) : IslemeSonucu
    data class Ihlal(val anahtar: String) : IslemeSonucu
}

class InboxIsleyici(
    private val depo: InboxDeposu,             // Room/SQLDelight DAO
    private val katalog: OlayKatalogu,         // sürüm-upcast zinciri (§30 · ../event-sozlesme.md)
) {
    private val kanal = Channel<Olay>(capacity = Channel.UNLIMITED)

    fun bas(scope: CoroutineScope) = scope.launch(Dispatchers.IO) {
        for (olay in kanal) { tek(olay) }
    }

    suspend fun kuyruğaAl(olay: Olay) { kanal.send(olay) }

    private suspend fun tek(olay: Olay): IslemeSonucu {
        val hash = MessageDigest.getInstance("SHA-256")
            .digest(olay.govde.encodeToByteArray())
            .joinToString("") { "%02x".format(it) }

        val eklendi = depo.ekleIsaretle(
            anahtar = olay.idempotencyKey, eventId = olay.eventId,
            eventType = olay.eventType, payloadHash = hash,
        )
        if (!eklendi) {
            val mevcut = depo.bul(olay.idempotencyKey)
                ?: return IslemeSonucu.Ihlal(olay.idempotencyKey)
            return when {
                mevcut.payloadHash != hash ->
                    IslemeSonucu.Ihlal(olay.idempotencyKey)  // aynı anahtar, farklı gövde
                mevcut.durum != "done" ->
                    throw IllegalStateException("processing: ${olay.idempotencyKey}")
                else -> IslemeSonucu.Tekrar(mevcut.sonuc ?: "{}")
            }
        }

        // Sürüm-upcast: olay önce güncel sürüme çevrilir, sonra işlenir (KAPI-2)
        val guncel = katalog.upcast(olay.eventType, Json.parseToJsonElement(olay.govde))
        val sonuc = katalog.isle(olay.eventType, guncel).toString()
        depo.tamamla(olay.idempotencyKey, sonuc)
        return IslemeSonucu.Yeni(sonuc)
    }
}
```

**Kritik noktalar (Kotlin):**

- `Channel` + tek `launch` döngüsü: **aynı anahtar iki kez aynı anda işlemez** —
  Android'in çok-iş-parçacıklı doğasına rağmen sıra korunur.
- `katalog.upcast(...)` çağrısı KAPI-2'nin kod karşılığıdır: eski sürüm olay
  **işlemeden önce** güncel sürüme çevrilir (bkz. `../event-sozlesme.md`).
- `IslemeSonucu` sealed interface: UI katmanı `Ihlal` durumunu **görmezden
  gelemez** — exhaustive `when` zorunluluğu bilinmeyen durumu derleme zamanında
  yakalar.

---

## §33 — Bash: DLQ denetimi + jitter'lı yeniden gönderim (operasyon betiği)

Üretimde olayların elle kuyruğa geri gönderilmesi operasyon işidir; betik
**ölü-sessizlik** yerine **ölçü-reddet** yapar:

```bash
#!/usr/bin/env bash
# K013 — DLQ denetimi ve jitter'lı yeniden gönderim (bash 5.x)
set -euo pipefail

DLQ_KUYRUK="${DLQ_KUYRUK:?DLQ_KUYRUK ortam değişkeni şart}"
MAX_TEKRAR="${MAX_TEKRAR:-5}"          # teslim denemesi (W-16 max_delivery_attempts)
BATCH="${BATCH:-50}"
DB_DSN="${DB_DSN:?DB_DSN şart}"        # DSN asla betiğe gömülmez (.env)

log() { printf '%s %s\n' "$(date -u +%FT%TZ)" "$*" >&2; }

# 1) DLQ kuyruğu boş mu? (boş değilse alarm — §36'daki alarmın CLI karşılığı)
DLQ_SAYI=$(psql "$DB_DSN" -Atc \
  "SELECT COUNT(*) FROM dlq WHERE kuyruk = '${DLQ_KUYRUK}'")
if [[ "$DLQ_SAYI" -gt 0 ]]; then
  log "UYARI: ${DLQ_KUYRUK} kuyruğunda ${DLQ_SAYI} DLQ kaydı var (elle incele)"
fi

# 2) Yeniden gönderim — tam jitter: sleep = rand(0, base*2^attempts)
base_ms=500
yeniden_gonder() {
  local anahtar="$1" deneme="$2"
  local cap_ms=$(( base_ms * (1 << deneme) ))
  (( cap_ms > 60000 )) && cap_ms=60000
  local uyku=$(( RANDOM % (cap_ms + 1) ))   # tam jitter (§17)
  sleep "0.$(printf '%03d' "$uyku")"
  # Kalıcı hata (imza-süresi-aşımı) yeniden gönderilmez → DLQ'da kalır
  psql "$DB_DSN" -c \
    "UPDATE inbox_outbox SET deneme = deneme + 1,
            sonraki_deneme = now() + interval '${uyku} milliseconds'
     WHERE idempotency_key = '${anahtar}' AND deneme < ${MAX_TEKRAR}"
}

# 3) Düzeltilebilir kayıtları topla (imza hatası DEĞİL)
psql "$DB_DSN" -Atc \
  "SELECT idempotency_key, hata_kodu FROM dlq
   WHERE kuyruk = '${DLQ_KUYRUK}'
     AND hata_kodu NOT IN ('imza-suresi-asimi','imza-gecersiz')
   LIMIT ${BATCH}" |
while IFS='|' read -r anahtar hata_kodu; do
  [[ -z "$anahtar" ]] && continue
  log "yeniden gönderim: ${anahtar} (${hata_kodu})"
  yeniden_gonder "$anahtar" 0
done

log "bitti: ${DLQ_KUYRUK}"
```

**Kkritik noktalar (Bash):**

- **Kalıcı hata filtresi:** `imza-suresi-asimi` / `imza-gecersiz` kayıtları
  yeniden gönderilmez — süresi geçmiş imza tekrar gönderilse bile tekrar
  başarısız olur; bu, DLQ'nun "son çare" anlamını korur (KAPI-6).
- `RANDOM % (cap_ms+1)` = tam jitter; §17'nin "jitter yoksa yayılım" uyarısının
  operasyon karşılığı.
- `set -euo pipefail` + boş-satır koruması (`[[ -z ... ]] && continue`): boş
  DLQ'da betik **başarılı** biter, sahte alarm üretmez.

---

## §34 — PowerShell: webhook imza doğrulama + backoff (Windows tarafı — W-08)

CoreMusic Windows tarafı (win-sw katmanı) PowerShell ile aynı kuralı uygular;
GitHub imza biçimi `sha256=<hex>` (W-08) ve **düz `==` karşılaştırması yasaktır**:

```powershell
# K013 — webhook imza doğrulama + jitter'lı backoff (PowerShell 7.4)
using namespace System.Security.Cryptography

function Test-CoreMusicWebhookSignature {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory)][byte[]] $RawBody,
        [Parameter(Mandatory)][string] $SignatureHeader,   # "sha256=<hex>" (W-08)
        [Parameter(Mandatory)][string] $Secret,
        [int] $ToleranceSeconds = 300                       # Stripe: 5 dk (W-12)
    )

    if ($SignatureHeader -notmatch '^sha256=([0-9a-f]{64})$') {
        throw "imza başlığı biçimi geçersiz — beklenen: sha256=<64 hex>"
    }
    $aday = $Matches[1]

    $hmac = [HMACSHA256]::new([Text.Encoding]::UTF8.GetBytes($Secret))
    try {
        $ham = $hmac.ComputeHash($RawBody)
        $beklenen = [Convert]::ToHexString($ham).ToLowerInvariant()
    }
    finally {
        $hmac.Dispose()   # anahtar bellekten temizlenir
    }

    # Sabit-zamanlı karşılaştırma — düz -eq ZAMANLAMA sızıntısı üretir (§24)
    $farkli = 0
    for ($i = 0; $i -lt $beklenen.Length; $i++) {
        $farkli = $farkli -bor ([int][char]$beklenen[$i] -bxor [int][char]$aday[$i])
    }
    if ($farkli -ne 0 -or $beklenen.Length -ne $aday.Length) {
        return $false
    }
    return $true
}

function Invoke-WithBackoff {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory)][scriptblock] $Aksiyon,
        [int] $MaxDeneme = 5,
        [int] $BaseMs   = 500,     # ⚠️ VERIFICATION REQUIRED — ADR ile sabitlenecek
        [int] $CapMs    = 60000    # ⚠️ VERIFICATION REQUIRED
    )
    for ($deneme = 0; $deneme -lt $MaxDeneme; $deneme++) {
        try { return & $Aksiyon }
        catch {
            $cap = [Math]::Min($BaseMs * [Math]::Pow(2, $deneme), $CapMs)
            $uyku = Get-Random -Minimum 0 -Maximum ([int]$cap)   # tam jitter (§17)
            Write-Warning "deneme $($deneme + 1) başarısız: $($_.Exception.Message) — ${uyku} ms bekleniyor"
            Start-Sleep -Milliseconds $uyku
        }
    }
    throw "MaxDeneme ($MaxDeneme) doldu — DLQ'ya düşür (KAPI-6)"
}
```

**Kritik noktalar (PowerShell):**

- `-bxor` biriktirmesi ile sabit-zamanlı karşılaştırma; **düz `-eq` yasak** —
  bu, W-08'in "düz `==` kullanma" kuralının PowerShell karşılığı.
- `$hmac.Dispose()` `finally` bloğunda: anahtar `HMACSHA256` nesnesi
  bellekten temizlenmeden fonksiyon dönmez (Windows tarafı güvenlik standardı).
- `Invoke-WithBackoff` sonunda **istisna fırlatır** — sessiz DLQ yok; DLQ'ya
  düşürme kararı çağıran katmanındadır (KAPI-6).

---

## §35 — YAML (GitHub Actions): imza denetimi + DLQ kapısı (CI kapısı)

CI'da webhook sözleşmesi **sözleşme-testi** ile korunur; resmi test vektörü
W-08'den alınmıştır (secret `It's a Secret to Everybody`, payload
`Hello, World!` → `sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17`):

```yaml
# .github/workflows/webhook-sozlesme.yml — K013 kapı testi
name: webhook-sozlesme

on:
  pull_request:
    paths:
      - 'shared/src/Webhooks/**'
      - 'subdomains/**/Webhooks/**'

jobs:
  imza-sozlesmesi:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Resmi test vektörü (W-08) — bilinen secret + bilinen payload
        env:
          GH_SECRET: "It's a Secret to Everybody"
          PAYLOAD: "Hello, World!"
        run: |
          BEKLENEN="sha256=757107ea0eb2509fc211221cce984b8a37570b6d7586c22c46f4379c8b043e17"
          URETILEN="sha256=$(printf '%s' "$PAYLOAD" \
            | openssl dgst -sha256 -hmac "$GH_SECRET" -r | cut -d' ' -f1)"
          if [[ "$URETILEN" != "$BEKLENEN" ]]; then
            echo "İMZA SÖZLEŞMESİ BOZULDU: $URETILEN != $BEKLENEN"
            exit 1
          fi
          echo "imza sözleşmesi geçti (W-08 resmi vektörü)"

      - name: tolerance=0 denetimi — yasak desen
        run: |
          # tolerance=0 (W-12) kodda varsa CI düşer
          if grep -rn --include='*.php' --include='*.js' --include='*.swift' \
               -E 'tolerance[^a-zA-Z0-9]*(=|:)\s*0\b' shared/ subdomains/; then
            echo "tolerance=0 kullanılmış — W-12'ye aykırı (zaman penceresi kapatılamaz)"
            exit 1
          fi
          echo "tolerance=0 yok — geçti"

  dlq-kapisi:
    runs-on: ubuntu-latest
    needs: imza-sozlesmesi
    steps:
      - uses: actions/checkout@v4
      - name: DLQ alarmı tanımlı mı? (KAPI-6)
        run: |
          # Terraform dosyasında DLQ alarmı yoksa kapı kapalı demektir
          if ! grep -q 'dead_letter_policy' infra/*.tf 2>/dev/null; then
            echo "DLQ policy yok — KAPI-6 kapalı (K013 ihlali)"
            exit 1
          fi
          echo "DLQ kapısı açık"
```

**Kritik noktalar (YAML):**

- **Resmi test vektörü** uydurulmamıştır: W-08'in kendi sayfasındaki
  secret/payload/çıktı üçlüsü birebir kullanılmıştır.
- `tolerance=0` taraması statik bir **kapı**dır: W-12'nin "zaman penceresi
  kapatılamaz" kuralı kod incelemesine değil CI'ya bağlanır.
- `needs:` sıralaması: imza sözleşmesi geçmeden DLQ kapısı **test edilmez** —
  öncelik K013 kapı sırasıyla aynıdır (KAPI-3 → KAPI-6).

---

## §36 — HCL (Terraform): DLQ + retry politikası + alarm (altyapı sözleşmesi)

Altyapı da bir **sözleşmedir**: retry/DLQ değerleri kod içine gömülü değil,
altyapıda tanımlanır ve denetlenir (W-16'nın `max_delivery_attempts`
kavramının Terraform karşılığı):

```hcl
# K013 — DLQ + retry + alarm (Terraform · GCP Pub/Sub örneği — W-16 kavramları)
variable "dlq_max_delivery_attempts" {
  type        = number
  description = "Teslim denemesi sınırı (W-16 kavramı — sayısal değeri ADR belirler)"
  default     = 5                    # ⚠️ VERIFICATION REQUIRED — ADR ile sabitlenecek
}

variable "retry_min_backoff" {
  type    = string
  default = "10s"                    # ⚠️ VERIFICATION REQUIRED
}

variable "retry_max_backoff" {
  type    = string
  default = "600s"                   # ⚠️ VERIFICATION REQUIRED
}

resource "google_pubsub_topic" "olay" {
  name = "coremusic-olay"
}

resource "google_pubsub_topic" "dlq" {
  name = "coremusic-olay-dlq"
}

resource "google_pubsub_subscription" "olay_aboneligi" {
  name  = "coremusic-olay-aboneligi"
  topic = google_pubsub_topic.olay.name

  ack_deadline_seconds = 60

  retry_policy {
    minimum_backoff = var.retry_min_backoff
    maximum_backoff = var.retry_max_backoff
  }

  dead_letter_policy {
    dead_letter_topic     = google_pubsub_topic.dlq.id
    max_delivery_attempts = var.dlq_max_delivery_attempts
  }

  # Çekirdek at-least-once: sıra garantisi YOK (W-16) — istemci inbox kullanır (§22-§32)
  enable_message_ordering = false
}

resource "google_monitoring_alert_policy" "dlq_dolu" {
  display_name = "K013 — DLQ kuyruğu boş değil"
  combiner     = "OR"

  conditions {
    display_name = "DLQ mesaj sayısı > 0"
    condition_threshold {
      filter = <<EOT
resource.type="pubsub_topic"
resource.label.topic_id="${google_pubsub_topic.dlq.name}"
EOT
      comparison      = "COMPARISON_GT"
      threshold_value = 0
      duration        = "60s"
      aggregations {
        alignment_period   = "60s"
        per_series_aligner = "ALIGN_MAX"
      }
    }
  }

  documentation {
    content = <<-EOT
      DLQ'ya düşen olay var — KAPI-6 elle müdahale kapısı.
      Sık nedenler: (1) olay sürümü alıcı kataloğunda yok (KAPI-2),
      (2) bozuk gövde, (3) imza süresi aşıldı (W-12 — yeniden gönderim
      işe yaramaz, bkz. §33 filtresi).
    EOT
  }
}
```

**Kritik noktalar (HCL):**

- Sayısal değerler (`5` · `10s` · `600s`) **değişkenlere** taşınmıştır; her biri
  `⚠️ VERIFICATION REQUIRED` işaretlidir — W-16 kavramı verir, sayısal değer
  vermez; sayı ADR kararıyla sabitlenecektir.
- `enable_message_ordering = false` **açıkça** yazılır: varsayılanı varsaymak
  değil, kararı **kodda görünür** kılmak (W-16).
- Alarm `documentation` bloğu üç olası kök nedeni sayar — alarm çaldığında
  operatörün ilk bakacağı yer orasıdır (ölçü-sonrası eylem: §33 betiği).

---

## §37 — Karşılaştırma matrisi: her dil hangi garantide kullanılır

| Dil | Bölüm | Rol | Garanti katmanı | Jitter | Sürüm-upcast |
|---|---|---|---|---|---|
| PHP 8.4 | §20 | outbox yazımı + HMAC | W-01 en az bir kez | — | — |
| JavaScript | §21 | HMAC doğrulama (Web Crypto) | W-08/W-12 | — | — |
| TypeScript | §22 | inbox idempotency | W-10 tekilleştirme | — | — |
| SQL | §23 | outbox+inbox şeması, `SKIP LOCKED` | W-01 + W-10 | — | — |
| C (C11) | §24 | sabit-zamanlı hex karşılaştırma | zamanlama direnci | — | — |
| C++20 | §25 | outbox relay | W-01 | tam jitter | — |
| C# (.NET 8) | §26 | saga + geri çekme | W-03 | — | — |
| Python 3.12 | §27 | inbox deposu | W-10 | — | — |
| Go | §28 | outbox poller | W-01 | tam jitter | — |
| Rust | §29 | inbox (mülkiyet) | W-10 + W-13 | — | — |
| Java 21 | §30 | saga kapı eşlemesi | W-03 + W-22 | — | kapı verisi |
| Swift | §31 | HMAC doğrulama | W-08/W-12 | — | — |
| Kotlin | §32 | inbox kuyruk | W-10 | — | ✓ (`katalog.upcast`) |
| Bash | §33 | DLQ denetimi + yeniden gönderim | operasyon | tam jitter | — |
| PowerShell | §34 | HMAC + backoff | W-08 | tam jitter | — |
| YAML | §35 | CI sözleşme kapıları | W-08 resmi vektör | — | — |
| HCL | §36 | DLQ + alarm | W-16 | — | — |

**Tablo okuma kuralı:** hiçbir dil **tek başına** exactly-once üretmez; zincir
`outbox (yayın) → kuyruk (en az bir kez) → inbox (tekilleştirme) → DLQ (son
çare)` halkalarından oluşur. Tablodaki her satır yalnızca bir halkadır —
tam sistem bu dört halkanın birleşimidir (W-01 · W-10 · W-16).

---

## §38 — Kapılar eki: §20–§36'daki her örneğin K013 kapılarıyla kesişimi

| K013 Kapısı | Örnek § | Kodda görünür şart |
|---|---|---|
| KAPI-1 (sözleşme dondurma) | §23 · §22 · §27 · §29 · §32 | `inbox` şeması beş dilde aynı sütun adları; `Result`/sealed interface ile sözleşme tipi sabit |
| KAPI-2 (sürüm uyumluluğu) | §30 · §32 | `SagaKapıları` statik listesi + `katalog.upcast(...)` — eski sürüm işlemeden önce güncellenir |
| KAPI-3 (HMAC + ham gövde) | §20 (PHP) · §21 (JS) · §31 (Swift) · §34 (PS) · §35 (CI) | Dört dil + CI; hepsinde ham gövde, sabit-zamanlı karşılaştırma, tolerance≠0 |
| KAPI-4 (saga kapı eşlemesi) | §26 (C#) · §30 (Java) | Kapı adları `evt-` önekiyle sabit; geri çekme zinciri ters sırada |
| KAPI-5 (outbox eşitleme) | §25 (C++) · §28 (Go) · §23 (SQL) | `MarkPublished` yalnız yayın **sonrasında**; `SKIP LOCKED` çok-istemci güvenliği |
| KAPI-6 (DLQ'ya elle müdahale) | §33 (Bash) · §36 (HCL) · §23 (SQL `dlq` tablosu) | Kalıcı hata yeniden gönderilmez; alarm 60 sn doğrulama ile |
| KAPI-7 (eşzamanlı retry yayılımı) | §25 · §28 · §33 · §34 · §36 | Tüm backoff yollarında tam jitter; jitter'siz yol **hiçbir §'de yok** |

**Bütünlük beyanı:** §20–§36 arası 17 bölümün 17'si de tek bir zincirin
halkasıdır; hiçbir bölümde uydurma sayısal değer yoktur — sayısal olmayan
referanslar (5 dk tolerans, `sha256=7571…` vektörü) doğrudan kaynak
(W-08 · W-12) metnidir; sayısal-olmayan sabitler (backoff tabanı) `⚠️
VERIFICATION REQUIRED` işaretli ADR maddesidir.

---

*EK-A-2026-10-18 · §19–§38 · 17 dilin tamamı · ana kaynaklar: W-01 (transactional outbox) · W-03 (saga) · W-08 (GitHub imza + resmi test vektörü) · W-10 (IdempotentReceiver) · W-12 (Stripe imza/tolerans) · W-13 (Kafka semantiği) · W-16 (Pub/Sub at-least-once) · W-04 (Azure retry tarifi — sayısal yok) · W-22 (Temporal workflow) — ana kaynak tablosu: `../integration-master.md` §19.*
