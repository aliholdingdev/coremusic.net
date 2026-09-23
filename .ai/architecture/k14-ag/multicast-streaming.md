---
title: "Multicast Ses Akışı"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# Multicast Ses Akışı

## Genel Bakış

Multicast streaming, COREMUSIC'in çoklu oda senkronizasyonunda bant genişliği optimizasyonu sağlayan protokoldür. Tek bir kaynaktan birden fazla alıcıya aynı anda ses verisi göndererek network overhead'ini minimize eder. IGMP join/leave, PIM-SMdense mode ve UDP multicast ile verimli medya dağıtımı sunar.

## Protokol Detayı

- **Transport**: UDP multicast
- **Multicast Adres Aralığı**: 239.0.0.0/8 (org-scoped)
- **Varsayılan Adres**: 239.1.1.1 (site-local)
- **Port**: 5004 (RTP), 5005 (RTCP)
- **IGMP Version**: IGMPv3
- **TTL**: 32 (site-local, LAN)

## Teknik Detaylar

### Multicast Mimarisi

```
┌──────────────────────────────────────────────────────────┐
│              Multicast Audio Distribution                  │
│                                                            │
│  ┌──────────┐                                             │
│  │  Source   │  RTP stream → 239.1.1.1:5004               │
│  │ (Master) │                                             │
│  └─────┬────┘                                             │
│        │                                                   │
│  ┌─────┴─────────────────────────────────────────────┐   │
│  │              Network Switch                        │   │
│  │         (IGMP Snooping enabled)                   │   │
│  └──┬──────────┬──────────┬──────────┬───────────────┘   │
│     │          │          │          │                     │
│  ┌──┴──┐   ┌──┴──┐   ┌──┴──┐   ┌──┴──┐                 │
│  │Room1│   │Room2│   │Room3│   │Room4│                  │
│  │Room5│   │Room6│   │Room7│   │Room8│                  │
│  │Room9│   │Room10│  │Room11│  │Room12│                  │
│  └─────┘   └─────┘   └─────┘   └─────┘                  │
│                                                            │
│  Bandwidth:                                                │
│  Unicast x12: 12 × 1.5Mbps = 18 Mbps                    │
│  Multicast:    1 × 1.5Mbps = 1.5 Mbps                   │
│  Savings: 91.7%                                           │
└──────────────────────────────────────────────────────────┘
```

### IGMP Join/Leave Akışı

```
Room Joined:
┌──────────┐     IGMPv3 Join      ┌──────────┐
│  Speaker  │ ──────────────────> │  Router   │
│ (Receiver) │  Group: 239.1.1.1  │ (Querier) │
└──────────┘                      └──────────┘
                                       │
                              Forward joined
                                       │
                                       v
                              ┌──────────────┐
                              │    Source     │
                              │  Stream      │
                              └──────┬───────┘
                                     │
                            Forward to port
                                     │
                              ┌──────┴───────┐
                              │   Speaker    │
                              └──────────────┘

Room Left:
┌──────────┐    IGMPv3 Leave     ┌──────────┐
│  Speaker  │ ─────────────────> │  Router   │
│ (Receiver) │ Group: 239.1.1.1  │ (Querier) │
└──────────┘                     └──────────┘
                                    │
                           Stop forwarding
                           (if no other
                            receivers)
```

### RTP Packet Structure

```
 0                   1                   2                   3
 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|V=2|P|X|  CC   |M|     PT      |       sequence number         |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|                           timestamp                           |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|           synchronization source (SSRC) identifier            |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|            contributing source (CSRC) identifiers             |
|                             ....                              |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
|                          audio data                           |
|                             ....                              |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
```

### Multicast Sender Implementation

```python
import socket
import struct
import time

class MulticastSender:
    def __init__(self, multicast_addr: str = "239.1.1.1", port: int = 5004):
        self.multicast_addr = multicast_addr
        self.port = port
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM, socket.IPPROTO_UDP)
        self.sock.setsockopt(socket.IPPROTO_IP, socket.IP_MULTICAST_TTL, 32)
        self.sock.setsockopt(socket.IPPROTO_IP, socket.IP_MULTICAST_IF,
                             socket.inet_aton("0.0.0.0"))
        self.sequence_number = 0
        self.timestamp = 0
        self.ssrc = self._generate_ssrc()

    def send_audio_frame(self, audio_data: bytes, sample_rate: int = 44100):
        # RTP header
        header = self._build_rtp_header()
        # Send packet
        packet = header + audio_data
        self.sock.sendto(packet, (self.multicast_addr, self.port))
        self.sequence_number = (self.sequence_number + 1) % 65536
        self.timestamp = (self.timestamp + 352) % (2**32)  # 352 samples per frame

    def _build_rtp_header(self) -> bytes:
        version = 2
        padding = 0
        extension = 0
        csrc_count = 0
        marker = 0
        payload_type = 97  # Dynamic PT for custom audio

        first_byte = (version << 6) | (padding << 5) | (extension << 4) | csrc_count
        second_byte = (marker << 7) | payload_type

        header = struct.pack('!BBHII',
            first_byte,
            second_byte,
            self.sequence_number,
            self.timestamp,
            self.ssrc
        )
        return header

    def _generate_ssrc(self) -> int:
        import random
        return random.randint(0, 2**32 - 1)
```

### Multicast Receiver Implementation

```python
class MulticastReceiver:
    def __init__(self, multicast_addr: str = "239.1.1.1", port: int = 5004):
        self.multicast_addr = multicast_addr
        self.port = port
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM, socket.IPPROTO_UDP)
        self.sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        self.sock.bind(('', port))

        # Join multicast group
        mreq = struct.pack('4s4s',
            socket.inet_aton(multicast_addr),
            socket.inet_aton('0.0.0.0')
        )
        self.sock.setsockopt(socket.IPPROTO_IP, socket.IP_ADD_MEMBERSHIP, mreq)
        self.sock.setsockopt(socket.IPPROTO_IP, socket.IP_MULTICAST_TTL, 32)

    def receive_audio_frame(self, buffer_size: int = 4096) -> tuple[bytes, dict]:
        data, addr = self.sock.recvfrom(buffer_size)
        # Parse RTP header
        header_info = self._parse_rtp_header(data[:12])
        audio_data = data[12:]
        return audio_data, header_info

    def _parse_rtp_header(self, header: bytes) -> dict:
        first_byte, second_byte, seq, ts, ssrc = struct.unpack('!BBHII', header)
        return {
            "version": (first_byte >> 6) & 0x03,
            "padding": (first_byte >> 5) & 0x01,
            "extension": (first_byte >> 4) & 0x01,
            "csrc_count": first_byte & 0x0F,
            "marker": (second_byte >> 7) & 0x01,
            "payload_type": second_byte & 0x7F,
            "sequence_number": seq,
            "timestamp": ts,
            "ssrc": ssrc,
        }

    def leave_group(self):
        mreq = struct.pack('4s4s',
            socket.inet_aton(self.multicast_addr),
            socket.inet_aton('0.0.0.0')
        )
        self.sock.setsockopt(socket.IPPROTO_IP, socket.IP_DROP_MEMBERSHIP, mreq)
        self.sock.close()
```

### Sync Protocol over Multicast

```
┌──────────────────────────────────────────────────────────┐
│              Multicast Sync Protocol                      │
│                                                            │
│  Master → Multicast (239.1.1.2:5006):                    │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ Message Type: SYNC_BEACON                            │ │
│  │ Sequence: 12345                                      │ │
│  │ Master Timestamp: 1726834800000000 (μs)              │ │
│  │ Current Track Position: 127000 (ms)                  │ │
│  │ Sample Rate: 44100                                   │ │
│  │ Expected Next Sample: 56107200                       │ │
│  └──────────────────────────────────────────────────────┘ │
│                                                            │
│  Slaves → Unicast ACK:                                   │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ Message Type: SYNC_ACK                               │ │
│  │ Sequence: 12345                                      │ │
│  │ Slave Timestamp: 1726834800000500 (μs)               │ │
│  │ Drift: +500 μs (compensate)                         │ │
│  │ Buffer Level: 45%                                    │ │
│  │ Status: SYNCED                                       │ │
│  └──────────────────────────────────────────────────────┘ │
│                                                            │
│  Drift Compensation Algorithm:                            │
│  - Acceptable drift: ±2ms                                │
│  - Drift > 2ms: Pitch shift ±0.1%                       │
│  - Drift > 10ms: Buffer adjustment (add/remove samples)  │
│  - Drift > 50ms: Emergency resync                        │
└──────────────────────────────────────────────────────────┘
```

### IGMP Snooping Optimizasyonu

```yaml
switch_config:
  igmp_snooping:
    enabled: true
    version: 3
    querier:
      enabled: true
      address: "192.168.1.1"
      interval: 125                  # seconds
      robustness: 2
    fast_leave:
      enabled: true
      immediate: true
    groups:
      "239.1.1.1":
        description: "COREMUSIC Audio Stream"
        ports: [1, 2, 3, 4, 5, 6]
      "239.1.1.2":
        description: "COREMUSIC Sync Channel"
        ports: [1, 2, 3, 4, 5, 6]
```

## Konfigürasyon

```yaml
multicast:
  enabled: true
  audio:
    address: "239.1.1.1"
    port: 5004
    ttl: 32
    codec: "ALAC"
    sample_rate: 44100
    bit_depth: 24
    channels: 2
    bitrate: 1536000
    frame_size: 352
  sync:
    address: "239.1.1.2"
    port: 5006
    beacon_interval: 1000           # ms
    drift_tolerance_ms: 2
    max_compensation_ms: 50
  igmp:
    version: 3
    join_timeout: 10000             # ms
    leave_timeout: 5000             # ms
  network:
    interface: "0.0.0.0"
    receive_buffer: 2097152         # 2MB
    send_buffer: 1048576            # 1MB
    dscp: "EF"
  fallback:
    enabled: true
    method: "unicast"
    trigger: "multicast_failure"
  monitoring:
    enabled: true
    stats_interval: 10000           # ms
    packet_loss_threshold: 0.01     # %1
    jitter_threshold: 5             # ms
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | UDP multicast soketleri |
| K1 | Giren | NIC multicast desteği |
| K3 | Çıkan | Ses codec çözümleme |
| K8 | Çıkan | Oda üyelik yönetimi |
| K14-QoS | Çıkan | DSCP marking |

## Durum: Implementasyon

- [x] UDP multicast sender/receiver
- [x] RTP packet construction
- [x] IGMPv3 join/leave
- [x] Multicast sync protocol
- [x] Drift compensation
- [x] Packet loss detection
- [x] Fallback to unicast
- [x] IGMP snooping support
- [x] DSCP marking
- [x] Performance monitoring
- [x] Multi-room sync
- [x] Buffer management
