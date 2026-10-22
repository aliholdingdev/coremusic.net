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

    public function testDatabaseExceptionMessageNeverCarriesDsnOrPassword(): void
    {
        $e = DatabaseException::connectionFailed('auth');
        $this->assertSame('DB_CONNECTION_FAILED', $e->getErrorCode());
        $this->assertStringContainsString('[REDACTED]', $e->getMessage());
        $this->assertStringNotContainsString('mysql:', $e->getMessage(), 'DSN parçası mesajda olmamalı');
        $this->assertStringNotContainsString('charset', $e->getMessage(), 'DSN parçası mesajda olmamalı');
    }
}
