---
type: architecture
category: logging
title: "CoreMusic — Deep Logging System Architecture"
date: 2026-08-10
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Deep Logging System Architecture

**Zorunlu Baglantilar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[index.md]] · [[keys.md]]

---

## 1. Amac

CoreMusic platformu icin **tam kapsamli, cok katmanli, derin loglama sistemi** tasarimi. Sistem; guvenlik olaylarini, performans metriklerini, kullanici aktivitelerini ve sistem olaylarini **her seviyede** (TRACE-CRITICAL) loglar. Hem dosya tabanli (.log) hem de veritabani tabanli (MySQL) cift katmanli depolama saglar.

---

## 2. Kapsam

| Kapsam | Kapsam Disi |
|--------|-------------|
| Tum PHP backend servisleri | Frontend JS loglama (ayri ADR) |
| 10 subdomain panel | C++ audio engine loglama |
| 7 backend servis | Donanim seviyesi loglama |
| Middleware otomatik loglama | Network packet capture |
| MySQL 18 BCNF DB log tablolari | External ELK/Loki entegrasyonu |
| Real-time izleme | - |
| Otomatik redaction | - |

---

## 3. Terminoloji

| Terim | Tanim |
|-------|-------|
| **PSR-3** | PHP Logger Interface standardi (php-fig/log) |
| **Monolog** | PSR-3 uyumlu PHP loglama kutuphanesi |
| **Structured Logging** | Loglarin context array olarak kaydedilmesi |
| **Redaction** | Hassas verilerin otomatik maskelemesi |
| **Log Rotation** | Dosya rotasyonu (gunluk/buyukluk) |
| **Correlation ID** | Istek bazli takip kimligi (UUID) |
| **Trace ID | Daginik sistemlerde istek zincirleme |
| **Span** | Bir islemin baslangic-bitis suresi |

---

## 4. Mimari Tasarim — 3 Katmanli Loglama

```
+-----------------------------------------------------+
|                   LOG PRODUCERS                       |
|  (Middleware, Helper, AOP Decorator, Manuel Logger)   |
+------------------------+------------------------------+
                         |
+------------------------v------------------------------+
|                 LOG PROCESSORS                        |
|  (Redaction, Formatting, Enrichment, Sampling)        |
+------------------------+------------------------------+
                         |
+------------------------v------------------------------+
|                LOG DESTINATIONS                       |
|  +--------------+  +--------------+  +-----------+   |
|  |  .log Files  |  |   MySQL DB   |  | Real-Time |   |
|  |  (dosya)     |  |   (5 tablo)  |  | (monitor) |   |
|  +--------------+  +--------------+  +-----------+   |
+-----------------------------------------------------+
```

---

## 5. Loglama Seviyeleri (6 Seviye)

| Seviye | Sayi | Kullanim | Production | Ornek |
|--------|------|----------|------------|-------|
| **TRACE** | 10 | Her function call, her DB query | Kapali | `SELECT * FROM users - 3ms` |
| **DEBUG** | 20 | Gelistirme bilgileri | Kapali | `Session created: abc123` |
| **INFO** | 30 | Normal operasyon | Acik | `User logged in: id=42` |
| **WARN** | 40 | Potansiyel sorunlar | Acik | `Rate limit %80'e ulasti` |
| **ERROR** | 50 | Hata durumlari | Acik | `DB connection failed` |
| **CRITICAL** | 60 | Sistem durmasi | Acik | `Auth bypass tespit edildi` |

---

## 6. Loglama Kategorileri (5 Kategori)

| # | Kategori | Dosya Adi | MySQL Tablosu | Kullanim |
|---|----------|-----------|---------------|----------|
| 1 | **Guvenlik** | `security.log` | `log_security` | CSRF, auth, rate limit, brute force |
| 2 | **Performans** | `performance.log` | `log_performance` | Query time, TTFB, memory, disk |
| 3 | **Kullanici** | `activity.log` | `log_activity` | Login, CRUD, playlist, dinleme |
| 4 | **Sistem** | `system.log` | `log_system` | Start/stop, cron, deployment |
| 5 | **Genel** | `app.log` | `log_events` | Tum olaylar, genel loglama |

---

## 7. Dosya Yapisi

### 7.1 Log Dosyalari Yapisi

```
logs/
+-- app/                              # Genel loglar
|   +-- app.log                       # Tum INFO+ loglari
|   +-- app-2026-08-10.log            # Gunluk rotasyon
|   +-- app-2026-08-09.log
|   +-- ...
+-- security/                         # Guvenlik loglari
|   +-- security.log
|   +-- security-2026-08-10.log
|   +-- ...
+-- performance/                      # Performans loglari
|   +-- performance.log
|   +-- performance-2026-08-10.log
|   +-- ...
+-- activity/                         # Kullanici aktivite loglari
|   +-- activity.log
|   +-- activity-2026-08-10.log
|   +-- ...
+-- system/                           # Sistem loglari
|   +-- system.log
|   +-- system-2026-08-10.log
|   +-- ...
+-- error/                            # Sadece ERROR/CRITICAL
|   +-- error.log
|   +-- error-2026-08-10.log
|   +-- ...
+-- debug/                            # Sadece TRACE/DEBUG (dev)
|   +-- debug.log
|   +-- ...
+-- archive/                          # Aylik arsiv
|   +-- 2026-07/
|   |   +-- app-2026-07.tar.gz
|   |   +-- security-2026-07.tar.gz
|   |   +-- ...
|   +-- 2026-08/
|       +-- ...
```

### 7.2 Dosya Formati

```
[2026-08-10 14:30:00.123] [INFO] [security] [abc123-def456] User login successful {"user_id":42,"ip":"192.168.1.1","method":"password"}
```

**Alanlar:**
| Alan | Format | Aciklama |
|------|--------|----------|
| Timestamp | `YYYY-MM-DD HH:MM:SS.mmm` | Milisaniye hassasiyeti |
| Level | `TRACE/DEBUG/INFO/WARN/ERROR/CRITICAL` | Log seviyesi |
| Category | `security/performance/activity/system/app` | Kategori |
| Correlation ID | UUID v4 | Istek takip kimligi |
| Message | Serbest metin | Olay aciklamasi |
| Context | JSON (opsiyonel) | Ek veriler |

---

## 8. MySQL Tablo Tasarimi (5 Tablo)

### 8.1 log_events (Genel Olay Logu)

```sql
CREATE TABLE log_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL COMMENT 'UUID v4 - istek takip',
    level ENUM('TRACE','DEBUG','INFO','WARN','ERROR','CRITICAL') NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'app',
    message TEXT NOT NULL,
    context JSON NULL COMMENT 'Ek veriler (JSON formatinda)',
    user_id INT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL COMMENT 'IPv4/IPv6',
    user_agent VARCHAR(500) NULL,
    request_method VARCHAR(10) NULL,
    request_uri VARCHAR(2000) NULL,
    response_code SMALLINT UNSIGNED NULL,
    memory_usage INT UNSIGNED NULL COMMENT 'Byte cinsinden',
    peak_memory INT UNSIGNED NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3) COMMENT 'Milisaniye hassasiyeti',

    INDEX idx_created_at (created_at),
    INDEX idx_level (level),
    INDEX idx_category (category),
    INDEX idx_user_id (user_id),
    INDEX idx_correlation_id (correlation_id),
    INDEX idx_level_category (level, category),
    INDEX idx_created_level (created_at, level),
    FULLTEXT INDEX ft_message (message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Genel olay loglari - tum uygulama olaylari';
```

### 8.2 log_security (Guvenlik Logu)

```sql
CREATE TABLE log_security (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL,
    event_type ENUM(
        'CSRF_VIOLATION',
        'AUTH_ATTEMPT_SUCCESS',
        'AUTH_ATTEMPT_FAILED',
        'AUTH_BYPASS_DETECTED',
        'RATE_LIMIT_EXCEEDED',
        'BRUTE_FORCE_DETECTED',
        'SESSION_HIJACK_ATTEMPT',
        'XSS_ATTEMPT',
        'SQL_INJECTION_ATTEMPT',
        'PERMISSION_DENIED',
        'CREDENTIAL_VAULT_ACCESS',
        'API_KEY_USED',
        'TOKEN_REFRESH',
        'SESSION_CREATED',
        'SESSION_DESTROYED',
        'CORS_VIOLATION',
        'CONTENT_SECURITY_POLICY_VIOLATION',
        'SUSPICIOUS_REQUEST'
    ) NOT NULL COMMENT 'Guvenlik olay tipi',
    severity ENUM('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL DEFAULT 'MEDIUM',
    user_id INT UNSIGNED NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NULL,
    request_method VARCHAR(10) NULL,
    request_uri VARCHAR(2000) NULL,
    request_body JSON NULL COMMENT 'Otomatik redaction uygulanmis',
    threat_indicators JSON NULL COMMENT 'Tehdit gostergeleri',
    blocked TINYINT(1) DEFAULT 0 COMMENT 'Olay engellendi mi?',
    response_action VARCHAR(100) NULL COMMENT 'Uygulanan aksiyon',
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_created_at (created_at),
    INDEX idx_event_type (event_type),
    INDEX idx_severity (severity),
    INDEX idx_ip_address (ip_address),
    INDEX idx_user_id (user_id),
    INDEX idx_severity_created (severity, created_at),
    INDEX idx_event_type_created (event_type, created_at),
    FULLTEXT INDEX ft_uri (request_uri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Guvenlik olay loglari - OWASP Top 10:2025 uyumlu';
```

### 8.3 log_performance (Performans Logu)

```sql
CREATE TABLE log_performance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL,
    metric_type ENUM(
        'QUERY_TIME',
        'API_RESPONSE_TIME',
        'TTFB',
        'MEMORY_USAGE',
        'PEAK_MEMORY',
        'DISK_IO',
        'CACHE_HIT',
        'CACHE_MISS',
        'DB_CONNECTION_TIME',
        'FILE_UPLOAD_TIME',
        'FFMPEG_PROCESS_TIME',
        'AUDIO_DECODE_TIME',
        'PAGE_RENDER_TIME',
        'MIDDLEWARE_TIME',
        'TOTAL_REQUEST_TIME'
    ) NOT NULL COMMENT 'Metrik tipi',
    metric_name VARCHAR(100) NULL COMMENT 'Ozel metrik adi (DB query, endpoint vb.)',
    metric_value DECIMAL(12,3) NOT NULL COMMENT 'Metrik degeri (milisaniye, byte vb.)',
    metric_unit ENUM('ms','bytes','count','percent','ops') NOT NULL DEFAULT 'ms',
    context JSON NULL COMMENT 'Ek detaylar',
    user_id INT UNSIGNED NULL,
    request_uri VARCHAR(2000) NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_created_at (created_at),
    INDEX idx_metric_type (metric_type),
    INDEX idx_metric_name (metric_name),
    INDEX idx_user_id (user_id),
    INDEX idx_metric_type_created (metric_type, created_at),
    INDEX idx_created_metric (created_at, metric_type),
    FULLTEXT INDEX ft_metric_name (metric_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Performans metrik loglari - query time, TTFB, memory';
```

### 8.4 log_system (Sistem Logu)

```sql
CREATE TABLE log_system (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NULL,
    event_type ENUM(
        'SERVICE_START',
        'SERVICE_STOP',
        'SERVICE_ERROR',
        'CRON_JOB_START',
        'CRON_JOB_COMPLETE',
        'CRON_JOB_FAILED',
        'DEPLOYMENT_START',
        'DEPLOYMENT_COMPLETE',
        'DEPLOYMENT_FAILED',
        'CONFIG_CHANGE',
        'SCHEMA_MIGRATION',
        'BACKUP_START',
        'BACKUP_COMPLETE',
        'BACKUP_FAILED',
        'HEALTH_CHECK',
        'HEALTH_CHECK_FAILED',
        'DISK_SPACE_WARNING',
        'MEMORY_WARNING',
        'CPU_WARNING',
        'SSL_CERT_EXPIRING',
        'CRON_SCHEDULED',
        'QUEUE_OVERFLOW',
        'WORKER_START',
        'WORKER_STOP',
        'GRACEFUL_SHUTDOWN',
        'EMERGENCY_STOP'
    ) NOT NULL COMMENT 'Sistem olay tipi',
    severity ENUM('INFO','WARNING','ERROR','CRITICAL') NOT NULL DEFAULT 'INFO',
    component VARCHAR(100) NOT NULL COMMENT 'Sistem bileseni (nginx, php-fpm, mysql vb.)',
    message TEXT NOT NULL,
    context JSON NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_created_at (created_at),
    INDEX idx_event_type (event_type),
    INDEX idx_severity (severity),
    INDEX idx_component (component),
    INDEX idx_severity_created (severity, created_at),
    INDEX idx_event_type_created (event_type, created_at),
    FULLTEXT INDEX ft_message (message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sistem olay loglari - servis, cron, deployment';
```

### 8.5 log_activity (Kullanici Aktivite Logu)

```sql
CREATE TABLE log_activity (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    action ENUM(
        'LOGIN',
        'LOGOUT',
        'REGISTER',
        'PROFILE_UPDATE',
        'PASSWORD_CHANGE',
        'PASSWORD_RESET',
        'MUSIC_PLAY',
        'MUSIC_PAUSE',
        'MUSIC_STOP',
        'MUSIC_SKIP',
        'MUSIC_ADD_TO_PLAYLIST',
        'MUSIC_REMOVE_FROM_PLAYLIST',
        'PLAYLIST_CREATE',
        'PLAYLIST_DELETE',
        'PLAYLIST_UPDATE',
        'ALBUM_VIEW',
        'ARTIST_VIEW',
        'SEARCH',
        'DOWNLOAD_START',
        'DOWNLOAD_COMPLETE',
        'DOWNLOAD_FAILED',
        'UPLOAD_START',
        'UPLOAD_COMPLETE',
        'FILE_MANAGE',
        'SETTINGS_CHANGE',
        'DEVICE_REGISTER',
        'DEVICE_SYNC',
        'SHARING',
        'COMMENT_POST',
        'LIKE',
        'FOLLOW',
        'UNFOLLOW',
        'NAVIGATION',
        'PAGE_VIEW',
        'API_CALL'
    ) NOT NULL COMMENT 'Kullanici aksiyonu',
    entity_type VARCHAR(50) NULL COMMENT 'Varlik turu (music, album, playlist vb.)',
    entity_id INT UNSIGNED NULL COMMENT 'Varlik ID',
    entity_name VARCHAR(500) NULL COMMENT 'Varlik adi (redacted)',
    metadata JSON NULL COMMENT 'Ek bilgiler',
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    device_type ENUM('desktop','mobile','tablet','car','studio','home') NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity_type (entity_type),
    INDEX idx_user_action (user_id, action),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_action_created (action, created_at),
    FULLTEXT INDEX ft_entity_name (entity_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kullanici aktivite loglari - CRUD, dinleme, arama';
```

---

## 9. PHP Logger Yapisi

### 9.1 Dizin Yapisi

```
shared/src/
+-- Logging/
|   +-- Logger.php                      # Ana Logger class (PSR-3)
|   +-- LoggerInterface.php             # PSR-3 uyumlu arayuz
|   +-- LogProcessor.php                # Redaction, enrichment
|   +-- LogFormatter.php                # Dosya formati
|   +-- Channel/
|   |   +-- SecurityLogger.php          # Guvenlik ozel
|   |   +-- PerformanceLogger.php       # Performans ozel
|   |   +-- ActivityLogger.php          # Kullanici aktivite ozel
|   |   +-- SystemLogger.php            # Sistem ozel
|   +-- Handler/
|   |   +-- DailyRotatingFileHandler.php # Gunluk dosya rotasyonu
|   |   +-- MySqlHandler.php            # MySQL yazici
|   |   +-- ErrorFileHandler.php        # Sadece ERROR+
|   |   +-- StreamHandler.php           # Konsol ciktisi
|   +-- Formatter/
|   |   +-- LineFormatter.php           # Tek satir format
|   |   +-- JsonFormatter.php           # JSON format (MySQL icin)
|   +-- Middleware/
|   |   +-- RequestLoggingMiddleware.php # Otomatik request loglama
|   |   +-- SecurityLoggingMiddleware.php # Guvenlik olayi loglama
|   +-- Decorator/
|   |   +-- LoggingDecorator.php        # AOP benzeri decorator
|   |   +-- TimingDecorator.php         # Performans olcumu
|   +-- Helper/
|       +-- Log.php                      # Static helper (Log::info, Log::security)
```

### 9.2 PSR-3 Uyumlu Logger

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    private array $channels = [];
    private LogProcessor $processor;

    public function __construct(LogProcessor $processor)
    {
        $this->processor = $processor;
        $this->initChannels();
    }

    private function initChannels(): void
    {
        $this->channels['app'] = $this->createChannel('app');
        $this->channels['security'] = $this->createChannel('security');
        $this->channels['performance'] = $this->createChannel('performance');
        $this->channels['activity'] = $this->createChannel('activity');
        $this->channels['system'] = $this->createChannel('system');
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $message = $this->processor->redact($message);
        $context = $this->processor->redactContext($context);
        $context['correlation_id'] = $this->processor->getCorrelationId();

        foreach ($this->channels as $channel) {
            $channel->log($level, $message, $context);
        }
    }
}
```

### 9.3 Static Helper Class

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Logging\Helper;

class Log
{
    private static ?LoggerInterface $logger = null;

    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function info(string $message, array $context = []): void
    {
        self::$logger?->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::$logger?->error($message, $context);
    }

    public static function security(string $message, array $context = []): void
    {
        $context['channel'] = 'security';
        self::$logger?->warning($message, $context);
    }

    public static function performance(string $metric, float $value, string $unit = 'ms', array $context = []): void
    {
        $context['metric'] = ['name' => $metric, 'value' => $value, 'unit' => $unit];
        self::$logger?->info("Performance: {$metric} = {$value}{$unit}", $context);
    }

    public static function activity(string $action, array $context = []): void
    {
        $context['channel'] = 'activity';
        self::$logger?->info("Activity: {$action}", $context);
    }

    public static function system(string $event, string $component, array $context = []): void
    {
        $context['channel'] = 'system';
        $context['component'] = $component;
        self::$logger?->info("System: {$event}", $context);
    }
}
```

### 9.4 Otomatik Redaction Processor

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Logging;

class LogProcessor
{
    private const REDACT_PATTERNS = [
        '/password["\s:=]+["\']?([^"\'&\s]+)/i' => 'password: [REDACTED]',
        '/api[_-]?key["\s:=]+["\']?([^"\'&\s]+)/i' => 'api_key: [REDACTED]',
        '/token["\s:=]+["\']?([^"\'&\s]+)/i' => 'token: [REDACTED]',
        '/secret["\s:=]+["\']?([^"\'&\s]+)/i' => 'secret: [REDACTED]',
        '/authorization["\s:=]+["\']?(Bearer\s+)?([^"\'&\s]+)/i' => 'Authorization: [REDACTED]',
        '/cookie["\s:=]+["\']?([^"\'&\s]+)/i' => 'cookie: [REDACTED]',
        '/\b\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}\b/' => '[REDACTED_CARD]',
        '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/' => '[REDACTED_EMAIL]',
    ];

    private const REDACT_EXCLUDE_FIELDS = [
        'user_id', 'ip_address', 'correlation_id', 'created_at',
        'request_method', 'response_code', 'metric_type', 'metric_value',
    ];

    public function redact(string $message): string
    {
        foreach (self::REDACT_PATTERNS as $pattern => $replacement) {
            $message = preg_replace($pattern, $replacement, $message);
        }
        return $message;
    }

    public function redactContext(array $context): array
    {
        foreach ($context as $key => $value) {
            if (in_array($key, self::REDACT_EXCLUDE_FIELDS, true)) {
                continue;
            }
            if (is_string($value)) {
                $context[$key] = $this->redact($value);
            } elseif (is_array($value)) {
                $context[$key] = $this->redactContext($value);
            }
        }
        return $context;
    }

    public function getCorrelationId(): string
    {
        return $_SERVER['HTTP_X_CORRELATION_ID'] ?? uniqid('', true);
    }
}
```

### 9.5 Middleware Otomatik Loglama

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Logging\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use CoreMusic\Logging\Helper\Log;

class RequestLoggingMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $start = hrtime(true);
        $correlationId = $this->generateCorrelationId();

        Log::info('Request started', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'ip' => $this->getClientIp($request),
            'correlation_id' => $correlationId,
        ]);

        $response = $handler->handle($request);

        $duration = (hrtime(true) - $start) / 1e6;

        Log::info('Request completed', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'status' => $response->getStatusCode(),
            'duration_ms' => round($duration, 3),
            'correlation_id' => $correlationId,
        ]);

        Log::performance('total_request_time', $duration, 'ms', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'status' => $response->getStatusCode(),
        ]);

        if ($response->getStatusCode() >= 400) {
            Log::error('Request failed', [
                'status' => $response->getStatusCode(),
                'uri' => (string) $request->getUri(),
                'duration_ms' => round($duration, 3),
            ]);
        }

        return $response;
    }
}
```

### 9.6 AOP/Decorator Pattern

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Logging\Decorator;

use CoreMusic\Logging\Helper\Log;

class TimingDecorator
{
    private object $subject;
    private string $component;

    public function __construct(object $subject, string $component)
    {
        $this->subject = $subject;
        $this->component = $component;
    }

    public function __call(string $method, array $args): mixed
    {
        $start = hrtime(true);

        try {
            $result = $this->subject->{$method}(...$args);
            $duration = (hrtime(true) - $start) / 1e6;

            Log::performance("{$this->component}.{$method}", $duration, 'ms', [
                'component' => $this->component,
                'method' => $method,
                'success' => true,
            ]);

            return $result;

        } catch (\Throwable $e) {
            $duration = (hrtime(true) - $start) / 1e6;

            Log::error("{$this->component}.{$method} failed", [
                'component' => $this->component,
                'method' => $method,
                'duration_ms' => round($duration, 3),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }
}
```

---

## 10. Rotasyon ve Arsivleme Stratejisi

### 10.1 Gunluk Rotasyon

| Parametre | Deger |
|-----------|-------|
| Rotasyon periyodu | Gunluk (her gece 00:00 UTC) |
| Dosya adi formati | `{category}-{YYYY-MM-DD}.log` |
| Aktif dosya | `{category}.log` (symlink veya en son dosya) |
| Saklama suresi | 90 gun aktif, sonra arsiv |

### 10.2 Aylik Arsivleme

| Parametre | Deger |
|-----------|-------|
| Arsiv formati | `{category}-{YYYY-MM}.tar.gz` |
| Arsiv konumu | `logs/archive/{YYYY-MM}/` |
| Saklama suresi | 12 ay |
| Otomatik silme | 12 ay sonra `logs/archive/`'den kaldirilir |

### 10.3 MySQL Rotasyonu

| Parametre | Deger |
|-----------|-------|
| Tablo partition | AYLIK partition by `created_at` |
| Aktif partition | Mevcut ay + sonraki ay |
| Arsiv partition | 6 ay oncesine kadar |
| Silme stratejisi | 6 ay eski partition'lar DROP |
| Backup | Arsiv oncesi mysqldump |

---

## 11. Real-Time Izleme Sistemi

### 11.1 Izleme Bilesenleri

```
+-----------------------------------------------------+
|              REAL-TIME MONITOR                        |
|                                                      |
|  +--------------+  +--------------+                  |
|  |  File Watcher |  |  DB Poller   |                  |
|  |  (inotify)    |  |  (MySQL)     |                  |
|  +------+-------+  +------+-------+                  |
|         |                 |                           |
|         v                 v                           |
|  +-----------------------------------+               |
|  |       Event Aggregator            |               |
|  |  (Buffer + Batch Insert)          |               |
|  +----------------+------------------+               |
|                   |                                  |
|           +-------+-------+                          |
|           v               v                          |
|  +------------+  +------------+                      |
|  | WebSocket  |  |  Dashboard |                      |
|  |  Server    |  |  (Admin)   |                      |
|  +------------+  +------------+                      |
+-----------------------------------------------------+
```

### 11.2 Dashboard Verileri

| Metrik | Kaynak | Guncelleme |
|--------|--------|------------|
| Toplam log sayisi | `log_events` COUNT | Her 5 sn |
| Hata orani (%) | `log_events` WHERE level IN (ERROR, CRITICAL) | Her 5 sn |
| Ortalama TTFB | `log_performance` AVG | Her 10 sn |
| Aktif kullanici | `log_activity` DISTINCT user_id | Her 30 sn |
| Guvenlik uyarilari | `log_security` WHERE severity IN (HIGH, CRITICAL) | Her 5 sn |
| Sistem sagligi | `log_system` WHERE event_type = 'HEALTH_CHECK' | Her 60 sn |

### 11.3 Alarm Esikleri

| Metrik | Esik | Aksiyon |
|--------|------|---------|
| Hata orani | >%5 | Email + Slack |
| TTFB | >2000ms | Email |
| Rate limit | >%80 | Email + Block |
| Guvenlik | CRITICAL | Anlik bildirim |
| Disk | >%80 | Email |
| Memory | >%85 | Email |

---

## 12. Entegrasyon Noktalari

### 12.1 Middleware Pipeline'a Entegrasyon

```
OriginCheck -> Cors -> RateLimiter -> SecurityHeaders -> SessionManager -> Csrf -> BypassAuth -> Auth -> Permission -> Validation
         |              |                   |              |              |              |            |            |           |
   SecurityLogger  SecurityLogger    RequestLogger   SecurityLogger  SecurityLogger   SecurityLogger  RequestLogger  SecurityLogger  SecurityLogger
```

**Yeni Middleware Eklenecek:**
| # | Middleware | Gorev |
|---|-----------|-------|
| 7 | `RequestLoggingMiddleware` | Her istegi otomatik logla |
| 8 | `SecurityLoggingMiddleware` | Guvenlik olaylarini logla |

### 12.2 Controller Entegrasyonu

```php
// Controller icinde kullanimi
class MusicController
{
    public function play(Request $request): Response
    {
        $musicId = $request->getAttribute('id');

        Log::activity('MUSIC_PLAY', [
            'entity_type' => 'music',
            'entity_id' => $musicId,
        ]);

        // ... is mantigi
    }
}
```

### 12.3 Repository Entegrasyonu

```php
// TimingDecorator ile otomatik performans loglama
$musicRepo = new TimingDecorator(
    new MusicRepository($pdo),
    'MusicRepository'
);

// Otomatik olarak:
// - MusicRepository.findAll basladi
// - MusicRepository.findAll 3.45ms surdu
```

---

## 13. Konfigurasyon

### 13.1 .env Loglama Ayarlari

```env
# Loglama Seviyesi
LOG_LEVEL=INFO                    # TRACE, DEBUG, INFO, WARN, ERROR, CRITICAL
LOG_CHANNEL=app                   # Varsayilan kanal

# Dosya Loglama
LOG_FILE_ENABLED=true
LOG_FILE_PATH=logs/
LOG_FILE_ROTATION=daily           # daily, size
LOG_FILE_PERMISSION=0644

# MySQL Loglama
LOG_DB_ENABLED=true
LOG_DB_CONNECTION=mysql           # PDO connection name
LOG_DB_TABLE_PREFIX=log_

# Performans
LOG_SLOW_QUERY_THRESHOLD=1000    # ms
LOG_SLOW_API_THRESHOLD=500       # ms
LOG_MEMORY_WARNING_PERCENT=80

# Redaction
LOG_REDACTION_ENABLED=true
LOG_REDACTION_PATTERNS=password,api_key,token,secret

# Real-Time
LOG_REALTIME_ENABLED=true
LOG_WEBSOCKET_PORT=9743
LOG_DASHBOARD_REFRESH=5          # saniye
```

### 13.2 Service Container Kaydi

```php
// Container'a logger kaydi
$container->set('Logger', function ($container) {
    $processor = new LogProcessor();

    $handlers = [];

    if (getenv('LOG_FILE_ENABLED') === 'true') {
        $handlers[] = new DailyRotatingFileHandler(
            getenv('LOG_FILE_PATH'),
            getenv('LOG_LEVEL')
        );
        $handlers[] = new ErrorFileHandler(
            getenv('LOG_FILE_PATH') . '/error'
        );
    }

    if (getenv('LOG_DB_ENABLED') === 'true') {
        $handlers[] = new MySqlHandler(
            $container->get('Database'),
            getenv('LOG_DB_TABLE_PREFIX')
        );
    }

    $logger = new Logger($processor, $handlers);
    Log::setLogger($logger);

    return $logger;
});
```

---

## 14. Test Stratejisi

| Test Turu | Kapsam | Hedef |
|-----------|--------|-------|
| Unit Test | LogProcessor redaction, Formatter | >=90% |
| Unit Test | Static Helper methods | >=90% |
| Unit Test | ChannelLogger her kanal | >=90% |
| Integration Test | MySQL handler write/read | >=80% |
| Integration Test | Middleware auto-logging | >=80% |
| E2E Test | Real-time dashboard | >=70% |
| Performance Test | 1000 log/s write throughput | - |
| Security Test | Redaction coverage | 100% |

---

## 15. Uygulama Plani (10 Faz)

| Faz | Gorev | Sure | Bagimlilik |
|-----|-------|------|------------|
| 1 | MySQL tablolari olustur (5 tablo) | 1 saat | - |
| 2 | LogProcessor + Redaction sistemi | 2 saat | Faz 1 |
| 3 | Logger + Channel yapisi (PSR-3) | 3 saat | Faz 2 |
| 4 | Dosya handler'lari (DailyRotating, Error) | 2 saat | Faz 3 |
| 5 | MySQL handler | 2 saat | Faz 1, 3 |
| 6 | Static Helper (Log class) | 1 saat | Faz 3 |
| 7 | Middleware'ler (Request, Security) | 3 saat | Faz 3, 6 |
| 8 | AOP Decorator (Timing) | 2 saat | Faz 6 |
| 9 | Real-time izleme (WebSocket + Dashboard) | 4 saat | Faz 5 |
| 10 | Testler + Dokumantasyon | 3 saat | Tum fazlar |

**Toplam Tahmini Sure:** ~23 saat (3-4 is gunu)

---

## 16. Riskler ve Azaltma

| Risk | Olasilik | Etki | Azaltma |
|------|----------|------|---------|
| Performans dususu (log write) | Orta | Yuksek | Async write, batch insert |
| MySQL tablo buyuklugu | Yuksek | Orta | Partition, rotation, cleanup |
| Redaction eksik kalmasi | Dusuk | Yuksek | Pattern coverage test |
| Dosya izinleri | Dusuk | Orta | Automated permission check |
| Disk alani dolmasi | Orta | Yuksek | Monitoring + alert |

---

## 17. Bagimliliklar

| Bagimlilik | Versiyon | Zorunlu |
|------------|---------|---------|
| monolog/monolog | ^3.0 | Evet |
| psr/log | ^2.0/^3.0 | Evet |
| ext-pdo | - | Evet |
| ext-json | - | Evet |
| ext-mbstring | - | Evet |
| ReactPHP (WebSocket) | ^1.0 | Opsiyonel |

---

## 18. Cross References

| Kaynak | Hedef | Iliski |
|--------|-------|--------|
| [[CLAUDE.md]] | Hard Guardrails | Zero Hallucination - Redaction |
| [[AGENTS.md]] | Health Check | Log-based monitoring |
| [[ADR-022-database-hardened-security]] | Security | Redaction policy |
| [[ADR-042-vault-restructuring-2026-08-03]] | Audit trail | Log format standardi |
| [[ADR-010-csrf-protection-strategy]] | CSRF | Security log events |
| [[ADR-013-rate-limiting-apcu]] | Rate limit | Performance + Security logs |

---

## 19. Quality Report

| Metrik | Deger |
|--------|-------|
| Version | 1.0.0 |
| Status | Draft - Insan onayi bekliyor |
| MySQL Tablolari | 5 |
| PHP Class'lari | ~15 |
| Log Seviyeleri | 6 (TRACE-CRITICAL) |
| Log Kategorileri | 5 |
| Faz Sayisi | 10 |
| Tahmini Sure | 23 saat |
| Bagimliliklar | 4 (monolog, psr/log, pdo, json) |
| Test Coverage Hedefi | >=80% |
| Risk | 5 (tumu azaltilabilir) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team - Human Mode - Truth Mode
**Status:** DRAFT - Onay bekliyor
