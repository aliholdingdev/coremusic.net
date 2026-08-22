/**
 * CoreMusic — ScrollManager
 * Route bazlı scroll pozisyonu kaydetme/geri yükleme.
 *
 * @module features/ScrollManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class ScrollManager {
    #eventBus;
    /** @type {Map<string, number>} */
    #positions = new Map();

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    init() {
        this.#saveCurrent();

        window.addEventListener('scroll', () => {
            this.#saveCurrent();
        }, { passive: true });

        this.#eventBus.on('nav:complete', () => {
            this.#restorePosition();
        });
    }

    /** Mevcut URL için scroll pozisyonunu kaydet */
    #saveCurrent() {
        const url = window.location.pathname;
        this.#positions.set(url, window.scrollY || document.documentElement.scrollTop);
    }

    /** Kayıtlı scroll pozisyonunu geri yükle */
    #restorePosition() {
        const url = window.location.pathname;
        const pos = this.#positions.get(url) || 0;
        requestAnimationFrame(() => {
            window.scrollTo(0, pos);
        });
    }

    destroy() {
        this.#positions.clear();
    }
}
