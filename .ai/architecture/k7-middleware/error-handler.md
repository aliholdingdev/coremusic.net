---
title: "Error Handler Middleware"
layer: K7
category: "Middleware"
date: "2026-09-20"
version: "1.0.0"
status: "draft"
---

# Error Handler Middleware

## Genel Bakış

Error Handler Middleware, pipeline içinde oluşan tüm istisnaları yakalar ve uygun HTTP yanıtlarına dönüştürür. Exception mapping kullanarak uygulama hatalarını API dostu JSON yanıtlarına çevirir. Production ve development ortamları için farklı hata detayları sağlar.

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
├── [9] Logging
│
▼
[10] Error Handler Middleware  ← Son savunma hattı
│
▼
Handler (K6)
```

Error handler, pipeline'ın en son katmanında çalışır. Diğer middleware'lerden ve handler'dan gelen tüm istisnaları yakalar.

## Teknik Detaylar

### İstisna Hiyerarşisi

```
Throwable
│
├── Exception (PHP)
│   ├── HttpException
│   │   ├── BadRequestException (400)
│   │   ├── UnauthorizedException (401)
│   │   ├── ForbiddenException (403)
│   │   ├── NotFoundException (404)
│   │   ├── MethodNotAllowedException (405)
│   │   ├── ConflictException (409)
│   │   ├── UnprocessableEntityException (422)
│   │   └── TooManyRequestsException (429)
│   │
│   ├── ValidationException
│   │   └── 422 Unprocessable Entity
│   │
│   ├── AuthenticationException
│   │   └── 401 Unauthorized
│   │
│   ├── AuthorizationException
│   │   └── 403 Forbidden
│   │
│   └── DatabaseException
│       └── 500 Internal Server Error
│
└── Error (PHP)
    ├── TypeError
    ├── ValueError
    └── DivisionByZeroError
```

### Exception Mapping

```php
namespace CoreMusic\ErrorHandler;

class ExceptionMapper
{
    private array $mappings = [];

    public function register(string $exceptionClass, int $statusCode, string $errorCode): void
    {
        $this->mappings[$exceptionClass] = [
            'status' => $statusCode,
            'code' => $errorCode,
        ];
    }

    public function map(\Throwable $e): array
    {
        foreach ($this->mappings as $class => $mapping) {
            if ($e instanceof $class) {
                return [
                    'status' => $mapping['status'],
                    'code' => $mapping['code'],
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'status' => 500,
            'code' => 'INTERNAL_SERVER_ERROR',
            'message' => 'Beklenmeyen bir hata oluştu.',
        ];
    }
}

// Varsayılan mapping'ler
$mapper = new ExceptionMapper();
$mapper->register(BadRequestException::class, 400, 'BAD_REQUEST');
$mapper->register(UnauthorizedException::class, 401, 'UNAUTHORIZED');
$mapper->register(ForbiddenException::class, 403, 'FORBIDDEN');
$mapper->register(NotFoundException::class, 404, 'NOT_FOUND');
$mapper->register(MethodNotAllowedException::class, 405, 'METHOD_NOT_ALLOWED');
$mapper->register(ConflictException::class, 409, 'CONFLICT');
$mapper->register(ValidationException::class, 422, 'VALIDATION_ERROR');
$mapper->register(TooManyRequestsException::class, 429, 'RATE_LIMIT_EXCEEDED');
```

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private ExceptionMapper $mapper;
    private LoggerInterface $logger;
    private bool $debug;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->handleError($e, $request);
        }
    }

    private function handleError(
        \Throwable $e,
        ServerRequestInterface $request
    ): ResponseInterface {
        // Hatayı logla
        $this->logger->error('Unhandled exception', [
            'exception' => $e,
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
            'ip' => $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown',
            'trace_id' => $request->getHeaderLine('X-Request-ID'),
        ]);

        // Exception'ı HTTP yanıtına dönüştür
        $mapped = $this->mapper->map($e);

        $response = [
            'error' => $mapped['code'],
            'message' => $mapped['message'],
            'status' => $mapped['status'],
        ];

        // Debug modunda ek bilgi ekle
        if ($this->debug) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
            ];
        }

        // Request ID ekle
        $requestId = $request->getHeaderLine('X-Request-ID');
        if (!empty($requestId)) {
            $response['request_id'] = $requestId;
        }

        $psr7Response = new Response($mapped['status']);
        $psr7Response->getBody()->write(json_encode($response, JSON_PRETTY_PRINT));

        return $psr7Response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Request-ID', $requestId);
    }
}
```

### Hata Yanıt Formatları

```json
// 400 Bad Request
{
    "error": "BAD_REQUEST",
    "message": "Geçersiz istek formatı.",
    "status": 400,
    "request_id": "req_abc123"
}

// 401 Unauthorized
{
    "error": "UNAUTHORIZED",
    "message": "Kimlik doğrulama gerekli.",
    "status": 401,
    "request_id": "req_abc123"
}

// 404 Not Found
{
    "error": "NOT_FOUND",
    "message": "Kaynak bulunamadı: /api/v1/tracks/999",
    "status": 404,
    "request_id": "req_abc123"
}

// 500 Internal Server Error (Debug modu)
{
    "error": "INTERNAL_SERVER_ERROR",
    "message": "Beklenmeyen bir hata oluştu.",
    "status": 500,
    "request_id": "req_abc123",
    "debug": {
        "exception": "PDOException",
        "file": "/app/src/Database.php",
        "line": 42,
        "trace": ["#0 ..."]
    }
}
```

### Custom Error Pages

```php
// 404 için özel sayfa
if ($mapped['status'] === 404) {
    $response['message'] = sprintf(
        'Kaynak bulunamadı: %s %s',
        $request->getMethod(),
        $request->getUri()->getPath()
    );
}

// 429 için retry bilgisi
if ($mapped['status'] === 429) {
    $retryAfter = $e->getRetryAfter() ?? 60;
    $psr7Response = $psr7Response
        ->withHeader('Retry-After', (string) $retryAfter);
    $response['retry_after'] = $retryAfter;
}
```

## Kod / Konfigürasyon

### Konfigürasyon

```php
// config/error-handler.php
return [
    'debug' => env('APP_DEBUG', false),
    'log_level' => 'error',
    'exclude_exceptions' => [
        HttpException::class, // Zaten HTTP durum koduna sahip
    ],
    'error_responses' => [
        404 => [
            'message' => 'Kaynak bulunamadı.',
            'suggestion' => 'URL adresini kontrol edin.',
        ],
        500 => [
            'message' => 'Sunucu hatası oluştu.',
            'suggestion' => 'Lütfen daha sonra tekrar deneyin.',
        ],
    ],
];
```

### Error Handler Factory

```php
namespace CoreMusic\ErrorHandler;

class ErrorHandlerFactory
{
    public static function create(
        ContainerInterface $container
    ): ErrorHandlerMiddleware {
        $mapper = new ExceptionMapper();
        $logger = $container->get(LoggerInterface::class);
        $config = $container->get('config')['error_handler'] ?? [];

        return new ErrorHandlerMiddleware(
            $mapper,
            $logger,
            $config['debug'] ?? false
        );
    }
}
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| PSR-3 Logger | `psr/log` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Temel exception handling | Planlandı |
| Faz 2 | Exception mapper kaydı | Planlandı |
| Faz 3 | Debug modu desteği | Planlandı |
| Faz 4 | Structured logging | Planlandı |
| Faz 5 | Custom error responses | Planlandı |
