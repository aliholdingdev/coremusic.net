<?php declare(strict_types=1);

namespace CoreMusic\Home\Container;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Database\ConnectionFactory;
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

    /**
     * home_music · home_playlist registry kaydı (idempotent) + registry erişimi.
     *
     * P0-2 (2026-10-30): `home_music` / `home_playlist` anahtarları
     * DatabaseRegistry'de kayıtlı değildi → `registry->get()` RuntimeException
     * ("Database 'home_music' is not registered").
     * Kayıt deseni api.coremusic.net `ApiContainerBase::getMusicRegistry /
     * getPlaylistRegistry` ile AYNI: ConnectionFactory::getOrRegister +
     * providerFor (mevcut kaydı okur, yoksa kaydeder → idempotent).
     * DatabaseManager lazy'dir: kayıt PDO bağlantısı AÇMAZ.
     *
     * @return IDatabaseRegistry
     */
    public static function databaseRegistry(): IDatabaseRegistry
    {
        $factory = ConnectionFactory::getInstance();

        $factory->getOrRegister(
            'home_music',
            $factory->providerFor('home_music'),
            (string) ($_ENV['DB_HOST'] ?? (defined('DB_HOST') ? DB_HOST : '')),
            (string) ($_ENV['DB_MUSIC_NAME'] ?? (defined('DB_MUSIC_NAME') ? DB_MUSIC_NAME : 'coremusic_musics')),
            (string) ($_ENV['DB_USER'] ?? (defined('DB_USER') ? DB_USER : '')),
            (string) ($_ENV['DB_PASSWORD'] ?? (defined('DB_PASSWORD') ? DB_PASSWORD : '')),
            (int) ($_ENV['DB_PORT'] ?? (defined('DB_PORT') ? DB_PORT : 3306)),
            (string) ($_ENV['DB_CHARSET'] ?? (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4')),
        );

        $factory->getOrRegister(
            'home_playlist',
            $factory->providerFor('home_playlist'),
            (string) ($_ENV['DB_HOST'] ?? (defined('DB_HOST') ? DB_HOST : '')),
            (string) ($_ENV['DB_PLAYLIST_NAME'] ?? 'coremusic_playlist'),
            (string) ($_ENV['DB_USER'] ?? (defined('DB_USER') ? DB_USER : '')),
            (string) ($_ENV['DB_PASSWORD'] ?? (defined('DB_PASSWORD') ? DB_PASSWORD : '')),
            (int) ($_ENV['DB_PORT'] ?? (defined('DB_PORT') ? DB_PORT : 3306)),
            (string) ($_ENV['DB_CHARSET'] ?? (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4')),
        );

        return $factory->registry();
    }
}
