<?php

declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * SessionInitializer — giriş noktaları için merkezi session başlatma.
 *
 * PRD-SES-001: session_* çağrıları SessionBootstrapper'da tek yerde yapılır;
 * bu sınıf yalnız idempotent bir kapı (static guard) sağlar ve bootstrapper'a delege eder.
 */
final class SessionInitializer
{
    private static bool $started = false;

    /**
     * Session'ı başlat (aktifse / daha önce başlatıldıysa no-op). Birden çok çağrı güvenli.
     */
    public static function ensureStarted(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        SessionBootstrapper::ensureStarted();
        self::$started = true;
    }
}
