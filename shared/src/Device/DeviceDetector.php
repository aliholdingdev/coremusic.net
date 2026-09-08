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

    private const EMBEDDED_AGENTS = [
        'Raspberry Pi', 'RPi', 'aarch64', 'armv7', 'armv8', 'armv6', 'CrOS', 'kiosk', 'Raspbian', 'Embedded',
    ];

    private const TV_AGENTS = [
        'Tizen', 'Web0S', 'webOS', 'SmartTV', 'BRAVIA',
        'NetCast', 'AppleTV', 'Android TV', 'GoogleTV', 'HbbTV',
        'Roku', 'Hisense', 'Viera', 'MiTV',
    ];

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
        // 0. Özel HTTP başlığı veya kiosk bayrağı kontrolü
        if (!empty($_SERVER['HTTP_X_DEVICE_TYPE']) && strtolower((string)$_SERVER['HTTP_X_DEVICE_TYPE']) === 'embedded') {
            return 'embedded';
        }

        $ua = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? '';

        // 1. Smart TV agent kontrolü
        if (self::isTvAgent($ua)) {
            return '4k-tv';
        }

        // 2. Embedded device (RPi5, ARM Linux) kontrolü
        if (self::isEmbeddedDevice($ua)) {
            return 'embedded';
        }

        // 3. Viewport boyutu varsa ona ve User-Agent bağlamına göre tespit
        if ($viewportW !== null) {
            return self::detectFromViewportAndContext($viewportW, $viewportH, $ua);
        }

        // 3. Mobile agent kontrolü (User-Agent string'i)
        $mobileDevice = self::detectFromUserAgent($ua);
        if ($mobileDevice !== null) {
            return $mobileDevice;
        }

        // 4. Varsayılan: desktop
        return 'desktop';
    }

    /**
     * Viewport boyutu ve User-Agent bağlamından cihaz tespit et
     */
    public static function detectFromViewportAndContext(int $width, ?int $height = null, string $ua = ''): string
    {
        // Embedded device tespit edildiyse viewport'a bakmadan embedded dön
        if (self::isEmbeddedDevice($ua)) {
            return 'embedded';
        }

        if ($width <= self::PHONE_MAX) {
            return 'phone';
        }

        if ($width >= self::TABLET_MIN && $width <= self::TABLET_MAX) {
            // RPi5 7" 1024x600 = embedded
            if ($height !== null && $height <= 600) {
                return 'embedded';
            }
            // 1024x768 gibi laptop ekranları: Masaüstü işletim sistemi varsa laptop kabul edilir
            if (self::isDesktopOS($ua)) {
                return 'laptop';
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
            // 3840px ekranlarda: TV User Agent varsa veya TV bayrağı varsa 4k-tv
            if (self::isTvAgent($ua)) {
                return '4k-tv';
            }
            // Masaüstü işletim sistemi + fare/klavye tespit edilmişse 4k-monitor
            if (self::isDesktopOS($ua)) {
                return '4k-monitor';
            }
            return '4k-tv';
        }

        return '4k-monitor';
    }

    /**
     * Sadece viewport boyutuna göre cihaz tespit et (JS / fallback tarafı için)
     */
    public static function detectFromViewport(int $width, ?int $height = null): string
    {
        if ($width <= self::PHONE_MAX) {
            return 'phone';
        }
        if ($width >= self::TABLET_MIN && $width <= self::TABLET_MAX) {
            // RPi5 1024x600 = embedded, 1024x768 = laptop, diğer = tablet
            if ($height !== null && $height <= 600) {
                return 'embedded';
            }
            if ($height !== null && $height >= 768) {
                return 'laptop';
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
     * User-Agent bir Smart TV'ye mi ait?
     */
    public static function isTvAgent(string $ua): bool
    {
        if ($ua === '') {
            return false;
        }

        foreach (self::TV_AGENTS as $tvNeedle) {
            if (stripos($ua, $tvNeedle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * User-Agent embedded bir cihaza (RPi5, ARM Linux) mı ait?
     */
    public static function isEmbeddedDevice(string $ua): bool
    {
        if ($ua === '') {
            return false;
        }

        foreach (self::EMBEDDED_AGENTS as $needle) {
            if (stripos($ua, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * User-Agent masaüstü işletim sistemine mi ait?
     */
    public static function isDesktopOS(string $ua): bool
    {
        if ($ua === '') {
            return false;
        }

        // Mobile veya TV agent ise masaüstü değil
        if (self::isTvAgent($ua)) {
            return false;
        }

        // Embedded device (RPi5) ise masaüstü değil
        if (self::isEmbeddedDevice($ua)) {
            return false;
        }

        if (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) {
            return false;
        }

        return stripos($ua, 'Windows NT') !== false
            || stripos($ua, 'Macintosh') !== false
            || (stripos($ua, 'Linux') !== false && stripos($ua, 'X11') !== false);
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
     * Cihazın tam 1024px gömülü (Raspberry Pi 5 / 7" Touch) olup olmadığını kontrol et
     */
    public static function isEmbedded1024(string $deviceType, ?int $viewportW = null, ?int $viewportH = null): bool
    {
        if ($deviceType !== 'embedded') {
            return false;
        }

        if ($viewportW !== null && $viewportW > 1024) {
            return false;
        }

        if ($viewportH !== null && $viewportH > 600) {
            return false;
        }

        return true;
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
