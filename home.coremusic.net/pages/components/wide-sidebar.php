<?php declare(strict_types=1);
/**
 * pages/components/wide-sidebar.php — "Şu an çalan" kenar çubuğu (Playlist + Geçmiş)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (_player.css — SAĞ KENAR ÇUBUĞU bloğu)
 * SSOT     : .ai/.png/home-1920/Linux 1920 -  Playlist  Page.png → 4 bölüm
 *            .ai/.png/home-1024/Linux  1024 - Playlist Page.png  → yalnız 1+2
 * Girdi    : WideSidebarComponent ($this) — $_SESSION current_* (PlayerInfoComponent ile aynı kaynak)
 * Bölümler : 1 profil kartı (dairesel avatar +5 buton) · 2 öneriler (2×2 sanatçı ızgarası)
 *            · 3 durum kartı (yalnız wide) · 4 boş kart (yalnız wide)
 * Not      : Başlıklar Figma'daki gibi ikonlu değer satırları; etiket metni yalnız
 *            "Yıldız:" ve "Süre :" — hardcoded İRSAT GÜR verisi kaldırıldı.
 * ------------------------------------------------------------------
 *
 * @var \CoreMusic\Home\Component\WideSidebarComponent $this
 */
$figmaBase    = $this->figmaBase;
$sbCover      = $this->cover;
$sbTitle      = $this->title;
$sbArtist     = $this->artist;
$sbAlbum      = $this->album;
$sbDuration   = $this->duration;
$sbElapsed    = $this->elapsed;
$sbBitrate    = $this->bitrate;
$sbStars      = $this->stars;
$sbShowStatus = $this->showStatusCards;
$sbRecRows    = $this->recRows;
$sbArtistsUrl = $this->artistsHref;
?>
<aside class="page-sidebar--figma-wide" aria-label="Şu an çalan">

    <!-- ══ 1) PROFİL KARTI — dairesel avatar + şarkı + 5 aksiyon butonu ══ -->
    <div class="page-sidebar__profile-card">
        <div class="page-sidebar__art">
            <img class="page-sidebar__avatar" src="<?= $this->h($sbCover) ?>" alt="Sanatçı Görseli" width="140" height="140" loading="lazy">
        </div>

        <div class="page-sidebar__meta">
            <h2 class="page-sidebar__song-title" id="np-rec-title"><?= $this->h($sbTitle) ?></h2>
            <p class="page-sidebar__song-sub" id="np-rec-sub"><?= $this->h($sbArtist) ?> , <?= $this->h($sbAlbum) ?></p>
        </div>

        <div class="page-sidebar__actions">
            <button type="button" class="page-action-btn page-action-btn--pink" title="Şarkı Sözleri" aria-label="Şarkı Sözleri">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                </svg>
            </button>
            <button type="button" class="page-action-btn page-action-btn--glass" title="Favorilere Ekle" aria-label="Favorilere Ekle">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#ff4fd8" aria-hidden="true">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>
            <button type="button" class="page-action-btn page-action-btn--glass" title="Beğen" aria-label="Beğen">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                    <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                </svg>
            </button>
            <button type="button" class="page-action-btn page-action-btn--glass" title="İndir" aria-label="İndir">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                </svg>
            </button>
            <button type="button" class="page-action-btn page-action-btn--glass" title="Daha Fazla" aria-label="Daha Fazla">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                    <path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- ══ 2) ÖNERİLER — 2 satır × (2 etiket + 4 dairesel sanatçı) ══ -->
    <div class="page-sidebar__recs">
        <?php foreach ($sbRecRows as $sbRow): ?>
            <p class="page-sidebar__rec-labels">
                <?php foreach ($sbRow['labels'] as $sbLabel): ?>
                    <span class="page-sidebar__rec-label"><?= $this->h($sbLabel) ?></span>
                <?php endforeach; ?>
            </p>
            <div class="page-sidebar__artist-grid">
                <?php foreach ($sbRow['artists'] as $sbArt): ?>
                    <a class="page-sidebar__artist-item" href="<?= $this->h($sbArtistsUrl) ?>">
                        <span class="page-sidebar__artist-thumb">
                            <img src="<?= $this->h($sbArt['img']) ?>" alt="<?= $this->h($sbArt['name']) ?>" loading="lazy">
                        </span>
                        <span class="page-sidebar__artist-name"><?= $this->h($sbArt['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($sbShowStatus): ?>
    <!-- ══ 3) DURUM KARTI — Figma bilgi (1681,328) 218×114 (yalnız 1920 / 3840) ══ -->
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

    <!-- ══ 4) BOŞ KART — Figma durum (1680,462) 224×507 ══ -->
    <div class="page-sidebar__status-card" aria-hidden="true"></div>
    <?php endif; ?>
</aside>
