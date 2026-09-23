---
title: "Logging Middleware"
layer: K7
category: "Middleware"
date: "2026-09-20"
version: "1.0.0"
status: "draft"
---

# Logging Middleware

## Genel Bakış

Logging Middleware, her HTTP isteğini ve yanıtını loglayarak izlenebilirlik sağlar. Correlation ID (trace ID) oluşturarak istek zincirlerini takip eder. Structured logging formatı kullanarak log analizi ve monitoring araçlarıyla entegrasyon kolaylığı sağlar.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
├── [2] Security Headers
├── [3] Rate Limit
├── [4] CORS
├── [5] Origin Check
├── [6] Session
├── [7] CSRF
├── [8] Request Validation
│
▼
[9] Logging Middleware  ← Request/Response loglama
│
├── [10] Error Handler
│
▼
Handler
```

Logging middleware, request validation'dan sonra çalışır. Doğrulanmış istekleri loglayarak gereksiz log hacmini azaltır.

## Teknik Detaylar

### Correlation ID Akışı

```
İstek Gelir
│
├── X-Request-ID başlığı var mı?
│   ├── Evet → Mevcut ID'yi kullan
│   └── Hayır → Yeni ID üret (uuid4)
│
├── Request log kaydı
│   ├── correlation_id
│   ├── method, path, query
│   ├── client_ip, user_agent
│   └── timestamp
│
▼
Handler çalıştırılır (süre ölçülür)
│
▼
Response log kaydı
│   ├── correlation_id
│   ├── status_code
│   ├── duration_ms
│   └── response_size
│
▼
Yanıt döner → X-Request-ID header'ı eklenir
```

### Structured Logging Formatı

```json
{
    "timestamp": "2026-09-20T15:45:00.000Z",
    "level": "info",
    "message": "HTTP Request",
    "context": {
        "correlation_id": "550e8400-e29b-41d4-a716-446655440000",
        "method": "POST",
        "path": "/api/v1/tracks",
        "query": "format=flac",
        "client_ip": "192.168.1.100",
        "user_agent": "Mozilla/5.0...",
        "user_id": "usr_abc123",
        "request_size": 1024
    },
    "response": {
        "status_code": 201,
        "duration_ms": 45.2,
        "response_size": 256
    }
}
```

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

class LoggingMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private bool $logRequestBody;
    private bool $logResponseBody;
    private int $maxBodyLength;

    public function __construct(
        LoggerInterface $logger,
        array $config = []
    ) {
        $this->logger = $logger;
        $this->logRequestBody = $config['log_request_body'] ?? false;
        $this->logResponseBody = $config['log_response_body'] ?? false;
        $this->maxBodyLength = $config['max_body_length'] ?? 1000;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Correlation ID oluştur veya mevcut olanı kullan
        $correlationId = $this->getOrCreateCorrelationId($request);
        $request = $request->withAttribute('correlation_id', $correlationId);

        // Request logunu kaydet
        $this->logRequest($request, $correlationId);

        // Süre ölçümü başlat
        $startTime = microtime(true);

        // Handler'ı çalıştır
        $response = $handler->handle($request);

        // Süre hesapla
        $duration = (microtime(true) - $startTime) * 1000;

        // Response logunu kaydet
        $this->logResponse($response, $correlationId, $duration);

        // Response'a correlation ID ekle
        return $response->withHeader('X-Request-ID', $correlationId);
    }

    private function getOrCreateCorrelationId(
        ServerRequestInterface $request
    ): string {
        $existingId = $request->getHeaderLine('X-Request-ID');

        if (!empty($existingId) && $this->isValidUuid($existingId)) {
            return $existingId;
        }

        return Uuid::uuid4()->toString();
    }

    private function logRequest(
        ServerRequestInterface $request,
        string $correlationId
    ): void {
        $context = [
            'correlation_id' => $correlationId,
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
            'query' => $request->getUri()->getQuery(),
            'client_ip' => $this->getClientIp($request),
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'content_type' => $request->getHeaderLine('Content-Type'),
            'content_length' => $request->getHeaderLine('Content-Length'),
        ];

        // İstek body'sini logla (gerekirse)
        if ($this->logRequestBody) {
            $body = (string) $request->getBody();
            $context['request_body'] = mb_substr($body, 0, $this->maxBodyLength);
        }

        $this->logger->info('HTTP Request', $context);
    }

    private function logResponse(
        ResponseInterface $response,
        string $correlationId,
        float $duration
    ): void {
        $context = [
            'correlation_id' => $correlationId,
            'status_code' => $response->getStatusCode(),
            'duration_ms' => round($duration, 2),
            'response_size' => $response->getBody()->getSize(),
        ];

        $level = $this->getLogLevel($response->getStatusCode());
        $this->logger->log($level, 'HTTP Response', $context);
    }

    private function getLogLevel(int $statusCode): string
    {
        if ($statusCode >= 500) {
            return 'error';
        }
        if ($statusCode >= 400) {
            return 'warning';
        }
        return 'info';
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        return $request->getHeaderLine('X-Forwarded-For')
            ?: $request->getServerParams()['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    private function isValidUuid(string $uuid): bool
    {
        return preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        ) === 1;
    }
}
```

### Log Çıktı Örnekleri

```
[2026-09-20 15:45:00] INFO: HTTP Request
  correlation_id: 550e8400-e29b-41d4-a716-446655440000
  method: POST
  path: /api/v1/tracks
  client_ip: 192.168.1.100
  user_agent: Mozilla/5.0
  content_type: application/json
  content_length: 1024

[2026-09-20 15:45:00] INFO: HTTP Response
  correlation_id: 550e8400-e29b-41d4-a716-446655440000
  status_code: 201
  duration_ms: 45.23
  response_size: 256
```

### Performance Logging

```php
// Slow query tespiti
private function logSlowRequest(
    ServerRequestInterface $request,
    float $duration,
    string $correlationId
): void {
    $threshold = 1000; // 1 saniye

    if ($duration > $threshold) {
        $this->logger->warning('Slow Request Detected', [
            'correlation_id' => $correlationId,
            'path' => $request->getUri()->getPath(),
            'duration_ms' => round($duration, 2),
            'threshold_ms' => $threshold,
        ]);
    }
}
```

## Kod / Konfigürasyon

### Konfigürasyon

```php
// config/logging.php
return [
    'channels' => [
        'access' => [
            'driver' => 'daily',
            'path' => storage_path('logs/access.log'),
            'days' => 30,
        ],
        'slow' => [
            'driver' => 'daily',
            'path' => storage_path('logs/slow.log'),
            'days' => 90,
        ],
    ],
    'middleware' => [
        'log_request_body' => env('LOG_REQUEST_BODY', false),
        'log_response_body' => false,
        'max_body_length' => 1000,
        'slow_threshold_ms' => 1000,
        'exclude_paths' => [
            '/health',
            '/metrics',
        ],
    ],
];
```

### Log Rotate ve Cleanup

```php
// Günlük log temizleme
class LogCleanupCommand
{
    public function execute(): void
    {
        $logDir = storage_path('logs');
        $days = 30;

        $files = glob("{$logDir}/*.log.*");
        $threshold = strtotime("-{$days} days");

        foreach ($files as $file) {
            if (filemtime($file) < $threshold) {
                unlink($file);
            }
        }
    }
}
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-3 Logger | `psr/log` | Evet |
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| Ramsey UUID | `ramsey/uuid` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Temel request/response loglama | Planlandı |
| Faz 2 | Correlation ID üretimi | Planlandı |
| Faz 3 | Structured logging formatı | Planlandı |
| Faz 4 | Slow request detection | Planlandı |
| Faz 5 | Log rotation ve cleanup | Planlandı |
