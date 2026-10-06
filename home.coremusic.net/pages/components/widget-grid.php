<?php declare(strict_types=1);
/**
 * pages/components/widget-grid.php — Widget Grid ("Div2 Button")
 *
 * HTML contract → assets.coremusic.net/Css/03_Layout/_widget-grid.css
 *   <section class="widget-grid"> — embedded (1024) + wide/4K (1920+)
 *
 * Slot sayımı — aaa.md §13.9 (çelişkide kural PNG/Figma'yı ezer):
 *   embedded 12 slot → row1 2 · row2 5 · row3 5
 *   wide/4K  20 slot → row1 4 · row2 8 · row3 8
 *   (wide row1 4. slot = 3 satır boyu uzanan tam boy cam kart, .widget-card--tall)
 * Boş slotlar görünür cam konturlu .widget-card--empty olarak üretilir (PNG SSOT).
 *
 * Figma SSOT: node 1639:10160 (1024 · 365×171) · node 2850:21494 (1920 · 752×184)
 * JS: assets.coremusic.net/js/components/composites/WidgetAreaComponent.js (data-cm-component="cm-home-widget-grid")
 *
 * @var \CoreMusic\Home\Component\WidgetGridComponent $this
 */

$midEmpty  = $this->isWide() ? 6 : 3;   /* row2: Saat + EQ + boşlar */
$botEmpty  = $this->isWide() ? 3 : 0;   /* row3: 5 dolu + boşlar (wide) */
$emptyCard = '<div class="widget-card widget-card--empty" aria-hidden="true"></div>';
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

        <?php if ($this->isWide()): ?>
        <!-- wide row1 3. slot: boş cam kart (Figma 2850:21574) -->
        <?= $emptyCard ?>
        <?php endif; ?>
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

        <?php for ($i = 0; $i < $midEmpty; $i++) echo $emptyCard; ?>
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

        <a href="#" class="widget-card widget-card--service widget-card--favori" aria-label="Favoriler">
            <img src="<?= $this->h($this->iconFavori) ?>" class="widget-card__icon widget-card__icon--service" alt="" loading="lazy">
        </a>

        <a href="#" class="widget-card widget-card--service widget-card--bitrate" aria-label="Bit Hızı">
            <img src="<?= $this->h($this->iconBitrate) ?>" class="widget-card__icon widget-card__icon--service" alt="" loading="lazy">
        </a>

        <?php for ($i = 0; $i < $botEmpty; $i++) echo $emptyCard; ?>
    </div>

    <?php if ($this->isWide()): ?>
    <!-- wide row1 4. slot: 3 satır boyu uzanan tam boy cam kart (Figma 2850:21622, 169×184) -->
    <div class="widget-card widget-card--empty widget-card--tall" aria-hidden="true"></div>
    <?php endif; ?>

</section>
