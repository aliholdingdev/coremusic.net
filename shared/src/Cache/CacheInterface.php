<?php declare(strict_types=1);

namespace CoreMusic\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 0): bool;
    public function delete(string $key): bool;
    public function increment(string $key, int $step = 1): int|false;
    public function clear(): bool;
    public function has(string $key): bool;
}
