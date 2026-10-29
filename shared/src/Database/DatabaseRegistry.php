<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Contracts\Database\ISqlDatabaseManager;
use CoreMusic\Contracts\Database\INoSqlDatabaseManager;

/**
 * DatabaseRegistry — key → manager map (tek kayıt/okuma kaynağı).
 *
 * registerProvider: sürücü seçimi tek karar noktasıdır (match):
 *  mysql | mariadb → DatabaseManager (MySQL PDO)
 *  sqlite         → SQLiteDatabaseManager (host param = DOSYA YOLU)
 *  mssql          → SqlsrvDatabaseManager (pdo_sqlsrv)
 *  mongodb        → MongoDbManager (mongodb extension)
 * Bilinmeyen provider: RuntimeException — kayıt OLUŞMADAN fırlatılır
 * (fail-closed: yarı-kayıt yok).
 *
 * registerMySql kolaylık alias'ıdır (pre-multi-DB çağıranlar için destekleniyor,
 * IDatabaseRegistry sözleşmesidir — ADR-003/014).
 */
final class DatabaseRegistry implements IDatabaseRegistry
{
    /**
     * @var array<string, IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager>
     */
    private array $managers = [];

    public function registerMySql(string $key, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void
    {
        $this->managers[$key] = new DatabaseManager(
            new DatabaseConfig($host, $dbName, $user, $password, $port, $charset)
        );
    }

    public function registerProvider(string $key, string $providerType, string $host, string $dbName, string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void
    {
        $this->managers[$key] = match (true) {
            $providerType === 'mysql' || $providerType === 'mariadb'
                => new DatabaseManager(new DatabaseConfig($host, $dbName, $user, $password, $port, $charset)),
            $providerType === 'sqlite'
                // SQLite: host param = dosya yolu; dbName/user/pass/port/charset geçersizdir.
                => new SQLiteDatabaseManager($host),
            $providerType === 'mssql'
                => new SqlsrvDatabaseManager($host, $dbName, $user, $password, $port),
            $providerType === 'mongodb'
                => new MongoDbManager($host, $dbName, $user, $password, $port),
            default
                => throw new \RuntimeException("Unsupported provider type: {$providerType} for key: {$key}"),
        };
    }

    /**
     * Get a database manager by key.
     *
     * @param string $key Database key
     * @return IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager
     * @throws \RuntimeException If database is not registered
     */
    public function get(string $key): IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager
    {
        if (!isset($this->managers[$key])) {
            throw new \RuntimeException("Database '{$key}' is not registered.");
        }

        return $this->managers[$key];
    }

    /**
     * Get a SQL database manager by key.
     *
     * @param string $key Database key
     * @return ISqlDatabaseManager
     * @throws \RuntimeException If database is not registered or is not a SQL database
     */
    public function getSql(string $key): ISqlDatabaseManager
    {
        $manager = $this->get($key);

        if (!$manager instanceof ISqlDatabaseManager) {
            throw new \RuntimeException("Database '{$key}' is not a SQL database (cannot call getSql on NoSQL database).");
        }

        return $manager;
    }

    /**
     * Get a NoSQL database manager by key.
     *
     * @param string $key Database key
     * @return INoSqlDatabaseManager
     * @throws \RuntimeException If database is not registered or is not a NoSQL database
     */
    public function getNoSql(string $key): INoSqlDatabaseManager
    {
        $manager = $this->get($key);

        if (!$manager instanceof INoSqlDatabaseManager) {
            throw new \RuntimeException("Database '{$key}' is not a NoSQL database (cannot call getNoSql on SQL database).");
        }

        return $manager;
    }

    public function has(string $key): bool
    {
        return isset($this->managers[$key]);
    }
}