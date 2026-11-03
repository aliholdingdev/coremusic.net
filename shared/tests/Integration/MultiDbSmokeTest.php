<?php declare(strict_types=1);

namespace CoreMusic\Test\Integration;

use CoreMusic\Contracts\Database\INoSqlDatabaseManager;
use CoreMusic\Database\ConnectionFactory;
use CoreMusic\Database\DatabaseException;
use PHPUnit\Framework\TestCase;

/**
 * Multi-DB canlı smoke — Faz 1 (1C).
 *
 * Kapsam: ConnectionFactory → DatabaseRegistry → 4 provider
 * (mysql | mssql | sqlite | mongodb) gerçek bağlantı yolu.
 *
 * @group live
 *
 * Kurallar (bağlayıcı):
 *  - grup `live` → varsayılan `composer test` DIŞINDA (phpunit.xml <groups><exclude>).
 *    Çalıştırma: `vendor/bin/phpunit --group live`.
 *  - DDL yalnız `cm_smoke_*` adlı dosya/şemalara; ürün şemasına DDL/DML YOK.
 *  - Extension yoksa veya env anahtarı tanımsızsa → dürüst markTestSkipped
 *    (sahte yeşil yasak; gerekçe nedenle yazılır).
 *  - Secret değerleri hiçbir assert mesajına/çıktıya yazılmaz.
 *  - env mutasyonu $_ENV+putenv çiftinden yapılır ve tearDown'da geri alınır
 *    (ConnectionFactory::env() $_ENV öncelikli — ConnectionFactory.php:282-289).
 *
 * Canlı smoke için gereken env anahtarları (raporlanan, .env'e bu test dokunmaz):
 *  DB_PROVIDER_SMOKE · DB_SMOKE_NAME (cm_smoke_*) · DB_SMOKE_PATH (sqlite)
 *  DB_SMOKE_PORT (mssql — TDS kapı numarası; yoksa gerekçeli SKIP)
 *  DB_SMOKE_MONGO_PORT (mongodb — yoksa 27017; global DB_PORT mssql/mysql'a ayrıdır)
 *  DB_SMOKE_HOST/DB_SMOKE_USER/DB_SMOKE_PASSWORD (mssql kimliği — değerler rapora/çıktıya YAZILMAZ)
 *
 * Not: bağlantı yoksa test dürüst FAIL/SKIP verir; "GECTİ" UYDURULMAZ.
 */
final class MultiDbSmokeTest extends TestCase
{
    /** @var list<string> */
    private array $tempFiles = [];

    /**
     * Değiştirilen env anahtarlarının ilk değerleri (tearDown restore).
     *
     * @var array<string, array{env: string|null, getenv: string|null}>
     */
    private array $envBackup = [];

    protected function setUp(): void
    {
        ConnectionFactory::reset();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        foreach ($this->envBackup as $key => $original) {
            if ($original['env'] === null) {
                unset($_ENV[$key]);
            } else {
                $_ENV[$key] = $original['env'];
            }
            if ($original['getenv'] === null) {
                putenv($key);
            } else {
                putenv($key . '=' . $original['getenv']);
            }
        }
        $this->envBackup = [];

        ConnectionFactory::reset();

        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];

        parent::tearDown();
    }

    /**
     * Çözüm sırası: DB_PROVIDER_{KEY} → DB_PROVIDER → 'mysql'
     * (ConnectionFactory::providerFor, ConnectionFactory.php:178-190).
     */
    public function testProviderResolutionOrder_keyOverrideBeatsGlobalBeatsDefault(): void
    {
        $factory = ConnectionFactory::getInstance();

        // 1) her iki anahtar da yok → varsayılan 'mysql'
        $this->pinEnv('DB_PROVIDER_SMOKE', null);
        $this->pinEnv('DB_PROVIDER', null);
        $this->assertSame('mysql', $factory->providerFor('smoke'), 'varsayılan provider mysql olmalı');

        // 2) yalnız global → global geçerli
        $this->pinEnv('DB_PROVIDER', 'sqlite');
        $this->assertSame('sqlite', $factory->providerFor('smoke'), 'DB_PROVIDER global geçerli olmalı');

        // 3) key bazlı override → her zaman kazanır
        $this->pinEnv('DB_PROVIDER_SMOKE', 'mssql');
        $this->assertSame('mssql', $factory->providerFor('smoke'), 'DB_PROVIDER_SMOKE globali ezmeli');
    }

    /**
     * Bilinmeyen provider → kayıt OLUŞMADAN exception (fail-closed, yarı kayıt yok).
     * DatabaseRegistry.php:51-52 (match default → RuntimeException).
     */
    public function testUnknownProviderFailsClosedWithoutRegistration(): void
    {
        $factory = ConnectionFactory::getInstance();
        $this->pinEnv('DB_PROVIDER_SMOKE', 'postgresql');
        $this->pinEnv('DB_SMOKE_NAME', 'cm_smoke_probe');
        $this->pinEnv('DB_SMOKE_PATH', $this->tempCmSmokePath());

        try {
            $factory->get('smoke');
            $this->fail('Unsupported provider için RuntimeException bekleniyordu');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Unsupported provider type', $e->getMessage());
            $this->assertStringContainsString('postgresql', $e->getMessage());
        }

        $this->assertFalse($factory->has('smoke'), 'bilinmeyen provider sonrası kayıt OLMAMALI (fail-closed)');
    }

    /**
     * Eksik DB_{KEY}_NAME → bağlantı AÇILMADAN DB_CONFIG_MISSING (fail-safe).
     * ConnectionFactory::schemaName, ConnectionFactory.php:268-276.
     */
    public function testSmokeKeyMissingSchemaNameFailsClosedWithoutConnection(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite yüklü değil — sqlite provider yolu test edilemez');
        }

        $factory = ConnectionFactory::getInstance();
        $this->pinEnv('DB_PROVIDER_SMOKE', 'sqlite');
        $this->pinEnv('DB_SMOKE_NAME', null); // bilinçli eksik
        $this->pinEnv('DB_SMOKE_PATH', $this->tempCmSmokePath());

        try {
            $factory->get('smoke');
            $this->fail('DatabaseException bekleniyordu (DB_CONFIG_MISSING)');
        } catch (DatabaseException $e) {
            $this->assertSame('DB_CONFIG_MISSING', $e->getErrorCode());
            $this->assertStringContainsString('DB_SMOKE_NAME', $e->getMessage());
        }

        $this->assertFalse($factory->has('smoke'), 'eksik şema adında kayıt OLUŞMAMALI (fail-safe)');
    }

    /**
     * sqlite canlı: DB_{KEY}_PATH çözümü + transaction commit/rollback turu.
     * DDL yalnız cm_smoke_* önekli GEÇİCİ dosyaya (repo/DB dışı temp).
     */
    public function testSqliteSmoke_pathResolutionAndTransactionRoundtrip(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite yüklü değil — sqlite smoke çalıştırılamaz');
        }

        $factory = ConnectionFactory::getInstance();
        $path = $this->tempCmSmokePath();
        $this->pinEnv('DB_PROVIDER_SMOKE', 'sqlite');
        $this->pinEnv('DB_SMOKE_NAME', 'cm_smoke_probe');
        $this->pinEnv('DB_SMOKE_PATH', $path);

        $pdo = $factory->get('smoke');
        $this->assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME), 'sqlite sürücüsü bekleniyor');

        // PATH çözümü bağımsız kanıtı: PDO main DB dosyası benim temp dosyam olmalı.
        $stmt = $pdo->prepare('PRAGMA database_list');
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        $this->assertIsArray($row, 'PRAGMA database_list satır dönmeli');
        $this->assertSame(
            $this->normalizePath($path),
            $this->normalizePath((string) ($row[2] ?? '')),
            'DB_SMOKE_PATH çözümü temp dosyaya bağlanmalı'
        );

        // DDL — yalnız cm_smoke_* dosyasına.
        $pdo->exec('CREATE TABLE IF NOT EXISTS cm_smoke_probe (id INTEGER PRIMARY KEY, note TEXT NOT NULL)');

        // commit yolu
        $result = $factory->runInTransaction('smoke', static function (\PDO $p): int {
            $insert = $p->prepare('INSERT INTO cm_smoke_probe (note) VALUES (:note)');
            $insert->execute([':note' => 'commit-row']);
            return 1;
        });
        $this->assertSame(1, $result, 'runInTransaction dönüş değeri commit sonrası dönmeli');

        $count = $factory->get('smoke')->prepare('SELECT COUNT(1) FROM cm_smoke_probe WHERE note = :n');
        $count->execute([':n' => 'commit-row']);
        $this->assertSame(1, (int) $count->fetchColumn(), 'commit edilen satır görünmeli');

        // rollback yolu: satır yazılır, exception fırlatılır → orijinal exception rethrow + rollback.
        try {
            $factory->runInTransaction('smoke', static function (\PDO $p): void {
                $insert = $p->prepare('INSERT INTO cm_smoke_probe (note) VALUES (:note)');
                $insert->execute([':note' => 'rollback-row']);
                throw new \RuntimeException('smoke-rollback');
            });
            $this->fail('RuntimeException rethrow edilmeliydi');
        } catch (\RuntimeException $e) {
            $this->assertSame('smoke-rollback', $e->getMessage(), 'ORİJİNAL exception rethrow edilmeli (wrapper değil)');
        }

        $verify = $factory->get('smoke');
        $this->assertFalse($verify->inTransaction(), 'rollback sonrası transaction açık kalmamalı');

        $rolled = $verify->prepare('SELECT COUNT(1) FROM cm_smoke_probe WHERE note = :n');
        $rolled->execute([':n' => 'rollback-row']);
        $this->assertSame(0, (int) $rolled->fetchColumn(), 'rollback edilen satır VERİLMEMİŞ olmalı');
    }

    /**
     * sqlite PATH yok → DB_HOST (dosya yolu olarak) fallback'i (ConnectionFactory.php:248-263).
     */
    public function testSqliteSmoke_pathFallsBackToDbHost(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite yüklü değil — sqlite smoke çalıştırılamaz');
        }

        $factory = ConnectionFactory::getInstance();
        $path = $this->tempCmSmokePath();
        $this->pinEnv('DB_PROVIDER_SMOKE', 'sqlite');
        $this->pinEnv('DB_SMOKE_NAME', 'cm_smoke_probe');
        $this->pinEnv('DB_SMOKE_PATH', null); // PATH bilinçli yok
        $this->pinEnv('DB_HOST', $path);      // host = dosya yolu

        $pdo = $factory->get('smoke');
        $this->assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));

        $stmt = $pdo->prepare('PRAGMA database_list');
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        $this->assertIsArray($row, 'PRAGMA database_list satır dönmeli');
        $this->assertSame(
            $this->normalizePath($path),
            $this->normalizePath((string) ($row[2] ?? '')),
            'DB_HOST fallback i dosya yolu olarak kullanılmış olmalı'
        );
    }

    /**
     * mysql canlı: gerçek bağlantı + prepared okuma + transaction turu (salt okuma).
     * ÜRÜN ŞEMASINA DDL/DML YOK — yalnız sabit SELECT.
     */
    public function testMysqlSmoke_liveConnectionAndReadonlyTransaction(): void
    {
        if (!\extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql yüklü değil — canlı mysql smoke çalıştırılamaz');
        }

        $factory = ConnectionFactory::getInstance();
        $this->requireLiveSmokeName('mysql');
        $this->pinEnv('DB_PROVIDER_SMOKE', 'mysql');
        // DB_SMOKE_PORT yalnız mssql içindir (portFor per-key override);
        // mysql global DB_PORT'a düşer — kullanıcı 1433 ayarlasa bile mysql 3306'da kalır.
        $this->pinEnv('DB_SMOKE_PORT', null);
        // DB_SMOKE_HOST/USER/PASSWORD .env'te (mssql kimliği) — mysql bunları
        // KULLANMAZ; per-key override null'lanır → global DB_HOST/DB_USER/DB_PASSWORD.
        $this->pinEnv('DB_SMOKE_HOST', null);
        $this->pinEnv('DB_SMOKE_USER', null);
        $this->pinEnv('DB_SMOKE_PASSWORD', null);

        // Bağlantı hatası burada dürüst FAIL verir (DatabaseException — sahte yeşil yok).
        $pdo = $factory->get('smoke');
        $this->assertSame('mysql', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));

        $stmt = $pdo->prepare('SELECT 1 AS ok');
        $stmt->execute();
        $this->assertSame(1, (int) $stmt->fetchColumn(), 'prepared SELECT 1 dönmeli');

        $value = $factory->runInTransaction('smoke', static function (\PDO $p): int {
            $s = $p->prepare('SELECT 1 AS ok');
            $s->execute();
            return (int) $s->fetchColumn();
        });
        $this->assertSame(1, $value, 'runInTransaction canlı mysql yolunda commit etmeli');
    }

    /**
     * mssql (pdo_sqlsrv) canlı: gerçek bağlantı + prepared okuma.
     * ÜRÜN ŞEMASINA DDL/DML YOK.
     */
    public function testMssqlSmoke_liveReadonlyProbe(): void
    {
        if (!\extension_loaded('pdo_sqlsrv')) {
            $this->markTestSkipped('pdo_sqlsrv yüklü değil — canlı mssql smoke çalıştırılamaz');
        }

        $factory = ConnectionFactory::getInstance();
        $this->requireLiveSmokeName('mssql');
        $this->pinEnv('DB_PROVIDER_SMOKE', 'mssql');

        // TDS kapısı DB_SMOKE_PORT ile ayrıştırılır (portFor: DB_SMOKE_PORT → DB_PORT → 3306).
        // Anahtar yoksa dürüst SKIP: global 3306 (MySQL) sqlsrv'yi "Protocol error in
        // TDS stream" ile yanlış kapıya götürür — bilinen makine gerçeği 2026-10-30
        // (MSSQL$ALI örnek dinleyicisi dinamik portta, geçerli SQL auth kimliği yok).
        $port = $this->envValue('DB_SMOKE_PORT');
        if ($port === null || $port === '') {
            $this->markTestSkipped(
                'DB_SMOKE_PORT tanımlı değil — canlı mssql smoke için shared/config/.env içine '
                . 'DB_SMOKE_PORT=<sqlsrv dinleme kapısı> eklenmeli (makine gerçeği: örnek '
                . 'dinamik portta dinliyor ve geçerli SQL auth kimliği yok — env anahtarı raporda listelendi)'
            );
        }
        $this->pinEnv('DB_SMOKE_PORT', $port);

        $pdo = $factory->get('smoke');
        $this->assertSame('sqlsrv', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));

        $stmt = $pdo->prepare('SELECT 1 AS ok');
        $stmt->execute();
        $this->assertSame(1, (int) $stmt->fetchColumn(), 'prepared SELECT 1 dönmeli');
    }

    /**
     * mongodb (mongodb eklentisi) canlı: NoSQL manager + salt-okuma probe.
     * write()/DDL YOK — yalnız execute() okuma.
     */
    public function testMongoDbSmoke_liveReadonlyProbe(): void
    {
        if (!\extension_loaded('mongodb')) {
            $this->markTestSkipped('mongodb eklentisi yüklü değil — canlı mongodb smoke çalıştırılamaz');
        }

        $factory = ConnectionFactory::getInstance();
        $this->requireLiveSmokeName('mongodb');
        $this->pinEnv('DB_PROVIDER_SMOKE', 'mongodb');
        // Per-key kimlik/host anahtarları mongodb'de de null'lanır (davranış
        // değişikliği yok — global fallback; DB_SMOKE_* .env'te mssql kimliğidir).
        $this->pinEnv('DB_SMOKE_HOST', null);
        $this->pinEnv('DB_SMOKE_USER', null);
        $this->pinEnv('DB_SMOKE_PASSWORD', null);
        // MongoDbManager port'u portFor('smoke') üzerinden gelir; global DB_PORT
        // (3306/mysql) mongo'yu yanlış kapıya götürür → özel anahtar, yoksa 27017.
        $mongoPort = $this->envValue('DB_SMOKE_MONGO_PORT');
        $this->pinEnv(
            'DB_SMOKE_PORT',
            ($mongoPort === null || $mongoPort === '') ? '27017' : $mongoPort
        );

        $manager = $factory->getNoSqlManager('smoke');
        $this->assertInstanceOf(INoSqlDatabaseManager::class, $manager);

        // Salt-okuma: _id asla yok edilemez → filtre hiçbir satır eşleştirmez (deterministik []).
        $rows = $manager->execute(
            '{"_id":{"$exists":false}}',
            ['collection' => 'cm_smoke_probe']
        );
        $this->assertIsArray($rows, 'MongoDbManager::execute() array dönmeli');
        $this->assertSame([], $rows, 'hiçbir satır eşleşmemeli (salt okuma)');

        $this->assertFalse($manager->supportsTransactions(), 'MongoDbManager supportsTransactions() false (sözleşme)');
        $this->assertSame('', $manager->lastInsertId(), 'MongoDbManager lastInsertId() "" (sözleşme)');
    }

    /**
     * Canlı smoke için DB_SMOKE_NAME zorunlu + güvenlik: yalnız cm_smoke_* şeması.
     * Eksik → markTestSkipped (gerekçe anahtar adını söyler); ürün şeması → FAIL.
     */
    private function requireLiveSmokeName(string $provider): string
    {
        $name = $this->envValue('DB_SMOKE_NAME');
        if ($name === null || $name === '') {
            $this->markTestSkipped(
                "DB_SMOKE_NAME tanımlı değil — canlı {$provider} smoke için shared/config/.env içine "
                . 'DB_SMOKE_NAME=cm_smoke_* eklenmeli (env anahtarı raporda listelendi)'
            );
        }

        $this->assertStringStartsWith(
            'cm_smoke',
            $name,
            'güvenlik: canlı smoke yalnız cm_smoke_* şemasına bağlanabilir (ürün şemasına DDL/DML yasak)'
        );

        return $name;
    }

    /**
     * $_ENV + putenv çifti pin — ConnectionFactory::env() $_ENV öncelik okur,
     * providerFor() hem $_ENV hem getenv kontrol eder (ConnectionFactory.php:181-189).
     */
    private function pinEnv(string $key, ?string $value): void
    {
        if (!array_key_exists($key, $this->envBackup)) {
            $current = getenv($key);
            $this->envBackup[$key] = [
                'env'     => isset($_ENV[$key]) ? (string) $_ENV[$key] : null,
                'getenv'  => $current !== false ? (string) $current : null,
            ];
        }

        if ($value === null) {
            unset($_ENV[$key]);
            putenv($key); // çıplak ad = ortam değişkenini kaldırır
        } else {
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    }

    /** $_ENV → getenv → null (değer hiçbir yere yazılmaz/yazdırılmaz). */
    private function envValue(string $key): ?string
    {
        if (isset($_ENV[$key])) {
            return (string) $_ENV[$key];
        }
        $value = getenv($key);
        return $value !== false ? (string) $value : null;
    }

    /** cm_smoke_ önekli geçici sqlite dosya yolu (dosya üretilmez; sqlite açınca oluşturur). */
    private function tempCmSmokePath(): string
    {
        $path = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . 'cm_smoke_' . bin2hex(random_bytes(6)) . '.sqlite';
        $this->tempFiles[] = $path;
        return $path;
    }

    /** Windows/Unix ayrımını nötrler (PRAGMA yolu ↔ hedef yol karşılaştırması). */
    private function normalizePath(string $path): string
    {
        return strtolower(str_replace('\\', '/', $path));
    }
}