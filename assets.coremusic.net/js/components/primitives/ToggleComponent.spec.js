// @vitest-environment jsdom
/**
 * ToggleComponent.spec — C08 cm-toggle.
 *
 * @requires vitest + jsdom (paket henüz kurulu değil)
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import ToggleComponent from './ToggleComponent.js';

/** @type {ToggleComponent|null} */
let component = null;

/**
 * @param {{checked?: boolean, disabled?: boolean}} [opts]
 * @returns {HTMLButtonElement}
 */
function buildToggle(opts = {}) {
    const el = document.createElement('button');
    el.type = 'button';
    el.className = 'toggle';
    el.setAttribute('data-cm-component', 'cm-toggle');
    el.setAttribute('data-cm-checked', String(Boolean(opts.checked)));
    if (opts.disabled) el.setAttribute('disabled', '');

    const knob = document.createElement('span');
    knob.className = 'toggle__knob';
    el.appendChild(knob);

    document.body.appendChild(el);
    return el;
}

describe('ToggleComponent', () => {
    beforeEach(() => {
        document.body.textContent = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
        document.body.textContent = '';
    });

    it('init: role=switch eklenir, data-cm-checked okunur', () => {
        const el = buildToggle({ checked: true });
        component = new ToggleComponent(el);
        component.init();

        expect(el.getAttribute('role')).toBe('switch');
        expect(component.state.checked).toBe(true);
        expect(el.getAttribute('aria-checked')).toBe('true');
        expect(el.classList.contains('toggle--active')).toBe(true);
    });

    it('click: durum değişir + cm:toggle:change yayınlanır', () => {
        const el = buildToggle();
        component = new ToggleComponent(el);
        component.init();
        component.mount();

        /** @type {CustomEvent|null} */
        let received = null;
        el.addEventListener('cm:toggle:change', (e) => {
            received = /** @type {CustomEvent} */ (e);
        });

        el.click();

        expect(received).not.toBeNull();
        expect(received.detail.checked).toBe(true);
        expect(el.getAttribute('aria-checked')).toBe('true');
        expect(el.classList.contains('toggle--active')).toBe(true);

        el.click();
        expect(component.state.checked).toBe(false);
        expect(el.getAttribute('aria-checked')).toBe('false');
    });

    it('setChecked aynı değere: yayım YAPILMAZ', () => {
        const el = buildToggle();
        component = new ToggleComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:toggle:change', () => {
            fired += 1;
        });

        component.setChecked(false); // zaten false
        expect(fired).toBe(0);

        component.setChecked(true);
        expect(fired).toBe(1);
    });

    it('disabled: tıklama durumu değiştirmez', () => {
        const el = buildToggle({ disabled: true });
        component = new ToggleComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:toggle:change', () => {
            fired += 1;
        });

        el.click();
        expect(fired).toBe(0);
        expect(component.state.checked).toBe(false);
    });

    it('setDisabled: aria-disabled senkronu', () => {
        const el = buildToggle();
        component = new ToggleComponent(el);
        component.init();

        component.setDisabled(true);
        expect(el.getAttribute('aria-disabled')).toBe('true');
        expect(el.hasAttribute('disabled')).toBe(true);

        component.setDisabled(false);
        expect(el.hasAttribute('disabled')).toBe(false);
    });

    it('destroy: sonraki tıklama yayım yapmaz', () => {
        const el = buildToggle();
        component = new ToggleComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:toggle:change', () => {
            fired += 1;
        });

        component.destroy();
        component = null;

        // Not: native <button> disabled değil ama listener abort edildi
        el.click();
        expect(fired).toBe(0);
    });
});
