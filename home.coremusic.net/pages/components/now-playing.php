<?php declare(strict_types=1);
/**
 * pages/components/now-playing.php — Bileşen: Şu An Çalan (v2)
 * PNG SSOT: .ai/.png/home-1024/ (embedded sol 42%) + .ai/.png/home-1920/ (wide sol üst)
 * Yükleyen: CoreMusic\Home\Component\NowPlayingComponent
 * Erişim: $this->{song,album,artist,bitrate,elapsed,duration,seekPct,imgNowArt,imgStar}
 *         $this->variant->isWide()
 */
?>
<?php if ($this->variant->isWide()): ?>
<section class="now-playing now-playing--wide" aria-label="Şu an çalan">
    <div class="now-playing__art now-playing__art--wide"><img src="<?= $this->h($this->imgNowArt) ?>" alt="Çalan şarkının albüm kapağı" width="136" height="136" loading="lazy"/></div>
    <div class="now-playing__info now-playing__info--wide">
        <h1 class="now-playing__meta-line"><span class="now-playing__meta-label">Şarkı Adı :</span> <span class="now-playing__meta-value"><?= $this->song ?></span></h1>
        <p class="now-playing__meta-line"><span class="now-playing__meta-label">Album :</span> <span class="now-playing__meta-value"><?= $this->album ?></span></p>
        <p class="now-playing__meta-line"><span class="now-playing__meta-label">Sanatçı :</span> <span class="now-playing__meta-value"><?= $this->artist ?></span></p>
        <div class="now-playing__meta-line">
            <span class="now-playing__meta-label">Yıldız :</span>
            <span class="now-playing__stars" role="img" aria-label="Değerlendirme: 5 üzerinden 5 yıldız"><img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/><img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/><img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/><img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/><img src="<?= $this->h($this->imgStar) ?>" alt="" width="14" height="14" loading="lazy"/></span>
        </div>
        <p class="now-playing__meta-line"><span class="now-playing__meta-label">Bit rate :</span> <span class="now-playing__meta-value"><?= $this->bitrate ?></span></p>
        <p class="now-playing__meta-line"><span class="now-playing__meta-label">Süre :</span> <span class="now-playing__meta-value"><?= $this->elapsed ?> / <?= $this->duration ?></span></p>
        <!-- PNG home-1920: kart altı ilerleme çubuğu — pembe play dairesi + bar (salt görüntü) -->
        <div class="media-progress media-progress--wide" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>">
            <span class="media-progress__play" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></span>
            <div class="media-progress__bar"><div class="media-progress__fill" data-progress="<?= $this->seekPct ?>"></div></div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="now-playing" aria-label="Şu an çalan">
    <div class="now-playing__art"><img src="<?= $this->h($this->imgNowArt) ?>" alt="Çalan şarkının albüm kapağı" width="100" height="100" loading="lazy"/></div>
    <div class="now-playing__info">
        <h1 class="now-playing__title"><?= $this->song ?></h1>
        <p class="now-playing__subtitle"><?= $this->album ?></p>
        <p class="now-playing__artist"><?= $this->artist ?></p>
        <!-- PNG birebir: medya ilerleme göstergesi — SEEK DEĞİL, salt görüntü
             progress bar (tıklama/drag YOK; süre — bar — süre tek satır) -->
        <div class="media-progress" role="progressbar" aria-label="Medya ilerlemesi" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->seekPct ?>" aria-valuetext="<?= $this->elapsed ?> / <?= $this->duration ?>">
            <span class="media-progress__time" id="np_time_current"><?= $this->elapsed ?></span>
            <div class="media-progress__bar"><div class="media-progress__fill" data-progress="<?= $this->seekPct ?>"></div></div>
            <span class="media-progress__time media-progress__time--total" id="np_time_total"><?= $this->duration ?></span>
        </div>
    </div>
</section>
<?php endif; ?>
