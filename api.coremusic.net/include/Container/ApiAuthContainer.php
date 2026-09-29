<?php declare(strict_types=1);

namespace CoreMusic\Api\Container;

use CoreMusic\Api\Controller\AuthController;
use CoreMusic\Auth\Repository\UserRepository;
use CoreMusic\Auth\Service\AuthService;
use CoreMusic\Auth\Service\SessionManager;
use CoreMusic\Cache\CacheManager;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Database\DatabaseRegistry;
use CoreMusic\Security\CacheRateLimiter;

/**
 * ApiAuthContainer — API tarafının auth bağımlılık grafiği.
 *
 * SSOT: `auth.coremusic.net/include` içindeki AuthService / UserRepository /
 * SessionManager NEW'LENİR, KOPYALANMAZ (Faz 1b — L0-L2).
 *
 * Oturum: auth.coremusic.net ile AYNI session (SESSION_NAME + .coremusic.net
 * cookie + aynı save path) → API'de açılan oturum sitenin geri kalanında da
 * geçerli, web'de açılan oturum API'de kimlik doğrular.
 */
final class ApiAuthContainer
{
    private static ?ISessionManager $session = null;

    private static ?IAuthService $authService = null;

    private static ?AuthController $controller = null;

    public static function session(): ISessionManager
    {
        if (self::$session === null) {
            self::$session = new SessionManager(
                defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS',
                defined('SESSION_COOKIE_DOMAIN') ? SESSION_COOKIE_DOMAIN : '.coremusic.net',
            );
        }

        return self::$session;
    }

    public static function authService(): IAuthService
    {
        if (self::$authService === null) {
            $registry = new DatabaseRegistry();
            $registry->registerMySql(
                'auth',
                DB_HOST,
                DB_NAME,
                DB_USER,
                DB_PASSWORD,
                DB_PORT,
                DB_CHARSET,
            );

            self::$authService = new AuthService(
                new UserRepository($registry),
                self::session(),
                new CacheRateLimiter(CacheManager::getAdapter()),
                self::passwordPepper(),
            );
        }

        return self::$authService;
    }

    public static function controller(): AuthController
    {
        if (self::$controller === null) {
            self::$controller = new AuthController(
                self::authService(),
                self::session(),
                defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net',
                defined('AUTH_URL') ? AUTH_URL : 'http://auth.coremusic.net',
            );
        }

        return self::$controller;
    }

    /**
     * Argon2id pepper'ı (SSOT): hash'leri üreten auth.coremusic.net'in
     * APP_PEPPER'ı kullanılır — API kendi .env değeriyle doğrularsa tüm
     * şifre doğrulamaları başarısız olur.
     *
     * .env dosyası OKUNUR, yazdırılmaz/yazılmaz (Guardrail: Secret Yok).
     */
    private static function passwordPepper(): string
    {
        $authEnv = dirname(__DIR__, 2) . '/../auth.coremusic.net/config/.env';

        if (is_file($authEnv)) {
            $lines = file($authEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    $pos = strpos($line, '=');
                    if ($pos === false || trim(substr($line, 0, $pos)) !== 'APP_PEPPER') {
                        continue;
                    }
                    $value = trim(substr($line, $pos + 1), " \t\"'");
                    if ($value !== '') {
                        return $value;
                    }
                }
            }
        }

        return (string) ($_ENV['APP_PEPPER'] ?? (defined('APP_PEPPER') ? APP_PEPPER : ''));
    }
}
