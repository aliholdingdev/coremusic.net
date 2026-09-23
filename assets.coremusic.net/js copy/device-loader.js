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
       CSS FILE MAP — devices.config.js'den yüklenir (SSOT)
       ============================================================ */
    var DEVICES = window.CoreMusic && window.CoreMusic.DEVICES ? window.CoreMusic.DEVICES : {};
    var HOME_CSS = DEVICES.HOME_CSS || {};
    var AUTH_CSS = DEVICES.AUTH_CSS || {};
    var VIEW_CSS = DEVICES.VIEW_CSS || {};
    var ALL_DEVICES = DEVICES.ALL || Object.keys(HOME_CSS);

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
        // Embedded device (RPi5, ARM Linux) — viewport'a bakmadan embedded
        var ua = navigator.userAgent || '';
        if (/Raspberry Pi|RPi|aarch64|armv7|armv8|CrOS/i.test(ua)) return 'embedded';

        if (w <= BP.PHONE_MAX) return 'phone';
        if (w >= BP.TABLET_MIN && w <= BP.TABLET_MAX) {
            if (h <= 600) return 'embedded';
            return 'tablet';
        }
        if (w <= BP.LAPTOP_MAX) return 'laptop';
        if (w <= BP.DESKTOP_MAX) return 'desktop';
        if (w <= BP.FOUR_K_TV_MAX) {
            if (/Tizen|Web0S|webOS|SmartTV|BRAVIA|NetCast|AppleTV|Android TV|GoogleTV|HbbTV|Roku/i.test(ua)) {
                return '4k-tv';
            }
            if (window.matchMedia && window.matchMedia('(pointer: fine)').matches && /Windows|Macintosh|Linux/i.test(ua)) {
                return '4k-monitor';
            }
            return '4k-tv';
        }
        return '4k-monitor';
    }

    /**
     * User-Agent'den mobile cihaz tespit et
     * @param {string} ua
     * @returns {string|null}
     */
    function detectUA(ua) {
        if (!ua) return null;
        // Embedded device (RPi5, ARM Linux) — her zaman embedded
        if (/Raspberry Pi|RPi|aarch64|armv7|armv8|CrOS/i.test(ua)) return 'embedded';
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
     * Cache-buster — öncelik: window.CoreMusic.RouterConfig.cssVersion
     * (HtmlShellRenderer inline config), fallback: data-cm-css-buster
     * @returns {string}
     */
    function cssBuster() {
        var rc = window.CoreMusic && window.CoreMusic.RouterConfig;
        if (rc && rc.cssVersion) return String(rc.cssVersion);
        var el = document.querySelector('script[data-cm-device-loader]');
        return el ? (el.getAttribute('data-cm-css-buster') || '') : '';
    }

    /**
     * Tek bir CSS dosyası yükle/değiştir
     * @param {string} href  CSS dosya yolu
     * @param {string} id    Link element ID
     * @returns {HTMLLinkElement}
     */
    function loadCSS(href, id) {
        var buster = cssBuster();
        if (buster) {
            href += (href.indexOf('?') > -1 ? '&' : '?') + 'v=' + buster;
        }

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

    /**
     * Map device type / viewport to one of the 3 primary UI tiers:
     * 'phone' | 'embedded' | 'wide'
     * NOT: 4K artık ayrı DOM tier DEĞİL — DeviceManager ≥2561px'te Wide markup
     * render eder, ölçeği d-4k.css (≥3840px zoom) üstlenir. Bu yüzden 4k-tv /
     * 4k-monitor cihazları 'wide' tier'a map edilir (tier-sync reload döngüsü önlenir).
     */
    function getTier(device, w) {
        var ua = navigator.userAgent || '';
        if (/Raspberry Pi|RPi|aarch64|armv7|armv8|CrOS/i.test(ua)) return 'embedded';
        if (device === 'phone' || (w && w <= BP.PHONE_MAX)) return 'phone';
        if (device === '4k-tv' || device === '4k-monitor' || (w && w > BP.DESKTOP_MAX)) return 'wide';
        if (device === 'desktop' || device === 'laptop' || (w && w > BP.TABLET_MAX)) return 'wide';
        return 'embedded';
    }

    /* ============================================================
       RESIZE OBSERVER (debounced 300ms)
       ============================================================ */
    var resizeTimer = null;
    var lastDevice = null;

    function onResize(state) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            var w = window.innerWidth || document.documentElement.clientWidth;
            var h = window.innerHeight || document.documentElement.clientHeight;
            var newDevice = detect(w, h);
            var ua = navigator.userAgent || '';
            var uaDevice = detectUA(ua);
            if (uaDevice && (uaDevice === 'phone' || uaDevice === 'tablet' || uaDevice === 'embedded')) {
                newDevice = uaDevice;
            }

            // Viewport çerezini güncelle
            try {
                document.cookie = 'cm_viewport_w=' + w + ';path=/;max-age=86400;SameSite=Lax';
                document.cookie = 'cm_viewport_h=' + h + ';path=/;max-age=86400;SameSite=Lax';
            } catch (e) {}

            var oldTier = getTier(lastDevice, w);
            var newTier = getTier(newDevice, w);

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
                var oldDevice = lastDevice;
                lastDevice = newDevice;

                // Cihaz geçiş animasyonu: fade-out → CSS yükle → fade-in (CSP-safe)
                var main = document.querySelector('.page-home, main[data-device]');
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

        // User-Agent mobile/embedded kontrolü
        var ua = navigator.userAgent || '';
        var uaDevice = detectUA(ua);
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
                var viewportIsSpecific = (device !== 'desktop');
                if (!viewportIsSpecific) {
                    device = opts.serverDevice;
                }
            }
        }

        // İlk yüklemede sunucu render edilen tier ile tespit edilen tier uyuşmazlığını kontrol et
        var detectedTier = getTier(device, w);
        var mainEl = document.querySelector('main[data-tier]');
        var renderedTier = mainEl ? mainEl.getAttribute('data-tier') : null;

        // Treat '4k' and 'wide' as equivalent — 4K devices use Wide markup + d-4k.css zoom scaling
        var tiersMatch = (renderedTier === detectedTier) ||
                         (renderedTier === '4k' && detectedTier === 'wide') ||
                         (renderedTier === 'wide' && detectedTier === '4k');

        if (renderedTier && !tiersMatch) {
            var syncAttempts = parseInt(sessionStorage.getItem('cm_tier_sync_count') || '0', 10);
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
