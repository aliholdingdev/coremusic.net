<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Controller;

use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Log\LoggerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Session fixation (C1) — oturum kimliği kimlik değiştiren her başarı dalında dönmeli.
 *
 * Kanıt noktaları:
 *   include/Controller/AuthController.php:145  handleLogin   → regenerateId()
 *   include/Controller/AuthController.php:194  handleRegister→ regenerateId()
 *   include/Controller/AuthController.php:226  handleLogout  → regenerateId()
 *   include/Handler/AuthKeyRedirectHandler.php:87 establishAuthenticatedSession → regenerateId()
 *   (auth_key akışı AuthKeyRedirectHandlerTest ile korunur)
 */
final class AuthControllerSessionFixationTest extends TestCase
{
    private ISessionManager $session;
    private AuthController $controller;

    protected function setUp(): void
    {
        $this->session = $this->createMock(ISessionManager::class);
        $this->session->method('consumePendingRedirect')->willReturn(null);
        $this->session->method('getGender')->willReturn('neutral');

        $authService = $this->createMock(IAuthService::class);
        $authService->method('login')->willReturn(['success' => true]);
        $authService->method('register')->willReturn(['success' => true]);

        $this->controller = new AuthController($authService, $this->session);
    }

    protected function tearDown(): void
    {
        LoggerFactory::reset();
        parent::tearDown();
    }

    public function testLogin_success_rotatesSessionId(): void
    {
        $this->session->expects($this->once())->method('regenerateId');

        $this->controller->handleLogin([
            'body'         => ['email' => 'auditor@example.test', 'password' => 'password123'],
            'server'       => ['REMOTE_ADDR' => '127.0.0.1'],
            'query_params' => [],
        ]);
    }

    public function testRegister_success_rotatesSessionId(): void
    {
        $this->session->expects($this->once())->method('regenerateId');

        $this->controller->handleRegister([
            'body' => [
                'username'    => 'auditor',
                'email'       => 'auditor@example.test',
                'password'    => 'password123',
                'gender'      => 'neutral',
                'agree_terms' => 1,
            ],
            'server'       => ['REMOTE_ADDR' => '127.0.0.1'],
            'query_params' => [],
        ]);
    }

    public function testLogout_rotatesSessionIdAfterDestroy(): void
    {
        $this->session->expects($this->once())->method('regenerateId');

        $this->controller->handleLogout(['uri' => 'logout']);
    }

    public function testLogin_failedCredentials_doesNotRotateSessionId(): void
    {
        $authService = $this->createMock(IAuthService::class);
        $authService->method('login')->willThrowException(
            \CoreMusic\Exception\AuthenticationException::invalidCredentials()
        );
        $controller = new AuthController($authService, $this->session);

        $this->session->expects($this->never())->method('regenerateId');

        $result = $controller->handleLogin([
            'body'         => ['email' => 'auditor@example.test', 'password' => 'wrong-password'],
            'server'       => ['REMOTE_ADDR' => '127.0.0.1'],
            'query_params' => [],
        ]);

        $this->assertSame(401, $result['httpStatus']);
    }
}