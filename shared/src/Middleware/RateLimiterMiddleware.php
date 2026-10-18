<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Contracts\Middleware\IMiddleware;
use CoreMusic\Contracts\Security\IRateLimiter;
use CoreMusic\Cache\CacheManager;
use CoreMusic\Security\CacheRateLimiter;

final class RateLimiterMiddleware implements IMiddleware
{
    private const DEFAULT_IP       = '0.0.0.0';

    /**
     * Cache çözülemezse fail-closed uygulanacak auth uçları (ADR-013 §5.4 şart 1a).
     * Genel uçlar fail-open kalır (ADR-013 §3 — hizmet sürekliliği).
     *
     * @var string[]
     */
    public const AUTH_FAIL_CLOSED_PATHS = [
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/logout',
        '/set-gender',
        '/select-gender',
        '/session',
        '/validate-key',
    ];

    /** @var string[] */
    private readonly array $trustedProxies;

    /** @var string[] */
    private readonly array $failClosedPaths;

    public function __construct(
        private readonly int $maxRequests   = 60,
        private readonly int $windowSeconds = 60,
        ?array $trustedProxies = null,
        private readonly ?IRateLimiter $rateLimiter = null,
        ?array $failClosedPaths = null,
    ) {
        $this->trustedProxies = $trustedProxies
            ?? (defined('TRUSTED_PROXIES') && is_array(TRUSTED_PROXIES) ? TRUSTED_PROXIES : ['127.0.0.1', '::1']);
        $this->failClosedPaths = $failClosedPaths ?? self::AUTH_FAIL_CLOSED_PATHS;
    }

    public function handle(array $request, callable $next): array
    {
        $limiter = $this->resolveRateLimiter();
        if ($limiter === null) {
            $path = $this->resolveRequestPath($request);
            if (in_array($path, $this->failClosedPaths, true)) {
                // ADR-013 §5.4 şart 1a: auth uçları fail-closed.
                \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limiter_unavailable', ['path' => $path, 'mode' => 'fail-closed']);
                return [
                    'httpStatus' => 503,
                    'type'       => 'json',
                    'body'       => ['error' => 'rate_limiter_unavailable'],
                    'headers'    => ['Retry-After' => (string)$this->windowSeconds],
                    'halt'       => true,
                ];
            }
            // ADR-013 §5.4 şart 1a: genel uçlar fail-open — ama gözlemlenebilir olmalı.
            \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limiter_unavailable', ['path' => $path, 'mode' => 'fail-open']);
            return $next($request);
        }

        $ip  = $this->resolveClientIp($request);
        $key = md5($ip);

        // Varlık kontrolü önce: sayaç maxRequests'e ulaştıysa istek sayılır ve 429 döner.
        if ($limiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {
            // P5-32: 429 artık gözlemlenir (öncekte hiç loglanmıyordu).
            \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limited', [
                'ip'   => $ip,
                'path' => $this->resolveRequestPath($request),
                'code' => 429,
            ]);
            return [
                'httpStatus' => 429,
                'type'       => 'json',
                'body'       => ['error' => 'rate_limit_exceeded'],
                'headers'    => ['Retry-After' => (string)$this->windowSeconds],
                'halt'       => true,
            ];
        }

        $limiter->increment($key, $this->windowSeconds);

        return $next($request);
    }

    private function resolveClientIp(array $request): string
    {
        $server   = $request['server'] ?? [];
        $remoteIp = $server['REMOTE_ADDR'] ?? self::DEFAULT_IP;

        if (filter_var($remoteIp, FILTER_VALIDATE_IP) && $this->isTrustedProxy($remoteIp)) {
            foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP'] as $key) {
                if (!empty($server[$key])) {
                    $ip = trim(explode(',', (string)$server[$key])[0]);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return filter_var($remoteIp, FILTER_VALIDATE_IP) ? $remoteIp : self::DEFAULT_IP;
    }

    private function isTrustedProxy(string $ip): bool
    {
        return in_array($ip, $this->trustedProxies, true);
    }

    private function resolveRateLimiter(): ?IRateLimiter
    {
        if ($this->rateLimiter !== null) {
            return $this->rateLimiter;
        }

        try {
            return new CacheRateLimiter(CacheManager::getAdapter());
        } catch (\RuntimeException) {
            return null;
        }
    }

    private function resolveRequestPath(array $request): string
    {
        $uri  = (string)($request['uri'] ?? ($request['server']['REQUEST_URI'] ?? '/'));
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';

        return '/' . trim($path, '/');
    }
}
