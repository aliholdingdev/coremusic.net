<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Database;

use CoreMusic\Database\ConnectionFactory;
use CoreMusic\Database\DatabaseException;
use PHPUnit\Framework\TestCase;

/**
 * Adım 03 — PDO bağlantı katmanı birim testleri (bağlantı GEREKTİRMEZ yollar).
 *
 * Canlı bağlantı yolları (SELECT 1 smoke, runInTransaction commit/rollback)
 * gerçek DB erişimi gerektirir → smoke çalıştırmasında ayrıca raporlanır
 * (bağlantı yoksa UNKNOWN; "GECTI" UYDURULMAZ).
 */
final class ConnectionFactoryTest extends TestCase
{
    /** @var list<string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        ConnectionFactory::reset();
        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];
        parent::tearDown();
    }

    /** Geçici env dosyası yaz (repo DIŞI temp). */
    private function envFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'cmstep03');
        self::assertNotFalse($path, 'tempnam başarısız');
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;
        return $path;
    }

    public function testGetInstanceIsSingleton(): void
    {
        $a = ConnectionFactory::getInstance($this->envFile('DB_HOST=127.0.0.1'));
        $b = ConnectionFactory::getInstance();
        $this->assertSame($a, $b, 'getInstance süreç başına tek factory döndürmeli');
    }

    public function testMissingSchemaEnvFailsSafeWithoutConnection(): void
    {
        // DB_TESTNONET_NAME tanımsız → bağlantı AÇILMADAN fail-safe exception.
        $factory = new ConnectionFactory($this->envFile("DB_HOST=127.0.0.1\nDB_PORT=1\n"));

        try {
            $factory->get('testnonet');
            $this->fail('DatabaseException bekleniyordu (DB_CONFIG_MISSING)');
        } catch (DatabaseException $e) {
            $this->assertSame('DB_CONFIG_MISSING', $e->getErrorCode());
            $this->assertStringContainsString('DB_TESTNONET_NAME', $e->getMessage());
        }

        $this->assertFalse($factory->has('testnonet'), 'eksik yapılandırmada kayıt OLUŞMAMALI');
    }

    public function testConnectionFailureIsWrappedAndRedacted(): void
    {
        if (!\extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql yüklü değil — bağlantı yolu test edilemez');
        }

        // 127.0.0.1:1 — hiçbir servis dinlemez → bağlantı reddi deterministik.
        $secret = 'S3cretPh4se-NotInMessage';
        $factory = new ConnectionFactory($this->envFile(
            "DB_HOST=127.0.0.1\nDB_PORT=1\nDB_USER=nobody\nDB_PASSWORD={$secret}\nDB_CM_NONET_NAME=cm_nonet\n"
        ));

        try {
            $factory->get('cm_nonet');
            $this->fail('DatabaseException bekleniyordu (DB_CONNECTION_FAILED)');
        } catch (DatabaseException $e) {
            $this->assertSame('DB_CONNECTION_FAILED', $e->getErrorCode());
            $this->assertInstanceOf(\PDOException::class, $e->getPrevious(), 'orijinal PDOException korunmalı');
            $this->assertStringContainsString('[REDACTED]', $e->getMessage(), 'DSN maskeli olmalı');
            $this->assertStringNotContainsString($secret, $e->getMessage(), 'parola mesajda OLMAMALI');
            $this->assertStringNotContainsString('nobody', $e->getMessage(), 'kullanıcı adı mesajda OLMAMALI');
        }
    }

    public function testManagerRegisteredOncePerDbAfterFailedAttempt(): void
    {
        if (!\extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql yüklü değil');
        }

        $factory = new ConnectionFactory($this->envFile(
            "DB_HOST=127.0.0.1\nDB_PORT=1\nDB_USER=nobody\nDB_PASSWORD=x\nDB_CM_REUSE_NAME=cm_reuse\n"
        ));

        try {
            $factory->get('cm_reuse');
        } catch (DatabaseException) {
            // beklenen — bağlantı reddi
        }

        $this->assertTrue($factory->has('cm_reuse'), 'başarısız denemeden sonra manager kaydı kalmalı (singleton)');
        $this->assertSame(
            $factory->registry()->get('cm_reuse'),
            $factory->registry()->get('cm_reuse'),
            'ikinci get() AYNI manager instance kullanmalı — yeni bağlantı yok'
        );
    }

    public function testEnvExampleTemplateHasNoSecretValues(): void
    {
        $template = dirname(__DIR__, 3) . '/config/.env.example';
        $this->assertFileExists($template, 'shared/config/.env.example şablonu mevcut olmalı');

        $content = (string) file_get_contents($template);

        // Global anahtarlar tanımlı olmalı.
        foreach (['DB_HOST=', 'DB_PORT=', 'DB_USER=', 'DB_PASSWORD=', 'DB_CHARSET='] as $needle) {
            $this->assertStringContainsString($needle, $content, "şablonda {$needle} olmalı");
        }

        // 18 şema anahtarından disk-kanıtlı 5'i zorunlu.
        foreach (['DB_AUTH_NAME=', 'DB_USER_NAME=', 'DB_MUSIC_NAME=', 'DB_PLAYLIST_NAME=', 'DB_SYSTEM_NAME='] as $needle) {
            $this->assertStringContainsString($needle, $content, "şablonda {$needle} olmalı");
        }

        // Şablonda secret DEĞERİ yok: DB_PASSWORD satırı yalnız "=<boş>" olabilir.
        $this->assertMatchesRegularExpression(
            '/^DB_PASSWORD=\s*$/m',
            $content,
            'şablonda DB_PASSWORD boş olmalı (secret yasak)'
        );
    }

    /**
 * Port çözüm sırası: DB_{KEY}_PORT → DB_PORT → 3306
 * (ConnectionFactory::portFor — mysql 3306 ↔ mssql 1433 ayrıştırması;
 * canlı smoke bulgusu 2026-10-30: tek global port sqlsrv'yi yanlış kapıya
 * götürüyordu "Protocol error in TDS stream").
 */
public function testPortResolutionOrder_keyOverrideBeatsGlobalBeatsDefault(): void
{
    $saved = [];
    $pin   = static function (string $key, ?string $value) use (&$saved): void {
        if (!array_key_exists($key, $saved)) {
            $saved[$key] = isset($_ENV[$key]) ? (string) $_ENV[$key] : null;
        }
        if ($value === null) {
            unset($_ENV[$key]);
            putenv($key);
        } else {
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    };

    try {
        $factory = new ConnectionFactory($this->envFile("DB_HOST=127.0.0.1\n"));

        // 1) key bazlı override her zaman kazanır
        $pin('DB_PORT', '3306');
        $pin('DB_TESTPORT_PORT', '1433');
        $this->assertSame(1433, $factory->portFor('testport'), 'DB_TESTPORT_PORT global DB_PORT\'u ezmeli');

        // 2) key yok → global DB_PORT
        $pin('DB_TESTPORT_PORT', null);
        $this->assertSame(3306, $factory->portFor('testport'), 'DB_PORT fallback geçerli olmalı');

        // 3) ikisi de yok → varsayılan 3306
        $pin('DB_PORT', null);
        $this->assertSame(3306, $factory->portFor('testport'), 'varsayılan port 3306 olmalı');
    } finally {
        foreach ($saved as $key => $value) {
            if ($value === null) {
                unset($_ENV[$key]);
                putenv($key);
            } else {
                $_ENV[$key] = $value;
                putenv($key . '=' . $value);
            }
        }
    }
}

/**
     * Host/kullanıcı/parola çözüm sırası: DB_{KEY}_* → DB_* → varsayılan
     * (ConnectionFactory::hostFor/userFor/passwordFor — canlı smoke 2026-10-30:
     * aynı key 'smoke' hem mysql hem mssql; per-key kimlik/host zorunlu).
     * Değerler DUMMY'dir — gerçek credential asla kullanılmaz/yazdırılmaz.
     */
    public function testHostUserPasswordResolutionOrder_keyOverrideBeatsGlobalBeatsDefault(): void
    {
        $saved = [];
        $pin   = static function (string $key, ?string $value) use (&$saved): void {
            if (!array_key_exists($key, $saved)) {
                $saved[$key] = isset($_ENV[$key]) ? (string) $_ENV[$key] : null;
            }
            if ($value === null) {
                unset($_ENV[$key]);
                putenv($key);
            } else {
                $_ENV[$key] = $value;
                putenv($key . '=' . $value);
            }
        };

        try {
            $factory = new ConnectionFactory($this->envFile("DB_HOST=127.0.0.1\n"));

            // HOST: key override → global → 'localhost'
            $pin('DB_HOST', 'globalhost');
            $pin('DB_TESTCRED_HOST', 'keyhost');
            $this->assertSame('keyhost', $factory->hostFor('testcred', 'mysql'), 'DB_TESTCRED_HOST global DB_HOST\'u ezmeli');

            $pin('DB_TESTCRED_HOST', null);
            $this->assertSame('globalhost', $factory->hostFor('testcred', 'mysql'), 'DB_HOST fallback geçerli olmalı');

            $pin('DB_HOST', null);
            $this->assertSame('localhost', $factory->hostFor('testcred', 'mysql'), 'varsayılan host localhost olmalı');

            // USER: key override → global → ''
            $pin('DB_USER', 'globaluser');
            $pin('DB_TESTCRED_USER', 'keyuser');
            $this->assertSame('keyuser', $factory->userFor('testcred'), 'DB_TESTCRED_USER global DB_USER\'u ezmeli');

            $pin('DB_TESTCRED_USER', null);
            $this->assertSame('globaluser', $factory->userFor('testcred'), 'DB_USER fallback geçerli olmalı');

            $pin('DB_USER', null);
            $this->assertSame('', $factory->userFor('testcred'), 'varsayılan kullanıcı boş string olmalı');

            // PASSWORD: key override → global → '' (DUMMY değerler — secret yazılmaz)
            $pin('DB_PASSWORD', 'globalpass');
            $pin('DB_TESTCRED_PASSWORD', 'keypass');
            $this->assertSame('keypass', $factory->passwordFor('testcred'), 'DB_TESTCRED_PASSWORD global DB_PASSWORD\'u ezmeli');

            $pin('DB_TESTCRED_PASSWORD', null);
            $this->assertSame('globalpass', $factory->passwordFor('testcred'), 'DB_PASSWORD fallback geçerli olmalı');

            $pin('DB_PASSWORD', null);
            $this->assertSame('', $factory->passwordFor('testcred'), 'varsayılan parola boş string olmalı');
        } finally {
            foreach ($saved as $key => $value) {
                if ($value === null) {
                    unset($_ENV[$key]);
                    putenv($key);
                } else {
                    $_ENV[$key] = $value;
                    putenv($key . '=' . $value);
                }
            }
        }
    }

    /**
     * sqlite host dalı DEĞİŞMEDİ: DB_{KEY}_PATH → DB_HOST → fail-safe
     * DB_CONFIG_MISSING. hostFor salt-okuma çözümleyicidir — bağlantı AÇILMAZ.
     */
    public function testHostForSqlite_resolvesPathThenDbHostUnchanged(): void
    {
        $saved = [];
        $pin   = static function (string $key, ?string $value) use (&$saved): void {
            if (!array_key_exists($key, $saved)) {
                $saved[$key] = isset($_ENV[$key]) ? (string) $_ENV[$key] : null;
            }
            if ($value === null) {
                unset($_ENV[$key]);
                putenv($key);
            } else {
                $_ENV[$key] = $value;
                putenv($key . '=' . $value);
            }
        };

        try {
            $factory = new ConnectionFactory($this->envFile("DB_HOST=127.0.0.1\n"));

            // 1) PATH var → PATH kazanır (salt-okuma — dosya açılmaz)
            $pin('DB_HOST', '/tmp/global.sqlite');
            $pin('DB_TESTSQL_PATH', '/tmp/key.sqlite');
            $this->assertSame('/tmp/key.sqlite', $factory->hostFor('testsql', 'sqlite'), 'DB_TESTSQL_PATH global DB_HOST\'u ezmeli');

            // 2) PATH yok → DB_HOST (dosya yolu olarak) — mevcut davranış korunur
            $pin('DB_TESTSQL_PATH', null);
            $this->assertSame('/tmp/global.sqlite', $factory->hostFor('testsql', 'sqlite'), 'DB_HOST fallback (dosya yolu) korunmalı');

            // 3) ikisi de yok → fail-safe DB_CONFIG_MISSING (bağlantı AÇILMAZ)
            $pin('DB_HOST', null);
            try {
                $factory->hostFor('testsql', 'sqlite');
                $this->fail('DatabaseException bekleniyordu (DB_CONFIG_MISSING)');
            } catch (DatabaseException $e) {
                $this->assertSame('DB_CONFIG_MISSING', $e->getErrorCode());
                $this->assertStringContainsString('DB_TESTSQL_PATH', $e->getMessage());
            }
        } finally {
            foreach ($saved as $key => $value) {
                if ($value === null) {
                    unset($_ENV[$key]);
                    putenv($key);
                } else {
                    $_ENV[$key] = $value;
                    putenv($key . '=' . $value);
                }
            }
        }
    }

public function testDatabaseExceptionMessageNeverCarriesDsnOrPassword(): void
    {
        $e = DatabaseException::connectionFailed('auth');
        $this->assertSame('DB_CONNECTION_FAILED', $e->getErrorCode());
        $this->assertStringContainsString('[REDACTED]', $e->getMessage());
        $this->assertStringNotContainsString('mysql:', $e->getMessage(), 'DSN parçası mesajda olmamalı');
        $this->assertStringNotContainsString('charset', $e->getMessage(), 'DSN parçası mesajda olmamalı');
    }
}
