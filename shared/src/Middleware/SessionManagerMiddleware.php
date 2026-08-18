<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
use CoreMusic\PageRouter\SessionInitializer;

final class SessionManagerMiddleware implements IMiddleware
{
    public function __construct(
        private readonly SessionInitializer $sessionInit,
        private readonly string $sessionName = 'COREMUSIC_SESS',
        private readonly string $cookieDomain = '.coremusic.net',
    ) {}

    public function handle(array $request, callable $next): array
    {
        $this->ensureSessionStarted();
        $this->sessionInit->startOrExtend($request['_csp_nonce'] ?? null);

        $request['_csp_nonce'] = $this->sessionInit->getCspNonce();
        $request['_session']   = $this->sessionInit->getSessionData();

        return $next($request);
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        session_name($this->sessionName);

        // Session save path — php.ini'den oku, fallback C:\temp
        $savePath = ini_get('session.save_path') ?: 'C:\temp';
        if (!is_dir($savePath)) {
            @mkdir($savePath, 0777, true);
        }
        session_save_path($savePath);

        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => $this->cookieDomain,
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }
}
