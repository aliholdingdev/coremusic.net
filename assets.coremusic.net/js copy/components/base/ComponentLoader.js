/**
 * ComponentLoader — Auto-discover and mount data-cm-component elements.
 *
 * Scans DOM for data-cm-component attributes, instantiates components via registry.
 * MutationObserver for dynamic content (SPA compatibility).
 *
 * @package CoreMusic\Components\Base
 */
export default class ComponentLoader {
    /** @type {import('./ComponentRegistry.js').default} */
    #registry = null;

    /** @type {MutationObserver|null} */
    #observer = null;

    /** @type {WeakSet<HTMLElement>} Already mounted elements */
    #initialized = new WeakSet();

    /**
     * @param {import('./ComponentRegistry.js').default} [registry] — Varsayılan: singleton
     */
    constructor(registry) {
        this.#registry = registry;
    }

    /**
     * Mevcut DOM'daki data-cm-component element'lerini mount eder.
     *
     * @param {HTMLElement} [root=document.body] — Tarama kökü
     * @returns {number} Mount edilen component sayısı
     */
    scan(root = document.body) {
        const elements = root.querySelectorAll('[data-cm-component]');
        let count = 0;

        elements.forEach((el) => {
            if (this.#mountElement(el)) {
                count++;
            }
        });

        return count;
    }

    /**
     * MutationObserver başlatır — dinamik eklenen element'leri otomatik mount eder.
     *
     * @param {HTMLElement} [root=document.body]
     */
    observe(root = document.body) {
        this.#observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const el = /** @type {HTMLElement} */ (node);

                            // Eklenen element'in kendisi bir component mi?
                            if (el.dataset?.cmComponent) {
                                this.#mountElement(el);
                            }

                            // İçinde component var mı?
                            const children = el.querySelectorAll?.('[data-cm-component]');
                            children?.forEach((child) => this.#mountElement(child));
                        }
                    });
                }
            }
        });

        this.#observer.observe(root, {
            childList: true,
            subtree: true,
        });
    }

    /**
     * MutationObserver'ı durdurur.
     */
    disconnect() {
        this.#observer?.disconnect();
        this.#observer = null;
    }

    /**
     * Tek bir element'i mount eder.
     *
     * @param {HTMLElement} element — data-cm-component içeren element
     * @returns {boolean} Başarılı mı?
     */
    #mountElement(element) {
        // Zaten mount edilmiş mi?
        if (this.#initialized.has(element)) {
            return false;
        }

        const componentName = element.dataset.cmComponent;
        if (!componentName) return false;

        // Config data attribute'tan okunur
        const initialState = this.#parseInitialState(element);

        // Registry'den instance oluştur
        const instance = this.#registry.getInstance(componentName, element, initialState);
        if (!instance) return false;

        // Lifecycle
        try {
            instance.init();
            instance.mount();
            instance._setMounted(true);
            this.#initialized.add(element);
            return true;
        } catch (error) {
            console.error(`[ComponentLoader] Failed to mount ${componentName}:`, error);
            return false;
        }
    }

    /**
     * Element'in data attribute'larından initial state okur.
     *
     * @param {HTMLElement} element
     * @returns {object}
     */
    #parseInitialState(element) {
        const state = {};

        // data-cm-config → JSON parse
        if (element.dataset.cmConfig) {
            try {
                Object.assign(state, JSON.parse(element.dataset.cmConfig));
            } catch (e) {
                console.warn('[ComponentLoader] Invalid data-cm-config JSON:', e);
            }
        }

        // data-cm-* attribute'ları → flat key-value
        for (const [key, value] of Object.entries(element.dataset)) {
            if (key === 'cmComponent' || key === 'cmConfig' || key === 'cmId') continue;
            if (key.startsWith('cm')) {
                // camelCase → camelCase (zaten)
                state[key] = value;
            }
        }

        return state;
    }
}
