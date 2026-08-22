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
     * @param {object} config
     * @param {object} config.modules — Modül sınıfları { name: Class }
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

    /** Tüm modülleri başlat */
    async init() {
        if (this.#state === 'running') return;
        this.#state = 'booting';

        const modClasses = this.#config.modules || {};
        const eventBus = new modClasses.EventBus();

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
