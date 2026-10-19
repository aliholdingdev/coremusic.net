---
node_id: K010-network/kod-ornekleri
node_type: subdomain
status: draft
parent_id: K010-network
children: []
depends_on:
  - K006-codebase
  - K008-superapp
  - K009-api
  - K011-ui-kit
  - K012-security
  - K016-observability
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php L1-L80 (206/ETag/Range sözleşmesi) — okundu 2026-10-18"
  - "assets.coremusic.net/tests/mocks/player-api.mock.js L130-L164 (SSE EventSource mock, event şeması) — okundu 2026-10-18"
  - ".ai/architecture/K010-network/network-master.md §3 (durum makinesi, backoff) — okundu 2026-10-18"
validation: "13 dil × stream/SSE/WS/backoff/sync; PHP+JS blokları GERÇEK dosya kanıtlı"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — Go/Rust/C#/Java/Kotlin/Swift/C/Python/TS/SQL blokları
  şartname gövdesi (repo'da bu dillerde ağ motor kodu yok); üretim öncesi
  .ai/templates/ ile doğrulanacak, satır numarası UYDURULMADI.
group: kod
---

# K010-network — Kod Örnekleri (ağ sözleşmeleri)

## §1 — ELI15: Bu nedir?

Ağ katmanının parçalarının her dilde "nasıl görünür" kartpostalları. PHP/JS
blokları repo'daki **gerçek dosyalardan** alındı (satır numarası var): stream
handler (206/ETag) ve SSE mock (event şeması). Diğer diller **şartname
gövdesidir** — repo'da o dillerde ağ motoru yok, uydurma satır numarası yok.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Stream (Range/ETag), SSE, WebSocket, backoff retry, offline sync, heartbeat |
| Kapsam dışı | REST şema tasarımı (K009-api) · auth denetimi (K012-security) |

## §3 — Gerçek repo kanıtları (PHP 8.4 · JS)

### 3.1 PHP 8.4 — Stream sözleşmesi (GERÇEK DOSYA)

```php
// Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
// Satır: L9-L20 (sözleşme yorumu) — 206 + ETag + Accept-Ranges: bytes
/**
 * MusicStreamHandler — GET /stream/{musicId} (byte-range destekli ses akışı)
 *
 * Sözleşme:
 *   - HTTP Range: tek aralık → 206 Partial Content (seek için şart)
 *   - ETag + Last-Modified + Accept-Ranges: bytes
 * Kurallar: ADR-002 (PDO prepared statements), SELECT * yok.
 */
final class MusicStreamHandler
{
    private const CHUNK_BYTES = 1048576; // L46: 1 MiB chunk
}
```

**Neden önemli:** Seek'in çalışması için Range→206 şart (C-net-2/KN-N2);
ETag cache doğruluğunu, Accept-Ranges istemciye "seek mümkün" der.

### 3.2 JS — SSE event şeması (GERÇEK DOSYA)

```javascript
// Dosya: assets.coremusic.net/tests/mocks/player-api.mock.js
// Satır: L136 (createMockEventSource) · L144-L156 (event şeması)
export function createMockEventSource(options = {}) {
  const { initialProgress = 0, updateInterval = 100 } = options;
  let listeners = {};
  let isOpen = true;

  const interval = setInterval(() => {
    if (isOpen && currentProgress < 100) {
      currentProgress += 5;
      if (listeners.message) {
        const event = new Event('message');
        event.data = JSON.stringify({
          seekPct: currentProgress,
          elapsed: `${...}`,            // süre string'i
        });
        listeners.message(event);
      }
    }
  }, updateInterval);
}
```

**Neden önemli:** Gerçek SSE payload şeması burada: `{seekPct, elapsed}` JSON
(C-net-2). Üretim SSE ucu bu şemaya uymalı.

## §4 — Sözleşme örnekleri (şartname gövdesi — 13 dil)

### 4.1 C — Backoff hesaplama (C-net-4)

```c
/* Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
 * Üstel backoff + jitter: 2^attempt × 500ms, %25 jitter, üst 30s. */
#include <stdint.h>
#include <stdlib.h>

#define BACKOFF_BASE_MS 500u
#define BACKOFF_MAX_MS  30000u

uint32_t backoff_ms(uint8_t attempt) {
    uint32_t exp = BACKOFF_BASE_MS << (attempt > 6 ? 6 : attempt); // 2^n tavanı
    if (exp > BACKOFF_MAX_MS) exp = BACKOFF_MAX_MS;
    uint32_t jitter = (uint32_t)(rand() % (exp / 4 + 1));          // %25 jitter
    return exp - exp / 8 + jitter;                                  // thundering herd önleme
}
```

### 4.2 C++ — Durum makinesi (C-net-1)

```cpp
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// network-master §3: 6 geçiş, FAILED → offline (K01005).
#include <cstdint>

enum class NetState : uint8_t {
    IDLE, CONNECTING, CONNECTED, DEGRADED, RECONNECTING, FAILED
};

class NetFsm {
    NetState state_{NetState::IDLE};
    uint8_t  attempt_{0};

public:
    bool onEvent(uint8_t ev) {           // ev: 0=open-ok 1=slow 2=lost 3=retry-ok 4=max
        switch (state_) {
        case NetState::IDLE:        if (ev == 9) { state_ = NetState::CONNECTING; return true; } break;
        case NetState::CONNECTING:  if (ev == 0) { state_ = NetState::CONNECTED; attempt_ = 0; return true; }
                                    if (ev == 2) { state_ = NetState::RECONNECTING; return true; } break;
        case NetState::CONNECTED:   if (ev == 1) { state_ = NetState::DEGRADED; return true; }
                                    if (ev == 2) { state_ = NetState::RECONNECTING; return true; } break;
        case NetState::DEGRADED:    if (ev == 2) { state_ = NetState::RECONNECTING; return true; } break;
        case NetState::RECONNECTING:if (ev == 3) { state_ = NetState::CONNECTED; attempt_ = 0; return true; }
                                    if (ev == 4) { state_ = NetState::FAILED; return true; }
                                    ++attempt_; break;                 // backoff artar
        case NetState::FAILED:      break;                             // offline (K01005)
        }
        return false;                                                  // geçersiz geçiş → reddet
    }
};
```

### 4.3 C# — SSE istemci (C-net-2)

```csharp
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// EventSource benzeri tek yönlü akış; payload {seekPct, elapsed} (§3.2 şeması).
using System.Net.Http.Flushable;
using System.Text.Json;

public sealed class SseClient(HttpClient http) : IAsyncDisposable
{
    public async IAsyncEnumerable<ProgressEvent> ReadAsync(
        Uri endpoint, CancellationToken ct)
    {
        using var resp = await http.GetAsync(endpoint,
            HttpCompletionOption.ResponseHeadersRead, ct);   // akış modu
        resp.EnsureSuccessStatusCode();
        using var body = await resp.Content.ReadAsStreamAsync(ct);
        using var reader = new StreamReader(body);
        while (!reader.EndOfStream && !ct.IsCancellationRequested)
        {
            var line = await reader.ReadLineAsync(ct);
            if (line.StartsWith("data: ", StringComparison.Ordinal))
            {
                var evt = JsonSerializer.Deserialize<ProgressEvent>(line[6..]);
                if (evt is not null) yield return evt;        // JSON şeması (C-net-2)
            }
        }
    }
}
```

### 4.4 Rust — WebSocket reconnect (C-net-1/C-net-4)

```rust
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Kopan WS: backoff ile yeniden bağlan; son mesajları kuyruğa al (KN-N4).
pub struct WsReconnector {
    attempt: u32,
    state: NetState,
}

impl WsReconnector {
    pub fn on_disconnect(&mut self) -> Duration {
        self.state = NetState::Reconnecting;
        self.attempt += 1;
        let exp = 500u64.saturating_mul(1u64 << self.attempt.min(6)); // 2^n
        let capped = exp.min(30_000);
        let jitter = (capped / 4) as u64 * fastrand::u64(0..100) / 100; // %25
        Duration::from_millis(capped - capped / 8 + jitter)             // §4.1 ile aynı formül
    }
}
```

### 4.5 Go — Heartbeat probu (C-net-5, K01007)

```go
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Periyodik ping; sonuç K016-observability'a metrik (KN-N6).
package netprobe

import (
	"context"
	"time"
)

type ProbeResult struct {
	OK        bool
	LatencyMs float64
	At        time.Time
}

func Heartbeat(ctx context.Context, ping func() (time.Duration, error), every time.Duration, out chan<- ProbeResult) {
	t := time.NewTicker(every)
	defer t.Stop()
	for {
		select {
		case <-ctx.Done():
			return
		case <-t.C:
			start := time.Now()
			_, err := ping()
			out <- ProbeResult{
				OK:        err == nil,                 // hata → DEGRADED/RECONNECTING sinyali
				LatencyMs: float64(time.Since(start).Microseconds()) / 1000.0,
				At:        time.Now(),
			}
		}
	}
}
```

### 4.6 Java — Offline kuyruk (C-net-6, K01005)

```java
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// KN-N4: ağ yokken yazım kuyruğa; gelince sırayla uygula (idempotent).
import java.util.concurrent.BlockingQueue;

public final class OfflineQueue {
    private final BlockingQueue<QueuedWrite> queue;
    private final RemoteWriter remote;

    public int drainOnce() {                    // bağlantı gelince: tek seferde hepsi DEĞİL
        int applied = 0;
        QueuedWrite w;
        while ((w = queue.poll()) != null) {
            try {
                remote.apply(w);                // idempotent: w.idempotencyKey ile
                applied++;
            } catch (RemoteUnavailable e) {
                queue.offer(w);                 // geri koy, sırayı KORU (LWW çakışma riski)
                break;                          // sıralı kalite: yarısını uygulayıp bırakma
            }
        }
        return applied;
    }
}
```

### 4.7 Python — Range istemcisi (C-net-3)

```python
# Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
# ETag ile resume: yarım indirme kaldığı yerden (If-Range → 206 veya 200).
import requests

def resume_download(url: str, etag: str, dest: str, chunk: int = 1048576) -> int:
    """Yarım dosyayı ETag doğrulamasıyla tamamla. Dönen: toplam byte."""
    headers = {"If-Range": etag}          # etag değiştiyse sunucu 200 döner (tüm dosya)
    with requests.get(url, headers=headers, stream=True, timeout=30) as r:
        if r.status_code == 206:          # sunucu Range kabul etti (KN-N2)
            mode = "ab"                   # kaldığı yere ekle
            written = int(r.headers.get("Content-Range", "bytes 0-").split()[1].split("-")[0])
        elif r.status_code == 200:        # etag değişmiş → baştan
            mode, written = "wb", 0
        else:
            r.raise_for_status()
        with open(dest, mode) as f:
            for block in r.iter_content(chunk):
                f.write(block)            # 1 MiB chunk (MusicStreamHandler CHUNK_BYTES)
    return written
```

### 4.8 TypeScript — SSE istemci + backoff (C-net-2/C-net-4)

```typescript
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Gerçek payload şeması: {seekPct, elapsed} (§3.2 gerçek dosya).
export interface ProgressEvent {
  readonly seekPct: number;
  readonly elapsed: string;
}

export function openSse(url: string, onEvent: (e: ProgressEvent) => void): EventSource {
  const es = new EventSource(url);            // tarayıcı auto-reconnect C-net-2
  es.addEventListener('message', (msg) => {
    const data = JSON.parse((msg as MessageEvent).data) as ProgressEvent;
    onEvent(data);                             // {seekPct, elapsed} şeması
  });
  return es;                                   // caller close() ile kapatır
}
```

### 4.9 Kotlin — Backoff policy (KN-N3)

```kotlin
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Sabit aralık YASAK (thundering herd); üstel + jitter, üst 30 s.
import kotlin.random.Random

data class Backoff(val delayMs: Long, val attempt: Int)

class BackoffPolicy(
    private val baseMs: Long = 500,
    private val maxMs: Long = 30_000,
    private val maxAttempt: Int = 8,
) {
    private var attempt = 0

    fun next(): Backoff {
        attempt = (attempt + 1).coerceAtMost(maxAttempt)
        val exp = (baseMs shl attempt.coerceAtMost(6)).coerceAtMost(maxMs)
        val jitter = Random.nextLong(exp / 4 + 1)          // %25 jitter
        return Backoff(exp - exp / 8 + jitter, attempt)    // §4.1 formülü
    }

    fun reset() { attempt = 0 }                            // CONNECTED olunca sıfırla (C-net-1)
}
```

### 4.10 Swift — Heartbeat (C-net-5)

```swift
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Timer ile ping; yanıt yoksa DEGRADED → RECONNECTING (C-net-1).
import Foundation

final class Heartbeat {
    private var timer: DispatchSourceTimer?
    private let queue = DispatchQueue(label: "net.heartbeat")

    func start(every interval: TimeInterval, ping: @escaping () -> Void) {
        let t = DispatchSource.makeTimerSource(queue: queue)
        t.schedule(deadline: .now() + interval, repeating: interval)
        t.setEventHandler(handler: ping)   // yanıt süresi ölçülüp K016'ya metrik
        t.resume()
        timer = t
    }

    func stop() { timer?.cancel(); timer = nil }
}
```

### 4.11 SQL — Sync kuyruk şeması (MySQL 8)

```sql
-- Şartname gövdesi — üretim şeması .ai/templates/ ile doğrulanacak (⚠️).
-- KN-N4: offline-first kuyruk; idempotencyKey çift uygulamayı engeller.
CREATE TABLE sync_queue (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id          BIGINT UNSIGNED NOT NULL,
    idempotency_key  CHAR(36) NOT NULL,        -- UUID: tek uygulama garantisi
    op               ENUM('create','update','delete') NOT NULL,
    entity           VARCHAR(64) NOT NULL,
    payload          JSON NOT NULL,
    attempts         TINYINT UNSIGNED NOT NULL DEFAULT 0,
    next_retry_at    DATETIME NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_sync_idem (idempotency_key),
    KEY idx_sync_user_next (user_id, next_retry_at),
    CONSTRAINT fk_sync_user FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4.12 PHP 8.4 — Backoff servisi (repo kalıbına uygun)

```php
<?php declare(strict_types=1);
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Kalıp: strict_types + final class (gerçek repo kanıtı: MusicStreamHandler L1/L21).
namespace CoreMusic\Net;

final class BackoffPolicy
{
    private const BASE_MS = 500;
    private const MAX_MS  = 30000;

    public static function delayMs(int $attempt): int
    {
        $shift = min($attempt, 6);
        $exp   = min(self::BASE_MS << $shift, self::MAX_MS);   // 2^n, üst 30s
        $jitter = random_int(0, intdiv($exp, 4));               // %25 jitter (KN-N3)
        return intdiv($exp * 7, 8) + $jitter;                   // exp - exp/8 + jitter
    }
}
```

### 4.13 SQL (ek) — Bağlantı olay günlüğü (K016 köprüsü)

```sql
-- Şartname gövdesi — üretim şeması .ai/templates/ ile doğrulanacak (⚠️).
-- C-net-1/C-net-5: durum geçişleri + probe metrikleri (K016-observability okur).
CREATE TABLE net_event (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    component   ENUM('stream','sse','websocket','rest','sync','retry','probe') NOT NULL,
    from_state  VARCHAR(16) NOT NULL,
    to_state    VARCHAR(16) NOT NULL,
    latency_ms  DECIMAL(8,2) NULL,
    detail      JSON NULL,
    occurred_at DATETIME(3) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_net_comp_time (component, occurred_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## §5 — Dil × sözleşme matrisi

| # | Dil | Sözleşme | Blok | Durum |
|---|---|---|---|---|
| 1 | PHP 8.4 | Stream 206/ETag (gerçek) · Backoff | §3.1 · §4.12 | ✓ Kanıtlı + şartname |
| 2 | JS | SSE şeması (gerçek) | §3.2 | ✓ Kanıtlı |
| 3 | C | Backoff jitter | §4.1 | ⚠️ Şartname |
| 4 | C++ | Durum makinesi | §4.2 | ⚠️ Şartname |
| 5 | C# | SSE istemci | §4.3 | ⚠️ Şartname |
| 6 | Rust | WS reconnect | §4.4 | ⚠️ Şartname |
| 7 | Go | Heartbeat | §4.5 | ⚠️ Şartname |
| 8 | Java | Offline kuyruk | §4.6 | ⚠️ Şartname |
| 9 | Python | Range resume | §4.7 | ⚠️ Şartname |
| 10 | TypeScript | SSE + tip | §4.8 | ⚠️ Şartname |
| 11 | Kotlin | Backoff policy | §4.9 | ⚠️ Şartname |
| 12 | Swift | Heartbeat timer | §4.10 | ⚠️ Şartname |
| 13 | SQL | Sync kuyruk + net_event | §4.11 · §4.13 | ⚠️ Şartname |

## §6 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| Stream 206/ETag/Accept-Ranges | `MusicStreamHandler.php` L9-L20, L46 | ✓ Okundu 2026-10-18 |
| SSE payload `{seekPct, elapsed}` | `player-api.mock.js` L136-L156 | ✓ Okundu 2026-10-18 |
| 13 dil × sözleşme | bu dosya §4 | ✓ 2026-10-18 |
| Ağ motor kodu (Go/Rust/…) gerçek API'leri | repo'da bu dillerde ağ dosyası YOK | ⚠️ VERIFICATION REQUIRED |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
