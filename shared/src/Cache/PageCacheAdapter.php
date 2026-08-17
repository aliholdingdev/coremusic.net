<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class PageCacheAdapter implements PageCacheInterface
{
    private CacheInterface $cache;
    private bool $enabled;
    private int $ttlPage;

    public function __construct(?CacheInterface $cache = null, bool $enabled = true, int $ttlPage = 600)
    {
        $this->cache = $cache ?? CacheManager::getAdapter();
        $this->enabled = $enabled;
        $this->ttlPage = $ttlPage;
    }

    public function isPageCacheEnabled(): bool
    {
        return $this->enabled;
    }

    public function ttlPage(): int
    {
        return $this->ttlPage;
    }

    public function pageKey(string $uri, bool $authenticated): string
    {
        $prefix = $authenticated ? 'page_auth_' : 'page_pub_';
        return $prefix . md5($uri);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = $this->cache->get($key);
        if ($cached !== false) {
            return $cached;
        }

        $value = $callback();
        $this->cache->set($key, $value, $ttl);
        return $value;
    }
}
