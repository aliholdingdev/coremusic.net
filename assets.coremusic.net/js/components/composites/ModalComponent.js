/**
 * C07 Modal bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/ModalComponent
 * @requires ADR-001 (framework yasak), ADR-012 (CSP)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C07
 * BEM: .modal, .modal__overlay, .modal__content, .modal__header, .modal__body, .modal__footer
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';
import { trapFocus, setAria, announce } from '../base/ComponentAccessibility.js';

/**
 * @typedef {Object} ModalState
 * @property {boolean} open
 * @property {string} title
 * @property {string} size
 * @property {boolean} dismissible
 */

export default class ModalComponent extends ComponentBase {
    /** @type {(() => void)|null} Odak tuzağı bırakma fonksiyonu */
    #releaseFocus = null;

    /** @type {HTMLElement|null} Açılışta odaklanan eleman */
    #previouslyFocused = null;

    /** @returns {ModalState} */
    defaultState() {
        return { open: false, title: '', size: '', dismissible: true };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('modal');
        root.dataset.component = 'modal';
        root.hidden = true;
        setAria(root, { role: 'dialog', 'aria-modal': 'true' });

        const overlay = document.createElement('div');
        overlay.className = 'modal__overlay';
        overlay.dataset.action = 'dismiss';

        const content = document.createElement('div');
        content.className = 'modal__content';

        const header = document.createElement('div');
        header.className = 'modal__header';

        const title = document.createElement('h2');
        title.className = 'modal__title';

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'modal__close';
        close.dataset.action = 'close';
        close.textContent = '×';
        setAria(close, { 'aria-label': 'Kapat' });

        const body = document.createElement('div');
        body.className = 'modal__body';

        const footer = document.createElement('div');
        footer.className = 'modal__footer';

        header.append(title, close);
        content.append(header, body, footer);
        overlay.appendChild(content);
        root.appendChild(overlay);

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(document, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {ModalState} next */
    onUpdate(prev, next) {
        if (prev.open !== next.open || prev.title !== next.title || prev.size !== next.size) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { open, title, size } = this.state;

        root.classList.toggle('is-open', open);
        root.classList.toggle('modal--wide', size === 'wide');
        root.hidden = !open;

        const titleEl = this.$('.modal__title');
        if (titleEl instanceof HTMLElement) titleEl.textContent = title || '';

        if (open) this.#onOpen();
        else this.#onClose();
    }

    /** Odak tuzağı + geri odaklama kurulumu. */
    #onOpen() {
        const root = this.el;
        if (!root || this.#releaseFocus) return;

        this.#previouslyFocused = document.activeElement instanceof HTMLElement
            ? document.activeElement
            : null;

        this.#releaseFocus = trapFocus(root) || null;

        const content = this.$('.modal__content');
        if (content instanceof HTMLElement) {
            const first = content.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (first instanceof HTMLElement) first.focus();
        }
        announce(root, `${this.state.title || 'Pencere'} açıldı`);
        this.emit(eventName('modal', 'open'), { title: this.state.title });
    }

    /** Odak tuzağını bırak ve odağı geri ver. */
    #onClose() {
        this.#releaseFocus?.();
        this.#releaseFocus = null;

        const back = this.#previouslyFocused;
        this.#previouslyFocused = null;
        if (back && document.contains(back)) back.focus();

        this.emit(eventName('modal', 'close'), { title: this.state.title });
    }

    /** Modalı açar. */
    open() {
        this.setState({ open: true });
        return this;
    }

    /** Modalı kapatır (kapatılabilirse). */
    close(force = false) {
        if (!force && !this.state.dismissible) return this;
        this.setState({ open: false });
        return this;
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const target = event.target instanceof Element
            ? event.target.closest('[data-action]')
            : null;
        if (!target || !this.el?.contains(target)) return;

        if (target.dataset.action === 'dismiss' || target.dataset.action === 'close') {
            this.close();
        }
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key !== 'Escape') return;
        if (!this.state.open) return;
        this.close();
    }

    destroy() {
        this.#releaseFocus?.();
        this.#releaseFocus = null;
        this.#previouslyFocused = null;
        super.destroy();
    }
}
