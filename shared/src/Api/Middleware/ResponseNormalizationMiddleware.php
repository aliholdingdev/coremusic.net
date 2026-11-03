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
        // X-Frame-Options: tek kaynak = IIS (api.coremusic.net/web.config customHeaders).
        // PHP yalnızca gateway yolunda basıyordu; /health ve 404'te yoktu → IIS tüm
        // API yanıtlarını kapsadığı için (ADR-020 tek-kaynak) PHP sürümü kaldırıldı.
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
        if ($this->matchesEtag($ifNoneMatch, $etag)) {
            http_response_code(304);

            $response['httpStatus'] = 304;
            $response['halt']       = true;
            $response['body']       = '';
        }

        return $response;
    }

    /**
     * RFC 9110 uyumlu ETag karşılaştırıcı (weak ETag ve liste desteği).
     */
    private function matchesEtag(string $ifNoneMatch, string $etag): bool
    {
        if (trim($ifNoneMatch) === '') {
            return false;
        }

        if (trim($ifNoneMatch) === '*') {
            return true;
        }

        $cleanEtag = trim($etag, " \"\t\n\r\0\x0B");
        $tags = explode(',', $ifNoneMatch);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (str_starts_with($tag, 'W/') || str_starts_with($tag, 'w/')) {
                $tag = substr($tag, 2);
            }
            if (trim($tag, " \"\t\n\r\0\x0B") === $cleanEtag) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cache-Control header'ları (ADR-007 §2.2c ve §2.2e uyumlu SWR ve ETag).
     */
    private function sendCacheControl(bool $cacheable, int $cacheTtl): void
    {
        if ($cacheable && $cacheTtl > 0) {
            header("Cache-Control: public, max-age={$cacheTtl}, s-maxage={$cacheTtl}, stale-while-revalidate={$cacheTtl}, stale-if-error=300");
        } else {
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
        }
    }
}
