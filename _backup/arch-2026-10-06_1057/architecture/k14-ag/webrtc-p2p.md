---
title: "WebRTC Peer-to-Peer İletişim"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# WebRTC Peer-to-Peer İletişim

## Genel Bakış

WebRTC (Web Real-Time Communication), COREMUSIC'in tarayıcı tabanlı peer-to-peer ses ve veri iletişimini sağlayan katmandır. ICE, STUN ve TURN protokolleri ile NAT traversal, SRTP ile güvenli ses akışı ve SCTP ile güvenilir veri kanalı desteği sunar.

## Protokol Detayı

- **Spec**: W3C WebRTC, IETF RTCWeb
- **Transport**: UDP (DTLS/SRTP) + TCP (fallback)
- **Media**: RTP/RTCP over DTLS
- **Data**: SCTP over DTLS
- **Signaling**: SDP (Session Description Protocol)
- **NAT Traversal**: ICE (STUN/TURN)

## Teknik Detaylar

### WebRTC Bağlantı Akışı

```
┌──────────┐              ┌──────────┐              ┌──────────┐
│  Peer A  │              │ Signaling│              │  Peer B  │
└─────┬────┘              └─────┬────┘              └─────┬────┘
      │                         │                         │
      │ 1. CreateOffer (SDP)    │                         │
      │────────────────────────>│                         │
      │                         │ 2. Offer forwarded      │
      │                         │────────────────────────>│
      │                         │                         │
      │                         │ 3. CreateAnswer (SDP)   │
      │                         │<────────────────────────│
      │ 4. Answer forwarded     │                         │
      │<────────────────────────│                         │
      │                         │                         │
      │ ═══ ICE Candidate Exchange ═══                    │
      │ 5. ICE candidates       │                         │
      │────────────────────────>│────────────────────────>│
      │ 6. ICE candidates       │                         │
      │<────────────────────────│<────────────────────────│
      │                         │                         │
      │ ═══ ICE Connectivity Check ═══                    │
      │ 7. STUN Binding Request │                         │
      │═════════════════════════════════════════════════>│
      │ 8. STUN Binding Response│                         │
      │<═════════════════════════════════════════════════│
      │                         │                         │
      │ ═══ DTLS Handshake ═══                           │
      │ 9. DTLS ClientHello     │                         │
      │════════════════════════════════════════════════>│
      │ 10. DTLS ServerHello    │                         │
      │<═══════════════════════════════════════════════│
      │                         │                         │
      │ ═══ SRTP Media Stream ═══                        │
      │ 11. Audio/Video RTP     │                         │
      │════════════════════════════════════════════════>│
```

### SDP (Session Description Protocol)

```
v=0
o=- 4611731400430051336 2 IN IP4 127.0.0.1
s=COREMUSIC Session
t=0 0
a=group:BUNDLE 0 1
a=msid-semantic: WMS COREMUSIC

m=audio 9 UDP/TLS/RTP/SAVPF 111
c=IN IP4 0.0.0.0
a=rtcp:9 IN IP4 0.0.0.0
a=ice-ufrag:coremusic123
a=ice-pwd:abcdef1234567890abcdef12
a=fingerprint:sha-256 AA:BB:CC:...
a=mid:0
a=extmap:1 urn:ietf:params:rtp-hdrext:ssrc-audio-level
a=sendrecv
a=msid:coremusic-stream audio
a=rtcp-mux
a=rtpmap:111 opus/48000/2
a=fmtp:111 minptime=10;useinbandfec=1
a=ssrc:1234567890 cname:coremusic@local
a=ssrc:1234567890 msid:coremusic-stream audio

m=application 9 UDP/DTLS/SCTP 5000
c=IN IP4 0.0.0.0
a=ice-ufrag:coremusic123
a=ice-pwd:abcdef1234567890abcdef12
a=fingerprint:sha-256 AA:BB:CC:...
a=mid:1
a=sctp-port:5000
a=max-message-size:65536
```

### ICE Candidate Tipleri

```
┌─────────────────────────────────────────────────────┐
│                ICE Candidate Types                   │
│                                                       │
│  Type 1: Host Candidate (lokal)                      │
│  ┌─────────────────────────────────────┐             │
│  │ a=candidate:1 1 UDP 2130706431     │             │
│  │   192.168.1.50 30000 typ host      │             │
│  └─────────────────────────────────────┘             │
│                                                       │
│  Type 2: Server Reflexive (STUN)                     │
│  ┌─────────────────────────────────────┐             │
│  │ a=candidate:2 1 UDP 1694498815     │             │
│  │   203.0.113.50 12345 typ srflx    │             │
│  │   raddr 192.168.1.50 rport 30000  │             │
│  └─────────────────────────────────────┘             │
│                                                       │
│  Type 3: Relay (TURN)                                │
│  ┌─────────────────────────────────────┐             │
│  │ a=candidate:3 1 UDP 894304767      │             │
│  │   198.51.100.1 45678 typ relay     │             │
│  │   raddr 203.0.113.50 rport 12345  │             │
│  └─────────────────────────────────────┘             │
└─────────────────────────────────────────────────────┘
```

### NAT Traversal Stratejisi

```
NAT Type Detection:

1. STUN Binding Request/Response
   → public IP:port öğrenme
   → NAT mapping hint (port behavior)

2. NAT Type Mapping:
   ┌─────────────────────────────────────┐
   │ NAT Type     │ Behavior             │
   ├─────────────────────────────────────┤
   │ Full Cone    │ Any external → host   │
   │ Restricted   │ Same external → host  │
   │ Port Rest.   │ Same port+ext → host  │
   │ Symmetric    │ Different per dest    │
   └─────────────────────────────────────┘

3. Traversal Decision:
   - Full Cone / Restricted Cone: Direct P2P
   - Port Restricted: Try P2P, fallback TURN
   - Symmetric: TURN relay
```

### DTLS-SRTP Akışı

```
1. DTLS Handshake (over UDP):
   ClientHello → ServerHello → Certificate →
   ServerKeyExchange → CertificateRequest →
   ClientCertificate → ClientKeyExchange →
   Finished

2. Key Material Extraction:
   srtp_key = DTLS-Exporter("EXTRACTOR-dtls_srtp", 0, 32)
   srtp_salt = DTLS-Exporter("EXTRACTOR-dtls_srtp", 1, 14)

3. SRTP Encryption (AES-128-CM + HMAC-SHA1-80):
   - Master key: srtp_key
   - Master salt: srtp_salt
   - Session key derivation per SSRC
   - Packet: [RTP Header][Encrypted Audio][Auth Tag]
```

### SCTP Data Channel

```
┌──────────────────────────────────────────────────┐
│              SCTP over DTLS                        │
│                                                    │
│  Channel Types:                                    │
│  ┌─────────────┬──────────────┬─────────────────┐ │
│  │ Reliable    │ Unreliable   │ Ordered         │ │
│  │ Ordered     │ Unordered    │ Unreliable      │ │
│  └─────────────┴──────────────┴─────────────────┘ │
│                                                    │
│  COREMUSIC Usage:                                  │
│  - "player-control": Reliable/Ordered              │
│    → Play, pause, seek, volume                    │
│  - "metadata-update": Reliable/Ordered            │
│    → Track info, album art                        │
│  - "position-sync": Unreliable/Unordered          │
│    → Real-time position updates (50ms)            │
│  - "room-state": Reliable/Unordered               │
│    → Room membership changes                      │
└──────────────────────────────────────────────────┘
```

### ICE Candidate Pair Prioritization

```
Priority Calculation:
priority = (2^32) * MIN(G,D) + 2 * MAX(G,D) + (G>D ? 1 : 0)

Where:
  G = 65535 - component-id
  D = preference value based on candidate type:
    Host:       126
    ServerRef:  100
    PeerRef:   110
    Relay:      0

Candidate Pair States:
  Waiting → In-Progress → Succeeded → Failed
```

## Konfigürasyon

```yaml
webrtc:
  enabled: true
  ice:
    stun_servers:
      - url: "stun:stun.coremusic.local:3478"
      - url: "stun:stun.l.google.com:19302"
    turn_servers:
      - url: "turn:turn.coremusic.local:3478"
        username: "coremusic"
        credential: "${TURN_CREDENTIAL}"
        credential_type: "password"
    ice_candidate_pool_size: 5
    ice_transport_policy: "all"         # all, relay, none
    bundle_policy: "max-bundle"
    rtcp_mux_policy: "require"
  dtls:
    enabled: true
    certificate_type: "ECDSA"
    signature_algorithms: ["ecdsa_p256_sha256"]
  srtp:
    enabled: true
    crypto_suite: "AES_CM_128_HMAC_SHA1_80"
  sctp:
    enabled: true
    port: 5000
    max_message_size: 65536
    max_retransmit_attempts: 10
  channels:
    player-control:
      ordered: true
      max_retransmits: null
    position-sync:
      ordered: false
      max_retransmits: 3
    room-state:
      ordered: true
      max_retransmits: 5
  codec:
    audio: "opus"
    sample_rate: 48000
    channels: 2
    bitrate: 128000
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | UDP/TCP soketleri |
| K14-TLS | Giren | DTLS handshaking |
| K6 | Çıkan | SRTP şifreleme |
| K8 | Çıkan | TURN server yönetimi |
| K14-WebSocket | Yatay | Signaling channel |

## Durum: Implementasyon

- [x] SDP creation/modification
- [x] ICE candidate gathering
- [x] ICE connectivity checks
- [x] STUN/TURN server integration
- [x] DTLS-SRTP handshake
- [x] Opus codec negotiation
- [x] SCTP data channels
- [x] NAT type detection
- [x] Candidate pair prioritization
- [x] Bundle/RTCP-mux
- [x] ICE restart handling
