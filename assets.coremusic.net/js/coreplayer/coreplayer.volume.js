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
      const type = this.getCookie('MM_PlaybackType');
      if (type === '1') {
        const v = document.getElementById('main-video');
        if (v) return v;
      }
      return document.getElementById('audio') || document.getElementById('main-audio');
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
      this._bound = { setFromInput: this._setFromInput.bind(this), setFromDrag: this._setFromDrag.bind(this), setFromWheel: this._setFromWheel.bind(this), startDrag: this._startDrag.bind(this), stopDrag: this._stopDrag.bind(this) };
      this._containerEl = null;
      this._config = { container: '.volume-set-slider', fillSelector: '#volume2', percentSelector: 'p.c-footer__volume-slider-volume-size', iconSelector: '#volumeIcon', iconBasePath: this.#resolveThemePath(), trackSelector: '#volumeclick' };
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
      this._initialized = true;
      if (config) Object.assign(this._config, config);

      const el = (this._containerEl = document.querySelector(this._config.container));
      if (!el) return;

      const input = el.querySelector('input[type="range"]');
      if (input) {
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

      const saved = VolumeRepository.getVolumeCookie();
      this.setVolume(saved);

      this._updateVolumeIcon(saved);
    }

    destroy() {
      this._initialized = false;
      const el = this._containerEl;
      if (!el) return;

      const input = el.querySelector('input[type="range"]');
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

      this._containerEl = null;
    }

    getVolume() {
      return VolumeRepository.getVolumeCookie();
    }

    setVolume(raw) {
      const value = VolumeCalculator.edgeSnap(VolumeCalculator.clamp(raw));
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

    _updateSlider(value) {
      const el = this._containerEl;
      if (!el) return;
      const input = el.querySelector('input[type="range"]');
      if (input) input.value = String(value);
    }

    _updateFill(value) {
      const fill = document.querySelector(this._config.fillSelector);
      if (fill) fill.style.width = Math.round(value * 100) + '%';
    }

    _updatePercentDisplay(value) {
      const pct = document.querySelector(this._config.percentSelector);
      if (pct) pct.textContent = '% ' + VolumeCalculator.toPercent(value);
    }

    _updateVolumeIcon(value) {
      const icon = document.querySelector(this._config.iconSelector);
      if (icon) {
        icon.src = this._config.iconBasePath + VolumeCalculator.getIconName(value);
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
