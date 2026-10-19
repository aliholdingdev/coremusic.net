<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\Admin\Routing\AuthAliasRedirector;
use PHPUnit\Framework\TestCase;

/**
 * G2 (2026-10-19) sözleşmesi: admin host'ta /login ve /register, kernel ÖNCESİ
 * (AuthGuard yetkisinden önce) lokal Figma formlarına 302 ile yönlenir; auth
 * domain redirect'i YOKTUR.
 *
 * Doğrudan production sınıfını (AuthAliasRedirector) çağırır — bootstrap bunu
 * header('Location: ...') + exit ile uygular. AuthGuard::checkAuthRedirectRoute
 * shared AuthRouteConfig ile bu URI'leri auth.coremusic.net'e atardı; bu alias
 * o çakışmayı kernel öncesi çözer.
 */
final class AdminLoginAliasRedirectTest extends TestCase
{
    public function testLoginAliasesToAdminLoginForm(): void
    {
        $result = AuthAliasRedirector::resolve('/login');
        self::assertNotNull($result);
        self::assertSame(302, $result['status']);
        self::assertSame('/admin-login', $result['location']);
    }

    public function testRegisterAliasesToAdminRegisterForm(): void
    {
        $result = AuthAliasRedirector::resolve('/register');
        self::assertNotNull($result);
        self::assertSame(302, $result['status']);
        self::assertSame('/admin-register', $result['location']);
    }

    public function testQueryStringIsPreserved(): void
    {
        $result = AuthAliasRedirector::resolve('/login', 'error=invalid_key');
        self::assertNotNull($result);
        self::assertSame('/admin-login?error=invalid_key', $result['location']);
    }

    public function testTrailingSlashIsNormalized(): void
    {
        $result = AuthAliasRedirector::resolve('/login/');
        self::assertNotNull($result);
        self::assertSame('/admin-login', $result['location']);
    }

    public function testNonAliasRoutesReturnNull(): void
    {
        // Korunması gereken rota kümesi — bunlar ASLA alias'lanmamalı.
        self::assertNull(AuthAliasRedirector::resolve('/admin-dashboard'));
        self::assertNull(AuthAliasRedirector::resolve('/admin-login'));
        self::assertNull(AuthAliasRedirector::resolve('/admin-register'));
        self::assertNull(AuthAliasRedirector::resolve('/auth/callback'));
        self::assertNull(AuthAliasRedirector::resolve('/health'));
        self::assertNull(AuthAliasRedirector::resolve('/'));
    }
}
