/**
 * C12 Tooltip bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/TooltipComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C12
 * BEM: .tooltip, .tooltip--top, .tooltip--bottom, .tooltip__arrow
 *
 * Not: tooltip içeriği textContent ile basılır (innerHTML YASAK).
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';
import { setAria } from '../base/ComponentAccessibility.js';

/** @type {Readonly<Record<string, string>>} Konum → BEM sınıfı */
const PLACEMENTS = Object.freeze({ top: 'tooltip--top', bottom: 'tooltip--bottom' });

/**
 * @typedef {Object} TooltipState
 * @property {string} content
 * @property {''|'top'|'bottom'} placement
 * @property {boolean} visible
 */

export default class TooltipComponent extends ComponentBase {
    /** @returns {TooltipState} */
    defaultState() {
        return { content: '', placement: 'top', visible: false };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('tooltip');
        root.dataset.component = 'tooltip';
        root.setAttribute('role', 'tooltip');
        root.hidden = true;

        const arrow = document.createElement('span');
        arrow.className = 'tooltip__arrow';
        arrow.setAttribute('aria-hidden', 'true');

        const content = document.createElement('span');
        content.className = 'tooltip__text';

        root.append(content, arrow);

        // Tetikleyici: bileşenin önceki kardeşidir (data-tooltip-trigger)
        const trigger = this.#findTrigger();
        if (trigger) {
            const id = `${this.id}-tooltip`;
            root.id = id;
            setAria(trigger, { 'aria-describedby': id });

            this.on(trigger, 'mouseenter', () => this.show());
            this.on(trigger, 'mouseleave', () => this.hide());
            this.on(trigger, 'focus', () => this.show());
            this.on(trigger, 'blur', () => this.hide());
            this.on(trigger, 'keydown', (event) => this.#onKeyDown(event));
        }
    }

    /** @returns {HTMLElement|null} Tetikleyici eleman */
    #findTrigger() {
        const host = this.el?.parentElement;
        if (!host) return null;
        const explicit = host.querySelector('[data-tooltip-trigger]');
        if (explicit instanceof HTMLElement) return explicit;
        return host.querySelector(':scope > [data-tooltip]') || null;
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {TooltipState} next */
    onUpdate(prev, next) {
        if (prev.content !== next.content || prev.placement !== next.placement || prev.visible !== next.visible) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { content, placement, visible } = this.state;

        Object.values(PLACEMENTS).forEach((cls) => root.classList.remove(cls));
        const cls = PLACEMENTS[placement] || PLACEMENTS.top;
        root.classList.add(cls);

        root.classList.toggle('is-visible', visible);
        root.hidden = !visible;

        const text = this.$('.tooltip__text');
        if (text instanceof HTMLElement) text.textContent = content || '';
    }

    /** Tooltip'ı gösterir. */
    show() {
        if (this.state.visible) return this;
        this.setState({ visible: true });
        this.emit(eventName('tooltip', 'show'), { content: this.state.content });
        return this;
    }

    /** Tooltip'ı gizler. */
    hide() {
        if (!this.state.visible) return this;
        this.setState({ visible: false });
        this.emit(eventName('tooltip', 'hide'), {});
        return this;
    }

    /** @param {KeyboardEvent} event */
    #onKeyDown(event) {
        if (event.key === 'Escape') this.hide();
    }
}
