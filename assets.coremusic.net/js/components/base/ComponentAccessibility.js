/**
 * ComponentAccessibility — WCAG 2.2 AA yardımcıları (Faz 2 / Batch 1).
 *
 * @module assets.coremusic.net/js/components/base/ComponentAccessibility
 *
 * Karar #6 (master prompt): görsel boyut Figma'da kalır (27×12 vb.); etkileşimli
 * bileşenler ::before ile 48×48px dokunma alanı alır. Bu modül o alanı okur ve
 * doğrular. Kaynak CSS: `--cm-hit-target` token'ı (01_Abstracts/a-primitive-tokens.css).
 *
 * Not: sr-only/live-region CSS'i diskte yok (u-helpers-utility.css boş, import edilmiyor)
 * → bu modül yalnızca saf JS tutar; aria-live gereken durumlar DOM atributu ile çözülür.
 */

'use strict';

/** Erişilebilir odaklanabilir elemanlar (özel eleman + disabled filtresi). */
const FOCUSABLE_SELECTOR = [
    'a[href]',
    'area[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    'iframe',
    'audio[controls]',
    'video[controls]',
    '[contenteditable]:not([contenteditable="false"])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

/**
 * Konteyner içinde odaklanabilir (ve görünür) elemanları döndürür.
 *
 * @param {HTMLElement} container — Kök eleman
 * @param {{visibleOnly?: boolean}} [options] — visibleOnly: display/visibility filtresi
 * @returns {HTMLElement[]} sıralı liste (DOM sırası)
 */
export function getFocusable(container, options = {}) {
    if (!container || typeof container.querySelectorAll !== 'function') return [];

    const nodes = Array.from(container.querySelectorAll(FOCUSABLE_SELECTOR));
    if (!options.visibleOnly) return nodes.filter(isEnabled);

    return nodes.filter((el) => isEnabled(el) && isVisible(el));
}

/**
 * @param {HTMLElement} el
 * @returns {boolean} disabled/destructive değilse true
 */
function isEnabled(el) {
    if (el.hasAttribute('disabled') || el.getAttribute('aria-disabled') === 'true') {
        return false;
    }
    if (el.closest('[disabled]')) return false;
    return true;
}

/**
 * @param {HTMLElement} el
 * @returns {boolean} basit görünürlük kontrolü (offsetParent + styles)
 */
function isVisible(el) {
    if (el.hasAttribute('hidden')) return false;
    const style = el.ownerDocument?.defaultView?.getComputedStyle(el);
    if (!style) return true;
    return style.display !== 'none' && style.visibility !== 'hidden';
}

/**
 * Odak tuzağı kurar — Tab/Shift+Tab döngüsü + isteğe bağlı odak geri alma.
 *
 * @param {HTMLElement} container — Tuzağın kökü (modal, drawer vb.)
 * @param {{signal?: AbortSignal, restore?: boolean, initialFocus?: HTMLElement}} [options]
 * @returns {() => void} Manuel temizleme fonksiyonu (signal yoksa)
 */
export function trapFocus(container, options = {}) {
    const { signal, restore = true, initialFocus = null } = options;
    if (!container || typeof container.addEventListener !== 'function') return () => {};

    const doc = container.ownerDocument || document;
    const previouslyFocused = doc.activeElement instanceof HTMLElement ? doc.activeElement : null;

    /**
     * @param {KeyboardEvent} event
     */
    const onKeyDown = (event) => {
        if (event.key !== 'Tab') return;

        const focusable = getFocusable(container, { visibleOnly: true });
        if (focusable.length === 0) {
            event.preventDefault();
            container.focus?.();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        const active = doc.activeElement;

        if (event.shiftKey && (active === first || active === container)) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && active === last) {
            event.preventDefault();
            first.focus();
        }
    };

    /** @param {Event} _event */
    const onFocusIn = (_event) => {
        if (!container.contains(doc.activeElement)) {
            const focusable = getFocusable(container, { visibleOnly: true });
            (initialFocus || focusable[0] || container).focus?.();
        }
    };

    container.addEventListener('keydown', onKeyDown, true);
    doc.addEventListener('focusin', onFocusIn, true);

    const cleanup = () => {
        container.removeEventListener('keydown', onKeyDown, true);
        doc.removeEventListener('focusin', onFocusIn, true);
        if (restore && previouslyFocused && doc.contains(previouslyFocused)) {
            previouslyFocused.focus();
        }
    };

    if (signal) {
        if (signal.aborted) {
            cleanup();
        } else {
            signal.addEventListener('abort', cleanup, { once: true });
        }
    }

    // İlk odak
    const target = initialFocus || getFocusable(container, { visibleOnly: true })[0] || container;
    target.focus?.();

    return cleanup;
}

/**
 * aria özelliklerini güvenle yazar (değeri olmayanlar kaldırılır).
 *
 * @param {HTMLElement} el — Hedef eleman
 * @param {Object<string, string|boolean|null|undefined>} attrs — { 'aria-expanded': false }
 */
export function setAria(el, attrs = {}) {
    if (!el || typeof el.setAttribute !== 'function') return;

    Object.entries(attrs).forEach(([key, value]) => {
        if (value === null || value === undefined || value === false) {
            el.removeAttribute(key);
            return;
        }
        el.setAttribute(key, value === true ? 'true' : String(value));
    });
}

/**
 * Karar #6 kontrolü: elemanın ::before dokunma alanı 48×48px mi?
 * `--cm-hit-target` token'ı CSS tarafından ::before'a uygulanır; bu fonksiyon hesaplar.
 *
 * @param {HTMLElement} el — Etkileşimli eleman (button, input, toggle vb.)
 * @returns {{compliant: boolean, width: number, height: number, required: number}}
 */
export function getHitTarget(el) {
    const required = 48;
    if (!el || !el.ownerDocument) {
        return { compliant: false, width: 0, height: 0, required };
    }

    const view = el.ownerDocument.defaultView;
    const before = view ? view.getComputedStyle(el, '::before') : null;
    const width = before ? Number.parseFloat(before.width) || 0 : 0;
    const height = before ? Number.parseFloat(before.height) || 0 : 0;
    const tokenRaw = view
        ? getComputedStyle(el).getPropertyValue('--cm-hit-target').trim()
        : '';
    const token = tokenRaw ? Number.parseFloat(tokenRaw) : 0;
    const effW = Math.max(width, token);
    const effH = Math.max(height, token);

    return {
        compliant: effW >= required && effH >= required,
        width: effW,
        height: effH,
        required,
    };
}

/**
 * Karar #6 — dokunma alanı uygun mu? (getHitTarget kısayolu)
 * @param {HTMLElement} el
 * @returns {boolean}
 */
export function isTouchTargetCompliant(el) {
    return getHitTarget(el).compliant;
}

/**
 * Canlı bölge (aria-live) mesajı yazar — CSS'siz, salt DOM.
 * Element yoksa oluşturur (sağlayıcı konteyner), varsa yalnız metni günceller.
 *
 * @param {HTMLElement} container — Mesajın ekleneceği kök
 * @param {string} message — Duyurulacak metin
 * @param {{polite?: boolean, clearMs?: number, signal?: AbortSignal}} [options]
 * @returns {HTMLElement|null} live region elemanı
 */
export function announce(container, message, options = {}) {
    if (!container) return null;

    let live = container.querySelector('[data-cm-live="true"]');
    if (!live) {
        live = container.ownerDocument.createElement('div');
        live.setAttribute('data-cm-live', 'true');
        live.setAttribute('role', 'status');
        live.setAttribute('aria-live', options.polite === false ? 'assertive' : 'polite');
        live.setAttribute('aria-atomic', 'true');
        container.appendChild(live);
    }

    live.textContent = message;

    if (options.clearMs > 0) {
        const timer = setTimeout(() => {
            if (live) live.textContent = '';
        }, options.clearMs);
        options.signal?.addEventListener('abort', () => clearTimeout(timer), { once: true });
    }

    return live;
}
