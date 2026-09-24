---
title: "K7 Middleware Katmanı - Genel Bakış"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# K7 Middleware Katmanı

## Genel Bakış

K7 Middleware katmanı, COREMUSIC web API'sinin HTTP isteklerini işleyen ara katmanları yönetir. Bu katman, PSR-15 standartlarına uygun HTTP middleware pipeline'ı kullanarak istek/yanıt döngüsünü denetler. Güvenlik, performans ve izlenebilirlik endişelerini merkezi bir noktada ele alır.

Middleware'ler, HTTP isteği (Request) ile uygulama mantığı (Handler) arasına yerleştirilir. Her middleware kendi sorumluluğunu yerine getirir, isteği değiştirir veya doğrudan yanıt döndürür.

## Pipeline Pozisyonu

Middleware zincirindeki yer ve iş akışı:

```
HTTP İsteği (Gelen)
│
├── [1] Compression Middleware      ← gzip/brotli sıkıştırma
├── [2] Security Headers Middleware ← HSTS, CSP, X-Frame-Options
├── [3] Rate Limit Middleware       ← throttle, 429 yanıtları
├── [4] CORS Middleware             ← cross-origin izinleri
├── [5] Origin Check Middleware     ← origin/referer doğrulama
├── [6] Session Middleware          ← oturum yönetimi
├── [7] CSRF Middleware             ← CSRF token doğrulama
├── [8] Request Validation Middleware ← body/query doğrulama
├── [9] Logging Middleware          ← correlation ID, erişim logları
├── [10] Error Handler Middleware   ← exception mapping
│
▼
K6 Uygulama (Handler) → K3 Ses Motoru → K1 Donanım
│
▲
HTTP Yanıtı (Giden)
```

## Teknik Detaylar

### PSR-15 Uyumluluğu

Tüm middleware'ler `Psr\Http\Server\MiddlewareInterface` arayüzünü uygular:

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
```

### Pipeline Oluşturma

```php
use CoreMusic\Pipeline\Pipeline;

$pipeline = new Pipeline();

$pipeline
    ->pipe(new CompressionMiddleware(['brotli', 'gzip']))
    ->pipe(new SecurityHeadersMiddleware())
    ->pipe(new RateLimitMiddleware($redis, 100, 60))
    ->pipe(new CorsMiddleware($corsPolicy))
    ->pipe(new OriginCheckMiddleware($allowedOrigins))
    ->pipe(new SessionMiddleware($sessionConfig))
    ->pipe(new CsrfMiddleware($csrfTokenManager))
    ->pipe(new RequestValidationMiddleware($validator))
    ->pipe(new LoggingMiddleware($logger))
    ->pipe(new ErrorHandlerMiddleware($errorMapper));

$response = $pipeline->process($request, $handler);
```

### Middleware Sıralama Mantığı

| Sıra | Middleware | Sorumluluk | Erken Durdurma |
|------|-----------|------------|----------------|
| 1 | Compression | Yanıt sıkıştırma | Hayır |
| 2 | Security Headers | Güvenlik başlıkları | Hayır |
| 3 | Rate Limit | İstek sınırlama | Evet (429) |
| 4 | CORS | Cross-origin izinleri | Evet (403) |
| 5 | Origin Check | Origin doğrulama | Evet (403) |
| 6 | Session | Oturum yükleme | Hayır |
| 7 | CSRF | CSRF koruması | Evet (403) |
| 8 | Request Validation | İstek doğrulama | Evet (422) |
| 9 | Logging | Loglama | Hayır |
| 10 | Error Handler | Hata yakalama | Evet |

### Performans Metrikleri

| Metrik | Hedef |
|--------|-------|
| Middleware pipeline overhead | < 0.5ms |
| Ortalama middleware processing time | < 0.05ms |
| Rate limit lookup latency | < 1ms |
| Session loading overhead | < 2ms |
| Compression CPU overhead | < 5% |

## Kod / Konfigürasyon

### Ana Middleware Registry

```php
namespace CoreMusic\Middleware;

class MiddlewareRegistry
{
    private array $middlewares = [];
    private array $order = [];

    public function register(string $name, MiddlewareInterface $middleware): void
    {
        $this->middlewares[$name] = $middleware;
    }

    public function getOrdered(): array
    {
        $ordered = [];
        foreach ($this->order as $name) {
            if (isset($this->middlewares[$name])) {
                $ordered[] = $this->middlewares[$name];
            }
        }
        return $ordered;
    }

    public function setOrder(array $order): void
    {
        $this->order = $order;
    }
}
```

### Konfigürasyon (config/middleware.php)

```php
return [
    'pipeline' => [
        'order' => [
            'compression',
            'security_headers',
            'rate_limit',
            'cors',
            'origin_check',
            'session',
            'csrf',
            'request_validation',
            'logging',
            'error_handler',
        ],
    ],
    'rate_limit' => [
        'max_requests' => 100,
        'window_seconds' => 60,
        'driver' => 'redis',
    ],
    'session' => [
        'driver' => 'cookie',
        'lifetime' => 7200,
        'secure' => true,
        'httponly' => true,
        'samesite' => 'lax',
    ],
    'compression' => [
        'algorithms' => ['brotli', 'gzip'],
        'min_length' => 1024,
    ],
    'cors' => [
        'allowed_origins' => ['https://app.coremusic.io'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-CSRF-Token'],
        'max_age' => 86400,
    ],
];
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-15 | `psr/http-server-middleware` | Evet |
| PSR-7 | `psr/http-message` | Evet |
| PSR-3 Logger | `psr/log` | Evet |
| Redis | `predis/predis` | Rate limit için |
| Squeeze | `matthiasmullie/minify` | Compression için |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | PSR-15 Pipeline temel yapısı | Planlandı |
| Faz 2 | Rate limit ve security headers | Planlandı |
| Faz 3 | Session ve CSRF koruması | Planlandı |
| Faz 4 | Request validation ve error handling | Planlandı |
| Faz 5 | Logging ve compression optimizasyonu | Planlandı |

**Genel Durum**: Planlama aşamasında — tüm middleware'ler Faz 1-5 içinde uygulanacaktır.
