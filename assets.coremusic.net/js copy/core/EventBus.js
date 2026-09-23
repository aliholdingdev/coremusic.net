/**
 * CoreMusic — EventBus
 * Pub/sub iletişim sistemi. Tüm modüller arası loose coupling sağlar.
 *
 * @module core/EventBus
 * @version 5.0.0
 */
export default class EventBus {
    /** @type {Map<string, Set<Function>>} */
    #listeners = new Map();

    /**
     * Event dinle
     * @param {string} event
     * @param {Function} fn
     * @returns {Function} unsubscribe fonksiyonu
     */
    on(event, fn) {
        if (!this.#listeners.has(event)) {
            this.#listeners.set(event, new Set());
        }
        this.#listeners.get(event).add(fn);
        return () => this.off(event, fn);
    }

    /**
     * Tek seferlik event dinle
     * @param {string} event
     * @param {Function} fn
     */
    once(event, fn) {
        const wrapper = (data) => {
            this.off(event, wrapper);
            fn(data);
        };
        this.on(event, wrapper);
    }

    /**
     * Event dinlemeyi bırak
     * @param {string} event
     * @param {Function} fn
     */
    off(event, fn) {
        const fns = this.#listeners.get(event);
        if (fns) {
            fns.delete(fn);
            if (fns.size === 0) this.#listeners.delete(event);
        }
    }

    /**
     * Event tetikle
     * @param {string} event
     * @param {*} data
     */
    emit(event, data) {
        const fns = this.#listeners.get(event);
        if (!fns) return;
        for (const fn of fns) {
            try {
                fn(data);
            } catch (err) {
                console.error(`[EventBus] Listener error on "${event}":`, err);
            }
        }
    }

    /** Tüm listener'ları temizle */
    destroy() {
        this.#listeners.clear();
    }
}
