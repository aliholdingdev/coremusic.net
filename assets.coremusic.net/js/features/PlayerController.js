/**
 * CoreMusic — PlayerController
 * Footer player state machine. STOPPED/PLAYING/PAUSED.
 *
 * @module features/PlayerController
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class PlayerController {
    #eventBus;

    /** @type {'STOPPED'|'PLAYING'|'PAUSED'} */
    #status = 'STOPPED';
    #volume = 100;
    #shuffle = false;
    /** @type {'off'|'one'|'all'} */
    #repeat = 'off';

    /** SVG icon'lar (play, pause, stop) */
    static ICONS = {
        play: '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>',
        pause: '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>',
        stop: '<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="6" width="12" height="12"/></svg>',
    };

    /** @param {import('../core/EventBus.js').default} eventBus */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    get status() { return this.#status; }
    get volume() { return this.#volume; }

    init() {
        this.#bindButtons();
        this.#bindSeek();
        this.#bindVolume();
    }

    /** Player butonlarına tıklama dinleyicileri bağla */
    #bindButtons() {
        const footer = document.querySelector('.footer');
        if (!footer) return;

        footer.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;

            const action = btn.dataset.action;
            switch (action) {
                case 'play': this.#onPlay(); break;
                case 'stop': this.#onStop(); break;
                case 'prev': this.#onPrev(); break;
                case 'next': this.#onNext(); break;
                case 'shuffle': this.#onShuffle(); break;
                case 'repeat': this.#onRepeat(); break;
                case 'mute': this.#onMute(); break;
            }
        });
    }

    /** Seek bar tıklama */
    #bindSeek() {
        const seek = document.querySelector('.now-playing__seek');
        if (!seek) return;

        seek.addEventListener('click', (e) => {
            const rect = seek.getBoundingClientRect();
            const pct = ((e.clientX - rect.left) / rect.width) * 100;
            this.#eventBus.emit('player:seek', { percent: Math.max(0, Math.min(100, pct)) });

            const fill = seek.querySelector('.now-playing__seek-fill');
            if (fill) fill.style.width = `${pct}%`;
        });
    }

    /** Volume slider */
    #bindVolume() {
        const slider = document.querySelector('.footer__volume-slider');
        if (!slider) return;

        slider.addEventListener('input', (e) => {
            this.#volume = parseInt(e.target.value, 10);
            const display = document.querySelector('.footer__volume-value');
            if (display) display.textContent = `% ${this.#volume}`;
            this.#eventBus.emit('player:volume', { volume: this.#volume });
        });
    }

    /** Play / Pause toggle */
    #onPlay() {
        if (this.#status === 'PLAYING') {
            this.#status = 'PAUSED';
            this.#updatePlayButton('play');
            this.#eventBus.emit('player:pause');
        } else {
            this.#status = 'PLAYING';
            this.#updatePlayButton('pause');
            this.#eventBus.emit('player:play');
        }
    }

    /** Stop */
    #onStop() {
        this.#status = 'STOPPED';
        this.#updatePlayButton('play');
        this.#resetProgress();
        this.#eventBus.emit('player:stop');
    }

    #onPrev() { this.#eventBus.emit('player:prev'); }
    #onNext() { this.#eventBus.emit('player:next'); }

    #onShuffle() {
        this.#shuffle = !this.#shuffle;
        this.#eventBus.emit('player:shuffle', { shuffle: this.#shuffle });
    }

    #onRepeat() {
        const cycle = { off: 'one', one: 'all', all: 'off' };
        this.#repeat = cycle[this.#repeat];
        this.#eventBus.emit('player:repeat', { repeat: this.#repeat });
    }

    #onMute() {
        this.#volume = this.#volume > 0 ? 0 : 100;
        const slider = document.querySelector('.footer__volume-slider');
        if (slider) slider.value = this.#volume;
        const display = document.querySelector('.footer__volume-value');
        if (display) display.textContent = `% ${this.#volume}`;
        this.#eventBus.emit('player:volume', { volume: this.#volume });
    }

    /** Play butonu ikonunu güncelle */
    #updatePlayButton(icon) {
        const btn = document.querySelector('[data-action="play"]');
        if (!btn) return;
        btn.innerHTML = PlayerController.ICONS[icon];
        btn.setAttribute('aria-label', icon === 'pause' ? 'Duraklat' : 'Oynat');
    }

    /** Progress bar'ı sıfırla */
    #resetProgress() {
        const bar = document.querySelector('.footer__progress-bar');
        if (bar) {
            bar.style.width = '0%';
            bar.setAttribute('aria-valuenow', '0');
        }
        const fill = document.querySelector('.now-playing__seek-fill');
        if (fill) fill.style.width = '0%';
    }

    destroy() {
        /* Event listener'lar DOM unload'da otomatik temizlenir */
    }
}
