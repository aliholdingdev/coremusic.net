<?php declare(strict_types=1);

namespace CoreMusic\Auth\Middleware;

/**
 * Session Middleware — Session başlatır ve yönetir.
 *
 * Her istekte session'ın başlatılıp başlatılmadığını kontrol eder.
 * Idle timeout ve session rotation uygular.
 */
final class SessionMiddleware implements MiddlewareInterface
{
    private const IDLE_TIMEOUT = 3600; // 1 saat
    private const ROTATION_INTERVAL = 900; // 15 dakika

    public function process(array $request, callable $next): array
    {
        $this->ensureSessionStarted();

        // Idle timeout kontrolü
        $lastActive = (int)($_SESSION['_session_last_active'] ?? 0);
        if ($lastActive > 0 && (time() - $lastActive) > self::IDLE_TIMEOUT) {
            $this->destroySession();
            return [
                'httpStatus' => 401,
                'type'       => 'json',
                'body'       => [
                    'success' => false,
                    'error'   => [
                        'code'    => 'SESSION_EXPIRED',
                        'message' => 'Oturumunuz sona erdi.',
                    ],
                ],
            ];
        }

        // Session rotation
        $lastRotation = (int)($_SESSION['_session_rotated_at'] ?? 0);
        if ($lastRotation === 0 || (time() - $lastRotation) > self::ROTATION_INTERVAL) {
            session_regenerate_id(true);
            $_SESSION['_session_rotated_at'] = time();
            if ($lastRotation === 0) {
                $_SESSION['_session_created_at'] = time();
            }
        }

        // Son aktivite zamanını güncelle
        $_SESSION['_session_last_active'] = time();

        return $next($request);
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name(defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS');

        $savePath = ini_get('session.save_path') ?: 'C:\temp';
        if (!is_dir($savePath)) {
            @mkdir($savePath, 0777, true);
        }
        session_save_path($savePath);

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
    }

    private function destroySession(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
        }
    }
}
