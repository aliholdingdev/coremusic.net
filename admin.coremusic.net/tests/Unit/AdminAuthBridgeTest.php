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
}
