---
title: "Rate Limiting"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# Rate Limiting

## Genel Bakış

Rate limiting, API'leri ve kaynakları aşırı kullanım, brute-force saldırıları ve DDoS saldırılarından korur. Token bucket ve sliding window algoritmalarını birlikte kullanarak esnek ama katı sınırlar belirler. Per-user ve per-IP bazlı limitler ile adil kaynak kullanımı sağlar.

## Teknik Detaylar

### Algoritmalar

**Token Bucket**: Sabit rate ile token eklenir, her istek bir token harcar. Burst toleransı sağlar.

**Sliding Window**: Belirli bir zaman dilimindeki istek sayısı sayılır. Daha hassas kontrol sağlar.

### Limit Stratejisi

```
┌─────────────────────────────────────────────────┐
│              RATE LIMIT MATRİSİ                │
├─────────────────┬──────────┬──────────┬─────────┤
│ Endpoint        │ User     │ IP       │ Global  │
├─────────────────┼──────────┼──────────┼─────────┤
│ /api/auth/login │ 5/dk    │ 10/dk    │ 100/dk  │
│ /api/auth/*     │ 20/dk   │ 50/dk    │ 500/dk  │
│ /api/audio/*    │ 100/dk  │ 200/dk   │ 1000/dk │
│ /api/ai/*       │ 10/dk   │ 30/dk    │ 200/dk  │
│ /api/*          │ 60/dk   │ 120/dk   │ 1000/dk │
│ /static/*       │ -       │ 300/dk   │ 5000/dk │
└─────────────────┴──────────┴──────────┴─────────┘
```

### Response Headers

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1758412800
Retry-After: 30
```

### Rate Limit Aşımında

- 429 Too Many Requests response döner
- Retry-After header'ı ile bekleme süresi bildirilir
- IP blacklist'e eklenir (tekrarlayan ihallerde)
- Audit log'a kaydedilir

## Konfigürasyon / Kod

```typescript
import Redis from 'ioredis';

const redis = new Redis(process.env.REDIS_URL);

// Rate Limit Konfigürasyonu
interface RateLimitConfig {
  windowMs: number;
  max: number;
  keyGenerator: (req: Request) => string;
  skipSuccessfulRequests?: boolean;
  skipFailedRequests?: boolean;
  handler?: (req: Request, res: Response) => void;
}

// Token Bucket Algoritması
class TokenBucket {
  private key: string;
  private capacity: number;
  private refillRate: number;

  constructor(key: string, capacity: number, refillRate: number) {
    this.key = `tb:${key}`;
    this.capacity = capacity;
    this.refillRate = refillRate;
  }

  async consume(tokens: number = 1): Promise<{
    allowed: boolean;
    remaining: number;
    resetAt: number;
  }> {
    const now = Date.now();
    const script = `
      local key = KEYS[1]
      local capacity = tonumber(ARGV[1])
      local refillRate = tonumber(ARGV[2])
      local now = tonumber(ARGV[3])
      local tokens = tonumber(ARGV[4])

      local bucket = redis.call('hmget', key, 'tokens', 'lastRefill')
      local currentTokens = tonumber(bucket[1]) or capacity
      local lastRefill = tonumber(bucket[2]) or now

      -- Token'ları yenile
      local elapsed = (now - lastRefill) / 1000
      local refill = math.floor(elapsed * refillRate)
      currentTokens = math.min(capacity, currentTokens + refill)

      -- Token harca
      if currentTokens >= tokens then
        currentTokens = currentTokens - tokens
        redis.call('hmset', key, 'tokens', currentTokens, 'lastRefill', now)
        redis.call('expire', key, math.ceil(capacity / refillRate) + 1)
        return {1, currentTokens, now + math.ceil((capacity - currentTokens) / refillRate * 1000)}
      else
        redis.call('hmset', key, 'tokens', currentTokens, 'lastRefill', now)
        redis.call('expire', key, math.ceil(capacity / refillRate) + 1)
        return {0, currentTokens, now + math.ceil((1 - currentTokens) / refillRate * 1000)}
      end
    `;

    const result = await redis.eval(
      script,
      1,
      this.key,
      this.capacity,
      this.refillRate,
      now,
      tokens
    ) as number[];

    return {
      allowed: result[0] === 1,
      remaining: result[1],
      resetAt: result[2],
    };
  }
}

// Sliding Window Algoritması
class SlidingWindow {
  private key: string;
  private windowMs: number;
  private maxRequests: number;

  constructor(key: string, windowMs: number, maxRequests: number) {
    this.key = `sw:${key}`;
    this.windowMs = windowMs;
    this.maxRequests = maxRequests;
  }

  async increment(): Promise<{
    allowed: boolean;
    remaining: number;
    resetAt: number;
  }> {
    const now = Date.now();
    const windowStart = now - this.windowMs;

    const script = `
      local key = KEYS[1]
      local windowStart = tonumber(ARGV[1])
      local now = tonumber(ARGV[2])
      local maxRequests = tonumber(ARGV[3])
      local windowMs = tonumber(ARGV[4])

      -- Eski entry'leri temizle
      redis.call('zremrangebyscore', key, 0, windowStart)

      -- Mevcut sayıyı kontrol et
      local currentCount = redis.call('zcard', key)

      if currentCount < maxRequests then
        -- Yeni isteği ekle
        redis.call('zadd', key, now, now .. ':' .. math.random(1000000))
        redis.call('pexpire', key, windowMs)
        return {1, maxRequests - currentCount - 1, now + windowMs}
      else
        -- En eski entry'nin zamanını bul
        local oldest = redis.call('zrange', key, 0, 0, 'WITHSCORES')
        local resetAt = tonumber(oldest[2]) + windowMs
        return {0, 0, resetAt}
      end
    `;

    const result = await redis.eval(
      script,
      1,
      this.key,
      windowStart,
      now,
      this.maxRequests,
      this.windowMs
    ) as number[];

    return {
      allowed: result[0] === 1,
      remaining: result[1],
      resetAt: result[2],
    };
  }
}

// Rate Limit Middleware
function rateLimit(config: RateLimitConfig) {
  return async (req: Request, res: Response, next: NextFunction) => {
    const key = config.keyGenerator(req);
    const bucket = new TokenBucket(key, config.max, config.max / (config.windowMs / 1000));

    const result = await bucket.consume();

    // Response header'larını ayarla
    res.setHeader('X-RateLimit-Limit', config.max);
    res.setHeader('X-RateLimit-Remaining', Math.max(0, result.remaining));
    res.setHeader('X-RateLimit-Reset', Math.ceil(result.resetAt / 1000));

    if (!result.allowed) {
      const retryAfter = Math.ceil((result.resetAt - Date.now()) / 1000);
      res.setHeader('Retry-After', retryAfter);

      await logRateLimitExceeded({
        key,
        ip: req.ip,
        path: req.path,
        method: req.method,
        retryAfter,
      });

      if (config.handler) {
        return config.handler(req, res);
      }

      return res.status(429).json({
        error: 'Too many requests',
        retryAfter,
      });
    }

    next();
  };
}

// Key Generator Fonksiyonları
function userKeyGenerator(req: Request): string {
  const userId = (req.user as any)?.sub;
  return userId ? `user:${userId}` : `ip:${req.ip}`;
}

function ipKeyGenerator(req: Request): string {
  return `ip:${req.ip}`;
}

function endpointKeyGenerator(req: Request): string {
  const userId = (req.user as any)?.sub;
  const base = userId ? `user:${userId}` : `ip:${req.ip}`;
  return `${base}:${req.route?.path || req.path}`;
}

// Önceden Tanımlı Limit Konfigürasyonları
export const rateLimits = {
  login: rateLimit({
    windowMs: 60000,
    max: 5,
    keyGenerator: (req) => `login:${req.ip}`,
  }),

  api: rateLimit({
    windowMs: 60000,
    max: 60,
    keyGenerator: userKeyGenerator,
  }),

  ai: rateLimit({
    windowMs: 60000,
    max: 10,
    keyGenerator: userKeyGenerator,
  }),

  upload: rateLimit({
    windowMs: 300000,
    max: 10,
    keyGenerator: userKeyGenerator,
  }),

  global: rateLimit({
    windowMs: 60000,
    max: 1000,
    keyGenerator: ipKeyGenerator,
  }),
};

// DDoS Koruması
async function ddosProtection(req: Request, res: Response, next: NextFunction) {
  const suspiciousPatterns = [
    /\.\.\//,  // Path traversal
    /<script/i, // XSS
    /union.*select/i, // SQL injection
    /eval\(/i, // Code injection
  ];

  const url = req.url + JSON.stringify(req.body);
  const isSuspicious = suspiciousPatterns.some(p => p.test(url));

  if (isSuspicious) {
    // IP'yi geçici blacklist'e ekle
    await redis.setex(`bl:${req.ip}`, 3600, 'suspicious');

    await logSuspiciousActivity({
      ip: req.ip,
      path: req.path,
      pattern: url,
      userAgent: req.headers['user-agent'],
    });

    return res.status(403).json({ error: 'Request blocked' });
  }

  // IP blacklist kontrolü
  const isBlacklisted = await redis.get(`bl:${req.ip}`);
  if (isBlacklisted) {
    return res.status(403).json({ error: 'IP temporarily blocked' });
  }

  next();
}
```

## Güvenlik Kontrolleri

- [ ] Login endpoint'leri sıkı rate limiting ile korunmalı (5/dk)
- [ ] AI endpoint'leri带宽 sınırlı olmalı
- [ ] Upload endpoint'leri dosya boyutu ve adet sınırı ile korunmalı
- [ ] Rate limit aşımlarında audit log tutulmalı
- [ ] Tekrarlayan ihallerde IP blacklist'e eklenmeli
- [ ] Global rate limit DDoS koruması sağlamalı
- [ ] Response header'ları doğru bilgi vermeli
- [ ] Redis high availability ile çalışmalı
- [ ] Rate limit cache'i cluster modunda senkronize olmalı
- [ ] Webhook'lar için ayrı rate limit olmalı

## Bağımlılıklar

- **redis**: Rate limit sayacı ve cache
- **audit-logging.md**: Rate limit ihlalleri loglanır
- **security-headers.md**: Retry-After header'ı

## Durum: Implementasyon

- [x] Algoritma seçimi yapıldı (Token Bucket + Sliding Window)
- [x] Limit matrisi tanımlandı
- [ ] Token bucket implemente edilecek
- [ ] Sliding window implemente edilecek
- [ ] Redis entegrasyonu kurulacak
- [ ] DDoS koruması aktifleştirilecek
- [ ] Rate limit UI'ı yapılacak
- [ ] Monitoring dashboard oluşturulacak
