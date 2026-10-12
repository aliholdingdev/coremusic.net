/**
 * C06 Tab bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/TabsComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C06
 * BEM: .tab, .tab--active, .tab__indicator, .tab-list
 *
 * MIGRASYON: interactive/TabsComponent.js davranış sözleşmesi korunur
 * (init, mount, activate(index), getActiveIndex(), getTabCount(), destroy).
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';
import { setAria } from '../base/ComponentAccessibility.js';

/**
 * @typedef {Object} TabsState
 * @property {string[]} tabs
 * @property {number} activeIndex
 * @property {string[]} panels
 */

export default class TabsComponent extends ComponentBase {
    /** @returns {TabsState} */
    defaultState() {
        return { tabs: [], activeIndex: 0, panels: [] };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('tabs');
        root.dataset.component = 'tabs';

        const list = document.createElement('div');
        list.className = 'tab-list';
        list.setAttribute('role', 'tablist');

        const panels = document.createElement('div');
        panels.className = 'tabs__panels';

        root.append(list, panels);

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(root, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.#build();
    }

    /** @param {object} prev @param {TabsState} next */
    onUpdate(prev, next) {
        if (prev.tabs !== next.tabs || prev.panels !== next.panels) this.#build();
        else if (prev.activeIndex !== next.activeIndex) this.#syncActive();
    }

    /** Sekme + panel iskeletini state'ten kurar. */
    #build() {
        const root = this.el;
        const list = this.$('.tab-list');
        const panels = this.$('.tabs__panels');
        if (!root || !(list instanceof HTMLElement) || !(panels instanceof HTMLElement)) return;

        const { tabs, panels: panelTexts } = this.state;

        list.textContent = '';
        panels.textContent = '';

        tabs.forEach((label, index) => {
            const tab = document.createElement('button');
            tab.type = 'button';
            tab.className = 'tab';
            tab.id = `${this.id}-tab-${index}`;
            tab.dataset.index = String(index);
            tab.setAttribute('role', 'tab');
            setAria(tab, {
                'aria-selected': 'false',
                'aria-controls': `${this.id}-panel-${index}`,
            });
            tab.textContent = label;
            list.appendChild(tab);

            const panel = document.createElement('div');
            panel.className = 'tabs__panel';
            panel.id = `${this.id}-panel-${index}`;
            panel.dataset.index = String(index);
            panel.setAttribute('role', 'tabpanel');
            setAria(panel, { 'aria-labelledby': `${this.id}-tab-${index}` });
            panel.hidden = true;
            panel.textContent = panelTexts[index] ?? '';
            panels.appendChild(panel);
        });

        this.#syncActive();
    }

    /** Aktif sekme/panel görünürlüğünü eşler. */
    #syncActive() {
        const { activeIndex } = this.state;
        const tabs = this.$$('.tab');
        const panels = this.$$('.tabs__panel');

        tabs.forEach((tab, index) => {
            const active = index === activeIndex;
            tab.classList.toggle('tab--active', active);
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
        });

        panels.forEach((panel, index) => {
            if (panel instanceof HTMLElement) panel.hidden = index !== activeIndex;
        });
    }

    /**
     * Aktif sekmeyi değiştirir (eski API — korunur).
     * @param {number} index
     */
    activate(index) {
        const { tabs, activeIndex } = this.state;
        if (!Number.isInteger(index) || index < 0 || index >= tabs.length) return this;
        if (index === activeIndex) return this;

        this.setState({ activeIndex: index });
        this.emit(eventName('tabs', 'change'), {
            index,
            label: tabs[index],
        });
        return this;
    }

    /** @returns {number} */
    getActiveIndex() {
        return this.state.activeIndex;
    }

    /** @returns {number} */
    getTabCount() {
        return this.state.tabs.length;
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const target = event.target instanceof Element ? event.target.closest('.tab') : null;
        if (!(target instanceof HTMLElement) || !this.el?.contains(target)) return;
        this.activate(Number(target.dataset.index));
    }

    /** @param {KeyboardEvent} event — Ok tuşları ile sekme gezinmesi (WAI-ARIA) */
    #onKeyDown(event) {
        const key = event.key;
        if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(key)) return;

        const count = this.getTabCount();
        if (count === 0) return;

        const current = this.getActiveIndex();
        let next = current;

        if (key === 'ArrowRight') next = (current + 1) % count;
        else if (key === 'ArrowLeft') next = (current - 1 + count) % count;
        else if (key === 'Home') next = 0;
        else if (key === 'End') next = count - 1;

        event.preventDefault();
        this.activate(next);

        const tab = this.$$('.tab')[next];
        if (tab instanceof HTMLElement) tab.focus();
    }
}
