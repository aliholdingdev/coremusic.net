<?php declare(strict_types=1);

namespace CoreMusic\Security;

use CoreMusic\Config\ConfigManager;

final class SecurityHelper
{
    /** @var string[] Allowed redirect hosts (SSOT — AuthController bu listeyi kullanır) */
    private const ALLOWED_REDIRECT_HOSTS = [
        'music.coremusic.net',
        'admin.coremusic.net',
        'auth.coremusic.net',
        'home.coremusic.net',
        'coremusic.net',
        'localhost',
        '127.0.0.1',
    ];

    /** @var int[] Allowed redirect ports */
    private const ALLOWED_PORTS = [80, 443, 81, 3001, 5000, 6000, 9741, 9742, 9743];

    /**
     * Redirect URI'nin güvenli olup olmadığını kontrol et.
     * Open Redirect saldırılarını önler.
     *
     * @see ADR-010, AuthController::isRedirectUriSafe()
     */
    public static function isRedirectUriSafe(string $uri): bool
    {
        // Relative paths are always safe
        if ($uri === '' || $uri === '/' || str_starts_with($uri, '/')) {
            return true;
        }

        $parsed = parse_url($uri);
        if ($parsed === false || empty($parsed['host'])) {
            return false;
        }

        $host = strtolower($parsed['host']);

        if (isset($parsed['port']) && !in_array((int)$parsed['port'], self::ALLOWED_PORTS, true)) {
            return false;
        }

        foreach (self::ALLOWED_REDIRECT_HOSTS as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost)) {
                return true;
            }
        }

        return false;
    }

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
