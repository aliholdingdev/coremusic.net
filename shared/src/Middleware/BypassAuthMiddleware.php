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

    public function __construct(
        private readonly ConfigManager $config
    ) {}

    public function handle(array $request, callable $next): array
    {
        if (SecurityHelper::isTestBypassActive($this->config)) {
            SecurityHelper::logTestBypass('BypassAuthMiddleware', __FILE__, __LINE__);

            $request['_auth'] = array_merge($request['_auth'] ?? [], [
                'userId' => self::BYPASS_USER_ID,
                'role'   => self::BYPASS_ROLE,
                'bypass' => true,
                'user'   => ['id' => self::BYPASS_USER_ID, 'username' => self::BYPASS_USERNAME, 'role' => self::BYPASS_ROLE],
            ]);
        }

        return $next($request);
    }
}
