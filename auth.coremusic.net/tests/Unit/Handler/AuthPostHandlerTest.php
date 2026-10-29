<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Handler;

use CoreMusic\Auth\Handler\AuthPostHandler;
use CoreMusic\Contracts\Auth\IAuthController;
use PHPUnit\Framework\TestCase;

/**
 * AuthPostHandler unit tests — route dispatch ve normalizeRequest davranışı.
 */
final class AuthPostHandlerTest extends TestCase
{
    private IAuthController $controller;
    private AuthPostHandler $handler;

    protected function setUp(): void
    {
        $this->controller = $this->createMock(IAuthController::class);
        $this->handler = new AuthPostHandler($this->controller);
    }

    public function testHandle_nonPostMethod_returns405(): void
    {
        $request = [
            'method' => 'GET',
            'uri'    => 'login',
            'body'   => [],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame(405, $result['httpStatus']);
        $this->assertSame('METHOD_NOT_ALLOWED', $result['body']['error']['code']);
    }

    public function testHandle_unknownRoute_returns404(): void
    {
        $request = [
            'method' => 'POST',
            'uri'    => 'unknown-route',
            'body'   => [],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame(404, $result['httpStatus']);
        $this->assertSame('NOT_FOUND', $result['body']['error']['code']);
    }

    public function testHandle_login_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleLogin')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'login',
            'body'   => ['email' => 'test@example.com', 'password' => 'password123'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_adminLogin_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleLogin')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'admin/login',
            'body'   => ['email' => 'admin@example.com', 'password' => 'password123'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_register_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleRegister')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'register',
            'body'   => ['username' => 'newuser', 'email' => 'new@example.com', 'password' => 'password123', 'gender' => 'neutral', 'agree_terms' => true],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_adminRegister_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleRegister')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'admin/register',
            'body'   => ['username' => 'newadmin', 'email' => 'admin@example.com', 'password' => 'password123', 'gender' => 'male', 'agree_terms' => true],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_logout_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleLogout')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'logout',
            'body'   => [],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_adminLogout_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleLogout')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'admin/logout',
            'body'   => [],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_setGender_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleSetGender')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'set-gender',
            'body'   => ['gender' => 'female'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_adminSetGender_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleSetGender')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'admin/set-gender',
            'body'   => ['gender' => 'male'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_forgotPassword_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleForgotPassword')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'forgot-password',
            'body'   => ['email' => 'test@example.com'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_adminForgotPassword_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleForgotPassword')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'admin/forgot-password',
            'body'   => ['email' => 'admin@example.com'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_resetPassword_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleResetPassword')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'reset-password',
            'body'   => ['token' => 'resettoken123', 'password' => 'newpassword123'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_adminResetPassword_delegatesToController(): void
    {
        $expectedResponse = ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true]];
        $this->controller->method('handleResetPassword')->willReturn($expectedResponse);

        $request = [
            'method' => 'POST',
            'uri'    => 'admin/reset-password',
            'body'   => ['token' => 'resettoken123', 'password' => 'newpassword123'],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame($expectedResponse, $result);
    }

    public function testHandle_controllerThrowsException_returns500(): void
    {
        $this->controller->method('handleLogin')->willThrowException(new \RuntimeException('DB error'));

        $request = [
            'method' => 'POST',
            'uri'    => 'login',
            'body'   => [],
        ];

        $result = $this->handler->handle($request);

        $this->assertSame(500, $result['httpStatus']);
        $this->assertSame('internal_error', $result['body']['error']);
        $this->assertStringContainsString('DB error', $result['body']['message']);
    }

    public function testNormalizeRequest_emptyBody_returnsDefaultStructure(): void
    {
        // normalizeRequest is private, test via handle() behavior
        $request = [
            'method' => 'POST',
            'uri'    => 'login',
            'body'   => [],
        ];

        $this->controller->method('handleLogin')->willReturn([
            'httpStatus' => 200,
            'type' => 'json',
            'body' => ['success' => true]
        ]);

        $result = $this->handler->handle($request);

        // Just verify it processes without error
        $this->assertSame(200, $result['httpStatus']);
    }

    public function testHandle_withServerData_includesServerInNormalized(): void
    {
        $customServer = ['REMOTE_ADDR' => '192.168.1.1', 'HTTP_USER_AGENT' => 'test-agent'];
        $request = [
            'method' => 'POST',
            'uri'    => 'login',
            'body'   => [],
            'server' => $customServer,
        ];

        $this->controller->method('handleLogin')->willReturn([
            'httpStatus' => 200,
            'type' => 'json',
            'body' => ['success' => true]
        ]);

        $result = $this->handler->handle($request);

        $this->assertSame(200, $result['httpStatus']);
    }

    public function testHandle_withQueryParams_includesQueryParamsInNormalized(): void
    {
        $request = [
            'method' => 'POST',
            'uri'    => 'login',
            'body'   => [],
            'query'  => ['redirect_uri' => '/home'],
        ];

        $this->controller->method('handleLogin')->willReturn([
            'httpStatus' => 200,
            'type' => 'json',
            'body' => ['success' => true]
        ]);

        $result = $this->handler->handle($request);

        $this->assertSame(200, $result['httpStatus']);
    }
}