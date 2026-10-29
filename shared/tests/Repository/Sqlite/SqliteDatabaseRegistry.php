<?php declare(strict_types=1);

namespace CoreMusic\Test\Repository\Sqlite;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Contracts\Database\ISqlDatabaseManager;
use CoreMusic\Contracts\Database\INoSqlDatabaseManager;

/**
 * Test double: her anahtar için aynı SQLite yöneticisini döndürür.
 */
final class SqliteDatabaseRegistry implements IDatabaseRegistry
{
    public function __construct(private readonly ISqlDatabaseManager $manager) {}

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

    public function registerProvider(
        string $key,
        string $providerType,
        string $host,
        string $dbName,
        string $user,
        string $password,
        int $port = 3306,
        string $charset = 'utf8mb4'
    ): void {
        // Test double — generic provider kayıt yok.
    }

    public function get(string $key): IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager
    {
        return $this->manager;
    }

    public function getSql(string $key): ISqlDatabaseManager
    {
        return $this->manager;
    }

    public function getNoSql(string $key): INoSqlDatabaseManager
    {
        throw new \RuntimeException("SqliteDatabaseRegistry does not support NoSQL databases.");
    }

    public function has(string $key): bool
    {
        return true;
    }
}