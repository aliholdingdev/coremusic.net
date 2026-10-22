<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Config\EnvParser;
use CoreMusic\Log\LoggerFactory;

/**
 * ConnectionFactory — .env tabanlı, DB başına TEK PDO bağlantısı (singleton).
 *
 * Mevcut katman üzerine kurulur (duplicate factory DEĞİL):
 *  DatabaseRegistry (key → DatabaseManager map) + DatabaseManager (lazy PDO).
 *  Bu sınıf yalnızca .env okuma + per-DB lazy kayıt + PDO erişimi sağlar;
 *  İŞLETMEZ (ADR-002: bu katman yalnız bağlantı sağlar — SQL katmanı repository).
 *
 * Config kaynağı (Adım 03):
 *  shared/config/.env  (şablon: shared/config/.env.example)
 *   DB_HOST · DB_PORT · DB_USER · DB_PASSWORD · DB_CHARSET   — global
 *   DB_{KEY}_NAME  — DB başına şema adı, KEY = strtoupper($db)
 *   Örn: get('auth') → DB_AUTH_NAME=coremusic_auth
 *  Disk kanıtı: auth.coremusic.net/config/constants.php (DB_AUTH_NAME) ·
 *  api.coremusic.net/include/Container/ApiContainer.php (DB_MUSIC_NAME,
 *  DB_PLAYLIST_NAME, DB_USER_NAME, DB_SYSTEM_NAME).
 *  EnvParser::loadIntoEnv() $_ENV'e yazar (constants.php ile aynı pattern);
 *  .env dosyası yoksa no-op → mevcut ortam değişkenleri (Apache/IIS/env) geçerli.
 *
 * Singleton: ConnectionFactory::getInstance() süreç başına tek factory;
 *  aynı $db anahtarına ikinci get() çağrısı YENİ bağlantı AÇMAZ
 *  (PDO DatabaseManager::$pdo içinde cache'lidir).
 *
 * EŞZAMANLILIK (PDO + fork): PDO bağlantıları fork ile devralınamaz — child
 * process parent'ın socket'ini kullanamaz; pcntl_fork / paralel worker sonrası
 * çocuk süreç YENİ bağlantı açmalıdır (factory yeniden çağrılabilir; statik
 * $instance child'da kopyalanır ve DatabaseManager'daki PDO TAZELENMELİDİR).
 *
 * Hata yönetimi: PDOException → DatabaseException::connectionFailed()
 *  (DSN/parola mesajda [REDACTED]; orijinal PDOException $previous; log
 *  FileHandler → Redactor). Fail-safe: yapılandırma eksikse bağlantı AÇILMAZ.
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
        // Dosya yoksa EnvParser no-op (is_file kontrolü EnvParser::load içinde).
        EnvParser::loadIntoEnv($this->envFile);
    }

    /**
     * Süreç-başına tek factory. $envFile yalnız ilk çağrıda dikkate alınır.
     */
    public static function getInstance(?string $envFile = null): self
    {
        return self::$instance ??= new self($envFile);
    }

    /**
     * Test/singleton reset — LoggerFactory::reset() ile aynı pattern.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * DB için PDO bağlantısı — aynı $db'de tekrar bağlantı AÇILMAZ.
     *
     * @throws DatabaseException Bağlantı hatası veya eksik yapılandırma.
     */
    public function get(string $db): \PDO
    {
        try {
            return $this->manager($db)->getPdo();
        } catch (DatabaseException $e) {
            throw $e; // yapılandırma/tutarlılık hatası — zaten kontrollü
        } catch (\PDOException $e) {
            $this->logConnectionFailure($db, $e);
            throw DatabaseException::connectionFailed($db, $e);
        }
    }

    /**
     * DB kaydı factory altında açılmış mı (bağlantı KURULMADAN kontrol).
     */
    public function has(string $db): bool
    {
        return $this->registry->has($db);
    }

    /**
     * Altındaki registry (repository wiring için).
     */
    public function registry(): DatabaseRegistry
    {
        return $this->registry;
    }

    /**
     * Callable'ı transaction içinde çalıştırır; exception'da otomatik rollback.
     *
     *  - $fn PDO alır; dönüş değeri commit sonrası döner.
     *  - Zaten aktif transaction'da ise savepoint OLUŞTURULMAZ (nested desteklenmez);
     *    callable mevcut transaction'a yazılır.
     *  - Exception'da rollback yapılır ve ORİJİNAL exception rethrow edilir
     *    (wrapper'a çevrilmez — çağrıcı tip yakalama davranışı korunur).
     *
     * @throws \Throwable Callback exception'ı — rollback sonrası aynen yükseltilir.
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
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * DB için manager döndürür; ilk erişimde .env'den yapılandırıp kaydeder.
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
     * Şema adı: DB_{KEY}_NAME — eksikse fail-safe exception (boş string devam YOK).
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
     * Ortam okuma: $_ENV öncelikli, getenv fallback, sonra default.
     * Not: DB_PASSWORD boş string meşrudur ('' != eksik — getenv false dönerse default).
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
     * Bağlantı hatası logu — mesaj Redactor'dan geçer (FileHandler garantisi);
     * exception mesajı zaten DSN/parola içermez.
     */
    private function logConnectionFailure(string $db, \PDOException $e): void
    {
        LoggerFactory::getInstance()->error(
            "ConnectionFactory: DB bağlantı hatası: '{$db}'",
            ['exception' => $e::class . ': ' . $e->getMessage()]
        );
    }
}
