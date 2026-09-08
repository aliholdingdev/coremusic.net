<?php declare(strict_types=1);

namespace CoreMusic\Device;

/**
 * CoreMusic — Device Manager
 *
 * Merkezi cihaz yönetim sınıfı. Tüm cihaz bazlı kararları tek yerden yönetir.
 * PHP template'lerinde if/else ile HTML sınırlamak için kullanılır.
 *
 * Kullanım:
 *   $dm = DeviceManager::fromRequest();
 *   if ($dm->isEmbedded()) { ... }
 *   if ($dm->isDesktop()) { ... }
 *   echo $dm->layoutClass();  // "layout--embedded"
 *   $dm->widgetCount();       // 4
 *   $dm->recentCardCount();   // 3
 *
 * Version: 2.0.0 — 2026-09-05
 * Changelog:
 *   v2.0.0 — Per-request singleton factory (instance()), PSR-12 strict compliance
 *   v1.0.0 — Initial release
 */
final class DeviceManager
{
    /* ============================================================
       DEVICE TYPE CONSTANTS
       ============================================================ */
    public const EMBEDDED   = 'embedded';
    public const PHONE      = 'phone';
    public const TABLET     = 'tablet';
    public const LAPTOP     = 'laptop';
    public const DESKTOP    = 'desktop';
    public const FOUR_K_TV  = '4k-tv';
    public const FOUR_K_MON = '4k-monitor';

    private const ALL_DEVICES = [
        self::EMBEDDED, self::PHONE, self::TABLET,
        self::LAPTOP, self::DESKTOP, self::FOUR_K_TV, self::FOUR_K_MON,
    ];

    /* ============================================================
       NAV LINK MAP — hangi cihazda hangi nav link'ler gösterilir
       ============================================================ */
    private const NAV_LINKS = [
        self::EMBEDDED => [
            ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kutuphane',  'label' => 'Kütüphane',  'active' => false],
            ['href' => '/radyo',      'label' => 'Radyo',      'active' => false],
            ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
        ],
        self::PHONE => [
            ['href' => '/home',      'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kutuphane', 'label' => 'Kütüphane',  'active' => false],
            ['href' => '/ayarlar',   'label' => 'Ayarlar',    'active' => false],
        ],
        self::TABLET => [
            ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kesfet',     'label' => 'Keşfet',     'active' => false],
            ['href' => '/albumler',   'label' => 'Albümler',   'active' => false],
            ['href' => '/kutuphane',  'label' => 'Kütüphane',  'active' => false],
            ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
        ],
        self::LAPTOP => [
            ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kesfet',     'label' => 'Keşfet',     'active' => false],
            ['href' => '/albumler',   'label' => 'Albümler',   'active' => false],
            ['href' => '/sanatcilar', 'label' => 'Sanatçılar', 'active' => false],
            ['href' => '/goz-at',     'label' => 'Göz At',     'active' => false],
            ['href' => '/gecmis',     'label' => 'Geçmiş',     'active' => false],
            ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
            ['href' => '/hakkimizda', 'label' => 'Hakkımızda', 'active' => false],
        ],
        self::DESKTOP => [
            ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kesfet',     'label' => 'Keşfet',     'active' => false],
            ['href' => '/albumler',   'label' => 'Albümler',   'active' => false],
            ['href' => '/sanatcilar', 'label' => 'Sanatçılar', 'active' => false],
            ['href' => '/goz-at',     'label' => 'Göz At',     'active' => false],
            ['href' => '/gecmis',     'label' => 'Geçmiş',     'active' => false],
            ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
            ['href' => '/hakkimizda', 'label' => 'Hakkımızda', 'active' => false],
        ],
        self::FOUR_K_TV => [
            ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kesfet',     'label' => 'Keşfet',     'active' => false],
            ['href' => '/albumler',   'label' => 'Albümler',   'active' => false],
            ['href' => '/sanatcilar', 'label' => 'Sanatçılar', 'active' => false],
            ['href' => '/goz-at',     'label' => 'Göz At',     'active' => false],
            ['href' => '/gecmis',     'label' => 'Geçmiş',     'active' => false],
            ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
        ],
        self::FOUR_K_MON => [
            ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
            ['href' => '/kesfet',     'label' => 'Keşfet',     'active' => false],
            ['href' => '/albumler',   'label' => 'Albümler',   'active' => false],
            ['href' => '/sanatcilar', 'label' => 'Sanatçılar', 'active' => false],
            ['href' => '/goz-at',     'label' => 'Göz At',     'active' => false],
            ['href' => '/gecmis',     'label' => 'Geçmiş',     'active' => false],
            ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
            ['href' => '/hakkimizda', 'label' => 'Hakkımızda', 'active' => false],
        ],
    ];

    /* ============================================================
       INSTANCE (Per-request singleton)
       ============================================================ */
    private string $device;
    private string $viewMode;
    private bool   $isAuth;
    private ?int   $viewportW;
    private ?int   $viewportH;

    /** Per-request singleton — tüm template'ler aynı instance'ı paylaşır */
    private static ?self $instance = null;

    private function __construct(
        string $device,
        string $viewMode,
        bool   $isAuth,
        ?int   $viewportW = null,
        ?int   $viewportH = null
    ) {
        $this->device    = $device;
        $this->viewMode  = $viewMode;
        $this->isAuth    = $isAuth;
        $this->viewportW = $viewportW;
        $this->viewportH = $viewportH;
    }

    /* ============================================================
       FACTORY
       ============================================================ */

    /**
     * Per-request singleton factory — tüm template'ler (header/home/footer) aynı instance'ı paylaşır.
     * İlk çağrıtta instance oluşturur, sonraki çağrılarda mevcut instance'ı döndürür.
     *
     * @param array{viewportW?: int|null, viewportH?: int|null, viewMode?: string, isAuth?: bool} $overrides
     */
    public static function instance(array $overrides = []): self
    {
        if (self::$instance === null) {
            self::$instance = self::fromRequest(
                viewportW: $overrides['viewportW'] ?? null,
                viewportH: $overrides['viewportH'] ?? null,
                viewMode:  $overrides['viewMode']  ?? 'home',
                isAuth:    $overrides['isAuth']    ?? false,
            );
        } elseif (!empty($overrides)) {
            // Override'lar varsa mevcut instance'ı güncelle (readonly alanlar hariç)
            if (array_key_exists('viewMode', $overrides)) {
                self::$instance->viewMode = $overrides['viewMode'];
            }
            if (array_key_exists('isAuth', $overrides)) {
                self::$instance->isAuth = (bool)$overrides['isAuth'];
            }
        }

        return self::$instance;
    }

    /**
     * Mevcut singleton instance'ı sıfırla (test için)
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Request'ten DeviceManager oluştur
     * Viewport bilgisi: $_SERVER → cookie (cm_viewport_w/h) → null
     */
    public static function fromRequest(
        ?string $userAgent = null,
        ?int    $viewportW = null,
        ?int    $viewportH = null,
        string  $viewMode  = 'home',
        bool    $isAuth    = false,
    ): self {
        // Viewport bilgisi cookie'den de okunabilir
        if ($viewportW === null && !empty($_COOKIE['cm_viewport_w'])) {
            $viewportW = (int)$_COOKIE['cm_viewport_w'];
        }
        if ($viewportH === null && !empty($_COOKIE['cm_viewport_h'])) {
            $viewportH = (int)$_COOKIE['cm_viewport_h'];
        }

        $device = DeviceDetector::detect($userAgent, $viewportW, $viewportH);
        return new self($device, $viewMode, $isAuth, $viewportW, $viewportH);
    }

    /**
     * Mevcut device string'inden DeviceManager oluştur
     */
    public static function fromDevice(
        string $device,
        string $viewMode = 'home',
        bool   $isAuth = false,
        ?int   $viewportW = null,
        ?int   $viewportH = null
    ): self {
        $device = in_array($device, self::ALL_DEVICES, true) ? $device : self::DESKTOP;
        return new self($device, $viewMode, $isAuth, $viewportW, $viewportH);
    }

    /* ============================================================
       DEVICE TYPE QUERIES
       ============================================================ */

    public function device(): string
    {
        return $this->device;
    }

    /**
     * Standartlaştırılmış Device Profile adı:
     * embedded-1024 | small-desktop | desktop | tv-4k | 4k-monitor | phone | tablet
     */
    public function deviceProfile(): string
    {
        return match ($this->device) {
            self::EMBEDDED   => 'embedded-1024',
            self::LAPTOP     => 'small-desktop',
            self::DESKTOP    => 'desktop',
            self::FOUR_K_TV  => 'tv-4k',
            self::FOUR_K_MON => '4k-monitor',
            self::PHONE      => 'phone',
            self::TABLET     => 'tablet',
            default          => 'desktop',
        };
    }

    public function isEmbedded(): bool
    {
        return $this->device === self::EMBEDDED;
    }

    public function isEmbedded1024(): bool
    {
        return DeviceDetector::isEmbedded1024($this->device, $this->viewportW, $this->viewportH);
    }

    public function viewportWidth(): ?int
    {
        return $this->viewportW;
    }

    public function viewportHeight(): ?int
    {
        return $this->viewportH;
    }

    public function isPhone(): bool
    {
        return $this->device === self::PHONE;
    }

    public function isTablet(): bool
    {
        return $this->device === self::TABLET;
    }

    public function isLaptop(): bool
    {
        return $this->device === self::LAPTOP;
    }

    public function isSmallDesktop(): bool
    {
        return $this->device === self::LAPTOP;
    }

    public function isDesktop(): bool
    {
        return $this->device === self::DESKTOP;
    }

    public function is4kTv(): bool
    {
        return $this->device === self::FOUR_K_TV;
    }

    public function isTv(): bool
    {
        return $this->device === self::FOUR_K_TV;
    }

    public function is4kMonitor(): bool
    {
        return $this->device === self::FOUR_K_MON;
    }

    public function isTouch(): bool
    {
        return in_array($this->device, [self::PHONE, self::TABLET, self::EMBEDDED], true);
    }

    public function isWide(): bool
    {
        return in_array($this->device, [self::DESKTOP, self::FOUR_K_TV, self::FOUR_K_MON], true);
    }

    public function isLarge(): bool
    {
        return in_array($this->device, [self::FOUR_K_TV, self::FOUR_K_MON], true);
    }

    public function isMobile(): bool
    {
        return in_array($this->device, [self::PHONE, self::TABLET], true);
    }

    public function viewMode(): string
    {
        return $this->viewMode;
    }

    public function isAuth(): bool
    {
        return $this->isAuth;
    }

    /* ============================================================
       CSS CLASS HELPERS
       ============================================================ */

    /**
     * Layout class: "layout--embedded", "layout--desktop", vb.
     * HTML elementine eklenecek: class="layout--embedded"
     */
    public function layoutClass(): string
    {
        return 'layout--' . $this->device;
    }

    /**
     * Tüm device class'larını döndür: "layout--embedded device--embedded is-touch"
     */
    public function allClasses(): string
    {
        $classes = [
            $this->layoutClass(),
            'device--' . $this->device,
        ];

        if ($this->isTouch()) {
            $classes[] = 'is-touch';
        }
        if ($this->isWide()) {
            $classes[] = 'is-wide';
        }
        if ($this->isLarge()) {
            $classes[] = 'is-large';
        }
        if ($this->isMobile()) {
            $classes[] = 'is-mobile';
        }

        return implode(' ', $classes);
    }

    /**
     * HTML data attribute'ları: data-device="embedded" data-touch="true"
     */
    public function dataAttributes(): string
    {
        return sprintf(
            'data-device="%s" data-touch="%s" data-wide="%s" data-view-mode="%s"',
            htmlspecialchars($this->device, ENT_QUOTES, 'UTF-8'),
            $this->isTouch() ? 'true' : 'false',
            $this->isWide() ? 'true' : 'false',
            htmlspecialchars($this->viewMode, ENT_QUOTES, 'UTF-8'),
        );
    }

    /* ============================================================
       CONTENT CONFIG — template'lerde kullanılacak sayılar
       ============================================================ */

    /**
     * Widget sayısı
     */
    public function widgetCount(): int
    {
        return match ($this->device) {
            self::PHONE      => 2,
            self::TABLET     => 4,
            self::EMBEDDED   => 4,
            self::LAPTOP     => 4,
            self::DESKTOP    => 6,
            self::FOUR_K_TV  => 6,
            self::FOUR_K_MON => 6,
            default          => 4,
        };
    }

    /**
     * "En Son Dinlenen" kart sayısı
     */
    public function recentCardCount(): int
    {
        return match ($this->device) {
            self::PHONE      => 2,
            self::TABLET     => 4,
            self::EMBEDDED   => 3,
            self::LAPTOP     => 5,
            self::DESKTOP    => 7,
            self::FOUR_K_TV  => 8,
            self::FOUR_K_MON => 8,
            default          => 5,
        };
    }

    /**
     * "Çalma Listeleri" kart sayısı
     */
    public function playlistCount(): int
    {
        return match ($this->device) {
            self::PHONE      => 2,
            self::TABLET     => 3,
            self::EMBEDDED   => 3,
            self::LAPTOP     => 4,
            self::DESKTOP    => 5,
            self::FOUR_K_TV  => 6,
            self::FOUR_K_MON => 6,
            default          => 4,
        };
    }

    /**
     * "Sıradaki Şarkılar" mini kart sayısı
     */
    public function upNextCount(): int
    {
        return match ($this->device) {
            self::PHONE      => 2,
            self::TABLET     => 3,
            self::EMBEDDED   => 3,
            self::LAPTOP     => 4,
            self::DESKTOP    => 6,
            self::FOUR_K_TV  => 8,
            self::FOUR_K_MON => 8,
            default          => 4,
        };
    }

    /* ============================================================
       FEATURE TOGGLES — hangi cihazda ne gösterilir
       ============================================================ */

    /**
     * Volume kontrolü gösterilsin mi?
     * PNG home-1024 footer'da volume VAR (hoparlör + slider + %100) —
     * embedded dahil gösterilir; yalnız phone'da gizlenir.
     */
    public function showVolume(): bool
    {
        return !$this->isPhone();
    }

    /**
     * Tam metadata gösterilsin mi? (bitrate, süre vs.)
     */
    public function showFullMetadata(): bool
    {
        return !$this->isEmbedded() && !$this->isPhone();
    }

    /**
     * Sidebar gösterilsin mi? (Göz At sayfası için)
     */
    public function showSidebar(): bool
    {
        return $this->isWide() || $this->isLaptop();
    }

    /**
     * Seek bar Showing Now Playing card'da gösterilsin mi?
     */
    public function showSeekBar(): bool
    {
        return true; // tüm cihazlarda göster
    }

    /**
     * Playlist toggle gösterilsin mi?
     */
    public function showPlaylistToggle(): bool
    {
        return !$this->isPhone();
    }

    /**
     * Podcast widget gösterilsin mi? (sadece geniş ekranlar)
     */
    public function showPodcastWidget(): bool
    {
        return $this->isWide();
    }

    /**
     * Radyo widget gösterilsin mi?
     */
    public function showRadioWidget(): bool
    {
        return $this->widgetCount() >= 5;
    }

    /**
     * Footer utility icon'ları gösterilsin mi?
     * Phone hariç tüm cihazlarda gösterilir (repeat, shuffle, EQ, fullscreen, playlist, WiFi, BT, settings, AI terminal).
     */
    public function showUtilityIcons(): bool
    {
        return !$this->isPhone();
    }

    /**
     * Footer seek slider (input range) gösterilsin mi?
     * Phone hariç tüm cihazlarda gösterilir.
     */
    public function showFooterSeekSlider(): bool
    {
        return !$this->isPhone();
    }

    /**
     * Karşılama popup'ı (Welcome Popup) gösterilsin mi?
     * YALNIZCA 1024px çözünürlüğe sahip gömülü/Raspberry Pi 5 cihazlarda gösterilir.
     * Masaüstü, laptop, 1080p, 2K, 4K ve telefonlarda bu popup KESİNLİKLE yüklenmez.
     */
    public function shouldRenderWelcomePopup(): bool
    {
        return $this->isEmbedded1024();
    }

    /* ============================================================
       RESOLUTION-BASED LAYOUT DECISIONS (4 TIERS)
       Tier 1: Embedded / Tablet (1024px) -> Image 2 Mockup
       Tier 2: Desktop & Standart (1920px FHD, 2K, 3K, Laptops) -> Image 3 Mockup
       Tier 3: 4K TV & Monitors (3840px) -> Scaled 4K Layout
       Tier 4: Phone (<=767px) -> Mobile Layout
       ============================================================ */

    /**
     * Bu cihaz için desteklenen bir çözüm oranı mı?
     */
    public function isSupportedResolution(): bool
    {
        return true;
    }

    /**
     * Fallback ekranı gösterilsin mi?
     * Tüm ekran boyutları (Mobil, 1024px Gömülü/Tablet, 1920px Masaüstü/Standart, 4K TV)
     * ilgili optimize HTML/CSS bloklarıyla desteklendiği için false döner.
     */
    public function shouldShowFallback(): bool
    {
        return false;
    }

    /**
     * 4K özel DOM tier KALDIRILDI (v2.0.1 — 4K ölçek sözleşmesi):
     * ≥2561px cihazlar Wide (1920 tabanlı) markup render eder; 4K uyumluluğu
     * assets.coremusic.net/Css/08_Devices/d-4k.css (≥3840px zoom ölçek katmanı) sağlar.
     * ScaleManager.js ≥2561px'te skip:true ile ölçeği CSS'e devreder.
     *
     * @deprecated 2.0.1 4K DOM tier kaldırıldı — daima false döner. Metod imzası,
     *           çağıranlar (header.php, footer.php, pages/home.php) için geçici olarak korunur.
     */
    public function shouldRender4kLayout(): bool
    {
        return false;
    }

    /**
     * Embedded / Tablet (1024px) layout render edilsin mi?
     * Tier 1: 7 inç Gömülü (Raspberry Pi 5 / Linux Embedded) ve Tabletler (1024px)
     * Referans: Image 2 Mockup (Linux 1024 - Home Page.png)
     */
    public function shouldRenderEmbeddedLayout(): bool
    {
        if ($this->isPhone()) {
            return false;
        }

        if ($this->isEmbedded() || $this->isTablet()) {
            return true;
        }

        if ($this->viewportW !== null && $this->viewportW <= 1024) {
            return true;
        }

        return false;
    }

    /**
     * Geniş (1920px FHD / 2K / 3K) Masaüstü ve Standart Ekranlar layout render edilsin mi?
     * Tier 2: 1025px - 2560px standart ekranlar VE ≥2561px 4K TV/Monitörler
     * (4K DOM tier kaldırıldı — v2.0.1; 4K'da da Wide markup render edilir).
     * Referans: Image 3 Mockup (Linux - 1920 - Home.png)
     */
    public function shouldRenderWideLayout(): bool
    {
        if ($this->isPhone()) {
            return false;
        }

        if ($this->shouldRenderEmbeddedLayout()) {
            return false;
        }

        if ($this->shouldRender4kLayout()) {
            return false;
        }

        return true;
    }

    /* ============================================================
       NAV LINKS
       ============================================================ */

    /**
     * Bu cihaz için nav link listesini döndür
     * @return array<array{href: string, label: string, active: bool}>
     */
    public function navLinks(): array
    {
        return self::NAV_LINKS[$this->device] ?? self::NAV_LINKS[self::DESKTOP];
    }

    /* ============================================================
       STATIC HELPERS
       ============================================================ */

    /**
     * Tüm desteklenen cihaz türlerini döndür
     * @return string[]
     */
    public static function supportedDevices(): array
    {
        return self::ALL_DEVICES;
    }

    /**
     * Geçerli bir cihaz string'i mi?
     */
    public static function isValidDevice(string $device): bool
    {
        return in_array($device, self::ALL_DEVICES, true);
    }
}
