---
node_id: K010-network/service-discovery/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/service-discovery
role: ornek
children: []
depends_on:
  - "K010-network/service-discovery/service-discovery-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans standartlar: service-discovery-standart-kanit.md §1 (RFC 6762/6763/6761/7252)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Kapsam split'i (cihaz keşfi vs servis kaydı) + kütüphane seçimi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kod
---

# service-discovery — kod-ornekleri (kademe-2 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: TTL önbellekli
> servis kayıt/çözümleme deposu (DNS-SD modelinden türetilmiş — RFC 6763 §1/2).
> Gerçek mDNS sorusu kütüphane gerektirir; kütüphane seçimi faz C'dedir.

## §1 PHP 8.4 — ServiceRegistry (TTL önbellekli kayıt/çözümleme · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/service-discovery — servis kayıt/çözüm deposu (TASARIM ÖRNEĞİ).
 * Model: DNS-SD (RFC 6763) — hizmet adı + tip + adres; TTL ile son kullanma.
 * Sınır: mDNS ağa paket atmaz (kütüphane seçimi faz C); dağıtım K018-devops.
 */
final class ServiceRegistry
{
    /** @var array<string, array{addr: string, port: int, expiresAt: float}> */
    private array $entries = [];

    /** @param float $ttlSeconds kayıt ömrü (DNS-SD TTL modeli — değer faz C) */
    public function register(string $service, string $addr, int $port, float $ttlSeconds = 120.0): void
    {
        $this->entries[$service] = [
            'addr' => $addr,
            'port' => $port,
            'expiresAt' => microtime(true) + $ttlSeconds,
        ];
    }

    /** @return array{addr: string, port: int}|null süre dolmuşsa null (ve kaydı sil) */
    public function resolve(string $service): ?array
    {
        $e = $this->entries[$service] ?? null;
        if ($e === null) {
            return null;
        }
        if ($e['expiresAt'] < microtime(true)) {
            unset($this->entries[$service]);
            return null;
        }
        return ['addr' => $e['addr'], 'port' => $e['port']];
    }
}
```

## §2 Vanilla JS (ES6+) — ServiceRegistry + online tazeleme sinyali

```js
/**
 * K014/service-discovery — tarayıcı tarafı servis deposu (TASARIM ÖRNEĞİ).
 * Not: tarayıcıda mDNS/DNS-SD yoktur; kayıt/çözüm bridge üzerinden gelir
 * (device-communication §2 ile aynı WS-bridge deseni). Sınır: K018/K012 değil.
 */
export class ServiceRegistry {
  /** @param {{ttlMs?: number, bridgeUrl?: string}} cfg */
  constructor(cfg = {}) {
    this.ttlMs = cfg.ttlMs ?? 120_000;      // DNS-SD TTL modeli — değer faz C
    this.bridgeUrl = cfg.bridgeUrl ?? null;  // varsa bridge ile genişletme (faz C)
    /** @type {Map<string, {addr: string, port: number, expiresAt: number}>} */
    this.entries = new Map();
  }

  /** @param {string} service @param {string} addr @param {number} port */
  register(service, addr, port) {
    this.entries.set(service, { addr, port, expiresAt: Date.now() + this.ttlMs });
  }

  /** @param {string} service @returns {{addr: string, port: number} | null} */
  resolve(service) {
    const e = this.entries.get(service);
    if (!e) return null;
    if (e.expiresAt < Date.now()) { this.entries.delete(service); return null; }
    return { addr: e.addr, port: e.port };
  }

  /** Bağlantı gelince depoyu tazele (connectivity online olayı ile). */
  async refreshOnOnline() {
    window.addEventListener('online', async () => {
      if (!this.bridgeUrl) return;
      try {
        const res = await fetch(this.bridgeUrl);          // yalnız taşıma; yanıt anlamı K012/K013
        if (!res.ok) return;
        for (const s of await res.json()) this.register(s.name, s.addr, s.port);
      } catch { /* geçici — sonraki online olayında tekrar */ }
    });
  }
}
```

## §3 C++20 — ServiceRegistry (Neva Engine · RAII · const-correct)

```cpp
// K014/service-discovery — servis kayıt/çözüm deposu (TASARIM ÖRNEĞİ).
// Model: DNS-SD (RFC 6763). Sınır: ağ paketi atmaz (mDNS kütüphanesi faz C),
//        servis anlamı çözümlemez (K012).
#pragma once
#include <chrono>
#include <optional>
#include <string>
#include <string_view>
#include <unordered_map>

namespace cm::net {

struct Endpoint {
    std::string addr;
    std::uint16_t port;
};

class ServiceRegistry {
public:
    explicit ServiceRegistry(std::chrono::seconds ttl = std::chrono::seconds{120}) // değer faz C
        : ttl_(ttl) {}

    void register_service(std::string_view name, Endpoint ep) {
        entries_[std::string{name}] = Entry{std::move(ep), clock::now() + ttl_};
    }

    // Süre dolmuş kaydı görmez ve siler (lazy expiry).
    [[nodiscard]] std::optional<Endpoint> resolve(std::string_view name) const {
        auto it = entries_.find(std::string{name});
        if (it == entries_.end()) return std::nullopt;
        if (it->second.expires_at < clock::now()) { entries_.erase(it); return std::nullopt; }
        return it->second.ep;
    }

private:
    using clock = std::chrono::steady_clock;
    struct Entry { Endpoint ep; clock::time_point expires_at; };
    std::chrono::seconds ttl_;
    mutable std::unordered_map<std::string, Entry> entries_; // lazy expiry için mutable
};

} // namespace cm::net
```

## §4 C# — ServiceRegistry (record DTO · nullable · ConfigureAwait(false))

```csharp
// K014/service-discovery — servis kayıt/çözüm deposu (TASARIM ÖRNEĞİ).
// Model: DNS-SD (RFC 6763). Sınır: ağ keşfi (mDNS) kütüphane seçimi faz C.
#nullable enable
using System.Collections.Concurrent;

namespace CoreMusic.Network.Discovery;

public sealed record ServiceEndpoint(string Addr, ushort Port);

public sealed class ServiceRegistry(TimeSpan? ttl = null)
{
    private readonly TimeSpan _ttl = ttl ?? TimeSpan.FromSeconds(120); // değer faz C
    private readonly ConcurrentDictionary<string, (ServiceEndpoint Ep, DateTimeOffset ExpiresAt)> _entries = new();

    public void Register(string service, ServiceEndpoint ep)
        => _entries[service] = (ep, DateTimeOffset.UtcNow.Add(_ttl));

    /// <summary>Süre dolmuş kaydı null döner ve depodan düşer (lazy expiry).</summary>
    public ServiceEndpoint? Resolve(string service)
    {
        if (!_entries.TryGetValue(service, out var e)) return null;
        if (e.ExpiresAt < DateTimeOffset.UtcNow)
        {
            _entries.TryRemove(service, out _);
            return null;
        }
        return e.Ep;
    }
}
```

## §5 Python 3.12+ — ServiceRegistry (dataclass · lazy expiry)

```python
# K014/service-discovery — servis kayıt/çözüm deposu (TASARIM ÖRNEĞİ).
# Model: DNS-SD (RFC 6763). Sınır: mDNS ağa paket atmaz (kütüphane faz C),
#        servis anlamı çözümlemez (K012).
from __future__ import annotations

import time
from dataclasses import dataclass


@dataclass(frozen=True, slots=True)
class ServiceEndpoint:
    addr: str
    port: int


@dataclass(slots=True)
class _Entry:
    ep: ServiceEndpoint
    expires_at: float


class ServiceRegistry:
    def __init__(self, ttl: float = 120.0) -> None:  # TTL değeri faz C
        self._ttl = ttl
        self._entries: dict[str, _Entry] = {}

    def register(self, service: str, ep: ServiceEndpoint) -> None:
        self._entries[service] = _Entry(ep, time.monotonic() + self._ttl)

    def resolve(self, service: str) -> ServiceEndpoint | None:
        e = self._entries.get(service)
        if e is None:
            return None
        if e.expires_at < time.monotonic():
            del self._entries[service]   # lazy expiry
            return None
        return e.ep
```

## §6 Go — ServiceRegistry (mutex korumalı · lazy expiry)

```go
// K014/service-discovery — servis kayıt/çözüm deposu (TASARIM ÖRNEĞİ).
// Model: DNS-SD (RFC 6763). Sınır: mDNS ağa paket atmaz (kütüphane faz C),
//        dağıtım K018-devops.
package discovery

import (
	"sync"
	"time"
)

type Endpoint struct {
	Addr string
	Port uint16
}

type entry struct {
	ep        Endpoint
	expiresAt time.Time
}

type Registry struct {
	mu      sync.Mutex
	ttl     time.Duration
	entries map[string]entry
}

func NewRegistry(ttl time.Duration) *Registry { // TTL değeri faz C
	if ttl <= 0 {
		ttl = 120 * time.Second
	}
	return &Registry{ttl: ttl, entries: make(map[string]entry)}
}

func (r *Registry) Register(service string, ep Endpoint) {
	r.mu.Lock()
	defer r.mu.Unlock()
	r.entries[service] = entry{ep: ep, expiresAt: time.Now().Add(r.ttl)}
}

// Resolve süre dolmuş kaydı bulursa siler ve false döner (lazy expiry).
func (r *Registry) Resolve(service string) (Endpoint, bool) {
	r.mu.Lock()
	defer r.mu.Unlock()
	e, ok := r.entries[service]
	if !ok {
		return Endpoint{}, false
	}
	if time.Now().After(e.expiresAt) {
		delete(r.entries, service)
		return Endpoint{}, false
	}
	return e.ep, true
}
```

## §7 Rust — ServiceRegistry (RwLock · lazy expiry)

```rust
// K014/service-discovery — servis kayıt/çözüm deposu (TASARIM ÖRNEĞİ).
// Model: DNS-SD (RFC 6763). Sınır: mDNS ağa paket atmaz (kütüphane faz C).
use std::collections::HashMap;
use std::sync::RwLock;
use std::time::{Duration, Instant};

#[derive(Clone, Debug)]
pub struct Endpoint {
    pub addr: String,
    pub port: u16,
}

struct Entry {
    ep: Endpoint,
    expires_at: Instant,
}

pub struct ServiceRegistry {
    ttl: Duration, // TTL değeri faz C
    entries: RwLock<HashMap<String, Entry>>,
}

impl ServiceRegistry {
    pub fn new(ttl: Duration) -> Self {
        Self { ttl, entries: RwLock::new(HashMap::new()) }
    }

    pub fn register(&self, service: impl Into<String>, ep: Endpoint) {
        let mut map = self.entries.write().expect("kilit zehirlenmesi");
        map.insert(service.into(), Entry { ep, expires_at: Instant::now() + self.ttl });
    }

    /// Süre dolmuş kaydı bulursa siler ve None döner (lazy expiry).
    pub fn resolve(&self, service: &str) -> Option<Endpoint> {
        let mut map = self.entries.write().expect("kilit zehirlenmesi");
        let e = map.get(service)?;
        if e.expires_at < Instant::now() {
            map.remove(service);
            return None;
        }
        Some(e.ep.clone())
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | TTL depo (array) | mDNS atımı yok — faz C |
| 2 | JS ES6+ | Web UI | TTL depo + online tazeleme | bridge URL faz C |
| 3 | C++20 | Neva Engine | TTL depo (unordered_map) | mDNS kütüphanesi faz C |
| 4 | C# | Windows kolu | TTL depo (ConcurrentDictionary) | keşif kütüphanesi faz C |
| 5 | Python | ağ testi/araç | TTL depo (dataclass) | python-zeroconf seçilmemiş (§2/6) |
| 6 | Go | sunucu/tünel | TTL depo (mutex) | zeroconf seçilmemiş (§2/5) |
| 7 | Rust | sistem | TTL depo (RwLock) | mDNS kütüphanesi faz C |

**Kapı:** 7/7 dil örneği üretildi; gerçek ağa paket atan keşif (mDNS/DNS-SD sorgusu)
kütüphane seçimine bağlıdır — kapsam split'i + kütüphane faz C'de
(service-discovery-standart-kanit.md §4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
