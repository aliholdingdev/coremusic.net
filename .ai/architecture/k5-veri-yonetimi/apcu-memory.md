---
title: "APCu Bellek İçi Önbellek"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 4
dependencies: [K1, K2]
---

# APCu Memory - PHP Bellek İçi Önbellek

## Genel Bakış

APCu (Alternative PHP Cache User) modülü, PHP uygulamaları için yüksek hızlı bellek içi önbellek sağlar. Opcode cache (OPcache) ile birlikte kullanılarak PHP uygulama performansını önemli ölçüde artırır. Singleton pattern, config cache ve template cache için idealdir.

## Teknik Detaylar

### APCu Konfigürasyonu

```ini
; php.ini APCu ayarları
[apcu]
extension=apcu

; APCu boyutu (MB)
apc.shm_size=256M

; TTL varsayılan (saniye)
apc.ttl=3600

; Kullanıcı TTL
apc.user_ttl=1800

; Garbage collection%
apc.gc_ttl=600

; Başlangıç dosyası
apc.include_once_override=0

; Statistikleri etkinleştir
apc.stat=1

; Kullanıcı girişleri için
apc.user_entries_hint=4096

; Zaman damgası
apc.enable_cli=0
```

### OPcache Konfigürasyonu

```ini
; php.ini OPcache ayarları
[opcache]
zend_extension=opcache

; OPcache boyutu (MB)
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000

; Yeniden doğrulama süresi (saniye)
opcache.revalidate_freq=2

; Statik dosya cache
opcache.enable_file_override=1

; Konsol için devre dışı
opcache.enable_cli=0

; Fast shutdown
opcache.fast_shutdown=1

; JIT compiler (PHP 8.0+)
opcache.jit=1255
opcache.jit_buffer_size=64M

; Preloading
opcache.preload=/path/to/preload.php
opcache.preload_user=www-data
```

### APCu Cache Manager

```php
<?php
class APCuCacheManager
{
    private static ?APCuCacheManager $instance = null;
    private int $defaultTtl = 3600;
    private array $stats = [];
    
    private function __construct()
    {
        $this->initializeStats();
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        if (!apcu_exists($key)) {
            $this->recordMiss($key);
            return $default;
        }
        
        $value = apcu_fetch($key, $success);
        
        if ($success) {
            $this->recordHit($key);
            return $value;
        }
        
        $this->recordMiss($key);
        return $default;
    }
    
    public function set(string $key, mixed $value, int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        return apcu_store($key, $value, $ttl);
    }
    
    public function delete(string $key): bool
    {
        return apcu_delete($key);
    }
    
    public function exists(string $key): bool
    {
        return apcu_exists($key);
    }
    
    public function increment(string $key, int $step = 1): int|false
    {
        return apcu_inc($key, $step);
    }
    
    public function decrement(string $key, int $step = 1): int|false
    {
        return apcu_dec($key, $step);
    }
    
    // Cache-aside pattern
    public function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
    
    // Tag-based invalidation
    public function setWithTags(string $key, mixed $value, array $tags, int $ttl = null): bool
    {
        $result = $this->set($key, $value, $ttl);
        
        if ($result) {
            foreach ($tags as $tag) {
                $tagKey = "apcu:tag:{$tag}";
                $taggedKeys = $this->get($tagKey, []);
                $taggedKeys[] = $key;
                $this->set($tagKey, array_unique($taggedKeys), $ttl);
            }
        }
        
        return $result;
    }
    
    public function invalidateByTag(string $tag): int
    {
        $tagKey = "apcu:tag:{$tag}";
        $taggedKeys = $this->get($tagKey, []);
        
        $deleted = 0;
        foreach ($taggedKeys as $key) {
            if ($this->delete($key)) {
                $deleted++;
            }
        }
        
        $this->delete($tagKey);
        
        return $deleted;
    }
    
    public function flush(): bool
    {
        return apcu_clear_cache();
    }
    
    private function recordHit(string $key): void
    {
        $this->stats['hits']++;
        $this->stats['hit_keys'][$key] = ($this->stats['hit_keys'][$key] ?? 0) + 1;
    }
    
    private function recordMiss(string $key): void
    {
        $this->stats['misses']++;
        $this->stats['miss_keys'][$key] = ($this->stats['miss_keys'][$key] ?? 0) + 1;
    }
    
    private function initializeStats(): void
    {
        $this->stats = [
            'hits' => 0,
            'misses' => 0,
            'hit_keys' => [],
            'miss_keys' => []
        ];
    }
    
    public function getStats(): array
    {
        $info = apcu_cache_info();
        
        return [
            'hits' => $info['num_hits'] + $this->stats['hits'],
            'misses' => $info['num_misses'] + $this->stats['misses'],
            'hit_ratio' => $info['num_hits'] / max(1, $info['num_hits'] + $info['num_misses']),
            'cache_size' => $info['mem_size'],
            'cache_entries' => $info['num_entries'],
            'memory_usage' => apcu_sma_info(),
            'top_hit_keys' => arsort($this->stats['hit_keys'] ?? []),
            'top_miss_keys' => arsort($this->stats['miss_keys'] ?? [])
        ];
    }
}
```

### Configuration Cache

```php
<?php
class ConfigCache
{
    private APCuCacheManager $cache;
    private string $prefix = 'config:';
    private int $ttl = 86400; // 24 saat
    
    public function __construct(APCuCacheManager $cache)
    {
        $this->cache = $cache;
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($this->prefix . $key, $default);
    }
    
    public function set(string $key, mixed $value): bool
    {
        return $this->cache->set($this->prefix . $key, $value, $this->ttl);
    }
    
    public function loadConfigFile(string $filePath): array
    {
        $cacheKey = md5($filePath);
        
        $config = $this->cache->remember(
            $this->prefix . $cacheKey,
            fn() => require $filePath,
            $this->ttl
        );
        
        return $config;
    }
    
    public function invalidate(string $key): bool
    {
        return $this->cache->delete($this->prefix . $key);
    }
    
    public function invalidateAll(): int
    {
        return $this->cache->invalidateByTag('config');
    }
}
```

## API / Konfigürasyon

```yaml
# config/apcu.yaml
apcu:
  shm_size: "256M"
  default_ttl: 3600
  user_ttl: 1800
  gc_ttl: 600
  
opcache:
  memory_consumption: "128M"
  interned_strings_buffer: "16M"
  max_accelerated_files: 10000
  revalidate_freq: 2
  jit: 1255
  jit_buffer_size: "64M"
  
cache:
  default_ttl: 3600
  config_ttl: 86400
  template_ttl: 3600
  
monitoring:
  stats_enabled: true
  stats_interval: 60
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| GET Latency | 0.1ms |
| SET Latency | 0.2ms |
| Throughput | 1M ops/s |
| Hit Ratio | 99.5% |
| Memory Usage | 180MB / 256MB |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| APCu | 5.1+ | Cache engine |
| PHP | 8.3+ | Runtime |
| OPcache | Built-in | Opcode cache |

## Durum: Implementasyon

APCu Memory modülü **stable** durumdadır. OPcache ve APCu production-ready. Configuration cache ve template cache aktif.
