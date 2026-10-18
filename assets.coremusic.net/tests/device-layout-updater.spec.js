// @vitest-environment jsdom
/**
 * device-layout-updater.spec — F2: className korupsiyonu + birikim.
 *
 * Sözleşme (updateLayoutClasses):
 *  a) Strip yalnız `layout--` ile BAŞLAYAN token'lara uygulanır —
 *     "home-layout--phone" gibi başka bloğun modifier'ı KORUNUR,
 *     "home-" çöpü üretilmez.
 *  b) main + header + footer üçü de layout--* strip eder (birikim yok).
 *  c) Aynı cihaz iki kez uygulanırsa duplicate modifier oluşmaz.
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeAll, beforeEach } from 'vitest';
import '../js/core/breakpoints.js'; // window.CoreMusic.BreakpointAPI (SSOT)
import '../js/device-layout-updater.js'; // IIFE → window.CoreMusic.DeviceLayoutUpdater

/** @returns {{main: HTMLElement, header: HTMLElement, footer: HTMLElement}} */
function buildLayout() {
    const main = document.createElement('main');
    main.className = 'page-home home-layout';
    const header = document.createElement('header');
    header.className = 'site-header';
    const footer = document.createElement('footer');
    footer.className = 'footer';
    document.body.append(main, header, footer);
    return { main, header, footer };
}

/** @param {HTMLElement} el @returns {string[]} token listesi */
function tokens(el) {
    return el.className.split(/\s+/).filter((t) => t !== '');
}

describe('device-layout-updater — updateLayoutClasses', () => {
    beforeAll(async () => {
        // IIFE DOMContentLoaded dinleyicisi tamamlansın (boş body'de no-op)
        if (document.readyState === 'loading') {
            await new Promise((resolve) => {
                document.addEventListener('DOMContentLoaded', resolve, { once: true });
            });
        }
    });

    beforeEach(() => {
        document.body.textContent = '';
    });

    it('main: apply(phone) → layout--desktop gider, layout--phone gelir, home-layout KORUNUR', () => {
        const { main } = buildLayout();
        main.className = 'page-home home-layout layout--desktop';

        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');

        expect(tokens(main)).not.toContain('layout--desktop');
        expect(tokens(main)).toContain('layout--phone');
        expect(tokens(main)).toContain('home-layout'); // blok sınıfı korunuyor
        expect(main.getAttribute('data-tier')).toBe('phone');
    });

    it('main: home-layout--phone token’ı strip’lenirken "home-" çöpü KALMAZ (eski regex korupsiyonu)', () => {
        const { main } = buildLayout();
        // Eski durum: desktop uygulanmış → home-layout--wide + layout--desktop
        main.className = 'home-layout home-layout--wide layout--desktop';

        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');

        expect(tokens(main)).not.toContain('home-'); // korupsiyon çöpü yok
        expect(tokens(main)).toContain('home-layout'); // blok korunur
        expect(tokens(main)).toContain('home-layout--phone'); // tier modifier
        expect(tokens(main)).not.toContain('home-layout--wide');
        expect(tokens(main)).not.toContain('layout--desktop');
    });

    it('header: layout--* strip edilir — desktop+phone aynı anda BİRİKMEZ', () => {
        const { header } = buildLayout();
        // Eski hata: header regex'i layout--* strip etmediğinden ikisi birden duruyordu
        header.className = 'site-header site-header--desktop layout--desktop layout--phone';

        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');

        expect(tokens(header)).not.toContain('layout--desktop');
        expect(tokens(header).filter((t) => t.startsWith('layout--'))).toEqual(['layout--phone']);
        expect(tokens(header)).toContain('site-header--phone');
        expect(tokens(header)).not.toContain('site-header--desktop');
    });

    it('footer: layout--* strip edilir — birikim yok, tier modifier doğru', () => {
        const { footer } = buildLayout();
        footer.className = 'footer footer--desktop layout--desktop layout--phone';

        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');

        expect(tokens(footer)).not.toContain('layout--desktop');
        expect(tokens(footer).filter((t) => t.startsWith('layout--'))).toEqual(['layout--phone']);
        expect(tokens(footer)).toContain('footer--mobile'); // tier: phone
        expect(tokens(footer)).toContain('footer--phone'); // device modifier
        expect(tokens(footer)).not.toContain('footer--desktop');
    });

    it('idempotent: apply(phone) iki kez → duplicate modifier YOK', () => {
        const { main, header, footer } = buildLayout();

        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');
        const afterFirst = [tokens(main), tokens(header), tokens(footer)];
        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');
        const afterSecond = [tokens(main), tokens(header), tokens(footer)];

        expect(afterSecond).toEqual(afterFirst);
        for (const list of afterSecond) {
            expect(new Set(list).size).toBe(list.length); // hiç duplicate token yok
        }
    });

    it('desktop → phone → desktop geçişi: hiçbir tarafın diğer cihaz modifier’ı kalmaz', () => {
        const { main, header, footer } = buildLayout();

        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('desktop');
        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('phone');
        window.CoreMusic.DeviceLayoutUpdater.updateLayoutClasses('desktop');

        for (const el of [main, header, footer]) {
            expect(tokens(el)).not.toContain('layout--phone');
        }
        expect(tokens(main)).toContain('layout--desktop');
        expect(tokens(header)).toContain('layout--desktop');
        expect(tokens(footer)).toContain('layout--desktop');
        // home-layout blok sınıfı her adımda korunur
        expect(tokens(main)).toContain('home-layout');
    });
});
