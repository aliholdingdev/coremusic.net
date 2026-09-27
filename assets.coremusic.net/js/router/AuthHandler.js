import { EVENTS } from './config/events.js';

export default class AuthHandler {
    #router;
    #listeners = [];

    constructor(router) { this.#router = router; }

    init() {
        const submitHandler = (e) => this.#handleSubmit(e);
        document.addEventListener(EVENTS.SUBMIT, submitHandler);
        this.#listeners.push({ target: document, event: EVENTS.SUBMIT, handler: submitHandler });
    }

    destroy() {
        for (const { target, event, handler } of this.#listeners) target.removeEventListener(event, handler);
        this.#listeners = [];
    }

    #handleSubmit(e) {
        const form = e.target;
        if (form.id === 'gender-form') { e.preventDefault(); this.#handleGenderForm(form); return; }
        if (form.id === 'lgn-form') { e.preventDefault(); this.#handleLoginForm(form); return; }
        if (form.id === 'reg-form') { e.preventDefault(); this.#handleRegisterForm(form); return; }
    }

    async #handleLoginForm(form) {
        const btn = form.querySelector('button[type=submit]');
        const errEl = form.querySelector('.lgn-error') || document.getElementById('lgn-err');
        if (btn) { btn.disabled = true; btn.textContent = 'Giriş yapılıyor...'; }
        if (errEl) errEl.style.display = 'none';
        try {
            const r = await fetch('/login', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': document.querySelector('[name=csrf_token]').value }, body: JSON.stringify({ email: form.querySelector('[name=email]').value, password: form.querySelector('[name=password]').value }), credentials: 'include' });
            const d = await r.json();
            if (d.redirect) { window.location.href = d.redirect; return; }
            if (errEl) { errEl.textContent = d.error?.message || 'Giriş başarısız.'; errEl.style.display = 'block'; }
        } catch { if (errEl) { errEl.textContent = 'Bağlantı hatası.'; errEl.style.display = 'block'; } }
        if (btn) { btn.disabled = false; btn.textContent = 'Giriş Yap'; }
    }

    async #handleRegisterForm(form) {
        const btn = form.querySelector('button[type=submit]');
        const errEl = form.querySelector('.lgn-error') || document.getElementById('reg-err');
        if (btn) { btn.disabled = true; btn.textContent = 'Kayıt olunuyor...'; }
        if (errEl) errEl.style.display = 'none';
        try {
            const r = await fetch('/register', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': document.querySelector('[name=csrf_token]').value }, body: JSON.stringify({ username: form.querySelector('[name=username]').value, email: form.querySelector('[name=email]').value, password: form.querySelector('[name=password]').value, agree_terms: true }), credentials: 'include' });
            const d = await r.json();
            if (d.redirect) { window.location.href = d.redirect; return; }
            if (errEl) { errEl.textContent = d.error?.message || 'Kayıt başarısız.'; errEl.style.display = 'block'; }
        } catch { if (errEl) { errEl.textContent = 'Bağlantı hatası.'; errEl.style.display = 'block'; } }
        if (btn) { btn.disabled = false; btn.textContent = 'Kayıt Ol'; }
    }

    async #handleGenderForm(form) {
        const gender = form.querySelector('#gender-input')?.value;
        if (!gender || gender === 'neutral') return;
        const btn = form.querySelector('#continue-btn');
        if (btn) { btn.disabled = true; btn.textContent = 'Devam ediliyor...'; }
        try {
            const r = await fetch('/set-gender', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': document.querySelector('[name=csrf_token]').value }, body: JSON.stringify({ gender }), credentials: 'include' });
            const d = await r.json();
            window.location.href = d.redirect || '/login';
        } catch { window.location.href = '/login'; }
    }
}
