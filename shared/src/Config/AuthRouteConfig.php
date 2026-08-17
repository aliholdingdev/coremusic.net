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

    public static function getAuthUrl(string $scheme = 'http'): string
    {
        return $scheme . '://auth.coremusic.net';
    }

    public static function buildAuthRedirectUrl(
        string $route,
        string $returnUrl = '',
        string $authDomain = '',
        string $callbackDomain = '',
    ): string {
        if ($authDomain === '') {
            $authDomain = self::getAuthUrl('https');
        }
        $baseUrl = $callbackDomain !== '' ? $callbackDomain : 'https://home.coremusic.net';
        $callbackUrl = $baseUrl . '/auth/callback';
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
