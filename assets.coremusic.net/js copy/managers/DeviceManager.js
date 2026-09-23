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
    static BREAKPOINTS = {
        PHONE_MAX: 767,
        TABLET_MIN: 768,
        TABLET_MAX: 1024,
        EMBEDDED_MAX: 1024,
        LAPTOP_MAX: 1440,
        DESKTOP_MAX: 2560,
        FOUR_K_TV_MAX: 3840,
    };

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

    /** Viewport boyutundan cihaz tespit et */
    #detect() {
        const w = window.innerWidth || document.documentElement.clientWidth;
        const h = window.innerHeight || document.documentElement.clientHeight;
        const BP = DeviceManager.BREAKPOINTS;

        if (w <= BP.PHONE_MAX) return 'phone';
        if (w >= BP.TABLET_MIN && w <= BP.TABLET_MAX) {
            return h <= 600 ? 'embedded' : 'tablet';
        }
        if (w <= BP.LAPTOP_MAX) return 'laptop';
        if (w <= BP.DESKTOP_MAX) return 'desktop';
        if (w <= BP.FOUR_K_TV_MAX) return '4k-tv';
        return '4k-monitor';
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
