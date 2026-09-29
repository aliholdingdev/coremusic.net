/**
 * ComponentRegistry — Singleton registry for JS components.
 *
 * Maps BEM block names to ComponentBase subclasses.
 * Lazy instantiation with instance caching.
 *
 * Faz 2 / Batch 1 — eklenen: duplicate-register guard (aynı ad iki kez
 * register edilmez; mevcut sınıf korunur, konsol uyarısı basılır).
 *
 * @package CoreMusic\Components\Base
 * @version 2.0.0
 */
export default class ComponentRegistry {
    /** @type {Map<string, typeof ComponentBase>} Name → Class mapping */
    #registry = new Map();

    /** @type {Map<string, ComponentBase>} id → Instance cache */
    #instances = new Map();

    /** @type {ComponentRegistry|null} */
    static #instance = null;

    /** @returns {ComponentRegistry} */
    static getInstance() {
        return (ComponentRegistry.#instance ??= new ComponentRegistry());
    }

    /**
     * Component class'ı register eder.
     *
     * @param {string} name — BEM block adı (ör: 'cm-tabs')
     * @param {typeof import('./ComponentBase.js').default} componentClass
     * @returns {this} — chainable
     */
    register(name, componentClass) {
        // Runtime validation — prototype chain kontrolü
        if (typeof componentClass !== 'function') {
            console.error(`[ComponentRegistry] ${name}: must be a class constructor`);
            return this;
        }
        // Duplicate guard (Faz 2): mevcut sınıf korunur, sessiz ezme yok
        if (this.#registry.has(name)) {
            console.warn(
                `[ComponentRegistry] ${name}: zaten kayıtlı — mevcut sınıf korundu, yenisi yok sayıldı.`
            );
            return this;
        }
        this.#registry.set(name, componentClass);
        return this;
    }

    /**
     * Component class'ını alır.
     * @param {string} name
     * @returns {typeof import('./ComponentBase.js').default|undefined}
     */
    get(name) {
        return this.#registry.get(name);
    }

    /**
     * Component instance'ını alır veya oluşturur (singleton per element).
     *
     * @param {string} name
     * @param {HTMLElement} element
     * @param {object} [initialState]
     * @returns {import('./ComponentBase.js').default|null}
     */
    getInstance(name, element, initialState) {
        const key = `${name}#${element.id || element.dataset.cmId || ''}`;
        if (this.#instances.has(key)) {
            return this.#instances.get(key);
        }

        const Class = this.#registry.get(name);
        if (!Class) {
            console.error(`[ComponentRegistry] Unknown component: ${name}`);
            return null;
        }

        const instance = new Class(element, initialState);
        this.#instances.set(key, instance);
        return instance;
    }

    /**
     * Kayıtlı tüm component adlarını listeler.
     * @returns {string[]}
     */
    list() {
        return [...this.#registry.keys()];
    }

    /**
     * Tüm instance'ları destroy eder (her biri try/catch — bozuk instance
     * temizliği durdurmaz; Faz 2 sağlamlaştırma).
     */
    destroyAll() {
        this.#instances.forEach((inst) => {
            try {
                inst.destroy();
            } catch {
                // Bozuk instance — yut, temizliğe devam et
            }
        });
        this.#instances.clear();
    }

    /**
     * Kayıt sayısını döndürür.
     * @returns {number}
     */
    get size() {
        return this.#registry.size;
    }
}
