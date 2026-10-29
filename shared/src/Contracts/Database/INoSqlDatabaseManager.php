<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Database;

/**
 * Interface for NoSQL database managers (MongoDB, etc.).
 *
 * Does NOT include getPdo() or transaction methods since NoSQL databases
 * may not use PDO or support ACID transactions in the same way.
 * Implementations should provide their own connection management.
 */
interface INoSqlDatabaseManager
{
    /**
     * Execute a read query and return results as array.
     *
     * @param string $query Query specification (JSON filter, pipeline, etc.)
     * @param array $params Query options (projection, sort, limit, collection, etc.)
     * @return array Query results
     */
    public function execute(string $query, array $params = []): array;

    /**
     * Execute a write operation (insert/update/delete).
     *
     * @param string $query Operation specification (JSON bulk operations, etc.)
     * @param array $params Operation options (collection, etc.)
     * @return bool Success status
     */
    public function write(string $query, array $params = []): bool;

    /**
     * Get the last inserted ID (if applicable).
     *
     * @return string Last inserted ID or empty string if not applicable
     */
    public function lastInsertId(): string;

    /**
     * Check if the database supports transactions.
     *
     * @return bool True if transactions are supported
     */
    public function supportsTransactions(): bool;
}