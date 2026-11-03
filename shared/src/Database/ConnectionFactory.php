<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Config\EnvParser;
use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\ISqlDatabaseManager;
use CoreMusic\Contracts\Database\INoSqlDatabaseManager;
use CoreMusic\Log\LoggerFactory;

/**
 * ConnectionFactory — .env tabanlı, DB başına TEK bağlantı (singleton).
 *
 * Mevcut katman üzerine kurulur (duplicate factory DEĞİL):
 *  DatabaseRegistry (key → manager map) + sürücü manager'ları (lazy bağlantı).
 *  Bu sınıf yalnızca .env okuma + per-DB lazy kayıt + PDO erişimi sağlar;
 *  İŞLETMEZ (ADR-002: bu katman yalnız bağlantı sağlar — SQL katmanı repository).
 *
 * Config kaynağı (Adım 03):
 *  shared/config/.env  (şablon: shared/config/.env.example)
 *   DB_PROVIDER (global) · DB_PROVIDER_{KEY} (KEY bazlı override)
 *   DB_HOST · DB_PORT · DB_USER · DB_PASSWORD · DB_CHARSET — global
 *   DB_{KEY}_HOST · DB_{KEY}_PORT · DB_{KEY}_USER · DB_{KEY}_PASSWORD — KEY bazlı override
 *   (canlı smoke 2026-10-30: aynı key 'smoke' hem mysql hem mssql kullanıyor —
 *    per-key kimlik/host ayrıştırması zorunlu)
 *   DB_{KEY}_NAME — DB başına şema adı, KEY = strtoupper($db)
 *   Örn: get('auth') → DB_AUTH_NAME=coremusic_auth
 *  sqlite'ta host parametresi DOSYA YOLUDUR: DB_{KEY}_PATH → DB_HOST → fail-safe.
 *  Disk kanıtı: auth.coremusic.net/config/constants.php (DB_AUTH_NAME) ·
 *  api.coremusic.net/include/Container/ApiContainerBase.php (DB_MUSIC_NAME L184,
 *  DB_PLAYLIST_NAME L203, DB_USER_NAME L222, DB_SYSTEM_NAME L241 — eski
 *  ApiContainer.php 2026-10-30'da silindi, anahtarlar Base'e taşındı).
 *  EnvParser::loadIntoEnv() $_ENV'e yazar (constants.php ile aynı pattern);
 *  .env dosyası yoksa no-op → mevcut ortam değişkenleri (Apache/IIS/env) geçerli.
 *
 * Singleton: ConnectionFactory::getInstance() süreç başına tek factory;
 *  aynı $db anahtarına ikinci get() çağrısı YENİ bağlantı AÇMAZ
 *  (PDO manager::$pdo içinde cache'lidir).
 *
 * EŞZAMANLILIK (PDO + fork): PDO bağlantıları fork ile devralınamaz — child
 * process parent'ın socket'ini kullanamaz; çocuk süreç YENİ bağlantı açmalıdır
 * (factory yeniden çağrılabilir; statik $instance child'da kopyalanır ve
 * DatabaseManager'daki PDO TAZELENMELİDİR).
 *
 * Hata yönetimi: PDOException → DatabaseException::connectionFailed()
 *  (DSN/parola mesajda [REDACTED]; orijinal PDOException $previous; log
 *  FileHandler → Redactor). LogicException (PDO-dışı sürücü) →
 *  DatabaseException::unsupportedDriver(). Fail-safe: yapılandırma eksikse
 *  bağlantı AÇILMAZ.
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
     * NOTE: Only works for SQL databases (MySQL, SQLite, MSSQL) that implement ISqlDatabaseManager.
     * For MongoDB/NoSQL databases, use getSqlManager() or getNoSqlManager() from registry directly.
     *
     * @throws DatabaseException Bağlantı hatası, eksik yapılandırma veya PDO-dışı sürücü.
     */
    public function get(string $db): \PDO
    {
        try {
            return $this->getSqlManager($db)->getPdo();
        } catch (DatabaseException $e) {
            throw $e; // yapılandırma/tutarlılık hatası — zaten kontrollü
        } catch (\PDOException $e) {
            $this->logConnectionFailure($db, $e);
            throw DatabaseException::connectionFailed($db, $e);
        }
    }

    /**
     * Get a SQL database manager by key (for repositories that need PDO access).
     * Auto-registers the database if not already registered.
     *
     * @param string $db Database key
     * @return ISqlDatabaseManager
     * @throws DatabaseException If database is not registered or is not a SQL database
     */
    public function getSqlManager(string $db): ISqlDatabaseManager
    {
        if (!$this->registry->has($db)) {
            $this->registerDatabase($db);
        }
        return $this->registry->getSql($db);
    }

    /**
     * Get a NoSQL database manager by key (for MongoDB).
     * Auto-registers the database if not already registered.
     *
     * @param string $db Database key
     * @return INoSqlDatabaseManager
     * @throws DatabaseException If database is not registered or is not a NoSQL database
     */
    public function getNoSqlManager(string $db): INoSqlDatabaseManager
    {
        if (!$this->registry->has($db)) {
            $this->registerDatabase($db);
        }
        return $this->registry->getNoSql($db);
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
     * get-or-register: mevcut kaydı OKUR, yoksa provider/parametrelerle kaydeder.
     *
     * db-access-guard repository kuralı (onay 2026-10-24): repository yeni
     * bağlantı KURMAZ — mevcut registry kaydı okunur veya tek kayıt kaynağı
     * üzerinden (bu metot, DB_PROVIDER uyumlu registerProvider ile) kurulur.
     *
     * @return IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager key bazlı cache'li manager (tek instance).
     */
    public function getOrRegister(
        string $key,
        string $provider,
        string $host,
        string $dbName,
        string $user,
        string $password,
        int $port = 3306,
        string $charset = 'utf8mb4',
    ): IDatabaseManager|ISqlDatabaseManager|INoSqlDatabaseManager {
        if ($this->registry->has($key)) {
            return $this->registry->get($key);
        }

        $this->registry->registerProvider($key, $provider, $host, $dbName, $user, $password, $port, $charset);

        return $this->registry->get($key);
    }

    /**
     * Port çözümü: DB_{KEY}_PORT → DB_PORT → '3306'.
     *
     * KEY = strtoupper($db). Provider başına port AYRIŞTIRMAK zorunludur:
     * aynı host'ta mysql (3306) ve mssql (1433) birlikte yaşarken tek global
     * port her ikisini de yanlış kapıya götürür (canlı smoke bulgusu 2026-10-30:
     * sqlsrv → MySQL portuna bağlanıp "Protocol error in TDS stream" veriyordu).
     * sqlite'ta port anlamsızdır (host = dosya yolu).
     */
    public function portFor(string $db): int
    {
        $envKey = 'DB_' . strtoupper($db) . '_PORT';
        $port = $this->env($envKey, '');
        if ($port === '') {
            $port = $this->env('DB_PORT', '3306');
        }
        return (int) $port;
    }

    /**
     * Provider çözümü: DB_PROVIDER_<KEY> → DB_PROVIDER → 'mysql'.
     * KEY = strtoupper($db). Repository yüzeyi de bunu çağırır (tek karar noktası).
     */
    public function providerFor(string $db): string
    {
        $envKey = 'DB_PROVIDER_' . strtoupper($db);
        if (isset($_ENV[$envKey])) {
            return (string) $_ENV[$envKey];
        }
        $value = getenv($envKey);
        if ($value !== false) {
            return (string) $value;
        }

        return $this->env('DB_PROVIDER', 'mysql');
    }

    /**
     * Callable'ı transaction içinde çalıştırır; exception'da otomatik rollback.
     *
     *  - $fn PDO alır; dönüş değeri commit sonrası döner.
     *  - Zaten aktif transaction'da ise savepoint OLUŞTURULMAZ (nested desteklenmez);
     *    callable mevcut transaction'a yazılır.
     *  - Exception'da rollback yapılır ve ORİJİNAL exception rethrow edilir
     *    (wrapper'a çevrilmez — çağırıcı tip yakalama davranışı korunur).
     *
     * @throws \Throwable Callback exception'ı — rollback sonrası aynen yükseltilir.
     */
    public function runInTransaction(string $db, callable $fn): mixed
    {
        $manager = $this->getSqlManager($db);
        $pdo = $manager->getPdo();

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
            $this->rollBackIfActive($pdo);
            throw $e;
        }
    }

    /**
     * Auto-register a database based on provider configuration.
     */
    private function registerDatabase(string $db): void
    {
        $provider = $this->providerFor($db);

        $this->registry->registerProvider(
            $db,
            $provider,
            $this->hostFor($db, $provider),
            $this->schemaName($db),
            $this->userFor($db),
            $this->passwordFor($db),
            $this->portFor($db),
            $this->env('DB_CHARSET', 'utf8mb4'),
        );
    }

    /**
     * Host çözümü: provider sqlite ise "host" DOSYA YOLUDUR —
     * DB_{KEY}_PATH → DB_HOST sırasıyla okunur; ikisi de boşsa fail-safe
     * configMissing (sqlite'a boş yol ile devam ETMEZ).
     *
     * Non-sqlite: DB_{KEY}_HOST → DB_HOST → 'localhost'
     * (canlı smoke 2026-10-30: aynı key 'smoke' hem mysql hem mssql kullanıyor —
     * per-key kimlik/host ayrıştırması zorunlu; yalnız global host mssql'yi
     * yanlış sunucuya yönlendiriyordu). KEY = strtoupper($db) (portFor ile aynı).
     */
    public function hostFor(string $db, string $provider): string
    {
        if ($provider === 'sqlite') {
            $pathKey = 'DB_' . strtoupper($db) . '_PATH';
            $path    = $this->env($pathKey, '');
            if ($path === '') {
                $path = $this->env('DB_HOST', '');
            }
            if ($path === '') {
                throw DatabaseException::configMissing($db, $pathKey);
            }
            return $path;
        }

        $host = $this->env('DB_' . strtoupper($db) . '_HOST', '');
        if ($host === '') {
            $host = $this->env('DB_HOST', '');
        }
        if ($host === '') {
            $host = 'localhost';
        }
        return $host;
    }

    /**
     * Kullanıcı çözümü: DB_{KEY}_USER → DB_USER → ''.
     *
     * KEY = strtoupper($db) (portFor ile aynı). Varlık kontrolü env() notuyla
     * aynıdır: boş string meşrudur ('' != eksik — yalnız anahtar YOKSA
     * global'e düşülür, providerFor ile aynı desen).
     * (canlı smoke 2026-10-30: aynı key 'smoke' hem mysql hem mssql kullanıyor —
     * per-key kimlik/host ayrıştırması zorunlu; global kimlik mssql'ye
     * "Login failed" verdiriyordu.)
     */
    public function userFor(string $db): string
    {
        $envKey = 'DB_' . strtoupper($db) . '_USER';
        if (isset($_ENV[$envKey])) {
            return (string) $_ENV[$envKey];
        }
        $value = getenv($envKey);
        if ($value !== false) {
            return (string) $value;
        }
        return $this->env('DB_USER', '');
    }

    /**
     * Parola çözümü: DB_{KEY}_PASSWORD → DB_PASSWORD → ''.
     *
     * KEY = strtoupper($db) (portFor ile aynı). Not: boş string meşru bir
     * değerdir (env() docblock notu ile aynı — '' != eksik; yalnız anahtar
     * YOKSA global'e düşülür, providerFor ile aynı desen).
     * (canlı smoke 2026-10-30: aynı key 'smoke' hem mysql hem mssql kullanıyor —
     * per-key kimlik/host ayrıştırması zorunlu.)
     */
    public function passwordFor(string $db): string
    {
        $envKey = 'DB_' . strtoupper($db) . '_PASSWORD';
        if (isset($_ENV[$envKey])) {
            return (string) $_ENV[$envKey];
        }
        $value = getenv($envKey);
        if ($value !== false) {
            return (string) $value;
        }
        return $this->env('DB_PASSWORD', '');
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
     * commit() başarısız olduğunda transaction açık kalmış olabilir; yalnız aktifse rollback yapar.
     */
    private function rollBackIfActive(\PDO $pdo): void
    {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
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