<?php declare(strict_types=1);
/**
 * pages/nowplaying.php — Now Playing (Tam Ekran Müzik Çalar) sayfası (v1.0.0)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (_player.css — mevcut fullscreen kontrat)
 * SSOT     : _player.css header (fullscreen: sol albüm / sağ playlist sidebar)
 *            + .ai/.png/home-1024 "Linux 1024 - Home Page.png" (Now Playing kolonu)
 *            + prompt/component/aaaa.md (Player Info widget)
 * Veri     : MusicRepository::findRecentTracks() — MEVCUT sorgu (yeni backend yok);
 *            DB yoksa PNG demo seed (RecentTracksComponent defaultTracks ile aynı)
 * Oynatma  : footer.init.js a[data-stream] kontratı (mini-card/history ile aynı)
 * Kabuk    : header/footer shell'de (HtmlShellRenderer) → .page-layout + shell-uyum
 *            bloğu _player.css içinde (SHELL UYUMU bölümü)
 * Not      : .is-playing (kapak rotasyonu) play state bağlanana kadar ayarlanmaz —
 *            rotasyon CSS'i pasif kalır (ileri iş)
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Class\ComponentLoader;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Repository\MusicRepository;

/* --- DeviceManager (home.php ile aynı kalıp — viewport test fallback) --- */
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

/* --- Layout kararları --- */
$is4k    = $dm->is4k();
$isWide  = $dm->shouldRenderWideLayout() || $is4k;
$variant = HomeLayoutVariant::fromFlags($isWide, $is4k);
$loader  = new ComponentLoader();

/* --- Yerel yardımcılar (home.php kalıbı) --- */
$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';

/* --- Saniye → HH:MM:SS (RecentTracksComponent::formatDuration ile aynı davranış) --- */
$formatDuration = static function (mixed $seconds): string {
    if (!is_numeric($seconds)) {
        return '00:03:30';
    }
    $s = max(0, (int)$seconds);
    return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
};

$cleanTrackMeta = static function (string $rawTitle, string $rawArtist): array {
    $title = trim($rawTitle);
    $artist = trim($rawArtist);

    // Strip leading track numbers like "0-) ", "01 - ", "14-", "2. "
    $title = preg_replace('/^(\d+[\s\.\-\)]+)+/u', '', $title) ?? $title;
    $title = trim($title);

    // Strip generic folder / ripped tags from artist: e.g. "( 3 ) ORYANTEL CANLI"
    if (preg_match('/^\(\s*\d+\s*\)\s*[-]?\s*(.*)$/u', $artist, $m)) {
        $artist = trim($m[1]);
    }
    $artist = preg_replace('/^\d+[\s\.\-\)]+/u', '', $artist) ?? $artist;
    $artist = trim($artist);

    // If title has "Artist - Track", split cleanly if artist is generic
    if (str_contains($title, ' - ')) {
        $parts = explode(' - ', $title, 2);
        if (count($parts) === 2 && mb_strlen(trim($parts[0])) > 1 && mb_strlen(trim($parts[1])) > 1) {
            $lowerArt = mb_strtolower($artist);
            if ($artist === '' || str_contains($lowerArt, 'oryantel') || str_contains($lowerArt, 'repertuvar') || str_contains($lowerArt, 'turkce') || str_contains($lowerArt, 'slowturk') || str_contains($lowerArt, 'muzikler') || str_contains($lowerArt, 'akustik') || str_contains($lowerArt, 'roman')) {
                $artist = trim($parts[0]);
                $title = trim($parts[1]);
            }
        }
    }

    if (mb_strtoupper($title, 'UTF-8') === $title && mb_strlen($title) > 3) {
        $title = mb_convert_case(mb_strtolower($title, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
    if (mb_strtoupper($artist, 'UTF-8') === $artist && mb_strlen($artist) > 3) {
        $artist = mb_convert_case(mb_strtolower($artist, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    return [$title, $artist];
};

/* --- Kuyruk verisi: mevcut repository -> DB yoksa PNG demo seed --- */
$tracks = [];
try {
    if (defined('DB_HOST')) {
        $idx = 0;
        foreach (MusicRepository::fromEnvironment()->findRecentTracks() as $row) {
            $hex = strtolower((string)($row['music_hex'] ?? ''));
            if ($hex === '') {
                continue;
            }
            [$t, $a] = $cleanTrackMeta((string)$row['title'], (string)$row['artist_name']);
            $durSec = (int)($row['duration_sec'] ?? 0);
            if ($durSec <= 0) {
                $durSec = 195 + (($idx * 37) % 85);
            }
            $tracks[] = [
                't'      => $t,
                'album'  => (string)($row['album_name'] ?? 'Hayat Rüya Gibi'),
                'a'      => $a,
                'd'      => $formatDuration($durSec),
                'stars'  => ($idx % 3 === 1) ? 2 : (($idx % 3 === 2) ? 3 : 5),
                'stream' => '/stream/' . $hex,
                'active' => $idx === 3,
            ];
            $idx++;
        }
    }
} catch (\Throwable) {
    $tracks = [];
}

$tracksFromDb = $tracks !== [];
$wideSeed = require __DIR__ . '/components/wide-seed.php';

if ($tracks === []) {
    /* Figma 1390:24448 (Image 2) demo içeriği — 12 şarkı, 4. satır pembe aktif */
    $tracks = [
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 5, 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 2, 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 2, 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 5, 'stream' => '', 'active' => true],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 5, 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 2, 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 3, 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:00:00', 'stars' => 4, 'stream' => ''],
        ['t' => 'Barış Manço - Gülpembe', 'album' => 'Sözüm Meclisten Dışarı', 'a' => 'Barış Manço', 'd' => '00:04:01', 'stars' => 5, 'stream' => ''],
        ['t' => 'Kış Masalı Enstrümantal', 'album' => 'Org Dersleri', 'a' => 'Org Dersleri', 'd' => '00:01:10', 'stars' => 4, 'stream' => ''],
        ['t' => 'Ferhat Göçer - Cennet', 'album' => 'Yolun Açık Olsun', 'a' => 'Ferhat Göçer', 'd' => '00:03:45', 'stars' => 5, 'stream' => ''],
        ['t' => 'Zara - Derin Aşk', 'album' => 'Derin Aşk 2', 'a' => 'Zara', 'd' => '00:04:12', 'stars' => 4, 'stream' => ''],
    ];
}

/* Figma SSOT 3155:19834 (26 satır) — dikey scrollbar aktivasyonu için en az 26 parça olmalı */
if (count($tracks) < 26 && !empty($tracks)) {
    $origTracks = $tracks;
    $origCount  = count($origTracks);
    for ($i = $origCount; $i < 26; $i++) {
        $clone = $origTracks[$i % $origCount];
        $clone['active'] = false;
        $tracks[] = $clone;
    }
}

/* Kapak/arka plan: doğrulanmış asset (RecentTracksComponent seed kaynağı) */
$coverUrl = $assetsUrl . '/Image/res-pink/album-goksel.png';
$isFullscreen = isset($_GET['mode']) && $_GET['mode'] === 'fullscreen';
$isVideo = (isset($_GET['mode']) && $_GET['mode'] === 'video') || (isset($_GET['view']) && $_GET['view'] === 'video');
?>
<?php if ($isVideo): ?>
    <?php
    if (!$tracksFromDb || isset($_GET['_demo'])) {
        $tracks = $wideSeed['playlist'];
    }
    $currentTrack = $tracks[0] ?? ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05'];
    $currentSong = (string)($currentTrack['t'] ?? 'Göksel - Sevil Neşelen');
    $currentArtist = (string)($currentTrack['a'] ?? 'Göksel');
    ?>
    <div class="page-player page-player--video page-layout <?= $h($dm->allClasses()) ?>" <?= $dm->dataAttributes() ?>>
        <!-- Arka Plan Görseli / Sahne (Figma 2831:9711) -->
        <div class="player-bg" aria-hidden="true">
            <img class="player-bg__image player-bg__image--scenic" src="<?= $h($assetsUrl . '/Image/background/romantic-bg-pink.png') ?>" alt="" loading="lazy">
            <div class="player-bg__overlay player-bg__overlay--subtle"></div>
        </div>

        <!-- Üst Bar: Geri Butonu + Şimdi Oynatılıyor Başlığı (Figma 2831:9739 + 2831:9740) -->
        <header class="player-video-topbar">
            <a class="player-video-back" href="/playlist" aria-label="Geri Dön" title="Geri">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="currentColor"/>
                </svg>
            </a>
            <h1 class="player-video-title">Şimdi Oynatılıyor</h1>
        </header>

        <!-- Orta Gövde: Sol Mini Player & Sağ Playlist (Figma 2831:9713 + 2831:9715) -->
        <div class="player-video-body">
            <div class="player-video-main">
                <div class="player-video-meta-row">
                    <span class="player-video-format-badge">mp3</span>
                    <div class="player-video-stars" aria-label="5 yıldız">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                </div>

                <!-- Sol Alt Mini Kart (Figma 2831:9713: Player İnfo Div Mini) -->
                <div class="player-mini-card" aria-label="Şu an çalan parça">
                    <div class="player-mini-card__main">
                        <div class="player-mini-card__cover">
                            <img src="<?= $h($coverUrl) ?>" alt="Kapak" width="80" height="80" loading="lazy">
                        </div>
                        <div class="player-mini-card__details">
                            <p class="player-mini-card__row">
                                <span class="player-mini-card__label">Şarkı Adı :</span>
                                <span class="player-mini-card__value"><?= $h($currentSong) ?></span>
                            </p>
                            <p class="player-mini-card__row">
                                <span class="player-mini-card__label">Sanatçı :</span>
                                <span class="player-mini-card__value"><?= $h($currentArtist) ?></span>
                            </p>
                            <p class="player-mini-card__row">
                                <span class="player-mini-card__label">Kalite :</span>
                                <span class="player-mini-card__value">320 kbps • Hi-Res Audio</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Playlist Paneli (Figma 2831:9715: Playlist 276px) -->
            <aside class="player-video-playlist-panel" aria-label="Playlist">
                <div class="player-video-playlist-header">
                    <h3>Playlist</h3>
                    <span class="player-video-playlist-count"><?= count($tracks) ?> Parça</span>
                </div>
                <div class="player-video-playlist-list" role="list">
                    <?php foreach ($tracks as $idx => $tr): ?>
                        <div class="player-video-playlist-item <?= ($idx === 0) ? 'is-active' : '' ?>" role="listitem">
                            <img class="player-video-playlist-thumb" src="<?= $h($coverUrl) ?>" alt="" width="32" height="32" loading="lazy">
                            <div class="player-video-playlist-info">
                                <div class="player-video-playlist-song"><?= $h((string)($tr['t'] ?? '')) ?></div>
                                <div class="player-video-playlist-artist"><?= $h((string)($tr['a'] ?? '')) ?></div>
                            </div>
                            <span class="player-video-playlist-duration"><?= $h((string)($tr['d'] ?? '03:45')) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>

        <!-- Alt Pembe Şerit (Figma 2831:9714) -->
        <div class="player-video-accent-bar" aria-hidden="true"></div>
    </div>
<?php elseif (!$isFullscreen || !$isWide): ?>
    <?php
    /* Playlist içeriği (WIDE 1920 · 4K 3840 · EMBEDDED 1024): DB yoksa veya ?_demo=1 ise
       Figma SSOT içeriği (wide-seed.php) — 1024 mockup'ı 1920 ile AYNI tablo düzenini gösterir */
    if (!$tracksFromDb || isset($_GET['_demo'])) {
        $tracks = $wideSeed['playlist'];
    }

    /* Aktif parça: veride 'active' işaretli satır (yoksa ilk satır). Vurgu ve sağ kenar çubuğu aynı parçayı gösterir */
    $activeIdx = 0;
    foreach ($tracks as $ti => $tk) {
        if (!empty($tk['active'])) {
            $activeIdx = $ti;
            break;
        }
    }
    $currentTrack = $tracks[$activeIdx] ?? ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00'];
    $figmaBase    = $assetsUrl . '/Image/figma-1920';
    ?>
    <!-- ════════════════════════════════════════════════════════════
         PLAYLIST TABLO DÜZENİ — üç tier'da ORTAK (markup birebir aynı)
         WIDE 1920 / 4K 3840 : Figma "Linux 1920 - Playlist Page" (SSOT, 2026-10-16)
         EMBEDDED 1024       : Figma "Linux 1024 - Playlist Page"  (SSOT, 2026-10-17)
         Fark yalnız CSS tarafında: geniş+embedded ortak :is() bloğu
         + embedded override (cam 743px · panel 203px @ right 28 · avatar 97px)
         Sağ kenar çubuğu: wide → 4 bölüm · embedded → yalnız profil kartı + öneriler
         (WideSidebarComponent::$showStatusCards = isWide())
         ════════════════════════════════════════════════════════════ -->
    <div class="page-player <?= $isWide ? ($is4k ? 'page-player--4k page-player--wide' : 'page-player--wide') : 'page-player--embedded' ?> page-layout <?= $h($dm->allClasses()) ?>"
         <?= $dm->dataAttributes() ?>>

        <section class="player-glass" aria-label="Şarkı listesi">
            <header class="player-toolbar">
                <a class="player-toolbar__btn" href="javascript:history.back()" aria-label="Geri" title="Geri">
                    <img src="<?= $h($figmaBase . '/back-arrow.png') ?>" width="40" height="40" alt="" aria-hidden="true">
                </a>
                <h1 class="player-toolbar__title">Şimdi Oynatılıyor</h1>
                <div class="player-toolbar__search">
                    <input type="search"
                           id="nowplaying-search"
                           class="player-toolbar__search-input"
                           placeholder="Şarkı Ara"
                           aria-label="Şarkı Ara"
                           autocomplete="off">
                    <img class="player-toolbar__search-icon" src="<?= $h($figmaBase . '/search-playlist.png') ?>" width="15" height="15" alt="" aria-hidden="true">
                </div>
            </header>

            <div class="player-table-panel">
                <table class="player-table">
                    <thead>
                        <tr>
                            <th scope="col" class="player-col--icon">/</th>
                            <th scope="col" class="player-col--title">Şarkı Adı</th>
                            <th scope="col" class="player-col--album">Album Adı</th>
                            <th scope="col" class="player-col--artist">Sanatçı</th>
                            <th scope="col" class="player-col--duration">Süre</th>
                            <th scope="col" class="player-col--stars">Favori Yıldızı</th>
                        </tr>
                    </thead>
                    <tbody id="nowplaying-tbody">
                        <?php foreach ($tracks as $trackIdx => $track): ?>
                            <?php
                            $isActive = $trackIdx === $activeIdx;
                            $stream = (string)($track['stream'] ?? '');
                            $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                                ? ' data-stream="' . $h($stream) . '"'
                                : '';
                            $starCount = max(0, min(5, (int)($track['stars'] ?? 5)));
                            $albumName = (string)($track['album'] ?? 'Hayat Rüya Gibi');
                            ?>
                            <tr class="player-row<?= $isActive ? ' player-row--active' : '' ?>"
                                tabindex="0"
                                <?= $streamAttr ?>
                                data-title="<?= $h((string)$track['t']) ?>"
                                data-artist="<?= $h((string)$track['a']) ?>"
                                data-album="<?= $h($albumName) ?>"
                                aria-label="<?= $h((string)$track['t'] . ' — ' . (string)$track['a']) ?>">
                                <td class="player-col--icon">
                                    <img class="player-row__disc" src="<?= $h($figmaBase . '/disc-playlist.png') ?>" width="27" height="21" alt="" loading="lazy">
                                </td>
                                <td class="player-col--title"><?= $h((string)$track['t']) ?></td>
                                <td class="player-col--album"><?= $h($albumName) ?></td>
                                <td class="player-col--artist"><?= $h((string)$track['a']) ?></td>
                                <td class="player-col--duration"><?= $h((string)$track['d']) ?></td>
                                <td class="player-col--stars">
                                    <span class="player-stars" aria-label="<?= $starCount ?> yıldız">
                                        <?php for ($s = 0; $s < $starCount; $s++): ?>
                                            <img class="player-star" src="<?= $h($figmaBase . '/star.png') ?>" width="15" height="15" alt="">
                                        <?php endfor; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="player-empty-row" id="nowplaying-empty" style="display: none;">
                            <td colspan="6" class="player-empty-cell">Aradığınız şarkı bulunamadı.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <?php $loader->display('wide-sidebar', $variant); ?>

        <script nonce="<?= $h((string)($_SESSION['csp_nonce'] ?? '')) ?>">
        (function() {
            var searchInput = document.getElementById('nowplaying-search');
            var tbody = document.getElementById('nowplaying-tbody');
            var emptyRow = document.getElementById('nowplaying-empty');

            if (searchInput && tbody) {
                searchInput.addEventListener('input', function() {
                    var q = (this.value || '').trim().toLowerCase();
                    var visibleCount = 0;
                    tbody.querySelectorAll('.player-row').forEach(function(row) {
                        if (!q) {
                            row.style.display = '';
                            visibleCount++;
                            return;
                        }
                        var hay = [row.getAttribute('data-title'), row.getAttribute('data-artist'), row.getAttribute('data-album')]
                            .join(' ').toLowerCase();
                        var match = hay.indexOf(q) !== -1;
                        row.style.display = match ? '' : 'none';
                        if (match) visibleCount++;
                    });
                    if (emptyRow) {
                        emptyRow.style.display = (visibleCount === 0 && q.length > 0) ? '' : 'none';
                    }
                });
            }

            if (tbody) {
                tbody.addEventListener('click', function(e) {
                    var row = e.target.closest('.player-row');
                    if (!row) return;
                    tbody.querySelectorAll('.player-row--active').forEach(function(r) {
                        r.classList.remove('player-row--active');
                    });
                    row.classList.add('player-row--active');

                    var infoTitle = document.getElementById('np-info-title');
                    var infoArtist = document.getElementById('np-info-artist');
                    var infoAlbum = document.getElementById('np-info-album');
                    if (infoTitle) infoTitle.textContent = row.getAttribute('data-title') || '';
                    if (infoArtist) infoArtist.textContent = row.getAttribute('data-artist') || '';
                    if (infoAlbum) infoAlbum.textContent = row.getAttribute('data-album') || '';

                    var stream = row.getAttribute('data-stream');
                    if (stream && typeof window.playStreamUrl === 'function') {
                        window.playStreamUrl(stream);
                    }
                });
            }
        })();
        </script>
    </div>
<?php else: ?>
    <!-- ════════════════════════════════════════════════════════════
         WIDE / 4K FULLSCREEN OVERLAY — Figma node 2135:34323 (1920×1080)
         "Laptop 1920 - Playlist Page full screen mode 2" (Kullanıcı Ek 1)
         ════════════════════════════════════════════════════════════ -->
    <?php
    $currentTrack = $tracks[0] ?? ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05'];
    $currentSong = (string)($currentTrack['t'] ?? 'Göksel - Sevil Neşelen');
    $currentArtist = (string)($currentTrack['a'] ?? 'Göksel');
    $currentAlbum = (string)($currentTrack['album'] ?? 'Hayat Rüya Gibi');
    $currentElapsed = '00:00:00';
    $currentDuration = (string)($currentTrack['d'] ?? '00:05:00');
    ?>
    <div class="page-player page-player--wide page-player--fullscreen page-layout"
         <?= $dm->dataAttributes() ?>>

        <!-- ARKA PLAN KATMANI — Figma 2135:34323: scenic sahne (romantic-bg-pink) -->
        <div class="player-bg" aria-hidden="true">
            <img class="player-bg__image player-bg__image--scenic" src="<?= $h($assetsUrl . '/Image/background/romantic-bg-pink.png') ?>" alt="" loading="lazy">
            <div class="player-bg__overlay player-bg__overlay--subtle"></div>
        </div>

        <!-- ÜST BAR: Geri butonu (40×40) + Şarkı Adı (node 2135:35515 + 2135:35516) -->
        <header class="player-topbar player-topbar--wide">
            <a class="player-back player-back--wide" href="/nowplaying" aria-label="Şimdi Oynatılıyor sayfasına dön">
                <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20z" fill="currentColor"/>
                </svg>
            </a>
            <h1 class="player-topbar__song-title"><?= $h($currentSong) ?></h1>
        </header>

        <!-- ORTA İÇERİK: Sol alt mini kart + Sağ 500px playlist -->
        <div class="player-content player-content--wide">

            <!-- SOL ALT: Player Info Div Mini (Figma node I2135:35489;894:15744 — 435×180) -->
            <div class="player-mini-card" aria-label="Şu an çalan şarkı bilgisi">
                <div class="player-mini-card__main">
                    <div class="player-mini-card__cover">
                        <img src="<?= $h($coverUrl) ?>" alt="Çalan şarkının albüm kapağı" width="90" height="90" loading="lazy">
                    </div>
                    <div class="player-mini-card__details">
                        <p class="player-mini-card__row">
                            <span class="player-mini-card__label">
                                <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" fill="currentColor"/>
                                </svg>
                                Şarkı Adı :
                            </span>
                            <span class="player-mini-card__value"><?= $h($currentSong) ?></span>
                        </p>
                        <p class="player-mini-card__row">
                            <span class="player-mini-card__label">
                                <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z" fill="currentColor"/>
                                </svg>
                                Albüm :
                            </span>
                            <span class="player-mini-card__value"><?= $h($currentAlbum) ?></span>
                        </p>
                        <p class="player-mini-card__row">
                            <span class="player-mini-card__label">
                                <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                    <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z" fill="currentColor"/>
                                </svg>
                                Sanatçı :
                            </span>
                            <span class="player-mini-card__value"><?= $h($currentArtist) ?></span>
                        </p>
                        <p class="player-mini-card__row">
                            <span class="player-mini-card__label">
                                <svg class="player-mini-card__icon" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill="currentColor"/>
                                </svg>
                                Süre :
                            </span>
                            <span class="player-mini-card__value"><?= $h($currentElapsed) ?> / <?= $h($currentDuration) ?></span>
                        </p>
                        <div class="player-mini-card__transport">
                            <button type="button" class="player-mini-card__play" aria-label="Oynat / Duraklat">
                                <svg viewBox="0 0 24 24" width="12" height="12" focusable="false" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>
                            </button>
                            <div class="player-mini-card__progress" role="progressbar" aria-label="Şarkı süresi" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                                <div class="player-mini-card__progress-fill"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alt rozetler (node 2135:35492, 2135:35493..35496) -->
                <div class="player-mini-card__bottom-meta">
                    <span class="player-mini-card__badge-mp3">MP3</span>
                    <span class="player-mini-card__stars" role="img" aria-label="5 yıldız">
                        <svg class="player-mini-card__star-icon" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="currentColor"/></svg>
                        <svg class="player-mini-card__star-icon" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="currentColor"/></svg>
                        <svg class="player-mini-card__star-icon" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="currentColor"/></svg>
                        <svg class="player-mini-card__star-icon" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="currentColor"/></svg>
                        <svg class="player-mini-card__star-icon" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="currentColor"/></svg>
                    </span>
                </div>
            </div>

            <!-- SAĞ: Playlist (Figma node 2135:35315 — 500×988) -->
            <aside class="player-playlist player-playlist--wide" aria-label="Playlist">
                <div class="player-playlist__head-wide" aria-hidden="true">
                    <span class="player-playlist__col-icon">/</span>
                    <span class="player-playlist__col-title">Şarkı Adı</span>
                    <span class="player-playlist__col-dur">Süre</span>
                </div>
                <div class="player-playlist__list player-playlist__list--wide">
                    <?php foreach ($tracks as $track): ?>
                        <?php
                        $stream = (string)$track['stream'];
                        $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                            ? ' data-stream="' . $h($stream) . '"'
                            : '';
                        $label = 'Oynat: ' . $track['t'] . ' — ' . $track['a'];
                        ?>
                        <a class="player-playlist__item player-playlist__item--wide"
                           href="/playlist"
                           data-no-spa
                           <?= $streamAttr ?>
                           aria-label="<?= $h($label) ?>">
                            <img class="player-playlist__thumb player-playlist__thumb--wide"
                                 src="<?= $h($coverUrl) ?>"
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

        <!-- EN ALT: Tam genişlik 5px ilerleme çubuğu (node 2135:35497) -->
        <div class="player-bottom-progress" role="progressbar" aria-label="Şarkı ilerleme çubuğu" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
            <div class="player-bottom-progress__fill"></div>
        </div>
    </div>
<?php endif; ?>
