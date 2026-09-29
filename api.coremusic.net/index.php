<?php declare(strict_types=1);

/**
 * CoreMusic API Service — Entry Point
 *
 * JSON-only front controller (SPA router KULLANMAZ).
 *   /health    → canlılık ucu (Faz 0: 200 döner)
 *   /api/v1/*  → CoreMusic\Api\Gateway'e yönlendirilir
 *   diğer      → 404 JSON
 *
 * Hata sözleşmesi: {error:{code,message}} — exception mesajı ASLA istemciye dönmez.
 */

require_once __DIR__ . '/autoload.php';

use CoreMusic\Api\Auth\ApiSessionManager;
use CoreMusic\Api\Gateway;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Api\Middleware\AuthenticationMiddleware;
use CoreMusic\Api\Middleware\AuthorizationMiddleware;
use CoreMusic\Api\Middleware\RateLimitMiddleware;
use CoreMusic\Api\Middleware\RequestValidationMiddleware;
use CoreMusic\Api\Middleware\ResponseNormalizationMiddleware;
use CoreMusic\Api\Registry\ServiceRegistry;
use CoreMusic\Api\Routing\RouteTable;
use CoreMusic\Api\Versioning\VersionResolver;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Cache\ApcuAdapter;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Middleware\CorsMiddleware;
use CoreMusic\Security\CacheRateLimiter;

/* --- Config (constants + app + cors + routes) --- */
require_once __DIR__ . '/config/constants.php';
$appConfig    = require __DIR__ . '/config/app.php';
$corsConfig   = require __DIR__ . '/config/cors.php';
$routeTable   = RouteTable::fromArray(require __DIR__ . '/config/routes.php');

RuntimeBootstrap::boot(DEBUG_MODE);

/* --- Logger --- */
$logger = LoggerFactory::getInstance(dirname(__DIR__), DEBUG_MODE ? 'debug' : 'error');

/* --- HTTPS Detection --- */
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

$currentHost = $_SERVER['HTTP_HOST'] ?? 'api.coremusic.net';
$currentPort = (int)($_SERVER['SERVER_PORT'] ?? ($isHttps ? 443 : 80));

if (str_contains($currentHost, ':')) {
    [$currentHost, $portFromHost] = explode(':', $currentHost, 2);
    $currentPort = (int)$portFromHost;
}

/* --- Config Objects --- */
$domainConfig = new DomainConfig(dirname(__DIR__) . '/shared/config/domain.php');
$scheme = $isHttps ? 'https' : 'http';
$domainConfig->setOverrides($scheme, $currentHost, $currentPort);

$appConfig['session']['cookie_secure'] = $isHttps;
$config = new ConfigManager($appConfig);

/* --- JSON response helper --- */
$jsonResponse = static function (array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'");
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};

/* --- Request Parsing --- */
$requestUri = rtrim((string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$method     = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$logger->info("Request: {$method} {$requestUri}", [
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '-',
]);

/* --- 1. Health --- */
if ($requestUri === '/health') {
    $jsonResponse([
        'status'    => 'ok',
        'service'   => 'api.coremusic.net',
        'version'   => APP_VERSION,
        'timestamp' => (new \DateTimeImmutable())->format('c'),
    ]);
}

/* --- 2. API Gateway (/api/v1/*) --- */
if (str_starts_with($requestUri, '/api/')) {
    // Middleware pipeline'ının beklediği request formatı (Cors: server + method).
    $pipelineRequest = [
        'server' => $_SERVER,
        'method' => $method,
        'uri'    => $requestUri,
    ];

    try {
        /* --- Middleware pipeline (ADR-020 §1.1-B.1) — sabit sıra ---
         * ResponseNormalization → Cors → RateLimit → Authentication
         *                         → RequestValidation → Authorization
         */
        $corsMiddleware = new CorsMiddleware($corsConfig);
        $apiCache       = (function_exists('apcu_enabled') && apcu_enabled())
            ? new ApcuAdapter()
            : new MemoryAdapter();

        $pipeline = (new ApiMiddlewarePipeline())
            ->pipe(new ResponseNormalizationMiddleware())
            ->pipe(static fn (array $req, callable $next): array => $corsMiddleware->handle($req, $next))
            ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))
            ->pipe(new AuthenticationMiddleware(new ApiSessionManager()))
            ->pipe(new RequestValidationMiddleware())
            ->pipe(new AuthorizationMiddleware());

        $gateway = new Gateway(
            new VersionResolver(),
            new ServiceRegistry(),
            $pipeline,
            $routeTable,
        );
        $result = $gateway->dispatch($pipelineRequest);

        // CorsMiddleware header'ları gövde yerine HTTP header katmanına yazar.
        foreach ($result['headers'] ?? [] as $headerName => $headerValue) {
            header($headerName . ': ' . $headerValue);
        }

        // Header anahtarı API sözleşmesine ({data,meta} / {error:{...}}) dahil değildir.
        unset($result['headers']);

        // Preflight (Cors 'halt' ile kısa devre eder): gövdesiz 204.
        if (($result['halt'] ?? false) === true) {
            http_response_code((int) ($result['httpStatus'] ?? 204));
            echo (string) ($result['body'] ?? '');
            exit;
        }

        $status = (int) http_response_code();
        $jsonResponse($result, $status >= 400 ? $status : 200);
    } catch (\Throwable $e) {
        $logger->error("Unhandled: {$e->getMessage()}", [
            'file'   => $e->getFile() . ':' . $e->getLine(),
            'trace'  => $e->getTraceAsString(),
            'uri'    => $requestUri,
            'method' => $method,
        ]);
        $jsonResponse([
            'error' => [
                'code'    => 'SERVER_INTERNAL_ERROR',
                'message' => 'Sunucu hatası.',
            ],
        ], 500);
    }
}

/* --- 3. 404 --- */
$jsonResponse([
    'error' => [
        'code'    => 'NOT_FOUND',
        'message' => 'Uç bulunamadı.',
    ],
], 404);
