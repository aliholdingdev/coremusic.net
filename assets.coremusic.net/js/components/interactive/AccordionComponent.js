/**
 * AccordionComponent — Genişleme/daralma accordion.
 *
 * HTML contract:
 * <div class="cm-accordion" data-cm-component="cm-accordion">
 *   <div class="cm-accordion__item" data-index="0">
 *     <button class="cm-accordion__header" aria-expanded="false">Bölüm 1</button>
 *     <div class="cm-accordion__content" role="region" hidden>İçerik 1</div>
 *   </div>
 * </div>
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

export default class AccordionComponent extends ComponentBase {
    /** @type {{ header: HTMLElement, content: HTMLElement, isOpen: boolean }[]} */
    #items = [];

    /** @type {boolean} Eşzamanlı açık kalma izni */
    #allowMultiple = false;

    /**
     * @param {HTMLElement} element
     * @param {object} [options]
     * @param {boolean} [options.allowMultiple=false]
     */
    constructor(element, options = {}) {
        super(element, options);
        this.#allowMultiple = options.allowMultiple ?? false;
    }

    init() {
        const itemElements = this.$$('.cm-accordion__item');

        this.#items = [...itemElements].map((item) => ({
            header: item.querySelector('.cm-accordion__header'),
            content: item.querySelector('.cm-accordion__content'),
            isOpen: false,
        }));

        // ARIA
        this.#items.forEach((item, i) => {
            if (item.header) {
                item.header.setAttribute('aria-expanded', 'false');
                item.header.setAttribute('aria-controls', `acc-content-${i}`);
            }
            if (item.content) {
                item.content.id = `acc-content-${i}`;
                item.content.setAttribute('role', 'region');
                item.content.hidden = true;
            }
        });
    }

    mount() {
        this.#items.forEach((item, index) => {
            if (item.header) {
                this.on(item.header, 'click', () => this.toggle(index));
            }
        });

        // Keyboard: Enter/Space
        this.on(this.el, 'keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const header = /** @type {HTMLElement} */ (e.target);
                if (header.classList.contains('cm-accordion__header')) {
                    e.preventDefault();
                    const index = this.#items.findIndex((item) => item.header === header);
                    if (index >= 0) this.toggle(index);
                }
            }
        });
    }

    /**
     * Belirli bir item'ı açar.
     * @param {number} index
     */
    expand(index) {
        const item = this.#items[index];
        if (!item || item.isOpen) return;

        // Multi mode değilse diğerlerini kapat
        if (!this.#allowMultiple) {
            this.#items.forEach((other, i) => {
                if (i !== index && other.isOpen) this.collapse(i);
            });
        }

        item.isOpen = true;
        item.header?.setAttribute('aria-expanded', 'true');
        if (item.content) item.content.hidden = false;

        this.emit('cm:accordion:expand', { index });
    }

    /**
     * Belirli bir item'ı kapatır.
     * @param {number} index
     */
    collapse(index) {
        const item = this.#items[index];
        if (!item || !item.isOpen) return;

        item.isOpen = false;
        item.header?.setAttribute('aria-expanded', 'false');
        if (item.content) item.content.hidden = true;

        this.emit('cm:accordion:collapse', { index });
    }

    /**
     * Toggle.
     * @param {number} index
     */
    toggle(index) {
        const item = this.#items[index];
        if (!item) return;
        item.isOpen ? this.collapse(index) : this.expand(index);
    }

    expandAll() {
        this.#items.forEach((_, i) => this.expand(i));
    }

    collapseAll() {
        this.#items.forEach((_, i) => this.collapse(i));
    }

    destroy() {
        this.#items = [];
        super.destroy();
    }
}
