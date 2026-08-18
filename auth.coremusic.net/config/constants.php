<?php declare(strict_types=1);

/**
 * CoreMusic Auth — Constants
 *
 * .env dosyasından okunan değerleri define() ile sabitlere çevirir.
 * Bu dosya sadece bir kez include edilmelidir (require_once).
 */

if (!defined('APP_ENV_MODE')) {
    $envFile = dirname(__DIR__) . '/config/.env';
    if (file_exists($envFile)) {
        \CoreMusic\Config\EnvParser::loadIntoEnv($envFile);
    }
}

$env = static fn(string $key, string|int|bool|null $default = null): mixed =>
    $_ENV[$key] ?? getenv($key) ?: $default;

/* ─── Application ─── */
if (!defined('APP_ENV_MODE')) {
    $envMode = $env('APP_ENV_MODE', 'development');
    if (!in_array($envMode, ['development', 'production', 'test'], true)) {
        http_response_code(500);
        exit('Invalid APP_ENV_MODE');
    }
    define('APP_ENV_MODE', $envMode);
    define('DEBUG_MODE', APP_ENV_MODE !== 'production');
    define('APP_NAME', $env('APP_NAME', 'CoreMusic Auth'));
    define('APP_VERSION', $env('APP_VERSION', '2.1.0'));
    define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));
}

/* ─── Database ─── */
if (!defined('DB_HOST')) {
    define('DB_HOST', $env('DB_HOST', 'localhost'));
    define('DB_AUTH_NAME', $env('DB_AUTH_NAME', 'coremusic_auth'));
    define('DB_USER', $env('DB_USER', ''));
    define('DB_PASSWORD', $env('DB_PASSWORD', ''));
    define('DB_PORT', (int)$env('DB_PORT', 3306));
    define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));
}

/* ─── Session ─── */
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', $env('SESSION_NAME', 'COREMUSIC_SESS'));
    define('SESSION_LIFETIME', (int)$env('SESSION_LIFETIME', 7200));
    define('SESSION_COOKIE_DOMAIN', $env('SESSION_COOKIE_DOMAIN', '.coremusic.net'));
    define('SESSION_SAVE_PATH', $env('SESSION_SAVE_PATH', 'C:\temp'));
}

/* ─── Security ─── */
if (!defined('CSRF_TOKEN_LENGTH')) {
    define('CSRF_TOKEN_LENGTH', (int)$env('CSRF_TOKEN_LENGTH', 32));
    define('RATE_LIMIT_MAX', (int)$env('RATE_LIMIT_MAX', 60));
    define('RATE_LIMIT_WINDOW', (int)$env('RATE_LIMIT_WINDOW', 60));
}

/* ─── Mode Flags ─── */
if (!defined('TEST_MODE')) {
    define('TEST_MODE', in_array(strtolower((string)$env('TEST_MODE', 'false')), ['true', '1', 'yes', 'on'], true));
    define('FORCE_AUTH_BYPASS', in_array(strtolower((string)$env('FORCE_AUTH_BYPASS', 'false')), ['true', '1', 'yes', 'on'], true));
}

/* ─── Trusted Proxies ─── */
if (!defined('TRUSTED_PROXIES')) {
    define('TRUSTED_PROXIES', ['127.0.0.1', '::1']);
}

/* ─── Path ─── */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
    define('PAGES_PATH', ROOT_PATH . '/pages');
    define('INCLUDE_PATH', ROOT_PATH . '/include');
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

/* ─── URLs ─── */
if (!defined('AUTH_URL')) {
    define('AUTH_URL', $env('AUTH_URL', 'http://auth.coremusic.net'));
    define('MUSIC_URL', $env('MUSIC_URL', 'http://home.coremusic.net:81'));
    define('ASSETS_URL', $env('ASSETS_URL', 'http://assets.coremusic.net'));
}
