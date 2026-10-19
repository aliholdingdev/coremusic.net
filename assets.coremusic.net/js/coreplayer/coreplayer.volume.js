(function () {
  'use strict';

  /* ------------------------------------------------------------
   * L0 — VolumeRepository (cookie + media access)
   * ------------------------------------------------------------ */
  const VolumeRepository = {
    _mediaElement: null,
    _cookieName: 'MM_Volume',
    COOKIE_DAYS: 365,

    setMediaElement(el) {
      this._mediaElement = el;
    },

    setCookieName(name) {
      this._cookieName = name;
    },

    getCookie(name) {
      return window.CorePlayerShared.getCookie(name);
    },

    setCookie(name, value, days) {
      window.CorePlayerShared.setCookie(name, value, days);
    },

    getVolumeCookie() {
      const raw = this.getCookie(this._cookieName);
      const val = parseFloat(raw);
      return raw !== null && !isNaN(val) ? val : 0.5;
    },

    saveVolumeCookie(value) {
      this.setCookie(this._cookieName, String(value), this.COOKIE_DAYS);
    },

    getMediaElement() {
      if (this._mediaElement) return this._mediaElement;
      /* Tek kaynak: fallback zinciri CorePlayerShared içinde (DRY) */
      return window.CorePlayerShared.getMediaElement();
    },
  };

  /* ------------------------------------------------------------
   * L1 — VolumeCalculator
   * ------------------------------------------------------------ */
  const VolumeCalculator = {
    clamp(value) {
      return Math.min(1, Math.max(0, value));
    },

    edgeSnap(value) {
      if (value >= 0.98) return 1;
      if (value <= 0.02) return 0;
      return value;
    },

    fromClientX(clientX, trackLeft, trackWidth) {
      if (!trackWidth) return 0;
      return this.clamp((clientX - trackLeft) / trackWidth);
    },

    fromWheel(deltaY, current) {
      const step = deltaY > 0 ? -0.05 : 0.05;
      return this.clamp(current + step);
    },

    toPercent(value) {
      return Math.round(this.edgeSnap(value) * 100);
    },

    getIconName(value) {
      if (value === 0) return 'volume-mute.png';
      if (value < 0.33) return 'volume-low.png';
      if (value < 0.66) return 'volume-medium.png';
      return 'volume-high.png';
    },
  };

  /* ------------------------------------------------------------
   * L2 — VolumeController (orchestrator)
   * ------------------------------------------------------------ */
  class VolumeController {
    constructor() {
      this._isDragging = false;
      this._lastNonZero = 0;
      this._iconEl = null;
      this._inputEl = null;
      this._fillEl = null;
      this._pctEl = null;
      this._bound = { setFromInput: this._setFromInput.bind(this), setFromDrag: this._setFromDrag.bind(this), setFromWheel: this._setFromWheel.bind(this), startDrag: this._startDrag.bind(this), stopDrag: this._stopDrag.bind(this), toggleMute: this._toggleMute.bind(this), toggleMuteKey: this._toggleMuteKey.bind(this) };
      this._containerEl = null;
      this._config = { container: '.footer-player__volume', fillSelector: '#volume2', percentSelector: '.footer-player__volume-value', iconSelector: '#volumeIcon', iconBasePath: this.#resolveThemePath(), trackSelector: '#volumeclick' };
    }

    /** Resolve theme image path from data-gender attribute on <html>. */
    #resolveThemePath() {
      const gender = document.documentElement?.getAttribute('data-gender') || 'neutral';
      const THEME_ASSET_MAP = Object.freeze({ female: 'res-pink', male: 'res-blue', neutral: 'res-default' });
      const folder = THEME_ASSET_MAP[gender] || 'res-default';
      return `//assets.coremusic.net/Image/${folder}/`;
    }

    init(config) {
      if (this._initialized) return;
      if (config) Object.assign(this._config, config);

      const el = (this._containerEl = document.querySelector(this._config.container));
      if (!el) {
        /* Sessiz ölüm önleme: flag set EDİLMEZ → sonraki init çağrısı retry eder */
        if (window.dbg && typeof window.dbg.warn === 'function') window.dbg.warn('[VolumeController] container bulunamadı:', this._config.container);
        return;
      }

      const input = el.querySelector('input[type="range"]');
      if (input) {
        this._inputEl = input;
        input.addEventListener('input', this._bound.setFromInput);
        input.addEventListener('change', this._bound.setFromInput);
      }

      const track = el.querySelector(this._config.trackSelector) || el;
      track.addEventListener('mousedown', this._bound.startDrag);
      track.addEventListener('touchstart', this._bound.startDrag, { passive: false });
      document.addEventListener('mouseup', this._bound.stopDrag);
      document.addEventListener('touchend', this._bound.stopDrag);
      document.addEventListener('touchcancel', this._bound.stopDrag);

      el.addEventListener('wheel', this._bound.setFromWheel, { passive: false });

      /* Cached lookups — setVolume başına tekrar querySelector yok */
      this._fillEl = document.querySelector(this._config.fillSelector);
      this._pctEl = document.querySelector(this._config.percentSelector);

      /* Mute toggle (icon) — icon yoksa sessizce atla */
      const icon = el.querySelector(this._config.iconSelector);
      if (icon) {
        this._iconEl = icon;
        /* A11y: bir kez, ikon bind edilirken — erişilebilir adın tek kaynağı aria-label; alt dekoratif */
        icon.setAttribute('role', 'button');
        icon.setAttribute('tabindex', '0');
        icon.setAttribute('alt', '');
        icon.addEventListener('click', this._bound.toggleMute);
        icon.addEventListener('keydown', this._bound.toggleMuteKey);
      }

      /* Yalnız başarılı bind sonunda flag → başarısızlıkta retry mümkün */
      this._initialized = true;

      const saved = VolumeRepository.getVolumeCookie();
      this.setVolume(saved);
    }

    destroy() {
      this._initialized = false;
      const el = this._containerEl;
      if (!el) return;

      if (this._iconEl) {
        this._iconEl.removeEventListener('click', this._bound.toggleMute);
        this._iconEl.removeEventListener('keydown', this._bound.toggleMuteKey);
        this._iconEl = null;
      }

      const input = this._inputEl || el.querySelector('input[type="range"]');
      if (input) {
        input.removeEventListener('input', this._bound.setFromInput);
        input.removeEventListener('change', this._bound.setFromInput);
      }

      const track = el.querySelector(this._config.trackSelector) || el;
      track.removeEventListener('mousedown', this._bound.startDrag);
      track.removeEventListener('touchstart', this._bound.startDrag);
      document.removeEventListener('mouseup', this._bound.stopDrag);
      document.removeEventListener('touchend', this._bound.stopDrag);
      document.removeEventListener('touchcancel', this._bound.stopDrag);
      el.removeEventListener('wheel', this._bound.setFromWheel);

      this._inputEl = null;
      this._fillEl = null;
      this._pctEl = null;
      this._containerEl = null;
    }

    getVolume() {
      return VolumeRepository.getVolumeCookie();
    }

    setVolume(raw) {
      const value = VolumeCalculator.edgeSnap(VolumeCalculator.clamp(raw));
      if (value > 0) this._lastNonZero = value;
      VolumeRepository.saveVolumeCookie(value);

      const media = VolumeRepository.getMediaElement();
      if (media) media.volume = value;

      this._updateSlider(value);
      this._updateFill(value);
      this._updatePercentDisplay(value);
      this._updateVolumeIcon(value);
    }

    /* ---- internal event handlers ---- */

    _setFromInput(e) {
      const raw = parseFloat(e.target.value) || 0;
      this.setVolume(raw);
    }

    _startDrag(e) {
      const el = this._containerEl;
      if (!el) return;
      if (e.target.closest('input')) return;

      this._isDragging = true;
      e.preventDefault();
      this._applyDrag(e);

      document.addEventListener('mousemove', this._bound.setFromDrag);
      document.addEventListener('touchmove', this._bound.setFromDrag, { passive: false });
    }

    _stopDrag() {
      if (!this._isDragging) return;
      this._isDragging = false;
      document.removeEventListener('mousemove', this._bound.setFromDrag);
      document.removeEventListener('touchmove', this._bound.setFromDrag);
    }

    _setFromDrag(e) {
      if (!this._isDragging) return;
      this._applyDrag(e);
    }

    _applyDrag(e) {
      const el = this._containerEl;
      if (!el) return;
      const track = el.querySelector(this._config.trackSelector) || el;
      const rect = track.getBoundingClientRect();
      const clientX = e.clientX !== undefined ? e.clientX : (e.touches ? e.touches[0].clientX : 0);
      const raw = VolumeCalculator.fromClientX(clientX, rect.left, rect.width);
      this.setVolume(raw);
    }

    _setFromWheel(e) {
      e.preventDefault();
      const current = VolumeRepository.getVolumeCookie();
      const raw = VolumeCalculator.fromWheel(e.deltaY, current);
      this.setVolume(raw);
    }

    _toggleMute() {
      const current = VolumeCalculator.edgeSnap(VolumeCalculator.clamp(VolumeRepository.getVolumeCookie()));
      if (current === 0) {
        this.setVolume(this._lastNonZero || 0.5);
      } else {
        this.setVolume(0);
      }
    }

    _toggleMuteKey(e) {
      if (e.repeat) return;
      if (e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar') return;
      e.preventDefault();
      this._toggleMute();
    }

    _updateSlider(value) {
      const el = this._containerEl;
      if (!el) return;
      const input = this._inputEl || el.querySelector('input[type="range"]');
      if (input) input.value = String(value);
    }

    _updateFill(value) {
      const fill = this._fillEl || document.querySelector(this._config.fillSelector);
      if (fill) fill.style.width = Math.round(value * 100) + '%';
    }

    _updatePercentDisplay(value) {
      const pct = this._pctEl || document.querySelector(this._config.percentSelector);
      if (pct) pct.textContent = '% ' + VolumeCalculator.toPercent(value);
    }

    _updateVolumeIcon(value) {
      const icon = this._iconEl || document.querySelector(this._config.iconSelector);
      if (icon) {
        /* src cache: aynı ikon adında DOM yazımı yok (dataset karşılaştırması) */
        const iconName = VolumeCalculator.getIconName(value);
        if (icon.dataset.iconName !== iconName) {
          icon.dataset.iconName = iconName;
          icon.src = this._config.iconBasePath + iconName;
        }
        /* A11y: label yalnız mute ↔ sesli DURUM DEĞİŞİMİNDE yazılır */
        const label = value === 0 ? 'Sesi aç' : 'Sesi kapat';
        if (icon.getAttribute('aria-label') !== label) {
          icon.setAttribute('aria-label', label);
        }
      }

      if (window._footerUpdateVolumeIcon) {
        window._footerUpdateVolumeIcon(value);
      }
    }
  }

  window.VolumeRepository = VolumeRepository;

  window.VolumeController = new VolumeController();

  /* ------------------------------------------------------------
   * Expose for external hooks
   * ------------------------------------------------------------ */
  window._footerUpdateVolumeIcon = window._footerUpdateVolumeIcon || null;
})();
