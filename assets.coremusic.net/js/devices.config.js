/**
 * CoreMusic — Device CSS Configuration
 * Tek CSS haritası kaynağı (Single Source of Truth)
 * DeviceCssMap.php, device-loader.js, DeviceManager.js ile senkronize
 *
 * @module devices-config
 * @version 1.0.0 — 2026-09-03
 */
window.CoreMusic = window.CoreMusic || {};
window.CoreMusic.DEVICES = Object.freeze({

    /** Cihaz bazlı ana CSS dosyaları — 4K tek SSOT: d-4k.css (v3.0.0) */
    HOME_CSS: Object.freeze({
        'embedded':   '08_Devices/d-embedded.css',
        'phone':      '08_Devices/d-phone.css',
        'tablet':     '08_Devices/d-tablet.css',
        'laptop':     '08_Devices/d-laptop.css',
        'desktop':    '08_Devices/d-desktop.css',
        '4k-tv':      '08_Devices/d-4k.css',
        '4k-monitor': '08_Devices/d-4k.css',
    }),

    /** Auth sayfaları için CSS dosyaları */
    AUTH_CSS: Object.freeze({
        'embedded':   '08_Devices/d-auth-embedded.css',
        'phone':      '08_Devices/d-auth-phone.css',
        'tablet':     '08_Devices/d-auth-tablet.css',
        'laptop':     '08_Devices/d-auth-laptop.css',
        'desktop':    '08_Devices/d-auth-desktop.css',
        '4k-tv':      '08_Devices/d-auth-4k-tv.css',
        '4k-monitor': '08_Devices/d-auth-4k-monitor.css',
    }),

    /** View mode CSS dosyaları */
    VIEW_CSS: Object.freeze({
        'home':   '09_ViewModes/v-home.css',
        'pro':    '09_ViewModes/v-pro.css',
        'studio': '09_ViewModes/v-studio.css',
        'car':    '09_ViewModes/v-car.css',
    }),

    /** Tüm desteklenen cihaz türleri */
    ALL: Object.freeze(['embedded', 'phone', 'tablet', 'laptop', 'desktop', '4k-tv', '4k-monitor']),
});
