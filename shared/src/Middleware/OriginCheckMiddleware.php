<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

/**
 * OriginCheck Middleware (L1 — Pipeline #1)
 *
 * İsteğin HTTP Origin başlığını izin verilen whitelist'e karşı kontrol eder.
 * CORS-inline handler'lardan bağımsız, middleware pipeline'da çalışır.
 *
 * ADR-010/022 uyumlu. Frozen sıra: OriginCheck → Cors → RateLimiter → ...
 */
final class OriginCheckMiddleware implements IMiddleware
{
    /** @var string[] */
    private readonly array $allowedOrigins;

    /** @var string[] */
    private readonly array $devFallbacks;

    /**
     * @param array{allowed_origins?: array<string,string>, dev_fallback?: string[]} $corsConfig
     */
    public function __construct(
        private readonly bool $isProduction = false,
        ?array $corsConfig = null,
    ) {
        $this->allowedOrigins = $corsConfig['allowed_origins'] ?? [];
        $this->devFallbacks   = $corsConfig['dev_fallback'] ?? [];
    }

    public function handle(array $request, callable $next): array
    {
        $origin = $request['server']['HTTP_ORIGIN'] ?? '';

        // Same-origin veya origin yoksa → devam
        if ($origin === '') {
            return $next($request);
        }

        if ($this->isOriginAllowed($origin)) {
            return $next($request);
        }

        // İzinsiz origin → 403
        return [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => ['error' => 'origin_not_allowed', 'message' => 'CORS origin policy violation.'],
            'headers'    => [],
            'halt'       => true,
        ];
    }

    private function isOriginAllowed(string $origin): bool
    {
        $parsed = parse_url($origin);
        if ($parsed === false || empty($parsed['host'])) {
            return false;
        }

        $host = strtolower($parsed['host']);

        // Allowed origins listesinde kontrol
        foreach ($this->allowedOrigins as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost)) {
                return true;
            }
        }

        // Production'da dev fallback yok
        if ($this->isProduction) {
            return false;
        }

        // Dev fallback
        foreach ($this->devFallbacks as $fallback) {
            if ($origin === $fallback) {
                return true;
            }
        }

        return false;
    }
}
