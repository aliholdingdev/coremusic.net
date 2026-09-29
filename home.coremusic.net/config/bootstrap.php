<?php declare(strict_types=1);

use CoreMusic\Home\Container\HomeContainer;
use CoreMusic\Home\Auth\HomeAuthBridge;
use CoreMusic\Home\Stream\MusicStreamHandler;
use CoreMusic\PageRouter\PageRouterKernel;
use CoreMusic\Session\SessionBootstrapper;

/* ─── DI Container ─── */
$homeContainer = HomeContainer::getInstance($config, $domainConfig);

/* ─── Special Routes (PageRouterKernel'den önce) ─── */
$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Health check
if ($requestUri === '/health') {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'  => 'ok',
        'service' => 'home.coremusic.net',
        'version' => APP_VERSION,
        'time'    => date('c'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Music stream — byte-range destekli ses akışı (HTML shell'e girmez)
if (preg_match('#^/stream/([0-9a-fA-F]{32})$#', $requestUri, $streamMatch) === 1) {
    MusicStreamHandler::dispatch($streamMatch[1]);
}
if (str_starts_with($requestUri, '/stream')) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Not Found';
    exit;
}

// Auth callback — redirect sorununu önlemek için kernel'den önce işle
if ($requestUri === '/auth/callback' || $requestUri === 'auth/callback') {
    $authKeyRaw = (string)($_GET['auth_key'] ?? '');
    $homeLogger = \CoreMusic\Log\LoggerFactory::getInstance();
    $homeLogger->debug('[Home] Auth callback triggered', [
        'has_auth_key' => $authKeyRaw !== '',
        'auth_key_prefix' => $authKeyRaw !== '' ? substr($authKeyRaw, 0, 8) . '...' : '',
    ]);

    // Auth bypass — yalnız yerel .env kaynaklı, üretim dışı ve UUID dolu olmalı (fail-closed)
    $bypassActive = (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS)
        && (defined('APP_ENV_MODE') && APP_ENV_MODE !== 'production')
        && (defined('BYPASS_USER_UUID') && (string)constant('BYPASS_USER_UUID') !== '');
    if ($bypassActive) {
        SessionBootstrapper::ensureStarted();
        $_SESSION['MM_UserID']      = constant('BYPASS_USER_UUID');
        $_SESSION['MM_UserRole']    = constant('BYPASS_ROLE');
        $_SESSION['MM_Username']    = constant('BYPASS_USERNAME');
        $_SESSION['MM_Permissions'] = [];
        $homeLogger->debug('[Home] Auth bypass active — session created directly');
        header('Location: /home', true, 302);
        exit;
    }

    if ($authKeyRaw === '') {
        $homeLogger->warning('[Home] Auth callback with empty auth_key, redirecting to /login');
        header('Location: /login', true, 302);
        exit;
    }

    // Session başlat — tüm parametreler SessionBootstrapper SSOT'undan gelir
    SessionBootstrapper::ensureStarted();

    // Auth key doğrula + session oluştur
    $authBridge = $homeContainer->get(HomeAuthBridge::class);
    $result = $authBridge->validateAndCreateSession($authKeyRaw);

    if ($result['success']) {
        $homeLogger->debug('[Home] Auth callback session created', [
            'user_id' => $result['user']['id'] ?? '-',
        ]);
        // Session'u diske yaz
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header('Location: /home', true, 302);
        exit;
    }

    // Başarısız → login'e dön
    $homeLogger->warning('[Home] Auth callback validation failed', [
        'error' => $result['error'] ?? 'unknown',
    ]);
    header('Location: /login?error=invalid_key', true, 302);
    exit;
}

/* ─── PageRouterKernel ─── */
$kernel = new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);
try {
    // Shared routes from infrastructure
    $routesFile = dirname(__DIR__) . '/vendor/coremusic/shared-infrastructure/config/routes.php';
    if (!file_exists($routesFile)) {
        // Fallback for local dev symlink structure
        $routesFile = dirname(__DIR__, 2) . '/shared/config/routes.php';
    }
    
    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
} catch (\Throwable $e) {
    $logger = \CoreMusic\Log\LoggerFactory::getInstance();
    $logger->error('[Home] Unhandled: ' . $e->getMessage(), [
        'file'  => $e->getFile() . ':' . $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    echo 'Internal Server Error';
}
