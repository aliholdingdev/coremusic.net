<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use CoreMusic\Cache\CacheInterface;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Middleware\RateLimiterMiddleware;
use CoreMusic\Security\CacheRateLimiter;

/**
 * RateLimiterMiddleware — Rate Limiting Testleri
 *
 * ADR-013: cache tabanlı rate limiting (60 req/60s).
 *
 * ÖNEMLİ: Sahte (mock) cache kontratı YOK — eski mock `set => true` her koşulda
 * döndüğü için 429 ÜRETİMİ asla kanıtlanamıyordu. Tüm sayaç testleri GERÇEK
 * MemoryAdapter ile yazılır: limit altı geçer, limit aşımında 429 döner.
 *
 * @covers \CoreMusic\Middleware\RateLimiterMiddleware
 * @covers \CoreMusic\Security\CacheRateLimiter
 */
final class RateLimiterMiddlewareTest extends TestCase
{
    private MemoryAdapter $cache;

    protected function setUp(): void
    {
        $this->cache = new MemoryAdapter();
    }

    private function middleware(
        int $maxRequests = 60,
        int $windowSeconds = 60,
        ?array $trustedProxies = null,
        ?CacheInterface $cache = null,
    ): RateLimiterMiddleware {
        return new RateLimiterMiddleware(
            maxRequests: $maxRequests,
            windowSeconds: $windowSeconds,
            trustedProxies: $trustedProxies,
            rateLimiter: new CacheRateLimiter($cache ?? $this->cache),
        );
    }

    /**
     * @param array<string, mixed> $server
     */
    private function request(string $ip = '192.168.1.1', array $server = []): array
    {
        return ['method' => 'GET', 'server' => array_merge(['REMOTE_ADDR' => $ip], $server)];
    }

    /**
     * @param array<string, mixed> $request
     * @return array{0: array<string, mixed>, 1: bool}
     */
    private function passThrough(RateLimiterMiddleware $middleware, array $request): array
    {
        $coreCalled = false;
        $result = $middleware->handle($request, function (array $req) use (&$coreCalled): array {
            $coreCalled = true;

            return ['status' => 200];
        });

        return [$result, $coreCalled];
    }

    /* ============================================================
       İLK İSTEK + SINIR ALTI — HER ZAMAN İZİN
       ============================================================ */

    public function testFirstRequestAlwaysAllowed(): void
    {
        $middleware = $this->middleware(maxRequests: 60);

        [$result, $coreCalled] = $this->passThrough($middleware, $this->request());

        $this->assertTrue($coreCalled, 'First request should pass through');
        $this->assertSame(200, $result['status']);
    }

    public function testRequestsBelowLimitPassThrough(): void
    {
        $middleware = $this->middleware(maxRequests: 3);

        for ($i = 1; $i <= 3; $i++) {
            [$result, $coreCalled] = $this->passThrough($middleware, $this->request());
            $this->assertTrue($coreCalled, "Request #{$i} (limit 3) pass-through olmalı");
            $this->assertSame(200, $result['status']);
        }
    }

    /* ============================================================
       LIMIT AŞIMI — 429 GERÇEK ADAPTER İLE ÜRETİLİR
       ============================================================ */

    public function testRateLimitExceededReturns429(): void
    {
        $middleware = $this->middleware(maxRequests: 60, windowSeconds: 60);

        // 60 istek tamam, 61. istek limiti aşar.
        for ($i = 1; $i <= 60; $i++) {
            $this->assertTrue($this->passThrough($middleware, $this->request())[1], "Istek #{$i} izinli olmalı");
        }

        [$result, $coreCalled] = $this->passThrough($middleware, $this->request());

        $this->assertFalse($coreCalled, 'Limit aşılan istek handler\'a geçmemeli');
        $this->assertSame(429, $result['httpStatus']);
        $this->assertSame('rate_limit_exceeded', $result['body']['error']);
        $this->assertSame('60', $result['headers']['Retry-After']);
        $this->assertTrue($result['halt']);
    }

    public function testRateLimitExactlyAtMaxAllowed(): void
    {
        $middleware = $this->middleware(maxRequests: 60);

        for ($i = 1; $i <= 60; $i++) {
            [$result, $coreCalled] = $this->passThrough($middleware, $this->request());
            $this->assertTrue($coreCalled, "Tam limitte (#{$i}) istek hala izinli olmalı");
        }
    }

    public function testRateLimitStaysClosedForRestOfWindow(): void
    {
        $middleware = $this->middleware(maxRequests: 2);

        $this->passThrough($middleware, $this->request());
        $this->passThrough($middleware, $this->request());

        // 429 sonrası sayaç resetlenmemeli — pencere kapanana dek 429 sürer.
        for ($i = 0; $i < 3; $i++) {
            [$result, $coreCalled] = $this->passThrough($middleware, $this->request());
            $this->assertFalse($coreCalled);
            $this->assertSame(429, $result['httpStatus']);
        }
    }

    /* ============================================================
       IP BAĞIMSIZ SAYAÇ + ANAHTAR BİÇİMİ (rl: + md5(ip))
       ============================================================ */

    public function testCountersArePerClientIp(): void
    {
        $middleware = $this->middleware(maxRequests: 1);

        [$first, $firstCalled] = $this->passThrough($middleware, $this->request('203.0.113.10'));
        $this->assertTrue($firstCalled);

        [$second, $secondCalled] = $this->passThrough($middleware, $this->request('203.0.113.10'));
        $this->assertFalse($secondCalled);
        $this->assertSame(429, $second['httpStatus']);

        // Farklı IP aynı pencereden etkilenmez.
        [, $otherIpCalled] = $this->passThrough($middleware, $this->request('203.0.113.11'));
        $this->assertTrue($otherIpCalled, 'Farklı IP ayrı sayaca sahip olmalı');
    }

    public function testCounterKeyIsRlPrefixWithHashedIp(): void
    {
        $middleware = $this->middleware(maxRequests: 60);

        $this->passThrough($middleware, $this->request('10.0.0.1'));

        $this->assertSame(1, $this->cache->get('rl:' . md5('10.0.0.1')));
        $this->assertFalse($this->cache->get('rl:' . md5('10.0.0.2')));
    }

    public function testTrustedProxyXForwardedFor(): void
    {
        $middleware = $this->middleware(
            maxRequests: 60,
            trustedProxies: ['127.0.0.1'],
        );

        $this->passThrough($middleware, $this->request('127.0.0.1', [
            'HTTP_X_FORWARDED_FOR' => '203.0.113.50, 70.41.3.18',
        ]));

        // X-Forwarded-For'daki ilk IP sayaç anahtarı olmalı.
        $this->assertSame(1, $this->cache->get('rl:' . md5('203.0.113.50')));
        $this->assertFalse($this->cache->get('rl:' . md5('127.0.0.1')));
    }

    public function testUntrustedProxyUsesRemoteAddr(): void
    {
        $middleware = $this->middleware(
            maxRequests: 60,
            trustedProxies: ['10.0.0.1'],  // 192.168.1.1 trusted değil
        );

        $this->passThrough($middleware, $this->request('192.168.1.1', [
            'HTTP_X_FORWARDED_FOR' => '203.0.113.50',
        ]));

        $this->assertSame(1, $this->cache->get('rl:' . md5('192.168.1.1')));
        $this->assertFalse($this->cache->get('rl:' . md5('203.0.113.50')));
    }

    /* ============================================================
       CUSTOM LIMITS
       ============================================================ */

    public function testCustomMaxRequestsAndWindow(): void
    {
        $middleware = $this->middleware(maxRequests: 10, windowSeconds: 30);

        for ($i = 1; $i <= 10; $i++) {
            $this->assertTrue($this->passThrough($middleware, $this->request())[1]);
        }

        [$result, $coreCalled] = $this->passThrough($middleware, $this->request());

        $this->assertFalse($coreCalled);
        $this->assertSame(429, $result['httpStatus']);
        $this->assertSame('30', $result['headers']['Retry-After']);
    }

    /* ============================================================
       CACHE KULLANILAMAZSA — ADR-013 fail-open (genel uçlar)
       ============================================================ */

    public function testCacheOutageFailsOpen(): void
    {
        $deadCache = new class implements CacheInterface {
            public function get(string $key): mixed
            {
                return false;
            }

            public function set(string $key, mixed $value, int $ttl = 0): bool
            {
                return false;
            }

            public function delete(string $key): bool
            {
                return false;
            }

            public function increment(string $key, int $step = 1): int|false
            {
                return false;
            }

            public function clear(): bool
            {
                return false;
            }

            public function has(string $key): bool
            {
                return false;
            }
        };

        $middleware = $this->middleware(maxRequests: 1, cache: $deadCache);

        for ($i = 1; $i <= 3; $i++) {
            [, $coreCalled] = $this->passThrough($middleware, $this->request());
            $this->assertTrue($coreCalled, 'Cache=outage → genel uçlar fail-open (ADR-013)');
        }
    }

    public function testDefaultAdapterAllowsFirstRequest(): void
    {
        // rateLimiter enjekte edilmezse CacheManager adapter'ı devreye girer.
        $middleware = new RateLimiterMiddleware(maxRequests: 60, windowSeconds: 60);

        [$result, $coreCalled] = $this->passThrough($middleware, $this->request('198.51.100.1'));

        $this->assertTrue($coreCalled, 'Varsayılan adapter ile ilk istek geçmeli');
        $this->assertSame(200, $result['status']);
    }
}
