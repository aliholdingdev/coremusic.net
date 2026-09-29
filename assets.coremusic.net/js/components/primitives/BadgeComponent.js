/**
 * BadgeComponent — C10 Figma Badge (Faz 2 / Batch 1).
 *
 * HTML contract:
 * <span class="badge badge--primary" data-cm-component="cm-badge">Etiket</span>
 * <button class="badge badge--success" data-cm-component="cm-badge" data-cm-count="3">…</button>
 *
 * Figma (page 18:2907): padding 2px 8px, radius 9999px, font 12px/600;
 * bg = renk %20 tint (--cm-badge-tint-alpha), metin = renk.
 *
 * Emits: cm:badge:click (yalnız etkileşimli kök: button/a/[role="button"])
 *
 * @package CoreMusic\Components\Primitives
 */
import ComponentBase from '../base/ComponentBase.js';
import { CM_EVENTS } from '../base/ComponentEvents.js';

/** Modifier → token eşlemesi (envanter C10: primary|success|error + neutral) */
const VARIANTS = ['primary', 'success', 'error', 'neutral'];

export default class BadgeComponent extends ComponentBase {
    /** @type {HTMLElement|null} */
    #control = null;

    /** @type {boolean} Tıklanabilir kök mü? */
    #interactive = false;

    defaultState() {
        return {
            label: '',
            count: '',        // boşsa rozet metni kullanılır
            variant: 'neutral',
        };
    }

    init() {
        const el = this.el;
        if (!el) return;
        this.#control = el;

        // Varyantı sınıftan oku (markup kaynaklıdır)
        const variant =
            VARIANTS.find((v) => el.classList.contains(`badge--${v}`)) ?? this.state.variant;

        this.#interactive =
            el.dataset?.cmInteractive === 'true' ||
            el.matches('button, a[href], [role="button"]');

        this.setState({
            label: el.dataset?.cmLabel ?? (el.textContent ?? '').trim(),
            count: el.dataset?.cmCount ?? '',
            variant,
        });
    }

    mount() {
        if (!this.#control || !this.#interactive) return;

        this.on(this.#control, 'click', () => {
            this.emit(CM_EVENTS.BADGE_CLICK, {
                id: this.id,
                label: this.state.label,
                count: this.state.count,
                variant: this.state.variant,
            });
            // Link default'u engellenmez (SPA router yönetir)
        });
    }

    /**
     * Tek render noktası — state → DOM senkronu.
     * @param {object} _prev
     * @param {object} next
     */
    onUpdate(_prev, next) {
        const el = this.#control;
        if (!el) return;

        VARIANTS.forEach((v) => el.classList.toggle(`badge--${v}`, next.variant === v));

        const hasCount = next.count !== '' && next.count !== undefined && next.count !== null;
        el.classList.toggle('badge--count', Boolean(hasCount));

        // Metin: count > label > mevcut (textContent — innerHTML YASAK)
        const text = hasCount ? String(next.count) : next.label;
        if (text && el.textContent !== text) el.textContent = text;

        if (hasCount) el.setAttribute('aria-label', `${next.label}: ${next.count}`);
    }

    /**
     * @param {string} variant — primary|success|error|neutral
     * @returns {BadgeComponent} this
     */
    setVariant(variant) {
        if (!VARIANTS.includes(variant)) return this;
        return this.setState({ variant });
    }

    /**
     * @param {string} label
     * @returns {BadgeComponent} this
     */
    setLabel(label) {
        return this.setState({ label: String(label ?? '') });
    }

    /**
     * @param {string|number} count — ''/null rozet sayacını gizler
     * @returns {BadgeComponent} this
     */
    setCount(count) {
        const next = count === null || count === undefined ? '' : String(count);
        return this.setState({ count: next });
    }

    destroy() {
        this.#control = null;
        super.destroy();
    }
}
