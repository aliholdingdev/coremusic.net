/**
 * music-spa-manager.spec.js — Adım 8/9 API ilk-yükleme akışları
 *
 * Kapsar: GET /api/v1/music/recent (home grid), GET /api/v1/music/history
 * (history listeleri), API hata durumunda PHP render fallback'i, formatDuration.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';

const HEX = 'a1b2c3d4e5f60718293a4b5c6d7e8f90';
const ITEMS = [
    { music_hex: HEX, title: 'Test Parça', artist_name: 'Test Sanatçı', duration_sec: 180 },
    { music_hex: 'f'.repeat(32), title: 'İkinci Parça', artist_name: 'Sanatçı2', duration_sec: 3661 },
];

async function bootManager() {
    await import('../js/music-spa-manager.js');
    return window.MusicSpaManager;
}

function mockFetchOk(payload) {
    global.fetch = vi.fn().mockResolvedValue({
        ok: true,
        json: async () => payload,
    });
}

function mockFetchFail() {
    global.fetch = vi.fn().mockResolvedValue({
        ok: false,
        json: async () => ({}),
    });
}

describe('MusicSpaManager — Adım 8/9 API ilk yükleme', () => {
    beforeEach(() => {
        vi.resetModules();
        document.body.innerHTML = '';
        mockFetchFail();
    });

    it('recent grid API ile tazelenir (kartlar replaceChildren ile yenilenir)', async () => {
        document.body.innerHTML = `
            <section aria-label="En Son Dinlenen Şarkılar">
                <div class="home_song__btn-card-grid"><span class="home-song__mini-card">eski kart</span></div>
            </section>`;
        mockFetchOk({ data: { items: ITEMS } });

        const manager = await bootManager();
        await manager.loadRecentTracksFromApi();

        const cards = document.querySelectorAll('.home-song__mini-card');
        expect(cards).toHaveLength(2);
        expect(cards[0].getAttribute('data-stream')).toBe(`/stream/${HEX}`);
        expect(cards[0].getAttribute('data-title')).toBe('Test Parça');
        expect(cards[0].querySelector('.home-song__mini-card__title').textContent).toBe('Test Parça');
        const subtitles = cards[0].querySelectorAll('.home-song__mini-card__subtitle');
        expect(subtitles[0].textContent).toBe('Test Sanatçı');
        expect(subtitles[1].textContent).toBe('00:03:00');
    });

    it('history embedded liste API ile tazelenir (satır + süre formatı)', async () => {
        document.body.innerHTML = `
            <ul class="history-list__items"><li class="history-list__item">eski satır</li></ul>`;
        mockFetchOk({ data: { items: ITEMS } });

        const manager = await bootManager();
        await manager.loadHistoryFromApi();

        const rows = document.querySelectorAll('.history-list__item');
        expect(rows).toHaveLength(2);
        expect(rows[0].querySelector('.history-row').getAttribute('data-stream')).toBe(`/stream/${HEX}`);
        expect(rows[0].querySelector('.history-row__artist').textContent).toBe('Test Sanatçı');
        expect(rows[1].querySelector('.history-row__duration').textContent).toBe('01:01:01');
    });

    it('wide kolon listesi de API ile tazelenir', async () => {
        document.body.innerHTML = `
            <div class="history-cols"><div class="history-col"><ul class="history-col__items"></ul></div></div>`;
        mockFetchOk({ data: { items: ITEMS } });

        const manager = await bootManager();
        await manager.loadHistoryFromApi();

        const rows = document.querySelectorAll('.history-col__items .history-list__item');
        expect(rows).toHaveLength(2);
        expect(rows[0].querySelector('.history-row--mini')).not.toBeNull();
        expect(rows[0].querySelector('.history-row__title').textContent).toBe('Test Parça');
    });

    it('API hatasında PHP render sessizce korunur (fallback)', async () => {
        document.body.innerHTML = `
            <section aria-label="En Son Dinlenen Şarkılar">
                <div class="home_song__btn-card-grid"><span class="home-song__mini-card">eski kart</span></div>
            </section>`;
        mockFetchFail();

        const manager = await bootManager();
        await manager.loadRecentTracksFromApi();

        const cards = document.querySelectorAll('.home-song__mini-card');
        expect(cards).toHaveLength(1);
        expect(cards[0].textContent).toContain('eski kart');
    });

    it('boş items listesinde PHP render korunur', async () => {
        document.body.innerHTML = `
            <section aria-label="En Son Dinlenen Şarkılar">
                <div class="home_song__btn-card-grid"><span class="home-song__mini-card">eski kart</span></div>
            </section>`;
        mockFetchOk({ data: { items: [] } });

        const manager = await bootManager();
        await manager.loadRecentTracksFromApi();

        expect(document.querySelectorAll('.home-song__mini-card')).toHaveLength(1);
    });

    it('fetch reddedilirse (ağ hatası) hata fırlatmaz, DOM sabit kalır', async () => {
        document.body.innerHTML = `
            <ul class="history-list__items"><li class="history-list__item">eski satır</li></ul>`;
        global.fetch = vi.fn().mockRejectedValue(new Error('network down'));

        const manager = await bootManager();
        await expect(manager.loadHistoryFromApi()).resolves.toBeUndefined();

        expect(document.querySelectorAll('.history-list__item')).toHaveLength(1);
    });

    it('formatDuration — int saniye → HH:MM:SS (PHP render paritesi)', async () => {
        const manager = await bootManager();
        expect(manager.formatDuration(180)).toBe('00:03:00');
        expect(manager.formatDuration(3661)).toBe('01:01:01');
        expect(manager.formatDuration(0)).toBe('00:00:00');
        expect(manager.formatDuration(undefined)).toBe('00:00:00');
    });

    it('sayfada hedef DOM yoksa fetch hiç çağrılmaz', async () => {
        document.body.innerHTML = `<div class="bos-sayfa"></div>`;
        const fetchSpy = vi.fn();
        global.fetch = fetchSpy;

        const manager = await bootManager();
        await manager.loadInitialData();

        expect(fetchSpy).not.toHaveBeenCalled();
    });
});
