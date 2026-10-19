<?php declare(strict_types=1);

namespace CoreMusic\Config;

final class AuthRouteConfig
{
    private const CLIENT_ID = 'coremusic-web';

    private const AUTH_ROUTES = [
        'login',
        'register',
        'select-gender',
        'forgot-password',
        'reset-password',
    ];

    public static function isAuthRoute(string $uri): bool
    {
        $normalized = ltrim(trim($uri), '/');
        return in_array($normalized, self::AUTH_ROUTES, true);
    }

    public static function isAuthRedirectRoute(string $uri): bool
    {
        return self::isAuthRoute($uri);
    }

    public static function getAuthRoutes(): array
    {
        return self::AUTH_ROUTES;
    }

    public static function getAuthUrl(string $scheme = 'http', ?DomainConfig $domain = null): string
    {
        $host = $domain?->getSubdomainHost('auth') ?? 'auth.coremusic.net';
        return $scheme . '://' . $host;
    }

    public static function getHomeUrl(string $scheme = 'http', ?DomainConfig $domain = null): string
    {
        $host = $domain?->getSubdomainHost('home') ?? 'home.coremusic.net';
        $port = $domain?->getSubdomainPortByName('home') ?? 80;
        $portSuffix = ($port !== 80 && $port !== 443) ? ':' . $port : '';
        return $scheme . '://' . $host . $portSuffix;
    }

    public static function buildAuthRedirectUrl(
        string $route,
        string $returnUrl = '',
        string $authDomain = '',
        string $callbackDomain = '',
        ?DomainConfig $domain = null,
    ): string {
        if ($authDomain === '') {
            $authDomain = self::getAuthUrl('http', $domain);
        }
        if ($callbackDomain === '') {
            $callbackDomain = self::getHomeUrl('http', $domain);
        }
        $callbackUrl = $callbackDomain . '/auth/callback';
        $params = [
            'client_id'     => self::CLIENT_ID,
            'response_type' => 'session',
            'redirect_uri'  => $returnUrl !== ''
                ? $callbackUrl . '?return=' . urlencode($returnUrl)
                : $callbackUrl,
        ];

        return $authDomain . '/' . $route . '?' . http_build_query($params);
    }
}
