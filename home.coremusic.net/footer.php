<?php declare(strict_types=1);
/**
 * footer.php — CoreMusic Footer Player Bar (v3.0)
 *
 * Figma SSOT (API extraction — node-id=1639-9773):
 *   1024: Footer 1025×91, Album 85×85, Progressbar 1025×4.8,
 *         Player Buttons 297×62 (4 btn, 35×35 each, gap 22),
 *         MediaInfo Avalon 10px w500 ls:1.35 lh:13.5,
 *         Volume 186×14, Text icons 15×15
 *   1920: Footer 1923×92, Progressbar 1921×4.8,
 *         Player Buttons 126×27, Volume 353×14
 *
 * Figma Font: Avalon 10px w500 (all footer text)
 * Figma Effects: text drop-shadow rgba(0,0,0,0.8) 0.5,0.5,0.1
 * BEM: .footer-player, .footer-player__cover, .footer-player__info, etc.
 * Layer: L3 Presentation · 03_Layout (_footer.css)
 * Version: 3.0.0 — 2026-09-23 (Figma API pixel-perfect)
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
$nonce     = $h((string)($_SESSION['csp_nonce'] ?? ''));

/* ─── Çalma durumu (Figma varsayılan metinleri) ─── */
$progressPct     = (int)($_SESSION['progress_pct'] ?? 65);
$currentSong     = $h((string)($_SESSION['current_song'] ?? 'Göksel - Sevil Neşelen'));
$currentAlbum    = $h((string)($_SESSION['current_album'] ?? 'Hayat Rüya Gibi'));
$currentArtist   = $h((string)($_SESSION['current_artist'] ?? 'Göksel'));
$currentElapsed  = $h((string)($_SESSION['current_elapsed'] ?? '00:00:00'));
$currentDuration = $h((string)($_SESSION['current_duration'] ?? '00:05:00'));
$currentBitrate  = $h((string)($_SESSION['current_bitrate'] ?? '350 kbps'));
$currentArt      = $_SESSION['current_art'] ?? ($assetsUrl . '/Image/res-pink/album-goksel.png');
$volumePct       = max(0, min(100, (int)($_SESSION['volume'] ?? 100)));
$volumeRatio     = number_format($volumePct / 100, 4, '.', '');

/* ─── İkonlar (PNG birebir) ─── */
$iconBack  = $h($assetsUrl . '/Image/res-pink/media-back.png');
$iconPlay  = $h($assetsUrl . '/Image/res-pink/media-play.png');
$iconPause = $h($assetsUrl . '/Image/res-pink/media-pause.png');
$iconStop  = $h($assetsUrl . '/Image/res-pink/media-stop.png');
$iconNext  = $h($assetsUrl . '/Image/res-pink/media-ileri.png');
$iconVol   = $h($assetsUrl . '/Image/res-pink/volume-high.png');
$iconMusic   = $h($assetsUrl . '/Image/res-pink/music.png');
$iconCd      = $h($assetsUrl . '/Image/res-pink/cd-ico.png');
$iconMic     = $h($assetsUrl . '/Image/res-pink/mic-1.png');
$iconTimer   = $h($assetsUrl . '/Image/res-pink/timer.png');
$iconBitrate = $h($assetsUrl . '/Image/res-pink/bit-rate.png');

/* ─── Tier class ─── */
$footerTierClass = $dm->shouldRender4kLayout()
    ? 'footer-player--4k'
    : ($dm->shouldRenderWideLayout() ? 'footer-player--wide' : 'footer-player--embedded');
?>
<footer class="footer footer-player <?= $footerTierClass ?> <?= $dm->allClasses() ?>" role="contentinfo" aria-label="Oynatıcı" <?= $dm->dataAttributes() ?>>

    <!-- Progressbar: Figma 1025×4.8 (top edge) -->
    <div class="footer-player__progress" id="seekbarclick" role="progressbar" aria-valuenow="<?= $progressPct ?>" aria-valuemin="0" aria-valuemax="100">
        <input type="range" id="seekbar" min="0" max="100" step="0.0001" value="<?= $progressPct ?>" aria-label="Şarkı pozisyonu" class="footer-player__seek-input">
        <div class="footer-player__progress-bar" id="seekbar2" data-progress="<?= $progressPct ?>"></div>
    </div>

    <div class="footer-player__inner">
        <!-- ZON 1 — Sol: Albüm kapağı 85×85 + Meta bilgileri -->
        <div class="footer-player__meta">
            <img class="footer-player__cover" id="footer_songimages" src="<?= $h((string)$currentArt) ?>" alt="<?= $currentSong ?> albüm kapağı" loading="lazy">
            <div class="footer-player__info">
                <span class="footer-player__text footer-player__song"><img src="<?= $iconMusic ?>" width="10" height="10" loading="lazy">Şarkı Adı : <span id="footer_songname"><?= $currentSong ?></span></span>
                <span class="footer-player__text footer-player__album"><img src="<?= $iconCd ?>" width="10" height="10" loading="lazy">Albüm : <span id="footer_albumadi"><?= $currentAlbum ?></span></span>
                <span class="footer-player__text footer-player__artist"><img src="<?= $iconMic ?>" width="10" height="10" loading="lazy">Sanatçı : <span id="footer_sanatci"><?= $currentArtist ?></span></span>
                <span class="footer-player__text footer-player__time">
                    <img class="footer-player__icon" src="<?= $iconTimer ?>" alt="" width="13" height="13" loading="lazy">
                    <span id="gettime_audio"><?= $currentElapsed ?></span>
                    <span class="footer-player__sep">/</span>
                    <span id="footer_sure"><?= $currentDuration ?></span>
                    <span class="footer-player__sep">/</span>
                    <img class="footer-player__icon" src="<?= $iconBitrate ?>" alt="" width="13" height="13" loading="lazy">
                    <span id="footer_bitrate"><?= $currentBitrate ?></span>
                </span>
            </div>
        </div>

        <!-- ZON 2 — Orta: Oynatma butonları (⏮ ▶ ⏹ ⏭) -->
        <section class="footer-player__controls" aria-label="Oynatma kontrolleri">
            <button class="footer-player__btn" id="prevBtn" aria-label="Önceki">
                <img src="<?= $iconBack ?>" alt="" width="20" height="20" loading="lazy">
            </button>
            <button class="footer-player__btn footer-player__btn--play" id="playBtnWrap" aria-label="Oynat">
                <img class="footer-player__play-icon" id="playBtn" src="<?= $iconPlay ?>" alt="Oynat" width="20" height="20" loading="lazy">
                <img class="footer-player__play-icon footer-player__play-icon--pause is-hidden" id="pauseBtn" src="<?= $iconPause ?>" alt="Duraklat" width="20" height="20" loading="lazy">
            </button>
            <button class="footer-player__btn" id="stopBtn" aria-label="Durdur">
                <img src="<?= $iconStop ?>" alt="" width="20" height="20" loading="lazy">
            </button>
            <button class="footer-player__btn" id="nextBtn" aria-label="Sonraki">
                <img src="<?= $iconNext ?>" alt="" width="20" height="20" loading="lazy">
            </button>
        </section>

<?php if ($dm->showUtilityIcons()): ?>
        <section class="footer-player__controls" aria-label="Oynatma kontrolleri">
<?php endif; ?>
             <div class="footer__utility-icons">
                <button class="footer__utility-icon" aria-label="Tekrarla" data-action="repeat" title="Tekrarla">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/repat.png') ?>" height="20" width="20" title="Tekrarla" />
                </button>
                <button class="footer__utility-icon" aria-label="Karıştır" data-action="shuffle" title="Karıştır">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/karistir.png') ?>" height="20" width="20" title="Karıştır" />
                </button>
                <button class="footer__utility-icon" aria-label="Ekolayzır" data-action="openEqualizerPopup" title="Ekolayzır">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/equalizer.png') ?>" height="20" width="20" title="Tam Ekran" />
                </button>
                <button class="footer__utility-icon" aria-label="Tam Ekran" data-action="fullscreen" title="Tam Ekran">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/full-screen.png') ?>" height="20" width="20" title="AI Terminal" />
                </button>
                <button class="footer__utility-icon" aria-label="Playlist" data-action="openPlaylistPopup" title="Playlist">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/playlist1.png') ?>" height="20" width="20" title="Playlist" />
                </button>
                <button class="footer__utility-icon" aria-label="Wi-Fi" data-action="openWifiPopup" title="Wi-Fi">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/wifi-1-on.png') ?>" height="20" width="20" title="Wi-Fi" />
                </button>
                <button class="footer__utility-icon" aria-label="Bluetooth" data-action="openBluetoothPopup" title="Bluetooth" style="width:13px;height:13px;">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/bluethoot1.png') ?>" height="16" width="16" title="Bluetooth" />
                </button>
                <button class="footer__utility-icon" aria-label="Ayarlar" data-action="openQuickSettingsPopup" title="Ayarlar">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/settings-white.png') ?>" height="20" width="20" title="Ayarlar" />
                </button>
                <button class="footer__utility-icon" aria-label="AI Terminal" data-action="openAI_TerminalPopup" title="AI Terminal">
                    <img src="<?= $h($assetsUrl . '/Image/res-pink/terminal.png') ?>" height="20" width="20" title="Ekolayzır" />
                </button>
            </div>
<?php if ($dm->showUtilityIcons()): ?>
        </section>
<?php else: ?>
        </section>
<?php endif; ?>

<!-- ZON 3 — Sağ: Hoparlör + Volume slider -->
<?php if ($dm->showVolume()): ?>
        <div class="footer-player__volume">
            <img id="volumeIcon" src="<?= $iconVol ?>" alt="Ses" width="20" height="20" loading="lazy">
            <div class="footer-player__volume-track" id="volumeclick">
                <input type="range" id="volume" class="footer-player__volume-slider"
                       min="0" max="1" step="0.01" value="<?= $volumeRatio ?>"
                       aria-label="Ses seviyesi">
                <div class="footer-player__volume-fill" id="volume2" data-progress="<?= $volumePct ?>"></div>
            </div>
            <span class="footer-player__volume-value">% <?= $volumePct ?></span>
        </div>
<?php endif; ?>

    </div>
</footer>

<!-- CorePlayer modülleri -->
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.shared.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.volume.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.seekbar.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.controls.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.progressbar.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/core/helper.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/core/footer.init.js?v=2.0.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/features/welcome-modal.js?v=2.0.0"></script>
