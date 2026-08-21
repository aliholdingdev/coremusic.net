<?php declare(strict_types=1);
/**
 * CoreMusic Home — Footer Partial (Player Bar)
 * BEM: .footer → __inner → __meta-section | __controls-section | __utility-section
 * Design: 02-home-screens.md, 01-component-inventory.md
 * Height: 90px (1024) · 104px (desktop) · 138px (4K)
 * Version: 2.0.0 — 2026-08-21
 */

$currentSong   = htmlspecialchars($_SESSION['current_song'] ?? 'Şarkı Adı', ENT_QUOTES, 'UTF-8');
$currentAlbum  = htmlspecialchars($_SESSION['current_album'] ?? 'Albümüm', ENT_QUOTES, 'UTF-8');
$currentArtist = htmlspecialchars($_SESSION['current_artist'] ?? 'Sanatçı', ENT_QUOTES, 'UTF-8');
$currentDuration = htmlspecialchars($_SESSION['current_duration'] ?? '00:05:00', ENT_QUOTES, 'UTF-8');
$currentArt    = $_SESSION['current_art'] ?? '/assets.coremusic.net/Image/res-pink/default-album.png';
$bitrate       = '320 kbps';
$volumePct     = (int)($_SESSION['volume'] ?? 100);
$currentTime   = '09:00:00';
?>
<footer class="footer" role="contentinfo">
    <!-- Pembe ilerleme çubuğu — footer üstü, full-width -->
    <div class="footer__progress">
        <div class="footer__progress-bar" style="width: 65%;" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="footer__inner">
        <!-- ZON 1 — Sol: Albüm Kapağı + Metadata -->
        <div class="footer__meta-section">
            <img
                class="footer__album-art"
                src="<?= htmlspecialchars($currentArt, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= $currentSong ?> albüm kapağı"
                width="120"
                height="120"
                loading="lazy"
            >
            <div class="footer__meta-stack">
                <div class="footer__text footer__song-name">
                    <span class="fp-icon" aria-hidden="true">&#9835;</span>
                    <span class="fp-label">Şarkı Adı :</span>
                    <span class="fp-value"><?= $currentSong ?></span>
                </div>
                <div class="footer__text footer__album-name">
                    <span class="fp-icon" aria-hidden="true">&#9898;</span>
                    <span class="fp-label">Albümüm :</span>
                    <span class="fp-value"><?= $currentAlbum ?></span>
                </div>
                <div class="footer__text footer__singer-name">
                    <span class="fp-icon" aria-hidden="true">&#9734;</span>
                    <span class="fp-label">Sanatçı :</span>
                    <span class="fp-value"><?= $currentArtist ?></span>
                </div>
                <div class="footer__text footer__sure">
                    <span class="fp-label">Süre :</span>
                    <span class="fp-value"><?= $currentTime ?> / <?= $currentDuration ?></span>
                    <span class="fp-sep">/</span>
                    <span class="fp-label">Bit rate :</span>
                    <span class="fp-value"><?= $bitrate ?></span>
                </div>
            </div>
        </div>

        <!-- ZON 2 — Orta: Player Kontrolleri -->
        <div class="footer__controls-section">
            <div class="footer__controls">
                <!-- Önceki -->
                <button class="player-btn" type="button" aria-label="Önceki şarkı" data-action="prev">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
                    </svg>
                </button>
                <!-- Oynat/Duraklat -->
                <button class="player-btn player-btn--play" type="button" aria-label="Oynat" data-action="play">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </button>
                <!-- Durdur -->
                <button class="player-btn" type="button" aria-label="Durdur" data-action="stop">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <rect x="6" y="6" width="12" height="12"/>
                    </svg>
                </button>
                <!-- Sonraki -->
                <button class="player-btn" type="button" aria-label="Sonraki şarkı" data-action="next">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- ZON 3 — Sağ: Utility İkonları + Volume -->
        <div class="footer__utility-section">
            <div class="footer__utility-icons">
                <button class="footer__utility-icon" type="button" aria-label="Karışık çal" data-action="shuffle">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/>
                    </svg>
                </button>
                <button class="footer__utility-icon" type="button" aria-label="Tekrarla" data-action="repeat">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                    </svg>
                </button>
            </div>

            <!-- Volume Slider -->
            <div class="footer__volume-group">
                <button class="footer__utility-icon" type="button" aria-label="Ses" data-action="mute">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                    </svg>
                </button>
                <input
                    type="range"
                    class="footer__volume-slider"
                    min="0"
                    max="100"
                    value="<?= $volumePct ?>"
                    aria-label="Ses seviyesi"
                    data-action="volume"
                >
                <span class="footer__volume-value">% <?= $volumePct ?></span>
            </div>
        </div>
    </div>
</footer>
