<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Application Bootstrap
 *
 * Zincir: constants.php (index.php) → RuntimeBootstrap → bu dosya → PageRouterKernel.
 * Desen: home.coremusic.net/config/bootstrap.php (kernel öncesi özel rotalar).
 *
 * Guardrail: .ai/.templates/backend/php-template.md — strict_types, PSR-12, no ORM.
 */

use CoreMusic\Admin\Auth\AdminAuthBridge;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\PageRouter\PageRouterKernel;
use CoreMusic\Session\SessionBootstrapper;

/* ─── HTTPS Detection ─── */
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);

$currentHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'admin.coremusic.net';
$currentPort = (int)($_SERVER['SERVER_PORT'] ?? ($isHttps ? 443 : 80));

if (str_contains($currentHost, ':')) {
    [$currentHost, $portFromHost] = explode(':', $currentHost, 2);
    $currentPort = (int)$portFromHost;
}

/* ─── Config Objects ─── */
$domainFile = dirname(__DIR__) . '/vendor/coremusic/shared-infrastructure/config/domain.php';
if (!file_exists($domainFile)) {
    $domainFile = dirname(__DIR__, 2) . '/shared/config/domain.php';
}
$domainConfig = new DomainConfig(file_exists($domainFile) ? $domainFile : null);
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$appConfig = [
    'app' => [
        'name'     => APP_NAME,
        'version'  => APP_VERSION,
        'env'      => APP_ENV_MODE,
        'timezone' => APP_TIMEZONE,
        'debug'    => DEBUG_MODE,
        'test_mode'=> TEST_MODE,
    ],
    'domain' => [
        'host'          => $currentHost,
        'port'          => $currentPort,
        'isHttps'       => $isHttps,
        'subdomainPort' => 80,
    ],
    'session' => [
        'name'          => SESSION_NAME,
        'lifetime'      => 7200,
        'cookie_secure' => $isHttps,
    ],
    'security' => [
        'csrfTokenLength' => CSRF_TOKEN_LENGTH,
        'rateLimitMax'    => RATE_LIMIT_MAX,
        'rateLimitWindow' => RATE_LIMIT_WINDOW,
    ],
];
$config = new ConfigManager($appConfig);

/* ─── Special Routes (PageRouterKernel'den önce) ─── */
$requestUri = rtrim(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');

// Health check
if ($requestUri === '/health') {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'  => 'ok',
        'service' => 'admin.coremusic.net',
        'version' => APP_VERSION,
        'time'    => date('c'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Auth callback — redirect sorununu önlemek için kernel'den önce işle
if ($requestUri === '/auth/callback' || $requestUri === 'auth/callback') {
    $authKeyRaw = (string)($_GET['auth_key'] ?? '');
    $adminLogger = LoggerFactory::getInstance();
    $adminLogger->debug('[Admin] Auth callback triggered', [
        'has_auth_key' => $authKeyRaw !== '',
    ]);

    if ($authKeyRaw === '') {
        $adminLogger->warning('[Admin] Auth callback with empty auth_key, redirecting to /login');
        header('Location: /login', true, 302);
        exit;
    }

    // Session başlat — tüm parametreler SessionBootstrapper SSOT'undan gelir
    SessionBootstrapper::ensureStarted();

    // Auth key doğrula + session oluştur
    $authBridge = new AdminAuthBridge();
    $result = $authBridge->validateAndCreateSession($authKeyRaw);

    if ($result['success']) {
        $adminLogger->debug('[Admin] Auth callback session created', [
            'user_id' => $result['user']['id'] ?? ($result['user']['user_id'] ?? '-'),
        ]);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header('Location: /admin-dashboard', true, 302);
        exit;
    }

    // Başarısız → login'e dön
    $adminLogger->warning('[Admin] Auth callback validation failed', [
        'error' => $result['error'] ?? 'unknown',
    ]);
    header('Location: /login?error=invalid_key', true, 302);
    exit;
}

/* ─── PageRouterKernel ─── */
$kernel = new PageRouterKernel($config, $domainConfig, null, null);
try {
    // Rotalar admin-local: shared/config/routes.php DEĞİŞTİRİLMEZ
    $kernel->handle($_SERVER, $_GET, $_POST, __DIR__ . '/routes.php');
} catch (\Throwable $e) {
    $logger = LoggerFactory::getInstance();
    $logger->error('[Admin] Unhandled: ' . $e->getMessage(), [
        'file'  => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    echo 'Internal Server Error';
}
