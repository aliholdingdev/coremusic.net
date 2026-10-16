---
node_id: K014-network/comms/kod-ornekleri
node_type: module
status: draft
parent_id: K014-network/comms
role: ornek
children: []
depends_on: []
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Aile çapraz okuma: 5 torunun kendi kod dosyaları comms/<torun>/<torun>-kod-ornekleri.md (100% kapsama)"
validation:
  - "Her dil §1–§7'de bir kez geçer + §8 dil matrisi tam (7/7) — 2026-10-15"
  - "Kütüphane seçimi sabitlenmez: her blokta seçim ifadesi ⚠️ VERIFICATION REQUIRED (faz C)"
open_questions:
  - "Gerçek kütüphane/dil seçimi (http/ws/tcp için) → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Torunların üretim koduna geçişi → ADR-097 K4 fazı (C)"
group: kod
---

# comms — kod-ornekleri (kademe-2 · module · rol: ornek)

**ELI15:** Aile düzeyinde tek ortak tasarım sorusu: **5 protokol (http · websocket ·
webrtc · tcp-ip · device-communication) nasıl tek bir `Transport` arayüzünün arkasına
sığar?** Her dilde bu seam (dikiş) gösterilir. Protokol-bazlı ayrıntılı örnekler
**torun dosyalarındadır** — bu dosya tekrar üretmez.

**⚠️ TASARIM ÖRNEĞİ:** K014-network'de kod **YOKTUR** (status: PLANNED —
`network-kapsam.md` §6/§29). Aşağıdaki bloklar üretim kodu değildir.

**Sınır (ne DEĞİL):** Protokol iç mantığı (torun kod dosyaları) · şifreleme (K009) ·
donanım (K001) · iş anlamı (K012/K013).

## §1 PHP 8.4 — Transport seam (aile)

```php
<?php
declare(strict_types=1);

/**
 * K014/comms — 5 protokolü tek arayüz arkasına sığdırma (TASARIM ÖRNEĞİ).
 * Seçim: hangi somut sınıfın kullanılacağı faz C'de belirlenir.
 */
interface Transport
{
    public function connect(): void;
    public function send(string $payload): void;
    public function close(): void;
}

// Aday somutlar (isim = tasarım etiketi, seçim ⚠️ VERIFICATION REQUIRED):
// HttpTransport · WebSocketTransport · WebRtcTransport · TcpTransport · LocalDeviceTransport
final class TransportRegistry
{
    /** @var array<string, Transport> */
    private array $transports = [];

    public function register(string $kind, Transport $t): void
    {
        $this->transports[$kind] = $t;
    }

    public function get(string $kind): Transport
    {
        if (!isset($this->transports[$kind])) {
            throw new \RuntimeException("transport yok: {$kind}"); // fail-safe
        }
        return $this->transports[$kind];
    }
}
```

## §2 Vanilla JS (ES6+) — Transport seam (aile)

```js
// K014/comms — aile seam (TASARIM ÖRNEĞİ). Seçim ⚠️ faz C.
export class TransportRegistry {
  #transports = new Map();

  register(kind, transport) {
    if (typeof transport?.send !== 'function') {
      throw new TypeError(`geçersiz transport: ${kind}`); // fail-safe
    }
    this.#transports.set(kind, transport);
  }

  get(kind) {
    const t = this.#transports.get(kind);
    if (!t) throw new Error(`transport yok: ${kind}`);
    return t;
  }
}

// Örnek kullanım (tarayıcı tarafı seam'i — http/ws/webrtc adayları):
// registry.register('ws', createWebSocketTransport(url));
```

## §3 C++20 — Transport seam (aile · RAII · noexcept)

```cpp
// K014/comms — aile seam (TASARIM ÖRNEĞİ). RT-ses yolu değil; kuyruklu taşıma.
#include <memory>
#include <string>
#include <string_view>
#include <unordered_map>
#include <stdexcept>

namespace k014::comms {

class Transport {
public:
    virtual ~Transport() = default;
    virtual void connect() = 0;
    virtual void send(std::string_view payload) = 0;
    virtual void close() noexcept = 0;
};

class TransportRegistry {
    std::unordered_map<std::string, std::unique_ptr<Transport>> transports_;
public:
    void register_kind(std::string kind, std::unique_ptr<Transport> t) {
        transports_.insert_or_assign(std::move(kind), std::move(t));
    }
    Transport& get(const std::string& kind) {
        auto it = transports_.find(kind);
        if (it == transports_.end()) throw std::runtime_error("transport yok: " + kind);
        return *it->second;
    }
};

} // namespace k014::comms
```

## §4 C# — Transport seam (aile · nullable · ConfigureAwait(false))

```csharp
#nullable enable
// K014/comms — aile seam (TASARIM ÖRNEĞİ). Seçim ⚠️ faz C.
namespace K014.Comms;

public interface ITransport
{
    Task ConnectAsync(CancellationToken ct = default);
    Task SendAsync(ReadOnlyMemory<byte> payload, CancellationToken ct = default);
    ValueTask DisposeAsync();
}

public sealed class TransportRegistry
{
    private readonly Dictionary<string, ITransport> _transports = new(StringComparer.Ordinal);

    public void Register(string kind, ITransport transport)
        => _transports[kind] = transport ?? throw new ArgumentNullException(nameof(transport));

    public ITransport Get(string kind)
        => _transports.TryGetValue(kind, out var t)
            ? t
            : throw new InvalidOperationException($"transport yok: {kind}");
}
```

## §5 Python 3.12+ — Transport seam (aile · async · Protocol)

```python
# K014/comms — aile seam (TASARIM ÖRNEĞİ). Kütüphane seçimi ⚠️ faz C.
from __future__ import annotations

from typing import Protocol, runtime_checkable


@runtime_checkable
class Transport(Protocol):
    async def connect(self) -> None: ...
    async def send(self, payload: bytes) -> None: ...
    async def close(self) -> None: ...


class TransportRegistry:
    def __init__(self) -> None:
        self._transports: dict[str, Transport] = {}

    def register(self, kind: str, transport: Transport) -> None:
        self._transports[kind] = transport

    def get(self, kind: str) -> Transport:
        try:
            return self._transports[kind]
        except KeyError as exc:  # fail-safe: bilinmeyen taşıma reddedilir
            raise LookupError(f"transport yok: {kind}") from exc
```

## §6 Go — Transport seam (aile · interface)

```go
// K014/comms — aile seam (TASARIM ÖRNEĞİ). Seçim ⚠️ faz C.
package comms

import (
	"context"
	"fmt"
	"sync"
)

type Transport interface {
	Connect(ctx context.Context) error
	Send(ctx context.Context, payload []byte) error
	Close() error
}

type Registry struct {
	mu sync.RWMutex
	by map[string]Transport
}

func NewRegistry() *Registry {
	return &Registry{by: make(map[string]Transport)}
}

func (r *Registry) Register(kind string, t Transport) {
	r.mu.Lock()
	defer r.mu.Unlock()
	r.by[kind] = t
}

func (r *Registry) Get(kind string) (Transport, error) {
	r.mu.RLock()
	defer r.mu.RUnlock()
	t, ok := r.by[kind]
	if !ok {
		return nil, fmt.Errorf("transport yok: %s", kind) // fail-safe
	}
	return t, nil
}
```

## §7 Rust — Transport seam (aile · async trait · taşınan handle)

```rust
// K014/comms — aile seam (TASARIM ÖRNEĞİ). async_trait kütüphane seçimi ⚠️ faz C.
use std::collections::HashMap;
use std::fmt;

#[derive(Debug)]
pub struct TransportMissing(pub String);

impl fmt::Display for TransportMissing {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        write!(f, "transport yok: {}", self.0)
    }
}
impl std::error::Error for TransportMissing {}

#[allow(async_fn_in_trait)] // tasarım örneği: somut seçim faz C'de
pub trait Transport {
    async fn connect(&mut self) -> Result<(), Box<dyn std::error::Error>>;
    async fn send(&mut self, payload: &[u8]) -> Result<(), Box<dyn std::error::Error>>;
    async fn close(&mut self) -> Result<(), Box<dyn std::error::Error>>;
}

pub struct Registry {
    by: HashMap<String, Box<dyn Transport + Send>>,
}

impl Registry {
    pub fn new() -> Self {
        Self { by: HashMap::new() }
    }
    pub fn register(&mut self, kind: impl Into<String>, t: Box<dyn Transport + Send>) {
        self.by.insert(kind.into(), t);
    }
    pub fn get_mut(&mut self, kind: &str) -> Result<&mut (dyn Transport + Send), TransportMissing> {
        self.by
            .get_mut(kind)
            .map(|b| b.as_mut())
            .ok_or_else(|| TransportMissing(kind.to_string()))
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | § | Seam tipi | Kütüphane seçimi |
|---|---|---|---|---|
| 1 | PHP 8.4 | §1 | interface + registry | ⚠️ faz C (runtime-bridge vs socket eklentisi) |
| 2 | Vanilla JS ES6+ | §2 | class + Map | ⚠️ faz C (fetch/WS/RTC zaten tarayıcıda — seçim API düzeyinde) |
| 3 | C++20 | §3 | sanal sınıf + unique_ptr | ⚠️ faz C (Boost.Beast vs asio) |
| 4 | C# | §4 | interface + async | ⚠️ faz C (SocketsHttpHandler vs ek paket) |
| 5 | Python 3.12+ | §5 | typing.Protocol + asyncio | ⚠️ faz C (httpx vs aiohttp) |
| 6 | Go | §6 | interface + RWMutex | ⚠️ faz C (stdlib yeterli mi?) |
| 7 | Rust | §7 | async trait + Box | ⚠️ faz C (tokio + hangi HTTP/WS crate) |

**Tekrar eden omurga:** her dilde `TransportRegistry` — bilinmeyen taşıma
**fail-safe** ile reddedilir; protokol-bazlı detay (retry, backoff, WS bridge,
4-byte uzunluk öneki vb.) torun kod dosyalarındadır.

**Kapı:** aile seam'i 7/7 dilde gösterildi ✅; somut kütüphane ve torun üretimi
**faz C** — bu dosya üretim kodu kanıtı sayılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
