// @vitest-environment jsdom
/**
 * ComponentRegistry.spec — F1: key çakışması düzeltmesi.
 *
 * Sözleşme: id'siz + cmId'siz aynı isimli 2+ element → 2 AYRI instance;
 * cmId yoksa benzersiz kimlik üretilir ve elemente kaydedilir (cm-<seq>);
 * mevcut id/cmId kullanımı öncelikli korunur.
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import ComponentRegistry from '../../js/components/base/ComponentRegistry.js';

/**
 * Basit stub — registry yalnız constructor + destroy çağırır.
 * (ComponentBase abstract olduğu için doğrudan kullanılamaz.)
 */
class StubComponent {
    constructor(element, initialState = {}) {
        this.element = element;
        this.initialState = initialState;
    }
    destroy() {}
}

/** @type {ComponentRegistry|null} */
let registry = null;

describe('ComponentRegistry', () => {
    beforeEach(() => {
        document.body.textContent = '';
        // Test başına taze instance (singleton değil — test izolasyonu)
        registry = new ComponentRegistry();
    });

    afterEach(() => {
        registry?.destroyAll();
        registry = null;
        document.body.textContent = '';
    });

    it('aynı isimli 2 id’siz element → 2 AYRI instance (F1)', () => {
        registry.register('cm-stub', StubComponent);
        const a = document.createElement('div');
        const b = document.createElement('div');
        document.body.append(a, b);

        const instanceA = registry.getInstance('cm-stub', a);
        const instanceB = registry.getInstance('cm-stub', b);

        expect(instanceA).not.toBeNull();
        expect(instanceB).not.toBeNull();
        expect(instanceA).not.toBe(instanceB);
        expect(instanceA.element).toBe(a);
        expect(instanceB.element).toBe(b);
    });

    it('cmId yoksa üretilir ve elemente kaydedilir (cm-<seq>, benzersiz)', () => {
        registry.register('cm-stub', StubComponent);
        const a = document.createElement('div');
        const b = document.createElement('div');
        document.body.append(a, b);

        registry.getInstance('cm-stub', a);
        registry.getInstance('cm-stub', b);

        expect(a.dataset.cmId).toMatch(/^cm-\d+$/);
        expect(b.dataset.cmId).toMatch(/^cm-\d+$/);
        expect(a.dataset.cmId).not.toBe(b.dataset.cmId);
    });

    it('singleton-per-element korunur: aynı element 2. çağrıda aynı instance', () => {
        registry.register('cm-stub', StubComponent);
        const a = document.createElement('div');
        document.body.append(a);

        const first = registry.getInstance('cm-stub', a);
        const second = registry.getInstance('cm-stub', a);

        expect(second).toBe(first);
    });

    it('id öncelikli: key id’den türetilir, mevcut cmId ezilmez', () => {
        registry.register('cm-stub', StubComponent);
        const a = document.createElement('div');
        a.id = 'tab-1';
        a.dataset.cmId = 'cm-custom';
        document.body.append(a);

        const first = registry.getInstance('cm-stub', a);

        expect(a.dataset.cmId).toBe('cm-custom'); // mevcut cmId korundu
        expect(registry.getInstance('cm-stub', a)).toBe(first);

        const b = document.createElement('div');
        b.id = 'tab-2';
        document.body.append(b);
        expect(registry.getInstance('cm-stub', b)).not.toBe(first);
    });

    it('3+ element, hiçbiri kimliksiz → 3 ayrı instance + 3 farklı cmId', () => {
        registry.register('cm-stub', StubComponent);
        const els = [0, 1, 2].map(() => {
            const el = document.createElement('div');
            document.body.append(el);
            return el;
        });

        const instances = els.map((el) => registry.getInstance('cm-stub', el));

        expect(new Set(instances).size).toBe(3);
        const cmIds = els.map((el) => el.dataset.cmId);
        expect(new Set(cmIds).size).toBe(3);
    });

    it('kayıtsız isim → null döner + console.error (akış bozulmaz)', () => {
        const errorSpy = vi.spyOn(console, 'error').mockImplementation(() => {});
        const a = document.createElement('div');
        document.body.append(a);

        expect(registry.getInstance('cm-unknown', a)).toBeNull();
        expect(errorSpy).toHaveBeenCalledOnce();
        errorSpy.mockRestore();
    });

    it('destroyAll sonrası aynı element yeniden instance edilir (cache temiz)', () => {
        registry.register('cm-stub', StubComponent);
        const a = document.createElement('div');
        document.body.append(a);

        const first = registry.getInstance('cm-stub', a);
        registry.destroyAll();
        const second = registry.getInstance('cm-stub', a);

        expect(second).not.toBe(first);
    });

    /* ─── P1-6: SPA instance sızıntısı — destroyDetached ─── */

    it('destroyDetached: KOPMUŞ element destroy edilir + cache temizlenir', () => {
        registry.register('cm-stub', StubComponent);
        const drop = document.createElement('div');
        document.body.append(drop);

        const stale = registry.getInstance('cm-stub', drop);
        const destroySpy = vi.spyOn(stale, 'destroy');

        // SPA navigasyonu: DomPatcher.replaceChildren ile DOM kopar
        drop.remove();
        const removed = registry.destroyDetached();

        expect(removed).toBe(1);
        expect(destroySpy).toHaveBeenCalledTimes(1);

        // Cache temiz: element geri gelse bile YENİ instance üretilir
        document.body.append(drop);
        expect(registry.getInstance('cm-stub', drop)).not.toBe(stale);
    });

    it('destroyDetached: BAĞLI (canlı) instance’a DOKUNMAZ — canlı UI bozulmaz', () => {
        registry.register('cm-stub', StubComponent);
        const live = document.createElement('div');
        const stale = document.createElement('div');
        document.body.append(live, stale);

        const liveInst = registry.getInstance('cm-stub', live);
        const staleInst = registry.getInstance('cm-stub', stale);
        const liveDestroy = vi.spyOn(liveInst, 'destroy');
        const staleDestroy = vi.spyOn(staleInst, 'destroy');

        stale.remove();
        registry.destroyDetached();

        expect(staleDestroy).toHaveBeenCalledTimes(1);
        expect(liveDestroy).not.toHaveBeenCalled();
        // canlı instance aynı referans olarak cache'te durur
        expect(registry.getInstance('cm-stub', live)).toBe(liveInst);
    });

    it('destroyDetached: kopuk element yoksa 0 döner, kimse destroy edilmez', () => {
        registry.register('cm-stub', StubComponent);
        const a = document.createElement('div');
        document.body.append(a);

        const inst = registry.getInstance('cm-stub', a);
        const spy = vi.spyOn(inst, 'destroy');

        expect(registry.destroyDetached()).toBe(0);
        expect(spy).not.toHaveBeenCalled();
        expect(registry.getInstance('cm-stub', a)).toBe(inst);
    });
});
