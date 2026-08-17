<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Middleware;

interface IMiddleware
{
    public function handle(array $request, callable $next): array;
}
