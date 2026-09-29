<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Contracts\Middleware\IMiddleware;
use CoreMusic\Cache\CacheInterface;
use CoreMusic\Cache\CacheManager;

final class RateLimiterMiddleware implements IMiddleware
{
    private const CACHE_KEY_PREFIX = 'rl:';
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
        private readonly ?CacheInterface $cache = null,
        ?array $failClosedPaths = null,
    ) {
        $this->trustedProxies = $trustedProxies
            ?? (defined('TRUSTED_PROXIES') && is_array(TRUSTED_PROXIES) ? TRUSTED_PROXIES : ['127.0.0.1', '::1']);
        $this->failClosedPaths = $failClosedPaths ?? self::AUTH_FAIL_CLOSED_PATHS;
    }

    public function handle(array $request, callable $next): array
    {
        $cache = $this->cache ?? $this->resolveCacheAdapter();
        if ($cache === null) {
            $path = $this->resolveRequestPath($request);
            if (in_array($path, $this->failClosedPaths, true)) {
                // ADR-013 §5.4 şart 1a: auth uçları fail-closed.
                error_log('[ADR-013] rate limiter cache unavailable -> fail-closed on ' . $path);
                return [
                    'httpStatus' => 503,
                    'type'       => 'json',
                    'body'       => ['error' => 'rate_limiter_unavailable'],
                    'headers'    => ['Retry-After' => (string)$this->windowSeconds],
                    'halt'       => true,
                ];
            }
            // ADR-013 §5.4 şart 1a: genel uçlar fail-open — ama gözlemlenebilir olmalı.
            error_log('[ADR-013] rate limiter cache unavailable -> fail-open on ' . $path);
            return $next($request);
        }

        $ip  = $this->resolveClientIp($request);
        $key = self::CACHE_KEY_PREFIX . md5($ip);

        $isNew = $cache->set($key, 1, $this->windowSeconds);
        if ($isNew) {
            return $next($request);
        }

        $count = $cache->increment($key, 1);
        if ($count === false) {
            error_log('[ADR-013] rate limiter increment failed -> fail-open on ' . $this->resolveRequestPath($request));
            $cache->set($key, 1, $this->windowSeconds);
            return $next($request);
        }

        if ((int)$count > $this->maxRequests) {
            return [
                'httpStatus' => 429,
                'type'       => 'json',
                'body'       => ['error' => 'rate_limit_exceeded'],
                'headers'    => ['Retry-After' => (string)$this->windowSeconds],
                'halt'       => true,
            ];
        }

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

    private function resolveCacheAdapter(): ?CacheInterface
    {
        try {
            return CacheManager::getAdapter();
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
