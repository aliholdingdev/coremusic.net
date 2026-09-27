/**
 * ToastComponent — Bildirim popup'ı (singleton).
 *
 * Container otomatik oluşturulur, PHP render etmez.
 * Usage: ToastComponent.getInstance().show('Mesaj', { type: 'success' });
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

/** Statik (kod içi) HTML parçalarını temizleyen sanitizer — innerHTML sink YOK */
const DANGEROUS_ELEMENTS = 'script, iframe, object, embed, applet, form, base, link[rel="import"]';

/**
 * Statik HTML'i (ikon SVG'leri) parse + sanitize edip fragment döndürür.
 * @param {string} html
 * @returns {DocumentFragment}
 */
function parseStaticHtml(html) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    for (const el of doc.querySelectorAll(DANGEROUS_ELEMENTS)) el.remove();
    for (const el of doc.querySelectorAll('*')) {
        for (const attr of [...el.attributes]) {
            if (attr.name.toLowerCase().startsWith('on')) el.removeAttribute(attr.name);
        }
    }
    const fragment = document.createDocumentFragment();
    if (doc.body) fragment.append(...doc.body.childNodes);
    return fragment;
}

export default class ToastComponent extends ComponentBase {
    /** @type {HTMLElement} Toast container */
    #container = null;

    /** @type {HTMLElement[]} Active toast elements */
    #toasts = [];

    /** @type {number} Maksimum aynı anda görünür */
    #maxVisible = 5;

    /** @type {number} Varsayılan süre (ms) */
    #defaultDuration = 3000;

    /** @type {ToastComponent|null} */
    static #instance = null;

    /**
     * @param {HTMLElement} [container] — Varsayılan: body'ye oluşturur
     */
    constructor(container) {
        // Container yoksa oluştur
        if (!container) {
            container = document.createElement('div');
            container.className = 'cm-toast-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
        }
        super(container);
        this.#container = container;
    }

    /** @returns {ToastComponent} */
    static getInstance() {
        if (!ToastComponent.#instance) {
            ToastComponent.#instance = new ToastComponent();
        }
        return ToastComponent.#instance;
    }

    init() {}

    mount() {
        // Event delegation — close button
        this.on(this.#container, 'click', (e) => {
            const closeBtn = /** @type {HTMLElement} */ (e.target).closest('.cm-toast__close');
            if (closeBtn) {
                const toast = closeBtn.closest('.cm-toast');
                if (toast) this.#dismissToast(toast);
            }
        });
    }

    /**
     * Toast gösterir.
     *
     * @param {string} message
     * @param {object} [options]
     * @param {'info'|'success'|'error'|'warning'} [options.type='info']
     * @param {number} [options.duration=3000] — ms
     * @param {boolean} [options.dismissible=true]
     * @returns {HTMLElement} Toast elementi
     */
    show(message, options = {}) {
        const {
            type = 'info',
            duration = this.#defaultDuration,
            dismissible = true,
        } = options;

        // Max toast limit
        while (this.#toasts.length >= this.#maxVisible) {
            this.#dismissToast(this.#toasts[0]);
        }

        // Toast element oluştur
        const icons = {
            success: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.5 4.5L6 12L2.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            error: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M12 4L4 12M4 4l8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
            warning: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 5v3M8 11h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
            info: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/><path d="M8 7v4M8 5h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
        };

        // Bilinmeyen type → C16 BEM modifier seti dışına çıkma (yalnız --success/--error/--warning/--info)
        const variant = Object.prototype.hasOwnProperty.call(icons, type) ? type : 'info';

        const toast = document.createElement('div');
        toast.className = `cm-toast cm-toast--${variant}`;
        toast.setAttribute('role', 'alert');

        // İkon — statik SVG, DOMParser + sanitizer
        const iconEl = document.createElement('span');
        iconEl.className = 'cm-toast__icon';
        iconEl.appendChild(parseStaticHtml(icons[variant]));
        toast.appendChild(iconEl);

        // Mesaj — kullanıcı türevli veri → textContent (XSS yok)
        const messageEl = document.createElement('span');
        messageEl.className = 'cm-toast__message';
        messageEl.textContent = message == null ? '' : String(message);
        toast.appendChild(messageEl);

        // Kapat butonu — statik DOM
        if (dismissible) {
            const closeBtn = document.createElement('button');
            closeBtn.className = 'cm-toast__close';
            closeBtn.type = 'button';
            closeBtn.setAttribute('aria-label', 'Kapat');
            closeBtn.textContent = '×';
            toast.appendChild(closeBtn);
        }

        this.#container.appendChild(toast);
        this.#toasts.push(toast);

        // Animate in
        requestAnimationFrame(() => toast.classList.add('cm-toast--visible'));

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => this.#dismissToast(toast), duration);
        }

        this.emit('cm:toast:show', { message, type });
        return toast;
    }

    success(message, options = {}) {
        return this.show(message, { ...options, type: 'success' });
    }

    error(message, options = {}) {
        return this.show(message, { ...options, type: 'error' });
    }

    warning(message, options = {}) {
        return this.show(message, { ...options, type: 'warning' });
    }

    info(message, options = {}) {
        return this.show(message, { ...options, type: 'info' });
    }

    clearAll() {
        [...this.#toasts].forEach((t) => this.#dismissToast(t));
    }

    /**
     * Toast'ı kaldırır.
     * @param {HTMLElement} toast
     */
    #dismissToast(toast) {
        toast.classList.remove('cm-toast--visible');
        toast.addEventListener('transitionend', () => {
            toast.remove();
            this.#toasts = this.#toasts.filter((t) => t !== toast);
        }, { once: true });

        // Fallback: transition bitmezse 300ms sonra kaldır
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
                this.#toasts = this.#toasts.filter((t) => t !== toast);
            }
        }, 300);

        this.emit('cm:toast:dismiss');
    }

    destroy() {
        this.clearAll();
        this.#container?.remove();
        this.#container = null;
        ToastComponent.#instance = null;
        super.destroy();
    }
}
