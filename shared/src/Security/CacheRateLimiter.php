<?php declare(strict_types=1);

namespace CoreMusic\Security;

use CoreMusic\Contracts\Security\IRateLimiter;
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

        // Tek atomik adım: get-then-set yarışı yok; dönüş değeri artık yok sayılmıyor.
        $count = $this->cache->increment($cacheKey, 1);
        if ($count !== false) {
            return;
        }

        // Key yok (yeni pencere) veya increment başarısız → pencereyi sıfırdan başlat.
        if (!$this->cache->set($cacheKey, 1, $windowSeconds)) {
            // Fail-safe: sayaç tutulamazsa istekler fail-open akar — sessiz değil, gözlemlenir.
            \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limiter_increment_failed', ['key' => $cacheKey]);
        }
    }

    public function reset(string $key): void
    {
        $this->cache->delete(self::KEY_PREFIX . $key);
    }
}
