// @vitest-environment jsdom
/**
 * oauth-manager.spec — P1-3: popup null → TypeError + 60sn lock.
 *
 * Sözleşme:
 *  - window.open popup engellendiğinde null döner → `popup.closed`/`popup.location`
 *    üzerinden TypeError ÜRETİLMEZ.
 *  - 60sn timeout/poll kurulmaz: connect() hemen {success:false, message} ile
 *    döner → caller'daki buton kilidi (disabled) serbest kalır, kullanıcı
 *    "Popup engellendi" mesajını görür.
 * Kural: innerHTML YASAK.
 */
import { describe, it, expect, afterEach, vi } from 'vitest';
import { createRequire } from 'node:module';

// oauth-manager.js klasik script (module.exports guard'lı) — ESM export yok.
const require = createRequire(import.meta.url);
/** @type {{connect: (p: string) => Promise<{success: boolean, message?: string}>}} */
const OAuthManager = require('../js/oauth-manager.js');

/** @type {Array<() => void>} */
const restoreFns = [];

function restoreAll() {
    while (restoreFns.length > 0) restoreFns.pop()();
    vi.restoreAllMocks();
    vi.unstubAllGlobals();
    vi.useRealTimers();
}

/** window.open → null (popup blocker) */
function mockPopupBlocked() {
    const spy = vi.spyOn(window, 'open').mockReturnValue(null);
    restoreFns.push(() => spy.mockRestore());
    return spy;
}

/** fetch → başarılı connect yanıtı (redirect URL var) */
function mockConnectFetch() {
    const spy = vi.fn().mockResolvedValue({
        ok: true,
        json: async () => ({ success: true, redirect: 'https://provider.example/oauth/authorize' }),
    });
    vi.stubGlobal('fetch', spy);
    restoreFns.push(() => vi.unstubAllGlobals());
    return spy;
}

describe('OAuthManager.connect — popup engellendi (P1-3)', () => {
    afterEach(restoreAll);

    it('window.open null dönerse TypeError ATMAZ ve hemen {success:false} döner', async () => {
        const openSpy = mockPopupBlocked();
        const fetchSpy = mockConnectFetch();

        const started = performance.now();
        let resolved = null;
        let threw = null;
        try {
            resolved = await OAuthManager.connect('spotify');
        } catch (error) {
            threw = error;
        }
        const elapsedMs = performance.now() - started;

        expect(threw).toBeNull();
        expect(fetchSpy).toHaveBeenCalledOnce();
        expect(openSpy).toHaveBeenCalledOnce();
        expect(resolved).toEqual(expect.objectContaining({ success: false }));
        expect(String(resolved.message)).toMatch(/popup/i);
        // 60sn poll/timeout zincirine girilmedi — anında döner
        expect(elapsedMs).toBeLessThan(1000);
    });

    it('popup null iken interval/timeout KURULMAZ (60sn kilit tamamen serbest)', async () => {
        vi.useFakeTimers();
        mockPopupBlocked();
        mockConnectFetch();

        const pending = OAuthManager.connect('github');
        const result = await pending;

        // Ne 500ms poll interval'ı ne de 60sn timeout'ı oluşturulmuş olmalı
        expect(vi.getTimerCount()).toBe(0);

        // İleri sarılırsa da hiçbir timer patlamaz
        await vi.advanceTimersByTimeAsync(61000);
        expect(result.success).toBe(false);
        expect(vi.getTimerCount()).toBe(0);
    });

    it('popup AÇIKSA (null değil) akış eskisi gibi poll eder — regresyon değil', async () => {
        vi.useFakeTimers();
        // closed=true → ilk 500ms poll'de kapanma saptanır
        const popup = { closed: true, location: { href: 'https://provider.example/authorize' }, close() {}, opener: window };
        const openSpy = vi.spyOn(window, 'open').mockReturnValue(popup);
        restoreFns.push(() => openSpy.mockRestore());
        // 1. çağrı: connect (redirect) · 2. çağrı: bağlantı durumu kontrolü
        const fetchSpy = vi.fn()
            .mockResolvedValueOnce({
                ok: true,
                json: async () => ({ success: true, redirect: 'https://provider.example/oauth/authorize' }),
            })
            .mockResolvedValue({
                ok: true,
                json: async () => ({ success: true, connections: [] }),
            });
        vi.stubGlobal('fetch', fetchSpy);
        restoreFns.push(() => vi.unstubAllGlobals());

        const pending = OAuthManager.connect('spotify');

        // 500ms poll → kapanma saptanır → 1000ms sonra status fetch'i
        await vi.advanceTimersByTimeAsync(2000);
        const result = await pending;

        expect(result).toBeDefined();
        // status kontrolü 2. fetch çağrısı üretir (poll gerçekten çalıştı)
        expect(fetchSpy.mock.calls.length).toBeGreaterThanOrEqual(2);
    });
});
