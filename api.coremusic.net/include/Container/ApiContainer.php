<?php declare(strict_types=1);

namespace CoreMusic\Api\Container;

use CoreMusic\Api\Controller\HistoryController;
use CoreMusic\Api\Controller\MusicController;
use CoreMusic\Api\Controller\PlaylistController;
use CoreMusic\Api\Controller\UserController;
use CoreMusic\Api\Controller\UserHeaderController;
use CoreMusic\Api\Repository\HistoryRepository;
use CoreMusic\Api\Repository\IHistoryRepository;
use CoreMusic\Api\Repository\IMusicRepository;
use CoreMusic\Api\Repository\INotificationRepository;
use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Repository\IUserProfileRepository;
use CoreMusic\Api\Repository\MusicRepository;
use CoreMusic\Api\Repository\NotificationRepository;
use CoreMusic\Api\Repository\PlaylistRepository;
use CoreMusic\Api\Repository\UserProfileRepository;
use CoreMusic\Api\Service\HistoryService;
use CoreMusic\Api\Service\MusicService;
use CoreMusic\Api\Service\PlaylistService;
use CoreMusic\Api\Service\UserHeaderService;
use CoreMusic\Api\Service\UserService;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Database\DatabaseRegistry;

/**
 * ApiContainer — Faz 4: route['handler'] → controller dispatch + müzik/playlist/profil
 * bağımlılık grafiği (auth grafiği ApiAuthContainer SSOT'unda kalır).
 *
 * ADR-003/022: her DB için tek registry, tek bağlantı. auth bağlantısı
 * ApiAuthContainer::authRegistry() ile paylaşılır (UserRepository tekil kuralı).
 */
final class ApiContainer
{
    private static ?IDatabaseRegistry $musicRegistry = null;

    private static ?IDatabaseRegistry $playlistRegistry = null;

    private static ?IMusicRepository $musicRepository = null;

    private static ?IPlaylistRepository $playlistRepository = null;

    private static ?IUserProfileRepository $userProfileRepository = null;

    private static ?MusicController $musicController = null;

    private static ?PlaylistController $playlistController = null;

    private static ?UserController $userController = null;

    private static ?HistoryController $historyController = null;

    private static ?UserHeaderController $userHeaderController = null;

    private static ?IHistoryRepository $historyRepository = null;

    private static ?INotificationRepository $notificationRepository = null;

    /**
     * Gateway handlerResolver girişi — route'daki handler adına göre controller.
     *
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     */
    public static function handle(array $route, array $request): array
    {
        return match ((string) ($route['handler'] ?? '')) {
            'authController'       => ApiAuthContainer::controller()->handle($route, $request),
            'musicController'      => self::musicController()->handle($route, $request),
            'playlistController'   => self::playlistController()->handle($route, $request),
            'userController'       => self::userController()->handle($route, $request),
            'historyController'    => self::historyController()->handle($route, $request),
            'userHeaderController' => self::userHeaderController()->handle($route, $request),
            default                => throw new \RuntimeException(
                'Bilinmeyen handler: ' . (string) ($route['handler'] ?? '(bos)')
            ),
        };
    }

    public static function historyController(): HistoryController
    {
        if (self::$historyController === null) {
            self::$historyController = new HistoryController(
                new \CoreMusic\Api\Service\HistoryService(self::historyRepository())
            );
        }

        return self::$historyController;
    }

    public static function userHeaderController(): UserHeaderController
    {
        if (self::$userHeaderController === null) {
            self::$userHeaderController = new UserHeaderController(
                new UserHeaderService(
                    self::userProfileRepository(),
                    self::playlistRepository(),
                    self::musicRepository(),
                    self::notificationRepository()
                )
            );
        }

        return self::$userHeaderController;
    }

    public static function musicController(): MusicController
    {
        if (self::$musicController === null) {
            self::$musicController = new MusicController(new MusicService(self::musicRepository()));
        }

        return self::$musicController;
    }

    public static function playlistController(): PlaylistController
    {
        if (self::$playlistController === null) {
            self::$playlistController = new PlaylistController(new PlaylistService(self::playlistRepository()));
        }

        return self::$playlistController;
    }

    public static function userController(): UserController
    {
        if (self::$userController === null) {
            self::$userController = new UserController(new UserService(self::userProfileRepository()));
        }

        return self::$userController;
    }

    /* ─────────────── Repositories + Registry'ler (DB başına tek bağlantı) ─────────────── */

    public static function musicRepository(): IMusicRepository
    {
        if (self::$musicRepository === null) {
            if (self::$musicRegistry === null) {
                $registry = new DatabaseRegistry();
                $registry->registerMySql(
                    'music',
                    DB_HOST,
                    (string) ($_ENV['DB_MUSIC_NAME'] ?? (defined('DB_MUSIC_NAME') ? DB_MUSIC_NAME : 'coremusic_musics')),
                    DB_USER,
                    DB_PASSWORD,
                    DB_PORT,
                    DB_CHARSET,
                );
                self::$musicRegistry = $registry;
            }

            self::$musicRepository = new MusicRepository(self::$musicRegistry);
        }

        return self::$musicRepository;
    }

    private static function playlistRepository(): IPlaylistRepository
    {
        if (self::$playlistRepository === null) {
            if (self::$playlistRegistry === null) {
                $registry = new DatabaseRegistry();
                $registry->registerMySql(
                    'playlist',
                    DB_HOST,
                    // Şema adı migration kanıtı (20261021_create_playlist_tables.sql).
                    (string) ($_ENV['DB_PLAYLIST_NAME'] ?? 'coremusic_playlist'),
                    DB_USER,
                    DB_PASSWORD,
                    DB_PORT,
                    DB_CHARSET,
                );
                self::$playlistRegistry = $registry;
            }

            self::$playlistRepository = new PlaylistRepository(self::$playlistRegistry);
        }

        return self::$playlistRepository;
    }

    private static function userProfileRepository(): IUserProfileRepository
    {
        if (self::$userProfileRepository === null) {
            self::$userProfileRepository = new UserProfileRepository(ApiAuthContainer::authRegistry());
        }

        return self::$userProfileRepository;
    }

    private static function historyRepository(): IHistoryRepository
    {
        if (self::$historyRepository === null) {
            // self::$musicRegistry'nin register edilmiş olmasını garanti et
            self::musicRepository();

            $userRegistry = new DatabaseRegistry();
            $userRegistry->registerMySql(
                'user',
                DB_HOST,
                (string) ($_ENV['DB_USER_NAME'] ?? 'coremusic_user'),
                DB_USER,
                DB_PASSWORD,
                DB_PORT,
                DB_CHARSET,
            );

            self::$historyRepository = new HistoryRepository(
                $userRegistry,
                self::$musicRegistry
            );
        }

        return self::$historyRepository;
    }

    /**
     * Bildirim repo (coremusic_system DB) — header uç verisi (okunmamış sayısı).
     * System registry münferit: ADR-003 (DB başına tek registry) — system bağlantısı
     * başka repo ile paylaşılmıyor (şu an tek kullanıcı notificationRepository).
     */
    private static function notificationRepository(): INotificationRepository
    {
        if (self::$notificationRepository === null) {
            $systemRegistry = new DatabaseRegistry();
            $systemRegistry->registerMySql(
                'system',
                DB_HOST,
                (string) ($_ENV['DB_SYSTEM_NAME'] ?? 'coremusic_system'),
                DB_USER,
                DB_PASSWORD,
                DB_PORT,
                DB_CHARSET,
            );

            self::$notificationRepository = new NotificationRepository($systemRegistry);
        }

        return self::$notificationRepository;
    }
}
