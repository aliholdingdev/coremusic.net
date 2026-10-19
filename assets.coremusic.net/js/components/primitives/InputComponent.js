/**
 * InputComponent — C05 Figma Input (Faz 2 / Batch 1).
 *
 * HTML contract (iki şekil de desteklenir):
 * <label class="input" data-cm-component="cm-input"><span class="input__label">Ara</span>
 *   <input class="input__field" type="search"><span class="input__hint">…</span></label>
 * <input class="input" data-cm-component="cm-input">   ← kök doğrudan kontrol olabilir
 *
 * Figma (page 18:2907): Light 108×19 r3 stroke .2 → 1920 tier 136×30; Dark fill %35.
 * Karar #6: dokunma alanı ::before 48×48px.
 *
 * Emits: cm:input:change · cm:input:focus · cm:input:blur
 *
 * @package CoreMusic\Components\Primitives
 */
import ComponentBase from '../base/ComponentBase.js';
import { CM_EVENTS } from '../base/ComponentEvents.js';

/** Statu modifier'ları (inventory C05: .input--error / .input--success) */
const STATUSES = ['error', 'success'];

export default class InputComponent extends ComponentBase {
    /** @type {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement|null} */
    #field = null;

    defaultState() {
        return {
            value: '',
            disabled: false,
            invalid: false,
            status: '', // '' | 'error' | 'success'
            hint: '',
        };
    }

    init() {
        const el = this.el;
        if (!el) return;

        // Kök kontrolün kendisi mi, içindeki alan mı?
        const isField = /^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName);
        const field = isField
            ? /** @type {any} */ (el)
            : /** @type {HTMLInputElement|null} */ (el.querySelector('input, textarea, select'));

        if (!field) {
            // Alan yoksa bileşen pasif kalır (sessiz — loader hatasız kalsın)
            return;
        }
        this.#field = field;

        this.setState({
            value: field.value ?? '',
            disabled: field.disabled || el.hasAttribute('disabled'),
            invalid: field.getAttribute('aria-invalid') === 'true' || el.classList.contains('input--error'),
            hint: el.querySelector('.input__hint')?.textContent ?? '',
        });
    }

    mount() {
        const field = this.#field;
        if (!field) return;

        this.on(field, 'input', () => {
            this.setState({ value: field.value });
            this.emit(CM_EVENTS.INPUT_CHANGE, { id: this.id, value: field.value });
        });

        // Select/checkbox gibi alanlarda 'change' tetiklenir
        this.on(field, 'change', () => {
            if (field.value !== this.state.value) {
                this.setState({ value: field.value });
            }
            this.emit(CM_EVENTS.INPUT_CHANGE, { id: this.id, value: field.value });
        });

        this.on(field, 'focus', () => {
            this.emit(CM_EVENTS.INPUT_FOCUS, { id: this.id });
        });

        this.on(field, 'blur', () => {
            this.emit(CM_EVENTS.INPUT_BLUR, { id: this.id, value: field.value });
        });
    }

    /**
     * Tek render noktası — state → DOM senkronu.
     * @param {object} _prev
     * @param {object} next
     */
    onUpdate(_prev, next) {
        const root = this.el;
        const field = this.#field;
        if (!root || !field) return;

        STATUSES.forEach((s) => root.classList.toggle(`input--${s}`, next.status === s));
        root.classList.toggle('input--disabled', Boolean(next.disabled));

        field.disabled = Boolean(next.disabled);
        field.setAttribute('aria-invalid', next.invalid || next.status === 'error' ? 'true' : 'false');

        if (next.hint !== undefined) {
            const hint = root.querySelector('.input__hint');
            if (hint && hint.textContent !== next.hint) hint.textContent = next.hint;
        }

        // Programatik set ile DOM'u ezme (kullanıcı yazımı korunur)
        if (typeof next.value === 'string' && field.value !== next.value) {
            field.value = next.value;
        }
    }

    /**
     * Doğrulama durumu — modifier + aria-invalid + isteğe bağlı ipucu metni.
     * @param {'error'|'success'|''} status
     * @param {string} [hint] — Durum mesajı (boşsa değişmez)
     * @returns {InputComponent} this
     */
    setStatus(status, hint = '') {
        if (status !== '' && !STATUSES.includes(status)) return this;
        return this.setState({
            status,
            invalid: status === 'error',
            ...(hint ? { hint } : {}),
        });
    }

    /**
     * @param {string} value
     * @returns {InputComponent} this
     */
    setValue(value) {
        return this.setState({ value: String(value ?? '') });
    }

    /**
     * @param {boolean} disabled
     * @returns {InputComponent} this
     */
    setDisabled(disabled) {
        return this.setState({ disabled: Boolean(disabled) });
    }

    /** @returns {string} Anlık DOM değeri */
    get value() {
        return this.#field?.value ?? '';
    }

    destroy() {
        this.#field = null;
        super.destroy();
    }
}
