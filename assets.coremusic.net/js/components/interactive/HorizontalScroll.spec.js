/**
 * @vitest-environment jsdom
 */
import { describe, it, expect } from 'vitest';
import { wheelToHorizontalDelta, canScrollHorizontally } from './HorizontalScroll.js';

describe('wheelToHorizontalDelta', () => {
    it('dikey tekerleği piksel olarak yatay kaydırmaya çevirir', () => {
        expect(wheelToHorizontalDelta(0, 100, 0, 800)).toBe(100);
    });

    it('yatay bileşen baskınsa deltaX kullanılır', () => {
        expect(wheelToHorizontalDelta(30, 10, 0, 800)).toBe(30);
    });

    it('satır modunda (deltaMode=1) 16px karşılığı uygulanır', () => {
        expect(wheelToHorizontalDelta(0, 3, 1, 800)).toBe(48);
    });

    it('sayfa modunda (deltaMode=2) kapsayıcı genişliği kadar kaydırır', () => {
        expect(wheelToHorizontalDelta(0, 1, 2, 800)).toBe(800);
    });

    it('negatif delta geri kaydırmayı verir', () => {
        expect(wheelToHorizontalDelta(0, -120, 0, 800)).toBe(-120);
    });
});

describe('canScrollHorizontally', () => {
    const metrics = (scrollLeft) => ({ scrollLeft, scrollWidth: 2000, clientWidth: 1000 });

    it('başlangıçta ileri yönde kaydırılabilir', () => {
        expect(canScrollHorizontally(metrics(0), 100)).toBe(true);
    });

    it('sonda ileri yönde kaydırılamaz (olay sayfaya geri verilir)', () => {
        expect(canScrollHorizontally(metrics(1000), 100)).toBe(false);
    });

    it('başlangıçta geri yönde kaydırılamaz', () => {
        expect(canScrollHorizontally(metrics(0), -100)).toBe(false);
    });

    it('ortada her iki yönde kaydırılabilir', () => {
        expect(canScrollHorizontally(metrics(500), -100)).toBe(true);
        expect(canScrollHorizontally(metrics(500), 100)).toBe(true);
    });

    it('taşma yoksa kaydırılamaz', () => {
        expect(canScrollHorizontally({ scrollLeft: 0, scrollWidth: 900, clientWidth: 1000 }, 100)).toBe(false);
    });

    it('delta 0 ise kaydırılamaz', () => {
        expect(canScrollHorizontally(metrics(500), 0)).toBe(false);
    });
});
