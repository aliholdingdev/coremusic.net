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
        // WP2/J3: resize bind YOK (device-loader sahipler); köprü kurulur.
        window.addEventListener('devicechange', this.#onWindowDeviceChange);
    }

    /** Viewport boyutundan cihaz tespit et — WP2/J2: SSOT delegasyonu
        (DeviceLoader.getDevice() yoksa çalışır; UA-aware tek gövde
        BreakpointAPI.detectDevice). */
    #detect() {
        const w = window.innerWidth || document.documentElement.clientWidth;
        const h = window.innerHeight || document.documentElement.clientHeight;
        return window.CoreMusic.BreakpointAPI.detectDevice(w, h);
    }

    /** Cihaz CSS'ini uygula — WP2/J3: CSS YAZMAZ (tek yazıcı = device-loader;
        SSR link ilk boyamayi zaten yapar; buradaki eski #loadCSS bustersizdi
        → link kavgasi/churn — silindi). Yalniz durum atributlarini gunceller. */
    #applyDevice(device) {
        if (document.body) {
            document.body.setAttribute('data-device', device);
        }

        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.deviceType = device;
    }

    /* WP2/J3: #loadCSS silindi (tek yazici = device-loader.loadCSS) ve
       #bindResize silindi (tek resize sahibi = device-loader, 300ms debounce;
       buradaki 200ms ikinci yazim kavgasi uretiyordu). */

    /** window 'devicechange' → EventBus köprüsü (device-loader yayınlar;
        dinleyiciler EventBus üzerinden: TouchManager rebind (J5b), gelecek moduller). */
    #onWindowDeviceChange = (e) => {
        this.#eventBus?.emit('devicechange', e && e.detail ? e.detail : {});
    };

    destroy() {
        window.removeEventListener('devicechange', this.#onWindowDeviceChange);
    }
}
