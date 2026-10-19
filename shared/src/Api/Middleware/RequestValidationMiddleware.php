<?php
declare(strict_types=1);

/**
 * Request Validation Middleware for API requests.
 *
 * @file RequestValidationMiddleware.php
 * @version 1.1.0
 * @see ADR-084-api-gateway-architecture
 * @see ADR-020-api-public-security (§2.2 istek doğrulama → 422 + alan hataları)
 */

namespace CoreMusic\Api\Middleware;

use CoreMusic\Api\ApiResponse;

/**
 * Route'a bağlı istek doğrulaması.
 *
 * Faz 2'deki riskler kapatıldı:
 *  1. Route verisi pipeline'da olmadığı için middleware fiilen atıldı →
 *     Gateway artık `$request['_route']` aktarıyor.
 *  2. Latent TypeError: return type'ı var olmayan bir sınıfı gösteriyordu →
 *     class-typed return kalmadı; kural motoru `CoreMusic\Api\Middleware\Validator`
 *     (bağımlılıksız). vendor/Respect kaybı olsa bile fatal mümkün değil.
 *
 * Ayrıştırılan gövde doğrulanır (pipeline request'i değil):
 *   - Content-Type yok / JSON değil → 415
 *   - JSON bozuksa                 → 400
 *   - Kural ihlali                  → 422 + {field: message}
 */
final class RequestValidationMiddleware
{
    private const BODY_METHODS = ['POST', 'PUT', 'PATCH'];

    private readonly Validator $validator;

    public function __construct(?Validator $validator = null)
    {
        $this->validator = $validator ?? new Validator();
    }

    /**
     * Process request validation.
     */
    public function __invoke(array $request, callable $next): array
    {
        $route = $request['_route'] ?? null;
        $rules = is_array($route) ? ($route['validation'] ?? null) : null;

        // Route kural içermiyorsan hiçbir girdi kısıtı uygulanmaz
        // (Faz 1b AuthController validation sözleşmelerini buraya bağlar).
        if (!is_array($rules) || $rules === []) {
            return $next($request);
        }

        $method  = strtoupper((string) ($request['method'] ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET')));
        $isBody  = in_array($method, self::BODY_METHODS, true);

        if ($isBody) {
            $contentType = $this->contentType($request);

            if ($contentType === null || !$this->isAcceptedContentType($contentType)) {
                return ApiResponse::error(
                    'UNSUPPORTED_MEDIA_TYPE',
                    'Content-Type must be application/json',
                    415,
                    ['content_type' => $contentType ?? '']
                );
            }

            $decoded = $this->decodeBody($request, $contentType);
            if ($decoded['error'] !== null) {
                return ApiResponse::error('INVALID_JSON', 'Request body is not valid JSON', 400, [
                    'reason' => $decoded['error'],
                ]);
            }

            $data = $decoded['data'];
        } else {
            $data = $request['query'] ?? $_GET;
            if (!is_array($data)) {
                $data = [];
            }
        }

        $errors = $this->validator->validate($data, $rules);
        if ($errors !== []) {
            // Faz 3a UI sözleşmesi: 422 GÖVDESİNDE error.fields zorunlu.
            // error.details (Faz 2 sözleşmesi) korunur — iki anahtar da aynı
            // {alan: mesaj} haritasını taşır.
            $response = ApiResponse::error('VALIDATION_ERROR', 'Validation failed', 422, $errors);
            $response['error']['fields'] = $errors;

            return $response;
        }

        return $next($request);
    }

    /**
     * Content-Type başlığı (pipeline `server` dizilimi → superglobal fallback).
     */
    private function contentType(array $request): ?string
    {
        $server = is_array($request['server'] ?? null) ? $request['server'] : $_SERVER;

        $value = $server['CONTENT_TYPE'] ?? $server['HTTP_CONTENT_TYPE'] ?? null;
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return null;
    }

    private function isAcceptedContentType(string $contentType): bool
    {
        $lower = strtolower($contentType);

        return str_contains($lower, 'application/json')
            || str_contains($lower, 'application/problem+json')
            || str_contains($lower, 'application/x-www-form-urlencoded');
    }

    /**
     * Gövdeyi çöz.
     *
     * `server['CONTENT_TYPE']` yoksa `$_SERVER['CONTENT_TYPE']` okunur —
     * yanlış content-type testi `text/plain` göndererek 415 alır.
     *
     * @return array{data: array<string, mixed>, error: string|null}
     */
    private function decodeBody(array $request, string $contentType): array
    {
        // Test / önceden çözülmüş gövde: `$request['body']` varsa onu kullan.
        if (array_key_exists('body', $request)) {
            $body = $request['body'];

            if (is_array($body)) {
                return ['data' => $body, 'error' => null];
            }

            if (is_string($body)) {
                return $this->decodeJson($body);
            }

            return ['data' => [], 'error' => 'unsupported_body_type'];
        }

        if (str_contains(strtolower($contentType), 'application/x-www-form-urlencoded')) {
            return ['data' => $_POST, 'error' => null];
        }

        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return ['data' => [], 'error' => null];
        }

        return $this->decodeJson($raw);
    }

    /**
     * @return array{data: array<string, mixed>, error: string|null}
     */
    private function decodeJson(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            return ['data' => [], 'error' => json_last_error_msg()];
        }

        return ['data' => $decoded, 'error' => null];
    }
}
