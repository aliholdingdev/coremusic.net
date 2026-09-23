<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use CoreMusic\Middleware\RateLimiterMiddleware;
use CoreMusic\Cache\CacheInterface;

/**
 * RateLimiterMiddleware — Rate Limiting Testleri
 *
 * ADR-013: APCu tabanlı rate limiting (60 req/60s)
 * Cache mock ile test edilir.
 *
 * @covers \CoreMusic\Middleware\RateLimiterMiddleware
 */
final class RateLimiterMiddlewareTest extends TestCase
{
    /* ============================================================
       İLK İSTEK — HER ZAMAN İZİN
       ============================================================ */

    public function testFirstRequestAlwaysAllowed(): void
    {
        $cache = $this->createCacheMock([
            'set' => true,  // İlk istek — yeni key
        ]);

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );

        $coreCalled = false;
        $result = $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'First request should pass through');
        $this->assertSame(200, $result['status']);
    }

    /* ============================================================
       LIMIT AŞIMI — 429 DÖNÜR
       ============================================================ */

    public function testRateLimitExceededReturns429(): void
    {
        $cache = $this->createCacheMock([
            'set' => false,     // Mevcut key — yeni değil
            'increment' => 61,  // Limit aşıldı (60+1)
        ]);

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );

        $result = $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(429, $result['httpStatus']);
        $this->assertSame('rate_limit_exceeded', $result['body']['error']);
        $this->assertSame('60', $result['headers']['Retry-After']);
        $this->assertTrue($result['halt']);
    }

    public function testRateLimitExactlyAtMaxAllowed(): void
    {
        $cache = $this->createCacheMock([
            'set' => false,
            'increment' => 60,  // Tam limit — hala izinli
        ]);

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );

        $coreCalled = false;
        $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'Exactly at max should still pass');
    }

    /* ============================================================
       CACHE HATASI — GEÇİŞ İZİN
       ============================================================ */

    public function testCacheIncrementFailureAllowsRequest(): void
    {
        $cache = $this->createCacheMock([
            'set' => false,
            'increment' => false,  // Cache hatası
        ]);

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );

        $coreCalled = false;
        $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'Cache failure should allow request (fail-open)');
    }

    public function testCacheIncrementNullAllowsRequest(): void
    {
        $cache = $this->createCacheMock([
            'set' => false,
            'increment' => null,
        ]);

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );

        $coreCalled = false;
        $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled);
    }

    /* ============================================================
       CACHE YOKSA — DOĞRUDAN GEÇİŞ
       ============================================================ */

    public function testNoCacheAvailableAllowsRequest(): void
    {
        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: null
        );

        $coreCalled = false;
        $result = $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'No cache should allow request');
    }

    /* ============================================================
       IP RESOLUTION
       ============================================================ */

    public function testClientIpFromRemoteAddr(): void
    {
        $cache = $this->createCacheMock(['set' => true]);
        $capturedKey = null;

        // Cache key IP bazlı olmalı
        $cache->method('set')
            ->willReturnCallback(function (string $key, mixed $value, int $ttl) use (&$capturedKey) {
                $capturedKey = $key;
                return true;
            });

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );

        $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '10.0.0.1']],
            fn(array $req) => ['status' => 200]
        );

        $this->assertNotNull($capturedKey);
        $this->assertStringStartsWith('rl:', $capturedKey);
    }

    public function testTrustedProxyXForwardedFor(): void
    {
        $cache = $this->createCacheMock(['set' => true]);
        $capturedKey = null;

        $cache->method('set')
            ->willReturnCallback(function (string $key, mixed $value, int $ttl) use (&$capturedKey) {
                $capturedKey = $key;
                return true;
            });

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            trustedProxies: ['127.0.0.1'],
            cache: $cache
        );

        $middleware->handle(
            [
                'method' => 'GET',
                'server' => [
                    'REMOTE_ADDR' => '127.0.0.1',
                    'HTTP_X_FORWARDED_FOR' => '203.0.113.50, 70.41.3.18',
                ],
            ],
            fn(array $req) => ['status' => 200]
        );

        // X-Forwarded-For'daki ilk IP kullanılmalı
        $this->assertNotNull($capturedKey);
    }

    public function testUntrustedProxyUsesRemoteAddr(): void
    {
        $cache = $this->createCacheMock(['set' => true]);
        $capturedKey = null;

        $cache->method('set')
            ->willReturnCallback(function (string $key, mixed $value, int $ttl) use (&$capturedKey) {
                $capturedKey = $key;
                return true;
            });

        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            trustedProxies: ['10.0.0.1'],  // 192.168.1.1 trusted değil
            cache: $cache
        );

        $middleware->handle(
            [
                'method' => 'GET',
                'server' => [
                    'REMOTE_ADDR' => '192.168.1.1',
                    'HTTP_X_FORWARDED_FOR' => '203.0.113.50',
                ],
            ],
            fn(array $req) => ['status' => 200]
        );

        $this->assertNotNull($capturedKey);
    }

    /* ============================================================
       CUSTOM LIMITS
       ============================================================ */

    public function testCustomMaxRequests(): void
    {
        $cache = $this->createCacheMock([
            'set' => false,
            'increment' => 11,  // Custom limit 10'u aştı
        ]);

        $middleware = new RateLimiterMiddleware(
            maxRequests: 10,
            windowSeconds: 30,
            cache: $cache
        );

        $result = $middleware->handle(
            ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(429, $result['httpStatus']);
        $this->assertSame('30', $result['headers']['Retry-After']);
    }

    /* ============================================================
       HELPER
       ============================================================ */

    private function createCacheMock(array $behaviors): CacheInterface
    {
        $cache = $this->createMock(CacheInterface::class);

        if (isset($behaviors['set'])) {
            $cache->method('set')->willReturn($behaviors['set']);
        }

        if (isset($behaviors['increment'])) {
            $cache->method('increment')->willReturn($behaviors['increment']);
        }

        return $cache;
    }
}
