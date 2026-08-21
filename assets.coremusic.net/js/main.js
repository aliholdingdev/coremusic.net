/**
 * CoreMusic — main.js v5.0.0
 *
 * Merkezi uygulama motoru. 11 modul, tek dosya.
 * Vanilla JS ES6+, ITCSS 9-layer, BEM/BEMIT.
 *
 * Moduller:
 *   1. EventBus          — Pub/sub iletisim
 *   2. CoreMusicApp      — Uygulama orkestrasyon
 *   3. DeviceManager     — Cihaz tespit, breakpoint CSS
 *   4. ThemeManager      — Gender-based tema motoru
 *   5. ViewModeManager   — home/pro/studio/car gorunum modu
 *   6. SPARouterAdapter  — Client-side SPA navigasyon
 *   7. PlayerController  — Muzik oynatici state machine
 *   8. WidgetManager     — Ana sayfa widget'lari
 *   9. CardManager       — Kart etkilesim yoneticisi
 *  10. ScrollManager     — Route bazli scroll kaydetme
 *  11. TouchManager      — Embedded cihazlarda dokunma
 *
 * @version 5.0.0
 * @author CoreMusic Team
 */
(function () {
    'use strict';

    if (typeof history.pushState !== 'function') return;

    /* ================================================================
       1. EVENT BUS
       ================================================================ */

    /**
     * Publish/Subscribe iletisim sistemi.
     * Tum moduller arasi loose coupling saglar.
     */
    class EventBus {
        /** @type {Map<string, Set<Function>>} */
        #listeners = new Map();

        /**
         * Event dinle
         * @param {string} event
         * @param {Function} fn
         * @returns {Function} unsubscribe fonksiyonu
         */
        on(event, fn) {
            if (!this.#listeners.has(event)) {
                this.#listeners.set(event, new Set());
            }
            this.#listeners.get(event).add(fn);
            return () => this.off(event, fn);
        }

        /**
         * Event dinlemeyi birak
         * @param {string} event
         * @param {Function} fn
         */
        off(event, fn) {
            const fns = this.#listeners.get(event);
            if (fns) {
                fns.delete(fn);
                if (fns.size === 0) this.#listeners.delete(event);
            }
        }

        /**
         * Event tetikle
         * @param {string} event
         * @param {*} data
         */
        emit(event, data) {
            const fns = this.#listeners.get(event);
            if (!fns) return;
            for (const fn of fns) {
                try {
                    fn(data);
                } catch (err) {
                    console.error(`[EventBus] Listener error on "${event}":`, err);
                }
            }
        }

        /**
         * Eventi bir kez dinle
         * @param {string} event
         * @param {Function} fn
         */
        once(event, fn) {
            const wrapper = (data) => {
                this.off(event, wrapper);
                fn(data);
            };
            this.on(event, wrapper);
        }

        /** Tum dinleyicileri temizle */
        destroy() {
            this.#listeners.clear();
        }
    }

    /* ================================================================
       2. CORE MUSIC APP
       ================================================================ */

    /**
     * Ana uygulama orkestratoru.
     * Modulleri sirayla baslatir, baarliliga gore devam eder.
     */
    class CoreMusicApp {
        /** @type {Map<string, object>} */
        #modules = new Map();
        /** @type {string} */
        #state = 'idle';

        /**
         * Uygulamayi baslat
         * @returns {Promise<void>}
         */
        async init() {
            this.#state = 'booting';

            this.#initModule('eventBus', new EventBus());
            this.#initModule('device', new DeviceManager());
            this.#initModule('theme', new ThemeManager());
            this.#initModule('viewMode', new ViewModeManager());
            this.#initModule('router', new SPARouterAdapter());
            this.#initModule('player', new PlayerController());
            this.#initModule('widgets', new WidgetManager());
            this.#initModule('cards', new CardManager());
            this.#initModule('scroll', new ScrollManager());
            this.#initModule('touch', new TouchManager());

            this.#state = 'running';
            this.getModule('eventBus').emit('app:ready');
        }

        /**
         * Modul kaydet
         * @param {string} name
         * @param {object} instance
         */
        #initModule(name, instance) {
            if (this.#modules.has(name)) {
                console.warn(`[CoreMusicApp] Module "${name}" already registered, skipping.`);
                return;
            }
            this.#modules.set(name, instance);
        }

        /**
         * Modul al
         * @param {string} name
         * @returns {object|undefined}
         */
        getModule(name) {
            return this.#modules.get(name);
        }

        /** Uygulamayi durdur, kaynaklari serbest birak */
        destroy() {
            for (const [name, mod] of this.#modules) {
                if (typeof mod.destroy === 'function') {
                    try {
                        mod.destroy();
                    } catch (err) {
                        console.error(`[CoreMusicApp] Error destroying module "${name}":`, err);
                    }
                }
            }
            this.#modules.clear();
            this.#state = 'idle';
        }

        /** @returns {string} */
        get state() {
            return this.#state;
        }
    }

    /* ================================================================
       3. DEVICE MANAGER
       ================================================================ */

    /**
     * Cihaz tespit ve breakpoint yoneticisi.
     * Mevcut DeviceLoader modulunu bridge'ler veya kendi tespitini yapar.
     *
     * Breakpoint haritasi:
     *   phone      <=767px
     *   tablet     768-1024px (h>600)
     *   embedded   <=1024px (h<=600)
     *   laptop     1025-1440px
     *   desktop    1441-2560px
     *   4k-tv      2561-3840px
     *   4k-monitor >=3841px
     */
    class DeviceManager {
        /** @type {EventBus} */
        #eventBus;
        /** @type {string} */
        #currentDevice = '';
        /** @type {number|null} */
        #resizeTimer = null;

        /** @type {object} */
        static BREAKPOINTS = {
            PHONE_MAX: 767,
            TABLET_MIN: 768,
            TABLET_MAX: 1024,
            EMBEDDED_MAX: 1024,
            LAPTOP_MAX: 1440,
            DESKTOP_MAX: 2560,
            FOUR_K_TV_MAX: 3840,
        };

        /**
         * DeviceManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;

            // Bridge: mevcut DeviceLoader varsa onu kullan
            const dl = window.CoreMusic?.DeviceLoader;
            if (dl) {
                this.#currentDevice = dl.getDevice ? dl.getDevice() : 'desktop';
            } else {
                this.#currentDevice = this.#detect();
            }

            this.#apply();
            this.#startObserver();
        }

        /**
         * Viewport boyutundan cihaz tespit et
         * @returns {string}
         */
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

        /** Body'ye data-device attribute'u uygula */
        #apply() {
            if (document.body) {
                document.body.setAttribute('data-device', this.#currentDevice);
            }
            window.CoreMusic = window.CoreMusic || {};
            window.CoreMusic.deviceType = this.#currentDevice;
        }

        /** Resize observer — 200ms debounce */
        #startObserver() {
            window.addEventListener('resize', () => {
                if (this.#resizeTimer !== null) {
                    clearTimeout(this.#resizeTimer);
                }
                this.#resizeTimer = setTimeout(() => {
                    this.#resizeTimer = null;
                    const newDevice = this.#detect();
                    if (newDevice !== this.#currentDevice) {
                        const oldDevice = this.#currentDevice;
                        this.#currentDevice = newDevice;
                        this.#apply();

                        if (this.#eventBus) {
                            this.#eventBus.emit('devicechange', {
                                device: newDevice,
                                previous: oldDevice,
                            });
                        }
                        window.dispatchEvent(new CustomEvent('cm:devicechange', {
                            detail: { device: newDevice, previous: oldDevice },
                        }));
                    }
                }, 200);
            });
        }

        /**
         * Mevcut cihaz turunu dondur
         * @returns {string}
         */
        getDevice() {
            return this.#currentDevice;
        }

        /** Kaynaklari serbest birak */
        destroy() {
            if (this.#resizeTimer !== null) {
                clearTimeout(this.#resizeTimer);
                this.#resizeTimer = null;
            }
        }
    }

    /* ================================================================
       4. THEME MANAGER
       ================================================================ */

    /**
     * Gender-based tema motoru.
     * Cookie, data attribute veya default'tan tema okur.
     *
     * Tema degerleri:
     *   female  — accent: #ff4fd8
     *   male    — accent: #4f8fff
     *   neutral — accent: #a855f7
     */
    class ThemeManager {
        /** @type {EventBus} */
        #eventBus;
        /** @type {string} */
        #currentTheme = 'neutral';

        /** @type {object} */
        static THEMES = {
            female:  { accent: '#ff4fd8', accentRgb: '255,79,216' },
            male:    { accent: '#4f8fff', accentRgb: '79,143,255' },
            neutral: { accent: '#a855f7', accentRgb: '168,85,247' },
        };

        /**
         * ThemeManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
            this.#currentTheme = this.#readTheme();
            this.#apply();
        }

        /**
         * Temyi oncelik sirasina gore oku:
         * 1. Cookie: cm_gender=...
         * 2. data-gender attribute (html veya body)
         * 3. Default: neutral
         * @returns {string}
         */
        #readTheme() {
            // 1. Cookie
            const cookieMatch = document.cookie.match(/cm_gender=([^;]+)/);
            if (cookieMatch) {
                const val = decodeURIComponent(cookieMatch[1]).toLowerCase();
                if (ThemeManager.THEMES[val]) return val;
            }

            // 2. data-gender attribute
            const htmlGender = document.documentElement.dataset.gender;
            if (htmlGender && ThemeManager.THEMES[htmlGender]) return htmlGender;

            const bodyGender = document.body?.dataset.gender;
            if (bodyGender && ThemeManager.THEMES[bodyGender]) return bodyGender;

            // 3. Default
            return 'neutral';
        }

        /**
         * CSS custom property'leri uygula
         * @param {string} [theme]
         */
        #apply(theme) {
            const t = theme || this.#currentTheme;
            const vars = ThemeManager.THEMES[t];
            if (!vars) return;

            const root = document.documentElement;
            root.style.setProperty('--accent', vars.accent);
            root.style.setProperty('--accent-rgb', vars.accentRgb);
            root.setAttribute('data-gender', t);
        }

        /**
         * Temayi degistir
         * @param {string} gender — female, male veya neutral
         */
        setTheme(gender) {
            if (!ThemeManager.THEMES[gender]) {
                console.warn(`[ThemeManager] Unknown theme: "${gender}"`);
                return;
            }
            const oldTheme = this.#currentTheme;
            this.#currentTheme = gender;
            this.#apply(gender);

            if (this.#eventBus) {
                this.#eventBus.emit('themechange', { theme: gender, previous: oldTheme });
            }
        }

        /**
         * Mevcut temayi dondur
         * @returns {string}
         */
        getTheme() {
            return this.#currentTheme;
        }

        /** Temyi siradaki option'a toggle et */
        toggle() {
            const keys = Object.keys(ThemeManager.THEMES);
            const idx = keys.indexOf(this.#currentTheme);
            const next = keys[(idx + 1) % keys.length];
            this.setTheme(next);
        }

        /** Kaynaklari serbest birak */
        destroy() {
            // Temizlenecek kaynak yok
        }
    }

    /* ================================================================
       5. VIEW MODE MANAGER
       ================================================================ */

    /**
     * Gorunum modu yoneticisi: home, pro, studio, car.
     * Her mod farkli bir layout ve CSS yukler.
     */
    class ViewModeManager {
        /** @type {EventBus} */
        #eventBus;
        /** @type {string} */
        #currentMode = 'home';

        /** @type {string[]} */
        static MODES = ['home', 'pro', 'studio', 'car'];

        /**
         * ViewModeManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;

            // data-view-mode attribute'dan oku
            const bodyMode = document.body?.dataset.viewMode;
            if (bodyMode && ViewModeManager.MODES.includes(bodyMode)) {
                this.#currentMode = bodyMode;
            }

            this.#apply();
        }

        /** Body'ye data-view-mode attribute'u uygula */
        #apply() {
            if (document.body) {
                document.body.setAttribute('data-view-mode', this.#currentMode);
            }
        }

        /**
         * Gorunum modunu degistir
         * @param {string} mode
         */
        setMode(mode) {
            if (!ViewModeManager.MODES.includes(mode)) {
                console.warn(`[ViewModeManager] Unknown mode: "${mode}"`);
                return;
            }
            const oldMode = this.#currentMode;
            this.#currentMode = mode;
            this.#apply();

            if (this.#eventBus) {
                this.#eventBus.emit('viewmodechange', { mode, previous: oldMode });
            }
        }

        /**
         * Mevcut modu dondur
         * @returns {string}
         */
        getMode() {
            return this.#currentMode;
        }

        /** Kaynaklari serbest birak */
        destroy() {
            // Temizlenecek kaynak yok
        }
    }

    /* ================================================================
       6. SPA ROUTER ADAPTER
       ================================================================ */

    /**
     * Client-side SPA navigasyon adaptoru.
     * Mevcut Router.js modulunu kullanir veya basit bir fallback saglar.
     *
     * Kurallar:
     *   - history.pushState zorunlu (hash routing yasak)
     *   - [data-no-spa] linkleri atlar
     *   - DOMParser ile HTML parse eder
     *   - CSRF token sync
     */
    class SPARouterAdapter {
        /** @type {EventBus} */
        #eventBus;
        /** @type {boolean} */
        #initialized = false;

        /**
         * SPARouterAdapter baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
        }

        /** Router baslat */
        init() {
            if (this.#initialized) return;

            // Mevcut Router varsa onu kullan
            const existingRouter = window.CoreMusic?.Router;
            if (existingRouter && typeof existingRouter.init === 'function') {
                this.#initialized = true;
                return;
            }

            // Fallback: basit SPA navigasyon
            document.addEventListener('click', (e) => this.#handleClick(e));
            window.addEventListener('popstate', () => this.#handlePopstate());
            this.#initialized = true;
        }

        /**
         * Link tiklama handler
         * @param {MouseEvent} e
         */
        #handleClick(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;

            // data-no-spa varsa atla
            if (link.hasAttribute('data-no-spa')) return;

            const href = link.getAttribute('href');
            if (!href) return;

            // Dishi linkler atla
            if (href.startsWith('http') && !href.includes(window.location.hostname)) return;
            if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

            e.preventDefault();
            this.navigate(href);
        }

        /**
         * Sayfayi yukle ve icerigi guncelle
         * @param {string} url
         * @returns {Promise<void>}
         */
        async navigate(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Content guncelle
                const mainContent = doc.querySelector('[data-spa-content]') || doc.querySelector('main');
                const currentMain = document.querySelector('[data-spa-content]') || document.querySelector('main');

                if (mainContent && currentMain) {
                    currentMain.textContent = '';
                    while (mainContent.firstChild) {
                        currentMain.appendChild(mainContent.firstChild);
                    }
                }

                // Title guncelle
                const newTitle = doc.querySelector('title');
                if (newTitle) {
                    document.title = newTitle.textContent;
                }

                // History push
                history.pushState({}, '', url);

                if (this.#eventBus) {
                    this.#eventBus.emit('route:change', { url });
                }
            } catch (err) {
                console.error('[SPARouterAdapter] Navigation failed:', err);
                window.location.href = url;
            }
        }

        /** Popstate handler — geri/ileri butonlari */
        #handlePopstate() {
            const url = window.location.pathname + window.location.search;
            this.navigate(url);
        }

        /** Kaynaklari serbest birak */
        destroy() {
            // Event listener'lar sayfa reload ile temizlenir
            this.#initialized = false;
        }
    }

    /* ================================================================
       7. PLAYER CONTROLLER
       ================================================================ */

    /**
     * Muzik oynatici state machine.
     * Footer player ve now-playing kartini kontrol eder.
     *
     * States: STOPPED, PLAYING, PAUSED
     * Actions: play, pause, stop, prev, next, volume, mute, shuffle, repeat, seek
     */
    class PlayerController {
        /** @type {EventBus} */
        #eventBus;

        /** @type {string} */
        static STATES = {
            STOPPED: 'STOPPED',
            PLAYING: 'PLAYING',
            PAUSED: 'PAUSED',
        };

        /** @type {object} */
        #state = {
            status: 'STOPPED',
            song: '',
            album: '',
            artist: '',
            art: '',
            duration: 300,
            currentTime: 0,
            volume: 100,
            shuffle: false,
            repeat: 'off', // off, one, all
        };

        /** @type {number|null} */
        #progressInterval = null;

        /**
         * PlayerController baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
            this.#bindEvents();
        }

        /** DOM event listener'larini bagla */
        #bindEvents() {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;

                const action = btn.getAttribute('data-action');
                if (action) this.#handleAction(action, btn);
            });

            // Seek bar tiklama
            document.addEventListener('click', (e) => {
                const seek = e.target.closest('.now-playing__seek, .footer__progress-bar');
                if (!seek) return;

                const rect = seek.getBoundingClientRect();
                const pct = (e.clientX - rect.left) / rect.width;
                this.#state.currentTime = Math.floor(pct * this.#state.duration);
                this.#updateProgress();
            });
        }

        /**
         * Aksiyon isle
         * @param {string} action
         * @param {HTMLElement} btn
         */
        #handleAction(action, btn) {
            switch (action) {
                case 'play':
                    this.#play();
                    break;
                case 'pause':
                    this.#pause();
                    break;
                case 'stop':
                    this.#stop();
                    break;
                case 'prev':
                    this.#prev();
                    break;
                case 'next':
                    this.#next();
                    break;
                case 'volume':
                    this.#setVolume(btn);
                    break;
                case 'mute':
                    this.#toggleMute();
                    break;
                case 'shuffle':
                    this.#toggleShuffle();
                    break;
                case 'repeat':
                    this.#cycleRepeat();
                    break;
            }
        }

        /** Oynat — icon degis (play -> pause), status=PLAYING */
        #play() {
            this.#state.status = PlayerController.STATES.PLAYING;
            this.#updateIcons();
            this.#startProgress();

            if (this.#eventBus) {
                this.#eventBus.emit('player:play', this.#state);
            }
        }

        /** Duraklat — icon degis (pause -> play), status=PAUSED */
        #pause() {
            this.#state.status = PlayerController.STATES.PAUSED;
            this.#updateIcons();
            this.#stopProgress();

            if (this.#eventBus) {
                this.#eventBus.emit('player:pause', this.#state);
            }
        }

        /** Durdur — icon reset, status=STOPPED */
        #stop() {
            this.#state.status = PlayerController.STATES.STOPPED;
            this.#state.currentTime = 0;
            this.#updateIcons();
            this.#updateProgress();
            this.#stopProgress();

            if (this.#eventBus) {
                this.#eventBus.emit('player:stop', this.#state);
            }
        }

        /** Onceki sarki */
        #prev() {
            this.#state.currentTime = 0;
            this.#updateProgress();
            if (this.#eventBus) {
                this.#eventBus.emit('player:prev', this.#state);
            }
        }

        /** Sonraki sarki */
        #next() {
            this.#state.currentTime = 0;
            this.#updateProgress();
            if (this.#eventBus) {
                this.#eventBus.emit('player:next', this.#state);
            }
        }

        /**
         * Ses seviyesini ayarla
         * @param {HTMLElement} btn
         */
        #setVolume(btn) {
            const slider = btn.closest('.volume-control')?.querySelector('input[type="range"]');
            if (slider) {
                this.#state.volume = parseInt(slider.value, 10);
            }
            this.#updateVolumeDisplay();
        }

        /** Sesi ac/kapa toggle */
        #toggleMute() {
            if (this.#state.volume > 0) {
                this.#state._prevVolume = this.#state.volume;
                this.#state.volume = 0;
            } else {
                this.#state.volume = this.#state._prevVolume || 100;
            }
            this.#updateVolumeDisplay();
        }

        /** Shuffle toggle */
        #toggleShuffle() {
            this.#state.shuffle = !this.#state.shuffle;
            this.#updateShuffleIcon();

            if (this.#eventBus) {
                this.#eventBus.emit('player:shuffle', { shuffle: this.#state.shuffle });
            }
        }

        /** Repeat cycle: off -> one -> all -> off */
        #cycleRepeat() {
            const order = ['off', 'one', 'all'];
            const idx = order.indexOf(this.#state.repeat);
            this.#state.repeat = order[(idx + 1) % order.length];
            this.#updateRepeatIcon();

            if (this.#eventBus) {
                this.#eventBus.emit('player:repeat', { repeat: this.#state.repeat });
            }
        }

        /** Ikonlari duruma gore guncelle */
        #updateIcons() {
            const playBtns = document.querySelectorAll('[data-action="play"], [data-action="pause"]');
            const isPlaying = this.#state.status === PlayerController.STATES.PLAYING;

            playBtns.forEach((btn) => {
                const action = isPlaying ? 'pause' : 'play';
                btn.setAttribute('data-action', action);

                // Text content guncelle (innerHTML yasak)
                const icon = btn.querySelector('i, svg, .icon');
                if (icon) {
                    icon.textContent = isPlaying ? '\u23F8' : '\u25B6';
                }
            });
        }

        /** Progress bar guncelle */
        #updateProgress() {
            const bars = document.querySelectorAll('.footer__progress-bar, .now-playing__seek');
            const pct = this.#state.duration > 0
                ? (this.#state.currentTime / this.#state.duration) * 100
                : 0;

            bars.forEach((bar) => {
                bar.style.width = pct + '%';
            });

            // Time displays
            const timeEls = document.querySelectorAll('.player-time__current, .now-playing__time-current');
            timeEls.forEach((el) => {
                el.textContent = this.#formatTime(this.#state.currentTime);
            });

            const durEls = document.querySelectorAll('.player-time__duration, .now-playing__time-duration');
            durEls.forEach((el) => {
                el.textContent = this.#formatTime(this.#state.duration);
            });
        }

        /** Progress timer baslat */
        #startProgress() {
            this.#stopProgress();
            this.#progressInterval = setInterval(() => {
                if (this.#state.status !== PlayerController.STATES.PLAYING) return;
                if (this.#state.currentTime >= this.#state.duration) {
                    this.#next();
                    return;
                }
                this.#state.currentTime += 1;
                this.#updateProgress();
            }, 1000);
        }

        /** Progress timer durdur */
        #stopProgress() {
            if (this.#progressInterval !== null) {
                clearInterval(this.#progressInterval);
                this.#progressInterval = null;
            }
        }

        /** Ses gosterimini guncelle */
        #updateVolumeDisplay() {
            const volEls = document.querySelectorAll('.volume-display, [data-volume-value]');
            volEls.forEach((el) => {
                el.textContent = this.#state.volume + '%';
            });

            const volBars = document.querySelectorAll('.volume-bar__fill');
            volBars.forEach((bar) => {
                bar.style.width = this.#state.volume + '%';
            });
        }

        /** Shuffle ikonu guncelle */
        #updateShuffleIcon() {
            const btns = document.querySelectorAll('[data-action="shuffle"]');
            btns.forEach((btn) => {
                btn.classList.toggle('is-active', this.#state.shuffle);
            });
        }

        /** Repeat ikonu guncelle */
        #updateRepeatIcon() {
            const btns = document.querySelectorAll('[data-action="repeat"]');
            btns.forEach((btn) => {
                btn.classList.toggle('is-active', this.#state.repeat !== 'off');
                const label = btn.querySelector('.repeat-label');
                if (label) {
                    label.textContent = this.#state.repeat === 'one' ? '1' : '';
                }
            });
        }

        /**
         * Saniyeyi formatla
         * @param {number} secs
         * @returns {string}
         */
        #formatTime(secs) {
            const s = Math.max(0, Math.floor(secs));
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            if (h > 0) {
                return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
            }
            return `${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')}`;
        }

        /**
         * Oyuncu durumunu dondur
         * @returns {object}
         */
        getState() {
            return { ...this.#state };
        }

        /**
         * Sarki bilgilerini yukle
         * @param {object} meta — { song, album, artist, art, duration }
         */
        loadTrack(meta) {
            Object.assign(this.#state, meta);
            this.#state.currentTime = 0;
            this.#updateProgress();

            // Now Playing kartini guncelle
            const titleEls = document.querySelectorAll('.now-playing__title, .footer__song-name');
            titleEls.forEach((el) => { el.textContent = meta.song || ''; });

            const artistEls = document.querySelectorAll('.now-playing__artist, .footer__artist');
            artistEls.forEach((el) => { el.textContent = meta.artist || ''; });

            const albumEls = document.querySelectorAll('.now-playing__album, .footer__album');
            albumEls.forEach((el) => { el.textContent = meta.album || ''; });
        }

        /** Kaynaklari serbest birak */
        destroy() {
            this.#stopProgress();
        }
    }

    /* ================================================================
       8. WIDGET MANAGER
       ================================================================ */

    /**
     * Ana sayfa widget yoneticisi.
     * Clock, Weather, Speakers, Folders widget'larini kontrol eder.
     */
    class WidgetManager {
        /** @type {EventBus} */
        #eventBus;
        /** @type {number|null} */
        #clockInterval = null;

        /**
         * WidgetManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
            this.#initClock();
            this.#initWeather();
            this.#initSpeakers();
            this.#initFolders();
        }

        /** Clock widget — her saniye guncelle */
        #initClock() {
            const updateClock = () => {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');

                const clockEls = document.querySelectorAll('.home-widget__title[data-widget="clock"]');
                clockEls.forEach((el) => {
                    el.textContent = h + ':' + m;
                });

                // Tarih widget'lari
                const dateEls = document.querySelectorAll('.home-widget__title[data-widget="date"]');
                const days = ['Pazar', 'Pazartesi', 'Sali', 'Carsamba', 'Persembe', 'Cuma', 'Cumartesi'];
                const months = ['Ocak', 'Subat', 'Mart', 'Nisan', 'Mayis', 'Haziran',
                    'Temmuz', 'Agustos', 'Eylul', 'Ekim', 'Kasim', 'Aralik'];
                dateEls.forEach((el) => {
                    el.textContent = `${now.getDate()} ${months[now.getMonth()]} ${days[now.getDay()]}`;
                });
            };

            updateClock();
            this.#clockInterval = setInterval(updateClock, 1000);
        }

        /** Weather widget — placeholder */
        #initWeather() {
            // API olmadan placeholder degerler
            const tempEls = document.querySelectorAll('.home-widget__detail[data-widget="weather"]');
            tempEls.forEach((el) => {
                el.textContent = '---';
            });
        }

        /** Speakers widget — placeholder */
        #initSpeakers() {
            // Hoparlor durumu placeholder
        }

        /** Folders widget — buton tiklama */
        #initFolders() {
            document.addEventListener('click', (e) => {
                const folderBtn = e.target.closest('.home-widget__folder-btn, [data-widget="folders"] button');
                if (!folderBtn) return;

                const folder = folderBtn.getAttribute('data-folder');
                if (this.#eventBus) {
                    this.#eventBus.emit('widget:folder-open', { folder });
                }
            });
        }

        /** Kaynaklari serbest birak */
        destroy() {
            if (this.#clockInterval !== null) {
                clearInterval(this.#clockInterval);
                this.#clockInterval = null;
            }
        }
    }

    /* ================================================================
       9. CARD MANAGER
       ================================================================ */

    /**
     * Kart etkilesim yoneticisi.
     * Event delegation ile .card-grid container'da tek click listener.
     */
    class CardManager {
        /** @type {EventBus} */
        #eventBus;

        /**
         * CardManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
            this.#bindEvents();
        }

        /** Event delegation — tek click listener */
        #bindEvents() {
            document.addEventListener('click', (e) => {
                const card = e.target.closest('.media-card, .card-grid__item');
                if (!card) return;

                const data = {
                    id: card.dataset.id || null,
                    type: card.dataset.type || null,
                    title: card.querySelector('.media-card__title, .card__title')?.textContent || '',
                    url: card.querySelector('a')?.href || card.dataset.url || null,
                };

                if (this.#eventBus) {
                    this.#eventBus.emit('card:click', data);
                }
            });

            // Hover: cursor pointer
            document.addEventListener('mouseover', (e) => {
                const card = e.target.closest('.media-card, .card-grid__item');
                if (card) {
                    card.style.cursor = 'pointer';
                }
            });
        }

        /** Kaynaklari serbest birak */
        destroy() {
            // Event delegation, sayfa reload ile temizlenir
        }
    }

    /* ================================================================
       10. SCROLL MANAGER
       ================================================================ */

    /**
     * Route bazli scroll pozisyon kaydetme.
     * Her route icin scrollY'yi hafizada tutar.
     */
    class ScrollManager {
        /** @type {EventBus} */
        #eventBus;
        /** @type {Map<string, number>} */
        #positions = new Map();

        /**
         * ScrollManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
            this.#bindEvents();
        }

        /** Scroll ve popstate event'lerini dinle */
        #bindEvents() {
            // Scroll pozisyonunu kaydet (debounced)
            let scrollTimer = null;
            window.addEventListener('scroll', () => {
                if (scrollTimer !== null) return;
                scrollTimer = setTimeout(() => {
                    scrollTimer = null;
                    const url = window.location.pathname;
                    this.#positions.set(url, window.scrollY);
                }, 100);
            }, { passive: true });

            // Route degisikliklerinde scroll restore
            if (this.#eventBus) {
                this.#eventBus.on('route:change', (data) => {
                    this.#restoreScroll(data.url);
                });
            }

            // Popstate'te restore
            window.addEventListener('popstate', () => {
                const url = window.location.pathname;
                this.#restoreScroll(url);
            });
        }

        /**
         * Scroll pozisyonunu restore et
         * @param {string} url
         */
        #restoreScroll(url) {
            const saved = this.#positions.get(url) || 0;
            requestAnimationFrame(() => {
                window.scrollTo(0, saved);
            });
        }

        /**
         * Belirli bir URL icin kayitli scroll pozisyonunu dondur
         * @param {string} url
         * @returns {number}
         */
        getPosition(url) {
            return this.#positions.get(url) || 0;
        }

        /** Kaynaklari serbest birak */
        destroy() {
            this.#positions.clear();
        }
    }

    /* ================================================================
       11. TOUCH MANAGER
       ================================================================ */

    /**
     * Embedded cihazlarda dokunma etkilesimleri.
     * Swipe: yatay kaydirma -> card-grid scroll.
     * Long press: 500ms -> context menu placeholder.
     *
     * Sadece embedded device'da aktif.
     */
    class TouchManager {
        /** @type {EventBus} */
        #eventBus;
        /** @type {boolean} */
        #active = false;
        /** @type {number|null} */
        #longPressTimer = null;
        /** @type {{ x: number, y: number }|null} */
        #touchStart = null;

        /**
         * TouchManager baslat
         * @param {object} [opts]
         * @param {EventBus} [opts.eventBus]
         */
        constructor(opts = {}) {
            this.#eventBus = opts.eventBus;
            this.#active = this.#isEmbedded();

            if (this.#active) {
                this.#bindEvents();
            }
        }

        /**
         * Embedded cihaz kontrolu
         * @returns {boolean}
         */
        #isEmbedded() {
            const device = window.CoreMusic?.deviceType || '';
            return device === 'embedded';
        }

        /** Touch event'lerini bagla */
        #bindEvents() {
            document.addEventListener('touchstart', (e) => this.#onTouchStart(e), { passive: true });
            document.addEventListener('touchmove', (e) => this.#onTouchMove(e), { passive: true });
            document.addEventListener('touchend', (e) => this.#onTouchEnd(e), { passive: true });
        }

        /**
         * Touch baslangic
         * @param {TouchEvent} e
         */
        #onTouchStart(e) {
            const touch = e.touches[0];
            this.#touchStart = { x: touch.clientX, y: touch.clientY };

            // Long press baslat
            this.#longPressTimer = setTimeout(() => {
                this.#onLongPress(e);
            }, 500);
        }

        /**
         * Touch hareket — long press'i iptal et
         * @param {TouchEvent} e
         */
        #onTouchMove(e) {
            if (this.#longPressTimer !== null) {
                clearTimeout(this.#longPressTimer);
                this.#longPressTimer = null;
            }
        }

        /**
         * Touch bitis — swipe kontrolu
         * @param {TouchEvent} e
         */
        #onTouchEnd(e) {
            if (this.#longPressTimer !== null) {
                clearTimeout(this.#longPressTimer);
                this.#longPressTimer = null;
            }

            if (!this.#touchStart) return;

            const touch = e.changedTouches[0];
            const dx = touch.clientX - this.#touchStart.x;
            const dy = touch.clientY - this.#touchStart.y;
            this.#touchStart = null;

            // Yatay swipe kontrolu (dikey hareketten buyuk olmali)
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
                this.#onSwipe(dx > 0 ? 'right' : 'left');
            }
        }

        /**
         * Long press handler — context menu placeholder
         * @param {TouchEvent} e
         */
        #onLongPress(e) {
            const card = e.target.closest('.media-card, .card-grid__item');
            if (!card) return;

            if (this.#eventBus) {
                this.#eventBus.emit('touch:longpress', {
                    element: card,
                    id: card.dataset.id || null,
                });
            }
        }

        /**
         * Swipe handler — card-grid'de yatay scroll
         * @param {string} direction — 'left' veya 'right'
         */
        #onSwipe(direction) {
            const grids = document.querySelectorAll('.card-grid, .card-scroll');
            grids.forEach((grid) => {
                const scrollAmount = 200;
                grid.scrollBy({
                    left: direction === 'left' ? scrollAmount : -scrollAmount,
                    behavior: 'smooth',
                });
            });

            if (this.#eventBus) {
                this.#eventBus.emit('touch:swipe', { direction });
            }
        }

        /** Kaynaklari serbest birak */
        destroy() {
            if (this.#longPressTimer !== null) {
                clearTimeout(this.#longPressTimer);
                this.#longPressTimer = null;
            }
            this.#touchStart = null;
        }
    }

    /* ================================================================
       BOOT SEQUENCING
       ================================================================ */

    const app = new CoreMusicApp();

    document.addEventListener('DOMContentLoaded', () => {
        app.init();
    });

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.App = app;
    window.CoreMusic.version = '5.0.0';

})();
