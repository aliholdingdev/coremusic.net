<?php declare(strict_types=1);
/**
 * pages/components/widget-grid.php — Wide/4K Widget Grid ("Div2 Button")
 *
 * HTML contract → assets.coremusic.net/Css/04_Components/_widget-grid.css
 *   <section class="widget-grid"> — yalnız wide/4K (isWide()) render edilir.
 *
 * Figma SSOT: node 2850:21494 (1920 · 752×184) — bkz. WidgetGridComponent.php doc-block
 * JS: assets.coremusic.net/js/components/composites/WidgetAreaComponent.js (data-cm-component="cm-home-widget-grid")
 *
 * @var \CoreMusic\Home\Component\WidgetGridComponent $this
 */
?>
<section class="widget-grid" aria-label="Widget'lar" data-cm-component="cm-home-widget-grid">

    <div class="widget-grid__row widget-grid__row--top">
        <div class="widget-card widget-card--speaker">
            <img src="<?= $this->h($this->iconSpeaker) ?>" class="widget-card__icon widget-card__icon--speaker" alt="" loading="lazy">
            <div class="widget-card__text">
                <p class="widget-card__label"><?= $this->speakerLabel ?></p>
                <p class="widget-card__value"><?= $this->speakerName ?></p>
            </div>
        </div>

        <div class="widget-card widget-card--weather">
            <img src="<?= $this->h($this->iconWeather) ?>" class="widget-card__icon widget-card__icon--weather" alt="" loading="lazy">
            <div class="widget-card__text">
                <p class="widget-card__label widget-card__label--title">Hava Durumu</p>
                <p class="widget-card__value" data-widget="weather-condition"><?= $this->weatherCondition ?></p>
                <p class="widget-card__value widget-card__value--sub" data-widget="weather-city"><?= $this->weatherCity ?></p>
            </div>
        </div>
    </div>

    <div class="widget-grid__row widget-grid__row--mid">
        <div class="widget-card widget-card--clock">
            <img src="<?= $this->h($this->iconClock) ?>" class="widget-card__icon widget-card__icon--clock" alt="" loading="lazy">
            <div class="widget-card__text">
                <p class="widget-card__value widget-card__value--clock-time" data-widget="clock-time"><?= $this->clockTime ?></p>
                <p class="widget-card__value widget-card__value--clock-date" data-widget="clock-date"><?= $this->clockDate ?></p>
            </div>
        </div>

        <a href="#" class="widget-card widget-card--eq" aria-label="Equalizer hızlı erişim">
            <img src="<?= $this->h($this->iconEqualizer) ?>" class="widget-card__icon widget-card__icon--eq" alt="" loading="lazy">
        </a>

        <div class="widget-card widget-card--storage">
            <img src="<?= $this->h($this->iconStorage) ?>" class="widget-card__icon widget-card__icon--storage" alt="" loading="lazy">
            <div class="widget-card__text">
                <p class="widget-card__label">Depolama</p>
                <p class="widget-card__value"><?= $this->storageUsed ?> / <?= $this->storageTotal ?></p>
                <div class="widget-card__progress" role="progressbar" aria-label="Depolama kullanımı" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $this->storagePct ?>">
                    <div class="widget-card__progress-track"><div class="widget-card__progress-fill" data-progress="<?= $this->storagePct ?>"></div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-grid__row widget-grid__row--bottom">
        <a href="#" class="widget-card widget-card--filemanager" aria-label="Dosya Yöneticisi">
            <img src="<?= $this->h($this->iconFolder) ?>" class="widget-card__icon widget-card__icon--filemanager" alt="" loading="lazy">
            <span class="widget-card__label">Kütüphanelerim</span>
        </a>

        <a href="#" class="widget-card widget-card--service widget-card--youtube-music" aria-label="Youtube Music">
            <img src="<?= $this->h($this->iconYoutubeMusic) ?>" class="widget-card__icon widget-card__icon--service" alt="" loading="lazy">
        </a>

        <a href="#" class="widget-card widget-card--service widget-card--youtube" aria-label="Youtube">
            <img src="<?= $this->h($this->iconYoutube) ?>" class="widget-card__icon widget-card__icon--service" alt="" loading="lazy">
        </a>

        <a href="#" class="widget-card widget-card--service widget-card--deezer" aria-label="Deezer">
            <img src="<?= $this->h($this->iconDeezer) ?>" class="widget-card__icon widget-card__icon--service" alt="" loading="lazy">
        </a>
    </div>

</section>
