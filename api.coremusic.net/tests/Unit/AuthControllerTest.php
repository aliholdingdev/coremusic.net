<?php
declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use CoreMusic\Api\Auth\ApiSessionManager;
use CoreMusic\Api\Controller\AuthController;
use CoreMusic\Api\Service\IRedirectService;
use CoreMusic\Api\Service\ITokenService;
use CoreMusic\Api\Service\IUserTransformer;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Faz 1b — AuthController sözleşme çevirisi (Faz 3a UI · ADR-084).
 *
 * Servis istisnası → HTTP kodu + error.code eşlemesi burada kilitlenir:
 *   INVALID_CREDENTIALS 401 · GENDER_MISMATCH 403 · *_TAKEN 409 ·
 *   VALIDATION_ERROR 422 (+error.fields) · RATE_LIMITED 429 ·
 *   TOKEN_INVALID 400 · INTERNAL_ERROR Gateway'den 500.
 *
 * DB GEREKTİRMEZ: IAuthService, ITokenService, IRedirectService, IUserTransformer
 * sahteleri (stub) enjekte edilir.
 */
final class AuthControllerTest extends TestCase
{
    private const MUSIC_URL = 'http://home.coremusic.net';
    private const AUTH_URL  = 'http://auth.coremusic.net';

    /** @var array<string, mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        http_response_code(200);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    /**
     * @param array<string, mixed> $overrides servis davranışı / gövde
     */
    private function controller(array $overrides = [], ?ApiSessionManager $session = null): AuthController
    {
        return new AuthController(
            $this->service($overrides),
            $session ?? new ApiSessionManager(),
            $this->tokenService($overrides),
            $this->redirectService(),
            $this->userTransformer($overrides),
        );
    }

    /**
     * @param array<string, mixed> $overrides servis davranışı
     */
    private function service(array $overrides = []): IAuthService
    {
        return new class ($overrides) implements IAuthService {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array
            {
                $this->maybeThrow('login');
                if (!empty($this->config['login_returns'])) {
                    // F3: closure ise identity'yi test'e geri ver (identity-OR kanıtı)
                    $returns = $this->config['login_returns'];

                    return $returns instanceof \Closure ? $returns($identity, $password) : $returns;
                }

                return [
                    'success'  => true,
                    'redirect' => '/home',
                    'auth_key' => 'ak-123',
                    'user'     => [
                        'id' => 'u1', 'username' => 'demo', 'email' => 'demo@example.com',
                        'gender' => 'female', 'password_hash' => '$argon2id$never',
                    ],
                ];
            }

            public function register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array
            {
                $this->maybeThrow('register');

                return [
                    'success'  => true,
                    'redirect' => '/home',
                    'auth_key' => 'ak-456',
                    'user'     => ['id' => 'u2', 'username' => 'yeni', 'email' => 'yeni@example.com'],
                ];
            }

            public function logout(): void
            {
                $this->maybeThrow('logout');
            }

            public function isAuthenticated(): bool
            {
                return true;
            }

            public function getCurrentUser(): ?array
            {
                $this->maybeThrow('me');

                return array_key_exists('current_user', $this->config) ? $this->config['current_user'] : [
                    'id' => 'u1', 'username' => 'demo', 'email' => 'demo@example.com', 'gender' => 'female',
                ];
            }

            public function requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1'): array
            {
                $this->maybeThrow('forgot');

                return ['success' => true, 'message' => 'Sıfırlama bağlantısı gönderildi.'];
            }

            public function resetPassword(string $token, string $newPassword): array
            {
                $this->maybeThrow('reset');

                return ['success' => true, 'message' => 'Şifreniz güncellendi.'];
            }

            public function validateSessionKey(string $authKey): array
            {
                return [];
            }

            private function maybeThrow(string $action): void
            {
                $throw = $this->config['throw'][$action] ?? null;
                if ($throw instanceof \Throwable) {
                    throw $throw;
                }
            }
        };
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function tokenService(array $overrides = []): ITokenService
    {
        return new class ($overrides) implements ITokenService {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function buildAuthPayload(array $authResult, array $requestBody): array
            {
                return [
                    'access_token' => 'mock-access-token',
                    'token_type'   => 'Bearer',
                    'expires_in'   => 3600,
                ];
            }

            public function revokeUserTokens(string $userId): void
            {
                // no-op
            }

            public function isValidAccessToken(string $jti): bool
            {
                return true;
            }
        };
    }

    private function redirectService(): IRedirectService
    {
        return new class implements IRedirectService {
            public function resolveRedirect(array $requestBody, string $authKey = ''): string
            {
                $candidate = (string) ($requestBody['redirect_uri'] ?? '');
                if ($candidate === '') {
                    $candidate = (string) ($requestBody['redirect'] ?? '');
                }
                if ($candidate === '') {
                    $candidate = (string) ($_GET['redirect_uri'] ?? $_GET['redirect'] ?? '');
                }

                // Login/register actions: authKey provided, use musicUrl/home as fallback
                // setGender: no authKey, use relative /login as fallback
                $isLoginOrRegister = $authKey !== '';
                $fallback = $isLoginOrRegister ? 'http://home.coremusic.net/home' : '/login';

                if ($candidate !== '' && (str_starts_with($candidate, '/') || str_starts_with($candidate, 'http://localhost') || str_starts_with($candidate, 'http://127.0.0.1'))) {
                    $target = $candidate;
                } else {
                    $target = $fallback;
                }

                if ($authKey !== '') {
                    $separator = str_contains($target, '?') ? '&' : '?';
                    $target   .= $separator . 'auth_key=' . urlencode($authKey);
                }

                return $target;
            }

            public function getDefaultFallback(): string
            {
                // logout uses this: should return authUrl so that logout redirect becomes authUrl/login
                return 'http://auth.coremusic.net';
            }
        };
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function userTransformer(array $overrides = []): IUserTransformer
    {
        return new class ($overrides) implements IUserTransformer {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function transform(array $user): array
            {
                $gender = (string) ($user['gender'] ?? 'neutral');

                return [
                    'id'       => (string) ($user['id'] ?? ($user['user_id'] ?? '')),
                    'username' => (string) ($user['username'] ?? ''),
                    'email'    => (string) ($user['email'] ?? ''),
                    'gender'   => $gender !== '' ? $gender : 'neutral',
                ];
            }
        };
    }

    /** @param array<string, mixed> $body */
    private function handle(AuthController $controller, string $action, array $body = []): array
    {
        return $controller->handle(
            ['action' => $action],
            ['server' => ['REMOTE_ADDR' => '127.0.0.1', 'CONTENT_TYPE' => 'application/json'], 'method' => 'POST', 'uri' => '/api/v1/auth/x', 'body' => $body],
        );
    }

    public function testLoginSuccessFollowsContractEnvelope(): void
    {
        $result = $this->handle($this->controller(), 'login', ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123']);

        $this->assertTrue($result['success']);
        $this->assertSame('ak-123', $result['data']['auth_key']);
        $this->assertSame(
            ['id', 'username', 'email', 'gender'],
            array_keys($result['data']['user']),
            'Sözleşme kullanıcı gövdesi: {id,username,email,gender}'
        );
        $this->assertArrayNotHasKey('password_hash', $result['data']['user'], 'Hash asla sızdırılmamalı');
        $this->assertStringStartsWith(self::MUSIC_URL . '/home', $result['data']['redirect']);
        $this->assertStringContainsString('auth_key=ak-123', $result['data']['redirect']);
        $this->assertSame(200, http_response_code());
    }

    public function testLoginWithSafeRedirectUriIsHonoured(): void
    {
        $result = $this->handle($this->controller(), 'login', [
            'email' => 'demo@example.com', 'password' => 'uzun-sifre-123', 'redirect_uri' => '/auth/callback',
        ]);

        $this->assertStringStartsWith('/auth/callback', $result['data']['redirect']);
    }

    public function testUnsafeRedirectUriFallsBackToMusicHome(): void
    {
        $result = $this->handle($this->controller(), 'login', [
            'email' => 'demo@example.com', 'password' => 'uzun-sifre-123', 'redirect_uri' => 'https://evil.example.com/phish',
        ]);

        $this->assertStringStartsWith(self::MUSIC_URL . '/home', $result['data']['redirect']);
    }

    public function testInvalidCredentialsMapsTo401(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['login' => AuthenticationException::invalidCredentials()],
        ]), 'login', ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123']);

        $this->assertSame(401, http_response_code());
        $this->assertSame('INVALID_CREDENTIALS', $result['error']['code'] ?? null);
    }

    public function testGenderMismatchMapsTo403(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['login' => AuthenticationException::genderMismatch('female')],
        ]), 'login', ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123']);

        $this->assertSame(403, http_response_code(), 'GENDER_MISMATCH sözleşmede 403');
        $this->assertSame('GENDER_MISMATCH', $result['error']['code'] ?? null);
    }

    public function testDuplicateEmailMapsTo409EmailTaken(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['register' => ConflictException::emailAlreadyExists()],
        ]), 'register', ['email' => 'demo@example.com']);

        $this->assertSame(409, http_response_code());
        $this->assertSame('EMAIL_TAKEN', $result['error']['code'] ?? null);
    }

    public function testDuplicateUsernameMapsTo409UsernameTaken(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['register' => ConflictException::usernameAlreadyExists()],
        ]), 'register', ['username' => 'demo']);

        $this->assertSame(409, http_response_code());
        $this->assertSame('USERNAME_TAKEN', $result['error']['code'] ?? null);
    }

    public function testPasswordTooShortMapsTo422WithErrorFields(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['register' => ValidationException::passwordTooShort(8)],
        ]), 'register', ['password' => 'kisa']);

        $this->assertSame(422, http_response_code());
        $this->assertSame('VALIDATION_ERROR', $result['error']['code'] ?? null);
        $this->assertArrayHasKey('fields', $result['error'], 'Faz 3a UI error.fields okur');
        $this->assertArrayHasKey('password', $result['error']['fields']);
    }

    public function testMultipleValidationErrorsExposeEachField(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['register' => ValidationException::multiple(['username' => 'Geçersiz.', 'agree_terms' => 'Koşulları kabul etmelisiniz.'])],
        ]), 'register', []);

        $this->assertSame(422, http_response_code());
        $this->assertSame(['username', 'agree_terms'], array_keys($result['error']['fields']));
    }

    public function testResetWithExpiredTokenMapsTo400TokenInvalid(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['reset' => ValidationException::tokenExpired()],
        ]), 'resetPassword', ['token' => 'bad', 'password' => 'yeni-sifre-1234']);

        $this->assertSame(400, http_response_code());
        $this->assertSame('TOKEN_INVALID', $result['error']['code'] ?? null);
    }

    public function testRateLimitMapsTo429RateLimited(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['login' => RateLimitException::loginRateLimited(900)],
        ]), 'login', ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123']);

        $this->assertSame(429, http_response_code());
        $this->assertSame('RATE_LIMITED', $result['error']['code'] ?? null);
    }

    public function testGenericThrowableIsRethrownToGatewayFor500(): void
    {
        // PDO/beklenmeyen istisna controller'da YAKLANMAZ → Gateway dispatch
        // catch'i 500 INTERNAL_ERROR döner (shared ApiPipelineSecurityTest kilitler).
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('db down');

        $this->handle($this->controller([
            'throw' => ['login' => new \RuntimeException('db down')],
        ]), 'login', ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123']);
    }

    public function testForgotPasswordAlwaysReturns200WithContractMessage(): void
    {
        $result = $this->handle($this->controller(), 'forgotPassword', ['email' => 'yok@example.com']);

        $this->assertSame(200, http_response_code());
        $this->assertTrue($result['success']);
        $this->assertSame('Sıfırlama bağlantısı gönderildi.', $result['message']);
        $this->assertArrayNotHasKey('data', $result, 'Enumaration koruması: gövde sadece success+message');
    }

    public function testResetPasswordReturnsContractMessage(): void
    {
        $result = $this->handle($this->controller(), 'resetPassword', ['token' => 'ok', 'password' => 'yeni-sifre-1234']);

        $this->assertSame(200, http_response_code());
        $this->assertSame('Şifreniz sıfırlandı.', $result['message']);
    }

    public function testLogoutReturnsRedirect(): void
    {
        $result = $this->handle($this->controller(), 'logout');

        $this->assertTrue($result['success']);
        $this->assertSame(self::AUTH_URL . '/login', $result['data']['redirect']);
    }

    public function testMeReturnsContractUserShape(): void
    {
        $result = $this->handle($this->controller(), 'me');

        $this->assertSame(['id', 'username', 'email', 'gender'], array_keys($result['data']['user']));
    }

    public function testMeWithoutUserMapsTo401(): void
    {
        $result = $this->handle($this->controller(['current_user' => null]), 'me');

        $this->assertSame(401, http_response_code());
        $this->assertSame('UNAUTHORIZED', $result['error']['code'] ?? null);
    }

    public function testSetGenderWritesSessionAndRedirects(): void
    {
        $session = new ApiSessionManager();

        $result = $this->handle($this->controller([], $session), 'setGender', ['gender' => 'female']);

        $this->assertTrue($result['success']);
        $this->assertSame('female', $result['data']['gender']);
        $this->assertSame('female', $session->getGender(), 'cm_gender session\'e yazılmalı (gender gate)');
        $this->assertSame('/login', $result['data']['redirect']);
    }

    /** Faz 3: hibrit CSRF dağıtımı — login/me data.csrf_token. */
    public function testLoginAndMeExposeCsrfTokenFromSession(): void
    {
        $session = new ApiSessionManager();
        $session->set('csrf_token', 'csrf-test-token');

        $login = $this->handle(
            $this->controller([], $session),
            'login',
            ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123'],
        );
        $this->assertSame(
            'csrf-test-token',
            $login['data']['csrf_token'] ?? null,
            'Faz 3: login data.csrf_token dağıtılır (SessionLifecycle kaynağı)'
        );

        $me = $this->handle($this->controller([], $session), 'me');
        $this->assertSame(
            'csrf-test-token',
            $me['data']['csrf_token'] ?? null,
            'Faz 3: me data.csrf_token dağıtılır'
        );
    }

    /** Faz 3: token yoksa csrf_token boş string (sözleşme alanı yine mevcut). */
    public function testLoginExposesEmptyCsrfTokenWhenSessionHasNone(): void
    {
        $result = $this->handle(
            $this->controller([], new ApiSessionManager()),
            'login',
            ['email' => 'demo@example.com', 'password' => 'uzun-sifre-123'],
        );

        $this->assertArrayHasKey('csrf_token', $result['data']);
        $this->assertSame('', $result['data']['csrf_token']);
    }

    // ─── F3: identity-OR login — email OLMADAN da giriş ───

    /**
     * AuthController::login `$body['email'] ?? ['identity'] ?? ['username']` okur.
     * route'da email required kalktıktan sonra username-only gövde servise
     * identity olarak username ile ulaşmalıdır (422 OLMAMALI).
     */
    public function testUsernameOnlyLoginPassesIdentityToService(): void
    {
        $seenIdentity = null;

        $result = $this->handle($this->controller([
            'login_returns' => static function (string $identity, string $password) use (&$seenIdentity): array {
                $seenIdentity = $identity;

                return [
                    'success'  => true,
                    'redirect' => '/home',
                    'auth_key' => 'ak-789',
                    'user'     => [
                        'id' => 'u9', 'username' => 'demo', 'email' => 'demo@example.com',
                        'gender' => 'neutral', 'password_hash' => '$argon2id$never',
                    ],
                ];
            },
        ]), 'login', ['username' => 'demo', 'password' => 'uzun-sifre-123']);

        $this->assertSame('demo', $seenIdentity, 'identity = username (email gövdede yok)');
        $this->assertTrue($result['success']);
        $this->assertSame(200, http_response_code());
        $this->assertArrayNotHasKey('error', $result, 'username-only login 422 üretmemeli');
    }

    /**
     * identity-OR kaybı YOK: gövdede email/identity/username YOKSA AuthService
     * (real: AuthService::login → ValidationException::emptyFields()) 422 üretir
     * ve controller bunu 422 VALIDATION_ERROR + error.fields'e çevirir.
     */
    public function testEmptyIdentityStillYields422FromServiceLayer(): void
    {
        $result = $this->handle($this->controller([
            'throw' => ['login' => ValidationException::emptyFields()],
        ]), 'login', ['password' => 'uzun-sifre-123']);

        $this->assertSame(422, http_response_code());
        $this->assertSame('VALIDATION_ERROR', $result['error']['code'] ?? null);
        $this->assertArrayHasKey('fields', $result['error']);
    }
}