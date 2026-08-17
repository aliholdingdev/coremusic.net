<?php declare(strict_types=1);

namespace CoreMusic\Auth\Container;

use CoreMusic\Cache\CacheManager;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Interfaces\Auth\IAuthService;
use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Interfaces\Auth\IUserRepository;
use CoreMusic\Interfaces\Database\IDatabaseRegistry;
use CoreMusic\Interfaces\Security\IRateLimiter;
use CoreMusic\Database\DatabaseRegistry;
use CoreMusic\Security\CacheRateLimiter;
use CoreMusic\Auth\Service\AuthService;
use CoreMusic\Auth\Service\SessionManager;
use CoreMusic\Auth\Controller\AuthController;
use DI\Container;
use DI\ContainerBuilder;

final class AuthContainer
{
    private static ?Container $instance = null;

    public static function getInstance(ConfigManager $config, DomainConfig $domainConfig): Container
    {
        if (self::$instance === null) {
            $builder = new ContainerBuilder();
            $builder->addDefinitions(self::getDefinitions($config, $domainConfig));
            self::$instance = $builder->build();
        }
        return self::$instance;
    }

    private static function getDefinitions(ConfigManager $config, DomainConfig $domainConfig): array
    {
        return [
            IDatabaseRegistry::class => function () {
                $registry = new DatabaseRegistry();
                $registry->registerMySql(
                    'auth',
                    DB_HOST,
                    DB_AUTH_NAME,
                    DB_USER,
                    DB_PASSWORD,
                    DB_PORT,
                    DB_CHARSET
                );
                return $registry;
            },

            ISessionManager::class => function () {
                return new SessionManager(
                    SESSION_NAME ?? 'COREMUSIC_SESS',
                    SESSION_COOKIE_DOMAIN ?? '.coremusic.net',
                );
            },

            IUserRepository::class => \DI\autowire(\CoreMusic\Auth\Repository\UserRepository::class),

            IRateLimiter::class => function () {
                return new CacheRateLimiter(CacheManager::getAdapter());
            },

            IAuthService::class => \DI\autowire(AuthService::class)
                ->constructor(
                    \DI\get(IUserRepository::class),
                    \DI\get(ISessionManager::class),
                    \DI\get(IRateLimiter::class),
                    $_ENV['APP_PEPPER'] ?? '',
                ),

            AuthController::class => \DI\autowire(AuthController::class),
        ];
    }
}
