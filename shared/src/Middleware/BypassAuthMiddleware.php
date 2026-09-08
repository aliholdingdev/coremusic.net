<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
use CoreMusic\Security\SecurityHelper;
use CoreMusic\Config\ConfigManager;

final class BypassAuthMiddleware implements IMiddleware
{
    private const BYPASS_USER_ID  = 1;
    private const BYPASS_ROLE     = 'admin';
    private const BYPASS_USERNAME = 'test_user';
    /** BINARY(16) uyumlu UUID hex (checkAuthenticated string+hex bekliyor) */
    private const BYPASS_USER_UUID = '00000000000000000000000000000001';

    public function __construct(
        private readonly ConfigManager $config
    ) {}

    public function handle(array $request, callable $next): array
    {
        if (SecurityHelper::isTestBypassActive($this->config)) {
            SecurityHelper::logTestBypass('BypassAuthMiddleware', __FILE__, __LINE__);

            /* Session'a da yaz — AuthGuard::checkAuthenticated() ve sayfa içi
               kontroller session üzerinden bakar; yalnız request['_auth']
               doldurmak bypass'ı AuthGuard'a görünmez kılıyordu (ADR-008). */
            if (empty($_SESSION['MM_UserID'])) {
                $_SESSION['MM_UserID']     = self::BYPASS_USER_UUID;
                $_SESSION['MM_UserRole']   = self::BYPASS_ROLE;
                $_SESSION['MM_Username']   = self::BYPASS_USERNAME;
                $_SESSION['MM_Permissions'] = [];
            }

            $request['_auth'] = array_merge($request['_auth'] ?? [], [
                'userId' => self::BYPASS_USER_UUID,
                'role'   => self::BYPASS_ROLE,
                'bypass' => true,
                'user'   => ['id' => self::BYPASS_USER_UUID, 'username' => self::BYPASS_USERNAME, 'role' => self::BYPASS_ROLE],
            ]);
        }

        return $next($request);
    }
}
