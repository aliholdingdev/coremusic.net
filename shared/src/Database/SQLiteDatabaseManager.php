<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Contracts\Database\ISqlDatabaseManager;

/**
 * SQLiteDatabaseManager — SQLite (file or memory) implementation of ISqlDatabaseManager.
 *
 * Driver: pdo_sqlite (bundled with PHP core since 5.1).
 * DSN: sqlite:{path} or sqlite::memory:
 * No host/port/user/password — file path only.
 *
 * Concurrency: SQLite file locks; :memory: databases are per-process.
 * Fork safety: same as DatabaseManager — child must reinitialize.
 *
 * NOT (Adım 03): execute()/write() mevcut (repository sözleşmesi) — SQL katmanı
 * repository'dir; bağlantı katmanı yeni SQL API üretmez.
 */
final class SQLiteDatabaseManager extends AbstractPdoDatabaseManager
{
    public function __construct(private readonly string $path = ':memory:')
    {
        if (!\extension_loaded('pdo_sqlite')) {
            throw new \RuntimeException(
                'PDO driver pdo_sqlite not available: install it or enable the extension.'
            );
        }
    }

    protected function buildDsn(): string
    {
        return sprintf('sqlite:%s', $this->path);
    }

    protected function getCredentials(): array
    {
        return ['', ''];
    }
}