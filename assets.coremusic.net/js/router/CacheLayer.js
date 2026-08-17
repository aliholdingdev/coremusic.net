import { normalizeCacheKey } from './UrlUtils.js';
export default class CacheLayer {
    static DEFAULT_TTL = 600;
    static MAX_ENTRIES = 100;
    static TTL_MAP = { static: 3600, user: 120, dynamic: 60, default: 600 };
    #store = new Map();
    #tags = new Map();
    #logger;
    #maxEntries;
    #defaultTtl;
    #ttlMap;
    #accessCounter = 0;

    constructor(options = {}, logger = null) {
        const opts = (options && typeof options === 'object' && !Array.isArray(options)) ? options : {};
        this.#maxEntries = Number.isInteger(opts.maxSize) && opts.maxSize > 0 ? opts.maxSize : CacheLayer.MAX_ENTRIES;
        this.#defaultTtl = Number.isInteger(opts.defaultTtl) && opts.defaultTtl > 0 ? opts.defaultTtl : CacheLayer.DEFAULT_TTL;
        this.#ttlMap = (opts.ttlMap && typeof opts.ttlMap === 'object') ? { ...CacheLayer.TTL_MAP, ...opts.ttlMap } : { ...CacheLayer.TTL_MAP };
        this.#logger = logger && typeof logger.debug === 'function' ? logger : { debug() {}, info() {}, warn() {}, error() {} };
    }

    get(url) {
        const key = normalizeCacheKey(url);
        const entry = this.#store.get(key);
        if (!entry) return null;
        if (this.#isExpired(entry)) { this.#store.delete(key); this.#cleanupTags(key); return null; }
        if (!entry.html || typeof entry.html !== 'string') { this.#store.delete(key); this.#cleanupTags(key); return null; }
        entry.accessedAt = ++this.#accessCounter;
        return entry;
    }

    set(url, entry, cacheable = true, tags = []) {
        if (!cacheable) return;
        const key = normalizeCacheKey(url);
        if (this.#store.size >= this.#maxEntries) this.#evictLRU();
        const ttlType = entry.meta?.ttlType || 'default';
        const ttl = this.#ttlMap[ttlType] || this.#defaultTtl;
        this.#store.set(key, { html: entry.html, meta: entry.meta, timestamp: Date.now(), ttl, accessedAt: ++this.#accessCounter });
        for (const tag of tags) { if (!this.#tags.has(tag)) this.#tags.set(tag, new Set()); this.#tags.get(tag).add(key); }
    }

    clear() { this.#store.clear(); this.#tags.clear(); }
    delete(url) { const key = normalizeCacheKey(url); this.#store.delete(key); this.#cleanupTags(key); }
    invalidateTag(tag) { const keys = this.#tags.get(tag); if (!keys) return; for (const key of keys) this.#store.delete(key); this.#tags.delete(tag); }
    get size() { return this.#store.size; }

    evictHalf() {
        const entries = [...this.#store.entries()].sort(([, a], [, b]) => a.accessedAt - b.accessedAt);
        const half = Math.ceil(entries.length / 2);
        for (let i = 0; i < half; i++) { const key = entries[i][0]; this.#store.delete(key); this.#cleanupTags(key); }
    }

    #evictLRU() {
        let oldest = null; let oldestKey = null;
        for (const [key, entry] of this.#store) { if (!oldest || entry.accessedAt < oldest) { oldest = entry.accessedAt; oldestKey = key; } }
        if (oldestKey) this.#store.delete(oldestKey);
    }

    #cleanupTags(key) { for (const [tag, keys] of this.#tags) { keys.delete(key); if (keys.size === 0) this.#tags.delete(tag); } }
    #isExpired(entry) { return (Date.now() - entry.timestamp) > (entry.ttl * 1000); }
}
