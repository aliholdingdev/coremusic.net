<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Device\DeviceCssMap;
use CoreMusic\Device\DeviceDetector;

final class HtmlShellRenderer
{
    private readonly string $headerPath;
    private readonly string $footerPath;

    public function __construct(
        private readonly ConfigManager $config,
        private readonly DomainConfig $domainConfig,
        ?string $headerPath = null,
        ?string $footerPath = null
    ) {
        $this->headerPath = $headerPath ?? (defined('HEADER_PATH') ? (string)HEADER_PATH : '');
        $this->footerPath = $footerPath ?? (defined('FOOTER_PATH') ? (string)FOOTER_PATH : '');
    }

    public function render(
        string $container,
        string $route,
        array  $meta,
        string $csrfToken,
        array  $protectedRoutes = [],
        array  $sessionData = []
    ): string {
        $assetsUrl    = $this->domainConfig->getUrl('assets');
        $appName      = (string)$this->config->get('app.name', 'CoreMusic');
        $appVersion   = (string)$this->config->get('app.version', '1.0.0');
        $isDebug      = (bool)$this->config->get('app.debug', false);

        $deviceType   = DeviceDetector::detect(
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            null,
            null
        );
        // Session'dan gelen device_type varsa ve geçerliyse onu kullan
        $sessionDevice = $sessionData['device_type'] ?? null;
        if ($sessionDevice !== null && in_array($sessionDevice, ['phone','tablet','embedded','laptop','desktop','4k-tv','4k-monitor'], true)) {
            $deviceType = $sessionDevice;
        }
        $cspNonce     = $sessionData['csp_nonce'] ?? '';
        $gender       = $sessionData['cm_gender'] ?? $sessionData['gender'] ?? 'neutral';

        $viewMode = DeviceCssMap::sanitizeViewMode($sessionData['view_mode'] ?? null);

        $mainJsFile  = dirname(__DIR__, 3) . '/assets.coremusic.net/js/main.js';
        $mainJsTime  = is_file($mainJsFile) ? (string)filemtime($mainJsFile) : '';
        $cacheBuster = $mainJsTime !== '' ? $mainJsTime : $appVersion;

        $h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        $isAuthRoute = AuthRouteConfig::isAuthRoute($route);

        $assetsEsc    = $h($assetsUrl);
        $pageTitle    = $h((string)($meta['title'] ?? $appName));
        $appNameEsc   = $h($appName);
        $cspNonceH    = $h($cspNonce);
        $nonceAttr    = $cspNonceH !== '' ? ' nonce="' . $cspNonceH . '"' : '';
        $csrfEsc      = $h($csrfToken);
        $deviceCssPath = DeviceCssMap::toCssPath($deviceType);
        $authDeviceCssPath = DeviceCssMap::authToCssPath($deviceType);
        $viewCssPath   = DeviceCssMap::viewModeToCssPath($viewMode);

        if ($isAuthRoute) {
            // Auth: auth-bundled (base styles) THEN device CSS (overrides)
            // crossorigin="anonymous" — fonts loaded from assets.coremusic.net need CORS
            $css = '<link rel="stylesheet" href="' . $assetsEsc . '/Css/auth-bundled.css?v=' . $cacheBuster . '"' . $nonceAttr . ' crossorigin="anonymous">';
            $css .= '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $authDeviceCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . ' crossorigin="anonymous">';
        } else {
            // Home: self-contained device CSS + view mode
            $css = '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $deviceCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . ' crossorigin="anonymous">';
            $css .= '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $viewCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . ' crossorigin="anonymous">';
        }

        $nonceMeta = $cspNonceH !== '' ? '<meta name="csp-nonce" content="' . $cspNonceH . '">' : '';

        ob_start();

        echo '<!doctype html><html lang="tr" data-gender="' . $h($gender) . '"><head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">';
        echo '<title>' . $pageTitle . ' — ' . $appNameEsc . '</title>';
        echo '<link rel="icon" type="image/x-icon" href="favicon.ico">';
        echo '<link rel="preconnect" href="' . $assetsEsc . '" crossorigin="anonymous">';
        echo $css;
        echo $nonceMeta;
        echo '</head>';

        $bodyClass = $isAuthRoute
            ? ' class="auth-page" data-device="' . $h($deviceType) . '" data-view="' . $h($viewMode) . '"'
            : ' data-device="' . $h($deviceType) . '" data-view="' . $h($viewMode) . '"';

        echo '<body' . $bodyClass . '>';
        echo '<input type="hidden" name="csrf_token" id="csrf-global" value="' . $csrfEsc . '">';

        if ($isAuthRoute) {
            echo '<main id="main-content">' . $container . '</main>';
        } else {
            echo '<main class="l-main-wrapper" id="main-content" aria-busy="false">' . $container . '</main>';
        }

        // Inline script — window.CoreMusic.RouterConfig
        $jsDomain     = json_encode(['host' => $this->domainConfig->getHost(), 'port' => $this->domainConfig->getPort(), 'scheme' => $this->domainConfig->getScheme(), 'isHttps' => $this->domainConfig->isHttps()], JSON_UNESCAPED_UNICODE);
        $jsAssetsUrl  = json_encode($assetsUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $jsAppName    = json_encode($appName, JSON_UNESCAPED_UNICODE);
        $jsRoute      = json_encode($route, JSON_UNESCAPED_SLASHES);
        $jsProtected  = json_encode(array_values($protectedRoutes), JSON_UNESCAPED_SLASHES);

        echo '<script' . $nonceAttr . '>';
        echo 'window.CoreMusic = window.CoreMusic || {};';
        echo 'window.CoreMusic.RouterConfig = {';
        echo 'enabled: true,';
        echo 'assetsUrl: ' . $jsAssetsUrl . ',';
        echo 'appName: ' . $jsAppName . ',';
        echo 'domain: ' . $jsDomain . ',';
        echo 'initialRoute: ' . $jsRoute . ',';
        echo 'protectedRoutes: ' . $jsProtected . ',';
        echo 'logLevel: ' . json_encode($isDebug ? 'debug' : 'info') . ',';
        echo 'cssVersion: ' . json_encode($cacheBuster) . ',';
        echo 'user: null';
        echo '};';
        echo '</script>';

        echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/main.js?v=' . $cacheBuster . '" type="module" defer></script>';

        // Device Loader — client-side cihaz tespiti ve CSS yeniden yükleme
        echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/device-loader.js?v=' . $cacheBuster . '"'
            . ' data-cm-device-loader'
            . ' data-assets-url="' . $assetsEsc . '"'
            . ' data-is-auth="' . ($isAuthRoute ? 'true' : 'false') . '"'
            . ' data-view-mode="' . $h($viewMode) . '"'
            . ' data-server-device="' . $h($deviceType) . '"'
            . ' defer></script>';

        // Auth-specific JS (theme engine + gender background + page scripts)
        if ($isAuthRoute) {
            echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/Js/auth/auth-theme.js?v=' . $cacheBuster . '" defer></script>';
            echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/Js/auth/auth-gender-bg.js?v=' . $cacheBuster . '"'
                . ' data-cm-gender-bg'
                . ' data-assets-url="' . $assetsEsc . '"'
                . ' defer></script>';
            $authJsMap = [
                'select-gender' => 'gender-select.js',
                'login'         => 'login.js',
                'register'      => 'register.js',
            ];
            $authPageName = ltrim($route, '/');
            if (isset($authJsMap[$authPageName])) {
                echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/Js/auth/' . $authJsMap[$authPageName] . '?v=' . $cacheBuster . '" defer></script>';
            }
        }

        echo '<noscript><p>Bu uygulama JavaScript gerektirmektedir.</p></noscript>';
        echo '</body></html>';

        return (string)ob_get_clean();
    }
}
