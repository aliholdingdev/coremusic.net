---
title: "HTTP/2 & HTTP/3 Protokol Katmanı"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# HTTP/2 & HTTP/3 Protokol Katmanı

## Genel Bakış

HTTP/2 ve HTTP/3, COREMUSIC'in tüm REST API ve medya akış iletişimini sağlayan temel protokollerdir. HTTP/2 ile TCP tabanlı multiplexing, HTTP/3 ile QUIC tabanlı low-latency bağlantılar desteklenir. Her iki protokol de sunucu-sunucu ve sunucu-istemci iletişimini optimize eder.

## Protokol Detayı

### HTTP/2 (RFC 7540)
- **Transport**: TCP + TLS 1.2/1.3
- **Mekanizma**: Binary framing, stream multiplexing, header compression (HPACK)
- **Akış MODELİ**: Bidirectional, her stream bağımsız
- **Pencere Yönetimi**: Flow control per-stream ve connection-level
- **Server Push**: İsteğe bağlı resource push

### HTTP/3 (RFC 9114)
- **Transport**: QUIC (RFC 9000) — UDP tabanlı
- **Mekanizma**: 0-RTT connection establishment, native multiplexing
- **Head-of-Line Blocking**: TCP'deki HOL problemi QUIC ile çözülür
- **Bağlantı Geçişleri**: Network change (WiFi ↔ Cellular) seamless migration
- **Şifreleme**: QUIC, TLS 1.3'ü transport katmanına entegre eder

## Teknik Detaylar

### HTTP/2 Frame Yapısı

```
+-----------------------------------------------+
|                 Length (24)                     |
+---------------+---------------+---------------+
|   Type (8)    |   Flags (8)   |
+-+-------------+---------------+------...-----+
|R|                 Stream ID (31)             |
+-+---------------------------------------------+
|                 Frame Payload (0...)         |
+-----------------------------------------------+
```

**Frame Türleri:**
- `DATA` (0x0): Gövde verisi
- `HEADERS` (0x1): HTTP header'ları
- `PRIORITY` (0x2): Akış önceliği
- `RST_STREAM` (0x3): Akış iptali
- `SETTINGS` (0x4): Bağlantı parametreleri
- `PUSH_PROMISE` (0x5): Server push bildirimi
- `PING` (0x6): Heartbeat
- `GOAWAY` (0x7): Bağlantı sonlandırma
- `WINDOW_UPDATE` (0x8): Flow control
- `CONTINUATION` (0x9): Header fragmentasyonu

### HTTP/2 Conection Setup

```
┌─────────┐                              ┌─────────┐
│ Client  │                              │ Server  │
└────┬────┘                              └────┬────┘
     │  TCP SYN                              │
     │──────────────────────────────────────>│
     │  TCP SYN-ACK                          │
     │<──────────────────────────────────────│
     │  TCP ACK                              │
     │──────────────────────────────────────>│
     │  TLS ClientHello                      │
     │──────────────────────────────────────>│
     │  TLS ServerHello + EncryptedExtension │
     │<──────────────────────────────────────│
     │  HTTP/2 SETTINGS                     │
     │──────────────────────────────────────>│
     │  HTTP/2 SETTINGS + ACK               │
     │<──────────────────────────────────────│
     │  HTTP/2 WINDOW_UPDATE                │
     │──────────────────────────────────────>│
```

### QUIC (HTTP/3) Bağlantı Akışı

```
┌─────────┐                              ┌─────────┐
│ Client  │                              │ Server  │
└────┬────┘                              └────┬────┘
     │  QUIC Initial (ClientHello)           │
     │──────────────────────────────────────>│
     │  QUIC Initial + Handshake             │
     │<──────────────────────────────────────│
     │  QUIC Handshake Complete              │
     │──────────────────────────────────────>│
     │  0-RTT Data (varsa)                   │
     │──────────────────────────────────────>│
     │  Bidirectional Stream                 │
     │<──────────────────────────────────────>│
```

### HPACK Header Compression

```
Indexed Header (1-bit flag = 1):
+---+---+---+---+---+---+---+---+
| 1 |        Index (7+)         |
+---+---+---+---+---+---+---+---+

Literal Header (1-bit flag = 0):
+---+---+---+---+---+---+---+---+
| 0 | 0 |      Index (6+)      |
+---+---+---+---+---+---+---+---+
| H |     Value Length (7+)     |
+---+---+---+---+---+---+---+---+
|       Value String            |
+---+---+---+---+---+---+---+---+
```

**Static Table (61 entry):**
| Index | Header Name | Header Value |
|-------|-------------|--------------|
| 1 | :authority | (empty) |
| 2 | :method | GET |
| 3 | :method | POST |
| 4 | :path | / |
| 5 | :scheme | http |
| 6 | :scheme | https |
| 7 | :status | 200 |
| 25 | accept-encoding | gzip, deflate |
| 26 | accept-language | (empty) |
| 48 | content-type | application/json |

### COREMUSIC Entegrasyonu

**API Endpoint Sensitivity:**
```
Yüksek frekans: /api/v1/now-playing     → HTTP/2 prioritized stream
Orta frekans:   /api/v1/playlists        → HTTP/2 standard stream
Düşük frekans:  /api/v1/settings         → HTTP/2 deferred stream
Akış:           /api/v1/stream           → HTTP/3 QUIC preferred
```

**QoS Mapping (DSCP):**
```yaml
endpoints:
  /api/v1/stream:
    dscp: "EF"           # Expedited Forwarding
    priority: 46
  /api/v1/now-playing:
    dscp: "AF41"
    priority: 34
  /api/v1/playlist:
    dscp: "AF21"
    priority: 18
  /api/v1/settings:
    dscp: "CS0"
    priority: 0
```

### H2C (Cleartext HTTP/2)

```
Client                              Server
  |--- PRI * HTTP/2.0 --------------->|
  |--- SM: H2C Upgrade ------------->|
  |                                   |
  |<-- 101 Switching Protocols -------|
  |    Connection: Upgrade            |
  |    Upgrade: h2c                   |
  |                                   |
  |<== HTTP/2 Binary Framing =======>|
```

### Server Push Stratejisi

```python
# COREMUSIC server push kuralları
PUSH_RULES = {
    "/api/v1/album/{id}": [
        "/api/v1/album/{id}/tracks",    # Albüm track listesi
        "/api/v1/album/{id}/artwork",   # Albüm kapağı
    ],
    "/api/v1/playlist/{id}": [
        "/api/v1/playlist/{id}/tracks",
    ],
    "/api/v1/player/state": [
        "/api/v1/player/queue",
        "/api/v1/now-playing",
    ],
}
```

## Konfigürasyon

```yaml
http2:
  enabled: true
  max_concurrent_streams: 100
  initial_window_size: 1048576          # 1MB
  max_frame_size: 16384                 # 16KB
  max_header_list_size: 8192            # 8KB
  header_table_size: 4096
  enable_push: true
  enable_connect_protocol: true
  settings_timeout: 10000               # ms
  graceful_shutdown_timeout: 30000      # ms

http3:
  enabled: true
  max_udp_datagram_size: 1200
  max_streams_bidi: 100
  max_streams_uni: 100
  initial_max_data: 10485760            # 10MB
  initial_max_stream_data_bidi_local: 1048576
  max_idle_timeout: 30000               # ms
  max_ack_delay: 25                     # ms
  congestion_controller: "bbr"
  enable_0rtt: true
  active_connection_id_limit: 4
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | TCP/UDP soket yönetimi |
| K6 | Çıkan | TLS 1.3 entegrasyonu |
| K7 | Çıkan | Middleware HTTP pipeline |
| K8 | Çıkan | Servis endpoint keşfi |

## Durum: Implementasyon

- [x] HTTP/2connection establishment
- [x] HPACK header compression
- [x] Stream multiplexing
- [x] Flow control optimizasyonu
- [x] Server push implementasyonu
- [x] H2C cleartext desteği
- [x] QUIC 0-RTT connection
- [x] HTTP/3 connection migration
- [x] BBR congestion controller
- [x] DSCP QoS mapping
- [x] Graceful shutdown
