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
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Auth\Controller\AuthController;
use CoreMusic\Auth\Container\AuthContainer;
use CoreMusic\Auth\Handler\AuthPostHandler;
use CoreMusic\PageRouter\PageRouterKernel;
use CoreMusic\Log\LoggerFactory;

const MAX_REQUEST_BODY_SIZE = 8192;

/* ─── Config (constants + app + cors) ─── */
require_once __DIR__ . '/config/constants.php';
$appConfig     = require __DIR__ . '/config/app.php';
$corsConfig    = require __DIR__ . '/config/cors.php';

RuntimeBootstrap::boot(DEBUG_MODE);

/* ─── Logger ─── */
$logger = LoggerFactory::getInstance(dirname(__DIR__), DEBUG_MODE ? 'debug' : 'error');

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

/* ─── Config Objects ─── */
$domainConfig = new DomainConfig(dirname(__DIR__) . '/shared/config/domain.php');
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$appConfig['session']['cookie_secure'] = $isHttps;
$config = new ConfigManager($appConfig);

/* ─── Session Helper — Tek SSoT ─── */
function cm_session_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS');
    $savePath = ini_get('session.save_path') ?: 'C:\temp';
    session_save_path($savePath);
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '.coremusic.net',
        'secure'   => $isHttps,
        'httponly'  => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ─── Special Routes (JSON endpoints — PageRouterKernel'den önce) ─── */
$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method     = $_SERVER['REQUEST_METHOD'];

if ($requestUri === '/health' || $requestUri === '/session' || $requestUri === '/validate-key') {
    $container  = AuthContainer::getInstance($config, $domainConfig);
    $controller = $container->get(AuthController::class);

    cm_session_start();

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
    // auth_key ile root'a gelindiyse — bu bir callback, key'i doğrula
    if (!empty($_GET['auth_key'])) {
        cm_session_start();
        $akContainer  = AuthContainer::getInstance($config, $domainConfig);
        $akController = $akContainer->get(AuthController::class);
        $akResult = $akController->handleValidateKey([
            'query_params' => $_GET,
            'body'         => ['auth_key' => $_GET['auth_key']],
            'server'       => $_SERVER,
        ]);
        if (($akResult['httpStatus'] ?? 0) === 200 && !empty($akResult['body']['success'])) {
            $akUser = $akResult['body']['user'];
            $akSession = $akContainer->get(\CoreMusic\Interfaces\Auth\ISessionManager::class);
            $akSession->setAuthUser($akUser);
            if (!empty($akUser['gender'])) {
                $akSession->setGender($akUser['gender']);
            }
            header('Location: /home', true, 302);
            exit;
        }
        header('Location: /login?error=invalid_key', true, 302);
        exit;
    }

    $redirectUri = MUSIC_URL . '/auth/callback';
    $params = http_build_query([
        'client_id'     => 'coremusic-web',
        'response_type' => 'session',
        'redirect_uri'  => $redirectUri,
    ]);
    header('Location: /select-gender?' . $params, true, 302);
    exit;
}

/* ─── Gender Gate: /login & /register → /select-gender if no gender ─── */
$authGenderPages = ['login', 'register'];
$pageNameCheck = ltrim($requestUri, '/');
if ($method !== 'POST' && in_array($pageNameCheck, $authGenderPages, true)) {
    cm_session_start();
    $sessionGender = $_SESSION['cm_gender'] ?? '';
    $cookieGender  = $_COOKIE['cm_gender'] ?? '';
    $hasGender     = !empty($sessionGender) || !empty($cookieGender);
    if (!$hasGender) {
        $params = http_build_query($_GET);
        header('Location: /select-gender' . ($params ? '?' . $params : ''), true, 302);
        exit;
    }
}

/* ─── Default OAuth Redirect (GET only, missing params) ─── */
$authPages = ['login', 'register', 'forgot-password', 'reset-password'];
$pageName  = ltrim($requestUri, '/');
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

/* ─── AuthContainer + Handler ─── */
$container  = AuthContainer::getInstance($config, $domainConfig);
$controller = $container->get(AuthController::class);
$authHandler = new AuthPostHandler($controller);

$handlers = [];
foreach (['login', 'register', 'select-gender', 'forgot-password', 'reset-password', 'logout', 'set-gender'] as $uri) {
    $handlers[$uri] = $authHandler;
}

/* ─── Request Log ─── */
$logger->info("Request: {$method} {$requestUri}", [
    'ip'   => $_SERVER['REMOTE_ADDR'] ?? '-',
    'ua'   => $_SERVER['HTTP_USER_AGENT'] ?? '-',
]);

/* ─── Shared Components ─── */
$authHelper = new \CoreMusic\PageRouter\PageRouterHelper();
$urlBuilder = new \CoreMusic\PageRouter\AuthUrlBuilder($domainConfig, $authHelper);
$registry   = new \CoreMusic\PageRouter\RouteRegistry();
$authGuard  = new \CoreMusic\PageRouter\AuthGuard($authHelper, $urlBuilder, true);
$router     = new \CoreMusic\PageRouter\PageRouter(
    $registry, $config, $domainConfig, $authHelper,
    $authGuard, $urlBuilder, new \CoreMusic\Cache\PageCacheAdapter(), $handlers,
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
    corsConfig:    $corsConfig,
);

try {
    $routesFile = dirname(__DIR__) . '/shared/config/auth-routes.php';
    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
} catch (\Throwable $e) {
    $logger->error("Unhandled: {$e->getMessage()}", [
        'file' => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => ['code' => 'SERVER_INTERNAL_ERROR', 'message' => 'Sunucu hatası.']], JSON_UNESCAPED_UNICODE);
}
