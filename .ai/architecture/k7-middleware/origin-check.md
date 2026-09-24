---
title: "Origin Check Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# Origin Check Middleware

## Genel Bakış

Origin Check Middleware, gelen HTTP isteklerinin kaynağını doğrular. Cross-origin isteklerde `Origin`, `Referer` ve `Host` başlıklarını kontrol ederek yetkisiz erişim girişimlerini engeller. CORS policy'sinden önce çalışarak saldırgan istekleripipeline'a sokmaz.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
├── [2] Security Headers
├── [3] Rate Limit
├── [4] CORS
│
▼
[5] Origin Check Middleware  ← Burada filtreleme
│
├── [6] Session
├── [7] CSRF
├── [8] Request Validation
├── [9] Logging
├── [10] Error Handler
│
▼
Handler (K6)
```

Origin Check, CORS middleware'inden sonra çalışır. CORS ister kapıda olsun ister olmasın, Origin Check ikinci savunma hattıdır. <!-- ⚠️ VERIFICATION REQUIRED: "门前osa Horse" bozuk metindi, bağlama göre yazıldı (2026-09-24) --> CORS olmayan isteklerde bile doğrulama yapar.

## Teknik Detaylar

### Doğrulama Yöntemleri

Origin Check middleware'i üç aşamalı doğrulama yapar:

**1. Host Header Doğrulaması**
Gelen isteğin `Host` başlığı izin verilen domain listesinde olmalıdır:

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class OriginCheckMiddleware
{
    private array $allowedHosts;
    private string $strictMode;

    public function __construct(array $config)
    {
        $this->allowedHosts = $config['allowed_hosts'] ?? [];
        $this->strictMode = $config['strict_mode'] ?? 'strict';
    }

    private function validateHost(ServerRequestInterface $request): bool
    {
        $host = $request->getUri()->getHost();
        return in_array($host, $this->allowedHosts, true);
    }
}
```

**2. Origin Header Doğrulaması**
POST, PUT, DELETE gibi modification isteklerinde `Origin` başlığı zorunludur:

```php
private function validateOrigin(ServerRequestInterface $request): bool
{
    $method = $request->getMethod();

    // GET ve HEAD istekleri için origin zorunlu değil
    if (in_array($method, ['GET', 'HEAD'], true)) {
        return true;
    }

    $origin = $request->getHeaderLine('Origin');

    if (empty($origin)) {
        return $this->strictMode === 'strict' ? false : true;
    }

    $originHost = parse_url($origin, PHP_URL_HOST);
    return in_array($originHost, $this->allowedHosts, true);
}
```

**3. Referer Header Doğrulaması**
Origin başlığı yoksa Referer ile kontrol yapılır:

```php
private function validateReferer(ServerRequestInterface $request): bool
{
    $referer = $request->getHeaderLine('Referer');

    if (empty($referer)) {
        return true; // Referer opsiyonel
    }

    $refererHost = parse_url($referer, PHP_URL_HOST);
    return in_array($refererHost, $this->allowedHosts, true);
}
```

### Doğrulama Sırası

```
İstek Gelir
│
├── Host başlığı kontrol → Geçersiz → 400 Bad Request
├── Origin başlığı kontrol → Geçersiz → 403 Forbidden
├── Referer başlığı kontrol → Geçersiz → 403 Forbidden (opsiyonel)
│
▼
Tüm kontroller geçildi → Bir sonraki middleware'e devam
```

### Hata Yanıtları

```json
// Geçersiz Host
{
    "error": "INVALID_HOST",
    "message": "İzin verilmeyen host: evil.example.com",
    "status": 400
}

// Geçersiz Origin
{
    "error": "INVALID_ORIGIN",
    "message": "İzin verilmeyen origin",
    "status": 403
}
```

### Whitelist ve Blacklist Desteği

```php
$config = [
    'allowed_hosts' => [
        'app.coremusic.io',
        'api.coremusic.io',
        '*.coremusic.io',      // wildcard destek
    ],
    'blocked_hosts' => [
        'malicious.example.com',
    ],
    'blocked_origins' => [
        'http://localhost',    // production'da localhost engelle
    ],
    'strict_mode' => 'strict', // strict veya loose
];
```

## Kod / Konfigürasyon

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class OriginCheckMiddleware implements MiddlewareInterface
{
    private OriginValidator $validator;
    private ResponseFactoryInterface $responseFactory;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Host kontrolü
        if (!$this->validator->validateHost($request)) {
            return $this->createErrorResponse(
                $request,
                'INVALID_HOST',
                400
            );
        }

        // Origin kontrolü
        if (!$this->validator->validateOrigin($request)) {
            return $this->createErrorResponse(
                $request,
                'INVALID_ORIGIN',
                403
            );
        }

        // Referer kontrolü (opsiyonel)
        if (!$this->validator->validateReferer($request)) {
            return $this->createErrorResponse(
                $request,
                'INVALID_REFERER',
                403
            );
        }

        return $handler->handle($request);
    }

    private function createErrorResponse(
        ServerRequestInterface $request,
        string $code,
        int $status
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse($status);
        $body = json_encode([
            'error' => $code,
            'message' => "Origin doğrulama başarısız: {$code}",
            'status' => $status,
        ]);

        $response->getBody()->write($body);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
```

### Test Konfigürasyonu

```php
// tests/Middleware/OriginCheckMiddlewareTest.php

public function test_invalid_host_returns_400(): void
{
    $middleware = new OriginCheckMiddleware([
        'allowed_hosts' => ['app.coremusic.io'],
    ]);

    $request = ServerRequestFactory::fromGlobals();
    $request = $request->withUri(
        $request->getUri()->withHost('evil.example.com')
    );

    $response = $middleware->process($request, $this->handler);

    $this->assertEquals(400, $response->getStatusCode());
}

public function test_valid_origin_passes_through(): void
{
    $middleware = new OriginCheckMiddleware([
        'allowed_hosts' => ['app.coremusic.io'],
    ]);

    $request = ServerRequestFactory::fromGlobals();
    $request = $request->withHeader('Origin', 'https://app.coremusic.io');

    $response = $middleware->process($request, $this->handler);

    $this->assertEquals(200, $response->getStatusCode());
}
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| PSR-17 Factory | `psr/http-factory` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Host header doğrulama | Planlandı |
| Faz 2 | Origin ve Referer kontrolü | Planlandı |
| Faz 3 | Wildcard ve pattern desteği | Planlandı |
| Faz 4 | Blacklist entegrasyonu | Planlandı |
| Faz 5 | Test kapsamı ve edge case'ler | Planlandı |
