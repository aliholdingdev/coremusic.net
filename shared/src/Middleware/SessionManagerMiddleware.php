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
        $this->sessionInit->startOrExtend();

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
