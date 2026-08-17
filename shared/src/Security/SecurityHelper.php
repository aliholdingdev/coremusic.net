<?php declare(strict_types=1);

namespace CoreMusic\Security;

use CoreMusic\Config\ConfigManager;

final class SecurityHelper
{
    public static function isTestBypassActive(ConfigManager $config): bool
    {
        if ($config->get('app.env') === 'production') {
            return false;
        }
        return self::isTruthy($config->get('app.test_mode', false))
            || self::isTruthy($config->get('app.force_auth_bypass', false));
    }

    private static function isTruthy(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_string($value) => in_array(strtolower($value), ['true', '1', 'yes', 'on'], true),
            is_int($value) => $value === 1,
            default => false,
        };
    }

    public static function logTestBypass(string $context, string $file, int $line): void
    {
        $logFile = dirname(__DIR__, 4) . '/coremusic_php_errors.log';
        $message = sprintf(
            "[%s] [TEST_BYPASS] context=%s file=%s line=%d\n",
            date('d-M-Y H:i:s e'),
            $context,
            basename($file),
            $line
        );
        @file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
    }
}
