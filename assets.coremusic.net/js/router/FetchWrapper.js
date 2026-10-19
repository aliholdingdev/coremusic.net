import { HEADERS, AJAX_VALUE, CONTENT_TYPES } from './config/headers.js';
import { ABORT_ERROR_NAME } from './config/error-types.js';
import { combineSignals, createTimeout } from './config/signal-utils.js';

export default class FetchWrapper {
    static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 });
    #controller = null;
    #csrfSync;
    #logger;

    constructor(logger, csrfSync = null) { this.#logger = logger; this.#csrfSync = csrfSync; }

    async fetch(url, options = {}) {
        this.abort();
        this.#controller = new AbortController();
        const { signal: timeoutSignal, clear: clearTimeoutFn } = createTimeout(FetchWrapper.CONFIG.TIMEOUT_MS);
        const combinedSignal = options.signal
            ? combineSignals(options.signal, this.#controller.signal, timeoutSignal)
            : combineSignals(this.#controller.signal, timeoutSignal);
        try { return await this.#fetchWithRetry(url, combinedSignal); }
        finally { clearTimeoutFn(); this.#controller = null; }
    }

    async #fetchWithRetry(url, signal) {
        let lastError = null;
        for (let attempt = 0; attempt <= FetchWrapper.CONFIG.MAX_RETRIES; attempt++) {
            if (signal.aborted) throw new DOMException('Aborted', ABORT_ERROR_NAME);
            try {
                const response = await this.#doFetch(url, signal, attempt);
                if (!response.ok && response.status >= 500 && attempt < FetchWrapper.CONFIG.MAX_RETRIES) {
                    await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
                    continue;
                }
                return response;
            } catch (err) {
                lastError = err;
                if (err.name === ABORT_ERROR_NAME) throw err;
                if (attempt < FetchWrapper.CONFIG.MAX_RETRIES) await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
            }
        }
        throw lastError || new Error('Fetch failed');
    }

    abort() { if (this.#controller) { this.#controller.abort(); this.#controller = null; } }

    async #doFetch(url, signal, attempt) {
        const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };
        const csrfHeader = this.#csrfSync?.getHeader();
        if (csrfHeader) Object.assign(headers, csrfHeader);
        const traceId = this.#logger?.traceId;
        if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
        return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
    }

    #delayWithAbort(ms, signal) {
        return new Promise((resolve, reject) => {
            if (signal.aborted) { reject(new DOMException('Aborted', ABORT_ERROR_NAME)); return; }
            const id = setTimeout(() => { signal.removeEventListener('abort', onAbort); resolve(); }, ms);
            const onAbort = () => { clearTimeout(id); reject(new DOMException('Aborted', ABORT_ERROR_NAME)); };
            signal.addEventListener('abort', onAbort, { once: true });
        });
    }
}
