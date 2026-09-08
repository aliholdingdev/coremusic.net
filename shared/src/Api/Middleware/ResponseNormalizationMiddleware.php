<?php
declare(strict_types=1);

/**
 * Response Normalization Middleware for API responses.
 *
 * @file ResponseNormalizationMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

/**
 * Response Normalization Middleware for standardized responses.
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
        
        // Add ETag if response has data
        if (isset($response['data']) && !empty($response['data'])) {
            $this->addETag($response);
        }
        
        // Add Cache-Control headers
        $this->addCacheControl($request, $response);
        
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
     * Add ETag header for caching.
     */
    private function addETag(array $response): void
    {
        $etag = md5(json_encode($response));
        header('ETag: "' . $etag . '"');
        
        // Check if client has matching ETag
        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        if ($ifNoneMatch === '"' . $etag . '"') {
            http_response_code(304);
            exit;
        }
    }

    /**
     * Add Cache-Control headers based on route.
     */
    private function addCacheControl(array $request, array $response): void
    {
        $route = $request['_route'] ?? null;
        $cacheable = $route['cacheable'] ?? false;
        $cacheTtl = $route['cacheTtl'] ?? 0;
        
        if ($cacheable && $cacheTtl > 0) {
            header("Cache-Control: public, max-age={$cacheTtl}");
        } else {
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
        }
    }
}
