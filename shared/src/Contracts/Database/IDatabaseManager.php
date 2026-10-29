<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Database;

/**
 * Base interface for all database managers.
 *
 * Common operations shared by both SQL and NoSQL databases.
 * Specific interfaces (ISqlDatabaseManager, INoSqlDatabaseManager) extend this.
 */
interface IDatabaseManager
{
    /**
     * Execute a read query and return results as array.
     *
     * @param string $query SQL query or query specification
     * @param array $params Bound parameters or query options
     * @return array Query results
     */
    public function execute(string $query, array $params = []): array;

    /**
     * Execute a write operation (INSERT/UPDATE/DELETE or equivalent).
     *
     * @param string $query SQL query or operation specification
     * @param array $params Bound parameters or operation options
     * @return bool Success status
     */
    public function write(string $query, array $params = []): bool;

    /**
     * Get the last inserted ID.
     *
     * @return string Last inserted ID (empty string if not applicable)
     */
    public function lastInsertId(): string;
}