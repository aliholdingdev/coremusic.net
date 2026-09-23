---
title: "DNS Çözümleyici"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# DNS Çözümleyici

## Genel Bakış

COREMUSIC DNS çözümleyici, tüm domain name resolution işlemlerini merkezi olarak yönetir. Custom DNS sunucu desteği, DNS-over-HTTPS (DoH), DNS-over-TLS (DoT) ve yerel DNS önbellek ile hızlı ve güvenli çözümleme sağlar. mDNS ile `.local` domain desteği yerel ağ keşfini tamamlar.

## Protokol Detayı

- **RFC**: 1034/1035 (DNS), 8484 (DoH), 7858 (DoT)
- **Transport**: UDP port 53 (klasik), TCP port 53 (büyük yanıt), TCP port 443 (DoH), TCP port 853 (DoT)
- **Önbellek TTL**: Dinamik, record tipine göre
- **Retry Policy**: Exponential backoff (1s, 2s, 4s, 8s)

## Teknik Detaylar

### DNS Message Format

```
+-------------------------------------+
|           Header (12 bytes)          |
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
|  NAME | TYPE | CLASS | TTL | RDATA   |
+-------------------------------------+
|         Additional Section           |
+-------------------------------------+
```

### DNS Record Tipleri

| Record | Kod | Açıklama |
|--------|-----|----------|
| A | 1 | IPv4 address |
| AAAA | 28 | IPv6 address |
| CNAME | 5 | Canonical name |
| MX | 15 | Mail exchange |
| NS | 2 | Name server |
| PTR | 12 | Pointer (reverse) |
| SOA | 6 | Start of authority |
| SRV | 33 | Service location |
| TXT | 16 | Text strings |
| CAA | 257 | Certification authority |

### DNS Resolution Akışı

```
┌─────────────────────────────────────────────────────┐
│              DNS Resolution Pipeline                 │
│                                                       │
│  1. Local Cache Check                                │
│     ├── HIT → Return (TTL > 0)                      │
│     └── MISS → Continue                              │
│                                                       │
│  2. Hosts File Check (/etc/hosts)                    │
│     ├── FOUND → Return                               │
│     └── NOT FOUND → Continue                         │
│                                                       │
│  3. mDNS Resolution (.local domains)                 │
│     ├── FOUND → Return                               │
│     └── NOT FOUND → Continue                         │
│                                                       │
│  4. Recursive Resolution                             │
│     ├── Root DNS (.)                                 │
│     │   └── → TLD servers (.com, .net, etc)         │
│     ├── TLD DNS                                      │
│     │   └── → Authoritative DNS                     │
│     └── Authoritative DNS                            │
│         └── → Final IP address                       │
│                                                       │
│  5. Cache Update (TTL-based expiry)                  │
│                                                       │
│  6. Return Result                                    │
└─────────────────────────────────────────────────────┘
```

### DNS-over-HTTPS (DoH) Akışı

```
POST /dns-query HTTP/1.1
Host: dns.coremusic.local
Content-Type: application/dns-message
Content-Length: 33

<binary DNS query>

Response:
HTTP/1.1 200 OK
Content-Type: application/dns-message
Cache-Control: max-age=300

<binary DNS response>
```

### DNS-over-TLS (DoT) Akışı

```
1. TCP Connection (port 853)
   └─── TLS 1.3 Handshake ────

2. DNS Query (length-prefixed):
   [2 bytes: length][DNS message]

3. DNS Response:
   [2 bytes: length][DNS message]

4. Keep-alive: 30s idle timeout
```

### Cache Yönetimi

```python
class DNSCache:
    def __init__(self, max_size: int = 10000):
        self.cache: dict[str, CacheEntry] = {}
        self.max_size = max_size
        self.stats = {"hits": 0, "misses": 0}

    def get(self, domain: str, rtype: str) -> Optional[DNSRecord]:
        key = f"{domain}:{rtype}"
        if key in self.cache:
            entry = self.cache[key]
            if not entry.is_expired():
                self.stats["hits"] += 1
                return entry.record
            else:
                del self.cache[key]
        self.stats["misses"] += 1
        return None

    def put(self, record: DNSRecord):
        key = f"{record.name}:{record.rtype}"
        if len(self.cache) >= self.max_size:
            self._evict_lru()
        self.cache[key] = CacheEntry(
            record=record,
            ttl=record.ttl,
            created_at=time.time()
        )

    def _evict_lru(self):
        oldest_key = min(self.cache, key=lambda k: self.cache[k].created_at)
        del self.cache[oldest_key]

    @property
    def hit_rate(self) -> float:
        total = self.stats["hits"] + self.stats["misses"]
        return self.stats["hits"] / total if total > 0 else 0
```

### Upstream DNS Sunucu Seçimi

```yaml
dns_servers:
  primary:
    - address: "1.1.1.1"
      provider: "Cloudflare"
      protocol: "doh"
      url: "https://1.1.1.1/dns-query"
    - address: "8.8.8.8"
      provider: "Google"
      protocol: "doh"
      url: "https://dns.google/dns-query"

  fallback:
    - address: "9.9.9.9"
      provider: "Quad9"
      protocol: "dot"
      port: 853

  local:
    - address: "192.168.1.1"
      provider: "Router"
      protocol: "udp"
      port: 53

  selection_policy: "fastest-respond"
  health_check:
    interval: 60000           # ms
    timeout: 5000             # ms
    failure_threshold: 3
```

### DNS Query Pipeline

```python
class DNSResolver:
    def __init__(self):
        self.cache = DNSCache()
        self.upstreams = UpstreamManager()
        self.local_mdns = MDNSResolver()

    async def resolve(self, domain: str, rtype: str = "A") -> DNSRecord:
        # 1. Cache check
        cached = self.cache.get(domain, rtype)
        if cached:
            return cached

        # 2. mDNS for .local
        if domain.endswith(".local"):
            return await self.local_mdns.resolve(domain, rtype)

        # 3. Upstream resolution
        record = await self.upstreams.query(domain, rtype)

        # 4. Cache result
        if record:
            self.cache.put(record)

        return record

    async def reverse_resolve(self, ip: str) -> Optional[str]:
        PTR = self._ip_to_ptr(ip)
        record = await self.resolve(PTR, "PTR")
        return record.rdata if record else None
```

## Konfigürasyon

```yaml
dns:
  enabled: true
  local_address: "0.0.0.0"
  port: 53
  cache:
    enabled: true
    max_size: 10000
    default_ttl: 300
    max_ttl: 86400
  upstreams:
    - address: "1.1.1.1"
      protocol: "doh"
      weight: 100
    - address: "8.8.8.8"
      protocol: "doh"
      weight: 80
    - address: "9.9.9.9"
      protocol: "dot"
      weight: 60
  health_check:
    enabled: true
    interval: 60000
    timeout: 5000
    failure_threshold: 3
  mdns:
    enabled: true
    domain: "local"
  hosts_file: "/etc/coremusic/hosts"
  logging:
    enabled: false
    level: "info"
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | UDP/TCP socket I/O |
| K14-mDNS | Giren | .local domain çözümleme |
| K6 | Çıkan | DoH/DoT TLS |
| K14-TLS | Çıkan | Certificate validation |

## Durum: Implementasyon

- [x] Klasik DNS (UDP/TCP port 53)
- [x] DNS-over-HTTPS (DoH)
- [x] DNS-over-TLS (DoT)
- [x] Cache yönetimi (LRU eviction)
- [x] TTL-based expiry
- [x] mDNS .local çözümleme
- [x] Reverse DNS lookup
- [x] Upstream health checking
- [x] Fallback mekanizması
- [x] Hosts file desteği
- [x] Query logging
