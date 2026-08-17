<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

final class CsrfMiddleware implements IMiddleware
{
    /** @var list<string> */
    private readonly array $bypassRoutes;

    /** @param list<string>|null $bypassRoutes */
    public function __construct(?array $bypassRoutes = null)
    {
        $this->bypassRoutes = $bypassRoutes ?? ['set-gender'];
    }

    public function handle(array $request, callable $next): array
    {
        $method = strtoupper($request['method'] ?? 'GET');

        $stateChanging = !in_array($method, ['GET', 'HEAD', 'OPTIONS'], true);
        if (!$stateChanging) {
            return $next($request);
        }

        $uri = trim((string)($request['uri'] ?? ''), '/');
        if (in_array($uri, $this->bypassRoutes, true)) {
            return $next($request);
        }

        $sessionToken = ($request['_session']['csrf_token'] ?? '') ?: '';
        if ($sessionToken === '') {
            return $this->fail();
        }

        $submitted = $request['headers']['x-csrf-token'] ?? '';
        if ($submitted === '') {
            $submitted = $request['body']['csrf_token'] ?? '';
        }

        if ($submitted === '' || !hash_equals($sessionToken, (string)$submitted)) {
            return $this->fail();
        }

        return $next($request);
    }

    private function fail(): array
    {
        return [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => ['error' => 'csrf_invalid'],
            'headers'    => [],
            'halt'       => true,
        ];
    }
}
