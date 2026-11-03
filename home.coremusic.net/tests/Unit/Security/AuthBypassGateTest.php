<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Security;

use PHPUnit\Framework\TestCase;

/**
 * Bypass güvenlik kapısı (ADR-008 kapsamı korunur; prod'da tamamen işlevsiz).
 *
 * Kaynak (kaynağa-bağımlı regresyon kapısı — davranış testi değil, KOD SÖZLEŞMESİ):
 *  - constants.php: hardcoded BYPASS_USER_UUID varsayılanı '' (fail-closed)
 *  - bootstrap.php: bypass yazımı HomeSessionManager::set üzerinden (doğrudan $_SESSION yok)
 *  - MusicStreamHandler: APP_ENV_MODE tanımsızken fail-closed + hardcoded
 *    kullanıcı yolu (PII) yok + boş kütüphane path guard'ı
 */
final class AuthBypassGateTest extends TestCase
{
    private function source(string $relativePath): string
    {
        // __DIR__ = home.coremusic.net/tests/Unit/Security → 3 seviye yukarı = proje kökü
        $path = dirname(__DIR__, 3) . '/' . $relativePath;
        $raw = file_get_contents($path);
        $this->assertNotFalse($raw, "Kaynak okunamadı: {$relativePath}");

        return (string) $raw;
    }

    public function testConstants_hardcodedBypassUuidDefaultIsRemoved(): void
    {
        $src = $this->source('config/constants.php');

        // Eski varsayılan bypass'ı .env olmadan da açıyordu (fail-open).
        $this->assertStringNotContainsString("'00000000000000000000000000000001'", $src);
        $this->assertStringContainsString("\$env('BYPASS_USER_UUID', '')", $src);
    }

    public function testConstants_bypassReadsAreGuardedByProductionEnv(): void
    {
        $src = $this->source('config/constants.php');

        // Üretimde bayrak hiç okunmaz (bypass değerleri '' kalır).
        $this->assertStringContainsString("APP_ENV_MODE === 'production'", $src);
        $this->assertStringContainsString('if (!$isProdEnv) {', $src);
    }

    public function testBootstrap_bypassRequiresNonProductionAndFilledUuid(): void
    {
        $src = $this->source('config/bootstrap.php');

        $this->assertStringContainsString("APP_ENV_MODE !== 'production'", $src);
        $this->assertStringContainsString("(string)constant('BYPASS_USER_UUID') !== ''", $src);
    }

    public function testBootstrap_bypassWritesThroughSessionApiNotSuperglobal(): void
    {
        $src = $this->source('config/bootstrap.php');

        $this->assertStringContainsString('$homeContainer->get(HomeSessionManager::class)', $src);
        // Doğrudan superglobal ataması kalmamalı (yalnız yorumlardaki adı geçebilir).
        $this->assertStringNotContainsString("\$_SESSION['MM_UserID']", $src);
        $this->assertStringNotContainsString("\$_SESSION['MM_UserRole']", $src);
    }

    public function testStreamHandler_bypassFailsClosedWhenEnvModeUndefined(): void
    {
        $src = $this->source('include/Stream/MusicStreamHandler.php');

        // Eski koşul `defined('APP_ENV_MODE') && APP_ENV_MODE === 'production'`
        // tanımsız ortamda bypass'ı açık bırakıyordu.
        $this->assertStringContainsString(
            "!defined('APP_ENV_MODE') || APP_ENV_MODE === 'production'",
            $src
        );
    }

    public function testStreamHandler_libraryPathHasNoHardcodedUserPath(): void
    {
        $src = $this->source('include/Stream/MusicStreamHandler.php');

        // P0-3: PII (kullanıcı adı/dosya yolu) sabit kodda olmaz.
        $this->assertStringNotContainsString('C:\\\\Users', $src);
        $this->assertStringNotContainsString('Bayram Ali', $src);
    }

    public function testStreamHandler_emptyLibraryPathIsGuarded(): void
    {
        $src = $this->source('include/Stream/MusicStreamHandler.php');

        // realpath('') CWD döndürür → repo içi dosyalara izin verirdi; guard şart.
        $this->assertStringContainsString('trim($this->libraryPath)', $src);
        $this->assertStringContainsString("\$this->fail(404, 'Kütüphane bulunamadı')", $src);
    }
}