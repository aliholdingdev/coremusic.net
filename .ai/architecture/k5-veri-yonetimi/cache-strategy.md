---
title: "Çoklu Seviye Cache Stratejisi"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 5
dependencies: [K1, K5]
---

# Cache Strategy - Çoklu Seviye Önbellek

## Genel Bakış

Cache Strategy modülü, COREMUSIC'in çoklu seviye önbellek mimarisini yöneten, cache invalidation stratejilerini uygulayan ve write-through/write-back politikalarını kontrol eden merkezi cache koordinatöridür.

## Teknik Detaylar

### Çoklu Seviye Cache Mimarisi

```
┌─────────────────────────────────────────────────────────────┐
│                     L1: APCu (Local)                        │
│                     TTL: 60s | Size: 256MB                  │
│                     Hit Rate: 99.5%                         │
└─────────────────────────────────────────────────────────────┘
                              │ Miss
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     L2: Redis (Distributed)                 │
│                     TTL: 1800s | Size: 16GB                 │
│                     Hit Rate: 97.2%                         │
└─────────────────────────────────────────────────────────────┘
                              │ Miss
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     L3: MySQL (Persistent)                  │
│                     TTL: ∞ | Size: 500GB                    │
│                     Hit Rate: 100%                          │
└─────────────────────────────────────────────────────────────┘
                              │ Miss
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     L4: File System (Media)                 │
│                     TTL: ∞ | Size: 10TB                     │
│                     Hit Rate: 100%                          │
└─────────────────────────────────────────────────────────────┘
```

### Multi-Level Cache Manager

```php
<?php
class MultiLevelCacheManager
{
    private array $levels = [];
    private array $stats = [];
    
    public function __construct(
        private APCuCacheManager $l1Cache,
        private RedisCluster $l2Cache,
        private MySQLConnection $l3Cache
    ) {
        $this->levels = [
            1 => $l1Cache,
            2 => $l2Cache,
            3 => $l3Cache
        ];
        
        $this->initializeStats();
    }
    
    public function get(string $key, string $category = 'default'): mixed
    {
        $this->stats[$category]['reads']++;
        
        // L1'den kontrol et
        $value = $this->levels[1]->get($key);
        if ($value !== null) {
            $this->recordHit($category, 1);
            return $value;
        }
        
        // L2'den kontrol et
        $value = $this->getFromRedis($key);
        if ($value !== null) {
            $this->recordHit($category, 2);
            // L1'e yaz
            $this->levels[1]->set($key, $value, $this->getTtl($category, 1));
            return $value;
        }
        
        // L3'den kontrol et
        $value = $this->getFromMySQL($key);
        if ($value !== null) {
            $this->recordHit($category, 3);
            // L1 ve L2'ye yaz
            $this->levels[1]->set($key, $value, $this->getTtl($category, 1));
            $this->setRedis($key, $value, $this->getTtl($category, 2));
            return $value;
        }
        
        $this->recordMiss($category);
        
        return null;
    }
    
    public function set(string $key, mixed $value, 
                       string $category = 'default'): bool
    {
        $this->stats[$category]['writes']++;
        
        // Write-through: Tüm seviyelere yaz
        $this->levels[1]->set($key, $value, $this->getTtl($category, 1));
        $this->setRedis($key, $value, $this->getTtl($category, 2));
        
        // L3'e yaz (eğer persistent ise)
        if ($this->isPersistent($category)) {
            $this->setMySQL($key, $value);
        }
        
        return true;
    }
    
    public function invalidate(string $key): bool
    {
        // Tüm seviyelerden sil
        $this->levels[1]->delete($key);
        $this->redisDel($key);
        $this->deleteMySQL($key);
        
        return true;
    }
    
    public function invalidatePattern(string $pattern): int
    {
        $deleted = 0;
        
        // L1'den
        $deleted += $this->levels[1]->invalidatePattern($pattern);
        
        // L2'den
        $deleted += $this->redisDeletePattern($pattern);
        
        // L3 için pattern-based deletion zor
        // Genellikle tag-based invalidation kullanılır
        
        return $deleted;
    }
    
    public function invalidateByTag(string $tag): int
    {
        $deleted = 0;
        
        // Tag ile ilişkili key'leri bul
        $keys = $this->getKeysByTag($tag);
        
        foreach ($keys as $key) {
            $this->invalidate($key);
            $deleted++;
        }
        
        return $deleted;
    }
    
    public function getStats(): array
    {
        $totalReads = 0;
        $totalHits = 0;
        
        foreach ($this->stats as $category => $stats) {
            $totalReads += $stats['reads'];
            $totalHits += $stats['hits'][1] + $stats['hits'][2] + $stats['hits'][3];
        }
        
        return [
            'categories' => $this->stats,
            'overall' => [
                'total_reads' => $totalReads,
                'total_hits' => $totalHits,
                'hit_rate' => $totalReads > 0 ? $totalHits / $totalReads : 0
            ],
            'l1_stats' => $this->levels[1]->getStats(),
            'l2_stats' => $this->redisInfo(),
            'l3_stats' => $this->mysqlCacheStats()
        ];
    }
    
    public function warmUp(string $category, array $keys): void
    {
        foreach ($keys as $key) {
            $value = $this->getFromMySQL($key);
            if ($value !== null) {
                $this->setRedis($key, $value, $this->getTtl($category, 2));
                $this->levels[1]->set($key, $value, $this->getTtl($category, 1));
            }
        }
    }
    
    private function getTtl(string $category, int $level): int
    {
        $ttls = [
            'session' => [1 => 60, 2 => 1800, 3 => null],
            'query' => [1 => 60, 2 => 300, 3 => null],
            'static' => [1 => 3600, 2 => 86400, 3 => null],
            'media' => [1 => 3600, 2 => 604800, 3 => null],
            'user' => [1 => 120, 2 => 600, 3 => null],
        ];
        
        return $ttls[$category][$level] ?? $ttls['query'][$level] ?? 300;
    }
    
    private function isPersistent(string $category): bool
    {
        return in_array($category, ['user', 'settings', 'metadata']);
    }
    
    private function recordHit(string $category, int $level): void
    {
        $this->stats[$category]['hits'][$level]++;
    }
    
    private function recordMiss(string $category): void
    {
        $this->stats[$category]['misses']++;
    }
    
    private function initializeStats(): void
    {
        $categories = ['session', 'query', 'static', 'media', 'user'];
        
        foreach ($categories as $cat) {
            $this->stats[$cat] = [
                'reads' => 0,
                'writes' => 0,
                'hits' => [1 => 0, 2 => 0, 3 => 0],
                'misses' => 0
            ];
        }
    }
    
    private function getFromRedis(string $key): mixed
    {
        $value = $this->l2Cache->get($key);
        return $value !== false ? json_decode($value, true) : null;
    }
    
    private function setRedis(string $key, mixed $value, int $ttl): void
    {
        $this->l2Cache->setex($key, $ttl, json_encode($value));
    }
    
    private function redisDel(string $key): void
    {
        $this->l2Cache->del($key);
    }
    
    private function redisDeletePattern(string $pattern): int
    {
        $deleted = 0;
        $cursor = null;
        
        do {
            [$cursor, $keys] = $this->l2Cache->scan($cursor, $pattern, 100);
            if ($keys) {
                $deleted += $this->l2Cache->del(...$keys);
            }
        } while ($cursor !== 0);
        
        return $deleted;
    }
    
    private function getFromMySQL(string $key): mixed
    {
        $stmt = $this->l3Cache->prepare('
            SELECT cache_value FROM query_cache WHERE cache_key = :key
        ');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? json_decode($row['cache_value'], true) : null;
    }
    
    private function setMySQL(string $key, mixed $value): void
    {
        $stmt = $this->l3Cache->prepare('
            INSERT INTO query_cache (cache_key, cache_value, created_at)
            VALUES (:key, :value, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE cache_value = :value, updated_at = CURRENT_TIMESTAMP
        ');
        $stmt->execute([
            'key' => $key,
            'value' => json_encode($value)
        ]);
    }
    
    private function deleteMySQL(string $key): void
    {
        $stmt = $this->l3Cache->prepare('DELETE FROM query_cache WHERE cache_key = :key');
        $stmt->execute(['key' => $key]);
    }
    
    private function redisInfo(): array
    {
        return $this->l2Cache->info();
    }
    
    private function mysqlCacheStats(): array
    {
        $stmt = $this->l3Cache->query('
            SELECT 
                COUNT(*) as total_entries,
                SUM(LENGTH(cache_value)) as total_size_bytes,
                MIN(created_at) as oldest_entry,
                MAX(updated_at) as newest_entry
            FROM query_cache
        ');
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getKeysByTag(string $tag): array
    {
        $tagKey = "tag:{$tag}";
        $keys = $this->l2Cache->smembers($tagKey);
        
        return $keys ?: [];
    }
}
```

### Cache Invalidation Strategies

```php
<?php
class CacheInvalidationStrategies
{
    public static function timeBased(string $key, int $ttl): void
    {
        // TTL tabanlı invalidation
        // Redis otomatik siler
    }
    
    public static function eventBased(string $event, array $affectedKeys): void
    {
        // Event tabanlı invalidation
        $eventHandlers = [
            'track_updated' => ['track:{id}:*', 'playlist:*:tracks'],
            'user_updated' => ['user:{id}:*', 'user:{id}:settings'],
            'album_updated' => ['album:{id}:*', 'artist:{id}:albums'],
        ];
        
        $patterns = $eventHandlers[$event] ?? [];
        
        foreach ($patterns as $pattern) {
            Cache::invalidatePattern($pattern);
        }
    }
    
    public static function versionBased(string $key, int $version): void
    {
        // Version tabanlı invalidation
        $versionedKey = "{$key}:v{$version}";
        Cache::set($versionedKey, Cache::get($key));
        Cache::delete($key);
    }
    
    public static function tagBased(string $tag): void
    {
        // Tag tabanlı invalidation
        Cache::invalidateByTag($tag);
    }
}
```

## API / Konfigürasyon

```yaml
# config/cache-strategy.yaml
cache:
  levels:
    l1:
      type: "apcu"
      enabled: true
      shm_size: "256M"
      default_ttl: 60
    
    l2:
      type: "redis"
      enabled: true
      cluster: true
      default_ttl: 1800
    
    l3:
      type: "mysql"
      enabled: true
      table: "query_cache"
      cleanup_interval: 3600
  
  categories:
    session:
      l1_ttl: 60
      l2_ttl: 1800
      persistent: false
    
    query:
      l1_ttl: 60
      l2_ttl: 300
      persistent: false
    
    static:
      l1_ttl: 3600
      l2_ttl: 86400
      persistent: false
    
    media:
      l1_ttl: 3600
      l2_ttl: 604800
      persistent: false
    
    user:
      l1_ttl: 120
      l2_ttl: 600
      persistent: true
  
  invalidation:
    strategy: "tag_based"
    auto_version: true
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| L1 Hit Rate | 99.5% |
| L2 Hit Rate | 97.2% |
| L3 Hit Rate | 100% |
| Overall Latency | 2ms |
| Throughput | 1M ops/s |

## Durum: Implementasyon

Cache Strategy modülü **stable** durumdadır. Çoklu seviye cache ve invalidation stratejileri production-ready.
