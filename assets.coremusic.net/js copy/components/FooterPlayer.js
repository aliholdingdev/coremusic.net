/**
 * CoreMusic — Footer Player Controller (v1.0)
 * Figma SSOT: node-id=1639-9773 (1024: 1025×90) / child[1] of 2831-13747 (1920: 1923×92)
 * BEM: .footer-player, .footer-player__*
 * Layer: L3 Presentation · JS Module
 * Version: 1.0.0 — 2026-09-22
 *
 * Footer player bar'ı kontrol eder:
 *   - Play/Pause/Stop/Next/Prev
 *   - Seek bar (Figma 1025×4.8)
 *   - Volume control (Figma 186×14 / 353×14)
 *   - Track metadata display
 */

'use strict';

/**
 * FooterPlayer — Footer player bar controller
 */
class FooterPlayer {
  /** @type {HTMLElement|null} */
  #footer = null;

  /** @type {Object} */
  #elements = {};

  /** @type {Object} */
  #state = {
    playing: false,
    progress: 0,
    volume: 80,
    currentSong: '',
    currentAlbum: '',
    currentArtist: '',
    elapsed: '00:00:00',
    duration: '00:05:00',
  };

  /**
   * @param {HTMLElement} footer — .footer-player elementi
   */
  constructor(footer) {
    this.#footer = footer;
    this.#cacheElements();
    this.#bindEvents();
  }

  /** DOM elementlerini cache'le */
  #cacheElements() {
    if (!this.#footer) return;

    this.#elements = {
      playBtn: this.#footer.querySelector('#playBtn'),
      pauseBtn: this.#footer.querySelector('#pauseBtn'),
      prevBtn: this.#footer.querySelector('#prevBtn'),
      nextBtn: this.#footer.querySelector('#nextBtn'),
      stopBtn: this.#footer.querySelector('#stopBtn'),
      seekbar: this.#footer.querySelector('#seekbar'),
      seekbarBar: this.#footer.querySelector('#seekbar2'),
      volume: this.#footer.querySelector('#volume'),
      volumeFill: this.#footer.querySelector('#volume2'),
      songName: this.#footer.querySelector('#footer_songname'),
      albumName: this.#footer.querySelector('#footer_albumadi'),
      artistName: this.#footer.querySelector('#footer_sanatci'),
      elapsed: this.#footer.querySelector('#gettime_audio'),
      duration: this.#footer.querySelector('#footer_sure'),
      cover: this.#footer.querySelector('#footer_songimages'),
    };
  }

  /** Event listener'ları bağla */
  #bindEvents() {
    const el = this.#elements;

    // Play/Pause toggle
    if (el.playBtn) {
      el.playBtn.addEventListener('click', () => this.#togglePlay());
    }
    if (el.pauseBtn) {
      el.pauseBtn.addEventListener('click', () => this.#togglePlay());
    }

    // Previous
    if (el.prevBtn) {
      el.prevBtn.addEventListener('click', () => this.#emit('prev'));
    }

    // Next
    if (el.nextBtn) {
      el.nextBtn.addEventListener('click', () => this.#emit('next'));
    }

    // Stop
    if (el.stopBtn) {
      el.stopBtn.addEventListener('click', () => this.#emit('stop'));
    }

    // Seek bar
    if (el.seekbar) {
      el.seekbar.addEventListener('input', (e) => {
        const pct = parseFloat(e.target.value);
        this.setProgress(pct);
        this.#emit('seek', { percent: pct });
      });
    }

    // Volume
    if (el.volume) {
      el.volume.addEventListener('input', (e) => {
        const vol = parseFloat(e.target.value) * 100;
        this.setVolume(vol);
        this.#emit('volume', { percent: vol });
      });
    }
  }

  /** Play/Pause durumunu değiştir */
  #togglePlay() {
    this.#state.playing = !this.#state.playing;
    this.#updatePlayButton();
    this.#emit(this.#state.playing ? 'play' : 'pause');
  }

  /** Play butonu görünümünü güncelle */
  #updatePlayButton() {
    const el = this.#elements;
    if (el.playBtn && el.pauseBtn) {
      el.playBtn.classList.toggle('is-hidden', this.#state.playing);
      el.pauseBtn.classList.toggle('is-hidden', !this.#state.playing);
    }
  }

  /**
   * Progress bar'ı güncelle
   * @param {number} percent — 0-100
   */
  setProgress(percent) {
    const bar = this.#elements.seekbarBar;
    if (bar) {
      bar.style.width = `${Math.max(0, Math.min(100, percent))}%`;
    }
    this.#state.progress = percent;
  }

  /**
   * Volume'ı güncelle
   * @param {number} percent — 0-100
   */
  setVolume(percent) {
    const fill = this.#elements.volumeFill;
    if (fill) {
      fill.style.width = `${Math.max(0, Math.min(100, percent))}%`;
    }
    this.#state.volume = percent;
  }

  /**
   * Track metadata'yı güncelle
   * @param {Object} data — {title, album, artist, elapsed, duration, cover}
   */
  updateTrack(data) {
    const el = this.#elements;
    if (data.title && el.songName) el.songName.textContent = data.title;
    if (data.album && el.albumName) el.albumName.textContent = data.album;
    if (data.artist && el.artistName) el.artistName.textContent = data.artist;
    if (data.elapsed && el.elapsed) el.elapsed.textContent = data.elapsed;
    if (data.duration && el.duration) el.duration.textContent = data.duration;
    if (data.cover && el.cover) el.cover.src = data.cover;

    Object.assign(this.#state, data);
  }

  /**
   * Elapsed time'ı güncelle
   * @param {string} time — "00:00:00" formatında
   */
  setElapsed(time) {
    if (this.#elements.elapsed) {
      this.#elements.elapsed.textContent = time;
    }
    this.#state.elapsed = time;
  }

  /** Custom event emit */
  #emit(name, detail = {}) {
    this.#footer?.dispatchEvent(new CustomEvent(`footer:${name}`, { detail }));
  }

  /** Mevcut state'i döndür */
  getState() {
    return { ...this.#state };
  }
}

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { FooterPlayer };
}
if (typeof window !== 'undefined') {
  window.FooterPlayer = FooterPlayer;
}
