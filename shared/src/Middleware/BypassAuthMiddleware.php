<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
use CoreMusic\Security\SecurityHelper;
use CoreMusic\Config\ConfigManager;

final class BypassAuthMiddleware implements IMiddleware
{
    /** @var array{uuid: string, role: string, username: string}|empty Boş array = fail-closed */
    private array $bypassConfig;

    public function __construct(
        private readonly ConfigManager $config
    ) {
        $this->bypassConfig = $this->loadBypassConfig();
    }

    /**
     * Bypass credential'larını config'den oku — fail-closed: tanımsızsa bypass yok.
     */
    private function loadBypassConfig(): array
    {
        $uuid     = defined('BYPASS_USER_UUID') ? constant('BYPASS_USER_UUID') : '';
        $role     = defined('BYPASS_ROLE') ? constant('BYPASS_ROLE') : '';
        $username = defined('BYPASS_USERNAME') ? constant('BYPASS_USERNAME') : '';

        if ($uuid === '' || $role === '' || $username === '') {
            return []; // Fail-closed: credential eksikse bypass devre dışı
        }

        return ['uuid' => $uuid, 'role' => $role, 'username' => $username];
    }

    public function handle(array $request, callable $next): array
    {
        if (SecurityHelper::isTestBypassActive($this->config)) {
            SecurityHelper::logTestBypass('BypassAuthMiddleware', __FILE__, __LINE__);

            $bypass = $this->bypassConfig;
            if (empty($bypass)) {
                return $next($request); // Fail-closed: config yoksa bypass yapma
            }

            /* Session'a da yaz — AuthGuard::checkAuthenticated() ve sayfa içi
               kontroller session üzerinden bakar; yalnız request['_auth']
               doldurmak bypass'ı AuthGuard'a görünmez kılıyordu (ADR-008). */
            if (empty($_SESSION['MM_UserID'])) {
                $_SESSION['MM_UserID']      = $bypass['uuid'];
                $_SESSION['MM_UserRole']    = $bypass['role'];
                $_SESSION['MM_Username']    = $bypass['username'];
                $_SESSION['MM_Permissions'] = [];
            }

            $request['_auth'] = array_merge($request['_auth'] ?? [], [
                'userId' => $bypass['uuid'],
                'role'   => $bypass['role'],
                'bypass' => true,
                'user'   => ['id' => $bypass['uuid'], 'username' => $bypass['username'], 'role' => $bypass['role']],
            ]);
        }

        return $next($request);
    }
}
