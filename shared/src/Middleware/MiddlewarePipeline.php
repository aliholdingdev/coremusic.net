<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
use CoreMusic\PageRouter\StructuredLogger;

final class MiddlewarePipeline
{
    /** @var IMiddleware[] */
    private array $middlewares = [];

    public function __construct(
        private readonly ?StructuredLogger $logger = null
    ) {}

    public function pipe(IMiddleware $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function run(array $request, callable $core): array
    {
        $pipeline = $this->buildPipeline($core);
        return $pipeline($request);
    }

    private function buildPipeline(callable $core): callable
    {
        $pipeline = $core;
        foreach (array_reverse($this->middlewares) as $middleware) {
            $pipeline = $this->wrap($middleware, $pipeline);
        }
        return $pipeline;
    }

    private function wrap(IMiddleware $middleware, callable $next): callable
    {
        return function (array $request) use ($middleware, $next): array {
            $name  = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
            $start = hrtime(true);

            $response = $middleware->handle($request, $next);

            $durationMs = (hrtime(true) - $start) / 1_000_000;
            if ($this->logger !== null) {
                $this->logger->timing('MiddlewarePipeline', $name, $durationMs);
            }

            return $response;
        };
    }
}
