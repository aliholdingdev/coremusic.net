<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Log;

use CoreMusic\Log\FileHandler;
use CoreMusic\Log\Redactor;
use CoreMusic\PageRouter\StructuredLogger;
use PHPUnit\Framework\TestCase;

/**
 * Faz 4a — .ai/AGENTS.md §17 Edge Case 3:
 * "Sensitive data log'da → [REDACTED] ile maskeleme".
 *
 * Hem Redactor (maskeleme mantığı) hem yazma yolları (FileHandler,
 * StructuredLogger) tek noktadan doğrulanır.
 */
final class LoggerRedactionTest extends TestCase
{
    /** Gerçek log'da görülen 64-char hex auth_key. */
    private const AUTH_KEY = '5e6981c8dfe57a73a92b9e95f5bef278fff1b1872fe32499e0171b6d935e5a6b';

    /** Korunması gereken correlation id (8-4-4-4-12). */
    private const UUID = '550e8400-e29b-41d4-a716-446655440000';

    private string $logDir = '';

    /** @var array<string, string> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        foreach (['REQUEST_URI', 'REQUEST_METHOD', 'REMOTE_ADDR'] as $key) {
            $this->serverBackup[$key] = $_SERVER[$key] ?? '';
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->serverBackup as $key => $value) {
            if ($value === '') {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $value;
            }
        }

        if ($this->logDir !== '' && is_dir($this->logDir)) {
            foreach (glob($this->logDir . '/*.log') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($this->logDir);
        }
    }

    public function testAuthKeyInQueryStringIsRedacted(): void
    {
        // Gerçekçi satır: hassas değer URI + JSON context içinde.
        $line = '[2026-09-06 17:59:08] [DEBUG] [GET /auth/callback?return=%2Fhome&auth_key=' . self::AUTH_KEY
            . '] [127.0.0.1] [Home] Auth callback triggered {"has_auth_key":true,"auth_key_prefix":"5e6981c8..."}';

        $redacted = Redactor::redact($line);

        $this->assertStringContainsString('auth_key=[REDACTED]', $redacted);
        $this->assertStringNotContainsString(self::AUTH_KEY, $redacted);
        // Alt-anhtarlar maskeleme flag'i değildir, korunur (false-positive yok).
        $this->assertStringContainsString('"has_auth_key":true', $redacted);
        $this->assertStringContainsString('"auth_key_prefix":"5e6981c8..."', $redacted);
    }

    public function testPasswordIsRedactedInKeyValueAndJsonFormats(): void
    {
        // key=value
        $kv = Redactor::redact('[AUTH] login_failed email=a@b.com password=hunter2 ip=127.0.0.1');
        $this->assertStringContainsString('password=[REDACTED]', $kv);
        $this->assertStringNotContainsString('hunter2', $kv);
        $this->assertStringContainsString('ip=127.0.0.1', $kv);

        // JSON / dizi context — çok kelimeli değer dahil, tırnak yapısı korunur.
        $json = Redactor::redact('{"module":"auth","password":"my pass word","csrf_token":"abc123"}');
        $this->assertStringContainsString('"password":"[REDACTED]"', $json);
        $this->assertStringNotContainsString('my pass word', $json);
        $this->assertStringContainsString('"csrf_token":"[REDACTED]"', $json);

        // key: value + auth-scheme (Bearer) çok kelimeli değer.
        $bearer = Redactor::redact('Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.payload-signature');
        $this->assertStringContainsString('Authorization: [REDACTED]', $bearer);
        $this->assertStringNotContainsString('eyJhbGciOiJIUzI1NiJ9', $bearer);
    }

    public function testCleanLinePassesThroughUnchanged(): void
    {
        $cleanLines = [
            '[2026-09-27 11:00:00] [INFO] [GET /home] [127.0.0.1] [Home] page rendered {"status":"ok","durationMs":12.5}',
            '[Home] Auth callback session created {"user_id":"-"}',
        ];

        foreach ($cleanLines as $line) {
            $this->assertSame($line, Redactor::redact($line), 'Temiz satır değişmemeli: ' . $line);
        }
    }

    /**
     * B-F-17 / P5: e-posta PII'si logda düz metin durmaz — yerel kısım
     * maskelenir, alan adı korunur (ops tanınırlığı).
     */
    public function testEmailLocalPartMaskedDomainKept(): void
    {
        $masked = Redactor::redact('[AUTH] login_failed email=bayram@example.com ip=10.0.0.1');

        $this->assertSame('[AUTH] login_failed email=***@example.com ip=10.0.0.1', $masked);
        $this->assertStringNotContainsString('bayram@example.com', $masked);
    }

    public function testEmailMaskInsideJsonContext(): void
    {
        $masked = Redactor::redact('{"email":"kullanici@coremusic.net","ok":true}');

        $this->assertStringContainsString('"email":"***@coremusic.net"', $masked);
        $this->assertStringNotContainsString('kullanici@coremusic.net', $masked);
    }

    public function testSixtyFourCharHashIsRedactedButUuidIsPreserved(): void
    {
        $line = '[INFO] migrate done hash=' . self::AUTH_KEY . ' traceId=' . self::UUID;

        $this->assertSame(
            '[INFO] migrate done hash=[REDACTED] traceId=' . self::UUID,
            Redactor::redact($line)
        );

        // 64-char base64 de maskelenir (48 bayt → tam 64 char, padding yok).
        $base64 = base64_encode(random_bytes(48));
        $this->assertSame(64, strlen($base64));
        $this->assertStringNotContainsString($base64, Redactor::redact('payload=' . $base64));

        // UUID tek başına da maskelenmez.
        $this->assertSame(self::UUID, Redactor::redact(self::UUID));
    }

    public function testFileHandlerWritePathAppliesRedaction(): void
    {
        $this->logDir = sys_get_temp_dir() . '/cm_logger_redaction_' . bin2hex(random_bytes(6));
        $this->assertTrue(mkdir($this->logDir, 0777, true));

        // Sızıntının gerçekleştiği gerçek yol: URI'deki auth_key.
        $_SERVER['REQUEST_URI']    = '/auth/callback?return=%2Fhome&auth_key=' . self::AUTH_KEY;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR']    = '127.0.0.1';

        $logger = new FileHandler($this->logDir, 'info');
        $logger->info('[Home] Auth callback triggered', ['has_auth_key' => true]);

        $file = $this->logDir . '/coremusic_php_errors.log';
        $this->assertFileExists($file);

        $content = (string) file_get_contents($file);

        $this->assertStringContainsString('auth_key=[REDACTED]', $content);
        $this->assertStringNotContainsString(self::AUTH_KEY, $content);
        $this->assertStringContainsString('"has_auth_key":true', $content);
        // Loglama seviyesi değişmedi: satır hâlâ INFO olarak yazılır.
        $this->assertStringContainsString('[INFO]', $content);
    }

    public function testStructuredLoggerWritePathAppliesRedaction(): void
    {
        $logFile = sys_get_temp_dir() . '/cm_structured_redaction_' . bin2hex(random_bytes(6)) . '.log';
        $previous = ini_get('error_log');

        if (ini_set('error_log', $logFile) === false) {
            $this->markTestSkipped('error_log ini bu ortamda değiştirilemiyor');
        }

        try {
            $logger = new StructuredLogger('info');
            $logger->info('auth', 'login_failed', ['password' => 'hunter2', 'ip' => '127.0.0.1']);

            clearstatcache();
            $content = (string) file_get_contents($logFile);

            $this->assertStringContainsString('"password":"[REDACTED]"', $content);
            $this->assertStringNotContainsString('hunter2', $content);
            $this->assertStringContainsString('"ip":"127.0.0.1"', $content);
        } finally {
            ini_set('error_log', $previous === false ? '' : $previous);
            @unlink($logFile);
        }
    }
}
