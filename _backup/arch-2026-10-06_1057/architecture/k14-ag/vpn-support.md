---
title: "VPN Desteği & WireGuard Entegrasyonu"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# VPN Desteği & WireGuard Entegrasyonu

## Genel Bakış

COREMUSIC VPN desteği, uzaktan erişim ve güvenli tünel iletişimi için WireGuard tabanlı entegrasyon sağlar. Ev dışı ağlardan güvenli medya erişimi, çoklu site birleştirme ve şifreli peer-to-peer iletişim bu katman tarafından yönetilir. Minimal overhead ve yüksek performans ile real-time ses akışına uygun VPN altyapısı sunar.

## Protokol Detayı

- **Protokol**: WireGuard (IETF draft-irtf-curdle-wireguard)
- **Transport**: UDP port 51820
- **Şifreleme**: ChaCha20-Poly1305
- **Key Exchange**: Curve25519 (ECDH)
- **Kingerprinting**: BLAKE2s
- **Stateless Design**: Cookie-based DDoS koruması

## Teknik Detaylar

### WireGuard Tünel Mimarisi

```
┌──────────────────────────────────────────────────────────┐
│                WireGuard Tünel Mimarisi                   │
│                                                            │
│  ┌──────────────┐                    ┌──────────────┐     │
│  │   Client A   │                    │   Server     │     │
│  │   (wg0)      │                    │   (wg0)      │     │
│  │              │                    │              │     │
│  │  10.0.0.2/32 │═══UDP 51820══════>│  10.0.0.1/32 │     │
│  │              │<═══════════════════│              │     │
│  └──────┬───────┘                    └──────┬───────┘     │
│         │                                    │             │
│    ┌────┴────┐                         ┌────┴────┐        │
│    │ Tunnel  │                         │ Tunnel  │        │
│    │ Encrypt │                         │ Decrypt │        │
│    └────┬────┘                         └────┬────┘        │
│         │                                    │             │
│    ┌────┴────┐                         ┌────┴────┐        │
│    │  Real   │                         │  Real   │        │
│    │ Network │                         │ Network │        │
│    └─────────┘                         └─────────┘        │
│                                                            │
│  Handshake Flow:                                           │
│  1. Initiation (Type 1) → 2. Response (Type 2)            │
│  3. Cookie Reply (Type 3, DDoS) → 4. Data (Type 4)       │
└──────────────────────────────────────────────────────────┘
```

### WireGuard Paket Yapısı

```
Type 1 - Initiation:
+-------+-------+-------+-------+
| Type  |  S.I  |  T.I  | ...   |
| (1B)  | (4B)  | (4B)  |       |
+-------+-------+-------+-------+
| Encrypted Payload (112 bytes)  |
| [ ephemeral (32)              ]|
| [ encrypted_static (32)       ]|
| [ encrypted_timestamp (12)    ]|
| [ mac1 (16)                   ]|
| [ mac2 (16)                   ]|
+-------------------------------+

Type 2 - Response:
+-------+-------+-------+-------+
| Type  |  R.S  |  T.I  | ...   |
| (1B)  | (4B)  | (4B)  |       |
+-------+-------+-------+-------+
| Encrypted Payload (64 bytes)   |
| [ ephemeral (32)              ]|
| [ empty (16)                  ]|
| [ mac1 (16)                   ]|
| [ mac2 (16)                   ]|
+-------------------------------+

Type 4 - Data:
+-------------------------------+
| Counter (8 bytes)             |
+-------------------------------+
| Encrypted Packet              |
+-------------------------------+
```

### Key Exchange Akışı

```
┌──────────┐                          ┌──────────┐
│  Client   │                          │  Server   │
└─────┬────┘                          └─────┬────┘
      │                                      │
      │ Static Key Pair:                     │
      │ Private: sk_c (32 bytes)            │
      │ Public: pk_c (32 bytes)             │
      │                                      │
      │ Initiator ephemeral:                 │
      │ eph_sk_c (32 bytes)                 │
      │ eph_pk_c = Curve25519(eph_sk_c)     │
      │                                      │
      │ 1. Type 1 Initiation                │
      │    (eph_pk_c, Enc(sk_c, pk_s, ts))  │
      │─────────────────────────────────────>│
      │                                      │
      │ Responder ephemeral:                 │
      │ eph_sk_s (32 bytes)                 │
      │ eph_pk_s = Curve25519(eph_sk_s)     │
      │                                      │
      │ Shared secret:                       │
      │ SS1 = Curve25519(eph_sk_s, eph_pk_c)│
      │ SS2 = Curve25519(sk_s, eph_pk_c)    │
      │                                      │
      │ 2. Type 2 Response                   │
      │    (eph_pk_s, Enc(sk_s, pk_c, SS))  │
      │<─────────────────────────────────────│
      │                                      │
      │ Both sides derive:                   │
      │ Symmetric key = HKDF(SS1, SS2)      │
      │                                      │
```

### COREMUSIC VPN Kullanım Senaryoları

```
Senaryo 1: Ev Dışı Erişim
┌──────────┐    Internet    ┌──────────┐    LAN     ┌──────────┐
│  Client  │───WireGuard───>│  Router  │───Local───>│  Server  │
│  (Uzak)  │    Tunnel      │  (NAT)   │   Network  │  (CORE)  │
└──────────┘                └──────────┘            └──────────┘

Senaryo 2: Çoklu Site
┌──────────┐                ┌──────────┐                ┌──────────┐
│  Site A  │◄──WireGuard───►│  Site B  │◄──WireGuard───►│  Site C  │
│10.0.1.0/24│   Tunnel      │10.0.2.0/24│   Tunnel      │10.0.3.0/24│
└──────────┘                └──────────┘                └──────────┘

Senaryo 3: Cihazlar Arası P2P
┌──────────┐                ┌──────────┐
│  iPhone  │◄──WireGuard───>│  Speaker  │
│ 10.0.0.2 │    Direct      │ 10.0.0.1  │
└──────────┘                └──────────┘
```

### Split Tunneling

```python
class WireGuardManager:
    def __init__(self):
        self.config = WireGuardConfig()
        self.peers: dict[str, Peer] = {}

    def setup_split_tunnel(self, allowed_ips: list[str]):
        """Allow only specific traffic through tunnel"""
        self.config.peers[0].allowed_ips = allowed_ips

    def setup_full_tunnel(self):
        """Route all traffic through tunnel"""
        self.config.peers[0].allowed_ips = ["0.0.0.0/0", "::/0"]

    def setup_exclude_tunnel(self, exclude_ips: list[str]):
        """Exclude specific IPs from tunnel"""
        # Use more specific routes for excluded IPs
        self.config.peers[0].allowed_ips = ["0.0.0.0/0"]
        for ip in exclude_ips:
            self.config.routing.add_exclusion(ip)
```

### Performance Monitoring

```python
class WireGuardMonitor:
    def get_stats(self) -> dict:
        return {
            "interface": self.interface_name,
            "rx_bytes": self.rx_bytes,
            "tx_bytes": self.tx_bytes,
            "rx_packets": self.rx_packets,
            "tx_packets": self.tx_packets,
            "latest_handshake": self.latest_handshake,
            "transfer_speed": self.calculate_speed(),
            "latency": self.measure_latency(),
            "peers": self.get_peer_stats()
        }

    def measure_latency(self) -> float:
        """RTT measurement via handshake timing"""
        start = time.time()
        self._send_handshake_initiation()
        # Wait for response with timeout
        return (time.time() - start) * 1000  # ms
```

### DDoS Koruması (Cookie Mechanism)

```
1. Initial Request (Type 1) → Cookie Reply (Type 3)
   Server sends cookie in Type 3
   Cookie = HMAC(cookie_secret, IP:port)

2. Subsequent Request (Type 1 with mac2)
   Client includes cookie in mac2 field
   Server validates: HMAC(cookie_secret, IP:port) == cookie

3. Rate Limiting:
   - Max 1 handshake/second per IP
   - Burst allowance: 5
   - Backoff: exponential on failure
```

## Konfigürasyon

```yaml
vpn:
  wireguard:
    enabled: true
    interface: "wg0"
    listen_port: 51820
    private_key: "${WG_PRIVATE_KEY}"
    address:
      - "10.0.0.1/32"
    dns:
      - "10.0.0.1"
    mtu: 1420
    peers:
      - public_key: "${PEER_PUBLIC_KEY}"
        allowed_ips:
          - "10.0.0.2/32"
        endpoint: "remote.coremusic.local:51820"
        persistent_keepalive: 25
    routing:
      enabled: true
      table: "auto"
      pre_up: []
      post_up:
        - "iptables -A FORWARD -i wg0 -j ACCEPT"
        - "iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE"
      pre_down:
        - "iptables -D FORWARD -i wg0 -j ACCEPT"
      post_down: []
    performance:
      congestion_control: "cubic"
      buffer_size: 1048576           # 1MB
      socket_buffer: 212992          # 208KB
    health_check:
      enabled: true
      interval: 30000                # ms
      timeout: 5000
      failure_threshold: 3
    logging:
      level: "info"
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | UDP soket yönetimi |
| K1 | Giren | NIC performansı |
| K6 | Çıkan | Key exchange güvenliği |
| K14-DNS | Çıkan | Peer hostname çözümleme |

## Durum: Implementasyon

- [x] WireGuard interface setup
- [x] Key pair generation (Curve25519)
- [x] Handshake initiation/response
- [x] ChaCha20-Poly1305 encryption
- [x] Split tunneling
- [x] Full tunneling
- [x] Peer management
- [x] Cookie-based DDoS protection
- [x] Performance monitoring
- [x] Health checking
- [x] Auto-reconnect
- [x] DNS leak prevention
