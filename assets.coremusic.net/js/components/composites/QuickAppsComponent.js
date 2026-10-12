/**
 * C18 Quick Apps Row bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/QuickAppsComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C18
 * BEM: .home-quick-apps, .home-quick-app, .home-quick-app__icon, .home-quick-app__label
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';

/**
 * @typedef {Object} QuickApp
 * @property {string} id
 * @property {string} label
 * @property {string} icon
 * @property {string} [href]
 */

/**
 * @typedef {Object} QuickAppsState
 * @property {QuickApp[]} apps
 */

export default class QuickAppsComponent extends ComponentBase {
    /** @returns {QuickAppsState} */
    defaultState() {
        return { apps: [] };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('home-quick-apps');
        root.dataset.component = 'home-quick-apps';
        root.setAttribute('role', 'list');

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(root, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.#build();
    }

    /** @param {object} prev @param {QuickAppsState} next */
    onUpdate(prev, next) {
        if (prev.apps !== next.apps) this.#build();
    }

    /** Uygulama kısayollarını state'ten kurar. */
    #build() {
        const root = this.el;
        if (!root) return;

        // Sadece oluşturduğumuz düğümleri temizle (başka içerik korunur)
        this.$$('.home-quick-app').forEach((node) => node.remove());

        this.state.apps.forEach((app) => {
            const item = document.createElement('div');
            item.className = 'home-quick-app';
            item.dataset.component = 'home-quick-app';
            item.dataset.id = app.id || '';
            item.setAttribute('role', 'listitem');
            item.tabIndex = 0;

            const icon = document.createElement('span');
            icon.className = 'home-quick-app__icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = app.icon || '';

            const label = document.createElement('span');
            label.className = 'home-quick-app__label';
            label.textContent = app.label || '';

            item.append(icon, label);
            root.appendChild(item);
        });
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const item = event.target instanceof Element
            ? event.target.closest('.home-quick-app')
            : null;
        if (!(item instanceof HTMLElement) || !this.el?.contains(item)) return;
        this.#activate(item);
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const item = event.target instanceof Element
            ? event.target.closest('.home-quick-app')
            : null;
        if (!(item instanceof HTMLElement)) return;
        event.preventDefault();
        this.#activate(item);
    }

    /** @param {HTMLElement} item */
    #activate(item) {
        const app = this.state.apps.find((entry) => entry.id === item.dataset.id);
        this.emit(eventName('home-quick-apps', 'select'), {
            id: item.dataset.id || '',
            label: app ? app.label : '',
            href: app && app.href ? app.href : '',
        });
    }
}
