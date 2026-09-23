/**
 * DataBinder — Declarative state → DOM binding.
 *
 * data-cm-bind="stateKey" attribute'ları ile otomatik güncelleme.
 * ComponentBase.setState() çağrıldığında binding'ler tetiklenir.
 *
 * Binding türleri:
 *   data-cm-bind="key"              → textContent
 *   data-cm-bind-src="key"          → src attribute
 *   data-cm-bind-class="key"        → classList.toggle (boolean)
 *   data-cm-bind-style:prop="key"   → inline style prop
 *   data-cm-bind-attr:name="key"    → herhangi bir attribute
 *   data-cm-if="key"                → display:none toggle
 *
 * @package CoreMusic\Components\Base
 */
export default class DataBinder {
    /** @type {import('./ComponentBase.js').default} Parent component */
    #component = null;

    /** @type {Map<string, Set<HTMLElement>>} key → elements mapping */
    #bindings = new Map();

    /**
     * @param {import('./ComponentBase.js').default} component
     */
    constructor(component) {
        this.#component = component;
    }

    /**
     * Binding'leri tara ve kaydet — DOM'daki data-cm-* attribute'larını bulur.
     *
     * @param {HTMLElement} [root] — Tarama kökü (varsayılan: component.el)
     */
    scan(root) {
        const container = root || this.#component.el;
        if (!container) return;

        // data-cm-bind (textContent)
        container.querySelectorAll('[data-cm-bind]').forEach((el) => {
            const key = el.dataset.cmBind;
            this.#addBinding(key, el);
        });

        // data-cm-bind-src (src attribute)
        container.querySelectorAll('[data-cm-bind-src]').forEach((el) => {
            const key = el.dataset.cmBindSrc;
            this.#addBinding(key, el);
        });

        // data-cm-bind-class (classList toggle)
        container.querySelectorAll('[data-cm-bind-class]').forEach((el) => {
            const key = el.dataset.cmBindClass;
            this.#addBinding(key, el);
        });

        // data-cm-bind-style:prop
        container.querySelectorAll('[data-cm-bind-style]').forEach((el) => {
            const key = el.dataset.cmBindStyle;
            this.#addBinding(key, el);
        });

        // data-cm-bind-attr:name
        container.querySelectorAll('[data-cm-bind-attr]').forEach((el) => {
            const key = el.dataset.cmBindAttr;
            this.#addBinding(key, el);
        });

        // data-cm-if (display toggle)
        container.querySelectorAll('[data-cm-if]').forEach((el) => {
            const key = el.dataset.cmIf;
            this.#addBinding(key, el);
        });
    }

    /**
     * Tek bir key'i günceller — ilgili tüm element'leri apply eder.
     *
     * @param {string} key
     * @param {*} value
     */
    update(key, value) {
        const elements = this.#bindings.get(key);
        if (!elements) return;

        elements.forEach((el) => this.#applyBinding(el, key, value));
    }

    /**
     * Tüm binding'leri günceller — component.setState() çağrıldığında kullanılır.
     *
     * @param {object} state — Component state
     */
    updateAll(state) {
        this.#bindings.forEach((elements, key) => {
            const value = state[key];
            elements.forEach((el) => this.#applyBinding(el, key, value));
        });
    }

    /**
     * Binding tipini belirler ve DOM'a uygular.
     *
     * @param {HTMLElement} element
     * @param {string} key
     * @param {*} value
     */
    #applyBinding(element, key, value) {
        // data-cm-bind → textContent
        if (element.dataset.cmBind === key) {
            element.textContent = value ?? '';
            return;
        }

        // data-cm-bind-src → src
        if (element.dataset.cmBindSrc === key) {
            element.src = value ?? '';
            return;
        }

        // data-cm-bind-class → classList toggle
        if (element.dataset.cmBindClass === key) {
            element.classList.toggle(element.dataset.cmBindClass, Boolean(value));
            return;
        }

        // data-cm-bind-style → inline style
        if (element.dataset.cmBindStyle === key) {
            const prop = element.dataset.cmBindStyleProp || 'opacity';
            element.style[prop] = value ?? '';
            return;
        }

        // data-cm-bind-attr → arbitrary attribute
        if (element.dataset.cmBindAttr === key) {
            const attrName = element.dataset.cmBindAttrName;
            if (attrName) {
                if (value === null || value === undefined || value === false) {
                    element.removeAttribute(attrName);
                } else {
                    element.setAttribute(attrName, String(value));
                }
            }
            return;
        }

        // data-cm-if → display toggle
        if (element.dataset.cmIf === key) {
            element.hidden = !Boolean(value);
            element.style.display = value ? '' : 'none';
        }
    }

    /**
     * Binding kaydı ekler.
     *
     * @param {string} key
     * @param {HTMLElement} element
     */
    #addBinding(key, element) {
        if (!this.#bindings.has(key)) {
            this.#bindings.set(key, new Set());
        }
        this.#bindings.get(key).add(element);
    }

    /**
     * Tüm binding'leri temizler.
     */
    clear() {
        this.#bindings.clear();
    }

    /**
     * Binding sayısını döndürür.
     * @returns {number}
     */
    get size() {
        return this.#bindings.size;
    }
}
