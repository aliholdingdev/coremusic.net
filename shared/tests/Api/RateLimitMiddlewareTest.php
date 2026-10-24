<?php declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Middleware\RateLimitMiddleware;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Security\CacheRateLimiter;
use PHPUnit\Framework\TestCase;

/**
 * RateLimitMiddleware — API varyantı.
 *
 * Kontrat: CoreMusic\Contracts\Security\IRateLimiter (impl: CacheRateLimiter).
 * Gerçek MemoryAdapter ile limit altı geçiş ve limit aşımında 429 kanıtlanır.
 *
 * @covers \CoreMusic\Api\Middleware\RateLimitMiddleware
 * @covers \CoreMusic\Security\CacheRateLimiter
 */
final class RateLimitMiddlewareTest extends TestCase
{
    private MemoryAdapter $cache;

    protected function setUp(): void
    {
        $this->cache = new MemoryAdapter();
        $_SERVER['REMOTE_ADDR'] = '198.51.100.7';
        unset($_SERVER['HTTP_X_API_KEY']);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_X_API_KEY']);
    }

    private function middleware(int $maxRequests = 60, int $windowSeconds = 60): RateLimitMiddleware
    {
        return new RateLimitMiddleware(
            new CacheRateLimiter($this->cache),
            $maxRequests,
            $windowSeconds,
        );
    }

    public function testRequestsUpToLimitPassThrough(): void
    {
        $middleware = $this->middleware(maxRequests: 2, windowSeconds: 60);

        for ($i = 1; $i <= 2; $i++) {
            $called = false;
            $response = $middleware([], function () use (&$called): array {
                $called = true;

                return ['ok' => true];
            });

            $this->assertTrue($called, "Istek #{$i} limit altı pass-through olmalı");
            $this->assertSame(['ok' => true], $response);
        }
    }

    public function testRequestOverLimitReturnsRateLimitExceeded(): void
    {
        $middleware = $this->middleware(maxRequests: 2, windowSeconds: 60);

        for ($i = 1; $i <= 2; $i++) {
            $middleware([], static fn (): array => ['ok' => true]);
        }

        $called = false;
        $response = $middleware([], function () use (&$called): array {
            $called = true;

            return ['ok' => true];
        });

        $this->assertFalse($called, 'Limit aşılan istek handler\'a geçmemeli');
        $this->assertSame('RATE_LIMIT_EXCEEDED', $response['error']['code'] ?? null);
        $this->assertSame('Too many requests', $response['error']['message'] ?? null);
    }

    public function testApiKeyDoesNotBypassExhaustedIpRateLimit(): void
    {
        $middleware = $this->middleware(maxRequests: 1, windowSeconds: 60);

        // IP anahtarını tüket (header'sız istek).
        $middleware([], static fn (): array => ['ok' => true]);
        $limited = $middleware([], static fn (): array => ['ok' => true]);
        $this->assertSame('RATE_LIMIT_EXCEEDED', $limited['error']['code'] ?? null);

        // SEC-01: API key eklemek IP sayacını atlatamaz — ip: bucket'ı her istekte taranır.
        $_SERVER['HTTP_X_API_KEY'] = 'test-api-key';
        $called = false;
        $response = $middleware([], function () use (&$called): array {
            $called = true;

            return ['ok' => true];
        });

        $this->assertFalse($called, 'API key, tükenmiş IP rate-limit\'i atlatamaz (SEC-01)');
        $this->assertSame('RATE_LIMIT_EXCEEDED', $response['error']['code'] ?? null);
    }

    public function testFreshIpWithApiKeyStillPassesUnderLimit(): void
    {
        $middleware = $this->middleware(maxRequests: 1, windowSeconds: 60);

        // Farklı IP + API key: ip: ve api: bucket'ları boş → limit altında geçer.
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_X_API_KEY'] = 'another-key';
        $called = false;
        $middleware([], function () use (&$called): array {
            $called = true;

            return ['ok' => true];
        });

        $this->assertTrue($called, 'Tükenmemiş IP + API key isteği limit altında geçmeli');
    }
}
