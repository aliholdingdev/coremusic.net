<?php declare(strict_types=1);

namespace CoreMusic\Home\Container;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Home\Session\HomeSessionManager;
use CoreMusic\Home\Auth\HomeAuthBridge;
use DI\Container;
use DI\ContainerBuilder;

/**
 * Home DI Container
 *
 * Home.coremusic.net için bağımlılık enjeksiyonu.
 * Shared paketindeki interface'leri home implementasyonlarıyla bağlar.
 */
final class HomeContainer
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
            HomeSessionManager::class => function () {
                return new HomeSessionManager(
                    SESSION_NAME,
                    '.coremusic.net',
                );
            },

            HomeAuthBridge::class => \DI\autowire(HomeAuthBridge::class)
                ->constructor(
                    \DI\get(HomeSessionManager::class),
                    \DI\string(AUTH_URL),
                ),
        ];
    }
}
