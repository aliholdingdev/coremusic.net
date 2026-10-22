<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\Admin\Routing\AdminRootRedirector;
use PHPUnit\Framework\TestCase;

/**
 * Admin kök (/) sözleşmesi — 2026-10-19, revize 2026-10-21.
 *
 * Shared PageRouter::handleRootUrl() kök isteği home.coremusic.net'e (oturum
 * açık) veya auth login'ine (oturum yok) atıyordu. Admin host'ta bu davranış
 * yanlıştır: kök daima admin içindedir.
 *
 *   - oturum açık → /admin-dashboard
 *   - oturum yok  → auth.coremusic.net/admin/login (2026-10-21: lokal
 *                   /admin-login kaldırıldı — formlar auth subdomain'de)
 *
 * Doğrudan production sınıfını çağırır — bootstrap sonucu
 * header('Location: ...') + exit ile uygular.
 */
final class AdminRootRedirectTest extends TestCase
{
    private const AUTH_LOGIN = 'http://auth.coremusic.net/admin/login';

    public function testAuthenticatedRootGoesToDashboard(): void
    {
        $result = AdminRootRedirector::resolve(true, self::AUTH_LOGIN);
        self::assertSame(302, $result['status']);
        self::assertSame('/admin-dashboard', $result['location']);
    }

    public function testUnauthenticatedRootGoesToAuthDomainAdminLogin(): void
    {
        $result = AdminRootRedirector::resolve(false, self::AUTH_LOGIN);
        self::assertSame(302, $result['status']);
        self::assertSame(self::AUTH_LOGIN, $result['location']);
    }

    public function testRootNeverTargetsHomeDomain(): void
    {
        // Regresyon: kök ASLA home.coremusic.net'e yönlendirilmemeli
        // (shared handleRootUrl davranışı). Auth domain'e yönlendirme
        // 2026-10-21 kararıyla ISTENEN davranıştır.
        foreach ([true, false] as $authed) {
            $location = AdminRootRedirector::resolve($authed, self::AUTH_LOGIN)['location'];
            self::assertStringNotContainsString('home', $location);
        }
    }
}
