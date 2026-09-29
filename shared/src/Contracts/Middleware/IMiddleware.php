<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Middleware;

interface IMiddleware
{
    public function handle(array $request, callable $next): array;
}
