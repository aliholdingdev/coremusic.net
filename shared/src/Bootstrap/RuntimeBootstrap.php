<?php declare(strict_types=1);

namespace CoreMusic\Bootstrap;

final class RuntimeBootstrap
{
    public static function boot(bool $debugMode = false): void
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');

        if ($debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            ini_set('display_errors', '0');
        }

        ini_set('log_errors', '1');
    }
}
