<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Log;

use CoreMusic\Log\FileHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * P5 Enterprise Logger testleri:
 *  - PSR-3 sözleşmesi (psr/log ^3.0) — implementasyon + dispatcher
 *  - B-F-14: debug() guard düzeltmesi (önce tersine idi → debug modda hiç yazmıyordu)
 *  - P5: traceId stitching ([trace=...] satırda)
 *  - P5: securityEvent (403/429 gövdesi gözlemlenir)
 *  - minLevel kapısı
 */
final class Psr3LoggerTest extends TestCase
{
    private string $dir;
    private FileHandler $handler;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/cm_log_' . bin2hex(random_bytes(6));
        mkdir($this->dir, 0700, true);
        FileHandler::clearTraceId();
    }

    protected function tearDown(): void
    {
        FileHandler::clearTraceId();
        foreach (glob($this->dir . '/*.log') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->dir);
    }

    private function errorsLog(): string
    {
        $path = $this->dir . '/coremusic_php_errors.log';
        return is_file($path) ? (string) file_get_contents($path) : '';
    }

    public function testImplementsPsr3LoggerInterface(): void
    {
        $this->assertInstanceOf(LoggerInterface::class, new FileHandler($this->dir));
    }

    public function testAllPsr3MethodsAreCallable(): void
    {
        $handler = new FileHandler($this->dir, 'debug', true);

        $handler->emergency('e');
        $handler->alert('a');
        $handler->critical('c');
        $handler->error('err');
        $handler->warning('w');
        $handler->notice('n');
        $handler->info('i');
        $handler->log('debug', 'd');

        $log = $this->errorsLog();
        foreach (['[EMERGENCY]', '[ALERT]', '[CRITICAL]', '[ERROR]', '[WARNING]', '[NOTICE]', '[INFO]', '[DEBUG]'] as $tag) {
            $this->assertStringContainsString($tag, $log, "{$tag} satırı üretilmeli");
        }
    }

    public function testLogDispatcher_unknownLevelFallsBackToError(): void
    {
        $handler = new FileHandler($this->dir, 'debug', true);
        $handler->log('bogus-level', 'bilinmeyen seviye');

        $log = $this->errorsLog();
        $this->assertStringContainsString('[ERROR]', $log);
        $this->assertStringContainsString('bilinmeyen seviye', $log);
    }

    /**
     * B-F-14 regresyonu: debug() debugMode AÇIKKEN yazmalıdır (eski guard tersine idi).
     */
    public function testDebug_writesWhenDebugModeEnabled(): void
    {
        $handler = new FileHandler($this->dir, 'debug', true);
        $handler->debug('geliştirme izi', ['k' => 'v']);

        $log = $this->errorsLog();
        $this->assertStringContainsString('[DEBUG]', $log);
        $this->assertStringContainsString('geliştirme izi', $log);
        // debug modda info split dosyası da yazılır
        $infoPath = $this->dir . '/coremusic_php_info.log';
        $this->assertFileExists($infoPath);
        $this->assertStringContainsString('[DEBUG]', (string) file_get_contents($infoPath));
    }

    public function testDebug_skippedWhenDebugModeDisabled(): void
    {
        $handler = new FileHandler($this->dir, 'debug', false);
        $handler->debug('bu yazılmamalı');

        $this->assertStringNotContainsString('bu yazılmamalı', $this->errorsLog());
    }

    public function testMinLevelGate_blocksLowerLevels(): void
    {
        $handler = new FileHandler($this->dir, 'error', true);
        $handler->info('gizli');
        $handler->warning('gizli-2');
        $handler->error('görünür');

        $log = $this->errorsLog();
        $this->assertStringNotContainsString('gizli', $log);
        $this->assertStringContainsString('[ERROR]', $log);
        $this->assertStringContainsString('görünür', $log);
    }

    public function testTraceId_isStampedOnLines(): void
    {
        FileHandler::setTraceId('trace-abc-123');
        $handler = new FileHandler($this->dir);
        $handler->info('istek satırı');

        $log = $this->errorsLog();
        $this->assertStringContainsString('[trace=trace-abc-123]', $log);
        $this->assertStringContainsString('istek satırı', $log);
    }

    public function testSecurityEvent_writesWarningLine(): void
    {
        $handler = new FileHandler($this->dir);
        $handler->securityEvent('rate_limited', ['ip' => '10.0.0.1', 'path' => '/login', 'code' => 429]);

        $log = $this->errorsLog();
        $this->assertStringContainsString('[SECURITY] rate_limited', $log);
        $this->assertStringContainsString('ip=10.0.0.1', $log);
        $this->assertStringContainsString('code=429', $log);

        // Yeni güvenlik akışı: coremusic_php_security.log (kullanıcı talebi 2026-10-07)
        $secPath = $this->dir . '/coremusic_php_security.log';
        $this->assertFileExists($secPath);
        $this->assertStringContainsString('[SECURITY] rate_limited', (string) file_get_contents($secPath));
    }

    public function testAuthEvent_masksEmailPii(): void
    {
        $handler = new FileHandler($this->dir);
        $handler->authEvent('login_failed', ['email' => 'bayram@example.com', 'ip' => '10.0.0.1', 'reason' => 'invalid']);

        $log = $this->errorsLog();
        $this->assertStringContainsString('email=***@example.com', $log);
        $this->assertStringNotContainsString('bayram@example.com', $log);
        $this->assertStringContainsString('[AUTH] login_failed', $log);
    }

    public function testSecretsInContext_areRedacted(): void
    {
        $handler = new FileHandler($this->dir);
        $handler->error('deneme', ['password' => 'super-secret', 'auth_key' => 'abc123']);

        $log = $this->errorsLog();
        $this->assertStringContainsString('"password":"[REDACTED]"', $log);
        $this->assertStringNotContainsString('super-secret', $log);
    }
}
