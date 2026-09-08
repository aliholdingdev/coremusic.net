<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
use CoreMusic\Session\SessionBootstrapper;
use CoreMusic\Session\SessionLifecycle;

final class SessionManagerMiddleware implements IMiddleware
{
    public function __construct(
        private readonly SessionLifecycle $sessionInit,
    ) {}

    public function handle(array $request, callable $next): array
    {
        SessionBootstrapper::ensureStarted();
        $this->sessionInit->startOrExtend($request['_csp_nonce'] ?? null);

        $request['_csp_nonce'] = $this->sessionInit->getCspNonce();
        $request['_session']   = $this->sessionInit->getSessionData();

        return $next($request);
    }
}
