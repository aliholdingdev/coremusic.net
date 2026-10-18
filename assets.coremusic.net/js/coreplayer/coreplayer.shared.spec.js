/**
 * @vitest-environment jsdom
 */
/**
 * coreplayer.shared.spec — window.CorePlayerShared sözleşmesi (salt test).
 *
 * Kapsam (görev tanımı): getMediaElement, setMediaType, savePlaybackStatus,
 * getCookie/setCookie — 4 API fonksiyonunun public dalları.
 * formatTime bilinçli olarak KAPSAM DIŞI (bu görev yalnız 4 fonksiyonu istiyor).
 *
 * Ortam kanıtı (probe, jsdom 30.1.1): vitest jsdom varsayılan URL'i
 * http://localhost:3000/ → location.protocol === 'http:'.
 * window.location non-configurable: Object.defineProperty →
 * "Cannot redefine property: location"; delete window.location → false.
 * Bu yüzden setCookie'un https → '; Secure' dalı spec'ten DOĞRULANAMAZ;
 * doğrulama vitest.config.js'e environmentOptions.jsdom.url=https://... gerektirir
 * (config değişikliği bu görevin kapsamı dışı → VERIFICATION REQUIRED).
 * Test edilebilen http: dalı alttaki cookie-spy testiyle kanıtlanır.
 *
 * Ortam kalıbı: breakpoints.spec.js (IIFE → beforeAll dynamic import) +
 * helper.spec.js (cookie-spy deseni).
 */
import { describe, it, expect, beforeAll, afterAll, afterEach, vi } from 'vitest';

/** Bu spec'in yazdığı tüm cookie isimleri — afterEach'de temizlenir. */
const SPEC_COOKIES = ['mediaType', 'isPlaying', 'cm_spec_round', 'cm_spec_spy', 'cm_spec_missing'];

function seedCookie(name, value) {
    document.cookie = name + '=' + value + ';path=/';
}

/** Silme: path=/ eşleşmesi + geçmiş expires (jsdom cookie jar contract). */
function expireCookie(name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
}

/** document event izleyicileri test sonunda kaldırılır (sızıntı yok). */
const tracked = [];
function onDocument(type, fn) {
    document.addEventListener(type, fn);
    tracked.push([type, fn]);
}

/** DOM elemanı üretimi — innerHTML YASAK (assets AGENTS.md §4.3) → createElement. */
function makeMedia(tag, id) {
    const el = document.createElement(tag);
    if (id) el.id = id;
    document.body.appendChild(el);
    return el;
}

/** @type {object|null} window.CorePlayerShared (IIFE side-effect sonrası) */
let S = null;

beforeAll(async () => {
    await import('./coreplayer.shared.js'); // IIFE → window.CorePlayerShared
    S = window.CorePlayerShared;
});

afterEach(() => {
    tracked.forEach(([type, fn]) => document.removeEventListener(type, fn));
    tracked.length = 0;
    SPEC_COOKIES.forEach(expireCookie);
    document.querySelectorAll('#video, #audio, #main-audio, video, audio')
        .forEach((el) => el.remove());
});

describe('getMediaElement — cookie mediaType dalları', () => {
    it('mediaType=video: #video kimlikli eleman döner (querySelector fallback’inden önce)', () => {
        seedCookie('mediaType', 'video');
        const byId = makeMedia('video', 'video');
        const unscoped = makeMedia('video');

        expect(S.getMediaElement()).toBe(byId);
        expect(S.getMediaElement()).not.toBe(unscoped);
    });

    it('mediaType=video: #video yoksa sayfadaki ilk <video> döner', () => {
        seedCookie('mediaType', 'video');
        const only = makeMedia('video');

        expect(S.getMediaElement()).toBe(only);
    });

    it('mediaType=video: hiç <video> yoksa null döner', () => {
        seedCookie('mediaType', 'video');

        expect(S.getMediaElement()).toBeNull();
    });

    it('mediaType=audio: #audio önceliklidir (#main-audio varken bile)', () => {
        seedCookie('mediaType', 'audio');
        makeMedia('audio', 'main-audio');
        const primary = makeMedia('audio', 'audio');

        expect(S.getMediaElement()).toBe(primary);
    });

    it('mediaType=audio: #audio yoksa #main-audio döner', () => {
        seedCookie('mediaType', 'audio');
        const fallback = makeMedia('audio', 'main-audio');

        expect(S.getMediaElement()).toBe(fallback);
    });

    it('mediaType cookie’siz (default dal): aday yoksa null, #main-audio eklenince o döner', () => {
        expect(S.getMediaElement()).toBeNull();

        const main = makeMedia('audio', 'main-audio');

        expect(S.getMediaElement()).toBe(main);
    });

    it('bilinmeyen mediaType (bogus): audio dalına düşer — video elemanı SEÇİLMEZ', () => {
        seedCookie('mediaType', 'bogus');
        makeMedia('video', 'video');
        const audioEl = makeMedia('audio', 'audio');

        expect(S.getMediaElement()).toBe(audioEl);
    });
});

describe('setMediaType — allowlist audio|video', () => {
    it('audio: true döner + cookie mediaType=audio + event cm:player:mediatype {type:audio}', () => {
        const evt = vi.fn();
        onDocument('cm:player:mediatype', evt);

        const ok = S.setMediaType('audio');

        expect(ok).toBe(true);
        expect(S.getCookie('mediaType')).toBe('audio');
        expect(evt).toHaveBeenCalledTimes(1);
        expect(evt.mock.calls[0][0].detail).toEqual({ type: 'audio' });
    });

    it('video: true döner + cookie mediaType=video + event {type:video}', () => {
        const evt = vi.fn();
        onDocument('cm:player:mediatype', evt);

        const ok = S.setMediaType('video');

        expect(ok).toBe(true);
        expect(S.getCookie('mediaType')).toBe('video');
        expect(evt).toHaveBeenCalledTimes(1);
        expect(evt.mock.calls[0][0].detail).toEqual({ type: 'video' });
    });

    it.each(['bogus', 42, undefined, null])(
        'geçersiz değer (%s): false döner + önceki cookie KORUNUR + event YOK',
        (bad) => {
            seedCookie('mediaType', 'audio'); // meşru önceki değer
            const evt = vi.fn();
            onDocument('cm:player:mediatype', evt);

            const ok = S.setMediaType(bad);

            expect(ok).toBe(false);
            expect(S.getCookie('mediaType')).toBe('audio');
            expect(evt).not.toHaveBeenCalled();
        }
    );
});

describe('savePlaybackStatus — allowlist play|pause|stop', () => {
    it.each(['play', 'pause', 'stop'])(
        '%s: true döner + cookie isPlaying=%s + event cm:player:status {status:%s}',
        (status) => {
            const evt = vi.fn();
            onDocument('cm:player:status', evt);

            const ok = S.savePlaybackStatus(status);

            expect(ok).toBe(true);
            expect(S.getCookie('isPlaying')).toBe(status);
            expect(evt).toHaveBeenCalledTimes(1);
            expect(evt.mock.calls[0][0].detail).toEqual({ status: status });
        }
    );

    it.each(['playing', 'PLAY', 1, undefined, null])(
        'geçersiz değer (%s): false döner + önceki cookie KORUNUR + event YOK',
        (bad) => {
            seedCookie('isPlaying', 'stop'); // meşru önceki değer
            const evt = vi.fn();
            onDocument('cm:player:status', evt);

            const ok = S.savePlaybackStatus(bad);

            expect(ok).toBe(false);
            expect(S.getCookie('isPlaying')).toBe('stop');
            expect(evt).not.toHaveBeenCalled();
        }
    );
});

describe('getCookie / setCookie yardımcıları', () => {
    it('roundtrip: setCookie’nin yazdığı değeri getCookie aynen okur', () => {
        S.setCookie('cm_spec_round', 'hello-world', 7);

        expect(S.getCookie('cm_spec_round')).toBe('hello-world');
    });

    it('var olmayan cookie ismi null döner', () => {
        expect(S.getCookie('cm_spec_missing')).toBeNull();
    });

    it('geçmiş expires ile silme: ertesi okumada null (temizlik mekanizmasının kendisi)', () => {
        S.setCookie('cm_spec_round', 'x', 7);
        expect(S.getCookie('cm_spec_round')).toBe('x');

        expireCookie('cm_spec_round');

        expect(S.getCookie('cm_spec_round')).toBeNull();
    });

    it('isim sınırı: "media" kısmi ismi, mediaType cookie’sinden yanlışlıkla okunmaz', () => {
        seedCookie('mediaType', 'video');

        expect(S.getCookie('media')).toBeNull();
    });
});

describe('setCookie — write string (cookie-spy, helper.spec.js deseni)', () => {
    /** @type {string[]} */
    const writes = [];
    /** @type {PropertyDescriptor|null} */
    let originalDescriptor = null;

    beforeAll(() => {
        originalDescriptor = Object.getOwnPropertyDescriptor(document, 'cookie')
            || Object.getOwnPropertyDescriptor(Document.prototype, 'cookie');
        Object.defineProperty(document, 'cookie', {
            configurable: true,
            get: () => writes.join('; '),
            set: (v) => { writes.push(String(v)); },
        });
    });

    afterAll(() => {
        if (originalDescriptor) {
            Object.defineProperty(document, 'cookie', originalDescriptor);
            originalDescriptor = null;
        }
    });

    it('http: dalı — expires + path=/ yazılır, "; Secure" EKLENMEZ (jsdom localhost kırılmasın)', () => {
        writes.length = 0;

        S.setCookie('cm_spec_spy', '1', 7);

        expect(writes).toHaveLength(1);
        expect(writes[0]).toContain('cm_spec_spy=1');
        expect(writes[0]).toContain('expires=');
        expect(writes[0]).toContain('path=/');
        expect(writes[0]).not.toContain('; Secure');
        // https: → '; Secure' dalı bu ortamda DOĞRULANAMAZ (dosya başlığı probe kanıtı:
        // jsdom 30 window.location non-configurable). Config-level doğrulama gerekir
        // → VERIFICATION REQUIRED, uydurma assertion yazılmadı.
    });
});
