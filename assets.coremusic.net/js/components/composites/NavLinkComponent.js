/**
 * C01 NavLink bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/NavLinkComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C01
 * BEM: .nav-link, .nav-link--active, .nav-link--hover
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';
import { setAria } from '../base/ComponentAccessibility.js';

/**
 * @typedef {Object} NavLinkState
 * @property {string} label
 * @property {string} href
 * @property {boolean} active
 * @property {string} badge
 */

export default class NavLinkComponent extends ComponentBase {
    /** @returns {NavLinkState} */
    defaultState() {
        return { label: '', href: '#', active: false, badge: '' };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('nav-link');
        root.dataset.component = 'nav-link';
        root.setAttribute('role', 'link');

        const label = document.createElement('span');
        label.className = 'nav-link__text';

        const badge = document.createElement('span');
        badge.className = 'nav-link__badge';
        badge.hidden = true;

        root.append(label, badge);

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(root, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {NavLinkState} next */
    onUpdate(prev, next) {
        if (prev.label !== next.label || prev.active !== next.active || prev.badge !== next.badge) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { label, href, active, badge } = this.state;

        root.classList.toggle('nav-link--active', active);
        setAria(root, { 'aria-current': active ? 'page' : 'false' });

        if (href) root.setAttribute('href', href);

        const labelEl = this.$('.nav-link__text');
        if (labelEl instanceof HTMLElement) labelEl.textContent = label || '';

        const badgeEl = this.$('.nav-link__badge');
        if (badgeEl instanceof HTMLElement) {
            badgeEl.textContent = badge || '';
            badgeEl.hidden = badge === '';
        }
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const state = this.state;
        if (state.active) event.preventDefault();
        this.emit(eventName('nav-link', 'navigate'), {
            href: state.href,
            label: state.label,
        });
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        this.#onClick(/** @type {any} */ (event));
    }
}
