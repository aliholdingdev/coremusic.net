<?php declare(strict_types=1);

/**
 * CoreMusic Home — App Config
 *
 * ConfigManager'a verilecek konfigürasyon dizisini döndürür.
 * constants.php include edildikten sonra çağrılmalıdır.
 */

return [
    'app' => [
        'name'              => APP_NAME,
        'version'           => APP_VERSION,
        'env'               => APP_ENV_MODE,
        'timezone'          => APP_TIMEZONE,
        'debug'             => DEBUG_MODE,
        'test_mode'         => TEST_MODE,
        'force_auth_bypass' => FORCE_AUTH_BYPASS,
    ],
    'domain' => [
        'host'          => $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost',
        'port'          => (int)($_SERVER['SERVER_PORT'] ?? 80),
        'isHttps'       => (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        ),
        'subdomainPort' => 80,
    ],
    'session' => [
        'name'     => SESSION_NAME,
        'lifetime' => 7200,
    ],
    'security' => [
        'csrfTokenLength' => CSRF_TOKEN_LENGTH,
        'rateLimitMax'    => RATE_LIMIT_MAX,
        'rateLimitWindow' => RATE_LIMIT_WINDOW,
    ],
];
