/**
 * C11 Avatar bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/AvatarComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C11
 * BEM: .avatar, .avatar--sm, .avatar--md, .avatar--lg, .avatar--xl
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';

/** @type {Readonly<Record<string, string>>} Boyut → BEM sınıfı */
const SIZES = Object.freeze({ sm: 'avatar--sm', md: 'avatar--md', lg: 'avatar--lg', xl: 'avatar--xl' });

/**
 * @typedef {Object} AvatarState
 * @property {string} name
 * @property {string} src
 * @property {''|'sm'|'md'|'lg'|'xl'} size
 * @property {boolean} online
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

/**
 * Ad baş harflerini üretir (ör. "Ada Lovelace" → "AL").
 * @param {string} name
 * @returns {string}
 */
function initials(name) {
    if (typeof name !== 'string' || name.trim() === '') return '';
    const parts = name.trim().split(/\s+/).slice(0, 2);
    return parts.map((part) => part.charAt(0).toLocaleUpperCase('tr-TR')).join('');
}

export default class AvatarComponent extends ComponentBase {
    /** @returns {AvatarState} */
    defaultState() {
        return { name: '', src: '', size: '', online: false };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('avatar');
        root.dataset.component = 'avatar';

        const image = document.createElement('img');
        image.className = 'avatar__image';
        image.alt = '';
        image.decoding = 'async';
        image.hidden = true;

        const fallback = document.createElement('span');
        fallback.className = 'avatar__fallback';
        fallback.setAttribute('aria-hidden', 'true');

        const status = document.createElement('span');
        status.className = 'avatar__status';
        status.hidden = true;

        root.append(image, fallback, status);
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {AvatarState} next */
    onUpdate(prev, next) {
        if (prev.name !== next.name || prev.src !== next.src || prev.size !== next.size || prev.online !== next.online) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { name, src, size, online } = this.state;

        Object.values(SIZES).forEach((cls) => root.classList.remove(cls));
        if (size && SIZES[size]) root.classList.add(SIZES[size]);

        root.classList.toggle('avatar--online', online === true);

        const image = this.$('.avatar__image');
        if (image instanceof HTMLImageElement) {
            const safe = safeImageSrc(src);
            if (safe) {
                image.src = safe;
                image.alt = name;
                image.hidden = false;
            } else {
                image.removeAttribute('src');
                image.alt = '';
                image.hidden = true;
            }
        }

        const fallback = this.$('.avatar__fallback');
        if (fallback instanceof HTMLElement) fallback.textContent = initials(name);

        const status = this.$('.avatar__status');
        if (status instanceof HTMLElement) {
            status.hidden = online !== true;
            status.setAttribute('aria-label', online ? 'Çevrimiçi' : 'Çevrimdışı');
        }

        // Görsel yoksa erişilebilir ad yine de görünür/korunur
        root.setAttribute('title', name || '');
    }
}
