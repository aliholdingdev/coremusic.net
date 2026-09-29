// @vitest-environment jsdom
/**
 * ButtonComponent.spec — C04 cm-button.
 *
 * @requires vitest + jsdom (paket henüz kurulu değil → package.json'a eklenmeli)
 * Kural: innerHTML YASAK — DOM elle kurulur (createElement).
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import ButtonComponent from './ButtonComponent.js';

/** @type {ButtonComponent|null} */
let component = null;

/**
 * @param {{tag?: string, variant?: string, disabled?: boolean}} opts
 * @returns {HTMLElement}
 */
function buildButton(opts = {}) {
    const el = document.createElement(opts.tag || 'button');
    el.className = `btn btn--${opts.variant || 'primary'}`;
    el.setAttribute('data-cm-component', 'cm-button');
    el.setAttribute('data-cm-variant', opts.variant || 'primary');
    if (opts.tag !== 'button') el.setAttribute('href', '/');
    if (opts.disabled) el.setAttribute('disabled', '');
    el.textContent = 'Kaydet';
    document.body.appendChild(el);
    return el;
}

describe('ButtonComponent', () => {
    beforeEach(() => {
        document.body.textContent = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
        document.body.textContent = '';
    });

    it('init: data-cm-variant state\'e geçer', () => {
        const el = buildButton({ variant: 'secondary' });
        component = new ButtonComponent(el);
        component.init();

        expect(component.state.variant).toBe('secondary');
        expect(el.classList.contains('btn--secondary')).toBe(true);
        expect(el.classList.contains('btn--primary')).toBe(false);
    });

    it('mount: tıklama cm:button:click yayınlar (detail.variant)', () => {
        const el = buildButton();
        component = new ButtonComponent(el);
        component.init();
        component.mount();

        /** @type {CustomEvent|null} */
        let received = null;
        el.addEventListener('cm:button:click', (e) => {
            received = /** @type {CustomEvent} */ (e);
        });

        el.click();

        expect(received).not.toBeNull();
        expect(received.detail.variant).toBe('primary');
        expect(received.detail.label).toBe('Kaydet');
    });

    it('disabled: tıklama yayım yapmaz', () => {
        const el = buildButton({ disabled: true });
        component = new ButtonComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:button:click', () => {
            fired += 1;
        });

        el.click();
        expect(fired).toBe(0);
        expect(el.getAttribute('aria-disabled')).toBe('true');
    });

    it('setLoading: is-loading + aria-busy + yayım engeli', () => {
        const el = buildButton();
        component = new ButtonComponent(el);
        component.init();
        component.mount();

        component.setLoading(true);
        expect(el.classList.contains('is-loading')).toBe(true);
        expect(el.getAttribute('aria-busy')).toBe('true');

        let fired = 0;
        el.addEventListener('cm:button:click', () => {
            fired += 1;
        });
        el.click();
        expect(fired).toBe(0);

        component.setLoading(false);
        expect(el.classList.contains('is-loading')).toBe(false);
        expect(el.hasAttribute('aria-busy')).toBe(false);
    });

    it('destroy: dinleyiciler kalkar (abort)', () => {
        const el = buildButton();
        component = new ButtonComponent(el);
        component.init();
        component.mount();
        component.destroy();

        let fired = 0;
        el.addEventListener('cm:button:click', () => {
            fired += 1;
        });
        el.click();
        expect(fired).toBe(0);
        component = null;
    });

    it('<a class="btn"> kökü çalışır (ErrorHandler.php)', () => {
        const el = buildButton({ tag: 'a', variant: 'primary' });
        component = new ButtonComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:button:click', () => {
            fired += 1;
        });
        el.click();
        expect(fired).toBe(1);
        // <a>'da disabled attribute yazılmaz
        component.setDisabled(true);
        expect(el.hasAttribute('disabled')).toBe(false);
        expect(el.getAttribute('aria-disabled')).toBe('true');
    });
});
