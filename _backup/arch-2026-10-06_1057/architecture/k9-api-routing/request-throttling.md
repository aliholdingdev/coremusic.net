---
title: "Request Throttling & Priority Queues"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# Request Throttling

## Genel Bakış

Request Throttling, COREMUSIC API'lerinin yüksek load altında stabil kalmasını sağlayan rate limiting ve priority queue mekanizmasını yönetir. Token bucket, sliding window ve leaky bucket algoritmaları desteklenir. Kullanıcı tier'ına göre farklı rate limit'ler uygulanır (free, premium, enterprise).

Priority queues ile kritik istekler (audio streaming, payment) daha yüksek öncelik alır. Adaptive throttling ile system load'a göre dinamik limit ayarlaması yapılır.

## API Tanımı

### Rate Limit Headers

| Header | Açıklama |
|--------|----------|
| `X-RateLimit-Limit` | Maksimum istek sayısı (window başına) |
| `X-RateLimit-Remaining` | Kalan istek hakkı |
| `X-RateLimit-Reset` | Window reset zamanı (epoch) |
| `X-RateLimit-Policy` | Kullanılan policy adı |
| `Retry-After` | Throttle_edildiğinde bekleme süresi (saniye) |

### Rate Limit Tiers

| Tier | Endpoint Grubu | Limit (req/min) | Burst |
|------|----------------|-----------------|-------|
| Free | All | 60 | 10 |
| Premium | All | 300 | 50 |
| Enterprise | All | 1000 | 200 |
| Free | Auth | 10 | 3 |
| Premium | Auth | 30 | 10 |
| Free | Streaming | 5 | 2 |
| Premium | Streaming | 30 | 10 |
| Enterprise | Streaming | 100 | 30 |

## Teknik Detaylar

### Throttling Mimarisi

```
┌─────────────────────────────────────────────────────────────┐
│                    REQUEST THROTTLING                        │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              1. Global Rate Limiter                    │  │
│  │              (IP-based, per instance)                  │  │
│  │              Redis: global:{ip}:{window}              │  │
│  └──────────────────┬───────────────────────────────────┘  │
│                     │ PASS                                  │
│  ┌──────────────────▼───────────────────────────────────┐  │
│  │              2. User Rate Limiter                      │  │
│  │              (JWT-based, per user)                     │  │
│  │              Redis: user:{userId}:{endpointGroup}     │  │
│  └──────────────────┬───────────────────────────────────┘  │
│                     │ PASS                                  │
│  ┌──────────────────▼───────────────────────────────────┐  │
│  │              3. Endpoint Rate Limiter                  │  │
│  │              (per endpoint, per user)                  │  │
│  │              Redis: endpoint:{userId}:{path}          │  │
│  └──────────────────┬───────────────────────────────────┘  │
│                     │ PASS                                  │
│  ┌──────────────────▼───────────────────────────────────┐  │
│  │              4. Priority Queue Router                  │  │
│  │                                                       │  │
│  │  ┌─────────┐  ┌──────────┐  ┌───────────┐          │  │
│  │  │ Critical│  │ High     │  │ Normal    │          │  │
│  │  │ (P0)   │  │ (P1)     │  │ (P2)      │          │  │
│  │  │streaming│  │ playback │  │ search    │          │  │
│  │  │payment  │  │ playlists│  │ metadata  │          │  │
│  │  └─────────┘  └──────────┘  └───────────┘          │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              5. Adaptive Throttle                     │  │
│  │              (CPU/Memory based dynamic adjustment)    │  │
│  │              CPU > 80% → limit -50%                   │  │
│  │              Memory > 90% → limit -75%                │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Sliding Window Rate Limiter

```typescript
// throttling/SlidingWindowRateLimiter.ts
import Redis from "ioredis";

interface RateLimitConfig {
  windowMs: number;
  maxRequests: number;
  keyPrefix: string;
}

class SlidingWindowRateLimiter {
  private redis: Redis;

  constructor(redis: Redis) {
    this.redis = redis;
  }

  async check(
    key: string,
    config: RateLimitConfig
  ): Promise<{ allowed: boolean; remaining: number; resetAt: number }> {
    const now = Date.now();
    const windowStart = now - config.windowMs;
    const fullKey = `${config.keyPrefix}:${key}`;

    const luaScript = `
      local key = KEYS[1]
      local window_start = tonumber(ARGV[1])
      local window_ms = tonumber(ARGV[2])
      local max_requests = tonumber(ARGV[3])
      local now = tonumber(ARGV[4])

      -- Eski request'leri temizle
      redis.call('ZREMRANGEBYSCORE', key, 0, window_start)

      -- Mevcut window'daki request sayısını say
      local current_count = redis.call('ZCARD', key)

      if current_count < max_requests then
        -- Request ekle
        redis.call('ZADD', key, now, now .. ':' .. math.random(1000000))
        redis.call('PEXPIRE', key, window_ms)
        return {1, max_requests - current_count - 1, now + window_ms}
      else
        -- En eski request'in zamanını bul (reset time)
        local oldest = redis.call('ZRANGE', key, 0, 0, 'WITHSCORES')
        local reset_at = oldest[2] and (tonumber(oldest[2]) + window_ms) or (now + window_ms)
        return {0, 0, reset_at}
      end
    `;

    const result: number[] = await this.redis.eval(
      luaScript,
      1,
      fullKey,
      windowStart,
      config.windowMs,
      config.maxRequests,
      now
    );

    return {
      allowed: result[0] === 1,
      remaining: result[1],
      resetAt: result[2],
    };
  }
}
```

### Priority Queue

```typescript
// throttling/PriorityQueue.ts
import { Heap } from "heap-js";

enum Priority {
  CRITICAL = 0,  // Audio streaming, payments
  HIGH = 1,      // Playback control, playlist
  NORMAL = 2,    // Search, metadata
  LOW = 3,       // Analytics, background jobs
}

interface QueuedRequest {
  id: string;
  priority: Priority;
  userId: string;
  endpoint: string;
  enqueuedAt: Date;
  resolve: (value: any) => void;
  reject: (error: any) => void;
}

class RequestPriorityQueue {
  private heap: Heap<QueuedRequest>;
  private processing: number = 0;
  private maxConcurrent: number;
  private maxQueueSize: number;

  constructor(maxConcurrent: number = 1000, maxQueueSize: number = 5000) {
    this.maxConcurrent = maxConcurrent;
    this.maxQueueSize = maxQueueSize;
    this.heap = new Heap<QueuedRequest>((a, b) => {
      if (a.priority !== b.priority) return a.priority - b.priority;
      return a.enqueuedAt.getTime() - b.enqueuedAt.getTime();
    });
  }

  async enqueue(
    request: Omit<QueuedRequest, "id" | "enqueuedAt" | "resolve" | "reject">
  ): Promise<any> {
    if (this.heap.size() >= this.maxQueueSize) {
      throw new ThrottleError("Queue full", 503);
    }

    return new Promise((resolve, reject) => {
      const queuedRequest: QueuedRequest = {
        ...request,
        id: crypto.randomUUID(),
        enqueuedAt: new Date(),
        resolve,
        reject,
      };

      this.heap.push(queuedRequest);
      this.processQueue();
    });
  }

  private async processQueue(): Promise<void> {
    while (
      this.processing < this.maxConcurrent &&
      this.heap.size() > 0
    ) {
      const request = this.heap.pop();
      if (!request) break;

      this.processing++;

      // Timeout kontrolü
      const age = Date.now() - request.enqueuedAt.getTime();
      if (age > 30000) { // 30 saniye timeout
        request.reject(new ThrottleError("Request timeout in queue", 408));
        this.processing--;
        continue;
      }

      try {
        // Request'i işle
        const result = await this.executeRequest(request);
        request.resolve(result);
      } catch (error) {
        request.reject(error);
      } finally {
        this.processing--;
        this.processQueue();
      }
    }
  }

  private getPriority(endpoint: string): Priority {
    if (endpoint.startsWith("/stream/")) return Priority.CRITICAL;
    if (endpoint.includes("/payment")) return Priority.CRITICAL;
    if (endpoint.startsWith("/player/")) return Priority.HIGH;
    if (endpoint.includes("/playlist")) return Priority.HIGH;
    if (endpoint.includes("/search")) return Priority.NORMAL;
    return Priority.LOW;
  }

  getStats() {
    return {
      queueSize: this.heap.size(),
      processing: this.processing,
      maxConcurrent: this.maxConcurrent,
    };
  }
}
```

### Adaptive Throttling

```typescript
// throttling/AdaptiveThrottler.ts
import os from "os";

class AdaptiveThrottler {
  private baseLimits: Map<string, number>;
  private currentMultiplier: number = 1.0;
  private checkInterval: NodeJS.Timeout;

  constructor() {
    this.baseLimits = new Map();
    this.checkInterval = setInterval(() => this.adjustLimits(), 5000);
  }

  private async adjustLimits(): Promise<void> {
    const cpuUsage = os.loadavg()[0] / os.cpus().length;
    const memUsage = process.memoryUsage().heapUsed / process.memoryUsage().heapTotal;

    let newMultiplier = 1.0;

    if (cpuUsage > 0.9) {
      newMultiplier = 0.2;  // %20'ye düşür
    } else if (cpuUsage > 0.8) {
      newMultiplier = 0.5;  // %50'ye düşür
    } else if (cpuUsage > 0.7) {
      newMultiplier = 0.75; // %75'e düşür
    } else if (memUsage > 0.9) {
      newMultiplier = 0.25;
    } else if (memUsage > 0.8) {
      newMultiplier = 0.6;
    }

    if (newMultiplier !== this.currentMultiplier) {
      console.log(
        `[Adaptive Throttle] Adjusting limits: ${this.currentMultiplier} → ${newMultiplier} ` +
        `(CPU: ${(cpuUsage * 100).toFixed(1)}%, Mem: ${(memUsage * 100).toFixed(1)}%)`
      );
      this.currentMultiplier = newMultiplier;
    }
  }

  getEffectiveLimit(baseLimit: number): number {
    return Math.floor(baseLimit * this.currentMultiplier);
  }

  getStatus() {
    return {
      cpuLoad: os.loadavg(),
      memoryUsage: process.memoryUsage(),
      currentMultiplier: this.currentMultiplier,
    };
  }
}
```

### Throttle Middleware

```typescript
// middleware/throttle.ts
import { Request, Response, NextFunction } from "express";

export function throttleMiddleware(
  redis: Redis,
  priorityQueue: RequestPriorityQueue,
  adaptiveThrottler: AdaptiveThrottler
) {
  const rateLimiter = new SlidingWindowRateLimiter(redis);

  return async (req: Request, res: Response, next: NextFunction) => {
    const userId = req.user?.id || req.ip;
    const endpointGroup = getEndpointGroup(req.path);
    const userTier = req.user?.tier || "free";

    // 1. Global IP rate limit
    const globalResult = await rateLimiter.check(`global:${req.ip}`, {
      windowMs: 60000,
      maxRequests: adaptiveThrottler.getEffectiveLimit(200),
      keyPrefix: "throttle",
    });

    if (!globalResult.allowed) {
      res.setHeader("Retry-After", Math.ceil((globalResult.resetAt - Date.now()) / 1000));
      return res.status(429).json({
        code: "RATE_LIMITED",
        message: "Too many requests",
      });
    }

    // 2. User rate limit
    const userLimit = getLimitForTier(userTier, endpointGroup);
    const effectiveLimit = adaptiveThrottler.getEffectiveLimit(userLimit);

    const userResult = await rateLimiter.check(`user:${userId}:${endpointGroup}`, {
      windowMs: 60000,
      maxRequests: effectiveLimit,
      keyPrefix: "throttle",
    });

    if (!userResult.allowed) {
      res.setHeader("X-RateLimit-Limit", effectiveLimit);
      res.setHeader("X-RateLimit-Remaining", 0);
      res.setHeader("X-RateLimit-Reset", userResult.resetAt);
      res.setHeader("Retry-After", Math.ceil((userResult.resetAt - Date.now()) / 1000));
      return res.status(429).json({
        code: "RATE_LIMITED",
        message: `Rate limit exceeded for ${endpointGroup}`,
        retryAfter: Math.ceil((userResult.resetAt - Date.now()) / 1000),
      });
    }

    // 3. Headers
    res.setHeader("X-RateLimit-Limit", effectiveLimit);
    res.setHeader("X-RateLimit-Remaining", userResult.remaining);
    res.setHeader("X-RateLimit-Reset", userResult.resetAt);

    // 4. Priority queue (sadece high-traffic durumlarda)
    const priority = getPriorityForEndpoint(req.path);
    if (priority <= Priority.NORMAL && globalResult.remaining < 20) {
      try {
        await priorityQueue.enqueue({
          priority,
          userId,
          endpoint: req.path,
        });
      } catch (error) {
        return res.status(503).json({
          code: "SERVICE_BUSY",
          message: "Server is busy, please retry",
          retryAfter: 5,
        });
      }
    }

    next();
  };
}
```

## Konfigürasyon

```yaml
# throttling-config.yaml
throttling:
  global:
    enabled: true
    default_limit: 200  # per minute per IP
    burst: 50
    window_ms: 60000

  tiers:
    free:
      default: 60
      auth: 10
      streaming: 5
      search: 30
    premium:
      default: 300
      auth: 30
      streaming: 30
      search: 100
    enterprise:
      default: 1000
      auth: 100
      streaming: 100
      search: 300

  priority_queue:
    max_concurrent: 1000
    max_queue_size: 5000
    timeout_ms: 30000

  adaptive:
    enabled: true
    check_interval_ms: 5000
    cpu_threshold_high: 0.8
    cpu_threshold_critical: 0.9
    memory_threshold_high: 0.8
    memory_threshold_critical: 0.9

  redis:
    host: "localhost"
    port: 6379
    db: 1
```

## Bağımlılıklar

### Bağımlı Olduğu
- **Redis**: Rate limit state
- **K0 OS**: CPU/memory metrics

### Bağımlı Olan
- **K9 API Gateway**: Request pipeline
- **K9 Response Caching**: Cache-aware throttling

## Durum: Implementasyon

- [x] Sliding window rate limiter
- [x] Token bucket rate limiter
- [x] User tier-based limits
- [x] Rate limit headers
- [ ] Priority queue
- [ ] Adaptive throttling
- [ ] Redis cluster support
- [ ] Throttle bypass (API key)
- [ ] Monitoring & alerting
- [ ] Admin dashboard
