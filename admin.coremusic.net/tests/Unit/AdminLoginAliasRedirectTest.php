<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\Admin\Routing\AuthAliasRedirector;
use PHPUnit\Framework\TestCase;

/**
 * 2026-10-21 sözleşmesi: admin host'ta /login, /register ve eski kanonik
 * /admin-login, /admin-register, kernel ÖNCESİ (AuthGuard yetkisinden önce)
 * auth domain'deki admin formlarına 302 ile yönlenir
 * (auth.coremusic.net/admin/* — ADR-043; lokal formlar kaldırıldı).
 *
 * Doğrudan production sınıfını (AuthAliasRedirector) çağırır — bootstrap bunu
 * header('Location: ...') + exit ile uygular. Query string korunur.
 */
final class AdminLoginAliasRedirectTest extends TestCase
{
    private const AUTH_BASE = 'http://auth.coremusic.net';

    public function testLoginAliasesToAuthDomainAdminLogin(): void
    {
        $result = AuthAliasRedirector::resolve('/login', self::AUTH_BASE);
        self::assertNotNull($result);
        self::assertSame(302, $result['status']);
        self::assertSame(self::AUTH_BASE . '/admin/login', $result['location']);
    }

    public function testRegisterAliasesToAuthDomainAdminRegister(): void
    {
        $result = AuthAliasRedirector::resolve('/register', self::AUTH_BASE);
        self::assertNotNull($result);
        self::assertSame(302, $result['status']);
        self::assertSame(self::AUTH_BASE . '/admin/register', $result['location']);
    }

    public function testLegacyCanonicalPathsKeepWorking(): void
    {
        // Eski linkler/bookmarks: /admin-login → auth /admin/login
        $result = AuthAliasRedirector::resolve('/admin-login', self::AUTH_BASE);
        self::assertNotNull($result);
        self::assertSame(self::AUTH_BASE . '/admin/login', $result['location']);

        $result = AuthAliasRedirector::resolve('/admin-register', self::AUTH_BASE);
        self::assertNotNull($result);
        self::assertSame(self::AUTH_BASE . '/admin/register', $result['location']);
    }

    public function testQueryStringIsPreserved(): void
    {
        $result = AuthAliasRedirector::resolve('/login', self::AUTH_BASE, 'error=invalid_key');
        self::assertNotNull($result);
        self::assertSame(self::AUTH_BASE . '/admin/login?error=invalid_key', $result['location']);
    }

    public function testTrailingSlashIsNormalized(): void
    {
        $result = AuthAliasRedirector::resolve('/login/', self::AUTH_BASE);
        self::assertNotNull($result);
        self::assertSame(self::AUTH_BASE . '/admin/login', $result['location']);
    }

    public function testNonAliasRoutesReturnNull(): void
    {
        // Korunması gereken rota kümesi — bunlar ASLA alias'lanmamalı.
        self::assertNull(AuthAliasRedirector::resolve('/admin-dashboard', self::AUTH_BASE));
        self::assertNull(AuthAliasRedirector::resolve('/auth/callback', self::AUTH_BASE));
        self::assertNull(AuthAliasRedirector::resolve('/health', self::AUTH_BASE));
        self::assertNull(AuthAliasRedirector::resolve('/', self::AUTH_BASE));
    }
}
