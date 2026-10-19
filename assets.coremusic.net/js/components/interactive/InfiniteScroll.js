/**
 * InfiniteScroll — Sonsuz kaydırma.
 *
 * IntersectionObserver ile sentinel element'i izler.
 * Yeni sayfa yükleme callback'i async destekler.
 *
 * HTML contract:
 * <div class="cm-infinite-scroll" data-cm-component="cm-infinite-scroll">
 *   <div class="cm-infinite-scroll__list">...</div>
 *   <div class="cm-infinite-scroll__sentinel" aria-hidden="true"></div>
 *   <div class="cm-infinite-scroll__loader" hidden>Yükleniyor...</div>
 * </div>
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

export default class InfiniteScroll extends ComponentBase {
    /** @type {HTMLElement|null} */
    #sentinel = null;

    /** @type {IntersectionObserver|null} */
    #observer = null;

    /** @type {boolean} */
    #isLoading = false;

    /** @type {boolean} */
    #hasMore = true;

    /** @type {number} */
    #page = 1;

    /** @type {number} px cinsinden tetikleme mesafesi */
    #threshold = 200;

    /** @type {Function|null} Async loadMore callback */
    #loadCallback = null;

    /**
     * @param {HTMLElement} element
     * @param {object} [options]
     * @param {number} [options.threshold=200] — Tetikleme mesafesi (px)
     * @param {Function} [options.loadMore] — async (page) => items[]
     */
    constructor(element, options = {}) {
        super(element, options);
        this.#threshold = options.threshold ?? 200;
        this.#loadCallback = options.loadMore ?? null;
    }

    defaultState() {
        return { page: 1, isLoading: false, hasMore: true };
    }

    init() {
        this.#sentinel = this.$('.cm-infinite-scroll__sentinel');
    }

    mount() {
        if (!this.#sentinel) return;

        this.#observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !this.#isLoading && this.#hasMore) {
                        this.loadMore();
                    }
                });
            },
            { rootMargin: `0px 0px ${this.#threshold}px 0px` }
        );

        this.#observer.observe(this.#sentinel);
    }

    /**
     * Manuel tetikleme.
     */
    async loadMore() {
        if (this.#isLoading || !this.#hasMore) return;

        this.#isLoading = true;
        this.setState({ isLoading: true });

        const loader = this.$('.cm-infinite-scroll__loader');
        if (loader) loader.hidden = false;

        this.emit('cm:infinite-scroll:load', { page: this.#page });

        try {
            if (this.#loadCallback) {
                const items = await this.#loadCallback(this.#page);
                if (!items || items.length === 0) {
                    this.end();
                    return;
                }
            }

            this.#page++;
            this.setState({ page: this.#page, isLoading: false });
            this.emit('cm:infinite-scroll:loaded', { page: this.#page });
        } catch (error) {
            console.error('[InfiniteScroll] Load error:', error);
            this.setState({ isLoading: false });
        } finally {
            this.#isLoading = false;
            if (loader) loader.hidden = true;
        }
    }

    /**
     * Sayfa numarasını sıfırlar.
     */
    reset() {
        this.#page = 1;
        this.#hasMore = true;
        this.#isLoading = false;
        this.setState({ page: 1, isLoading: false, hasMore: true });
    }

    /**
     * Yüklemeyi durdurur (son sayfa).
     */
    end() {
        this.#hasMore = false;
        this.setState({ hasMore: false });
        this.#observer?.disconnect();
        this.emit('cm:infinite-scroll:end');
    }

    destroy() {
        this.#observer?.disconnect();
        this.#observer = null;
        this.#sentinel = null;
        this.#loadCallback = null;
        super.destroy();
    }
}
