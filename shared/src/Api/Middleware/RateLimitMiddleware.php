<?php
declare(strict_types=1);

/**
 * Rate Limit Middleware for API requests.
 *
 * @file RateLimitMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

use CoreMusic\Contracts\Security\IRateLimiter;
use CoreMusic\Api\ApiResponse;

/**
 * Rate Limit Middleware using IRateLimiter (default: CacheRateLimiter).
 */
final class RateLimitMiddleware
{
    private const DEFAULT_MAX_REQUESTS = 60;
    private const DEFAULT_WINDOW_SECONDS = 60;

    public function __construct(
        private readonly IRateLimiter $rateLimiter,
        private int $maxRequests = self::DEFAULT_MAX_REQUESTS,
        private int $windowSeconds = self::DEFAULT_WINDOW_SECONDS
    ) {}

    /**
     * Process rate limiting for API requests.
     */
    public function __invoke(array $request, callable $next): array
    {
        // SEC-01: ip: bucket'ı header'dan bağımsız HER istekte taranır; header
        // mevcutsa ek olarak api: bucket'ı da eklenir. Böylece X-Api-Key header'ı
        // döndürülerek IP rate-limit'i (login brute-force) atlatılamaz.
        $keys = $this->getRateLimitKeys();

        // Check if rate limited — her anahtar taranır.
        foreach ($keys as $key) {
            if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {
                header('Retry-After: ' . $this->windowSeconds);
                return ApiResponse::error(
                    'RATE_LIMIT_EXCEEDED',
                    'Too many requests',
                    429
                );
            }
        }

        // Increment rate limit counter — tüm anahtarlar sayılır.
        foreach ($keys as $key) {
            $this->rateLimiter->increment($key, $this->windowSeconds);
        }

        return $next($request);
    }

    /**
     * Get the rate limit keys: always the IP bucket; additionally the API-key
     * bucket when the header is present. Both are scanned so a client cannot
     * bypass the IP counter by rotating the X-Api-Key header (SEC-01).
     *
     * @return string[]
     */
    private function getRateLimitKeys(): array
    {
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $keys = ['ip:' . $ip];

        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if ($apiKey !== null) {
            $keys[] = 'api:' . hash('sha256', $apiKey);
        }

        return $keys;
    }

    /**
     * Set custom rate limit parameters.
     */
    public function withLimits(int $maxRequests, int $windowSeconds): self
    {
        $clone = clone $this;
        $clone->maxRequests = $maxRequests;
        $clone->windowSeconds = $windowSeconds;
        return $clone;
    }
}
