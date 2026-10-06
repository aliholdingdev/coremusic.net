---
title: "Load Balancing Stratejileri"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# Load Balancing Stratejileri

## Genel Bakış

COREMUSIC load balancing katmanı, gelen istekleri birden fazla backend sunucusu arasında dağıtarak yüksek erişilebilirlik ve performans sağlar. L4/L7 load balancing, health checking, session affinity ve dinamik ağırlıklandırma ile real-time ses akışına uygun optimizasyonlar sunar.

## Protokol Detayı

- **L4 (Transport)**: TCP/UDP tabanlı, fast switching
- **L7 (Application)**: HTTP/WebSocket tabanlı, content-aware
- **Algoritmalar**: Round Robin, Weighted RR, Least Connections, IP Hash
- **Health Check**: TCP connect, HTTP GET, custom probe
- **Failover**: Automatic detection, graceful drain

## Teknik Detaylar

### Load Balancing Mimarisi

```
┌──────────────────────────────────────────────────────────┐
│                 Load Balancing Mimarisi                    │
│                                                            │
│  ┌──────────────────────────────────────────────────┐     │
│  │              External Traffic                     │     │
│  │                    │                               │     │
│  │              ┌─────┴─────┐                         │     │
│  │              │   L4 LB    │  (TCP/UDP passthrough)  │     │
│  │              │  (Fast)    │                         │     │
│  │              └─────┬─────┘                         │     │
│  │                    │                               │     │
│  │              ┌─────┴─────┐                         │     │
│  │              │   L7 LB    │  (Content routing)     │     │
│  │              │  (Smart)   │                         │     │
│  │              └─────┬─────┘                         │     │
│  │                    │                               │     │
│  │     ┌──────────────┼──────────────┐               │     │
│  │     │              │              │               │     │
│  │  ┌──┴──┐       ┌──┴──┐       ┌──┴──┐            │     │
│  │  │App 1│       │App 2│       │App 3│            │     │
│  │  │:8080│       │:8081│       │:8082│            │     │
│  │  └─────┘       └─────┘       └─────┘            │     │
│  └──────────────────────────────────────────────────┘     │
└──────────────────────────────────────────────────────────┘
```

### Algoritma Detayları

#### Round Robin
```python
class RoundRobinBalancer:
    def __init__(self, servers: list[Server]):
        self.servers = servers
        self.current_index = 0

    def next_server(self) -> Server:
        server = self.servers[self.current_index]
        self.current_index = (self.current_index + 1) % len(self.servers)
        return server
```

#### Weighted Round Robin
```python
class WeightedRoundRobinBalancer:
    def __init__(self, servers: list[WeightedServer]):
        self.servers = servers
        self.current_weights = [0] * len(servers)

    def next_server(self) -> Server:
        total_weight = sum(s.weight for s in self.servers)

        for i, server in enumerate(self.servers):
            self.current_weights[i] += server.weight

        max_index = max(range(len(self.servers)), key=lambda i: self.current_weights[i])
        selected = self.servers[max_index]
        self.current_weights[max_index] -= total_weight

        return selected
```

#### Least Connections
```python
class LeastConnectionsBalancer:
    def __init__(self, servers: list[Server]):
        self.servers = servers

    def next_server(self) -> Server:
        return min(self.servers, key=lambda s: s.active_connections)

    def on_connection_open(self, server: Server):
        server.active_connections += 1

    def on_connection_close(self, server: Server):
        server.active_connections -= 1
```

#### IP Hash
```python
class IPHashBalancer:
    def __init__(self, servers: list[Server]):
        self.servers = servers

    def next_server(self, client_ip: str) -> Server:
        hash_value = hashlib.md5(client_ip.encode()).hexdigest()
        index = int(hash_value, 16) % len(self.servers)
        return self.servers[index]
```

### Health Checking

```
┌──────────────────────────────────────────────────────┐
│              Health Check Pipeline                    │
│                                                        │
│  1. TCP Connect Check                                 │
│     └── Connect to port, check RST/FIN               │
│                                                        │
│  2. HTTP Health Check                                 │
│     └── GET /health → 200 OK                          │
│     └── Response time < threshold                     │
│                                                        │
│  3. Application Check                                 │
│     └── Custom endpoint: /health/app                  │
│     └── Check DB connection, cache, etc              │
│                                                        │
│  4. Passive Check (Runtime)                           │
│     └── Monitor response codes                       │
│     └── Track error rates                            │
│     └── Timeout detection                            │
│                                                        │
│  States:                                               │
│  ┌──────┐  pass   ┌────────┐  fail   ┌────────┐     │
│  │ DOWN │────────>│  UP    │────────>│ DOWN   │     │
│  └──────┘         └────────┘         └────────┘     │
│                      │   ▲                            │
│                      │   │ recover                    │
│                      v   │                            │
│                   ┌────────┐                          │
│                   │DRAINING│                          │
│                   └────────┘                          │
└──────────────────────────────────────────────────────┘
```

### Session Affinity

```python
class SessionAffinity:
    def __init__(self):
        self.sticky_sessions: dict[str, str] = {}

    def get_server(self, session_id: str, servers: list) -> Server:
        # 1. Check sticky session
        if session_id in self.sticky_sessions:
            server_id = self.sticky_sessions[session_id]
            server = self._find_server(servers, server_id)
            if server and server.is_healthy():
                return server

        # 2. Assign new server
        server = self._least_loaded(servers)
        self.sticky_sessions[session_id] = server.id
        return server

    def _least_loaded(self, servers: list) -> Server:
        return min(servers, key=lambda s: s.active_connections)
```

### Layer 7 Routing Rules

```yaml
routing_rules:
  - name: "API Routes"
    match:
      host: "api.coremusic.local"
      path_prefix: "/api/v1"
    action:
      backend_pool: "api-servers"
      health_check: "/health"
      timeout: 5000

  - name: "WebSocket Routes"
    match:
      path_prefix: "/ws"
    action:
      backend_pool: "ws-servers"
      session_affinity: true
      health_check: "/ws/health"

  - name: "Media Streaming"
    match:
      path_prefix: "/media"
      methods: ["GET"]
    action:
      backend_pool: "media-servers"
      cache_enabled: true
      compression: false

  - name: "Default"
    match:
      path_prefix: "/"
    action:
      backend_pool: "default-servers"
```

### DSCP-Based Routing

```
┌──────────────────────────────────────────────────────┐
│              QoS-Aware Load Balancing                 │
│                                                        │
│  DSCP → Backend Priority Mapping:                     │
│  ┌────────┬────────────────┬────────────────────┐    │
│  │ DSCP   │ Traffic Type   │ Backend Pool       │    │
│  ├────────┼────────────────┼────────────────────┤    │
│  │ EF (46)│ Audio Stream   │ Premium Pool       │    │
│  │ AF41   │ Now Playing    │ Standard Pool      │    │
│  │ AF21   │ Playlist       │ Standard Pool      │    │
│  │ CS0    │ Default        │ Standard Pool      │    │
│  └────────┴────────────────┴────────────────────┘    │
│                                                        │
│  Premium Pool:                                         │
│  - Dedicated CPU cores                               │
│  - Higher network priority                           │
│  - Smaller connection limits                         │
│  - Reserved bandwidth                                │
└──────────────────────────────────────────────────────┘
```

### Connection Draining

```
Graceful Shutdown Sequence:

1. Mark server as DRAINING
   └── Stop new connections

2. Wait for existing connections
   └── Timeout: 30s (configurable)

3. Send GOAWAY to HTTP/2 connections
   └── Server processes in-flight requests

4. Monitor connection count
   └── Force close if timeout exceeded

5. Remove from pool
   └── Update DNS/load balancer
```

## Konfigürasyon

```yaml
load_balancing:
  enabled: true
  algorithm: "weighted-round-robin"
  health_check:
    enabled: true
    interval: 10000                 # ms
    timeout: 5000                   # ms
    failure_threshold: 3
    success_threshold: 2
    types:
      - type: "tcp"
        port: 8080
      - type: "http"
        path: "/health"
        expected_status: 200
  backends:
    - name: "api-servers"
      servers:
        - address: "192.168.1.101:8080"
          weight: 100
        - address: "192.168.1.102:8080"
          weight: 100
        - address: "192.168.1.103:8080"
          weight: 80
      session_affinity: false
      max_connections: 1000
      timeout: 30000

    - name: "ws-servers"
      servers:
        - address: "192.168.1.101:8443"
          weight: 100
        - address: "192.168.1.102:8443"
          weight: 100
      session_affinity: true
      max_connections: 500
      timeout: 60000

    - name: "media-servers"
      servers:
        - address: "192.168.1.110:8080"
          weight: 100
          tags: ["premium"]
        - address: "192.168.1.111:8080"
          weight: 100
          tags: ["standard"]
  failover:
    enabled: true
    drain_timeout: 30000
    retry_policy: "exponential"
    max_retries: 3
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | TCP/UDP soket yönetimi |
| K14-TLS | Giren | TLS termination |
| K14-QoS | Çıkan | DSCP routing |
| K8 | Çıkan | Servis health bilgisi |

## Durum: Implementasyon

- [x] Round Robin
- [x] Weighted Round Robin
- [x] Least Connections
- [x] IP Hash
- [x] Health checking (TCP/HTTP/Custom)
- [x] Session affinity
- [x] Connection draining
- [x] DSCP-aware routing
- [x] Failover handling
- [x] L7 content routing
- [x] Rate limiting per backend
- [x] Metrics collection
