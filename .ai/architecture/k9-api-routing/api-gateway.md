---
title: "API Gateway"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# API Gateway

## Genel Bakış

API Gateway, COREMUSIC'in tüm dış isteklerini tek giriş noktasından yöneten merkezi bileşendir. TLS termination, authentication, rate limiting, request routing ve load balancing işlemlerini merkezi olarak gerçekleştirir. Her istemci türü için optimize edilmiş response'lar döndürür.

Gateway, reverse proxy olarak çalışır ve upstream servisler之间的 health check, circuit breaking, retry mekanizmalarını yönetir. Kubernetes Ingress Controller veya bağımsız olarak deploy edilebilir.

## API Tanımı

### Public Endpoints

| Endpoint | Method | Açıklama | Rate Limit |
|----------|--------|----------|------------|
| `/api/v1/auth/login` | POST | Kullanıcı girişi | 10 req/min |
| `/api/v1/auth/refresh` | POST | Token yenileme | 30 req/min |
| `/api/v1/auth/logout` | POST | Oturum kapatma | 30 req/min |
| `/api/v1/users/me` | GET | Profil bilgisi | 100 req/min |
| `/api/v1/users/me` | PUT | Profil güncelleme | 20 req/min |
| `/api/v1/tracks` | GET | Şarkı listesi | 200 req/min |
| `/api/v1/tracks/{id}` | GET | Şarkı detayı | 200 req/min |
| `/api/v1/playlists` | GET/POST | Playlist CRUD | 100 req/min |
| `/api/v1/stream/{trackId}` | GET | Ses stream'i | 50 req/min |
| `/api/v1/search` | GET | Arama sorgusu | 100 req/min |
| `/api/v1/ai/recommend` | POST | AI önerileri | 20 req/min |
| `/api/v1/ai/eq-optimize` | POST | EQ optimizasyonu | 10 req/min |

### Internal Endpoints (sadece inner network)

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/internal/health` | GET | Health check |
| `/internal/metrics` | GET | Prometheus metrics |
| `/internal/config` | GET | Runtime config |
| `/internal/trace/{traceId}` | GET | Distributed trace |

## Teknik Detaylar

### Gateway Mimarisi

```
┌──────────────────────────────────────────────────────────┐
│                    API Gateway (Rust)                     │
│                                                          │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐        │
│  │  Listener   │  │  Pipeline  │  │  Upstream  │        │
│  │  (TLS)      │→ │  (Chain)   │→ │  (Pool)    │        │
│  └────────────┘  └────────────┘  └────────────┘        │
│                                                          │
│  Pipeline Chain:                                         │
│  1. TLS Termination (rustls)                             │
│  2. Request Parsing (body size limit: 10MB)              │
│  3. Rate Limit Check (Redis Lua script)                  │
│  4. Authentication (JWT verify + API key lookup)         │
│  5. Authorization (RBAC check)                           │
│  6. Request Validation (JSON Schema / OpenAPI)           │
│  7. Header Transformation (X-Request-ID, X-Forwarded-For)│
│  8. Route Matching (path, method, header)                │
│  9. Load Balancer (round-robin, weighted, least-conn)    │
│  10. Timeout & Circuit Breaker                           │
│  11. Response Transformation                             │
│  12. Cache Store (Redis)                                 │
└──────────────────────────────────────────────────────────┘
```

### Rate Limiting Implementasyonu

```rust
// Token Bucket Algorithm - Redis-backed
use redis::Cmd;

pub struct TokenBucket {
    key: String,
    capacity: u64,
    refill_rate: f64, // tokens per second
    refill_interval: Duration,
}

impl TokenBucket {
    pub async fn acquire(&self, redis: &RedisPool) -> Result<bool, RateLimitError> {
        let script = r#"
            local key = KEYS[1]
            local capacity = tonumber(ARGV[1])
            local refill_rate = tonumber(ARGV[2])
            local now = tonumber(ARGV[3])
            local requested = tonumber(ARGV[4])

            local bucket = redis.call('hmget', key, 'tokens', 'last_refill')
            local tokens = tonumber(bucket[1]) or capacity
            local last_refill = tonumber(bucket[2]) or now

            local elapsed = now - last_refill
            local refill_amount = elapsed * refill_rate
            tokens = math.min(capacity, tokens + refill_amount)

            if tokens >= requested then
                tokens = tokens - requested
                redis.call('hmset', key, 'tokens', tokens, 'last_refill', now)
                redis.call('expire', key, math.ceil(capacity / refill_rate) * 2)
                return 1
            else
                return 0
            end
        "#;

        let result: bool = redis
            .eval(script, 1)
            .arg(&self.key)
            .arg(self.capacity)
            .arg(self.refill_rate)
            .arg(chrono::Utc::now().timestamp())
            .arg(1)
            .await?;

        Ok(result)
    }
}
```

### Load Balancing Stratejileri

```rust
pub enum LoadBalanceStrategy {
    RoundRobin,
    WeightedRoundRobin {
        weights: HashMap<String, u32>,
    },
    LeastConnections,
    IpHash,
    Random,
}

pub struct LoadBalancer {
    upstreams: Vec<Upstream>,
    strategy: LoadBalanceStrategy,
    health_checker: HealthChecker,
}

impl LoadBalancer {
    pub fn select(&self, request: &Request) -> Option<&Upstream> {
        let healthy: Vec<&Upstream> = self.upstreams
            .iter()
            .filter(|u| self.health_checker.is_healthy(u))
            .collect();

        match self.strategy {
            LoadBalanceStrategy::RoundRobin => {
                // Atomic counter ile round-robin
                static COUNTER: AtomicUsize = AtomicUsize::new(0);
                let idx = COUNTER.fetch_add(1, Ordering::Relaxed) % healthy.len();
                healthy.get(idx).copied()
            }
            LoadBalanceStrategy::LeastConnections => {
                healthy.iter().min_by_key(|u| u.active_connections())
                    .copied()
            }
            LoadBalanceStrategy::IpHash => {
                let hash = self.hash_client_ip(request);
                healthy.get(hash % healthy.len()).copied()
            }
            _ => healthy.first().copied(),
        }
    }
}
```

### Circuit Breaker

```rust
pub struct CircuitBreaker {
    state: Arc<RwLock<CircuitState>>,
    failure_threshold: u32,
    recovery_timeout: Duration,
    half_open_max: u32,
}

pub enum CircuitState {
    Closed { failure_count: u32 },
    Open { opened_at: Instant },
    HalfOpen { success_count: u32 },
}

impl CircuitBreaker {
    pub async fn call<F, T>(&self, f: F) -> Result<T, CircuitBreakerError>
    where
        F: Future<Output = Result<T, anyhow::Error>>,
    {
        let state = self.state.read().await;
        match *state {
            CircuitState::Open { opened_at } => {
                if opened_at.elapsed() > self.recovery_timeout {
                    drop(state);
                    self.transition_to_half_open().await;
                } else {
                    return Err(CircuitBreakerError::CircuitOpen);
                }
            }
            CircuitState::HalfOpen { success_count } => {
                if success_count >= self.half_open_max {
                    return Err(CircuitBreakerError::CircuitOpen);
                }
            }
            _ => {}
        }
        drop(state);

        match f.await {
            Ok(result) => {
                self.on_success().await;
                Ok(result)
            }
            Err(e) => {
                self.on_failure().await;
                Err(CircuitBreakerError::Upstream(e))
            }
        }
    }
}
```

### Middleware Zinciri

```
Request Flow:
┌─────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│  CORS   │ → │  Auth    │ → │  Rate    │ → │  Route   │
│Middleware│   │Middleware│   │  Limit   │   │  Match   │
└─────────┘   └──────────┘   └──────────┘   └──────────┘
                    │              │              │
              JWT/API Key    Redis Check    Path/Method
              Validation     Token Bucket   Header Match
```

## Konfigürasyon

```yaml
# api-gateway.yaml
gateway:
  instance_id: "coremusic-gw-01"
  workers: 8  # CPU core sayisi
  max_connections: 10000
  request_timeout: 30s
  keep_alive: 75s

  tls:
    enabled: true
    min_version: "1.2"
    ciphers:
      - "TLS_AES_256_GCM_SHA384"
      - "TLS_CHACHA20_POLY1305_SHA256"

  upstreams:
    - name: "user-service"
      targets:
        - "http://user-svc:8081"
        - "http://user-svc-2:8081"
      health_check:
        path: "/health"
        interval: 10s
        timeout: 5s
        unhealthy_threshold: 3

    - name: "track-service"
      targets:
        - "http://track-svc:8082"
      health_check:
        path: "/health"
        interval: 10s

    - name: "ai-service"
      targets:
        - "http://ai-svc:8083"
      health_check:
        path: "/health"
        interval: 30s
      circuit_breaker:
        failure_threshold: 5
        recovery_timeout: 60s

  cors:
    allowed_origins:
      - "https://app.coremusic.com"
      - "https://web.coremusic.com"
    allowed_methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"]
    allowed_headers: ["Authorization", "Content-Type", "X-API-Key"]
    max_age: 3600
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K0 OS**: Network stack (io_uring/epoll), TLS
- **Redis**: Rate limiting, session, caching

### Bağımlı Olan
- **BFF Layer**: Backend for Frontend transformation
- **K10 Services**: Tüm backend servisleri
- **K4 AI**: AI servis endpoint'leri

## Durum: Implementasyon

- [x] Gateway temel scaffold
- [x] TLS termination (rustls)
- [x] Rate limiting (token bucket + Redis)
- [x] JWT authentication
- [x] Basic routing (path-based)
- [x] Health check
- [ ] Circuit breaker
- [ ] Retry with exponential backoff
- [ ] Request/response transformation
- [ ] WebSocket proxying
- [ ] gRPC proxying
- [ ] Admin dashboard
