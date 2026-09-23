import { isCrossOrigin } from './UrlUtils.js';

const DEFAULT_REDIRECTS = Object.freeze({ blocked: '/403', error: '/error' });

export default class GuardPipeline {
    #guards = [];
    #guardNames = new Set();
    #logger;
    #redirects;

    constructor(logger = null, redirects = {}) {
        this.#logger = logger || { info() {}, error() {}, warn() {}, debug() {} };
        this.#redirects = { ...DEFAULT_REDIRECTS, ...redirects };
    }

    register(guardFn) {
        if (typeof guardFn !== 'function') throw new Error('Guard must be a function');
        const key = guardFn.name || `guard_${this.#guards.length}`;
        if (this.#guardNames.has(key)) return;
        this.#guardNames.add(key);
        this.#guards.push(guardFn);
    }

    async run(ctx, isProtected = false) {
        const start = performance.now();

        if (!isProtected && this.#guards.length === 0) return { pass: true };

        for (const guard of this.#guards) {
            try {
                const result = await guard(ctx);
                if (result === false) {
                    return { pass: false, redirect: this.#redirects.blocked, guardMs: performance.now() - start };
                }
                if (result?.redirect) {
                    return { pass: false, redirect: result.redirect, crossOrigin: isCrossOrigin(result.redirect), guardMs: performance.now() - start };
                }
            } catch {
                return { pass: false, redirect: this.#redirects.error, guardMs: performance.now() - start };
            }
        }

        return { pass: true, guardMs: performance.now() - start };
    }
}
