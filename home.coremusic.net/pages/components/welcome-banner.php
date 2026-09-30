<?php declare(strict_types=1);
/**
 * pages/components/welcome-banner.php — Wide/4K üst satır orta sütun "Hoş Geldin" kartı
 *
 * HTML contract → assets.coremusic.net/Css/04_Components/_welcome-banner.css
 *   <section class="welcome-banner welcome-banner--wide">
 *
 * Figma SSOT: node 2849:21492 (1920 · 491×184) — bkz. WelcomeBannerComponent.php doc-block
 *
 * @var \CoreMusic\Home\Component\WelcomeBannerComponent $this
 */
?>
<section class="welcome-banner welcome-banner--wide" aria-label="Hoş geldin" data-cm-component="cm-home-welcome-banner">
    <div class="welcome-banner__overlay"></div>
    <div class="welcome-banner__content">
        <p class="welcome-banner__eyebrow"><?= $this->eyebrow ?></p>
        <p class="welcome-banner__user"><?= $this->userName ?></p>
    </div>
</section>
