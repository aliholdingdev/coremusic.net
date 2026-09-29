import { MAIN_CONTENT_SELECTOR } from './config/css-selectors.js';

const DANGEROUS_ELEMENTS = 'script, iframe, object, embed, applet, form, base, link[rel="import"]';
const ON_PREFIX = 'on';

/**
 * DomPatcher — SPA router DOM güncelleme katmanı (ADR-021 sözleşmesi).
 *
 * Sözleşme (DEĞİŞMEZ — public API):
 *   - safeSetHTML(container, html)
 *   - patchDOM(html, container)  → Promise<void>
 *   - setErrorState(errorCode)
 *   - setAriaBusy(busy)
 *
 * DOM yazma stratejisi:
 *   Statik/sunucu HTML'i DOMParser.parseFromString + sanitizer ile temizlenir,
 *   sonuç DocumentFragment olarak container'a replaceChildren() ile konur.
 *   innerHTML SINK YOK → TrustedTypes policy'ye ve CSP trusted-types
 *   direktifine bağımlılık kalktı (aynı sanitizer davranışı korunur).
 */
export default class DomPatcher {
    #logger;
    constructor(logger) { this.#logger = logger; }

    /**
     * HTML string'ini parse eder ve tehlikeli element/attribute'lardan arındırır.
     * @param {string} html
     * @returns {Document} Sanitize edilmiş belge (body içinde temiz düğümler)
     */
    #parseSanitized(html) {
        const source = typeof html === 'string' ? html : '';
        const doc = new DOMParser().parseFromString(source, 'text/html');
        for (const el of doc.querySelectorAll(DANGEROUS_ELEMENTS)) el.remove();
        for (const el of doc.querySelectorAll('*')) {
            for (const attr of [...el.attributes]) {
                if (attr.name.toLowerCase().startsWith(ON_PREFIX)) el.removeAttribute(attr.name);
            }
        }
        return doc;
    }

    /**
     * Sanitize edilmiş statik HTML'i container'a yazar (mevcut içerik temizlenir).
     * @param {HTMLElement} container
     * @param {string} html
     */
    safeSetHTML(container, html) {
        const doc = this.#parseSanitized(html);
        container.replaceChildren(...doc.body.childNodes);
    }

    /**
     * Sunucudan gelen HTML'i ana içerik alanına yazar, <title> senkronu yapar.
     * @param {string} html
     * @param {HTMLElement} container
     * @returns {Promise<void>}
     */
    async patchDOM(html, container) {
        return new Promise(resolve => {
            requestAnimationFrame(() => {
                if (!container) { resolve(); return; }
                const doc = this.#parseSanitized(html);
                const app = doc.querySelector(MAIN_CONTENT_SELECTOR) || doc.body;
                container.replaceChildren(...(app ? app.childNodes : []));
                const titleEl = doc.querySelector('title');
                if (titleEl?.textContent) document.title = titleEl.textContent;
                resolve();
            });
        });
    }

    setErrorState(errorCode) {
        const container = document.querySelector(MAIN_CONTENT_SELECTOR);
        if (!container) return;
        if (errorCode) container.dataset.error = String(errorCode);
        else delete container.dataset.error;
    }

    setAriaBusy(busy) {
        const container = document.querySelector(MAIN_CONTENT_SELECTOR);
        if (container) container.setAttribute('aria-busy', String(busy));
    }
}
