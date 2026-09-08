<?php declare(strict_types=1);
/**
 * footer.php — CoreMusic Footer Player Bar
 * Layer: L3 Presentation · 03_Layout (_footer.css) + 04_Components (seek, volume)
 * SSOT: .ai/.png/home-1024/ + home-1920/ (PNG sadakati, Guardrail #11)
 * Yükseklik: CSS token: --footer-h (90/96/100/104/120/130px — a-layout-tokens.css)
 * Yapı: KAİ coreplayer sözleşmesi (seekbarclick/volume/volume2/mplay) + PNG birebir görünüm
 * Version: 1.3.0 — 2026-09-06 (Volume/Seekbar/Media Play — KAİ entegrasyonu + 4K token)
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

/* ── Çalma durumu (PNG metinleri varsayılan) ── */
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

$iconBack  = $h($assetsUrl . '/Image/res-pink/media-back.png');
$iconPlay  = $h($assetsUrl . '/Image/res-pink/media-play.png');
$iconPause = $h($assetsUrl . '/Image/res-pink/media-pause.png');
$iconStop  = $h($assetsUrl . '/Image/res-pink/media-stop.png');
$iconNext  = $h($assetsUrl . '/Image/res-pink/media-ileri.png');
$iconVol   = $h($assetsUrl . '/Image/res-pink/volume-high.png');

/* Satır ikonları — PNG mockup birebir (res-pink envanteri) */
$iconMusic   = $h($assetsUrl . '/Image/res-pink/music.png');
$iconCd      = $h($assetsUrl . '/Image/res-pink/cd-ico.png');
$iconMic     = $h($assetsUrl . '/Image/res-pink/mic-1.png');
$iconTimer   = $h($assetsUrl . '/Image/res-pink/timer.png');
$iconBitrate = $h($assetsUrl . '/Image/res-pink/bit-rate.png');
?>
<footer class="footer <?= $dm->allClasses() ?>" role="contentinfo" <?= $dm->dataAttributes() ?>>

    <!-- Seekbar — footer üstü, full-width (PNG: pembe bar ≈4px) · KAİ: #seekbarclick/#seekbar/#seekbar2 -->
    <div class="footer__progress" id="seekbarclick">
        <input
            type="range"
            id="seekbar"
            min="0"
            max="100"
            step="0.0001"
            value="<?= $progressPct ?>"
            aria-label="Şarkı pozisyonu"
        >
        <div class="footer__progress-bar" id="seekbar2" style="width: <?= $progressPct ?>%;" role="progressbar" aria-label="Çalma ilerlemesi" aria-valuenow="<?= $progressPct ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="footer__inner">

        <!-- ZON 1 — Sol: Albüm kapağı + "Etiket : Değer" meta satırları (PNG birebir) -->
        <div class="footer__meta-section">
            <img
                class="footer__album-art"
                id="footer_songimages"
                src="<?= $h((string)$currentArt) ?>"
                alt="<?= $currentSong ?> albüm kapağı"
                width="120"
                height="120"
                loading="lazy"
            >
            <div class="footer__meta-stack">
                <div class="footer__text footer__song-name">
                    <img class="fp-icon-img" src="<?= $iconMusic ?>" alt="" width="13" height="13" loading="lazy">
                    <span class="fp-label">Şarkı Adı :</span>
                    <span class="fp-value" id="footer_songname"><?= $currentSong ?></span>
                </div>
                <div class="footer__text footer__album-name">
                    <img class="fp-icon-img" src="<?= $iconCd ?>" alt="" width="13" height="13" loading="lazy">
                    <span class="fp-label">Album :</span>
                    <span class="fp-value" id="footer_albumadi"><?= $currentAlbum ?></span>
                </div>
                <div class="footer__text footer__singer-name">
                    <img class="fp-icon-img" src="<?= $iconMic ?>" alt="" width="13" height="13" loading="lazy">
                    <span class="fp-label">Sanatçı :</span>
                    <span class="fp-value" id="footer_sanatci"><?= $currentArtist ?></span>
                </div>
                <div class="footer__text footer__sure">
                    <img class="fp-icon-img" src="<?= $iconTimer ?>" alt="Süre" title="Süre" width="13" height="13" loading="lazy">
                    <span class="fp-value" id="gettime_audio"><?= $currentElapsed ?></span>
                    <span class="fp-sep">/</span>
                    <span class="fp-value" id="footer_sure"><?= $currentDuration ?></span>
                    <span class="fp-sep">/</span>
                    <img class="fp-icon-img" src="<?= $iconBitrate ?>" alt="Bit rate" title="Bit rate" width="13" height="13" loading="lazy">
                    <span class="fp-value" id="footer_bitrate"><?= $currentBitrate ?></span>
                </div>
            </div>
        </div>

        <!-- ZON 2 — Orta: 4 dairesel oynatma butonu (⏮ ▶ ⏹ ⏭ — PNG sırası, KAİ mplay API) -->
        <section class="footer__controls-section center" aria-label="Oynatma kontrolleri">
            <div class="footer__controls">
                <div class="player-btn c-desk-btn" id="prevBtn">
                    <img class="c-footer__playctrl1 icon-white" src="<?= $iconBack ?>" alt="Önceki" title="Önceki" loading="lazy" width="24" height="24">
                </div>
                <div class="player-btn player-btn--play c-desk-btn-play" id="playBtnWrap">
                    <img class="c-footer__playctrl2 c-footer__playctrl--play icon-white" id="playBtn" src="<?= $iconPlay ?>" alt="Oynat" title="Oynat" loading="lazy" width="28" height="28">
                    <img class="c-footer__playctrl2 c-footer__playctrl--pause is-hidden icon-white" id="pauseBtn" src="<?= $iconPause ?>" alt="Duraklat" title="Duraklat" loading="lazy" width="28" height="28">
                </div>
                <div class="player-btn c-desk-btn" id="stopBtn">
                    <img class="c-footer__playctrl3 icon-white" src="<?= $iconStop ?>" alt="Durdur" title="Durdur" loading="lazy" width="24" height="24">
                </div>
                <div class="player-btn c-desk-btn" id="nextBtn">
                    <img class="c-footer__playctrl4 icon-white" src="<?= $iconNext ?>" alt="İleri" title="İleri" loading="lazy" width="24" height="24">
                </div>
            </div>
        </section>

        <!-- ZON 3 — Sağ: Hoparlör + pembe volume slider + % değeri (PNG birebir, KAİ sözleşme) -->
        <div class="footer__utility-section">
<?php if ($dm->showVolume()): ?>
            <div class="volume-set-slider">
                <img id="volumeIcon" class="c-footer__volume-slider-volume-size icon-white" src="<?= $iconVol ?>" height="20" width="20" alt="Ses Seviyesi" title="Ses Seviyesi" loading="lazy">
                <div class="footer__volume-track" id="volumeclick">
                    <input
                        type="range"
                        id="volume"
                        class="footer__volume-slider"
                        min="0"
                        max="1"
                        step="0.01"
                        value="<?= $volumeRatio ?>"
                        aria-label="Ses seviyesi"
                    >
                    <div class="footer__volume-fill" id="volume2" style="width: <?= $volumePct ?>%;"></div>
                </div>
                <p class="footer__volume-value c-footer__volume-slider-volume-size">% <?= $volumePct ?></p>
            </div>
<?php endif; ?>
        </div>
    </div>
</footer>

<!-- CorePlayer modülleri — KAİ sözleşme sırası: shared ÖNCE, sonra volume/seekbar/controls/progressbar -->
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.shared.js?v=1.3.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.volume.js?v=1.3.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.seekbar.js?v=1.3.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.controls.js?v=1.3.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/coreplayer/coreplayer.progressbar.js?v=1.3.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/core/helper.js?v=1.3.0"></script>
<script nonce="<?= $nonce ?>" src="<?= $h($assetsUrl) ?>/js/core/footer.init.js?v=1.3.0"></script>
