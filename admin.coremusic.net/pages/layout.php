<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Layout (shared shell partial · figma 2161:12499 "Admin - layout")
 * Senior Glassmorphism & Audiophile Studio Workstation Edition
 *
 * Kullanım: sayfa önce kendi içeriğini üretir, sonra bu dosyayı require eder:
 *
 *   $pageTitle = '...'; $activeNav = '...'; $content = '...';
 *   require __DIR__ . '/layout.php';
 *
 * Değişkenler: $content (zorunlu), $pageTitle (opsiyonel), $activeNav (opsiyonel).
 *
 * NOT: <main id="main-content"> burada ÜRETİLMEZ — container HtmlShellRenderer
 * tarafından zaten <main id="main-content"> içine sarılır. Header/footer chrome dosyası
 * admin'de diskte YOKTUR (renderChrome '' döner).
 *
 * Sidebar gezinme kaynağı: config/routes.php kayıt anahtarları.
 * CSS: p-admin-layout.css .adm-page--layout .adm-auth-panel gizler.
 */

$pageTitle  = $pageTitle ?? 'Kontrol Paneli';
$activeNav  = $activeNav ?? '';
$content    = $content ?? '';
$breadcrumb = 'Home / ' . $pageTitle;

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$authUrl   = defined('AUTH_URL') ? rtrim((string)AUTH_URL, '/') : '';

// Gezinme — rota anahtarları (routes.php) SSOT; etiketler ve gruplar Figma 2161:12504 ile birebir.
$navGroup1 = [
    'admin-settings'  => ['/admin-settings', 'Music Sitesi Ayarları'],
    'admin-dashboard' => ['/admin-dashboard', 'Admin Paneli Ayarları'],
];

$navGroup2 = [
    'admin-songs'     => ['/admin-songs', 'Şarkı listesi'],
    'admin-song-add'  => ['/admin-song-add', 'Şarkı Ekle'],
    'admin-playlist'  => ['/admin-playlist', 'Playlist Oluştur'],
];

// Navbar/footer — session kimliği (AdminAuthBridge MM_* anahtarları); figma
// metinleri korunur ("Bayram Ali - admin", "User Type : Administartor" yazım
// hataları dâhil — extracted-admin-1920.md §4/§8.6).
$username   = (string)($_SESSION['MM_Username'] ?? '');
$userLabel  = $username !== '' ? $username : 'Bayram Ali';
$ipAddress  = (string)($_SERVER['REMOTE_ADDR'] ?? '');
if ($ipAddress === '' || $ipAddress === '::1' || $ipAddress === '127.0.0.1') {
    $ipAddress = '95.95.0.201';
}
$userRole   = (string)($_SESSION['MM_UserRole'] ?? 'Administartor');

$copyright  = 'Copyright © 2024-2025 Core Music for Ali Admin . All rights reserved.  Core Music';
$userInitials = mb_strtoupper(mb_substr($userLabel, 0, 2));
?>
<div class="adm-page adm-page--layout<?= $activeNav !== '' ? ' adm-page--' . $h(str_replace('admin-', '', $activeNav)) : '' ?>">
    <div class="adm-bg" aria-hidden="true">
        <div class="adm-bg__image"></div>
        <div class="adm-bg__scrim"></div>
    </div>

    <!-- Auth paneli — layout ekranında gizli (p-admin-layout.css); boş shell korunur -->
    <div class="adm-auth-panel"></div>

    <nav class="adm-sidebar" aria-label="Yönetim menüsü">
        <div class="adm-sidebar__section">
            <p class="adm-sidebar__title">Menu</p>
            <div class="adm-sidebar__group">
                <?php foreach ($navGroup1 as $key => [$href, $label]): ?>
                    <a class="adm-sidebar__item<?= $activeNav === $key ? ' is-active' : '' ?>"
                       href="<?= $h($href) ?>"
                       <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                        <span class="adm-sidebar__icon-frame" aria-hidden="true">
                            <img class="adm-sidebar__icon" src="<?= $h($assetsUrl) ?>/Image/res-pink/back.png" alt="" aria-hidden="true" width="13" height="13">
                        </span>
                        <span class="adm-sidebar__label"><?= $h($label) ?></span>
                        <span class="adm-sidebar__chevron" aria-hidden="true">&rsaquo;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="adm-sidebar__section">
            <p class="adm-sidebar__subtitle">Şarkılar</p>
            <div class="adm-sidebar__group adm-sidebar__group--songs">
                <?php foreach ($navGroup2 as $key => [$href, $label]): ?>
                    <a class="adm-sidebar__item<?= $activeNav === $key ? ' is-active' : '' ?>"
                       href="<?= $h($href) ?>"
                       <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                        <span class="adm-sidebar__icon-frame" aria-hidden="true">
                            <img class="adm-sidebar__icon" src="<?= $h($assetsUrl) ?>/Image/res-pink/back.png" alt="" aria-hidden="true" width="13" height="13">
                        </span>
                        <span class="adm-sidebar__label"><?= $h($label) ?></span>
                        <span class="adm-sidebar__chevron" aria-hidden="true">&rsaquo;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="adm-sidebar__sys-card" aria-hidden="true">
            <div class="adm-sidebar__sys-head">
                <span class="adm-sidebar__sys-led"></span>
                <span class="adm-sidebar__sys-title">AUDIO ENGINE v3.2</span>
            </div>
            <div class="adm-sidebar__sys-row">
                <span class="adm-sidebar__sys-dim">Topoloji</span>
                <span class="adm-sidebar__sys-hi">Class AB &bull; ±42.5V</span>
            </div>
            <div class="adm-sidebar__sys-row">
                <span class="adm-sidebar__sys-dim">Gecikme</span>
                <span class="adm-sidebar__sys-hi adm-sidebar__sys-hi--green">1.2ms &bull; Bit-Exact</span>
            </div>
        </div>
    </nav>

    <header class="adm-navbar" role="banner">
        <div class="adm-navbar__user">
            <div class="adm-navbar__avatar" aria-hidden="true">
                <span><?= $h($userInitials) ?></span>
            </div>
            <span class="adm-navbar__name"><?= $h($userLabel) ?> - admin</span>
        </div>
        <div class="adm-navbar__telemetry">
            <img class="adm-navbar__icon adm-navbar__icon--wifi" src="<?= $h($assetsUrl) ?>/Image/res-pink/wifi-1-on.png" alt="Wi-Fi" width="11" height="12">
            <img class="adm-navbar__icon adm-navbar__icon--bt" src="<?= $h($assetsUrl) ?>/Image/res-pink/bluethoot-1-connected.png" alt="Bluetooth" width="11" height="12">
            <span class="adm-navbar__battery-wrap">
                <img class="adm-navbar__icon adm-navbar__icon--battery" src="<?= $h($assetsUrl) ?>/Image/res-pink/power-system/battery-100.png" alt="Pil" width="17" height="16">
                <span class="adm-navbar__battery">100%</span>
            </span>
        </div>
        <a class="adm-navbar__logout" href="<?= $h($authUrl) ?>/logout" data-no-spa aria-label="Çıkış" title="Güvenli Çıkış">
            <img class="adm-navbar__icon--logout" src="<?= $h($assetsUrl) ?>/Image/res-pink/session-logout.png" alt="" aria-hidden="true" width="15" height="16">
            <span class="adm-u-sr-only">Çıkış</span>
        </a>
    </header>

    <div class="adm-content">
        <h1 class="adm-panel__title">Admin Panelim</h1>
        <p class="adm-panel__breadcrumb"><?= $h($breadcrumb) ?></p>
        <?= $content ?>
    </div>

    <footer class="adm-footer">
        <span class="adm-footer__meta adm-footer__meta--user">Username : <?= $h($userLabel) ?></span>
        <span class="adm-footer__meta adm-footer__meta--ip">IP : <?= $h($ipAddress) ?></span>
        <span class="adm-footer__meta adm-footer__meta--role">User Type : <?= $h($userRole) ?></span>
    </footer>

    <div class="adm-copyright"><?= $h($copyright) ?></div>
</div>
