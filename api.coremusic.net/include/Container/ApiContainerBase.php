<?php declare(strict_types=1);

namespace CoreMusic\Api\Container;

use CoreMusic\Api\Controller\BillingController;
use CoreMusic\Api\Controller\HistoryController;
use CoreMusic\Api\Controller\MusicController;
use CoreMusic\Api\Controller\PlaylistController;
use CoreMusic\Api\Controller\StreamController;
use CoreMusic\Api\Controller\UserController;
use CoreMusic\Api\Controller\UserHeaderController;
use CoreMusic\Api\Repository\BillingRepository;
use CoreMusic\Api\Repository\HistoryRepository;
use CoreMusic\Api\Repository\IBillingRepository;
use CoreMusic\Api\Repository\IHistoryRepository;
use CoreMusic\Api\Repository\IMusicRepository;
use CoreMusic\Api\Repository\INotificationRepository;
use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Repository\IUserProfileRepository;
use CoreMusic\Api\Repository\MusicRepository;
use CoreMusic\Api\Repository\NotificationRepository;
use CoreMusic\Api\Repository\PlaylistRepository;
use CoreMusic\Api\Repository\UserProfileRepository;
use CoreMusic\Api\Service\BillingService;
use CoreMusic\Api\Service\HistoryService;
use CoreMusic\Api\Service\MusicService;
use CoreMusic\Api\Service\PlaylistService;
use CoreMusic\Api\Service\UserHeaderService;
use CoreMusic\Api\Service\UserService;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Database\ConnectionFactory;

/**
 * ApiContainerBase — API container temel sınıfı (template method pattern).
 *
 * v1 ve v2 container'larının ortak bağımlılık grafiğini içerir.
 * v2 Container bu sınıfı extend edip sadece farklı davranışları override eder.
 *
 * ADR-003/022: her DB için tek registry, tek bağlantı. auth bağlantısı
 * ApiAuthContainer::authRegistry() ile paylaşılır (UserRepository tekil kuralı).
 */
abstract class ApiContainerBase
{
    private static ?IDatabaseRegistry $musicRegistry = null;
    private static ?IDatabaseRegistry $playlistRegistry = null;
    private static ?IDatabaseRegistry $userRegistry = null;
    private static ?IDatabaseRegistry $systemRegistry = null;
    private static ?IDatabaseRegistry $billingRegistry = null;

    private static ?IMusicRepository $musicRepository = null;
    private static ?IPlaylistRepository $playlistRepository = null;
    private static ?IUserProfileRepository $userProfileRepository = null;
    private static ?IHistoryRepository $historyRepository = null;
    private static ?INotificationRepository $notificationRepository = null;
    private static ?IBillingRepository $billingRepository = null;

    private static ?MusicController $musicController = null;
    private static ?PlaylistController $playlistController = null;
    private static ?UserController $userController = null;
    private static ?HistoryController $historyController = null;
    private static ?UserHeaderController $userHeaderController = null;
    private static ?StreamController $streamController = null;
    private static ?BillingController $billingController = null;

    /**
     * Versiyon bilgisini döndürür (örn: 'v1', 'v2').
     * Alt sınıflar bu metodu implement etmelidir.
     */
    abstract protected static function getVersion(): string;

    /**
     * Gateway handler resolver — route['handler'] → controller dispatch.
     *
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     * @return array API sözleşmesi gövdesi
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
            'streamController'     => self::streamController()->handle($route, $request),
            'billingController'    => self::billingController()->handle($route, $request),
            default                => throw new \RuntimeException(
                'Bilinmeyen handler: ' . (string) ($route['handler'] ?? '(bos)')
            ),
        };
    }

    /* ─────────────── Controllers ─────────────── */

    protected static function createMusicController(): MusicController
    {
        return new MusicController(new MusicService(self::musicRepository()));
    }

    protected static function createPlaylistController(): PlaylistController
    {
        return new PlaylistController(new PlaylistService(self::playlistRepository()));
    }

    protected static function createUserController(): UserController
    {
        return new UserController(new UserService(self::userProfileRepository()));
    }

    protected static function createHistoryController(): HistoryController
    {
        return new HistoryController(new HistoryService(self::historyRepository()));
    }

    protected static function createStreamController(): StreamController
    {
        return new StreamController(self::musicRepository());
    }

    protected static function createBillingController(): BillingController
    {
        return new BillingController(new BillingService(self::billingRepository()));
    }

    protected static function createUserHeaderController(): UserHeaderController
    {
        return new UserHeaderController(
            new UserHeaderService(
                self::userProfileRepository(),
                self::playlistRepository(),
                self::musicRepository(),
                self::notificationRepository()
            )
        );
    }

    public static function musicController(): MusicController
    {
        if (self::$musicController === null) {
            self::$musicController = self::createMusicController();
        }
        return self::$musicController;
    }

    public static function playlistController(): PlaylistController
    {
        if (self::$playlistController === null) {
            self::$playlistController = self::createPlaylistController();
        }
        return self::$playlistController;
    }

    public static function userController(): UserController
    {
        if (self::$userController === null) {
            self::$userController = self::createUserController();
        }
        return self::$userController;
    }

    public static function historyController(): HistoryController
    {
        if (self::$historyController === null) {
            self::$historyController = self::createHistoryController();
        }
        return self::$historyController;
    }

    public static function userHeaderController(): UserHeaderController
    {
        if (self::$userHeaderController === null) {
            self::$userHeaderController = self::createUserHeaderController();
        }
        return self::$userHeaderController;
    }

    public static function streamController(): StreamController
    {
        if (self::$streamController === null) {
            self::$streamController = self::createStreamController();
        }
        return self::$streamController;
    }

    public static function billingController(): BillingController
    {
        if (self::$billingController === null) {
            self::$billingController = self::createBillingController();
        }
        return self::$billingController;
    }

    /* ─────────────── Repositories + Registry'ler (DB başına tek bağlantı) ─────────────── */

    protected static function getMusicRegistry(): IDatabaseRegistry
    {
        if (self::$musicRegistry === null) {
            $factory = ConnectionFactory::getInstance();
            $factory->getOrRegister(
                'music',
                $factory->providerFor('music'),
                DB_HOST,
                (string) ($_ENV['DB_MUSIC_NAME'] ?? (defined('DB_MUSIC_NAME') ? DB_MUSIC_NAME : 'coremusic_musics')),
                DB_USER,
                DB_PASSWORD,
                (int) DB_PORT,
                DB_CHARSET,
            );
            self::$musicRegistry = $factory->registry();
        }
        return self::$musicRegistry;
    }

    protected static function getPlaylistRegistry(): IDatabaseRegistry
    {
        if (self::$playlistRegistry === null) {
            $factory = ConnectionFactory::getInstance();
            $factory->getOrRegister(
                'playlist',
                $factory->providerFor('playlist'),
                DB_HOST,
                (string) ($_ENV['DB_PLAYLIST_NAME'] ?? 'coremusic_playlist'),
                DB_USER,
                DB_PASSWORD,
                (int) DB_PORT,
                DB_CHARSET,
            );
            self::$playlistRegistry = $factory->registry();
        }
        return self::$playlistRegistry;
    }

    protected static function getUserRegistry(): IDatabaseRegistry
    {
        if (self::$userRegistry === null) {
            $factory = ConnectionFactory::getInstance();
            $factory->getOrRegister(
                'user',
                $factory->providerFor('user'),
                DB_HOST,
                (string) ($_ENV['DB_USER_NAME'] ?? 'coremusic_user'),
                DB_USER,
                DB_PASSWORD,
                (int) DB_PORT,
                DB_CHARSET,
            );
            self::$userRegistry = $factory->registry();
        }
        return self::$userRegistry;
    }

    protected static function getSystemRegistry(): IDatabaseRegistry
    {
        if (self::$systemRegistry === null) {
            $factory = ConnectionFactory::getInstance();
            $factory->getOrRegister(
                'system',
                $factory->providerFor('system'),
                DB_HOST,
                (string) ($_ENV['DB_SYSTEM_NAME'] ?? 'coremusic_system'),
                DB_USER,
                DB_PASSWORD,
                (int) DB_PORT,
                DB_CHARSET,
            );
            self::$systemRegistry = $factory->registry();
        }
        return self::$systemRegistry;
    }

    protected static function getBillingRegistry(): IDatabaseRegistry
    {
        if (self::$billingRegistry === null) {
            $factory = ConnectionFactory::getInstance();
            $factory->getOrRegister(
                BillingRepository::DB_KEY,
                $factory->providerFor(BillingRepository::DB_KEY),
                DB_HOST,
                (string) ($_ENV['DB_BILLING_NAME'] ?? 'coremusic_billing'),
                DB_USER,
                DB_PASSWORD,
                (int) DB_PORT,
                DB_CHARSET,
            );
            self::$billingRegistry = $factory->registry();
        }
        return self::$billingRegistry;
    }

    public static function musicRepository(): IMusicRepository
    {
        if (self::$musicRepository === null) {
            self::$musicRepository = new MusicRepository(self::getMusicRegistry());
        }
        return self::$musicRepository;
    }

    public static function playlistRepository(): IPlaylistRepository
    {
        if (self::$playlistRepository === null) {
            self::$playlistRepository = new PlaylistRepository(self::getPlaylistRegistry());
        }
        return self::$playlistRepository;
    }

    public static function billingRepository(): IBillingRepository
    {
        if (self::$billingRepository === null) {
            self::$billingRepository = new BillingRepository(self::getBillingRegistry());
        }
        return self::$billingRepository;
    }

    public static function userProfileRepository(): IUserProfileRepository
    {
        if (self::$userProfileRepository === null) {
            self::$userProfileRepository = new UserProfileRepository(ApiAuthContainer::authRegistry());
        }
        return self::$userProfileRepository;
    }

    public static function historyRepository(): IHistoryRepository
    {
        if (self::$historyRepository === null) {
            // F14: 'user' registry ikinci kez register ediliyordu (265-275 ↔ 201-214).
            // ConnectionFactory::getOrRegister idempotent olduğu için davranış
            // DEĞİŞMEZ — sadece tekrarlı kayıt kaldırıldı (self::getUserRegistry()).
            self::$historyRepository = new HistoryRepository(
                self::getUserRegistry(),
                self::getMusicRegistry()
            );
        }
        return self::$historyRepository;
    }

    public static function notificationRepository(): INotificationRepository
    {
        if (self::$notificationRepository === null) {
            self::$notificationRepository = new NotificationRepository(self::getSystemRegistry());
        }
        return self::$notificationRepository;
    }

}