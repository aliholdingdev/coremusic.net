<?php declare(strict_types=1);

namespace CoreMusic\Auth\Service;

use CoreMusic\Interfaces\Auth\ISessionManager;

final class SessionManager implements ISessionManager
{
    private const COOKIE_EXPIRY = 42000;

    public function __construct(
        private readonly string $sessionName = 'COREMUSIC_SESS',
        private readonly string $cookieDomain = '.coremusic.net',
    ) {}

    public function setAuthUser(array $user): void
    {
        // UUID hex string olarak sakla (BINARY(16) uyumlu)
        $_SESSION['MM_UserID']      = $user['id'];
        $_SESSION['MM_Username']    = $user['username'];
        $_SESSION['MM_Email']       = $user['email'];
        $_SESSION['MM_DisplayName'] = $user['display_name'] ?? $user['username'];
        $_SESSION['MM_AccountType'] = $user['account_type'] ?? 'free';
        $_SESSION['MM_Image']       = $user['avatar_url'] ?? '';
        $_SESSION['MM_Gender']      = $user['gender'] ?? 'neutral';
        $_SESSION['_session_last_active'] = time();
    }

    public function setRegisteredUser(array $created): void
    {
        $_SESSION['MM_UserID']      = $created['user_id'] ?? '';
        $_SESSION['MM_Username']    = $created['username'] ?? '';
        $_SESSION['MM_Email']       = $created['email'] ?? '';
        $_SESSION['MM_DisplayName'] = $created['display_name'] ?? $created['username'] ?? '';
        $_SESSION['MM_UserRole']    = $created['role_name'] ?? 'free';
        $_SESSION['MM_AccountType'] = $created['account_type'] ?? 'free';
        $_SESSION['MM_Image']       = $created['image'] ?? '';
        $_SESSION['MM_Gender']      = $created['gender'] ?? 'neutral';
        $_SESSION['_session_last_active'] = time();
    }

    public function getUserId(): ?string
    {
        $userId = $_SESSION['MM_UserID'] ?? null;
        if ($userId === null || !is_string($userId) || $userId === '') {
            return null;
        }
        return $userId;
    }

    public function isAuthenticated(): bool
    {
        return $this->getUserId() !== null;
    }

    public function destroy(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - self::COOKIE_EXPIRY, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
        }

        session_name($this->sessionName);
        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '.coremusic.net',
            'secure'   => $isHttps,
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
        session_start();
        session_regenerate_id(true);
        $now = time();
        $_SESSION['_session_last_active'] = $now;
        $_SESSION['_session_created_at']  = $now;
        $_SESSION['_session_rotated_at']  = $now;
        $_SESSION['csp_nonce'] = bin2hex(random_bytes(32));
    }

    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function setGender(string $gender): void
    {
        $_SESSION['cm_gender'] = $gender;
    }

    public function getGender(): string
    {
        return $_SESSION['cm_gender'] ?? $_SESSION['MM_Gender'] ?? 'neutral';
    }

    public function setPendingRedirect(string $uri): void
    {
        $_SESSION['_pending_redirect_uri'] = $uri;
    }

    public function consumePendingRedirect(): ?string
    {
        $uri = $_SESSION['_pending_redirect_uri'] ?? null;
        unset($_SESSION['_pending_redirect_uri']);
        return $uri;
    }

    public function regenerateCspNonce(): string
    {
        $nonce = bin2hex(random_bytes(32));
        $_SESSION['csp_nonce'] = $nonce;
        return $nonce;
    }

    public function getCspNonce(): string
    {
        return $_SESSION['csp_nonce'] ?? '';
    }

    public function isIdleExpired(int $timeoutSeconds): bool
    {
        $lastActivity = (int)($_SESSION['_session_last_active'] ?? 0);
        return $lastActivity > 0 && (time() - $lastActivity) > $timeoutSeconds;
    }

    public function touch(): void
    {
        $_SESSION['_session_last_active'] = time();
    }

    public function rotateIfNeeded(int $intervalSeconds): bool
    {
        $now = time();
        $lastRotation = (int)($_SESSION['_session_rotated_at'] ?? 0);
        if ($lastRotation === 0) {
            $_SESSION['_session_rotated_at'] = $now;
            $_SESSION['_session_created_at'] = $now;
            return false;
        }
        if (($now - $lastRotation) > $intervalSeconds) {
            session_regenerate_id(true);
            $_SESSION['_session_rotated_at'] = $now;
            return true;
        }
        return false;
    }

    public function clearDisplayCookies(): void
    {
        $names = ['MM_Username', 'MM_Image', 'MM_UserID', 'MM_UserDesc', 'MM_UserYear'];
        foreach ($names as $name) {
            if (isset($_COOKIE[$name])) {
                setcookie($name, '', time() - 3600, '/', '.coremusic.net');
            }
        }
    }

    public function getCookieParams(): array
    {
        return session_get_cookie_params();
    }

    public function all(): array
    {
        return $_SESSION;
    }
}
