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
        var btns = document.querySelectorAll('.lgn-gender-btn');
        var input = document.getElementById('gender-input');
        var submitBtn = document.getElementById('continue-btn');
        var errEl = document.getElementById('lgn-err');
        var form = document.getElementById('gender-form');
        if (!form || !input || !submitBtn) return;

        var redirectUri = (new URLSearchParams(window.location.search)).get('redirect_uri') || '';

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
            var gender = input.value;
            if (!gender) return;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Kaydediliyor...';

            var params = {};
            if (redirectUri) params['redirect_uri'] = redirectUri;
            var qs = Object.keys(params).length ? '?' + new URLSearchParams(params).toString() : '';
            var postUrl = '/set-gender' + qs;

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
                    var loginParams = {};
                    if (redirectUri) loginParams['redirect_uri'] = redirectUri;
                    var loginQs = Object.keys(loginParams).length ? '?' + new URLSearchParams(loginParams).toString() : '';
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
