/**
 * C02 Hero bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/HeroComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C02
 * BEM: .hero, .hero__title, .hero__subtitle, .hero__overlay
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';

/**
 * @typedef {Object} HeroState
 * @property {string} title
 * @property {string} subtitle
 * @property {string} image
 * @property {string} actionLabel
 * @property {boolean} loading
 */

/**
 * Sadece güvenli görsel URL'lerine izin verir (javascript: vb. engeli).
 * @param {string} value
 * @returns {string}
 */
function safeImageSrc(value) {
    if (typeof value !== 'string' || value.trim() === '') return '';
    const url = value.trim();
    return /^(https?:|\/|\.\.\/|data:image\/)/i.test(url) ? url : '';
}

export default class HeroComponent extends ComponentBase {
    /** @returns {HeroState} */
    defaultState() {
        return { title: '', subtitle: '', image: '', actionLabel: '', loading: false };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('hero');
        root.dataset.component = 'hero';

        const image = document.createElement('img');
        image.className = 'hero__image';
        image.alt = '';
        image.decoding = 'async';
        image.hidden = true;

        const overlay = document.createElement('div');
        overlay.className = 'hero__overlay';
        overlay.setAttribute('aria-hidden', 'true');

        const content = document.createElement('div');
        content.className = 'hero__content';

        const title = document.createElement('h1');
        title.className = 'hero__title';

        const subtitle = document.createElement('p');
        subtitle.className = 'hero__subtitle';

        const action = document.createElement('button');
        action.type = 'button';
        action.className = 'btn btn--primary hero__action';
        action.dataset.action = 'cta';
        action.hidden = true;

        content.append(title, subtitle, action);
        root.append(image, overlay, content);

        this.on(root, 'click', (event) => this.#onClick(event));
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {HeroState} next */
    onUpdate(prev, next) {
        if (prev.title !== next.title
            || prev.subtitle !== next.subtitle
            || prev.image !== next.image
            || prev.actionLabel !== next.actionLabel
            || prev.loading !== next.loading) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { title, subtitle, image, actionLabel, loading } = this.state;
        root.classList.toggle('is-loading', loading === true);
        root.setAttribute('aria-busy', String(loading === true));

        const imageEl = this.$('.hero__image');
        if (imageEl instanceof HTMLImageElement) {
            const src = safeImageSrc(image);
            if (src && !loading) {
                imageEl.src = src;
                imageEl.hidden = false;
            } else {
                imageEl.removeAttribute('src');
                imageEl.hidden = true;
            }
        }

        const titleEl = this.$('.hero__title');
        if (titleEl instanceof HTMLElement) titleEl.textContent = title || '';

        const subtitleEl = this.$('.hero__subtitle');
        if (subtitleEl instanceof HTMLElement) {
            subtitleEl.textContent = subtitle || '';
            subtitleEl.hidden = subtitleEl.textContent === '';
        }

        const actionEl = this.$('.hero__action');
        if (actionEl instanceof HTMLElement) {
            actionEl.textContent = actionLabel || '';
            actionEl.hidden = actionLabel === '';
        }
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const target = event.target instanceof Element
            ? event.target.closest('[data-action="cta"]')
            : null;
        if (!target || !this.el?.contains(target)) return;
        this.emit(eventName('hero', 'cta'), { title: this.state.title });
    }
}
