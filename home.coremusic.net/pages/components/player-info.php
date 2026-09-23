<?php declare(strict_types=1);
/**
 * pages/components/player-info.php — Player Info & Now Playing (Merged)
 *
 * @var \CoreMusic\Home\Component\PlayerInfoComponent $this
 */
$isWide = $this->variant->isWide();
?>
<?php if ($isWide): ?>
<player_info class="player-info player-info--wide" aria-label="Şu an çalan" data-cm-component="cm-player-info" data-cm-config='<?= htmlspecialchars(json_encode(["song" => strip_tags($this->song), "artist" => strip_tags($this->artist), "progress" => $this->seekPct], JSON_HEX_TAG | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8') ?>'>
    
    <div class="player-info__cover">
        <img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" loading="lazy"/>
    </div>
    
    <div class="player-info__info-panel player-info__info-panel--wide">
        <h1 class="player-info__title"><span class="player-info__label"><img src="<?= $this->h($this->iconMusic) ?>" class="player-info__text-img" loading="lazy">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Şarkı Adı :</span> <span class="now-playing__meta-value"><?= $this->song ?></span></h1>
        <p class="player-info__album"><span class="player-info__label"><img src="<?= $this->h($this->iconCd) ?>" class="player-info__text-img" loading="lazy">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Albüm :</span> <span class="now-playing__meta-value"><?= $this->album ?></span></p>
        <p class="player-info__singer"><span class="player-info__label"><img src="<?= $this->h($this->iconMic) ?>" class="player-info__text-img" loading="lazy">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sanatçı :</span> <span class="now-playing__meta-value"><?= $this->artist ?></span></p>
        <div class="player-info__star">
            <span class="player-info__label"><img src="<?= $this->h($this->iconStar) ?>" class="player-info__text-img" loading="lazy">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Yıldız : <span class="player-info__stars player-info__label-value" role="img" aria-label="Değerlendirme: 5 üzerinden 5 yıldız">
                <img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/>
                <img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/>
                <img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/>
                <img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/>
                <img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/>
                </span>
            </span>
        </div>
        <p class="player-info__bitrate"><span class="player-info__label">
            <img src="<?= $this->h($this->iconBitrate) ?>" class="player-info__text-img" loading="lazy"> 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Bit rate :</span> 
            <span class="now-playing__meta-value">
                <?= $this->bitrate ?>
            </span>
        </p>
        <p class="player-info__duration"><span class="player-info__label">
            <img src="<?= $this->h($this->iconTimer) ?>" class="player-info__text-img" loading="lazy"> 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Süre :</span> 
            <span class="now-playing__meta-value">
                <?= $this->elapsed ?> / <?= $this->duration ?>
            </span>
        </p>
        
        <img src="<?= $this->h($this->iconPlay) ?>" class="player-info__text-img" loading="lazy">    
        <div class="player-info__progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>">
            <div class="player-info__progress__bar"><div class="player-info__progress__fill" data-progress="<?= $this->seekPct ?>" style="width: <?= $this->seekPct ?>%;"></div></div>
        </div>
    </div>
</player_info>
<?php else: ?>
<player_info class="now-playing now-playing--embedded" aria-label="Şu an çalan" data-cm-component="cm-player-info" data-cm-config='<?= htmlspecialchars(json_encode(["song" => strip_tags($this->song), "artist" => strip_tags($this->artist), "progress" => $this->seekPct], JSON_HEX_TAG | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8') ?>'>
    <div class="now-playing__art now-playing__art--embedded"><img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" width="100" height="100" loading="lazy"/></div>
    <div class="now-playing__info now-playing__info--embedded">
        <h1 class="now-playing__title"><?= $this->song ?></h1>
        <p class="now-playing__subtitle"><?= $this->album ?></p>
        <p class="now-playing__artist"><?= $this->artist ?></p>
        
        <div class="media-progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>" aria-valuetext="<?= $this->elapsed ?> / <?= $this->duration ?>">
            <span class="media-progress__time" id="np_time_current"><?= $this->elapsed ?></span>
            <div class="media-progress__bar"><div class="media-progress__fill" data-progress="<?= $this->seekPct ?>" style="width: <?= $this->seekPct ?>%;"></div></div>
            <span class="media-progress__time media-progress__time--total" id="np_time_total"><?= $this->duration ?></span>
        </div>
    </div>
</player_info>
<?php endif; ?>
