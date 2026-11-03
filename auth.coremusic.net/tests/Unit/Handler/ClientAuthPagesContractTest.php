<?php declare(strict_types=1);

namespace Tests\Unit\Handler;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\SpaRoute;

final class ClientAuthPagesContractTest extends TestCase
{
    private string $pagesDir;
    private string $routesFile;

    protected function setUp(): void
    {
        $this->pagesDir   = dirname(__DIR__, 3) . '/pages';
        $this->routesFile = dirname(__DIR__, 4) . '/shared/config/auth-routes.php';
    }

    public function testClientPagesExist(): void
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

    public function testClientPagesContainCsrfAndSecurityStructures(): void
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
            $this->assertStringContainsString('csrf', $content, "{$page} CSRF güvenliği taşımalı");
        }
    }

    public function testRoutesFileContainsClientRoutes(): void
    {
        $this->assertFileExists($this->routesFile);
        /** @var array<string, SpaRoute> $routes */
        $routes = require $this->routesFile;

        $expectedRoutes = [
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'select-gender',
            'set-gender',
            'logout',
        ];

        foreach ($expectedRoutes as $routeKey) {
            $this->assertArrayHasKey($routeKey, $routes, "Rota tanımı eksik: {$routeKey}");
            $route = $routes[$routeKey];
            $this->assertInstanceOf(SpaRoute::class, $route);
        }
    }
}
