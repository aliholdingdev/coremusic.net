<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\SpaRoute;
use CoreMusic\PageRouter\AuthGuard;
use CoreMusic\PageRouter\AuthUrlBuilder;
use CoreMusic\PageRouter\PageRouterHelper;
use CoreMusic\Config\DomainConfig;

final class AuthGuardTest extends TestCase
{
    private AuthGuard $guard;
    private PageRouterHelper $helper;

    protected function setUp(): void
    {
        $domainConfig = new DomainConfig();
        $domainConfig->setOverrides('https', 'home.coremusic.net', 443);
        $this->helper = new PageRouterHelper();
        $urlBuilder = new AuthUrlBuilder($domainConfig, $this->helper);
        $this->guard = new AuthGuard($this->helper, $urlBuilder);
    }

    public function testAuthRequiredButNotLoggedInRedirectsToLogin(): void
    {
        $_SESSION = [];
        $route = new SpaRoute(page: 'home', requiresAuth: true);
        $result = $this->guard->check('home', $route, false);
        $this->assertNotNull($result);
        $this->assertSame(302, $result['httpStatus']);
        $this->assertArrayHasKey('Location', $result['headers']);
        $this->assertStringContainsString('auth.coremusic.net', $result['headers']['Location']);
        $this->assertStringContainsString('login', $result['headers']['Location']);
    }

    public function testAuthRequiredButNotLoggedInSpaReturns403(): void
    {
        $_SESSION = [];
        $route = new SpaRoute(page: 'home', requiresAuth: true);
        $result = $this->guard->check('home', $route, true);
        $this->assertNotNull($result);
        $this->assertSame(403, $result['httpStatus']);
        $this->assertArrayHasKey('redirect', $result['body']);
    }

    public function testAuthNotRequiredPassesThrough(): void
    {
        $_SESSION = [];
        // 'kesfet' is NOT an auth redirect route, so guard passes through
        $route = new SpaRoute(page: 'kesfet', requiresAuth: false);
        $result = $this->guard->check('kesfet', $route, false);
        $this->assertNull($result);
    }

    public function testLoginRouteRedirectsToAuthService(): void
    {
        $_SESSION = [];
        // 'login' IS an auth redirect route — guard redirects to auth.coremusic.net
        $route = new SpaRoute(page: 'login', requiresAuth: false);
        $result = $this->guard->check('login', $route, false);
        $this->assertNotNull($result);
        $this->assertSame(302, $result['httpStatus']);
        $this->assertStringContainsString('auth.coremusic.net', $result['headers']['Location']);
    }

    public function testAuthenticatedUserOnLoginPageRedirectsToHome(): void
    {
        $_SESSION = ['MM_UserID' => 42];
        $route = new SpaRoute(page: 'login', requiresAuth: false);
        $result = $this->guard->check('login', $route, false);
        $this->assertNotNull($result);
        $this->assertSame(302, $result['httpStatus']);
        $this->assertSame('/home', $result['headers']['Location']);
    }

    public function testLogoutRedirectsToAuthLogout(): void
    {
        $_SESSION = ['MM_UserID' => 42];
        $route = new SpaRoute(page: 'logout', requiresAuth: false);
        $result = $this->guard->check('logout', $route, false);
        $this->assertNotNull($result);
        $this->assertSame(302, $result['httpStatus']);
        $this->assertStringContainsString('auth.coremusic.net', $result['headers']['Location']);
        $this->assertStringContainsString('logout', $result['headers']['Location']);
    }

    public function testRoleMismatchReturnsForbidden(): void
    {
        $_SESSION = ['MM_UserID' => 42, 'MM_UserRole' => 'user'];
        $route = new SpaRoute(page: 'admin', requiresAuth: true, requiredRole: 'admin');
        $result = $this->guard->check('admin', $route, false);
        $this->assertNotNull($result);
        $this->assertSame(403, $result['httpStatus']);
    }

    public function testRoleMatchPassesThrough(): void
    {
        $_SESSION = ['MM_UserID' => 42, 'MM_UserRole' => 'admin'];
        $route = new SpaRoute(page: 'admin', requiresAuth: true, requiredRole: 'admin');
        $result = $this->guard->check('admin', $route, false);
        $this->assertNull($result);
    }
}
