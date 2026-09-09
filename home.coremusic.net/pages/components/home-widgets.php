<?php declare(strict_types=1);
/**
 * pages/components/home-widgets.php — Bileşen: Widget Grid 2×2 (v2)
 * PNG SSOT: .ai/.png/home-1024/ (sağ 58%) + .ai/.png/home-1920/ (top-right, cluster sarmalayıcı)
 * Yükleyen: CoreMusic\Home\Component\HomeWidgetsComponent
 * Erişim: $this->{dateLabel,imgBt,imgSaat,imgDate,imgFolder,imgYoutube}
 *         $this->variant->isWide()
 */
?>
<?php if ($this->variant->isWide()): ?><div class="home-widgets-cluster--wide">
<?php endif; ?>
<div class="home-widget-grid">

    <article class="home-widget">
        <header class="home-widget__header">
            <span class="home-widget__icon"><img src="<?= $this->h($this->imgBt) ?>" alt="" width="20" height="20" loading="lazy"/></span>
            <h2 class="home-widget__title">Hoparlör</h2>
        </header>
        <div class="home-widget__glass home-widget__info">Core Music - Hoparlör</div>
    </article>

    <article class="home-widget">
        <header class="home-widget__header">
            <span class="home-widget__icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="14" r="4"/><path d="M16 18a4 4 0 0 0 0-8 6 6 0 0 0-11.3 2"/></svg></span>
            <h2 class="home-widget__title">Hava Durumu</h2>
        </header>
        <div class="home-widget__glass home-widget__info">
            <span class="home-widget__subtitle">Genelya 13°</span>
            Istanbul
        </div>
    </article>

    <article class="home-widget">
        <header class="home-widget__header">
            <span class="home-widget__icon"><img src="<?= $this->h($this->imgSaat) ?>" alt="" width="20" height="20" loading="lazy"/></span>
            <h2 class="home-widget__title">07:00</h2>
        </header>
        <div class="home-widget__glass home-widget__info">
            <img src="<?= $this->h($this->imgDate) ?>" alt="" width="14" height="14" loading="lazy"/>
            <span class="home-widget__subtitle"><?= $this->dateLabel ?></span>
        </div>
    </article>

    <!-- PNG 2. satır sağ: EQ göstergesi + 3 boş slot (dekoratif) -->
    <div class="home-widget-cell home-widget-cell--slots" aria-hidden="true">
        <div class="home-slot home-slot--eq">
            <span class="widget-eq-bars"><span></span><span></span><span></span><span></span><span></span></span>
        </div>
        <div class="home-slot"></div>
        <div class="home-slot"></div>
        <div class="home-slot"></div>
    </div>

    <!-- PNG 3. satır sol: Kitaplığım (kompakt panel) -->
    <article class="home-widget home-widget--compact">
        <header class="home-widget__header">
            <span class="home-widget__icon"><img src="<?= $this->h($this->imgFolder) ?>" alt="" width="20" height="20" loading="lazy"/></span>
            <h2 class="home-widget__title">Kitaplığım</h2>
        </header>
    </article>

    <!-- PNG 3. satır sağ: uygulama kısayolları -->
    <div class="home-widget-cell home-widget-cell--apps">
        <button type="button" class="home-app-btn home-app-btn--sparkle" data-action="shuffle" aria-label="Karıştır"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.4 7.6L22 12l-7.6 2.4L12 22l-2.4-7.6L2 12l7.6-2.4z"/></svg></button>
        <button type="button" class="home-app-btn home-app-btn--youtube" data-action="open-youtube" aria-label="YouTube"><img src="<?= $this->h($this->imgYoutube) ?>" alt="" width="20" height="20" loading="lazy"/></button>
        <button type="button" class="home-app-btn home-app-btn--heart" data-action="open-favorites" aria-label="Favoriler"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54z"/></svg></button>
        <div class="home-slot" aria-hidden="true"></div>
    </div>

</div>
<?php if ($this->variant->isWide()): ?>
</div>
<?php endif; ?>
