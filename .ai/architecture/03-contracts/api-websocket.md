---
type: architecture
category: contracts
title: "API WebSocket Architecture"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API WebSocket Architecture

**Zorunlu Bağlantılar:** [[api-architecture-master]]

---

## 1. Amaç

CoreMusic platformundaki real-time iletişim ihtiyacını karşılayan WebSocket mimarisini tanımlar. RFC 6455 uyumlu, güvenli ve ölçeklenebilir WebSocket protokolü kullanılır.

---

## 2. WebSocket Kullanım Alanları

| Alan | Açıklama | Kanal |
|------|----------|-------|
| Player Status | Anlık oynatma durumu | `player:status` |
| Spectrum Analyzer | Gerçek zamanlı frekans spektrumu | `player:spectrum` |
| Live Notifications | Anlık bildirimler | `user:notifications` |
| Device Status | Cihaz bağlantı durumu | `device:status` |
| Equalizer Updates | EQ değişiklikleri | `player:eq` |
| Playlist Sync | Çalma listesi senkronizasyonu | `playlist:sync` |
| Download Progress | İndirme ilerleme durumu | `download:progress` |
| Chat/Comments | Canlı sohbet yorumları | `social:chat` |

---

## 3. WebSocket Protokol Detayı (RFC 6455)

| Özellik | Değer |
|---------|-------|
| Protocol | `wss://` (TLS zorunlu) |
| Version | RFC 6455 |
| Frame Format | Text frame (JSON) |
| Max Frame | 64KB |
| Max Message | 1MB |
| Compression | `permessage-deflate` (opsiyonel) |

---

## 4. Connection Lifecycle

```
┌─────────────────────────────────────────────────────────┐
│ 1. CONNECT                                              │
│    Client → Server: WebSocket Handshake (wss://)        │
│    Server → Client: 101 Switching Protocols              │
├─────────────────────────────────────────────────────────┤
│ 2. AUTH                                                  │
│    Client → Server: {"type":"auth","token":"..."}        │
│    Server → Client: {"type":"auth_ok","userId":123}      │
├─────────────────────────────────────────────────────────┤
│ 3. SUBSCRIBE                                             │
│    Client → Server: {"type":"subscribe","ch":"player:*"} │
│    Server → Client: {"type":"sub_ok","ch":"player:*"}    │
├─────────────────────────────────────────────────────────┤
│ 4. MESSAGE (Bidirectional)                               │
│    Client ← → Server: {"type":"event","data":{...}}      │
├─────────────────────────────────────────────────────────┤
│ 5. DISCONNECT                                            │
│    Client → Server: Close frame (1000)                   │
│    Server → Client: Close frame (1000)                   │
└─────────────────────────────────────────────────────────┘
```

---

## 5. Message Format (JSON)

### 5.1 Client → Server

```json
{
    "type": "subscribe",
    "ch": "player:status",
    "id": "msg-001"
}
```

### 5.2 Server → Client

```json
{
    "type": "event",
    "ch": "player:status",
    "data": {
        "state": "playing",
        "trackId": 12345,
        "position": 120.5,
        "duration": 240.0
    },
    "ts": 1691592000
}
```

### 5.3 Hata Mesajı

```json
{
    "type": "error",
    "code": 4001,
    "message": "Unauthorized",
    "id": "msg-001"
}
```

---

## 6. Message Types

| Type | Yön | Açıklama |
|------|-----|----------|
| `auth` | C→S | Kimlik doğrulama |
| `auth_ok` | S→C | Doğrulama başarılı |
| `auth_fail` | S→C | Doğrulama başarısız |
| `subscribe` | C→S | Kanala abone ol |
| `sub_ok` | S→C | Abonelik başarılı |
| `unsubscribe` | C→S | Abonelik iptali |
| `event` | S→C | Olay bildirimi |
| `ping` | C→S | Sağlık kontrolü |
| `pong` | S→C | Sağlık yanıtı |
| `error` | S→C | Hata bildirimi |

---

## 7. Heartbeat / Ping-Pong

| Özellik | Değer |
|---------|-------|
| Ping Intervali | 30 saniye |
| Pong Timeout | 10 saniye |
| Max Missed Pongs | 3 |
| Aksiyon | Bağlantıyı sonlandır |

```php
// Server-side heartbeat
$loop->addPeriodicTimer(30, function () {
    foreach ($this->connections as $conn) {
        if (!$conn->pongReceived) {
            $conn->missedPongs++;
            if ($conn->missedPongs >= 3) {
                $conn->close(1000, 'Heartbeat timeout');
                continue;
            }
        }
        $conn->pongReceived = false;
        $conn->send(json_encode(['type' => 'ping']));
    }
});
```

---

## 8. Reconnection Strategy

| Deneme | Gecikme | Max |
|--------|---------|-----|
| 1 | 1s | — |
| 2 | 2s | — |
| 3 | 4s | — |
| 4 | 8s | — |
| 5 | 16s | — |
| 6+ | 30s | Max 60s |

**Jitter:** ±500ms rastgele ekleme (thundering herd önleme).

```javascript
// Client-side reconnection
class WebSocketReconnect {
    constructor(url, maxRetries = 5) {
        this.url = url;
        this.retryCount = 0;
        this.maxRetries = maxRetries;
    }

    connect() {
        this.ws = new WebSocket(this.url);
        this.ws.onclose = () => this.reconnect();
    }

    reconnect() {
        if (this.retryCount >= this.maxRetries) return;
        const delay = Math.min(1000 * Math.pow(2, this.retryCount), 30000);
        const jitter = Math.random() * 500;
        setTimeout(() => this.connect(), delay + jitter);
        this.retryCount++;
    }
}
```

---

## 9. Channel-Based Subscription

| Channel Pattern | Kapsam | Örnek |
|-----------------|--------|-------|
| `player:status` | Tek player durumu | Player state changes |
| `player:spectrum` | Tek player spectrum | Real-time EQ data |
| `player:*` | Tüm player olayları | All player events |
| `user:notifications` | Kullanıcı bildirimleri | Push notifications |
| `device:status` | Cihaz durumu | Connection status |
| `playlist:sync` | Çalma listesi senk. | Playlist updates |
| `download:progress` | İndirme ilerlemesi | Progress updates |
| `social:chat` | Sohbet mesajları | Chat messages |
| `admin:system` | Admin bildirimleri | System alerts |

---

## 10. WebSocket vs SSE Karşılaştırması

| Özellik | WebSocket | SSE |
|---------|-----------|-----|
| Protokol | RFC 6455 | HTTP/1.1 |
| Yön | Bidirectional | Unidirectional (S→C) |
| Format | JSON/Binary | Text (text/event-stream) |
| Reconnection | Manuel | Otomatik |
| Channel | Var | Yok (tek stream) |
| Browser | Tümü | Tümü (IE除外) |
| Use Case | Real-time, bidirectional | Bildirim, streaming |
| Security | WSS (TLS) | HTTPS |
| Kullanım | Player, chat, spectrum | Notification fallback |

---

## 11. Güvenlik

### 11.1 Token Auth on Connect

```javascript
// Connection with token
const ws = new WebSocket('wss://ws.coremusic.net:9742');
ws.onopen = () => {
    ws.send(JSON.stringify({
        type: 'auth',
        token: getAuthToken()
    }));
};
```

### 11.2 Güvenlik Kuralları

| Kural | Değer |
|-------|-------|
| TLS | Zorunlu (wss://) |
| Origin Check | Whitelist domain'ler |
| Rate Limit | 100 msg/sn per connection |
| Max Connections | 50 per user |
| Token Refresh | Her 30 dakikada |
| IP Whitelist | Internal services only |

---

## 12. Message Examples

### 12.1 Player Status Update

```json
{
    "type": "event",
    "ch": "player:status",
    "data": {
        "state": "playing",
        "trackId": 12345,
        "title": "Bohemian Rhapsody",
        "artist": "Queen",
        "position": 120.5,
        "duration": 354.0,
        "volume": 80,
        "shuffle": false,
        "repeat": "off"
    }
}
```

### 12.2 Spectrum Data

```json
{
    "type": "event",
    "ch": "player:spectrum",
    "data": {
        "bands": [0.8, 0.6, 0.4, 0.7, 0.9, 0.5, 0.3, 0.6, 0.8, 0.4, 0.2, 0.5, 0.7, 0.9, 0.6, 0.3],
        "bass": 0.75,
        "mid": 0.55,
        "treble": 0.45
    }
}
```

### 12.3 Download Progress

```json
{
    "type": "event",
    "ch": "download:progress",
    "data": {
        "downloadId": "dl-789",
        "progress": 65,
        "speed": "2.5 MB/s",
        "eta": "45s",
        "status": "downloading"
    }
}
```

---

## 13. Edge Cases

| Durum | Çözüm |
|-------|-------|
| Network kopması | Reconnect + state restore |
| Duplicate message | Event ID ile idempotency |
| Message queue overflow | Drop old messages, keep latest |
| Auth token süresi | Refresh + reconnect |
| Channel spam | Rate limiting per channel |

---

## 14. Warnings

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | WSS olmadan produção | Güvenlik açığı |
| 2 | Heartbeat yoksa ghost connection | Resource leak |
| 3 | Token refresh edilmezse | Disconnect |
| 4 | Binary data JSON'a encode edilmezse | Parsing hatası |
| 5 | Channel pattern çok genişse | Performans düşüşü |

---

## 15. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| [[api-architecture-master]] | Ana mimari referans | Master |
| [[ADR-017-dsp-hardware-mode]] | Audio streaming | Real-time audio data |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| RFC Compliance | RFC 6455 ✅ |
| Channel Count | 9 channel |
| Message Types | 10 type |
| Reconnection | Exponential backoff + jitter |
| Security | WSS + Token Auth |
| Heartbeat | 30s interval |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
