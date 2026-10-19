// @vitest-environment jsdom
/**
 * helper.spec — C4: setCookie (cookiesSet) Secure flag davranışı.
 *
 * Sözleşme:
 *  - default: location.protocol === 'https:' → '; Secure' eklenir
 *    (http://localhost'da eklenmez → cookie KIRILMAZ)
 *  - secure parametresi açıkça verilirse default ezilir
 *  - mevcut 3 argümanlı çağrı imzası DEĞİŞMEZ (geriye dönük uyum)
 * Kural: innerHTML YASAK.
 */
import { describe, it, expect, beforeAll, afterAll } from 'vitest';

/** @type {PropertyDescriptor|null} */
let originalDescriptor = null;
/** @type {string[]} */
const writes = [];

function installCookieSpy() {
    originalDescriptor = Object.getOwnPropertyDescriptor(document, 'cookie')
        || Object.getOwnPropertyDescriptor(Document.prototype, 'cookie');
    writes.length = 0;
    Object.defineProperty(document, 'cookie', {
        configurable: true,
        get: () => writes.join('; '),
        set: (v) => { writes.push(String(v)); },
    });
}

function restoreCookie() {
    if (originalDescriptor) {
        Object.defineProperty(document, 'cookie', originalDescriptor);
        originalDescriptor = null;
    }
}

describe('CoreHelper.cookies.set — Secure flag (C4a)', () => {
    beforeAll(async () => {
        await import('../../js/core/helper.js'); // IIFE → window.CoreHelper
        installCookieSpy();
    });

    afterAll(() => {
        restoreCookie();
    });

    it('http (default): Secure EKLENMEZ — localhost cookie’si kırılmaz', () => {
        writes.length = 0;
        window.CoreHelper.cookies.set('cm_test_a', '1', 7);

        expect(writes).toHaveLength(1);
        expect(writes[0]).toContain('path=/');
        expect(writes[0]).not.toContain('; Secure');
    });

    it('secure=true: "; Secure" eklenir (4. argüman override)', () => {
        writes.length = 0;
        window.CoreHelper.cookies.set('cm_test_b', '1', 7, true);

        expect(writes).toHaveLength(1);
        expect(writes[0]).toContain('; Secure');
    });

    it('secure=false: default ezilir, Secure YOK', () => {
        writes.length = 0;
        window.CoreHelper.cookies.set('cm_test_c', '1', 7, false);

        expect(writes).toHaveLength(1);
        expect(writes[0]).not.toContain('; Secure');
    });

    it('geriye dönük uyum: 3 argümanlı çağrı sorunsuz + expires korunur', () => {
        writes.length = 0;
        window.CoreHelper.cookies.set('cm_test_d', 'v', 30);

        expect(writes).toHaveLength(1);
        expect(writes[0]).toContain('cm_test_d=v');
        expect(writes[0]).toContain('expires=');
    });
});
