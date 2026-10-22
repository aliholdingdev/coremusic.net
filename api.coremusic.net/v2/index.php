<?php declare(strict_types=1);

/**
 * CoreMusic API v2 — yeni giriş noktası (ADR-084 Contract First).
 *
 * v1'den (../index.php) FARK: yalnız /v2/* route'larını tanımlar; v1 route'ları
 * ve index.php DEĞİŞMEZ. ADR-094/020 frozen middleware sırası birebir korunur:
 *   ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication
 *                         → (koşullu CSRF) → RequestValidation → Authorization
 *
 * v2 sözleşmesi (zarf):
 *   Başarı : HTTP 2xx · { "data": ... }            (ApiResponse::ok/created)
 *   Hata   : HTTP 4xx/5xx · { "error": {code,message} }  (ApiResponse::error)
 *   v1'den kasıtlı fark: {success:true} alanı YOK.
 *
 * Kasıtlı reuse (kopyalama YOK): Gateway, RouteTable, ApiMiddlewarePipeline,
 * AuthenticationMiddleware, Cors/Origin/RateLimit/Csrf middleware, ApiResponse,
 * ApiSessionManager, JwtService — shared katmanından AYNI example kullanılır.
 *
 * v1'le ortak davranış (korunur): /health (DB'siz), /v2/* → /api/v2/* alias,
 * CORS preflight → 204 (Route/Auth'a uğramaz).
 */

if (PHP_SAPI === 'cli') {
    fwrite(STDERR, 'v2/index.php HTTP entry point — CLI erişimi reddedildi' . PHP_EOL);
    exit(1);
}

// v1 autoload bridge'i (CoreMusic\Api\* → include/ + shared vendor + Auth SSOT).
require_once __DIR__ . '/../autoload.php';
// v2 controller autoload (CoreMusic\Api\V2\* → v2/).
require_once __DIR__ . '/bootstrap.php';

use CoreMusic\Api\Auth\ApiSessionManager;
use CoreMusic\Api\Container\ApiAuthContainer;
use CoreMusic\Api\Gateway;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Api\Middleware\AuthenticationMiddleware;
use CoreMusic\Api\Middleware\AuthorizationMiddleware;
use CoreMusic\Api\Middleware\RateLimitMiddleware;
use CoreMusic\Api\Middleware\RequestValidationMiddleware;
use CoreMusic\Api\Middleware\ResponseNormalizationMiddleware;
use CoreMusic\Api\Registry\ServiceRegistry;
use CoreMusic\Api\Routing\RouteTable;
use CoreMusic\Api\V2\V2Container;
use CoreMusic\Api\Versioning\VersionResolver;
use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Cache\ApcuAdapter;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Middleware\CorsMiddleware;
use CoreMusic\Middleware\CsrfMiddleware;
use CoreMusic\Middleware\OriginCheckMiddleware;
use CoreMusic\Security\CacheRateLimiter;

/* --- Config (v2 routes + v2 cors; constants/app v1 ile ortak, salt-okunur) --- */
require_once __DIR__ . '/../config/constants.php';
$appConfig  = require __DIR__ . '/../config/app.php';
$corsConfig = require __DIR__ . '/config/cors.php';
$routeTable = RouteTable::fromArray(require __DIR__ . '/config/routes.php');

RuntimeBootstrap::boot(DEBUG_MODE);

$logger = LoggerFactory::getInstance(dirname(__DIR__, 2), DEBUG_MODE ? 'debug' : 'error');

/* --- HTTPS detection (v1 ile aynı) --- */
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);
$appConfig['session']['cookie_secure'] = $isHttps;
$config = new ConfigManager($appConfig); // session config'i ($_SESSION) için

/* --- JSON response helper (v1 ile aynı zarf: {data}/{error}) --- */
$jsonResponse = static function (array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'");
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};

/* --- Request parsing --- */
$requestUri = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$method     = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* --- Alias: /v2/* → /api/v2/* (tek kaynak kabul noktası) --- */
if (str_starts_with($requestUri, '/v2/') || $requestUri === '/v2') {
    $rawUri   = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $queryPos = strpos($rawUri, '?');
    $suffix   = $queryPos === false ? '' : substr($rawUri, $queryPos);

    $requestUri            = '/api' . $requestUri;
    $_SERVER['REQUEST_URI'] = '/api' . $rawUri . $suffix;
}

$logger->info("Request[v2]: {$method} {$requestUri}", ['ip' => $_SERVER['REMOTE_ADDR'] ?? '-']);

/* --- Health (DB'siz canlılık — pipeline dışı) --- */
if ($requestUri === '/health') {
    $jsonResponse([
        'status'    => 'ok',
        'service'   => 'api.coremusic.net',
        'version'   => 'v2',
        'timestamp' => (new \DateTimeImmutable())->format('c'),
    ]);
}

/* --- v2 Gateway (/api/v2/*) --- */
if (str_starts_with($requestUri, '/api/')) {
    // Auth uçları SSOT AuthService ile çalışır → $_SESSION şart (session cookie).
    if (preg_match('#^/api/v2/auth(/|$)#', $requestUri) === 1) {
        \CoreMusic\Session\SessionBootstrapper::ensureStarted();
    }

    // Pipeline'ın beklediği request formatı (v1 ile birebir).
    $pipelineRequest = [
        'server'  => $_SERVER,
        'method'  => $method,
        'uri'     => $requestUri,
        'headers' => ['x-csrf-token' => $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''],
        'body'    => $_POST + (json_decode(file_get_contents('php://input'), true) ?? []),
    ];

    try {
        $corsMiddleware = new CorsMiddleware($corsConfig);
        $originCheckMiddleware = new OriginCheckMiddleware(
            defined('APP_ENV_MODE') && APP_ENV_MODE === 'production',
            $corsConfig
        );
        $csrfMiddleware = new CsrfMiddleware();
        $apiCache = (function_exists('apcu_enabled') && apcu_enabled())
            ? new ApcuAdapter()
            : new MemoryAdapter();

        $pipeline = (new ApiMiddlewarePipeline())
            ->pipe(new ResponseNormalizationMiddleware())
            ->pipe(static fn (array $req, callable $next): array => $originCheckMiddleware->handle($req, $next))
            ->pipe(static fn (array $req, callable $next): array => $corsMiddleware->handle($req, $next))
            ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))
            ->pipe(new AuthenticationMiddleware(
                new ApiSessionManager(),
                ApiAuthContainer::jwtService(),
                static fn (string $jti): bool =>
                    ApiAuthContainer::userRepository()->isValidAccessToken($jti),
            ))
            // B-F-02: yalnız SESSION ile kimliklenmiş state-changing istekler CSRF
            // token gerektirir. Bearer/API-key ve public uçlar CSRF'ye tabi DEĞİL.
            ->pipe(static function (array $req, callable $next) use ($csrfMiddleware): array {
                if (($req['_auth_user']['method'] ?? '') !== 'session') {
                    return $next($req);
                }
                return $csrfMiddleware->handle($req, $next);
            })
            ->pipe(new RequestValidationMiddleware())
            ->pipe(new AuthorizationMiddleware());

        // v2 handler resolver: route['handler'] → V2Container dispatch.
        $gateway = new Gateway(
            new VersionResolver(),
            new ServiceRegistry(),
            $pipeline,
            $routeTable,
            static fn (array $route, array $request): array => V2Container::handle($route, $request),
        );
        $result = $gateway->dispatch($pipelineRequest);

        foreach ($result['headers'] ?? [] as $headerName => $headerValue) {
            header($headerName . ': ' . $headerValue);
        }
        unset($result['headers']);

        if (($result['halt'] ?? false) === true) {
            http_response_code((int) ($result['httpStatus'] ?? 204));
            echo (string) ($result['body'] ?? '');
            exit;
        }

        $status = (int) http_response_code();
        $jsonResponse($result, $status >= 400 ? $status : 200);
    } catch (\Throwable $e) {
        $logger->error("Unhandled[v2]: {$e->getMessage()}", [
            'file'   => $e->getFile() . ':' . $e->getLine(),
            'trace'  => $e->getTraceAsString(),
            'uri'    => $requestUri,
            'method' => $method,
        ]);
        // Stack trace / kolon adı istemciye ASLA dönmez (ADR-020 §2.2F).
        $jsonResponse(['error' => ['code' => 'INTERNAL_ERROR', 'message' => 'Sunucu hatası.']], 500);
    }
}

/* --- 404 --- */
$jsonResponse(['error' => ['code' => 'NOT_FOUND', 'message' => 'Uç bulunamadı.']], 404);
