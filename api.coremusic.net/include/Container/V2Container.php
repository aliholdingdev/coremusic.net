<?php declare(strict_types=1);

namespace CoreMusic\Api\Container;

/**
 * V2Container — API v2 Container (extends V1Container).
 *
 * v1 Container'dan inherit eder, sadece v2'ye özgü farkları override eder.
 * Şu an v1 ile birebir aynı davranır (backward compatible).
 *
 * Gelecekte v2'ye özgü değişiklikler (ör: yeni controller, farklı service)
 * bu sınıfta override edilebilir.
 *
 * Örnek override senaryoları:
 * - createMusicController() → V2\MusicController
 * - createUserController() → V2\UserController (farklı DTO/validasyon)
 * - notificationRepository() → farklı repository
 */
final class V2Container extends V1Container
{
    protected static function getVersion(): string
    {
        return 'v2';
    }

    /*
     * Gelecekte v2'ye özgü override'ler için hazır template:
     *
     * protected static function createMusicController(): MusicController
     * {
     *     return new \CoreMusic\Api\V2\Controller\MusicController(
     *         new \CoreMusic\Api\V2\Service\MusicService(self::musicRepository())
     *     );
     * }
     *
     * public static function musicController(): MusicController
     * {
     *     if (self::$musicController === null) {
     *         self::$musicController = self::createMusicController();
     *     }
     *     return self::$musicController;
     * }
     */
}