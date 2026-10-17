---
node_id: K010-network/connectivity/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/connectivity
role: ornek
children: []
depends_on:
  - "K010-network/connectivity/connectivity-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans standartlar: connectivity-standart-kanit.md §1–§3 (RFC 8305/6555/3927 · MDN online/offline · WLAN API)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Yeniden bağlanma stratejisi parametreleri (tetik/backoff sırası) → ⚠️ UNKNOWN (faz C)"
group: kod
---

# connectivity — kod-ornekleri (kademe-2 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: bağlantı durumunu
> tutan + koptuğunda üstel geri çekilmeli (exponential backoff) yeniden bağlanma
> denetçisi (supervisor). Backoff sırası/parametreleri UNKNOWN'dur (index §A/2).

## §1 PHP 8.4 — ConnectionSupervisor (strict_types · tek iş: durum + backoff)

```php
<?php
declare(strict_types=1);

/**
 * K014/connectivity — bağlantı denetçisi iskeleti (TASARIM ÖRNEĞİ).
 * Sınır: protokol anlamı yok (comms/), şifreleme yok (K009), donanım yok (K001).
 */
final class ConnectionSupervisor
{
    public const STATE_CONNECTED = 'connected';
    public const STATE_RECONNECTING = 'reconnecting';
    public const STATE_FAILED = 'failed';

    private string $state = self::STATE_FAILED;
    private int $attempt = 0;

    /** @param \Closure(): bool $dial false = bağlantı kurulamadı */
    public function __construct(
        private readonly \Closure $dial,
        private readonly int $maxRetries = 5,
        private readonly int $baseDelayUs = 100_000, // 100ms — bilinen başlangıç değeri
    ) {
    }

    public function state(): string
    {
        return $this->state;
    }

    /**
     * Bağlantı koptuğunda çağrılır; üstel geri çekilme ile yeniden dener.
     *
     * @throws RuntimeException tüm denemeler doldu
     */
    public function reconnect(): void
    {
        $this->state = self::STATE_RECONNECTING;
        $this->attempt = 0;
        while ($this->attempt <= $this->maxRetries) {
            if ($this->attempt > 0) {
                usleep($this->baseDelayUs * (2 ** ($this->attempt - 1))); // 100ms → 200ms → …
            }
            $this->attempt++;
            if (($this->dial)()) {
                $this->state = self::STATE_CONNECTED;
                return;
            }
        }
        $this->state = self::STATE_FAILED;
        throw new RuntimeException('yeniden bağlanma doldu');
    }
}
```

## §2 Vanilla JS (ES6+) — online/offline olayları + backoff reconnect

```js
/**
 * K014/connectivity — tarayıcı bağlantı denetçisi (TASARIM ÖRNEĞİ).
 * Kanıt: MDN onLine + online/offline olayları (connectivity-standart-kanit.md §2).
 * Sınır: protokol anlamı yok (comms/), şifreleme yok (K009).
 */
export class ConnectionSupervisor {
  /** @param {{connect: () => Promise<void>, maxRetries?: number, baseMs?: number}} cfg */
  constructor(cfg) {
    this.connect = cfg.connect;                 // somut taşıyıcı (WS/HTTP/…) — çağırıcı verir
    this.maxRetries = cfg.maxRetries ?? 5;
    this.baseMs = cfg.baseMs ?? 100;            // bilinen başlangıç değeri — strateji faz C
    this.state = 'failed';
    this.#bind();
  }

  #bind() {
    // Tarayıcıda bağlantı durumu sinyali: online/offline olayları (MDN §2/5–6).
    window.addEventListener('online', () => { this.state = 'reconnecting'; this.#loop(); });
    window.addEventListener('offline', () => { this.state = 'disconnected'; });
  }

  /** Manuel tetik de mümkündür (transport-ölçü koptuğunda). */
  async reconnect() {
    this.state = 'reconnecting';
    await this.#loop();
  }

  async #loop() {
    for (let attempt = 0; attempt <= this.maxRetries; attempt++) {
      if (attempt > 0) {
        await new Promise(r => setTimeout(r, this.baseMs * 2 ** (attempt - 1)));
      }
      if (navigator.onLine === false) continue; // offline ise dial boşa düşmesin (MDN §2/4)
      try {
        await this.connect();
        this.state = 'connected';
        return;
      } catch {
        // geçici — devam
      }
    }
    this.state = 'failed';
    throw new Error('yeniden bağlanma doldu');
  }
}
```

## §3 C++20 — ConnectionSupervisor (Neva Engine · RAII · noexcept yollar)

```cpp
// K014/connectivity — bağlantı denetçisi (TASARIM ÖRNEĞİ).
// Sınır: taşıyıcı (ASIO soket bağlama) faz C'de; burada yalnız durum + backoff sözleşmesi.
#pragma once
#include <chrono>
#include <expected>
#include <functional>
#include <thread>

namespace cm::net {

enum class ConnState { connected, reconnecting, failed };

class ConnectionSupervisor {
public:
    struct Config {
        int max_retries{5};
        std::chrono::milliseconds base_delay{100}; // bilinen başlangıç — strateji faz C
    };

    // dial: false = bağlanamadı. Caller platform taşıyıcısını verir (faz C).
    using DialFn = std::function<bool()>;

    explicit ConnectionSupervisor(DialFn dial, Config cfg = {})
        : dial_(std::move(dial)), cfg_(cfg) {}

    [[nodiscard]] ConnState state() const noexcept { return state_; }

    // Kopukken çağrılır; üstel geri çekilme ile yeniden dener.
    [[nodiscard]] std::expected<void, const char*> reconnect() {
        state_ = ConnState::reconnecting;
        for (int attempt = 0; attempt <= cfg_.max_retries; ++attempt) {
            if (attempt > 0)
                std::this_thread::sleep_for(cfg_.base_delay * (1 << (attempt - 1)));
            if (dial_()) { state_ = ConnState::connected; return {}; }
        }
        state_ = ConnState::failed;
        return std::unexpected("yeniden bağlanma doldu");
    }

private:
    DialFn dial_;
    Config cfg_;
    ConnState state_{ConnState::failed};
};

} // namespace cm::net
```

## §4 C# — ConnectionSupervisor (nullable · ConfigureAwait(false) · CancellationToken)

```csharp
// K014/connectivity — bağlantı denetçisi (TASARIM ÖRNEĞİ).
// Sınır: protokol anlamı yok (comms/), şifreleme yok (K009).
#nullable enable

namespace CoreMusic.Network.Connectivity;

public enum ConnState { Connected, Reconnecting, Failed }

public sealed record SupervisorConfig(int MaxRetries = 5, int BaseDelayMs = 100);

public sealed class ConnectionSupervisor(
    Func<CancellationToken, Task<bool>> dial,
    SupervisorConfig? cfg = null)
{
    private readonly SupervisorConfig _cfg = cfg ?? new SupervisorConfig();
    public ConnState State { get; private set; } = ConnState.Failed;

    /// <summary>Bağlantı koptuğunda çağrılır; üstel geri çekilme ile yeniden dener.</summary>
    /// <exception cref="TimeoutException">tüm denemeler doldu</exception>
    public async Task ReconnectAsync(CancellationToken ct = default)
    {
        State = ConnState.Reconnecting;
        for (var attempt = 0; attempt <= _cfg.MaxRetries; attempt++)
        {
            if (attempt > 0)
                await Task.Delay(TimeSpan.FromMilliseconds(_cfg.BaseDelayMs * Math.Pow(2, attempt - 1)), ct)
                          .ConfigureAwait(false);
            if (await dial(ct).ConfigureAwait(false))
            {
                State = ConnState.Connected;
                return;
            }
        }
        State = ConnState.Failed;
        throw new TimeoutException("yeniden bağlanma doldu");
    }
}
```

## §5 Python 3.12+ — async reconnect döngüsü (asyncio)

```python
# K014/connectivity — async bağlantı denetçisi (TASARIM ÖRNEĞİ).
# Sınır: protokol anlamı yok (comms/), şifreleme yok (K009).
from __future__ import annotations

import asyncio
from collections.abc import Awaitable, Callable
from dataclasses import dataclass
from enum import StrEnum


class ConnState(StrEnum):
    CONNECTED = "connected"
    RECONNECTING = "reconnecting"
    FAILED = "failed"


@dataclass(frozen=True, slots=True)
class SupervisorConfig:
    max_retries: int = 5
    base_delay: float = 0.1   # 100ms — bilinen başlangıç; strateji faz C


class ReconnectExhausted(Exception):
    """Tüm yeniden denemeler doldu."""


class ConnectionSupervisor:
    def __init__(self, dial: Callable[[], Awaitable[bool]],
                 cfg: SupervisorConfig | None = None) -> None:
        self._dial = dial
        self._cfg = cfg or SupervisorConfig()
        self.state = ConnState.FAILED

    async def reconnect(self) -> None:
        self.state = ConnState.RECONNECTING
        for attempt in range(self._cfg.max_retries + 1):
            if attempt:
                await asyncio.sleep(self._cfg.base_delay * 2 ** (attempt - 1))  # 100ms → 200ms → …
            if await self._dial():
                self.state = ConnState.CONNECTED
                return
        self.state = ConnState.FAILED
        raise ReconnectExhausted("yeniden bağlanma doldu")
```

## §6 Go — ReconnectWithBackoff (fonksiyonel · context iptali)

```go
// K014/connectivity — geri çekilmeli yeniden bağlanma (TASARIM ÖRNEĞİ).
// Sınır: protokol anlamı yok (comms/), şifreleme yok (K009).
package connectivity

import (
	"context"
	"errors"
	"fmt"
	"time"
)

// ErrReconnectExhausted tüm denemelerin dolduğunu gösterir.
var ErrReconnectExhausted = errors.New("yeniden bağlanma doldu")

type Config struct {
	MaxRetries int
	BaseDelay  time.Duration
}

func DefaultConfig() Config { return Config{MaxRetries: 5, BaseDelay: 100 * time.Millisecond} }

// dial false dönerse geçici kabul edilir; context iptalinde erken biter.
func ReconnectWithBackoff(ctx context.Context, dial func(context.Context) (bool, error), cfg Config) error {
	for attempt := 0; attempt <= cfg.MaxRetries; attempt++ {
		if attempt > 0 {
			select {
			case <-time.After(cfg.BaseDelay << uint(attempt-1)):
			case <-ctx.Done():
				return ctx.Err()
			}
		}
		ok, err := dial(ctx)
		if err != nil && !errors.Is(err, context.Canceled) {
			// geçici hatada devam et (kalıcı ayrımı comms katmanında — faz C)
			_ = err
		}
		if ok {
			return nil
		}
	}
	return fmt.Errorf("%w: %d deneme", ErrReconnectExhausted, cfg.MaxRetries+1)
}
```

## §7 Rust — tokio tabanlı ReconnectSupervisor (async · taşınan JoinHandle)

```rust
// K014/connectivity — async bağlantı denetçisi (TASARIM ÖRNEĞİ).
// Sınır: protokol anlamı yok (comms/), şifreleme yok (K009).
use std::future::Future;
use std::pin::Pin;
use std::time::Duration;

#[derive(Clone, Copy, Debug, PartialEq, Eq)]
pub enum ConnState {
    Connected,
    Reconnecting,
    Failed,
}

#[derive(Clone)]
pub struct SupervisorConfig {
    pub max_retries: u32,
    pub base_delay: Duration,
}

impl Default for SupervisorConfig {
    fn default() -> Self {
        Self { max_retries: 5, base_delay: Duration::from_millis(100) }
    }
}

#[derive(Debug, thiserror::Error)]
#[error("yeniden bağlanma doldu: {attempts} deneme")]
pub struct ReconnectExhausted { pub attempts: u32 }

pub struct ConnectionSupervisor<F, Fut> {
    dial: F,
    cfg: SupervisorConfig,
    pub state: ConnState,
}

impl<F, Fut> ConnectionSupervisor<F, Fut>
where
    F: Fn() -> Fut,
    Fut: Future<Output = bool>,
{
    pub fn new(dial: F, cfg: SupervisorConfig) -> Self {
        Self { dial, cfg, state: ConnState::Failed }
    }

    pub async fn reconnect(&mut self) -> Result<(), ReconnectExhausted> {
        self.state = ConnState::Reconnecting;
        for attempt in 0..=self.cfg.max_retries {
            if attempt > 0 {
                tokio::time::sleep(self.cfg.base_delay * 2u32.pow(attempt - 1)).await; // 100ms → 200ms → …
            }
            if (self.dial)().await {
                self.state = ConnState::Connected;
                return Ok(());
            }
        }
        self.state = ConnState::Failed;
        Err(ReconnectExhausted { attempts: self.cfg.max_retries + 1 })
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | durum + backoff denetçisi | taşıyıcı (dial) çağırıcıdan |
| 2 | JS ES6+ | Web UI | online/offline olay + backoff | tarayıcı sinyali MDN §2 |
| 3 | C++20 | Neva Engine | durum + backoff denetçisi | ASIO taşıyıcısı faz C |
| 4 | C# | Windows kolu | async denetçisi | CancellationToken zinciri |
| 5 | Python | ağ testi/araç | async döngü | stdlib asyncio — kütüphane yok |
| 6 | Go | sunucu/tünel | fonksiyonel backoff | context iptali yerleşik |
| 7 | Rust | sistem | async denetçisi | tokio — bağımlılık seçimi faz C |

**Kapı:** 7/7 dil örneği üretildi; backoff **sırası/parametreleri** strateji değildir —
index §A/2 (UNKNOWN) faz C'de kararlaştırılır (connectivity-standart-kanit.md §4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
