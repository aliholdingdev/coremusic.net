<?php
declare(strict_types=1);

/**
 * API Middleware Pipeline.
 *
 * @file ApiMiddlewarePipeline.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

/**
 * API Middleware Pipeline for request processing.
 */
final class ApiMiddlewarePipeline
{
    private array $middlewares = [];

    /**
     * Add middleware to the pipeline.
     */
    public function pipe(callable $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Process request through the middleware pipeline.
     */
    public function process(array $request, callable $handler): array
    {
        $pipeline = $handler;
        
        // Build pipeline in reverse order
        foreach (array_reverse($this->middlewares) as $middleware) {
            $pipeline = function (array $request) use ($middleware, $pipeline) {
                return $middleware($request, $pipeline);
            };
        }
        
        return $pipeline($request);
    }
}
