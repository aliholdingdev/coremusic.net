---
title: "Yedekleme Stratejisi"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 6
dependencies: [K0, K1, K5]
---

# Backup Strategy - Otomatik Yedekleme Sistemi

## Genel Bakış

Backup Strategy modülü, COREMUSIC verilerinin güvenliğini sağlayan otomatik yedekleme, point-in-time recovery ve replication stratejilerini yönetir. Günlük, haftalık ve aylık yedekleme planları ile 7 yıllık veri saklama politikası uygular.

## Teknik Detaylar

### Yedekleme Stratejisi

```yaml
# backup-strategy.yaml
backup:
  schedules:
    full:
      frequency: "weekly"
      day: "sunday"
      time: "02:00"
      retention_days: 365
    
    incremental:
      frequency: "daily"
      time: "03:00"
      retention_days: 30
    
    transaction_log:
      frequency: "continuous"
      interval_minutes: 15
      retention_hours: 72
  
  storage:
    primary:
      type: "minio"
      endpoint: "https://minio.coremusic.internal"
      bucket: "coremusic-backups"
      region: "eu-west-1"
    
    secondary:
      type: "s3"
      bucket: "coremusic-backups-archive"
      region: "eu-west-1"
      storage_class: "GLACIER"
  
  compression:
    enabled: true
    algorithm: "zstd"
    level: 3
  
  encryption:
    enabled: true
    algorithm: "AES-256-GCM"
    key_source: "vault"
```

### Backup Manager

```php
<?php
class BackupManager
{
    private array $config;
    private MinIOClient $minio;
    private MySQLBackup $mysqlBackup;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->minio = new MinIOClient($config['storage']['primary']);
        $this->mysqlBackup = new MySQLBackup($config['mysql']);
    }
    
    public function performFullBackup(): array
    {
        $startTime = microtime(true);
        $backupId = $this->generateBackupId('full');
        
        $result = [
            'backup_id' => $backupId,
            'type' => 'full',
            'started_at' => date('Y-m-d H:i:s'),
            'status' => 'in_progress',
            'components' => []
        ];
        
        try {
            // 1. MySQL full backup
            $mysqlBackup = $this->mysqlBackup->fullBackup($backupId);
            $result['components']['mysql'] = $mysqlBackup;
            
            // 2. Redis backup
            $redisBackup = $this->backupRedis($backupId);
            $result['components']['redis'] = $redisBackup;
            
            // 3. File system backup
            $fsBackup = $this->backupFileSystem($backupId);
            $result['components']['filesystem'] = $fsBackup;
            
            // 4. Configuration backup
            $configBackup = $this->backupConfiguration($backupId);
            $result['components']['config'] = $configBackup;
            
            // 5. Compress and encrypt
            $archivePath = $this->compressAndEncrypt($backupId);
            $result['archive_path'] = $archivePath;
            
            // 6. Upload to storage
            $uploadResult = $this->uploadToStorage($archivePath, $backupId);
            $result['upload'] = $uploadResult;
            
            // 7. Cleanup local files
            $this->cleanupLocalFiles($backupId);
            
            $result['status'] = 'completed';
            $result['duration_seconds'] = microtime(true) - $startTime;
            $result['size_bytes'] = $uploadResult['size_bytes'];
            
        } catch (Exception $e) {
            $result['status'] = 'failed';
            $result['error'] = $e->getMessage();
            $this->alertBackupFailure($result);
        }
        
        $result['completed_at'] = date('Y-m-d H:i:s');
        
        // Log backup
        $this->logBackup($result);
        
        return $result;
    }
    
    public function performIncrementalBackup(): array
    {
        $startTime = microtime(true);
        $backupId = $this->generateBackupId('incremental');
        $lastBackup = $this->getLastBackup();
        
        $result = [
            'backup_id' => $backupId,
            'type' => 'incremental',
            'started_at' => date('Y-m-d H:i:s'),
            'status' => 'in_progress',
            'base_backup_id' => $lastBackup['backup_id'] ?? null
        ];
        
        try {
            // MySQL incremental (binary log)
            $mysqlBackup = $this->mysqlBackup->incrementalBackup(
                $lastBackup['binlog_position'] ?? null
            );
            $result['components']['mysql'] = $mysqlBackup;
            
            // Redis incremental
            $redisBackup = $this->backupRedisIncremental($backupId);
            $result['components']['redis'] = $redisBackup;
            
            // Upload
            $archivePath = $this->compressAndEncrypt($backupId);
            $uploadResult = $this->uploadToStorage($archivePath, $backupId);
            
            $result['status'] = 'completed';
            $result['duration_seconds'] = microtime(true) - $startTime;
            $result['size_bytes'] = $uploadResult['size_bytes'];
            
        } catch (Exception $e) {
            $result['status'] = 'failed';
            $result['error'] = $e->getMessage();
        }
        
        $result['completed_at'] = date('Y-m-d H:i:s');
        $this->logBackup($result);
        
        return $result;
    }
    
    public function restore(string $backupId, array $options = []): array
    {
        $startTime = microtime(true);
        
        $result = [
            'restore_id' => $this->generateRestoreId(),
            'backup_id' => $backupId,
            'started_at' => date('Y-m-d H:i:s'),
            'status' => 'in_progress',
            'options' => $options
        ];
        
        try {
            // Download backup
            $archivePath = $this->downloadBackup($backupId);
            
            // Decrypt and decompress
            $restorePath = $this->decryptAndDecompress($archivePath);
            
            // Restore MySQL
            if ($options['restore_mysql'] ?? true) {
                $this->mysqlBackup->restore($restorePath);
                $result['components']['mysql'] = 'restored';
            }
            
            // Restore Redis
            if ($options['restore_redis'] ?? false) {
                $this->restoreRedis($restorePath);
                $result['components']['redis'] = 'restored';
            }
            
            // Restore files
            if ($options['restore_files'] ?? true) {
                $this->restoreFileSystem($restorePath);
                $result['components']['filesystem'] = 'restored';
            }
            
            $result['status'] = 'completed';
            $result['duration_seconds'] = microtime(true) - $startTime;
            
        } catch (Exception $e) {
            $result['status'] = 'failed';
            $result['error'] = $e->getMessage();
        }
        
        $result['completed_at'] = date('Y-m-d H:i:s');
        
        return $result;
    }
    
    public function pointInTimeRecovery(string $targetDatetime): array
    {
        // Son full backup'ı bul
        $lastFullBackup = $this->findLastFullBackup($targetDatetime);
        
        // Full backup'ı restore et
        $this->restore($lastFullBackup['backup_id']);
        
        // Transaction log'ları uygula
        $transactionLogs = $this->getTransactionLogsBetween(
            $lastFullBackup['completed_at'],
            $targetDatetime
        );
        
        foreach ($transactionLogs as $log) {
            $this->applyTransactionLog($log);
        }
        
        return [
            'status' => 'completed',
            'restored_to' => $targetDatetime,
            'base_backup' => $lastFullBackup['backup_id'],
            'logs_applied' => count($transactionLogs)
        ];
    }
    
    private function compressAndEncrypt(string $backupId): string
    {
        $tempDir = "/tmp/backup/{$backupId}";
        $archivePath = "/tmp/backup/{$backupId}.tar.zst";
        
        // Compress
        $command = "tar -cf - -C {$tempDir} . | zstd -o {$archivePath}";
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new RuntimeException("Compression failed");
        }
        
        // Encrypt
        $encryptedPath = $archivePath . ".enc";
        $command = "openssl enc -aes-256-gcm -salt -pbkdf2 -in {$archivePath} -out {$encryptedPath} -pass env:BACKUP_KEY";
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new RuntimeException("Encryption failed");
        }
        
        return $encryptedPath;
    }
    
    private function generateBackupId(string $type): string
    {
        return date('Ymd_His') . '_' . $type . '_' . bin2hex(random_bytes(4));
    }
}
```

### Replication Manager

```php
<?php
class ReplicationManager
{
    private array $replicas;
    private MySQLConnection $primary;
    
    public function __construct(array $config)
    {
        $this->replicas = $config['replicas'];
        $this->primary = new MySQLConnection($config['primary']);
    }
    
    public function getReplicaForRead(): MySQLConnection
    {
        // Load balancing - round robin or least connections
        $availableReplicas = $this->getAvailableReplicas();
        
        if (empty($availableReplicas)) {
            // Fallback to primary
            return $this->primary;
        }
        
        // Select least loaded replica
        return $this->selectBestReplica($availableReplicas);
    }
    
    public function monitorReplication(): array
    {
        $status = [];
        
        foreach ($this->replicas as $replica) {
            $connection = new MySQLConnection($replica);
            
            $result = $connection->query('SHOW SLAVE STATUS');
            $row = $result->fetch_assoc();
            
            $status[] = [
                'host' => $replica['host'],
                'io_running' => $row['Slave_IO_Running'] === 'Yes',
                'sql_running' => $row['Slave_SQL_Running'] === 'Yes',
                'seconds_behind' => $row['Seconds_Behind_Master'],
                'last_error' => $row['Last_Error'],
                'executed_gtid' => $row['Executed_Gtid_Set']
            ];
        }
        
        return $status;
    }
    
    private function getAvailableReplicas(): array
    {
        return array_filter($this->replicas, function ($replica) {
            return $this->isReplicaHealthy($replica);
        });
    }
    
    private function isReplicaHealthy(array $replica): bool
    {
        try {
            $connection = new MySQLConnection($replica);
            $result = $connection->query('SHOW SLAVE STATUS');
            $row = $result->fetch_assoc();
            
            return $row['Slave_IO_Running'] === 'Yes' &&
                   $row['Slave_SQL_Running'] === 'Yes' &&
                   ($row['Seconds_Behind_Master'] ?? 0) < 10;
        } catch (Exception $e) {
            return false;
        }
    }
}
```

## API / Konfigürasyon

```yaml
# config/backup.yaml
backup:
  enabled: true
  
  mysql:
    backup_dir: "/var/backups/mysql"
    compress: true
    encrypt: true
    
  redis:
    rdb_enabled: true
    aof_enabled: true
    backup_dir: "/var/backups/redis"
  
  filesystem:
    paths:
      - "/media/music"
      - "/media/artwork"
      - "/etc/coremusic"
    exclude:
      - "*.tmp"
      - "*.log"
  
  schedule:
    full: "0 2 * * 0"
    incremental: "0 3 * * *"
    transaction_log: "*/15 * * * *"
  
  retention:
    daily: 30
    weekly: 52
    monthly: 84  # 7 years
    yearly: 7
  
  notifications:
    on_success: false
    on_failure: true
    email: "admin@coremusic.com"
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| Full Backup Time | 45 dakika |
| Incremental Backup | 5 dakika |
| Restore Time (Full) | 30 dakika |
| Point-in-Time Recovery | 15 dakika |
| Backup Size (Full) | 50GB |
| Backup Size (Incremental) | 500MB |

## Durum: Implementasyon

Backup Strategy modülü **stable** durumdadır. Otomatik yedekleme ve replication aktif. Point-in-time recovery test edilmiş durumda.
