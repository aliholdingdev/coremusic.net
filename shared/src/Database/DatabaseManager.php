<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Contracts\Database\ISqlDatabaseManager;

/**
 * DatabaseManager — tek DB bağlantısı (lazy PDO); DB başına tek instance (MySQL/MariaDB).
 *
 * Kayıt yolu: DatabaseRegistry::registerMySql() (veya ConnectionFactory .env'den).
 * PDO ayarları: ERRMODE_EXCEPTION · FETCH_ASSOC · EMULATE_PREPARES=false · utf8mb4 (ADR-002/022).
 *
 * EŞZAMANLILIK (PDO + fork): PDO bağlantıları fork ile devralınamaz — child
 * process parent'ın socket'ini kullanamaz; pcntl_fork / paralel worker sonrası
 * çocuk süreç kendi DatabaseManager/factory çağrısıyla YENİ bağlantı açmalıdır.
 *
 * NOT (Adım 03): execute()/write() mevcut (repository sözleşmesi) — SQL katmanı
 * repository'dir; bağlantı katmanı yeni SQL API üretmez.
 *
 * Diğer sağlayıcılar:
 *  - SQLiteDatabaseManager  (pdo_sqlite)
 *  - SqlsrvDatabaseManager  (pdo_sqlsrv)
 *  - MongoDbManager         (mongodb extension)
 */
final class DatabaseManager extends AbstractPdoDatabaseManager
{
    public function __construct(private readonly DatabaseConfig $config)
    {
    }

    protected function buildDsn(): string
    {
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $this->config->host,
            $this->config->port,
            $this->config->dbName,
            $this->config->charset,
        );
    }

    protected function getCredentials(): array
    {
        return [$this->config->user, $this->config->password];
    }
}