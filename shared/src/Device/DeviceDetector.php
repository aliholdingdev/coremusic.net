<?php declare(strict_types=1);

namespace CoreMusic\Device;

/**
 * CoreMusic — Device Detector
 * User-Agent ve viewport'a göre cihaz tespiti
 * Breakpoint'ler: a-breakpoint-tokens.css ile senkronize
 *
 * Breakpoint Haritası:
 *   phone      ≤767px
 *   tablet     768-1024px
 *   embedded   ≤1024px (RPi5 — 1024x600)
 *   laptop     1025-1440px
 *   desktop    1441-2560px (varsayılan)
 *   4k-tv      2561-3840px
 *   4k-monitor ≥3841px
 */
final class DeviceDetector
{
    private const PHONE_MAX      = 767;
    private const TABLET_MIN     = 768;
    private const TABLET_MAX     = 1024;
    private const EMBEDDED_MAX   = 1024;
    private const LAPTOP_MAX     = 1440;
    private const DESKTOP_MAX    = 2560;
    private const FOUR_K_TV_MAX  = 3840;

    private const MOBILE_AGENTS = [
        'Android'     => 'phone',
        'iPhone'      => 'phone',
        'iPad'        => 'tablet',
        'iPod'        => 'phone',
        'Windows Phone' => 'phone',
        'BlackBerry'  => 'phone',
        'Opera Mini'  => 'phone',
        'Opera Mobi'  => 'phone',
    ];

    /**
     * User-Agent ve viewport boyutundan cihaz türü tespit et
     *
     * @param string|null $userAgent  HTTP User-Agent header
     * @param int|null    $viewportW  Viewport genişliği (px)
     * @param int|null    $viewportH  Viewport yüksekliği (px)
     * @return string     Device type: phone|tablet|embedded|laptop|desktop|4k-tv|4k-monitor
     */
    public static function detect(
        ?string $userAgent = null,
        ?int    $viewportW = null,
        ?int    $viewportH = null
    ): string {
        $ua = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? '';

        // 1. Mobile agent kontrolü (User-Agent string'i)
        $mobileDevice = self::detectFromUserAgent($ua);
        if ($mobileDevice !== null) {
            return $mobileDevice;
        }

        // 2. Viewport boyutu varsa ona göre tespit
        if ($viewportW !== null) {
            return self::detectFromViewport($viewportW, $viewportH);
        }

        // 3. Varsayılan: desktop
        return 'desktop';
    }

    /**
     * Sadece viewport boyutuna göre cihaz tespit et (JS tarafı için)
     */
    public static function detectFromViewport(int $width, ?int $height = null): string
    {
        if ($width <= self::PHONE_MAX) {
            return 'phone';
        }
        if ($width >= self::TABLET_MIN && $width <= self::TABLET_MAX) {
            // RPi5 1024x600 = embedded, diğer tablet'ler = tablet
            if ($height !== null && $height <= 600) {
                return 'embedded';
            }
            return 'tablet';
        }
        if ($width <= self::LAPTOP_MAX) {
            return 'laptop';
        }
        if ($width <= self::DESKTOP_MAX) {
            return 'desktop';
        }
        if ($width <= self::FOUR_K_TV_MAX) {
            return '4k-tv';
        }
        return '4k-monitor';
    }

    /**
     * User-Agent string'inden mobile cihaz tespit et
     */
    private static function detectFromUserAgent(string $ua): ?string
    {
        if ($ua === '') {
            return null;
        }

        foreach (self::MOBILE_AGENTS as $needle => $deviceType) {
            if (stripos($ua, $needle) !== false) {
                return $deviceType;
            }
        }

        // Android tablet kontrolü (tablet olmadan)
        if (stripos($ua, 'Android') !== false && stripos($ua, 'Mobile') === false) {
            return 'tablet';
        }

        // Touch cihaz kontrolü (genel)
        if (stripos($ua, 'Touch') !== false && stripos($ua, 'Windows') === false) {
            return 'phone';
        }

        return null;
    }

    /**
     * Cihaz türünün mobile olup olmadığını kontrol et
     */
    public static function isMobile(string $deviceType): bool
    {
        return in_array($deviceType, ['phone', 'tablet'], true);
    }

    /**
     * Cihaz türünün touch-first olup olmadığını kontrol et
     */
    public static function isTouchFirst(string $deviceType): bool
    {
        return in_array($deviceType, ['phone', 'tablet', 'embedded'], true);
    }

    /**
     * Cihaz türünün auth device CSS kullanıp kullanmayacağını belirle
     */
    public static function getAuthCssPath(string $deviceType): string
    {
        return DeviceCssMap::authToCssPath($deviceType);
    }

    /**
     * Cihaz türünün home device CSS kullanıp kullanmayacağını belirle
     */
    public static function getHomeCssPath(string $deviceType): string
    {
        return DeviceCssMap::toCssPath($deviceType);
    }
}
