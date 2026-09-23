---
title: "K9 API & Routing - Genel Bakış"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# K9 API & Routing

## Genel Bakış

K9 API & Routing katmanı, COREMUSIC mimarisinin dış dünyayla iletişimini yöneten merkezi ağ geçididir. Bu katman, tüm istemci türlerinden (web, mobil, masaüstü, IoT) gelen HTTP/gRPC/GraphQL isteklerini alır, doğrular, yönlendirir ve ilgili backend servislerine iletir. Aynı zamanda rate limiting, caching, versioning ve monitoring gibicross-cutting concerns'leri merkezi olarak yönetir.

API Gateway, microservices mimarisinin ön cephesi olarak çalışır ve BFF (Backend for Frontend) pattern'ini uygulayarak her istemci türü için optimize edilmiş API'ler sunar. Event-driven yapı ile asenkron iletişim desteklenir.

## Mimari Diyagram

```
┌─────────────────────────────────────────────────────────────┐
│                     DIŞ İSTEMCİLER                          │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────────┐   │
│  │  Web UI │  │Mobile App│  │Desktop  │  │ IoT Device  │   │
│  └────┬────┘  └────┬────┘  └────┬────┘  └──────┬──────┘   │
│       │            │            │               │           │
└───────┼────────────┼────────────┼───────────────┼───────────┘
        │            │            │               │
┌───────▼────────────▼────────────▼───────────────▼───────────┐
│                   K9 API GATEWAY                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │ Rate     │  │ Auth     │  │ Request  │  │ Response │   │
│  │ Limiter  │  │ Middleware│  │ Router   │  │ Cache    │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              BFF Layer (Mobile / Web / Desktop)       │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │         GraphQL / gRPC / REST Router                  │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────┐
│                    K10 SERVİS LAYER                         │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐      │
│  │ K4 AI   │  │ K5 Ses  │  │ K6      │  │ K7      │      │
│  │ Engine  │  │ Motoru  │  │ Playlist│  │ User    │      │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘      │
└─────────────────────────────────────────────────────────────┘
```

## Bileşenler

| Dosya | Açıklama |
|-------|----------|
| [api-gateway.md](./api-gateway.md) | API Gateway, request routing, load balancing |
| [bff-pattern.md](./bff-pattern.md) | Backend for Frontend pattern |
| [cqrs-pattern.md](./cqrs-pattern.md) | Command Query Responsibility Segregation |
| [event-bus.md](./event-bus.md) | Event Bus, message broker, pub/sub |
| [spa-router.md](./spa-router.md) | SPA Router, client-side routing |
| [openapi-spec.md](./openapi-spec.md) | OpenAPI 3.0 specification |
| [versioning-strategy.md](./versioning-strategy.md) | API versioning stratejisi |
| [graphql-layer.md](./graphql-layer.md) | GraphQL optional layer |
| [grpc-internal.md](./grpc-internal.md) | gRPC internal communication |
| [request-throttling.md](./request-throttling.md) | Request throttling, priority queues |
| [response-caching.md](./response-caching.md) | Response caching, ETags |

## Teknik Detaylar

### Runtime Stack

```
K9 Tech Stack:
├── Gateway:          Kong / Traefik / Custom (Rust/Go)
├── Protocol:         HTTP/2, gRPC, WebSocket, GraphQL
├── Auth:             OAuth 2.0 + JWT + API Key
├── Rate Limiting:    Token Bucket (Redis-backed)
├── Caching:          Redis Cluster + CDN Edge Cache
├── Schema:           OpenAPI 3.0 / GraphQL Schema
├── Monitoring:       Prometheus + Grafana + Jaeger
└── Event Bus:        Apache Kafka / RabbitMQ
```

### Request Lifecycle

```
1. Client Request → TLS Termination
2. Rate Limit Check (Redis)
3. JWT/API Key Validation
4. Request Transformation (Header injection, body parsing)
5. Route Matching (Path-based, Header-based, Method-based)
6. BFF Transformation (optional)
7. Load Balancer → Upstream Service
8. Response Transformation
9. Cache Store (if cacheable)
10. Client Response
```

### Performance Hedefleri

| Metrik | Hedef |
|--------|-------|
| P50 Latency | < 5ms (gateway overhead) |
| P99 Latency | < 20ms (gateway overhead) |
| Throughput | > 100K req/s per instance |
| Availability | 99.99% uptime |
| Error Rate | < 0.1% |

## Bağımlılıklar

### Üst Katmanlar (K9'in kullandığı)
- **K1 Hardware**: TLS termination için donanım hızlandırma
- **K0 OS**: Network stack, epoll/io_uring

### Alt Katmanlar (K9'i kullananlar)
- **K10 Service Layer**: Backend servisler
- **K11-K20**: Tüm istemci uygulamaları

### Dış Servisler
- Redis: Rate limiting, caching, session
- Kafka/RabbitMQ: Event bus
- Consul/etcd: Service discovery
- Prometheus/Grafana: Monitoring

## Konfigürasyon

```yaml
# k9-gateway-config.yaml
gateway:
  listen: "0.0.0.0:8443"
  tls:
    cert: "/etc/coremusic/tls/server.crt"
    key: "/etc/coremusic/tls/server.key"
  rate_limit:
    enabled: true
    default_rps: 100
    burst: 50
    redis_url: "redis://localhost:6379"
  auth:
    jwt_issuer: "https://auth.coremusic.local"
    jwt_audience: "coremusic-api"
    api_key_header: "X-API-Key"
  cache:
    enabled: true
    redis_url: "redis://localhost:6379"
    default_ttl: 300
  monitoring:
    prometheus_port: 9090
    tracing_enabled: true
    jaeger_endpoint: "http://localhost:14268/api/traces"
```

## Durum: Implementasyon

- [x] API Gateway temel yapı
- [x] Rate limiting (token bucket)
- [x] JWT authentication middleware
- [ ] BFF pattern implementasyonu
- [ ] CQRS read/write separation
- [ ] Event bus entegrasyonu
- [ ] GraphQL layer
- [ ] gRPC internal communication
- [ ] Response caching
- [ ] OpenAPI spec auto-generation
