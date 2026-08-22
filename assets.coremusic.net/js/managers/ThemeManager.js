/**
 * CoreMusic — ThemeManager
 * Gender-based tema motoru (ADR-044). female/male/neutral.
 *
 * @module managers/ThemeManager
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class ThemeManager {
    #eventBus;
    /** @type {'female'|'male'|'neutral'} */
    #currentTheme = 'neutral';

    /** Tema token haritası */
    static THEMES = {
        female: {
            '--accent': '#ff4fd8',
            '--accent-hover': '#ff7ae3',
            '--accent-soft': 'rgba(255,79,216,0.15)',
            '--glass-bg': 'rgba(255,79,216,0.08)',
        },
        male: {
            '--accent': '#4f8fff',
            '--accent-hover': '#7ab0ff',
            '--accent-soft': 'rgba(79,143,255,0.15)',
            '--glass-bg': 'rgba(79,143,255,0.08)',
        },
        neutral: {
            '--accent': '#a855f7',
            '--accent-hover': '#c084fc',
            '--accent-soft': 'rgba(168,85,247,0.15)',
            '--glass-bg': 'rgba(168,85,247,0.08)',
        },
    };

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    get theme() { return this.#currentTheme; }

    init() {
        this.#loadTheme();
    }

    /**
     * Tema değiştir
     * @param {'female'|'male'|'neutral'} gender
     */
    setTheme(gender) {
        if (!ThemeManager.THEMES[gender]) return;
        this.#currentTheme = gender;
        this.#applyTheme();
        this.#saveTheme(gender);
        this.#eventBus.emit('themechange', { gender, tokens: ThemeManager.THEMES[gender] });
    }

    /** Sonraki temaya geç */
    toggle() {
        const order = ['neutral', 'female', 'male'];
        const idx = order.indexOf(this.#currentTheme);
        this.setTheme(order[(idx + 1) % order.length]);
    }

    /** Tema token'larını CSS'e uygula */
    #applyTheme() {
        const vars = ThemeManager.THEMES[this.#currentTheme];
        Object.entries(vars).forEach(([key, value]) => {
            document.documentElement.style.setProperty(key, value);
        });
    }

    /** Cookie'den veya data attribute'tan tema yükle */
    #loadTheme() {
        let saved = null;

        // 1. Cookie'den oku
        const match = document.cookie.match(/cm_gender=([^;]+)/);
        if (match) saved = match[1];

        // 2. Body data attribute'tan oku
        if (!saved) saved = document.body?.dataset?.gender;

        // 3. Session'dan oku (PHP tarafında set edilmiş)
        if (!saved) saved = window.CoreMusic?.gender;

        if (saved && ThemeManager.THEMES[saved]) {
            this.#currentTheme = saved;
        }

        this.#applyTheme();
    }

    /** Temayı cookie'ye kaydet */
    #saveTheme(gender) {
        document.cookie = `cm_gender=${gender}; path=/; domain=.coremusic.net; max-age=31536000; samesite=Lax`;
    }

    destroy() {
        Object.keys(ThemeManager.THEMES['neutral']).forEach((key) => {
            document.documentElement.style.removeProperty(key);
        });
    }
}
