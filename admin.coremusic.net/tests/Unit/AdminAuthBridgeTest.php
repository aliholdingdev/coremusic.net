<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\Admin\Auth\AdminAuthBridge;
use PHPUnit\Framework\TestCase;

/**
 * AdminAuthBridge — boş-key davranışı (ağ çağrısı YOK: empty_key erken döner).
 */
final class AdminAuthBridgeTest extends TestCase
{
    public function testEmptyKeyReturnsErrorWithoutNetworkCall(): void
    {
        // authUrl bilerek geçersiz — empty_key dalı ağ katmanına hiç inmemeli
        $bridge = new AdminAuthBridge('http://127.0.0.1:9');

        $result = $bridge->validateAndCreateSession('');

        self::assertFalse($result['success']);
        self::assertSame('empty_key', $result['error']);
        self::assertArrayNotHasKey('user', $result);
    }

    public function testCurrentUserReturnsNullWhenNoSessionUser(): void
    {
        $bridge = new AdminAuthBridge('http://127.0.0.1:9');

        // Session başlatılmadı / MM_UserID yok → unauthenticated
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        unset($_SESSION['MM_UserID']);

        self::assertFalse($bridge->isAuthenticated());
        self::assertNull($bridge->getCurrentUser());
    }

    public function testEstablishSessionSetsExpectedKeysAndAuthenticatesUser(): void
    {
        $bridge = new AdminAuthBridge('http://127.0.0.1:9');

        $userInfo = [
            'id'           => 'adm-uuid-001',
            'username'     => 'superadmin',
            'email'        => 'admin@coremusic.net',
            'display_name' => 'Super Administrator',
            'account_type' => 'admin',
            'avatar_url'   => 'https://assets.coremusic.net/avatar.png',
            'gender'       => 'male',
        ];

        $bridge->establishSession($userInfo);

        self::assertTrue($bridge->isAuthenticated());
        self::assertSame('adm-uuid-001', $_SESSION['MM_UserID']);
        self::assertSame('superadmin', $_SESSION['MM_Username']);
        self::assertSame('admin@coremusic.net', $_SESSION['MM_Email']);
        self::assertSame('admin', $_SESSION['MM_AccountType']);
        self::assertArrayHasKey('_session_created_at', $_SESSION);
        self::assertArrayHasKey('_session_last_active', $_SESSION);
        self::assertArrayHasKey('last_activity', $_SESSION);

        $current = $bridge->getCurrentUser();
        self::assertNotNull($current);
        self::assertSame('adm-uuid-001', $current['id']);
        self::assertSame('superadmin', $current['username']);
        self::assertSame('admin@coremusic.net', $current['email']);
        self::assertSame('Super Administrator', $current['display_name']);
    }
}
