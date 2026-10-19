// @vitest-environment jsdom
/**
 * InputComponent.spec — C05 cm-input.
 *
 * @requires vitest + jsdom (paket henüz kurulu değil)
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import InputComponent from './InputComponent.js';

/** @type {InputComponent|null} */
let component = null;

/**
 * Wrapper + label + input + hint kurar.
 * @returns {{root: HTMLElement, field: HTMLInputElement}}
 */
function buildInput() {
    const root = document.createElement('label');
    root.className = 'input';
    root.setAttribute('data-cm-component', 'cm-input');

    const label = document.createElement('span');
    label.className = 'input__label';
    label.textContent = 'Ara';

    const field = document.createElement('input');
    field.className = 'input__field';
    field.type = 'search';
    field.value = '';

    const hint = document.createElement('span');
    hint.className = 'input__hint';
    hint.textContent = 'En az 3 karakter';

    root.append(label, field, hint);
    document.body.appendChild(root);
    return { root, field };
}

describe('InputComponent', () => {
    beforeEach(() => {
        document.body.textContent = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
        document.body.textContent = '';
    });

    it('init: iç input alanını bulur ve state\'e alır', () => {
        const { root, field } = buildInput();
        component = new InputComponent(root);
        component.init();

        expect(component.state.hint).toBe('En az 3 karakter');
        expect(component.value).toBe('');

        field.value = 'core';
        expect(component.value).toBe('core');
    });

    it('input: cm:input:change yayınlanır (detail.value)', () => {
        const { root, field } = buildInput();
        component = new InputComponent(root);
        component.init();
        component.mount();

        /** @type {CustomEvent|null} */
        let received = null;
        root.addEventListener('cm:input:change', (e) => {
            received = /** @type {CustomEvent} */ (e);
        });

        field.value = 'jazz';
        field.dispatchEvent(new Event('input', { bubbles: true }));

        expect(received).not.toBeNull();
        expect(received.detail.value).toBe('jazz');
        expect(component.state.value).toBe('jazz');
    });

    it('focus/blur: cm:input:focus ve cm:input:blur yayınlanır', () => {
        const { root, field } = buildInput();
        component = new InputComponent(root);
        component.init();
        component.mount();

        const events = [];
        root.addEventListener('cm:input:focus', () => events.push('focus'));
        root.addEventListener('cm:input:blur', () => events.push('blur'));

        field.dispatchEvent(new Event('focus', { bubbles: true }));
        field.dispatchEvent(new Event('blur', { bubbles: true }));

        expect(events).toEqual(['focus', 'blur']);
    });

    it('setStatus(\'error\'): sınıf + aria-invalid + hint', () => {
        const { root, field } = buildInput();
        component = new InputComponent(root);
        component.init();
        component.mount();

        component.setStatus('error', 'Geçersiz değer');

        expect(root.classList.contains('input--error')).toBe(true);
        expect(field.getAttribute('aria-invalid')).toBe('true');
        expect(root.querySelector('.input__hint').textContent).toBe('Geçersiz değer');
    });

    it('setStatus(\'success\'): success sınıfı, error kalkar', () => {
        const { root } = buildInput();
        component = new InputComponent(root);
        component.init();

        component.setStatus('error');
        component.setStatus('success');

        expect(root.classList.contains('input--error')).toBe(false);
        expect(root.classList.contains('input--success')).toBe(true);
    });

    it('setDisabled: field.disabled senkronlanır', () => {
        const { root, field } = buildInput();
        component = new InputComponent(root);
        component.init();

        component.setDisabled(true);
        expect(field.disabled).toBe(true);
        expect(root.classList.contains('input--disabled')).toBe(true);

        component.setDisabled(false);
        expect(field.disabled).toBe(false);
    });

    it('kök doğrudan input ise çalışır', () => {
        const field = document.createElement('input');
        field.className = 'input';
        field.type = 'text';
        field.setAttribute('data-cm-component', 'cm-input');
        document.body.appendChild(field);

        component = new InputComponent(field);
        component.init();
        component.mount();

        field.value = 'abc';
        field.dispatchEvent(new Event('input', { bubbles: true }));
        expect(component.state.value).toBe('abc');
    });
});
