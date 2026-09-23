---
title: "Security Headers Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# Security Headers Middleware

## Genel Bakış

Security Headers Middleware, tüm HTTP yanıtlarına güvenlik başlıkları ekleyerek web uygulamasını yaygın saldırılara karşı korur. HSTS, CSP, X-Frame-Options gibi başlıkları merkezi bir noktadan yöneterek uygulama genelinde tutarlı güvenlik politikası sağlar.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
│
▼
[2] Security Headers Middleware  ← Güvenlik başlıkları eklenir
│
├── [3] Rate Limit
├── [4] CORS
├── [5] Origin Check
├── [6] Session
├── [7] CSRF
├── [8] Request Validation
├── [9] Logging
├── [10] Error Handler
│
▼
Handler → Yanıt → Başlıklar eklenmiş olarak döner
```

Güvenlik başlıkları response dönerken eklenir. Compression middleware'i zaten çalışmışsa, security headers yanıtın son haliyle birleşir.

## Teknik Detaylar

### Eklenecek Güvenlik Başlıkları

```php
namespace CoreMusic\Middleware;

class SecurityHeadersConfig
{
    public static function getDefault(): array
    {
        return [
            // HTTPS zorlaması (1 yıl)
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',

            // MIME type sniffing engelleme
            'X-Content-Type-Options' => 'nosniff',

            // Clickjacking koruması
            'X-Frame-Options' => 'DENY',

            // XSS koruması (artık önerilmiyor ama legacy destek)
            'X-XSS-Protection' => '0',

            // Referrer politikası
            'Referrer-Policy' => 'strict-origin-when-cross-origin',

            // Permissions policy
            'Permissions-Policy' => 'camera=(), microphone=(self), geolocation=()',

            // Content Security Policy
            'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self'; media-src 'self'; object-src 'none'; frame-ancestors 'none';",

            // Cross-Origin politikaları
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];
    }
}
```

### CSP Politika Detayları

```
Content-Security-Policy:
    default-src 'self';
    script-src 'self' 'nonce-{random}';
    style-src 'self' 'unsafe-inline';
    img-src 'self' data: https://cdn.coremusic.io;
    font-src 'self' https://fonts.gstatic.com;
    connect-src 'self' https://api.coremusic.io wss://ws.coremusic.io;
    media-src 'self' blob:;
    object-src 'none';
    frame-ancestors 'none';
    base-uri 'self';
    form-action 'self';
    upgrade-insecure-requests;
```

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class SecurityHeadersMiddleware implements MiddlewareInterface
{
    private array $headers;
    private bool $isProduction;

    public function __construct(array $config = [])
    {
        $this->headers = $config['headers'] ?? SecurityHeadersConfig::getDefault();
        $this->isProduction = $config['production'] ?? false;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        foreach ($this->headers as $name => $value) {
            // Production'da HSTS ekle
            if ($name === 'Strict-Transport-Security' && !$this->isProduction) {
                continue;
            }

            // X-XSS-Protection modern tarayıcılarda kaldırıldı
            if ($name === 'X-XSS-Protection' && $this->isModernBrowser($request)) {
                continue;
            }

            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    private function isModernBrowser(ServerRequestInterface $request): bool
    {
        $userAgent = $request->getHeaderLine('User-Agent');
        // Chrome 78+, Firefox 72+, Safari 13.1+, Edge 79+
        return preg_match('/(Chrome\/(7[89]|[89]\d)|Firefox\/(7[2-9]|[89]\d)|Safari\/(1[3-9]|[2-9]\d)|Edge\/(79|[89]\d))/', $userAgent);
    }
}
```

### Ortam Bazlı Konfigürasyon

```php
// Development
$devHeaders = new SecurityHeadersMiddleware([
    'production' => false,
    'headers' => [
        'Content-Security-Policy' => "default-src 'self' 'unsafe-inline' 'unsafe-eval'; connect-src *",
        'X-Frame-Options' => 'SAMEORIGIN',
    ],
]);

// Production
$prodHeaders = new SecurityHeadersMiddleware([
    'production' => true,
    'headers' => SecurityHeadersConfig::getDefault(),
]);
```

### CSP Raporlama

```php
// CSP violation raporlama endpoint'i
'Content-Security-Policy-Report-Only' => "default-src 'self'; report-uri /csp-report; report-to csp-endpoint;",

// Rapor formatı
'Report-To' => json_encode([
    'group' => 'csp-endpoint',
    'max_age' => 10886400,
    'endpoints' => [
        ['url' => 'https://api.coremusic.io/csp-report'],
    ],
]),
```

## Kod / Konfigürasyon

### Üretim Konfigürasyonu

```php
// config/security-headers.php
return [
    'production' => env('APP_ENV') === 'production',

    'headers' => [
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(), microphone=(self), geolocation=()',
        'Cross-Origin-Embedder-Policy' => 'require-corp',
        'Cross-Origin-Opener-Policy' => 'same-origin',
        'Cross-Origin-Resource-Policy' => 'same-origin',
    ],

    'csp' => [
        'report_only' => env('APP_ENV') !== 'production',
        'report_uri' => '/csp-report',
    ],
];
```

### CSP Report Handler

```php
class CspReportHandler
{
    private LoggerInterface $logger;

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $report = json_decode((string) $request->getBody(), true);

        $this->logger->warning('CSP Violation', [
            'document_uri' => $report['document-uri'] ?? '',
            'violation_directive' => $report['violated-directive'] ?? '',
            'blocked_uri' => $report['blocked-uri'] ?? '',
            'source_file' => $report['source-file'] ?? '',
            'line_number' => $report['line-number'] ?? 0,
        ]);

        return new Response(204);
    }
}
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| PSR-3 Logger | `psr/log` | CSP raporlama için |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Temel güvenlik başlıkları | Planlandı |
| Faz 2 | CSP politika oluşturma | Planlandı |
| Faz 3 | HSTS preload entegrasyonu | Planlandı |
| Faz 4 | CSP raporlama sistemi | Planlandı |
| Faz 5 | Ortam bazlı politika yönetimi | Planlandı |
