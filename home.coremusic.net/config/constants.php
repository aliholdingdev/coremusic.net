<?php declare(strict_types=1);

/**
 * CoreMusic Home — Constants
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
    $envMode = $env('APP_ENV_MODE', '');
    if ($envMode === '' || !in_array($envMode, ['development', 'production', 'test'], true)) {
        http_response_code(500);
        exit('Configuration error: APP_ENV_MODE not set');
    }
    define('APP_ENV_MODE', $envMode);
    define('DEBUG_MODE', APP_ENV_MODE !== 'production');
    define('APP_NAME', $env('APP_NAME', 'CoreMusic'));
    define('APP_VERSION', $env('APP_VERSION', '2.3.0'));
    define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));
}

/* --- Mode Flags --- */
if (!defined('TEST_MODE')) {
    define('TEST_MODE', in_array(strtolower((string)$env('TEST_MODE', 'false')), ['true', '1', 'yes', 'on'], true));

    // Auth bypass — auth.coremusic.net'in /bypass-status endpoint'inden HTTP ile okunur
    // SSRF koruması: Host allowlist + scheme validation + timeout
    $authBypassDefault = 'false';
    $bypassUuid = '';
    $bypassRole = '';
    $bypassUsername = '';
    $authUrl = defined('AUTH_URL') ? AUTH_URL : 'http://auth.coremusic.net';
    $authHost = parse_url($authUrl, PHP_URL_HOST);
    $authScheme = parse_url($authUrl, PHP_URL_SCHEME);
    $allowedAuthHosts = ['auth.coremusic.net', 'localhost', '127.0.0.1'];
    if (in_array($authScheme, ['http', 'https'], true) && in_array($authHost, $allowedAuthHosts, true)) {
        $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
        $authConfigResponse = @file_get_contents($authUrl . '/bypass-status', false, $ctx);
        if ($authConfigResponse !== false) {
            $authConfig = json_decode($authConfigResponse, true);
            if (is_array($authConfig)) {
                if (isset($authConfig['force_auth_bypass'])) {
                    $authBypassDefault = $authConfig['force_auth_bypass'] ? 'true' : 'false';
                }
                $bypassUuid = $authConfig['bypass_uuid'] ?? '';
                $bypassRole = $authConfig['bypass_role'] ?? '';
                $bypassUsername = $authConfig['bypass_username'] ?? '';
            }
        }
    }
    define('FORCE_AUTH_BYPASS', in_array(strtolower($authBypassDefault), ['true', '1', 'yes', 'on'], true));
    define('BYPASS_USER_UUID', $bypassUuid);
    define('BYPASS_ROLE', $bypassRole);
    define('BYPASS_USERNAME', $bypassUsername);
}

/* --- Path --- */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
    define('PAGES_PATH', ROOT_PATH . '/pages');
    define('HEADER_PATH', ROOT_PATH . '/header.php');
    define('FOOTER_PATH', ROOT_PATH . '/footer.php');
    define('DEFAULT_PAGE', $env('DEFAULT_PAGE', 'home'));
}

/* --- Session --- */
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', $env('SESSION_NAME', 'COREMUSIC_SESS'));
    define('CSRF_TOKEN_LENGTH', (int)$env('CSRF_TOKEN_LENGTH', 32));
    define('RATE_LIMIT_MAX', (int)$env('RATE_LIMIT_MAX', 60));
    define('RATE_LIMIT_WINDOW', (int)$env('RATE_LIMIT_WINDOW', 60));
}

// Session save path — php.ini'den oku, fallback sys_get_temp_dir
$sessionPath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0750, true);
}
session_save_path($sessionPath);
ini_set('session.save_path', $sessionPath);

/* --- Trusted Proxies --- */
if (!defined('TRUSTED_PROXIES')) {
    define('TRUSTED_PROXIES', ['127.0.0.1', '::1']);
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
if (!defined('AUTH_URL')) {
    define('AUTH_URL', $env('AUTH_URL', COREMUSIC_SCHEME . '://auth.coremusic.net'));
    define('MUSIC_URL', $env('MUSIC_URL', COREMUSIC_SCHEME . '://home.coremusic.net'));
    define('ASSETS_URL', $env('ASSETS_URL', COREMUSIC_SCHEME . '://assets.coremusic.net'));
}

/* --- Database (HomeDB — user, music, etc.) --- */
if (!defined('DB_HOST')) {
    define('DB_HOST', $env('DB_HOST', 'localhost'));
    define('DB_HOME_NAME', $env('DB_HOME_NAME', 'coremusic_user'));
    define('DB_USER', $env('DB_USER', ''));
    define('DB_PASSWORD', $env('DB_PASSWORD', ''));
    define('DB_PORT', (int)$env('DB_PORT', 3306));
    define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));
}

/* --- Auth Pepper (auth_key validate için) --- */
if (!defined('APP_PEPPER')) {
    define('APP_PEPPER', $env('APP_PEPPER', ''));
}
