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

    /**
     * Shell header dosyası yolu.
     */
    public function getHeaderPath(): string
    {
        return $this->headerPath;
    }

    /**
     * Shell footer dosyası yolu.
     */
    public function getFooterPath(): string
    {
        return $this->footerPath;
    }

    /**
     * Chrome (header/footer) parçasını include eder — C-F-13.
     *
     * Yol boşsa veya dosya yoksa '' döner (auth host HEADER_PATH tanımlamaz →
     * davranış değişmez). Partial'lar kendi bağımlılıklarını ($dm, $h,
     * $assetsUrl, $nonce) kendileri tanımlar; dışarıdan değişken GEREKMEZ.
     * Hata halinde buffer temizlenip exception yukarı taşınır.
     */
    private function renderChrome(string $path): string
    {
        if ($path === '' || !is_file($path)) {
            return '';
        }

        ob_start();
        try {
            include $path;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
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
        // CSS mtime: yalnız JS'e göre buster verilirse CSS düzenlemeleri tarayıcı
        // cache'inde kalıyor (ör. d-desktop.css @import zinciri eksik servis edildi).
        static $cssMtime = null;
        if ($cssMtime === null) {
            $cssMtime = '';
            $cssDir   = $assetsDir . 'Css';
            if (is_dir($cssDir)) {
                $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($cssDir, \FilesystemIterator::SKIP_DOTS));
                foreach ($it as $f) {
                    if ($f->isFile() && $f->getExtension() === 'css') {
                        $mt = (string)$f->getMTime();
                        if ($mt > $cssMtime) {
                            $cssMtime = $mt;
                        }
                    }
                }
            }
        }
        if ($cssMtime > $mainJsTime) {
            $mainJsTime = $cssMtime;
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
        /* Kalıcı medya elementi — SPA navigasyonunda shell sabit kalır,
           footer.init.js (getAudio) + PlaybackRepository #audio'yu bekler. */
        echo '<audio id="audio" class="vdisplay" preload="metadata"></audio>';
        echo '<input type="hidden" name="csrf_token" id="csrf-global" value="' . $csrfEsc . '">';

        // C-F-13: header/footer chrome ARTIK container DIŞINDA (shell seviyesi).
        // Önceki yapıda container içindeydiler → SPA patch replaceChildren +
        // DomPatcher script-strip ile footer klasik script'leri hiç yeniden
        // çalışmaz, player kontrolleri tam sayfa yenilemesine kadar ölü kalırdı;
        // iç içe <main> de (WCAG çift landmark) ortadan kalktı. header/footer.php
        // kendi bağımlılıklarını ($dm/$h/$assetsUrl/$nonce) kendileri tanımlar.
        echo $this->renderChrome($this->headerPath);

        if ($isAuthRoute) {
            echo '<main id="main-content"' . $deviceRenderer->tierAttribute() . '>' . $container . '</main>';
        } else {
            echo '<main class="l-main-wrapper" id="main-content" aria-busy="false"' . $deviceRenderer->tierAttribute() . '>' . $container . '</main>';
        }

        echo $this->renderChrome($this->footerPath);

        // Inline script — window.CoreMusic.RouterConfig
        // XSS: inline <script> içine gömülü JSON, `</script>` / `<!--` ile kapanışı kırabilir;
        // HEX_* flag'leri `<`, `>`, `&`, `'`, `"` karakterlerini \uXXXX'e çevirir (BUKİ).
        $hexFlags     = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        $jsDomain     = json_encode(['host' => $this->domainConfig->getHost(), 'port' => $this->domainConfig->getPort(), 'scheme' => $this->domainConfig->getScheme(), 'isHttps' => $this->domainConfig->isHttps()], $hexFlags | JSON_UNESCAPED_UNICODE);
        $jsAssetsUrl  = json_encode($assetsUrl, $hexFlags | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $jsAppName    = json_encode($appName, $hexFlags | JSON_UNESCAPED_UNICODE);
        $jsRoute      = json_encode($route, $hexFlags | JSON_UNESCAPED_SLASHES);
        // C-F-08 (1/2): Route anahtarları slash'siz gelir ('playlist'), client
        // normalizeUrl pathname'i ('/playlist') ile karşılaştırır → includes() hiç
        // eşleşmiyordu. Çıkışta leading-slash'e normalize edilir.
        $jsProtected  = json_encode(
            array_values(array_map(
                static fn ($key): string => '/' . ltrim((string) $key, '/'),
                $protectedRoutes
            )),
            $hexFlags | JSON_UNESCAPED_SLASHES
        );
        // C-F-08 (2/2): Guard zinciri gerçek kullanıcıyı okur (authGuard: user.id).
        // sessionData'dan üretilir; HttpOnly session cookie'nin İÇİNDEKİ hiçbir
        // sır (session id, token) bu payload'a GİRMEZ — yalnız kimlik görünümü.
        $userIdentity = null;
        if (is_string($sessionData['MM_UserID'] ?? null) && $sessionData['MM_UserID'] !== '') {
            $userIdentity = [
                'id'          => (string) $sessionData['MM_UserID'],
                'username'    => (string) ($sessionData['MM_Username'] ?? ''),
                'role'        => (string) ($sessionData['MM_UserRole'] ?? 'user'),
                'permissions' => is_array($sessionData['MM_Permissions'] ?? null)
                    ? array_values(array_filter($sessionData['MM_Permissions'], 'is_string'))
                    : [],
            ];
        }
        $jsUser = json_encode($userIdentity, $hexFlags | JSON_UNESCAPED_UNICODE);

        echo '<script' . $nonceAttr . '>';
        echo 'window.CoreMusic = window.CoreMusic || {};';
        echo 'window.CoreMusic.RouterConfig = {';
        echo 'enabled: true,';
        echo 'assetsUrl: ' . $jsAssetsUrl . ',';
        echo 'appName: ' . $jsAppName . ',';
        echo 'domain: ' . $jsDomain . ',';
        echo 'initialRoute: ' . $jsRoute . ',';
        echo 'protectedRoutes: ' . $jsProtected . ',';
        echo 'user: ' . $jsUser . ',';
        echo 'logLevel: ' . json_encode($isDebug ? 'debug' : 'info', $hexFlags) . ',';
        echo 'cssVersion: ' . json_encode($cacheBuster, $hexFlags) . ',';
        // DİKKAT: eski `echo 'user: null'` buradaydı — JS object literal'de
        // duplicate key'de SONUNCU kazanıyordu → payload her zaman null'a
        // dönüyordu (C-F-08 kullanıcı enjeksiyonu bu yüzden etkisizdi).
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

        // Auth-specific JS (gender background + page scripts)
        // Disk kanıtı (assets.coremusic.net/js/auth/): YALNIZ auth-gender-bg.js + gender-select.js VAR.
        // auth-theme.js, login.js, register.js YOK → 404 üreten script etiketleri kaldırıldı (ADR-042:
        // dosya yolu değil, render edilen referans silindi). Dizin adı lowercase `js` (büyük J yok).
        if ($isAuthRoute) {
            echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/auth/auth-gender-bg.js?v=' . $cacheBuster . '"'
                . ' data-cm-gender-bg'
                . ' data-assets-url="' . $assetsEsc . '"'
                . ' defer></script>';
            $authJsMap = [
                'select-gender' => 'gender-select.js',
            ];
            $authPageName = ltrim($route, '/');
            if (isset($authJsMap[$authPageName])) {
                echo '<script' . $nonceAttr . ' src="' . $assetsEsc . '/js/auth/' . $authJsMap[$authPageName] . '?v=' . $cacheBuster . '" defer></script>';
            }
        }

        echo '<noscript><p>Bu uygulama JavaScript gerektirmektedir.</p></noscript>';
        echo '</body></html>';

        return (string)ob_get_clean();
    }
}
