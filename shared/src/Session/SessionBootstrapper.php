<?php

declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * SessionBootstrapper — session_* API'lerine tek yetkili giriş noktası (SSOT).
 *
 * session_name / session_set_cookie_params / session_save_path / session_start
 * çağrıları YALNIZCA bu sınıfta bulunur. Diğer tüm kod Bootstrapper'a delege eder.
 *
 * Idempotenttir: ensureStarted() tekrar tekrar çağrılabilir.
 */
final class SessionBootstrapper
{
    private static bool $configured = false;

    public static function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = SessionConfig::fromEnvironment();
        self::configure($config);
        session_start();
    }

    /**
     * Session'ı sıfırla: verileri temizle, ID'yi yenile, taze session başlat.
     * (destroy → cookie temizliği → start → regenerate_id zincirinin tek yeri)
     */
    public static function restartFresh(): void
    {
        $config = SessionConfig::fromEnvironment();

        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        if (ini_get('session.use_cookies')) {
            setcookie(
                $config->name,
                '',
                time() - SessionConfig::COOKIE_EXPIRY,
                $config->cookieParams()['path'],
                $config->cookieParams()['domain'],
                $config->secure,
                $config->cookieParams()['httponly'],
            );
        }

        self::configure($config);
        session_start();
        session_regenerate_id(true);
    }

    public static function writeClose(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    private static function configure(SessionConfig $config): void
    {
        if (self::$configured && session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name($config->name);

        if ($config->savePath !== '') {
            session_save_path($config->savePath);
        }

        session_set_cookie_params($config->cookieParams());

        self::$configured = true;
    }
}
