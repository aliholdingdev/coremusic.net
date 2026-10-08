/**
 * CoreMusic — DeviceManager
 * Cihaz tespiti ve CSS yükleme. device-loader.js'i bridge'ler.
 *
 * @module managers/DeviceManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class DeviceManager {
    #eventBus;
    #currentDevice = 'desktop';

    /** Breakpoints — a-breakpoint-tokens.css ile senkronize */
    /* WP2/J2: static BREAKPOINTS kaldırıldı — tek SSOT `js/core/breakpoints.js`
       (window.CoreMusic.BREAKPOINTS). Kopya sabit yasak. */

    /** CSS dosya haritası — devices.config.js'den yüklenir (SSOT) */
    static get HOME_CSS() {
        return (window.CoreMusic && window.CoreMusic.DEVICES ? window.CoreMusic.DEVICES.HOME_CSS : null) || {};
    }

    static get AUTH_CSS() {
        return (window.CoreMusic && window.CoreMusic.DEVICES ? window.CoreMusic.DEVICES.AUTH_CSS : null) || {};
    }

    static get VIEW_CSS() {
        return (window.CoreMusic && window.CoreMusic.DEVICES ? window.CoreMusic.DEVICES.VIEW_CSS : null) || {};
    }

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    get device() { return this.#currentDevice; }

    /** Modülü başlat */
    init() {
        if (window.CoreMusic?.DeviceLoader) {
            this.#currentDevice = window.CoreMusic.DeviceLoader.getDevice?.() || this.#detect();
        } else {
            this.#currentDevice = this.#detect();
        }

        this.#applyDevice(this.#currentDevice);
        this.#bindResize();
    }

    /** Viewport boyutundan cihaz tespit et — WP2/J2: SSOT delegasyonu
        (DeviceLoader.getDevice() yoksa çalışır; UA-aware tek gövde
        BreakpointAPI.detectDevice). */
    #detect() {
        const w = window.innerWidth || document.documentElement.clientWidth;
        const h = window.innerHeight || document.documentElement.clientHeight;
        return window.CoreMusic.BreakpointAPI.detectDevice(w, h);
    }

    /** Cihaz CSS'ini uygula */
    #applyDevice(device) {
        const baseUrl = (window.CoreMusic?.RouterConfig?.assetsUrl || 'https://assets.coremusic.net') + '/Css/';
        const isAuth = document.body?.dataset?.page === 'auth';

        const cssMap = isAuth ? DeviceManager.AUTH_CSS : DeviceManager.HOME_CSS;
        const cssPath = cssMap[device] || cssMap.desktop;

        this.#loadCSS(baseUrl + cssPath, 'cm-device-css');

        if (document.body) {
            document.body.setAttribute('data-device', device);
        }

        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.deviceType = device;
    }

    /** Tek CSS dosyası yükle/değiştir */
    #loadCSS(href, id) {
        const existing = document.getElementById(id);
        if (existing) {
            if (existing.getAttribute('href') === href) return;
            existing.remove();
        }
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.id = id;
        document.head.appendChild(link);
    }

    /** Resize observer — 200ms debounce */
    #bindResize() {
        let timer = null;
        window.addEventListener('resize', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const newDevice = this.#detect();
                if (newDevice !== this.#currentDevice) {
                    const old = this.#currentDevice;
                    this.#currentDevice = newDevice;
                    this.#applyDevice(newDevice);
                    this.#eventBus.emit('devicechange', { device: newDevice, previous: old });
                }
            }, 200);
        });
    }

    destroy() {
        /* Resize listener DOM unload'da otomatik temizlenir */
    }
}
