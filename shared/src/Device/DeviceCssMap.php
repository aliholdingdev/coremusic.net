<?php declare(strict_types=1);

namespace CoreMusic\Device;

final class DeviceCssMap
{
    private const DEVICE_CSS = [
        'embedded'   => '08_Devices/d-embedded.css',
        'phone'      => '08_Devices/d-phone.css',
        'tablet'     => '08_Devices/d-tablet.css',
        'laptop'     => '08_Devices/d-laptop.css',
        'desktop'    => '08_Devices/d-desktop.css',
        '4k-tv'      => '08_Devices/d-4k-tv.css',
        '4k-monitor' => '08_Devices/d-4k-monitor.css',
    ];

    private const AUTH_DEVICE_CSS = [
        'embedded'   => '08_Devices/d-auth-embedded.css',
        'phone'      => '08_Devices/d-auth-phone.css',
        'tablet'     => '08_Devices/d-auth-tablet.css',
        'laptop'     => '08_Devices/d-auth-laptop.css',
        'desktop'    => '08_Devices/d-auth-desktop.css',
        '4k-tv'      => '08_Devices/d-auth-4k-tv.css',
        '4k-monitor' => '08_Devices/d-auth-4k-monitor.css',
    ];

    private const VIEW_MODE_CSS = [
        'home'    => '09_ViewModes/v-home.css',
        'pro'     => '09_ViewModes/v-pro.css',
        'studio'  => '09_ViewModes/v-studio.css',
        'car'     => '09_ViewModes/v-car.css',
    ];

    public static function toCssPath(string $deviceType): string
    {
        return self::DEVICE_CSS[$deviceType] ?? self::DEVICE_CSS['desktop'];
    }

    public static function authToCssPath(string $deviceType): string
    {
        return self::AUTH_DEVICE_CSS[$deviceType] ?? self::AUTH_DEVICE_CSS['desktop'];
    }

    public static function viewModeToCssPath(string $viewMode): string
    {
        return self::VIEW_MODE_CSS[$viewMode] ?? self::VIEW_MODE_CSS['home'];
    }

    public static function sanitizeViewMode(?string $mode): string
    {
        if ($mode === null || $mode === '') {
            return 'home';
        }
        return in_array($mode, array_keys(self::VIEW_MODE_CSS), true) ? $mode : 'home';
    }
}
