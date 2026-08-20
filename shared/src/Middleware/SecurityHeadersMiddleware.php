<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;
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
            'X-Content-Type-Options'         => 'nosniff',
            'X-Frame-Options'                => 'DENY',
            'X-XSS-Protection'               => '0',
            'Referrer-Policy'                => 'strict-origin-when-cross-origin',
            'Content-Security-Policy'        => $csp,
            'Require-Trusted-Types-For'      => "'script'",
            'Cross-Origin-Resource-Policy'   => 'cross-origin',
            'Permissions-Policy'             => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
            'X-Permitted-Cross-Domain-Policies' => 'none',
        ];

        if ($isHttps) {
            $headers['Cross-Origin-Opener-Policy']   = 'same-origin';
            $headers['Cross-Origin-Embedder-Policy'] = 'require-corp';
        }

        $response['headers'] = array_merge($response['headers'] ?? [], $headers);

        return $response;
    }

    private function buildCsp(array $request): string
    {
        $assetsOrigin = $this->resolveAssetsOrigin($request);
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
            "img-src 'self' data: {$assetsOrigin}; " .
            "font-src 'self' {$assetsOrigin} fonts.gstatic.com; " .
            "connect-src 'self' {$assetsOrigin}; " .
            "media-src 'self' {$assetsOrigin}; " .
            "frame-ancestors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'";
    }

    private function resolveAssetsOrigin(array $request): string
    {
        if ($this->domainConfig !== null) {
            $assetsUrl = $this->domainConfig->getUrl('assets');
            if ($assetsUrl !== '') {
                $parsed = parse_url($assetsUrl);
                $scheme = $parsed['scheme'] ?? 'http';
                $host   = $parsed['host']   ?? 'assets.coremusic.net';
                $port   = isset($parsed['port']) ? ':' . $parsed['port'] : '';
                return $scheme . '://' . $host . $port;
            }
        }
        return 'https://assets.coremusic.net';
    }
}
