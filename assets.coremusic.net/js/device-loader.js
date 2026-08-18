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
       BREAKPOINT CONSTANTS (a-breakpoint-tokens.css ile senkronize)
       ============================================================ */
    const BP = {
        PHONE_MAX:     767,
        TABLET_MIN:    768,
        TABLET_MAX:    1024,
        EMBEDDED_MAX:  1024,
        LAPTOP_MAX:    1440,
        DESKTOP_MAX:   2560,
        FOUR_K_TV_MAX: 3840,
    };

    /* ============================================================
       CSS FILE MAP (DeviceCssMap.php ile senkronize)
       ============================================================ */
    const HOME_CSS = {
        'embedded':   '08_Devices/d-embedded.css',
        'phone':      '08_Devices/d-phone.css',
        'tablet':     '08_Devices/d-tablet.css',
        'laptop':     '08_Devices/d-laptop.css',
        'desktop':    '08_Devices/d-desktop.css',
        '4k-tv':      '08_Devices/d-4k-tv.css',
        '4k-monitor': '08_Devices/d-4k-monitor.css',
    };

    const AUTH_CSS = {
        'embedded':   '08_Devices/d-auth-embedded.css',
        'phone':      '08_Devices/d-auth-phone.css',
        'tablet':     '08_Devices/d-auth-tablet.css',
        'laptop':     '08_Devices/d-auth-laptop.css',
        'desktop':    '08_Devices/d-auth-desktop.css',
        '4k-tv':      '08_Devices/d-auth-4k-tv.css',
        '4k-monitor': '08_Devices/d-auth-4k-monitor.css',
    };

    const VIEW_CSS = {
        'home':   '09_ViewModes/v-home.css',
        'pro':    '09_ViewModes/v-pro.css',
        'studio': '09_ViewModes/v-studio.css',
        'car':    '09_ViewModes/v-car.css',
    };

    const ALL_DEVICES = Object.keys(HOME_CSS);

    /* ============================================================
       DEVICE DETECTION
       ============================================================ */

    /**
     * Viewport boyutundan cihaz türü tespit et
     * @param {number} w  Viewport genişliği
     * @param {number} h  Viewport yüksekliği
     * @returns {string} Device type
     */
    function detect(w, h) {
        if (w <= BP.PHONE_MAX) return 'phone';
        if (w >= BP.TABLET_MIN && w <= BP.TABLET_MAX) {
            return h <= 600 ? 'embedded' : 'tablet';
        }
        if (w <= BP.LAPTOP_MAX) return 'laptop';
        if (w <= BP.DESKTOP_MAX) return 'desktop';
        if (w <= BP.FOUR_K_TV_MAX) return '4k-tv';
        return '4k-monitor';
    }

    /**
     * User-Agent'den mobile cihaz tespit et
     * @param {string} ua
     * @returns {string|null}
     */
    function detectUA(ua) {
        if (!ua) return null;
        if (/Android/i.test(ua)) return /Mobile/i.test(ua) ? 'phone' : 'tablet';
        if (/iPhone|iPod/i.test(ua)) return 'phone';
        if (/iPad/i.test(ua)) return 'tablet';
        if (/Windows Phone|BlackBerry|Opera Mini|Opera Mobi/i.test(ua)) return 'phone';
        return null;
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
     * Tek bir CSS dosyası yükle/değiştir
     * @param {string} href  CSS dosya yolu
     * @param {string} id    Link element ID
     * @returns {HTMLLinkElement}
     */
    function loadCSS(href, id) {
        var existing = document.getElementById(id);
        if (existing) {
            // Aynı dosya zaten yüklüyse atlama
            if (existing.getAttribute('href') === href) return existing;
            existing.remove();
        }

        var link = document.createElement('link');
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
        var el = document.getElementById(id);
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
        var base = baseUrl || '/Css/';

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
        var base = baseUrl || '/Css/';

        if (isAuth) {
            loadCSS(base + AUTH_CSS[device], 'cm-device-css');
        } else {
            loadCSS(base + HOME_CSS[device], 'cm-device-css');
        }
    }

    /* ============================================================
       RESIZE OBSERVER (debounced)
       ============================================================ */
    var resizeTimer = null;
    var lastDevice = null;

    function onResize(state) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            var w = window.innerWidth || document.documentElement.clientWidth;
            var h = window.innerHeight || document.documentElement.clientHeight;
            var newDevice = detect(w, h);

            if (newDevice !== lastDevice) {
                var oldDevice = lastDevice;
                lastDevice = newDevice;

                // Yeni device CSS yükle
                loadDeviceOnly(newDevice, state.isAuth, state.baseUrl);

                // Body attribute güncelle
                if (document.body) {
                    document.body.setAttribute('data-device', newDevice);
                }

                // Global güncelle
                if (window.CoreMusic) {
                    window.CoreMusic.deviceType = newDevice;
                }

                // Eventtet
                window.dispatchEvent(new CustomEvent('devicechange', {
                    detail: { device: newDevice, previous: oldDevice }
                }));
            }
        }, 200);
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
        var assetsUrl = opts.assetsUrl || '';
        var isAuth = !!opts.isAuth;
        var viewMode = opts.viewMode || 'home';
        var baseUrl = assetsUrl ? assetsUrl + '/Css/' : '/Css/';

        // State oluştur
        var state = {
            isAuth: isAuth,
            viewMode: viewMode,
            baseUrl: baseUrl,
        };

        // Viewport'tan tespit
        var w = window.innerWidth || document.documentElement.clientWidth;
        var h = window.innerHeight || document.documentElement.clientHeight;
        var device = detect(w, h);

        // User-Agent mobile kontrolü
        var ua = navigator.userAgent || '';
        var uaDevice = detectUA(ua);
        if (uaDevice && (uaDevice === 'phone' || uaDevice === 'tablet')) {
            device = uaDevice;
        }

        // Server tahmini varsa ve viewport uyuyorsa
        if (opts.serverDevice && ALL_DEVICES.indexOf(opts.serverDevice) !== -1) {
            device = opts.serverDevice;
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
            var script = document.querySelector('script[data-cm-device-loader]');
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
