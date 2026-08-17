<?php declare(strict_types=1);

namespace CoreMusic\Config;

use CoreMusic\Exception\ServerException;
use CoreMusic\Interfaces\Config\IConfigManager;

final class ConfigManager implements IConfigManager
{
    private const SENSITIVE_KEYS = [
        'database.mysql.password',
        'security.app_pepper',
        'security.encryption_key',
        'security.session_key',
    ];

    public function __construct(
        private array $config = [],
        private array $cache = [],
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        $this->cache[$key] = $value;
        return $value;
    }

    public function getSecure(string $key): string
    {
        $value = $this->get($key);
        if ($value === null || $value === '') {
            throw ServerException::configError($key);
        }
        return (string)$value;
    }

    public function getEnv(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    public function maskSecret(string $value, int $visibleStart = 3, int $visibleEnd = 3): string
    {
        $length = strlen($value);
        if ($length <= ($visibleStart + $visibleEnd)) {
            return str_repeat('*', $length);
        }
        $start = substr($value, 0, $visibleStart);
        $end = substr($value, -$visibleEnd);
        $mask = str_repeat('*', $length - $visibleStart - $visibleEnd);
        return $start . $mask . $end;
    }

    public function isSensitiveKey(string $key): bool
    {
        return in_array($key, self::SENSITIVE_KEYS, true);
    }

    public function isProduction(): bool
    {
        return $this->get('app.env') === 'production';
    }

    public function isDevelopment(): bool
    {
        return $this->get('app.env') === 'development';
    }

    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
            } else {
                if (!isset($config[$segment]) || !is_array($config[$segment])) {
                    $config[$segment] = [];
                }
                $config = &$config[$segment];
            }
        }

        unset($this->cache[$key]);
    }

    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return true;
    }

    public function all(): array
    {
        return $this->config;
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }
}
