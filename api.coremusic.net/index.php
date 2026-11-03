<?php declare(strict_types=1);

/**
 * CoreMusic API Service — Unified Entry Point (v1 + v2)
 *
 * JSON-only front controller (SPA router KULLANMAZ).
 *   /health       → canlılık ucu (Faz 0: 200 döner)
 *   /api/v1/*     → v1 Gateway'e yönlendirilir (V1Container)
 *   /api/v2/*     → v2 Gateway'e yönlendirilir (V2Container)
 *   /v1/*         → /api/v1/* alias
 *   /v2/*         → /api/v2/* alias
 *   diğer         → 404 JSON
 *
 * Hata sözleşmesi: {error:{code,message}} — exception mesajı ASLA istemciye dönmez.
 * Versiyon çözümleme: VersionResolver (URL path + Accept header)
 */

require_once __DIR__ . '/autoload.php';

use CoreMusic\Api\Auth\ApiSessionManager;
use CoreMusic\Api\Container\ApiAuthContainer;
use CoreMusic\Api\Container\V1Container;
use CoreMusic\Api\Container\V2Container;
use CoreMusic\Api\Gateway;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Api\Middleware\AuthenticationMiddleware;
use CoreMusic\Api\Middleware\AuthorizationMiddleware;
use CoreMusic\Api\Middleware\RateLimitMiddleware;
use CoreMusic\Api\Middleware\RequestValidationMiddleware;
use CoreMusic\Api\Middleware\ResponseNormalizationMiddleware;
use CoreMusic\Api\Registry\ServiceRegistry;
use CoreMusic\Api\Routing\RouteTable;
use CoreMusic\Api\Router\VersionResolver as RouterVersionResolver;
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

/* --- Version çözümle (routes yüklenmeden önce yapılır ki {version} yerine yazılabilsin) --- */
$versionResolver = new RouterVersionResolver();
$headers = getallheaders();
$version = $versionResolver->resolve($_SERVER['REQUEST_URI'], $headers);

/* --- Routes: {version} placeholder'ını runtime versiyonla değiştir --- */
$rawRoutes = require __DIR__ . '/config/routes.php';
$routes = replaceVersionPlaceholder($rawRoutes, $version);
$routeTable = RouteTable::fromArray($routes);

/**
 * Route dizisindeki {version} placeholder'ını gerçek versiyonla değiştirir.
 *
 * @param array<string, mixed> $routes
 * @param string $version
 * @return array<string, mixed>
 */
function replaceVersionPlaceholder(array $routes, string $version): array
{
    $result = [];
    foreach ($routes as $method => $routesByMethod) {
        $result[$method] = [];
        foreach ($routesByMethod as $path => $routeConfig) {
            $newPath = str_replace('{version}', $version, $path);
            $result[$method][$newPath] = $routeConfig;
        }
    }
    return $result;
}

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

/* --- Alias: /v1/* → /api/v1/* AND /v2/* → /api/v2/* (tek kaynak kabul noktası) --- */
if (str_starts_with($requestUri, '/v1/') || $requestUri === '/v1') {
    $rawUri   = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $queryPos = strpos($rawUri, '?');
    $suffix   = $queryPos === false ? '' : substr($rawUri, $queryPos);

    $requestUri            = '/api' . $requestUri;
    $_SERVER['REQUEST_URI'] = '/api' . $rawUri . $suffix;
} elseif (str_starts_with($requestUri, '/v2/') || $requestUri === '/v2') {
    $rawUri   = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $queryPos = strpos($rawUri, '?');
    $suffix   = $queryPos === false ? '' : substr($rawUri, $queryPos);

    $requestUri            = '/api' . $requestUri;
    $_SERVER['REQUEST_URI'] = '/api' . $rawUri . $suffix;
}

$logger->info("Request: {$method} {$requestUri}", [
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '-',
]);

/* --- 1. Health (version-aware) --- */
if ($requestUri === '/health') {
    // Health için varsayılan v1; Accept header ile v2 de isteyebilir
    $headers = getallheaders();
    $healthVersion = 'v1';
    if (preg_match('/application\/vnd\.coremusic\.(v\d+)\+json/i', $headers['Accept'] ?? '', $matches)) {
        $healthVersion = strtolower($matches[1]);
    }

    $healthStart = microtime(true);
    $healthBody  = [
        'status'    => 'ok',
        'service'   => 'api.coremusic.net',
        'version'   => $healthVersion,
        'timestamp' => (new \DateTimeImmutable())->format('c'),
    ];
    $logger->info('Health check', [
        'duration_ms' => round((microtime(true) - $healthStart) * 1000, 3),
    ]);
    $jsonResponse($healthBody);
}

// F5: /stream bloğu, pipeline'daki $apcuAvailable tanımından ÖNCE gelir
// (php üst-scope sızıntısı okunabilir ama bağımlılık olmasın diye blok
// içinde yeniden hesaplanır — pipeline kurulumu DEĞİŞMEDİ).
$apcuAvailableStream = function_exists('apcu_enabled') && apcu_enabled();

/* --- 1.1 Audio & Video Stream (/stream/{id} veya /api/v1/stream/{id}) --- */
if (preg_match('#^(?:/api/v1)?/stream/([a-f0-9]{32})$#i', $requestUri, $matches) === 1) {
    // F5: /stream byte-path Gateway ve middleware pipeline'INDAN ÖNCE yakalandığı
    // için rate-limit kapısından hiç geçmiyordu (tek istisnai uç).
    // Middleware SIRASI DEĞİŞMEZ (ADR-020/094 — 8 kapı donduruldu); aynı
    // CacheRateLimiter sözleşmesi (SEC-01: ip:/api: bucket'ları · PRD-SEC-004:
    // sayaç yazılamazsa fail-closed 503) auth ÖNCESİ bu bloğa explicit uygulanır.
    $streamRateCache = $apcuAvailableStream
        ? new ApcuAdapter()
        : new MemoryAdapter();
    if (!$apcuAvailableStream) {
        $logger->securityEvent('rate_limiter_unavailable', ['mode' => 'fail-open', 'route' => 'stream']);
    }
    $streamRate = (new RateLimitMiddleware(new CacheRateLimiter($streamRateCache)))(
        ['uri' => $requestUri, 'method' => $method, 'server' => $_SERVER],
        static fn (array $req): array => $req
    );
    if (isset($streamRate['error'])) {
        // 429 (RATE_LIMIT_EXCEEDED) veya 503 (RATE_LIMITER_UNAVAILABLE) —
        // http_response_code() ApiResponse::error tarafından kurulmuştur.
        $jsonResponse($streamRate, (int) http_response_code());
    }

    // Auth (Faz 4b kararı): home MusicStreamHandler paritesi — stream GİRİŞE ZORUNLU
    // (fail-closed). home 302 /login verirdi; API JSON-only Gateway olduğu için
    // sözleşmeye uygun karşılık 401 JSON'dur. Hybrid auth tek kaynaktan
    // (AuthenticationMiddleware): session cookie (auth.coremusic.net ile paylaşımlı
    // save path — Faz 1b) → RS256 Bearer + jti revocation. GET state-changing
    // olmadığı için CSRF tabi değildir (ADR-010 / B-F-02).
    \CoreMusic\Session\SessionBootstrapper::ensureStarted();

    $streamAuth = (new \CoreMusic\Api\Middleware\AuthenticationMiddleware(
        new ApiSessionManager(),
        \CoreMusic\Api\Container\ApiAuthContainer::jwtService(),
        static fn (string $jti): bool =>
            \CoreMusic\Api\Container\ApiAuthContainer::userRepository()->isValidAccessToken($jti),
    ))(
        ['uri' => $requestUri, 'method' => $method],
        static fn (array $req): array => $req,
    );

    if (isset($streamAuth['error'])) {
        $jsonResponse($streamAuth, 401);
    }

    // Uzun dosya akışında session kilidi tutulmasın (home parite: writeClose).
    \CoreMusic\Session\SessionBootstrapper::writeClose();

    $streamController = new \CoreMusic\Api\Controller\StreamController(
        \CoreMusic\Api\Container\V1Container::musicRepository()
    );
    $streamController->stream($matches[1]);
}

/* --- 2. API Gateway (/api/v1/* veya /api/v2/*) --- */
if (str_starts_with($requestUri, '/api/')) {
    // Container seçimi
    $container = ($version === 'v2') ? new V2Container() : new V1Container();

    // Auth uçları SSOT AuthService ile çalışır → $_SESSION şart (Faz 1b).
    // Onarım 2026-10-24 (ADR-052/010/011): session yalnız /api/v1/auth/*'te
    // açılıyordu → auth'lu uçlar (playlist/history/user) session-authenticated
    // istemciyi tanıyamıyor, 401 dönüyordu (ApiSessionManager session_start()
    // çağırmaz — ADR-052). Artık: auth route'ları HER durumda, diğer uçlar
    // session cookie GELDİYSE oturum açar; cookie'siz stateless istemcilere
    // (Bearer/curl) ek session cookie üretilmez (ADR-052 prensibi korunur).
    // startOrExtend() csrf_token üretimini (ADR-010, SessionLifecycle:47) de
    // tetikler. Middleware sırası ADR-020 DEĞİŞMEZ.
    $apiPrefix = '/api/' . $version;
    if (($method !== 'OPTIONS') && (
        preg_match('#^' . $apiPrefix . '/auth(/|$)#', $requestUri) === 1
        || isset($_COOKIE[\CoreMusic\Session\SessionConfig::fromEnvironment()->name])
    )) {
        (new \CoreMusic\Session\SessionLifecycle())->startOrExtend();
    }

    // Middleware pipeline'ın beklediği request formatı (Cors: server + method).
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
         *                         → (koşullu CSRF) → RequestValidation → Authorization
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
        $apcuAvailable  = function_exists('apcu_enabled') && apcu_enabled();
        $apiCache       = $apcuAvailable
            ? new ApcuAdapter()
            : new MemoryAdapter();
        if (!$apcuAvailable) {
            // SEC-02: APCu yok → MemoryAdapter fail-open; sessiz kalmasın, gözlemlenir.
            $logger->securityEvent('rate_limiter_unavailable', ['mode' => 'fail-open']);
        }

        $pipeline = (new ApiMiddlewarePipeline())
            ->pipe(new ResponseNormalizationMiddleware())
            ->pipe(static fn (array $req, callable $next): array => $originCheckMiddleware->handle($req, $next))
            ->pipe(static fn (array $req, callable $next): array => $corsMiddleware->handle($req, $next))
            ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))
            // P1-9: hybrid auth — session önce, sonra RS256 Bearer (JwtService) +
            // jti revocation (user_tokens). Bağımlılıklar ApiAuthContainer SSOT'undan.
            ->pipe(new AuthenticationMiddleware(
                new ApiSessionManager(),
                \CoreMusic\Api\Container\ApiAuthContainer::jwtService(),
                static fn (string $jti): bool =>
                    \CoreMusic\Api\Container\ApiAuthContainer::userRepository()->isValidAccessToken($jti),
            ))
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
            // Handler: version-aware container dispatch
            static fn (array $route, array $request): array =>
                $container::handle($route, $request),
        );
        $result = $gateway->dispatch($pipelineRequest);

        // CorsMiddleware header'ları gövde yerine HTTP header katmanına yazar.
        foreach ($result['headers'] ?? [] as $headerName => $headerValue) {
            header($headerName . ': ' . $headerValue);
        }

        // Header anahtarı API sözleşmesine ({data,meta} / {error:{...}}) dahil değildir.
        unset($result['headers']);

        // Preflight (Cors 'halt' ile kısa devre eder) + Adım 07 DELETE 204:
        // gövdesiz yanıt. Dizi gövde (CsrfMiddleware {error:{code:CSRF_TOKEN_INVALID,...}} — F1 zarf) JSON
        // olarak basılır — eski (string) cast "Array" yazıyordu (P5-32 gözetimi
        // JSON contract'e uymuyordu).
        if (($result['halt'] ?? false) === true) {
            http_response_code((int) ($result['httpStatus'] ?? 204));
            $haltBody = $result['body'] ?? '';
            echo is_array($haltBody)
                ? (string) json_encode($haltBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : (string) $haltBody;
            exit;
        }

        $status = (int) http_response_code();
        // 2xx status controller'dan geldiği gibi korunur (Adım 07: 201 create,
        // 204 delete halt yolu); 4xx/5xx aynen, diğer durumlar 200.
        $emitStatus = ($status >= 200 && $status < 300) || $status >= 400 ? $status : 200;
        $jsonResponse($result, $emitStatus);
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