import { ERROR_TYPES, ABORT_ERROR_NAME, REQUEST_TYPES } from './config/error-types.js';
import { HEADERS, CONTENT_TYPES } from './config/headers.js';

export default class ContentFetcher {
    #cache;
    #fetcher;
    #logger;
    #activeRequests = new Map();

    constructor(cache, fetcher, logger) {
        this.#cache = cache;
        this.#fetcher = fetcher;
        this.#logger = logger;
    }

    async fetch(target, container) {
        const cached = this.#cache.get(target);
        if (cached) return { responseData: cached, fetchMs: 0, cacheHit: true };

        const ctrl = new AbortController();
        this.#activeRequests.set(target, { type: REQUEST_TYPES.NAVIGATION, controller: ctrl });

        try {
            const start = performance.now();
            const response = await this.#fetcher.fetch(target, { signal: ctrl.signal });
            const contentType = response.headers.get(HEADERS.CONTENT_TYPE) || '';
            const responseData = await ContentFetcher.parseResponse(response, contentType, target, this.#cache, this.#logger);
            this.#activeRequests.delete(target);
            return { responseData, fetchMs: performance.now() - start, cacheHit: false };
        } catch (err) {
            this.#activeRequests.delete(target);
            if (err.name === ABORT_ERROR_NAME) {
                return { responseData: null, fetchMs: 0, cacheHit: false };
            }
            throw err;
        }
    }

    abort(target) {
        const r = this.#activeRequests.get(target);
        if (r) {
            r.controller.abort();
            this.#activeRequests.delete(target);
        }
    }

    cancelNavigations(exclude) {
        for (const [k, r] of this.#activeRequests) {
            if (r.type === REQUEST_TYPES.NAVIGATION && k !== exclude) {
                r.controller.abort();
                this.#activeRequests.delete(k);
            }
        }
    }

    static async parseResponse(response, contentType, target, cache, logger) {
        if (contentType.includes(CONTENT_TYPES.JSON)) {
            let json;
            try {
                json = await response.json();
            } catch {
                return null;
            }
            if (json.error === ERROR_TYPES.FORBIDDEN && json.redirect) {
                return { error: ERROR_TYPES.FORBIDDEN, redirect: json.redirect };
            }
            if (json.error === ERROR_TYPES.NOT_FOUND) {
                return { error: ERROR_TYPES.NOT_FOUND };
            }
            const html = json.container || '';
            cache.set(target, { html, meta: json.meta || {} }, json.meta?.cacheable !== false);
            return { html, meta: json.meta || {}, csrfToken: json.csrf_token };
        }

        const html = await response.text();
        cache.set(target, { html, meta: {} }, true);
        return { html, meta: {} };
    }
}
