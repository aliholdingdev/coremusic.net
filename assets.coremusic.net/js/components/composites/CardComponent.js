/**
 * C03 Card bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/CardComponent
 * @requires ADR-001 (framework yasak), ADR-018
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C03
 * BEM: .card, .card__image, .card__title, .card__meta, .card--compact, .card--wide
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';

/**
 * @typedef {Object} CardState
 * @property {string} id
 * @property {string} title
 * @property {string} meta
 * @property {string} image
 * @property {string} alt
 * @property {''|'compact'|'wide'} variant
 * @property {boolean} loading
 * @property {boolean} disabled
 * @property {string} href
 */

/** Varyant sınıf eşlemesi (envanter C03). */
const VARIANTS = Object.freeze({ compact: 'card--compact', wide: 'card--wide' });

/**
 * Sadece güvenli görsel URL'lerine izin verir (XSS: javascript: vb. engeli).
 * @param {string} value
 * @returns {string} boş veya güvenli URL
 */
function safeImageSrc(value) {
    if (typeof value !== 'string' || value.trim() === '') return '';
    const url = value.trim();
    if (/^(https?:|\/|\.\/|\.\.\/|data:image\/)/i.test(url)) return url;
    return '';
}

export default class CardComponent extends ComponentBase {
    /** @returns {CardState} */
    defaultState() {
        return {
            id: '',
            title: '',
            meta: '',
            image: '',
            alt: '',
            variant: '',
            loading: false,
            disabled: false,
            href: '',
        };
    }

    /** DOM iskeleti bir kez kurulur. */
    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('card');
        root.dataset.component = 'card';
        root.setAttribute('role', 'listitem');

        const image = document.createElement('img');
        image.className = 'card__image';
        image.alt = '';
        image.loading = 'lazy';
        image.decoding = 'async';
        image.hidden = true;

        const body = document.createElement('div');
        body.className = 'card__body';

        const title = document.createElement('h3');
        title.className = 'card__title';

        const meta = document.createElement('p');
        meta.className = 'card__meta';

        body.append(title, meta);
        root.append(image, body);

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(root, 'keydown', (event) => this.#onKeyDown(event));

        if (root.hasAttribute('tabindex') === false) root.tabIndex = 0;
    }

    /** İlk render. */
    mount() {
        this.render();
    }

    /**
     * State değişiminden sonra tek render noktası.
     * @param {object} prev
     * @param {CardState} next
     */
    onUpdate(prev, next) {
        if (prev.title !== next.title
            || prev.meta !== next.meta
            || prev.image !== next.image
            || prev.variant !== next.variant
            || prev.loading !== next.loading
            || prev.disabled !== next.disabled) {
            this.render();
        }
    }

    /** DOM'u state'e eşler — textContent kullanılır (innerHTML YASAK). */
    render() {
        const root = this.el;
        if (!root) return;

        const state = this.state;
        const image = this.$('.card__image');
        const title = this.$('.card__title');
        const meta = this.$('.card__meta');

        Object.values(VARIANTS).forEach((cls) => root.classList.remove(cls));
        if (state.variant && VARIANTS[state.variant]) {
            root.classList.add(VARIANTS[state.variant]);
        }

        root.classList.toggle('is-loading', state.loading === true);
        root.classList.toggle('is-disabled', state.disabled === true);
        root.setAttribute('aria-busy', String(state.loading === true));

        if (image instanceof HTMLImageElement) {
            const src = safeImageSrc(state.image);
            if (src && !state.loading) {
                image.src = src;
                image.alt = typeof state.alt === 'string' ? state.alt : '';
                image.hidden = false;
            } else {
                image.removeAttribute('src');
                image.alt = '';
                image.hidden = true;
            }
        }
        if (title instanceof HTMLElement) title.textContent = state.title || '';
        if (meta instanceof HTMLElement) {
            meta.textContent = state.meta || '';
            meta.hidden = meta.textContent === '';
        }
    }

    /**
     * Kart aktivasyonu (click / Enter / Space).
     * @param {Event} event
     */
    #activate(event) {
        const state = this.state;
        if (state.disabled || state.loading) return;
        if (state.href) return; // link modunda tarayıcı devralır

        event.preventDefault();
        this.emit(eventName('card', 'click'), {
            id: state.id,
            title: state.title,
        });
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        this.#activate(event);
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        this.#activate(event);
    }
}
