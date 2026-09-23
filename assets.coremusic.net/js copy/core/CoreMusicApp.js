/**
 * CoreMusic — CoreMusicApp
 * Uygulama lifecycle yönetimi. Modülleri sırayla init eder.
 *
 * @module core/CoreMusicApp
 * @version 5.0.0
 */
export default class CoreMusicApp {
    /** @type {Map<string, object>} */
    #modules = new Map();
    /** @type {'idle'|'booting'|'running'|'destroyed'} */
    #state = 'idle';
    /** @type {object} */
    #config;

    /**
     * @param {object} [config]
     * @param {import('./EventBus.js').default} [config.eventBus] — Mevcut EventBus instance
     * @param {object} [config.modules] — Modül sınıfları { name: Class } (eski API, init() ile)
     */
    constructor(config = {}) {
        this.#config = config;
    }

    get state() { return this.#state; }

    /**
     * Modül al
     * @param {string} name
     * @returns {object|undefined}
     */
    getModule(name) {
        return this.#modules.get(name);
    }

    /**
     * Modül register et (main.js API)
     * @param {string} name
     * @param {object} instance
     */
    registerModule(name, instance) {
        this.#modules.set(name, instance);
        this.#log(`Module "${name}" registered`);
    }

    /** Durumu running olarak ayarla */
    setRunning() {
        this.#state = 'running';
        this.#log('App state → running');
    }

    /**
     * Eski API: Config'deki modülleri başlat.
     * main.js kullanıyorsa registerModule + setRunning tercih edilir.
     */
    async init() {
        if (this.#state === 'running') return;
        this.#state = 'booting';

        const modClasses = this.#config.modules || {};
        const eventBus = this.#config.eventBus || (modClasses.EventBus ? new modClasses.EventBus() : null);

        if (!eventBus) {
            console.error('[CoreMusicApp] EventBus is critical — halting boot');
            return;
        }

        this.#modules.set('eventBus', eventBus);
        this.#log('EventBus initialized');

        const bootOrder = [
            ['device', modClasses.DeviceManager, true],
            ['theme', modClasses.ThemeManager, false],
            ['viewMode', modClasses.ViewModeManager, false],
            ['router', modClasses.SPARouterAdapter, false],
            ['player', modClasses.PlayerController, false],
            ['widgets', modClasses.WidgetManager, false],
            ['cards', modClasses.CardManager, false],
            ['scroll', modClasses.ScrollManager, false],
            ['touch', modClasses.TouchManager, false],
        ];

        for (const [name, ModClass, critical] of bootOrder) {
            if (!ModClass) continue;
            try {
                const instance = new ModClass(eventBus);
                instance.init();
                this.#modules.set(name, instance);
                this.#log(`${name} initialized`);
            } catch (err) {
                if (critical) {
                    console.error(`[CoreMusicApp] Critical module "${name}" failed — halting boot:`, err);
                    this.#state = 'destroyed';
                    return;
                }
                console.warn(`[CoreMusicApp] Non-critical module "${name}" skipped:`, err);
            }
        }

        this.#state = 'running';
        eventBus.emit('app:ready');
        this.#log('App ready');
    }

    /** Tüm modülleri temizle */
    destroy() {
        for (const [name, mod] of this.#modules) {
            try {
                if (typeof mod.destroy === 'function') mod.destroy();
            } catch (err) {
                console.warn(`[CoreMusicApp] Error destroying "${name}":`, err);
            }
        }
        this.#modules.clear();
        this.#state = 'destroyed';
    }

    /**
     * @param {string} msg
     */
    #log(msg) {
        console.log(`[CoreMusicApp] ${msg}`);
    }
}
