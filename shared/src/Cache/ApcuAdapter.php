<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class ApcuAdapter implements CacheInterface
{
    public function get(string $key): mixed
    {
        if (!function_exists('apcu_fetch')) {
            return false;
        }
        $success = false;
        $value = apcu_fetch($key, $success);
        return $success ? $value : false;
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        if (!function_exists('apcu_store')) {
            return false;
        }
        return apcu_store($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        if (!function_exists('apcu_delete')) {
            return false;
        }
        return apcu_delete($key);
    }

    public function increment(string $key, int $step = 1): int|false
    {
        if (!function_exists('apcu_inc')) {
            return false;
        }
        return apcu_inc($key, $step);
    }

    public function clear(): bool
    {
        if (!function_exists('apcu_clear_cache')) {
            return false;
        }
        return apcu_clear_cache();
    }

    public function has(string $key): bool
    {
        if (!function_exists('apcu_exists')) {
            return false;
        }
        return apcu_exists($key);
    }
}
