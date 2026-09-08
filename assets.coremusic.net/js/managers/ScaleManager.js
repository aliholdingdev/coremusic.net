/**
 * CoreMusic — ScaleManager v7.0.0
 * Dinamik, modüler ve iç içe (nested) hiyerarşileri destekleyen SOLID Ölçeklendirme Motoru.
 *
 * Referans: G:\Drive'ım\www\Yeni klasör\assets.coremusic.net\scale\
 * (scale.coordinator.js, footer.scale.js, header.scale.js, home.scale.js)
 *
 * SOLID Prensipleri:
 *   SRP — Sorumlulukların ayrımı:
 *         TierResolver (ekran tipi tespiti),
 *         ScaleCalculator (adım ve lineer enterpolasyon hesaplama),
 *         TransformApplier (DOM transform, transform-origin ve genişlik telafisi),
 *         NestedScaleCoordinator (iç içe bileşen ölçek senkronizasyonu),
 *         ScaleManager (yaşam döngüsü, olay dinleme, RAF koordinasyonu).
 *   OCP — Açık / Kapalı:
 *         Yeni bileşen veya nested eleman 'registerTarget()' ile dinamik eklenir;
 *         çekirdek koda dokunulmaz.
 *   LSP — Liskov Yerine Geçme:
 *         Tüm kural tanımları tek tip kural arayüzü ile çözümlenir (step, linear, skip).
 *   ISP — Arayüz Ayrımı:
 *         Gözlemciler (ResizeObserver, Fullscreen, DPR) izole modüller olarak çalışır.
 *   DIP — Bağımlılık Tersi:
 *         EventBus constructor ile enjekte edilir; global nesnelere bağımlılık minimize edilir.
 *
 * G: Drive Referans Değer Tablosu:
 *   Header (.site-header, origin: top left):
 *     ≤767px       : 0.75 (mobil)
 *     ≤1024px      : 0.88
 *     1025–1919px  : lineer 0.65 → 1.00
 *     1920px       : 1.00
 *     1921–3839px  : lineer 1.00 → 1.30
 *     ≥3840px      : 1.00
 *
 *   Footer (.footer, .player-footer, origin: bottom left):
 *     ≤767px       : 0.60 (mobil)
 *     ≤1024px      : 0.70
 *     1025–1368px (h≤768): 0.65
 *     1369–1520px (h≤768): 0.65
 *     1521–1920px  : 0.65
 *     1921–3839px  : lineer 0.65 → 1.10
 *     ≥3840px      : 0.85
 *
 *   Home (.page-home, origin: top left):
 *     ≤1024px      : skip (CSS @media kontrollü)
 *     1025–3839px  : lineer 0.65 → 1.50
 *     ≥3840px      : 0.85
 */

/* ============================================================
   1. TierResolver — Viewport & Cihaz Tipi Çözümleme (SRP)
   ============================================================ */
export class TierResolver {
    static BOUNDARIES = {
        PHONE_MAX: 767,
        EMBEDDED_MAX: 1024,
        LAPTOP_MAX: 1440,
        FHD_MAX: 1920,
        DESKTOP_MAX: 2560,
        FOUR_K_MIN: 3840,
    };

    /**
     * @param {number} width
     * @param {number} height
     * @returns {'phone'|'embedded'|'laptop'|'desktop'|'2k'|'4k'}
     */
    static resolve(width, height) {
        const B = TierResolver.BOUNDARIES;
        if (width <= B.PHONE_MAX) return 'phone';
        if (width <= B.EMBEDDED_MAX) return 'embedded';
        if (width <= B.LAPTOP_MAX) return 'laptop';
        if (width <= B.FHD_MAX) return 'desktop';
        if (width < B.FOUR_K_MIN) return '2k';
        return '4k';
    }
}

/* ============================================================
   2. ScaleCalculator — Ölçek Algoritması ve Lineer Enterpolasyon (SRP)
   ============================================================ */
export class ScaleCalculator {
    /**
     * Verilen kural listesi içinden viewport boyutlarına uygun ölçeği hesaplar.
     * @param {Array<Object>} rules
     * @param {number} sw - Screen Width
     * @param {number} sh - Screen Height
     * @returns {{scale: number, skip: boolean, rule: Object}|null}
     */
    static calculate(rules, sw, sh) {
        if (!Array.isArray(rules) || rules.length === 0) {
            return { scale: 1, skip: false, rule: null };
        }

        for (const r of rules) {
            // Yükseklik kısıtlaması varsa kontrol et
            if (r.maxH !== undefined && sh > r.maxH) continue;
            if (r.minH !== undefined && sh < r.minH) continue;

            // Genişlik kuralı: Sabit üst sınır
            if (r.max !== undefined && r.min === undefined) {
                if (sw <= r.max) {
                    return { scale: r.scale ?? 1, skip: Boolean(r.skip), rule: r };
                }
                continue;
            }

            // Genişlik kuralı: Sabit alt sınır
            if (r.min !== undefined && r.max === undefined) {
                if (sw >= r.min) {
                    return { scale: r.scale ?? 1, skip: Boolean(r.skip), rule: r };
                }
                continue;
            }

            // Genişlik aralığı: min ve max tanımlı
            if (r.min !== undefined && r.max !== undefined) {
                if (sw >= r.min && sw <= r.max) {
                    if (r.skip) return { scale: 1, skip: true, rule: r };

                    // Lineer enterpolasyon (from → to)
                    if (r.from !== undefined && r.to !== undefined) {
                        const ratio = (sw - r.min) / (r.max - r.min);
                        const interpolated = r.from + (r.to - r.from) * Math.max(0, Math.min(1, ratio));
                        return { scale: Math.round(interpolated * 10000) / 10000, skip: false, rule: r };
                    }

                    return { scale: r.scale ?? 1, skip: false, rule: r };
                }
            }
        }

        return { scale: 1, skip: false, rule: null };
    }
}

/* ============================================================
   3. TransformApplier — DOM Transform ve Boyutlandırma (SRP)
   ============================================================ */
export class TransformApplier {
    /**
     * @param {HTMLElement} el
     * @param {{scale:number, origin:string, compensateWidth?:boolean, toScreenWidth?:boolean, setCustomProp?:string}} opts
     */
    static apply(el, opts) {
        if (!el || !el.style) return;
        const { scale, origin, compensateWidth, toScreenWidth, setCustomProp } = opts;
        const sw = window.innerWidth;

        el.style.transform = `scale(${scale})`;
        el.style.transformOrigin = origin || 'top left';

        if (compensateWidth) {
            el.style.width = `${sw / scale}px`;
        }
        if (toScreenWidth) {
            el.style.maxWidth = `${sw}px`; /* viewport genişliği — screen.width 4K ekranda taşmaya neden olur */
        }
        if (setCustomProp) {
            el.style.setProperty(setCustomProp, String(scale));
        }
    }

    /**
     * @param {HTMLElement} el
     * @param {string} [customProp]
     */
    static reset(el, customProp) {
        if (!el || !el.style) return;
        el.style.transform = '';
        el.style.transformOrigin = '';
        el.style.width = '';
        el.style.maxWidth = '';
        if (customProp) {
            el.style.removeProperty(customProp);
        }
    }
}

/* ============================================================
   4. NestedScaleCoordinator — İç İçe Bileşen Yönetimi (SRP)
   ============================================================ */
export class NestedScaleCoordinator {
    /**
     * Ebeveyn scale'i altında yer alan alt bileşenlerin ölçeklerini koordine eder.
     * @param {HTMLElement} parentEl
     * @param {number} parentScale
     * @param {Array<Object>} nestedConfigs
     */
    static coordinateNested(parentEl, parentScale, nestedConfigs) {
        if (!parentEl || !Array.isArray(nestedConfigs)) return;

        for (const cfg of nestedConfigs) {
            const childElements = parentEl.querySelectorAll(cfg.selector);
            childElements.forEach(child => {
                if (cfg.inverseScale) {
                    // Ebeveyn büyürken dokunmatik hedefin sabit kalmasını sağlayan ters ölçek
                    const inv = 1 / parentScale;
                    child.style.transform = `scale(${inv})`;
                    child.style.transformOrigin = cfg.origin || 'center center';
                } else if (typeof cfg.customScale === 'function') {
                    const s = cfg.customScale(parentScale, window.innerWidth, window.innerHeight);
                    child.style.transform = `scale(${s})`;
                    child.style.transformOrigin = cfg.origin || 'center center';
                }
            });
        }
    }
}

/* ============================================================
   5. Varsayılan Hedef Tanımları (G: Drive Birebir Uyumlu)
   ============================================================ */
export const DEFAULT_SCALE_TARGETS = [
    {
        name: 'header',
        selector: '.site-header',
        origin: 'top left',
        compensateWidth: true,
        customProp: '--scale-header',
        rules: [
            { max: 767, scale: 0.75 },
            { max: 1024, scale: 0.88 },
            { min: 1025, max: 1919, from: 0.65, to: 1.00 },
            { min: 1920, max: 1920, scale: 1.00 },
            { min: 1921, max: 2560, scale: 1.00 },
            { min: 2561, skip: true }, // ≥2561px 2K & 4K ekranlarda yerel CSS yönetir
        ],
        nestedChildren: [
            { selector: '.header-widget--battery', origin: 'center right' },
            { selector: '.site-header__actions', origin: 'center right' }
        ]
    },
    {
        name: 'footer',
        selector: '.footer, .player-footer',
        origin: 'bottom left',
        compensateWidth: true,
        customProp: '--scale-footer',
        rules: [
            { max: 767, scale: 0.60 },
            { max: 1024, scale: 0.70 },
            { min: 1025, max: 1368, maxH: 768, scale: 0.65 },
            { min: 1369, max: 1520, maxH: 768, scale: 0.65 },
            { min: 1025, max: 1920, scale: 0.65 },
            { min: 1921, max: 2560, from: 0.65, to: 1.00 },
            { min: 2561, skip: true }, // ≥2561px 4K'da footer yerel CSS (120px/130px) ile yönetilir
        ],
        nestedChildren: [
            { selector: '.c-footer__seek-slider', origin: 'top center' },
            { selector: '.footer__utility-icons', origin: 'center right' },
            { selector: '.volume-set-slider', origin: 'center right' }
        ]
    },
    {
        name: 'home',
        selector: '.page-home',
        origin: 'top left',
        compensateWidth: true,
        toScreenWidth: true,
        customProp: '--scale-home',
        rules: [
            { max: 1024, skip: true }, // ≤1024px gömülü/tablet tasarımı CSS flex/grid ile yönetilir
            { min: 1025, max: 2560, from: 0.65, to: 1.00 },
            { min: 2561, skip: true }, // ≥2561px 4K Ultra HD tasarımı yerel CSS ile yönetilir
        ]
    }
];

/* ============================================================
   6. ScaleManager Ana Koordinatör Sınıfı (SRP / DIP)
   ============================================================ */
export default class ScaleManager {
    #eventBus;
    #targets = [];
    #rafId = null;
    #resizeObserver = null;
    #dprMql = null;
    #onDprChangeBound = null;
    #onFullscreenBound = null;
    #onWindowResizeBound = null;
    #isInitialized = false;

    /**
     * @param {Object} [eventBus] - CoreMusic EventBus instance
     */
    constructor(eventBus = null) {
        this.#eventBus = eventBus;
        this.#onWindowResizeBound = () => this.requestApply();
        this.#onFullscreenBound = () => this.requestApply();
        this.#onDprChangeBound = () => this.#handleDprChange();
    }

    /**
     * Yeni bir ölçeklenebilir hedef kaydet (OCP - Genişletilebilirlik).
     * @param {Object} target
     */
    registerTarget(target) {
        if (!target || !target.selector || !Array.isArray(target.rules)) {
            console.warn('[ScaleManager] Geçersiz hedef kaydı:', target);
            return;
        }
        // Aynı isimde hedef varsa güncelle, yoksa ekle
        const idx = this.#targets.findIndex(t => t.name === target.name);
        if (idx >= 0) {
            this.#targets[idx] = target;
        } else {
            this.#targets.push(target);
        }
    }

    /** Modülü başlat ve dinleyicileri bağla */
    init() {
        if (this.#isInitialized) return;

        // Varsayılan hedefleri kaydet
        for (const t of DEFAULT_SCALE_TARGETS) {
            this.registerTarget(t);
        }

        this.#bindListeners();
        this.#syncRootMetadata();
        this.applyAll();

        // G: Drive Legacy ScaleCoordinator & Global Fonksiyon Uyumluluğu
        this.#registerGlobalBridge();

        this.#isInitialized = true;
    }

    /** RAF ile sonraki karede toplu ölçek uygulama */
    requestApply() {
        if (this.#rafId !== null) {
            cancelAnimationFrame(this.#rafId);
        }
        this.#rafId = requestAnimationFrame(() => {
            this.#rafId = null;
            this.#syncRootMetadata();
            this.applyAll();
        });
    }

    /** Tüm hedefleri çözümle ve DOM'a uygula */
    applyAll() {
        const sw = window.innerWidth;
        const sh = window.innerHeight;
        const tier = TierResolver.resolve(sw, sh);
        const payload = { sw, sh, tier, targets: {} };

        for (const target of this.#targets) {
            const elements = document.querySelectorAll(target.selector);
            if (!elements || elements.length === 0) continue;

            const res = ScaleCalculator.calculate(target.rules, sw, sh);

            elements.forEach(el => {
                if (res.skip) {
                    TransformApplier.reset(el, target.customProp);
                } else {
                    TransformApplier.apply(el, {
                        scale: res.scale,
                        origin: target.origin,
                        compensateWidth: target.compensateWidth,
                        toScreenWidth: target.toScreenWidth,
                        setCustomProp: target.customProp,
                    });

                    // İç içe (nested) bileşen koordinasyonu
                    if (Array.isArray(target.nestedChildren)) {
                        NestedScaleCoordinator.coordinateNested(el, res.scale, target.nestedChildren);
                    }
                }
            });

            payload.targets[target.name] = { scale: res.scale, skip: res.skip };
        }

        // EventBus yayını
        if (this.#eventBus && typeof this.#eventBus.emit === 'function') {
            this.#eventBus.emit('scale:applied', payload);
        }
    }

    /** DOM kök düğümüne (html) metadata öznitelikleri ve CSS değişkenlerini senkronize eder */
    #syncRootMetadata() {
        const root = document.documentElement;
        if (!root) return;

        const sw = window.innerWidth;
        const sh = window.innerHeight;
        const tier = TierResolver.resolve(sw, sh);
        const dpr = (window.devicePixelRatio || 1).toFixed(2);
        const aspect = (sw / Math.max(sh, 1)).toFixed(2);

        root.setAttribute('data-scale-tier', tier);
        root.setAttribute('data-dpr', dpr);
        root.setAttribute('data-aspect', aspect);

        // Quick Controls görünürlüğü: ≤1024px gizli, >1024px görünür
        const qcDisplay = sw <= 1024 ? 'none' : 'flex';
        root.style.setProperty('--quick-controls-display', qcDisplay);
        root.style.setProperty('--qc-display', qcDisplay); // legacy token bridge
    }

    /** Event dinleyicilerini bağla */
    #bindListeners() {
        // 1. Window resize
        window.addEventListener('resize', this.#onWindowResizeBound, { passive: true });

        // 2. Fullscreen eventleri
        const FULLSCREEN_EVENTS = [
            'fullscreenchange',
            'webkitfullscreenchange',
            'mozfullscreenchange',
            'MSFullscreenChange'
        ];
        for (const ev of FULLSCREEN_EVENTS) {
            document.addEventListener(ev, this.#onFullscreenBound, { passive: true });
        }

        // 3. ResizeObserver
        if (typeof ResizeObserver === 'function' && document.body) {
            this.#resizeObserver = new ResizeObserver(() => {
                this.requestApply();
            });
            this.#resizeObserver.observe(document.body);
        }

        // 4. DPR / Monitor geçiş dinleyicisi
        this.#watchDpr();
    }

    /** Ekran DPR değişimini (farklı monitöre taşıma, tarayıcı zoom) izler */
    #watchDpr() {
        if (typeof window.matchMedia !== 'function') return;
        const dpr = window.devicePixelRatio || 1;
        this.#dprMql = window.matchMedia(`(resolution: ${dpr}dppx)`);
        if (typeof this.#dprMql.addEventListener === 'function') {
            this.#dprMql.addEventListener('change', this.#onDprChangeBound);
        }
    }

    #handleDprChange() {
        if (this.#dprMql && typeof this.#dprMql.removeEventListener === 'function') {
            this.#dprMql.removeEventListener('change', this.#onDprChangeBound);
        }
        this.#watchDpr();
        this.requestApply();
    }

    /**
     * G: Drive ve eski projelerle %100 geriye dönük uyumluluk köprüsü:
     * window.ScaleCoordinator, window.scaleFooterForScreen, window.scaleHeaderForScreen
     */
    #registerGlobalBridge() {
        const self = this;
        window.ScaleCoordinator = {
            runAll: () => self.requestApply(),
            onFullscreen: () => self.requestApply(),
            attachListeners: () => {} // Zaten modül içinde bağlı
        };

        window.scaleFooterForScreen = () => {
            const footerTarget = self.#targets.find(t => t.name === 'footer');
            if (!footerTarget) return;
            const res = ScaleCalculator.calculate(footerTarget.rules, window.innerWidth, window.innerHeight);
            const elements = document.querySelectorAll(footerTarget.selector);
            elements.forEach(el => {
                if (res.skip) TransformApplier.reset(el, footerTarget.customProp);
                else TransformApplier.apply(el, { scale: res.scale, origin: footerTarget.origin, compensateWidth: true });
            });
        };

        window.scaleHeaderForScreen = () => {
            const headerTarget = self.#targets.find(t => t.name === 'header');
            if (!headerTarget) return;
            const res = ScaleCalculator.calculate(headerTarget.rules, window.innerWidth, window.innerHeight);
            const elements = document.querySelectorAll(headerTarget.selector);
            elements.forEach(el => {
                if (res.skip) TransformApplier.reset(el, headerTarget.customProp);
                else TransformApplier.apply(el, { scale: res.scale, origin: headerTarget.origin, compensateWidth: true });
            });
        };

        window.scaleHomeForScreen = () => {
            const homeTarget = self.#targets.find(t => t.name === 'home');
            if (!homeTarget) return;
            const res = ScaleCalculator.calculate(homeTarget.rules, window.innerWidth, window.innerHeight);
            const elements = document.querySelectorAll(homeTarget.selector);
            elements.forEach(el => {
                if (res.skip) TransformApplier.reset(el, homeTarget.customProp);
                else TransformApplier.apply(el, { scale: res.scale, origin: homeTarget.origin, compensateWidth: true, toScreenWidth: true });
            });
        };
    }

    /** Modülü temizle ve dinleyicileri kaldır */
    destroy() {
        window.removeEventListener('resize', this.#onWindowResizeBound);
        const FULLSCREEN_EVENTS = [
            'fullscreenchange',
            'webkitfullscreenchange',
            'mozfullscreenchange',
            'MSFullscreenChange'
        ];
        for (const ev of FULLSCREEN_EVENTS) {
            document.removeEventListener(ev, this.#onFullscreenBound);
        }
        if (this.#resizeObserver) {
            this.#resizeObserver.disconnect();
            this.#resizeObserver = null;
        }
        if (this.#dprMql && typeof this.#dprMql.removeEventListener === 'function') {
            this.#dprMql.removeEventListener('change', this.#onDprChangeBound);
            this.#dprMql = null;
        }
        if (this.#rafId !== null) {
            cancelAnimationFrame(this.#rafId);
            this.#rafId = null;
        }
        this.#isInitialized = false;
    }
}
