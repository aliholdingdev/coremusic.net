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

        self::assertContains('auth/callback', $keys);
        self::assertContains('admin-dashboard', $keys);
        self::assertContains('admin-songs', $keys);
        self::assertContains('admin-song-add', $keys);
        self::assertContains('admin-playlist', $keys);
        self::assertContains('admin-settings', $keys);
        self::assertNotContains('login', $keys);
        self::assertNotContains('admin-login', $keys);
    }

    public function testAdminPagesRequireAuth(): void
    {
        foreach (['admin-dashboard', 'admin-songs', 'admin-song-add', 'admin-playlist', 'admin-settings'] as $key) {
            $route = $this->registry->resolve($key);
            self::assertInstanceOf(SpaRoute::class, $route, $key . ' rotası bulunamadı');
            self::assertTrue($route->requiresAuth, $key . ' auth gerektirmeli');
        }

        $protected = $this->registry->getProtectedRouteKeys();
        self::assertContains('admin-dashboard', $protected);
        self::assertContains('admin-songs', $protected);
    }

    public function testAuthCallbackRouteDoesNotRequireAuth(): void
    {
        $route = $this->registry->resolve('auth/callback');
        self::assertInstanceOf(SpaRoute::class, $route, 'auth/callback rotası bulunamadı');
        self::assertFalse($route->requiresAuth, 'auth/callback auth gerektirmemeli');
    }

    public function testUnknownRouteResolvesToNull(): void
    {
        self::assertNull($this->registry->resolve('no-such-page'));
    }
}
