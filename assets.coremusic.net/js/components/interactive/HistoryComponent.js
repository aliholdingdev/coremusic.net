/**
 * HistoryComponent — Geçmiş sayfası etkileşimi (cm-history)
 *
 * Sorumluluklar:
 *   1) "Geçmiş'te ara" input'u → satır filtresi (textContent tabanlı, innerHTML YOK)
 *   2) Geri/İleri butonları → window.history (data-action delegation)
 *
 * Mount: ComponentLoader — pages/history.php root element'i
 *        data-cm-component="cm-history" (SPA navigasyonunda MutationObserver ile otomatik)
 * Test : tests/components/history.spec.js (vitest + jsdom)
 *
 * @package CoreMusic\Components\Interactive
 * @version 1.0.0
 */
import ComponentBase from '../base/ComponentBase.js';

export default class HistoryComponent extends ComponentBase {
    /** @returns {{query: string}} */
    defaultState() {
        return { query: '' };
    }

    mount() {
        const input = this.$('.history-toolbar__input');
        if (input) {
            this.on(input, 'input', () => {
                const value = input.value;
                this.setState({ query: value });
                this.#filter(value);
            });
        }

        if (this.el) {
            this.on(this.el, 'click', (event) => {
                const target = event.target instanceof Element ? event.target : null;
                if (!target) return;

                if (target.closest('[data-action="history-back"]')) {
                    window.history.back();
                    return;
                }
                if (target.closest('[data-action="history-forward"]')) {
                    window.history.forward();
                }
            });
        }

        void this.#refresh();
    }

    /**
     * Adım 09 — hibrit tazeleme (initial PHP render + JS fetch).
     *
     * Sayfa açılışta PHP render'ıyla gelir (DB/seed fallback); buradan
     * v2 API'den fresh veri çekilir ve satırlar tazelenir. GET olduğu için
     * CSRF header gerekmez (CsrfMiddleware yalnız state-changing çağrıları
     * kapsar — Adım 08). Cookie'ler gönderilir: `credentials:'include'`.
     *
     * Herhangi bir hata sessizce yutulur: fallback her zaman PHP render'ıdır
     * (konsol temiz, sayfa boşalmaz — §9: hata değil).
     *
     * @returns {Promise<void>}
     */
    async #refresh() {
        try {
            const res = await fetch('/api/v1/music/history?page=1&per_page=50', {
                credentials: 'include',
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) return;

            const payload = await res.json();
            if (!payload?.success || !Array.isArray(payload?.data?.items)) return;

            this.#render(payload.data.items);
        } catch {
            /* network/parse — PHP initial render fallback kalır */
        }
    }

    /**
     * Adım 09 — API items ile mevcut satırları tazeler (textContent — innerHTML YOK).
     *
     * Yerleşim DEĞİŞMEZ (mockup gate — yalnız veri bağlanır): satır/kolon
     * yapısı DOM'dan okunur; item'lar PHP wide round-robin ile aynı sırayla
     * dağıtılır (wide: 6 kolon × 33 satır, embedded: tek liste).
     *
     * @param {Array<{music_hex?: string, title?: string, artist_name?: string, duration_sec?: number}>} items
     */
    #render(items) {
        if (items.length === 0) return;

        const colLists = this.$$('.history-col__items');
        const lists = colLists.length > 0 ? colLists : this.$$('.history-list__items');
        if (lists.length === 0) return;

        const rows = [];
        lists.forEach((list) => {
            list.querySelectorAll('.history-list__item').forEach((li) => {
                if (li instanceof HTMLElement) rows.push(li);
            });
        });
        if (rows.length === 0) return;

        rows.forEach((li, index) => {
            const item = items[index % items.length];
            const row = li.querySelector('.history-row');
            if (!(row instanceof HTMLElement)) return;

            const title = String(item.title ?? '');
            const artist = String(item.artist_name ?? '');

            const titleEl = row.querySelector('.history-row__title');
            if (titleEl) titleEl.textContent = title;

            const artistEl = row.querySelector('.history-row__artist');
            if (artistEl) artistEl.textContent = artist;

            const durationEl = row.querySelector('.history-row__duration');
            if (durationEl) durationEl.textContent = this.#formatDuration(item.duration_sec);

            /* Stream kontratı: /stream/{32hex} — footer.init.js + HomeSongButton ile aynı */
            const hex = String(item.music_hex ?? '').toLowerCase();
            if (/^[0-9a-f]{32}$/.test(hex)) {
                row.setAttribute('data-stream', '/stream/' + hex);
            } else {
                row.removeAttribute('data-stream');
            }

            row.setAttribute('aria-label', `Oynat: ${title} — ${artist}`);
        });

        this.emit('refresh', { count: items.length, rows: rows.length });
    }

    /**
     * HH:MM:SS biçimlendirme — history.php `$formatDuration` ile aynı çıktı.
     *
     * @param {unknown} seconds dinleme süresi (saniye)
     * @returns {string}
     */
    #formatDuration(seconds) {
        const total = Math.max(0, Math.floor(Number(seconds) || 0));
        const h = Math.floor(total / 3600);
        const m = Math.floor((total % 3600) / 60);
        const s = total % 60;
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(h)}:${pad(m)}:${pad(s)}`;
    }

    /**
     * Satırları sorguya göre filtreler; eşleşmeyen <li> gizlenir.
     * Boş sorgu = tüm satırlar görünür.
     *
     * @param {string} raw ham input değeri
     */
    #filter(raw) {
        const query = String(raw ?? '').trim().toLocaleLowerCase('tr');
        const items = this.$$('.history-list__item');
        let visible = 0;

        items.forEach((item) => {
            const row = item.querySelector('.history-row');
            const text = (row?.textContent ?? '').toLocaleLowerCase('tr');
            const match = query === '' || text.includes(query);
            item.hidden = !match;
            if (match) visible += 1;
        });

        const empty = this.$('.history-list__empty');
        if (empty) {
            empty.hidden = visible > 0;
        }

        this.emit('filter', { query, visible });
    }
}
