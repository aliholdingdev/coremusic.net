<?php declare(strict_types=1);
/**
 * CoreMusic Home — Header Partial
 * BEM: .site-header → __inner → __logo | __nav | __actions
 * Design: 02-home-screens.md, 01-component-inventory.md
 * Components: C01 (nav-link), C02 (system status), C03 (user pill)
 * Version: 2.0.0 — 2026-08-21
 */

$currentUser = $_SESSION['MM_Username'] ?? null;
$isAuth = $currentUser !== null && $currentUser !== '';
$avatarUrl = $_SESSION['MM_Avatar'] ?? '/assets.coremusic.net/Image/res-pink/default-avatar.png';
$gender = $_SESSION['cm_gender'] ?? $_SESSION['MM_Gender'] ?? 'neutral';
?>
<header class="site-header" role="banner">
    <div class="site-header__inner">
        <!-- Logo: Core (Bickham) + Music (Respective) -->
        <a href="/home" class="site-header__logo" data-no-spa aria-label="CoreMusic Ana Sayfa">
            <span class="logo-core">Core</span><span class="logo-music">Music</span>
        </a>

        <!-- Navigation: C01 nav-link × 8 -->
        <nav class="site-header__nav" aria-label="Ana navigasyon">
            <a href="/home" class="nav-link active" aria-current="page">Ana Sayfa</a>
            <a href="/kesfet" class="nav-link">Keşfet</a>
            <a href="/albumler" class="nav-link">Albümler</a>
            <a href="/sanatcilar" class="nav-link">Sanatçılar</a>
            <a href="/goz-at" class="nav-link">Göz At</a>
            <a href="/gecmis" class="nav-link">Geçmiş</a>
            <a href="/ayarlar" class="nav-link">Ayarlar</a>
            <a href="/hakkimizda" class="nav-link">Hakkımızda</a>
        </nav>

        <!-- Actions: User Pill + System Status + Power -->
        <div class="site-header__actions">
            <!-- C03: User Profile Pill -->
            <div class="header-user" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">
                <img
                    class="header-user__avatar"
                    src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= $isAuth ? htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8') : 'Kullanıcı' ?>"
                    width="35"
                    height="35"
                    loading="lazy"
                >
                <span class="header-user__name"><?= $isAuth ? htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8') : 'Misafir' ?></span>
                <span class="header-user__arrow" aria-hidden="true">&#9662;</span>
                <!-- Dropdown Menu -->
                <div class="header-user-dropdown" role="menu">
                    <a href="/profil" role="menuitem" data-no-spa>Profilim</a>
                    <a href="/ayarlar" role="menuitem" data-no-spa>Ayarlar</a>
                    <a href="/gecmis" role="menuitem" data-no-spa>Geçmiş</a>
                    <?php if ($isAuth): ?>
                        <a href="/logout" class="logout-btn" role="menuitem" data-no-spa>Çıkış Yap</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- C02: System Status Widget — WiFi + BT Pill -->
            <div class="header-border" style="border-radius:50px;height:37.4px;width:65px;">
                <div class="header-widget header-widget--signal">
                    <img src="/assets.coremusic.net/Image/res-pink/wifi-full.png" alt="Wi-Fi" height="25" width="25" loading="lazy">
                </div>
                <div class="header-widget header-widget--bt">
                    <img src="/assets.coremusic.net/Image/res-pink/bluethoot.png" alt="Bluetooth" height="25" width="25" loading="lazy">
                </div>
            </div>

            <!-- C02: Battery Pill -->
            <div class="header-border" style="border-radius:50px;height:37.4px;width:100px;justify-content:center;gap:4px;">
                <div class="header-widget header-widget--battery">
                    <img src="/assets.coremusic.net/Image/res-pink/battery-full.png" alt="Batarya" height="36" width="36" loading="lazy">
                </div>
                <span class="battery-pct">%100</span>
            </div>

            <!-- Action Buttons -->
            <a href="/ayarlar" class="header-action-btn" aria-label="Ayarlar" data-no-spa>
                <img src="/assets.coremusic.net/Image/res-pink/ayarlar.png" alt="" height="25" width="25" loading="lazy">
            </a>
            <a href="/logout" class="header-action-btn header-action-btn--logout" aria-label="Çıkış" data-no-spa>
                <img src="/assets.coremusic.net/Image/res-pink/power.png" alt="" height="25" width="25" loading="lazy">
            </a>
        </div>
    </div>
</header>
