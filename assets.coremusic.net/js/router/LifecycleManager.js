import ScriptInjector from './ScriptInjector.js';

const COMPONENT_SELECTOR = '[data-component]';

export default class LifecycleManager {
    #hooks = new WeakMap();
    #listeners = new Set();
    #timers = new Set();
    #scriptInjector;
    #logger;

    constructor(logger, scriptInjector = null) {
        this.#logger = logger;
        this.#scriptInjector = scriptInjector ?? new ScriptInjector(logger);
    }

    register(el, hooks) {
        this.#hooks.set(el, {
            mount: typeof hooks.mount === 'function' ? hooks.mount : null,
            unmount: typeof hooks.unmount === 'function' ? hooks.unmount : null,
            cleanup: typeof hooks.cleanup === 'function' ? hooks.cleanup : null
        });
    }

    unmount() {
        for (const e of this.#listeners) {
            try {
                e.el.removeEventListener(e.type, e.handler, e.options);
            } catch {}
        }
        this.#listeners.clear();

        for (const id of this.#timers) {
            clearTimeout(id);
            clearInterval(id);
        }
        this.#timers.clear();
    }

    mount(container) {
        if (!container) return;
        for (const el of container.querySelectorAll(COMPONENT_SELECTOR)) {
            const h = this.#hooks.get(el);
            try {
                h?.mount?.(el);
            } catch (e) {
                this.#logger.error('LifecycleManager', 'mount_error', { error: e.message });
            }
        }
        this.#scriptInjector.reinjectWithNonce(container);
    }
}
