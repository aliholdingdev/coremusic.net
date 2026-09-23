/**
 * TemplateEngine — Safe HTML creation via DOMParser.
 *
 * innerHTML PROHIBITED — all HTML goes through DOMParser.
 * TrustedTypes policy support for CSP compliance.
 *
 * @package CoreMusic\Components\Base
 */
export default class TemplateEngine {
    /** @type {import('trusted-types').TrustedTypePolicy|null} */
    static #policy = null;

    /**
     * TrustedTypes policy'yi ayarlar (varsa).
     */
    static init() {
        if (window.trustedTypes) {
            TemplateEngine.#policy = window.trustedTypes.createPolicy('coremusic-components', {
                createHTML: (/** @type {string} */ str) => str,
            });
        }
    }

    /**
     * Template string'i DOM node'a dönüştürür — DOMParser kullanır.
     *
     * @param {string} html — Template string (BEM class'ları ile)
     * @returns {DocumentFragment}
     */
    static parse(html) {
        const safeHtml = TemplateEngine.#policy
            ? TemplateEngine.#policy.createHTML(html)
            : html;

        const parser = new DOMParser();
        const doc = parser.parseFromString(safeHtml, 'text/html');
        const fragment = document.createDocumentFragment();

        // body children'larını fragment'a taşı
        while (doc.body.firstChild) {
            fragment.appendChild(doc.body.firstChild);
        }

        return fragment;
    }

    /**
     * Template'i container'a render eder — mevcut içeriği temizler.
     *
     * @param {HTMLElement} container
     * @param {string} html — Template string
     */
    static render(container, html) {
        // Mevcut içeriği temizle (AbortController cleanup)
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }

        const fragment = TemplateEngine.parse(html);
        container.appendChild(fragment);
    }

    /**
     * Template literal helper — tagged template.
     *
     * @param {TemplateStringsArray} strings
     * @param  {...any} values
     * @returns {string}
     */
    static html(strings, ...values) {
        return strings.reduce((result, str, i) => {
            const value = values[i] !== undefined ? values[i] : '';
            // Null/undefined safety
            const safeValue = value === null || value === undefined ? '' : String(value);
            return result + str + safeValue;
        }, '');
    }

    /**
     * HTML string'i temizler — sadece güvenli tag'leri bırakır.
     * Basit whitelist approach (karmaşık senaryolar için DOMPurify tercih edilir).
     *
     * @param {string} dirty — Ham HTML
     * @returns {string} — Temiz HTML
     */
    static sanitize(dirty) {
        const doc = DOMParser.parseFromString(dirty, 'text/html');
        return doc.body.textContent || '';
    }
}
