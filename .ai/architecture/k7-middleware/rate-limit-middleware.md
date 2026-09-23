---
title: "Rate Limit Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# Rate Limit Middleware

## Genel Bakış

Rate Limit Middleware, IP tabanlı istek sınırlaması yaparak API'nin aşırı yüklenmesini ve DDoS saldırılarını engeller. Sliding window algoritması kullanarak belirli zaman dilimlerinde izin verilen istek sayısını kontrol eder. Limit aşımında 429 Too Many Requests yanıtı ve Retry-After başlığı döndürür.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
├── [2] Security Headers
│
▼
[3] Rate Limit Middleware  ← Burada filtreleme
│
├── [4] CORS
├── [5] Origin Check
├── [6] Session
├── [7] CSRF
├── [8] Request Validation
├── [9] Logging
├── [10] Error Handler
│
▼
Handler
```

Rate limit, compression ve security headers'tan sonra çalışır. Güvenlik başlıkları zaten eklendiği için 429 yanıtlarında da geçerli olur.

## Teknik Detaylar

### Sliding Window Algoritması

```
Zaman Penceresi: 60 saniye, Maksimum İstek: 100

Zaman çizelgesi:
|----|----|----|----|----|----|----|----|----|----| 0-60s
  10   20   30   40   50   60   70   80   90  100  ← İstek sayıları

Anlık durum (t = 45s): Son 60 saniyede 65 istek → Kalan: 35
```

### Redis Tabanlı Rate Limiting

```php
namespace CoreMusic\Middleware;

use Predis\Client as RedisClient;

class RateLimitMiddleware implements MiddlewareInterface
{
    private RedisClient $redis;
    private int $maxRequests;
    private int $windowSeconds;
    private string $keyPrefix;

    public function __construct(
        RedisClient $redis,
        int $maxRequests = 100,
        int $windowSeconds = 60
    ) {
        $this->redis = $redis;
        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
        $this->keyPrefix = 'rate_limit:';
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $clientIp = $this->getClientIp($request);
        $key = $this->keyPrefix . $clientIp;

        $currentCount = $this->incrementCounter($key);
        $ttl = $this->redis->pttl($key);

        if ($currentCount > $this->maxRequests) {
            return $this->createRateLimitResponse($ttl);
        }

        $response = $handler->handle($request);
        return $this->addRateLimitHeaders($response, $currentCount, $ttl);
    }

    private function incrementCounter(string $key): int
    {
        $pipe = $this->redis->pipeline();
        $pipe->incr($key);
        $pipe->expire($key, $this->windowSeconds);
        $results = $pipe->execute();

        return $results[0]; // incr sonucu
    }
}
```

### Client IP Tespiti

```php
private function getClientIp(ServerRequestInterface $request): string
{
    // Proxy arkasındaysa X-Forwarded-For kontrolü
    $forwardedFor = $request->getHeaderLine('X-Forwarded-For');
    if (!empty($forwardedFor)) {
        $ips = explode(',', $forwardedFor);
        return trim($ips[0]); // İlk IP (en yakın proxy)
    }

    // X-Real-IP kontrolü
    $realIp = $request->getHeaderLine('X-Real-IP');
    if (!empty($realIp)) {
        return $realIp;
    }

    // Doğrudan bağlantı
    return $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
}
```

### Endpoint Bazlı Rate Limiting

```php
// Farklı endpoint'ler için farklı limitler
$endpointLimits = [
    '/api/v1/auth/login'     => ['max' => 5,  'window' => 60],   // brute force koruması
    '/api/v1/auth/register'  => ['max' => 3,  'window' => 300],  // spam koruması
    '/api/v1/audio/upload'   => ['max' => 10, 'window' => 60],   // yükleme limiti
    '/api/v1/audio/analyze'  => ['max' => 20, 'window' => 60],   // analiz limiti
    '/api/v1/tracks'         => ['max' => 100, 'window' => 60],  // genel API limiti
];

// Implementasyon
private function getLimitForEndpoint(string $path): array
{
    foreach ($endpointLimits as $pattern => $limit) {
        if (fnmatch($pattern, $path)) {
            return $limit;
        }
    }
    return ['max' => 100, 'window' => 60]; // varsayılan
}
```

### 429 Yanıt Formatı

```json
{
    "error": "RATE_LIMIT_EXCEEDED",
    "message": "Çok fazla istek. Lütfen bekleyin.",
    "retry_after": 23,
    "limit": 100,
    "remaining": 0,
    "reset_at": "2026-09-20T15:45:00Z"
}
```

### Response Headers

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 23
X-RateLimit-Reset: 1726846500
Retry-After: 23
```

## Kod / Konfigürasyon

### Ana Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class RateLimitMiddleware implements MiddlewareInterface
{
    private RateLimiter $limiter;
    private ResponseFactoryInterface $responseFactory;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $clientIp = $this->getClientIp($request);
        $endpoint = $request->getUri()->getPath();
        $limit = $this->limiter->getLimitForEndpoint($endpoint);

        $key = "rate:{$clientIp}:{$endpoint}";
        $result = $this->limiter->check($key, $limit['max'], $limit['window']);

        if ($result->isExceeded()) {
            return $this->createRateLimitResponse($result);
        }

        $response = $handler->handle($request);

        return $response
            ->withHeader('X-RateLimit-Limit', (string) $limit['max'])
            ->withHeader('X-RateLimit-Remaining', (string) $result->getRemaining())
            ->withHeader('X-RateLimit-Reset', (string) $result->getResetTime());
    }

    private function createRateLimitResponse(RateLimitResult $result): ResponseInterface
    {
        $retryAfter = $result->getRetryAfter();

        $response = $this->responseFactory->createResponse(429);
        $response->getBody()->write(json_encode([
            'error' => 'RATE_LIMIT_EXCEEDED',
            'message' => 'Çok fazla istek. Lütfen bekleyin.',
            'retry_after' => $retryAfter,
            'limit' => $result->getLimit(),
            'remaining' => 0,
            'reset_at' => date('c', $result->getResetTime()),
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Retry-After', (string) $retryAfter)
            ->withHeader('X-RateLimit-Limit', (string) $result->getLimit())
            ->withHeader('X-RateLimit-Remaining', '0')
            ->withHeader('X-RateLimit-Reset', (string) $result->getResetTime());
    }
}
```

### Redis Lua Script (Atomik İşlem)

```lua
-- rate_limit.lua
local key = KEYS[1]
local limit = tonumber(ARGV[1])
local window = tonumber(ARGV[2])
local now = tonumber(ARGV[3])

-- Mevcut pencereyi hesapla
local window_start = now - window

-- Eski kayıtları temizle
redis.call('ZREMRANGEBYSCORE', key, 0, window_start)

-- Mevcut istek sayısını al
local current = redis.call('ZCARD', key)

if current < limit then
    -- İsteği ekle
    redis.call('ZADD', key, now, now .. math.random())
    redis.call('EXPIRE', key, window)
    return {current + 1, limit - current - 1, 0}
else
    -- Limit aşıldı
    local oldest = redis.call('ZRANGE', key, 0, 0, 'WITHSCORES')
    local retry_after = math.ceil(oldest[2] + window - now)
    return {current, 0, retry_after}
end
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| Redis | `predis/predis` | Evet |
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | IP tabanlı sliding window | Planlandı |
| Faz 2 | Endpoint bazlı limitler | Planlandı |
| Faz 3 | Redis Lua script optimizasyonu | Planlandı |
| Faz 4 | User bazlı rate limiting (auth) | Planlandı |
| Faz 5 | Monitoring ve alerting | Planlandı |
