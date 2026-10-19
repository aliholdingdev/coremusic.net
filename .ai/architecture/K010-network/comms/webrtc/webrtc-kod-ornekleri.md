---
node_id: K010-network/comms/webrtc/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/comms/webrtc
role: ornek
children: []
depends_on:
  - "K010-network/comms/webrtc/webrtc-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — örnekler TASARIM ÖRNEĞİ'dir"
  - "Referans: W3C WebRTC 1.0 + RFC 8445 ICE (webrtc-standart-kanit.md §1)"
validation:
  - "Örnekler yalnız bilinen API'lerle yazıldı; sinyal kanalı taşıyıcısı varsayım olarak işaretli"
open_questions:
  - "Sinyal kanalı taşıyıcısı (websocket varsayımı) → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "TURN sunucusu → ⚠️ UNKNOWN (faz C)"
group: kod
---

# webrtc — kod-ornekleri (kademe-3 · module · rol: ornek)

> **UYARI:** K014'te kod YOK. Örnekler **tasarım şablonudur**. Ortak sözleşme: signaling
> üzerinden offer/answer alışveriş → ICE → peer bağlantısı → data channel. Sinyal kanalı
> burada **websocket varsayımıyla** yazılmıştır (⚠️ doğrulanmadı — faz C).

## §1 Vanilla JS (ES6+) — RTCPeerConnection + data channel (tarayıcı — birincil kol)

```js
/**
 * K014/comms/webrtc — offer/answer + data channel (TASARIM ÖRNEĞİ).
 * Sınır: sinyal kanalı taşıyıcısı websocket VARSAYIMI (⚠️ faz C);
 * medya codec'i K015; şifreleme tarayıcı yerleşik (DTLS-SRTP, RFC 8826).
 */
export class PeerLink {
  /** @param {(msg: object) => void} sendSignal sinyal mesajını kanala yaz (WS varsayımı) */
  constructor(sendSignal) {
    this.pc = new RTCPeerConnection({
      // ICE sunucuları — TURN sahibi UNKNOWN (faz C); STUN varsayılanı tarayıcıdan gelir
      iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
    });
    this.sendSignal = sendSignal;
    this.onState = () => {};
    this.onData = () => {};

    this.dc = this.pc.createDataChannel('coremusic', { ordered: true });
    this.dc.addEventListener('message', (ev) => this.onData(ev.data));

    this.pc.addEventListener('icecandidate', (ev) => {
      if (ev.candidate) this.sendSignal({ type: 'ice-candidate', candidate: ev.candidate });
    });
    this.pc.addEventListener('connectionstatechange', () => {
      this.onState(this.pc.connectionState); // new|connecting|connected|failed|disconnected|closed
    });
  }

  /** Yeni bağlantı başlatır (caller). */
  async createOffer() {
    const offer = await this.pc.createOffer();
    await this.pc.setLocalDescription(offer);
    this.sendSignal({ type: 'offer', sdp: this.pc.localDescription });
  }

  /** Karşı taraftan gelen sinyal mesajlarını işler. */
  async handleSignal(msg) {
    switch (msg.type) {
      case 'offer':
        await this.pc.setRemoteDescription(msg.sdp);
        await this.pc.setLocalDescription(await this.pc.createAnswer());
        this.sendSignal({ type: 'answer', sdp: this.pc.localDescription });
        break;
      case 'answer':
        await this.pc.setRemoteDescription(msg.sdp);
        break;
      case 'ice-candidate':
        await this.pc.addIceCandidate(msg.candidate);
        break;
    }
  }

  send(data) {
    if (this.dc.readyState === 'open') this.dc.send(data);
    // kapalıyken bırak — üst katman kuyruklar (offline-sync sınırı)
  }

  close() {
    this.dc.close();
    this.pc.close();
  }
}
```

## §2 PHP 8.4 — sinyal köprüsü (WS üzerinden offer/answer yönlendirme)

```php
<?php
declare(strict_types=1);

/**
 * K014/comms/webrtc — sinyal köprüsü (TASARIM ÖRNEĞİ).
 * Rol: iki istemci arasındaki offer/answer/ice-candidate mesajlarını yönlendirir;
 * SDP YORUMLAMAZ (K013/K012 sınırı — yalnız taşır).
 */
final class SignalingBridge
{
    /** @var array<string, object> peerId => oturum kaydı */
    private array $peers = [];

    /**
     * Gelen sinyal mesajını eşleştirilmiş peer'a iletir.
     * Beklenen şekil: {"type":"offer"|"answer"|"ice-candidate", ...}
     */
    public function relay(string $fromPeerId, string $toPeerId, string $jsonPayload): void
    {
        $msg = json_decode($jsonPayload, true, flags: JSON_THROW_ON_ERROR);
        if (!in_array($msg['type'] ?? '', ['offer', 'answer', 'ice-candidate'], true)) {
            return; // bilinmeyen tür — sessizce bırak (hata sınıfı: kalıcı)
        }
        $target = $this->peers[$toPeerId] ?? null;
        if ($target === null) {
            return; // peer yok — hata sınıfı: kalıcı, retry yok
        }
        $target->send($jsonPayload); // WS kanalı (websocket torunu)
    }

    public function register(string $peerId, object $channel): void
    {
        $this->peers[$peerId] = $channel;
    }
}
```

## §3 C++20 — native peer iskeleti (Neva Engine köprüsü — kütüphane seçimi faz C)

```cpp
// K014/comms/webrtc — native WebRTC peer arayüzü (TASARIM ÖRNEĞİ).
// Kütüphane: libwebrtc / libdatachannel — seçimi faz C; burada yalnız sözleşme.
#pragma once
#include <functional>
#include <string>
#include <span>

namespace cm::net {

enum class PeerState { new_, connecting, connected, failed, disconnected, closed };

class PeerLink {
public:
    struct Handlers {
        std::function<void(std::string_view sdp_json)> on_local_description; // sinyale yaz
        std::function<void(std::string_view candidate_json)> on_ice_candidate;
        std::function<void(PeerState)> on_state;
        std::function<void(std::span<const uint8_t>)> on_data;               // data channel
    };

    explicit PeerLink(Handlers h) : h_(std::move(h)) {}

    void create_offer();                       // caller tarafı
    void handle_signal(std::string_view json); // offer/answer/ice-candidate
    bool send(std::span<const uint8_t> payload);
    void close();

private:
    Handlers h_;
    // faz C: peer connection handle + data channel (libdatachannel adayı)
};

} // namespace cm::net
```

## §4 C# — sinyal istemcisi (ClientWebSocket üzerinden — WS torunu ile kesişim)

```csharp
// K014/comms/webrtc — sinyal kanalı istemcisi (TASARIM ÖRNEĞİ).
// Rol: WebRTC sinyal mesajlarını WS üzerinden taşır; SDP çözümlemez.
#nullable enable
using System.Text.Json;

namespace CoreMusic.Network.Signaling;

public sealed record SignalEnvelope(string Type, string From, string To, string Payload);

public sealed class SignalingClient
{
    private readonly Func<string, Task> _sendOverWs; // websocket torunu — kanal buradan gelir

    public SignalingClient(Func<string, Task> sendOverWs) => _sendOverWs = sendOverWs;

    public event Func<SignalEnvelope, Task>? OfferReceived;
    public event Func<SignalEnvelope, Task>? AnswerReceived;
    public event Func<SignalEnvelope, Task>? IceCandidateReceived;

    /// <summary>Gelen WS mesajını sinyal türüne yönlendirir (yalnız taşıma).</summary>
    public async Task DispatchAsync(string wsPayload)
    {
        var env = JsonSerializer.Deserialize<SignalEnvelope>(wsPayload)
                  ?? throw new FormatException("bozuk sinyal zarfı");
        switch (env.Type)
        {
            case "offer":
                if (OfferReceived is not null) await OfferReceived(env).ConfigureAwait(false);
                break;
            case "answer":
                if (AnswerReceived is not null) await AnswerReceived(env).ConfigureAwait(false);
                break;
            case "ice-candidate":
                if (IceCandidateReceived is not null) await IceCandidateReceived(env).ConfigureAwait(false);
                break;
            default:
                return; // bilinmeyen tür — kalıcı, retry yok
        }
    }

    public Task RelayAsync(SignalEnvelope env) =>
        _sendOverWs(JsonSerializer.Serialize(env));
}
```

## §5 Python 3.12+ — sinyal köprüsü (aiortc adayı — async)

```python
# K014/comms/webrtc — sinyal köprüsü (TASARIM ÖRNEĞİ).
# Kütüphane: aiortc (Python native WebRTC) — aday, seçimi faz C.
from __future__ import annotations

import json
from collections.abc import Awaitable, Callable
from typing import Any

SIGNAL_TYPES = {"offer", "answer", "ice-candidate"}


class SignalingBridge:
    """İki peer arasındaki sinyal mesajlarını yönlendirir; SDP yorumlamaz (yalnız taşır)."""

    def __init__(self) -> None:
        self._peers: dict[str, Callable[[str], Awaitable[None]]] = {}

    def register(self, peer_id: str, send: Callable[[str], Awaitable[None]]) -> None:
        self._peers[peer_id] = send

    async def relay(self, raw: str, *, from_peer: str, to_peer: str) -> None:
        try:
            msg: dict[str, Any] = json.loads(raw)
        except json.JSONDecodeError:
            return  # bozuk zarf — kalıcı hata, retry yok
        if msg.get("type") not in SIGNAL_TYPES:
            return  # bilinmeyen tür — kalıcı
        target = self._peers.get(to_peer)
        if target is None:
            return  # peer yok — kalıcı
        await target(raw)
```

## §6 Go — sinyal yönlendirici

```go
// K014/comms/webrtc — sinyal yönlendirici (TASARIM ÖRNEĞİ).
package signaling

import (
	"encoding/json"
	"errors"
	"fmt"
)

var (
	ErrUnknownType = errors.New("bilinmeyen sinyal türü") // kalıcı
	ErrNoPeer      = errors.New("hedef peer yok")         // kalıcı
)

type Envelope struct {
	Type    string          `json:"type"`
	From    string          `json:"from"`
	To      string          `json:"to"`
	Payload json.RawMessage `json:"payload"`
}

type Sender func([]byte) error // websocket torunu — kanal

type Bridge struct {
	peers map[string]Sender
}

func NewBridge() *Bridge { return &Bridge{peers: map[string]Sender{}} }

func (b *Bridge) Register(id string, send Sender) { b.peers[id] = send }

// Relay offer/answer/ice-candidate dışındakileri reddeder (retry yok).
func (b *Bridge) Relay(raw []byte) error {
	var env Envelope
	if err := json.Unmarshal(raw, &env); err != nil {
		return fmt.Errorf("bozuk zarf: %w", err)
	}
	switch env.Type {
	case "offer", "answer", "ice-candidate":
	default:
		return ErrUnknownType
	}
	send, ok := b.peers[env.To]
	if !ok {
		return ErrNoPeer
	}
	return send(raw)
}
```

## §7 Rust — sinyal yönlendirici

```rust
// K014/comms/webrtc — sinyal yönlendirici (TASARIM ÖRNEĞİ).
use std::collections::HashMap;

#[derive(Debug, serde::Serialize, serde::Deserialize)]
pub struct Envelope {
    #[serde(rename = "type")]
    pub kind: String,
    pub from: String,
    pub to: String,
    pub payload: serde_json::Value,
}

#[derive(Debug, thiserror::Error)]
pub enum SignalError {
    #[error("bozuk zarf: {0}")]
    Malformed(#[from] serde_json::Error),
    #[error("bilinmeyen sinyal türü: {0}")]
    UnknownType(String),
    #[error("hedef peer yok: {0}")]
    NoPeer(String),
}

const SIGNAL_TYPES: [&str; 3] = ["offer", "answer", "ice-candidate"];

pub struct Bridge {
    peers: HashMap<String, Box<dyn Fn(&str) + Send + Sync>>,
}

impl Bridge {
    pub fn new() -> Self {
        Self { peers: HashMap::new() }
    }

    pub fn register(&mut self, id: impl Into<String>, send: Box<dyn Fn(&str) + Send + Sync>) {
        self.peers.insert(id.into(), send);
    }

    /// offer/answer/ice-candidate dışındakileri ve bilinmeyen peer'ları reddeder
    /// (kalıcı hata — retry yok).
    pub fn relay(&self, raw: &str) -> Result<(), SignalError> {
        let env: Envelope = serde_json::from_str(raw)?;
        if !SIGNAL_TYPES.contains(&env.kind.as_str()) {
            return Err(SignalError::UnknownType(env.kind));
        }
        let send = self.peers.get(&env.to).ok_or_else(|| {
            SignalError::NoPeer(env.to.clone())
        })?;
        send(raw);
        Ok(())
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Örnek rolü | WebRTC API'si | Not |
|---|---|---|---|---|
| 1 | JS ES6+ | tam peer (tarayıcı) | RTCPeerConnection (W3C, yerleşik) | birincil kol |
| 2 | PHP 8.4 | sinyal köprüsü | yok — yalnız yönlendirme | backend |
| 3 | C++20 | native peer arayüzü | libwebrtc/libdatachannel adayı | Neva Engine |
| 4 | C# | sinyal istemcisi | yok — WS taşıma | Windows kolu |
| 5 | Python | sinyal köprüsü | aiortc adayı | test/araç |
| 6 | Go | sinyal yönlendirici | yok — WS taşıma | sunucu/tünel |
| 7 | Rust | sinyal yönlendirici | yok — WS taşıma | sistem |

**Sınır notu:** 7 örneğin 5'i yalnız **sinyal yönlendirme** yapar (SDP yorumlamaz —
K012/K013 sınırı korunur); tam peer bağlantısı JS (tarayıcı) ve C++ (native) örneklerinde.

**Kapı:** sinyal kanalı taşıyıcısı (websocket) **varsayımı** her örnekte işaretli;
faz C'de kararlaştırılıp güncellenecek.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
