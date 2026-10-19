/**
 * ComponentBase — Abstract base class for all CoreMusic JS components.
 *
 * Faz 2 / Batch 1 — yeniden yazım (API birebir korunmuş, 2 hata düzeltilmiş).
 *
 * Lifecycle: constructor → init() → mount() → [update()]* → destroy()
 * State: Private #state + setState() partial update (shallow diff)
 * Events: AbortController-based auto-cleanup
 * DOM: DOMParser + sanitizer (innerHTML PROHIBITED — TrustedTypes sink'e gerek yok)
 *
 * Faz 2 düzeltmeleri (davranış bozmadan):
 * 1. onUpdate(prev, next) artık tanımlı (varsayılan no-op) — eski kod setState içinde
 *    çağırdığı halde tanım yoktu → her setState TypeError atıyordu.
 * 2. Observer kaydı: addObserver() ile eklenen MutationObserver/ResizeObserver/
 *    IntersectionObserver destroy() içinde otomatik disconnect edilir.
 *
 * Uyumluluk (main.js + 6 mevcut alt sınıf korunur):
 * - constructor(element, initialState={}) + abstract check
 * - defaultState() METOT (getter değil)
 * - on(target, event, handler, options) — hedef odaklı imza
 * - emit('select') → 'cm:select' (cm: prefix; tam ad isteyen 'cm:button:click' verir)
 * - get state/el/isMounted/id/signal + _setMounted
 *
 * @package CoreMusic\Components\Base
 * @version 2.0.0
 */
export default class ComponentBase {
    /** @type {object} Reactive state */
    #state = {};

    /** @type {HTMLElement|null} Root DOM element */
    #el = null;

    /** @type {AbortController} Event cleanup controller (sinyalin sahibi) */
    #abortController = null;

    /** @type {boolean} Lifecycle state */
    #mounted = false;

    /** @type {string} Unique component ID */
    #id = '';

    /** @type {Map<string, ComponentBase>} Child components */
    #children = new Map();

    /** @type {Set<MutationObserver|ResizeObserver|IntersectionObserver>} destroy'da disconnect */
    #observers = new Set();

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

    /** Başlangıç — constructor'dan sonra bir kez (ComponentLoader çağırır) */
    init() {}

    /** DOM'a bağlanır, event'ler kurulur (ComponentLoader çağırır) */
    mount() {}

    /**
     * Cleanup: event'ler (abort), observer'lar (disconnect), child component'ler.
     * OVERRIDE edilirse super.destroy() SONDA çağrılmalı.
     */
    destroy() {
        this.#children.forEach((child) => child.destroy());
        this.#children.clear();

        this.#observers.forEach((observer) => {
            try {
                observer.disconnect();
            } catch {
                // Zaten kopmuş — yut
            }
        });
        this.#observers.clear();

        this.#abortController.abort();
        this.#el = null;
        this.#mounted = false;
    }

    /**
     * Observer'ı kaydet → destroy() otomatik disconnect eder.
     * Yeni API (Faz 2, ekleme — mevcut kodu etkilemez).
     *
     * @param {MutationObserver|ResizeObserver|IntersectionObserver} observer
     * @returns {typeof observer} Zincirleme için this
     */
    addObserver(observer) {
        if (observer) this.#observers.add(observer);
        return observer;
    }

    /* ═══════════════════════════════════════════════════════════
     * STATE MANAGEMENT
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Partial state update — shallow merge.
     * Tek render noktası: onUpdate(prev, next) bir kez çağrılır.
     *
     * @param {object} partial — Güncellenen alanlar
     * @returns {ComponentBase} Zincirleme için this
     */
    setState(partial) {
        const prev = { ...this.#state };
        this.#state = { ...this.#state, ...partial };
        this.onUpdate(prev, this.#state);
        this.emit('cm:component:update', { prev, next: this.#state });
        return this;
    }

    /**
     * State değişiminden sonra çağrılır — tek render/güncelleme kancası.
     * Varsayılan no-op'tur (Faz 2 düzeltmesi: eski kodda çağrı vardı, tanım yoktu).
     * Alt sınıflar DOM güncellemesini burada yapar.
     *
     * @param {object} prev — Önceki state (kopya)
     * @param {object} next — Yeni state
     */
    onUpdate(prev, next) {
        // no-op — subclass override eder
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
     * destroy() → abort() tüm bu listener'ları kapatır.
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
     * Ad biçimi (master prompt): tam ad 'cm:button:click' gibi verilirse aynen kullanılır;
     * kısa ad 'select' verilirse 'cm:select' olur (eski davranış korunur).
     *
     * @param {string} eventName — Event adı (ör: 'select' → 'cm:select')
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
     * Tüm çocukları destroy eder (idempotent).
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
