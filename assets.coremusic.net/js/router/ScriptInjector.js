const NONCE_SELECTOR = 'meta[name="csp-nonce"]';

export default class ScriptInjector {
    #logger;

    constructor(logger) {
        this.#logger = logger;
    }

    reinjectWithNonce(container) {
        if (!container) return;

        const nonce = document.querySelector(NONCE_SELECTOR)?.getAttribute('content');
        if (!nonce) return;

        for (const old of container.querySelectorAll('script[src]')) {
            const s = document.createElement('script');
            s.nonce = nonce;
            for (const attr of old.attributes) {
                s.setAttribute(attr.name, attr.value);
            }
            old.parentNode.replaceChild(s, old);
        }
    }
}
