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
use CoreMusic\Middleware\CsrfMiddleware;
use CoreMusic\Middleware\OriginCheckMiddleware;
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

/* --- 0. Alias: Faz 3a UI `API_URL + '/v1/...'` çağırır (api.coremusic.net/v1/...),
 *     route tablosu ise `/api/v1/...` kayıtlıdır → tek kaynak kabul noktası.      */
if (str_starts_with($requestUri, '/v1/') || $requestUri === '/v1') {
    $rawUri   = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $queryPos = strpos($rawUri, '?');
    $suffix   = $queryPos === false ? '' : substr($rawUri, $queryPos);

    $requestUri            = '/api' . $requestUri;
    $_SERVER['REQUEST_URI'] = '/api' . $rawUri . $suffix;
}

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
    // Auth uçları SSOT AuthService ile çalışır → $_SESSION şart (Faz 1b).
    // Aynı SessionBootstrapper = auth.coremusic.net ile aynı cookie/save path.
    if (preg_match('#^/api/v1/auth(/|$)#', $requestUri) === 1) {
        \CoreMusic\Session\SessionBootstrapper::ensureStarted();
    }

    // Middleware pipeline'ının beklediği request formatı (Cors: server + method).
    // headers/body: CsrfMiddleware (B-F-02) x-csrf-token header'ını ve gövde
    // token'ını bu alanlardan okur — session-auth state-changing uçlar için gerekli.
    $pipelineRequest = [
        'server'  => $_SERVER,
        'method'  => $method,
        'uri'     => $requestUri,
        'headers' => ['x-csrf-token' => $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''],
        'body'    => $_POST + (json_decode(file_get_contents('php://input'), true) ?? []),
    ];

    try {
        /* --- Middleware pipeline (ADR-020 §1.1-B.1) — sabit sıra ---
         * ResponseNormalization → Cors → RateLimit → Authentication
         *                         → RequestValidation → Authorization
         */
        $corsMiddleware = new CorsMiddleware($corsConfig);
        // B-F-02/B-F-07: frozen sıra OriginCheck(#1) → Cors(#2). İzinsiz Origin'i
        // OriginCheck 403 ile reddeder (Cors yalnız header yayıcıdır — B-F-03).
        // Origin yoksa (curl/Bearer istemciler) geçer.
        $originCheckMiddleware = new OriginCheckMiddleware(
            defined('APP_ENV_MODE') && APP_ENV_MODE === 'production',
            $corsConfig
        );
        $csrfMiddleware = new CsrfMiddleware();
        $apiCache       = (function_exists('apcu_enabled') && apcu_enabled())
            ? new ApcuAdapter()
            : new MemoryAdapter();

        $pipeline = (new ApiMiddlewarePipeline())
            ->pipe(new ResponseNormalizationMiddleware())
            ->pipe(static fn (array $req, callable $next): array => $originCheckMiddleware->handle($req, $next))
            ->pipe(static fn (array $req, callable $next): array => $corsMiddleware->handle($req, $next))
            ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))
            ->pipe(new AuthenticationMiddleware(new ApiSessionManager()))
            // B-F-02: yalnız SESSION ile kimliklenmiş state-changing istekler CSRF
            // token gerektirir. Bearer/API-key (header-auth) ve public uçlar CSRF'ye
            // tabi değildir — tarayıcı cross-site'te özel header forging yapamaz;
            // SameSite=Lax + OriginCheck ek katmanlardır.
            ->pipe(static function (array $req, callable $next) use ($csrfMiddleware): array {
                if (($req['_auth_user']['method'] ?? '') !== 'session') {
                    return $next($req);
                }
                return $csrfMiddleware->handle($req, $next);
            })
            ->pipe(new RequestValidationMiddleware())
            ->pipe(new AuthorizationMiddleware());

        $gateway = new Gateway(
            new VersionResolver(),
            new ServiceRegistry(),
            $pipeline,
            $routeTable,
            // Faz 1b: route['action'] → AuthController (SSOT auth.coremusic.net/include)
            static fn (array $route, array $request): array =>
                \CoreMusic\Api\Container\ApiAuthContainer::controller()->handle($route, $request),
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
