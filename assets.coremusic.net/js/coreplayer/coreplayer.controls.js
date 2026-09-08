(function () {
  'use strict';

  /* ------------------------------------------------------------
   * L0 — PlaybackRepository (cookie + media access)
   * ------------------------------------------------------------ */
  const PlaybackRepository = {
    _mediaElement: null,

    setMediaElement(el) {
      this._mediaElement = el;
    },

    getCookie(name) {
      return window.CorePlayerShared.getCookie(name);
    },

    setCookie(name, value, days) {
      window.CorePlayerShared.setCookie(name, value, days);
    },

    COOKIE_DAYS: 365,

    getPlayingState() {
      return this.getCookie('MM_PlayingState');
    },

    savePlayingState(val) {
      this.setCookie('MM_PlayingState', val, this.COOKIE_DAYS);
    },

    getCurrentSongId() {
      return this.getCookie('MM_CurrentSongID');
    },

    getCurrentSongType() {
      return this.getCookie('MM_CurrentSongType');
    },

    getMaxSongId() {
      return this.getCookie('MM_MaxSongID');
    },

    getAudio() {
      if (this._mediaElement) return this._mediaElement;
      return document.getElementById('audio') || document.getElementById('main-audio');
    },

    getVideo() {
      return document.getElementById('main-video');
    },
  };

  /* ------------------------------------------------------------
   * L1 — PlaybackCalculator
   * ------------------------------------------------------------ */
  const PlaybackCalculator = {
    calculatePreviousId(current) {
      const id = parseInt(current, 10);
      if (isNaN(id) || id <= 1) return '1';
      return String(id - 1);
    },

    calculateNextId(current, max) {
      const id = parseInt(current, 10);
      const mx = parseInt(max, 10);
      if (isNaN(id)) return '1';
      if (isNaN(mx) || id >= mx) return String(id);
      return String(id + 1);
    },
  };

  /* ------------------------------------------------------------
   * L2 — PlaybackController (orchestrator)
   * ------------------------------------------------------------ */
  class PlaybackController {
    constructor() {
      this._bound = { onEnded: this._onEnded.bind(this), onPlay: this._onPlay.bind(this), onPause: this._onPause.bind(this), onKeyDown: this._onKeyDown.bind(this) };
      this._mediaEl = null;
    }

    init() {
      this._attachMedia();

      document.addEventListener('keydown', this._bound.onKeyDown);
    }

    destroy() {
      this._detachMedia();
      document.removeEventListener('keydown', this._bound.onKeyDown);
    }

    _attachMedia() {
      this._detachMedia();
      const audio = PlaybackRepository.getAudio();
      const video = PlaybackRepository.getVideo();

      this._mediaEl = video || audio;
      if (this._mediaEl) {
        this._mediaEl.addEventListener('ended', this._bound.onEnded);
        this._mediaEl.addEventListener('play', this._bound.onPlay);
        this._mediaEl.addEventListener('pause', this._bound.onPause);
      }
    }

    _detachMedia() {
      if (this._mediaEl) {
        this._mediaEl.removeEventListener('ended', this._bound.onEnded);
        this._mediaEl.removeEventListener('play', this._bound.onPlay);
        this._mediaEl.removeEventListener('pause', this._bound.onPause);
        this._mediaEl = null;
      }
    }

    /* ---- control actions ---- */

    play() {
      const el = PlaybackRepository.getAudio();
      if (el) el.play().catch(() => {});
    }

    pause() {
      const el = PlaybackRepository.getAudio();
      if (el) el.pause();
    }

    stop() {
      const el = PlaybackRepository.getAudio();
      if (el) {
        el.pause();
        el.currentTime = 0;
      }
      PlaybackRepository.savePlayingState('0');
      this._updateViewStop();
    }

    previous() {
      const current = PlaybackRepository.getCurrentSongId();
      const prev = PlaybackCalculator.calculatePreviousId(current);

      const type = PlaybackRepository.getCurrentSongType();
      if (type === '1') {
        window.location.href = '/player/' + prev;
      } else {
        window.location.href = '/play/' + prev;
      }
    }

    next() {
      const current = PlaybackRepository.getCurrentSongId();
      const max = PlaybackRepository.getMaxSongId();
      const next = PlaybackCalculator.calculateNextId(current, max);

      const type = PlaybackRepository.getCurrentSongType();
      if (type === '1') {
        window.location.href = '/player/' + next;
      } else {
        window.location.href = '/play/' + next;
      }
    }

    /* ---- internal handlers ---- */

    _onEnded() {
      this.next();
    }

    _onPlay() {
      PlaybackRepository.savePlayingState('1');
      if (window.PlaybackView) {
        PlaybackView.updatePlayingState('play');
      }
    }

    _onPause() {
      PlaybackRepository.savePlayingState('0');
      if (window.PlaybackView) {
        PlaybackView.updatePlayingState('pause');
      }
    }

    _onKeyDown(e) {
      if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

      switch (e.code) {
        case 'MediaPlayPause':
          e.preventDefault();
          const el = PlaybackRepository.getAudio();
          if (el) {
            el.paused ? el.play().catch(() => {}) : el.pause();
          }
          break;
        case 'MediaStop':
          e.preventDefault();
          this.stop();
          break;
        case 'MediaTrackPrevious':
        case 'MediaRewind':
          e.preventDefault();
          this.previous();
          break;
        case 'MediaTrackNext':
        case 'MediaFastForward':
          e.preventDefault();
          this.next();
          break;
      }
    }

    _updateViewStop() {
      if (window.PlaybackView) {
        PlaybackView.updatePlayingState('stop');
        PlaybackView.updateAlbumArt();
      }
    }
  }

  /* ------------------------------------------------------------
   * L3 — PlaybackView (DOM rendering)
   * ------------------------------------------------------------ */
  const PlaybackView = {
    _albumArtSelector: '#footer_songimages',
    _playBtnSelector: '.c-footer__playctrl2[alt="Oynat"]',
    _pauseBtnSelector: '.c-footer__playctrl2[alt="Duraklat"]',
    _defaultAlbumArt: '/Image/background/bkimage1.png',
    _playIcon: '/Image/res-pink/play.png',
    _pauseIcon: '/Image/res-pink/media-pause.png',

    configure(config) {
      if (config.albumArtSelector) this._albumArtSelector = config.albumArtSelector;
      if (config.playBtnSelector) this._playBtnSelector = config.playBtnSelector;
      if (config.pauseBtnSelector) this._pauseBtnSelector = config.pauseBtnSelector;
      if (config.defaultAlbumArt) this._defaultAlbumArt = config.defaultAlbumArt;
      if (config.playIcon) this._playIcon = config.playIcon;
      if (config.pauseIcon) this._pauseIcon = config.pauseIcon;
    },

    updateAlbumArt() {
      const container = document.querySelector(this._albumArtSelector);
      if (!container) return;

      const img = container.querySelector('img') || container;
      img.src = this._defaultAlbumArt;
    },

    updatePlayingState(state) {
      const playBtn = document.querySelector(this._playBtnSelector);
      const pauseBtn = document.querySelector(this._pauseBtnSelector);
      if (!playBtn || !pauseBtn) return;

      const isPlaying = (state === 'play' || state === 'playing');
      playBtn.style.display = isPlaying ? 'none' : '';
      pauseBtn.style.display = isPlaying ? '' : 'none';
    },

    showPlayingPopup(playing) {
      const popup = document.querySelector('.playing-popup');
      if (!popup) return;

      if (playing) {
        popup.classList.remove('playindis-hidden');
      } else {
        popup.classList.add('playindis-hidden');
      }
    },

    getMediaElement() {
      const override = PlaybackRepository._mediaElement;
      if (override) return override;
      const type = PlaybackRepository.getCookie('MM_PlaybackType');
      if (type === '1') {
        const v = document.getElementById('main-video');
        if (v) return v;
      }
      return document.getElementById('audio') || document.getElementById('main-audio');
    },
  };

  /* ------------------------------------------------------------
   * Exports
   * ------------------------------------------------------------ */
  const instance = new PlaybackController();

  window.PlaybackRepository = PlaybackRepository;

  window.mplay = instance.play.bind(instance);
  window.mpause = instance.pause.bind(instance);
  window.mstop = instance.stop.bind(instance);
  window.mgeri = instance.previous.bind(instance);
  window.mileri = instance.next.bind(instance);

  window.PlaybackController = instance;
  window.PlaybackView = PlaybackView;
})();
