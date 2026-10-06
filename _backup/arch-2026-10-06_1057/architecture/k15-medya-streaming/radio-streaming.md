---
title: "Radio Streaming Support"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Radio Streaming Support

## Genel Bakış

Radio streaming desteği, internet radyo istasyonlarını Shoutcast/Icecast protokolleri üzerinden bağlar ve çalar. COREMUSIC, SHOUTcast v2 ve Icecast 2.x protokolleri ile uyumlu bir radio client implemente eder. Genre-based discovery, station favorilere ekleme ve AutoDJ desteği mevcuttur.

## Radio Streaming Mimarisi

```
┌─────────────────────────────────────────────────┐
│           Radio Streaming Architecture          │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │ Station  │───▶│ Protocol │───▶│ Decoder  │  │
│  │ Discovery│    │ Handler  │    │ Engine   │  │
│  └──────────┘    └──────────┘    └────┬─────┘  │
│                                       │        │
│  Protocol Layer                       │        │
│  ├── HTTP Streaming (SHOUTcast) ─────┤        │
│  ├── Ogg Streaming (Icecast) ─────────┤        │
│  ├── RTSP (Real-time) ────────────────┤        │
│  └── Podcast RSS ─────────────────────┤        │
│                                       │        │
│  Station Metadata                     │        │
│  ├── Stream Title ────────────────────┤        │
│  ├── Current Song ────────────────────┤        │
│  ├── Genre/Bitrate ───────────────────┤        │
│  └── Listener Count ──────────────────┘        │
└─────────────────────────────────────────────────┘
```

## Teknik Detaylar

### SHOUTcast v2 Protocol

```
SHOUTcast v2 Protocol Messages:
┌─────────────────────────────────────────────────┐
│ GET /stream HTTP/1.0                           │
│ Host: stream.example.com:8000                  │
│ User-Agent: COREMUSIC/1.0                      │
│ Icy-MetaData: 1                                │
├─────────────────────────────────────────────────┤
│ HTTP/1.0 200 OK                                │
│ Content-Type: audio/mpeg                       │
│ icy-br: 128                                    │
│ icy-metaint: 16000                             │
│ icy-name: Example Radio                        │
│ icy-genre: Electronic                          │
│ icy-url: http://radio.example.com              │
│ icy-notice: Stream is live                     │
│ icy-pub: 1                                     │
│ icy-sr: 44100                                  │
│ icy-channels: 2                                │
│ icy-description: Example Radio Station         │
├─────────────────────────────────────────────────┤
│ [Audio Data] [Metadata Block] [Audio Data] ... │
└─────────────────────────────────────────────────┘
```

### Metadata Parsing

```
StreamTitle Parsing:
┌──────────────────────────────────────────────────┐
│  icy-metaint header value (e.g., 16000)         │
│  indicates byte position where metadata begins   │
├──────────────────────────────────────────────────┤
│                                                  │
│  1. Read 16000 bytes of audio                    │
│  2. Read 1 byte: metadata length (N)            │
│  3. Read N*16 bytes of metadata                  │
│  4. Parse StreamTitle and StreamUrl              │
│  5. Continue reading audio                       │
│                                                  │
│  Metadata Format:                                │
│  StreamTitle='Artist - Title';StreamUrl='URL';  │
└──────────────────────────────────────────────────┘
```

### Icecast 2.x Protocol

```
Icecast Protocol Extensions:
┌─────────────────────────────────────────────────┐
│ X-ACLUUID: Authentication UUID                 │
│ X-ACLPASSWORD: Authentication password          │
│ ice-audio-info: bitrate=128&channels=2&...     │
├─────────────────────────────────────────────────┤
│ Status Page: /status.xsl                        │
│ Admin Page: /admin/                              │
│ JSON Stats: /status-json.xsl                    │
│                                                    │
│ Relay Configuration:                             │
│ <relay>                                          │
│   <server>relay.example.com</server>             │
│   <port>8000</port>                              │
│   <mount>/live</mount>                           │
│   <local-mount>/relay/live</local-mount>         │
│ </relay>                                         │
└─────────────────────────────────────────────────┘
```

### Station Discovery

```
Station Discovery Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐
│ Directory│───▶│ Metadata │───▶│ Station  │
│ API      │    │ Extractor│    │ Database │
└──────────┘    └──────────┘    └──────────┘
     │               │               │
     ▼               ▼               ▼
  Radio Browser   Genre/BPM      MySQL DB
  API Query       Language       Redis Cache
  Language        Bitrate        User Rating
```

### Stream Error Handling

```
Error Recovery Strategy:
┌──────────────┬────────────────────────────────┐
│ Hata Tipi    │ Müdahale                       │
├──────────────┼────────────────────────────────┤
│ Connection   │ 3 retry, 5sn bekleme          │
│ Drop         │ Otomatik reconnect            │
│ Timeout      │ 30sn timeout, alternative url │
│ 403/404      │ Station inactive, skip        │
│ Corrupt      │ Buffer flush, restart decode  │
│ Rate Limit   │ Backoff algorithm             │
└──────────────┴────────────────────────────────┘
```

## Kod / Konfigürasyon

### Radio Client Konfigürasyonu

```yaml
radio_client:
  protocols:
    shoutcast:
      version: 2
      timeout: 10
      reconnect_delay: 5
      max_reconnect: 3
      
    icecast:
      version: 2
      timeout: 10
      reconnect_delay: 5
      max_reconnect: 3
      
    rtsp:
      enabled: false
      
  streaming:
    buffer_size: 256000
    pre_buffer_ms: 3000
    max_buffer_ms: 10000
    
  metadata:
    parse_interval: 16000
    update_interval: 10000
    
  directory:
    enabled: true
    cache_ttl: 3600
    max_stations: 1000
```

### Radio Database Schema

```sql
CREATE TABLE radio_stations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(512) NOT NULL,
    genre VARCHAR(100),
    bitrate INT,
    sample_rate INT,
    channels TINYINT,
    codec VARCHAR(50),
    homepage VARCHAR(512),
    logo_url VARCHAR(512),
    country VARCHAR(2),
    language VARCHAR(5),
    is_public BOOLEAN DEFAULT TRUE,
    last_checked DATETIME,
    listener_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_genre (genre),
    INDEX idx_bitrate (bitrate),
    INDEX idx_language (language),
    INDEX idx_country (country)
);
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FFmpeg | 6.1.x | Audio decode |
| libshout | 2.4.x | SHOUTcast client |
| libcurl | 8.x | HTTP streaming |
| MySQL | 8.0.x | Station database |
| Redis | 7.x | Metadata cache |

## Durum: Implementasyon

Radio streaming desteği aktif geliştirme aşamasındadır. SHOUTcast/Icecast temel desteği tamamlanmıştır.

| Özellik | Durum |
|---------|-------|
| SHOUTcast v2 | Tamamlandı |
| Icecast 2.x | Tamamlandı |
| Stream Metadata | Tamamlandı |
| Station Discovery | Tamamlandı |
| Auto-Reconnect | Tamamlandı |
| Station Favorites | Tamamlandı |
| Genre Filter | Tamamlandı |
| RTSP Support | Planlama |
