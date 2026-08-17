<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Config;

interface IConfigManager
{
    public function get(string $key, mixed $default = null): mixed;
    public function getSecure(string $key): string;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function all(): array;
    public function isProduction(): bool;
    public function isDevelopment(): bool;
}
