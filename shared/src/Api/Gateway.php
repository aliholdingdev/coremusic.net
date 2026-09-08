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
use CoreMusic\Api\Versioning\VersionResolver;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;

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
     * Dispatch an API request through the middleware pipeline.
     */
    public function dispatch(array $request): array
    {
        // Create API request object
        $apiRequest = new ApiRequest();
        
        // Resolve API version
        $version = $this->versionResolver->resolve($apiRequest);
        $apiRequest = $apiRequest->withAttribute('version', $version);
        
        // Match route
        $route = $this->matchRoute($apiRequest, $version);
        if ($route === null) {
            return ApiResponse::error('NOT_FOUND', 'Route not found', 404);
        }
        
        $apiRequest = $apiRequest->withAttribute('route', $route);
        
        // Execute middleware pipeline
        try {
            $response = $this->middlewarePipeline->process(
                $request,
                function (array $request) use ($route) {
                    return $this->invokeHandler($route, $request);
                }
            );
            
            return $response;
        } catch (\Throwable $e) {
            return ApiResponse::error(
                'INTERNAL_ERROR',
                'An internal error occurred',
                500,
                ['exception' => $e->getMessage()]
            );
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
