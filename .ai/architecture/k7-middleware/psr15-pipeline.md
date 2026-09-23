---
title: "PSR-15 Pipeline Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# PSR-15 HTTP Middleware Pipeline

## Genel Bakış

PSR-15 HTTP Middleware Pipeline, COREMUSIC web API'sinin istek işleme zincirini yönetir. PSR-15 (HTTP Server Request Handlers) standardına uygun olarak middleware'leri sıralı bir şekilde çalıştırır. Her middleware bir sonraki handler'a geçiş yapar veya doğrudan yanıt döndürür.

## Pipeline Pozisyonu

```
HTTP İsteği (PSR-7 ServerRequestInterface)
│
│  ┌──────────────────────────────────────┐
│  │         PSR-15 Pipeline              │
│  │                                      │
│  │  ┌─────┐  ┌─────┐  ┌─────┐         │
│  │  │MW-1 │→│MW-2 │→│MW-3 │→ ... → Handler
│  │  └─────┘  └─────┘  └─────┘         │
│  │      ↑         ↑         ↑           │
│  │  process()  process()  process()    │
│  └──────────────────────────────────────┘
│
▼
Handler (K6 Uygulama)
│
▲
│  ┌──────────────────────────────────────┐
│  │         Yanıt Döngüsü               │
│  │  ← Response (PSR-7 ResponseInterface)│
│  └──────────────────────────────────────┘
│
HTTP Yanıtı
```

## Teknik Detaylar

### PSR-15 Arayüzleri

```php
// Middleware Arayüzü
namespace Psr\Http\Server;

interface MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}

// Handler Arayüzü
interface RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
```

### Pipeline Implementasyonu

```php
namespace CoreMusic\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline implements RequestHandlerInterface
{
    private array $middlewares = [];
    private RequestHandlerInterface $handler;
    private int $index = 0;

    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function setHandler(RequestHandlerInterface $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $this->index = 0;
        return $this->execute($request);
    }

    private function execute(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->index >= count($this->middlewares)) {
            return $this->handler->handle($request);
        }

        $middleware = $this->middlewares[$this->index++];
        $pipeline = $this;

        return $middleware->process(
            $request,
            new class($pipeline) implements RequestHandlerInterface
            {
                private Pipeline $pipeline;

                public function __construct(Pipeline $pipeline)
                {
                    $this->pipeline = $pipeline;
                }

                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return $this->pipeline->execute($request);
                }
            }
        );
    }
}
```

### Pipeline Oluşturma

```php
$pipeline = new Pipeline();

// Middleware'leri sırayla ekle
$pipeline
    ->pipe(new CompressionMiddleware())
    ->pipe(new SecurityHeadersMiddleware())
    ->pipe(new RateLimitMiddleware($redis))
    ->pipe(new CorsMiddleware($corsPolicy))
    ->pipe(new OriginCheckMiddleware($allowedHosts))
    ->pipe(new SessionMiddleware($sessionConfig))
    ->pipe(new CsrfMiddleware($csrfTokenManager))
    ->pipe(new RequestValidationMiddleware($validator))
    ->pipe(new LoggingMiddleware($logger))
    ->pipe(new ErrorHandlerMiddleware($errorMapper));

// Handler'ı ayarla
$pipeline->setHandler(new AppController());

// İsteği işle
$response = $pipeline->process($request);
```

### Erken Durdurma (Short-Circuit)

```php
// Rate limit middleware - erken durdurma örneği
class RateLimitMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $result = $this->limiter->check($request);

        if ($result->isExceeded()) {
            // Handler'ı çalıştırmadan doğrudan yanıt dön
            return new Response(429, [
                'Retry-After' => $result->getRetryAfter(),
            ]);
        }

        // Devam et - handler'ı çalıştır
        return $handler->handle($request);
    }
}
```

### Pipeline Debugging

```php
class DebugPipeline extends Pipeline
{
    private LoggerInterface $logger;

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info('Pipeline started', [
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
        ]);

        $start = microtime(true);
        $response = parent::process($request);
        $duration = microtime(true) - $start;

        $this->logger->info('Pipeline completed', [
            'status' => $response->getStatusCode(),
            'duration_ms' => round($duration * 1000, 2),
        ]);

        return $response;
    }
}
```

### Hata Yönetimi Pipeline İçinde

```php
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (HttpException $e) {
            return $this->handleHttpException($e, $request);
        } catch (\Throwable $e) {
            return $this->handleUnexpectedError($e, $request);
        }
    }

    private function handleHttpException(
        HttpException $e,
        ServerRequestInterface $request
    ): ResponseInterface {
        return new Response($e->getStatusCode(), [
            'Content-Type' => 'application/json',
        ], json_encode([
            'error' => $e->getMessage(),
            'status' => $e->getStatusCode(),
        ]));
    }
}
```

## Kod / Konfigürasyon

### Pipeline Factory

```php
namespace CoreMusic\Pipeline;

class PipelineFactory
{
    private ContainerInterface $container;
    private array $config;

    public function __construct(
        ContainerInterface $container,
        array $config
    ) {
        $this->container = $container;
        $this->config = $config;
    }

    public function create(): Pipeline
    {
        $pipeline = new Pipeline();

        foreach ($this->config['order'] as $middlewareName) {
            $middleware = $this->container->get($middlewareName);
            $pipeline->pipe($middleware);
        }

        $handler = $this->container->get(
            $this->config['handler'] ?? 'app.handler'
        );

        $pipeline->setHandler($handler);

        return $pipeline;
    }
}
```

### Konfigürasyon

```php
// config/pipeline.php
return [
    'order' => [
        'middleware.compression',
        'middleware.security_headers',
        'middleware.rate_limit',
        'middleware.cors',
        'middleware.origin_check',
        'middleware.session',
        'middleware.csrf',
        'middleware.request_validation',
        'middleware.logging',
        'middleware.error_handler',
    ],
    'handler' => 'app.controller',
    'debug' => env('APP_DEBUG', false),
];
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| PSR-11 Container | `psr/container` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Temel Pipeline yapısı | Planlandı |
| Faz 2 | Middleware chaining | Planlandı |
| Faz 3 | Error handling integration | Planlandı |
| Faz 4 | Debug ve logging desteği | Planlandı |
| Faz 5 | Performance optimizasyonu | Planlandı |
