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
$is4k    = $dm->shouldRender4kLayout();
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
        return '00:00:00';
    }
    $s = max(0, (int)$seconds);
    return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
};

/* --- Kuyruk verisi: mevcut repository (yeni sorgu YOK) → DB yoksa PNG demo seed --- */
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

/* Kapak/arka plan: doğrulanmış asset (RecentTracksComponent seed kaynağı) */
$coverUrl = $assetsUrl . '/Image/res-pink/album-goksel.png';
$isFullscreen = isset($_GET['mode']) && $_GET['mode'] === 'fullscreen';
?>
<?php if ($isWide && !$isFullscreen): ?>
    <!-- ════════════════════════════════════════════════════════════
         WIDE / 4K — Figma node 1390:24448 (1920×1080)
         "Laptop 1920 - Playlist Page" (Şimdi Oynatılıyor)
         SSOT: 1390-24448-Laptop-1920---Playlist-Page.png (Kullanıcı Ek 2)
         Layout: Üst Toolbar (Geri + "Şimdi Oynatılıyor" + Arama "Şarkı Ara")
                 + Sol 6-Kolonlu Şarkı Tablosu + Sağ Sanatçı & Öneriler Kartı
         ════════════════════════════════════════════════════════════ -->
    <?php
    $currentTrack = $tracks[0] ?? ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05'];
    $currentSong = (string)($currentTrack['t'] ?? 'Göksel - Sevil Neşelen');
    $currentArtist = (string)($currentTrack['a'] ?? 'Göksel');
    $currentAlbum = (string)($currentTrack['album'] ?? 'Hayat Rüya Gibi');
    ?>
    <div class="page-player page-player--wide page-layout <?= $h($dm->allClasses()) ?>"
         <?= $dm->dataAttributes() ?>>

        <div class="player-toolbar">
            <div class="player-toolbar__left">
                <a class="player-toolbar__btn player-toolbar__btn--circle" href="javascript:history.back()" aria-label="Geri" title="Geri">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20z" fill="currentColor"/>
                    </svg>
                </a>
                <a class="player-toolbar__btn player-toolbar__btn--circle" href="javascript:history.forward()" aria-label="İleri" title="İleri">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" fill="currentColor"/>
                    </svg>
                </a>
                <h1 class="player-toolbar__title">Şimdi Oynatılıyor</h1>
                <a class="player-toolbar__btn player-toolbar__btn--home" href="/" aria-label="Ana sayfa" title="Ana Sayfa">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" fill="currentColor"/>
                    </svg>
                </a>
                <a class="player-toolbar__btn player-toolbar__btn--disk" href="/diskplayer" aria-label="Disk Player (Tam Ekran)" title="Disk Player (Tam Ekran)">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z" fill="currentColor"/>
                    </svg>
                    <span class="player-toolbar__btn-text">Disk Player</span>
                </a>
            </div>

            <div class="player-toolbar__search">
                <input type="search"
                       id="nowplaying-search"
                       class="player-toolbar__search-input"
                       placeholder="Şarkı Ara"
                       aria-label="Şarkı Ara"
                       autocomplete="off">
                <svg class="player-toolbar__search-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <!-- ORTA İÇERİK: Sol 6-Kolonlu Şarkı Tablosu + Sağ Sanatçı & Öneriler Kartı -->
        <div class="player-body player-body--wide">

            <!-- SOL: 6 Kolonlu Şarkı Tablosu Paneli -->
            <section class="player-table-panel" aria-label="Şarkı Listesi">
                <div class="player-table-scroll">
                    <table class="player-table">
                        <thead>
                            <tr>
                                <th class="player-col--thumb" scope="col">/</th>
                                <th class="player-col--title" scope="col">Şarkı Adı</th>
                                <th class="player-col--album" scope="col">Album Adı</th>
                                <th class="player-col--artist" scope="col">Sanatçı</th>
                                <th class="player-col--duration" scope="col">Süre</th>
                                <th class="player-col--stars" scope="col">Favori Yıldızı</th>
                            </tr>
                        </thead>
                        <tbody id="nowplaying-tbody">
                            <?php foreach ($tracks as $i => $track): ?>
                                <?php
                                $isActive = !empty($track['active']);
                                $stream = (string)($track['stream'] ?? '');
                                $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                                    ? ' data-stream="' . $h($stream) . '"'
                                    : '';
                                $starCount = (int)($track['stars'] ?? 5);
                                $thumbUrl = $assetsUrl . '/Image/res-pink/default-album.png';
                                ?>
                                <tr class="player-row <?= $isActive ? 'player-row--active' : '' ?>"
                                    tabindex="0"
                                    <?= $streamAttr ?>
                                    data-title="<?= $h((string)$track['t']) ?>"
                                    data-artist="<?= $h((string)$track['a']) ?>"
                                    data-album="<?= $h((string)($track['album'] ?? '')) ?>"
                                    aria-label="<?= $h($track['t'] . ' — ' . $track['a']) ?>">
                                    <td class="player-col--thumb">
                                        <img class="player-row__thumb" src="<?= $h($thumbUrl) ?>" alt="" width="32" height="32" loading="lazy">
                                    </td>
                                    <td class="player-col--title"><?= $h((string)$track['t']) ?></td>
                                    <td class="player-col--album"><?= $h((string)($track['album'] ?? 'Hayat Rüya Gibi')) ?></td>
                                    <td class="player-col--artist"><?= $h((string)$track['a']) ?></td>
                                    <td class="player-col--duration"><?= $h((string)$track['d']) ?></td>
                                    <td class="player-col--stars">
                                        <div class="player-stars" aria-label="<?= $starCount ?> yıldız">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <span class="player-star <?= $s <= $starCount ? 'player-star--filled' : '' ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- SAĞ: Sanatçı & Öneriler Sidebar — SSOT Figma 2831:9443 / 1390:24448 (420px) -->
            <aside class="player-sidebar player-sidebar--recommendations" aria-label="Sanatçı ve Öneriler">
                <!-- Sekmeler: Önerilenler / Şarkı Kuyruğu -->
                <div class="player-sidebar__tabs" role="tablist">
                    <button class="player-sidebar__tab is-active" type="button" role="tab" aria-selected="true" data-tab-target="tab-recommendations">
                        Önerilenler
                    </button>
                    <button class="player-sidebar__tab" type="button" role="tab" aria-selected="false" data-tab-target="tab-queue">
                        Şarkı Kuyruğu (<?= count($tracks) ?>)
                    </button>
                </div>

                <!-- 1. PANE: Önerilenler & Sanatçı -->
                <div class="player-sidebar__pane" id="tab-recommendations" role="tabpanel">
                    <div class="player-sidebar__art">
                        <img class="player-sidebar__avatar" src="<?= $h($coverUrl) ?>" alt="Sanatçı Görseli" width="140" height="140" loading="lazy">
                    </div>

                    <div class="player-sidebar__meta">
                        <h2 class="player-sidebar__song-title" id="np-rec-title"><?= $h($currentSong) ?></h2>
                        <p class="player-sidebar__song-sub" id="np-rec-sub"><?= $h($currentArtist) ?> , <?= $h($currentAlbum) ?></p>
                    </div>

                    <div class="player-sidebar__actions">
                        <button type="button" class="player-action-btn player-action-btn--pink" title="Şarkı Sözleri" aria-label="Şarkı Sözleri">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                            </svg>
                        </button>
                        <button type="button" class="player-action-btn player-action-btn--glass" title="Favorilere Ekle" aria-label="Favorilere Ekle">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="#ff4fd8" aria-hidden="true">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                        <button type="button" class="player-action-btn player-action-btn--glass" title="Beğen" aria-label="Beğen">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                                <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                            </svg>
                        </button>
                        <button type="button" class="player-action-btn player-action-btn--glass" title="İndir" aria-label="İndir">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                                <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                            </svg>
                        </button>
                        <button type="button" class="player-action-btn player-action-btn--glass" title="Daha Fazla" aria-label="Daha Fazla">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                                <path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Önerilen Sanatçılar -->
                    <div class="player-sidebar__section">
                        <div class="player-sidebar__section-header">
                            <h3 class="player-sidebar__section-title">Önerilen Sanatçılar</h3>
                            <a class="player-sidebar__section-link" href="/singer">Tümünü Göster</a>
                        </div>
                        <div class="player-sidebar__artist-grid">
                            <a class="player-sidebar__artist-item" href="/singer?q=goksel">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($coverUrl) ?>" alt="Göksel" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Göksel</span>
                            </a>
                            <a class="player-sidebar__artist-item" href="/singer?q=zara">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($assetsUrl . '/Image/res-pink/album-default.png') ?>" alt="Zara" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Zara</span>
                            </a>
                            <a class="player-sidebar__artist-item" href="/singer?q=sibelcan">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($coverUrl) ?>" alt="Sibel Can" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Sibel Can</span>
                            </a>
                            <a class="player-sidebar__artist-item" href="/singer?q=dilsoz">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($assetsUrl . '/Image/res-pink/default-album.png') ?>" alt="Dilsöz" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Dilsöz</span>
                            </a>
                        </div>
                    </div>

                    <!-- Son Dinlenenler -->
                    <div class="player-sidebar__section">
                        <div class="player-sidebar__section-header">
                            <h3 class="player-sidebar__section-title">Son Dinlenenler</h3>
                            <a class="player-sidebar__section-link" href="/history">Tümünü Göster</a>
                        </div>
                        <div class="player-sidebar__artist-grid">
                            <a class="player-sidebar__artist-item" href="/singer?q=ferhatgocer">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($assetsUrl . '/Image/res-pink/album-default.png') ?>" alt="Ferhat Göçer" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Ferhat Göçer</span>
                            </a>
                            <a class="player-sidebar__artist-item" href="/singer?q=barismanco">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($coverUrl) ?>" alt="Barış Manço" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Barış Manço</span>
                            </a>
                            <a class="player-sidebar__artist-item" href="/singer?q=zara">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($assetsUrl . '/Image/res-pink/default-album.png') ?>" alt="Zara" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Zara</span>
                            </a>
                            <a class="player-sidebar__artist-item" href="/singer?q=kismasali">
                                <div class="player-sidebar__artist-thumb">
                                    <img src="<?= $h($coverUrl) ?>" alt="Kış Masalı" loading="lazy">
                                </div>
                                <span class="player-sidebar__artist-name">Kış Masalı</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. PANE: Şarkı Kuyruğu (Figma 1390 Info Kartı & Detaylar) -->
                <div class="player-sidebar__pane is-hidden" id="tab-queue" role="tabpanel">
                    <div class="player-sidebar__cover-card">
                        <img class="player-sidebar__cover-img" id="np-info-cover" src="<?= $h($coverUrl) ?>" alt="Albüm Kapağı" width="221" height="200" loading="lazy">
                    </div>
                    <div class="player-sidebar__info-meta">
                        <p class="player-sidebar__info-row">
                            <span class="player-sidebar__info-label">Şarkı Adı :</span>
                            <span class="player-sidebar__info-val" id="np-info-title"><?= $h($currentSong) ?></span>
                        </p>
                        <p class="player-sidebar__info-row">
                            <span class="player-sidebar__info-label">Albüm :</span>
                            <span class="player-sidebar__info-val" id="np-info-album"><?= $h($currentAlbum) ?></span>
                        </p>
                        <p class="player-sidebar__info-row">
                            <span class="player-sidebar__info-label">Sanatçı :</span>
                            <span class="player-sidebar__info-val" id="np-info-artist"><?= $h($currentArtist) ?></span>
                        </p>
                        <p class="player-sidebar__info-row">
                            <span class="player-sidebar__info-label">Bit rate :</span>
                            <span class="player-sidebar__info-val">350 kpps</span>
                        </p>
                        <p class="player-sidebar__info-row">
                            <span class="player-sidebar__info-label">Süre :</span>
                            <span class="player-sidebar__info-val">00:00:00 / 00:05:00</span>
                        </p>
                    </div>
                </div>
            </aside>

        </div>

        <script>
        (function() {
            var searchInput = document.getElementById('nowplaying-search');
            var tbody = document.getElementById('nowplaying-tbody');

            if (searchInput && tbody) {
                searchInput.addEventListener('input', function() {
                    var q = (this.value || '').trim().toLowerCase();
                    var rows = tbody.querySelectorAll('.player-row');
                    rows.forEach(function(row) {
                        if (!q) {
                            row.style.display = '';
                            return;
                        }
                        var t = (row.getAttribute('data-title') || '').toLowerCase();
                        var a = (row.getAttribute('data-artist') || '').toLowerCase();
                        var al = (row.getAttribute('data-album') || '').toLowerCase();
                        if (t.indexOf(q) !== -1 || a.indexOf(q) !== -1 || al.indexOf(q) !== -1) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
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

                    var title = row.getAttribute('data-title') || '';
                    var artist = row.getAttribute('data-artist') || '';
                    var album = row.getAttribute('data-album') || '';

                    var infoTitle = document.getElementById('np-info-title');
                    var infoArtist = document.getElementById('np-info-artist');
                    var infoAlbum = document.getElementById('np-info-album');
                    var recTitle = document.getElementById('np-rec-title');
                    var recSub = document.getElementById('np-rec-sub');

                    if (infoTitle) infoTitle.textContent = title;
                    if (infoArtist) infoArtist.textContent = artist;
                    if (infoAlbum) infoAlbum.textContent = album;
                    if (recTitle) recTitle.textContent = title;
                    if (recSub) recSub.textContent = artist + ' , ' + album;

                    var stream = row.getAttribute('data-stream');
                    if (stream && typeof window.playStreamUrl === 'function') {
                        window.playStreamUrl(stream);
                    }
                });
            }

            // Sidebar Tab Değişimi (Önerilenler / Şarkı Kuyruğu)
            var tabs = document.querySelectorAll('.player-sidebar__tab');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var targetId = this.getAttribute('data-tab-target');
                    if (!targetId) return;
                    tabs.forEach(function(t) {
                        t.classList.remove('is-active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    this.classList.add('is-active');
                    this.setAttribute('aria-selected', 'true');
                    var panes = document.querySelectorAll('.player-sidebar__pane');
                    panes.forEach(function(p) {
                        if (p.id === targetId) {
                            p.classList.remove('is-hidden');
                        } else {
                            p.classList.add('is-hidden');
                        }
                    });
                });
            });
        })();
        </script>
    </div>
<?php elseif ($isWide && $isFullscreen): ?>
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

        <!-- ARKA PLAN KATMANI -->
        <div class="player-bg" aria-hidden="true">
            <img class="player-bg__image" src="<?= $h($coverUrl) ?>" alt="" loading="lazy">
            <div class="player-bg__overlay"></div>
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

        <!-- EN ALT: Tam genişlik 5px ilerleme çubuğu (node 2135:35497) -->
        <div class="player-bottom-progress" role="progressbar" aria-label="Şarkı ilerleme çubuğu" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
            <div class="player-bottom-progress__fill"></div>
        </div>
    </div>
<?php else: ?>
    <!-- ════════════════════════════════════════════════════════════
         EMBEDDED 1024×600 — Figma SSOT
         ════════════════════════════════════════════════════════════ -->
    <div class="page-player page-player--embedded page-layout"
         <?= $dm->dataAttributes() ?>>

        <!-- ARKA PLAN KATMANI — _player.css: .player-bg (bulanık kapak + gradient overlay) -->
        <div class="player-bg" aria-hidden="true">
            <img class="player-bg__image" src="<?= $h($coverUrl) ?>" alt="" loading="lazy">
            <div class="player-bg__overlay"></div>
        </div>

        <!-- GERİ — shell header altında konumlanır -->
        <a class="player-back" href="/" aria-label="Ana sayfaya dön">
            <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20z" fill="currentColor"/>
            </svg>
        </a>

        <div class="player-content">

            <!-- SOL: albüm görseli + Player Info kartı -->
            <div class="player-visual">
                <div class="player-visual__art">
                    <img class="player-visual__image"
                         src="<?= $h($coverUrl) ?>"
                         alt="Çalan şarkının albüm kapağı"
                         loading="lazy">
                    <div class="player-visual__glow"></div>
                </div>
                <?php $loader->display('player-info', $variant); ?>
            </div>

            <!-- SAĞ: Sıradaki Şarkılar (kuyruk) — _player.css .player-playlist kontratı -->
            <aside class="player-playlist" aria-label="Sıradaki Şarkılar">
                <div class="player-playlist__header">
                    <h2 class="player-playlist__title">Sıradaki Şarkılar</h2>
                    <span class="player-playlist__count"><?= count($tracks) ?> şarkı</span>
                </div>
                <div class="player-playlist__list">
                    <?php foreach ($tracks as $track): ?>
                        <?php
                        $stream = (string)$track['stream'];
                        $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                            ? ' data-stream="' . $h($stream) . '"'
                            : '';
                        $label = 'Oynat: ' . $track['t'] . ' — ' . $track['a'];
                        ?>
                        <a class="player-playlist__item"
                           href="/playlist"
                           data-no-spa
                           <?= $streamAttr ?>
                           aria-label="<?= $h($label) ?>">
                            <img class="player-playlist__thumb"
                                 src="<?= $h($assetsUrl . '/Image/res-pink/default-album.png') ?>"
                                 alt=""
                                 width="44"
                                 height="44"
                                 loading="lazy">
                            <span class="player-playlist__info">
                                <span class="player-playlist__track-title"><?= $h((string)$track['t']) ?></span>
                                <span class="player-playlist__track-artist"><?= $h((string)$track['a']) ?></span>
                            </span>
                            <span class="player-playlist__duration"><?= $h((string)$track['d']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

        </div>
    </div>
<?php endif; ?>
