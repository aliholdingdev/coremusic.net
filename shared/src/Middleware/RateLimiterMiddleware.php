<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
use CoreMusic\Cache\CacheInterface;
use CoreMusic\Cache\CacheManager;

final class RateLimiterMiddleware implements IMiddleware
{
    private const CACHE_KEY_PREFIX = 'rl:';
    private const DEFAULT_IP       = '0.0.0.0';

    /** @var string[] */
    private readonly array $trustedProxies;

    public function __construct(
        private readonly int $maxRequests   = 60,
        private readonly int $windowSeconds = 60,
        ?array $trustedProxies = null,
        private readonly ?CacheInterface $cache = null,
    ) {
        $this->trustedProxies = $trustedProxies
            ?? (defined('TRUSTED_PROXIES') && is_array(TRUSTED_PROXIES) ? TRUSTED_PROXIES : ['127.0.0.1', '::1']);
    }

    public function handle(array $request, callable $next): array
    {
        $cache = $this->cache ?? $this->resolveCacheAdapter();
        if ($cache === null) {
            return $next($request);
        }

        $ip  = $this->resolveClientIp($request);
        $key = self::CACHE_KEY_PREFIX . md5($ip);

        $isNew = $cache->set($key, 1, $this->windowSeconds);
        if ($isNew) {
            return $next($request);
        }

        $count = $cache->increment($key, 1);
        if ($count === false || $count === null) {
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
}
