<?php declare(strict_types=1);

namespace CoreMusic\Auth\Controller;

use CoreMusic\Interfaces\Auth\IAuthService;
use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ValidationException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\ErrorResponse;

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
        $defaultRedirect = (defined('AUTH_URL') ? AUTH_URL : 'https://auth.coremusic.net') . '/';
        $redirectUrl = $postUri
            ?? $this->session->consumePendingRedirect()
            ?? $request['query_params']['redirect_uri']
            ?? $request['query_params']['redirect']
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
        return ['httpStatus' => 200, 'success' => true, 'status' => 'ok', 'service' => 'auth.coremusic.net', 'version' => APP_VERSION, 'timestamp' => date('c')];
    }

    public function handleSessionCheck(array $request): array
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return ['httpStatus' => 200, 'authenticated' => false];
        }
        return [
            'httpStatus'    => 200,
            'authenticated' => true,
            'user' => [
                'id'       => $userId,
                'username' => $this->session->get('MM_Username', ''),
                'email'    => $this->session->get('MM_Email', ''),
                'gender'   => $this->session->getGender(),
            ],
        ];
    }

    public function handlePage(array $request): array
    {
        $normalizedUri = $request['normalizedUri'] ?? '/';
        $pageName = trim($normalizedUri, '/');

        if (!empty($request['query_params']['redirect_uri'])) {
            $this->session->setPendingRedirect($request['query_params']['redirect_uri']);
        }

        $gender = $this->session->getGender();
        $genderPages = ['login', 'register', 'forgot-password', 'reset-password'];

        if ($gender === 'neutral' && in_array($pageName, $genderPages, true)) {
            $redirectUri = $request['query_params']['redirect_uri'] ?? '';
            $qs = $redirectUri !== '' ? '?redirect_uri=' . urlencode($redirectUri) : '';
            return ['httpStatus' => 302, 'type' => 'redirect', 'headers' => ['Location' => '/select-gender' . $qs]];
        }

        $pageFile = PAGES_PATH . '/' . $pageName . '.php';
        if (!file_exists($pageFile)) {
            return ['httpStatus' => 404, 'type' => 'html', 'html' => '<h1>404 - Sayfa bulunamadı</h1>'];
        }

        $csrfToken = $this->session->get('csrf_token', '');
        $pendingRedirect = $this->session->get('_pending_redirect_uri') ?? $request['query_params']['redirect_uri'] ?? '';

        ob_start();
        $csrfTokenEsc = htmlspecialchars((string)$csrfToken, ENT_QUOTES, 'UTF-8');
        $redirectUriEsc = htmlspecialchars((string)$pendingRedirect, ENT_QUOTES, 'UTF-8');
        $cspNonce = htmlspecialchars($this->session->get('csp_nonce', ''), ENT_QUOTES, 'UTF-8');
        $genderEsc = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
        require $pageFile;
        $html = ob_get_clean();

        return ['httpStatus' => 200, 'type' => 'html', 'html' => $html];
    }

    public function redirectLogin(array $request): array
    {
        return ['httpStatus' => 302, 'type' => 'redirect', 'headers' => ['Location' => '/select-gender']];
    }

    public function handleLogin(array $request): array
    {
        $post = $request['body'];
        $redirectUrl = $this->resolveRedirectUrl($request, $post['redirect_uri'] ?? null);
        $identity = trim((string)($post['email'] ?? $post['identity'] ?? ''));
        $password = (string)($post['password'] ?? '');
        $clientIp = $request['server']['REMOTE_ADDR'] ?? '127.0.0.1';

        try {
            $result = $this->authService->login($identity, $password, $this->session->getGender(), $clientIp);
            $this->session->regenerateId();
            $result['redirect'] = !empty($result['auth_key'])
                ? $this->buildAuthKeyUrl($redirectUrl, $result['auth_key'])
                : $redirectUrl;
            return $result;
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }

    public function handleRegister(array $request): array
    {
        $redirectUrl = $this->resolveRedirectUrl($request);
        $post = $request['body'];
        $clientIp = $request['server']['REMOTE_ADDR'] ?? '127.0.0.1';

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
            return $result;
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }

    public function handleLogout(array $request): array
    {
        try {
            $this->authService->logout();
        } catch (\Throwable $e) {
            error_log('[AuthController] Logout error: ' . $e->getMessage());
        }
        $this->session->regenerateId();
        return ['httpStatus' => 200, 'success' => true, 'redirect' => (defined('MUSIC_URL') ? MUSIC_URL : 'https://home.coremusic.net') . '/'];
    }

    public function handleSetGender(array $request): array
    {
        $post = $request['body'];
        $gender = match ((string)($post['gender'] ?? 'neutral')) {
            'male', 'female' => (string)$post['gender'],
            default          => 'neutral',
        };

        $this->session->setGender($gender);

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

        return ['httpStatus' => 200, 'success' => true, 'gender' => $gender, 'redirect' => $redirectUrl];
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
            $result['httpStatus'] = 200;
            return $result;
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
            $result['httpStatus'] = 200;
            return $result;
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }

    /**
     * Auth key validation — cross-domain session transfer.
     * Called by home.coremusic.net/auth/callback to validate auth_key.
     */
    public function handleValidateKey(array $request): array
    {
        $authKey = trim((string)($request['body']['auth_key'] ?? $request['query_params']['auth_key'] ?? ''));

        try {
            $userInfo = $this->authService->validateSessionKey($authKey);
            return [
                'httpStatus' => 200,
                'type'       => 'json',
                'success'    => true,
                'user'       => $userInfo,
            ];
        } catch (\Throwable $e) {
            return $this->mapAuthException($e);
        }
    }
}
