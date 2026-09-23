export default class MemoryWatchdog {
    #cache;
    #logger;
    #config;
    #lastCheck = 0;

    constructor(cache, logger, config = {}) {
        this.#cache = cache;
        this.#logger = logger;
        this.#config = {
            checkInterval: config.checkInterval ?? 5000,
            thresholdMB: config.thresholdMB ?? 100,
            checkEvery: config.checkEvery ?? 10
        };
    }

    check(navCount) {
        if (navCount % this.#config.checkEvery !== 0) return;

        const now = Date.now();
        if (now - this.#lastCheck < this.#config.checkInterval) return;
        this.#lastCheck = now;

        const mem = typeof performance !== 'undefined' ? performance.memory : null;
        if (!mem) return;

        if (Math.round((mem.usedJSHeapSize / (1024 * 1024)) * 10) / 10 > this.#config.thresholdMB) {
            this.#cache.evictHalf();
        }
    }

    destroy() {}
}
