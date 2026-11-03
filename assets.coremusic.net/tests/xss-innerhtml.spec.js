/**
 * xss-innerhtml.spec.js — DOM XSS regresyonu (music-spa-manager.js)
 *
 * Kapsar: API'den gelen değerler şablona girerken kaçış + innerHTML sink'inin
 * DOMParser desenine çevrilmesi (assets CLAUDE.md §7.7 · .ai CLAUDE.md §21).
 * Her test, saldırı yükünün AYRI attribute/element üretmediğini doğrular.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';

const HEX = 'a1b2c3d4e5f60718293a4b5c6d7e8f90';
const ART_PAYLOAD = 'x" onerror="window.__xss=1';
const STREAM_PAYLOAD = 'x" onclick="window.__xss=1';
const PLAYLIST_ID_PAYLOAD = '1" onfocus="window.__xss=1';

function mockFetch(payload) {
    global.fetch = vi.fn().mockResolvedValue({
        ok: true,
        json: async () => payload,
    });
}

async function bootManager() {
    await import('../js/music-spa-manager.js');
    return window.MusicSpaManager;
}

describe('DOM XSS — innerHTML yerine DOMParser + escapeHtml', () => {
    beforeEach(() => {
        vi.resetModules();
        document.body.innerHTML = '';
        delete window.__xss;
        // jsdom'de rAF yoksa updateRecentTracksLive'in fade-in adımı patlar
        if (typeof window.requestAnimationFrame !== 'function') {
            window.requestAnimationFrame = (cb) => setTimeout(cb, 0);
        }
        mockFetch({ success: false });
    });

    it('updateRecentTracksLive: art URL attribute injection yapmaz', async () => {
        document.body.innerHTML = `
            <section aria-label="En Son Dinlenen Şarkılar">
                <div class="home_song__btn-card-grid"></div>
            </section>`;
        const manager = await bootManager();

        manager.updateRecentTracksLive({
            hex: HEX,
            title: 'Parça',
            artist: 'Sanatçı',
            duration: '00:03:00',
            art: ART_PAYLOAD,
        });

        const card = document.querySelector('.home-song__mini-card');
        expect(card).not.toBeNull();

        const imgs = card.querySelectorAll('img');
        const artImg = card.querySelector('.home-song__mini-card__art-img');
        // payload tek img'ın src değeri olarak KALIR (parsed attribute değil)
        expect(artImg.getAttribute('src')).toBe(ART_PAYLOAD);
        expect(artImg.hasAttribute('onerror')).toBe(false);
        // payload ek element/attribute doğurmamış: 4 img, hepsi şablondaki
        expect(imgs).toHaveLength(4);
        expect(card.querySelectorAll('[onerror], [onclick], [onload]')).toHaveLength(0);
        expect(window.__xss).toBeUndefined();
    });

    it('updateHistoryPageLive: streamUrl attribute injection yapmaz (embedded)', async () => {
        document.body.innerHTML = `<ul class="history-list__items"></ul>`;
        const manager = await bootManager();

        manager.updateHistoryPageLive({
            hex: HEX,
            title: 'Parça',
            artist: 'Sanatçı',
            duration: '00:03:00',
            streamUrl: STREAM_PAYLOAD,
        });

        const row = document.querySelector('.history-row');
        expect(row).not.toBeNull();
        expect(row.getAttribute('data-stream')).toBe(STREAM_PAYLOAD);
        expect(row.hasAttribute('onclick')).toBe(false);
        expect(document.querySelectorAll('[onclick], [onerror]')).toHaveLength(0);
        expect(window.__xss).toBeUndefined();
    });

    it('updateHistoryPageLive: wide kolonda da kaçış korunur', async () => {
        document.body.innerHTML = `
            <div class="history-cols"><div class="history-col"><ul class="history-col__items"></ul></div></div>`;
        const manager = await bootManager();

        manager.updateHistoryPageLive({
            hex: HEX,
            title: 'Parça',
            artist: 'Sanatçı',
            duration: '00:03:00',
            streamUrl: STREAM_PAYLOAD,
        });

        const row = document.querySelector('.history-row--mini');
        expect(row).not.toBeNull();
        expect(row.getAttribute('data-stream')).toBe(STREAM_PAYLOAD);
        expect(row.hasAttribute('onclick')).toBe(false);
        expect(window.__xss).toBeUndefined();
    });

    it('openAddToPlaylistModal: playlist_id attribute injection yapmaz', async () => {
        mockFetch({
            success: true,
            data: {
                playlist_shortcuts: [
                    { playlist_id: PLAYLIST_ID_PAYLOAD, name: 'Liste <script>' },
                ],
            },
        });
        const manager = await bootManager();

        await manager.openAddToPlaylistModal({ hex: HEX, title: 'Parça', artist: 'Sanatçı' });

        const modal = document.querySelector('#cm-playlist-modal');
        expect(modal).not.toBeNull();

        const btn = modal.querySelector('.cm-modal-item-btn');
        expect(btn).not.toBeNull();
        expect(btn.getAttribute('data-playlist-id')).toBe(PLAYLIST_ID_PAYLOAD);
        expect(btn.hasAttribute('onfocus')).toBe(false);
        // script elementi sanitizer tarafından düşürülmüş olmalı
        expect(modal.querySelectorAll('script')).toHaveLength(0);
        expect(modal.querySelectorAll('[onfocus], [onclick], [onerror]')).toHaveLength(0);
        expect(window.__xss).toBeUndefined();
    });

    it('modal: şarkı adı HTML olarak basılmaz (textContent)', async () => {
        mockFetch({ success: true, data: { playlist_shortcuts: [] } });
        const manager = await bootManager();

        await manager.openAddToPlaylistModal({
            hex: HEX,
            title: '<img src=x onerror="window.__xss=1">',
            artist: '</strong><script>window.__xss=1</script>',
        });

        const modal = document.querySelector('#cm-playlist-modal');
        expect(modal.querySelector('strong').textContent)
            .toBe('<img src=x onerror="window.__xss=1">');
        expect(modal.querySelectorAll('img, script')).toHaveLength(0);
        expect(window.__xss).toBeUndefined();
    });
});