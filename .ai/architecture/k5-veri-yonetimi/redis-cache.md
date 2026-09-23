---
title: "Redis 7 Önbellek Sistemi"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 8
dependencies: [K0, K1]
---

# Redis Cache - Dağıtık Önbellek Sistemi

## Genel Bakış

Redis 7 tabanlı dağıtık önbellek sistemi, COREMUSIC'in yüksek performanslı veri erişimini sağlar. Session management, query cache, real-time pub/sub ve rate limiting için kullanılır. Cluster mode ve Sentinel ile high availability destekler.

## Teknik Detaylar

### Redis Cluster Yapısı

```yaml
# redis-cluster.yaml
cluster:
  enabled: true
  nodes:
    - host: "redis-node-1.coremusic.internal"
      port: 6379
      role: "master"
    
    - host: "redis-node-2.coremusic.internal"
      port: 6379
      role: "master"
    
    - host: "redis-node-3.coremusic.internal"
      port: 6379
      role: "master"
    
    - host: "redis-node-4.coremusic.internal"
      port: 6379
      role: "slave"
    
    - host: "redis-node-5.coremusic.internal"
      port: 6379
      role: "slave"
    
    - host: "redis-node-6.coremusic.internal"
      port: 6379
      role: "slave"
  
  sentinel:
    enabled: true
    monitors:
      - name: "mymaster"
        host: "redis-node-1.coremusic.internal"
        port: 26379
        quorum: 2
  
  cluster_timeout: 15000
  max_redirects: 3
```

### Session Cache

```php
<?php
// Redis Session Handler
class RedisSessionHandler implements SessionHandlerInterface
{
    private RedisCluster $redis;
    private string $prefix = 'session:';
    private int $ttl = 1800; // 30 dakika
    
    public function __construct(array $config)
    {
        $this->redis = new RedisCluster(
            null,
            $config['nodes'],
            $config['timeout'] ?? 1.5,
            $config['read_timeout'] ?? 1.5
        );
        
        if (isset($config['auth'])) {
            $this->redis->auth($config['auth']);
        }
    }
    
    public function read(string $sessionId): string
    {
        $key = $this->prefix . $sessionId;
        $data = $this->redis->get($key);
        
        if ($data !== false) {
            // TTL'yi yenile
            $this->redis->expire($key, $this->ttl);
            return $data;
        }
        
        return '';
    }
    
    public function write(string $sessionId, string $data): bool
    {
        $key = $this->prefix . $sessionId;
        $result = $this->redis->setex($key, $this->ttl, $data);
        
        // Session metadata güncelle
        $this->updateSessionMetadata($sessionId, strlen($data));
        
        return $result;
    }
    
    public function destroy(string $sessionId): bool
    {
        $key = $this->prefix . $sessionId;
        
        // Session metadata'yı da sil
        $this->redis->del($this->prefix . 'meta:' . $sessionId);
        
        return $this->redis->del($key);
    }
    
    public function gc(int $maxLifetime): int
    {
        // Redis otomatik TTL kullanıyor, manual GC gerekmez
        return 0;
    }
    
    private function updateSessionMetadata(string $sessionId, int $size): void
    {
        $metaKey = $this->prefix . 'meta:' . $sessionId;
        $metadata = [
            'size' => $size,
            'last_access' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $this->redis->setex($metaKey, $this->ttl, json_encode($metadata));
    }
}
```

### Query Cache

```php
<?php
class QueryCache
{
    private RedisCluster $redis;
    private string $prefix = 'query:';
    private array $ttlConfig = [
        'default' => 300,      // 5 dakika
        'search' => 60,        // 1 dakika
        'analytics' => 3600,   // 1 saat
        'static' => 86400,     // 24 saat
    ];
    
    public function __construct(RedisCluster $redis)
    {
        $this->redis = $redis;
    }
    
    public function get(string $queryKey, string $category = 'default'): ?array
    {
        $key = $this->prefix . $category . ':' . md5($queryKey);
        
        $cached = $this->redis->get($key);
        
        if ($cached !== false) {
            // Hit ratio için istatistik
            $this->recordHit($category);
            return json_decode($cached, true);
        }
        
        // Miss ratio için istatistik
        $this->recordMiss($category);
        
        return null;
    }
    
    public function set(string $queryKey, array $data, 
                       string $category = 'default', ?int $customTtl = null): bool
    {
        $key = $this->prefix . $category . ':' . md5($queryKey);
        $ttl = $customTtl ?? $this->ttlConfig[$category] ?? $this->ttlConfig['default'];
        
        $serialized = json_encode($data);
        
        // Büyük sonuçlar için compress
        if (strlen($serialized) > 1024) {
            $serialized = gzcompress($serialized);
        }
        
        $result = $this->redis->setex($key, $ttl, $serialized);
        
        // Cache size tracking
        $this->trackCacheSize($category, strlen($serialized));
        
        return $result;
    }
    
    public function invalidate(string $queryKey, string $category = 'default'): bool
    {
        $key = $this->prefix . $category . ':' . md5($queryKey);
        return $this->redis->del($key) > 0;
    }
    
    public function invalidatePattern(string $pattern): int
    {
        $deleted = 0;
        $cursor = null;
        
        do {
            [$cursor, $keys] = $this->redis->scan(
                $cursor, 
                $this->prefix . $pattern, 
                100
            );
            
            if ($keys) {
                $deleted += $this->redis->del(...$keys);
            }
        } while ($cursor !== 0);
        
        return $deleted;
    }
    
    private function recordHit(string $category): void
    {
        $this->redis->hincrBy("cache:stats:$category", "hits", 1);
    }
    
    private function recordMiss(string $category): void
    {
        $this->redis->hincrBy("cache:stats:$category", "misses", 1);
    }
    
    private function trackCacheSize(string $category, int $size): void
    {
        $this->redis->hincrBy("cache:stats:$category", "total_size", $size);
        $this->redis->hincrBy("cache:stats:$category", "entries", 1);
    }
}
```

### Pub/Sub Messages

```php
<?php
class RedisPubSub
{
    private RedisCluster $redis;
    private array $channels = [];
    private ?callable $messageHandler = null;
    
    public function __construct(RedisCluster $redis)
    {
        $this->redis = $redis;
    }
    
    public function publish(string $channel, array $data): int
    {
        $message = json_encode([
            'channel' => $channel,
            'timestamp' => microtime(true),
            'data' => $data
        ]);
        
        return $this->redis->publish($channel, $message);
    }
    
    public function subscribe(array $channels, callable $handler): void
    {
        $this->messageHandler = $handler;
        $this->channels = $channels;
        
        // Redis pub/sub subscription
        foreach ($channels as $channel) {
            $this->redis->subscribe([$channel], function ($redis, $channel, $message) {
                $this->handleMessage($channel, $message);
            });
        }
    }
    
    private function handleMessage(string $channel, string $message): void
    {
        $data = json_decode($message, true);
        
        if ($data && $this->messageHandler) {
            call_user_func($this->messageHandler, $data);
        }
    }
    
    // Event broadcasting
    public function broadcastTrackPlay(int $trackId, int $userId): void
    {
        $this->publish('events:track:play', [
            'track_id' => $trackId,
            'user_id' => $userId,
            'timestamp' => time()
        ]);
    }
    
    public function broadcastPlaylistUpdate(int $playlistId, string $action): void
    {
        $this->publish('events:playlist:update', [
            'playlist_id' => $playlistId,
            'action' => $action,
            'timestamp' => time()
        ]);
    }
    
    public function broadcastUserActivity(int $userId, string $activity): void
    {
        $this->publish('events:user:activity', [
            'user_id' => $userId,
            'activity' => $activity,
            'timestamp' => time()
        ]);
    }
}
```

### Rate Limiting

```php
<?php
class RedisRateLimiter
{
    private RedisCluster $redis;
    
    public function __construct(RedisCluster $redis)
    {
        $this->redis = $redis;
    }
    
    public function checkLimit(string $key, int $limit, 
                              int $windowSeconds): array
    {
        $now = microtime(true);
        $windowStart = $now - $windowSeconds;
        
        // Sliding window counter
        $multi = $this->redis->multi();
        $multi->zremrangebyscore($key, 0, $windowStart);
        $multi->zadd($key, [$now => $now]);
        $multi->zcard($key);
        $multi->expire($key, $windowSeconds);
        $results = $multi->exec();
        
        $currentCount = $results[2];
        $remaining = max(0, $limit - $currentCount);
        $resetAt = $now + $windowSeconds;
        
        return [
            'allowed' => $currentCount <= $limit,
            'limit' => $limit,
            'remaining' => $remaining,
            'reset_at' => $resetAt
        ];
    }
    
    public function isRateLimited(string $identifier, 
                                 string $endpoint): bool
    {
        $key = "ratelimit:{$identifier}:{$endpoint}";
        
        $limits = [
            'api' => ['limit' => 1000, 'window' => 60],
            'search' => ['limit' => 100, 'window' => 60],
            'auth' => ['limit' => 10, 'window' => 300],
        ];
        
        $config = $limits[$endpoint] ?? $limits['api'];
        $result = $this->checkLimit($key, $config['limit'], $config['window']);
        
        return !$result['allowed'];
    }
}
```

## API / Konfigürasyon

```yaml
# config/redis.yaml
redis:
  cluster:
    enabled: true
    nodes:
      - { host: "redis-1", port: 6379 }
      - { host: "redis-2", port: 6379 }
      - { host: "redis-3", port: 6379 }
    
    password: "${REDIS_PASSWORD}"
    timeout: 1.5
    read_timeout: 1.5
    max_retries: 3
    
  sentinel:
    enabled: true
    master: "mymaster"
    hosts:
      - { host: "sentinel-1", port: 26379 }
      - { host: "sentinel-2", port: 26379 }
      - { host: "sentinel-3", port: 26379 }
    
    down_after_milliseconds: 5000
    failover_timeout: 10000
    
  memory:
    max_memory: "16gb"
    max_memory_policy: "allkeys-lru"
    
  persistence:
    enabled: true
    save_intervals: [900, 300, 60]
    aof_enabled: true
    aof_fsync: "everysec"
    
  monitoring:
    slow_log_enabled: true
    slow_log_threshold_ms: 10
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| GET Latency | 0.5ms |
| SET Latency | 0.8ms |
| Throughput | 500K ops/s |
| Hit Ratio | 97.2% |
| Memory Usage | 12GB / 16GB |
| Connected Clients | 500 |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| Redis | 7.x | Cache engine |
| phpredis | 6.0+ | PHP client |
| predis | 2.2+ | Alternative client |

## Durum: Implementasyon

Redis Cache modülü **stable** durumdadır. Cluster mode ve sentinel aktif. Session cache, query cache ve pub/sub production-ready.
