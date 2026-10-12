<?php declare(strict_types=1);
/**
 * pages/components/player-info.php — Player Info & Now Playing (Merged)
 *
 * HTML contract → assets.coremusic.net/Css/04_Components/_player-info.css (v2.0)
 *   wide     (isWide ≥1025px) → <section class="player-info player-info--wide">
 *   embedded (≤1024px)        → <section class="now-playing now-playing--embedded">
 *
 * Figma SSOT: node 1646:17727 (1024 · 392×131) · node 2849:21489 (1920 · 469×184)
 * JS: assets.coremusic.net/js/components/interactive/PlayerInfoComponent.js
 *
 * @var \CoreMusic\Home\Component\PlayerInfoComponent $this
 */
$isWide   = $this->variant->isWide();
$nonce    = $this->h((string)($_SESSION['csp_nonce'] ?? ''));
$cmConfig = htmlspecialchars(
    json_encode(
        [
            'song'     => strip_tags($this->song),
            'artist'   => strip_tags($this->artist),
            'progress' => $this->seekPct,
        ],
        JSON_HEX_TAG | JSON_HEX_APOS
    ),
    ENT_QUOTES,
    'UTF-8'
);
?>
<?php if ($isWide): ?>
<section class="player-info player-info--wide" aria-label="Şu an çalan" data-cm-component="cm-player-info" data-cm-config='<?= $cmConfig ?>'>
    <div class="player-info__cover">
        <img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" loading="lazy">
    </div>
    <div class="player-info__info-panel">
        <h1 class="player-info__row player-info__title"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconMusic) ?>" class="player-info__label-icon-img player-info__label-icon-img--music" alt="" loading="lazy"></span>Şarkı Adı :</span> <span class="now-playing__meta-value"><?= $this->song ?></span></h1>
        <p class="player-info__row player-info__album"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconCd) ?>" class="player-info__label-icon-img player-info__label-icon-img--cd" alt="" loading="lazy"></span>Albüm :</span> <span class="now-playing__meta-value"><?= $this->album ?></span></p>
        <p class="player-info__row player-info__singer"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconMic) ?>" class="player-info__label-icon-img player-info__label-icon-img--mic" alt="" loading="lazy"></span>Sanatçı :</span> <span class="now-playing__meta-value"><?= $this->artist ?></span></p>
        <p class="player-info__row player-info__star"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconStar) ?>" class="player-info__label-icon-img player-info__label-icon-img--star" alt="" loading="lazy"></span>Yıldız :</span> <span class="player-info__stars" role="img" aria-label="Değerlendirme: 5 üzerinden 5 yıldız"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"><img src="<?= $this->h($this->imgStar) ?>" alt="" loading="lazy"></span></p>
        <p class="player-info__row player-info__bitrate"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconBitrate) ?>" class="player-info__label-icon-img player-info__label-icon-img--bitrate" alt="" loading="lazy"></span>Bit rate :</span> <span class="now-playing__meta-value"><?= $this->bitrate ?></span><span class="player-info__badges"><img src="<?= $this->h($this->iconMp3) ?>" class="player-info__badge player-info__badge--mp3" alt="MP3" loading="lazy"><img src="<?= $this->h($this->iconMp4) ?>" class="player-info__badge player-info__badge--mp4" alt="MP4" loading="lazy"><img src="<?= $this->h($this->iconVlc) ?>" class="player-info__badge player-info__badge--vlc" alt="VLC" loading="lazy"></span></p>
        <p class="player-info__row player-info__duration"><span class="player-info__label"><span class="player-info__label-icon"><img src="<?= $this->h($this->iconTimer) ?>" class="player-info__label-icon-img player-info__label-icon-img--timer" alt="" loading="lazy"></span>Süre :</span> <span class="now-playing__meta-value"><?= $this->elapsed ?> / <?= $this->duration ?></span></p>
        <div class="player-info__transport">
            <img src="<?= $this->h($this->iconPlay) ?>" class="player-info__text-img player-info__play" alt="Oynat" loading="lazy">
            <div class="player-info__progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>">
                <div class="player-info__progress__bar"><div class="player-info__progress__fill" data-progress="<?= $this->seekPct ?>"></div></div>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="now-playing now-playing--embedded" aria-label="Şu an çalan" data-cm-component="cm-player-info" data-cm-config='<?= $cmConfig ?>'>
    <div class="now-playing__art now-playing__art--embedded">
        <img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" loading="lazy">
    </div>
    <div class="now-playing__info now-playing__info--embedded">
        <h1 class="now-playing__title"><?= $this->song ?></h1>
        <p class="now-playing__subtitle"><?= $this->album ?></p>
        <p class="now-playing__artist"><?= $this->artist ?></p>
    </div>
    <div class="media-progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>" aria-valuetext="<?= $this->elapsed ?> / <?= $this->duration ?>">
        <span class="media-progress__time" id="np_time_current"><?= $this->elapsed ?></span>
        <div class="media-progress__bar"><div class="media-progress__fill" data-progress="<?= $this->seekPct ?>"></div></div>
        <span class="media-progress__time media-progress__time--total" id="np_time_total"><?= $this->duration ?></span>
    </div>
</section>
<?php endif; ?>
