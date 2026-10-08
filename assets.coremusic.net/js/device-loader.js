/**
 * CoreMusic — Device Loader (Client-Side Dynamic CSS)
 * Viewport ve User-Agent'a göre cihaz tespit eder, doğru CSS'yi dinamik yükler
 * Breakpoint'ler: a-breakpoint-tokens.css ile senkronize
 *
 * Breakpoint Haritası:
 *   phone      ≤767px
 *   tablet     768-1024px (yükseklik >600px)
 *   embedded   ≤1024px (yükseklik ≤600px — RPi5)
 *   laptop     1025-1440px
 *   desktop    1441-2560px (varsayılan)
 *   4k-tv      2561-3840px
 *   4k-monitor ≥3841px
 *
 * Kullanım:
 *   <script src="/Js/device-loader.js" defer></script>
 *   // veya module olarak:
 *   import { DeviceLoader } from '/Js/device-loader.js';
 */
(function () {
    'use strict';

    /* ============================================================
       BREAKPOINT CONSTANTS — WP2/J2: tek SSOT `js/core/breakpoints.js`
       (HtmlShellRenderer bunu devices.config.js'den ÖNCE emit eder).
       Burada yalnız alias tutulur; kopya değer/fonksiyon YASAK (fail-fast:
       API yoksa hata görünür — sessiz yedek kopya değil).
       ============================================================ */
    const BP = window.CoreMusic.BREAKPOINTS;
    const BPI = window.CoreMusic.BreakpointAPI;

    /* ============================================================
       CSS FILE MAP — devices.config.js'den yüklenir (SSOT)
       ============================================================ */
    const DEVICES = window.CoreMusic && window.CoreMusic.DEVICES ? window.CoreMusic.DEVICES : {};
    const HOME_CSS = DEVICES.HOME_CSS || {};
    const AUTH_CSS = DEVICES.AUTH_CSS || {};
    const VIEW_CSS = DEVICES.VIEW_CSS || {};
    const ALL_DEVICES = DEVICES.ALL || Object.keys(HOME_CSS);

    /* ============================================================
       DEVICE DETECTION
       ============================================================ */

    /**
     * Viewport boyutundan cihaz türü tespit et — WP2/J2: SSOT delegasyonu
     * (gövde `js/core/breakpoints.js` → BreakpointAPI.detectDevice).
     * @param {number} w  Viewport genişliği
     * @param {number} h  Viewport yüksekliği
     * @returns {string} Device type
     */
    function detect(w, h) {
        return BPI.detectDevice(w, h);
    }

    /**
     * User-Agent'den mobile cihaz tespit et — WP2/J2: SSOT delegasyonu.
     * @param {string} ua
     * @returns {string|null}
     */
    function detectUA(ua) {
        return BPI.detectUA(ua);
    }

    /* ============================================================
       CSS LOADING ENGINE
       ============================================================ */

    /** Aktif CSS link'lerini tut */
    const activeLinks = {
        device: null,
        view: null,
        auth: null,
    };

    /**
     * Cache-buster — öncelik: window.CoreMusic.RouterConfig.cssVersion
     * (HtmlShellRenderer inline config), fallback: data-cm-css-buster
     * @returns {string}
     */
    function cssBuster() {
        const rc = window.CoreMusic && window.CoreMusic.RouterConfig;
        if (rc && rc.cssVersion) return String(rc.cssVersion);
        const el = document.querySelector('script[data-cm-device-loader]');
        return el ? (el.getAttribute('data-cm-css-buster') || '') : '';
    }

    /**
     * Tek bir CSS dosyası yükle/değiştir
     * @param {string} href  CSS dosya yolu
     * @param {string} id    Link element ID
     * @returns {HTMLLinkElement}
     */
    function loadCSS(href, id) {
        const buster = cssBuster();
        if (buster) {
            href += (href.indexOf('?') > -1 ? '&' : '?') + 'v=' + buster;
        }

        const existing = document.getElementById(id);
        if (existing) {
            // Aynı dosya zaten yüklüyse atlama
            if (existing.getAttribute('href') === href) return existing;
            existing.remove();
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.id = id;
        document.head.appendChild(link);
        return link;
    }

    /**
     * CSS'i kaldır
     * @param {string} id
     */
    function removeCSS(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    /**
     * Cihaz türüne göre tüm CSS'leri yükle
     * @param {string} device    Device type
     * @param {boolean} isAuth   Auth sayfası mı?
     * @param {string} viewMode  View mode
     * @param {string} baseUrl   CSS base URL
     */
    function loadAll(device, isAuth, viewMode, baseUrl) {
        const base = baseUrl || '/Css/';

        if (isAuth) {
            // Auth: auth-bundled (base) THEN device CSS (overrides)
            loadCSS(base + 'auth-bundled.css', 'cm-auth-bundled');
            loadCSS(base + AUTH_CSS[device], 'cm-device-css');
            removeCSS('cm-view-css');
        } else {
            // Home: d-{device}.css + v-{viewMode}.css
            loadCSS(base + HOME_CSS[device], 'cm-device-css');
            loadCSS(base + VIEW_CSS[viewMode] || VIEW_CSS['home'], 'cm-view-css');
            removeCSS('cm-auth-bundled');
        }
    }

    /**
     * Sadece device CSS'i değiştir (view korunarak)
     * @param {string} device
     * @param {boolean} isAuth
     * @param {string} baseUrl
     */
    function loadDeviceOnly(device, isAuth, baseUrl) {
        const base = baseUrl || '/Css/';

        if (isAuth) {
            loadCSS(base + AUTH_CSS[device], 'cm-device-css');
        } else {
            loadCSS(base + HOME_CSS[device], 'cm-device-css');
        }
    }

    /**
     * Map device type / viewport to one of the 3 primary UI tiers:
     * 'phone' | 'embedded' | 'wide'
     * NOT: 4K artık ayrı DOM tier DEĞİL — DeviceManager ≥2561px'te Wide markup
     * render eder, ölçeği d-4k.css (≥3840px zoom) üstlenir. Bu yüzden 4k-tv /
     * 4k-monitor cihazları 'wide' tier'a map edilir (tier-sync reload döngüsü önlenir).
     */
    function getTier(device, w) {
        return BPI.tierOf(device, w);
    }

    /* ============================================================
       RESIZE OBSERVER (debounced 300ms)
       ============================================================ */
    let resizeTimer = null;
    let lastDevice = null;

    function onResize(state) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            const w = window.innerWidth || document.documentElement.clientWidth;
            const h = window.innerHeight || document.documentElement.clientHeight;
            let newDevice = detect(w, h);
            const ua = navigator.userAgent || '';
            const uaDevice = detectUA(ua);
            if (uaDevice && (uaDevice === 'phone' || uaDevice === 'tablet' || uaDevice === 'embedded')) {
                newDevice = uaDevice;
            }

            // Viewport çerezini güncelle
            try {
                document.cookie = 'cm_viewport_w=' + w + ';path=/;max-age=86400;SameSite=Lax';
                document.cookie = 'cm_viewport_h=' + h + ';path=/;max-age=86400;SameSite=Lax';
            } catch (e) {}

            const oldTier = getTier(lastDevice, w);
            const newTier = getTier(newDevice, w);

            // Tier sınırı aşıldıysa koşullu HTML bloklarının sunucudan yeniden yüklenmesi gerekir
            if (newTier !== oldTier) {
                lastDevice = newDevice;
                if (window.CoreMusic && window.CoreMusic.Router && typeof window.CoreMusic.Router.navigate === 'function') {
                    window.CoreMusic.Router.navigate(window.location.pathname);
                } else {
                    window.location.reload();
                }
                return;
            }

            if (newDevice !== lastDevice) {
                const oldDevice = lastDevice;
                lastDevice = newDevice;

                // Cihaz geçiş animasyonu: fade-out → CSS yükle → fade-in (CSP-safe)
                const main = document.querySelector('.page-home, main[data-device]');
                if (main) {
                    main.classList.add('cm-device-transitioning');
                }

                // Yeni device CSS yükle
                loadDeviceOnly(newDevice, state.isAuth, state.baseUrl);

                // CSS yüklendikten sonra fade-in
                setTimeout(function () {
                    if (main) {
                        main.classList.remove('cm-device-transitioning');
                        main.classList.add('cm-device-transition-complete');
                        setTimeout(function () {
                            main.classList.remove('cm-device-transition-complete');
                        }, 250);
                    }
                }, 160);

                // Body attribute güncelle
                if (document.body) {
                    document.body.setAttribute('data-device', newDevice);
                }

                // Global güncelle
                if (window.CoreMusic) {
                    window.CoreMusic.deviceType = newDevice;
                }

                // Event tetikle
                window.dispatchEvent(new CustomEvent('devicechange', {
                    detail: { device: newDevice, previous: oldDevice }
                }));
            }
        }, 300);
    }

    /* ============================================================
       INIT
       ============================================================ */

    /**
     * DeviceLoader'ı başlat
     * @param {Object} opts
     * @param {string} opts.assetsUrl    Assets URL
     * @param {boolean} opts.isAuth      Auth sayfası mı?
     * @param {string} opts.viewMode     View mode (home/pro/studio/car)
     * @param {string} opts.serverDevice Sunucu cihaz tahmini (varsa)
     * @returns {string} Tespit edilen cihaz
     */
    function init(opts) {
        opts = opts || {};
        const assetsUrl = opts.assetsUrl || '';
        const isAuth = !!opts.isAuth;
        const viewMode = opts.viewMode || 'home';
        const baseUrl = assetsUrl ? assetsUrl + '/Css/' : '/Css/';

        // State oluştur
        const state = {
            isAuth: isAuth,
            viewMode: viewMode,
            baseUrl: baseUrl,
        };

        // Viewport'tan tespit
        const w = window.innerWidth || document.documentElement.clientWidth;
        const h = window.innerHeight || document.documentElement.clientHeight;
        let device = detect(w, h);

        // User-Agent mobile/embedded kontrolü
        const ua = navigator.userAgent || '';
        const uaDevice = detectUA(ua);
        if (uaDevice && (uaDevice === 'phone' || uaDevice === 'tablet' || uaDevice === 'embedded')) {
            device = uaDevice;
        }

        // Viewport bilgisini cookie'ye yaz (sunucu tarafı tespit için)
        try {
            document.cookie = 'cm_viewport_w=' + w + ';path=/;max-age=86400;SameSite=Lax';
            document.cookie = 'cm_viewport_h=' + h + ';path=/;max-age=86400;SameSite=Lax';
        } catch (e) {}

        // Server tahmini: sadece viewport belirsiz olduğunda (desktop default) kullan
        // Viewport tespiti her zaman öncelikli (DevTools resize senaryosu için)
        if (opts.serverDevice && ALL_DEVICES.indexOf(opts.serverDevice) !== -1) {
            if (device === 'desktop' && opts.serverDevice !== 'desktop') {
                const viewportIsSpecific = (device !== 'desktop');
                if (!viewportIsSpecific) {
                    device = opts.serverDevice;
                }
            }
        }

        // İlk yüklemede sunucu render edilen tier ile tespit edilen tier uyuşmazlığını kontrol et
        const detectedTier = getTier(device, w);
        const mainEl = document.querySelector('main[data-tier]');
        const renderedTier = mainEl ? mainEl.getAttribute('data-tier') : null;

        // Treat '4k' and 'wide' as equivalent — 4K devices use Wide markup + d-4k.css zoom scaling
        const tiersMatch = (renderedTier === detectedTier) ||
                         (renderedTier === '4k' && detectedTier === 'wide') ||
                         (renderedTier === 'wide' && detectedTier === '4k');

        if (renderedTier && !tiersMatch) {
            const syncAttempts = parseInt(sessionStorage.getItem('cm_tier_sync_count') || '0', 10);
            if (syncAttempts < 2) {
                sessionStorage.setItem('cm_tier_sync_count', String(syncAttempts + 1));
                if (window.CoreMusic && window.CoreMusic.Router && typeof window.CoreMusic.Router.navigate === 'function') {
                    window.CoreMusic.Router.navigate(window.location.pathname);
                } else {
                    window.location.reload();
                }
                return device;
            }
        } else {
            sessionStorage.removeItem('cm_tier_sync_count');
        }

        // CSS yükle
        loadAll(device, isAuth, viewMode, baseUrl);

        // Son kaydet
        lastDevice = device;

        // Body'ye device ekle
        if (document.body) {
            document.body.setAttribute('data-device', device);
        }

        // Global'e kaydet
        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.deviceType = device;
        window.CoreMusic.DeviceLoader = {
            detect: detect,
            getTier: getTier,
            loadAll: loadAll,
            loadDeviceOnly: loadDeviceOnly,
            getDevice: function () { return lastDevice; },
            BREAKPOINTS: BP,
        };

        // Resize dinle
        window.addEventListener('resize', function () {
            onResize(state);
        });

        return device;
    }

    /* ============================================================
       AUTO-INIT (script data attribute'lardan)
       ============================================================ */
    if (typeof document !== 'undefined') {
        document.addEventListener('DOMContentLoaded', function () {
            const script = document.querySelector('script[data-cm-device-loader]');
            if (script) {
                init({
                    assetsUrl:   script.getAttribute('data-assets-url') || '',
                    isAuth:      script.getAttribute('data-is-auth') === 'true',
                    viewMode:    script.getAttribute('data-view-mode') || 'home',
                    serverDevice: script.getAttribute('data-server-device') || null,
                });
            }
        });
    }

    // Module export
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = { DeviceLoader: { init: init, detect: detect, loadAll: loadAll } };
    }
})();
