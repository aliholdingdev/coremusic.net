/**
 * CoreMusic — Gender Select Page Handler
 * Handles gender button selection and form submission
 * Works with auth-gender-bg.js for animated background transitions
 *
 * Kullanım:
 *   <script src="/Js/auth/gender-select.js" defer></script>
 */
(function () {
    'use strict';

    function init() {
        const btns = document.querySelectorAll('.lgn-gender-btn');
        const input = document.getElementById('gender-input');
        const submitBtn = document.getElementById('continue-btn');
        const errEl = document.getElementById('lgn-err');
        const form = document.getElementById('gender-form');
        if (!form || !input || !submitBtn) return;

        const redirectUri = (new URLSearchParams(window.location.search)).get('redirect_uri') || '';

        // Gender button selection
        btns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                btns.forEach(function (b) { b.classList.remove('selected'); });
                btn.classList.add('selected');
                input.value = btn.dataset.gender;
                submitBtn.disabled = false;
                try { localStorage.setItem('cm_gender', btn.dataset.gender); } catch (e) {}
            });
        });

        // Form submission
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const gender = input.value;
            if (!gender) return;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Kaydediliyor...';

            const params = {};
            if (redirectUri) params['redirect_uri'] = redirectUri;
            const qs = Object.keys(params).length ? '?' + new URLSearchParams(params).toString() : '';
            const postUrl = '/set-gender' + qs;

            fetch(postUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('[name=csrf_token]').value
                },
                body: JSON.stringify({ gender: gender }),
                credentials: 'include'
            })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.redirect) {
                    try { localStorage.setItem('cm_gender', gender); } catch (e) {}
                    window.location.href = d.redirect;
                } else {
                    const loginParams = {};
                    if (redirectUri) loginParams['redirect_uri'] = redirectUri;
                    const loginQs = Object.keys(loginParams).length ? '?' + new URLSearchParams(loginParams).toString() : '';
                    window.location.href = '/login' + loginQs;
                }
            })
            .catch(function () {
                errEl.textContent = 'Bağlantı hatası.';
                errEl.classList.add('lgn-error--on');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Devam Et';
            });
        });
    }

    if (typeof document !== 'undefined') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }
})();
