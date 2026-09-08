<?php declare(strict_types=1);
/**
 * CoreMusic Home — Full-Screen Music Player
 *
 * Layout: Fullscreen with background artist image
 *   Left: Full-screen album art / video background
 *   Right: Playlist sidebar (307px)
 *   Bottom: Mini player with controls
 *
 * Platform: 1024×600 Embedded (RPi5) baseline with CSS responsive scaling
 *
 * Version: 1.0.0 — 2026-09-03
 */

if (!class_exists(CoreMusic\Device\DeviceManager::class)) {
    if (file_exists(__DIR__ . '/../../shared/src/Device/DeviceManager.php')) {
        require_once __DIR__ . '/../../shared/src/Device/DeviceManager.php';
    } elseif (file_exists(__DIR__ . '/../shared/src/Device/DeviceManager.php')) {
        require_once __DIR__ . '/../shared/src/Device/DeviceManager.php';
    }
}
use CoreMusic\Device\DeviceManager;

// ── DeviceManager oluştur ──
if (!isset($dm)) {
    $dm = DeviceManager::fromRequest(
        viewportW: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        viewportH: (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
        viewMode:  'player',
        isAuth:    ($_SESSION['MM_Username'] ?? '') !== '',
    );
}

// ── Session verileri ──
$username = htmlspecialchars($_SESSION['MM_Username'] ?? '', ENT_QUOTES, 'UTF-8');
$email    = htmlspecialchars($_SESSION['MM_Email'] ?? '', ENT_QUOTES, 'UTF-8');
$gender   = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['MM_Gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$isAuth   = $username !== '';

$currentSong   = htmlspecialchars($_SESSION['current_song'] ?? 'Göksel - Sevil Neşelen', ENT_QUOTES, 'UTF-8');
$currentAlbum  = htmlspecialchars($_SESSION['current_album'] ?? 'Hayat Rüya Gibi', ENT_QUOTES, 'UTF-8');
$currentArtist = htmlspecialchars($_SESSION['current_artist'] ?? 'Göksel', ENT_QUOTES, 'UTF-8');
$currentArt    = $_SESSION['current_art'] ?? ASSETS_URL . '/Image/res-pink/album-kursat.png';

// ── Device profile & View Context ──
$deviceProfile = $dm->deviceProfile();
$viewContext = [
    'deviceProfile' => $deviceProfile,
    'dm'            => $dm,
    'viewMode'      => 'player',
    'isTouch'       => $dm->isTouch(),
    'isWide'        => $dm->isWide(),
    'isLarge'       => $dm->isLarge(),
];

// ── Assets URL ──
$assetsUrl = ASSETS_URL;
$nonce     = htmlspecialchars((string)($_SESSION['csp_nonce'] ?? ''), ENT_QUOTES, 'UTF-8');

// ── Playlist verisi (örnek) ──
$playlist = [
    ['title' => 'Göksel - Sevil Neşelen', 'artist' => 'Göksel', 'duration' => '04:32', 'art' => $assetsUrl . '/Image/res-pink/album-goksel.png'],
    ['title' => 'Göksel - Kabahat Senin Se...', 'artist' => 'Göksel', 'duration' => '03:45', 'art' => $assetsUrl . '/Image/res-pink/album-goksel.png'],
    ['title' => 'Barış Manço - Gülpembe', 'artist' => 'Barış Manço', 'duration' => '05:12', 'art' => $assetsUrl . '/Image/res-pink/album-kursat.png'],
    ['title' => 'Kış Masalı Enstrümental', 'artist' => 'Org Dersleri', 'duration' => '06:30', 'art' => $assetsUrl . '/Image/res-pink/album-kursat.png'],
    ['title' => 'Tarkan - Şımarık', 'artist' => 'Tarkan', 'duration' => '04:15', 'art' => $assetsUrl . '/Image/res-pink/album-kursat.png'],
    ['title' => 'MFÖ - Güllerin Günlüğü', 'artist' => 'MFÖ', 'duration' => '03:58', 'art' => $assetsUrl . '/Image/res-pink/album-kursat.png'],
    ['title' => 'Sezen Aksu - Gülümse', 'artist' => 'Sezen Aksu', 'duration' => '04:22', 'art' => $assetsUrl . '/Image/res-pink/album-kursat.png'],
    ['title' => 'Ajda Pekkan - İstanbul', 'artist' => 'Ajda Pekkan', 'duration' => '03:48', 'art' => $assetsUrl . '/Image/res-pink/album-kursat.png'],
];
$trackCount = count($playlist);
?>

<?php require __DIR__ . '/../header.php'; ?>

<!-- ============================================================
     FULLSCREEN MUSIC PLAYER — Pattern 3: Fullscreen
     Background: Artist image (cover)
     Left: Album art / visual
     Right: Playlist sidebar
     Bottom: Mini player with controls
     ============================================================ -->
<main class="page-player <?= $dm->allClasses() ?> page-player--<?= $dm->device() ?>" role="main" aria-label="Müzik Çalar" <?= $dm->dataAttributes() ?>>

    <!-- Background Image Layer -->
    <div class="player-bg" aria-hidden="true">
        <img src="<?= $currentArt ?>" alt="" class="player-bg__image" loading="eager">
        <div class="player-bg__overlay"></div>
    </div>

    <!-- Back Button -->
    <button class="player-back" type="button" aria-label="Geri" data-action="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

    <!-- Main Content -->
    <div class="player-content">

        <!-- Left: Album Art / Visual -->
        <div class="player-visual">
            <div class="player-visual__art">
                <img src="<?= $currentArt ?>" alt="<?= $currentSong ?>" class="player-visual__image" loading="eager">
                <div class="player-visual__glow"></div>
            </div>
        </div>

        <!-- Right: Playlist Sidebar -->
        <div class="player-playlist" role="region" aria-label="Çalma Listesi">
            <div class="player-playlist__header">
                <h2 class="player-playlist__title">Şimdi Oynatılıyor</h2>
                <span class="player-playlist__count"><?= $trackCount ?> şarkı</span>
            </div>

            <div class="player-playlist__list" role="list">
                <?php foreach ($playlist as $index => $track): ?>
                <div class="player-playlist__item <?= $index === 0 ? 'is-active' : '' ?>" role="listitem" data-index="<?= $index ?>" tabindex="0">
                    <img src="<?= htmlspecialchars($track['art'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="player-playlist__thumb" loading="lazy">
                    <div class="player-playlist__info">
                        <span class="player-playlist__track-title"><?= htmlspecialchars($track['title'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="player-playlist__track-artist"><?= htmlspecialchars($track['artist'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="player-playlist__duration"><?= htmlspecialchars($track['duration'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Mini Player Bar (Bottom) -->
    <div class="player-mini" role="region" aria-label="Çalma Kontrolleri">
        <div class="player-mini__seek">
            <input type="range" min="0" max="100" value="0" step="0.1" class="player-mini__seek-input" id="playerSeekbar" aria-label="Şarkı pozisyonu">
            <div class="player-mini__seek-progress" id="seekProgress" style="width: 0%;"></div>
            <span class="player-mini__time player-mini__time--current" id="playerCurrentTime">00:00</span>
            <span class="player-mini__time player-mini__time--total" id="playerTotalTime">04:32</span>
        </div>

        <div class="player-mini__info">
            <img src="<?= $currentArt ?>" alt="" class="player-mini__thumb" loading="eager">
            <div class="player-mini__meta">
                <span class="player-mini__song" id="playerSongTitle"><?= $currentSong ?></span>
                <span class="player-mini__artist" id="playerSongArtist"><?= $currentArtist ?></span>
            </div>
        </div>

        <div class="player-mini__controls">
            <button class="player-mini__btn" type="button" aria-label="Tekrarla" data-action="repeat" title="Tekrarla">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="17 1 21 5 17 9"></polyline>
                    <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                    <polyline points="7 23 3 19 7 15"></polyline>
                    <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                </svg>
            </button>

            <button class="player-mini__btn" type="button" aria-label="Önceki şarkı" data-action="prev" title="Önceki">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="19 20 9 12 19 4 19 20"></polygon>
                    <line x1="5" y1="19" x2="5" y2="5" stroke="currentColor" stroke-width="2"></line>
                </svg>
            </button>

            <button class="player-mini__btn player-mini__btn--play" type="button" aria-label="Oynat" data-action="play" id="playerPlayBtn" title="Oynat">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" id="playIcon">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" id="pauseIcon" style="display:none;">
                    <rect x="6" y="4" width="4" height="16"></rect>
                    <rect x="14" y="4" width="4" height="16"></rect>
                </svg>
            </button>

            <button class="player-mini__btn" type="button" aria-label="Sonraki şarkı" data-action="next" title="Sonraki">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 4 15 12 5 20 5 4"></polygon>
                    <line x1="19" y1="5" x2="19" y2="19" stroke="currentColor" stroke-width="2"></line>
                </svg>
            </button>

            <button class="player-mini__btn" type="button" aria-label="Karıştır" data-action="shuffle" title="Karıştır">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 3 21 3 21 8"></polyline>
                    <line x1="4" y1="20" x2="21" y2="3"></line>
                    <polyline points="21 16 21 21 16 21"></polyline>
                    <line x1="15" y1="15" x2="21" y2="21"></line>
                    <line x1="4" y1="4" x2="9" y2="9"></line>
                </svg>
            </button>
        </div>

        <div class="player-mini__volume">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
            </svg>
            <input type="range" min="0" max="100" value="80" step="1" class="player-mini__volume-input" id="playerVolume" aria-label="Ses seviyesi">
        </div>
    </div>

</main>

<script nonce="<?= $nonce ?>">
document.addEventListener('DOMContentLoaded', function () {
    // ── Player State ──
    const state = {
        isPlaying: false,
        currentTrack: 0,
        currentTime: 0,
        duration: 272, // 04:32 in seconds
        volume: 80,
        repeat: false,
        shuffle: false,
    };

    // ── DOM Elements ──
    const playBtn = document.getElementById('playerPlayBtn');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const seekbar = document.getElementById('playerSeekbar');
    const seekProgress = document.getElementById('seekProgress');
    const currentTimeEl = document.getElementById('playerCurrentTime');
    const totalTimeEl = document.getElementById('playerTotalTime');
    const songTitleEl = document.getElementById('playerSongTitle');
    const songArtistEl = document.getElementById('playerSongArtist');
    const volumeSlider = document.getElementById('playerVolume');
    const playlistItems = document.querySelectorAll('.player-playlist__item');

    // ── Playlist Data ──
    const tracks = <?= json_encode($playlist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    // ── Format Time ──
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }

    // ── Update Player UI ──
    function updatePlayer() {
        const track = tracks[state.currentTrack];
        if (!track) return;

        songTitleEl.textContent = track.title;
        songArtistEl.textContent = track.artist;
        totalTimeEl.textContent = track.duration;

        // Update album art
        const bgImage = document.querySelector('.player-bg__image');
        const visualImage = document.querySelector('.player-visual__image');
        const miniThumb = document.querySelector('.player-mini__thumb');
        if (bgImage) bgImage.src = track.art;
        if (visualImage) visualImage.src = track.art;
        if (miniThumb) miniThumb.src = track.art;

        // Update active playlist item
        playlistItems.forEach(function (item, index) {
            item.classList.toggle('is-active', index === state.currentTrack);
        });

        // Parse duration
        const parts = track.duration.split(':');
        state.duration = (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
        state.currentTime = 0;
        updateSeekBar();
    }

    // ── Update Seek Bar ──
    function updateSeekBar() {
        const progress = (state.currentTime / state.duration) * 100;
        seekProgress.style.width = progress + '%';
        seekbar.value = progress;
        currentTimeEl.textContent = formatTime(state.currentTime);
    }

    // ── Play/Pause Toggle ──
    function togglePlay() {
        state.isPlaying = !state.isPlaying;
        playIcon.style.display = state.isPlaying ? 'none' : 'block';
        pauseIcon.style.display = state.isPlaying ? 'block' : 'none';
        playBtn.setAttribute('aria-label', state.isPlaying ? 'Durdur' : 'Oynat');
        playBtn.setAttribute('title', state.isPlaying ? 'Durdur' : 'Oynat');
    }

    // ── Play Button Click ──
    playBtn.addEventListener('click', togglePlay);

    // ── Previous Track ──
    function prevTrack() {
        if (state.currentTime > 3) {
            state.currentTime = 0;
        } else {
            state.currentTrack = (state.currentTrack - 1 + tracks.length) % tracks.length;
        }
        updatePlayer();
    }

    // ── Next Track ──
    function nextTrack() {
        if (state.shuffle) {
            state.currentTrack = Math.floor(Math.random() * tracks.length);
        } else {
            state.currentTrack = (state.currentTrack + 1) % tracks.length;
        }
        updatePlayer();
    }

    // ── Seek Bar Interaction ──
    seekbar.addEventListener('input', function () {
        const percent = parseFloat(this.value);
        state.currentTime = (percent / 100) * state.duration;
        updateSeekBar();
    });

    // ── Volume Control ──
    volumeSlider.addEventListener('input', function () {
        state.volume = parseInt(this.value, 10);
    });

    // ── Playlist Item Click ──
    playlistItems.forEach(function (item, index) {
        item.addEventListener('click', function () {
            state.currentTrack = index;
            state.currentTime = 0;
            updatePlayer();
            if (!state.isPlaying) {
                togglePlay();
            }
        });
        item.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                state.currentTrack = index;
                state.currentTime = 0;
                updatePlayer();
                if (!state.isPlaying) {
                    togglePlay();
                }
            }
        });
    });

    // ── Control Button Actions ──
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        switch (action) {
            case 'prev':
                prevTrack();
                break;
            case 'next':
                nextTrack();
                break;
            case 'repeat':
                state.repeat = !state.repeat;
                btn.classList.toggle('is-active', state.repeat);
                break;
            case 'shuffle':
                state.shuffle = !state.shuffle;
                btn.classList.toggle('is-active', state.shuffle);
                break;
            case 'goBack':
                window.history.back();
                break;
        }
    });

    // ── Keyboard Shortcuts ──
    document.addEventListener('keydown', function (e) {
        if (e.target.tagName === 'INPUT') return;

        switch (e.code) {
            case 'Space':
                e.preventDefault();
                togglePlay();
                break;
            case 'ArrowLeft':
                e.preventDefault();
                state.currentTime = Math.max(0, state.currentTime - 5);
                updateSeekBar();
                break;
            case 'ArrowRight':
                e.preventDefault();
                state.currentTime = Math.min(state.duration, state.currentTime + 5);
                updateSeekBar();
                break;
            case 'ArrowUp':
                e.preventDefault();
                state.volume = Math.min(100, state.volume + 5);
                volumeSlider.value = state.volume;
                break;
            case 'ArrowDown':
                e.preventDefault();
                state.volume = Math.max(0, state.volume - 5);
                volumeSlider.value = state.volume;
                break;
            case 'KeyN':
                nextTrack();
                break;
            case 'KeyP':
                prevTrack();
                break;
        }
    });

    // ── Simulate Playback (Demo) ──
    let playbackInterval = null;

    function startPlayback() {
        if (playbackInterval) clearInterval(playbackInterval);
        playbackInterval = setInterval(function () {
            if (!state.isPlaying) return;

            state.currentTime += 0.1;
            if (state.currentTime >= state.duration) {
                if (state.repeat) {
                    state.currentTime = 0;
                } else {
                    nextTrack();
                }
            }
            updateSeekBar();
        }, 100);
    }

    // ── Initialize ──
    updatePlayer();
    startPlayback();
});
</script>

<?php require __DIR__ . '/../footer.php'; ?>
