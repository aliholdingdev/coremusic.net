#!/usr/bin/env php
<?php declare(strict_types=1);

/**
 * API Key provisioning CLI — ADR-020 §2.1 (cm_live_<prefix>_<random>) / §2.2-1.
 *
 * KULLANIM
 *   php bin/api-key-create.php --name="ci-runner" --type=service --scopes=api.access,auth.read
 *   php bin/api-key-create.php --name="my-key" --user=<32-hex> --ttl=90d --ip=10.0.0.5
 *
 * GİRDİ
 *   --type    user|service|server   (varsayılan: user)
 *   --name    <metin>               (ZORUNLU)
 *   --user    <32 hex UUID>         (service/server için verilmezse DB'de
 *                                    role_name='service' olan kullanıcı aranır)
 *   --scopes  virgülle ayrılmış     (varsayılan: api.access)
 *   --ip      virgülle ayrılmış IP allowlist (boş = kısıt yok)
 *   --ttl     900s | 30m | 12h | 30d | 7w | <saniye>  (yok = süresiz)
 *   --sqlite  <yol>  dosya tabanlı SQLite (offline/CI; MySQL yerine)
 *   --help
 *
 * ÇIKTI
 *   Ham anahtar EKRANA TAM BİR KEZ basılır ve bir daha gösterilmez;
 *   DB'ye yalnız SHA-256 hash + key_prefix yazılır. Loglara yalnız
 *   key_prefix düşer (ADR-020 §2.2-1).
 *
 * EXIT
 *   0 = başarılı · 1 = çalışma zamanı / DB hatası · 2 = kullanım hatası
 *
 * GÜVENLİK / GUARDRAIL
 *   - `.env` dosyası ASLA okunmaz/yazılmaz. MySQL bilgisi yalnızca ortam
 *     değişkenlerinden (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, DB_PORT,
 *     DB_CHARSET) alınır.
 *   - Şema sorgusu yapılmaz; tablo yapısı ADR-020 / canlı migration'a göredir.
 *   - SELECT * yok, ORM yok, PDO prepared statement (ADR-002).
 *
 * NOT (Windows): NTFS `:` karakterine dosya adında izin vermez; brief'teki
 *   `bin/api-key:create` komut adı `bin/api-key-create.php` olarak
 *   uygulanmıştır (komut sözleşmesi aynıdır).
 */

namespace {
    require_once dirname(__DIR__) . '/shared/vendor/autoload.php';
}

namespace CoreMusic\Console {

    use CoreMusic\Contracts\Database\IDatabaseManager;
    use CoreMusic\Contracts\Database\IDatabaseRegistry;
    use CoreMusic\Database\DatabaseRegistry;
    use CoreMusic\Repository\ApiKeyRepository;
    use CoreMusic\Security\UuidV7;

    /** Kullanım hatası — exit 2. */
    final class UsageError extends \InvalidArgumentException {}

    /**
     * Dosya tabanlı SQLite registry + manager (offline uçtan test / CI).
     *
     * Aynı nesne hem IDatabaseManager hem IDatabaseRegistry'dir: CLI tek
     * kayıt (`auth`) üzerinden ApiKeyRepository'ye servis eder.
     *
     * Şema, `coremusic_auth.api_keys` canlı şemasının birebir aynısıdır
     * (BINARY(16) → TEXT olarak saklanır; repository bunu UuidV7 ile çözer).
     */
    final class SqliteFileGateway implements IDatabaseManager, IDatabaseRegistry
    {
        private \PDO $pdo;

        public function __construct(string $path)
        {
            $dir = dirname($path);
            if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException("Dizin oluşturulamadı: {$dir}");
            }

            $this->pdo = new \PDO('sqlite:' . $path);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->pdo->exec('PRAGMA journal_mode = DELETE');

            $this->ensureSchema();
        }

        private function ensureSchema(): void
        {
            $this->pdo->exec(<<<'SQL'
                CREATE TABLE IF NOT EXISTS api_keys (
                    id            TEXT PRIMARY KEY,
                    user_id       TEXT NOT NULL,
                    key_hash      TEXT NOT NULL UNIQUE,
                    key_prefix    TEXT NOT NULL,
                    key_name      TEXT,
                    scopes        TEXT,
                    key_type      TEXT NOT NULL DEFAULT 'user',
                    rate_limit    INTEGER DEFAULT 1000,
                    is_active     INTEGER DEFAULT 1,
                    last_used_at  TEXT,
                    expires_at    TEXT,
                    created_at    TEXT,
                    updated_at    TEXT,
                    is_deleted    INTEGER DEFAULT 0,
                    deleted_at    TEXT
                );
                SQL);
            $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_apikeys_type   ON api_keys(key_type)');
            $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_apikeys_active ON api_keys(is_active)');
            $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_apikeys_hash   ON api_keys(key_hash)');
            $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_apikeys_prefix ON api_keys(key_prefix)');
            $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_apikeys_user   ON api_keys(user_id)');
        }

        /* --- IDatabaseManager --- */

        public function execute(string $sql, array $params = []): array
        {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        }

        public function write(string $sql, array $params = []): bool
        {
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute($params);
        }

        public function lastInsertId(): string
        {
            return (string) $this->pdo->lastInsertId();
        }

        public function beginTransaction(): bool
        {
            return $this->pdo->beginTransaction();
        }

        public function commit(): bool
        {
            return $this->pdo->commit();
        }

        public function rollBack(): bool
        {
            return $this->pdo->rollBack();
        }

        /* --- IDatabaseRegistry --- */

        public function registerMySql(
            string $key,
            string $host,
            string $dbName,
            string $user,
            string $password,
            int $port = 3306,
            string $charset = 'utf8mb4'
        ): void {
            // SQLite gateway — MySQL kaydı yok.
        }

        public function get(string $key): IDatabaseManager
        {
            return $this;
        }

        public function has(string $key): bool
        {
            return true;
        }
    }

    /**
     * `api-key:create` komutu.
     */
    final class ApiKeyCreateCommand
    {
        public const EXIT_OK    = 0;
        public const EXIT_ERROR = 1;
        public const EXIT_USAGE = 2;

        public const KEY_TYPES = ['user', 'service', 'server'];

        private const ALLOWED_OPTIONS = ['help', 'type', 'user', 'name', 'scopes', 'ip', 'ttl', 'sqlite'];

        private const USAGE = <<<TXT
        Kullanım:
          php bin/api-key-create.php --name=<metin> [--type=user|service|server]
                                     [--user=<32-hex>] [--scopes=a,b] [--ip=a,b]
                                     [--ttl=900s|30m|12h|30d|7w] [--sqlite=<yol>]

        Çıktı: ham anahtar bir kez ekrana basılır; DB'ye yalnız hash + prefix yazılır.
        Exit : 0 başarılı · 1 DB/çalışma zamanı hatası · 2 kullanım hatası
        TXT;

        /**
         * @param list<string> $argv
         */
        public static function main(array $argv): int
        {
            try {
                $opts = self::parse($argv);
            } catch (UsageError $e) {
                fwrite(STDERR, "HATA: {$e->getMessage()}\n\n" . self::USAGE . "\n");

                return self::EXIT_USAGE;
            }

            if (!empty($opts['help'])) {
                fwrite(STDOUT, self::USAGE . "\n");

                return self::EXIT_OK;
            }

            try {
                return self::run($opts);
            } catch (UsageError $e) {
                fwrite(STDERR, "HATA: {$e->getMessage()}\n\n" . self::USAGE . "\n");

                return self::EXIT_USAGE;
            } catch (\Throwable $e) {
                // Guardrail: ham anahtar / parola asla hataya düşmez; yalnızca
                // exception sınıfı + mesajı (DSN parolası içermez) STDERR'e.
                fwrite(STDERR, "HATA: " . $e::class . ': ' . $e->getMessage() . "\n");

                return self::EXIT_ERROR;
            }
        }

        /**
         * @param array<string, mixed> $opts
         */
        private static function run(array $opts): int
        {
            $type   = strtolower((string) ($opts['type'] ?? 'user'));
            $name   = trim((string) ($opts['name'] ?? ''));
            $userId = trim((string) ($opts['user'] ?? ''));

            if ($name === '') {
                throw new UsageError('--name zorunludur');
            }
            if (!in_array($type, self::KEY_TYPES, true)) {
                throw new UsageError('--type şunlardan biri olmalı: ' . implode(', ', self::KEY_TYPES));
            }

            $scopes = self::csv($opts['scopes'] ?? null, ['api.access']);
            $ips    = self::csv($opts['ip'] ?? null, []);
            $expiresAt = self::ttlToExpiresAt(
                isset($opts['ttl']) && $opts['ttl'] !== true ? (string) $opts['ttl'] : null
            );

            // Bağlantı ancak girdi doğrulandıktan sonra kurulur.
            $registry = self::registry(
                isset($opts['sqlite']) && $opts['sqlite'] !== true ? (string) $opts['sqlite'] : null
            );

            if ($userId === '') {
                if ($type === 'user') {
                    throw new UsageError('user tipi için --user=<32 hex UUID> zorunludur');
                }
                $userId = self::findServiceUser($registry);
                if ($userId === null) {
                    throw new UsageError(
                        "role_name='service' olan aktif kullanıcı bulunamadı; --user=<32 hex UUID> ile belirtin"
                    );
                }
            }

            $userId = str_replace('-', '', strtolower($userId));
            if (!UuidV7::isValidHex($userId)) {
                throw new UsageError('--user 32 karakterlik hex UUID olmalıdır');
            }

            $repository = new ApiKeyRepository($registry);
            $created    = $repository->create([
                'user_id'      => $userId,
                'key_name'     => $name,
                'scopes'       => $scopes,
                'key_type'     => $type,
                'allowed_ips'  => $ips,
                'expires_at'   => $expiresAt,
            ]);

            self::report($created);

            return self::EXIT_OK;
        }

        /**
         * @param array<string, mixed> $created
         */
        private static function report(array $created): void
        {
            $rawKey = (string) $created['raw_key'];

            fwrite(STDOUT, "API key oluşturuldu\n");
            fwrite(STDOUT, '  id         : ' . $created['id'] . "\n");
            fwrite(STDOUT, '  user_id    : ' . $created['user_id'] . "\n");
            fwrite(STDOUT, '  key_prefix : ' . $created['key_prefix'] . "\n");
            fwrite(STDOUT, '  key_name   : ' . $created['key_name'] . "\n");
            fwrite(STDOUT, '  key_type   : ' . $created['key_type'] . "\n");
            fwrite(STDOUT, '  scopes     : ' . implode(', ', $created['scopes']) . "\n");
            fwrite(STDOUT, '  allowed_ips: ' . ($created['allowed_ips'] === [] ? '(yok)' : implode(', ', $created['allowed_ips'])) . "\n");
            fwrite(STDOUT, '  rate_limit : ' . $created['rate_limit'] . "\n");
            fwrite(STDOUT, '  expires_at : ' . ($created['expires_at'] ?? '-') . "\n");
            fwrite(STDOUT, "\n");
            fwrite(STDOUT, "--- RAW KEY (bir kez gösterilir; hemen kopyalayın) ---\n");
            fwrite(STDOUT, $rawKey . "\n");
            fwrite(STDOUT, "--- end ---\n");
            fwrite(STDOUT, "\nBu değer bir daha görüntülenemez; DB'de yalnız SHA-256 hash + key_prefix saklanır.\n");
        }

        /**
         * @param list<string> $argv
         * @return array<string, mixed>
         */
        public static function parse(array $argv): array
        {
            $opts = [];
            $args = array_slice($argv, 1);

            for ($i = 0, $n = count($args); $i < $n; $i++) {
                $arg = (string) $args[$i];

                if ($arg === '--help' || $arg === '-h') {
                    $opts['help'] = true;
                    continue;
                }

                if (!str_starts_with($arg, '--')) {
                    throw new UsageError("Beklenmeyen argüman: {$arg}");
                }

                $body = substr($arg, 2);
                if (str_contains($body, '=')) {
                    [$key, $value] = explode('=', $body, 2);
                } else {
                    $key   = $body;
                    $value = true;
                    if (isset($args[$i + 1]) && !str_starts_with((string) $args[$i + 1], '--')) {
                        $value = (string) $args[++$i];
                    }
                }

                if ($key === '' || !in_array($key, self::ALLOWED_OPTIONS, true)) {
                    throw new UsageError("Bilinmeyen seçenek: --{$key}");
                }

                // `--name` (değersiz) gibi bayrak kullanımı → net_usage hatası.
                if ($value === true && $key !== 'help') {
                    throw new UsageError("--{$key} için değer gerekiyor");
                }

                $opts[$key] = $value;
            }

            return $opts;
        }

        /**
         * TTL → `expires_at` (UTC yok, repository yerel `date()` kullanır).
         *
         * `900s` · `30m` · `12h` · `30d` · `7w` · çıplak saniye.
         *
         * @throws UsageError
         */
        public static function ttlToExpiresAt(?string $ttl): ?string
        {
            $ttl = trim((string) $ttl);
            if ($ttl === '' || strtolower($ttl) === 'never' || strtolower($ttl) === 'none') {
                return null;
            }

            if (preg_match('/^(\d+)\s*([smhdw]?)$/i', $ttl, $m) !== 1) {
                throw new UsageError("--ttl biçimi geçersiz: {$ttl} (örn. 900s, 30m, 12h, 30d, 7w)");
            }

            $amount = (int) $m[1];
            $unit   = strtolower($m[2] ?: 's');

            $seconds = $amount * match ($unit) {
                'm' => 60,
                'h' => 3600,
                'd' => 86400,
                'w' => 604800,
                default => 1,
            };

            if ($seconds <= 0) {
                throw new UsageError('--ttl sıfırdan büyük olmalı');
            }

            return date('Y-m-d H:i:s', time() + $seconds);
        }

        /**
         * Virgülle ayrılmış liste → normalize edilmiş dizi.
         *
         * @param mixed $value
         * @param list<string> $default
         * @return list<string>
         */
        private static function csv(mixed $value, array $default): array
        {
            if ($value === null || $value === true || $value === '') {
                return $default;
            }

            $items = array_filter(
                array_map('trim', explode(',', (string) $value)),
                static fn (string $v): bool => $v !== ''
            );

            return array_values(array_unique($items));
        }

        /**
         * `.env` OKUNMAZ — yalnız ortam değişkenleri.
         */
        private static function env(string $key, string $default = ''): string
        {
            $value = getenv($key);

            return ($value === false || $value === '') ? $default : $value;
        }

        /**
         * @throws UsageError DB'ye ulaşılamadığında (çıkış 1 ile yakalanır)
         */
        private static function registry(?string $sqlitePath): IDatabaseRegistry
        {
            if ($sqlitePath !== null && $sqlitePath !== '') {
                return new SqliteFileGateway($sqlitePath);
            }

            $registry = new DatabaseRegistry();
            $registry->registerMySql(
                ApiKeyRepository::DB_KEY,
                self::env('DB_HOST', 'localhost'),
                self::env('DB_NAME', 'coremusic_auth'),
                self::env('DB_USER', ''),
                self::env('DB_PASSWORD', ''),
                (int) self::env('DB_PORT', '3306'),
                self::env('DB_CHARSET', 'utf8mb4'),
            );

            return $registry;
        }

        /**
         * service/server key bağlanacağı kullanıcı: `role_name='service'`.
         *
         * @return string 32 hex UUID | null
         */
        private static function findServiceUser(IDatabaseRegistry $registry): ?string
        {
            $rows = $registry->get(ApiKeyRepository::DB_KEY)->execute(
                'SELECT u.id AS id FROM users u '
                . 'INNER JOIN user_assigned_roles ar ON ar.user_id = u.id '
                . 'INNER JOIN user_roles r ON r.id = ar.role_id '
                . "WHERE r.role_name = :role_name AND r.is_deleted = 0 "
                . 'AND u.is_active = 1 AND u.is_deleted = 0 '
                . 'ORDER BY u.created_at ASC LIMIT 1',
                ['role_name' => 'service']
            );

            if ($rows === []) {
                return null;
            }

            $id = (string) ($rows[0]['id'] ?? '');

            // MySQL BINARY(16) → hex; zaten hex gelen katmanlarda olduğu gibi bırak.
            if (strlen($id) === 16) {
                return bin2hex($id);
            }

            $clean = str_replace('-', '', $id);

            return strlen($clean) === 32 && ctype_xdigit($clean) ? strtolower($clean) : null;
        }
    }
}

namespace {
    // Yalnızca dosya doğrudan çalıştırıldığında (require edildiğinde DEĞİL).
    if (isset($_SERVER['SCRIPT_FILENAME'])
        && realpath((string) $_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)
    ) {
        exit(\CoreMusic\Console\ApiKeyCreateCommand::main($_SERVER['argv'] ?? []));
    }
}
