// @vitest-environment jsdom
/**
 * history.spec.js — HistoryComponent unit tests
 *
 * Coverage:
 * ✓ Mount (input + toolbar delegation bağlanır)
 * ✓ Arama filtresi (eşleşen satır görünür, eşleşmeyen gizlenir)
 * ✓ Boş sorgu = tüm satırlar görünür
 * ✓ Sonuç yoksa empty state görünür
 * ✓ cm:filter event'i yayınlanır
 * ✓ WCAG: filtre gizleme hidden attribute ile (ARIA live ready)
 *
 * @requires vitest + jsdom
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import HistoryComponent from '../../js/components/interactive/HistoryComponent.js';

/** @type {HistoryComponent|null} */
let component = null;

/** Geçmiş sayfası DOM iskeleti (pages/history.php ile aynı seçiciler) */
function buildHistoryDom() {
    document.body.innerHTML = `
        <div class="page-history page-layout" data-cm-component="cm-history">
            <div class="history-toolbar">
                <button type="button" class="history-toolbar__btn" data-action="history-back" aria-label="Geri"></button>
                <button type="button" class="history-toolbar__btn" data-action="history-forward" aria-label="İleri"></button>
                <input class="history-toolbar__input" type="search" aria-label="Geçmiş'te ara">
            </div>
            <ul class="history-list__items">
                <li class="history-list__item">
                    <a class="history-row" href="/playlist" data-no-spa>
                        <span class="history-row__title">Göksel - Sevil Neşelen</span>
                        <span class="history-row__artist">Göksel</span>
                        <span class="history-row__duration">00:03:05</span>
                    </a>
                </li>
                <li class="history-list__item">
                    <a class="history-row" href="/playlist" data-no-spa>
                        <span class="history-row__title">Barış Manco - Gulpembe</span>
                        <span class="history-row__artist">Barış Manco</span>
                        <span class="history-row__duration">00:04:01</span>
                    </a>
                </li>
            </ul>
            <p class="history-list__empty" hidden>Aramanızla eşleşen kayıt bulunamadı.</p>
        </div>`;

    return document.querySelector('.page-history');
}

function mountComponent() {
    const root = buildHistoryDom();
    component = new HistoryComponent(root);
    component.init();
    component.mount();
    return root;
}

function typeQuery(root, value) {
    const input = root.querySelector('.history-toolbar__input');
    input.value = value;
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

const visibleTitles = (root) =>
    [...root.querySelectorAll('.history-list__item')]
        .filter((item) => !item.hidden)
        .map((item) => item.querySelector('.history-row__title').textContent);

describe('HistoryComponent', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
    });

    it('mount edilir ve başlangıçta tüm satırlar görünür', () => {
        const root = mountComponent();
        expect(visibleTitles(root)).toHaveLength(2);
        expect(root.querySelector('.history-list__empty').hidden).toBe(true);
    });

    it('arama sorgusu eşleşen satırı korur, eşleşmeyeni gizler', () => {
        const root = mountComponent();
        typeQuery(root, 'göksel');

        expect(visibleTitles(root)).toEqual(['Göksel - Sevil Neşelen']);
        expect(root.querySelectorAll('.history-list__item')[1].hidden).toBe(true);
    });

    it('sanatçı adıyla da filtreler (metin içerik eşleşmesi)', () => {
        const root = mountComponent();
        typeQuery(root, 'manco');

        expect(visibleTitles(root)).toEqual(['Barış Manco - Gulpembe']);
    });

    it('boş sorgu tüm satırları geri getirir', () => {
        const root = mountComponent();
        typeQuery(root, 'göksel');
        typeQuery(root, '');

        expect(visibleTitles(root)).toHaveLength(2);
        expect(root.querySelector('.history-list__empty').hidden).toBe(true);
    });

    it('sonuç yoksa empty state görünür', () => {
        const root = mountComponent();
        typeQuery(root, 'bu-şarkı-yok-99');

        expect(visibleTitles(root)).toHaveLength(0);
        expect(root.querySelector('.history-list__empty').hidden).toBe(false);
    });

    it("cm:filter event'i query + visible sayısını taşır", () => {
        const root = mountComponent();
        const events = [];
        root.addEventListener('cm:filter', (e) => events.push(e.detail));

        typeQuery(root, 'göksel');

        expect(events).toHaveLength(1);
        expect(events[0]).toEqual({ query: 'göksel', visible: 1 });
    });

    it('büyük/küçük harf ve Türkçe karakter duyarsız eşleşir', () => {
        const root = mountComponent();
        typeQuery(root, 'GÖKSEL');

        expect(visibleTitles(root)).toEqual(['Göksel - Sevil Neşelen']);
    });

    /* ═══════════════ Adım 09 — hibrit API fetch ═══════════════ */

    /** Geçerli 32 hex music id */
    const MUSIC_HEX = '018f3a5b6c7d8e9f0123456789abcdef';

    it('başarılı fetch satırları API verisiyle tazeler (textContent + data-stream)', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({
                success: true,
                data: {
                    items: [
                        { music_hex: MUSIC_HEX, title: 'API Şarkı', artist_name: 'API Sanatçı', duration_sec: 3661 },
                        { music_hex: 'not-hex', title: 'İkinci', artist_name: 'Kimse', duration_sec: 65 },
                    ],
                    total: 2, page: 1, per_page: 50, from: null, to: null,
                },
            }),
        }));

        const root = mountComponent();
        await vi.waitFor(() => {
            expect(root.querySelector('.history-row__title').textContent).toBe('API Şarkı');
        });

        const rows = root.querySelectorAll('.history-list__item');
        expect(rows[0].querySelector('.history-row__artist').textContent).toBe('API Sanatçı');
        expect(rows[0].querySelector('.history-row__duration').textContent).toBe('01:01:01');
        expect(rows[0].querySelector('.history-row').getAttribute('data-stream'))
            .toBe('/stream/' + MUSIC_HEX);
        /* 2 item → 2 satır round-robin (kapasite korunur) */
        expect(rows[1].querySelector('.history-row__title').textContent).toBe('İkinci');
        /* geçersiz hex → data-stream yok (footer kontratı) */
        expect(rows[1].querySelector('.history-row').hasAttribute('data-stream')).toBe(false);
    });

    it('aria-label PHP kontratıyla güncellenir (Oynat: başlık — sanatçı)', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({
                success: true,
                data: { items: [{ music_hex: MUSIC_HEX, title: 'T', artist_name: 'A', duration_sec: 1 }] },
            }),
        }));

        const root = mountComponent();
        await vi.waitFor(() => {
            expect(root.querySelector('.history-row').getAttribute('aria-label')).toBe('Oynat: T — A');
        });
    });

    it('fetch 500 dönerse PHP initial render korunur (satırlar değişmez)', async () => {
        vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: false, status: 500 }));

        const root = mountComponent();
        await vi.waitFor(() => {
            expect(fetch).toHaveBeenCalledTimes(1);
        });

        expect(visibleTitles(root)).toEqual(['Göksel - Sevil Neşelen', 'Barış Manco - Gulpembe']);
    });

    it('ağ hatası sessizce yutulur (konsol temiz, sayfa bozulmaz)', async () => {
        vi.stubGlobal('fetch', vi.fn().mockRejectedValue(new Error('offline')));

        const root = mountComponent();
        await vi.waitFor(() => {
            expect(fetch).toHaveBeenCalledTimes(1);
        });

        expect(visibleTitles(root)).toHaveLength(2);
    });
});
