import { MAIN_CONTENT_SELECTOR } from './config/css-selectors.js';
const TRUSTED_POLICY = 'spa-router';
const DANGEROUS_ELEMENTS = 'script, iframe, object, embed, applet, form, base, link[rel="import"]';
const ON_PREFIX = 'on';

export default class DomPatcher {
    #logger;
    #trustedPolicy;
    constructor(logger) { this.#logger = logger; }

    #getTrustedPolicy() {
        if (this.#trustedPolicy !== undefined) return this.#trustedPolicy;
        if (window.trustedTypes) {
            let policy;
            try { policy = window.trustedTypes.getPolicy(TRUSTED_POLICY); } catch { policy = null; }
            if (!policy) { try { policy = window.trustedTypes.createPolicy(TRUSTED_POLICY, { createHTML: (s) => s }); } catch { this.#trustedPolicy = null; return null; } }
            this.#trustedPolicy = policy;
        } else { this.#trustedPolicy = null; }
        return this.#trustedPolicy;
    }

    #sanitize(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        for (const el of doc.querySelectorAll(DANGEROUS_ELEMENTS)) el.remove();
        for (const el of doc.querySelectorAll('*')) { for (const attr of [...el.attributes]) { if (attr.name.toLowerCase().startsWith(ON_PREFIX)) el.removeAttribute(attr.name); } }
        return doc.body ? doc.body.innerHTML : '';
    }

    safeSetHTML(container, html) {
        const clean = this.#sanitize(html);
        const policy = this.#getTrustedPolicy();
        container.innerHTML = policy ? policy.createHTML(clean) : clean;
    }

    async patchDOM(html, container) {
        return new Promise(resolve => {
            requestAnimationFrame(() => {
                if (!container) { resolve(); return; }
                const doc = new DOMParser().parseFromString(html, 'text/html');
                for (const el of doc.querySelectorAll(DANGEROUS_ELEMENTS)) el.remove();
                for (const el of doc.querySelectorAll('*')) { for (const attr of [...el.attributes]) { if (attr.name.toLowerCase().startsWith(ON_PREFIX)) el.removeAttribute(attr.name); } }
                const app = doc.querySelector(MAIN_CONTENT_SELECTOR) || doc.body;
                const cleanHtml = app ? app.innerHTML : html;
                const policy = this.#getTrustedPolicy();
                container.innerHTML = policy ? policy.createHTML(cleanHtml) : cleanHtml;
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
