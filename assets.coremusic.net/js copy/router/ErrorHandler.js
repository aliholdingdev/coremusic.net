import { ERROR_TYPES } from './config/error-types.js';

export default class ErrorHandler {
    #logger;

    constructor(logger) {
        this.#logger = logger;
    }

    #escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    errorHtml(code, message) {
        const codeNum = this.#escapeHtml(String(Number(code) || 500));
        return `<div class="page-error-container" data-error-code="${codeNum}"><div class="page-error-inner"><span class="page-error-code">${codeNum}</span><h1 class="page-error-title">Bir Hata Oluştu</h1><p class="page-error-message">${this.#escapeHtml(message)}</p><a href="/" class="page-error-home-link">Ana Sayfaya Dön</a></div></div>`;
    }

    getErrorInfo(errorType) {
        const errors = {
            [ERROR_TYPES.NOT_FOUND]: { code: 404, message: 'Sayfa bulunamadı.' },
            [ERROR_TYPES.FORBIDDEN]: { code: 403, message: 'Erişim reddedildi.' },
            [ERROR_TYPES.RATE_LIMIT]: { code: 429, message: 'Çok fazla istek.' },
            [ERROR_TYPES.RATE_LIMIT_EXCEEDED]: { code: 429, message: 'Çok fazla istek.' },
            [ERROR_TYPES.SERVER_ERROR]: { code: 500, message: 'Bir hata oluştu.' },
            [ERROR_TYPES.OFFLINE]: { code: 0, message: 'İnternet bağlantısı yok.' }
        };
        return errors[errorType] || errors[ERROR_TYPES.SERVER_ERROR];
    }
}
