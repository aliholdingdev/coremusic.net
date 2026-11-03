<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Contracts\Middleware\IMiddleware;
use CoreMusic\Config\DomainConfig;

final class SecurityHeadersMiddleware implements IMiddleware
{
    public function __construct(
        private readonly ?DomainConfig $domainConfig = null
    ) {}

    public function handle(array $request, callable $next): array
    {
        // Nonce üret — SessionManager'dan önce çalışır, nonce'u request'e yaz
        if (empty($request['_csp_nonce'])) {
            $request['_csp_nonce'] = bin2hex(random_bytes(32));
        }

        $response = $next($request);

        $csp     = $this->buildCsp($request);
        $isHttps = $this->domainConfig?->isHttps() ?? false;

        $headers = [
            'X-Content-Type-Options'            => 'nosniff',
            'X-Frame-Options'                   => 'DENY',
            'X-XSS-Protection'                  => '0',
            'Referrer-Policy'                   => 'strict-origin-when-cross-origin',
            'Content-Security-Policy'           => $csp,
            'Require-Trusted-Types-For'         => "'script'",
            'Cross-Origin-Resource-Policy'      => 'cross-origin',
            'Permissions-Policy'                => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
            'X-Permitted-Cross-Domain-Policies' => 'none',
        ];

        if ($isHttps) {
            $headers['Strict-Transport-Security']    = 'max-age=31536000; includeSubDomains; preload';
            $headers['Cross-Origin-Opener-Policy']   = 'same-origin';
            $headers['Cross-Origin-Embedder-Policy'] = 'require-corp';
        }

        $response['headers'] = array_merge($response['headers'] ?? [], $headers);

        return $response;
    }

    private function buildCsp(array $request): string
    {
        $assetsOrigin = $this->resolveAssetsOrigin($request);
        $apiOrigin    = $this->resolveApiOrigin($request);
        $mediaOrigin  = $this->resolveMediaOrigin($request);
        $nonce        = $request['_csp_nonce'] ?? '';

        $scriptSrc = $nonce !== ''
            ? "'strict-dynamic' 'nonce-{$nonce}' https:"
            : "'self' https:";

        $styleSrc = $nonce !== ''
            ? "'nonce-{$nonce}' 'self' {$assetsOrigin} fonts.googleapis.com"
            : "'self' {$assetsOrigin} fonts.googleapis.com";

        return
            "default-src 'self'; " .
            "script-src {$scriptSrc}; " .
            "style-src {$styleSrc}; " .
            // ADR-110 — style-src-attr unsafe-inline KABUL EDİLMİŞ RİSK (kullanıcı onayı 2026-10-30, batch #2)
            "style-src-attr 'unsafe-inline'; " .
            "img-src 'self' data: {$assetsOrigin} {$mediaOrigin}; " .
            "font-src 'self' {$assetsOrigin} fonts.gstatic.com; " .
            "connect-src 'self' {$assetsOrigin} {$apiOrigin} {$mediaOrigin}; " .
            "media-src 'self' {$assetsOrigin} {$mediaOrigin}; " .
            "frame-ancestors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'";
    }

    private function resolveAssetsOrigin(array $request): string
    {
        return $this->resolveOrigin('assets', 'https://assets.coremusic.net');
    }

    private function resolveMediaOrigin(array $request): string
    {
        return $this->resolveOrigin('media', 'https://media.coremusic.net');
    }

    /** connect-src — API origin (auth sayfaları fetch -> api.coremusic.net). */
    private function resolveApiOrigin(array $request): string
    {
        return $this->resolveOrigin('api', 'https://api.coremusic.net');
    }

    private function resolveOrigin(string $name, string $fallback): string
    {
        if ($this->domainConfig !== null) {
            $url = $this->domainConfig->getUrl($name);
            if ($url !== '') {
                $parsed = parse_url($url);
                $scheme = $parsed['scheme'] ?? 'http';
                $host   = $parsed['host']   ?? '';
                $port   = isset($parsed['port']) ? ':' . $parsed['port'] : '';
                if ($host !== '') {
                    return $scheme . '://' . $host . $port;
                }
            }
        }

        return $fallback;
    }
}
