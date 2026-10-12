// @vitest-environment jsdom
/**
 * BadgeComponent.spec — C10 cm-badge.
 *
 * @requires vitest + jsdom (paket henüz kurulu değil)
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import BadgeComponent from './BadgeComponent.js';

/** @type {BadgeComponent|null} */
let component = null;

/**
 * @param {{tag?: string, variant?: string, count?: string, interactive?: boolean}} [opts]
 * @returns {HTMLElement}
 */
function buildBadge(opts = {}) {
    const el = document.createElement(opts.tag || 'span');
    el.className = `badge badge--${opts.variant || 'primary'}`;
    el.setAttribute('data-cm-component', 'cm-badge');
    if (opts.count !== undefined) el.setAttribute('data-cm-count', opts.count);
    if (opts.interactive) el.setAttribute('data-cm-interactive', 'true');
    el.textContent = 'Yeni';
    document.body.appendChild(el);
    return el;
}

describe('BadgeComponent', () => {
    beforeEach(() => {
        document.body.textContent = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
        document.body.textContent = '';
    });

    it('init: varyant sınıftan, label textContent\'ten okunur', () => {
        const el = buildBadge({ variant: 'success' });
        component = new BadgeComponent(el);
        component.init();

        expect(component.state.variant).toBe('success');
        expect(component.state.label).toBe('Yeni');
    });

    it('span kök: tıklama yayım YAPMAZ (etkileşimli değil)', () => {
        const el = buildBadge();
        component = new BadgeComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:badge:click', () => {
            fired += 1;
        });

        el.click();
        expect(fired).toBe(0);
    });

    it('button kök: cm:badge:click yayınlanır', () => {
        const el = buildBadge({ tag: 'button', variant: 'error' });
        component = new BadgeComponent(el);
        component.init();
        component.mount();

        /** @type {CustomEvent|null} */
        let received = null;
        el.addEventListener('cm:badge:click', (e) => {
            received = /** @type {CustomEvent} */ (e);
        });

        el.click();

        expect(received).not.toBeNull();
        expect(received.detail.variant).toBe('error');
        expect(received.detail.label).toBe('Yeni');
    });

    it('setCount: metin count olur + badge--count + aria-label', () => {
        const el = buildBadge();
        component = new BadgeComponent(el);
        component.init();

        component.setCount(5);

        expect(el.textContent).toBe('5');
        expect(el.classList.contains('badge--count')).toBe(true);
        expect(el.getAttribute('aria-label')).toBe('Yeni: 5');

        component.setCount(null);
        expect(el.classList.contains('badge--count')).toBe(false);
        expect(el.textContent).toBe('Yeni');
    });

    it('setVariant: modifier değişimi (yalnız izinli set)', () => {
        const el = buildBadge({ variant: 'primary' });
        component = new BadgeComponent(el);
        component.init();

        component.setVariant('neutral');
        expect(el.classList.contains('badge--neutral')).toBe(true);
        expect(el.classList.contains('badge--primary')).toBe(false);

        component.setVariant('bogus'); // reddedilir
        expect(component.state.variant).toBe('neutral');
    });

    it('setLabel: textContent güncellenir (innerHTML değil)', () => {
        const el = buildBadge();
        component = new BadgeComponent(el);
        component.init();

        component.setLabel('Beta');
        expect(el.textContent).toBe('Beta');
    });

    it('data-cm-interactive=true olan span da yayım yapar', () => {
        const el = buildBadge({ interactive: true });
        component = new BadgeComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:badge:click', () => {
            fired += 1;
        });

        el.click();
        expect(fired).toBe(1);
    });
});
