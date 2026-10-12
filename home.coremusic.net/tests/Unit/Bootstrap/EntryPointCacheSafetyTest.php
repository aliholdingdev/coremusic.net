<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Bootstrap;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

/**
 * Regresyon koruması — B-F-24.
 *
 * home/coremusic.net/index.php içinde koşulsuz `apcu_clear_cache()` çağrısı,
 * tüm rate-limit / brute-force sayaçlarını (shared CacheManager -> ApcuAdapter,
 * rl:* ve rate_limit:login:* anahtarları) her istekte siliyordu.
 *
 * Bu test, giriş noktasının APCu'yu ASLA koşulsuz temizlememesini ve
 * opcache_reset yalnızca DEBUG_MODE koruması içinde görünmesini garanti eder.
 */
final class EntryPointCacheSafetyTest extends TestCase
{
    private const ENTRY_POINT = __DIR__ . '/../../../index.php';

    private string $source;

    protected function setUp(): void
    {
        $source = file_get_contents(self::ENTRY_POINT);
        $this->assertNotFalse($source, 'home index.php okunamadı');
        $this->source = (string) $source;
    }

    public function testEntryPoint_neverClearsApcuUnconditionally(): void
    {
        $this->assertStringNotContainsString(
            'apcu_clear_cache',
            $this->source,
            'B-F-24 regresyonu: giriş noktasında apcu_clear_cache çağrısı '
            . 'rate-limit sayaçlarını her istekte siler.'
        );
    }

    public function testEntryPoint_opcacheResetOnlyInsideDebugGuard(): void
    {
        // Tek opcache_reset çağrısı DEBUG_MODE korumalı blok içinde olmalı.
        $this->assertSame(
            1,
            substr_count($this->source, 'opcache_reset()'),
            'opcache_reset yalnızca bir kez (debug-guarded) bulunmalı.'
        );

        $guarded = "defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')";
        $this->assertStringContainsString(
            $guarded,
            $this->source,
            'opcache_reset koruması DEBUG_MODE şartına bağlı kalmalı.'
        );
    }

    public function testEntryPoint_debugGuardPrecedesOpcacheCall(): void
    {
        $guardPos   = strpos($this->source, "defined('DEBUG_MODE') && DEBUG_MODE");
        $opcachePos = strpos($this->source, 'opcache_reset()');

        $this->assertNotFalse($guardPos, 'DEBUG_MODE koruması bulunamadı');
        $this->assertNotFalse($opcachePos, 'opcache_reset çağrısı bulunamadı');
        $this->assertTrue(
            $guardPos < $opcachePos,
            'DEBUG_MODE koruması, opcache_reset çağrısından ÖNCE gelmeli.'
        );
    }
}
