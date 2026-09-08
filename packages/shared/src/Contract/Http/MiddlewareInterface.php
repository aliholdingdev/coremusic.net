<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Http;

use Closure;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, Closure $next): ResponseInterface;
}
