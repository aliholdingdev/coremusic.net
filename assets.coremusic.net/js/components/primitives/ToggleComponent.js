/**
 * ToggleComponent — C08 Figma Toggle (Faz 2 / Batch 1).
 *
 * HTML contract:
 * <button class="toggle" type="button" role="switch" aria-checked="false"
 *         data-cm-component="cm-toggle" data-cm-checked="false">
 *   <span class="toggle__knob"></span>
 * </button>
 *
 * Figma (page 18:2907): 12.5×5 r50, knob 5×5 → 1920 tier 24×10, knob 8×8.
 * Karar #6: görsel boyut Figma'da kalır; dokunma alanı ::before 48×48px
 * (pointer:coarse override'ı c-toggle.css ek bölümünde).
 *
 * Emits: cm:toggle:change
 *
 * @package CoreMusic\Components\Primitives
 */
import ComponentBase from '../base/ComponentBase.js';
import { CM_EVENTS } from '../base/ComponentEvents.js';

export default class ToggleComponent extends ComponentBase {
    /** @type {HTMLButtonElement|null} */
    #control = null;

    defaultState() {
        return { checked: false, disabled: false };
    }

    init() {
        const el = this.el;
        if (!el) return;

        this.#control = /** @type {any} */ (el);

        // role="switch" zorunlu (değilse ekle) — erişilebilirlik sözleşmesi
        if (!el.hasAttribute('role')) el.setAttribute('role', 'switch');

        // Başlangıç değeri: data-cm-checked (loader string verir) → boolean
        const fromData = el.dataset?.cmChecked;
        const checked =
            fromData !== undefined
                ? fromData === 'true' || fromData === '1'
                : el.getAttribute('aria-checked') === 'true' || el.classList.contains('toggle--active');

        this.setState({
            checked,
            disabled: el.hasAttribute('disabled') || el.getAttribute('aria-disabled') === 'true',
        });
    }

    mount() {
        if (!this.#control) return;

        // <button> zaten Enter/Space'de click üretir → ek keydown gerekmez
        this.on(this.#control, 'click', (event) => this.#onClick(event));
    }

    /**
     * @param {MouseEvent} event
     */
    #onClick(event) {
        if (this.state.disabled || this.#control?.hasAttribute('disabled')) {
            event.preventDefault();
            return;
        }
        this.toggle();
    }

    /**
     * Durumu çevirir ve cm:toggle:change yayınlar.
     * @returns {boolean} Yeni değer
     */
    toggle() {
        return this.setChecked(!this.state.checked);
    }

    /**
     * @param {boolean} checked
     * @returns {boolean} Uygulanan değer
     */
    setChecked(checked) {
        const next = Boolean(checked);
        if (next === this.state.checked) return this.state.checked;

        this.setState({ checked: next });
        this.emit(CM_EVENTS.TOGGLE_CHANGE, { id: this.id, checked: next });
        return next;
    }

    /**
     * @param {boolean} disabled
     * @returns {ToggleComponent} this
     */
    setDisabled(disabled) {
        return this.setState({ disabled: Boolean(disabled) });
    }

    /**
     * Tek render noktası — state → DOM senkronu.
     * @param {object} _prev
     * @param {object} next
     */
    onUpdate(_prev, next) {
        const el = this.#control;
        if (!el) return;

        el.setAttribute('aria-checked', String(next.checked));
        el.classList.toggle('toggle--active', next.checked);

        if (next.disabled) {
            el.setAttribute('disabled', '');
            el.setAttribute('aria-disabled', 'true');
        } else {
            el.removeAttribute('disabled');
            el.removeAttribute('aria-disabled');
        }
    }

    destroy() {
        this.#control = null;
        super.destroy();
    }
}
