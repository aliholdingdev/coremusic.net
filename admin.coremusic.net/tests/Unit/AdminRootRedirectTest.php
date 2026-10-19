<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\Admin\Routing\AdminRootRedirector;
use PHPUnit\Framework\TestCase;

/**
 * Admin kök (/) sözleşmesi — 2026-10-19.
 *
 * Shared PageRouter::handleRootUrl() kök isteği home.coremusic.net'e (oturum
 * açık) veya auth login'ine (oturum yok) atıyordu. Admin host'ta bu davranış
 * yanlıştır: kök daima admin içindedir.
 *
 *   - oturum açık → /admin-dashboard
 *   - oturum yok  → /admin-login
 *
 * Doğrudan production sınıfını çağırır — bootstrap sonucu
 * header('Location: ...') + exit ile uygular.
 */
final class AdminRootRedirectTest extends TestCase
{
    public function testAuthenticatedRootGoesToDashboard(): void
    {
        $result = AdminRootRedirector::resolve(true);
        self::assertSame(302, $result['status']);
        self::assertSame('/admin-dashboard', $result['location']);
    }

    public function testUnauthenticatedRootGoesToAdminLogin(): void
    {
        $result = AdminRootRedirector::resolve(false);
        self::assertSame(302, $result['status']);
        self::assertSame('/admin-login', $result['location']);
    }

    public function testRootNeverTargetsHomeOrAuthDomain(): void
    {
        // Regresyon: kök ASLA home.coremusic.net veya auth.coremusic.net'e
        // yönlendirilmemeli (shared handleRootUrl davranışı).
        foreach ([true, false] as $authed) {
            $location = AdminRootRedirector::resolve($authed)['location'];
            self::assertStringStartsWith('/', $location);
            self::assertStringNotContainsString('home', $location);
            self::assertStringNotContainsString('http', $location);
        }
    }
}
