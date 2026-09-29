<?php
declare(strict_types=1);

/**
 * API Gateway implementation.
 *
 * @file Gateway.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api;

use CoreMusic\Contracts\Api\GatewayInterface;
use CoreMusic\Contracts\Api\ServiceRegistryInterface;
use CoreMusic\Api\Versioning\ApiVersion;
use CoreMusic\Api\Versioning\VersionResolver;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Log\LoggerFactory;
use CoreMusic\Security\UuidV7;

/**
 * API Gateway implementation following GatewayInterface.
 */
final class Gateway implements GatewayInterface
{
    public function __construct(
        private readonly VersionResolver $versionResolver,
        private readonly ServiceRegistryInterface $serviceRegistry,
        private readonly ApiMiddlewarePipeline $middlewarePipeline
    ) {}

    /**
     * Kayıt defteri erişimi (sağlık tanımı / servis keşfi için).
     */
    public function getServiceRegistry(): ServiceRegistryInterface
    {
        return $this->serviceRegistry;
    }

    /**
     * Dispatch an API request through the middleware pipeline.
     *
     * Route matching middleware pipeline'ın İÇİNDE çalışır: CORS, rate limit,
     * auth ve validation eşleşmeyen rotalarda bile atlanmaz (ADR-020 §1.1-B.1).
     */
    public function dispatch(array $request): array
    {
        // Create API request object
        $apiRequest = new ApiRequest();

        // Resolve API version
        $version = $this->versionResolver->resolve($apiRequest);
        $apiRequest = $apiRequest->withAttribute('version', $version);

        try {
            return $this->middlewarePipeline->process(
                $request,
                function (array $request) use ($apiRequest, $version): array {
                    $route = $this->matchRoute($apiRequest, $version);
                    if ($route === null) {
                        return ApiResponse::error('NOT_FOUND', 'Route not found', 404);
                    }

                    $apiRequest->withAttribute('route', $route);

                    return $this->invokeHandler($route, $request);
                }
            );
        } catch (\Throwable $e) {
            $traceId = UuidV7::generate();

            LoggerFactory::getInstance()->error('API Gateway unhandled exception', [
                'trace_id'  => $traceId,
                'exception' => $e::class,
                'message'   => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
                'uri'       => $apiRequest->getUri(),
                'method'    => $apiRequest->getMethod(),
            ]);

            // ADR-020 §2.2F / OWASP API3: exception detayı istemciye asla dönmez.
            return ApiResponse::error('INTERNAL_ERROR', 'Servis hatası, tekrar deneyin.', 500);
        }
    }

    /**
     * Match route against registered routes.
     */
    private function matchRoute(ApiRequest $request, ApiVersion $version): ?array
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // This is a simplified route matching
        // In production, this would use a proper router
        $routes = [
            'GET' => [
                '/api/v1/auth' => ['service' => 'auth', 'handler' => 'authController'],
                '/api/v1/user' => ['service' => 'user', 'handler' => 'userController'],
                '/api/v1/music' => ['service' => 'music', 'handler' => 'musicController'],
                '/api/v1/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],
                '/api/v1/media' => ['service' => 'media', 'handler' => 'mediaController'],
                '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],
            ],
        ];

        $methodRoutes = $routes[$method] ?? [];

        foreach ($methodRoutes as $pattern => $route) {
            if (str_starts_with($uri, $pattern)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Invoke the route handler.
     */
    private function invokeHandler(array $route, array $request): array
    {
        // This would be implemented with actual service resolution
        // For now, return a placeholder response
        return ApiResponse::ok([
            'service' => $route['service'],
            'handler' => $route['handler'],
            'message' => 'Handler not implemented yet',
        ]);
    }
}
