---
title: "AirPlay 2 Ses Akışı"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# AirPlay 2 Ses Akışı

## Genel Bakış

AirPlay 2, Apple cihazlardan COREMUSIC hoparlörlerine yüksek kaliteli, düşük gecikmeli ses akışı sağlayan protokoldür. Çoklu oda desteği,考古δε fikir birliği tabanlı senkronizasyon ve ALAC (Apple Lossless) codec entegrasyonu ile profesyonel ses dağıtımı sunar.

## Protokol Detayı

- **Protokol**: AirPlay 2 (proprietary, reverse-engineered)
- **Transport**: TCP (kontrol) + RTP/UDP (ses)
- **Portlar**: 7000 (control), 7100 (RAOP), 49152+ (RTP)
- **Codec**: ALAC (Apple Lossless), AAC, PCM
- **Şifreleme**: AES-128-CTR (FairPlay)
- **Senkronizasyon**: PTP (Precision Time Protocol)

## Teknik Detaylar

### AirPlay 2 Bağlantı Akışı

```
┌──────────┐                              ┌──────────┐
│ iPhone   │                              │ Speaker  │
└─────┬────┘                              └─────┬────┘
      │                                         │
      │ 1. mDNS Discovery                       │
      │    _raop._tcp.local                      │
      │────────────────────────────────────────>│
      │                                         │
      │ 2. DNS-SD TXT Records                   │
      │    (features, version, etc)             │
      │<────────────────────────────────────────│
      │                                         │
      │ 3. TCP Connection (port 7000)           │
      │────────────────────────────────────────>│
      │                                         │
      │ 4. FairPlay Authentication              │
      │    (pairing + key exchange)             │
      │<══════════════════════════════════════>│
      │                                         │
      │ 5. SETUP (RTSP)                         │
      │    - Stream configuration               │
      │    - Buffer size negotiation             │
      │    - Crypt info exchange                │
      │────────────────────────────────────────>│
      │                                         │
      │ 6. RECORD (RTSP)                        │
      │    - Start streaming                    │
      │────────────────────────────────────────>│
      │                                         │
      │ 7. RTP Audio Stream                     │
      │    (UDP port 7100+)                     │
      │════════════════════════════════════════>│
      │                                         │
      │ 8. SYNC (PTP-based timing)             │
      │<══════════════════════════════════════>│
```

### RTSP (Real Time Streaming Protocol) Mesajları

```
OPTIONS * HTTP/1.1
CSeq: 1
User-Agent: AirPlay/2.0

ANNOUNCE RTSP/1.0
CSeq: 2
Content-Type: application/sdp

v=0
o=iPhone 0 0 IN IP4 192.168.1.50
s=COREMUSIC Stream
c=IN IP4 192.168.1.50
m=audio 0 RTP/AVP 96
a=rtpmap:96 L16/44100/2
a=fmtp:96 frames_per_packet=352
a=crypto:1 AES_CM_128_HMAC_SHA1_80 inline:base64key

SETUP RTSP/1.0
CSeq: 3
Transport: RTP/AVP/UDP;unicast;interleaved=0-1
Session: 12345678

RECORD RTSP/1.0
CSeq: 4
Session: 12345678
Range: npt=0-

SET_PARAMETER RTSP/1.0
CSeq: 5
Session: 12345678
Content-Type: text/parameters

volume: 0.75
progress: 127000/354000
```

### AirPlay 2 Çoklu Oda Protocol

```
┌────────────────────────────────────────────────────────┐
│              AirPlay 2 Multi-Room Sync                  │
│                                                          │
│  ┌──────────┐     ┌──────────┐     ┌──────────┐        │
│  │ Master   │────►│ Slave 1  │────►│ Slave 2  │        │
│  │ (iPhone) │     │ (Living) │     │(Bedroom) │        │
│  └────┬─────┘     └────┬─────┘     └────┬─────┘        │
│       │                │                │                │
│       │    PTP Sync    │    PTP Sync    │                │
│       │◄══════════════►│◄══════════════►│                │
│       │                │                │                │
│       │   Heartbeat    │   Heartbeat    │                │
│       │   (500ms)      │   (500ms)      │                │
│       │───────────────>│───────────────>│                │
│       │                │                │                │
│       │   Status ACK   │   Status ACK   │                │
│       │<───────────────│<───────────────│                │
│                                                          │
│  Drift Compensation:                                     │
│  - Master clock: PTP Hardware Clock                     │
│  - Slave offset: < ±2ms tolerance                       │
│  - Auto-adjust: Pitch shifting ±0.5%                    │
│  - Buffer: 500ms playback buffer                        │
└────────────────────────────────────────────────────────┘
```

### FairPlay Şifreleme

```
AES-128-CTR Şifreleme Akışı:

1. Key Exchange:
   Client → Server: ECDH public key (32 bytes)
   Server → Client: ECDH public key (32 bytes)
   Shared secret: ECDH(private_key, peer_public)

2. Session Key Derivation:
   master_key = HKDF(shared_secret, "AirPlay", 32)
   session_key = HKDF(master_key, "stream-key", 16)

3. Audio Encryption:
   plaintext: ALAC audio frame (N bytes)
   nonce: 8 bytes (stream-specific)
   ciphertext = AES-128-CTR(session_key, nonce, plaintext)

4. Authentication Tag:
   tag = HMAC-SHA1(session_key, ciphertext)
```

### ALAC Audio Configuration

```yaml
audio_config:
  codec: "ALAC"
  sample_rates:
    - 44100
    - 48000
    - 88200
    - 96000
  bit_depth: [16, 24]
  channels: [1, 2]
  frame_size: 352                   # samples per frame
  max_bitrate: 1536000              # 1.5 Mbps (lossless)
  buffer_size: 500000               # μs (500ms)
  latency_target: 2000              # μs (2ms)
```

### Discoverability TXT Records

```
_coremusic._tcp.local TXT Records:
  vs=770.11.2                  # AirPlay version
  fl=0x3A0B2E26               # Feature flags
    bit 0: Audio (AAC/L16)
    bit 1: Photo
    bit 2: Video
    bit 3: Screen mirroring
    bit 4: AirPlay 2
    bit 5: Metadata
    bit 6: Extended metadata
    bit 7: iTunes
    bit 8: Buffer full/resume
  am=AirPlay                    # Device model
  wt=49152                      # Wake-on-LAN port
  pw=false                      # Password required
  cn=0,1                        # Audio channels (0: stereo, 1: mono)
  et=0,1                        # Encryption types
  md=0,1,2                      # Metadata modes
```

## Konfigürasyon

```yaml
airplay:
  enabled: true
  device_name: "COREMUSIC Speaker"
  password: null
  port: 7000
  raop_port: 7100
  rtp_port_range: "49152-65535"
  encryption:
    enabled: true
    method: "AES-128-CTR"
  audio:
    codec: "ALAC"
    sample_rate: 44100
    bit_depth: 24
    channels: 2
    buffer_ms: 500
    latency_ms: 2
  multi_room:
    enabled: true
    sync_tolerance_ms: 2
    heartbeat_interval_ms: 500
    drift_compensation: true
    max_drift_ms: 50
  fairplay:
    enabled: true
    key_size: 128
  discovery:
    mdns_service: "_raop._tcp"
    txt_records:
      vs: "770.11.2"
      fl: "0x3A0B2E26"
  features:
    audio: true
    photo: false
    video: false
    mirroring: false
    metadata: true
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K14-mDNS | Giren | Service discovery |
| K14-UDP | Giren | RTP ses akışı |
| K6 | Çıkan | FairPlay şifreleme |
| K8 | Çıkan | AirPlay servis kaydı |
| K3 | Çıkan | Ses codec çözümleme |

## Durum: Implementasyon

- [x] mDNS service advertisement
- [x] RTSP OPTIONS/ANNOUNCE/SETUP
- [x] FairPlay key exchange
- [x] ALAC codec encode/decode
- [x] RTP streaming (UDP)
- [x] PTP clock synchronization
- [x] Multi-room sync
- [x] Drift compensation
- [x] Buffer management
- [x] Feature flag negotiation
- [x] Metadata passthrough
