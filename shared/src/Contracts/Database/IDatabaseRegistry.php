<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Database;

/**
 * Database Registry interface.
 *
 * Provides registration and retrieval of database managers for different providers.
 * Supports both SQL (ISqlDatabaseManager) and NoSQL (INoSqlDatabaseManager) databases.
 */
interface IDatabaseRegistry
{
    /**
     * Register a MySQL/MariaDB database (backward compatibility).
     *
     * @param string $key Unique key for this database
     * @param string $host Database host
     * @param string $dbName Database name
     * @param string $user Username
     * @param string $password Password
     * @param int $port Port (default 3306)
     * @param string $charset Charset (default utf8mb4)
     */
    public function registerMySql(string $key, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void;

    /**
     * Register a database provider (multi-DB support).
     *
     * Supported provider types: 'mysql', 'mariadb', 'sqlite', 'mssql', 'mongodb'
     *
     * @param string $key Unique key for this database
     * @param string $providerType Provider type ('mysql', 'sqlite', 'mssql', 'mongodb')
     * @param string $host Database host (or file path for SQLite)
     * @param string $dbName Database name
     * @param string $user Username
     * @param string $password Password
     * @param int $port Port
     * @param string $charset Charset
     */
    public function registerProvider(string $key, string $providerType, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void;

    /**
     * Get a database manager by key (base interface).
     *
     * @param string $key Database key
     * @return IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager
     * @throws \RuntimeException If database is not registered
     */
    public function get(string $key): IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager;

    /**
     * Get a SQL database manager by key.
     *
     * @param string $key Database key
     * @return ISqlDatabaseManager
     * @throws \RuntimeException If database is not registered or is not a SQL database
     */
    public function getSql(string $key): ISqlDatabaseManager;

    /**
     * Get a NoSQL database manager by key.
     *
     * @param string $key Database key
     * @return INoSqlDatabaseManager
     * @throws \RuntimeException If database is not registered or is not a NoSQL database
     */
    public function getNoSql(string $key): INoSqlDatabaseManager;

    /**
     * Check if a database is registered.
     *
     * @param string $key Database key
     * @return bool True if registered
     */
    public function has(string $key): bool;
}