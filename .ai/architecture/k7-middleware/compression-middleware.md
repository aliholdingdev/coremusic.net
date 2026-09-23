---
title: "Compression Middleware"
layer: K7
category: "Middleware"
date: "2026-09-20"
version: "1.0.0"
status: "draft"
---

# Compression Middleware

## Genel Bakış

Compression Middleware, HTTP yanıtlarını gzip veya brotli ile sıkıştırarak bant genişliği kullanımını optimize eder. İstemci tarafından desteklenen sıkıştırma algoritmasını otomatik olarak seçer (content negotiation). Büyük yanıtlarda önemli performans kazançları sağlar.

## Pipeline Pozisyonu

```
HTTP İsteği
│
▼
[1] Compression Middleware  ← İlk middleware, yanıtı son olarak sıkıştırır
│
├── [2] Security Headers
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
Handler
│
▲
Yanıt dönerken → Compression middleware sıkıştırma yapar
│
▼
Sıkıştırılmış Yanıt
```

Compression middleware pipeline'ın başında çalışır. Nedeni: Yanıt handler'dan döndükten sonra sıkıştırma yapılmalıdır. Bu nedenle middleware pipeline'ın "son" sırasında çalışır (response phase).

## Teknik Detaylar

### Sıkıştırma Algoritmaları

```
Algoritma Karşılaştırması:
├── gzip (RFC 1952)
│   ├── Hız: Hızlı (düşük CPU)
│   ├── Sıkıştırma: İyi (%60-80)
│   ├── Tarayıcı desteği: %100
│   └── Önerilen: Genel kullanım
│
├── br (Brotli - RFC 7932)
│   ├── Hız: Yavaş (yüksek CPU)
│   ├── Sıkıştırma: Çok iyi (%70-90)
│   ├── Tarayıcı desteği: %95+
│   └── Önerilen: Static assets
│
└── deflate (RFC 1951)
    ├── Hız: Çok hızlı
    ├── Sıkıştırma: Orta (%50-70)
    ├── Tarayıcı desteği: %100
    └── Önerilen: Legacy destek
```

### Content Negotiation

```php
namespace CoreMusic\Middleware;

class CompressionNegotiator
{
    private array $supportedAlgorithms = ['br', 'gzip', 'deflate'];

    public function negotiate(ServerRequestInterface $request): ?string
    {
        $acceptEncoding = $request->getHeaderLine('Accept-Encoding');

        if (empty($acceptEncoding)) {
            return null;
        }

        // İstemcinin desteklediği algoritmaları parse et
        $clientAlgorithms = $this->parseAcceptEncoding($acceptEncoding);

        // Öncelik sırasına göre en iyi algoritmayı seç
        foreach ($this->supportedAlgorithms as $algorithm) {
            if (in_array($algorithm, $clientAlgorithms, true)) {
                return $algorithm;
            }
        }

        return null;
    }

    private function parseAcceptEncoding(string $header): array
    {
        $algorithms = [];
        $parts = explode(',', $header);

        foreach ($parts as $part) {
            $part = trim($part);
            $parts = explode(';', $part);
            $algorithms[] = trim($parts[0]);
        }

        return $algorithms;
    }
}
```

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class CompressionMiddleware implements MiddlewareInterface
{
    private CompressionNegotiator $negotiator;
    private int $minLength;
    private array $contentTypes;

    public function __construct(array $config = [])
    {
        $this->negotiator = new CompressionNegotiator();
        $this->minLength = $config['min_length'] ?? 1024;
        $this->contentTypes = $config['content_types'] ?? [
            'text/html',
            'text/css',
            'text/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'text/plain',
        ];
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        // Sıkıştırma uygunluğunu kontrol et
        if (!$this->shouldCompress($request, $response)) {
            return $response;
        }

        // İstemcinin tercih ettiği algoritmayı seç
        $algorithm = $this->negotiator->negotiate($request);

        if ($algorithm === null) {
            return $response;
        }

        // Yanıtı sıkıştır
        return $this->compress($response, $algorithm);
    }

    private function shouldCompress(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): bool {
        // Zaten sıkıştırılmış mı?
        if ($response->hasHeader('Content-Encoding')) {
            return false;
        }

        // Content-Type uygun mu?
        $contentType = $response->getHeaderLine('Content-Type');
        if (!$this->isCompressibleContentType($contentType)) {
            return false;
        }

        // Minimum boyut kontrolü
        $bodySize = $response->getBody()->getSize();
        if ($bodySize < $this->minLength) {
            return false;
        }

        // Range request kontrolü
        if ($request->hasHeader('Range')) {
            return false;
        }

        return true;
    }

    private function isCompressibleContentType(string $contentType): bool
    {
        foreach ($this->contentTypes as $type) {
            if (strpos($contentType, $type) !== false) {
                return true;
            }
        }
        return false;
    }

    private function compress(
        ResponseInterface $response,
        string $algorithm
    ): ResponseInterface {
        $body = (string) $response->getBody();
        $originalSize = strlen($body);

        $compressed = match ($algorithm) {
            'gzip' => gzencode($body, 6),
            'br' => brotli_compress($body, 6, BROTLI_TEXT),
            'deflate' => deflate_encode($body, 6),
            default => $body,
        };

        $compressedSize = strlen($compressed);

        // Sıkıştırma faydalı mı?
        if ($compressedSize >= $originalSize) {
            return $response;
        }

        $ratio = round((1 - $compressedSize / $originalSize) * 100, 2);

        return $response
            ->withBody(new \GuzzleHttp\Psr7\Stream(fopen('php://temp', 'r+')))
            ->withHeader('Content-Encoding', $algorithm)
            ->withHeader('Content-Length', (string) $compressedSize)
            ->withHeader('X-Compression-Ratio', "{$ratio}%")
            ->getBody()->write($compressed);
    }
}
```

### Sıkıştırma Metrikleri

```json
{
    "compression": {
        "algorithm": "gzip",
        "original_size": 10240,
        "compressed_size": 3072,
        "ratio": "70.31%",
        "saved_bytes": 7168
    }
}
```

### Brotli Desteği

```php
// PHP 8.1+ brotli desteği
if (function_exists('brotli_compress')) {
    $compressed = brotli_compress(
        $body,
        BROTLI_QUALITY_DEFAULT, // 6
        BROTLI_TEXT
    );
} else {
    // Fallback: gzip
    $compressed = gzencode($body, 6);
}
```

## Kod / Konfigürasyon

### Konfigürasyon

```php
// config/compression.php
return [
    'algorithms' => ['br', 'gzip', 'deflate'],
    'min_length' => 1024,
    'content_types' => [
        'text/html',
        'text/css',
        'text/javascript',
        'application/javascript',
        'application/json',
        'application/xml',
        'text/xml',
        'text/plain',
        'image/svg+xml',
    ],
    'excluded_paths' => [
        '/api/v1/audio/stream',  // Zaten sıkıştırılmış
        '/api/v1/health',
    ],
    'gzip_level' => 6,
    'brotli_level' => 6,
];
```

### Static Assets için Önbellek

```php
// Static dosyalar için uzun süreli cache
class StaticCompressionMiddleware extends CompressionMiddleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = parent::process($request, $handler);

        // Static dosyalara cache header'ı ekle
        if ($this->isStaticAsset($request)) {
            $response = $response
                ->withHeader('Cache-Control', 'public, max-age=31536000, immutable')
                ->withHeader('Vary', 'Accept-Encoding');
        }

        return $response;
    }

    private function isStaticAsset(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();
        return preg_match('/\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico)$/', $path);
    }
}
```

### Entegrasyon Örneği

```php
// Pipeline'da kullanımı
$pipeline = new Pipeline();

$pipeline
    ->pipe(new CompressionMiddleware([
        'algorithms' => ['br', 'gzip'],
        'min_length' => 512,
    ]))
    ->pipe(new SecurityHeadersMiddleware())
    // ... diğer middleware'ler
    ->pipe(new ErrorHandlerMiddleware());
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| ext-zlib | PHP built-in | gzip için |
| ext-brotli | PHP extension | brotli için (opsiyonel) |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | gzip sıkıştırma desteği | Planlandı |
| Faz 2 | Brotli sıkıştırma desteği | Planlandı |
| Faz 3 | Content negotiation | Planlandı |
| Faz 4 | Static assets optimizasyonu | Planlandı |
| Faz 5 | Compression ratio monitoring | Planlandı |
