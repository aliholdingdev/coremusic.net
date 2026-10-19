<?php declare(strict_types=1);

namespace CoreMusic\Cache;

interface PageCacheInterface
{
    public function isPageCacheEnabled(): bool;
    public function ttlPage(): int;
    public function pageKey(string $uri, bool $authenticated): string;
    public function remember(string $key, int $ttl, callable $callback): mixed;
}
