/**
 * CoreMusic — ThemeManager
 * Gender-based tema motoru (ADR-044). female/male/neutral.
 * Color mode motoru: dark/light.
 *
 * @module managers/ThemeManager
 * @version 6.0.0
 * @requires core/EventBus
 */
export default class ThemeManager {
    #eventBus;
    /** @type {'female'|'male'|'neutral'} */
    #currentTheme = 'neutral';
    /** @type {'dark'|'light'|null} null = OS preferansını kullan */
    #currentMode = null;

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

    /** Geçerli color mode değerleri */
    static VALID_MODES = ['dark', 'light'];

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    get theme() { return this.#currentTheme; }
    get mode() { return this.#currentMode; }

    init() {
        this.#loadTheme();
        this.#loadMode();
        this.#watchSystemMode();
    }

    /* ============================================================
       GENDER THEME
       ============================================================ */

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
        /* v-home.css arka plan görseli html[data-gender] seçicisiyle eşleşir —
           cookie teması (cm_gender) buradan görünür olur (ADR-044). */
        document.documentElement.setAttribute('data-gender', this.#currentTheme);
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

    /* ============================================================
       COLOR MODE (DARK/LIGHT)
       ============================================================ */

    /**
     * Color mode değiştir
     * @param {'dark'|'light'|null} mode  null = OS preferansına dön
     */
    setMode(mode) {
        if (mode !== null && !ThemeManager.VALID_MODES.includes(mode)) return;

        this.#currentMode = mode;
        this.#applyMode();
        this.#saveMode(mode);
        this.#eventBus.emit('modechange', { mode });
    }

    /** Dark/Light arasında toggle */
    toggleMode() {
        const current = this.#resolveEffectiveMode();
        this.setMode(current === 'dark' ? 'light' : 'dark');
    }

    /** OS preferansını döndür (prefers-color-scheme) */
    getSystemMode() {
        if (window.matchMedia?.('(prefers-color-scheme: light)').matches) {
            return 'light';
        }
        return 'dark';
    }

    /** Effective mode'u hesapla (null ise OS kullanılır) */
    #resolveEffectiveMode() {
        return this.#currentMode ?? this.getSystemMode();
    }

    /** Mode'u HTML data attribute'a uygula */
    #applyMode() {
        if (this.#currentMode === null) {
            // null = attribute'u kaldır, CSS prefers-color-scheme kullanır
            document.documentElement.removeAttribute('data-mode');
        } else {
            document.documentElement.setAttribute('data-mode', this.#currentMode);
        }
    }

    /** Mode'u cookie'ye kaydet */
    #saveMode(mode) {
        if (mode === null) {
            // Cookie'yi sil
            document.cookie = 'cm_color_mode=; path=/; domain=.coremusic.net; max-age=0; samesite=Lax';
        } else {
            document.cookie = `cm_color_mode=${mode}; path=/; domain=.coremusic.net; max-age=31536000; samesite=Lax`;
        }
    }

    /** Cookie'den veya data attribute'tan mode yükle */
    #loadMode() {
        let saved = null;

        // 1. Cookie'den oku
        const match = document.cookie.match(/cm_color_mode=([^;]+)/);
        if (match) saved = match[1];

        // 2. HTML data attribute'tan oku (PHP tarafında set edilmiş)
        if (!saved) saved = document.documentElement?.dataset?.mode;

        // 3. data-color-mode attribute'undan oku (device-loader'dan)
        if (!saved) {
            const script = document.querySelector('script[data-cm-device-loader]');
            saved = script?.getAttribute('data-color-mode') || null;
        }

        if (saved && ThemeManager.VALID_MODES.includes(saved)) {
            this.#currentMode = saved;
        }

        this.#applyMode();
    }

    /** OS prefers-color-scheme değişikliklerini dinle */
    #watchSystemMode() {
        if (!window.matchMedia) return;

        const mq = window.matchMedia('(prefers-color-scheme: dark)');

        // Kullanıcı mode seçmediyse, OS değişikliklerini dinle
        const handler = () => {
            if (this.#currentMode === null) {
                // Mode null ise, OS değişikliği CSS tarafından otomatik ele alınır
                // Sadece event emit et
                this.#eventBus.emit('modechange', {
                    mode: this.getSystemMode(),
                    source: 'system',
                });
            }
        };

        if (mq.addEventListener) {
            mq.addEventListener('change', handler);
        } else if (mq.addListener) {
            mq.addListener(handler); // Safari < 14
        }
    }

    /* ============================================================
       CLEANUP
       ============================================================ */

    destroy() {
        // Theme tokens'ları temizle
        Object.keys(ThemeManager.THEMES['neutral']).forEach((key) => {
            document.documentElement.style.removeProperty(key);
        });

        // Mode attribute'u temizle
        document.documentElement.removeAttribute('data-mode');
    }
}
