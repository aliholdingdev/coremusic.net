<?php declare(strict_types=1);
/**
 * pages/components/wide-sidebar.php — 1920 wide "Şu an çalan" kenar çubuğu
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (_player.css — WIDE 1920 bloğu)
 * SSOT     : Figma 3155:19834 (Playlist) ve 3155:22392 (Geçmiş) — sağ kenar çubuğu iki frame'de aynı
 *            kapak (1681,87) 221×221 · bilgi (1681,328) 218×114 · durum (1680,462) 224×507
 * Girdi    : WideSidebarComponent ($this) — $_SESSION current_* (PlayerInfoComponent ile aynı kaynak)
 * Not      : Başlıklar Figma'daki gibi ikonlu değer satırları; etiket metni yalnız "Yıldız:" ve "Süre :"
 * Değişiklik: hardcoded `cover-ursat-gur.png` · `Bit rate : 350 kbps` · `Süre : 00:00:00`
 *             kaldırıldı → gerçek çalan kapak/veri (eski "İRSAT GÜR" verisi hatalı idi)
 * ------------------------------------------------------------------
 *
 * @var \CoreMusic\Home\Component\WideSidebarComponent $this
 */
$figmaBase  = $this->figmaBase;
$sbCover    = $this->cover;
$sbTitle    = $this->title;
$sbArtist   = $this->artist;
$sbAlbum    = $this->album;
$sbDuration = $this->duration;
$sbElapsed  = $this->elapsed;
$sbBitrate  = $this->bitrate;
$sbStars    = $this->stars;
?>
<aside class="page-sidebar--figma-wide" aria-label="Şu an çalan">
    <div class="page-sidebar__cover-card">
        <img class="page-sidebar__cover-img" src="<?= $this->h($sbCover) ?>" alt="Albüm kapağı" width="221" height="221" loading="lazy">
    </div>

    <div class="page-sidebar__info-card">
        <p class="page-sidebar__info-row page-sidebar__info-row--title">
            <img class="page-sidebar__info-icon" src="<?= $this->h($figmaBase . '/ico-music.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__info-val" id="np-info-title"><?= $this->h($sbTitle) ?></span>
        </p>
        <p class="page-sidebar__info-row page-sidebar__info-row--album">
            <img class="page-sidebar__info-icon" src="<?= $this->h($figmaBase . '/ico-cd.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__info-val" id="np-info-album"><?= $this->h($sbAlbum) ?></span>
        </p>
        <p class="page-sidebar__info-row page-sidebar__info-row--artist">
            <img class="page-sidebar__info-icon" src="<?= $this->h($figmaBase . '/ico-mic.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__info-val" id="np-info-artist"><?= $this->h($sbArtist) ?></span>
        </p>
        <p class="page-sidebar__info-row page-sidebar__info-row--stars">
            <img class="page-sidebar__info-icon" src="<?= $this->h($figmaBase . '/ico-star-2.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__info-label">Yıldız:</span>
            <span class="page-sidebar__info-stars" aria-label="<?= $this->h((string)$sbStars) ?> yıldız">
                <?php for ($s = 0; $s < $sbStars; $s++): ?>
                    <img src="<?= $this->h($figmaBase . '/ico-star-info.png') ?>" width="11" height="10" alt="">
                <?php endfor; ?>
            </span>
        </p>
        <p class="page-sidebar__info-row page-sidebar__info-row--bitrate">
            <img class="page-sidebar__info-icon" src="<?= $this->h($figmaBase . '/ico-bitrate.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__info-val">Bit rate : <?= $this->h($sbBitrate) ?></span>
            <img class="page-sidebar__format page-sidebar__format--mp3" src="<?= $this->h($figmaBase . '/ico-mp3.png') ?>" width="17" height="8" alt="MP3">
            <img class="page-sidebar__format page-sidebar__format--mp4" src="<?= $this->h($figmaBase . '/ico-mp4.png') ?>" width="19" height="13" alt="MP4">
            <img class="page-sidebar__format page-sidebar__format--vlc" src="<?= $this->h($figmaBase . '/ico-vlc.png') ?>" width="11" height="10" alt="VLC">
        </p>
        <p class="page-sidebar__info-row page-sidebar__info-row--time">
            <img class="page-sidebar__info-icon" src="<?= $this->h($figmaBase . '/ico-clock.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__info-val">Süre : <?= $this->h($sbElapsed) ?> / <?= $this->h($sbDuration) ?></span>
        </p>
        <div class="page-sidebar__progress" aria-hidden="true">
            <img class="page-sidebar__progress-play" src="<?= $this->h($figmaBase . '/ico-play.png') ?>" width="11" height="10" alt="">
            <span class="page-sidebar__progress-track">
                <span class="page-sidebar__progress-fill"></span>
            </span>
        </div>
    </div>

    <div class="page-sidebar__status-card" aria-hidden="true"></div>
</aside>
