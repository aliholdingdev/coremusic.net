---
node_id: K010-network/comms/websocket/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/comms/websocket
role: ornek
children: []
depends_on:
  - "K010-network/comms/websocket/websocket-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — örnekler TASARIM ÖRNEĞİ'dir"
  - "Referans: RFC 6455 (websocket-standart-kanit.md §1/1) — ping/pong, reconnect uygulama katmanı"
validation:
  - "Örnekler yalnız bilinen API'lerle yazıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Sunucu kütüphanesi seçimi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kod
---

# websocket — kod-ornekleri (kademe-3 · module · rol: ornek)

> **UYARI:** K014'te kod YOK (kapsam §6). Örnekler **tasarım şablonudur**. Her örnek
> aynı sözleşmeyi uygular: üstel geriçekmeli otomatik yeniden bağlanma + uygulama
> ping/pong keepalive + durum olayları (neva-connect WS 9742 kültürüyle uyumlu).

## §1 PHP 8.4 — WS istemci sarmalayıcı (Ratchet/Reactor bağımlı — iskelet)

```php
<?php
declare(strict_types=1);

/**
 * K014/comms/websocket — reconnect'li WS istemci iskeleti (TASARIM ÖRNEĞİ).
 * Kütüphane seçimi faz C; burada yalnız sözleşme: connect → onOpen/onMessage/onClose.
 * Sınır: mesaj içeriği K012/K015; şifreleme yok (wss yerleşik).
 */
interface WsEvents
{
    public function onOpen(): void;
    public function onMessage(string $payload, bool $binary): void;
    public function onClose(int $code, string $reason): void;
}

final class ReconnectingWsClient
{
    private int $attempt = 0;

    public function __construct(
        private readonly string $url,
        private readonly WsEvents $events,
        private readonly int $maxBackoffMs = 8000,
        private readonly int $pingIntervalS = 30,
    ) {}

    /** Bağlan; kopunca üstel geri çekilmeyi uygula (RFC 6455 reconnect tanımaz — bizim katman). */
    public function run(): void
    {
        while (true) {
            try {
                $conn = $this->connect();           // faz C: seçilen kütüphane
                $this->attempt = 0;
                $this->events->onOpen();
                $this->pump($conn);                 // okuma döngüsü + uygulama ping'i
            } catch (Throwable $e) {
                // geçici: bekle ve tekrar dene — kalıcı close kodu 1000/1001 ise çık
            }
            $backoff = min($this->maxBackoffMs, 100 * (2 ** $this->attempt++));
            usleep($backoff * 1000);
        }
    }

    private function pump(object $conn): void
    {
        // faz C: kütüphane okuma döngüsü; her pingIntervalS'de bir uygulama ping'i gönder
    }

    private function connect(): object
    {
        // faz C: wss:// bağlantısı (TLS yerleşik — K009 politika alanı)
        throw new RuntimeException('bağlantı katmanı faz C');
    }
}
```

## §2 Vanilla JS (ES6+) — reconnect'li WebSocket sınıfı

```js
/**
 * K014/comms/websocket — reconnect'li WS istemci (TASARIM ÖRNEĞİ).
 * Uygulama ping'i tarayıcıda gönderilir (otomatik pong yerleşiktir).
 */
export class ReconnectingSocket {
  /**
   * @param {string} url                          wss:// hedef
   * @param {{maxBackoffMs?: number, pingMs?: number,
   *   onOpen?: () => void, onMessage: (data: string|ArrayBuffer) => void,
   *   onClose?: (code: number) => void}} handlers
   */
  constructor(url, handlers) {
    this.url = url;
    this.h = handlers;
    this.maxBackoffMs = handlers.maxBackoffMs ?? 8000;
    this.pingMs = handlers.pingMs ?? 30000;
    this.attempt = 0;
    this.ws = null;
    this.pingTimer = 0;
    this.closedByUser = false;
  }

  connect() {
    this.closedByUser = false;
    this._open();
  }

  _open() {
    this.ws = new WebSocket(this.url);
    this.ws.addEventListener('open', () => {
      this.attempt = 0;
      this.h.onOpen?.();
      // uygulama katmanı keepalive — RFC 6455 ping/pong'u tarayıcıdan tetiklenmez
      this.pingTimer = setInterval(() => {
        if (this.ws?.readyState === WebSocket.OPEN) this.ws.send(JSON.stringify({ cmd: 'ping' }));
      }, this.pingMs);
    });
    this.ws.addEventListener('message', (ev) => this.h.onMessage(ev.data));
    this.ws.addEventListener('close', (ev) => {
      clearInterval(this.pingTimer);
      this.h.onClose?.(ev.code);
      if (!this.closedByUser) this._retry();
    });
  }

  _retry() {
    // 1000/1001 temiz kapanışta bile dene; üstel geri çekilme + tavan
    const backoff = Math.min(this.maxBackoffMs, 100 * 2 ** this.attempt++);
    setTimeout(() => this._open(), backoff);
  }

  send(data) {
    if (this.ws?.readyState === WebSocket.OPEN) this.ws.send(data);
    // kapalıyken bırak — üst katman kuyruklar (offline-sync sınırı)
  }

  close() {
    this.closedByUser = true;
    clearInterval(this.pingTimer);
    this.ws?.close(1001, 'istemci kapanıyor');
  }
}
```

## §3 C++20 — WS istemci arayüzü (Neva Engine köprüsü — boost/libwebsockets sarmalayıcı)

```cpp
// K014/comms/websocket — WS istemci arayüzü (TASARIM ÖRNEĞİ).
// Gerçek implementasyon faz C (libwebsockets/başlık seçimi); burada yalnız sözleşme.
#pragma once
#include <functional>
#include <string>
#include <chrono>

namespace cm::net {

enum class WsState { disconnected, connecting, connected, reconnecting };

class WsClient {
public:
    struct Handlers {
        std::function<void()> on_open;
        std::function<void(std::string_view, bool binary)> on_message;
        std::function<void(int code, std::string_view reason)> on_close;
        std::function<void(WsState)> on_state;
    };

    struct Config {
        std::string url;                                  // ws://127.0.0.1:9742
        std::chrono::milliseconds max_backoff{8000};
        std::chrono::seconds ping_interval{30};
    };

    WsClient(Config cfg, Handlers h) : cfg_(std::move(cfg)), h_(std::move(h)) {}

    void start();   // bağlantı döngüsü (arka thread)
    void stop();    // temiz kapanış (1001)
    bool send(std::string_view payload, bool binary = false);

private:
    Config cfg_;
    Handlers h_;
    // faz C: kütüphane handle'ı + SPSC kuyruk (neva-connect L0 kültürü)
};

} // namespace cm::net
```

## §4 C# — ClientWebSocket sarmalayıcı (yerleşik System.Net.WebSockets)

```csharp
// K014/comms/websocket — reconnect'li ClientWebSocket (TASARIM ÖRNEĞİ).
// Yerleşik System.Net.WebSockets — ek bağımlılık yok.
#nullable enable
using System.Net.WebSockets;
using System.Text;

namespace CoreMusic.Network.Ws;

public sealed class ReconnectingSocket(
    Uri url,
    TimeSpan? maxBackoff = null,
    TimeSpan? pingInterval = null) : IAsyncDisposable
{
    private readonly TimeSpan _maxBackoff = maxBackoff ?? TimeSpan.FromSeconds(8);
    private readonly TimeSpan _ping = pingInterval ?? TimeSpan.FromSeconds(30);
    private ClientWebSocket _ws = new();
    private int _attempt;

    public event Func<string, Task>? MessageReceived;
    public event Action? Connected;
    public event Action<string>? Disconnected;

    public async Task RunAsync(CancellationToken ct)
    {
        while (!ct.IsCancellationRequested)
        {
            try
            {
                await _ws.ConnectAsync(url, ct).ConfigureAwait(false);
                _attempt = 0;
                Connected?.Invoke();
                await Task.WhenAll(PumpAsync(ct), PingAsync(ct)).ConfigureAwait(false);
            }
            catch (OperationCanceledException) when (ct.IsCancellationRequested) { }
            catch (Exception ex)
            {
                Disconnected?.Invoke(ex.Message);
            }
            var backoff = TimeSpan.FromMilliseconds(
                Math.Min(_maxBackoff.TotalMilliseconds, 100 * Math.Pow(2, _attempt++)));
            try { await Task.Delay(backoff, ct).ConfigureAwait(false); }
            catch (OperationCanceledException) { break; }
            _ws.Dispose();
            _ws = new ClientWebSocket(); // yeniden kullanım kapalı — temiz state
        }
    }

    private async Task PingAsync(CancellationToken ct)
    {
        while (!ct.IsCancellationRequested)
        {
            await Task.Delay(_ping, ct).ConfigureAwait(false);
            if (_ws.State == WebSocketState.Open)
            {
                var bytes = Encoding.UTF8.GetBytes("{\"cmd\":\"ping\"}");
                await _ws.SendAsync(bytes, WebSocketMessageType.Text, true, ct)
                          .ConfigureAwait(false);
            }
        }
    }

    private async Task PumpAsync(CancellationToken ct)
    {
        var buf = new byte[8 * 1024];
        while (_ws.State == WebSocketState.Open)
        {
            using var ms = new MemoryStream();
            WebSocketReceiveResult res;
            do
            {
                res = await _ws.ReceiveAsync(buf, ct).ConfigureAwait(false);
                ms.Write(buf, 0, res.Count);
            } while (!res.EndOfMessage);
            if (res.MessageType == WebSocketMessageType.Close) break;
            if (MessageReceived is not null)
                await MessageReceived(Encoding.UTF8.GetString(ms.ToArray())).ConfigureAwait(false);
        }
    }

    public async ValueTask DisposeAsync()
    {
        if (_ws.State == WebSocketState.Open)
            await _ws.CloseAsync(WebSocketCloseStatus.NormalClosure, "kapanıyor", CancellationToken.None)
                      .ConfigureAwait(false);
        _ws.Dispose();
    }
}
```

## §5 Python 3.12+ — reconnect'li WS istemci (asyncio + websockets kütüphanesi adayı)

```python
# K014/comms/websocket — reconnect'li istemci (TASARIM ÖRNEĞİ).
# Kütüphane: websockets/aiows — seçimi faz C; burada sözleşme.
from __future__ import annotations

import asyncio
import json
import logging
from collections.abc import Awaitable, Callable

log = logging.getLogger(__name__)


class ReconnectingSocket:
    def __init__(
        self,
        url: str,
        on_message: Callable[[str], Awaitable[None]],
        *,
        max_backoff: float = 8.0,
        ping_interval: float = 30.0,
    ) -> None:
        self._url = url
        self._on_message = on_message
        self._max_backoff = max_backoff
        self._ping = ping_interval
        self._attempt = 0

    async def run(self) -> None:
        """Kopunca üstel geri çekilmeyi uygula; temiz kapanışta da yeniden dene."""
        while True:
            try:
                async with self._connect() as ws:          # faz C: kütüphane
                    self._attempt = 0
                    await asyncio.gather(self._pump(ws), self._keepalive(ws))
            except asyncio.CancelledError:
                raise
            except Exception as exc:                        # geçici hata
                log.warning("ws koptu: %s", exc)
            backoff = min(self._max_backoff, 0.1 * 2 ** self._attempt)
            self._attempt += 1
            await asyncio.sleep(backoff)

    async def _keepalive(self, ws) -> None:
        while True:
            await asyncio.sleep(self._ping)
            await ws.send(json.dumps({"cmd": "ping"}))      # uygulama ping'i

    async def _pump(self, ws) -> None:
        async for raw in ws:
            await self._on_message(raw)

    def _connect(self):
        raise NotImplementedError("kütüphane seçimi faz C")
```

## §6 Go — reconnect'li WS istemci (gorilla/websocket ekosistem adayı)

```go
// K014/comms/websocket — reconnect'li istemci (TASARIM ÖRNEĞİ).
// Kütüphane: gorilla/websocket ya da nhooyr.io/websocket — seçimi faz C.
package netws

import (
	"context"
	"encoding/json"
	"log/slog"
	"net/http"
	"time"

	"github.com/gorilla/websocket" // ekosistem adayı — doğrulanmış repo
)

type Handler func(payload []byte) error

type Client struct {
	URL         string
	MaxBackoff  time.Duration
	PingEvery   time.Duration
	OnMessage   Handler
}

func (c *Client) defaults() {
	if c.MaxBackoff == 0 {
		c.MaxBackoff = 8 * time.Second
	}
	if c.PingEvery == 0 {
		c.PingEvery = 30 * time.Second
	}
}

// Run kopunca üstel geri çekilmeyi uygular; ctx iptalinde temiz iner.
func (c *Client) Run(ctx context.Context) error {
	c.defaults()
	attempt := 0
	for {
		if err := c.session(ctx); err != nil && ctx.Err() != nil {
			return ctx.Err()
		}
		backoff := time.Duration(100<<uint(min(attempt, 7))) * time.Millisecond
		if backoff > c.MaxBackoff {
			backoff = c.MaxBackoff
		}
		attempt++
		slog.Info("ws yeniden bağlanıyor", "backoff", backoff)
		select {
		case <-ctx.Done():
			return ctx.Err()
		case <-time.After(backoff):
		}
	}
}

func (c *Client) session(ctx context.Context) error {
	dialer := websocket.Dialer{HandshakeTimeout: 10 * time.Second} // TLS yerleşik (wss)
	conn, _, err := dialer.DialContext(ctx, c.URL, http.Header{})
	if err != nil {
		return err
	}
	defer conn.Close()

	// okuma döngüsü + keepalive goroutine'i
	errc := make(chan error, 2)
	go func() {
		t := time.NewTicker(c.PingEvery)
		defer t.Stop()
		for {
			select {
			case <-ctx.Done():
				return
			case <-t.C:
				p, _ := json.Marshal(map[string]string{"cmd": "ping"})
				if err := conn.WriteMessage(websocket.TextMessage, p); err != nil {
					errc <- err
					return
				}
			}
		}
	}()
	go func() {
		for {
			_, msg, err := conn.ReadMessage()
			if err != nil {
				errc <- err
				return
			}
			if err := c.OnMessage(msg); err != nil {
				errc <- err
				return
			}
		}
	}()
	return <-errc
}

func min(a, b int) int { if a < b { return a }; return b }
```

## §7 Rust — reconnect'li WS istemci (tokio-tungstenite adayı)

```rust
// K014/comms/websocket — reconnect'li istemci (TASARIM ÖRNEĞİ).
// Kütüphane: tokio-tungstenite — bağımlılık seçimi faz C.
use std::time::Duration;
use futures_util::{SinkExt, StreamExt};
use tokio::time::sleep;

pub struct Config {
    pub url: String,
    pub max_backoff: Duration,
    pub ping_interval: Duration,
}

impl Default for Config {
    fn default() -> Self {
        Self {
            url: String::new(),
            max_backoff: Duration::from_secs(8),
            ping_interval: Duration::from_secs(30),
        }
    }
}

pub async fn run<F, Fut>(cfg: Config, mut on_message: F) -> Result<(), String>
where
    F: FnMut(String) -> Fut,
    Fut: std::future::Future<Output = ()>,
{
    let mut attempt: u32 = 0;
    loop {
        match session(&cfg, &mut on_message).await {
            Ok(()) => attempt = 0,                    // temiz bitiş bile yeniden dener
            Err(e) if e == "cancelled" => return Ok(()),
            Err(e) => tracing::warn!("ws koptu: {e}"),
        }
        let backoff = cfg
            .max_backoff
            .min(Duration::from_millis(100u64 << attempt.min(7)));
        attempt += 1;
        sleep(backoff).await;
    }
}

async fn session<F, Fut>(cfg: &Config, on_message: &mut F) -> Result<(), String>
where
    F: FnMut(String) -> Fut,
    Fut: std::future::Future<Output = ()>,
{
    use tokio_tungstenite::connect_async;
    let (ws, _) = connect_async(&cfg.url).await.map_err(|e| e.to_string())?;
    let (mut tx, mut rx) = ws.split();
    let mut ping = tokio::time::interval(cfg.ping_interval);
    loop {
        tokio::select! {
            _ = ping.tick() => {
                use tokio_tungstenite::tungstenite::Message;
                if tx.send(Message::Text(r#"{"cmd":"ping"}"#.into()))
                    .await.is_err() { return Err("ping gönderilemedi".into()); }
            }
            Some(Ok(msg)) = rx.next() => {
                if let Some(text) = msg.into_text().ok() {
                    on_message(text.to_string()).await;
                }
            }
            else => return Err("akış kapandı".into()),
        }
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Örnek tipi | Bağımlılık durumu | Keepalive |
|---|---|---|---|---|
| 1 | PHP 8.4 | istemci iskeleti | kütüphane seçimi faz C | uygulama ping'i |
| 2 | JS ES6+ | reconnect'li sınıf | yerleşik WebSocket | uygulama ping'i |
| 3 | C++20 | arayüz + sözleşme | libwebsockets adayı | config |
| 4 | C# | yerleşik ClientWebSocket | ek bağımlılık YOK | uygulama ping'i |
| 5 | Python | async istemci | websockets adayı | uygulama ping'i |
| 6 | Go | reconnect'li istemci | gorilla/websocket (repo doğrulandı) | uygulama ping'i |
| 7 | Rust | reconnect'li akış | tokio-tungstenite adayı | uygulama ping'i |

**Kapı:** 7/7 dil — reconnect + keepalive sözleşmesi her örnekte aynı; kütüphane
seçimleri faz C'de netleşir (standart-kanit.md §4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
