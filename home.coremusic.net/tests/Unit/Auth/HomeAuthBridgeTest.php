<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Auth;

use CoreMusic\Home\Auth\HomeAuthBridge;
use CoreMusic\Home\Session\HomeSessionManager;
use CoreMusic\Session\SessionBootstrapper;
use PHPUnit\Framework\TestCase;

/**
 * C3 regresyonu — HomeAuthBridge kimlik doğrulaması sonrası session ID rotasyonu.
 *
 * validateAndCreateSession() auth_key'i doğruladıktan sonra session'ı
 * setAuthUser ile kuruyor ama HomeSessionManager::regenerateId() hiç
 * çağrılmıyordu → session fixation (auth.coremusic.net C1 ile aynı zafiyet).
 */
final class HomeAuthBridgeTest extends TestCase
{
    private HomeAuthBridge $bridge;

    protected function setUp(): void
    {
        $_SESSION = [];
        SessionBootstrapper::ensureStarted();
        $this->bridge = new HomeAuthBridge(new HomeSessionManager(), 'http://auth.coremusic.net');
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        SessionBootstrapper::writeClose();
    }

    public function testEstablishSession_rotatesSessionIdAfterAuthSuccess(): void
    {
        $oldId = session_id();
        $this->assertNotSame('', $oldId, 'Test öncesi aktif session bekleniyor');

        $this->bridge->establishSession([
            'user_id'      => '018f00000000000000000000000000cc',
            'username'     => 'bayram',
            'email'        => 'bayram@example.test',
            'display_name' => 'Bayram',
            'gender'       => 'male',
        ]);

        $this->assertNotSame(
            $oldId,
            session_id(),
            'Session fixation (C3): auth success sonrası session ID değişmeli'
        );
        $this->assertSame('018f00000000000000000000000000cc', $_SESSION['MM_UserID']);
        $this->assertSame('male', $_SESSION['cm_gender']);
        $this->assertArrayHasKey(
            '_session_rotated_at',
            $_SESSION,
            'Lifecycle key\'leri rotasyon sonrası korunmalı (SessionInitializer bunları bekler)'
        );
    }

    public function testValidateAndCreateSession_sourceCallsRotationSeam(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../include/Auth/HomeAuthBridge.php');

        $this->assertNotFalse($source, 'HomeAuthBridge.php okunamadı');
        $this->assertStringContainsString(
            '$this->session->regenerateId()',
            (string) $source,
            'HomeAuthBridge session rotasyonu çağırmalı (C3)'
        );
    }
}
