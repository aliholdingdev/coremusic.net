import { normalizeUrl, isSameOrigin } from './UrlUtils.js';

export default class RouterEventManager {
    #listeners = new Map();
    #hoverTimer = null;

    bind(callbacks) {
        const { onNavigate, onPrefetch, onOffline, onOnline, onError, onUnhandledRejection } = callbacks;

        this.#addListener(window, 'popstate', () => onNavigate(window.location.pathname + window.location.search, false));
        this.#addListener(document, 'click', (e) => this.#handleClick(e, onNavigate));
        this.#addListener(document, 'mouseenter', (e) => this.#handleHover(e, onPrefetch), true);
        this.#addListener(window, 'offline', () => onOffline());
        this.#addListener(window, 'online', () => onOnline());
        this.#addListener(window, 'error', (e) => onError(e));
        this.#addListener(window, 'unhandledrejection', (e) => onUnhandledRejection(e));
    }

    unbind() {
        for (const [, e] of this.#listeners) {
            e.target.removeEventListener(e.type, e.handler, e.options);
        }
        this.#listeners.clear();
        if (this.#hoverTimer) clearTimeout(this.#hoverTimer);
    }

    #addListener(target, type, handler, options) {
        target.addEventListener(type, handler, options);
        this.#listeners.set(handler, { target, type, handler, options });
    }

    #handleClick(e, onNavigate) {
        const link = e.target.closest('a[href]');
        if (!link) return;
        if (!isSameOrigin(link.href)) return;
        if (link.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        if (link.hasAttribute('download') || link.getAttribute('rel') === 'external' || link.hasAttribute('data-no-spa')) return;
        e.preventDefault();
        onNavigate(normalizeUrl(link.href), true);
    }

    #handleHover(e, onPrefetch) {
        const link = e.target?.closest?.('a[href]');
        if (!link || !isSameOrigin(link.href)) return;
        if (this.#hoverTimer) clearTimeout(this.#hoverTimer);
        this.#hoverTimer = setTimeout(() => onPrefetch(normalizeUrl(link.href)), 100);
    }
}
