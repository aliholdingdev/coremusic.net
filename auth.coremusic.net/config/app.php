<?php declare(strict_types=1);

/**
 * CoreMusic Auth — App Config
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
    'session' => [
        'name'            => SESSION_NAME,
        'lifetime'        => SESSION_LIFETIME,
        'cookie_domain'   => SESSION_COOKIE_DOMAIN,
        'cookie_secure'   => true, // HTTPS detection runtime'da yapılır
        'cookie_samesite' => 'Lax',
    ],
    'security' => [
        'csrfTokenLength' => CSRF_TOKEN_LENGTH,
        'rateLimitMax'    => RATE_LIMIT_MAX,
        'rateLimitWindow' => RATE_LIMIT_WINDOW,
    ],
];
