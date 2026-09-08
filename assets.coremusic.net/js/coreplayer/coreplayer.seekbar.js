(function () {
  'use strict';

  /* ------------------------------------------------------------
   * L0 — SeekRepository (media + cookie access)
   * ------------------------------------------------------------ */
  const SeekRepository = {
    _mediaElement: null,

    setMediaElement(el) {
      this._mediaElement = el;
    },

    getCookie(name) {
      return window.CorePlayerShared.getCookie(name);
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
   * L1 — SeekCalculator
   * ------------------------------------------------------------ */
  const SeekCalculator = {
    clamp(value) {
      return Math.min(1, Math.max(0, value));
    },

    fromClientX(clientX, trackLeft, trackWidth) {
      if (!trackWidth) return 0;
      return this.clamp((clientX - trackLeft) / trackWidth);
    },

    fromWheel(deltaY, currentTime, duration) {
      if (!duration) return currentTime;
      const step = deltaY > 0 ? -5 : 5;
      return Math.min(duration, Math.max(0, currentTime + step));
    },

    toTime(percentage, duration) {
      return percentage * duration;
    },
  };

  /* ------------------------------------------------------------
   * L2 — SeekController (orchestrator)
   * ------------------------------------------------------------ */
  class SeekController {
    constructor() {
      this._isDragging = false;
      this._lastDragTime = 0;
      this._bound = { setFromInput: this._setFromInput.bind(this), startDrag: this._startDrag.bind(this), stopDrag: this._stopDrag.bind(this), setFromDrag: this._setFromDrag.bind(this), setFromWheel: this._setFromWheel.bind(this) };
      this._containerEl = null;
    }

    init(config) {
      if (this._initialized) return;
      this._initialized = true;
      config = config || {};
      const selector = config.container || '#seekbarclick';
      const el = (this._containerEl = document.querySelector(selector));
      if (!el) return;

      const input = el.querySelector('input[type="range"]');
      if (input) {
        input.addEventListener('input', this._bound.setFromInput);
        input.addEventListener('change', this._bound.setFromInput);
      }

      el.addEventListener('mousedown', this._bound.startDrag);
      document.addEventListener('mouseup', this._bound.stopDrag);

      el.addEventListener('touchstart', this._bound.startDrag, { passive: false });
      document.addEventListener('touchend', this._bound.stopDrag);
      document.addEventListener('touchcancel', this._bound.stopDrag);

      el.addEventListener('wheel', this._bound.setFromWheel, { passive: false });
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

      el.removeEventListener('mousedown', this._bound.startDrag);
      document.removeEventListener('mouseup', this._bound.stopDrag);

      el.removeEventListener('touchstart', this._bound.startDrag);
      document.removeEventListener('touchend', this._bound.stopDrag);
      document.removeEventListener('touchcancel', this._bound.stopDrag);

      el.removeEventListener('wheel', this._bound.setFromWheel);

      this._containerEl = null;
    }

    seek(percentage) {
      const media = SeekRepository.getMediaElement();
      if (!media || !media.duration) return;

      media.currentTime = SeekCalculator.toTime(SeekCalculator.clamp(percentage), media.duration);
    }

    /* ---- internal event handlers ---- */

    _setFromInput(e) {
      const raw = parseFloat(e.target.value) || 0;
      const max = parseFloat(e.target.max) || 1;
      const pct = SeekCalculator.clamp(raw / max);
      this.seek(pct);

      const media = SeekRepository.getMediaElement();
      if (window.SeekView) {
        SeekView.updateProgressBar(pct);
        SeekView.updateTimeDisplay(media ? media.currentTime : 0);
      }
    }

    _startDrag(e) {
      if (e.target.closest('input')) return;
      this._isDragging = true;
      this._lastDragTime = 0;
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
      const now = Date.now();
      if (this._lastDragTime && now - this._lastDragTime < 16) return;
      this._lastDragTime = now;
      this._applyDrag(e);
    }

    _applyDrag(e) {
      const el = this._containerEl;
      if (!el) return;
      const track = el.querySelector('.seekbar-slider') || el;
      const rect = track.getBoundingClientRect();
      const clientX = e.clientX !== undefined ? e.clientX : (e.touches ? e.touches[0].clientX : 0);
      const pct = SeekCalculator.fromClientX(clientX, rect.left, rect.width);

      this.seek(pct);

      if (window.SeekView) {
        SeekView.updateProgressBar(pct);
      }
    }

    _setFromWheel(e) {
      e.preventDefault();
      const media = SeekRepository.getMediaElement();
      if (!media || !media.duration) return;

      const t = SeekCalculator.fromWheel(e.deltaY, media.currentTime, media.duration);
      media.currentTime = t;

      const pct = media.duration ? t / media.duration : 0;
      if (window.SeekView) {
        SeekView.updateProgressBar(pct);
        SeekView.updateTimeDisplay(t);
      }
    }
  }

  /* ------------------------------------------------------------
   * L3 — SeekView (DOM rendering)
   * ------------------------------------------------------------ */
  const SeekView = {
    _containerSelector: '#seekbarclick',
    _fillSelector: '#seekbar2',
    _timeCurrentSelector: '#gettime_audio',
    _timeDurationSelector: '#footer_sure',

    configure(config) {
      if (config.container) this._containerSelector = config.container;
      if (config.fill) this._fillSelector = config.fill;
      if (config.timeCurrent) this._timeCurrentSelector = config.timeCurrent;
      if (config.timeDuration) this._timeDurationSelector = config.timeDuration;
    },

    updateProgressBar(percentage) {
      const el = document.querySelector(this._containerSelector);
      if (!el) return;

      const input = el.querySelector('input[type="range"]');
      if (input) {
        const max = parseFloat(input.max) || 1;
        input.value = String(percentage * max);
      }

      const fill = document.querySelector(this._fillSelector);
      if (fill) {
        const cw = el.clientWidth || el.getBoundingClientRect().width;
        if (cw > 0) {
          const thumbW = 10;
          const fillPx = percentage * (cw - thumbW) + thumbW / 2;
          fill.style.width = Math.round(fillPx) + 'px';
        } else {
          fill.style.width = Math.round(percentage * 100) + '%';
        }
      }
    },

    updateTimeDisplay(currentTime) {
      const cur = document.querySelector(this._timeCurrentSelector);
      if (cur) {
        cur.textContent = window._fmtTime ? window._fmtTime(currentTime) : this._formatTime(currentTime);
      }
    },

    updateDurationDisplay(duration) {
      const dur = document.querySelector(this._timeDurationSelector);
      if (dur) {
        dur.textContent = window._fmtTime ? window._fmtTime(duration) : this._formatTime(duration);
      }
    },

    _formatTime(s) {
      return window.CorePlayerShared.formatTime(s);
    },
  };

  window.SeekRepository = SeekRepository;

  window.SeekController = new SeekController();
  window.SeekView = SeekView;
  window._fmtTime = window._fmtTime || null;
})();
