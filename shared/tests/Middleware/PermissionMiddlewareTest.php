<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use CoreMusic\Middleware\PermissionMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * PermissionMiddleware — RBAC + fail-closed (yetkisiz erişim) testleri.
 *
 * Kritik davranış: rota meta'sında requiredRole/requiredPermission VARken
 * auth boşsa istek $next'e değil 401'e düşer. Public rotalar etkilenmez.
 * Sıra değişmez (ADR-010/043 frozen): yalnız gövde davranışı test edilir.
 *
 * @covers \CoreMusic\Middleware\PermissionMiddleware
 */
final class PermissionMiddlewareTest extends TestCase
{
    private PermissionMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new PermissionMiddleware();
        $_SERVER['REQUEST_URI'] = '/admin';
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_URI'], $_SERVER['REMOTE_ADDR']);
    }

    /**
     * @param array<string, mixed> $request
     * @return array{0: array<string, mixed>, 1: bool}
     */
    private function runMiddleware(array $request): array
    {
        $nextCalled = false;
        $result = $this->middleware->handle($request, function () use (&$nextCalled): array {
            $nextCalled = true;

            return ['httpStatus' => 200];
        });

        return [$result, $nextCalled];
    }

    public function testUnauthenticatedRequestWithRequiredRoleReturns401(): void
    {
        [$result, $nextCalled] = $this->runMiddleware([
            '_route_meta' => ['requiredRole' => 'admin'],
            '_auth'       => [],
        ]);

        $this->assertFalse($nextCalled, 'Fail-closed: $next çağrılmamalı');
        $this->assertSame(401, $result['httpStatus']);
        $this->assertSame('json', $result['type']);
        $this->assertTrue($result['halt']);
        $this->assertSame('unauthenticated', $result['body']['error']);
    }

    public function testUnauthenticatedRequestWithRequiredPermissionReturns401(): void
    {
        [$result, $nextCalled] = $this->runMiddleware([
            '_route_meta' => ['requiredPermission' => 'settings.edit'],
            '_auth'       => [],
        ]);

        $this->assertFalse($nextCalled);
        $this->assertSame(401, $result['httpStatus']);
        $this->assertSame('unauthenticated', $result['body']['error']);
    }

    public function testPublicRouteWithoutAuthStillPassesThrough(): void
    {
        [$result, $nextCalled] = $this->runMiddleware([
            '_route_meta' => [],
            '_auth'       => [],
        ]);

        $this->assertTrue($nextCalled, 'Public rotalar davranışını korumalı');
        $this->assertSame(200, $result['httpStatus']);
    }

    public function testAuthenticatedRoleMismatchReturns403(): void
    {
        [$result, $nextCalled] = $this->runMiddleware([
            '_route_meta' => ['requiredRole' => 'admin'],
            '_auth'       => ['userId' => 42, 'role' => 'user'],
        ]);

        $this->assertFalse($nextCalled);
        $this->assertSame(403, $result['httpStatus']);
        $this->assertSame('role_insufficient', $result['body']['error']);
    }

    public function testAuthenticatedWithMatchingRolePassesThrough(): void
    {
        [$result, $nextCalled] = $this->runMiddleware([
            '_route_meta' => ['requiredRole' => 'admin', 'requiredPermission' => 'settings.edit'],
            '_auth'       => ['userId' => 42, 'role' => 'admin', 'permissions' => ['settings.edit']],
        ]);

        $this->assertTrue($nextCalled);
        $this->assertSame(200, $result['httpStatus']);
    }

    public function testAuthenticatedMissingPermissionReturns403(): void
    {
        [$result, $nextCalled] = $this->runMiddleware([
            '_route_meta' => ['requiredPermission' => 'settings.edit'],
            '_auth'       => ['userId' => 42, 'role' => 'admin', 'permissions' => []],
        ]);

        $this->assertFalse($nextCalled);
        $this->assertSame(403, $result['httpStatus']);
        $this->assertSame('permission_denied', $result['body']['error']);
    }
}
