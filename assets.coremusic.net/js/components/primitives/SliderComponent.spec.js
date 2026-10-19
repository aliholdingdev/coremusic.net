// @vitest-environment jsdom
/**
 * SliderComponent.spec — C09 cm-slider.
 *
 * @requires vitest + jsdom (paket henüz kurulu değil)
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import SliderComponent from './SliderComponent.js';

/** @type {SliderComponent|null} */
let component = null;

/**
 * @param {{min?: number, max?: number, step?: number, value?: number, rootInput?: boolean}} [opts]
 * @returns {HTMLElement}
 */
function buildSlider(opts = {}) {
    const input = document.createElement('input');
    input.type = 'range';
    input.className = 'slider__input';
    input.min = String(opts.min ?? 0);
    input.max = String(opts.max ?? 100);
    input.step = String(opts.step ?? 1);
    input.value = String(opts.value ?? 0);
    input.setAttribute('aria-label', 'Seviye');

    if (opts.rootInput) {
        input.className = 'slider';
        input.setAttribute('data-cm-component', 'cm-slider');
        document.body.appendChild(input);
        return input;
    }

    const root = document.createElement('div');
    root.className = 'slider';
    root.setAttribute('data-cm-component', 'cm-slider');
    root.appendChild(input);
    document.body.appendChild(root);
    return root;
}

describe('SliderComponent', () => {
    beforeEach(() => {
        document.body.textContent = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
        document.body.textContent = '';
    });

    it('init: min/max/step/value state\'e geçer', () => {
        const root = buildSlider({ min: 0, max: 200, step: 5, value: 50 });
        component = new SliderComponent(root);
        component.init();

        expect(component.state.max).toBe(200);
        expect(component.state.step).toBe(5);
        expect(component.state.value).toBe(50);
        expect(component.value).toBe('50');
    });

    it('onUpdate: --cm-slider-progress dolum değişkenini yazar', () => {
        const root = buildSlider({ value: 25 });
        component = new SliderComponent(root);
        component.init();
        component.mount();

        component.setValue(50);

        const written = root.style.getPropertyValue('--cm-slider-progress');
        expect(written).toBe('50.00%');
    });

    it('setValue: min altına ve max üstüne kırpılır', () => {
        const root = buildSlider({ min: 0, max: 100 });
        component = new SliderComponent(root);
        component.init();

        component.setValue(-10);
        expect(component.state.value).toBe(0);

        component.setValue(500);
        expect(component.state.value).toBe(100);
    });

    it('input eventi: cm:slider:input yayınlanır (detail.value + progress)', () => {
        const root = buildSlider();
        const field = root.querySelector('input[type="range"]');
        component = new SliderComponent(root);
        component.init();
        component.mount();

        /** @type {CustomEvent|null} */
        let received = null;
        root.addEventListener('cm:slider:input', (e) => {
            received = /** @type {CustomEvent} */ (e);
        });

        field.value = '40';
        field.dispatchEvent(new Event('input', { bubbles: true }));

        expect(received).not.toBeNull();
        expect(received.detail.value).toBe(40);
        expect(received.detail.progress).toBe(40);
        expect(component.state.value).toBe(40);
    });

    it('change eventi: cm:slider:change yayınlanır', () => {
        const root = buildSlider();
        const field = root.querySelector('input[type="range"]');
        component = new SliderComponent(root);
        component.init();
        component.mount();

        let fired = 0;
        root.addEventListener('cm:slider:change', () => {
            fired += 1;
        });

        field.value = '70';
        field.dispatchEvent(new Event('change', { bubbles: true }));
        expect(fired).toBe(1);
    });

    it('aria-valuenow/min/max senkronlanır', () => {
        const root = buildSlider();
        const field = root.querySelector('input[type="range"]');
        component = new SliderComponent(root);
        component.init();
        component.mount();

        component.setValue(30);

        expect(field.getAttribute('aria-valuenow')).toBe('30');
        expect(field.getAttribute('aria-valuemin')).toBe('0');
        expect(field.getAttribute('aria-valuemax')).toBe('100');
    });

    it('kök doğrudan range input ise çalışır + disabled', () => {
        const field = buildSlider({ rootInput: true, value: 10 });
        component = new SliderComponent(field);
        component.init();
        component.mount();

        component.setDisabled(true);
        expect(field.disabled).toBe(true);
        expect(field.classList.contains('slider--disabled')).toBe(true);
    });

    it('boş kök: input + track/fill + thumb DOM ile kurulur (innerHTML yok)', () => {
        const root = document.createElement('div');
        root.className = 'slider';
        root.setAttribute('data-cm-component', 'cm-slider');
        root.setAttribute('data-cm-label', 'Seviye');
        document.body.appendChild(root);

        component = new SliderComponent(root);
        component.init();
        component.mount();

        const field = root.querySelector('input[type="range"]');
        expect(field).not.toBeNull();
        expect(field.getAttribute('class')).toBe('slider__input');
        expect(field.getAttribute('aria-label')).toBe('Seviye');
        expect(root.querySelector('.slider__track .slider__fill')).not.toBeNull();
        expect(root.querySelector('.slider__thumb')).not.toBeNull();
        // DOM sırası: input ilk sırada (CSS: input ~ thumb)
        expect(root.firstElementChild).toBe(field);

        field.value = '30';
        field.dispatchEvent(new Event('input', { bubbles: true }));
        expect(component.state.value).toBe(30);
        expect(root.style.getPropertyValue('--cm-slider-progress')).toBe('30.00%');
    });
});
