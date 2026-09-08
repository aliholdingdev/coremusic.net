<?php declare(strict_types=1);
/**
 * header.php — CoreMusic Global Header
 * Layer: L3 Presentation · 03_Layout (_header.css)
 * SSOT: .ai/.png/home-1024/ + home-1920/ (PNG sadakati, Guardrail #11)
 * Bileşenler: C01 nav-link · C02 header-widget · C03 header-user
 * Yükseklik: 60px (1024) · 70px (1920+) · 90px (4K) — CSS token: --header-h
 * Version: 1.0.0 — 2026-09-06 (sıfırdan yeniden yazım — PNG birebir)
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';

/* ── Cookie / Session verileri ── */
$cookieImage = $h(
    (is_string($_COOKIE['MM_Image'] ?? null) && $_COOKIE['MM_Image'] !== '')
        ? $_COOKIE['MM_Image']
        : $assetsUrl . '/Image/res-pink/users.png'
);

$cookieUsername = $h(
    (is_string($_SESSION['MM_Username'] ?? null) && $_SESSION['MM_Username'] !== '')
        ? $_SESSION['MM_Username']
        : ((is_string($_COOKIE['MM_Username'] ?? null) && $_COOKIE['MM_Username'] !== '')
            ? $_COOKIE['MM_Username']
            : 'Misafir')
);

$cookieWifi = $h(
    (is_string($_COOKIE['Wifcfg_wifisignal'] ?? null) && $_COOKIE['Wifcfg_wifisignal'] !== '')
        ? $_COOKIE['Wifcfg_wifisignal']
        : $assetsUrl . '/Image/res-pink/wifi/wifi-not-connection.png'
);

$cookieBt = $h(
    (is_string($_COOKIE['Bluethootcfg_btstatus'] ?? null) && $_COOKIE['Bluethootcfg_btstatus'] !== '')
        ? $_COOKIE['Bluethootcfg_btstatus']
        : $assetsUrl . '/Image/res-pink/bluethoot.png'
);

$cookiePower = $h(
    (is_string($_COOKIE['Powercfg_powerlevel'] ?? null) && $_COOKIE['Powercfg_powerlevel'] !== '')
        ? $_COOKIE['Powercfg_powerlevel']
        : $assetsUrl . '/Image/res-pink/power-system/battery-100.png'
);

$cookiePowerTxt = $h(
    (is_string($_COOKIE['Powercfg_powerlevel_txt'] ?? null) && $_COOKIE['Powercfg_powerlevel_txt'] !== '')
        ? $_COOKIE['Powercfg_powerlevel_txt']
        : '100%'
);

$settingsIcon = $h($assetsUrl . '/Image/res-pink/settings.png');
$logoutIcon   = $h($assetsUrl . '/Image/res-pink/session-logout.png');

/* ── Tier sınıfı: 4K (≥2561) → Wide (≥1920) → Embedded (≤1024) ── */
$headerTierClass = $dm->shouldRender4kLayout()
    ? 'site-header--4k site-header--tv'
    : ($dm->shouldRenderWideLayout() ? 'site-header--wide site-header--desktop' : 'site-header--1024 site-header--embedded');
?>
<header class="site-header <?= $headerTierClass ?> <?= $dm->allClasses() ?>" role="banner" <?= $dm->dataAttributes() ?>>
    <div class="site-header__inner">

        <a href="/home" class="site-header__logo" aria-label="CoreMusic" data-no-spa>
            <span class="logo-core">Core</span> <span class="logo-music">Music</span>
        </a>

        <nav class="site-header__nav" aria-label="Ana navigasyon">
<?php foreach ($dm->navLinks() as $link): ?>
            <a href="<?= $h((string)$link['href']) ?>" class="nav-link<?= $link['active'] ? ' active' : '' ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?> data-no-spa><?= $h((string)$link['label']) ?></a>
<?php endforeach; ?>
        </nav>

        <div class="site-header__actions" aria-label="Sistem ve kullanıcı">

            <!-- C02 — WiFi + Bluetooth kapsülü (65×37.4px, radius 50px) -->
            <div class="header-border header-border--wifi" title="Bağlantı Durumu">
                <div class="header-widget header-widget--signal">
                    <img src="<?= $cookieWifi ?>" alt="Wi-Fi" width="25" height="25" loading="lazy"/>
                </div>
                <div class="header-widget header-widget--bt">
                    <img src="<?= $cookieBt ?>" alt="Bluetooth" width="25" height="25" loading="lazy"/>
                </div>
            </div>

            <!-- C02 — Pil kapsülü (100×37.4px, radius 50px) -->
            <div class="header-border header-border--battery" title="Güç Durumu">
                <img src="<?= $cookiePower ?>" alt="Pil" width="22" height="22" loading="lazy"/>
                <span class="battery-pct"><?= $cookiePowerTxt ?></span>
            </div>

            <!-- C03 — Kullanıcı hapı (avatar 35×35 + isim + dropdown) -->
            <div class="header-user" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false">
                <img class="header-user__avatar" src="<?= $cookieImage ?>" alt="Avatar" width="35" height="35" loading="lazy"/>
                <span class="header-user__name"><?= $cookieUsername ?></span>
                <span class="header-user__arrow">&#9660;</span>
                <div class="header-user-dropdown" role="menu">
                    <a href="/profil" role="menuitem" data-no-spa>Profilim</a>
                    <a href="/ayarlar" role="menuitem" data-no-spa>Ayarlar</a>
                    <a href="/gecmis" role="menuitem" data-no-spa>Geçmiş</a>
                    <a href="/logout" class="logout-btn" role="menuitem" data-no-spa>Çıkış Yap</a>
                </div>
            </div>

            <div class="header-border header-border--actions">
                <button class="header-action-btn sidebar-toggle" type="button" aria-label="Sidebar aç/kapat" data-action="toggle-sidebar" title="Navigasyon paneli">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <a href="/ayarlar" class="header-action-btn" aria-label="Ayarlar">
                    <img src="<?= $settingsIcon ?>" alt="" width="22" height="22" loading="lazy"/>
                </a>
                <a href="/logout" class="header-action-btn" aria-label="Çıkış" data-no-spa>
                    <img src="<?= $logoutIcon ?>" alt="" width="22" height="22" loading="lazy"/>
                </a>
            </div>
        </div>
    </div>
</header>

<aside class="sidebar <?= $dm->allClasses() ?>" role="navigation" aria-label="Sidebar navigasyon" <?= $dm->dataAttributes() ?>>
    <div class="sidebar__scroll" role="menubar" aria-label="Sidebar menü">
        <!-- SidebarManager.js tarafından doldurulur -->
    </div>
    <div class="sidebar__resize-handle" role="separator" tabindex="0" aria-label="Sidebar genişletme tutamacı"></div>
</aside>
<div class="sidebar-overlay" aria-hidden="true"></div>
