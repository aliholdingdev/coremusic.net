<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Database;

/**
 * Interface for SQL database managers (MySQL, SQLite, SQL Server).
 *
 * Extends IDatabaseManager with SQL-specific methods.
 * All implementations use PDO and support transactions.
 */
interface ISqlDatabaseManager extends IDatabaseManager
{
    /**
     * Get the underlying PDO connection.
     *
     * @return \PDO The PDO connection instance
     */
    public function getPdo(): \PDO;
}