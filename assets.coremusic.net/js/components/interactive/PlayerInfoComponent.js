/**
 * PlayerInfoComponent — PHP-rendered player info'ya JS interactivity ekler.
 *
 * PHP tarafı HTML'i render eder, bu sınıf sadece davranış ekler:
 * - Progress bar tıklama ile seek
 * - Real-time süre güncelleme
 * - Cover image lazy loading fallback
 * - Keyboard shortcuts (space = play/pause)
 *
 * HTML contract (PHP tarafından render edilir):
 * <player_info data-cm-component="cm-player-info" data-cm-config='{"song":"...","progress":30}'>
 *   <div class="player-info__progress">...</div>
 * </player_info>
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

export default class PlayerInfoComponent extends ComponentBase {
    /** @type {HTMLElement|null} Progress bar */
    #progressBar = null;

    /** @type {HTMLElement|null} Progress fill */
    #progressFill = null;

    /** @type {number} Current progress % */
    #progress = 0;

    defaultState() {
        return {
            song: '',
            artist: '',
            progress: 0,
            isPlaying: false,
        };
    }

    init() {
        this.#progressBar = this.$('.player-info__progress') || this.$('.media-progress__bar');
        this.#progressFill = this.$('.player-info__progress__fill') || this.$('.media-progress__fill');

        // State'i config'den yükle
        try {
            const config = JSON.parse(this.el?.dataset?.cmConfig || '{}');
            this.#progress = config.progress ?? 0;
        } catch { /* ignore */ }
    }

    mount() {
        // Progress bar tıklama → seek
        if (this.#progressBar) {
            this.on(this.#progressBar, 'click', (e) => {
                const rect = this.#progressBar.getBoundingClientRect();
                const pct = Math.round(((e.clientX - rect.left) / rect.width) * 100);
                this.setProgress(pct);
                this.emit('cm:player:seek', { progress: pct });
            });
        }

        // Keyboard: Space = play/pause
        this.on(document, 'keydown', (e) => {
            if (e.key === ' ' && e.target === document.body) {
                e.preventDefault();
                this.togglePlay();
            }
        });

        // Cover image error fallback
        const coverImg = this.$('.player-info__cover img, .now-playing__art img');
        if (coverImg) {
            this.on(coverImg, 'error', () => {
                coverImg.src = '/Image/res-pink/album-goksel.png';
            });
        }
    }

    /**
     * Progress bar'ı günceller.
     * @param {number} pct — 0-100 arası yüzde
     */
    setProgress(pct) {
        this.#progress = Math.max(0, Math.min(100, pct));
        if (this.#progressFill) {
            this.#progressFill.style.width = `${this.#progress}%`;
        }
        // ARIA güncelle
        const progressEl = this.$('[role="progressbar"]');
        if (progressEl) {
            progressEl.setAttribute('aria-valuenow', String(this.#progress));
        }
        this.setState({ progress: this.#progress });
    }

    /**
     * Play/pause toggle.
     */
    togglePlay() {
        const isPlaying = !this.state.isPlaying;
        this.setState({ isPlaying });
        this.emit('cm:player:toggle', { isPlaying });

        // Play/pause ikonunu güncelle
        const playIcon = this.$('.player-info__text-img[src*="play"]');
        if (playIcon) {
            playIcon.alt = isPlaying ? 'Duraklat' : 'Oynat';
        }
    }

    /**
     * Şarkı bilgisini günceller (EventBus veya polling ile).
     * @param {object} data — { song, artist, album, elapsed, duration }
     */
    updateTrack(data) {
        if (data.song) {
            const titleEl = this.$('.player-info__title .now-playing__meta-value, .now-playing__title');
            if (titleEl) titleEl.textContent = data.song;
        }
        if (data.artist) {
            const artistEl = this.$('.player-info__singer .now-playing__meta-value, .now-playing__artist');
            if (artistEl) artistEl.textContent = data.artist;
        }
        if (data.elapsed) {
            const elapsedEl = this.$('#np_time_current, .media-progress__time');
            if (elapsedEl) elapsedEl.textContent = data.elapsed;
        }
        if (data.duration) {
            const durationEl = this.$('#np_time_total, .media-progress__time--total');
            if (durationEl) durationEl.textContent = data.duration;
        }
        this.setState(data);
    }

    destroy() {
        this.#progressBar = null;
        this.#progressFill = null;
        super.destroy();
    }
}
