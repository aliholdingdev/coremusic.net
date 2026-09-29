/**
 * ComponentEvents — Frozen event adları + yayım yardımcısı (Faz 2 / Batch 1).
 *
 * @module assets.coremusic.net/js/components/base/ComponentEvents
 * @requires ADR-001 (vanilla JS)
 *
 * Kural (master prompt): her public event `cm:{bileşen}:{fiil}` biçimindedir.
 * ComponentBase.emit('select') yalnız `cm:` öneklediği için bileşenler buradaki
 * TAM adları emit eder: emit(CM_EVENTS.BUTTON_CLICK) → 'cm:button:click'.
 *
 * Bu sözlük frozen'dır (vault kuralı) — yeni event eklenirse CSÜZ değil, ekleme yapılır.
 */

'use strict';

/**
 * @typedef {Object} CmEventMap
 * @property {string} BUTTON_CLICK      'cm:button:click'
 * @property {string} INPUT_CHANGE      'cm:input:change'
 * @property {string} INPUT_FOCUS       'cm:input:focus'
 * @property {string} INPUT_BLUR        'cm:input:blur'
 * @property {string} TOGGLE_CHANGE     'cm:toggle:change'
 * @property {string} SLIDER_INPUT      'cm:slider:input'
 * @property {string} SLIDER_CHANGE     'cm:slider:change'
 * @property {string} BADGE_CLICK       'cm:badge:click'
 * @property {string} SKELETON_LOADED   'cm:skeleton:loaded'
 */

/** @type {Readonly<CmEventMap>} */
export const CM_EVENTS = Object.freeze({
    BUTTON_CLICK: 'cm:button:click',
    INPUT_CHANGE: 'cm:input:change',
    INPUT_FOCUS: 'cm:input:focus',
    INPUT_BLUR: 'cm:input:blur',
    TOGGLE_CHANGE: 'cm:toggle:change',
    SLIDER_INPUT: 'cm:slider:input',
    SLIDER_CHANGE: 'cm:slider:change',
    BADGE_CLICK: 'cm:badge:click',
    SKELETON_LOADED: 'cm:skeleton:loaded',
});

/**
 * `cm:{component}:{action}` adı üretir (CM_EVENTS kullanmak tercih edilir; bu yardımcı
 * yalnız dinamik/kullanıcı tanımlı aksiyonlar içindir).
 *
 * @param {string} component — Bileşen adı (ör: 'button')
 * @param {string} action — Fiil (ör: 'click')
 * @returns {string} 'cm:button:click'
 * @throws {Error} component veya action boşsa
 */
export function eventName(component, action) {
    const c = String(component ?? '').trim();
    const a = String(action ?? '').trim();
    if (!c || !a) {
        throw new Error('eventName(component, action): both arguments are required');
    }
    return `cm:${c}:${a}`;
}

/**
 * CustomEvent yayınlar. Yalnız `detail` taşımak isteyen yardımcı katman içindir;
 * bileşenlerin ComponentBase.emit() kullanması beklenir.
 *
 * @param {EventTarget} target — Kaynak (genelde bileşenin kök elemanı)
 * @param {string} name — Tam event adı ('cm:button:click')
 * @param {*} [detail] — Olay verisi
 * @param {{bubbles?: boolean, composed?: boolean}} [options]
 * @returns {boolean} dispatchEvent sonucu
 * @throws {Error} name 'cm:' ile başlamıyorsa (frozen kural ihlali)
 */
export function emitEvent(target, name, detail, options = {}) {
    if (!name || !name.startsWith('cm:')) {
        throw new Error(`emitEvent: event name must start with "cm:" (got "${name}")`);
    }
    if (!target || typeof target.dispatchEvent !== 'function') {
        return false;
    }
    return target.dispatchEvent(
        new CustomEvent(name, {
            detail,
            bubbles: options.bubbles !== false,
            composed: options.composed !== false,
        })
    );
}

/**
 * Tek AbortController sarmalayıcısı — dinleyicileri ve gözlemcileri bağlar,
 * abort() ile hepsini temizler (sse, router ve ComponentBase ile aynı desen).
 */
export class AbortSignalCarrier {
    /** @type {AbortController|null} */
    #controller = null;

    constructor() {
        this.#controller = new AbortController();
    }

    /** @returns {AbortSignal} Sinyal (zaten abort edilmişse new-tanım) */
    get signal() {
        return this.#controller?.signal ?? AbortSignal.abort();
    }

    /** @returns {boolean} */
    get aborted() {
        return this.signal.aborted;
    }

    /**
     * Dinleyici bağlar — abort edilince otomatik kaldırılır.
     * @param {EventTarget} target
     * @param {string} type
     * @param {EventListenerOrEventListenerObject} handler
     * @param {AddEventListenerOptions} [options]
     * @returns {() => void} Elle temizleme fonksiyonu
     */
    on(target, type, handler, options) {
        if (!target || typeof target.addEventListener !== 'function') {
            return () => {};
        }
        const opts = { ...options, signal: this.signal };
        target.addEventListener(type, handler, opts);
        return () => {
            try {
                target.removeEventListener(type, handler, opts);
            } catch {
                // Sinyal zaten kullanılmış — yut
            }
        };
    }

    /**
     * Observer bağlar — abort'ta disconnect edilir.
     * @template {{disconnect: () => void}} T
     * @param {T} observer
     * @returns {T}
     */
    observe(observer) {
        if (!observer) return observer;
        const off = () => {
            try {
                observer.disconnect();
            } catch {
                // Zaten kopmuş — yut
            }
        };
        if (this.aborted) {
            off();
        } else {
            this.signal.addEventListener('abort', off, { once: true });
        }
        return observer;
    }

    /** Dinleyicileri/observer'ları kapatır (idempotent). */
    abort() {
        if (this.#controller && !this.#controller.signal.aborted) {
            this.#controller.abort();
        }
    }

    /** Abort sonrası yeni controller ile yeniden açar (test/tekrar kullanım). */
    reset() {
        if (!this.#controller || this.#controller.signal.aborted) {
            this.#controller = new AbortController();
        }
    }
}
