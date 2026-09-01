<?php declare(strict_types=1);

namespace CoreMusic\Auth\Middleware;

/**
 * Security Headers Middleware — Güvenlik başlıklarını ekler.
 *
 * CSP, HSTS, X-Content-Type-Options vb. başlıkları ayarlar.
 */
final class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function process(array $request, callable $next): array
    {
        $result = $next($request);

        // Güvenlik başlıklarını ekle
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // HTTPS ise HSTS ekle
        $isHttps = !empty($request['server']['HTTPS']) && $request['server']['HTTPS'] !== 'off';
        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // CSP nonce varsa ekle
        $nonce = $request['server']['csp_nonce'] ?? '';
        if ($nonce !== '') {
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}'; style-src 'self' 'unsafe-inline'");
        }

        return $result;
    }
}
