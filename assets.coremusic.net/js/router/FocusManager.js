import { FOCUSABLE_SELECTOR, MAIN_CONTENT_SELECTOR } from './config/css-selectors.js';

const ANNOUNCER_ID = 'spa-announcer';

export default class FocusManager {
    #containerSelector;

    constructor(containerSelector = MAIN_CONTENT_SELECTOR) {
        this.#containerSelector = containerSelector;
    }

    moveFocus() {
        const c = document.querySelector(this.#containerSelector);
        if (!c) return;
        const f = c.querySelector(FOCUSABLE_SELECTOR);
        if (f) f.focus();
    }

    announceNavigation(title) {
        let a = document.querySelector(`#${ANNOUNCER_ID}`);
        if (!a) {
            a = document.createElement('div');
            a.id = ANNOUNCER_ID;
            a.setAttribute('aria-live', 'polite');
            a.className = 'visually-hidden';
            // CSSOM inline gizleme: .visually-hidden kuralı yalnız 07_Vendors
            // bootstrap-utilities içinde ve her shell sayfasında yüklü değil.
            // Annunciator her koşulda görünmez olmalı (CSP uyumlu: style
            // atribütü değil, CSSOM ataması — ekran okuyucu aria-live ile okur).
            Object.assign(a.style, {
                position: 'absolute',
                width: '1px',
                height: '1px',
                padding: '0',
                margin: '-1px',
                overflow: 'hidden',
                clip: 'rect(0, 0, 0, 0)',
                whiteSpace: 'nowrap',
                border: '0',
            });
            document.body.appendChild(a);
        }
        a.textContent = title || 'Sayfa yüklendi';
    }
}
