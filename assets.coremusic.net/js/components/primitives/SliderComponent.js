/**
 * SliderComponent — C09 Figma Slider (Faz 2 / Batch 1).
 *
 * HTML contract (native range = ücretsiz klavye/aria):
 * <div class="slider" data-cm-component="cm-slider">
 *   <input class="slider__input" type="range" min="0" max="100" step="1" value="0">
 * </div>
 * (Kök doğrudan <input type="range"> ise de çalışır.)
 *
 * Figma (page 18:2907): track h1 r50 (fill --cm-pink), thumb 14px stroke --cm-pink.
 * Dolum: JS `--cm-slider-progress` değerini yazar (footer.init.js / coreplayer.seekbar.js
 * ile aynı, CSP uyumlu dinamik style — statik style="" yasağı kapsamı dışıdır).
 *
 * Emits: cm:slider:input · cm:slider:change
 *
 * @package CoreMusic\Components\Primitives
 */
import ComponentBase from '../base/ComponentBase.js';
import { CM_EVENTS } from '../base/ComponentEvents.js';

export default class SliderComponent extends ComponentBase {
    /** @type {HTMLInputElement|null} */
    #field = null;

    defaultState() {
        return {
            value: 0,
            min: 0,
            max: 100,
            step: 1,
            disabled: false,
        };
    }

    init() {
        const el = this.el;
        if (!el) return;

        const isRange = el.tagName === 'INPUT' && el.getAttribute('type') === 'range';
        const field = isRange ? /** @type {any} */ (el) : this.#ensureStructure();

        if (!field) return;
        this.#field = field;

        // Role/aria native range'de vardır; eksikse tamamla
        if (!field.hasAttribute('role')) field.setAttribute('role', 'slider');

        this.setState({
            value: Number(field.value) || 0,
            min: Number(field.min) || 0,
            max: field.max === '' ? 100 : Number(field.max),
            step: Number(field.step) || 1,
            disabled: field.disabled,
        });
    }

    /**
     * İç yapıyı garanti eder — input + track + fill + thumb.
     * DOM createElement ile kurulur (innerHTML YASAK). Eksik parçalar tamamlanır.
     *
     * @returns {HTMLInputElement|null} Range input (yoksa null)
     */
    #ensureStructure() {
        const root = this.el;
        const doc = root?.ownerDocument;
        if (!root || !doc) return null;

        let field = /** @type {HTMLInputElement|null} */ (
            root.querySelector('input[type="range"]')
        );

        if (!field) {
            field = doc.createElement('input');
            field.type = 'range';
            field.className = 'slider__input';
            field.min = String(this.state.min ?? 0);
            field.max = String(this.state.max ?? 100);
            field.step = String(this.state.step ?? 1);
            field.value = String(this.state.value ?? 0);
            const label = root.getAttribute('data-cm-label');
            if (label) field.setAttribute('aria-label', label);
            // İlk sırada olmalı (CSS sibling selectors: input ~ thumb)
            root.insertBefore(field, root.firstChild);
        }

        let track = root.querySelector('.slider__track');
        if (!track) {
            track = doc.createElement('span');
            track.className = 'slider__track';
            const fill = doc.createElement('span');
            fill.className = 'slider__fill';
            track.appendChild(fill);
            root.appendChild(track);
        }

        if (!root.querySelector('.slider__thumb')) {
            const thumb = doc.createElement('span');
            thumb.className = 'slider__thumb';
            root.appendChild(thumb);
        }

        return field;
    }

    mount() {
        const field = this.#field;
        if (!field) return;

        this.on(field, 'input', () => {
            const value = Number(field.value);
            this.setState({ value });
            this.emit(CM_EVENTS.SLIDER_INPUT, {
                id: this.id,
                value,
                progress: this.#progressOf(value),
            });
        });

        this.on(field, 'change', () => {
            const value = Number(field.value);
            if (value !== this.state.value) this.setState({ value });
            this.emit(CM_EVENTS.SLIDER_CHANGE, {
                id: this.id,
                value,
                progress: this.#progressOf(value),
            });
        });
    }

    /**
     * 0–100 yüzdesi (max === min durumunda 0).
     * @param {number} value
     * @returns {number}
     */
    #progressOf(value) {
        const { min, max } = this.state;
        if (max === min) return 0;
        const raw = ((value - min) / (max - min)) * 100;
        return Math.min(100, Math.max(0, raw));
    }

    /**
     * Tek render noktası — state → DOM senkronu + dolum değişkeni.
     * @param {object} _prev
     * @param {object} next
     */
    onUpdate(_prev, next) {
        const field = this.#field;
        if (!field) return;

        const value = Number(next.value);
        if (Number(field.value) !== value) field.value = String(value);

        field.disabled = Boolean(next.disabled);
        this.el?.classList.toggle('slider--disabled', Boolean(next.disabled));

        const progress = this.#progressOf(value).toFixed(2);
        // Dinamik dolum (Aynı desen: footer.init.js, coreplayer.seekbar.js)
        this.el?.style.setProperty('--cm-slider-progress', `${progress}%`);
        field.style.setProperty('--cm-slider-progress', `${progress}%`);

        field.setAttribute('aria-valuemin', String(next.min));
        field.setAttribute('aria-valuemax', String(next.max));
        field.setAttribute('aria-valuenow', String(value));
    }

    /**
     * @param {number} value
     * @returns {SliderComponent} this
     */
    setValue(value) {
        const { min, max, step } = this.state;
        const clamped = Math.min(max, Math.max(min, Number(value)));
        const stepped = step > 0 ? Math.round(clamped / step) * step : clamped;
        return this.setState({ value: stepped });
    }

    /**
     * @param {boolean} disabled
     * @returns {SliderComponent} this
     */
    setDisabled(disabled) {
        return this.setState({ disabled: Boolean(disabled) });
    }

    /** @returns {string} Anlık DOM değeri */
    get value() {
        return this.#field?.value ?? '0';
    }

    destroy() {
        this.el?.style.removeProperty('--cm-slider-progress');
        this.#field?.style.removeProperty('--cm-slider-progress');
        this.#field = null;
        super.destroy();
    }
}
