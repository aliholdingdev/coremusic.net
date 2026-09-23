/**
 * CoreMusic — SPARouterAdapter
 * Router.js bridge. Mevcut SPA router'ı modüler sisteme bağlar.
 *
 * @module router/SPARouterAdapter
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class SPARouterAdapter {
    #eventBus;
    /** @type {object|null} */
    #router = null;

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    get router() { return this.#router; }

    init() {
        // Mevcut Router.js modülü varsa onu kullan
        if (window.CoreMusic?.Router) {
            this.#router = window.CoreMusic.Router;
            this.#bridgeEvents();
            return;
        }

        // Router.js henüz yüklenmediyse basit SPA adapter
        this.#initSimpleRouter();
    }

    /** Mevcut Router.js ile event bridge */
    #bridgeEvents() {
        this.#eventBus.on('nav:navigate', (url) => {
            this.#router.navigate(url);
        });
    }

    /** Basit SPA navigasyon — Router.js yüklenene kadar */
    #initSimpleRouter() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;

            // data-no-spa ise normal navigasyon
            if (link.hasAttribute('data-no-spa')) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('http') || href.startsWith('#') || href.startsWith('mailto:')) return;

            e.preventDefault();
            this.navigate(href);
        });

        window.addEventListener('popstate', () => {
            this.#loadPage(window.location.pathname, false);
        });
    }

    /**
     * Sayfaya navigasyon
     * @param {string} url
     * @param {boolean} pushState — history.pushState eklesin mi
     */
    async navigate(url, pushState = true) {
        await this.#loadPage(url, pushState);
    }

    /** Sayfayı fetch ile yükle, DOM'u güncelle */
    async #loadPage(url, pushState = true) {
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Main content'i güncelle
            const newMain = doc.querySelector('main');
            const currentMain = document.querySelector('main');
            if (newMain && currentMain) {
                currentMain.replaceWith(newMain);
            }

            // Header güncelle
            const newHeader = doc.querySelector('header');
            const currentHeader = document.querySelector('header');
            if (newHeader && currentHeader) {
                currentHeader.replaceWith(newHeader);
            }

            // Footer güncelle
            const newFooter = doc.querySelector('footer');
            const currentFooter = document.querySelector('footer');
            if (newFooter && currentFooter) {
                currentFooter.replaceWith(newFooter);
            }

            // Title güncelle
            const newTitle = doc.querySelector('title');
            if (newTitle) document.title = newTitle.textContent;

            // History
            if (pushState) {
                history.pushState(null, '', url);
            }

            this.#eventBus.emit('nav:complete', { url });
        } catch (err) {
            console.error('[SPARouterAdapter] Navigation error:', err);
            this.#eventBus.emit('nav:error', { url, error: err });
        }
    }

    destroy() {
        this.#router = null;
    }
}
