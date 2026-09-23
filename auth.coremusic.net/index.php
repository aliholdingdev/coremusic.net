<?php declare(strict_types=1);

/**
 * CoreMusic Auth Service â€” Entry Point
 *
 * Shared SPA Router (PageRouterKernel) kullanÄ±r.
 * Auth-specific kodlar: include/ dizininde.
 * Auth-specific sayfalar: pages/ dizininde.
 */

require_once __DIR__ . '/autoload.php';

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Auth\Container\AuthContainer;
use CoreMusic\Auth\Handler\AuthPostHandler;
use CoreMusic\Auth\Handler\AuthKeyRedirectHandler;
use CoreMusic\Auth\Handler\AutoRedirectHandler;
use CoreMusic\PageRouter\PageRouterKernel;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Session\SessionBootstrapper;

const MAX_REQUEST_BODY_SIZE = 8192;

/* â”€â”€â”€ Config (constants + app + cors) â”€â”€â”€ */
require_once __DIR__ . '/config/constants.php';
$appConfig  = require __DIR__ . '/config/app.php';
$corsConfig = require __DIR__ . '/config/cors.php';

RuntimeBootstrap::boot(DEBUG_MODE);

/* â”€â”€â”€ Logger â”€â”€â”€ */
$logger = LoggerFactory::getInstance(dirname(__DIR__), DEBUG_MODE ? 'debug' : 'error');

/* â”€â”€â”€ HTTPS Detection â”€â”€â”€ */
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

/* â”€â”€â”€ Config Objects â”€â”€â”€ */
$domainConfig = new DomainConfig(dirname(__DIR__) . '/shared/config/domain.php');
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$appConfig['session']['cookie_secure'] = $isHttps;
$config = new ConfigManager($appConfig);

/* â”€â”€â”€ Request Parsing â”€â”€â”€ */
$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method     = $_SERVER['REQUEST_METHOD'];
$pageName   = ltrim($requestUri, '/');

/* â”€â”€â”€ 1. Special JSON Routes (before PageRouterKernel) â”€â”€â”€ */
if ($requestUri === '/health' || $requestUri === '/session' || $requestUri === '/validate-key' || $requestUri === '/bypass-status') {
    $container  = AuthContainer::getInstance($config, $domainConfig);
    $controller = $container->get(AuthController::class);

    SessionBootstrapper::ensureStarted();

    $result = match ($requestUri) {
        '/health'       => $controller->handleHealth([]),
        '/session'      => $controller->handleSessionCheck([]),
        '/validate-key' => $controller->handleValidateKey([
            'query_params' => $_GET,
            'body'         => $_POST + (json_decode(file_get_contents('php://input'), true) ?? []),
            'server'       => $_SERVER,
        ]),
        '/bypass-status' => [
            'httpStatus' => 200,
            'force_auth_bypass' => defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS,
            'test_mode' => defined('TEST_MODE') && TEST_MODE,
            'bypass_uuid' => defined('BYPASS_USER_UUID') ? constant('BYPASS_USER_UUID') : '',
            'bypass_role' => defined('BYPASS_ROLE') ? constant('BYPASS_ROLE') : '',
            'bypass_username' => defined('BYPASS_USERNAME') ? constant('BYPASS_USERNAME') : '',
        ],
        default => ['httpStatus' => 404],
    };

    http_response_code($result['httpStatus'] ?? 200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* â”€â”€â”€ 2. Root Redirect (auth_key callback veya select-gender) â”€â”€â”€ */
if ($requestUri === '' || $requestUri === '/') {
    // Auth bypass aktifse -> direkt home'a redirect et
    if (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS) {
        $bypassKey = hash_hmac('sha256', 'bypass_' . date('Y-m-d'), defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass');
        $redirectUrl = (defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net:81')
            . '/auth/callback?auth_key=' . urlencode($bypassKey);
        header('Location: ' . $redirectUrl, true, 302);
        exit;
    }

    $authKeyHandler = new AuthKeyRedirectHandler($config, $domainConfig);
    $authKeyHandler->handle();

    // auth_key yoksa â†’ select-gender'e yÃ¶nlendir
    $redirectUri = MUSIC_URL . '/auth/callback';
    $params = http_build_query([
        'client_id'     => 'coremusic-web',
        'response_type' => 'session',
        'redirect_uri'  => $redirectUri,
    ]);
    header('Location: /select-gender?' . $params, true, 302);
    exit;
}

/* --- 3. Auth Bypass: Tum auth sayfalarini home'a yonlendir --- */
if (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS && $method !== 'POST') {
    $redirectUrl = (defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net:81') . '/auth/callback';
    header('Location: ' . $redirectUrl, true, 302);
    exit;
}

/* â”€â”€â”€ 4. Gender Gate: /login â†’ /select-gender if no gender â”€â”€â”€ */
if ($method !== 'POST' && $pageName === 'login') {
    SessionBootstrapper::ensureStarted();
    $sessionGender = $_SESSION['cm_gender'] ?? '';
    $cookieGender  = $_COOKIE['cm_gender'] ?? '';
    if (empty($sessionGender) && empty($cookieGender)) {
        $params = http_build_query($_GET);
        header('Location: /select-gender' . ($params ? '?' . $params : ''), true, 302);
        exit;
    }
}

/* â”€â”€â”€ 4. Default OAuth Redirect (GET only, missing params) â”€â”€â”€ */
$authPages = ['login', 'register', 'forgot-password', 'reset-password'];
if ($method !== 'POST' && in_array($pageName, $authPages, true) && empty($_GET['client_id'])) {
    $defaultRedirectUri = MUSIC_URL . '/auth/callback';
    $params = http_build_query([
        'client_id'     => 'coremusic-web',
        'response_type' => 'session',
        'redirect_uri'  => $defaultRedirectUri,
    ]);
    header('Location: /' . $pageName . '?' . $params, true, 302);
    exit;
}

/* â”€â”€â”€ 5. Authenticated User Auto-Redirect â”€â”€â”€ */
$autoHandler = new AutoRedirectHandler($config, $domainConfig);
$autoHandler->handle($pageName, $method);

/* â”€â”€â”€ 6. PageRouterKernel (normal sayfa akÄ±ÅŸÄ±) â”€â”€â”€ */
$container  = AuthContainer::getInstance($config, $domainConfig);
$controller = $container->get(AuthController::class);
$authHandler = new AuthPostHandler($controller);

$handlers = [];
foreach (['login', 'register', 'select-gender', 'forgot-password', 'reset-password', 'logout', 'set-gender'] as $uri) {
    $handlers[$uri] = $authHandler;
}

$logger->info("Request: {$method} {$requestUri}", [
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '-',
    'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '-',
]);

$authHelper = new \CoreMusic\PageRouter\PageRouterHelper();
$urlBuilder = new \CoreMusic\PageRouter\AuthUrlBuilder($domainConfig, $authHelper);
$registry   = new \CoreMusic\PageRouter\RouteRegistry();
$authGuard  = new \CoreMusic\PageRouter\AuthGuard($authHelper, $urlBuilder, true);
$router     = new \CoreMusic\PageRouter\PageRouter(
    $registry, $config, $domainConfig, $authHelper,
    $authGuard, $urlBuilder, new \CoreMusic\Cache\PageCacheAdapter(), $handlers,
);

$kernel = new PageRouterKernel(
    config:       $config,
    domainConfig: $domainConfig,
    headerPath:   null,
    footerPath:   null,
    registry:     $registry,
    router:       $router,
    handlers:     $handlers,
    corsConfig:   $corsConfig,
);

try {
    $routesFile = dirname(__DIR__) . '/shared/config/auth-routes.php';
    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
} catch (\Throwable $e) {
    $logger->error("Unhandled: {$e->getMessage()}", [
        'file'  => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => ['code' => 'SERVER_INTERNAL_ERROR', 'message' => 'Sunucu hatasÄ±.']], JSON_UNESCAPED_UNICODE);
}
