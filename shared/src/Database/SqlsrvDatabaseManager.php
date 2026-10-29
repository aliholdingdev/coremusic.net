<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Contracts\Database\ISqlDatabaseManager;

/**
 * SqlsrvDatabaseManager — Microsoft SQL Server (sqlsrv / pdo_sqlsrv) implementation of ISqlDatabaseManager.
 *
 * Driver: pdo_sqlsrv (Microsoft SQL Server for PHP).
 * DSN: sqlsrv:Server={host},{port};Database={dbname}
 * Requires: extension_loaded('pdo_sqlsrv')
 * Authentication: SQL Server authentication (user/password) or Windows auth (integrated)
 *
 * Concurrency: SQL Server handles connection pooling at driver level.
 * Fork safety: same as DatabaseManager — child must reinitialize.
 *
 * NOT (Adım 03): execute()/write() mevcut (repository sözleşmesi) — SQL katmanı
 * repository'dir; bağlantı katmanı yeni SQL API üretmez.
 */
final class SqlsrvDatabaseManager extends AbstractPdoDatabaseManager
{
    public function __construct(
        private readonly string $host = 'localhost',
        private readonly string $dbName = 'coremusic',
        private readonly string $user = 'sa',
        private readonly string $password = '',
        private readonly int $port = 1433,
    ) {
        if (!\extension_loaded('pdo_sqlsrv')) {
            throw new \RuntimeException(
                'PDO driver pdo_sqlsrv not available: install it (composer require microsoft/pdo_sqlsrv or enable the WAMP extension).'
            );
        }
    }

    protected function buildDsn(): string
    {
        return sprintf(
            'sqlsrv:Server=%s,%d;Database=%s',
            $this->host,
            $this->port,
            $this->dbName,
        );
    }

    protected function getCredentials(): array
    {
        return [$this->user, $this->password];
    }
}