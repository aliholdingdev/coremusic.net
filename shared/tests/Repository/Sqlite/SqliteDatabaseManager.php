<?php declare(strict_types=1);

namespace CoreMusic\Test\Repository\Sqlite;

use CoreMusic\Contracts\Database\IDatabaseManager;

/**
 * Test double: SQLite in-memory `IDatabaseManager`.
 *
 * ADR-002 uyumu korunur (PDO prepared statement) — canlı MySQL'e bağlanmadan
 * `coremusic_auth.api_keys` şemasının birebir SQL sözleşmesi sınanır.
 */
final class SqliteDatabaseManager implements IDatabaseManager
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public function execute(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function write(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return (string) $this->pdo->lastInsertId();
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

    /**
     * `coremusic_auth.api_keys` — canlı şema ile birebir (BINARY(16) TEXT olarak).
     */
    public function createApiKeysSchema(): void
    {
        $this->pdo->exec(<<<'SQL'
            CREATE TABLE api_keys (
                id            TEXT PRIMARY KEY,
                user_id       TEXT NOT NULL,
                key_hash      TEXT NOT NULL UNIQUE,
                key_prefix    TEXT NOT NULL,
                key_name      TEXT,
                scopes        TEXT,
                key_type      TEXT NOT NULL DEFAULT 'user',
                rate_limit    INTEGER DEFAULT 1000,
                is_active     INTEGER DEFAULT 1,
                last_used_at  TEXT,
                expires_at    TEXT,
                created_at    TEXT,
                updated_at    TEXT,
                is_deleted    INTEGER DEFAULT 0,
                deleted_at    TEXT
            );
            CREATE INDEX idx_apikeys_type   ON api_keys(key_type);
            CREATE INDEX idx_apikeys_active ON api_keys(is_active);
            CREATE INDEX idx_apikeys_hash   ON api_keys(key_hash);
            CREATE INDEX idx_apikeys_prefix ON api_keys(key_prefix);
            CREATE INDEX idx_apikeys_user   ON api_keys(user_id);
            SQL);
    }
}
