(function () {
  'use strict';

  /* ------------------------------------------------------------
   * L0 — ProgressRepository (media + cookie access)
   * ------------------------------------------------------------ */
  const ProgressRepository = {
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
   * L1 — ProgressCalculator
   * ------------------------------------------------------------ */
  const ProgressCalculator = {
    calculatePercentage(currentTime, duration) {
      if (!duration || !isFinite(duration) || duration <= 0) return 0;
      return Math.min(1, Math.max(0, currentTime / duration));
    },

    _formatTime(s) {
      return window.CorePlayerShared.formatTime(s);
    },
  };

  /* ------------------------------------------------------------
   * L2 — ProgressController (orchestrator)
   * ------------------------------------------------------------ */
  class ProgressController {
    constructor() {
      this._bound = { onTimeUpdate: this._onTimeUpdate.bind(this) };
      this._mediaEl = null;
      this._rafId = null;
    }

    init() {
      window._updateProgressBar(0);

      const media = ProgressRepository.getMediaElement();
      if (media) {
        this._attachMedia(media);
        this._syncDuration(media);
      }
    }

    destroy() {
      this._detachMedia();
      if (this._rafId) {
        cancelAnimationFrame(this._rafId);
        this._rafId = null;
      }
    }

    _attachMedia(media) {
      this._detachMedia();
      this._mediaEl = media;
      media.addEventListener('timeupdate', this._bound.onTimeUpdate);
    }

    _detachMedia() {
      if (this._mediaEl) {
        this._mediaEl.removeEventListener('timeupdate', this._bound.onTimeUpdate);
        this._mediaEl = null;
      }
    }

    _onTimeUpdate() {
      if (this._rafId) return;

      this._rafId = requestAnimationFrame(() => {
        this._rafId = null;
        this._sync();
      });
    }

    _sync() {
      const media = ProgressRepository.getMediaElement();
      if (!media) return;

      const pct = ProgressCalculator.calculatePercentage(media.currentTime, media.duration);

      if (window._updateProgressBar) {
        window._updateProgressBar(pct);
      }

      if (window._updateTimeDisplay) {
        window._updateTimeDisplay(media.currentTime);
      }

      this._syncDuration(media);

      if ('mediaSession' in navigator) {
        navigator.mediaSession.setPositionState({
          duration: media.duration || 0,
          playbackRate: media.playbackRate || 1,
          position: media.currentTime || 0,
        });
      }
    }

    _syncDuration(media) {
      if (!media || !media.duration) return;

      if (window.SeekView && typeof SeekView.updateDurationDisplay === 'function') {
        SeekView.updateDurationDisplay(media.duration);
        return;
      }

      const durEl = document.querySelector('.seekbar-time-duration, .duration-time');
      if (durEl) {
        durEl.textContent = window._fmtTime ? window._fmtTime(media.duration) : ProgressCalculator._formatTime(media.duration);
      }
    }
  }

  /* ------------------------------------------------------------
   * L3 — Shared DOM update functions
   * ------------------------------------------------------------ */
  function updateProgressBar(percentage) {
    if (window.SeekView) {
      SeekView.updateProgressBar(percentage);
      return;
    }
    const el = document.querySelector('.seekbar');
    if (!el) return;
    const input = el.querySelector('input[type="range"]');
    if (input) input.value = String(percentage);
    const fill = el.querySelector('.seekbar2, .seekbar-fill');
    if (fill) fill.style.width = Math.round(percentage * 100) + '%';
  }

  function updateTimeDisplay(currentTime) {
    if (window.SeekView) {
      SeekView.updateTimeDisplay(currentTime);
      return;
    }
    const el = document.querySelector('.seekbar');
    if (!el) return;
    const cur = el.querySelector('.seekbar-time-current, .current-time');
    if (cur) {
      cur.textContent = window._fmtTime ? window._fmtTime(currentTime) : ProgressCalculator._formatTime(currentTime);
    }
  }

  function getMediaElement() {
    return ProgressRepository.getMediaElement();
  }

  /* ------------------------------------------------------------
   * Init
   * ------------------------------------------------------------ */
  window.ProgressRepository = ProgressRepository;

  window._updateProgressBar = updateProgressBar;
  window._updateTimeDisplay = updateTimeDisplay;
  window.getMediaElement = getMediaElement;

  window.ProgressController = new ProgressController();
})();
