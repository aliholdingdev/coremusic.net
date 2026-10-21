<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Contracts\Database\IDatabaseManager;

final class DatabaseManager implements IDatabaseManager
{
    private ?\PDO $pdo = null;

    public function __construct(private readonly DatabaseConfig $config)
    {
    }

    public function getPdo(): \PDO
    {
        if ($this->pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config->host,
                $this->config->port,
                $this->config->dbName,
                $this->config->charset,
            );

            $this->pdo = new \PDO($dsn, $this->config->user, $this->config->password, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return $this->pdo;
    }

    public function execute(string $sql, array $params = []): array
    {
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        return $result;
    }

    public function write(string $sql, array $params = []): bool
    {
        $stmt = $this->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->getPdo()->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    public function rollBack(): bool
    {
        return $this->getPdo()->rollBack();
    }
}
