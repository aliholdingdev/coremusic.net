<?php declare(strict_types=1);
/**
 * pages/diskplayer.php — Full Page / Fullscreen Music Player (v1.0.0)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (_player.css)
 * SSOT     : Figma node 2135:34323 (1920×1080)
 *            "Laptop 1920 - Playlist Page full screen mode 2" (Kullanıcı Ek 1)
 * Layout   : Fullscreen Headless (z-index: 9999)
 *            - Üst: Geri butonu (pembe daire) + Şarkı Adı
 *            - Sol Alt: Frosted Glass Mini Kart (Albüm kapağı, Şarkı/Albüm/Sanatçı/Süre, Play butonu, Progress)
 *            - Sağ: 500px Genişliğinde Cam Playlist Sidebar (12+ şarkı)
 *            - En Alt: 5px Tam Genişlik Neon Pembe İlerleme Çubuğu
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Repository\MusicRepository;

/* --- DeviceManager --- */
$testViewportW = null;
if (isset($_GET['_test_resp']) && preg_match('/^(1024|1440|1920|3840)$/', (string)$_GET['_test_resp'])) {
    $testViewportW = (int)$_GET['_test_resp'];
}

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => $testViewportW ?: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

/* --- Yerel yardımcılar --- */
$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';

/* --- Saniye → HH:MM:SS --- */
$formatDuration = static function (mixed $seconds): string {
    if (!is_numeric($seconds)) {
        return '00:00:00';
    }
    $s = max(0, (int)$seconds);
    return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
};

/* --- Kuyruk verisi --- */
$tracks = [];
try {
    if (defined('DB_HOST')) {
        $idx = 0;
        foreach (MusicRepository::fromEnvironment()->findRecentTracks() as $row) {
            $hex = strtolower((string)($row['music_hex'] ?? ''));
            if ($hex === '') {
                continue;
            }
            $tracks[] = [
                't'      => (string)$row['title'],
                'album'  => (string)($row['album_name'] ?? 'Hayat Rüya Gibi'),
                'a'      => (string)$row['artist_name'],
                'd'      => $formatDuration($row['duration_sec'] ?? null),
                'stream' => '/stream/' . $hex,
                'active' => $idx === 0,
            ];
            $idx++;
        }
    }
} catch (\Throwable) {
    $tracks = [];
}

if ($tracks === []) {
    /* Figma 2135:34323 demo içeriği */
    $tracks = [
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => '', 'active' => true],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Barış Manço - Gülpembe', 'album' => 'Sözüm Meclisten Dışarı', 'a' => 'Barış Manço', 'd' => '00:04:01', 'stream' => ''],
        ['t' => 'Ferhat Göçer - Cennet', 'album' => 'Yolun Açık Olsun', 'a' => 'Ferhat Göçer', 'd' => '00:03:45', 'stream' => ''],
    ];
}

$coverUrl = $assetsUrl . '/Image/res-pink/album-goksel.png';
$bgUrl    = $assetsUrl . '/Image/background/romantic-bg-pink.png';

$currentTrack    = $tracks[0] ?? ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05'];
$currentSong     = (string)($currentTrack['t'] ?? 'Göksel - Sevil Neşelen');
$currentArtist   = (string)($currentTrack['a'] ?? 'Göksel');
$currentAlbum    = (string)($currentTrack['album'] ?? 'Hayat Rüya Gibi');
$currentDuration = (string)($currentTrack['d'] ?? '00:03:05');
$currentElapsed  = '00:00:00';
?>
<div class="page-player page-player--wide page-player--fullscreen page-player--diskplayer"
     id="diskplayer-root"
     <?= $dm->dataAttributes() ?>>

    <!-- ARKA PLAN KATMANI (Figma 2135:34323 — Tam Ekran Manzara Duvar Kağıdı) -->
    <div class="player-bg" aria-hidden="true">
        <img class="player-bg__image player-bg__image--scenic" src="<?= $h($bgUrl) ?>" alt="" loading="lazy">
        <div class="player-bg__overlay player-bg__overlay--subtle"></div>
    </div>

    <!-- ÜST BAR: Geri butonu + Şarkı Adı + Plak & Tam Ekran Araçları -->
    <header class="player-topbar player-topbar--wide">
        <div class="player-topbar__left">
            <a class="player-back player-back--wide" href="/nowplaying" aria-label="Şimdi Oynatılıyor sayfasına dön" title="Şimdi Oynatılıyor">
                <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20z" fill="currentColor"/>
                </svg>
            </a>
            <h1 class="player-topbar__song-title" id="dp-topbar-title"><?= $h($currentSong) ?></h1>
        </div>
        <div class="player-topbar__right">
            <button type="button" class="player-toolbar__btn player-toolbar__btn--toggle-vinyl is-active" id="dp-toggle-vinyl" aria-label="Vinil Plak Aç / Kapat" title="Vinil Plak Göster / Gizle">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z" fill="currentColor"/>
                </svg>
                <span class="dp-toggle-text">Plak Modu</span>
            </button>
            <button type="button" class="player-toolbar__btn player-toolbar__btn--fs" id="dp-btn-fs" aria-label="Tam Ekran" title="Tam Ekran">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                    <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" fill="currentColor"/>
                </svg>
            </button>
        </div>
    </header>

    <!-- ORTA İÇERİK: Dönen Disk (Merkez) + Sol Alt Mini Kart + Sağ 500px Playlist -->
    <div class="player-content player-content--wide player-content--diskplayer">

        <!-- MERKEZ: Dönen Vinil Plak Diski (Vinyl Record Disk — Toggleable) -->
        <div class="player-visual player-visual--vinyl-center" id="dp-vinyl-container" aria-hidden="true">
            <div class="player-visual__vinyl-wrap">
                <div class="player-visual__vinyl is-playing" id="dp-vinyl">
                    <div class="player-visual__vinyl-grooves"></div>
                    <div class="player-visual__art">
                        <img class="player-visual__image" id="dp-vinyl-img" src="<?= $h($coverUrl) ?>" alt="Albüm Kapağı" loading="lazy">
                        <div class="player-visual__center-hole"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SOL ALT: Player Info Div Mini (Figma node I2135:35489;894:15744 — 435×180 Frosted Glass) -->
        <div class="player-mini-card" aria-label="Şu an çalan şarkı bilgisi">
            <div class="player-mini-card__main">
                <div class="player-mini-card__cover">
                    <img id="dp-card-cover" src="<?= $h($coverUrl) ?>" alt="Albüm kapağı" width="90" height="90" loading="lazy">
                </div>
                <div class="player-mini-card__details">
                    <p class="player-mini-card__row">
                        <span class="player-mini-card__label">
                            <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" fill="currentColor"/>
                            </svg>
                            Şarkı Adı :
                        </span>
                        <span class="player-mini-card__value" id="dp-card-title"><?= $h($currentSong) ?></span>
                    </p>
                    <p class="player-mini-card__row">
                        <span class="player-mini-card__label">
                            <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z" fill="currentColor"/>
                            </svg>
                            Albüm :
                        </span>
                        <span class="player-mini-card__value" id="dp-card-album"><?= $h($currentAlbum) ?></span>
                    </p>
                    <p class="player-mini-card__row">
                        <span class="player-mini-card__label">
                            <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z" fill="currentColor"/>
                            </svg>
                            Sanatçı :
                        </span>
                        <span class="player-mini-card__value" id="dp-card-artist"><?= $h($currentArtist) ?></span>
                    </p>
                    <p class="player-mini-card__row">
                        <span class="player-mini-card__label">
                            <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill="currentColor"/>
                            </svg>
                            Süre :
                        </span>
                        <span class="player-mini-card__value" id="dp-card-time"><?= $h($currentElapsed) ?> / <?= $h($currentDuration) ?></span>
                    </p>
                    <div class="player-mini-card__transport">
                        <button type="button" class="player-mini-card__play" id="dp-play-btn" aria-label="Oynat / Duraklat">
                            <svg id="dp-play-icon" viewBox="0 0 24 24" width="12" height="12" focusable="false" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>
                        </button>
                        <div class="player-mini-card__progress" id="dp-progress" role="progressbar" aria-label="Şarkı süresi" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                            <div class="player-mini-card__progress-fill" id="dp-progress-fill"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alt rozetler (MP3 + 5 Yıldız) -->
            <div class="player-mini-card__bottom-meta">
                <span class="player-mini-card__badge-mp3">MP3</span>
                <span class="player-mini-card__stars" role="img" aria-label="5 yıldız">
                    <?php for ($s = 0; $s < 5; $s++): ?>
                        <svg class="player-mini-card__star-icon" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="currentColor"/></svg>
                    <?php endfor; ?>
                </span>
            </div>
        </div>

        <!-- SAĞ: Playlist Sidebar (Figma node 2135:35315 — 500×988 Frosted Glass) -->
        <aside class="player-playlist player-playlist--wide" aria-label="Playlist">
            <div class="player-playlist__head-wide" aria-hidden="true">
                <span class="player-playlist__col-icon">/</span>
                <span class="player-playlist__col-title">Şarkı Adı</span>
                <span class="player-playlist__col-dur">Süre</span>
            </div>
            <div class="player-playlist__list player-playlist__list--wide" id="dp-playlist-list">
                <?php foreach ($tracks as $idx => $track): ?>
                    <?php
                    $stream = (string)($track['stream'] ?? '');
                    $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                        ? ' data-stream="' . $h($stream) . '"'
                        : '';
                    $isActive = !empty($track['active']);
                    ?>
                    <a class="player-playlist__item player-playlist__item--wide <?= $isActive ? 'player-playlist__item--active' : '' ?>"
                       href="javascript:void(0)"
                       <?= $streamAttr ?>
                       data-title="<?= $h((string)$track['t']) ?>"
                       data-artist="<?= $h((string)$track['a']) ?>"
                       data-album="<?= $h((string)($track['album'] ?? '')) ?>"
                       data-duration="<?= $h((string)$track['d']) ?>"
                       aria-label="<?= $h('Oynat: ' . $track['t'] . ' — ' . $track['a']) ?>">
                        <img class="player-playlist__thumb player-playlist__thumb--wide"
                             src="<?= $h($assetsUrl . '/Image/res-pink/default-album.png') ?>"
                             alt=""
                             width="34"
                             height="30"
                             loading="lazy">
                        <span class="player-playlist__track-title player-playlist__track-title--wide"><?= $h((string)$track['t']) ?></span>
                        <span class="player-playlist__duration player-playlist__duration--wide"><?= $h((string)$track['d']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

    </div>

    <!-- EN ALT: Tam genişlik 5px neon pembe ilerleme çubuğu (Figma node 2135:35497) -->
    <div class="player-bottom-progress" role="progressbar" aria-label="Şarkı ilerleme çubuğu" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
        <div class="player-bottom-progress__fill" id="dp-bottom-fill"></div>
    </div>
</div>

<!-- DiskPlayer İstemci Etkileşim Skripti -->
<script>
(function() {
    var vinyl = document.getElementById('dp-vinyl');
    var playBtn = document.getElementById('dp-play-btn');
    var playIcon = document.getElementById('dp-play-icon');
    var topbarTitle = document.getElementById('dp-topbar-title');
    var cardTitle = document.getElementById('dp-card-title');
    var cardArtist = document.getElementById('dp-card-artist');
    var cardAlbum = document.getElementById('dp-card-album');
    var cardTime = document.getElementById('dp-card-time');
    var playlistList = document.getElementById('dp-playlist-list');
    var isPlaying = true;

    function setPlayingState(playing) {
        isPlaying = playing;
        if (vinyl) {
            if (playing) {
                vinyl.classList.add('is-playing');
                vinyl.style.animationPlayState = 'running';
            } else {
                vinyl.classList.remove('is-playing');
                vinyl.style.animationPlayState = 'paused';
            }
        }
        if (playIcon) {
            playIcon.innerHTML = playing
                ? '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" fill="currentColor"/>' // pause
                : '<path d="M8 5v14l11-7z" fill="currentColor"/>'; // play
        }
    }

    if (playBtn) {
        playBtn.addEventListener('click', function(e) {
            e.preventDefault();
            setPlayingState(!isPlaying);
            if (typeof window.togglePlayPause === 'function') {
                window.togglePlayPause();
            }
        });
    }

    if (playlistList) {
        playlistList.addEventListener('click', function(e) {
            var item = e.target.closest('.player-playlist__item--wide');
            if (!item) return;
            e.preventDefault();

            playlistList.querySelectorAll('.player-playlist__item--active').forEach(function(el) {
                el.classList.remove('player-playlist__item--active');
            });
            item.classList.add('player-playlist__item--active');

            var title = item.getAttribute('data-title') || '';
            var artist = item.getAttribute('data-artist') || '';
            var album = item.getAttribute('data-album') || '';
            var duration = item.getAttribute('data-duration') || '00:03:00';
            var stream = item.getAttribute('data-stream');

            if (topbarTitle) topbarTitle.textContent = title;
            if (cardTitle) cardTitle.textContent = title;
            if (cardArtist) cardArtist.textContent = artist;
            if (cardAlbum) cardAlbum.textContent = album;
            if (cardTime) cardTime.textContent = '00:00:00 / ' + duration;

            setPlayingState(true);

            if (stream && typeof window.playStreamUrl === 'function') {
                window.playStreamUrl(stream);
            }
        });
    }

    var toggleVinylBtn = document.getElementById('dp-toggle-vinyl');
    var vinylContainer = document.getElementById('dp-vinyl-container');
    var btnFs = document.getElementById('dp-btn-fs');

    if (toggleVinylBtn && vinylContainer) {
        toggleVinylBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var isHidden = vinylContainer.classList.toggle('is-hidden');
            toggleVinylBtn.classList.toggle('is-active', !isHidden);
        });
    }

    if (btnFs) {
        btnFs.addEventListener('click', function(e) {
            e.preventDefault();
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(function() {});
            } else {
                document.exitFullscreen().catch(function() {});
            }
        });
    }

    // Klavye kısayolları: Boşluk tuşu = play/pause, ESC = geri dön
    window.addEventListener('keydown', function(e) {
        if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
        if (e.code === 'Space') {
            e.preventDefault();
            setPlayingState(!isPlaying);
        } else if (e.code === 'Escape') {
            window.location.href = '/nowplaying';
        }
    });
})();
</script>
