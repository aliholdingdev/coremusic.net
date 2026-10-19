<?php declare(strict_types=1);

namespace CoreMusic\Log;

/**
 * Logger Factory — PSR-3 uyumlu dosya tabanlı logger oluşturucu.
 *
 * Debug mode'da error.log, warning.log, info.log ayrı dosyalara yazılır.
 * Production'da sadece error.log aktif.
 */
final class LoggerFactory
{
    private static ?FileHandler $logger = null;

    public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
    {
        if (self::$logger === null) {
            $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
            self::$logger = new FileHandler($logDir, $level);
        }
        return self::$logger;
    }

    public static function reset(): void
    {
        self::$logger = null;
    }

    private static function resolveLogDir(): string
    {
        // Proje kök dizinine yaz (C:\www\coremusic.net\)
        return dirname(__DIR__, 3);
    }
}
