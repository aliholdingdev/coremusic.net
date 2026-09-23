---
title: "CORS Policy Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# CORS Policy Middleware

## Genel Bakış

CORS (Cross-Origin Resource Sharing) Policy Middleware, farklı origin'lerden gelen tarayıcı isteklerini yönetir. Preflight isteklerini (OPTIONS) yanıtlayarak, izin verilen origin/method/header'ları belirleyerek ve credentials politikasını uygulayarak güvenli cross-origin iletişimi sağlar.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
├── [2] Security Headers
├── [3] Rate Limit
│
▼
[4] CORS Policy Middleware  ← Cross-origin politikası
│
├── [5] Origin Check
├── [6] Session
├── [7] CSRF
├── [8] Request Validation
├── [9] Logging
├── [10] Error Handler
│
▼
Handler
```

CORS middleware'i origin check'ten önce çalışır. Preflight (OPTIONS) isteklerini doğrudan yanıtlar, normal isteklere CORS başlıkları ekler.

## Teknik Detaylar

### CORS Akışı

```
Tarayıcı İsteği
│
├── Preflight (OPTIONS)?
│   ├── Evet → Allowed-Origin/Methods/Headers kontrol
│   │   ├── İzinli → 204 No Content + CORS başlıkları
│   │   └── İzinli değil → 403 Forbidden
│   └── Hayır → Normal istek akışı
│
├── Normal İstek
│   ├── Origin başlığı var mı?
│   │   ├── Evet → Origin doğrulama
│   │   │   ├── İzinli → Response'a CORS başlıkları ekle
│   │   │   └── İzinli değil → Başlık ekleme, Origin Check'e devam
│   │   └── Hayır → Same-origin istek, başlık ekleme
│
▼
Sonraki Middleware
```

### Politika Konfigürasyonu

```php
namespace CoreMusic\Middleware;

use CoreMusic\Cors\CorsPolicy;

$config = [
    'allowed_origins' => [
        'https://app.coremusic.io',
        'https://admin.coremusic.io',
        'http://localhost:3000',     // geliştirme ortamı
    ],
    'allowed_methods' => [
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS',
    ],
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-CSRF-Token',
        'X-Request-ID',
        'Accept',
        'Origin',
    ],
    'exposed_headers' => [
        'X-Total-Count',
        'X-Pagination-Page',
    ],
    'allow_credentials' => true,
    'max_age' => 86400,          // preflight cache süresi (saniye)
    'supports_credentials' => true,
];

$policy = new CorsPolicy($config);
```

### Preflight İşleme

```php
class CorsMiddleware implements MiddlewareInterface
{
    private CorsPolicy $policy;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Preflight isteği kontrolü
        if ($request->getMethod() === 'OPTIONS') {
            return $this->handlePreflight($request);
        }

        // Normal istek - CORS başlıkları ekle
        $response = $handler->handle($request);
        return $this->addCorsHeaders($request, $response);
    }

    private function handlePreflight(
        ServerRequestInterface $request
    ): ResponseInterface {
        $origin = $request->getHeaderLine('Origin');
        $method = $request->getHeaderLine('Access-Control-Request-Method');
        $headers = $request->getHeaderLine('Access-Control-Request-Headers');

        // Origin kontrolü
        if (!$this->policy->isOriginAllowed($origin)) {
            return $this->createResponse(403);
        }

        // Method kontrolü
        if (!$this->policy->isMethodAllowed($method)) {
            return $this->createResponse(403);
        }

        // Headers kontrolü
        if (!$this->policy->areHeadersAllowed($headers)) {
            return $this->createResponse(403);
        }

        // Preflight yanıtı oluştur
        $response = $this->createResponse(204);
        return $this->addPreflightHeaders($response, $origin);
    }

    private function addPreflightHeaders(
        ResponseInterface $response,
        string $origin
    ): ResponseInterface {
        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-Token')
            ->withHeader('Access-Control-Max-Age', (string) $this->policy->getMaxAge())
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }

    private function addCorsHeaders(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $origin = $request->getHeaderLine('Origin');

        if (empty($origin)) {
            return $response; // Same-origin istek
        }

        if (!$this->policy->isOriginAllowed($origin)) {
            return $response; // Origin izinli değil, başlık ekleme
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Expose-Headers', 'X-Total-Count, X-Pagination-Page');
    }
}
```

### Wildcard Origin Desteği

```php
// Koruma modunda wildcard kullanımı
$policy = new CorsPolicy([
    'allowed_origins' => ['https://*.coremusic.io'],
    'allow_credentials' => false, // credentials ile wildcard çalışmaz
    'max_age' => 3600,
]);

// Production modu
$policy = new CorsPolicy([
    'allowed_origins' => [
        'https://app.coremusic.io',
        'https://admin.coremusic.io',
    ],
    'allow_credentials' => true,
]);
```

### CORS Hata Yanıtları

```json
// Geçersiz Origin
{
    "error": "CORS_ORIGIN_NOT_ALLOWED",
    "message": "Origin https://evil.com izin verilmiyor",
    "status": 403
}

// Geçersiz Method
{
    "error": "CORS_METHOD_NOT_ALLOWED",
    "message": "Method PATCH izin verilmiyor",
    "status": 403
}
```

## Kod / Konfigürasyon

### CorsPolicy Sınıfı

```php
namespace CoreMusic\Cors;

class CorsPolicy
{
    private array $allowedOrigins;
    private array $allowedMethods;
    private array $allowedHeaders;
    private bool $allowCredentials;
    private int $maxAge;

    public function __construct(array $config)
    {
        $this->allowedOrigins = $config['allowed_origins'] ?? [];
        $this->allowedMethods = $config['allowed_methods'] ?? ['GET', 'POST'];
        $this->allowedHeaders = $config['allowed_headers'] ?? ['Content-Type'];
        $this->allowCredentials = $config['allow_credentials'] ?? false;
        $this->maxAge = $config['max_age'] ?? 86400;
    }

    public function isOriginAllowed(string $origin): bool
    {
        foreach ($this->allowedOrigins as $pattern) {
            if ($this->matchOrigin($pattern, $origin)) {
                return true;
            }
        }
        return false;
    }

    private function matchOrigin(string $pattern, string $origin): bool
    {
        $pattern = str_replace('\*', '.*', preg_quote($pattern, '/'));
        return (bool) preg_match("/^{$pattern}$/", $origin);
    }

    public function isMethodAllowed(string $method): bool
    {
        return in_array(strtoupper($method), $this->allowedMethods, true);
    }

    public function areHeadersAllowed(string $headers): bool
    {
        $requested = array_map('trim', explode(',', $headers));
        foreach ($requested as $header) {
            if (!in_array(strtolower($header), array_map('strtolower', $this->allowedHeaders), true)) {
                return false;
            }
        }
        return true;
    }

    public function getMaxAge(): int
    {
        return $this->maxAge;
    }
}
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Temel CORS policy ve preflight | Planlandı |
| Faz 2 | Wildcard ve pattern matching | Planlandı |
| Faz 3 | Credentials politikası | Planlandı |
| Faz 4 | Cache ve performans optimizasyonu | Planlandı |
| Faz 5 | Test ve edge case'ler | Planlandı |
