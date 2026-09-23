---
title: "Network QoS & DSCP Marking"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# Network QoS & DSCP Marking

## Genel Bakış

COREMUSIC QoS katmanı, real-time ses akışının network seviyesinde önceliklendirilmesini sağlar. DSCP (Differentiated Services Code Point) marking, traffic shaping ve queue management ile gecikme hassasiyeti olan ses trafiğinin garanti altına alınır. Ağ kalite metrikleri izlenerek dinamik optimizasyon yapılır.

## Protokol Detayı

- **RFC**: 2474 (DSCP), 2475 (DiffServ), 3246 (EF PHB)
- **DSCP Values**: EF (46), AF41 (34), AF21 (18), CS0 (0)
- **Queue Management**: FIFO, Priority Queuing, WFQ
- **Traffic Shaping**: Token Bucket, Leaky Bucket
- **Monitoring**: RTT, jitter, packet loss, throughput

## Teknik Detaylar

### DSCP Code Point Tablosu

```
┌──────────────────────────────────────────────────────────────┐
│                    DSCP Code Point Mapping                    │
│                                                                │
│  DSCP Name  │ Decimal │ Binary    │ Per-Hop Behavior           │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  EF         │ 46      │ 101110    │ Expedited Forwarding       │
│             │         │           │ Real-time audio stream     │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  AF41       │ 34      │ 100010    │ Assured Forwarding 4.1     │
│             │         │           │ Now-playing metadata       │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  AF42       │ 36      │ 100100    │ Assured Forwarding 4.2     │
│             │         │           │ Player control commands    │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  AF31       │ 26      │ 011010    │ Assured Forwarding 3.1     │
│             │         │           │ Room sync traffic          │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  AF21       │ 18      │ 010010    │ Assured Forwarding 2.1     │
│             │         │           │ Playlist/library sync      │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  AF11       │ 10      │ 001010    │ Assured Forwarding 1.1     │
│             │         │           │ Background updates         │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  CS0        │ 0       │ 000000    │ Best Effort                │
│             │         │           │ Default traffic            │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  CS6        │ 48      │ 110000    │ Network Control           │
│             │         │           │ BGP, OSPF                  │
│  ───────────┼─────────┼───────────┼────────────────────────── │
│  CS7        │ 56      │ 111000    │ Network Control           │
│             │         │           │ STP, CDP                   │
└──────────────────────────────────────────────────────────────┘
```

### Traffic Classification

```
┌──────────────────────────────────────────────────────────────┐
│              Traffic Classification Pipeline                  │
│                                                                │
│  Incoming Packet                                               │
│        │                                                       │
│        ▼                                                       │
│  ┌────────────────────────────────────┐                      │
│  │ 1. Protocol Classification         │                      │
│  │    └── Port, Protocol, Direction  │                      │
│  └───────────────────┬────────────────┘                      │
│                      │                                        │
│        ▼             ▼                                        │
│  ┌────────────────────────────────────┐                      │
│  │ 2. Application Classification     │                      │
│  │    └── URL, Content-Type, Host    │                      │
│  └───────────────────┬────────────────┘                      │
│                      │                                        │
│        ▼             ▼                                        │
│  ┌────────────────────────────────────┐                      │
│  │ 3. DSCP Marking                    │                      │
│  │    └── Set DSCP value             │                      │
│  └───────────────────┬────────────────┘                      │
│                      │                                        │
│        ▼             ▼                                        │
│  ┌────────────────────────────────────┐                      │
│  │ 4. Queue Assignment                │                      │
│  │    └── Priority queue selection   │                      │
│  └────────────────────────────────────┘                      │
└──────────────────────────────────────────────────────────────┘
```

### Traffic Shaping (Token Bucket)

```python
import time

class TokenBucket:
    def __init__(self, rate: float, capacity: int):
        self.rate = rate              # tokens per second
        self.capacity = capacity      # max tokens
        self.tokens = capacity
        self.last_refill = time.time()

    def consume(self, tokens: int) -> bool:
        self._refill()
        if self.tokens >= tokens:
            self.tokens -= tokens
            return True
        return False

    def _refill(self):
        now = time.time()
        elapsed = now - self.last_refill
        self.tokens = min(self.capacity, self.tokens + elapsed * self.rate)
        self.last_refill = now

class TrafficShaper:
    def __init__(self):
        self.buckets = {
            "audio_stream": TokenBucket(rate=1_500_000, capacity=1_500_000),   # 1.5 Mbps
            "sync_channel": TokenBucket(rate=50_000, capacity=100_000),       # 50 Kbps
            "metadata": TokenBucket(rate=100_000, capacity=200_000),          # 100 Kbps
            "default": TokenBucket(rate=500_000, capacity=1_000_000),         # 500 Kbps
        }

    def shape(self, traffic_class: str, packet_size: int) -> bool:
        bucket = self.buckets.get(traffic_class, self.buckets["default"])
        return bucket.consume(packet_size)
```

### Queue Management

```python
from collections import deque
from enum import Enum

class TrafficPriority(Enum):
    CRITICAL = 0      # EF - Audio stream
    HIGH = 1          # AF41 - Now playing, control
    MEDIUM = 2        # AF31 - Sync, AF21 - Playlist
    LOW = 3           # AF11 - Background
    BEST_EFFORT = 4   # CS0 - Default

class PriorityQueueManager:
    def __init__(self, max_queue_size: int = 10000):
        self.queues = {
            TrafficPriority.CRITICAL: deque(maxlen=max_queue_size),
            TrafficPriority.HIGH: deque(maxlen=max_queue_size),
            TrafficPriority.MEDIUM: deque(maxlen=max_queue_size),
            TrafficPriority.LOW: deque(maxlen=max_queue_size),
            TrafficPriority.BEST_EFFORT: deque(maxlen=max_queue_size),
        }

    def enqueue(self, packet, priority: TrafficPriority):
        queue = self.queues[priority]
        if len(queue) < queue.maxlen:
            queue.append(packet)
        else:
            # Drop lower priority packets
            self._drop_preemption(packet, priority)

    def dequeue(self):
        for priority in TrafficPriority:
            if self.queues[priority]:
                return self.queues[priority].popleft()
        return None

    def _drop_preemption(self, new_packet, min_priority: TrafficPriority):
        """Drop lower priority packets to make room"""
        for priority in reversed(list(TrafficPriority)):
            if priority.value > min_priority.value and self.queues[priority]:
                self.queues[priority].popleft()
                self.queues[min_priority].append(new_packet)
                return
```

### QoS Monitoring

```python
import statistics

class QoSMonitor:
    def __init__(self, window_size: int = 100):
        self.window_size = window_size
        self.rtt_samples: list[float] = []
        self.jitter_samples: list[float] = []
        self.packet_loss: dict[str, int] = {}
        self.throughput: dict[str, float] = {}

    def record_rtt(self, sample_ms: float):
        self.rtt_samples.append(sample_ms)
        if len(self.rtt_samples) > self.window_size:
            self.rtt_samples.pop(0)

    def record_jitter(self, sample_ms: float):
        self.jitter_samples.append(sample_ms)
        if len(self.jitter_samples) > self.window_size:
            self.jitter_samples.pop(0)

    def get_metrics(self) -> dict:
        return {
            "rtt_avg": statistics.mean(self.rtt_samples) if self.rtt_samples else 0,
            "rtt_p95": self._percentile(self.rtt_samples, 95),
            "rtt_p99": self._percentile(self.rtt_samples, 99),
            "jitter_avg": statistics.mean(self.jitter_samples) if self.jitter_samples else 0,
            "jitter_p95": self._percentile(self.jitter_samples, 95),
            "packet_loss_rate": self._calculate_loss_rate(),
            "total_packets_sent": sum(self.packet_loss.values()),
        }

    def _percentile(self, data: list, pct: float) -> float:
        if not data:
            return 0
        sorted_data = sorted(data)
        index = int(len(sorted_data) * pct / 100)
        return sorted_data[min(index, len(sorted_data) - 1)]

    def _calculate_loss_rate(self) -> float:
        total_sent = sum(self.packet_loss.values())
        if total_sent == 0:
            return 0
        lost = sum(1 for v in self.packet_loss.values() if v > 0)
        return lost / total_sent
```

### Adaptive Bitrate

```python
class AdaptiveBitrate:
    def __init__(self):
        self.current_bitrate = 1_500_000  # 1.5 Mbps
        self.min_bitrate = 320_000         # 320 Kbps
        self.max_bitrate = 3_000_000       # 3 Mbps
        self.steps = [320_000, 640_000, 960_000, 1_500_000, 2_400_000, 3_000_000]

    def adjust(self, metrics: dict) -> int:
        rtt_p95 = metrics["rtt_p95"]
        jitter_p95 = metrics["jitter_p95"]
        loss_rate = metrics["packet_loss_rate"]

        # Decision matrix
        if loss_rate > 0.05 or rtt_p95 > 200:
            # Degrade aggressively
            self.current_bitrate = max(self.min_bitrate,
                self.steps[max(0, self.steps.index(self.current_bitrate) - 2)])
        elif loss_rate > 0.01 or rtt_p95 > 100 or jitter_p95 > 30:
            # Degrade moderately
            self.current_bitrate = max(self.min_bitrate,
                self.steps[max(0, self.steps.index(self.current_bitrate) - 1)])
        elif loss_rate < 0.001 and rtt_p95 < 50 and jitter_p95 < 5:
            # Try to upgrade
            self.current_bitrate = min(self.max_bitrate,
                self.steps[min(len(self.steps) - 1,
                    self.steps.index(self.current_bitrate) + 1)])

        return self.current_bitrate
```

### Linux Traffic Control Integration

```bash
# Egress shaping
tc qdisc add dev eth0 root handle 1: htb default 10

# Audio stream - highest priority
tc class add dev eth0 parent 1: classid 1:1 htb rate 5mbit ceil 10mbit
tc class add dev eth0 parent 1:1 classid 1:10 htb rate 2mbit ceil 5mbit prio 0

# Control traffic
tc class add dev eth0 parent 1:1 classid 1:20 htb rate 1mbit ceil 2mbit prio 1

# Sync traffic
tc class add dev eth0 parent 1:1 classid 1:30 htb rate 512kbit ceil 1mbit prio 2

# Default traffic
tc class add dev eth0 parent 1:1 classid 1:40 htb rate 256kbit ceil 512kbit prio 3

# DSCP marking
tc filter add dev eth0 parent 1: protocol ip prio 1 u32 \
    match ipdscp 46 0xfc flowid 1:10

tc filter add dev eth0 parent 1: protocol ip prio 1 u32 \
    match ipdscp 34 0xfc flowid 1:20

tc filter add dev eth0 parent 1: protocol ip prio 1 u32 \
    match ipdscp 18 0xfc flowid 1:30
```

## Konfigürasyon

```yaml
qos:
  enabled: true
  dscp_marking:
    enabled: true
    audio_stream: 46              # EF
    now_playing: 34               # AF41
    control: 36                   # AF42
    sync: 26                      # AF31
    playlist: 18                  # AF21
    background: 10                # AF11
    default: 0                    # CS0
  traffic_shaping:
    enabled: true
    algorithm: "token_bucket"
    rates:
      audio_stream: 1500000       # 1.5 Mbps
      sync_channel: 50000         # 50 Kbps
      metadata: 100000            # 100 Kbps
      default: 500000             # 500 Kbps
  queues:
    max_size: 10000
    drop_policy: "tail"
    wred:
      enabled: true
      min_threshold: 2000
      max_threshold: 8000
      drop_probability: 0.1
  monitoring:
    enabled: true
    interval: 1000                # ms
    window_size: 100
    alerts:
      rtt_threshold_ms: 100
      jitter_threshold_ms: 20
      packet_loss_threshold: 0.01
  adaptive_bitrate:
    enabled: true
    check_interval: 5000          # ms
    min_bitrate: 320000
    max_bitrate: 3000000
  buffer_bloat:
    enabled: true
    fq_codel:
      target_ms: 5
      interval_ms: 100
      quantum: 1514
      limit: 10240
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | Network I/O, socket options |
| K1 | Giren | NIC queue offloading |
| K14-LB | Yatay | QoS-aware routing |
| K3 | Çıkan | Bitrate adaptasyonu |

## Durum: Implementasyon

- [x] DSCP marking (EF, AF41, AF21, CS0)
- [x] Traffic classification
- [x] Token bucket shaping
- [x] Priority queue management
- [x] QoS monitoring (RTT, jitter, loss)
- [x] Adaptive bitrate control
- [x] Buffer bloat mitigation (FQ-CoDel)
- [x] Linux TC integration
- [x] Per-flow statistics
- [x] Alert system
- [x] Dynamic DSCP re-marking
- [x] Traffic policing
