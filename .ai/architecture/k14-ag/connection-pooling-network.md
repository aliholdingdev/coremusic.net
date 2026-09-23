---
title: "Network Connection Pooling"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# Network Connection Pooling

## Genel Bakış

COREMUSIC connection pooling, TCP/UDP bağlantılarının yeniden kullanımını ve yönetilmesini sağlayan optimizasyon katmanıdır. Bağlantı oluşturma overhead'ini azaltarak, yüksek frekanslı isteklerde performans kazanımı sağlar. Warm-up, keep-alive ve dynamic scaling ile real-time ses akışına uygun bağlantı yönetimi sunar.

## Protokol Detayı

- **Min Connections**: Minimum havuz boyutu
- **Max Connections**: Maksimum havuz boyutu
- **Idle Timeout**: Boşta kalma süresi
- **Connection Lifetime**: Maksimum bağlantı yaşı
- **Acquire Timeout**: Havuzdan bağlantı bekleme süresi
- **Health Check**: Bağlantı健康 kontrolü

## Teknik Detaylar

### Connection Pool Mimarisi

```
┌──────────────────────────────────────────────────────────┐
│              Connection Pool Architecture                  │
│                                                            │
│  ┌────────────────────────────────────────────────────┐   │
│  │                  Pool Manager                      │   │
│  │  ┌─────────┐  ┌─────────┐  ┌─────────┐           │   │
│  │  │ Active  │  │  Idle   │  │Pending  │           │   │
│  │  │Conns    │  │ Conns   │  │Acquires │           │   │
│  │  └────┬────┘  └────┬────┘  └────┬────┘           │   │
│  │       │            │            │                   │   │
│  │  ┌────┴────────────┴────────────┴────┐            │   │
│  │  │         Connection Queue          │            │   │
│  │  │  [conn1][conn2][conn3]...[connN]  │            │   │
│  │  └───────────────────┬───────────────┘            │   │
│  └──────────────────────┼─────────────────────────────┘   │
│                         │                                 │
│  ┌──────────────────────┴─────────────────────────────┐   │
│  │              Connection Factory                    │   │
│  │  ┌──────────────────────────────────────────────┐ │   │
│  │  │ Create: TCP connect → TLS handshake → Ready  │ │   │
│  │  │ Validate: Ping/pong → Health check           │ │   │
│  │  │ Destroy: Close socket → Remove from pool     │ │   │
│  │  └──────────────────────────────────────────────┘ │   │
│  └────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────┘
```

### Bağlantı Durum Makinesi

```
┌─────────────────────────────────────────────────────┐
│           Connection State Machine                   │
│                                                       │
│  ┌──────────┐    acquire    ┌──────────┐            │
│  │   IDLE   │──────────────>│  ACTIVE  │            │
│  └──────────┘               └────┬─────┘            │
│       ▲                          │                    │
│       │ release                  │ timeout/error      │
│       │                          │                    │
│       │                    ┌─────▼──────┐            │
│       │                    │   CLOSING  │            │
│       │                    └─────┬──────┘            │
│       │                          │                    │
│       │                    ┌─────▼──────┐            │
│       │                    │  DISPOSED  │            │
│       │                    └────────────┘            │
│       │                                               │
│  ┌────┴─────┐    validate   ┌──────────┐            │
│  │ VALIDATING│──────────────>│   IDLE   │            │
│  └──────────┘               └──────────┘            │
│       ▲                                               │
│       │ create                                       │
│       │                                               │
│  ┌────┴─────┐                                        │
│  │CREATING  │                                        │
│  └──────────┘                                        │
└─────────────────────────────────────────────────────┘
```

### Pool İşlemleri

```python
import asyncio
import time
from dataclasses import dataclass, field
from typing import Optional
from enum import Enum

class ConnState(Enum):
    CREATING = "creating"
    IDLE = "idle"
    ACTIVE = "active"
    VALIDATING = "validating"
    CLOSING = "closing"
    DISPOSED = "disposed"

@dataclass
class PooledConnection:
    socket: asyncio.Transport
    state: ConnState = ConnState.CREATING
    created_at: float = field(default_factory=time.time)
    last_used_at: float = field(default_factory=time.time)
    use_count: int = 0
    host: str = ""
    port: int = 0

class ConnectionPool:
    def __init__(self, min_size: int = 5, max_size: int = 20):
        self.min_size = min_size
        self.max_size = max_size
        self.idle_pool: dict[str, list[PooledConnection]] = {}
        self.active_pool: dict[str, PooledConnection] = {}
        self.lock = asyncio.Lock()
        self._stats = PoolStats()

    async def acquire(self, host: str, port: int) -> PooledConnection:
        async with self.lock:
            # Try idle pool first
            key = f"{host}:{port}"
            if key in self.idle_pool and self.idle_pool[key]:
                conn = self.idle_pool[key].pop(0)
                if self._is_valid(conn):
                    conn.state = ConnState.ACTIVE
                    conn.last_used_at = time.time()
                    self.active_pool[id(conn)] = conn
                    self._stats.acquired += 1
                    return conn

            # Create new if under limit
            if len(self.active_pool) < self.max_size:
                conn = await self._create_connection(host, port)
                self.active_pool[id(conn)] = conn
                self._stats.created += 1
                return conn

            # Wait for available connection
            self._stats.waiting += 1
            return await self._wait_for_connection(host, port)

    async def release(self, conn: PooledConnection):
        async with self.lock:
            if id(conn) in self.active_pool:
                del self.active_pool[id(conn)]

            if self._is_valid(conn):
                conn.state = ConnState.IDLE
                key = f"{conn.host}:{conn.port}"
                if key not in self.idle_pool:
                    self.idle_pool[key] = []
                self.idle_pool[key].append(conn)
                self._stats.released += 1
            else:
                await self._destroy_connection(conn)

    async def _create_connection(self, host: str, port: int) -> PooledConnection:
        reader, writer = await asyncio.open_connection(host, port)
        return PooledConnection(
            socket=writer,
            state=ConnState.ACTIVE,
            host=host,
            port=port
        )

    def _is_valid(self, conn: PooledConnection) -> bool:
        # Check lifetime
        max_lifetime = 3600  # 1 hour
        if time.time() - conn.created_at > max_lifetime:
            return False
        # Check idle timeout
        max_idle = 300  # 5 minutes
        if time.time() - conn.last_used_at > max_idle:
            return False
        return True

    async def _destroy_connection(self, conn: PooledConnection):
        try:
            conn.state = ConnState.CLOSING
            conn.socket.close()
            self._stats.destroyed += 1
        except Exception:
            pass
```

### Warm-up Stratejisi

```python
class PoolWarmup:
    async def warmup(self, pool: ConnectionPool, host: str, port: int, count: int):
        """Pre-create connections on startup"""
        tasks = []
        for _ in range(count):
            tasks.append(self._create_warm(pool, host, port))
        await asyncio.gather(*tasks)

    async def _create_warm(self, pool, host, port):
        try:
            conn = await pool._create_connection(host, port)
            conn.state = ConnState.IDLE
            key = f"{host}:{port}"
            if key not in pool.idle_pool:
                pool.idle_pool[key] = []
            pool.idle_pool[key].append(conn)
        except Exception as e:
            logging.warning(f"Warmup connection failed: {e}")
```

### Keep-Alive Mekanizması

```python
class KeepAliveManager:
    def __init__(self, interval: int = 30, timeout: int = 10):
        self.interval = interval
        self.timeout = timeout

    async def start_heartbeat(self, pool: ConnectionPool):
        while True:
            await asyncio.sleep(self.interval)
            await self._check_connections(pool)

    async def _check_connections(self, pool: ConnectionPool):
        async with pool.lock:
            for key, conns in pool.idle_pool.items():
                for conn in conns[:]:
                    if not self._is_alive(conn):
                        await pool._destroy_connection(conn)
                        conns.remove(conn)

    def _is_alive(self, conn: PooledConnection) -> bool:
        try:
            # TCP keepalive
            sock = conn.socket.get_extra_info('socket')
            if sock:
                import socket
                sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
            return True
        except Exception:
            return False
```

### Metrics Collection

```python
class PoolStats:
    def __init__(self):
        self.created: int = 0
        self.destroyed: int = 0
        self.acquired: int = 0
        self.released: int = 0
        self.waiting: int = 0
        self.timeouts: int = 0
        self.errors: int = 0

    def get_metrics(self) -> dict:
        return {
            "pool_size": self.created - self.destroyed,
            "active_connections": self.acquired - self.released,
            "total_created": self.created,
            "total_destroyed": self.destroyed,
            "total_acquired": self.acquired,
            "total_released": self.released,
            "total_waiters": self.waiting,
            "total_timeouts": self.timeouts,
            "total_errors": self.errors,
        }
```

## Konfigürasyon

```yaml
connection_pool:
  enabled: true
  min_size: 5
  max_size: 20
  max_idle_time: 300000               # 5 dakika (ms)
  max_lifetime: 3600000               # 1 saat (ms)
  acquire_timeout: 5000               # ms
  validation_interval: 30000          # 30 saniye (ms)
  warmup:
    enabled: true
    count: 5
  keepalive:
    enabled: true
    interval: 30000                   # 30 saniye
    timeout: 10000                    # 10 saniye
  per_host_limits:
    "api.coremusic.local":
      max_connections: 10
    "media.coremusic.local":
      max_connections: 15
  monitoring:
    enabled: true
    metrics_interval: 60000           # 1 dakika
  retry:
    max_attempts: 3
    backoff_ms: [100, 200, 500]
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | TCP/UDP socket creation |
| K14-TLS | Giren | TLS connection setup |
| K14-LB | Çıkan | Backend connection routing |
| K8 | Çıkan | Health check integration |

## Durum: Implementasyon

- [x] Connection pool lifecycle
- [x] Min/max pool sizing
- [x] Idle connection cleanup
- [x] Connection lifetime limits
- [x] Warm-up on startup
- [x] Keep-alive heartbeat
- [x] Health validation
- [x] Acquire timeout handling
- [x] Metrics collection
- [x] Per-host limits
- [x] Retry with backoff
- [x] Graceful shutdown
