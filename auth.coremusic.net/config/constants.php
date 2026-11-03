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

/* --- Application --- */
if (!defined('APP_ENV_MODE')) {
    $envMode = $env('APP_ENV_MODE', 'development');
    if (!in_array($envMode, ['development', 'production', 'test'], true)) {
        http_response_code(500);
        exit('Invalid APP_ENV_MODE');
    }
    define('APP_ENV_MODE', $envMode);
    define('DEBUG_MODE', APP_ENV_MODE !== 'production');
    define('APP_NAME', $env('APP_NAME', 'CoreMusic Auth'));
    define('APP_VERSION', $env('APP_VERSION', '2.3.0'));
    define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));
}

/* --- Database --- */
if (!defined('DB_HOST')) {
    define('DB_HOST', $env('DB_HOST', 'localhost'));
    define('DB_AUTH_NAME', $env('DB_AUTH_NAME', 'coremusic_auth'));
    define('DB_USER', $env('DB_USER', ''));
    define('DB_PASSWORD', $env('DB_PASSWORD', ''));
    define('DB_PORT', (int)$env('DB_PORT', 3306));
    define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));
}

/* --- Session --- */
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', $env('SESSION_NAME', 'COREMUSIC_SESS'));
    define('SESSION_LIFETIME', (int)$env('SESSION_LIFETIME', 7200));
    define('SESSION_COOKIE_DOMAIN', $env('SESSION_COOKIE_DOMAIN', '.coremusic.net'));
    $sessionSavePath = $env('SESSION_SAVE_PATH', '') ?: sys_get_temp_dir() . '/coremusic_sessions';
    define('SESSION_SAVE_PATH', $sessionSavePath);
}

/* --- Security --- */
if (!defined('CSRF_TOKEN_LENGTH')) {
    define('CSRF_TOKEN_LENGTH', (int)$env('CSRF_TOKEN_LENGTH', 32));
    define('RATE_LIMIT_MAX', (int)$env('RATE_LIMIT_MAX', 60));
    define('RATE_LIMIT_WINDOW', (int)$env('RATE_LIMIT_WINDOW', 60));
}

/* --- Security (Pepper) --- */
if (!defined('APP_PEPPER')) {
    $pepper = $env('APP_PEPPER', '');
    if ($pepper === '') {
        http_response_code(500);
        error_log('[CRITICAL] APP_PEPPER is empty — all password hashes would be identical. Set APP_PEPPER in .env');
        exit('Server misconfiguration: APP_PEPPER not set.');
    }
    define('APP_PEPPER', $pepper);
}

/* --- Mode Flags --- */
if (!defined('TEST_MODE')) {
    define('TEST_MODE', in_array(strtolower((string)$env('TEST_MODE', 'false')), ['true', '1', 'yes', 'on'], true));

    // Auth bypass — YALNIZ yerel yapılandırma (.env). Uzak HTTP fetch yasak (fail-closed).
    // APP_ENV_MODE=production → bayrak hiç okunmaz (home paritesi — SEC-04).
    $isProdEnv     = defined('APP_ENV_MODE') && APP_ENV_MODE === 'production';
    $bypassRaw     = '';
    $bypassUuid    = '';
    $bypassRole    = '';
    $bypassUsername= '';
    if (!$isProdEnv) {
        $bypassRaw      = (string)$env('FORCE_AUTH_BYPASS', 'false');
        $bypassUuid     = (string)$env('BYPASS_USER_UUID', '00000000000000000000000000000001');
        $bypassRole     = (string)$env('BYPASS_ROLE', 'test_user');
        $bypassUsername = (string)$env('BYPASS_USERNAME', 'test_user');
    }
    define('FORCE_AUTH_BYPASS', !$isProdEnv && in_array(strtolower($bypassRaw), ['true', '1', 'yes', 'on'], true));
    define('BYPASS_USER_UUID', $bypassUuid);
    define('BYPASS_ROLE', $bypassRole);
    define('BYPASS_USERNAME', $bypassUsername);
}

/* --- Trusted Proxies --- */
if (!defined('TRUSTED_PROXIES')) {
    define('TRUSTED_PROXIES', ['127.0.0.1', '::1']);
}

/* --- Path --- */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
    define('PAGES_PATH', ROOT_PATH . '/pages');
    define('INCLUDE_PATH', ROOT_PATH . '/include');
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

/* --- URLs --- */
// Scheme otomatik algılama: HTTPS termination proxy varsa HTTPS, yoksa HTTP
if (!defined('COREMUSIC_SCHEME')) {
    $detectedScheme = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    ) ? 'https' : 'http';
    define('COREMUSIC_SCHEME', $detectedScheme);
}

$portSuffix = '';
if (!empty($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], ':')) {
    $parts = explode(':', $_SERVER['HTTP_HOST']);
    $portSuffix = ':' . end($parts);
} elseif (!empty($_SERVER['SERVER_PORT']) && !in_array((int)$_SERVER['SERVER_PORT'], [80, 443], true)) {
    $portSuffix = ':' . $_SERVER['SERVER_PORT'];
}

$withPort = static function (string $url, string $portSuffix): string {
    if ($portSuffix === '') {
        return $url;
    }
    $parts = parse_url($url);
    if (!isset($parts['port']) && isset($parts['host'])) {
        $scheme = $parts['scheme'] ?? 'http';
        $host = $parts['host'];
        $path = $parts['path'] ?? '';
        return $scheme . '://' . $host . $portSuffix . $path;
    }
    return $url;
};

if (!defined('AUTH_URL')) {
    define('AUTH_URL', $withPort((string)$env('AUTH_URL', COREMUSIC_SCHEME . '://auth.coremusic.net'), $portSuffix));
    define('MUSIC_URL', $withPort((string)$env('MUSIC_URL', COREMUSIC_SCHEME . '://home.coremusic.net'), $portSuffix));
    define('ASSETS_URL', $withPort((string)$env('ASSETS_URL', COREMUSIC_SCHEME . '://assets.coremusic.net'), $portSuffix));
}
if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', $withPort((string)$env('ADMIN_URL', COREMUSIC_SCHEME . '://admin.coremusic.net'), $portSuffix));
}
