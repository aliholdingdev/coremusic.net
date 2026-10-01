<?php declare(strict_types=1);
/**
 * pages/components/welcome-banner.php — Wide/4K üst satır orta sütun "Hoş Geldin" kartı
 *
 * HTML contract → assets.coremusic.net/Css/04_Components/_welcome-banner.css
 *   <section class="welcome-banner welcome-banner--wide">
 *     .welcome-banner__overlay/__content
 *     __eyebrow · __user · __quote · __brand
 *     __cta (a[href=/kesfet] → __cta-text + __cta-icon[aria-hidden])
 *     __stats (ul) → __stat--1..5 (img.__stat-icon + __stat-text → __stat-num/__stat-label)
 *               → __more (li[aria-hidden] "+" ring)
 *
 * Figma SSOT: node 2849:21492 (1920 · 491×184) — bkz. WelcomeBannerComponent.php doc-block
 * İçerik metinleri/ölçüleri: PNG mockup numeric ölçümü (a-welcome-banner-tokens.css v2.0.0)
 *
 * @var \CoreMusic\Home\Component\WelcomeBannerComponent $this
 */
?>
<section class="welcome-banner welcome-banner--wide" aria-label="Hoş geldin">
    <div class="welcome-banner__overlay"></div>
    <div class="welcome-banner__content">
        <p class="welcome-banner__eyebrow"><?= $this->eyebrow ?></p>
        <p class="welcome-banner__user"><?= $this->userName ?></p>
        <p class="welcome-banner__quote"><?= $this->quote ?></p>
        <p class="welcome-banner__brand"><?= $this->brand ?></p>
        <a class="welcome-banner__cta" href="<?= $this->ctaHref ?>">
            <span class="welcome-banner__cta-text"><?= $this->ctaLabel ?></span>
            <span class="welcome-banner__cta-icon" aria-hidden="true"></span>
        </a>
        <ul class="welcome-banner__stats">
            <?php foreach ($this->stats as $i => $stat): ?>
            <li class="welcome-banner__stat welcome-banner__stat--<?= $i + 1 ?>">
                <img class="welcome-banner__stat-icon" src="<?= $stat['icon'] ?>" alt="" width="13" height="13">
                <span class="welcome-banner__stat-text">
                    <span class="welcome-banner__stat-num"><?= $stat['num'] ?></span>
                    <span class="welcome-banner__stat-label"><?= $stat['label'] ?></span>
                </span>
            </li>
            <?php endforeach; ?>
            <li class="welcome-banner__more" aria-hidden="true"></li>
        </ul>
    </div>
</section>
