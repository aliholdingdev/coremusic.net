<?php
declare(strict_types=1);

/**
 * Response Normalization Middleware for API responses.
 *
 * @file ResponseNormalizationMiddleware.php
 * @version 1.1.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

/**
 * Response Normalization Middleware for standardized responses.
 *
 * Faz 2 açık #5 kapatıldı:
 *   - `no-store` yanıtta artık ETag basılmaz (ETag yalnız `cacheable` route'da).
 *   - 304 yolu `exit` ile index.php'yi bypass etmiyor; `halt` + boş gövde
 *     döner, header kopyalama/index.php akışı korunur.
 */
final class ResponseNormalizationMiddleware
{
    /**
     * Process response normalization.
     */
    public function __invoke(array $request, callable $next): array
    {
        $response = $next($request);

        // Add standard headers
        $this->addStandardHeaders($response);

        $route     = $request['_route'] ?? null;
        $cacheable = is_array($route) && !empty($route['cacheable']);
        $cacheTtl  = is_array($route) ? (int) ($route['cacheTtl'] ?? 0) : 0;

        // Add Cache-Control headers
        $this->sendCacheControl($cacheable, $cacheTtl);

        // Add ETag only for cacheable responses (no-store + ETag çelişkisi yok).
        if ($cacheable && isset($response['data']) && !empty($response['data'])) {
            $response = $this->withETag($response);
        }

        return $response;
    }

    /**
     * Add standard response headers.
     */
    private function addStandardHeaders(array $response): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
    }

    /**
     * ETag üret; eşleşirse gövdesiz 304 döndür (exit YOK — pipeline/index.php
     * akışı korunur, Cors header'ları kaybolmaz).
     *
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    private function withETag(array $response): array
    {
        $etag = md5((string) json_encode($response));
        header('ETag: "' . $etag . '"');

        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        if ($ifNoneMatch === '"' . $etag . '"') {
            http_response_code(304);

            $response['httpStatus'] = 304;
            $response['halt']       = true;
            $response['body']       = '';
        }

        return $response;
    }

    /**
     * Cache-Control header'ları.
     */
    private function sendCacheControl(bool $cacheable, int $cacheTtl): void
    {
        if ($cacheable && $cacheTtl > 0) {
            header("Cache-Control: public, max-age={$cacheTtl}");
        } else {
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
        }
    }
}
