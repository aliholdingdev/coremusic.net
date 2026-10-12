<?php declare(strict_types=1);

namespace CoreMusic\Device;

/**
 * CoreMusic — Device Renderer
 *
 * Hybrid rendering sözleşmesinin sunucu (server initial render) tarafı:
 *   1) Server: doğru device/view CSS link'leri (ID'li) + main[data-tier] + loader data-* attribute'ları
 *   2) Client: device-loader.js aynı sözleşme üzerinden hydrate eder ve düzeltir
 *
 * Link ID'leri client sözleşmesidir (assets.coremusic.net/js/device-loader.js):
 *   cm-auth-bundled | cm-device-css | cm-view-css
 *
 * CSS haritası SSOT: DeviceCssMap (devices.config.js ile senkron)
 *
 * Version: 1.0.0 — 2026-09-06
 */
final class DeviceRenderer
{
    /** Client sözleşmesi: auth temel paket link ID'si */
    public const LINK_ID_AUTH_BUNDLED = 'cm-auth-bundled';
    /** Client sözleşmesi: cihaz CSS link ID'si */
    public const LINK_ID_DEVICE = 'cm-device-css';
    /** Client sözleşmesi: view mode CSS link ID'si */
    public const LINK_ID_VIEW = 'cm-view-css';

    public function __construct(
        private readonly string $device,
        private readonly bool   $isAuth,
        private readonly string $viewMode,
        private readonly string $assetsUrl,
        private readonly string $cacheBuster,
        private readonly string $nonceAttr = '',
    ) {
        if (!DeviceManager::isValidDevice($device)) {
            throw new \InvalidArgumentException('Geçersiz cihaz türü: ' . $device);
        }
    }

    /**
     * HtmlShellRenderer'dan oluştur (shell kendi device tespitini yapar;
     * DeviceManager per-request singleton'ına DOKUNMAZ)
     */
    public static function fromShell(
        string $device,
        bool   $isAuth,
        string $viewMode,
        string $assetsUrl,
        string $cacheBuster,
        string $nonceAttr = '',
    ): self {
        return new self($device, $isAuth, $viewMode, $assetsUrl, $cacheBuster, $nonceAttr);
    }

    /**
     * device → DOM tier (device-loader.js getTier karşılığı): phone | embedded | wide
     *
     * Karar DeviceManager layout fonksiyonlarıyla birebirdir:
     *   phone → 'phone', shouldRenderEmbeddedLayout() → 'embedded', aksi halde 'wide'
     * (4K ayrı DOM tier DEĞİL — ≥2561px Wide markup + d-4k.css zoom; client da 4k↔wide'i eşdeğer sayar)
     */
    public static function tierOf(string $device): string
    {
        $dm = DeviceManager::fromDevice($device);
        if ($dm->isPhone()) {
            return 'phone';
        }
        if ($dm->shouldRenderEmbeddedLayout()) {
            return 'embedded';
        }
        return 'wide';
    }

    public function device(): string
    {
        return $this->device;
    }

    public function isAuth(): bool
    {
        return $this->isAuth;
    }

    public function viewMode(): string
    {
        return $this->viewMode;
    }

    public function tier(): string
    {
        return self::tierOf($this->device);
    }

    /** main elementine eklenecek attribute: ' data-tier="wide"' */
    public function tierAttribute(): string
    {
        return ' data-tier="' . $this->e($this->tier()) . '"';
    }

    /** Aktif cihaz için device CSS yolu (DeviceCssMap SSOT) */
    public function deviceCssPath(): string
    {
        return $this->isAuth
            ? DeviceCssMap::authToCssPath($this->device)
            : DeviceCssMap::toCssPath($this->device);
    }

    /** Aktif view mode için CSS yolu */
    public function viewCssPath(): string
    {
        return DeviceCssMap::viewModeToCssPath($this->viewMode);
    }

    /**
     * Head CSS link'leri (ID'li — client hydrate sözleşmesi):
     *   auth → cm-auth-bundled (temel) + cm-device-css (override)
     *   home → cm-device-css + cm-view-css + sidebar (ID'siz — client dokunmaz)
     */
    public function headLinks(): string
    {
        if ($this->isAuth) {
            return $this->link('auth-bundled.css', self::LINK_ID_AUTH_BUNDLED)
                 . $this->link($this->deviceCssPath(), self::LINK_ID_DEVICE);
        }

        return $this->link($this->deviceCssPath(), self::LINK_ID_DEVICE)
             . $this->link($this->viewCssPath(), self::LINK_ID_VIEW)
             . $this->link('03_Layout/_sidebar.css', '');
    }

    /**
     * device-loader.js script tag'i data attribute'ları (sözleşme: data-cm-device-loader)
     * data-server-device → client ilk tespit hizalaması (hybrid tahmin düzeltmesi)
     */
    public function loaderAttributes(string $colorMode = ''): string
    {
        return ' data-cm-device-loader'
             . ' data-assets-url="' . $this->e($this->assetsUrl) . '"'
             . ' data-is-auth="' . ($this->isAuth ? 'true' : 'false') . '"'
             . ' data-view-mode="' . $this->e($this->viewMode) . '"'
             . ' data-server-device="' . $this->e($this->device) . '"'
             . ' data-color-mode="' . $this->e($colorMode) . '"';
    }

    /**
     * Tek stylesheet link üret (cache-buster + nonce + CORS)
     */
    private function link(string $path, string $id): string
    {
        $url = $this->assetsUrl . '/Css/' . $path . '?v=' . $this->cacheBuster;
        $idAttr = $id !== '' ? ' id="' . $this->e($id) . '"' : '';

        return '<link rel="stylesheet"' . $idAttr . ' href="' . $this->e($url) . '"'
             . $this->nonceAttr . ' crossorigin="anonymous">';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
