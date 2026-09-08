<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\AuthRouteConfig;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Device\DeviceDetector;
use CoreMusic\Device\DeviceRenderer;
use CoreMusic\Theme\ThemeManager;
use CoreMusic\ViewMode\ViewModeManager;

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

        // Viewport bilgisi: cookie (cm_viewport_w/h) → HTTP header → $_SERVER
        $viewportW = !empty($_SERVER['VIEWPORT_W'])
            ? (int)$_SERVER['VIEWPORT_W']
            : (!empty($_COOKIE['cm_viewport_w']) ? (int)$_COOKIE['cm_viewport_w'] : null);
        $viewportH = !empty($_SERVER['VIEWPORT_H'])
            ? (int)$_SERVER['VIEWPORT_H']
            : (!empty($_COOKIE['cm_viewport_h']) ? (int)$_COOKIE['cm_viewport_h'] : null);
        $deviceType   = DeviceDetector::detect(
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $viewportW,
            $viewportH
        );
        // Session'dan gelen device_type varsa ve geçerliyse onu kullan
        $sessionDevice = $sessionData['device_type'] ?? null;
        if ($sessionDevice !== null && in_array($sessionDevice, ['phone','tablet','embedded','laptop','desktop','4k-tv','4k-monitor'], true)) {
            $deviceType = $sessionDevice;
        }
        $cspNonce     = $sessionData['csp_nonce'] ?? '';
        $gender       = ThemeManager::detect($sessionData);
        $colorMode    = ThemeManager::detectMode($sessionData);

        $viewMode = ViewModeManager::detect($sessionData, $route);

        // Cache buster: kritik JS zincirinin EN YENİ mtime'ı — tek dosyanın mtime'ı
        // kullanılırsa diğer dosyalar değiştiğinde buster değişmez → eski JS cache'te kalır.
        $assetsDir   = dirname(__DIR__, 3) . '/assets.coremusic.net/';
        $busterFiles = ['js/main.js', 'js/devices.config.js', 'js/device-loader.js', 'js/device-layout-updater.js'];
        $mainJsTime  = '';
        foreach ($busterFiles as $bf) {
            $bp = $assetsDir . $bf;
            if (is_file($bp)) {
                $mt = (string)filemtime($bp);
                if ($mt > $mainJsTime) {
                    $mainJsTime = $mt;
                }
            }
        }
        $cacheBuster = $mainJsTime !== '' ? $mainJsTime : $appVersion;

        $h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        $isAuthRoute = AuthRouteConfig::isAuthRoute($route);

        $assetsEsc    = $h($assetsUrl);

        // Device Renderer — hybrid rendering sözleşmesinin sunucu tarafı (SSOT)
        //   1) ID'li CSS link'leri (client hydrate sözleşmesi: cm-*)
        //   2) main[data-tier] hizalaması
        //   3) device-loader.js data-* attribute'ları
        // NOT: $nonceAttr fromShell'den ÖNCE hesaplanmalıydı — bypass auth ile
        // bu akışa ilk kez ulaşıldığında NULL TypeError'a düşüyordu (2026-09-06).
        $cspNonceH    = $h($cspNonce);
        $nonceAttr    = $cspNonceH !== '' ? ' nonce="' . $cspNonceH . '"' : '';

        $deviceRenderer = DeviceRenderer::fromShell(
            device:     $deviceType,
            isAuth:     $isAuthRoute,
            viewMode:   $viewMode,
            assetsUrl:  $assetsUrl,
            cacheBuster: $cacheBuster,
            nonceAttr:  $nonceAttr,
        );

        $css = $deviceRenderer->headLinks();
        $pageTitle    = $h((string)($meta['title'] ?? $appName));
        $appNameEsc   = $h($appName);
        $csrfEsc      = $h($csrfToken);
        $nonceMeta = $cspNonceH !== '' ? '<meta name="csp-nonce" content="' . $cspNonceH . '">' : '';

        ob_start();

        echo '<!doctype html><html lang="tr" ' . ThemeManager::injectAttributes($gender, $colorMode) . '><head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">';
        echo '<title>' . $pageTitle . ' — ' . $appNameEsc . '</title>';
        echo '<link rel="icon" type="image/png" href="' . $assetsEsc . '/Image/res-pink/music.png">';
        echo '<link rel="preconnect" href="' . $assetsEsc . '" crossorigin="anonymous">';
        echo $css;
        // Body background image (dynamic assets URL via CSS custom property)
        echo '<style' . $nonceAttr . '>:root{--body-bg-image:url(\'' . $assetsEsc . '/Image/background/bkimage1.png\');--body-bg-attachment:fixed;--body-bg-size:cover;--body-bg-position:center;--body-bg-repeat:no-repeat}</style>';
        echo $nonceMeta;
        echo '</head>';

        $bodyClass = $isAuthRoute
            ? ' class="auth-page" data-device="' . $h($deviceType) . '" data-view="' . $h($viewMode) . '"'
            : ' data-device="' . $h($deviceType) . '" data-view="' . $h($viewMode) . '"';

        echo '<body' . $bodyClass . '>';
        echo '<input type="hidden" name="csrf_token" id="csrf-global" value="' . $csrfEsc . '">';

        if ($isAuthRoute) {
            echo '<main id="main-content"' . $deviceRenderer->tierAttribute() . '>' . $container . '</main>';
        } else {
            echo '<main class="l-main-wrapper" id="main-content" aria-busy="false"' . $deviceRenderer->tierAttribute() . '>' . $container . '</main>';
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

        // Device Config — CSS haritası tek kaynağı (device-loader.js'den önce yüklenmeli)
        echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/devices.config.js?v=' . $cacheBuster . '" defer></script>';

        // Device Loader — client-side cihaz tespiti ve CSS yeniden yükleme (hydrate)
        echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/device-loader.js?v=' . $cacheBuster . '"'
            . $deviceRenderer->loaderAttributes($colorMode ?? '')
            . ' defer></script>';

        // Device Layout Updater — cihaz değişikliğinde HTML yapısını güncelle
        if (!$isAuthRoute) {
            echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/device-layout-updater.js?v=' . $cacheBuster . '" defer></script>';
        }

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
