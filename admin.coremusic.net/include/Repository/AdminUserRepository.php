<?php declare(strict_types=1);

namespace CoreMusic\Admin\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Database\ConnectionFactory;

/**
 * AdminUserRepository — coremusic_auth şemasında admin_users veri erişimi.
 *
 * ADR-002: yalnız DatabaseManager üzerinden prepared statement; SELECT * yok.
 * Bağluk tembel (lazy): PDO ilk sorguda açılır — DB'siz ortamlarda (testler)
 * bağlantı denemesi yapılmaz.
 *
 * Bağlantı kaynağı TEKTTİR: shared ConnectionFactory registry (ADR-003/022) —
 * repository kendi PDO/DatabaseManager'ını KURMAZ; aynı şemaya tek connection.
 * Desen: home.coremusic.net/include/Repository/MusicRepository.php ile aynı
 * (ConnectionFactory get-or-register — db-access-guard repository kuralı).
 *
 * Taşınma kaynağı: pages/admin-dashboard.php (2026-10-24 — prezentasyonda
 * DB kodu yasak; db-access-guard admin.coremusic.net kapsamına alındı).
 */
final class AdminUserRepository
{
    private ?IDatabaseManager $db = null;

    private function __construct(
        private readonly string $dbHost,
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPassword,
        private readonly int $dbPort,
        private readonly string $dbCharset,
    ) {
    }

    /** Ortam değişkenlerinden (defined/ENV) repository kurar — bağlantı henüz açılmaz. */
    public static function fromEnvironment(): self
    {
        $name = defined('DB_ADMIN_NAME')
            ? (string)DB_ADMIN_NAME
            : (string)(($_ENV['DB_ADMIN_NAME'] ?? getenv('DB_ADMIN_NAME')) ?: 'coremusic_auth');

        $host = defined('DB_HOST') ? (string)DB_HOST : (string)(($_ENV['DB_HOST'] ?? 'localhost') ?: 'localhost');
        $user = defined('DB_USER') ? (string)DB_USER : (string)(($_ENV['DB_USER'] ?? '') ?: '');
        $pass = defined('DB_PASSWORD') ? (string)DB_PASSWORD : (string)(($_ENV['DB_PASSWORD'] ?? '') ?: '');
        $port = defined('DB_PORT') ? (int)DB_PORT : (int)(($_ENV['DB_PORT'] ?? 3306) ?: 3306);
        $cs   = defined('DB_CHARSET') ? (string)DB_CHARSET : (string)(($_ENV['DB_CHARSET'] ?? '') ?: 'utf8mb4');

        return new self($host, $name, $user, $pass, $port, $cs);
    }

    /**
     * Kayıtlı yönetici sayısı (soft-delete hariç — is_deleted = 0).
     *
     * @return int|null Sayı, veya null = boş kimlik bilgisi (DB'siz/test ortamı
     *                  — bağlantı DENEMEZ, AdminMarkupContractTest sözleşmesi:
     *                  doğrulanmamış sayı üretilmez, sayfa boş-duruma düşer).
     * @throws \Throwable bağlantı/sorgu hatası (çağıran \Throwable yakalar).
     */
    public function countActiveAdmins(): ?int
    {
        if ($this->dbUser === '' || $this->dbPassword === '') {
            return null;
        }

        $rows = $this->db()->execute('SELECT COUNT(*) AS c FROM admin_users WHERE is_deleted = 0');

        return isset($rows[0]['c']) ? (int)$rows[0]['c'] : null;
    }

    /**
     * Tembel bağlantı: ilk sorguda shared ConnectionFactory registry üzerinden
     * DatabaseManager çözülür (kendi bağlantımızı KURMAYIZ — tek bağlantı kaynağı).
     */
    private function db(): IDatabaseManager
    {
        if ($this->db === null) {
            $factory = ConnectionFactory::getInstance();
            $key     = 'admin_' . $this->dbName;
            if (!$factory->registry()->has($key)) {
                $factory->registry()->registerMySql(
                    $key,
                    $this->dbHost,
                    $this->dbName,
                    $this->dbUser,
                    $this->dbPassword,
                    $this->dbPort,
                    $this->dbCharset,
                );
            }
            $this->db = $factory->registry()->get($key);
        }

        return $this->db;
    }
}
