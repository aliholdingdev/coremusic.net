<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class SessionInitializer
{
    private const SESSION_MAX_LIFETIME = 1800;
    private const SESSION_IDLE_TIMEOUT = 3600;
    private const SESSION_ROTATION_INTERVAL = 1800;
    private const COOKIE_EXPIRY_SECONDS = 42000;

    public function startOrExtend(?string $externalNonce = null): array
    {
        $result = [
            'started'          => false,
            'idleTimedOut'     => false,
            'lifetimeTimedOut' => false,
        ];

        // Session save path — must match callback handler in home.coremusic.net/index.php
        $savePath = ini_get('session.save_path') ?: 'C:\\temp';
        if (!is_dir($savePath)) {
            @mkdir($savePath, 0777, true);
        }
        session_save_path($savePath);

        if (session_status() === PHP_SESSION_ACTIVE) {
            if ($this->isSessionExpired()) {
                $result['lifetimeTimedOut'] = true;
                $this->destroy();
                session_start();
                $this->initSessionKeys();
                $result['started'] = true;
            } else {
                $this->extendSession();
            }
        } else {
            session_start();
            $result['started'] = true;
            if ($this->isIdleTimedOut()) {
                $result['idleTimedOut'] = true;
                $this->destroy();
                session_start();
                $this->initSessionKeys();
                $result['started'] = true;
            }
        }

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Dışarıdan nonce gelirse onu kullan, yoksa üret
        $_SESSION['csp_nonce'] = $externalNonce ?? bin2hex(random_bytes(32));

        // Session rotation: 30 dakikada bir session ID yenile
        $lastRotation = $_SESSION['_session_rotated_at'] ?? 0;
        $now = time();
        if ($lastRotation === 0) {
            $_SESSION['_session_rotated_at'] = $now;
        } elseif (($now - $lastRotation) >= self::SESSION_ROTATION_INTERVAL) {
            session_regenerate_id(true);
            $_SESSION['_session_rotated_at'] = $now;
        }

        $now = time();
        $_SESSION['last_activity'] = $now;
        $_SESSION['_session_last_active'] = $now;

        return $result;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - self::COOKIE_EXPIRY_SECONDS, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_start();
        session_regenerate_id(true);
        $this->initSessionKeys();
    }

    private function initSessionKeys(): void
    {
        $now = time();
        $_SESSION['created_at'] = $now;
        $_SESSION['_session_created_at'] = $now;
        $_SESSION['last_activity'] = $now;
        $_SESSION['_session_last_active'] = $now;
        $_SESSION['_session_rotated_at'] = $now;
    }

    private function isIdleTimedOut(): bool
    {
        $lastActive = $_SESSION['_session_last_active'] ?? $_SESSION['last_activity'] ?? null;
        if ($lastActive === null) {
            return false;
        }
        return (time() - (int)$lastActive) >= self::SESSION_IDLE_TIMEOUT;
    }

    private function isSessionExpired(): bool
    {
        $createdAt = $_SESSION['_session_created_at'] ?? $_SESSION['created_at'] ?? null;
        if ($createdAt === null) {
            return false;
        }
        return (time() - (int)$createdAt) >= self::SESSION_MAX_LIFETIME;
    }

    private function extendSession(): void
    {
        $createdAt = $_SESSION['_session_created_at'] ?? $_SESSION['created_at'] ?? null;
        if ($createdAt === null) {
            $now = time();
            $_SESSION['created_at'] = $now;
            $_SESSION['_session_created_at'] = $now;
            $createdAt = $now;
        }
        if ((time() - (int)$createdAt) >= self::SESSION_MAX_LIFETIME) {
            $this->destroy();
            session_start();
            $this->initSessionKeys();
        }
    }

    public function getCspNonce(): string
    {
        return $_SESSION['csp_nonce'] ?? '';
    }

    public function getSessionData(): array
    {
        return $_SESSION;
    }
}
