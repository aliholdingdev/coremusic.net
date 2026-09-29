/**
 * C16 Toast bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/ToastComponent
 * @requires ADR-001 (framework yasak)
 * Figma: sayfa 18:2907 · Envanter: .ai/ui-design/02-component-inventory.md C16
 * BEM: .toast, .toast--success, .toast--error, .toast--info, .toast__icon, .toast__message
 *
 * MIGRASYON: interactive/ToastComponent.js API'si korunur
 * (show/success/error/warning/info/clearAll/destroy). Eski sürüm statik
 * DOMParser temizleyicisiyle sabit HTML basıyordu; yeni sürüm tamamen
 * createElement + textContent ile kurulur (innerHTML YASAK — ADR-001/OWASP).
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';
import { eventName } from '../base/ComponentEvents.js';
import { announce } from '../base/ComponentAccessibility.js';

/**
 * @typedef {Object} ToastState
 * @property {{id:number, message:string, type:string, duration:number}[]} items
 */

/** Bildirim türü → BEM sınıfı (envanter C16). */
const TYPE_CLASS = Object.freeze({
    success: 'toast--success',
    error: 'toast--error',
    warning: 'toast--warning',
    info: 'toast--info',
});

/** Bildirim türü → ikon metni (font ikonu — asset bağımlılığı yok). */
const TYPE_ICON = Object.freeze({
    success: '✓',
    error: '✕',
    warning: '!',
    info: 'i',
});

/** @type {number} Benzersiz sayaç */
let sequence = 0;

/** @type {Map<number, number>} Otomatik kapanma sayaçları */
const timers = new Map();

export default class ToastComponent extends ComponentBase {
    /** @type {ToastComponent|null} Uygulama genelinde tek instance */
    static #instance = null;

    /**
     * Singleton erişim — eski API sözleşmesi korunur
     * (main.js: `window.CoreMusic.Toast = ToastComponent.getInstance()`).
     * @returns {ToastComponent}
     */
    static getInstance() {
        if (ToastComponent.#instance) return ToastComponent.#instance;

        const container = document.createElement('div');
        container.setAttribute('data-cm-toast-stack', '');
        document.body.appendChild(container);

        const instance = new ToastComponent(container);
        instance.init();
        instance.mount();
        instance._setMounted(true);
        ToastComponent.#instance = instance;
        return instance;
    }

    /** @returns {ToastState} */
    defaultState() {
        return { items: [] };
    }

    /** İlk render — ComponentLoader.mount() buradan çağırır. */
    mount() {
        this.#render();
    }

    init() {
        const root = this.el;
        if (!root) return;

        root.classList.add('toast-stack');
        root.dataset.component = 'toast';
        root.setAttribute('role', 'region');
        root.setAttribute('aria-label', 'Bildirimler');
        root.setAttribute('aria-live', 'polite');

        this.on(root, 'click', (event) => this.#onClick(event));
    }

    /** @param {object} prev @param {ToastState} next */
    onUpdate(prev, next) {
        if (prev.items !== next.items) this.#render();
    }

    /** Bildirim listesini DOM'a eşler. */
    #render() {
        const root = this.el;
        if (!root) return;

        const { items } = this.state;
        root.textContent = '';

        items.forEach((item) => {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.dataset.id = String(item.id);
            if (TYPE_CLASS[item.type]) toast.classList.add(TYPE_CLASS[item.type]);
            toast.setAttribute('role', item.type === 'error' ? 'alert' : 'status');

            const icon = document.createElement('span');
            icon.className = 'toast__icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = TYPE_ICON[item.type] ?? TYPE_ICON.info;

            const message = document.createElement('span');
            message.className = 'toast__message';
            message.textContent = item.message;

            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'toast__close';
            close.dataset.action = 'close';
            close.dataset.id = String(item.id);
            close.setAttribute('aria-label', 'Bildirimi kapat');
            close.textContent = '×';

            toast.append(icon, message, close);
            root.appendChild(toast);
        });
    }

    /**
     * Bildirim gösterir.
     * @param {string} message — Metin (textContent ile basılır)
     * @param {{type?:string, duration?:number}} [options]
     * @returns {number} bildirim id'si
     */
    show(message, options = {}) {
        if (typeof message !== 'string' || message.trim() === '') return -1;

        const type = Object.hasOwn(TYPE_CLASS, options.type) ? options.type : 'info';
        const duration = Number.isFinite(options.duration) && options.duration > 0
            ? options.duration
            : type === 'error'
                ? 6000
                : 4000;

        const id = ++sequence;
        const next = [
            ...this.state.items,
            { id, message: message.trim(), type, duration },
        ].slice(-5); // Aynı anda en fazla 5 bildirim

        this.setState({ items: next });
        this.emit(eventName('toast', 'show'), { id, type, message: message.trim() });
        announce(this.el || document.body, message.trim());

        timers.set(id, globalThis.setTimeout(() => this.#remove(id), duration));
        return id;
    }

    /** @param {string} message @param {object} [options] */
    success(message, options = {}) {
        return this.show(message, { ...options, type: 'success' });
    }

    /** @param {string} message @param {object} [options] */
    error(message, options = {}) {
        return this.show(message, { ...options, type: 'error' });
    }

    /** @param {string} message @param {object} [options] */
    warning(message, options = {}) {
        return this.show(message, { ...options, type: 'warning' });
    }

    /** @param {string} message @param {object} [options] */
    info(message, options = {}) {
        return this.show(message, { ...options, type: 'info' });
    }

    /** Tüm bildirimleri temizler (eski API — korunur). */
    clearAll() {
        timers.forEach((handle) => globalThis.clearTimeout(handle));
        timers.clear();
        if (this.state.items.length > 0) this.setState({ items: [] });
        return this;
    }

    /**
     * Tek bildirimi kaldırır.
     * @param {number} id
     */
    #remove(id) {
        const handle = timers.get(id);
        if (handle !== undefined) {
            globalThis.clearTimeout(handle);
            timers.delete(id);
        }
        const next = this.state.items.filter((item) => item.id !== id);
        if (next.length !== this.state.items.length) this.setState({ items: next });
    }

    /** @param {MouseEvent} event */
    #onClick(event) {
        const target = event.target instanceof Element ? event.target.closest('[data-action="close"]') : null;
        if (!(target instanceof HTMLElement) || !this.el?.contains(target)) return;
        this.#remove(Number(target.dataset.id));
    }

    destroy() {
        timers.forEach((handle) => globalThis.clearTimeout(handle));
        timers.clear();
        if (ToastComponent.#instance === this) ToastComponent.#instance = null;
        super.destroy();
    }
}
