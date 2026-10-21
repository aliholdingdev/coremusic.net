<?php declare(strict_types=1);

namespace Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\SpaRoute;

final class AdminAuthPagesContractTest extends TestCase
{
    private string $pagesDir;
    private string $routesFile;

    protected function setUp(): void
    {
        $this->pagesDir   = dirname(__DIR__, 3) . '/pages/admin';
        $this->routesFile = dirname(__DIR__, 4) . '/shared/config/auth-routes.php';
    }

    public function testAdminPagesExist(): void
    {
        $expectedPages = [
            'login.php',
            'register.php',
            'forgot-password.php',
            'reset-password.php',
            'select-gender.php',
            'set-gender.php',
            'logout.php',
        ];

        foreach ($expectedPages as $page) {
            $path = $this->pagesDir . '/' . $page;
            $this->assertFileExists($path, "Sayfa bulunamadı: {$page}");
        }
    }

    public function testAdminPagesContainLightThemeAndSecurityFields(): void
    {
        $pagesToCheck = [
            'login.php',
            'register.php',
            'forgot-password.php',
            'reset-password.php',
            'select-gender.php',
            'set-gender.php',
        ];

        foreach ($pagesToCheck as $page) {
            $content = (string)file_get_contents($this->pagesDir . '/' . $page);
            $this->assertStringContainsString('data-mode="light"', $content, "{$page} Light Theme data-mode taşımalı");
            $this->assertStringContainsString('csrf_token', $content, "{$page} CSRF token alanı taşımalı");
            $this->assertStringContainsString('adm-auth-wrapper', $content, "{$page} adm-auth-wrapper sınıfı taşımalı");
            $this->assertStringContainsString('adm-auth-card--light', $content, "{$page} adm-auth-card--light sınıfı taşımalı");
        }
    }

    public function testRoutesFileContainsAdminRoutesWithLightMeta(): void
    {
        $this->assertFileExists($this->routesFile);
        /** @var array<string, SpaRoute> $routes */
        $routes = require $this->routesFile;

        $expectedRoutes = [
            'admin/login',
            'admin/register',
            'admin/forgot-password',
            'admin/reset-password',
            'admin/select-gender',
            'admin/set-gender',
            'admin/logout',
        ];

        foreach ($expectedRoutes as $routeKey) {
            $this->assertArrayHasKey($routeKey, $routes, "Rota tanımı eksik: {$routeKey}");
            $route = $routes[$routeKey];
            $this->assertInstanceOf(SpaRoute::class, $route);
            $this->assertTrue($route->meta['admin'] ?? false, "Rota admin meta bayrağı taşımalı: {$routeKey}");
            $this->assertSame('light', $route->meta['theme'] ?? '', "Rota light tema bayrağı taşımalı: {$routeKey}");
        }
    }
}
