import { CSRF_SELECTOR } from './config/css-selectors.js';
import { HEADERS } from './config/headers.js';

export default class CsrfSyncManager {
    getToken() { const el = document.querySelector(CSRF_SELECTOR); return el?.value || null; }
    update(token) { if (!token) return; const els = document.querySelectorAll(CSRF_SELECTOR); for (const el of els) el.value = token; }
    getHeader() { const token = this.getToken(); return token ? { [HEADERS.X_CSRF_TOKEN]: token } : {}; }
}
