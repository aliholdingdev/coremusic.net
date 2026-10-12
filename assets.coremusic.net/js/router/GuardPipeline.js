import { isCrossOrigin } from './UrlUtils.js';

// C-F-08: '/403' ve '/error' route'ları route tablosunda YOK (→ sunucu 404).
// Mevcut tek güvenli hedef '/home': oturumsuzsa sunucu AuthGuard zaten /login'e
// yönlendirir. Gerçek 403 sayfası P3'te eklenince bu hedefler güncellenir.
const DEFAULT_REDIRECTS = Object.freeze({ blocked: '/home', error: '/home' });

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
            } catch (err) {
                // C-F-08: sessiz yutma yerine logla — guard istisnası gözlemlenebilir olsun.
                this.#logger?.error?.('GuardPipeline', 'guard_exception', {
                    guard: guard?.name ?? 'unknown',
                    to: ctx?.to ?? null,
                    message: err?.message ?? String(err),
                });
                return { pass: false, redirect: this.#redirects.error, guardMs: performance.now() - start };
            }
        }

        return { pass: true, guardMs: performance.now() - start };
    }
}
