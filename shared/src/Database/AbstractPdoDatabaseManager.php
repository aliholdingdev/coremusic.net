<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Contracts\Database\ISqlDatabaseManager;
use CoreMusic\Database\Config\DatabaseConfig;

/**
 * Abstract base class for PDO-based database managers (MySQL, SQLite, SQL Server).
 *
 * Provides shared implementation for:
 * - Connection management (lazy PDO)
 * - Query execution (execute, write)
 * - Transaction handling (beginTransaction, commit, rollBack, runInTransaction)
 * - lastInsertId
 *
 * Subclasses only need to implement:
 * - buildDsn(): string - Build the DSN string for the specific driver
 * - getCredentials(): array - Return [user, password] for authentication
 *
 * @see DatabaseManager (MySQL/MariaDB)
 * @see SQLiteDatabaseManager (SQLite)
 * @see SqlsrvDatabaseManager (Microsoft SQL Server)
 */
abstract class AbstractPdoDatabaseManager implements ISqlDatabaseManager
{
    private ?\PDO $pdo = null;

    /**
     * Build the DSN string for this database driver.
     *
     * @return string DSN string (e.g., 'mysql:host=...;dbname=...')
     */
    abstract protected function buildDsn(): string;

    /**
     * Get credentials for database authentication.
     *
     * @return array{0: string, 1: string} [user, password]
     */
    abstract protected function getCredentials(): array;

    /**
     * {@inheritDoc}
     */
    public function getPdo(): \PDO
    {
        if ($this->pdo === null) {
            $dsn = $this->buildDsn();
            [$user, $password] = $this->getCredentials();

            $this->pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return $this->pdo;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(string $sql, array $params = []): array
    {
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $sql, array $params = []): bool
    {
        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(): string
    {
        return $this->getPdo()->lastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack(): bool
    {
        return $this->getPdo()->rollBack();
    }

    /**
     * {@inheritDoc}
     */
    public function runInTransaction(callable $fn): mixed
    {
        $pdo = $this->getPdo();

        if ($pdo->inTransaction()) {
            return $fn($pdo);
        }

        $pdo->beginTransaction();
        try {
            $result = $fn($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBackIfActive($pdo);
            throw $e;
        }
    }

    /**
     * Rollback if transaction is still active.
     *
     * Called when commit() might have failed and left transaction open.
     *
     * @param \PDO $pdo The PDO connection
     */
    private function rollBackIfActive(\PDO $pdo): void
    {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}