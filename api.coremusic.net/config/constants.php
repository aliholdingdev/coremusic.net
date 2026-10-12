<?php declare(strict_types=1);

/**
 * CoreMusic API — Constants
 *
 * .env dosyasından okunan değerleri define() ile sabitlere çevirir.
 * Bu dosya sadece bir kez include edilmelidir (require_once).
 *
 * Not: Faz 0 (iskelet) — .env zorunlu DEĞİLDIR; eksik değerler varsayılanla düşer.
 * API_KEY_PEPPER yalnız API key uçları (sonraki fazlar) devreye girdiğinde zorunlu olacak.
 *
 * SSOT fallback (2026-09-29): API'nin kendi config/.env dosyası çoğu kurulumda yok;
 * DB kimlik bilgileri yalnızca auth.coremusic.net/config/.env içinde tutulur. Kendi
 * .env yoksa veya DB anahtarları hâlâ boşsa, auth .env dosyası (salt OKU) fallback
 * olarak yüklenir — aynı SSOT deseni ApiAuthContainer::passwordPepper() ile kullanılır
 * (@see ApiAuthContainer::passwordPepper()). Guardrail: Secret Yok — değerler koda
 * basılmaz, konsola yazılmaz, dosyaya yazılmaz.
 */

if (!defined('APP_ENV_MODE')) {
    $ownEnvFile = dirname(__DIR__) . '/config/.env';
    if (file_exists($ownEnvFile)) {
        \CoreMusic\Config\EnvParser::loadIntoEnv($ownEnvFile);
    }

    // DB anahtarı tanımlı mı, boş mu? (false = getenv'te yok)
    $dbKeyMissing = static function (string $key): bool {
        $value = $_ENV[$key] ?? getenv($key);

        return $value === null || $value === false || $value === '';
    };

    // Kendi .env yoksa VEYA DB anahtarlarının biri hâlâ boşsa → auth .env fallback.
    if (
        !file_exists($ownEnvFile)
        || $dbKeyMissing('DB_HOST')
        || $dbKeyMissing('DB_NAME')
        || $dbKeyMissing('DB_USER')
        || $dbKeyMissing('DB_PASSWORD')
    ) {
        // __DIR__ = api.coremusic.net/config → dirname(__DIR__) = api.coremusic.net
        $authEnvFile = dirname(__DIR__) . '/../auth.coremusic.net/config/.env';
        if (is_file($authEnvFile)) {
            // loadIntoEnv() mevcut $_ENV değerini EZMEZ; bu yüzden "tanımlı ama boş"
            // DB anahtarlarını fallback öncesi kaldırıyoruz ki auth .env doldurabilsin.
            foreach (['DB_HOST', 'DB_NAME', 'DB_AUTH_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT', 'DB_CHARSET'] as $dbKey) {
                if ($dbKeyMissing($dbKey)) {
                    unset($_ENV[$dbKey]);
                }
            }
            \CoreMusic\Config\EnvParser::loadIntoEnv($authEnvFile);
        }
    }

    // Auth .env DB adını DB_AUTH_NAME anahtarıyla verir; API DB_NAME bekler.
    // define() öncesi tek seferlik eşleme — tekrar define PHP'de warning verir.
    if (!isset($_ENV['DB_NAME']) && isset($_ENV['DB_AUTH_NAME']) && $_ENV['DB_AUTH_NAME'] !== '') {
        $_ENV['DB_NAME'] = $_ENV['DB_AUTH_NAME'];
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
    define('APP_NAME', $env('APP_NAME', 'CoreMusic API'));
    define('APP_VERSION', $env('APP_VERSION', '1.0.0'));
    define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));
}

/* --- Database --- */
if (!defined('DB_HOST')) {
    define('DB_HOST', $env('DB_HOST', 'localhost'));
    define('DB_NAME', $env('DB_NAME', 'coremusic_auth'));
    define('DB_USER', $env('DB_USER', ''));
    define('DB_PASSWORD', $env('DB_PASSWORD', ''));
    define('DB_PORT', (int)$env('DB_PORT', 3306));
    define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));
}

/* --- Session (Faz 0: tanımlı, aktif değil — API stateless) --- */
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

/* --- Security (API Key Pepper) — Faz 0'da sadece okunur, doğrulama YOK --- */
if (!defined('API_KEY_PEPPER')) {
    define('API_KEY_PEPPER', (string)$env('API_KEY_PEPPER', ''));
}

/* --- Mode Flags --- */
if (!defined('TEST_MODE')) {
    define('TEST_MODE', in_array(strtolower((string)$env('TEST_MODE', 'false')), ['true', '1', 'yes', 'on'], true));
}

/* --- Trusted Proxies --- */
if (!defined('TRUSTED_PROXIES')) {
    define('TRUSTED_PROXIES', ['127.0.0.1', '::1']);
}

/* --- Path --- */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
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
if (!defined('API_URL')) {
    define('API_URL', $env('API_URL', COREMUSIC_SCHEME . '://api.coremusic.net'));
    define('AUTH_URL', $env('AUTH_URL', COREMUSIC_SCHEME . '://auth.coremusic.net'));
    define('MUSIC_URL', $env('MUSIC_URL', COREMUSIC_SCHEME . '://home.coremusic.net'));
    define('ASSETS_URL', $env('ASSETS_URL', COREMUSIC_SCHEME . '://assets.coremusic.net'));
}
