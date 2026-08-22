/**
 * CoreMusic — CardManager
 * Media card etkileşimleri. Event delegation pattern.
 *
 * @module features/CardManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class CardManager {
    #eventBus;

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    init() {
        this.#bindDelegation();
    }

    /** Tek listener ile tüm kart tıklamalarını yakala */
    #bindDelegation() {
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.media-card, .mini-card');
            if (!card) return;

            const data = {
                id: card.dataset.id || null,
                type: card.classList.contains('mini-card') ? 'mini' : 'media',
                title: card.querySelector('.media-card__title, .mini-card__title')?.textContent?.trim() || '',
                artist: card.querySelector('.media-card__meta, .mini-card__artist')?.textContent?.trim() || '',
                element: card,
            };

            this.#eventBus.emit('card:click', data);
        });
    }

    destroy() {
        /* Event delegation listener DOM unload'da temizlenir */
    }
}
