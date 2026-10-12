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
