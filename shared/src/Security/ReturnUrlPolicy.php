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

    /**
     * URL'nin güvenli olup olmadığını kontrol et (bool).
     * SecurityHelper::isRedirectUriSafe() bu metoda yönlendirilir.
     *
     * Kural: getSafeUrl() decode edilmiş URL'yi aynen döndürüyorsa ve ham URL
     * decode edilmiş hâliyle aynı authority'e sahipse → güvenli.
     * '/' dönüyorsa → güvenli değil (redirect engellendi).
     */
    public static function isAllowed(string $url): bool
    {
        if ($url === '' || $url === '/') {
            return true;
        }

        if (str_starts_with($url, '/')) {
            return true;
        }

        $safeUrl = self::getSafeUrl($url);
        $decoded = urldecode($url);

        // getSafeUrl() URL'yi ilk satırda urldecode() edip decode edilmiş
        // hâlini döndürür. Karşılaştırmayı decode edilmiş taraflarda yap:
        // raw'da %2F bulunan izinli redirect'ler (login redirect_uri zinciri)
        // eskiden raw !== decode edilmiş olduğu için hep reddediliyordu.
        if ($safeUrl !== $decoded) {
            return false;
        }

        // Güvenlik: ham (raw) URL de decode edilmiş hâliyle aynı authority'e
        // sahip olmalı. %23 / %40 gibi encode edilmiş karakterler tarayıcıda
        // raw parse'da farklı host veya userinfo üretir; raw URL olduğu gibi
        // Location/JSON redirect'e yazıldığı için bu, open redirect ve
        // auth_key sızıntısına yol açar. Ham parse'da userinfo varsa veya
        // host'lar farklıysa → reddet (getSafeUrl yalnızca decode edilmiş
        // tarafı denetlediği için bu kontrol ayrıca gerekir).
        $rawParsed     = parse_url($url);
        $decodedParsed = parse_url($decoded);
        if ($rawParsed === false || $decodedParsed === false) {
            return false;
        }
        if (isset($rawParsed['user']) || isset($rawParsed['pass'])) {
            return false;
        }
        if (strtolower((string)($rawParsed['host'] ?? '')) !== strtolower((string)($decodedParsed['host'] ?? ''))) {
            return false;
        }

        return true;
    }

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
