<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Handler;

use CoreMusic\Auth\Handler\AuthKeyRedirectHandler;
use CoreMusic\Auth\Service\SessionManager;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Session\SessionBootstrapper;
use PHPUnit\Framework\TestCase;

/**
 * C1 regresyonu — auth_key doğrulaması sonrası session ID rotasyonu.
 *
 * AuthKeyRedirectHandler::handle() başarı dalında `$session->setAuthUser($user)`
 * çağrılıyor ama session_regenerate_id() yoktu → session fixation:
 * saldırgan kendi session ID'sini kurbanın auth_key akışına taşıyabiliyordu.
 */
final class AuthKeyRedirectHandlerTest extends TestCase
{
    private SessionManager $session;

    protected function setUp(): void
    {
        $_SESSION = [];
        SessionBootstrapper::ensureStarted();
        $this->session = new SessionManager();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        SessionBootstrapper::writeClose();
    }

    public function testEstablishAuthenticatedSession_rotatesSessionId(): void
    {
        $oldId = session_id();
        $this->assertNotSame('', $oldId, 'Test öncesi aktif session bekleniyor');

        $handler = new AuthKeyRedirectHandler(new ConfigManager(), new DomainConfig());
        $handler->establishAuthenticatedSession($this->session, [
            'id'       => '018f00000000000000000000000000aa',
            'username' => 'bayram',
            'email'    => 'bayram@example.test',
            'gender'   => 'female',
        ]);

        $this->assertNotSame(
            $oldId,
            session_id(),
            'Session fixation (C1): auth_key doğrulaması sonrası session ID değişmeli'
        );
        $this->assertSame('018f00000000000000000000000000aa', $_SESSION['MM_UserID']);
        $this->assertSame('female', $_SESSION['cm_gender'], 'Gender rotasyon sonrası da korunmalı');
    }

    public function testEstablishAuthenticatedSession_withoutGender_keepsIdentity(): void
    {
        $handler = new AuthKeyRedirectHandler(new ConfigManager(), new DomainConfig());
        $handler->establishAuthenticatedSession($this->session, [
            'id'       => '018f00000000000000000000000000bb',
            'username' => 'ayse',
            'email'    => 'ayse@example.test',
        ]);

        $this->assertSame('018f00000000000000000000000000bb', $_SESSION['MM_UserID']);
        $this->assertSame('ayse', $_SESSION['MM_Username']);
    }

    public function testHandle_successBranch_wiresSessionRotation(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../include/Handler/AuthKeyRedirectHandler.php');

        $this->assertNotFalse($source, 'AuthKeyRedirectHandler.php okunamadı');
        $this->assertStringContainsString(
            '$this->establishAuthenticatedSession(',
            (string) $source,
            'handle() başarı dalı rotasyon seam\'ine bağlı kalmalı'
        );
    }
}
