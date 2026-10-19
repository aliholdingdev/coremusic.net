/**
 * CoreMusic Admin — admin-shell.js (Görev D)
 *
 * Görev: admin-login + admin-register formlarının fetch gönderimi.
 * Desen: auth.coremusic.net/pages/login.php (Faz 3a API sözleşmesi).
 *
 * Neden fetch: admin CSP `form-action 'self'` çapraz-origin native POST'u
 * engeller; kernel'de POST handler yoktur. Başarı → API `data.redirect`
 * (admin /auth/callback?auth_key=...) → AdminAuthBridge.
 *
 * Kurallar: ES6 const/let · eval ve DOM HTML-yazımı yasak (TrustedTypes)
 * · satır-içi script YOK (CSP strict-dynamic) · CSS yazımı YOK · inline
 * style YOK (Guardrail #3). SPA yeniden enjeksiyonuna dayanıklı: bağlama
 * düğüm bazında (form.dataset) yapılır, sayfa geçişinde taze düğümler
 * yeniden bağlanır.
 */

'use strict';

/**
 * HTTP durumuna göre Türkçe mesaj — auth login.php ile aynı sözleşme.
 *
 * @param {number} status HTTP durum kodu
 * @param {{error?:{code?:string,message?:string,fields?:Object<string,string>}}|null} body
 * @returns {{msg:string, first:string|null}}
 */
function admMessageFor(status, body) {
    const err = (body && body.error) || {};
    const fields = err.fields || null;
    if (status === 422) {
        if (fields) {
            const keys = Object.keys(fields);
            if (keys.length > 0) {
                return { msg: keys.map((k) => fields[k]).join(' '), first: keys[0] };
            }
        }
        return { msg: 'Alan hatası.', first: null };
    }
    switch (status) {
        case 400: return { msg: 'İstek geçersiz. Lütfen bilgilerinizi kontrol edin.', first: null };
        case 401: return { msg: 'Kullanıcı adı, email veya şifre hatalı.', first: null };
        case 403: return { msg: 'Bu hesap bu işlemi yapamaz.', first: null };
        case 409: return { msg: err.code === 'USERNAME_TAKEN' ? 'Bu kullanıcı adı kullanılıyor.' : 'Bu email kullanılıyor.', first: null };
        case 429: return { msg: 'Çok fazla deneme. Lütfen bekleyin.', first: null };
        case 500: return { msg: 'Sistem hatası.', first: null };
        case 503: return { msg: 'Bağlantı hatası, tekrar deneyin.', first: null };
        default: return { msg: err.message || (body && body.message) || 'İşlem başarısız.', first: null };
    }
}

/** Her iki form için alan eşlemesi (422 fields anahtarı → input id). */
const ADM_FIELD_FOCUS = {
    login: {
        email: 'adm-login-id',
        username: 'adm-login-id',
        password: 'adm-login-pw',
    },
    register: {
        username: 'adm-reg-usr',
        email: 'adm-reg-email',
        password: 'adm-reg-pw',
        agree_terms: 'adm-reg-terms',
        // name/birth_date API'de karşılığı olmayan figma alanlarıdır → odak yok.
    },
};

/**
 * Formu bağlar (düğüm bazında idempotent).
 *
 * @param {HTMLFormElement} form
 * @param {'login'|'register'} kind
 */
function admBindForm(form, kind) {
    if (!(form instanceof HTMLFormElement)) return;
    if (form.dataset.admBound === '1') return;
    form.dataset.admBound = '1';

    const btn = document.getElementById(kind === 'login' ? 'adm-login-btn' : 'adm-register-btn');
    const errEl = document.getElementById(kind === 'login' ? 'adm-login-err' : 'adm-register-err');
    if (!btn || !errEl) return;

    const labelEl = btn.querySelector('.adm-btn__label');
    const idleLabel = labelEl ? labelEl.textContent : '';
    const busyLabel = kind === 'login' ? 'Giriş yapılıyor...' : 'Kayıt yapılıyor...';

    const resetBtn = () => {
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        btn.classList.remove('is-loading');
        if (labelEl) labelEl.textContent = idleLabel;
    };
    const showError = (msg, first) => {
        errEl.textContent = msg;
        errEl.hidden = false;
        if (first && ADM_FIELD_FOCUS[kind][first]) {
            const el = document.getElementById(ADM_FIELD_FOCUS[kind][first]);
            if (el) el.focus();
        }
    };
    const hideError = () => {
        errEl.hidden = true;
        errEl.textContent = '';
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();

        const endpoint = form.dataset.endpoint || '';
        if (!endpoint) {
            showError('Yapılandırma hatası: servis adresi tanımsız.', null);
            return;
        }

        const redirectInput = form.querySelector('input[name="redirect_uri"]');
        const csrfInput = form.querySelector('input[name="csrf_token"]');
        const redirectUri = redirectInput ? redirectInput.value : '';

        const val = (name) => {
            const el = form.querySelector('[name="' + name + '"]');
            return el ? el.value : '';
        };

        let payload;
        if (kind === 'login') {
            payload = {
                email: val('email'),
                password: val('password'),
                redirect_uri: redirectUri,
            };
        } else {
            const terms = document.getElementById('adm-reg-terms');
            payload = {
                username: val('username'),
                email: val('email'),
                password: val('password'),
                gender: val('gender') || 'neutral',
                agree_terms: Boolean(terms && terms.checked),
                redirect_uri: redirectUri,
            };
            // name + birth_date: API karşılığı yok → isteğe gömülmez (Görev D kararı).
        }

        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        btn.classList.add('is-loading');
        if (labelEl) labelEl.textContent = busyLabel;
        hideError();

        let url = endpoint;
        if (redirectUri) url += '?redirect_uri=' + encodeURIComponent(redirectUri);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfInput ? csrfInput.value : '',
            },
            body: JSON.stringify(payload),
            credentials: 'include',
        })
            .then((res) => res.json()
                .catch(() => null)
                .then((body) => ({ status: res.status, body })))
            .then((res) => {
                const body = res.body || {};
                if (res.status >= 200 && res.status < 300) {
                    const data = body.data || body;
                    const target = data.redirect || body.redirect;
                    if (target) {
                        window.location.href = target;
                        return;
                    }
                    showError((body.error && body.error.message) || body.message || 'İşlem başarısız.', null);
                    resetBtn();
                    return;
                }
                console.error('[admin] ' + kind + ': api error', res.status, body);
                const m = admMessageFor(res.status, body);
                showError(m.msg, m.first);
                resetBtn();
                // Not: API yanıtındaki csrf_token admin oturumuna YAZILMAZ
                // (kronikler arası token sızıntısı; admin kernel'de zaten POST
                // handler yoktur).
            })
            .catch((cause) => {
                console.error('[admin] ' + kind + ': request failed', cause);
                showError('Bağlantı hatası. Lütfen tekrar deneyin.', null);
                resetBtn();
            });
    });
}

admBindForm(document.getElementById('adm-login-form'), 'login');
admBindForm(document.getElementById('adm-register-form'), 'register');
