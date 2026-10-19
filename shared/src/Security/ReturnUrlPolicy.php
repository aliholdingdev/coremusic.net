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

        // Ham kontrol karakteri (\t \n \r) hiçbir meşru redirect URL'inde
        // bulunmaz. Hem HAM hem tek katman urldecode edilmiş girdide bu
        // karakterlerden biri varsa → reddet. WHATWG parser bu karakterleri
        // girdiden attığı için '%0Aevil.com' gibi slash'sız varyantlar
        // decode + strip sonrası protokol-relative'e dönüşebilir; bu guard
        // isProtocolRelative kontrolünden ÖNCE erken ret yapar.
        // '' ve '/' early-return'lerle çakışmaz (bu değerler zaten bu
        // karakterleri içermez); '/dashboard' gibi normal path'ler etkilenmez.
        if (self::hasControlChars($url)) {
            return false;
        }

        if (str_starts_with($url, '/')) {
            // Tek '/' ile başlayan path'ler aynı-origin'dir ve güvenlidir.
            // '//' (protokol-relative) ve '/\' (tarayıcı backslash→slash
            // normalizasyonu) dış host'a redirect üretir → açık redirect.
            // Ham VE tek katman decode edilmiş girdiyi helper'ın İÇİNDE dene:
            // '/%2Fevil.com' ham tarafta masum görünür, decode sonrası
            // protokol-relative olur. Decode bilerek helper'da tek katman
            // yapılır (çift encode '%252F' açılmaz).
            if (self::isProtocolRelative($url)) {
                return false;
            }

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

    /**
     * Protokol-relative başlangıç kontrolü: '//' ve '/\'.
     *
     * WHATWG URL parser girdiden ASCII tab/newline'ı (\t \n \r) ATAR:
     * '/%09/evil.com' tarayıcıda '//evil.com' olur → gerçek açık redirect.
     * Bu yüzden girdi önce HAM, sonra TEK KATMAN rawurldecode edilip
     * temizlenir, sonra başlangıç kontrolü yapılır. Tek katman bilinçli:
     * '%252F' tek decode'da '%2F' kalır, protokol-relative sayılmaz.
     * Temizlik YALNIZCA bu kontrolün önünde; isAllowed'ın raw parse /
     * host eşitliği mantığına dokunmaz.
     */
    private static function isProtocolRelative(string $url): bool
    {
        foreach ([$url, rawurldecode($url)] as $candidate) {
            $clean = str_replace(["\t", "\n", "\r"], '', $candidate);
            if (str_starts_with($clean, '//') || str_starts_with($clean, '/\\')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Girdide ham kontrol karakteri (\t \n \r) var mı?
     *
     * Raw VE tek katman urldecode edilmiş hâl taranır: '%0A' ham tarafta
     * masum görünür ama decode sonrası literal newline olur; ham'daki
     * literal \n ise urldecode'dan geçip aynen kalır.
     */
    private static function hasControlChars(string $url): bool
    {
        return strpbrk($url, "\t\n\r") !== false
            || strpbrk(urldecode($url), "\t\n\r") !== false;
    }

    public static function getSafeUrl(?string $url, string $scheme = 'https'): string
    {
        if (empty($url)) {
            return '/';
        }

        $url = urldecode($url);

        // İkinci savunma hattı isAllowed() ile tutarlı: hiçbir meşru
        // redirect URL'inde ham kontrol karakteri (\t \n \r) bulunmaz →
        // mevcut reddetme davranışı gibi '/' döndür. Raw'daki kontrol
        // karakteri urldecode'tan geçip literal kaldığı ve '%0A' gibi
        // encode edilenler decode sonrası literal hâle geldiği için tek
        // kontrol decode sonrası yeterlidir.
        if (strpbrk($url, "\t\n\r") !== false) {
            return '/';
        }

        if (str_starts_with($url, '/')) {
            // Protokol-relative ('//host', '/\host' ya da tab/newline
            // temizliği sonrası '//host') dış host'a redirect üretir →
            // ikinci savunma hattı da diğeriyle tutarlı: reddedilen diğer
            // güvensiz URL'ler gibi '/' döndür.
            if (self::isProtocolRelative($url)) {
                return '/';
            }

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
