<?php declare(strict_types=1);

namespace CoreMusic\Api\Container;

use CoreMusic\Api\Controller\AuthController;
use CoreMusic\Api\Service\ITokenService;
use CoreMusic\Api\Service\IRedirectService;
use CoreMusic\Api\Service\IUserTransformer;
use CoreMusic\Api\Service\TokenService;
use CoreMusic\Api\Service\RedirectService;
use CoreMusic\Api\Service\UserTransformer;
use CoreMusic\Auth\Repository\UserRepository;
use CoreMusic\Auth\Service\AuthService;
use CoreMusic\Auth\Service\SessionManager;
use CoreMusic\Cache\CacheManager;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Contracts\Auth\IUserRepository;
use CoreMusic\Database\ConnectionFactory;
use CoreMusic\Security\CacheRateLimiter;
use CoreMusic\Security\JwtService;

/**
 * ApiAuthContainer — API tarafının auth bağımlılık grafiği.
 *
 * SSOT: `auth.coremusic.net/include` içindeki AuthService / UserRepository /
 * SessionManager NEW'LENİR, KOPYALANMAZ (Faz 1b — L0-L2).
 *
 * Oturum: auth.coremusic.net ile AYNI session (SESSION_NAME + .coremusic.net
 * cookie + aynı save path) → API'de açılan oturum sitenin geri kalanında da
 * geçerli, web'de açılan oturum API'de kimlik doğrular.
 *
 * Phase 2 refactoring: TokenService, RedirectService, UserTransformer eklendi
 * (AuthController'dan SRP ihlali giderildi).
 */
final class ApiAuthContainer
{
    private static ?ISessionManager $session = null;

    private static ?IAuthService $authService = null;

    private static ?AuthController $controller = null;

    private static ?UserRepository $userRepository = null;

    private static ?JwtService $jwtService = null;

    private static ?ITokenService $tokenService = null;

    private static ?IRedirectService $redirectService = null;

    private static ?IUserTransformer $userTransformer = null;

    private static ?\CoreMusic\Contracts\Database\IDatabaseRegistry $authRegistry = null;

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

    /**
     * auth DB registry — Faz 4: UserProfileRepository ile PAYLAŞILIR
     * (iki registry = iki bağlantı — tekil bağlantı kuralı).
     */
    public static function authRegistry(): \CoreMusic\Contracts\Database\IDatabaseRegistry
    {
        if (self::$authRegistry === null) {
            $factory = ConnectionFactory::getInstance();
            $factory->getOrRegister(
                'auth',
                $factory->providerFor('auth'),
                DB_HOST,
                DB_NAME,
                DB_USER,
                DB_PASSWORD,
                (int) DB_PORT,
                DB_CHARSET,
            );

            self::$authRegistry = $factory->registry();
        }

        return self::$authRegistry;
    }

    /**
     * UserRepository tekil — authService ile PAYLAŞILIR (iki registry = iki bağlantı).
     */
    public static function userRepository(): IUserRepository
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository(self::authRegistry());
        }

        return self::$userRepository;
    }

    /**
     * JWT service (RS256) — P1-9/B-F-01 hybrid auth.
     *
     * Key yolları: env (JWT_PRIVATE_KEY_PATH / JWT_PUBLIC_KEY_PATH) →
     * default `shared/config/jwt/`. private.pem .gitignore'dadır.
     */
    public static function jwtService(): JwtService
    {
        if (self::$jwtService === null) {
            $jwtDir = dirname(__DIR__, 3) . '/shared/config/jwt';

            self::$jwtService = new JwtService(
                // ?: — env değeri TANIMLI ama BOŞ olabilir (ör. `KEY=`), ?? bunu yakalamaz
                privateKeyPath: (string) (($_ENV['JWT_PRIVATE_KEY_PATH'] ?? '') !== '' ? $_ENV['JWT_PRIVATE_KEY_PATH'] : $jwtDir . '/private.pem'),
                publicKeyPath: (string) (($_ENV['JWT_PUBLIC_KEY_PATH'] ?? '') !== '' ? $_ENV['JWT_PUBLIC_KEY_PATH'] : $jwtDir . '/public.pem'),
                issuer: (string) (($_ENV['JWT_ISSUER'] ?? '') !== '' ? $_ENV['JWT_ISSUER'] : 'coremusic'),
                audience: (string) (($_ENV['JWT_AUDIENCE'] ?? '') !== '' ? $_ENV['JWT_AUDIENCE'] : 'coremusic-api'),
                ttlSeconds: (int) (($_ENV['JWT_TTL_SECONDS'] ?? '') !== '' ? $_ENV['JWT_TTL_SECONDS'] : 3600),
            );
        }

        return self::$jwtService;
    }

    /**
     * TokenService — JWT issue + persist + revoke.
     */
    public static function tokenService(): ITokenService
    {
        if (self::$tokenService === null) {
            self::$tokenService = new TokenService(
                self::jwtService(),
                self::userRepository(),
            );
        }

        return self::$tokenService;
    }

    /**
     * RedirectService — Güvenli redirect URL çözümleme.
     *
     * F9: default fallback `http://` → `https://home.coremusic.net` ve MUSIC_URL
     * host'u allowlist'ten geçmek ZORUNDA (coremusic.net + alt alan adları,
     * dev için loopback — ReturnUrlPolicy::ALLOWED_HOSTS ile aynı kafa).
     * Allowlist dışı host → fallback (open-redirect/zehirli config kaynağı).
     * https-only: production'da http:// değeri kesin reddedilir; development'ta
     * platform `.env`'i `MUSIC_URL=http://home.coremusic.net:81` (repo dışında
     * değiştirilemez) olduğundan http yalnızca allowlist host'una izin verilir.
     */
    public static function redirectService(): IRedirectService
    {
        if (self::$redirectService === null) {
            self::$redirectService = new RedirectService(self::musicUrl());
        }

        return self::$redirectService;
    }

    /**
     * F9: dış URL allowlist + scheme denetimi (tek kaynak doğrulama noktası).
     *
     * @return string Doğrulanmış müzik paneli kök URL'si
     */
    private static function musicUrl(): string
    {
        $fallback = 'https://home.coremusic.net';
        $configured = trim((string) (defined('MUSIC_URL') ? MUSIC_URL : ''));
        if ($configured === '') {
            return $fallback;
        }

        $parts = parse_url($configured);
        if ($parts === false || !isset($parts['host'])) {
            return $fallback;
        }

        $host   = strtolower($parts['host']);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));

        // Host allowlist: coremusic.net + alt alan adları (+ dev loopback).
        $isLoopback = in_array($host, ['localhost', '127.0.0.1'], true);
        $hostAllowed = $isLoopback
            || $host === 'coremusic.net'
            || str_ends_with($host, '.coremusic.net');
        if (!$hostAllowed) {
            return $fallback;
        }

        // https-only: production'da http:// değeri koşulsuz reddedilir; development'ta
        // repo .env'i `MUSIC_URL=http://home.coremusic.net:81` (`.env*` düzenlenemez)
        // olduğu için allowlist host'unda http'e izin verilir. Diğer tüm şemalar
        // (javascript:, ftp:, data: …) her iki ortamda da reddedilir.
        $isProduction = (defined('APP_ENV_MODE') ? APP_ENV_MODE : 'production') === 'production';
        if ($scheme !== 'https' && ($isProduction || $scheme !== 'http')) {
            return $fallback;
        }

        return $configured;
    }

    /**
     * UserTransformer — Kullanıcı verisini API sözleşmesine göre şekillendirme.
     */
    public static function userTransformer(): IUserTransformer
    {
        if (self::$userTransformer === null) {
            self::$userTransformer = new UserTransformer();
        }

        return self::$userTransformer;
    }

    public static function authService(): IAuthService
    {
        if (self::$authService === null) {
            self::$authService = new AuthService(
                self::userRepository(),
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
                self::tokenService(),
                self::redirectService(),
                self::userTransformer(),
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

        $fallback = (string) ($_ENV['APP_PEPPER'] ?? (defined('APP_PEPPER') ? APP_PEPPER : ''));
        if ($fallback !== '') {
            return $fallback;
        }

        // F18: sessiz boş pepper YASAK — salt'' ile Argon2id hash'i üretilirse
        // tüm şifre doğrulamaları sessizce bozulur ve hata aylarca "yanlış şifre"
        // olarak görünür. Erken ve anlamlı hata: fail-fast.
        throw new \RuntimeException(
            'APP_PEPPER bulunamadı: auth.coremusic.net/config/.env içindeki APP_PEPPER satırı '
            . 'yok ya da boş (F18 fail-fast — pepper olmadan Argon2id doğrulama yapılamaz).'
        );
    }
}