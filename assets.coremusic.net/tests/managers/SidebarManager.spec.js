// @vitest-environment jsdom
/**
 * SidebarManager.spec — P1-4/5: destroy() listener sızıntısı.
 *
 * Sözleşme:
 *  - init() eklenen dinleyiciler (window resize, toggle click, overlay click,
 *    sidebar click/keydown, ResizeHandle mousedown/touchstart + document drag)
 *    destroy() ile removeEventListener ile KALDIRILIR.
 *  - destroy sonrası olay yayılmaz (SPA'da dinleyici birikmez).
 * Kural: innerHTML YASAK — DOM elle kurulur.
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import SidebarManager, { ResizeHandle } from '../../js/managers/SidebarManager.js';

/** @returns {{sidebar: HTMLElement, toggle: HTMLElement, overlay: HTMLElement, handle: HTMLElement}} */
function buildDom() {
    const sidebar = document.createElement('div');
    sidebar.className = 'sidebar';
    const handle = document.createElement('div');
    handle.className = 'sidebar__resize-handle';
    sidebar.appendChild(handle);

    const toggle = document.createElement('button');
    toggle.className = 'sidebar-toggle';
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';

    document.body.append(sidebar, toggle, overlay);
    return { sidebar, toggle, overlay, handle };
}

/** @returns {{emit: ReturnType<typeof vi.fn>, on: ReturnType<typeof vi.fn>}} */
function fakeBus() {
    return { emit: vi.fn(), on: vi.fn() };
}

describe('SidebarManager.destroy — listener temizliği (P1-5)', () => {
    beforeEach(() => {
        document.body.replaceChildren();
        localStorage.clear();
    });

    afterEach(() => {
        document.body.replaceChildren();
        document.documentElement.removeAttribute('data-sidebar-mode');
    });

    it('destroy sonrası window resize handler çağrılmaz', () => {
        const bus = fakeBus();
        buildDom();
        const mgr = new SidebarManager(bus, { userId: 'spec-sidebar' });
        mgr.init();

        mgr.destroy();
        document.documentElement.setAttribute('data-sidebar-mode', 'sentinel');

        window.dispatchEvent(new Event('resize'));

        expect(document.documentElement.getAttribute('data-sidebar-mode')).toBe('sentinel');
    });

    it('destroy sonrası toggle butonuna click → sidebar:toggle YAYILMAZ', () => {
        const bus = fakeBus();
        const { toggle } = buildDom();
        const mgr = new SidebarManager(bus, { userId: 'spec-sidebar' });
        mgr.init();
        bus.emit.mockClear();

        mgr.destroy();
        toggle.dispatchEvent(new Event('click', { bubbles: true }));

        expect(bus.emit).not.toHaveBeenCalledWith('sidebar:toggle', expect.anything());
        expect(bus.emit).not.toHaveBeenCalled();
    });

    it('destroy sonrası overlay click → toggle ÇAĞRILMAZ (collapsed değişmez)', () => {
        const bus = fakeBus();
        const { sidebar, overlay } = buildDom();
        const mgr = new SidebarManager(bus, { userId: 'spec-sidebar' });
        mgr.init();

        // Overlay handler yalnız collapsed=false iken toggle çağırır → bilinçli aç
        mgr.toggle();
        const collapsedAfterOpen = sidebar.classList.contains('sidebar--collapsed');
        bus.emit.mockClear();

        mgr.destroy();
        overlay.dispatchEvent(new Event('click', { bubbles: true }));

        expect(sidebar.classList.contains('sidebar--collapsed')).toBe(collapsedAfterOpen);
        expect(bus.emit).not.toHaveBeenCalled();
    });

    it('destroy sonrası sidebar keydown (ArrowDown) → focus hareketi YOK', () => {
        const bus = fakeBus();
        const { sidebar } = buildDom();
        const mgr = new SidebarManager(bus, { userId: 'spec-sidebar' });
        mgr.init();
        bus.emit.mockClear();

        mgr.destroy();
        document.body.focus();
        sidebar.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true, cancelable: true }));

        // #handleKeyboard ArrowDown'da ilk odağa (.sidebar__item veya
        // .sidebar__section-header) focus() yapar
        const target = sidebar.querySelector('.sidebar__item, .sidebar__section-header');
        expect(target).not.toBeNull();
        expect(document.activeElement).not.toBe(target);
        expect(bus.emit).not.toHaveBeenCalled();
    });
});

describe('ResizeHandle.destroy — handle + document dinleyicileri (P1-5)', () => {
    beforeEach(() => {
        document.body.replaceChildren();
        document.body.style.cursor = '';
    });

    afterEach(() => {
        document.body.replaceChildren();
        document.body.style.cursor = '';
    });

    it('destroy ÖNCESİ mousedown drag’i başlatır, SONRA başlatmaz', () => {
        const sidebar = document.createElement('div');
        const handle = document.createElement('div');
        document.body.append(sidebar, handle);

        const handle2 = document.createElement('div');
        sidebar.appendChild(handle2);

        const rh = new ResizeHandle(sidebar, handle2, {});
        rh.init();

        // Pozitif kontrol: init edilmişken mousedown drag'i başlatır
        handle2.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
        expect(document.body.style.cursor).toBe('col-resize');
        expect(sidebar.classList.contains('sidebar--resizing')).toBe(true);

        rh.destroy();
        document.body.style.cursor = '';
        sidebar.classList.remove('sidebar--resizing');

        handle2.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
        expect(document.body.style.cursor).toBe('');
        expect(sidebar.classList.contains('sidebar--resizing')).toBe(false);
    });

    it('destroy yarım kalmış drag dinleyicilerini (document mousemove/mouseup) da kaldırır', () => {
        const sidebar = document.createElement('div');
        const handle = document.createElement('div');
        sidebar.appendChild(handle);
        document.body.append(sidebar);

        const rh = new ResizeHandle(sidebar, handle, {});
        rh.init();

        // Drag başlat (document'e mousemove/mouseup eklenir) — ama destroy ÖNCESİ
        handle.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
        rh.destroy();

        document.body.style.cursor = '';
        sidebar.classList.remove('sidebar--resizing');

        // destroy sonrası document mousemove → #onMouseMove çalışmaz
        document.dispatchEvent(new MouseEvent('mousemove', { bubbles: true, clientX: 300 }));
        expect(sidebar.classList.contains('sidebar--resizing')).toBe(false);
        expect(sidebar.style.width).toBe('');
    });
});
