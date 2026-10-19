/**
 * C14 Dropdown bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/DropdownComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C14
 * BEM: .dropdown, .dropdown__menu, .dropdown__item, .dropdown__item--active
 *
 * MIGRASYON: interactive/DropdownComponent.js davranış sözleşmesi korunur
 * (init, mount, open(), close(), toggle(), select(index), destroy).
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';
import { setAria, announce } from '../base/ComponentAccessibility.js';

/**
 * @typedef {Object} DropdownState
 * @property {string} label
 * @property {string[]} items
 * @property {number} selectedIndex
 * @property {boolean} open
 */

export default class DropdownComponent extends ComponentBase {
    /** @returns {DropdownState} */
    defaultState() {
        return { label: 'Seç', items: [], selectedIndex: -1, open: false };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('dropdown');
        root.dataset.component = 'dropdown';
        root.hidden = false;

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'dropdown__trigger';
        trigger.dataset.action = 'toggle';
        trigger.setAttribute('aria-haspopup', 'listbox');
        setAria(trigger, { 'aria-expanded': 'false' });

        const triggerText = document.createElement('span');
        triggerText.className = 'dropdown__trigger-text';
        trigger.appendChild(triggerText);

        const menu = document.createElement('ul');
        menu.className = 'dropdown__menu';
        menu.setAttribute('role', 'listbox');
        menu.hidden = true;

        root.append(trigger, menu);

        this.on(root, 'click', (event) => this.#onClick(event));
        this.on(document, 'click', (event) => this.#onOutsideClick(event));
        this.on(document, 'keydown', (event) => this.#onKeyDown(event));
    }

    mount() {
        this.#build();
    }

    /** @param {object} prev @param {DropdownState} next */
    onUpdate(prev, next) {
        if (prev.items !== next.items) this.#build();
        else if (prev.selectedIndex !== next.selectedIndex || prev.open !== next.open || prev.label !== next.label) {
            this.#sync();
        }
    }

    /** Menü öğelerini state'ten kurar (textContent — innerHTML YASAK). */
    #build() {
        const menu = this.$('.dropdown__menu');
        if (!(menu instanceof HTMLElement)) return;

        menu.textContent = '';

        this.state.items.forEach((label, index) => {
            const item = document.createElement('li');
            item.className = 'dropdown__item';
            item.dataset.index = String(index);
            item.setAttribute('role', 'option');
            setAria(item, { 'aria-selected': 'false' });
            item.tabIndex = -1;
            item.textContent = label;
            menu.appendChild(item);
        });

        this.#sync();
    }

    /** Görünür durumu state'e eşler. */
    #sync() {
        const root = this.el;
        const trigger = this.$('.dropdown__trigger');
        const triggerText = this.$('.dropdown__trigger-text');
        const menu = this.$('.dropdown__menu');
        const { open, selectedIndex, label, items } = this.state;

        if (root) root.classList.toggle('is-open', open);
        if (menu instanceof HTMLElement) menu.hidden = !open;

        if (trigger instanceof HTMLElement) {
            trigger.setAttribute('aria-expanded', String(open));
        }
        if (triggerText instanceof HTMLElement) {
            const selected = selectedIndex >= 0 ? items[selectedIndex] : '';
            triggerText.textContent = selected || label || 'Seç';
        }

        if (menu instanceof HTMLElement) {
            Array.from(menu.children).forEach((child, index) => {
                if (!(child instanceof HTMLElement)) return;
                const active = index === selectedIndex;
                child.classList.toggle('dropdown__item--active', active);
                child.setAttribute('aria-selected', String(active));
            });
        }
    }

    /** Menüyü açar (eski API — korunur). */
    open() {
        if (this.state.open) return this;
        this.setState({ open: true });
        announce(this.el || document.body, 'Menü açıldı');
        return this;
    }

    /** Menüyü kapatır (eski API — korunur). */
    close() {
        if (!this.state.open) return this;
        this.setState({ open: false });
        return this;
    }

    /** Aç/kapa (eski API — korunur). */
    toggle() {
        return this.state.open ? this.close() : this.open();
    }

    /**
     * Öğe seçer (eski API — korunur).
     * @param {number} index
     */
    select(index) {
        const { items, selectedIndex } = this.state;
        if (!Number.isInteger(index) || index < 0 || index >= items.length) return this;
        if (index === selectedIndex) return this.close();

        this.setState({ selectedIndex: index, open: false });
        this.emit(eventName('dropdown', 'select'), { index, label: items[index] });
        return this;
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const target = event.target instanceof Element
            ? event.target.closest('[data-action], .dropdown__item')
            : null;
        if (!target || !this.el?.contains(target)) return;

        if (target.dataset.action === 'toggle') {
            this.toggle();
            return;
        }
        if (target instanceof HTMLElement && target.classList.contains('dropdown__item')) {
            this.select(Number(target.dataset.index));
        }
    }

    /** Dışarı tıklama → kapat. @param {MouseEvent} event */
    #onOutsideClick(event) {
        if (!this.state.open) return;
        const target = event.target;
        if (target instanceof Node && this.el?.contains(target)) return;
        this.close();
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key === 'Escape' && this.state.open) {
            event.preventDefault();
            this.close();
            const trigger = this.$('.dropdown__trigger');
            if (trigger instanceof HTMLElement) trigger.focus();
            return;
        }
        if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;

        const { items, selectedIndex, open } = this.state;
        if (items.length === 0) return;

        event.preventDefault();
        if (!open) {
            this.open();
            return;
        }

        const delta = event.key === 'ArrowDown' ? 1 : -1;
        const next = (selectedIndex + delta + items.length) % items.length;
        this.setState({ selectedIndex: next });
    }
}
