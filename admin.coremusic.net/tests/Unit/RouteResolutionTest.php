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

    public function testLoginAndRegisterAreLocalAliasesToAdminForms(): void
    {
        // G2 (2026-10-19): /login ve /register artık admin'in lokal Figma
        // formlarına bağlanır (auth domain redirect meta'sı YOK — bootstrap'te
        // kernel öncesi302 ile /admin-login, /admin-register'e yönlenir).
        foreach (['login' => 'admin-login', 'register' => 'admin-register'] as $key => $page) {
            $route = $this->registry->resolve($key);
            self::assertInstanceOf(SpaRoute::class, $route, $key . ' rotası bulunamadı');
            self::assertFalse($route->requiresAuth, $key . ' auth gerektirmemeli');
            self::assertFalse($route->cacheable, $key . ' cacheable olmamalı (oturum-bağımlı chrome)');
            self::assertSame($page, $route->page, $key . ' lokal form sayfasına bağlanmalı');
            self::assertArrayNotHasKey('redirect_to', $route->meta, $key . ' auth domain redirect taşımamalı');
        }
    }

    public function testUnknownRouteResolvesToNull(): void
    {
        self::assertNull($this->registry->resolve('no-such-page'));
    }

    /**
     * G2 (2026-10-19): login/register artık lokal formların takma adıdır;
     * redirect_to meta'sı taşımaz (yukarıdaki test korur).
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
    }
}
