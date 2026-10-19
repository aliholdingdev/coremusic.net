// @vitest-environment jsdom
/**
 * device-loader.spec — P1-11: operatör öncelik bug (loadAll view fallback).
 *
 * Sözleşme:
 *  - `loadCSS(base + VIEW_CSS[viewMode] || VIEW_CSS['home'])` → `+` öncelikli
 *    olduğu için `base + undefined` ("/Css/undefined", truthy) fallback'i
 *    eziyordu → bilinmeyen viewMode'de /Css/undefined 404'ü gidiyordu.
 *  - Düzeltme: `base + (VIEW_CSS[viewMode] || VIEW_CSS['home'])`.
 *  - Bilinmeyen viewMode → fallback 'home' dosyası; bilinen viewMode → o dosya.
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeAll, beforeEach, afterEach, vi } from 'vitest';
import '../js/core/breakpoints.js'; // window.CoreMusic.BreakpointAPI (SSOT)

const VIEW_HOME = '09_ViewModes/v-home.css';
const VIEW_PRO = '09_ViewModes/v-pro.css';

/**
 * init() otomasyonu (script[data-cm-device-loader] + DOMContentLoaded).
 * @param {string} viewMode
 */
function runInit(viewMode) {
    const script = document.createElement('script');
    script.setAttribute('data-cm-device-loader', '');
    script.setAttribute('data-view-mode', viewMode);
    script.setAttribute('data-is-auth', 'false');
    document.body.appendChild(script);
    document.dispatchEvent(new Event('DOMContentLoaded'));
}

/** @returns {string|null} cm-view-css link'inin href'i */
function viewHref() {
    const el = document.getElementById('cm-view-css');
    return el ? el.getAttribute('href') : null;
}

describe('device-loader.loadAll — viewMode fallback (P1-11)', () => {
    beforeAll(async () => {
        window.CoreMusic.DEVICES = {
            HOME_CSS: {
                phone: '08_Devices/d-phone.css',
                tablet: '08_Devices/d-tablet.css',
                laptop: '08_Devices/d-laptop.css',
                desktop: '08_Devices/d-desktop.css',
                embedded: '08_Devices/d-embedded.css',
            },
            AUTH_CSS: { desktop: '08_Devices/auth-desktop.css' },
            VIEW_CSS: { home: VIEW_HOME, pro: VIEW_PRO },
            ALL: ['phone', 'tablet', 'laptop', 'desktop', 'embedded'],
        };
        // init() tier uyuşmazlığında reload/navigate çağırır — jsdom reload
        // "Not implemented" basar; mock Router ile emilir (test izolasyonu).
        window.CoreMusic.Router = { navigate: vi.fn() };
        await import('../js/device-loader.js');
    });

    beforeEach(() => {
        document.head.querySelectorAll('link').forEach((l) => l.remove());
        document.body.replaceChildren();
        window.CoreMusic.Router.navigate.mockClear();
    });

    afterEach(() => {
        document.body.replaceChildren();
    });

    it('bilinmeyen viewMode → fallback home dosyası yüklenir, /Css/undefined ÜRETİLMEZ', () => {
        runInit('does-not-exist');

        const href = viewHref();
        expect(href).not.toBeNull();
        expect(href).toContain(VIEW_HOME);
        expect(href).not.toContain('undefined');

        // Tüm link'lerde 'undefined' parçası olmamalı (device link'i dahil)
        const broken = [...document.querySelectorAll('link')].filter((l) =>
            String(l.getAttribute('href') || '').includes('undefined')
        );
        expect(broken).toHaveLength(0);
    });

    it('bilinen viewMode (pro) → o view dosyası fallback’e düşmeden yüklenir', () => {
        runInit('pro');

        const href = viewHref();
        expect(href).toContain(VIEW_PRO);
        expect(href).not.toContain('undefined');
    });

    it('view link’i device link’inden SONRA gelir (sıra sözleşmesi korunur)', () => {
        runInit('does-not-exist');

        const order = [...document.querySelectorAll('link[id$="-css"], #cm-device-css, #cm-view-css')]
            .map((l) => l.id);
        expect(order).toContain('cm-device-css');
        expect(order).toContain('cm-view-css');
        expect(order.indexOf('cm-view-css')).toBeGreaterThan(order.indexOf('cm-device-css'));
    });
});
