<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Security;

use CoreMusic\Cache\CacheInterface;
use CoreMusic\Security\CacheRateLimiter;
use CoreMusic\Security\RateLimiterUnavailableException;
use PHPUnit\Framework\TestCase;

/**
 * PRD-SEC-004: sayaç yazılamazsa istek fail-open geçmemeli.
 */
final class CacheRateLimiterFailClosedTest extends TestCase
{
    private function brokenCache(): CacheInterface
    {
        return new class implements CacheInterface {
            public function get(string $key): mixed { return false; }
            public function set(string $key, mixed $value, int $ttl = 0): bool { return false; }
            public function delete(string $key): bool { return false; }
            public function increment(string $key, int $step = 1): int|false { return false; }
            public function clear(): bool { return false; }
            public function has(string $key): bool { return false; }
        };
    }

    public function testIncrementThrowsWhenCounterCannotBeStored(): void
    {
        $limiter = new CacheRateLimiter($this->brokenCache());

        $this->expectException(RateLimiterUnavailableException::class);
        $limiter->increment('ip:1.2.3.4', 60);
    }

    public function testExceptionIsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, new RateLimiterUnavailableException());
    }

    public function testIncrementSucceedsWhenCounterStored(): void
    {
        $healthy = new class implements CacheInterface {
            public function get(string $key): mixed { return false; }
            public function set(string $key, mixed $value, int $ttl = 0): bool { return true; }
            public function delete(string $key): bool { return true; }
            public function increment(string $key, int $step = 1): int|false { return false; }
            public function clear(): bool { return true; }
            public function has(string $key): bool { return false; }
        };

        $limiter = new CacheRateLimiter($healthy);
        $limiter->increment('ip:1.2.3.4', 60);
        $this->assertTrue(true);
    }
}
