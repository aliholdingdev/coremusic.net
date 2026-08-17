<?php declare(strict_types=1);

namespace CoreMusic\Security;

final class ReturnUrlPolicy
{
    private const ALLOWED_HOSTS = [
        'coremusic.net',
        'home.coremusic.net',
        'auth.coremusic.net',
        'music.coremusic.net',
        'admin.coremusic.net',
        'localhost',
        '127.0.0.1',
    ];

    public static function getSafeUrl(?string $url, string $scheme = 'https'): string
    {
        if (empty($url)) {
            return '/';
        }

        $url = urldecode($url);

        if (str_starts_with($url, '/')) {
            return $url;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '/';
        }

        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return '/';
        }

        $host = strtolower($parsed['host']);
        $urlScheme = strtolower($parsed['scheme'] ?? '');

        if (in_array($urlScheme, ['javascript', 'data', 'vbscript'], true)) {
            return '/';
        }

        if (isset($parsed['user']) || isset($parsed['pass'])) {
            return '/';
        }

        foreach (self::ALLOWED_HOSTS as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return $url;
            }
        }

        return '/';
    }
}
