---
type: architecture
category: contracts
title: "API Observability — Logging, Metrics & Tracing"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Observability — Logging, Metrics & Tracing

## 1. Purpose

Defines observability standards for all CoreMusic API services: logging, metrics, tracing, and alerting. Ensures production behavior is visible and diagnosable.

---

## 2. Correlation ID

### 2.1 Header

```
X-Request-ID: 550e8400-e29b-41d4-a716-446655440000
```

### 2.2 Flow

```
Client Request (with or without X-Request-ID)
  → Server generates X-Request-ID if missing
    → All downstream services use same ID
      → Response includes X-Request-ID header
        → Client uses ID for debugging/support
```

### 2.3 Rules

| Rule | Value |
|------|-------|
| Format | UUID v4 |
| Propagation | Forward to all downstream services |
| Logging | Include in every log entry |
| Response | Always include in response header |
| Client-provided | Accepted if valid UUID v4 |

---

## 3. Structured Logging

### 3.1 Standard (PSR-3 / Monolog)

```json
{
  "timestamp": "2026-08-09T10:30:00.000Z",
  "level": "INFO",
  "channel": "api",
  "message": "Request completed",
  "context": {
    "request_id": "550e8400-e29b-41d4-a716-446655440000",
    "method": "GET",
    "uri": "/api/songs",
    "status_code": 200,
    "duration_ms": 45,
    "user_id": 1234,
    "ip": "192.168.1.100"
  }
}
```

### 3.2 Log Levels

| Level | When | Example |
|-------|------|---------|
| `DEBUG` | Development only | Query execution details |
| `INFO` | Normal operations | Request completed, user login |
| `WARNING` | Potential issues | Rate limit approaching, deprecated API usage |
| `ERROR` | Operation failed | Database error, external service timeout |
| `CRITICAL` | System failure | Auth bypass detected, data breach attempt |

---

## 4. Request/Response Logging

### 4.1 What to Log

| Data | Log Level | Redact |
|------|-----------|--------|
| Method + URI | INFO | No |
| Status code | INFO | No |
| Duration | INFO | No |
| User ID | INFO | No |
| Request body | DEBUG | Passwords, tokens |
| Response body | DEBUG | Passwords, tokens |
| Headers | DEBUG | Authorization |
| IP address | INFO | No |
| User-Agent | INFO | No |

### 4.2 Sensitive Data Redaction

| Field | Replacement |
|-------|-------------|
| `password` | `[REDACTED]` |
| `api_key` | `[REDACTED]` |
| `token` | `[REDACTED]` |
| `secret` | `[REDACTED]` |
| `authorization` | `[REDACTED]` |
| `credit_card` | `[REDACTED]` |

---

## 5. Audit Trail

| Event | Log Level | Retention |
|-------|-----------|-----------|
| User login/logout | INFO | 1 year |
| Password change | INFO | 1 year |
| Role/permission change | WARNING | 2 years |
| Data export | INFO | 1 year |
| Failed auth attempts | WARNING | 90 days |
| Security events | CRITICAL | 5 years |
| CRUD operations | DEBUG | 30 days |

---

## 6. Metrics Collection

### 6.1 Core Metrics

| Metric | Type | Description |
|--------|------|-------------|
| `api_request_duration_ms` | Histogram | Response time distribution |
| `api_requests_total` | Counter | Total requests by method/status |
| `api_errors_total` | Counter | Total errors by code |
| `api_active_connections` | Gauge | Current active connections |
| `api_request_size_bytes` | Histogram | Request body size |
| `api_response_size_bytes` | Histogram | Response body size |

### 6.2 Labels

| Label | Description |
|-------|-------------|
| `method` | HTTP method (GET, POST, PUT, DELETE) |
| `endpoint` | Route pattern (/api/songs/{id}) |
| `status_code` | HTTP status code |
| `service` | Service name (api, media, auth) |

### 6.3 SLO Targets

| Metric | Target |
|--------|--------|
| Availability | ≥99.9% |
| p50 latency | <100ms |
| p95 latency | <500ms |
| p99 latency | <2000ms |
| Error rate | <0.1% |

---

## 7. Health Check Endpoints

### 7.1 Endpoints

| Endpoint | Purpose | Interval |
|----------|---------|----------|
| `GET /health/live` | Liveness probe | Every 10s |
| `GET /health/ready` | Readiness probe | Every 30s |
| `GET /health/startup` | Startup probe | On deploy |

### 7.2 Response Format

```json
{
  "status": "healthy",
  "timestamp": "2026-08-09T10:30:00.000Z",
  "uptime_seconds": 86400,
  "checks": {
    "database": "ok",
    "redis": "ok",
    "disk": "ok",
    "memory": "ok"
  },
  "version": "1.0.0"
}
```

### 7.3 Health States

| Status | HTTP Code | Meaning |
|--------|-----------|---------|
| `healthy` | 200 | All systems operational |
| `degraded` | 200 | Partial system failure |
| `unhealthy` | 503 | Critical system failure |

---

## 8. Distributed Tracing

### 8.1 Trace Context Header

```
traceparent: 00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01
```

### 8.2 Trace Propagation

| Service | Span | Parent |
|---------|------|--------|
| API Gateway | span-0 | — |
| Auth Service | span-1 | span-0 |
| Music Service | span-2 | span-0 |
| Database Query | span-3 | span-2 |

---

## 9. Alerting Rules

| Alert | Condition | Severity | Action |
|-------|-----------|----------|--------|
| High error rate | >5% errors in 5 min | CRITICAL | Page on-call |
| Slow responses | p99 >2s for 10 min | WARNING | Notify team |
| Disk usage | >85% in 1 hour | WARNING | Cleanup jobs |
| Memory usage | >90% in 5 min | CRITICAL | Restart service |
| Auth failures | >10 in 1 min | WARNING | Rate limit + alert |
| DB connection pool | >80% utilized | WARNING | Scale up |

---

## 10. Log Rotation

| Parameter | Value |
|-----------|-------|
| Max file size | 100MB |
| Max files | 30 |
| Retention | 30 days (INFO), 1 year (WARN+), 5 years (CRITICAL) |
| Compression | gzip after rotation |
| Archive location | `/var/log/coremusic/archive/` |

---

## 11. Dashboard Integration

| Dashboard | Metrics | Refresh |
|-----------|---------|---------|
| API Overview | Request rate, error rate, latency | 10s |
| Service Health | Health check status, uptime | 30s |
| Database | Query time, connections, slow queries | 30s |
| Security | Auth failures, rate limits, anomalies | 60s |
| Business | Active users, popular tracks, downloads | 5min |

---

## 12. Cross References

| Document | Relationship |
|----------|-------------|
| [[api-architecture-master]] | Parent API architecture |
| [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit and vault standards |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode