<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\SpaRoute;
use CoreMusic\PageRouter\RouteRegistry;

final class RouteRegistryTest extends TestCase
{
    public function testResolveStaticRoute(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'home', path: 'home');
        $registry->register($route);

        $resolved = $registry->resolve('home');
        $this->assertNotNull($resolved);
        $this->assertSame('home', $resolved->page);
    }

    public function testResolveRootReturnsHome(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'home', path: 'home');
        $registry->register($route);

        $resolved = $registry->resolve('');
        $this->assertNotNull($resolved);
        $this->assertSame('home', $resolved->page);
    }

    public function testResolveNotFound(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'home', path: 'home');
        $registry->register($route);

        $resolved = $registry->resolve('nonexistent');
        $this->assertNull($resolved);
    }

    public function testResolveParameterizedRoute(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'album', path: 'album/{id}');
        $registry->register($route);

        $resolved = $registry->resolve('album/42');
        $this->assertNotNull($resolved);
        $this->assertSame('album', $resolved->page);
    }

    public function testGetProtectedRouteKeys(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(page: 'home', requiresAuth: true, path: 'home'));
        $registry->register(new SpaRoute(page: 'login', requiresAuth: false, path: 'login'));

        $keys = $registry->getProtectedRouteKeys();
        $this->assertContains('home', $keys);
        $this->assertNotContains('login', $keys);
    }
}
