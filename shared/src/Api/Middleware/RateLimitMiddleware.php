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

use CoreMusic\Security\CacheRateLimiter;
use CoreMusic\Api\ApiResponse;

/**
 * Rate Limit Middleware using CacheRateLimiter.
 */
final class RateLimitMiddleware
{
    private const DEFAULT_MAX_REQUESTS = 60;
    private const DEFAULT_WINDOW_SECONDS = 60;

    public function __construct(
        private readonly CacheRateLimiter $rateLimiter,
        private int $maxRequests = self::DEFAULT_MAX_REQUESTS,
        private int $windowSeconds = self::DEFAULT_WINDOW_SECONDS
    ) {}

    /**
     * Process rate limiting for API requests.
     */
    public function __invoke(array $request, callable $next): array
    {
        $key = $this->getRateLimitKey();
        
        // Check if rate limited
        if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {
            header('Retry-After: ' . $this->windowSeconds);
            return ApiResponse::error(
                'RATE_LIMIT_EXCEEDED',
                'Too many requests',
                429
            );
        }
        
        // Increment rate limit counter
        $this->rateLimiter->increment($key, $this->windowSeconds);
        
        return $next($request);
    }

    /**
     * Get the rate limit key based on IP or API key.
     */
    private function getRateLimitKey(): string
    {
        // Use API key if available, otherwise use IP
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        if ($apiKey !== null) {
            return 'api:' . hash('sha256', $apiKey);
        }
        
        // Use IP address
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return 'ip:' . $ip;
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
