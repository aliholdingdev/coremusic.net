---
node_id: K014-network/handoff/kod-ornekleri
node_type: module
status: draft
parent_id: K014-network/handoff
role: ornek
children: []
depends_on:
  - "K014-network/handoff/handoff-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans kaynaklar: handoff-standart-kanit.md §1–§2 (Remote Playback · Presentation · RFC 6455/9110)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "handoff ↔ multi-room sınırı (§7/1) + pozisyon paket formatı → ⚠️ UNKNOWN (faz C)"
group: kod
---

# handoff — kod-ornekleri (kademe-2 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: **handoff token
> değişimi** — kaynak cihaz `offer` eder (tek kullanımlık token + durum özeti),
> hedef cihaz `accept` ile alır; ikinci kabul reddedilir (tek devir — idempotency).
> Durum özeti **içeriği** K012/playback-service'e aittir; burada yalnız zarf.

## §1 PHP 8.4 — HandoffCoordinator (tek devir token · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/handoff — devir koordinatörü (TASARIM ÖRNEĞİ).
 * Sınır: durum özeti içeriği K012/playback-service; transport (REST/WS) taşıyıcıdır —
 *        handoff-standart-kanit.md §2 (RFC 9110/6455).
 * Karar: pozisyon paket formatı UNKNOWN (faz C) — payload burada opaque string.
 */
final class HandoffCoordinator
{
    /** @var array<string, array{payload: string, accepted: bool, expiresAt: float}> */
    private array $offers = [];

    private const TTL_SECONDS = 30.0; // süre değeri — bilinen başlangıç, politika faz C

    /** @return string tek kullanımlık token */
    public function offer(string $payload): string
    {
        $token = bin2hex(random_bytes(16));
        $this->offers[$token] = [
            'payload' => $payload,
            'accepted' => false,
            'expiresAt' => microtime(true) + self::TTL_SECONDS,
        ];
        return $token;
    }

    /**
     * Tek kabul hakkını alır; ikinci kabul null döner (tek devir).
     */
    public function accept(string $token): ?string
    {
        $o = $this->offers[$token] ?? null;
        if ($o === null || $o['accepted'] || $o['expiresAt'] < microtime(true)) {
            return null; // yok · zaten devredildi · süresi doldu
        }
        $this->offers[$token]['accepted'] = true;
        return $o['payload'];
    }
}
```

## §2 Vanilla JS (ES6+) — WS üzerinden offer/accept (taşıyıcı: RFC 6455)

```js
/**
 * K014/handoff — WS tabanlı devir istemcisi (TASARIM ÖRNEĞİ).
 * Taşıyıcı: WebSocket (handoff-standart-kanit.md §2/3, §2/5).
 * Sınır: payload içeriği K012; pozisyon paket formatı UNKNOWN (faz C).
 */
export class HandoffClient {
  /** @param {string} wsUrl çift yönlü kanal (comms/websocket sözleşmesi) */
  constructor(wsUrl) {
    this.wsUrl = wsUrl;
  }

  /** @returns {Promise<string>} tek kullanımlık devir token'ı */
  offer(payload) {
    return this.#rpc({ cmd: 'handoff-offer', payload });
  }

  /** @returns {Promise<string|null>} null = zaten devredildi/süresi doldu */
  async accept(token) {
    const res = await this.#rpc({ cmd: 'handoff-accept', token });
    return res?.payload ?? null;
  }

  #rpc(msg) {
    return new Promise((resolve, reject) => {
      const ws = new WebSocket(this.wsUrl);
      const timer = setTimeout(() => { ws.close(); reject(new Error('timeout')); }, 5000);
      ws.onmessage = (ev) => {
        clearTimeout(timer); ws.close();
        const r = JSON.parse(ev.data);
        if (r.error) resolve(null); else resolve(r);
      };
      ws.onerror = () => { clearTimeout(timer); reject(new Error('kanal hatası')); };
      ws.onopen = () => ws.send(JSON.stringify(msg));
    });
  }
}
```

## §3 C++20 — HandoffCoordinator (Neva Engine · unordered_map · noexcept okuma)

```cpp
// K014/handoff — devir koordinatörü (TASARIM ÖRNEĞİ).
// Sınır: payload opaque (içerik K012); transport faz C (WS/REST taşıyıcı).
#pragma once
#include <chrono>
#include <optional>
#include <random>
#include <string>
#include <string_view>
#include <unordered_map>

namespace cm::net {

class HandoffCoordinator {
public:
    struct Offer {
        std::string payload;
        bool accepted{false};
        std::chrono::steady_clock::time_point expires_at;
    };

    // Tek kullanımlık token üretir (32 hex karakter — basit rastgelelik; kripto değil).
    [[nodiscard]] std::string offer(std::string payload) {
        std::string token = randomToken();
        offers_[token] = Offer{std::move(payload), false,
                               clock::now() + std::chrono::seconds{30}}; // politika faz C
        return token;
    }

    // Tek kabul hakkı: ikinci kabul / süre dolmuş / yok → nullopt.
    [[nodiscard]] std::optional<std::string> accept(std::string_view token) {
        auto it = offers_.find(std::string{token});
        if (it == offers_.end() || it->second.accepted
            || it->second.expires_at < clock::now())
            return std::nullopt;
        it->second.accepted = true;
        return std::move(it->second.payload);
    }

private:
    using clock = std::chrono::steady_clock;
    std::unordered_map<std::string, Offer> offers_;

    static std::string randomToken() {
        static constexpr char hex[] = "0123456789abcdef";
        std::random_device rd;
        std::string out(32, '0');
        for (auto& c : out) c = hex[rd() & 0xF];
        return out;
    }
};

} // namespace cm::net
```

## §4 C# — HandoffCoordinator (ConcurrentDictionary · tek devir)

```csharp
// K014/handoff — devir koordinatörü (TASARIM ÖRNEĞİ).
// Sınır: payload opaque (içerik K012); transport (REST §2/4 · WS §2/3) taşıyıcıdır.
#nullable enable
using System.Collections.Concurrent;
using System.Security.Cryptography;

namespace CoreMusic.Network.Handoff;

public sealed record HandoffOffer(string Payload, bool Accepted, DateTimeOffset ExpiresAt);

public sealed class HandoffCoordinator(TimeSpan? ttl = null)
{
    private readonly TimeSpan _ttl = ttl ?? TimeSpan.FromSeconds(30); // politika faz C
    private readonly ConcurrentDictionary<string, HandoffOffer> _offers = new();

    /// <summary>Tek kullanımlık devir token'ı üretir.</summary>
    public string Offer(string payload)
    {
        var token = Convert.ToHexString(RandomNumberGenerator.GetBytes(16));
        _offers[token] = new HandoffOffer(payload, Accepted: false, DateTimeOffset.UtcNow.Add(_ttl));
        return token;
    }

    /// <summary>İlk kabul payload'ı döner; sonraki kabuller null (tek devir).</summary>
    public string? Accept(string token)
    {
        if (!_offers.TryGetValue(token, out var o)) return null;
        if (o.Accepted || o.ExpiresAt < DateTimeOffset.UtcNow) return null;
        var updated = o with { Accepted = true };
        if (!_offers.TryUpdate(token, updated, o)) return null; // yarışta kaybeden taraf null
        return o.Payload;
    }
}
```

## §5 Python 3.12+ — HandoffCoordinator (secrets token · basit kilit)

```python
# K014/handoff — devir koordinatörü (TASARIM ÖRNEĞİ).
# Sınır: payload opaque (içerik K012); transport taşıyıcıdır (REST/WS — §2).
from __future__ import annotations

import secrets
import threading
import time
from dataclasses import dataclass, field


@dataclass(slots=True)
class _Offer:
    payload: str
    accepted: bool = False
    expires_at: float = field(default_factory=lambda: time.monotonic() + 30.0)  # politika faz C


class HandoffCoordinator:
    def __init__(self) -> None:
        self._offers: dict[str, _Offer] = {}
        self._lock = threading.Lock()

    def offer(self, payload: str) -> str:
        """Tek kullanımlık devir token'ı üretir."""
        token = secrets.token_hex(16)
        with self._lock:
            self._offers[token] = _Offer(payload)
        return token

    def accept(self, token: str) -> str | None:
        """İlk kabul payload'ı döner; sonraki kabuller None (tek devir)."""
        with self._lock:
            o = self._offers.get(token)
            if o is None or o.accepted or o.expires_at < time.monotonic():
                return None
            o.accepted = True
            return o.payload
```

## §6 Go — HandoffCoordinator (sync.Mutex · tek devir)

```go
// K014/handoff — devir koordinatörü (TASARIM ÖRNEĞİ).
// Sınır: payload opaque (içerik K012); transport taşıyıcıdır (REST §2/4 · WS §2/3).
package handoff

import (
	"crypto/rand"
	"encoding/hex"
	"sync"
	"time"
)

type offer struct {
	payload   string
	accepted  bool
	expiresAt time.Time
}

type Coordinator struct {
	mu     sync.Mutex
	ttl    time.Duration
	offers map[string]offer
}

func NewCoordinator(ttl time.Duration) *Coordinator { // politika faz C
	if ttl <= 0 {
		ttl = 30 * time.Second
	}
	return &Coordinator{ttl: ttl, offers: make(map[string]offer)}
}

// Offer tek kullanımlık devir token'ı üretir.
func (c *Coordinator) Offer(payload string) (string, error) {
	buf := make([]byte, 16)
	if _, err := rand.Read(buf); err != nil {
		return "", err
	}
	token := hex.EncodeToString(buf)
	c.mu.Lock()
	defer c.mu.Unlock()
	c.offers[token] = offer{payload: payload, expiresAt: time.Now().Add(c.ttl)}
	return token, nil
}

// Accept ilk kabul payload'ı döner; sonraki kabuller "" (tek devir).
func (c *Coordinator) Accept(token string) string {
	c.mu.Lock()
	defer c.mu.Unlock()
	o, ok := c.offers[token]
	if !ok || o.accepted || time.Now().After(o.expiresAt) {
		return ""
	}
	o.accepted = true
	c.offers[token] = o
	return o.payload
}
```

## §7 Rust — HandoffCoordinator (Mutex<HashMap> · tek devir)

```rust
// K014/handoff — devir koordinatörü (TASARIM ÖRNEĞİ).
// Sınır: payload opaque (içerik K012); transport taşıyıcıdır (REST/WS — §2).
use std::collections::HashMap;
use std::sync::Mutex;
use std::time::{Duration, Instant};

struct Offer {
    payload: String,
    accepted: bool,
    expires_at: Instant,
}

pub struct HandoffCoordinator {
    ttl: Duration, // politika faz C
    offers: Mutex<HashMap<String, Offer>>,
}

impl HandoffCoordinator {
    pub fn new(ttl: Duration) -> Self {
        Self { ttl, offers: Mutex::new(HashMap::new()) }
    }

    /// Tek kullanımlık devir token'ı üretir (32 hex karakter).
    pub fn offer(&self, payload: impl Into<String>) -> String {
        let token = random_token();
        self.offers.lock().expect("kilit zehirlenmesi").insert(
            token.clone(),
            Offer { payload: payload.into(), accepted: false, expires_at: Instant::now() + self.ttl },
        );
        token
    }

    /// İlk kabul payload'ı döner; sonraki kabuller None (tek devir).
    pub fn accept(&self, token: &str) -> Option<String> {
        let mut map = self.offers.lock().expect("kilit zehirlenmesi");
        let o = map.get_mut(token)?;
        if o.accepted || o.expires_at < Instant::now() {
            return None;
        }
        o.accepted = true;
        Some(o.payload.clone())
    }
}

fn random_token() -> String {
    // TASARIM ÖRNEĞİ: basit rastgele hex — üretimde kriptografik RNG (K009 sınırı) faz C.
    use std::time::{SystemTime, UNIX_EPOCH};
    let t = SystemTime::now().duration_since(UNIX_EPOCH).map(|d| d.as_nanos()).unwrap_or(0);
    format!("{:032x}", t as u128)
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | tek devir token deposu | payload opaque (K012) |
| 2 | JS ES6+ | Web UI | WS offer/istemcisi | taşıyıcı RFC 6455 (§2/3) |
| 3 | C++20 | Neva Engine | tek devir deposu | transport faz C |
| 4 | C# | Windows kolu | yarış-güvenli (TryUpdate) | transport REST/WS — taşıyıcı |
| 5 | Python | ağ testi/araç | kilitli depo | payload opaque |
| 6 | Go | sunucu/tünel | mutex depo | crypto/rand yerleşik |
| 7 | Rust | sistem | Mutex<HashMap> | RNG seçimi K009 sınırında — faz C |

**Kapı:** 7/7 dil örneği üretildi; **pozisyon paket formatı ve §7/1 sınırı** açık
kaldığı için payload opaque bırakılmıştır (handoff-standart-kanit.md §4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
