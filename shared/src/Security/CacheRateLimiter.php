<?php declare(strict_types=1);

namespace CoreMusic\Security;

use CoreMusic\Interfaces\Security\IRateLimiter;
use CoreMusic\Cache\CacheInterface;

final class CacheRateLimiter implements IRateLimiter
{
    private const KEY_PREFIX = 'rl:';

    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $cacheKey = self::KEY_PREFIX . $key;
        $count = $this->cache->get($cacheKey);
        if ($count === false || $count === null) {
            return false;
        }
        return (int)$count >= $maxAttempts;
    }

    public function increment(string $key, int $windowSeconds): void
    {
        $cacheKey = self::KEY_PREFIX . $key;
        $existing = $this->cache->get($cacheKey);
        if ($existing === false || $existing === null) {
            $this->cache->set($cacheKey, 1, $windowSeconds);
        } else {
            $this->cache->increment($cacheKey, 1);
        }
    }

    public function reset(string $key): void
    {
        $this->cache->delete(self::KEY_PREFIX . $key);
    }
}
