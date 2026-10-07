<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use CoreMusic\Middleware\AuthMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * B-F-12 regresyonu — RBAC enjeksiyon zinciri.
 *
 * AuthMiddleware, session'dan gelen MM_UserRole / MM_Permissions değerlerini
 * $request['_auth'] alanına aktarmalıdır; PermissionMiddleware (pipeline #9)
 * tam olarak bu alanları okur. Rol yoksa 'user', izin yoksa [] (fail-closed).
 */
final class AuthMiddlewareTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $captured = [];

    protected function tearDown(): void
    {
        $_SESSION = [];
        $this->captured = [];
    }

    /** @param array<string, mixed> $session */
    private function authFor(array $session): array
    {
        $_SESSION = $session;
        $this->captured = [];

        (new AuthMiddleware())->handle(
            ['_session' => $session],
            function (array $request): array {
                $this->captured = $request;
                return $request;
            }
        );

        return $this->captured['_auth'] ?? [];
    }

    public function testInjectsRoleAndPermissionsFromSession(): void
    {
        $auth = $this->authFor([
            'MM_UserID'      => '018f00000000000000000000000000aa',
            'MM_Username'    => 'bayram',
            'MM_UserRole'    => 'admin',
            'MM_Permissions' => ['library.manage', 'user.delete'],
        ]);

        $this->assertSame('018f00000000000000000000000000aa', $auth['userId']);
        $this->assertSame('admin', $auth['role']);
        $this->assertSame('bayram', $auth['username']);
        $this->assertSame(['library.manage', 'user.delete'], $auth['permissions']);
    }

    public function testMissingRole_defaultsToUserAndEmptyPermissions(): void
    {
        $auth = $this->authFor([
            'MM_UserID'   => '018f00000000000000000000000000aa',
            'MM_Username' => 'bayram',
        ]);

        $this->assertSame('user', $auth['role'], 'Rol yoksa fail-safe default: user');
        $this->assertSame([], $auth['permissions'], 'İzin yoksa fail-closed: []');
    }

    public function testNonArrayPermissions_becomeEmptyArray(): void
    {
        $auth = $this->authFor([
            'MM_UserID'      => '018f00000000000000000000000000aa',
            'MM_UserRole'    => 'admin',
            'MM_Permissions' => 'corrupted-string',
        ]);

        $this->assertSame([], $auth['permissions'], 'Bozuk izin verisi asla string olarak geçmemeli');
    }

    public function testNoUserId_leavesAuthEmpty(): void
    {
        $auth = $this->authFor(['MM_Username' => 'anonymous']);

        $this->assertSame([], $auth, 'User yoksa _auth dolmamalı (PermissionMiddleware pasif kalır)');
    }
}
