/**
 * CoreMusic — Auth Gender Background System v2
 * Cinsiyet seçiminde animasyonlu arka plan geçişi
 * Cihaz çözünürlüğüne göre farklı çözünürlükte görseller
 *
 * Breakpoint'ler (DeviceLoader ile senkronize):
 *   phone      ≤767px      → login-bg-{gender}-small.jpg
 *   tablet     768-1024px  → login-bg-{gender}.jpg
 *   embedded   ≤1024×600   → login-bg-{gender}.jpg
 *   laptop     1025-1440px → login-bg-{gender}.jpg
 *   desktop    1441-2560px → login-bg-{gender}.jpg
 *   4k-tv      2561-3840px → login-bg-{gender}-4k.jpg
 *   4k-monitor ≥3841px     → login-bg-{gender}-4k.jpg
 *
 * Kullanım:
 *   <script src="/Js/auth/auth-gender-bg.js" defer></script>
 */
(function () {
    'use strict';

    /* ============================================================
       CONFIGURATION
       ============================================================ */
    var ASSETS_URL = '';
    var BG_BASE = '/Image/background/';
    var CROSSFADE_MS = 600;
    var EASING = 'cubic-bezier(0.4, 0, 0.2, 1)';

    /**
     * Cihaz çözünürlüğüne göre görsel dosya adı haritası
     * Anahtar: cihaz türü
     * Değer: çözünürlük soneki (boş = orijinal)
     */
    var RESOLUTION_MAP = {
        'phone':      '-small',
        'tablet':     '',
        'embedded':   '',
        'laptop':     '',
        'desktop':    '',
        '4k-tv':      '-4k',
        '4k-monitor': '-4k'
    };

    /**
     * Cinsiyet → arka plan görsel haritası
     */
    var GENDER_BG = {
        'female':  'login-bg-female.png',
        'male':    'login-bg-male.png'
    };

    /**
     * Cinsiyet → animasyon rengi haritası (ambient glow, particle renkleri)
     */
    var GENDER_COLORS = {
        'female':  { primary: '#F8B4C8', glow: 'rgba(248,180,200,0.25)', particle: 'rgba(232,180,184,0.4)' },
        'male':    { primary: '#A0C4E2', glow: 'rgba(160,196,226,0.25)', particle: 'rgba(91,143,185,0.4)' }
    };

    /* ============================================================
       STATE
       ============================================================ */
    var currentGender = null;
    var bgElement = null;
    var overlayEl = null;
    var isAnimating = false;

    /* ============================================================
       HELPERS
       ============================================================ */

    /**
     * Mevcut cihaz türünü al (DeviceLoader'dan veya viewport'tan)
     */
    function getDeviceType() {
        if (window.CoreMusic && window.CoreMusic.deviceType) {
            return window.CoreMusic.deviceType;
        }
        var w = window.innerWidth || document.documentElement.clientWidth;
        var h = window.innerHeight || document.documentElement.clientHeight;
        if (w <= 767) return 'phone';
        if (w <= 1024 && h <= 600) return 'embedded';
        if (w <= 1024) return 'tablet';
        if (w <= 1440) return 'laptop';
        if (w <= 2560) return 'desktop';
        if (w <= 3840) return '4k-tv';
        return '4k-monitor';
    }

    /**
     * Ekran çözünürlüğünü al (px cinsinden)
     */
    function getResolution() {
        var w = window.screen ? window.screen.width : window.innerWidth;
        var h = window.screen ? window.screen.height : window.innerHeight;
        return { width: w, height: h, total: w * h };
    }

    /**
     * Cinsiyet ve cihaz türüne göre arka plan URL'i oluştur
     */
    function buildBgUrl(gender, device) {
        var base = GENDER_BG[gender] || GENDER_BG['female'];
        var suffix = RESOLUTION_MAP[device] || '';
        var ext = base.split('.').pop();
        var name = base.replace('.' + ext, '');
        return ASSETS_URL + BG_BASE + name + suffix + '.' + ext;
    }

    /**
     * URL'in yüklenip yüklenmediğini kontrol et
     */
    function preloadImage(url) {
        return new Promise(function (resolve) {
            var img = new Image();
            img.onload = function () { resolve({ ok: true, width: img.naturalWidth, height: img.naturalHeight }); };
            img.onerror = function () { resolve({ ok: false }); };
            img.src = url;
        });
    }

    /**
     * Overlay elementini oluştur veya bul
     */
    function ensureOverlay() {
        if (overlayEl && overlayEl.parentNode) return overlayEl;
        overlayEl = document.createElement('div');
        overlayEl.className = 'lgn-bg__overlay';
        overlayEl.setAttribute('aria-hidden', 'true');
        overlayEl.style.cssText = [
            'position:absolute',
            'inset:0',
            'z-index:1',
            'background-size:cover',
            'background-position:center',
            'background-repeat:no-repeat',
            'opacity:0',
            'transition:opacity ' + CROSSFADE_MS + 'ms ' + EASING,
            'pointer-events:none',
            'will-change:opacity'
        ].join(';');
        if (bgElement) {
            bgElement.appendChild(overlayEl);
        }
        return overlayEl;
    }

    /**
     * CSS data-gender attribute'unu güncelle (tüm sayfa için)
     */
    function updateDataGender(gender) {
        var page = document.querySelector('.lgn-page');
        if (page) {
            page.setAttribute('data-gender', gender);
        }
        // Body'de de güncelle (SPA uyumluluğu)
        if (document.body) {
            document.body.setAttribute('data-gender', gender);
        }
    }

    /* ============================================================
       BACKGROUND CROSSFADE — ANİMASYONLU GEÇİŞ
       ============================================================ */

    /**
     * Mevcut animasyonu anında bitir (hızlı geçiş için)
     */
    function finishCurrentAnimation() {
        if (!overlayEl) return;
        // Overlay'ı anında gizle
        overlayEl.style.transition = 'none';
        overlayEl.style.opacity = '0';
        // Mevcut bg'yi overlay'deki görselle güncelle
        var currentBg = overlayEl.style.backgroundImage;
        if (currentBg && currentBg !== 'none' && bgElement) {
            bgElement.style.backgroundImage = currentBg;
        }
        overlayEl.style.backgroundImage = 'none';
        // Transition'ı geri yükle
        requestAnimationFrame(function () {
            if (overlayEl) {
                overlayEl.style.transition = '';
            }
        });
    }

    /**
     * Arka planı animasyonlu olarak değiştir
     * Hızlı geçişlerde mevcut animasyonu kesip yeniden başlar.
     */
    function transitionBg(newGender, device) {
        if (newGender === currentGender) return;

        // Animasyon sırasında yeni tıklanırsa — mevcut animasyonu bitir
        if (isAnimating) {
            finishCurrentAnimation();
            isAnimating = false;
        }

        isAnimating = true;
        var overlay = ensureOverlay();
        var newUrl = buildBgUrl(newGender, device);

        // Yeni görseli arka planda yükle
        preloadImage(newUrl).then(function (result) {
            if (!result.ok) {
                // Fallback: doğrudan değiştir
                if (bgElement) {
                    bgElement.style.backgroundImage = 'url("' + newUrl + '")';
                }
                currentGender = newGender;
                updateDataGender(newGender);
                isAnimating = false;
                return;
            }

            // Overlay'a yeni görseli ata
            overlay.style.backgroundImage = 'url("' + newUrl + '")';

            // Fade-in başlat — 2 kare gecikme ile
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    overlay.style.opacity = '1';

                    // Crossfade tamamlandı
                    setTimeout(function () {
                        // Mevcut arka planı güncelle
                        if (bgElement) {
                            bgElement.style.backgroundImage = 'url("' + newUrl + '")';
                        }
                        // Overlay'ı gizle (sonraki geçiş için hazır)
                        overlay.style.opacity = '0';
                        overlay.style.backgroundImage = 'none';

                        // State güncelle
                        currentGender = newGender;
                        updateDataGender(newGender);
                        isAnimating = false;
                    }, CROSSFADE_MS + 100);
                });
            });
        });
    }

    /**
     * İlk yükleme — animasyonsuz direkt ayarla
     */
    function setInitialBg(gender, device) {
        var url = buildBgUrl(gender, device);
        if (bgElement) {
            bgElement.style.backgroundImage = 'url("' + url + '")';
        }
        currentGender = gender;
        updateDataGender(gender);
    }

    /* ============================================================
       EVENT HANDLERS
       ============================================================ */

    /**
     * Gender butonuna tıklama — animasyonlu geçiş
     */
    function onGenderClick(e) {
        var btn = e.target.closest('.lgn-gender-btn');
        if (!btn) return;

        var gender = btn.getAttribute('data-gender');
        if (!gender) return;

        var device = getDeviceType();
        transitionBg(gender, device);
    }

    /**
     * URL'den cinsiyet oku (sayfa ilk yüklendiğinde)
     */
    function getGenderFromUrl() {
        var params = new URLSearchParams(window.location.search);
        return params.get('gender') || null;
    }

    /**
     * HTML'den mevcut cinsiyeti oku
     */
    function getGenderFromDOM() {
        var section = document.querySelector('.lgn-page');
        if (section) {
            return section.getAttribute('data-gender') || null;
        }
        return null;
    }

    /**
     * localStorage'dan cinsiyet oku
     */
    function getGenderFromStorage() {
        try {
            return localStorage.getItem('cm_gender') || null;
        } catch (e) {
            return null;
        }
    }

    /* ============================================================
       INIT
       ============================================================ */
    function init() {
        // ASSETS_URL'i data attribute'dan al
        var script = document.querySelector('script[data-cm-gender-bg]');
        if (script) {
            ASSETS_URL = script.getAttribute('data-assets-url') || '';
        }

        // BG elementini bul
        bgElement = document.querySelector('.lgn-bg');

        // Mevcut cinsiyeti belirle (öncelik: DOM > URL > Storage > default)
        var gender = getGenderFromDOM() || getGenderFromUrl() || getGenderFromStorage() || 'female';

        // İlk yükleme — animasyonsuz
        var device = getDeviceType();
        setInitialBg(gender, device);

        // Event delegation — gender butonları için
        document.addEventListener('click', onGenderClick);

        // Cihaz değişikliğini dinle (resize)
        var resizeTimer = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (currentGender) {
                    var newDevice = getDeviceType();
                    var newUrl = buildBgUrl(currentGender, newDevice);
                    if (bgElement) {
                        bgElement.style.backgroundImage = 'url("' + newUrl + '")';
                    }
                }
            }, 250);
        });

        // DeviceLoader event'ini dinle (cihaz değiştiğinde arka planı güncelle)
        window.addEventListener('devicechange', function (e) {
            if (currentGender && e.detail && e.detail.device) {
                var newUrl = buildBgUrl(currentGender, e.detail.device);
                if (bgElement) {
                    bgElement.style.backgroundImage = 'url("' + newUrl + '")';
                }
            }
        });

        // Global API
        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.GenderBg = {
            transition: transitionBg,
            buildUrl: buildBgUrl,
            getCurrentGender: function () { return currentGender; },
            getDeviceType: getDeviceType,
            getResolution: getResolution
        };
    }

    /* ============================================================
       AUTO-INIT
       ============================================================ */
    if (typeof document !== 'undefined') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }

    // Module export
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = { GenderBg: { init: init, transition: transitionBg, buildUrl: buildBgUrl } };
    }
})();
