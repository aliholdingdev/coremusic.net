<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Config\EnvParser;
use CoreMusic\Log\LoggerFactory;

/**
 * ConnectionFactory Ã¢â‚¬â€ .env tabanlÃ„Â±, DB baÃ…Å¸Ã„Â±na TEK PDO baÃ„Å¸lantÃ„Â±sÃ„Â± (singleton).
 *
 * Mevcut katman ÃƒÂ¼zerine kurulur (duplicate factory DEÃ„ÂÃ„Â°L):
 *  DatabaseRegistry (key Ã¢â€ â€™ DatabaseManager map) + DatabaseManager (lazy PDO).
 *  Bu sÃ„Â±nÃ„Â±f yalnÃ„Â±zca .env okuma + per-DB lazy kayÃ„Â±t + PDO eriÃ…Å¸imi saÃ„Å¸lar;
 *  Ã„Â°Ã…ÂLETMEZ (ADR-002: bu katman yalnÃ„Â±z baÃ„Å¸lantÃ„Â± saÃ„Å¸lar Ã¢â‚¬â€ SQL katmanÃ„Â± repository).
 *
 * Config kaynaÃ„Å¸Ã„Â± (AdÃ„Â±m 03):
 *  shared/config/.env  (Ã…Å¸ablon: shared/config/.env.example)
 *   DB_HOST Ã‚Â· DB_PORT Ã‚Â· DB_USER Ã‚Â· DB_PASSWORD Ã‚Â· DB_CHARSET   Ã¢â‚¬â€ global
 *   DB_{KEY}_NAME  Ã¢â‚¬â€ DB baÃ…Å¸Ã„Â±na Ã…Å¸ema adÃ„Â±, KEY = strtoupper($db)
 *   Ãƒâ€“rn: get('auth') Ã¢â€ â€™ DB_AUTH_NAME=coremusic_auth
 *  Disk kanÃ„Â±tÃ„Â±: auth.coremusic.net/config/constants.php (DB_AUTH_NAME) Ã‚Â·
 *  api.coremusic.net/include/Container/ApiContainer.php (DB_MUSIC_NAME,
 *  DB_PLAYLIST_NAME, DB_USER_NAME, DB_SYSTEM_NAME).
 *  EnvParser::loadIntoEnv() $_ENV'e yazar (constants.php ile aynÃ„Â± pattern);
 *  .env dosyasÃ„Â± yoksa no-op Ã¢â€ â€™ mevcut ortam deÃ„Å¸iÃ…Å¸kenleri (Apache/IIS/env) geÃƒÂ§erli.
 *
 * Singleton: ConnectionFactory::getInstance() sÃƒÂ¼reÃƒÂ§ baÃ…Å¸Ã„Â±na tek factory;
 *  aynÃ„Â± $db anahtarÃ„Â±na ikinci get() ÃƒÂ§aÃ„Å¸rÃ„Â±sÃ„Â± YENÃ„Â° baÃ„Å¸lantÃ„Â± AÃƒâ€¡MAZ
 *  (PDO DatabaseManager::$pdo iÃƒÂ§inde cache'lidir).
 *
 * EÃ…ÂZAMANLILIK (PDO + fork): PDO baÃ„Å¸lantÃ„Â±larÃ„Â± fork ile devralÃ„Â±namaz Ã¢â‚¬â€ child
 * process parent'Ã„Â±n socket'ini kullanamaz; pcntl_fork / paralel worker sonrasÃ„Â±
 * ÃƒÂ§ocuk sÃƒÂ¼reÃƒÂ§ YENÃ„Â° baÃ„Å¸lantÃ„Â± aÃƒÂ§malÃ„Â±dÃ„Â±r (factory yeniden ÃƒÂ§aÃ„Å¸rÃ„Â±labilir; statik
 * $instance child'da kopyalanÃ„Â±r ve DatabaseManager'daki PDO TAZELENMELÃ„Â°DÃ„Â°R).
 *
 * Hata yÃƒÂ¶netimi: PDOException Ã¢â€ â€™ DatabaseException::connectionFailed()
 *  (DSN/parola mesajda [REDACTED]; orijinal PDOException $previous; log
 *  FileHandler Ã¢â€ â€™ Redactor). Fail-safe: yapÃ„Â±landÃ„Â±rma eksikse baÃ„Å¸lantÃ„Â± AÃƒâ€¡ILMAZ.
 */
final class ConnectionFactory
{
    private static ?self $instance = null;

    private readonly DatabaseRegistry $registry;
    private readonly string $envFile;

    public function __construct(?string $envFile = null)
    {
        $this->envFile = $envFile ?? dirname(__DIR__, 2) . '/config/.env';
        $this->registry = new DatabaseRegistry();
        // Dosya yoksa EnvParser no-op (is_file kontrolÃƒÂ¼ EnvParser::load iÃƒÂ§inde).
        EnvParser::loadIntoEnv($this->envFile);
    }

    /**
     * SÃƒÂ¼reÃƒÂ§-baÃ…Å¸Ã„Â±na tek factory. $envFile yalnÃ„Â±z ilk ÃƒÂ§aÃ„Å¸rÃ„Â±da dikkate alÃ„Â±nÃ„Â±r.
     */
    public static function getInstance(?string $envFile = null): self
    {
        return self::$instance ??= new self($envFile);
    }

    /**
     * Test/singleton reset Ã¢â‚¬â€ LoggerFactory::reset() ile aynÃ„Â± pattern.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * DB iÃƒÂ§in PDO baÃ„Å¸lantÃ„Â±sÃ„Â± Ã¢â‚¬â€ aynÃ„Â± $db'de tekrar baÃ„Å¸lantÃ„Â± AÃƒâ€¡ILMAZ.
     *
     * @throws DatabaseException BaÃ„Å¸lantÃ„Â± hatasÃ„Â± veya eksik yapÃ„Â±landÃ„Â±rma.
     */
    public function get(string $db): \PDO
    {
        try {
            return $this->manager($db)->getPdo();
        } catch (DatabaseException $e) {
            throw $e; // yapÃ„Â±landÃ„Â±rma/tutarlÃ„Â±lÃ„Â±k hatasÃ„Â± Ã¢â‚¬â€ zaten kontrollÃƒÂ¼
        } catch (\PDOException $e) {
            $this->logConnectionFailure($db, $e);
            throw DatabaseException::connectionFailed($db, $e);
        }
    }

    /**
     * DB kaydÃ„Â± factory altÃ„Â±nda aÃƒÂ§Ã„Â±lmÃ„Â±Ã…Å¸ mÃ„Â± (baÃ„Å¸lantÃ„Â± KURULMADAN kontrol).
     */
    public function has(string $db): bool
    {
        return $this->registry->has($db);
    }

    /**
     * AltÃ„Â±ndaki registry (repository wiring iÃƒÂ§in).
     */
    public function registry(): DatabaseRegistry
    {
        return $this->registry;
    }

    /**
     * Callable'Ã„Â± transaction iÃƒÂ§inde ÃƒÂ§alÃ„Â±Ã…Å¸tÃ„Â±rÃ„Â±r; exception'da otomatik rollback.
     *
     *  - $fn PDO alÃ„Â±r; dÃƒÂ¶nÃƒÂ¼Ã…Å¸ deÃ„Å¸eri commit sonrasÃ„Â± dÃƒÂ¶ner.
     *  - Zaten aktif transaction'da ise savepoint OLUÃ…ÂTURULMAZ (nested desteklenmez);
     *    callable mevcut transaction'a yazÃ„Â±lÃ„Â±r.
     *  - Exception'da rollback yapÃ„Â±lÃ„Â±r ve ORÃ„Â°JÃ„Â°NAL exception rethrow edilir
     *    (wrapper'a ÃƒÂ§evrilmez Ã¢â‚¬â€ ÃƒÂ§aÃ„Å¸rÃ„Â±cÃ„Â± tip yakalama davranÃ„Â±Ã…Å¸Ã„Â± korunur).
     *
     * @throws \Throwable Callback exception'Ã„Â± Ã¢â‚¬â€ rollback sonrasÃ„Â± aynen yÃƒÂ¼kseltilir.
     */
    public function runInTransaction(string $db, callable $fn): mixed
    {
        $pdo = $this->get($db);

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

    /**
     * DB iÃƒÂ§in manager dÃƒÂ¶ndÃƒÂ¼rÃƒÂ¼r; ilk eriÃ…Å¸imde .env'den yapÃ„Â±landÃ„Â±rÃ„Â±p kaydeder.
     */
    private function manager(string $db): DatabaseManager
    {
        if (!$this->registry->has($db)) {
            $this->registry->registerMySql(
                $db,
                $this->env('DB_HOST', 'localhost'),
                $this->schemaName($db),
                $this->env('DB_USER', ''),
                $this->env('DB_PASSWORD', ''),
                (int) $this->env('DB_PORT', '3306'),
                $this->env('DB_CHARSET', 'utf8mb4'),
            );
        }

        $manager = $this->registry->get($db);
        if (!$manager instanceof DatabaseManager) {
            throw DatabaseException::unexpectedManager($db, $manager::class);
        }

        return $manager;
    }

    /**
     * Ã…Âema adÃ„Â±: DB_{KEY}_NAME Ã¢â‚¬â€ eksikse fail-safe exception (boÃ…Å¸ string devam YOK).
     */
    private function schemaName(string $db): string
    {
        $envKey = 'DB_' . strtoupper($db) . '_NAME';
        $dbName = $this->env($envKey, '');
        if ($dbName === '') {
            throw DatabaseException::configMissing($db, $envKey);
        }
        return $dbName;
    }

    /**
     * Ortam okuma: $_ENV ÃƒÂ¶ncelikli, getenv fallback, sonra default.
     * Not: DB_PASSWORD boÃ…Å¸ string meÃ…Å¸rudur ('' != eksik Ã¢â‚¬â€ getenv false dÃƒÂ¶nerse default).
     */
    private function env(string $key, string $default): string
    {
        if (isset($_ENV[$key])) {
            return (string) $_ENV[$key];
        }
        $value = getenv($key);
        return $value !== false ? (string) $value : $default;
    }

    /**
     * BaÃ„Å¸lantÃ„Â± hatasÃ„Â± logu Ã¢â‚¬â€ mesaj Redactor'dan geÃƒÂ§er (FileHandler garantisi);
     * exception mesajÃ„Â± zaten DSN/parola iÃƒÂ§ermez.
     */
    private function logConnectionFailure(string $db, \PDOException $e): void
    {
        LoggerFactory::getInstance()->error(
            "ConnectionFactory: DB baÃ„Å¸lantÃ„Â± hatasÃ„Â±: '{$db}'",
            ['exception' => $e::class . ': ' . $e->getMessage()]
        );
    }
}
