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
        foreach (MusicRepository::fromEnvironment()->findRecentTracks() as $row) {
            $hex = strtolower((string)($row['music_hex'] ?? ''));
            if ($hex === '') {
                continue;
            }
            $tracks[] = [
                't'      => (string)$row['title'],
                'a'      => (string)$row['artist_name'],
                'd'      => $formatDuration($row['duration_sec'] ?? null),
                'stream' => '/stream/' . $hex,
            ];
        }
    }
} catch (\Throwable) {
    $tracks = [];
}

if ($tracks === []) {
    /* PNG home-1024 node 1639:9904 demo içeriği (RecentTracksComponent ile birebir) */
    $tracks = [
        ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Kabahat Seni Se...', 'a' => 'Göksel', 'd' => '00:02:05', 'stream' => ''],
        ['t' => 'Barış Manco - Gulpembe', 'a' => 'Barış Manco', 'd' => '00:04:01', 'stream' => ''],
        ['t' => 'Kış Masalı Ensturmental', 'a' => 'Org Dersleri', 'd' => '00:01:10', 'stream' => ''],
    ];
}

/* Kapak/arka plan: doğrulanmış asset (RecentTracksComponent seed kaynağı) */
$coverUrl = $assetsUrl . '/Image/res-pink/album-goksel.png';
$pageClass = $isWide ? 'page-player' : 'page-player page-player--embedded';
?>
<div class="<?= $h($pageClass) ?> page-layout"
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
