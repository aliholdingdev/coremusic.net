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
        self::assertContains('admin-login', $keys);
        self::assertContains('admin-register', $keys);
        self::assertContains('admin-dashboard', $keys);
        self::assertContains('admin-songs', $keys);
        self::assertContains('admin-song-add', $keys);
        self::assertContains('admin-playlist', $keys);
        self::assertContains('admin-settings', $keys);
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

    /**
     * Görev D: admin'in kendi formları (admin-login/admin-register) yönlendirme
     * rotası DEĞİLDİR — redirect_to meta'sı taşımaz; eski login/register
     * redirect sözleşmesi değişmeden kalır (yukarıdaki test korur).
     */
    public function testAdminLoginAndRegisterAreFormPagesWithoutRedirectTo(): void
    {
        foreach (['admin-login' => 'admin-login', 'admin-register' => 'admin-register'] as $key => $page) {
            $route = $this->registry->resolve($key);
            self::assertInstanceOf(SpaRoute::class, $route, $key . ' rotası bulunamadı');
            self::assertFalse($route->requiresAuth, $key . ' auth gerektirmemeli');
            self::assertFalse($route->cacheable, $key . ' cacheable olmamalı (oturum-bağımlı chrome)');
            self::assertSame($page, $route->page, $key . ' sayfası eşleşmeli');
            self::assertArrayNotHasKey('redirect_to', $route->meta, $key . ' yönlendirme rotası değil — redirect_to taşımamalı');
        }

        // Eski login/register hâlâ auth'a yönlendiriyor (bozulmadı)
        $login = $this->registry->resolve('login');
        self::assertNotNull($login);
        self::assertArrayHasKey('redirect_to', $login->meta);
    }
}
