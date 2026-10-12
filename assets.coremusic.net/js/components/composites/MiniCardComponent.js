/**
 * C19 Mini Card bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/MiniCardComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C19
 * BEM: .home-mini-card, .home-mini-card__art, .home-mini-card__info,
 *      .home-mini-card__title, .home-mini-card__subtitle, .home-mini-card__duration
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';

/**
 * @typedef {Object} MiniCardState
 * @property {string} id
 * @property {string} title
 * @property {string} subtitle
 * @property {string} duration
 * @property {string} art
 * @property {boolean} playing
 */

/**
 * Sadece güvenli görsel URL'lerine izin verir.
 * @param {string} value
 * @returns {string}
 */
function safeImageSrc(value) {
    if (typeof value !== 'string' || value.trim() === '') return '';
    const url = value.trim();
    return /^(https?:|\/|\.\.\/|data:image\/)/i.test(url) ? url : '';
}

export default class MiniCardComponent extends ComponentBase {
    /** @returns {MiniCardState} */
    defaultState() {
        return { id: '', title: '', subtitle: '', duration: '', art: '', playing: false };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('home-mini-card');
        root.dataset.component = 'home-mini-card';
        root.setAttribute('role', 'listitem');
        root.tabIndex = 0;

        const art = document.createElement('img');
        art.className = 'home-mini-card__art';
        art.alt = '';
        art.loading = 'lazy';
        art.decoding = 'async';
        art.hidden = true;

        const info = document.createElement('div');
        info.className = 'home-mini-card__info';

        const title = document.createElement('span');
        title.className = 'home-mini-card__title';

        const subtitle = document.createElement('span');
        subtitle.className = 'home-mini-card__subtitle';

        const duration = document.createElement('span');
        duration.className = 'home-mini-card__duration';

        info.append(title, subtitle, duration);
        root.append(art, info);

        this.on(root, 'click', () => this.#activate());
        this.on(root, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {MiniCardState} next */
    onUpdate(prev, next) {
        if (prev.title !== next.title || prev.subtitle !== next.subtitle
            || prev.duration !== next.duration || prev.art !== next.art
            || prev.playing !== next.playing) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { title, subtitle, duration, art, playing } = this.state;
        root.classList.toggle('is-playing', playing === true);
        root.setAttribute('aria-label', `${title}${subtitle ? ` — ${subtitle}` : ''}`);

        const artEl = this.$('.home-mini-card__art');
        if (artEl instanceof HTMLImageElement) {
            const src = safeImageSrc(art);
            if (src) {
                artEl.src = src;
                artEl.alt = title ? `${title} kapak görseli` : '';
                artEl.hidden = false;
            } else {
                artEl.removeAttribute('src');
                artEl.alt = '';
                artEl.hidden = true;
            }
        }

        const titleEl = this.$('.home-mini-card__title');
        if (titleEl instanceof HTMLElement) titleEl.textContent = title || '';

        const subtitleEl = this.$('.home-mini-card__subtitle');
        if (subtitleEl instanceof HTMLElement) {
            subtitleEl.textContent = subtitle || '';
            subtitleEl.hidden = subtitleEl.textContent === '';
        }

        const durationEl = this.$('.home-mini-card__duration');
        if (durationEl instanceof HTMLElement) {
            durationEl.textContent = duration || '';
            durationEl.hidden = durationEl.textContent === '';
        }
    }

    #activate() {
        if (this.state.playing) return;
        this.emit(eventName('home-mini-card', 'select'), {
            id: this.state.id,
            title: this.state.title,
        });
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        this.#activate();
    }
}
