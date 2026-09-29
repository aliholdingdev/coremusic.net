/**
 * C17 Widget Area (Home Widget Grid) bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/WidgetAreaComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C17
 *
 * KARAR (Faz 2 Batch 2 checkpoint — "üretim kazandı"):
 *   Envanter C17 `.home-widget-area/.home-widget-panel` adlarını HİÇBİR yerde
 *   kullanılmıyor. Üretimde canlı olan yapı:
 *     - CSS: 05_Pages/_home-layout.css `.home-widget-grid` (grid, --widget-grid-cols)
 *             05_Pages/_home-components.css `.home-widget` + __header/__icon/__title/
 *             __subtitle/__info/__glass/--compact
 *     - JS:  js/features/WidgetManager.js  → .home-widget, .home-widget__title,
 *            .home-widget__subtitle, .home-widget__info, .home-widget__folder-btn
 *            js/device-layout-updater.js   → .home-widget-grid, .home-widget,
 *            --widget-grid-cols
 *   Bu bileşen üretim sınıflarını üretir; envanter C17 satırı vault-updater'a
 *   düzeltme olarak yazılır (vault çelişkisi #9).
 *
 * BEM (üretim): .home-widget-grid, .home-widget, .home-widget__header,
 *               .home-widget__icon, .home-widget__title, .home-widget__subtitle
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';

/**
 * @typedef {Object} WidgetItem
 * @property {string} id
 * @property {string} icon
 * @property {string} title
 * @property {string} subtitle
 * @property {string} [info]
 * @property {boolean} [compact]
 */

/**
 * @typedef {Object} WidgetAreaState
 * @property {WidgetItem[]} items
 * @property {number} columns  — device-layout-updater.js ile aynı değişken: --widget-grid-cols
 */

export default class WidgetAreaComponent extends ComponentBase {
    /** @returns {WidgetAreaState} */
    defaultState() {
        return { items: [], columns: 2 };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('home-widget-grid');
        root.dataset.component = 'home-widget-grid';

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(root, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.#build();
    }

    /** @param {object} prev @param {WidgetAreaState} next */
    onUpdate(prev, next) {
        if (prev.items !== next.items || prev.columns !== next.columns) this.#build();
    }

    /** Widget panellerini state'ten kurar (textContent — innerHTML YASAK). */
    #build() {
        const root = this.el;
        if (!root) return;

        const { items, columns } = this.state;

        // Sadece kendi oluşturduğu düğümleri temizle
        this.$$('.home-widget').forEach((node) => node.remove());

        // Aynı değişkeni device-layout-updater.js de yazar — çakışma yok, son değer geçerli
        root.style.setProperty('--widget-grid-cols', String(columns > 0 ? columns : 2));

        items.forEach((item) => {
            const panel = document.createElement('section');
            panel.className = 'home-widget';
            panel.dataset.component = 'home-widget';
            panel.dataset.id = item.id || '';
            if (item.compact) panel.classList.add('home-widget--compact');
            panel.tabIndex = 0;

            const header = document.createElement('div');
            header.className = 'home-widget__header';

            const icon = document.createElement('span');
            icon.className = 'home-widget__icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = item.icon || '';

            const title = document.createElement('h3');
            title.className = 'home-widget__title';
            title.textContent = item.title || '';

            header.append(icon, title);

            const subtitle = document.createElement('p');
            subtitle.className = 'home-widget__subtitle';
            subtitle.textContent = item.subtitle || '';
            subtitle.hidden = subtitle.textContent === '';

            panel.append(header, subtitle);

            if (item.info) {
                const info = document.createElement('p');
                info.className = 'home-widget__info';
                info.textContent = item.info;
                panel.appendChild(info);
            }

            root.appendChild(panel);
        });
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const panel = event.target instanceof Element
            ? event.target.closest('.home-widget')
            : null;
        if (!(panel instanceof HTMLElement) || !this.el?.contains(panel)) return;
        this.#activate(panel);
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const panel = event.target instanceof Element
            ? event.target.closest('.home-widget')
            : null;
        if (!(panel instanceof HTMLElement)) return;
        event.preventDefault();
        this.#activate(panel);
    }

    /** @param {HTMLElement} panel */
    #activate(panel) {
        const item = this.state.items.find((entry) => entry.id === panel.dataset.id);
        this.emit(eventName('home-widget-grid', 'select'), {
            id: panel.dataset.id || '',
            title: item ? item.title : '',
        });
    }
}
