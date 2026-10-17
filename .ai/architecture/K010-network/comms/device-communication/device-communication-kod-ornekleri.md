---
node_id: K010-network/comms/device-communication/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/comms/device-communication
role: ornek
children: []
depends_on:
  - "K010-network/comms/device-communication/device-communication-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans standartlar: device-communication-standart-kanit.md §1–§3 (RFC 6762/6763/7252/7641/8323/3927)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Yerel protokol seçimi (mDNS+CoAP vs özel UDP vs WS-bridge) → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kod
---

# device-communication — kod-ornekleri (kademe-3 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: yerel ağda cihaz
> keşfi + hafif UDP istek/yanıt iskeleti (RFC 6762 ad keşfi + RFC 7252 tarzı mesaj
> sözleşmesi örneklenir; protokol nihai seçimi faz C).

## §1 PHP 8.4 — UDP cihaz yoklayıcı (istemci · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/comms/device-communication — yerel cihaz yoklama iskeleti (TASARIM ÖRNEĞİ).
 * Sınır: DNS çözümleme yapmaz (mDNS için ayrı keşif katmanı — faz C),
 *        cihaz anlamını yorumlamaz (envanter K012/device-service).
 */
final readonly class DeviceProbe
{
    /** @param array{port: int, timeout: float, retries: int} cfg */
    public function __construct(private array $cfg = ['port' => 5683, 'timeout' => 1.5, 'retries' => 2])
    {
    }

    /**
     * UDP paketi gönderir; yanıt gelmezse false döner (basit yoklama).
     *
     * @return array{0: string, 1: string}|null [yanıt_bayti, gönderen_adres] | null
     */
    public function probe(string $broadcastAddr, string $payload): ?array
    {
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock === false) {
            return null;
        }
        try {
            socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, [
                'sec'  => (int)$this->cfg['timeout'],
                'usec' => (int)(($this->cfg['timeout'] - (int)$this->cfg['timeout']) * 1_000_000),
            ]);
            for ($attempt = 0; $attempt <= $this->cfg['retries']; $attempt++) {
                if ($attempt > 0) {
                    usleep(100_000 * (2 ** ($attempt - 1))); // 100ms → 200ms
                }
                socket_sendto($sock, $payload, strlen($payload), 0, $broadcastAddr, $this->cfg['port']);
                $from = ''; $port = 0;
                $n = @socket_recvfrom($sock, $reply, 2048, 0, $from, $port);
                if ($n !== false && $n > 0) {
                    return [$reply, $from];
                }
            }
            return null;
        } finally {
            socket_close($sock);
        }
    }
}
```

## §2 Vanilla JS (ES6+) — WS-bridge üzerinden cihaz yoklama (tarayıcı)

```js
/**
 * K014/comms/device-communication — tarayıcıda ham UDP/UDP BROADCAST YOKTUR (Sınır:
 * navigator'u gerçek bir ağ soketi yok); tarayıcı kolu, yerel bridge'e WebSocket ile
 * bağlanır ve bridge UDP'yi yürütür — tcp-ip-kod-ornekleri.md §2 ile aynı desen.
 */
export class DeviceProbe {
  /** @param {{url: string, timeoutMs?: number, retries?: number}} cfg */
  constructor(cfg) {
    this.url = cfg.url;                     // ws://localhost:PORT/bridge (faz C)
    this.timeoutMs = cfg.timeoutMs ?? 1500;
    this.retries = cfg.retries ?? 2;
  }

  /**
   * @param {string} target  "255.255.255.255" veya cihaz adresi
   * @param {string} payload tek paket yoklama gövdesi
   * @returns {Promise<{addr: string, body: string}>}
   */
  async probe(target, payload) {
    let lastError = 'yanıt yok';
    for (let attempt = 0; attempt <= this.retries; attempt++) {
      if (attempt > 0) await new Promise(r => setTimeout(r, 100 * 2 ** (attempt - 1)));
      try {
        const res = await this.#rpc(target, payload);
        if (res) return res;
      } catch (e) {
        lastError = e instanceof Error ? e.message : String(e);
      }
    }
    throw new Error(`cihaz yoklaması başarısız: ${lastError}`);
  }

  #rpc(target, payload) {
    return new Promise((resolve, reject) => {
      const ws = new WebSocket(this.url);
      const timer = setTimeout(() => { ws.close(); reject(new Error('timeout')); }, this.timeoutMs);
      ws.onmessage = (ev) => {
        clearTimeout(timer); ws.close();
        resolve({ addr: JSON.parse(ev.data).from, body: JSON.parse(ev.data).body });
      };
      ws.onerror = () => { clearTimeout(timer); reject(new Error('bridge hatası')); };
      ws.onopen = () => ws.send(JSON.stringify({ op: 'udp-send', target, payload }));
    });
  }
}
```

## §3 C++20 — UDP cihaz yoklayıcı (RAII · noexcept yollar)

```cpp
// K014/comms/device-communication — yerel cihaz yoklaması (TASARIM ÖRNEĞİ).
// Sınır: şifreleme üretmez, cihaz anlamını çözümlemez (K012/device-service).
// Not: gerçek üretimde WinSock2 (Windows) / POSIX socket; bu örnek arayüz sözleşmesidir.
#pragma once
#include <cstdint>
#include <optional>
#include <span>
#include <string>
#include <chrono>
#include <thread>

namespace cm::net {

struct ProbeReply {
    std::string from_addr;
    std::string body;
};

class DeviceProbe {
public:
    struct Config {
        std::uint16_t port{5683};               // CoAP varsayılan portu (RFC 7252) — aday
        std::chrono::milliseconds timeout{1500};
        int retries{2};
    };

    explicit DeviceProbe(Config cfg = {}) : cfg_(cfg) {}

    // Geçici başarısızlıkta üstel geri çekilme ile yeniden dener; yanıt yoksa nullopt.
    [[nodiscard]] std::optional<ProbeReply>
    probe(const std::string& target, std::span<const std::byte> payload) const {
        for (int attempt = 0; attempt <= cfg_.retries; ++attempt) {
            if (attempt > 0)
                std::this_thread::sleep_for(std::chrono::milliseconds{100} << (attempt - 1));
            if (auto r = sendOnce(target, payload); r) return r;
        }
        return std::nullopt;
    }

private:
    Config cfg_;
    [[nodiscard]] std::optional<ProbeReply>
    sendOnce(const std::string&, std::span<const std::byte>) const; // faz C: socket implementasyonu
};

} // namespace cm::net
```

## §4 C# — UdpClient ile cihaz yoklama (nullable · ConfigureAwait(false))

```csharp
// K014/comms/device-communication — UDP cihaz yoklaması (TASARIM ÖRNEĞİ).
// Sınır: cihaz anlamı çözümlemez; UDP güvenli kanal değildir (şifreleme K009).
#nullable enable
using System.Net;
using System.Net.Sockets;
using System.Text;

namespace CoreMusic.Network.Device;

public sealed record ProbeConfig(int Port = 5683, double TimeoutSeconds = 1.5, int Retries = 2);

public sealed class DeviceProbe(ProbeConfig? cfg = null)
{
    private readonly ProbeConfig _cfg = cfg ?? new ProbeConfig();

    /// <summary>Yoklama paketi gönderir; yanıt yoksa null döner.</summary>
    public async Task<(string Addr, string Body)?> ProbeAsync(
        string target, string payload, CancellationToken ct = default)
    {
        using var udp = new UdpClient();
        udp.Client.ReceiveTimeout = (int)(_cfg.TimeoutSeconds * 1000);
        var bytes = Encoding.UTF8.GetBytes(payload);
        var ep = new IPEndPoint(IPAddress.Parse(target), _cfg.Port);

        for (var attempt = 0; attempt <= _cfg.Retries; attempt++)
        {
            if (attempt > 0)
                await Task.Delay(TimeSpan.FromMilliseconds(100 * Math.Pow(2, attempt - 1)), ct)
                          .ConfigureAwait(false);
            try
            {
                await udp.SendAsync(bytes, bytes.Length, ep).ConfigureAwait(false);
                using var timeout = CancellationTokenSource.CreateLinkedTokenSource(ct);
                timeout.CancelAfter(TimeSpan.FromSeconds(_cfg.TimeoutSeconds));
                var res = await udp.ReceiveAsync(timeout.Token).ConfigureAwait(false);
                return (res.RemoteEndPoint.ToString(), Encoding.UTF8.GetString(res.Buffer));
            }
            catch (OperationCanceledException) when (!ct.IsCancellationRequested)
            {
                // bu deneme timeout — devam
            }
            catch (SocketException)
            {
                // geçici — devam
            }
        }
        return null;
    }
}
```

## §5 Python 3.12+ — asyncio ile cihaz yoklama (async · DatagramProtocol)

```python
# K014/comms/device-communication — async UDP cihaz yoklaması (TASARIM ÖRNEĞİ).
# Sınır: cihaz anlamı çözümlemez (K012); UDP güvenli kanal değildir (şifreleme K009).
from __future__ import annotations

import asyncio
from dataclasses import dataclass


@dataclass(frozen=True, slots=True)
class ProbeConfig:
    port: int = 5683          # CoAP varsayılanı (RFC 7252) — aday
    timeout: float = 1.5
    retries: int = 2


class _EchoProtocol(asyncio.DatagramProtocol):
    def __init__(self) -> None:
        self.future: asyncio.Future[tuple[str, str]] = asyncio.get_running_loop().create_future()

    def datagram_received(self, data: bytes, addr: tuple[str, int]) -> None:
        if not self.future.done():
            self.future.set_result((addr[0], data.decode("utf-8", "replace")))


async def probe(target: str, payload: str, cfg: ProbeConfig | None = None) -> tuple[str, str] | None:
    cfg = cfg or ProbeConfig()
    loop = asyncio.get_running_loop()
    last: tuple[str, str] | None = None
    for attempt in range(cfg.retries + 1):
        if attempt:
            await asyncio.sleep(0.1 * 2 ** (attempt - 1))  # 100ms → 200ms → …
        transport, proto = await loop.create_datagram_endpoint(
            _EchoProtocol, remote_addr=(target, cfg.port))
        try:
            transport.sendto(payload.encode("utf-8"))
            try:
                async with asyncio.timeout(cfg.timeout):
                    last = await proto.future
                    return last
            except asyncio.TimeoutError:
                continue
        finally:
            transport.close()
    return last
```

## §6 Go — net.PacketConn ile cihaz yoklama

```go
// K014/comms/device-communication — UDP cihaz yoklaması (TASARIM ÖRNEĞİ).
// Sınır: cihaz anlamı çözümlemez; UDP güvenli kanal değildir (şifreleme K009).
package deviceprobe

import (
	"context"
	"errors"
	"fmt"
	"net"
	"time"
)

type Config struct {
	Port    int
	Timeout time.Duration
	Retries int
}

func DefaultConfig() Config { return Config{Port: 5683, Timeout: 1500 * time.Millisecond, Retries: 2} }

// ErrNoReply, tüm denemelerde yanıt gelmediğini gösterir.
var ErrNoReply = errors.New("cihaz yoklamasında yanıt yok")

func Probe(ctx context.Context, target, payload string, cfg Config) (addr string, body string, err error) {
	conn, err := net.Dial("udp", fmt.Sprintf("%s:%d", target, cfg.Port))
	if err != nil {
		return "", "", err // kalıcı (adres/port hatası)
	}
	defer conn.Close()

	for attempt := 0; attempt <= cfg.Retries; attempt++ {
		if attempt > 0 {
			select {
			case <-time.After(time.Duration(100<<uint(attempt-1)) * time.Millisecond):
			case <-ctx.Done():
				return "", "", ctx.Err()
			}
		}
		_ = conn.SetDeadline(time.Now().Add(cfg.Timeout))
		if _, err := conn.Write([]byte(payload)); err != nil {
			continue // geçici (ağ/broadcast hatası)
		}
		buf := make([]byte, 2048)
		n, err := conn.Read(buf)
		if err != nil {
			continue // timeout — geçici
		}
		return conn.RemoteAddr().String(), string(buf[:n]), nil
	}
	return "", "", ErrNoReply
}
```

## §7 Rust — tokio UdpSocket ile cihaz yoklama (async)

```rust
// K014/comms/device-communication — async UDP cihaz yoklaması (TASARIM ÖRNEĞİ).
// Sınır: cihaz anlamı çözümlemez; UDP güvenli kanal değildir (şifreleme K009).
use std::net::SocketAddr;
use std::time::Duration;
use tokio::net::UdpSocket;

#[derive(Clone)]
pub struct ProbeConfig {
    pub timeout: Duration,
    pub retries: u32,
}

impl Default for ProbeConfig {
    fn default() -> Self {
        Self { timeout: Duration::from_millis(1500), retries: 2 }
    }
}

#[derive(Debug, thiserror::Error)]
pub enum ProbeError {
    #[error("cihaz yoklamasında yanıt yok: {0}")]
    NoReply(String),
    #[error("io: {0}")]
    Io(#[from] std::io::Error),
}

pub async fn probe(target: SocketAddr, payload: &[u8], cfg: ProbeConfig) -> Result<(SocketAddr, String), ProbeError> {
    let sock = UdpSocket::bind("0.0.0.0:0").await?; // yerel kök port — RFC 3927 adresi faz C
    let mut last = String::from("yanıt yok");
    for attempt in 0..=cfg.retries {
        if attempt > 0 {
            tokio::time::sleep(Duration::from_millis(100 << (attempt - 1))).await;
        }
        sock.send_to(payload, target).await?;
        let mut buf = [0u8; 2048];
        match tokio::time::timeout(cfg.timeout, sock.recv_from(&mut buf)).await {
            Ok(Ok((n, from))) => {
                return Ok((from, String::from_utf8_lossy(&buf[..n]).into_owned()));
            }
            Ok(Err(e)) => last = e.to_string(), // io geçici sayılır
            Err(_) => last = "timeout".into(),
        }
    }
    Err(ProbeError::NoReply(last))
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | UDP istemci (socket ext) | şifreleme yok, anlam yok |
| 2 | JS ES6+ | Web UI | WS-bridge RPC (tarayıcıda UDP yok) | bridge adresi faz C |
| 3 | C++20 | Neva Engine | UDP istemci arayüzü | WinSock2/POSIX implementasyonu faz C |
| 4 | C# | Windows kolu | UdpClient istemci | yerleşik .NET UDP |
| 5 | Python | ağ testi/araç | async DatagramProtocol | kütüphane yok — stdlib |
| 6 | Go | sunucu/tünel | net.Dial("udp") istemci | stdlib yerleşik |
| 7 | Rust | sistem | tokio UdpSocket istemci | tokio — bağımlılık seçimi faz C |

**Kapı:** 7/7 dil örneği üretildi; hiçbiri "verified üretim kodu" değildir —
protokol seçimi (mDNS+CoAP vs özel UDP vs WS-bridge) faz C'de kararlaştırılır
(standart-kanit.md §4 UNKNOWN satırı).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
