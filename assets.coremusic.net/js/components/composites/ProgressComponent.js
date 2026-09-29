/**
 * C15 Progress bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/ProgressComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C15
 * BEM: .progress, .progress__bar, .progress__fill
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { setAria } from '../base/ComponentAccessibility.js';

/**
 * @typedef {Object} ProgressState
 * @property {number} value 0-100
 * @property {number} max
 * @property {boolean} indeterminate
 * @property {string} label
 */

/** Değeri [0, max] aralığına kırpıp sayıya çevirir. */
function clamp(value, max) {
    const num = Number(value);
    if (!Number.isFinite(num)) return 0;
    return Math.min(Math.max(num, 0), max);
}

export default class ProgressComponent extends ComponentBase {
    /** @returns {ProgressState} */
    defaultState() {
        return { value: 0, max: 100, indeterminate: false, label: '' };
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('progress');
        root.dataset.component = 'progress';

        const bar = document.createElement('div');
        bar.className = 'progress__bar';

        const fill = document.createElement('div');
        fill.className = 'progress__fill';

        bar.appendChild(fill);
        root.appendChild(bar);

        setAria(root, { role: 'progressbar', 'aria-valuemin': '0' });
    }

    mount() {
        this.render();
    }

    /** @param {object} prev @param {ProgressState} next */
    onUpdate(prev, next) {
        if (prev.value !== next.value || prev.max !== next.max
            || prev.indeterminate !== next.indeterminate || prev.label !== next.label) {
            this.render();
        }
    }

    render() {
        const root = this.el;
        if (!root) return;

        const { value, max, indeterminate, label } = this.state;
        const safeMax = max > 0 ? max : 100;
        const current = clamp(value, safeMax);
        const percent = (current / safeMax) * 100;

        root.classList.toggle('progress--indeterminate', indeterminate === true);
        root.setAttribute('aria-valuenow', indeterminate ? '' : String(current));
        root.setAttribute('aria-valuemax', String(safeMax));
        if (label) root.setAttribute('aria-label', label);

        const fill = this.$('.progress__fill');
        if (fill instanceof HTMLElement) {
            // CSS custom property ile genişlik (inline style YASAK)
            fill.style.setProperty('--progress-percent', `${percent.toFixed(2)}%`);
        }
    }

    /**
     * İlerleme değerini günceller.
     * @param {number} value
     * @param {number} [max]
     */
    setValue(value, max) {
        const next = { value: clamp(value, max ?? this.state.max) };
        if (Number.isFinite(max) && Number(max) > 0) next.max = Number(max);
        this.setState(next);
        return this;
    }
}
