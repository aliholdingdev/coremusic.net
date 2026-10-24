<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Contracts\Database\IDatabaseManager;

/**
 * DatabaseManager Ã¢â‚¬â€ tek DB baÃ„Å¸lantÃ„Â±sÃ„Â± (lazy PDO); DB baÃ…Å¸Ã„Â±na tek instance.
 *
 * KayÃ„Â±t yolu: DatabaseRegistry::registerMySql() (veya ConnectionFactory .env'den).
 * PDO ayarlarÃ„Â±: ERRMODE_EXCEPTION Ã‚Â· FETCH_ASSOC Ã‚Â· EMULATE_PREPARES=false Ã‚Â· utf8mb4 (ADR-002/022).
 *
 * EÃ…ÂZAMANLILIK (PDO + fork): PDO baÃ„Å¸lantÃ„Â±sÃ„Â± fork ile devralÃ„Â±namaz Ã¢â‚¬â€ child
 * process parent'Ã„Â±n socket'ini kullanamaz; pcntl_fork / paralel worker sonrasÃ„Â±
 * ÃƒÂ§ocuk sÃƒÂ¼reÃƒÂ§ kendi DatabaseManager/factory ÃƒÂ§aÃ„Å¸rÃ„Â±sÃ„Â±yla YENÃ„Â° baÃ„Å¸lantÃ„Â± aÃƒÂ§malÃ„Â±dÃ„Â±r.
 *
 * NOT (AdÃ„Â±m 03): execute()/write() mevcut(repository sÃƒÂ¶zleÃ…Å¸mesi) Ã¢â‚¬â€ SQL katmanÃ„Â±
 * repository'dir; baÃ„Å¸lantÃ„Â± katmanÃ„Â± yeni SQL API ÃƒÂ¼retmez.
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
     * Callable'Ã„Â± transaction iÃƒÂ§inde ÃƒÂ§alÃ„Â±Ã…Å¸tÃ„Â±rÃ„Â±r; exception'da otomatik rollback.
     *
     * - $fn PDO alÃ„Â±r; dÃƒÂ¶nÃƒÂ¼Ã…Å¸ deÃ„Å¸eri commit sonrasÃ„Â± dÃƒÂ¶ner.
     * - Zaten aktif transaction'da ise savepoint OLUÃ…ÂTURULMAZ (nested desteklenmez);
     *   callable mevcut transaction'a yazÃ„Â±lÃ„Â±r.
     * - Exception'da rollback yapÃ„Â±lÃ„Â±r ve ORÃ„Â°JÃ„Â°NAL exception rethrow edilir.
     *
     * Yeni eklenmiÃ…Å¸ behavior-preserving API Ã¢â‚¬â€ interface'e EKLENMEDÃ„Â°
     * (IDatabaseManager implementorlarÃ„Â±: test double'lar kÃ„Â±rÃ„Â±lmasÃ„Â±n).
     *
     * @throws \Throwable Callback exception'Ã„Â± Ã¢â‚¬â€ rollback sonrasÃ„Â± aynen yÃƒÂ¼kseltilir.
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
            // commit() başarısızsa transaction açık kalabilir; rollback gerekir.
            // @phpstan-ignore-next-line identical.alwaysFalse
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
