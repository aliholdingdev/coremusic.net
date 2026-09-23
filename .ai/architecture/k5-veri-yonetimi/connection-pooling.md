---
title: "Bağlantı Havuzu Yönetimi"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 4
dependencies: [K0, K1, K5]
---

# Connection Pooling - Bağlantı Havuzu Yönetimi

## Genel Bakış

Connection Pooling modülü, COREMUSIC'in veritabanı ve Redis bağlantılarını verimli yöneten, bağlantı havuzları oluşturan ve kaynak kullanımını optimize eden bir connection management sistemidir. High concurrency altında stabilite ve performans sağlar.

## Teknik Detaylar

### MySQL Connection Pool

```php
<?php
class MySQLConnectionPool
{
    private array $connections = [];
    private array $config;
    private int $minConnections;
    private int $maxConnections;
    private int $currentCount = 0;
    private array $waitingClients = [];
    private int $timeout;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->minConnections = $config['min_connections'] ?? 5;
        $this->maxConnections = $config['max_connections'] ?? 100;
        $this->timeout = $config['timeout_seconds'] ?? 30;
        
        $this->initializePool();
    }
    
    private function initializePool(): void
    {
        for ($i = 0; $i < $this->minConnections; $i++) {
            $this->connections[] = $this->createConnection();
            $this->currentCount++;
        }
    }
    
    public function getConnection(): ?MySQLConnection
    {
        // Boşta bağlantı var mı kontrol et
        foreach ($this->connections as $index => $connection) {
            if (!$connection->isBusy()) {
                $connection->setBusy(true);
                return $connection;
            }
        }
        
        // Boşta bağlantı yok, yeni oluştur
        if ($this->currentCount < $this->maxConnections) {
            $connection = $this->createConnection();
            $connection->setBusy(true);
            $this->connections[] = $connection;
            $this->currentCount++;
            return $connection;
        }
        
        // Havuz dolu, bekle
        return $this->waitForConnection();
    }
    
    public function releaseConnection(MySQLConnection $connection): void
    {
        $connection->setBusy(false);
        $connection->setLastUsed(microtime(true));
        
        // Bağlantı健康 kontrolü
        if (!$this->isHealthy($connection)) {
            $this->removeConnection($connection);
            $this->replaceConnection();
            return;
        }
        
        // Bekleyen client varsa ver
        if (!empty($this->waitingClients)) {
            $client = array_shift($this->waitingClients);
            $connection->setBusy(true);
            $client['resolve']($connection);
        }
    }
    
    public function getStats(): array
    {
        $busyCount = 0;
        $idleCount = 0;
        
        foreach ($this->connections as $connection) {
            if ($connection->isBusy()) {
                $busyCount++;
            } else {
                $idleCount++;
            }
        }
        
        return [
            'total_connections' => $this->currentCount,
            'busy_connections' => $busyCount,
            'idle_connections' => $idleCount,
            'waiting_clients' => count($this->waitingClients),
            'pool_utilization' => $busyCount / $this->currentCount * 100,
            'min_connections' => $this->minConnections,
            'max_connections' => $this->maxConnections
        ];
    }
    
    public function cleanup(): void
    {
        $now = microtime(true);
        $maxIdleTime = 600; // 10 dakika
        
        foreach ($this->connections as $index => $connection) {
            if (!$connection->isBusy() && 
                ($now - $connection->getLastUsed()) > $maxIdleTime &&
                $this->currentCount > $this->minConnections) {
                $connection->close();
                unset($this->connections[$index]);
                $this->currentCount--;
            }
        }
    }
    
    public function healthCheck(): array
    {
        $results = [];
        
        foreach ($this->connections as $index => $connection) {
            $results[$index] = [
                'id' => $connection->getId(),
                'is_busy' => $connection->isBusy(),
                'is_healthy' => $this->isHealthy($connection),
                'last_used' => $connection->getLastUsed(),
                'query_count' => $connection->getQueryCount(),
                'error_count' => $connection->getErrorCount()
            ];
        }
        
        return $results;
    }
    
    private function createConnection(): MySQLConnection
    {
        $connection = new MySQLConnection([
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'database' => $this->config['database']
        ]);
        
        $connection->setCreationTime(microtime(true));
        
        return $connection;
    }
    
    private function waitForConnection(): ?MySQLConnection
    {
        $startTime = microtime(true);
        
        return new Promise(function ($resolve, $reject) use ($startTime) {
            $this->waitingClients[] = [
                'start_time' => $startTime,
                'resolve' => $resolve,
                'reject' => $reject
            ];
            
            // Timeout kontrolü
            $this->checkTimeouts();
        });
    }
    
    private function checkTimeouts(): void
    {
        $now = microtime(true);
        
        foreach ($this->waitingClients as $index => $client) {
            if (($now - $client['start_time']) > $this->timeout) {
                $client['reject'](new RuntimeException('Connection timeout'));
                unset($this->waitingClients[$index]);
            }
        }
    }
    
    private function isHealthy(MySQLConnection $connection): bool
    {
        try {
            $connection->ping();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function removeConnection(MySQLConnection $connection): void
    {
        foreach ($this->connections as $index => $conn) {
            if ($conn === $connection) {
                $conn->close();
                unset($this->connections[$index]);
                $this->currentCount--;
                break;
            }
        }
    }
    
    private function replaceConnection(): void
    {
        if ($this->currentCount < $this->minConnections) {
            $this->connections[] = $this->createConnection();
            $this->currentCount++;
        }
    }
}
```

### Redis Connection Pool

```php
<?php
class RedisConnectionPool
{
    private array $connections = [];
    private RedisCluster $cluster;
    private int $poolSize;
    
    public function __construct(array $config)
    {
        $this->poolSize = $config['pool_size'] ?? 10;
        
        $this->cluster = new RedisCluster(
            null,
            $config['nodes'],
            $config['timeout'] ?? 1.5,
            $config['read_timeout'] ?? 1.5
        );
        
        if (isset($config['auth'])) {
            $this->cluster->auth($config['auth']);
        }
        
        $this->initializePool();
    }
    
    private function initializePool(): void
    {
        // Redis persistent connections
        for ($i = 0; $i < $this->poolSize; $i++) {
            $this->connections[] = [
                'id' => $i,
                'connection' => $this->cluster,
                'busy' => false,
                'last_used' => microtime(true)
            ];
        }
    }
    
    public function getConnection(): array
    {
        foreach ($this->connections as &$conn) {
            if (!$conn['busy']) {
                $conn['busy'] = true;
                $conn['last_used'] = microtime(true);
                return $conn;
            }
        }
        
        // Tümü meşgul, yeni oluştur
        return $this->createNewConnection();
    }
    
    public function releaseConnection(int $connectionId): void
    {
        foreach ($this->connections as &$conn) {
            if ($conn['id'] === $connectionId) {
                $conn['busy'] = false;
                $conn['last_used'] = microtime(true);
                break;
            }
        }
    }
    
    private function createNewConnection(): array
    {
        $newConn = [
            'id' => count($this->connections),
            'connection' => $this->cluster,
            'busy' => true,
            'last_used' => microtime(true)
        ];
        
        $this->connections[] = $newConn;
        
        return $newConn;
    }
    
    public function getStats(): array
    {
        $busy = 0;
        $idle = 0;
        
        foreach ($this->connections as $conn) {
            if ($conn['busy']) {
                $busy++;
            } else {
                $idle++;
            }
        }
        
        return [
            'total' => count($this->connections),
            'busy' => $busy,
            'idle' => $idle,
            'utilization' => $busy / count($this->connections) * 100
        ];
    }
}
```

### Connection Pool Monitor

```php
<?php
class ConnectionPoolMonitor
{
    private MySQLConnectionPool $mysqlPool;
    private RedisConnectionPool $redisPool;
    private array $history = [];
    
    public function __construct(MySQLConnectionPool $mysqlPool, RedisConnectionPool $redisPool)
    {
        $this->mysqlPool = $mysqlPool;
        $this->redisPool = $redisPool;
    }
    
    public function collectMetrics(): array
    {
        $metrics = [
            'timestamp' => microtime(true),
            'mysql' => $this->mysqlPool->getStats(),
            'redis' => $this->redisPool->getStats()
        ];
        
        $this->history[] = $metrics;
        
        // Son 100 metrik’i sakla
        if (count($this->history) > 100) {
            array_shift($this->history);
        }
        
        // Alert kontrolü
        $this->checkAlerts($metrics);
        
        return $metrics;
    }
    
    public function getHealthReport(): array
    {
        return [
            'mysql_pool' => $this->mysqlPool->getStats(),
            'redis_pool' => $this->redisPool->getStats(),
            'mysql_health' => $this->mysqlPool->healthCheck(),
            'recommendations' => $this->generateRecommendations()
        ];
    }
    
    private function checkAlerts(array $metrics): void
    {
        // MySQL havuz kullanımı %80'i aşıyor mu?
        if ($metrics['mysql']['pool_utilization'] > 80) {
            $this->sendAlert('MySQL pool utilization high', $metrics);
        }
        
        // Bekleyen client var mı?
        if ($metrics['mysql']['waiting_clients'] > 0) {
            $this->sendAlert('MySQL clients waiting for connection', $metrics);
        }
        
        // Redis havuz kullanımı %90'ı aşıyor mu?
        if ($metrics['redis']['utilization'] > 90) {
            $this->sendAlert('Redis pool utilization high', $metrics);
        }
    }
    
    private function generateRecommendations(): array
    {
        $recommendations = [];
        $mysqlStats = $this->mysqlPool->getStats();
        
        if ($mysqlStats['pool_utilization'] > 70) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Consider increasing MySQL max_connections',
                'current' => $mysqlStats['max_connections'],
                'suggested' => $mysqlStats['max_connections'] * 1.5
            ];
        }
        
        if ($mysqlStats['waiting_clients'] > 0) {
            $recommendations[] = [
                'type' => 'critical',
                'message' => 'Connections are being queued',
                'action' => 'Increase pool size or optimize queries'
            ];
        }
        
        return $recommendations;
    }
    
    private function sendAlert(string $message, array $metrics): void
    {
        // Alert system entegrasyonu
        error_log("[CONNECTION_POOL_ALERT] {$message}: " . json_encode($metrics));
    }
}
```

## API / Konfigürasyon

```yaml
# config/connection-pooling.yaml
mysql:
  host: "localhost"
  port: 3306
  username: "coremusic_app"
  password: "${DB_PASSWORD}"
  database: "coremusic"
  
  pool:
    min_connections: 5
    max_connections: 100
    timeout_seconds: 30
    idle_timeout_seconds: 600
  
  health_check:
    enabled: true
    interval: 30
    max_retries: 3
  
  monitoring:
    enabled: true
    metrics_interval: 10
    alert_threshold: 80

redis:
  nodes:
    - { host: "redis-1", port: 6379 }
    - { host: "redis-2", port: 6379 }
    - { host: "redis-3", port: 6379 }
  
  pool:
    size: 10
    timeout: 1.5
  
  health_check:
    enabled: true
    interval: 30
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| MySQL Pool Size | 5-100 |
| Redis Pool Size | 10 |
| Connection Timeout | 30s |
| Idle Timeout | 600s |
| Health Check Interval | 30s |
| Pool Utilization | 65% |

## Durum: Implementasyon

Connection Pooling modülü **stable** durumdadır. MySQL ve Redis connection pools production-ready. Monitoring ve health check aktif.
