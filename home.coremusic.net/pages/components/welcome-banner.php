<?php declare(strict_types=1);
/**
 * pages/components/welcome-banner.php — Wide/4K üst satır orta sütun "Hoş Geldin" kartı
 *
 * HTML contract → assets.coremusic.net/Css/04_Components/_welcome-banner.css (v4.0.0)
 *   <section class="welcome-banner welcome-banner--wide" aria-label="Hoş geldin">
 *     .welcome-banner__hero[aria-hidden] → __hero-img (dekoratif kız fotoğrafı, çapraz panel)
 *     .welcome-banner__content
 *       __greet ("Hoş Geldin" — Bickham Script Two)
 *       __identity (figure) → __avatar (img, dekoratif) + __meta (figcaption)
 *         → __name (session kullanıcısı) · __tagline
 *
 * Arkaplan/sahne görseli ve tüm ölçüler CSS token'ıdır (a-welcome-banner-tokens.css
 * v4.0.0) — burada inline style YOK (Guardrail #3 / CSP).
 *
 * Figma SSOT: node 2849:21492 (1920 · 491×184) — bkz. WelcomeBannerComponent.php
 * İçerik yapısı: kullanıcının verdiği referans banner görseli
 *
 * @var \CoreMusic\Home\Component\WelcomeBannerComponent $this
 */
?>
<section class="welcome-banner welcome-banner--wide welcome-banner--<?= $this->h($this->gender) ?>"
         data-gender="<?= $this->h($this->gender) ?>"
         aria-label="Hoş geldin">

    <!-- SAĞ PANEL — dekoratif, dekoratif görsel olduğu için erişimden gizli -->
    <div class="welcome-banner__hero" aria-hidden="true">
        <img class="welcome-banner__hero-img"
             src="<?= $this->h($this->heroImage) ?>"
             alt=""
             decoding="async">
    </div>

    <div class="welcome-banner__content">
        <p class="welcome-banner__greet"><?= $this->h($this->greet) ?></p>

        <figure class="welcome-banner__identity">
            <img class="welcome-banner__avatar"
                 src="<?= $this->h($this->avatar) ?>"
                 alt=""
                 width="54"
                 height="54"
                 decoding="async">
            <figcaption class="welcome-banner__meta">
                <span class="welcome-banner__name"><?= $this->userName ?></span>
                <span class="welcome-banner__tagline"><?= $this->h($this->tagline) ?></span>
            </figcaption>
        </figure>
    </div>

</section>
