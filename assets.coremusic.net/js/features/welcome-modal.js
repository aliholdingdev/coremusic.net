/**
 * welcome-modal.js — Welcome Modal (PNG S02, embedded-only)
 * JS Layer: features
 * Sözleşme: #welcomeModalOverlay + .welcome-modal__btn/__input
 * Açılış: YALNIZCA embedded (RPi5 1024) cihazda; localStorage 'cm_welcome_dismissed'
 *         yoksa açılır. devicechange ile cihaz embedded'a geçtiğinde de açılır.
 * Kapanış: Başla butonu / Escape (PNG'de × close butonu YOK)
 * A11y: focus trap, Escape, aria-modal dialog
 * Version: 1.2.0 — 2026-09-09 (günlük sıfırlama: localStorage + tarih kontrolü)
 */
(function () {
    'use strict';

    const DISMISS_KEY = 'cm_welcome_dismissed';
    const DATE_KEY = 'cm_welcome_dismiss_date';

    function isEmbeddedDevice() {
        const bodyDevice = document.body ? document.body.dataset.device : null;
        const coreDevice = window.CoreMusic && window.CoreMusic.deviceType;
        return bodyDevice === 'embedded' || coreDevice === 'embedded';
    }

    function getTodayStr() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function isDismissedToday() {
        try {
            const savedDate = localStorage.getItem(DATE_KEY);
            return savedDate === getTodayStr();
        } catch (err) {
            return false;
        }
    }

    function getFocusable(modal) {
        return Array.from(
            modal.querySelectorAll('button, input, [href], [tabindex]:not([tabindex="-1"])')
        ).filter((el) => !el.disabled && el.offsetParent !== null);
    }

    function init() {
        const overlay = document.getElementById('welcomeModalOverlay');
        if (!overlay) return;

        const modal = overlay.querySelector('.welcome-modal');
        const startBtn = overlay.querySelector('.welcome-modal__btn');
        const nameInput = overlay.querySelector('.welcome-modal__input');
        let lastFocused = null;
        let opened = false;

        function dismiss() {
            try {
                localStorage.setItem(DISMISS_KEY, '1');
                localStorage.setItem(DATE_KEY, getTodayStr());
            } catch (err) {
                /* localStorage kapalıysa yalnızca gizle */
            }
            overlay.classList.add('is-hidden');
            document.removeEventListener('keydown', onKeydown);
            window.removeEventListener('devicechange', onDeviceChange);
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }

        function onKeydown(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                dismiss();
                return;
            }
            if (e.key !== 'Tab' || !modal) return;
            const focusable = getFocusable(modal);
            if (focusable.length === 0) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }

        function open() {
            if (opened) return;
            if (isDismissedToday()) return;
            opened = true;
            lastFocused = document.activeElement;
            overlay.classList.remove('is-hidden');
            document.addEventListener('keydown', onKeydown);
            const target = (nameInput && nameInput.value === '') ? nameInput : startBtn;
            if (target) target.focus();
        }

        function onDeviceChange(e) {
            const device = (e && e.detail && e.detail.device) || null;
            if (device === 'embedded' || (device === null && isEmbeddedDevice())) {
                open();
            }
        }

        if (startBtn) {
            startBtn.addEventListener('click', () => {
                const name = nameInput ? nameInput.value.trim() : '';
                if (name !== '') {
                    document.cookie = 'MM_Username=' + encodeURIComponent(name) +
                        '; path=/; max-age=31536000; SameSite=Lax';
                }
                dismiss();
            });
        }

        if (nameInput) {
            nameInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (startBtn) startBtn.click();
                }
            });
        }

        /* İlk yükleme: cihaz embedded ise aç (device-layout-updater embedded dışında
           inline display:none uygular; buradaki açılış yalnızca embedded'da etkili) */
        if (isEmbeddedDevice()) {
            open();
        }

        /* Cihaz sonradan embedded'a geçerse (ilk yükleme cookie'siz wide geldiğinde) aç */
        window.addEventListener('devicechange', onDeviceChange);

        /* RACE FIX: device-loader'ın devicechange'i bu script yüklenmeden tetiklenebilir
           (missed event). window.load + kısa gecikmeli yeniden kontrol — body[data-device]
           device-loader tarafından geç dolduruluyorsa da modal açılır. */
        window.addEventListener('load', () => {
            if (!opened && isEmbeddedDevice()) open();
        });
        setTimeout(() => {
            if (!opened && isEmbeddedDevice()) open();
        }, 200);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
