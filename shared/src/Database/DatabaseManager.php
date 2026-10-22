<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Contracts\Database\IDatabaseManager;

/**
 * DatabaseManager — tek DB bağlantısı (lazy PDO); DB başına tek instance.
 *
 * Kayıt yolu: DatabaseRegistry::registerMySql() (veya ConnectionFactory .env'den).
 * PDO ayarları: ERRMODE_EXCEPTION · FETCH_ASSOC · EMULATE_PREPARES=false · utf8mb4 (ADR-002/022).
 *
 * EŞZAMANLILIK (PDO + fork): PDO bağlantısı fork ile devralınamaz — child
 * process parent'ın socket'ini kullanamaz; pcntl_fork / paralel worker sonrası
 * çocuk süreç kendi DatabaseManager/factory çağrısıyla YENİ bağlantı açmalıdır.
 *
 * NOT (Adım 03): execute()/write() mevcut(repository sözleşmesi) — SQL katmanı
 * repository'dir; bağlantı katmanı yeni SQL API üretmez.
 */
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

    /**
     * Callable'ı transaction içinde çalıştırır; exception'da otomatik rollback.
     *
     * - $fn PDO alır; dönüş değeri commit sonrası döner.
     * - Zaten aktif transaction'da ise savepoint OLUŞTURULMAZ (nested desteklenmez);
     *   callable mevcut transaction'a yazılır.
     * - Exception'da rollback yapılır ve ORİJİNAL exception rethrow edilir.
     *
     * Yeni eklenmiş behavior-preserving API — interface'e EKLENMEDİ
     * (IDatabaseManager implementorları: test double'lar kırılmasın).
     *
     * @throws \Throwable Callback exception'ı — rollback sonrası aynen yükseltilir.
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
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
