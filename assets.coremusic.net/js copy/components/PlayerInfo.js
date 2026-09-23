/**
 * CoreMusic — Player Info Component (v1.0)
 * Figma SSOT: node-id=1646-17727 (1024: 392×131) / node-id=2849-21489 (1920: 469×184)
 * BEM: .player-info, .player-info__cover, .player-info__title, etc.
 * Layer: L3 Presentation · JS Module
 * Version: 1.0.0 — 2026-09-22
 */

'use strict';

/**
 * PlayerInfo — Player info panel manager
 * Figma pixel-perfect ölçümlerle çalışır
 */
class PlayerInfo {
  /** @type {HTMLElement|null} */
  #container = null;

  /** @type {Object} */
  #state = {
    cover: '',
    title: '',
    album: '',
    artist: '',
    elapsed: '00:00',
    duration: '00:00',
    progress: 0,
  };

  /**
   * @param {HTMLElement} container — .player-info elementi
   */
  constructor(container) {
    this.#container = container;
    this.#bindEvents();
  }

  /** Event listener'ları bağla */
  #bindEvents() {
    if (!this.#container) return;

    // Progress bar tıklama
    const progress = this.#container.querySelector('.player-info__progress');
    if (progress) {
      progress.addEventListener('click', (e) => {
        const rect = progress.getBoundingClientRect();
        const pct = ((e.clientX - rect.left) / rect.width) * 100;
        this.setProgress(pct);
        this.#emit('seek', { percent: pct });
      });
    }
  }

  /**
   * Player bilgilerini güncelle
   * @param {Object} data — {cover, title, album, artist, elapsed, duration, progress}
   */
  update(data) {
    if (!this.#container) return;

    Object.assign(this.#state, data);

    const cover = this.#container.querySelector('.player-info__cover');
    const title = this.#container.querySelector('.player-info__title');
    const album = this.#container.querySelector('.player-info__album');
    const artist = this.#container.querySelector('.player-info__artist');
    const elapsed = this.#container.querySelector('.player-info__elapsed');
    const duration = this.#container.querySelector('.player-info__duration');
    const progressFill = this.#container.querySelector('.player-info__progress-fill');

    if (cover && data.cover) cover.src = data.cover;
    if (title && data.title) title.textContent = data.title;
    if (album && data.album) album.textContent = data.album;
    if (artist && data.artist) artist.textContent = data.artist;
    if (elapsed && data.elapsed) elapsed.textContent = data.elapsed;
    if (duration && data.duration) duration.textContent = data.duration;
    if (progressFill && data.progress !== undefined) {
      progressFill.style.width = `${data.progress}%`;
    }
  }

  /**
   * Progress bar'ı güncelle
   * @param {number} percent — 0-100
   */
  setProgress(percent) {
    const fill = this.#container?.querySelector('.player-info__progress-fill');
    if (fill) {
      fill.style.width = `${Math.max(0, Math.min(100, percent))}%`;
    }
    this.#state.progress = percent;
  }

  /**
   * Current time'ı güncelle
   * @param {string} elapsed — "00:00" formatında
   */
  setElapsed(elapsed) {
    const el = this.#container?.querySelector('.player-info__elapsed');
    if (el) el.textContent = elapsed;
    this.#state.elapsed = elapsed;
  }

  /** Custom event emit */
  #emit(name, detail) {
    this.#container?.dispatchEvent(new CustomEvent(`player-info:${name}`, { detail }));
  }

  /** Mevcut state'i döndür */
  getState() {
    return { ...this.#state };
  }
}

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { PlayerInfo };
}
if (typeof window !== 'undefined') {
  window.PlayerInfo = PlayerInfo;
}
