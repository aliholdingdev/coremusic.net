<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use PHPUnit\Framework\TestCase;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Middleware\RateLimiterMiddleware;
use CoreMusic\Security\CacheRateLimiter;

/**
 * AdminRateLimitTest — Admin paneli rate-limit doğrulama testi (ADR-013).
 *
 * PRD §7.2: login/alias rotalarında rate-limit canlı kanıtı (limit aşımında 429).
 */
final class AdminRateLimitTest extends TestCase
{
    private MemoryAdapter $cache;

    protected function setUp(): void
    {
        $this->cache = new MemoryAdapter();
    }

    public function testAdminLoginRoute_returns429WhenRateLimitExceeded(): void
    {
        // 5 req / 60s limit
        $middleware = new RateLimiterMiddleware(
            maxRequests: 5,
            windowSeconds: 60,
            rateLimiter: new CacheRateLimiter($this->cache),
        );

        $request = [
            'method'  => 'POST',
            'uri'     => '/admin-login',
            'headers' => [],
            'server'  => ['REMOTE_ADDR' => '10.0.0.50'],
            'body'    => [],
        ];

        $next = static fn (array $req): array => [
            'status'  => 200,
            'headers' => [],
            'body'    => 'ok',
        ];

        // 5 requests pass
        for ($i = 0; $i < 5; $i++) {
            $response = $middleware->handle($request, $next);
            self::assertSame(200, $response['status'], "İstek {$i} 200 dönmeli");
        }

        // 6th request is blocked with 429 Too Many Requests
        $response = $middleware->handle($request, $next);
        $status = $response['httpStatus'] ?? $response['status'] ?? null;
        self::assertSame(429, $status, '6. istek 429 Too Many Requests dönmeli');
        self::assertSame(['error' => 'rate_limit_exceeded'], $response['body']);
        self::assertArrayHasKey('Retry-After', $response['headers']);
    }

    public function testDifferentIpAddressesHaveIndependentRateLimits(): void
    {
        $middleware = new RateLimiterMiddleware(
            maxRequests: 2,
            windowSeconds: 60,
            rateLimiter: new CacheRateLimiter($this->cache),
        );

        $next = static fn (array $req): array => ['status' => 200, 'headers' => [], 'body' => 'ok'];

        $reqA = ['method' => 'POST', 'uri' => '/admin-login', 'headers' => [], 'server' => ['REMOTE_ADDR' => '10.0.0.1'], 'body' => []];
        $reqB = ['method' => 'POST', 'uri' => '/admin-login', 'headers' => [], 'server' => ['REMOTE_ADDR' => '10.0.0.2'], 'body' => []];

        // IP A exhausts quota
        self::assertSame(200, $middleware->handle($reqA, $next)['status']);
        self::assertSame(200, $middleware->handle($reqA, $next)['status']);
        $respA3 = $middleware->handle($reqA, $next);
        self::assertSame(429, $respA3['httpStatus'] ?? $respA3['status']);

        // IP B still has quota
        self::assertSame(200, $middleware->handle($reqB, $next)['status']);
    }
}
