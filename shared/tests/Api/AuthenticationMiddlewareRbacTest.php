<?php
declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Auth\ApiSessionManager;
use CoreMusic\Api\Middleware\AuthenticationMiddleware;
use CoreMusic\Api\Middleware\AuthorizationMiddleware;
use CoreMusic\Security\JwtService;
use PHPUnit\Framework\TestCase;

/**
 * RBAC regresyonu: AuthenticationMiddleware tryAuthenticate() artık
 * `_auth_user` dizisine `roles`/`permissions` ekler — AuthorizationMiddleware
 * requiredRole/requiredPermission kontrolü bu alanlara bakar.
 *
 * Kritik yollar:
 *   - Session dalı: MM_UserRole (tek string) → ['role'], MM_Permissions (array) birebir.
 *   - JWT dalı: claims['roles']/'permissions' normalize edilir.
 *   - Entegrasyon: requiredRole='admin' route'u session admin için geçer (403 değil).
 *
 * JWT keypair'leri commit'li fixture'dır (tests/Fixtures/jwt/).
 */
final class AuthenticationMiddlewareRbacTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
        // Session dalı testlerinde Bearer header bulunmamalı.
        unset($_SERVER['HTTP_AUTHORIZATION']);
        // Public olmayan yol: RBAC uygulanır.
        $_SERVER['REQUEST_URI'] = '/api/v1/user/profile';
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    public function testSessionAuthNormalizesSingleStringRoleAndArrayPermissions(): void
    {
        $session = new ApiSessionManager();
        $session->setAuthUser(['id' => '018f00000000000000000000000000aa']);
        $session->set('MM_UserRole', 'admin');
        $session->set('MM_Permissions', ['user.read', 'user.update']);

        $authUser = $this->invokeMiddleware($session);

        $this->assertIsArray($authUser);
        $this->assertSame('session', $authUser['method']);
        $this->assertSame(['admin'], $authUser['roles'], 'Tek string rol tek elemanlı diziye sarılır');
        $this->assertSame(['user.read', 'user.update'], $authUser['permissions']);
    }

    public function testSessionAuthWithoutRoleOrPermissionsYieldsEmptyLists(): void
    {
        $session = new ApiSessionManager();
        $session->setAuthUser(['id' => '018f00000000000000000000000000aa']);
        // MM_UserRole / MM_Permissions hiç set edilmedi.

        $authUser = $this->invokeMiddleware($session);

        $this->assertIsArray($authUser);
        $this->assertSame([], $authUser['roles']);
        $this->assertSame([], $authUser['permissions']);
    }

    public function testSessionAuthFiltersNonStringPermissions(): void
    {
        $session = new ApiSessionManager();
        $session->setAuthUser(['id' => '018f00000000000000000000000000aa']);
        $session->set('MM_UserRole', 'user');
        $session->set('MM_Permissions', ['user.read', 42, null, 'user.write']);

        $authUser = $this->invokeMiddleware($session);

        $this->assertIsArray($authUser);
        $this->assertSame(['user.read', 'user.write'], $authUser['permissions'], 'String olmayan izinler elenir');
    }

    public function testJwtAuthNormalizesRolesAndPermissionsFromClaims(): void
    {
        $jwt = $this->createJwtService();
        $issued = $jwt->issue(
            '018f00000000000000000000000000bb',
            ['roles' => 'admin', 'permissions' => ['media.upload', 7]]
        );
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $issued['token'];

        $session = new ApiSessionManager(); // authenticated değil → JWT dalı
        $authUser = $this->invokeMiddleware($session, $jwt);

        $this->assertIsArray($authUser);
        $this->assertSame('jwt', $authUser['method']);
        $this->assertSame(['admin'], $authUser['roles'], 'String claim tek elemanlı diziye sarılır');
        $this->assertSame(['media.upload'], $authUser['permissions'], 'Array claimde yalnız string elemanlar kalır');
    }

    /**
     * Asıl bug regresyonu: requiredRole tanımlı route, session admin için
     * AuthorizationMiddleware'den GEÇER (403 değil); admin olmayan 403 alır.
     */
    public function testRequiredRoleRoutePassesForSessionAdminAndRejectsNonAdmin(): void
    {
        $route = ['requiredRole' => 'admin', 'public' => false];

        // Admin: AuthorizationMiddleware $next'e geçer.
        $adminSession = new ApiSessionManager();
        $adminSession->setAuthUser(['id' => '018f00000000000000000000000000aa']);
        $adminSession->set('MM_UserRole', 'admin');

        $authUser = $this->invokeMiddleware($adminSession);
        $this->assertIsArray($authUser);

        $nextCalled = false;
        $response = (new AuthorizationMiddleware())(
            ['_auth_user' => $authUser, '_route' => $route],
            static function () use (&$nextCalled): array {
                $nextCalled = true;

                return ['ok' => true];
            }
        );
        $this->assertTrue($nextCalled, 'Session admin requiredRole=admin route\'undan geçmeli');
        $this->assertSame(['ok' => true], $response);

        // Non-admin: 403 FORBIDDEN (fail-closed korunur).
        $userSession = new ApiSessionManager();
        $userSession->setAuthUser(['id' => '018f00000000000000000000000000cc']);
        $userSession->set('MM_UserRole', 'user');

        $userAuth = $this->invokeMiddleware($userSession);
        $this->assertIsArray($userAuth);

        $forbidden = (new AuthorizationMiddleware())(
            ['_auth_user' => $userAuth, '_route' => $route],
            static fn (): array => ['ok' => true]
        );
        $this->assertSame('FORBIDDEN', $forbidden['error']['code'] ?? null);
    }

    /**
     * AuthenticationMiddleware'i non-public route üzerinden çağırır ve
     * $next'e aktarılan `_auth_user`'ı döndürür (auth başarısızsa null).
     *
     * @return array<string, mixed>|null
     */
    private function invokeMiddleware(ApiSessionManager $session, ?JwtService $jwt = null): ?array
    {
        $middleware = new AuthenticationMiddleware($session, $jwt);
        $captured = null;

        $response = $middleware(
            ['_route_status' => null],
            static function (array $request) use (&$captured): array {
                $captured = $request['_auth_user'] ?? null;

                return ['ok' => true];
            }
        );

        if ($response === ['ok' => true]) {
            return $captured;
        }

        return null; // 401 UNAUTHORIZED
    }

    private function createJwtService(): JwtService
    {
        $fixtures = dirname(__DIR__) . '/Fixtures/jwt';

        return new JwtService(
            privateKeyPath: $fixtures . '/test-private.pem',
            publicKeyPath: $fixtures . '/test-public.pem',
            issuer: 'coremusic',
            audience: 'coremusic-api',
            ttlSeconds: 60,
        );
    }
}
