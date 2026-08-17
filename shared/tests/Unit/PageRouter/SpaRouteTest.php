<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\SpaRoute;

final class SpaRouteTest extends TestCase
{
    public function testImmutableDefaults(): void
    {
        $route = new SpaRoute(page: 'home');
        $this->assertSame('home', $route->page);
        $this->assertTrue($route->requiresAuth);
        $this->assertSame('', $route->title);
        $this->assertNull($route->requiredRole);
        $this->assertNull($route->requiredPermission);
        $this->assertTrue($route->cacheable);
        $this->assertSame([], $route->meta);
        $this->assertSame('', $route->path);
        $this->assertNull($route->handler);
        $this->assertNull($route->cacheTtl);
    }

    public function testCustomValues(): void
    {
        $route = new SpaRoute(
            page: 'login',
            requiresAuth: false,
            title: 'Giriş',
            requiredRole: 'admin',
            cacheable: false,
            meta: ['ttlType' => 'static'],
            path: 'login',
            cacheTtl: 3600,
        );
        $this->assertSame('login', $route->page);
        $this->assertFalse($route->requiresAuth);
        $this->assertSame('Giriş', $route->title);
        $this->assertSame('admin', $route->requiredRole);
        $this->assertFalse($route->cacheable);
        $this->assertSame(['ttlType' => 'static'], $route->meta);
        $this->assertSame(3600, $route->cacheTtl);
    }
}
