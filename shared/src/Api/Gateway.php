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
use CoreMusic\Api\Routing\RouteTable;
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
        private readonly ApiMiddlewarePipeline $middlewarePipeline,
        private readonly ?RouteTable $routeTable = null,
        private readonly ?\Closure $handlerResolver = null
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

        // Route eşleşmesi pipeline'ın DIŞINDA yapılır ki `_route` (ve 405/404
        // durumu) Authentication / Authorization / RequestValidation /
        // ResponseNormalization middleware'lerine ulaşsın (Faz 2 notu #2).
        $match = $this->matchRoute($apiRequest);
        $request['_route']        = $match['route'];
        $request['_route_status'] = $match['status'];
        $request['_route_allow']  = $match['allow'];

        try {
            return $this->middlewarePipeline->process(
                $request,
                function (array $request) use ($apiRequest, $version): array {
                    $status = $request['_route_status'] ?? RouteTable::STATUS_NOT_FOUND;
                    $route  = $request['_route'] ?? null;

                    if ($status === RouteTable::STATUS_METHOD_NOT_ALLOWED) {
                        return $this->methodNotAllowedResponse($request['_route_allow'] ?? []);
                    }

                    if ($status !== RouteTable::STATUS_OK || !is_array($route)) {
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
     * Route eşleşmesi — method-aware tablo (405 / 404 ayrımı).
     *
     * @return array{status: string, route: array<string, mixed>|null, allow: string[]}
     */
    private function matchRoute(ApiRequest $request): array
    {
        $table = $this->routeTable ?? RouteTable::defaults();

        return $table->match($request->getUri(), $request->getMethod());
    }

    /**
     * Yol biliniyor, method sunulmuyor → 405 + Allow başlığı.
     *
     * `Allow` yalnızca gerçekten sunulan methodları listeler (implemented
     * route'lar); Faz 1b AuthController bağlandığında POST satırı
     * `implemented: true` olur ve 405 ortadan kalkar.
     *
     * @param string[] $allow
     */
    private function methodNotAllowedResponse(array $allow): array
    {
        $header = implode(', ', $allow);

        $response = ApiResponse::error(
            'METHOD_NOT_ALLOWED',
            'Method not allowed for this endpoint',
            405,
            ['allow' => $header]
        );
        $response['headers'] = ['Allow' => $header];

        return $response;
    }

    /**
     * Invoke the route handler.
     *
     * Faz 1b: route'ta `action` varsa ve entry-point bir `handlerResolver`
     * verdiyse controller çağrılır (api.coremusic.net/index.php →
     * ApiAuthContainer::controller()). Resolver yoksa ya da route'ta action
     * yoksa davranış değişmez: placeholder yanıt döner (Faz 0/1a testleri).
     *
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     * @return array<string, mixed>
     */
    private function invokeHandler(array $route, array $request): array
    {
        if ($this->handlerResolver !== null && !empty($route['action'])) {
            $response = ($this->handlerResolver)($route, $request);

            if (is_array($response)) {
                return $response;
            }
        }

        return ApiResponse::ok([
            'service' => $route['service'],
            'handler' => $route['handler'],
            'message' => 'Handler not implemented yet',
        ]);
    }
}
