/**
 * ButtonComponent — C04 Figma Button (Faz 2 / Batch 1).
 *
 * HTML contract:
 * <button class="btn btn--primary" data-cm-component="cm-button" data-cm-variant="primary">Metin</button>
 * <a class="btn" href="/">…</a>            ← ErrorHandler.php (min-w/min-h ile yaşar)
 *
 * Figma (page 18:2907): 27×12 r2 stroke .5px → 1920 tier 47×20.
 * Karar #6: görsel boyut Figma'da kalır; dokunma alanı ::before 48×48px.
 *
 * Emits: cm:button:click
 *
 * @package CoreMusic\Components\Primitives
 * @requires ComponentBase, ComponentEvents (CM_EVENTS)
 */
import ComponentBase from '../base/ComponentBase.js';
import { CM_EVENTS } from '../base/ComponentEvents.js';

/** Desteklenen modifier'lar — state.variant bunlardan biri olmalı. */
const VARIANTS = ['primary', 'secondary', 'ghost', 'danger'];

export default class ButtonComponent extends ComponentBase {
    /** @type {HTMLButtonElement|HTMLAnchorElement|null} */
    #control = null;

    /** @type {boolean} Mount öncesi disabled durumu (loading ile ezilmez) */
    #nativeDisabled = false;

    defaultState() {
        return {
            variant: 'primary',
            loading: false,
            disabled: false,
        };
    }

    init() {
        const el = this.el;
        if (!el) return;

        this.#control = /** @type {any} */ (el);
        this.#nativeDisabled =
            el.hasAttribute('disabled') || el.getAttribute('aria-disabled') === 'true';

        const dsVariant = el.dataset?.cmVariant;
        const variant = VARIANTS.includes(dsVariant) ? dsVariant : this.state.variant;

        this.setState({
            variant,
            disabled: this.#nativeDisabled,
            loading: el.dataset?.cmLoading === 'true',
        });
    }

    mount() {
        if (!this.#control) return;

        // Tek dinleyici, delegation yok (kök = kontrolün kendisi)
        this.on(this.#control, 'click', (event) => this.#onClick(event));
    }

    /**
     * @param {MouseEvent} event
     */
    #onClick(event) {
        const { disabled, loading, variant } = this.state;

        if (disabled || loading || this.#control?.hasAttribute('disabled')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }

        // Link default'u korunur; tıklama yalnız yayınlanır
        this.emit(CM_EVENTS.BUTTON_CLICK, {
            id: this.id,
            variant,
            label: (this.#control?.textContent ?? '').trim(),
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

        // Variant modifier'ları — yalnız izinli seti uygula
        VARIANTS.forEach((v) => el.classList.toggle(`btn--${v}`, next.variant === v));

        // Disabled
        const disabled = Boolean(next.disabled) || Boolean(next.loading);
        el.classList.toggle('is-disabled', disabled);
        if (disabled) {
            el.setAttribute('aria-disabled', 'true');
        } else {
            el.removeAttribute('aria-disabled');
        }
        // <a> tag'inde disabled attribute geçersiz — yalnız <button>'da yaz
        if (el.tagName === 'BUTTON') {
            if (disabled) {
                el.setAttribute('disabled', '');
            } else if (!this.#nativeDisabled) {
                el.removeAttribute('disabled');
            }
        }

        // Loading
        el.classList.toggle('is-loading', Boolean(next.loading));
        if (next.loading) {
            el.setAttribute('aria-busy', 'true');
        } else {
            el.removeAttribute('aria-busy');
        }
    }

    /**
     * Loading durumunu değiştirir (async submit göstergesi).
     * @param {boolean} loading
     * @returns {ButtonComponent} this
     */
    setLoading(loading) {
        return this.setState({ loading: Boolean(loading) });
    }

    /**
     * Disabled durumunu değiştirir.
     * @param {boolean} disabled
     * @returns {ButtonComponent} this
     */
    setDisabled(disabled) {
        const next = Boolean(disabled);
        this.#nativeDisabled = next;
        return this.setState({ disabled: next });
    }

    /**
     * Variant değiştirir (primary|secondary|ghost|danger).
     * @param {string} variant
     * @returns {ButtonComponent} this
     */
    setVariant(variant) {
        if (!VARIANTS.includes(variant)) return this;
        return this.setState({ variant });
    }

    destroy() {
        this.#control = null;
        // Dinleyiciler base.destroy() içinde abort edilir
        super.destroy();
    }
}
