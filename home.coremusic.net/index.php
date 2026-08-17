<?php declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Config\EnvParser;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\PageRouter\PageRouterKernel;

$envFile = __DIR__ . '/config/.env';
if (file_exists($envFile)) {
    EnvParser::loadIntoEnv($envFile);
}

$env = static fn(string $key, string|int|bool|null $default = null): mixed =>
    $_ENV[$key] ?? getenv($key) ?: $default;

$envMode = $env('APP_ENV_MODE', '');
if ($envMode === '' || !in_array($envMode, ['development', 'production', 'test'], true)) {
    http_response_code(500);
    exit('Configuration error: APP_ENV_MODE not set');
}

define('APP_ENV_MODE', $envMode);
define('DEBUG_MODE', APP_ENV_MODE !== 'production');
define('APP_NAME', $env('APP_NAME', 'CoreMusic'));
define('APP_VERSION', $env('APP_VERSION', '2.0.0'));
define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));
define('TEST_MODE', in_array(strtolower((string)$env('TEST_MODE', 'false')), ['true', '1', 'yes', 'on'], true));
define('FORCE_AUTH_BYPASS', in_array(strtolower((string)$env('FORCE_AUTH_BYPASS', 'false')), ['true', '1', 'yes', 'on'], true));

define('PAGES_PATH', __DIR__ . '/pages');
define('HEADER_PATH', __DIR__ . '/header.php');
define('FOOTER_PATH', __DIR__ . '/footer.php');
define('DEFAULT_PAGE', $env('DEFAULT_PAGE', 'home'));

define('SESSION_NAME', $env('SESSION_NAME', 'COREMUSIC_SESS'));
define('CSRF_TOKEN_LENGTH', (int)$env('CSRF_TOKEN_LENGTH', 32));
define('RATE_LIMIT_MAX', (int)$env('RATE_LIMIT_MAX', 60));
define('RATE_LIMIT_WINDOW', (int)$env('RATE_LIMIT_WINDOW', 60));
define('TRUSTED_PROXIES', ['127.0.0.1', '::1']);

RuntimeBootstrap::boot(DEBUG_MODE);

$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);

$currentHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$currentPort = (int)($_SERVER['SERVER_PORT'] ?? ($isHttps ? 443 : 80));

if (str_contains($currentHost, ':')) {
    [$currentHost, $portFromHost] = explode(':', $currentHost, 2);
    $currentPort = (int)$portFromHost;
}

$domainConfig = new DomainConfig(dirname(__DIR__) . '/shared/config/domain.php');
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$config = new ConfigManager([
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
        'host'          => $currentHost,
        'port'          => $currentPort,
        'isHttps'       => $isHttps,
        'subdomainPort' => $isHttps ? 443 : 80,
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
]);

$kernel = new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);
try {
    $routesFile = dirname(__DIR__) . '/shared/config/routes.php';
    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
} catch (\Throwable $e) {
    error_log('[Home] Unhandled exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo 'Internal Server Error';
}
