<?php declare(strict_types=1);

namespace CoreMusic\Config;

final class EnvParser
{
    public static function load(string $envFile): array
    {
        if (!is_file($envFile)) {
            return [];
        }

        $env = [];
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2) {
                if (($value[0] === '"' && $value[-1] === '"') ||
                    ($value[0] === "'" && $value[-1] === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            $env[$key] = $value;
        }

        return $env;
    }

    public static function loadIntoEnv(string $envFile): void
    {
        $env = self::load($envFile);
        foreach ($env as $key => $value) {
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }
}
