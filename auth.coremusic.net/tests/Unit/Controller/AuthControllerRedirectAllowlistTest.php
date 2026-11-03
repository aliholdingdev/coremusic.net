<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Controller;

use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Log\LoggerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Open-redirect regresyonu — redirect allowlist (ReturnUrlPolicy SSOT).
 *
 * Girdi → AuthController::resolveRedirectUrl (include/Controller/AuthController.php:50)
 * ve handleSetGender (:262) → SecurityHelper::isRedirectUriSafe →
 * shared/src/Security/ReturnUrlPolicy::isAllowed.
 *
 * Kabul: javascript:, protokol-relative (//evil) ve dış domain REDDEDİLİR
 * (default hedefe düşer); göreli path ve beyazlisteli mutlak URL korunur;
 * logout asla kullanıcı girdisine redirect kurmaz.
 */
final class AuthControllerRedirectAllowlistTest extends TestCase
{
    private IAuthService $authService;
    private ISessionManager $session;
    private AuthController $controller;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(IAuthService::class);
        $this->session      = $this->createMock(ISessionManager::class);
        $this->session->method('consumePendingRedirect')->willReturn(null);
        $this->session->method('getGender')->willReturn('neutral');

        $this->controller = new AuthController($this->authService, $this->session);
    }

    protected function tearDown(): void
    {
        LoggerFactory::reset();
        parent::tearDown();
    }

    /** resolveRedirectUrl() güvenlik dışı hedefte neye düşer? (AuthController.php:41,50-52) */
    private function defaultRedirect(): string
    {
        return (defined('AUTH_URL') ? AUTH_URL : '') . '/';
    }

    private function loginRequest(?string $redirectUri): array
    {
        $post = [
            'email'    => 'auditor@example.test',
            'password' => 'password123',
        ];
        if ($redirectUri !== null) {
            $post['redirect_uri'] = $redirectUri;
        }

        return [
            'body'         => $post,
            'server'       => ['REMOTE_ADDR' => '127.0.0.1'],
            'query_params' => $redirectUri === null ? [] : ['redirect_uri' => $redirectUri],
        ];
    }

    private function registerRequest(?string $redirectUri): array
    {
        $post = [
            'username'    => 'auditor',
            'email'       => 'auditor@example.test',
            'password'    => 'password123',
            'gender'      => 'neutral',
            'agree_terms' => 1,
        ];
        if ($redirectUri !== null) {
            $post['redirect_uri'] = $redirectUri;
        }

        return [
            'body'         => $post,
            'server'       => ['REMOTE_ADDR' => '127.0.0.1'],
            'query_params' => $redirectUri === null ? [] : ['redirect_uri' => $redirectUri],
        ];
    }

    public function testLogin_externalAbsoluteUri_fallsBackToDefault(): void
    {
        $this->authService->method('login')->willReturn(['success' => true]);

        $result = $this->controller->handleLogin(
            $this->loginRequest('https://evil.com/steal?auth_key=x')
        );

        $redirect = (string)$result['body']['redirect'];
        $this->assertStringNotContainsString('evil.com', $redirect);
        $this->assertSame($this->defaultRedirect(), $redirect);
    }

    public function testLogin_javascriptScheme_fallsBackToDefault(): void
    {
        $this->authService->method('login')->willReturn(['success' => true]);

        $result = $this->controller->handleLogin($this->loginRequest('javascript:alert(document.cookie)'));

        $this->assertSame($this->defaultRedirect(), (string)$result['body']['redirect']);
    }

    public function testLogin_protocolRelative_fallsBackToDefault(): void
    {
        $this->authService->method('login')->willReturn(['success' => true]);

        $result = $this->controller->handleLogin($this->loginRequest('//evil.com/phish'));

        $redirect = (string)$result['body']['redirect'];
        $this->assertStringNotContainsString('evil.com', $redirect);
        $this->assertSame($this->defaultRedirect(), $redirect);
    }

    public function testLogin_relativePath_isPreserved(): void
    {
        $this->authService->method('login')->willReturn(['success' => true]);

        $result = $this->controller->handleLogin($this->loginRequest('/home'));

        $this->assertSame('/home', (string)$result['body']['redirect']);
    }

    public function testLogin_whitelistedAbsoluteUri_isPreserved(): void
    {
        $this->authService->method('login')->willReturn(['success' => true]);
        $allowed = 'https://home.coremusic.net/auth/callback';

        $result = $this->controller->handleLogin($this->loginRequest($allowed));

        $this->assertSame($allowed, (string)$result['body']['redirect']);
    }

    public function testRegister_usesSameAllowlistAsLogin(): void
    {
        $this->authService->method('register')->willReturn(['success' => true]);

        $result = $this->controller->handleRegister(
            $this->registerRequest('https://evil.com/register-hook')
        );

        $redirect = (string)$result['body']['redirect'];
        $this->assertStringNotContainsString('evil.com', $redirect);
        $this->assertSame($this->defaultRedirect(), $redirect);
    }

    public function testLogout_neverReadsAttackerRedirect(): void
    {
        // Logout hedefi sabittir (AuthController.php:232-237) — redirect_uri /
        // redirect / pendingRedirect girdilerinin hiçbiri okunmaz.
        $result = $this->controller->handleLogout([
            'uri'          => 'logout',
            'query_params' => ['redirect_uri' => 'https://evil.com/after-logout', 'redirect' => '//evil.com'],
            'body'         => ['redirect_uri' => 'https://evil.com/after-logout'],
        ]);

        $redirect = (string)$result['body']['redirect'];

        $this->assertStringEndsWith('/login', $redirect);
        $this->assertStringNotContainsString('evil.com', $redirect);
        $this->assertStringNotContainsString('after-logout', $redirect);
    }
}