<?php declare(strict_types=1);
/**
 * pages/components/welcome-banner.php — Bileşen: Hoş Geldin Banner (v2)
 * PNG SSOT: .ai/.png/home-1920/ top-center (bg + CTA + 5 istatistik)
 * Yükleyen: CoreMusic\Home\Component\WelcomeBannerComponent
 * Erişim: $this->{imgBg, username}
 */
?>
<section class="home-welcome-banner" aria-label="Hoş geldin">
    <div class="home-welcome-banner__bg"><img src="<?= $this->h($this->imgBg) ?>" alt="" loading="lazy"/></div>
    <div class="home-welcome-banner__content">
        <p class="home-welcome-banner__title">Hoş Geldin</p>
        <p class="home-welcome-banner__user"><?= $this->username ?></p>
        <p class="home-welcome-banner__tagline">"Müzik, ruhun gıdasıdır; her nota, bir hatırayı canlandırır."</p>
        <p class="home-welcome-banner__brand">Core Music</p>
        <a class="home-welcome-banner__btn" href="/kesfet" data-no-spa>Keşfetmeye Başla
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
        </a>
        <div class="home-welcome-banner__stats">
            <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg></span><span class="home-welcome-banner__stat-value">2.450</span></div>
            <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 5h2v14H3zm4 0h2v14H7zm4 0h2v14h-2zm4 0h6v14h-6z"/></svg></span><span class="home-welcome-banner__stat-value">156</span></div>
            <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5z"/></svg></span><span class="home-welcome-banner__stat-value">87</span></div>
            <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></span><span class="home-welcome-banner__stat-value">42</span></div>
            <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54z"/></svg></span><span class="home-welcome-banner__stat-value">1.250</span></div>
        </div>
    </div>
</section>
