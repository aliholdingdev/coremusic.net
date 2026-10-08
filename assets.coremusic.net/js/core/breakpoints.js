(function () {
    'use strict';

    /* ============================================================
       BREAKPOINT SSOT — WP2/J2 (2026-10-07)
       ------------------------------------------------------------
       Tek doğruluk kaynağı: cihaz/breakpoint/tier değerleri BURADADIR.
       Tüketenler (kopya SİLİNİR):
         - js/device-loader.js        (BP + detect + detectUA + getTier gövdeleri)
         - js/managers/DeviceManager.js  (static BREAKPOINTS + #detect)
         - js/device-layout-updater.js   (yerel getTier)
       PHP tarafı (DeviceDetector.php sabitleri) bu DEĞERLERE EŞİT olmalıdır —
       eşitlik shared/tests (breakpoints.spec.js parity) ile kilitlenir.
       Yükleme: classic IIFE (devices.config.js kalıbı) — ES import DEĞİL;
       HtmlShellRenderer devices.config.js'den ÖNCE emit eder.
       ============================================================ */

    var BREAKPOINTS = Object.freeze({
        PHONE_MAX: 767,
        TABLET_MIN: 768,
        TABLET_MAX: 1024,
        EMBEDDED_MAX: 1024,
        EMBEDDED_H_MAX: 600,
        LAPTOP_MAX: 1440,
        DESKTOP_MAX: 2560,
        FOUR_K_TV_MAX: 3840,
    });

    /* UA kalıpları — tek yerde (eski: device-loader içinde tekrar ediyordu) */
    var RE_EMBEDDED_UA = /Raspberry Pi|RPi|aarch64|armv7|armv8|CrOS/i;
    var RE_TV_UA = /Tizen|Web0S|webOS|SmartTV|BRAVIA|NetCast|AppleTV|Android TV|GoogleTV|HbbTV|Roku/i;
    var RE_DESKTOP_OS_UA = /Windows|Macintosh|Linux/i;

    function currentUA() {
        return (typeof navigator !== 'undefined' && navigator.userAgent) || '';
    }

    function pointerFine() {
        try {
            return !!(typeof window !== 'undefined' && window.matchMedia
                && window.matchMedia('(pointer: fine)').matches);
        } catch (e) {
            return false;
        }
    }

    /**
     * Viewport + UA'dan cihaz türü (7'li set).
     * @param {number} w
     * @param {number} h
     * @param {string} [ua] test enjeksiyonu (varsayılan: navigator.userAgent)
     * @param {boolean} [fine] test enjeksiyonu (varsayılan: matchMedia pointer:fine)
     * @returns {string} phone|tablet|embedded|laptop|desktop|4k-tv|4k-monitor
     */
    function detectDevice(w, h, ua, fine) {
        ua = typeof ua === 'string' ? ua : currentUA();
        if (fine === undefined) { fine = pointerFine(); }

        if (RE_EMBEDDED_UA.test(ua)) return 'embedded';
        if (w <= BREAKPOINTS.PHONE_MAX) return 'phone';
        if (w >= BREAKPOINTS.TABLET_MIN && w <= BREAKPOINTS.TABLET_MAX) {
            if (h <= BREAKPOINTS.EMBEDDED_H_MAX) return 'embedded';
            return 'tablet';
        }
        if (w <= BREAKPOINTS.LAPTOP_MAX) return 'laptop';
        if (w <= BREAKPOINTS.DESKTOP_MAX) return 'desktop';
        if (w <= BREAKPOINTS.FOUR_K_TV_MAX) {
            if (RE_TV_UA.test(ua)) return '4k-tv';
            if (fine && RE_DESKTOP_OS_UA.test(ua)) return '4k-monitor';
            return '4k-tv';
        }
        return '4k-monitor';
    }

    /**
     * UA'dan mobile/embedded tespiti (viewport'suz).
     * @param {string} [ua]
     * @returns {string|null}
     */
    function detectUA(ua) {
        ua = typeof ua === 'string' ? ua : currentUA();
        if (!ua) return null;
        if (RE_EMBEDDED_UA.test(ua)) return 'embedded';
        if (/Android/i.test(ua)) return /Mobile/i.test(ua) ? 'phone' : 'tablet';
        if (/iPhone|iPod/i.test(ua)) return 'phone';
        if (/iPad/i.test(ua)) return 'tablet';
        if (/Windows Phone|BlackBerry|Opera Mini|Opera Mobi/i.test(ua)) return 'phone';
        return null;
    }

    /**
     * DOM tier: 'phone' | 'embedded' | 'wide'
     * (SSR DeviceRenderer::tierOf ile aynı sözcük seti; 4K cihazlar 'wide' —
     * ölçek d-4k.css'e ait, ayrı tier DEĞİL.)
     * @param {string} device
     * @param {number} [w] viewport genişliği (fallback)
     * @param {string} [ua]
     * @returns {string}
     */
    function tierOf(device, w, ua) {
        ua = typeof ua === 'string' ? ua : currentUA();
        if (RE_EMBEDDED_UA.test(ua)) return 'embedded';
        if (device === 'phone' || (w && w <= BREAKPOINTS.PHONE_MAX)) return 'phone';
        if (device === '4k-tv' || device === '4k-monitor' || (w && w > BREAKPOINTS.DESKTOP_MAX)) return 'wide';
        if (device === 'desktop' || device === 'laptop' || (w && w > BREAKPOINTS.TABLET_MAX)) return 'wide';
        return 'embedded';
    }

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.BREAKPOINTS = BREAKPOINTS;
    window.CoreMusic.BreakpointAPI = Object.freeze({
        detectDevice: detectDevice,
        detectUA: detectUA,
        tierOf: tierOf,
    });
})();