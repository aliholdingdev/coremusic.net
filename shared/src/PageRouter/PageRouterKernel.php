<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Cache\PageCacheAdapter;
use CoreMusic\Exception\ErrorResponse;
use CoreMusic\Middleware\SessionManagerMiddleware;
use CoreMusic\Middleware\BypassAuthMiddleware;
use CoreMusic\Middleware\RateLimiterMiddleware;
use CoreMusic\Middleware\AuthMiddleware;
use CoreMusic\Middleware\SecurityHeadersMiddleware;
use CoreMusic\Middleware\CsrfMiddleware;
use CoreMusic\Middleware\MiddlewarePipeline;

final class PageRouterKernel
{
    private const RATE_LIMIT_MAX    = 60;
    private const RATE_LIMIT_WINDOW = 60;

    private readonly RequestNormalizer $normalizer;
    private readonly HtmlShellRenderer $shellRenderer;
    private readonly ResponseEmitter   $emitter;
    private readonly ErrorHandler      $errorHandler;
    private readonly StructuredLogger  $logger;
    private readonly RouteRegistry     $registry;
    private readonly PageRouter        $router;
    /** @var \CoreMusic\Interfaces\Middleware\IMiddleware[] */
    private readonly array $middlewares;
    /** @var array<string, object> */
    private readonly array $handlers;

    public function __construct(
        private readonly ConfigManager  $config,
        private readonly DomainConfig   $domainConfig,
        ?string $headerPath = null,
        ?string $footerPath = null,
        ?RequestNormalizer $normalizer = null,
        ?HtmlShellRenderer $shellRenderer = null,
        ?ResponseEmitter $emitter = null,
        ?ErrorHandler $errorHandler = null,
        ?RouteRegistry $registry = null,
        ?PageRouter $router = null,
        ?StructuredLogger $logger = null,
        ?array $middlewares = null,
        ?array $handlers = null,
    ) {
        $this->normalizer    = $normalizer ?? new RequestNormalizer();
        $this->shellRenderer = $shellRenderer ?? new HtmlShellRenderer($config, $domainConfig, $headerPath, $footerPath);
        $this->emitter       = $emitter ?? new ResponseEmitter();
        $this->errorHandler  = $errorHandler ?? new ErrorHandler($this->shellRenderer);
        $this->logger        = $logger ?? new StructuredLogger('info');
        $this->registry      = $registry ?? new RouteRegistry();
        $this->handlers      = $handlers ?? [];

        $authHelper = new PageRouterHelper();
        $urlBuilder = new AuthUrlBuilder($domainConfig, $authHelper);
        $authGuard  = new AuthGuard($authHelper, $urlBuilder);

        $this->router = $router ?? new PageRouter(
            $this->registry,
            $config,
            $domainConfig,
            $authHelper,
            $authGuard,
            $urlBuilder,
            new PageCacheAdapter(),
            $this->handlers,
        );

        $this->middlewares = $middlewares ?? $this->buildDefaultMiddlewares();
    }

    public function handle(
        array  $server = [],
        array  $get    = [],
        array  $post   = [],
        string $routesFile = ''
    ): void {
        $traceId = self::generateTraceId();
        $this->logger->setTraceId($traceId);

        $isSpa = false;
        $protectedRoutes = [];

        try {
            $request = $this->normalizeRequest($server, $get, $post);

            if ($routesFile !== '') {
                $this->registry->loadFromFile($routesFile);
            }

            $isSpa           = self::isSpaRequest($request);
            $protectedRoutes = $this->registry->getProtectedRouteKeys();

            $response = $this->runMiddlewareStack(
                $this->middlewares,
                $request,
                function (array $req) use ($isSpa, $protectedRoutes): array {
                    $result = $this->router->dispatch($req, '', $isSpa);
                    if (!$isSpa) {
                        return $this->wrapInHtmlShell($result, $protectedRoutes, $req);
                    }
                    return $result;
                }
            );

            $this->emitter->emit($response, $traceId, $isSpa);

        } catch (\Throwable $e) {
            error_log('[PageRouterKernel] FATAL traceId=' . $traceId . ' ' . $e->getMessage()
                . ' in ' . $e->getFile() . ':' . $e->getLine());

            $response = [
                'httpStatus' => 500,
                'type'       => 'json',
                'body'       => ErrorResponse::create(500, 'Sunucu hatası oluştu.', 'SERVER_INTERNAL_ERROR', $traceId),
            ];

            if (!$isSpa) {
                $response = $this->wrapInHtmlShell($response, $protectedRoutes, []);
            }

            $this->emitter->emit($response, $traceId, $isSpa);
        }
    }

    private function wrapInHtmlShell(array $result, array $protectedRoutes = [], array $request = []): array
    {
        $body       = $result['body']       ?? [];
        $httpStatus = $result['httpStatus'] ?? 200;
        $type       = $result['type']       ?? 'json';

        if ($type === 'redirect' || $httpStatus === 302) {
            $location = $result['headers']['Location'] ?? '/';
            return ['httpStatus' => 302, 'type' => 'redirect', 'body' => '', 'headers' => ['Location' => $location]];
        }

        if ($httpStatus === 403 && isset($body['redirect'])) {
            $redirectUrl = $this->buildRedirectUrl((string)$body['redirect']);
            return ['httpStatus' => 302, 'type' => 'redirect', 'body' => '', 'headers' => ['Location' => $redirectUrl]];
        }

        if ($httpStatus === 404) {
            return $this->errorHandler->buildErrorHtmlResult(404, $body);
        }

        if ($httpStatus >= 400) {
            return $this->errorHandler->buildErrorHtmlResult($httpStatus, $body);
        }

        $container  = (string)($body['container']  ?? '');
        $route      = (string)($body['route']       ?? 'home');
        $meta       = (array)($body['meta']         ?? []);
        $csrfToken  = (string)($body['csrf_token']  ?? '');
        $sessionData = $request['_session'] ?? $_SESSION;

        $html = $this->shellRenderer->render($container, $route, $meta, $csrfToken, $protectedRoutes, $sessionData);

        return ['httpStatus' => 200, 'type' => 'html', 'body' => $html, 'headers' => $result['headers'] ?? []];
    }

    private function buildRedirectUrl(string $redirect): string
    {
        $path = ltrim($redirect, '/');
        if ($path !== '' && !str_contains($path, '?') && !str_contains($path, '://')) {
            $segments = explode('/', $path);
            $encoded  = implode('/', array_map('rawurlencode', $segments));
            return '/' . $encoded;
        }
        return $redirect;
    }

    private function normalizeRequest(array $server, array $get, array $post): array
    {
        $this->normalizer->normalize();

        $uri = '';
        if (isset($_SERVER['REQUEST_URI'])) {
            $parsed = parse_url((string)$_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri    = trim((string)($parsed ?? ''), '/');
        }

        if ($uri === '' || $uri === 'index.php') {
            $uri = $get['page'] ?? $get['uri'] ?? '';
        }

        if (str_contains($uri, '?')) {
            $uri = explode('?', $uri)[0];
        }
        $uri = trim($uri, '/');

        return [
            'method'  => strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            'uri'     => $uri,
            'headers' => $this->extractHeaders($_SERVER),
            'body'    => $post,
            'server'  => $_SERVER,
            'query'   => $get,
        ];
    }

    private function extractHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerKey           = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerKey] = (string)$value;
            }
        }
        if (isset($server['CONTENT_TYPE'])) {
            $headers['content-type'] = (string)$server['CONTENT_TYPE'];
        }
        return $headers;
    }

    private function runMiddlewareStack(array $middlewares, array $request, callable $core): array
    {
        $pipeline = new MiddlewarePipeline($this->logger);
        foreach ($middlewares as $middleware) {
            $pipeline->pipe($middleware);
        }
        return $pipeline->run($request, $core);
    }

    private static function isSpaRequest(array $request): bool
    {
        return ($request['headers']['x-requested-with'] ?? '') === 'XMLHttpRequest';
    }

    /** @return \CoreMusic\Interfaces\Middleware\IMiddleware[] */
    private function buildDefaultMiddlewares(): array
    {
        $sessionInit = new SessionInitializer();
        return [
            new SessionManagerMiddleware($sessionInit),
            new BypassAuthMiddleware($this->config),
            new RateLimiterMiddleware(self::RATE_LIMIT_MAX, self::RATE_LIMIT_WINDOW),
            new AuthMiddleware(),
            new SecurityHeadersMiddleware($this->domainConfig),
            new CsrfMiddleware(),
        ];
    }

    private static function generateTraceId(): string
    {
        $bytes    = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
