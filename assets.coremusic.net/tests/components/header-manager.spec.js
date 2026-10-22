// @vitest-environment jsdom
/**
 * header-manager.spec.js — HeaderManager (Adım 10) unit tests
 *
 * Coverage:
 * ✓ Login özeti: profil+rol (textContent) + playlist kısayolları + bildirim rozeti
 * ✓ Anonim özet: PHP fallback isim korunur (sessiz boşluk YASAK)
 * ✓ API 500 → DOM hiç değişmez (fail-safe)
 * ✓ Idempotent render: 2. çağrıda playlist linkleri çoğalmaz
 * ✓ XSS: playlist adı HTML payload olarak yazılmaz (textContent-only)
 * ✓ CSP: render sonrası element.style boş (inline style YOK)
 *
 * @requires vitest + jsdom
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

const MODULE_PATH = '../../js/header-manager.js';
const MUSIC_LIST_ID = '018f3a5b6c7d8e9f0123456789abcdef';

/** header.php ile aynı seçiciler */
function buildHeaderDom() {
    document.body.innerHTML = `
        <header class="header site-header" role="banner">
            <div class="header-user" role="button" aria-expanded="false">
                <img class="header-user__avatar" src="/x.png" alt="Avatar"/>
                <span class="header-user__name">Misafir</span>
                <div class="header-user-dropdown" role="menu">
                    <a href="/ayarlar" role="menuitem" data-no-spa>Ayarlar</a>
                    <a href="/history" role="menuitem" data-no-spa>Geçmiş</a>
                    <a href="/logout" class="logout-btn" role="menuitem" data-no-spa>Çıkış Yap</a>
                </div>
            </div>
            <div class="site-header__actions">
                <button type="button" class="header-action-btn" data-action="notifications" aria-label="Bildirimler"></button>
                <a href="/logout" class="header-action-btn" aria-label="Çıkış"></a>
            </div>
        </header>
        <input type="search" aria-label="Ara"/>`;
}

function stubFetch(payload, { ok = true, status = 200 } = {}) {
    vi.stubGlobal('fetch', vi.fn().mockResolvedValue({
        ok,
        status,
        json: async () => payload,
    }));
}

/** IIFE modülü taze sayfada yükler (side-effect: new HeaderManager) */
async function loadModule() {
    vi.resetModules();
    await import(MODULE_PATH);
}

const loggedInPayload = {
    success: true,
    data: {
        user: { id: 'u1', username: 'Bayram', role: 'admin' },
        is_authenticated: true,
        playlist_shortcuts: [
            { playlist_id: MUSIC_LIST_ID, name: 'Yol Müzikleri' },
            { playlist_id: 'bb', name: 'Çalışma' },
        ],
        unread_notifications: 3,
        search_suggestions: [],
    },
};

const anonPayload = {
    success: true,
    data: {
        user: null,
        is_authenticated: false,
        playlist_shortcuts: [],
        unread_notifications: 0,
        search_suggestions: [],
    },
};

describe('HeaderManager (Adım 10 — hibrit header fetch)', () => {
    beforeEach(() => {
        buildHeaderDom();
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        document.body.innerHTML = '';
    });

    it('login özeti üç bloğu da render eder (profil+rol, playlist, bildirim)', async () => {
        stubFetch(loggedInPayload);

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelector('.header-user__name').textContent).toBe('Bayram (ADMIN)');
        });

        const container = document.querySelector('.header-dropdown-playlists');
        expect(container).not.toBeNull();
        expect(container.querySelector('.header-dropdown-playlists__title').textContent)
            .toBe('Çalma Listelerim');
        expect(container.querySelectorAll('a')).toHaveLength(2);
        expect(container.querySelector('a').getAttribute('href'))
            .toBe('/playlist?id=' + MUSIC_LIST_ID);
        expect(container.querySelector('a').getAttribute('role')).toBe('menuitem');

        const badge = document.querySelector('.header-notification-badge');
        expect(badge.textContent).toBe('3');
        expect(badge.hidden).toBe(false);
    });

    it('CSP: render sonrası hiçbir elementte inline style yok', async () => {
        stubFetch(loggedInPayload);

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelector('.header-notification-badge')).not.toBeNull();
        });

        const styled = [...document.querySelectorAll('body *')]
            .filter((el) => el.getAttribute('style') !== null && el.getAttribute('style') !== '');
        expect(styled).toEqual([]);
    });

    it('anonim özet: PHP fallback isim korunur, badge/liste eklenmez (fail-safe)', async () => {
        stubFetch(anonPayload);

        await loadModule();

        await vi.waitFor(() => {
            expect(fetch).toHaveBeenCalledTimes(1);
        });

        expect(document.querySelector('.header-user__name').textContent).toBe('Misafir');
        expect(document.querySelector('.header-dropdown-playlists')).toBeNull();
        expect(document.querySelector('.header-notification-badge')).toBeNull();
    });

    it('API 500 → DOM hiç değişmez (§9: sessiz boşluk YASAK, mevcut UI korunur)', async () => {
        stubFetch({ success: false }, { ok: false, status: 500 });

        await loadModule();

        await vi.waitFor(() => {
            expect(fetch).toHaveBeenCalledTimes(1);
        });

        expect(document.querySelector('.header-user__name').textContent).toBe('Misafir');
        expect(document.querySelector('.header-dropdown-playlists')).toBeNull();
    });

    it('idempotent: renderHeaderData iki kez çalışırsa linkler çoğalmaz', async () => {
        stubFetch(loggedInPayload);

        vi.resetModules();
        const mod = await import(MODULE_PATH);

        await vi.waitFor(() => {
            expect(document.querySelectorAll('.header-dropdown-playlists a')).toHaveLength(2);
        });

        /* modül IIFE dışa aktarmıyor — yeniden tetik için doğrudan DOM'a 2. render simülasyonu:
           aynı fetch tekrar çağrılır (mock hazır) */
        await loadModule();
        await vi.waitFor(() => {
            expect(document.querySelectorAll('.header-dropdown-playlists a')).toHaveLength(2);
        });
        expect(document.querySelectorAll('.header-dropdown-playlists__title')).toHaveLength(1);
        expect(mod).toBeDefined();
    });

    it('XSS: playlist adı HTML payload olarak yazılmaz (textContent-only)', async () => {
        stubFetch({
            success: true,
            data: {
                user: { username: '<img src=x onerror=alert(1)>', role: 'user' },
                playlist_shortcuts: [{ playlist_id: 'aa', name: '<img src=x onerror=alert(2)>' }],
                unread_notifications: 1,
                search_suggestions: [],
            },
        });

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelector('.header-dropdown-playlists a')).not.toBeNull();
        });

        expect(document.querySelectorAll('img[onerror]')).toHaveLength(0); /* payload DOM'a img EKLEMEDİ */
        const nameEl = document.querySelector('.header-user__name');
        expect(nameEl.textContent).toContain('<img src=x onerror=alert(1)>');
        expect(nameEl.querySelector('img')).toBeNull();
        const link = document.querySelector('.header-dropdown-playlists a');
        expect(link.querySelector('img')).toBeNull();
    });
});

describe('HeaderManager — Adım 11 (rol bazlı dropdown menü + SPA)', () => {
    const adminMenu = [
        { id: 'settings', label: 'Ayarlar', route: '/ayarlar', roles: ['user', 'admin'] },
        { id: 'history', label: 'Geçmiş', route: '/history', roles: ['user', 'admin'] },
        { id: 'admin', label: 'Admin Panel', route: 'https://admin.coremusic.net/', roles: ['admin'] },
    ];
    const userMenu = adminMenu.slice(0, 2);

    function payloadWithMenu(menu, extra = {}) {
        return {
            success: true,
            data: {
                user: { id: 'u1', username: 'Bayram', role: 'admin' },
                playlist_shortcuts: [],
                unread_notifications: 0,
                menu,
                search_suggestions: [],
                ...extra,
            },
        };
    }

    const stat = (href) => `.header-user-dropdown > a[href="${href}"]`;

    beforeEach(() => {
        buildHeaderDom();
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        document.body.innerHTML = '';
    });

    it('rol admin → 3 menü linki; statik PHP öğeleri gizli (çift menü YOK), logout görünür', async () => {
        stubFetch(payloadWithMenu(adminMenu));

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelectorAll('.header-dropdown-menu a')).toHaveLength(3);
        });
        expect(document.querySelector(stat('/ayarlar')).hidden).toBe(true);
        expect(document.querySelector(stat('/history')).hidden).toBe(true);
        expect(document.querySelector('.logout-btn').hidden).toBe(false);
    });

    it('rol user → API yalnız 2 öğe gönderir; admin link DOM\'da YOK (Hard Rule 2)', async () => {
        stubFetch(payloadWithMenu(userMenu, { user: { username: 'normal', role: 'user' } }));

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelectorAll('.header-dropdown-menu a')).toHaveLength(2);
        });
        expect(document.querySelector('.header-dropdown-menu a[href="https://admin.coremusic.net/"]')).toBeNull();
        expect(document.querySelector('.header-dropdown-menu a[href="/ayarlar"]')).not.toBeNull();
    });

    it('SPA: same-origin link data-route + data-no-spa YOK (delegate navigate eder); cross-origin admin data-no-spa (Hard Rule 3)', async () => {
        stubFetch(payloadWithMenu(adminMenu));

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelector('.header-dropdown-menu a[href="/ayarlar"]')).not.toBeNull();
        });

        const settings = document.querySelector('.header-dropdown-menu a[href="/ayarlar"]');
        expect(settings.getAttribute('data-route')).toBe('true');
        expect(settings.hasAttribute('data-no-spa')).toBe(false);
        expect(settings.getAttribute('role')).toBe('menuitem');

        const admin = document.querySelector('.header-dropdown-menu a[href="https://admin.coremusic.net/"]');
        expect(admin.hasAttribute('data-no-spa')).toBe(true);
        expect(admin.getAttribute('rel')).toBe('external');
    });

    it('anonim menü [] → statik öğeler gizli, yalnız logout görünür (API\'den gelmeyen öğe DOM\'da olmaz)', async () => {
        stubFetch({
            success: true,
            data: {
                user: null,
                playlist_shortcuts: [],
                unread_notifications: 0,
                menu: [],
                search_suggestions: [],
            },
        });

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelector(stat('/ayarlar')).hidden).toBe(true);
        });
        expect(document.querySelector('.header-dropdown-menu')).toBeNull();
        expect(document.querySelector(stat('/history')).hidden).toBe(true);
        expect(document.querySelector('.logout-btn').hidden).toBe(false);
    });

    it('§9 oturum/akış hatası: önce admin menü render, sonra 500 → eski menü gizlenir, statik öğeler geri gelir', async () => {
        stubFetch(payloadWithMenu(adminMenu));
        await loadModule();
        await vi.waitFor(() => {
            expect(document.querySelectorAll('.header-dropdown-menu a')).toHaveLength(3);
        });

        vi.unstubAllGlobals();
        stubFetch({ success: false }, { ok: false, status: 500 });
        await loadModule(); // yeni instance — aynı DOM

        await vi.waitFor(() => {
            expect(fetch).toHaveBeenCalledTimes(1);
        });
        expect(document.querySelector('.header-dropdown-menu').hidden).toBe(true);
        expect(document.querySelector(stat('/ayarlar')).hidden).toBe(false);
        expect(document.querySelector(stat('/history')).hidden).toBe(false);
    });

    it('XSS: menü label\'ı textContent ile yazılır (payload img OLUŞTURMAZ)', async () => {
        stubFetch(payloadWithMenu([
            { id: 'x', label: '<img src=x onerror=alert(9)>', route: '/ayarlar', roles: ['admin'] },
        ]));

        await loadModule();

        await vi.waitFor(() => {
            expect(document.querySelector('.header-dropdown-menu a')).not.toBeNull();
        });

        const link = document.querySelector('.header-dropdown-menu a');
        expect(link.textContent).toBe('<img src=x onerror=alert(9)>');
        expect(link.querySelector('img')).toBeNull();
        expect(document.querySelectorAll('img[onerror]')).toHaveLength(0);
    });
});
