<?php declare(strict_types=1);

/**
 * CoreMusic Auth Service — Entry Point
 *
 * Shared SPA Router (PageRouterKernel) kullanır.
 * Auth-specific kodlar: include/ dizininde.
 * Auth-specific sayfalar: pages/ dizininde.
 */

require_once __DIR__ . '/autoload.php';

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Config\EnvParser;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Auth\Container\AuthContainer;
use CoreMusic\Auth\Handler\AuthPostHandler;
use CoreMusic\PageRouter\PageRouterKernel;

const MAX_REQUEST_BODY_SIZE = 8192;

/* ─── Environment ─── */
$envFile = __DIR__ . '/config/.env';
if (file_exists($envFile)) {
    EnvParser::loadIntoEnv($envFile);
}

$env = static fn(string $key, string|int|bool|null $default = null): mixed =>
    $_ENV[$key] ?? getenv($key) ?: $default;

$envMode = $env('APP_ENV_MODE', 'development');
if (!in_array($envMode, ['development', 'production', 'test'], true)) {
    http_response_code(500);
    exit('Invalid APP_ENV_MODE');
}

define('APP_ENV_MODE', $envMode);
define('DEBUG_MODE', APP_ENV_MODE !== 'production');
define('APP_NAME', $env('APP_NAME', 'CoreMusic Auth'));
define('APP_VERSION', $env('APP_VERSION', '2.0.0'));
define('APP_TIMEZONE', $env('APP_TIMEZONE', 'Europe/Istanbul'));

define('DB_HOST', $env('DB_HOST', 'localhost'));
define('DB_AUTH_NAME', $env('DB_AUTH_NAME', 'coremusic_auth'));
define('DB_USER', $env('DB_USER', ''));
define('DB_PASSWORD', $env('DB_PASSWORD', ''));
define('DB_PORT', (int)$env('DB_PORT', 3306));
define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));

define('SESSION_NAME', $env('SESSION_NAME', 'COREMUSIC_SESS'));
define('SESSION_LIFETIME', (int)$env('SESSION_LIFETIME', 7200));
define('SESSION_COOKIE_DOMAIN', $env('SESSION_COOKIE_DOMAIN', '.coremusic.net'));
define('CSRF_TOKEN_LENGTH', (int)$env('CSRF_TOKEN_LENGTH', 32));
define('RATE_LIMIT_MAX', (int)$env('RATE_LIMIT_MAX', 60));
define('RATE_LIMIT_WINDOW', (int)$env('RATE_LIMIT_WINDOW', 60));
define('TEST_MODE', in_array(strtolower((string)$env('TEST_MODE', 'false')), ['true', '1', 'yes', 'on'], true));
define('FORCE_AUTH_BYPASS', in_array(strtolower((string)$env('FORCE_AUTH_BYPASS', 'false')), ['true', '1', 'yes', 'on'], true));
define('TRUSTED_PROXIES', ['127.0.0.1', '::1']);

define('ROOT_PATH', __DIR__);
define('PAGES_PATH', ROOT_PATH . '/pages');
define('INCLUDE_PATH', ROOT_PATH . '/include');
define('CONFIG_PATH', ROOT_PATH . '/config');

define('AUTH_URL', $env('AUTH_URL', 'https://auth.coremusic.net'));
define('MUSIC_URL', $env('MUSIC_URL', 'https://home.coremusic.net'));

RuntimeBootstrap::boot(DEBUG_MODE);

/* ─── HTTPS Detection ─── */
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

$currentHost = $_SERVER['HTTP_HOST'] ?? 'auth.coremusic.net';
$currentPort = (int)($_SERVER['SERVER_PORT'] ?? ($isHttps ? 443 : 80));

if (str_contains($currentHost, ':')) {
    [$currentHost, $portFromHost] = explode(':', $currentHost, 2);
    $currentPort = (int)$portFromHost;
}

/* ─── Config ─── */
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
    'session' => [
        'name'            => SESSION_NAME,
        'lifetime'        => SESSION_LIFETIME,
        'cookie_domain'   => SESSION_COOKIE_DOMAIN,
        'cookie_secure'   => $isHttps,
        'cookie_samesite' => 'Lax',
    ],
    'security' => [
        'csrfTokenLength' => CSRF_TOKEN_LENGTH,
        'rateLimitMax'    => RATE_LIMIT_MAX,
        'rateLimitWindow' => RATE_LIMIT_WINDOW,
    ],
]);

/* ─── CORS ─── */
$isProduction = APP_ENV_MODE === 'production';
$allowed = [];
foreach (['home', 'music', 'coremusic'] as $name) {
    $url = $domainConfig->getUrl($name);
    if ($url !== '') {
        $allowed[] = $url;
    }
}
if (!$isProduction) {
    $allowed[] = 'http://home.coremusic.net';
    $allowed[] = 'http://music.coremusic.net';
}
$allowed[] = 'https://home.coremusic.net';
$allowed[] = 'https://music.coremusic.net';
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/* ─── Special Routes (JSON endpoints — PageRouterKernel'den önce) ─── */
$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method     = $_SERVER['REQUEST_METHOD'];

if ($requestUri === '/health' || $requestUri === '/session' || $requestUri === '/validate-key') {
    $container  = AuthContainer::getInstance($config, $domainConfig);
    $controller = $container->get(AuthController::class);

    // Session başlat (CSRF nonce için gerekli)
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name(SESSION_NAME);
        session_start();
    }

    $result = match ($requestUri) {
        '/health'      => $controller->handleHealth([]),
        '/session'     => $controller->handleSessionCheck([]),
        '/validate-key' => $controller->handleValidateKey([
            'query_params' => $_GET,
            'body'         => $_POST + (json_decode(file_get_contents('php://input'), true) ?? []),
            'server'       => $_SERVER,
        ]),
        default => ['httpStatus' => 404],
    };

    http_response_code($result['httpStatus'] ?? 200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ─── Root Redirect ─── */
if ($requestUri === '' || $requestUri === '/') {
    $redirectUri = (defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net:81') . '/auth/callback';
    $params = http_build_query([
        'client_id'     => 'coremusic-web',
        'response_type' => 'session',
        'redirect_uri'  => $redirectUri,
    ]);
    header('Location: /select-gender?' . $params, true, 302);
    exit;
}

/* ─── Default OAuth Redirect (missing params) ─── */
$authPages = ['login', 'register', 'forgot-password', 'reset-password'];
$pageName  = ltrim($requestUri, '/');
if (in_array($pageName, $authPages, true) && empty($_GET['client_id'])) {
    $defaultRedirectUri = (defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net:81') . '/auth/callback';
    $params = http_build_query([
        'client_id'     => 'coremusic-web',
        'response_type' => 'session',
        'redirect_uri'  => $defaultRedirectUri,
    ]);
    header('Location: /' . $pageName . '?' . $params, true, 302);
    exit;
}

/* ─── AuthContainer + Handler ─── */
$container  = AuthContainer::getInstance($config, $domainConfig);
$controller = $container->get(AuthController::class);
$authHandler = new AuthPostHandler($controller);

// PageRouter, handler'ları URI'ye göre arar ($handlers[$uri]).
// Auth POST route'larının tamamı için handler kaydı yapıyoruz.
$handlers = [];
foreach (['login', 'register', 'select-gender', 'forgot-password', 'reset-password', 'logout', 'set-gender'] as $uri) {
    $handlers[$uri] = $authHandler;
}

// Session başlat ve CSRF token üret (HTML shell ve sayfalar için)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SESSION_NAME);
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
}

/* ─── Shared Components ─── */
$authHelper = new \CoreMusic\PageRouter\PageRouterHelper();
$urlBuilder = new \CoreMusic\PageRouter\AuthUrlBuilder($domainConfig, $authHelper);

// Auth.coremusic.net kendi auth sayfasıdır — checkAuthRedirectRoute self-redirect oluşturur.
// skipAuthRedirect: true ile sadece o check atlanır, authenticated→/home redirect çalışmaya devam eder.
$registry = new \CoreMusic\PageRouter\RouteRegistry();
$authGuard = new \CoreMusic\PageRouter\AuthGuard($authHelper, $urlBuilder, true);
$router   = new \CoreMusic\PageRouter\PageRouter(
    $registry,
    $config,
    $domainConfig,
    $authHelper,
    $authGuard,
    $urlBuilder,
    new \CoreMusic\Cache\PageCacheAdapter(),
    $handlers,
);

/* ─── PageRouterKernel ─── */
$kernel = new PageRouterKernel(
    config:        $config,
    domainConfig:  $domainConfig,
    headerPath:    null,
    footerPath:    null,
    registry:      $registry,
    router:        $router,
    handlers:      $handlers,
);

try {
    $routesFile = dirname(__DIR__) . '/shared/config/auth-routes.php';
    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
} catch (\Throwable $e) {
    error_log('[Auth] Unhandled exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => ['code' => 'SERVER_INTERNAL_ERROR', 'message' => 'Sunucu hatası.']], JSON_UNESCAPED_UNICODE);
}
