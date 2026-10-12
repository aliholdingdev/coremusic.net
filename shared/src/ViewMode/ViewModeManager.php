<?php declare(strict_types=1);

namespace CoreMusic\ViewMode;

/**
 * Manages view mode detection and CSS path resolution.
 *
 * View modes determine the UI variant served to the client.
 * Detection priority: session value > route prefix > default (home).
 */
final class ViewModeManager
{
    private const VALID_MODES = ['home', 'pro', 'studio', 'car'];

    private const VIEW_CSS = [
        'home'   => '09_ViewModes/v-home.css',
        'pro'    => '09_ViewModes/v-pro.css',
        'studio' => '09_ViewModes/v-studio.css',
        'car'    => '09_ViewModes/v-car.css',
    ];

    /** Route prefix to mode mapping */
    private const ROUTE_MAP = [
        '/studio' => 'studio',
        '/pro'    => 'pro',
        '/car'    => 'car',
    ];

    /**
     * Detect view mode from session and route. Session takes priority.
     *
     * @param array<string, mixed> $session
     * @return string Valid view mode string
     */
    public static function detect(array $session, string $route = ''): string
    {
        // 1. Check session value
        if (isset($session['view_mode']) && is_string($session['view_mode'])) {
            $mode = strtolower($session['view_mode']);
            if (self::isValid($mode)) {
                return $mode;
            }
        }

        // 2. Check route prefix
        if ($route !== '') {
            $lower = strtolower($route);
            foreach (self::ROUTE_MAP as $prefix => $mode) {
                if (str_starts_with($lower, $prefix)) {
                    return $mode;
                }
            }
        }

        // 3. Default
        return 'home';
    }

    /**
     * Return CSS file path for the given view mode.
     */
    public static function getCssPath(string $mode): string
    {
        if (!self::isValid($mode)) {
            return self::VIEW_CSS['home'];
        }

        return self::VIEW_CSS[$mode];
    }

    /**
     * Check whether the given mode is a valid view mode.
     */
    public static function isValid(string $mode): bool
    {
        return in_array(strtolower($mode), self::VALID_MODES, true);
    }

    /**
     * Return the data-view attribute value for HTML output.
     */
    public static function injectDataAttribute(string $mode): string
    {
        if (!self::isValid($mode)) {
            return 'home';
        }

        return strtolower($mode);
    }
}
