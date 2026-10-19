/**
 * CoreMusic — WidgetManager
 * Home right-panel widgets: clock, weather, speakers, folders.
 *
 * @module features/WidgetManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class WidgetManager {
    #eventBus;
    /** @type {Map<string, object>} */
    #widgets = new Map();
    /** @type {number[]} */
    #intervals = [];

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    init() {
        this.#initClock();
        this.#initWeather();
        this.#initFolders();
    }

    /** Saat widget'ı — her saniye güncelle */
    #initClock() {
        const widgets = document.querySelectorAll('.home-widget');
        let clockWidget = null;

        widgets.forEach((w) => {
            const title = w.querySelector('.home-widget__title');
            if (title && /^\d{2}:\d{2}$/.test(title.textContent.trim())) {
                clockWidget = w;
            }
        });

        if (!clockWidget) return;

        const title = clockWidget.querySelector('.home-widget__title');
        const subtitle = clockWidget.querySelector('.home-widget__subtitle');

        const updateClock = () => {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            if (title) title.textContent = `${hours}:${minutes}`;

            if (subtitle) {
                const days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
                const months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                    'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                subtitle.textContent = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            }
        };

        updateClock();
        const id = window.setInterval(updateClock, 1000);
        this.#intervals.push(id);
        this.#widgets.set('clock', { el: clockWidget, update: updateClock });
    }

    /** Hava durumu widget'ı — placeholder */
    #initWeather() {
        const widgets = document.querySelectorAll('.home-widget');
        widgets.forEach((w) => {
            const title = w.querySelector('.home-widget__title');
            if (title && title.textContent.includes('Hava Durumu')) {
                const info = w.querySelector('.home-widget__info');
                if (info && info.textContent.trim() === '--°C') {
                    info.textContent = '28°C';
                }
                this.#widgets.set('weather', { el: w });
            }
        });
    }

    /** Klasörler widget'ı — buton tıklama */
    #initFolders() {
        const folderBtns = document.querySelectorAll('.home-widget__folder-btn');
        folderBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const label = btn.getAttribute('aria-label') || 'folder';
                this.#eventBus.emit('widget:folders:click', { action: label });
            });
        });
    }

    destroy() {
        this.#intervals.forEach((id) => clearInterval(id));
        this.#intervals = [];
        this.#widgets.clear();
    }
}
