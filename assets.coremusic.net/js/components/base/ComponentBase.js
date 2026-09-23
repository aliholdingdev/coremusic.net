/**
 * ComponentBase — Abstract base class for all CoreMusic JS components.
 *
 * Lifecycle: constructor → init() → mount() → [update()]* → destroy()
 * State: Private #state + setState() partial update (shallow diff)
 * Events: AbortController-based auto-cleanup
 * DOM: DOMParser + TrustedTypes (innerHTML PROHIBITED)
 *
 * @package CoreMusic\Components\Base
 */
export default class ComponentBase {
    /** @type {object} Reactive state */
    #state = {};

    /** @type {HTMLElement|null} Root DOM element */
    #el = null;

    /** @type {AbortController} Event cleanup controller */
    #abortController = null;

    /** @type {boolean} Lifecycle state */
    #mounted = false;

    /** @type {string} Unique component ID */
    #id = '';

    /** @type {Map<string, ComponentBase>} Child components */
    #children = new Map();

    /**
     * @param {HTMLElement} element — Root DOM element (PHP tarafından render edilmiş)
     * @param {object} [initialState={}] — Başlangıç durumu
     */
    constructor(element, initialState = {}) {
        if (new.target === ComponentBase) {
            throw new Error('ComponentBase is abstract — use a concrete subclass');
        }

        this.#el = element;
        this.#id = element?.id || `cm-${crypto.randomUUID?.() || Date.now().toString(36)}`;
        this.#abortController = new AbortController();
        this.#state = { ...this.defaultState(), ...initialState };
    }

    /* ═══════════════════════════════════════════════════════════
     * ABSTRACT METHODS — subclasses MUST implement
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Varsayılan state tanımı — alt sınıflar override eder.
     * @returns {object}
     */
    defaultState() {
        return {};
    }

    /* ═══════════════════════════════════════════════════════════
     * LIFECYCLE
     * ═══════════════════════════════════════════════════════════ */

    /** Başlangıç — constructor'dan sonra bir kez */
    init() {}

    /** DOM'a bağlanır, event'ler kurulur */
    mount() {}

    /** Cleanup: event'ler, observer'lar, child component'ler */
    destroy() {
        this.#children.forEach((child) => child.destroy());
        this.#children.clear();
        this.#abortController.abort();
        this.#el = null;
        this.#mounted = false;
    }

    /* ═══════════════════════════════════════════════════════════
     * STATE MANAGEMENT
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Partial state update — shallow merge.
     * DataBinder varsa otomatik tetikler.
     *
     * @param {object} partial — Güncellenen alanlar
     */
    setState(partial) {
        const prev = { ...this.#state };
        this.#state = { ...this.#state, ...partial };
        this.onUpdate(prev, this.#state);
        this.emit('cm:component:update', { prev, next: this.#state });
    }

    /**
     * Shallow copy — dışarıdan state değiştirilemez.
     * @returns {object}
     */
    get state() {
        return { ...this.#state };
    }

    /* ═══════════════════════════════════════════════════════════
     * DOM HELPERS
     * ═══════════════════════════════════════════════════════════ */

    /**
     * querySelector wrapper — #el içinde arar.
     * @param {string} selector
     * @returns {HTMLElement|null}
     */
    $(selector) {
        return this.#el?.querySelector(selector) ?? null;
    }

    /**
     * querySelectorAll wrapper — NodeList döner.
     * @param {string} selector
     * @returns {NodeListOf<HTMLElement>}
     */
    $$(selector) {
        return this.#el?.querySelectorAll(selector) ?? [];
    }

    /* ═══════════════════════════════════════════════════════════
     * EVENT METHODS
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Event listener ekler — AbortController ile otomatik cleanup.
     *
     * @param {EventTarget} target — Event kaynağı
     * @param {string} event — Event adı
     * @param {Function} handler — Handler fonksiyonu
     * @param {AddEventListenerOptions} [options] — Listener options
     */
    on(target, event, handler, options) {
        target.addEventListener(event, handler, {
            ...options,
            signal: this.#abortController.signal,
        });
    }

    /**
     * CustomEvent dispatch eder — cm: prefix otomatik.
     *
     * @param {string} eventName — Event adı (ör: 'select', 'cm:select' olur)
     * @param {*} [detail] — Event verisi
     */
    emit(eventName, detail) {
        const name = eventName.startsWith('cm:') ? eventName : `cm:${eventName}`;
        this.#el?.dispatchEvent(
            new CustomEvent(name, {
                detail,
                bubbles: true,
                composed: true,
            })
        );
    }

    /* ═══════════════════════════════════════════════════════════
     * CHILD MANAGEMENT
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Çocuk component ekler.
     * @param {string} name
     * @param {ComponentBase} component
     */
    addChild(name, component) {
        this.#children.set(name, component);
    }

    /**
     * Çocuk component alır.
     * @param {string} name
     * @returns {ComponentBase|undefined}
     */
    getChild(name) {
        return this.#children.get(name);
    }

    /**
     * Tüm çocukları destroy eder.
     */
    destroyChildren() {
        this.#children.forEach((child) => child.destroy());
        this.#children.clear();
    }

    /* ═══════════════════════════════════════════════════════════
     * GETTERS
     * ═══════════════════════════════════════════════════════════ */

    /** @returns {HTMLElement|null} */
    get el() {
        return this.#el;
    }

    /** @returns {boolean} */
    get isMounted() {
        return this.#mounted;
    }

    /** @returns {string} */
    get id() {
        return this.#id;
    }

    /** @returns {AbortSignal} */
    get signal() {
        return this.#abortController.signal;
    }

    /**
     * Mount state'ini ayarla — ComponentLoader tarafından çağrılır.
     * @param {boolean} value
     */
    _setMounted(value) {
        this.#mounted = value;
    }
}
