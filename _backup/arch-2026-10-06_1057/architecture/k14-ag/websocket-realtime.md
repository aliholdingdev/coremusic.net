---
title: "WebSocket Real-Time İletişim"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# WebSocket Real-Time İletişim

## Genel Bakış

WebSocket, COREMUSIC'in real-time iletişim altyapısının temelini oluşturur. Player durumu senkronizasyonu, çoklu oda kontrolü, anlık bildirimler ve bidirectional messaging bu protokol üzerinden yürütülür. HTTP upgrade mekanizması ile TCP bağlantısı üzerine inşa edilir.

## Protokol Detayı

- **RFC**: 6455 (WebSocket), 8441 (WebSocket over HTTP/2)
- **Transport**: TCP (+ TLS opsiyonel)
- **İletişim Modeli**: Bidirectional, full-duplex
- **Frame Format**: Opcode-based binary framing
- **Keep-alive**: Ping/Pong heartbeat mekanizması

## Teknik Detaylar

### WebSocket Handshake

```
┌─────────┐                              ┌─────────┐
│ Client  │                              │ Server  │
└────┬────┘                              └────┬────┘
     │  GET /ws HTTP/1.1                      │
     │  Host: music.coremusic.local           │
     │  Upgrade: websocket                    │
     │  Connection: Upgrade                   │
     │  Sec-WebSocket-Key: dGhl...            │
     │  Sec-WebSocket-Version: 13             │
     │──────────────────────────────────────>│
     │                                       │
     │  HTTP/1.1 101 Switching Protocols      │
     │  Upgrade: websocket                    │
     │  Connection: Upgrade                   │
     │  Sec-WebSocket-Accept: s3pPL...        │
     │<──────────────────────────────────────│
     │                                       │
     │  ═══════ WebSocket Frame ═══════       │
     │<══════════════════════════════════════>│
```

### WebSocket Frame Yapısı

```
 0                   1                   2                   3
 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
+-+-+-+-+-------+-+-------------+-------------------------------+
|F|R|R|R| opcode|M| Payload len |    Extended payload length    |
|I|S|S|S|  (4)  |A|     (7)     |             (16/64)           |
|N|V|V|V|       |S|             |   (if payload len==126/127)   |
| |1|2|3|       |K|             |                               |
+-+-+-+-+-------+-+-------------+ - - - - - - - - - - - - - - - +
|     Extended payload length continued, if payload len == 127  |
+ - - - - - - - - - - - - - - - +-------------------------------+
|                               |Masking-key, if MASK set to 1  |
+-------------------------------+-------------------------------+
| Masking-key (continued)       |          Payload Data         |
+-------------------------------- - - - - - - - - - - - - - - - +
:                     Payload Data continued ...                :
+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
|                     Payload Data (continued)                  |
+---------------------------------------------------------------+
```

**Opcode Tablosu:**
| Opcode | Tür | Açıklama |
|--------|-----|----------|
| 0x0 | Continuation | Fragment devamı |
| 0x1 | Text | UTF-8 metin |
| 0x2 | Binary | Binary veri |
| 0x8 | Close | Bağlantı kapatma |
| 0x9 | Ping | Heartbeat isteği |
| 0xA | Pong | Heartbeat yanıtı |

### COREMUSIC WebSocket Mesaj Yapısı

```json
{
  "type": "player.state.changed",
  "timestamp": 1726834800000,
  "payload": {
    "player_id": "player-001",
    "state": "playing",
    "track": {
      "id": "track-abc123",
      "title": "Bohemian Rhapsody",
      "artist": "Queen",
      "duration": 354000,
      "position": 127000
    },
    "volume": 0.75,
    "shuffle": false,
    "repeat": "off"
  }
}
```

### Mesaj Türleri

| Mesaj Türü | Yön | Açıklama |
|------------|-----|----------|
| `player.state.changed` | Server → Client | Player durum değişikliği |
| `player.command` | Client → Server | Player komutu |
| `queue.updated` | Server → Client | Kuyruk güncelleme |
| `room.sync` | Server → Client | Çoklu oda senkronizasyonu |
| `room.join` | Client → Server | Odaya katılma |
| `room.leave` | Client → Server | Odadan ayrılma |
| `notification` | Server → Client | Anlık bildirim |
| `heartbeat` | Bidirectional | Bağlantı canlılığı |

### Çoklu Oda Senkronizasyonu

```
┌──────────────────────────────────────────────────┐
│                    Kitchen                        │
│  ┌──────────┐                                    │
│  │ Player 1 │◄──── WebSocket ────► ┌──────────┐  │
│  └──────────┘                      │  Hub     │  │
│                                    │ (Master) │  │
├────────────────────────────────────┤          │  │
│                    Living Room     │          │  │
│  ┌──────────┐                      │          │  │
│  │ Player 2 │◄──── WebSocket ────► │          │  │
│  └──────────┘                      │          │  │
├────────────────────────────────────┤          │  │
│                    Bedroom         │          │  │
│  ┌──────────┐                      │          │  │
│  │ Player 3 │◄──── WebSocket ────► │          │  │
│  └──────────┘                      └──────────┘  │
│                                                  │
│  Sync Protocol:                                  │
│  1. Master heartbeat (500ms)                     │
│  2. Slave ACK within 200ms                       │
│  3. Drift compensation < 5ms                     │
│  4. Auto-reconnect on failure                    │
└──────────────────────────────────────────────────┘
```

### Connection Management

```python
class WebSocketManager:
    def __init__(self):
        self.connections: dict[str, WebSocket] = {}
        self.heartbeat_interval = 30000  # ms
        self.reconnect_backoff = [1000, 2000, 4000, 8000, 16000]
        self.max_reconnect_attempts = 5

    async def connect(self, player_id: str):
        ws = await self._establish_connection(player_id)
        self.connections[player_id] = ws
        asyncio.create_task(self._heartbeat_loop(player_id))

    async def _heartbeat_loop(self, player_id: str):
        while player_id in self.connections:
            ws = self.connections[player_id]
            try:
                pong = await ws.ping()
                await asyncio.wait_for(pong, timeout=5000)
            except asyncio.TimeoutError:
                await self._reconnect(player_id)

    async def _reconnect(self, player_id: str):
        for delay in self.reconnect_backoff:
            try:
                await self.connect(player_id)
                return
            except Exception:
                await asyncio.sleep(delay / 1000)
        raise ConnectionError(f"Reconnect failed for {player_id}")
```

### Close Code'ları

| Code | Açıklama |
|------|----------|
| 1000 | Normal closure |
| 1001 | Going away (sayfa kapanışı) |
| 1002 | Protocol hatası |
| 1003 | Desteklenmeyen veri türü |
| 1006 | Anormal kapatma |
| 1008 | Politika ihlali |
| 1011 | Sunucu hatası |
| 1012 | Sunucu yeniden başlatılıyor |
| 1013 | Sunucu çok meşgul |
| 1014 | Bad gateway |
| 4001 | Custom: Player lautlanamadı |
| 4002 | Custom: Oda dolu |
| 4003 | Custom: Yetki yok |

## Konfigürasyon

```yaml
websocket:
  enabled: true
  path: "/ws"
  port: 8443
  max_connections: 1000
  max_frame_size: 65536                  # 64KB
  max_message_size: 1048576              # 1MB
  ping_interval: 30000                   # ms
  pong_timeout: 5000                     # ms
  close_timeout: 5000                    # ms
  handshake_timeout: 10000               # ms
  per_message_deflate: true
  compression_threshold: 512
  allowed_origins:
    - "https://music.coremusic.local"
  rate_limit:
    max_messages_per_second: 50
    burst_size: 100
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | TCP soket yönetimi |
| K14-HTTP | Giren | HTTP upgrade mekanizması |
| K6 | Çıkan | WSS (TLS) desteği |
| K7 | Çıkan | WebSocket middleware |

## Durum: Implementasyon

- [x] WebSocket handshake
- [x] Binary/Text frame desteği
- [x] Ping/Pong heartbeat
- [x] Per-message deflate compression
- [x] Player state sync
- [x] Room sync protocol
- [x] Auto-reconnect mekanizması
- [x] Rate limiting
- [x] Close code yönetimi
- [x] WSS (TLS) desteği
