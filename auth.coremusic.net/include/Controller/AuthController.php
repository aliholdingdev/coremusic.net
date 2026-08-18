<?php declare(strict_types=1);

namespace CoreMusic\Auth\Controller;

use CoreMusic\Interfaces\Auth\IAuthService;
use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ValidationException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\ErrorResponse;
use CoreMusic\Log\LoggerFactory;

final class AuthController
{
    private const ALLOWED_REDIRECT_HOSTS = [
        'music.coremusic.net',
        'admin.coremusic.net',
        'auth.coremusic.net',
        'home.coremusic.net',
        'coremusic.net',
        'localhost',
        '127.0.0.1',
    ];

    private const ALLOWED_PORTS = [80, 443, 81, 3001, 5000, 6000, 9741, 9742, 9743];

    public function __construct(
        private readonly IAuthService $authService,
        private readonly ISessionManager $session,
    ) {}

    private static function isRedirectUriSafe(string $uri): bool
    {
        if ($uri === '' || $uri === '/' || str_starts_with($uri, '/')) {
            return true;
        }
        $parsed = parse_url($uri);
        if ($parsed === false || empty($parsed['host'])) {
            return false;
        }
        $host = strtolower($parsed['host']);
        if (isset($parsed['port']) && !in_array($parsed['port'], self::ALLOWED_PORTS, true)) {
            return false;
        }
        foreach (self::ALLOWED_REDIRECT_HOSTS as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost)) {
                return true;
            }
        }
        return false;
    }

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

        if (!self::isRedirectUriSafe($redirectUrl)) {
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

    public function handleLogin(array $request): array
    {
        $post = $request['body'];
        $redirectUrl = $this->resolveRedirectUrl($request, $post['redirect_uri'] ?? null);
        $identity = trim((string)($post['email'] ?? $post['identity'] ?? ''));
        $password = (string)($post['password'] ?? '');
        $clientIp = $request['server']['REMOTE_ADDR'] ?? '127.0.0.1';

        $logger = LoggerFactory::getInstance();
        $logger->authEvent('login_attempt', ['email' => $identity, 'ip' => $clientIp]);

        try {
            $result = $this->authService->login($identity, $password, $this->session->getGender(), $clientIp);
            $this->session->regenerateId();
            $result['redirect'] = !empty($result['auth_key'])
                ? $this->buildAuthKeyUrl($redirectUrl, $result['auth_key'])
                : $redirectUrl;

            $logger->authEvent('login_success', [
                'email'   => $identity,
                'user_id' => $result['user']['id'] ?? '-',
                'ip'      => $clientIp,
            ]);

            return ['httpStatus' => 200, 'type' => 'json', 'body' => $result];
        } catch (\Throwable $e) {
            $logger->authEvent('login_failed', [
                'email'  => $identity,
                'ip'     => $clientIp,
                'reason' => $e->getMessage(),
            ]);
            return $this->mapAuthException($e);
        }
    }

    public function handleRegister(array $request): array
    {
        $redirectUrl = $this->resolveRedirectUrl($request);
        $post = $request['body'];
        $clientIp = $request['server']['REMOTE_ADDR'] ?? '127.0.0.1';

        $logger = LoggerFactory::getInstance();
        $logger->authEvent('register_attempt', ['email' => $post['email'] ?? '-', 'ip' => $clientIp]);

        try {
            $result = $this->authService->register([
                'username'    => trim((string)($post['username'] ?? '')),
                'email'       => trim((string)($post['email'] ?? '')),
                'password'    => (string)($post['password'] ?? ''),
                'gender'      => $post['gender'] ?? 'neutral',
                'agree_terms' => !empty($post['agree_terms']),
            ], $clientIp);
            $this->session->regenerateId();
            $result['redirect'] = !empty($result['auth_key'])
                ? $this->buildAuthKeyUrl($redirectUrl, $result['auth_key'])
                : $redirectUrl;

            $logger->authEvent('register_success', [
                'email'   => $result['user']['email'] ?? '-',
                'user_id' => $result['user']['id'] ?? '-',
                'ip'      => $clientIp,
            ]);

            return ['httpStatus' => 200, 'type' => 'json', 'body' => $result];
        } catch (\Throwable $e) {
            $logger->authEvent('register_failed', [
                'email'  => $post['email'] ?? '-',
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
            'redirect' => (defined('MUSIC_URL') ? MUSIC_URL : '') . '/',
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

        // Cookie fallback — session çalışmasa bile gender saklanır
        setcookie('cm_gender', $gender, [
            'expires'  => time() + (86400 * 30),
            'path'     => '/',
            'domain'   => '.coremusic.net',
            'secure'   => false,
            'httponly'  => false,
            'samesite' => 'Lax',
        ]);

        $redirectUri = $request['query_params']['redirect_uri'] ?? $post['redirect_uri'] ?? '';
        if ($redirectUri !== '' && !self::isRedirectUriSafe($redirectUri)) {
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
