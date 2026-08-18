<?php declare(strict_types=1);

/**
 * CoreMusic Home — Entry Point
 *
 * Shared SPA Router (PageRouterKernel) kullanır.
 * Auth entegrasyonu: include/ dizininde.
 */

require_once __DIR__ . '/autoload.php';

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Home\Container\HomeContainer;
use CoreMusic\Home\Auth\HomeAuthBridge;
use CoreMusic\PageRouter\PageRouterKernel;

/* ─── Config (constants + app) ─── */
require_once __DIR__ . '/config/constants.php';
$appConfig = require __DIR__ . '/config/app.php';

RuntimeBootstrap::boot(DEBUG_MODE);

/* ─── HTTPS Detection ─── */
$isHttps = $appConfig['domain']['isHttps'];
$currentHost = $appConfig['domain']['host'];
$currentPort = $appConfig['domain']['port'];

if (str_contains($currentHost, ':')) {
    [$currentHost, $portFromHost] = explode(':', $currentHost, 2);
    $currentPort = (int)$portFromHost;
}

/* ─── Config Objects ─── */
$domainConfig = new DomainConfig(dirname(__DIR__) . '/shared/config/domain.php');
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$config = new ConfigManager($appConfig);

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

// Auth callback — redirect sorununu önlemek için kernel'den önce işle
if ($requestUri === '/auth/callback' || $requestUri === 'auth/callback') {
    $authKeyRaw = (string)($_GET['auth_key'] ?? '');

    if ($authKeyRaw === '') {
        header('Location: /login', true, 302);
        exit;
    }

    // Session başlat — cookie domain'i middleware ile aynı olmalı
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name(defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS');
        $savePath = ini_get('session.save_path') ?: 'C:\temp';
        session_save_path($savePath);
        $cbIsHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '.coremusic.net',
            'secure'   => $cbIsHttps,
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    // Auth key doğrula + session oluştur
    $authBridge = $homeContainer->get(HomeAuthBridge::class);
    $result = $authBridge->validateAndCreateSession($authKeyRaw);

    if ($result['success']) {
        // Session'u diske yaz
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header('Location: /home', true, 302);
        exit;
    }

    // Başarısız → login'e dön
    header('Location: /login?error=invalid_key', true, 302);
    exit;
}

/* ─── PageRouterKernel ─── */
$kernel = new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);
try {
    $routesFile = dirname(__DIR__) . '/shared/config/routes.php';
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
