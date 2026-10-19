<?php declare(strict_types=1);

namespace CoreMusic\Test\Repository\Sqlite;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;

/**
 * Test double: her anahtar için aynı SQLite yöneticisini döndürür.
 */
final class SqliteDatabaseRegistry implements IDatabaseRegistry
{
    public function __construct(private readonly IDatabaseManager $manager) {}

    public function registerMySql(
        string $key,
        string $host,
        string $dbName,
        string $user,
        string $password,
        int $port = 3306,
        string $charset = 'utf8mb4'
    ): void {
        // Test double — MySQL kayıt yok.
    }

    public function get(string $key): IDatabaseManager
    {
        return $this->manager;
    }

    public function has(string $key): bool
    {
        return true;
    }
}
