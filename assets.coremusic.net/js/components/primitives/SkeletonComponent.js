/**
 * SkeletonComponent — C13 Figma Skeleton (Faz 2 / Batch 1).
 *
 * HTML contract:
 * <div class="skeleton skeleton--text" data-cm-component="cm-skeleton" data-cm-active="true"></div>
 * <div class="skeleton skeleton--circle" data-cm-component="cm-skeleton"></div>
 *
 * Figma (page 18:2907): bg = #d9d9d9 @%14 (dark) / #424242 @%14 (light), shine %6,
 * radius 8px, animasyon 1500ms ease-in-out infinite (--cm-skeleton-*).
 *
 * Emits: cm:skeleton:loaded (aktif → pasif geçişte bir kez)
 *
 * @package CoreMusic\Components\Primitives
 */
import ComponentBase from '../base/ComponentBase.js';
import { CM_EVENTS } from '../base/ComponentEvents.js';

/** envanter C13 modifier'ları */
const VARIANTS = ['text', 'circle', 'rect'];

export default class SkeletonComponent extends ComponentBase {
    /** @type {HTMLElement|null} */
    #control = null;

    /** @type {boolean} İlk loaded yayımı yapıldı mı? */
    #loadedEmitted = false;

    defaultState() {
        return {
            active: true,
            variant: 'text',
        };
    }

    init() {
        const el = this.el;
        if (!el) return;
        this.#control = el;

        const variant =
            VARIANTS.find((v) => el.classList.contains(`skeleton--${v}`)) ?? this.state.variant;

        const fromData = el.dataset?.cmActive;
        const active = fromData !== undefined ? fromData === 'true' : !el.classList.contains('is-loaded');

        this.setState({ active, variant });

        // ARIA: yüklenen içerik bölgesini temsil eder
        el.setAttribute('aria-busy', String(active));
        el.setAttribute('aria-hidden', active ? 'false' : 'true');
    }

    mount() {
        // Durum geçişleri dışarıdan tetiklenir: component.setActive(false)
        // Dinleyici yok → sızıntı yok (observer da kullanılmaz).
    }

    /**
     * Tek render noktası — state → DOM senkronu.
     * @param {object} prev
     * @param {object} next
     */
    onUpdate(prev, next) {
        const el = this.#control;
        if (!el) return;

        VARIANTS.forEach((v) => el.classList.toggle(`skeleton--${v}`, next.variant === v));

        el.classList.toggle('is-loaded', !next.active);
        el.setAttribute('aria-busy', String(next.active));
        el.setAttribute('aria-hidden', next.active ? 'false' : 'true');

        // aktif → pasif geçişte tek seferlik 'loaded'
        if (prev.active && !next.active && !this.#loadedEmitted) {
            this.#loadedEmitted = true;
            this.emit(CM_EVENTS.SKELETON_LOADED, { id: this.id, variant: next.variant });
        }
        // yeniden aktifleşirse tekrar yayınlansın
        if (next.active) this.#loadedEmitted = false;
    }

    /**
     * Yükleme durumunu değiştirir.
     * @param {boolean} active — true: göster, false: gizle + cm:skeleton:loaded yayınla
     * @returns {SkeletonComponent} this
     */
    setActive(active) {
        return this.setState({ active: Boolean(active) });
    }

    /**
     * @param {string} variant — text|circle|rect
     * @returns {SkeletonComponent} this
     */
    setVariant(variant) {
        if (!VARIANTS.includes(variant)) return this;
        return this.setState({ variant });
    }

    destroy() {
        this.#control = null;
        this.#loadedEmitted = false;
        super.destroy();
    }
}
