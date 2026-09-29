// @vitest-environment jsdom
/**
 * SkeletonComponent.spec — C13 cm-skeleton.
 *
 * @requires vitest + jsdom (paket henüz kurulu değil)
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import SkeletonComponent from './SkeletonComponent.js';

/** @type {SkeletonComponent|null} */
let component = null;

/**
 * @param {{variant?: string, active?: boolean}} [opts]
 * @returns {HTMLElement}
 */
function buildSkeleton(opts = {}) {
    const el = document.createElement('div');
    el.className = `skeleton skeleton--${opts.variant || 'text'}`;
    el.setAttribute('data-cm-component', 'cm-skeleton');
    el.setAttribute('data-cm-active', String(opts.active !== false));
    document.body.appendChild(el);
    return el;
}

describe('SkeletonComponent', () => {
    beforeEach(() => {
        document.body.textContent = '';
    });

    afterEach(() => {
        component?.destroy();
        component = null;
        document.body.textContent = '';
    });

    it('init: varyant sınıftan, active data attribute\'tan okunur', () => {
        const el = buildSkeleton({ variant: 'circle' });
        component = new SkeletonComponent(el);
        component.init();

        expect(component.state.variant).toBe('circle');
        expect(component.state.active).toBe(true);
        expect(el.getAttribute('aria-busy')).toBe('true');
        expect(el.classList.contains('is-loaded')).toBe(false);
    });

    it('setActive(false): is-loaded + aria-busy=false + tek seferlik event', () => {
        const el = buildSkeleton();
        component = new SkeletonComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:skeleton:loaded', () => {
            fired += 1;
        });

        component.setActive(false);

        expect(fired).toBe(1);
        expect(el.classList.contains('is-loaded')).toBe(true);
        expect(el.getAttribute('aria-busy')).toBe('false');
        expect(el.getAttribute('aria-hidden')).toBe('true');

        // Aynı değere tekrar: ikinci yayım YOK
        component.setActive(false);
        expect(fired).toBe(1);
    });

    it('yeniden aktifleşip pasifleşince event tekrar yayınlar', () => {
        const el = buildSkeleton();
        component = new SkeletonComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:skeleton:loaded', () => {
            fired += 1;
        });

        component.setActive(false);
        component.setActive(true);
        component.setActive(false);

        expect(fired).toBe(2);
    });

    it('setVariant: text → rect → circle modifier değişimi', () => {
        const el = buildSkeleton({ variant: 'text' });
        component = new SkeletonComponent(el);
        component.init();

        component.setVariant('rect');
        expect(el.classList.contains('skeleton--rect')).toBe(true);
        expect(el.classList.contains('skeleton--text')).toBe(false);

        component.setVariant('circle');
        expect(el.classList.contains('skeleton--circle')).toBe(true);
        expect(el.classList.contains('skeleton--rect')).toBe(false);

        component.setVariant('bogus'); // reddedilir
        expect(component.state.variant).toBe('circle');
    });

    it('is-loaded sınıfı geri alınabilir (yeniden yükleme)', () => {
        const el = buildSkeleton();
        component = new SkeletonComponent(el);
        component.init();

        component.setActive(false);
        expect(el.classList.contains('is-loaded')).toBe(true);

        component.setActive(true);
        expect(el.classList.contains('is-loaded')).toBe(false);
        expect(el.getAttribute('aria-busy')).toBe('true');
        expect(el.getAttribute('aria-hidden')).toBe('false');
    });

    it('destroy: tekrar kullanılabilir (idempotent)', () => {
        const el = buildSkeleton();
        component = new SkeletonComponent(el);
        component.init();
        component.mount();

        component.destroy();
        component.destroy();
        component = null;
        expect(true).toBe(true);
    });
});
