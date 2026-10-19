// @vitest-environment jsdom
/**
 * TouchManager.spec — P1-4: init() eklenen document dinleyicileri
 * destroy() ile kaldırılmıyordu (SPA listener sızıntısı).
 *
 * Sözleşme:
 *  - init() (device=embedded) touchstart/end/move ekler.
 *  - destroy() sonraki touchstart longpress timer'ı KURMAZ →
 *    touch:longpress event'i YAYILMAZ.
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import TouchManager from '../../js/features/TouchManager.js';

/**
 * jsdom'da TouchEvent/Touch yok → tanımlı touches/changedTouches'lı sahte olay.
 * @param {string} type
 * @param {Array<{clientX: number, clientY: number}>} touches
 * @returns {Event}
 */
function touchEvent(type, touches) {
    const ev = new Event(type, { bubbles: true, cancelable: true });
    Object.defineProperty(ev, 'touches', { value: touches });
    Object.defineProperty(ev, 'changedTouches', { value: touches });
    return ev;
}

/** @returns {{emit: ReturnType<typeof vi.fn>, on: ReturnType<typeof vi.fn>}} */
function fakeBus() {
    return { emit: vi.fn(), on: vi.fn() };
}

describe('TouchManager.destroy — document dinleyici temizliği (P1-4)', () => {
    beforeEach(() => {
        document.body.replaceChildren();
        window.CoreMusic = { deviceType: 'embedded' };
        vi.useFakeTimers();
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.restoreAllMocks();
        document.body.replaceChildren();
        delete window.CoreMusic;
    });

    it('init sonrası longpress tetiklenir, destroy sonrası TETİKLENMEZ', () => {
        const bus = fakeBus();
        const mgr = new TouchManager(bus);
        mgr.init();

        const card = document.createElement('div');
        card.className = 'media-card';
        document.body.appendChild(card);

        // init durumu: touchstart → 500ms → touch:longpress
        card.dispatchEvent(touchEvent('touchstart', [{ clientX: 10, clientY: 10 }]));
        vi.advanceTimersByTime(TouchManager.LONG_PRESS_MS + 10);
        expect(bus.emit).toHaveBeenCalledWith('touch:longpress', expect.anything());

        bus.emit.mockClear();
        mgr.destroy();

        card.dispatchEvent(touchEvent('touchstart', [{ clientX: 10, clientY: 10 }]));
        vi.advanceTimersByTime(TouchManager.LONG_PRESS_MS + 10);

        expect(bus.emit).not.toHaveBeenCalledWith('touch:longpress', expect.anything());
        expect(bus.emit).not.toHaveBeenCalled();
    });

    it('destroy bekleyen longpress timer’ını da temizler (yayın yok)', () => {
        const bus = fakeBus();
        const mgr = new TouchManager(bus);
        mgr.init();

        const card = document.createElement('div');
        card.className = 'media-card';
        document.body.appendChild(card);

        card.dispatchEvent(touchEvent('touchstart', [{ clientX: 5, clientY: 5 }]));
        // timer kurduruldu, henüz 500ms dolmadan destroy
        mgr.destroy();
        vi.advanceTimersByTime(TouchManager.LONG_PRESS_MS + 50);

        expect(bus.emit).not.toHaveBeenCalledWith('touch:longpress', expect.anything());
    });

    it('device embedded DEĞİLSE init dinleyici eklemez (destroy yine güvenli)', () => {
        window.CoreMusic = { deviceType: 'desktop' };
        const bus = fakeBus();
        const mgr = new TouchManager(bus);
        mgr.init();

        const card = document.createElement('div');
        card.className = 'media-card';
        document.body.appendChild(card);

        card.dispatchEvent(touchEvent('touchstart', [{ clientX: 1, clientY: 1 }]));
        vi.advanceTimersByTime(TouchManager.LONG_PRESS_MS + 10);
        expect(bus.emit).not.toHaveBeenCalled();

        // çift destroy hata üretmez
        expect(() => {
            mgr.destroy();
            mgr.destroy();
        }).not.toThrow();
    });
});
