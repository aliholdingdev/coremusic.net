<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class MemoryAdapter implements CacheInterface
{
    /** @var array<string, array{value: mixed, expires: int}> */
    private array $store = [];

    public function get(string $key): mixed
    {
        if (!isset($this->store[$key])) {
            return false;
        }
        $entry = $this->store[$key];
        if ($entry['expires'] > 0 && $entry['expires'] < time()) {
            unset($this->store[$key]);
            return false;
        }
        return $entry['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $this->store[$key] = [
            'value'   => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,
        ];
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->store[$key]);
        return true;
    }

    public function increment(string $key, int $step = 1): int|false
    {
        if (!isset($this->store[$key]) || !is_int($this->store[$key]['value'])) {
            return false;
        }
        $this->store[$key]['value'] += $step;
        return $this->store[$key]['value'];
    }

    public function clear(): bool
    {
        $this->store = [];
        return true;
    }

    public function has(string $key): bool
    {
        return isset($this->store[$key]);
    }
}
