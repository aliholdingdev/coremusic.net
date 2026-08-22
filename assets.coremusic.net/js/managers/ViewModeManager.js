/**
 * CoreMusic — ViewModeManager
 * Görünüm modu yönetimi (ADR-045). home/pro/studio/car.
 *
 * @module managers/ViewModeManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class ViewModeManager {
    #eventBus;
    /** @type {'home'|'pro'|'studio'|'car'} */
    #currentMode = 'home';

    /** View mode CSS haritası */
    static VIEW_CSS = {
        home: '09_ViewModes/v-home.css',
        pro: '09_ViewModes/v-pro.css',
        studio: '09_ViewModes/v-studio.css',
        car: '09_ViewModes/v-car.css',
    };

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    get mode() { return this.#currentMode; }

    init() {
        this.#currentMode = this.#detectMode();
        this.#applyMode(this.#currentMode);
    }

    /**
     * Görünüm modunu değiştir
     * @param {'home'|'pro'|'studio'|'car'} mode
     */
    setMode(mode) {
        if (!ViewModeManager.VIEW_CSS[mode]) return;
        if (mode === this.#currentMode) return;

        const previous = this.#currentMode;
        this.#currentMode = mode;
        this.#applyMode(mode);
        this.#eventBus.emit('viewmodechange', { viewMode: mode, previous });
    }

    /** Mevcut view mode'u tespit et */
    #detectMode() {
        const bodyMode = document.body?.dataset?.viewMode;
        if (bodyMode && ViewModeManager.VIEW_CSS[bodyMode]) return bodyMode;

        const path = window.location.pathname;
        if (path.startsWith('/studio')) return 'studio';
        if (path.startsWith('/pro')) return 'pro';
        if (path.startsWith('/car')) return 'car';
        return 'home';
    }

    /** View mode CSS'ini uygula */
    #applyMode(mode) {
        const baseUrl = '/assets.coremusic.net/Css/';
        const cssPath = ViewModeManager.VIEW_CSS[mode];

        const existing = document.getElementById('cm-view-css');
        if (existing) existing.remove();

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = baseUrl + cssPath;
        link.id = 'cm-view-css';
        document.head.appendChild(link);

        if (document.body) {
            document.body.setAttribute('data-view-mode', mode);
        }
    }

    destroy() {
        const el = document.getElementById('cm-view-css');
        if (el) el.remove();
    }
}
