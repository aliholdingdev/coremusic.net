<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Controller;

use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Log\LoggerFactory;
use PHPUnit\Framework\TestCase;

/**
 * ADR-043 regresyonu — admin logout akışı auth domain /admin/login'e döner;
 * normal akış /login'de kalır. admin/login ile auth/login ayrımı korunur.
 */
final class AuthControllerLogoutRedirectTest extends TestCase
{
    protected function tearDown(): void
    {
        LoggerFactory::reset();
        parent::tearDown();
    }

    public function testNormalLogoutRedirectsToLogin(): void
    {
        $controller = new AuthController(
            $this->createMock(IAuthService::class),
            $this->createMock(ISessionManager::class),
        );

        $result = $controller->handleLogout(['uri' => 'logout']);

        $this->assertSame(200, $result['httpStatus']);
        $redirect = (string)$result['body']['redirect'];
        $this->assertStringEndsWith('/login', $redirect);
        $this->assertStringNotContainsString('/admin/', $redirect);
    }

    public function testAdminLogoutRedirectsToAdminLogin(): void
    {
        $controller = new AuthController(
            $this->createMock(IAuthService::class),
            $this->createMock(ISessionManager::class),
        );

        $result = $controller->handleLogout(['uri' => 'admin/logout']);

        $this->assertSame(200, $result['httpStatus']);
        $this->assertStringEndsWith('/admin/login', (string)$result['body']['redirect']);
    }
}
