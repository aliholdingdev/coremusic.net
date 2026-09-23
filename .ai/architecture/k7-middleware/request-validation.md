---
title: "Request Validation Middleware"
layer: K7
category: "Middleware"
date: "2026-09-20"
version: "1.0.0"
status: "draft"
---

# Request Validation Middleware

## Genel Bakış

Request Validation Middleware, gelen HTTP isteklerinin body, query parametreleri ve header'larını doğrular. JSON Schema ve custom validation rules kullanarak isteklerin uygulama kurallarına uygunluğunu kontrol eder. Geçersiz isteklerde 422 Unprocessable Entity yanıtı döndürür.

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
│
▼
[8] Request Validation Middleware  ← İstek doğrulama
│
├── [9] Logging
├── [10] Error Handler
│
▼
Handler
```

Request validation, CSRF korumasından sonra çalışır. Güvenlik kontrolleri tamamlandıktan sonra iş mantığına geçmeden önce istek formatını doğrular.

## Teknik Detaylar

### Doğrulama Türleri

```
İstek Doğrulama
│
├── [1] Content-Type Kontrolü
│   ├── application/json → JSON body parse
│   ├── multipart/form-data → File upload parse
│   └── application/x-www-form-urlencoded → Form data parse
│
├── [2] Body Validation
│   ├── Zorunlu alanlar (required)
│   ├── Veri tipleri (string, integer, boolean, array, object)
│   ├── Uzunluk sınırları (min, max)
│   ├── Pattern matching (regex)
│   └── Custom validators
│
├── [3] Query Parameter Validation
│   ├── Sayfalama (page, per_page)
│   ├── Sıralama (sort, order)
│   └── Filtreleme (filter)
│
├── [4] Header Validation
│   ├── Authorization başlığı
│   ├── Content-Type başlığı
│   └── Custom header'lar
│
▼
Doğrulama geçildi → Handler'a devam
```

### Schema Tanımlama

```php
namespace CoreMusic\Validation;

$audioTrackSchema = [
    'type' => 'object',
    'required' => ['title', 'artist', 'duration'],
    'properties' => [
        'title' => [
            'type' => 'string',
            'minLength' => 1,
            'maxLength' => 255,
        ],
        'artist' => [
            'type' => 'string',
            'minLength' => 1,
            'maxLength' => 255,
        ],
        'duration' => [
            'type' => 'integer',
            'minimum' => 1,
            'maximum' => 86400, // 24 saat
        ],
        'genre' => [
            'type' => 'string',
            'enum' => ['rock', 'pop', 'jazz', 'classical', 'electronic'],
        ],
        'tags' => [
            'type' => 'array',
            'items' => ['type' => 'string'],
            'maxItems' => 10,
        ],
        'metadata' => [
            'type' => 'object',
            'properties' => [
                'bitrate' => ['type' => 'integer', 'minimum' => 128],
                'sample_rate' => ['type' => 'integer', 'enum' => [44100, 48000, 96000]],
            ],
        ],
    ],
    'additionalProperties' => false,
];
```

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class RequestValidationMiddleware implements MiddlewareInterface
{
    private SchemaValidator $validator;
    private array $schemas;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $route = $request->getUri()->getPath();
        $method = $request->getMethod();

        // Bu rota için schema var mı?
        $schema = $this->getSchemaForRoute($method, $route);

        if ($schema === null) {
            return $handler->handle($request);
        }

        // İsteği doğrula
        $errors = $this->validateRequest($request, $schema);

        if (!empty($errors)) {
            return $this->createValidationErrorResponse($errors);
        }

        // Doğrulanmış verileri request'e ekle
        $request = $this->attachValidatedData($request);

        return $handler->handle($request);
    }

    private function validateRequest(
        ServerRequestInterface $request,
        array $schema
    ): array {
        $errors = [];

        // Body doğrulama
        if (isset($schema['body'])) {
            $body = $this->parseBody($request);
            $bodyErrors = $this->validator->validate($body, $schema['body']);
            $errors = array_merge($errors, $bodyErrors);
        }

        // Query doğrulama
        if (isset($schema['query'])) {
            $query = $request->getQueryParams();
            $queryErrors = $this->validator->validate($query, $schema['query']);
            $errors = array_merge($errors, $queryErrors);
        }

        // Header doğrulama
        if (isset($schema['headers'])) {
            $headerErrors = $this->validateHeaders($request, $schema['headers']);
            $errors = array_merge($errors, $headerErrors);
        }

        return $errors;
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strpos($contentType, 'application/json') !== false) {
            $body = json_decode((string) $request->getBody(), true);
            return is_array($body) ? $body : [];
        }

        if (strpos($contentType, 'multipart/form-data') !== false) {
            return $request->getUploadedFiles();
        }

        parse_str((string) $request->getBody(), $body);
        return $body;
    }

    private function createValidationErrorResponse(array $errors): ResponseInterface
    {
        $response = new Response(422);
        $response->getBody()->write(json_encode([
            'error' => 'VALIDATION_ERROR',
            'message' => 'İstek doğrulaması başarısız.',
            'errors' => $errors,
            'status' => 422,
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
```

### Hata Formatı

```json
{
    "error": "VALIDATION_ERROR",
    "message": "İstek doğrulaması başarısız.",
    "errors": [
        {
            "field": "title",
            "message": "Title alanı zorunludur.",
            "code": "REQUIRED"
        },
        {
            "field": "duration",
            "message": "Duration 1 ile 86400 arasında olmalıdır.",
            "code": "MIN_MAX",
            "params": {"min": 1, "max": 86400}
        },
        {
            "field": "genre",
            "message": "Geçersiz tür: hip-hop",
            "code": "ENUM",
            "params": {"allowed": ["rock", "pop", "jazz", "classical", "electronic"]}
        }
    ],
    "status": 422
}
```

### Custom Validator'lar

```php
namespace CoreMusic\Validation;

class CustomValidators
{
    public static function getRules(): array
    {
        return [
            'email' => fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'url' => fn($value) => filter_var($value, FILTER_VALIDATE_URL) !== false,
            'uuid' => fn($value) => preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value),
            'slug' => fn($value) => preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value),
            'audio_format' => fn($value) => in_array($value, ['mp3', 'wav', 'flac', 'aac', 'ogg']),
            'positive_integer' => fn($value) => is_int($value) && $value > 0,
        ];
    }
}
```

## Kod / Konfigürasyon

### Konfigürasyon

```php
// config/validation.php
return [
    'schemas' => [
        'POST /api/v1/tracks' => [
            'body' => __DIR__ . '/../schemas/track-create.json',
            'headers' => ['Authorization' => 'required'],
        ],
        'PUT /api/v1/tracks/{id}' => [
            'body' => __DIR__ . '/../schemas/track-update.json',
        ],
        'GET /api/v1/tracks' => [
            'query' => [
                'page' => ['type' => 'integer', 'min' => 1],
                'per_page' => ['type' => 'integer', 'min' => 1, 'max' => 100],
                'sort' => ['type' => 'string', 'enum' => ['title', 'created_at', 'duration']],
            ],
        ],
    ],
    'strip_unknown' => true,
    'coerce_types' => false,
];
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| JSON Schema | `justinrainbow/json-schema` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Temel body validation | Planlandı |
| Faz 2 | JSON Schema desteği | Planlandı |
| Faz 3 | Custom validator'lar | Planlandı |
| Faz 4 | File upload validation | Planlandı |
| Faz 5 | Query ve header validation | Planlandı |
