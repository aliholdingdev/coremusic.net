<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Interfaces\Database\IDatabaseManager;

final class DatabaseManager implements IDatabaseManager
{
    private \PDO $pdo;

    public function __construct(DatabaseConfig $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config->host,
            $config->port,
            $config->dbName,
            $config->charset,
        );

        $this->pdo = new \PDO($dsn, $config->user, $config->password, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public function execute(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        return is_array($result) ? $result : [];
    }

    public function write(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}
