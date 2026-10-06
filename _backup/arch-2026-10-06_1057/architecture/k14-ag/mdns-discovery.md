---
title: "mDNS Service Discovery"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# mDNS Service Discovery

## Genel Bakış

mDNS (Multicast DNS), COREMUSIC'in yerel ağ üzerindeki cihaz ve servis keşfinin temel mekanizmasıdır. Bonjour (Apple) ve Avahi (Linux) ile uyumlu çalışarak, DNS yapısına ihtiyaç duymadan `.local` domain üzerinde name resolution sağlar. UPnP/DLNA, AirPlay ve yerel API keşfi bu servise bağlıdır.

## Protokol Detayı

- **RFC**: 6762 (mDNS), 6763 (DNS-SD)
- **Transport**: UDP port 5353
- **Multicast Adresi**: 224.0.0.251 (IPv4), ff02::fb (IPv6)
- **TTL**: 120 saniye (varsayılan)
- **Cache Flush**: Unique records 0.5s, shared records vary

## Teknik Detaylar

### mDNS Mesaj Yapısı

```
+-------------------------------------+
|         DNS Header (12 bytes)        |
|  ID | Flags | QDCOUNT | ANCOUNT     |
|  NSCOUNT | ARCOUNT                   |
+-------------------------------------+
|         Questions Section            |
|  QNAME | QTYPE | QCLASS             |
+-------------------------------------+
|         Answers Section              |
|  NAME | TYPE | CLASS | TTL | RDATA   |
+-------------------------------------+
|         Authority Section            |
+-------------------------------------+
|         Additional Section           |
+-------------------------------------+
```

### DNS-SD Resource Record Tipleri

| Record | Tür | Örnek |
|--------|-----|-------|
| SRV | Service | `_music._tcp.local` |
| TXT | Metadata | `path=/api/v1` |
| A | IPv4 | `192.168.1.100` |
| AAAA | IPv6 | `fe80::1` |
| PTR | Pointer | `_music._tcp.local` → instance |

### COREMUSIC Service Registration

```
Service: _music._tcp.local
Instance: "Living Room Speaker"
TXT Records:
  - version=2.0
  - path=/api/v1
  - ws-port=8443
  - http-port=8080
  - capabilities=airplay,dlna,web
  - room=living-room
  - max-volume=100
  - current-volume=75
  - status=playing
  - track=Bohemian+Rhapsody
```

### Discovery Akışı

```
┌──────────┐                          ┌──────────┐
│  Client   │                          │  Server   │
└─────┬────┘                          └─────┬────┘
      │                                      │
      │ 1. mDNS Query (unicast)              │
      │    _music._tcp.local                 │
      │─────────────────────────────────────>│
      │                                      │
      │ 2. mDNS Response                     │
      │    + A/AAAA records                  │
      │    + SRV record                      │
      │    + TXT records                     │
      │<─────────────────────────────────────│
      │                                      │
      │ 3. Unicast Probe (TTL=0)            │
      │─────────────────────────────────────>│
      │                                      │
      │ 4. Announce (multicast)              │
      │─────────────────────────────────────>│
      │                                      │
```

### Probing Mekanizması

```
mDNS Probing State Machine:

┌────────────┐     startup     ┌────────────┐
│  Probing    │───────────────>│  Probing    │
│  (Initial)  │                │  (Announce) │
└─────┬──────┘                └─────┬──────┘
      │                             │
      │ Unique conflict             │ Conflict
      │ detected                    │ detected
      │                             │
      v                             v
┌────────────┐                ┌────────────┐
│  Defending  │                │  Renaming  │
│  (Active)   │                │  (Retry)   │
└─────┬──────┘                └─────┬──────┘
      │                             │
      │ Record removed              │ Unique
      │                             │ available
      v                             v
┌────────────┐                ┌────────────┐
│  Finalize   │                │  Back to   │
│  (Cleanup)  │                │  Probing   │
└────────────┘                └────────────┘
```

### Cache Yönetimi

```python
class MDNSCache:
    def __init__(self):
        self.records: dict[str, list[CacheEntry]] = {}
        self.flush_timer = None

    def add(self, record: DNSRecord):
        key = self._key(record)
        if key not in self.records:
            self.records[key] = []

        # Unique record cache flush
        if record.rtype in ('A', 'AAAA', 'SRV'):
            self.records[key] = [CacheEntry(record, ttl=record.ttl)]
        else:
            self.records[key].append(CacheEntry(record, ttl=record.ttl))

    def query(self, service_type: str) -> list[DNSRecord]:
        results = []
        for key, entries in self.records.items():
            if service_type in key:
                for entry in entries:
                    if not entry.is_expired():
                        results.append(entry.record)
        return results

    def purge_expired(self):
        for key in list(self.records.keys()):
            self.records[key] = [
                e for e in self.records[key] if not e.is_expired()
            ]
            if not self.records[key]:
                del self.records[key]
```

### Multicast Grup Yönetimi

```yaml
multicast_config:
  ipv4:
    address: "224.0.0.251"
    port: 5353
    interface: "0.0.0.0"
    ttl: 255
  ipv6:
    address: "ff02::fb"
    port: 5353
    interface: "all"
    hop_limit: 255
  groups:
    - name: "_music._tcp.local"
      description: "COREMUSIC Audio Services"
    - name: "_airplay._tcp.local"
      description: "AirPlay Services"
    - name: "_raop._tcp.local"
      description: "Remote Audio Output Protocol"
    - name: "_http._tcp.local"
      description: "HTTP Services"
```

### Anti-Conflict Stratejisi

| Senaryo | Çözüm |
|---------|-------|
| Aynı isimde iki cihaz | Automatic rename: "Speaker (2)" |
| Network partition | TTL-based expiry + re-probe |
| IP değişikliği | A/AAAA record flush + re-announce |
| Cihaz kapatma | Goodbye packet (TTL=0) |
| Rate limiting | Maximum 1 query/second per type |

## Konfigürasyon

```yaml
mdns:
  enabled: true
  domain: "local"
  interface: "0.0.0.0"
  port: 5353
  ttl: 120
  probe_timeout: 250
  announce_interval: 600000              # 10 dakika
  query_interval: 900000                 # 15 dakika
  cache:
    max_entries: 1000
    purge_interval: 60000                # 1 dakika
  services:
    - name: "music"
      type: "_music._tcp"
      port: 8080
      txt_records:
        version: "2.0"
        path: "/api/v1"
  logging:
    enabled: false
    level: "info"
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | UDP multicast soketleri |
| K1 | Giren | NIC multicast desteği |
| K8 | Çıkan | Servis kaydı |
| K14-UPnP | Yatay | UPnP keşfi ile uyumluluk |

## Durum: Implementasyon

- [x] mDNS query/response
- [x] DNS-SD service registration
- [x] Probing ve announcement
- [x] Cache yönetimi
- [x] TTL-based expiry
- [x] Goodbye packet
- [x] IPv4/IPv6 dual-stack
- [x] Multicast grup yönetimi
- [x] Anti-conflict renaming
- [x] Bonjour/Avahi uyumluluğu
