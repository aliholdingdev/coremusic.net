<?php declare(strict_types=1);

namespace CoreMusic\Auth\Middleware;

/**
 * Origin Check Middleware — Gelen isteğin kaynağını doğrular.
 *
 * Sadece izin verilen CoreMusic subdomain'lerinden gelen isteklere izin verir.
 * İlk middleware olarak çalışır — güvenlik duvarı görevi görür.
 */
final class OriginCheckMiddleware implements MiddlewareInterface
{
    private const ALLOWED_ORIGINS = [
        'auth.coremusic.net',
        'music.coremusic.net',
        'admin.coremusic.net',
        'home.coremusic.net',
        'api.coremusic.net',
        'media.coremusic.net',
        'download.coremusic.net',
        'coremusic.net',
        'localhost',
        '127.0.0.1',
    ];

    public function process(array $request, callable $next): array
    {
        $origin = $request['server']['HTTP_ORIGIN'] ?? '';
        $referer = $request['server']['HTTP_REFERER'] ?? '';
        $host = $request['server']['HTTP_HOST'] ?? '';

        // Health ve session check endpoint'leri için origin kontrolü gevşet
        $uri = $request['uri'] ?? '';
        if (in_array($uri, ['/health', '/session', '/validate-key'], true)) {
            return $next($request);
        }

        // Origin varsa kontrol et
        if ($origin !== '') {
            $parsedOrigin = parse_url($origin);
            if ($parsedOrigin === false || empty($parsedOrigin['host'])) {
                return $this->reject('Invalid origin');
            }
            if (!$this->isAllowed($parsedOrigin['host'])) {
                return $this->reject('Origin not allowed: ' . $parsedOrigin['host']);
            }
        }

        // Origin yoksa Referer'dan kontrol et
        if ($origin === '' && $referer !== '') {
            $parsedReferer = parse_url($referer);
            if ($parsedReferer !== false && !empty($parsedReferer['host'])) {
                if (!$this->isAllowed($parsedReferer['host'])) {
                    return $this->reject('Referer not allowed: ' . $parsedReferer['host']);
                }
            }
        }

        return $next($request);
    }

    private function isAllowed(string $host): bool
    {
        $host = strtolower($host);
        foreach (self::ALLOWED_ORIGINS as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return true;
            }
        }
        return false;
    }

    private function reject(string $message): array
    {
        return [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => [
                'success' => false,
                'error'   => [
                    'code'    => 'ORIGIN_NOT_ALLOWED',
                    'message' => $message,
                ],
            ],
        ];
    }
}
