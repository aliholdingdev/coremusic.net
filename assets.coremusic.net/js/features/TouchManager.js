/**
 * CoreMusic — TouchManager
 * Embedded cihazlarda touch gestures. Sadece device=embedded'de aktif.
 *
 * @module features/TouchManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class TouchManager {
    #eventBus;
    /** @type {number|null} */
    #longPressTimer = null;
    /** @type {{x: number, y: number}|null} */
    #touchStart = null;

    static LONG_PRESS_MS = 500;
    static SWIPE_THRESHOLD = 50;

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    init() {
        const device = window.CoreMusic?.deviceType || document.body?.dataset?.device;
        if (device !== 'embedded') return;

        document.addEventListener('touchstart', this.#onTouchStart.bind(this), { passive: true });
        document.addEventListener('touchend', this.#onTouchEnd.bind(this), { passive: true });
        document.addEventListener('touchmove', this.#onTouchMove.bind(this), { passive: true });
    }

    /** @param {TouchEvent} e */
    #onTouchStart(e) {
        const touch = e.touches[0];
        this.#touchStart = { x: touch.clientX, y: touch.clientY };

        const card = e.target.closest('.media-card, .mini-card');
        if (card) {
            this.#longPressTimer = window.setTimeout(() => {
                this.#onLongPress(e, card);
            }, TouchManager.LONG_PRESS_MS);
        }
    }

    /** @param {TouchEvent} e */
    #onTouchMove(e) {
        if (this.#longPressTimer !== null) {
            clearTimeout(this.#longPressTimer);
            this.#longPressTimer = null;
        }
    }

    /** @param {TouchEvent} e */
    #onTouchEnd(e) {
        if (this.#longPressTimer !== null) {
            clearTimeout(this.#longPressTimer);
            this.#longPressTimer = null;
        }

        if (!this.#touchStart) return;

        const touch = e.changedTouches[0];
        const dx = touch.clientX - this.#touchStart.x;
        const dy = touch.clientY - this.#touchStart.y;

        if (Math.abs(dx) > TouchManager.SWIPE_THRESHOLD && Math.abs(dx) > Math.abs(dy)) {
            this.#onSwipe(dx > 0 ? 'right' : 'left');
        }

        this.#touchStart = null;
    }

    /** @param {TouchEvent} e */
    #onLongPress(e, card) {
        this.#eventBus.emit('touch:longpress', {
            element: card,
            id: card.dataset.id || null,
        });
    }

    /** @param {'left'|'right'} direction */
    #onSwipe(direction) {
        const grids = document.querySelectorAll('.card-grid--scroll, .card-grid');
        grids.forEach((grid) => {
            grid.scrollBy({
                left: direction === 'left' ? 200 : -200,
                behavior: 'smooth',
            });
        });
        this.#eventBus.emit('touch:swipe', { direction });
    }

    destroy() {
        if (this.#longPressTimer !== null) {
            clearTimeout(this.#longPressTimer);
            this.#longPressTimer = null;
        }
        this.#touchStart = null;
    }
}
