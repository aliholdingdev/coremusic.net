<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Interfaces\Database\IDatabaseManager;
use CoreMusic\Interfaces\Database\IDatabaseRegistry;

final class DatabaseRegistry implements IDatabaseRegistry
{
    /** @var array<string, IDatabaseManager> */
    private array $managers = [];

    public function registerMySql(string $key, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void
    {
        $this->managers[$key] = new DatabaseManager(
            new DatabaseConfig($host, $dbName, $user, $password, $port, $charset)
        );
    }

    public function get(string $key): IDatabaseManager
    {
        if (!isset($this->managers[$key])) {
            throw new \RuntimeException("Database '{$key}' is not registered.");
        }
        return $this->managers[$key];
    }

    public function has(string $key): bool
    {
        return isset($this->managers[$key]);
    }
}
