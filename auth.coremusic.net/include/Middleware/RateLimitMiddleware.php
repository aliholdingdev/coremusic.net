<?php declare(strict_types=1);

namespace CoreMusic\Auth\Middleware;

/**
 * Rate Limit Middleware — İstek hızını kısıtlar.
 *
 * IP bazlı rate limiting uygular.
 * APCu veya cache tabanlı çalışır.
 */
final class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly int $maxRequests = 60,
        private readonly int $windowSeconds = 60,
    ) {}

    public function process(array $request, callable $next): array
    {
        $clientIp = $request['server']['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rate_limit:auth:' . $clientIp;

        // APCu mevcutsa kullan
        if (function_exists('apcu_exists') && apcu_exists($key)) {
            $data = apcu_fetch($key);
            if ($data !== false) {
                $count = $data['count'] ?? 0;
                $start = $data['start'] ?? time();

                if ((time() - $start) > $this->windowSeconds) {
                    // Pencere sıfırlandı
                    apcu_store($key, ['count' => 1, 'start' => time()], $this->windowSeconds);
                } elseif ($count >= $this->maxRequests) {
                    return [
                        'httpStatus' => 429,
                        'type'       => 'json',
                        'body'       => [
                            'success' => false,
                            'error'   => [
                                'code'    => 'RATE_LIMIT_EXCEEDED',
                                'message' => 'Çok fazla istek. Lütfen bekleyin.',
                            ],
                        ],
                        'headers' => [
                            'Retry-After' => (string)($this->windowSeconds - (time() - $start)),
                        ],
                    ];
                } else {
                    apcu_store($key, ['count' => $count + 1, 'start' => $start], $this->windowSeconds);
                }
            } else {
                apcu_store($key, ['count' => 1, 'start' => time()], $this->windowSeconds);
            }
        }

        return $next($request);
    }
}
