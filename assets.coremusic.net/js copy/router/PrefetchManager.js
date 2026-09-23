import { HEADERS, CONTENT_TYPES } from './config/headers.js';
import { ABORT_ERROR_NAME, REQUEST_TYPES } from './config/error-types.js';
import ContentFetcher from './ContentFetcher.js';

export default class PrefetchManager {
    #cache;
    #fetcher;
    #logger;
    #prefetching = new Set();
    #activeRequests = new Map();
    #maxPrefetch;

    constructor(cache, fetcher, logger, maxPrefetch = 2) {
        this.#cache = cache;
        this.#fetcher = fetcher;
        this.#logger = logger;
        this.#maxPrefetch = maxPrefetch;
    }

    async prefetch(url) {
        if (this.#prefetching.has(url) || this.#cache.get(url) || this.#prefetching.size >= this.#maxPrefetch) return;

        this.#prefetching.add(url);
        const ctrl = new AbortController();
        this.#activeRequests.set(url, { type: REQUEST_TYPES.PREFETCH, controller: ctrl });

        try {
            const r = await this.#fetcher.fetch(url, { signal: ctrl.signal });
            if (r.ok) {
                const ct = r.headers?.get(HEADERS.CONTENT_TYPE) || '';
                await ContentFetcher.parseResponse(r, ct, url, this.#cache, this.#logger);
            }
        } catch (err) {
            if (err?.name !== ABORT_ERROR_NAME) {
                this.#logger?.debug('PrefetchManager', 'prefetch_failed', { url });
            }
        } finally {
            this.#prefetching.delete(url);
            this.#activeRequests.delete(url);
        }
    }

    abortAll() {
        for (const [, r] of this.#activeRequests) {
            if (r.type === REQUEST_TYPES.PREFETCH) r.controller.abort();
        }
        this.#activeRequests.clear();
    }
}
