<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Database;

interface IDatabaseRegistry
{
    public function registerMySql(string $key, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void;
    public function get(string $key): IDatabaseManager;
    public function has(string $key): bool;
}
