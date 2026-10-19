<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\PageRouter\RouteRegistry;
use CoreMusic\PageRouter\SpaRoute;
use PHPUnit\Framework\TestCase;

/**
 * Admin rota çözümleme testleri — admin-local config/routes.php.
 */
final class RouteResolutionTest extends TestCase
{
    private RouteRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new RouteRegistry();
        $this->registry->loadFromFile(__DIR__ . '/../../config/routes.php');
    }

    public function testRoutesFileLoadsSpaRoutesOnly(): void
    {
        $keys = $this->registry->getRegisteredKeys();

        self::assertContains('login', $keys);
        self::assertContains('register', $keys);
        self::assertContains('dashboard', $keys);
        self::assertContains('songs', $keys);
        self::assertContains('song-add', $keys);
        self::assertContains('playlist', $keys);
        self::assertContains('settings', $keys);
    }

    public function testAdminPagesRequireAuth(): void
    {
        foreach (['dashboard', 'songs', 'song-add', 'playlist', 'settings'] as $key) {
            $route = $this->registry->resolve($key);
            self::assertInstanceOf(SpaRoute::class, $route, $key . ' rotası bulunamadı');
            self::assertTrue($route->requiresAuth, $key . ' auth gerektirmeli');
        }

        $protected = $this->registry->getProtectedRouteKeys();
        self::assertContains('dashboard', $protected);
        self::assertContains('songs', $protected);
    }

    public function testLoginAndRegisterDoNotRequireAuthAndCarryRedirectToAuthDomain(): void
    {
        foreach (['login', 'register'] as $key) {
            $route = $this->registry->resolve($key);
            self::assertInstanceOf(SpaRoute::class, $route, $key . ' rotası bulunamadı');
            self::assertFalse($route->requiresAuth, $key . ' auth gerektirmemeli');
            self::assertArrayHasKey('redirect_to', $route->meta, $key . ' rotasinda redirect_to meta olmali');
            self::assertStringContainsString('auth.coremusic.net', $route->meta['redirect_to']);
            self::assertStringContainsString('client_id=coremusic-web', $route->meta['redirect_to']);
        }

        // Callback URL admin domainine dönmeli
        $login = $this->registry->resolve('login');
        self::assertNotNull($login);
        self::assertStringContainsString('admin.coremusic.net/auth/callback', $login->meta['redirect_to']);
    }

    public function testUnknownRouteResolvesToNull(): void
    {
        self::assertNull($this->registry->resolve('no-such-page'));
    }
}
