---
node_id: K010-network/comms/tcp-ip/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/comms/tcp-ip
role: ornek
children: []
depends_on:
  - "K010-network/comms/tcp-ip/tcp-ip-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — örnekler TASARIM ÖRNEĞİ'dir"
  - "Referans: RFC 9293 (tcp-ip-standart-kanit.md §1/1) — keepalive mekanizması"
validation:
  - "Örnekler yalnız bilinen API'lerle yazıldı; port/keepalive değerleri UNKNOWN olarak işaretli"
open_questions:
  - "Port aralığı · keepalive parametreleri → ⚠️ UNKNOWN (faz C)"
group: kod
---

# tcp-ip — kod-ornekleri (kademe-3 · module · rol: ornek)

> **UYARI:** K014'te kod YOK. Örnekler **tasarım şablonudur**. Ortak sözleşme: uzun ömürlü
> TCP istemci/sunucu iskeleti — length-prefixed çerçeveleme, keepalive, reconnect.
> Port/keepalive değerleri **UNKNOWN** (standart-kanit §4) — örneklerde yer tutucu kullanılmıştır.

## §1 PHP 8.4 — socket istemci (stream — yerleşik)

```php
<?php
declare(strict_types=1);

/**
 * K014/comms/tcp-ip — length-prefixed TCP istemci (TASARIM ÖRNEĞİ).
 * Port/keepalive: UNKNOWN (⚠️ faz C) — constructor ile enjekte edilir.
 * Sınır: donanım/sürücü K001; şifreleme yok (yalnız TCP).
 */
final class TcpFramedClient
{
    /** @var resource|null */
    private $sock = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,          // ⚠️ UNKNOWN — faz C'de atanacak
        private readonly float $connectTimeoutS = 3.0,
    ) {}

    public function connect(): void
    {
        $sock = @stream_socket_client(
            "tcp://{$this->host}:{$this->port}",
            $errno,
            $errstr,
            $this->connectTimeoutS,
        );
        if ($sock === false) {
            throw new RuntimeException("bağlantı başarısız: $errstr ($errno)"); // geçici sınıf
        }
        stream_set_timeout($sock, 5);
        // keepalive: PHP'de TCP_KEEPALIVE seçeneği platforma bağlı — ⚠️ faz C
        $this->sock = $sock;
    }

    /** Uzunluk-önekli çerçeve: 4 bayt BE uzunluk + gövde. */
    public function send(string $payload): void
    {
        if ($this->sock === null) {
            throw new RuntimeException("bağlı değil"); // geçici
        }
        $frame = pack('N', strlen($payload)) . $payload;
        if (fwrite($this->sock, $frame) !== strlen($frame)) {
            throw new RuntimeException("yazım yarıda kesildi"); // geçici
        }
    }

    public function read(): string
    {
        if ($this->sock === null) {
            throw new RuntimeException("bağlı değil");
        }
        $lenData = $this->readExact(4);
        $len = unpack('N', $lenData)[1];
        if ($len > 16 * 1024 * 1024) {
            throw new RuntimeException("çerçeve aşımı — kalıcı hata"); // kalıcı
        }
        return $this->readExact($len);
    }

    private function readExact(int $n): string
    {
        $buf = '';
        while (strlen($buf) < $n) {
            $chunk = fread($this->sock, $n - strlen($buf));
            if ($chunk === false || $chunk === '') {
                throw new RuntimeException("kopma"); // geçici — reconnect
            }
            $buf .= $chunk;
        }
        return $buf;
    }

    public function close(): void
    {
        if ($this->sock !== null) {
            fclose($this->sock);
            $this->sock = null;
        }
    }
}
```

## §2 Vanilla JS (ES6+) — tarayıcıda TCP yok; WS köprüsü alternatifi

```js
/**
 * K014/comms/tcp-ip — tarayıcı katmanı NOTU (TASARIM ÖRNEĞİ).
 * Tarayıcıda ham TCP socket API'si YOKTUR (WHATWG'ta karşılığı bulunmuyor).
 * Web UI → yerel ağ bağlantısı: ya WebSocket (websocket torunu) ya native köprü exe.
 * Bu örnek, köprü exe'nin WS arayüzüne düşen TCP istemci sarmalayıcısıdır.
 */
import { ReconnectingSocket } from '../websocket/websocket-kod-ornekleri.md'; // ⚠️ referans

export class TcpBridgeClient extends ReconnectingSocket {
  /** @param {string} bridgeUrl ör. ws://127.0.0.1:9742 */
  constructor(bridgeUrl) {
    super(bridgeUrl, {
      onMessage: (data) => this._onFrame(data),
    });
    /** @type {Map<number, {resolve: Function, reject: Function}>} */
    this.pending = new Map();
    this.nextId = 1;
  }

  /** Uzunluk-önekli çerçeveyi köprüye JSON komutu olarak yaz (çerçeveleme köprüde). */
  request(cmd, params) {
    const id = this.nextId++;
    return new Promise((resolve, reject) => {
      this.pending.set(id, { resolve, reject });
      this.send(JSON.stringify({ cmd, params, id }));
      setTimeout(() => {
        if (this.pending.delete(id)) reject(new Error(`zaman aşımı: ${cmd}`)); // geçici
      }, 10000);
    });
  }

  _onFrame(data) {
    const msg = JSON.parse(data);
    const waiter = this.pending.get(msg.id);
    if (!waiter) return; // beklenmeyen push — üst katman (K012) dinler
    this.pending.delete(msg.id);
    if (msg.error) waiter.reject(new Error(msg.error));
    else waiter.resolve(msg.result);
  }
}
```

## §3 C++20 — TCP istemçi (asio adayı — RAII, Neva Engine hizası)

```cpp
// K014/comms/tcp-ip — asio tabanlı length-prefixed istemci iskeleti (TASARIM ÖRNEĞİ).
// Kütüphane: asio (header-only) — aday, seçimi faz C.
#pragma once
#include <cstdint>
#include <span>
#include <string>
#include <vector>
#include <expected>

namespace cm::net {

enum class TcpError { connect_failed, closed, frame_oversize };

class TcpClient {
public:
    struct Config {
        std::string host;
        std::uint16_t port{};              // ⚠️ UNKNOWN — faz C
        std::chrono::milliseconds connect_timeout{3000};
        std::size_t max_frame{16u * 1024 * 1024};
    };

    explicit TcpClient(Config cfg) : cfg_(std::move(cfg)) {}

    // RAII: dtor socket'i kapatır; kopyalama kapalı, taşıma serbest.
    TcpClient(const TcpClient&) = delete;
    TcpClient& operator=(const TcpClient&) = delete;
    TcpClient(TcpClient&&) noexcept = default;
    ~TcpClient() = default;

    [[nodiscard]] std::expected<void, TcpError> connect();
    [[nodiscard]] std::expected<void, TcpError> send(std::span<const std::uint8_t> payload);
    [[nodiscard]] std::expected<std::vector<std::uint8_t>, TcpError> read_frame();
    void close() noexcept;

private:
    Config cfg_;
    // faz C: asio::ip::tcp::socket + async okuma zinciri
};

} // namespace cm::net
```

## §4 C# — TcpClient sarmalayıcı (System.Net.Sockets — yerleşik)

```csharp
// K014/comms/tcp-ip — length-prefixed TCP istemci (TASARIM ÖRNEĞİ).
// Yerleşik System.Net.Sockets.TcpClient — ek bağımlılık yok.
#nullable enable
using System.Net.Sockets;

namespace CoreMusic.Network.Tcp;

public sealed class FramedTcpClient : IDisposable
{
    private readonly TcpClient _client = new();
    private readonly NetworkStream _stream;
    private readonly int _maxFrame;

    public FramedTcpClient(string host, int port, int maxFrame = 16 * 1024 * 1024)
    {
        _maxFrame = maxFrame;
        _client.Connect(host, port); // port ⚠️ UNKNOWN — faz C'de atanacak
        _client.NoDelay = true;
        _stream = _client.GetStream();
    }

    /// <summary>4 bayt BE uzunluk + gövde çerçevesi gönderir.</summary>
    public async Task SendAsync(ReadOnlyMemory<byte> payload, CancellationToken ct = default)
    {
        var header = BitConverter.GetBytes(System.Net.IPAddress.HostToNetworkOrder(payload.Length));
        await _stream.WriteAsync(header, ct).ConfigureAwait(false);
        await _stream.WriteAsync(payload, ct).ConfigureAwait(false);
    }

    /// <summary>Bir çerçeve okur; kopmada SocketException (geçici sınıf).</summary>
    public async Task<byte[]> ReadFrameAsync(CancellationToken ct = default)
    {
        var header = await ReadExactAsync(4, ct).ConfigureAwait(false);
        var len = BitConverter.ToInt32(
            new[] { header[3], header[2], header[1], header[0] }); // BE → LE
        if (len < 0 || len > _maxFrame)
            throw new InvalidDataException("çerçeve aşımı — kalıcı hata");
        return await ReadExactAsync(len, ct).ConfigureAwait(false);
    }

    private async Task<byte[]> ReadExactAsync(int n, CancellationToken ct)
    {
        var buf = new byte[n];
        var read = 0;
        while (read < n)
        {
            var k = await _stream.ReadAsync(buf.AsMemory(read), ct).ConfigureAwait(false);
            if (k == 0) throw new IOException("kopma"); // geçici — reconnect
            read += k;
        }
        return buf;
    }

    public void Dispose()
    {
        _stream.Dispose();
        _client.Dispose();
    }
}
```

## §5 Python 3.12+ — asyncio TCP istemci (yerleşik asyncio — bağımlılık yok)

```python
# K014/comms/tcp-ip — asyncio length-prefixed istemci (TASARIM ÖRNEĞİ).
# Yerleşik asyncio streams — ek bağımlılık yok.
from __future__ import annotations

import asyncio
import struct

MAX_FRAME = 16 * 1024 * 1024


class TransientTcpError(Exception):
    """Geçici hata — reconnect (kopma, timeout)."""


class PermanentTcpError(Exception):
    """Kalıcı hata — retry yok (çerçeve aşımı)."""


class FramedTcpClient:
    def __init__(self, host: str, port: int, *, connect_timeout: float = 3.0) -> None:
        self._host = host
        self._port = port  # ⚠️ UNKNOWN — faz C
        self._connect_timeout = connect_timeout
        self._reader: asyncio.StreamReader | None = None
        self._writer: asyncio.StreamWriter | None = None

    async def connect(self) -> None:
        try:
            self._reader, self._writer = await asyncio.wait_for(
                asyncio.open_connection(self._host, self._port),
                self._connect_timeout,
            )
        except (TimeoutError, ConnectionError, OSError) as exc:
            raise TransientTcpError(str(exc)) from exc

    async def send(self, payload: bytes) -> None:
        if self._writer is None:
            raise TransientTcpError("bağlı değil")
        self._writer.write(struct.pack(">I", len(payload)) + payload)
        await self._writer.drain()

    async def read_frame(self) -> bytes:
        if self._reader is None:
            raise TransientTcpError("bağlı değil")
        try:
            header = await self._reader.readexactly(4)
            (length,) = struct.unpack(">I", header)
            if length > MAX_FRAME:
                raise PermanentTcpError("çerçeve aşımı")
            return await self._reader.readexactly(length)
        except (asyncio.IncompleteReadError, ConnectionError, TimeoutError) as exc:
            raise TransientTcpError(str(exc)) from exc

    async def close(self) -> None:
        if self._writer is not None:
            self._writer.close()
            await self._writer.wait_closed()
            self._reader = self._writer = None
```

## §6 Go — net.Dial TCP istemci (yerleşik net — bağımlılık yok)

```go
// K014/comms/tcp-ip — length-prefixed TCP istemci (TASARIM ÖRNEĞİ).
// Yerleşik net — ek bağımlılık yok.
package nettcp

import (
	"context"
	"encoding/binary"
	"errors"
	"fmt"
	"io"
	"net"
	"time"
)

var (
	ErrFrameOversize = errors.New("çerçeve aşımı — kalıcı") // kalıcı
	ErrClosed        = errors.New("kopma — geçici")         // geçici
)

const MaxFrame = 16 * 1024 * 1024

type Client struct {
	Addr    string        // "192.168.1.10:9742" — port ⚠️ UNKNOWN (faz C)
	Timeout time.Duration
	conn    net.Conn
}

func (c *Client) Connect(ctx context.Context) error {
	d := net.Dialer{Timeout: 3 * time.Second}
	if c.Timeout != 0 {
		d.Timeout = c.Timeout
	}
	conn, err := d.DialContext(ctx, "tcp", c.Addr)
	if err != nil {
		return fmt.Errorf("geçici: %w", err)
	}
	c.conn = conn
	return nil
}

func (c *Client) Send(payload []byte) error {
	if c.conn == nil {
		return ErrClosed
	}
	var header [4]byte
	binary.BigEndian.PutUint32(header[:], uint32(len(payload)))
	if _, err := c.conn.Write(header[:]); err != nil {
		return fmt.Errorf("geçici: %w", err)
	}
	if _, err := c.conn.Write(payload); err != nil {
		return fmt.Errorf("geçici: %w", err)
	}
	return nil
}

func (c *Client) ReadFrame() ([]byte, error) {
	if c.conn == nil {
		return nil, ErrClosed
	}
	var header [4]byte
	if _, err := io.ReadFull(c.conn, header[:]); err != nil {
		return nil, fmt.Errorf("geçici: %w", err)
	}
	n := binary.BigEndian.Uint32(header[:])
	if n > MaxFrame {
		return nil, ErrFrameOversize // kalıcı
	}
	buf := make([]byte, n)
	if _, err := io.ReadFull(c.conn, buf); err != nil {
		return nil, fmt.Errorf("geçici: %w", err)
	}
	return buf, nil
}

func (c *Client) Close() error {
	if c.conn == nil {
		return nil
	}
	err := c.conn.Close()
	c.conn = nil
	return err
}
```

## §7 Rust — tokio TCP istemci (length-prefixed)

```rust
// K014/comms/tcp-ip — tokio length-prefixed istemci (TASARIM ÖRNEĞİ).
// Bağımlılık: tokio (tcp+io) — doğrulanmış repo (kullanan websocket örneği).
use tokio::io::{AsyncReadExt, AsyncWriteExt};
use tokio::net::TcpStream;

pub const MAX_FRAME: u32 = 16 * 1024 * 1024;

#[derive(Debug, thiserror::Error)]
pub enum TcpError {
    #[error("geçici: {0}")]
    Transient(String),
    #[error("kalıcı: çerçeve aşımı")]
    FrameOversize,
}

pub struct FramedClient {
    addr: String, // ⚠️ port UNKNOWN — faz C
    stream: Option<TcpStream>,
}

impl FramedClient {
    pub fn new(addr: impl Into<String>) -> Self {
        Self { addr: addr.into(), stream: None }
    }

    pub async fn connect(&mut self) -> Result<(), TcpError> {
        let s = tokio::time::timeout(
            std::time::Duration::from_secs(3),
            TcpStream::connect(&self.addr),
        )
        .await
        .map_err(|_| TcpError::Transient("bağlantı zaman aşımı".into()))?
        .map_err(|e| TcpError::Transient(e.to_string()))?;
        self.stream = Some(s);
        Ok(())
    }

    pub async fn send(&mut self, payload: &[u8]) -> Result<(), TcpError> {
        let s = self.stream.as_mut().ok_or_else(|| TcpError::Transient("bağlı değil".into()))?;
        s.write_all(&(payload.len() as u32).to_be_bytes())
            .await
            .map_err(|e| TcpError::Transient(e.to_string()))?;
        s.write_all(payload)
            .await
            .map_err(|e| TcpError::Transient(e.to_string()))?;
        Ok(())
    }

    pub async fn read_frame(&mut self) -> Result<Vec<u8>, TcpError> {
        let s = self.stream.as_mut().ok_or_else(|| TcpError::Transient("bağlı değil".into()))?;
        let mut header = [0u8; 4];
        s.read_exact(&mut header)
            .await
            .map_err(|e| TcpError::Transient(e.to_string()))?;
        let len = u32::from_be_bytes(header);
        if len > MAX_FRAME {
            return Err(TcpError::FrameOversize); // kalıcı
        }
        let mut buf = vec![0u8; len as usize];
        s.read_exact(&mut buf)
            .await
            .map_err(|e| TcpError::Transient(e.to_string()))?;
        Ok(buf)
    }

    pub async fn close(&mut self) {
        if let Some(s) = self.stream.take() {
            let _ = s.shutdown().await;
        }
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Örnek tipi | Bağımlılık | Çerçeveleme |
|---|---|---|---|---|
| 1 | PHP 8.4 | stream istemci | yerleşik (streams) | 4B BE uzunluk |
| 2 | JS ES6+ | WS köprü sarmalayıcı | ham TCP tarayıcıda YOK | köprü JSON |
| 3 | C++20 | asio istemci arayüzü | asio adayı | 4B BE uzunluk |
| 4 | C# | TcpClient istemci | yerleşik Sockets | 4B BE uzunluk |
| 5 | Python | asyncio istemci | yerleşik asyncio | 4B BE uzunluk |
| 6 | Go | net.Dial istemci | yerleşik net | 4B BE uzunluk |
| 7 | Rust | tokio istemci | tokio (doğrulanmış) | 4B BE uzunluk |

**Kapı:** 7/7 dil — aynı çerçeveleme sözleşmesi; JS'in "TCP yok" gerçeği köprü deseniyle
çözüldü; port/keepalive değerleri UNKNOWN (faz C).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
