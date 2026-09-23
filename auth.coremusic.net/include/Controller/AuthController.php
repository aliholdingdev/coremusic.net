<?php declare(strict_types=1);

namespace CoreMusic\Auth\Controller;

use CoreMusic\Auth\Domain\DTO\LoginRequest;
use CoreMusic\Auth\Domain\DTO\RegisterRequest;
use CoreMusic\Interfaces\Auth\IAuthService;
use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ValidationException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\ErrorResponse;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Security\SecurityHelper;

/**
 * AuthController — Kimlik doğrulama endpoint'lerini yönetir.
 *
 * SRP: Tek sorumluluk — HTTP isteklerini Auth service'e yönlendirmek.
 * İş mantığı AuthService'de, Domain nesneleri kullanılır.
 *
 * Redirect validation: SecurityHelper → ReturnUrlPolicy (SSOT)
 */
final class AuthController
{
    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionManager $session,
    ) {}

    private function buildAuthKeyUrl(string $redirectUrl, string $authKey): string
    {
        $separator = str_contains($redirectUrl, '?') ? '&' : '?';
        return $redirectUrl . $separator . 'auth_key=' . urlencode($authKey);
    }

    private function resolveRedirectUrl(array $request, ?string $postUri = null): string
    {
        $defaultRedirect = (defined('AUTH_URL') ? AUTH_URL : '') . '/';
        $pendingRedirect = $this->session->consumePendingRedirect();
        $queryParams = $request['query_params']['redirect_uri'] ?? $request['query_params']['redirect'] ?? '';

        $redirectUrl = $postUri
            ?? $pendingRedirect
            ?? $queryParams
            ?? $defaultRedirect;

        if (!SecurityHelper::isRedirectUriSafe($redirectUrl)) {
            return $defaultRedirect;
        }
        return $redirectUrl;
    }

    private function mapAuthException(\Throwable $e): array
    {
        return match (true) {
            $e instanceof RateLimitException => [
                'httpStatus' => 429,
                'type'       => 'json',
                'body'       => ErrorResponse::fromException($e),
                'headers'    => ['Retry-After' => (string)$e->getRetryAfter()],
            ],
            $e instanceof ValidationException => [
                'httpStatus' => 422,
                'type'       => 'json',
                'body'       => ErrorResponse::fromException($e),
            ],
            $e instanceof AuthenticationException => [
                'httpStatus' => 401,
                'type'       => 'json',
                'body'       => ErrorResponse::fromException($e),
            ],
            $e instanceof ConflictException => [
                'httpStatus' => 409,
                'type'       => 'json',
                'body'       => ErrorResponse::fromException($e),
            ],
            default => [
                'httpStatus' => 500,
                'type'       => 'json',
                'body'       => ErrorResponse::create(500, DEBUG_MODE ? $e->getMessage() : 'Sunucu hatası.', 'SERVER_INTERNAL_ERROR'),
            ],
        };
    }

    public function handleHealth(array $request): array
    {
        return [
            'httpStatus' => 200,
            'type'       => 'json',
            'body' => [
                'success'   => true,
                'status'    => 'ok',
                'service'   => 'auth.coremusic.net',
                'version'   => APP_VERSION,
                'timestamp' => date('c'),
            ],
        ];
    }

    public function handleSessionCheck(array $request): array
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return ['httpStatus' => 200, 'type' => 'json', 'body' => ['authenticated' => false]];
        }
        return [
            'httpStatus' => 200,
            'type'       => 'json',
            'body' => [
                'authenticated' => true,
                'user' => [
                    'id'       => $userId,
                    'username' => $this->session->get('MM_Username', ''),
                    'email'    => $this->session->get('MM_Email', ''),
                    'gender'   => $this->session->getGender(),
                ],
            ],
        ];
    }

    /**
     * Login — LoginRequest DTO kullanarak AuthService'e bağlanır.
     */
    public function handleLogin(array $request): array
    {
        $post = $request['body'];
        $redirectUrl = $this->resolveRedirectUrl($request, $post['redirect_uri'] ?? null);

        $loginRequest = LoginRequest::fromArray($post, $request['server'] ?? $_SERVER, $this->session->getGender());

        $logger = LoggerFactory::getInstance();
        $logger->authEvent('login_attempt', ['email' => $loginRequest->identity, 'ip' => $loginRequest->clientIp]);

        try {
            $result = $this->authService->login(
                $loginRequest->identity,
                $loginRequest->password,
                $loginRequest->visitorGender,
                $loginRequest->clientIp,
            );

            $this->session->regenerateId();
            $result['redirect'] = !empty($result['auth_key'])
                ? $this->buildAuthKeyUrl($redirectUrl, $result['auth_key'])
                : $redirectUrl;

            $logger->authEvent('login_success', [
                'email'   => $loginRequest->identity,
                'user_id' => $result['user']['id'] ?? '-',
                'ip'      => $loginRequest->clientIp,
            ]);

            return ['httpStatus' => 200, 'type' => 'json', 'body' => $result];
        } catch (\Throwable $e) {
            $logger->authEvent('login_failed', [
                'email'  => $loginRequest->identity,
                'ip'     => $loginRequest->clientIp,
                'reason' => $e->getMessage(),
            ]);
            return $this->mapAuthException($e);
        }
    }

    /**
     * Register — RegisterRequest DTO kullanarak AuthService'e bağlanır.
     */
    public function handleRegister(array $request): array
    {
        $redirectUrl = $this->resolveRedirectUrl($request);
        $post = $request['body'];
        $clientIp = $request['server']['REMOTE_ADDR'] ?? '127.0.0.1';

        $registerRequest = RegisterRequest::fromArray($post, $request['server'] ?? $_SERVER, $this->session->getGender());

        $logger = LoggerFactory::getInstance();
        $logger->authEvent('register_attempt', ['email' => $registerRequest->email, 'ip' => $clientIp]);

        try {
            $result = $this->authService->register(
                [
                    'username' => $registerRequest->username,
                    'email' => $registerRequest->email,
                    'password' => $registerRequest->password,
                    'gender' => $registerRequest->gender,
                    'agreeTerms' => $registerRequest->agreeTerms,
                ],
                $clientIp,
                $registerRequest->visitorGender,
            );

            $this->session->regenerateId();
            $result['redirect'] = !empty($result['auth_key'])
                ? $this->buildAuthKeyUrl($redirectUrl, $result['auth_key'])
                : $redirectUrl;

            $logger->authEvent('register_success', [
                'email'   => $registerRequest->email,
                'user_id' => $result['user']['id'] ?? '-',
                'ip'      => $clientIp,
            ]);

            return ['httpStatus' => 200, 'type' => 'json', 'body' => $result];
        } catch (\Throwable $e) {
            $logger->authEvent('register_failed', [
                'email'  => $registerRequest->email,
                'ip'     => $clientIp,
                'reason' => $e->getMessage(),
            ]);
            return $this->mapAuthException($e);
        }
    }

    public function handleLogout(array $request): array
    {
        $logger = LoggerFactory::getInstance();
        $userId = $this->session->getUserId();

        try {
            $this->authService->logout();
        } catch (\Throwable $e) {
            $logger->error("Logout error: {$e->getMessage()}");
        }
        $this->session->regenerateId();

        $logger->authEvent('logout', ['user_id' => $userId ?? '-']);

        return ['httpStatus' => 200, 'type' => 'json', 'body' => [
            'success'  => true,
            'redirect' => (defined('AUTH_URL') ? AUTH_URL : '') . '/login',
        ]];
    }

    public function handleSetGender(array $request): array
    {
        $post = $request['body'];
        $gender = match ((string)($post['gender'] ?? 'neutral')) {
            'male', 'female' => (string)$post['gender'],
            default          => 'neutral',
        };

        $this->session->setGender($gender);

        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie('cm_gender', $gender, [
            'expires'  => time() + (86400 * 30),
            'path'     => '/',
            'domain'   => '.coremusic.net',
            'secure'   => $isHttps,
            'httponly'  => false,
            'samesite' => 'Lax',
        ]);

        $redirectUri = $request['query_params']['redirect_uri'] ?? $post['redirect_uri'] ?? '';
        if ($redirectUri !== '' && !SecurityHelper::isRedirectUriSafe($redirectUri)) {
            $redirectUri = '';
        }

        $clientId     = $request['query_params']['client_id'] ?? $post['client_id'] ?? 'coremusic-web';
        $responseType = $request['query_params']['response_type'] ?? $post['response_type'] ?? 'session';

        $params = ['client_id' => $clientId, 'response_type' => $responseType];
        if ($redirectUri !== '') {
            $params['redirect_uri'] = $redirectUri;
        }

        $redirectUrl = '/login?' . http_build_query($params);

        return ['httpStatus' => 200, 'type' => 'json', 'body' => ['success' => true, 'gender' => $gender, 'redirect' => $redirectUrl]];
    }

    public function handleForgotPassword(array $request): array
    {
        try {
            $result = $this->authService->requestPasswordReset(
                trim((string)($request['body']['email'] ?? '')),
                $request['server']['REQUEST_SCHEME'] ?? 'https',
                $request['server']['HTTP_HOST'] ?? 'auth.coremusic.net',
                $request['server']['REMOTE_ADDR'] ?? '127.0.0.1'
            );
            return ['httpStatus' => 200, 'type' => 'json', 'body' => $result];
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }

    public function handleResetPassword(array $request): array
    {
        try {
            $result = $this->authService->resetPassword(
                trim((string)($request['body']['token'] ?? '')),
                (string)($request['body']['password'] ?? '')
            );
            return ['httpStatus' => 200, 'type' => 'json', 'body' => $result];
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }

    public function handleValidateKey(array $request): array
    {
        $authKey = trim((string)($request['body']['auth_key'] ?? $request['query_params']['auth_key'] ?? ''));

        try {
            $userInfo = $this->authService->validateSessionKey($authKey);
            return [
                'httpStatus' => 200,
                'type'       => 'json',
                'body'       => ['success' => true, 'user' => $userInfo],
            ];
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }
}
