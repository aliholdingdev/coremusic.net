<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Layout (shared shell partial · figma 2161:12499 "Admin - layout")
 *
 * Kullanım: sayfa önce kendi içeriğini üretir, sonra bu dosyayı require eder:
 *
 *   $pageTitle = '...'; $activeNav = '...'; $content = '...';
 *   require __DIR__ . '/layout.php';
 *
 * Değişkenler: $content (zorunlu), $pageTitle (opsiyonel), $activeNav (opsiyonel).
 *
 * NOT: <main id="main-content"> burada ÜRETİLMEZ — container HtmlShellRenderer
 * tarafından zaten <main id="main-content"> içine sarılır (eski iskeletteki iç içe
 * main / çift landmark hatası giderildi). Header/footer chrome dosyası admin'de
 * diskte YOKTUR (renderChrome '' döner).
 *
 * Sidebar gezinme kaynağı: config/routes.php kayıt anahtarları (figma sidebar
 * etiketleri rota kümesiyle birebir örtüşmüyor → ⚠️ rapor; uydurma eşleme yok).
 * CSS: p-admin-layout.css .adm-page--layout .adm-auth-panel gizler (boş shell
 * yine de DOM'da durur — sözleşmede yer alır).
 */

$pageTitle  = $pageTitle ?? 'Kontrol Paneli';
$activeNav  = $activeNav ?? '';
$content    = $content ?? '';
$breadcrumb = 'Home / ' . $pageTitle;

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$authUrl   = defined('AUTH_URL') ? rtrim((string)AUTH_URL, '/') : '';

// Gezinme — rota anahtarları (routes.php) SSOT; etiketler rota title'ları.
// D2: admin-{pagename} adlandırma — ViewModeManager '/admin*' prefix'i ile senkron.
$navItems = [
    'admin-dashboard' => ['/admin-dashboard', 'Kontrol Paneli'],
    'admin-songs'     => ['/admin-songs', 'Şarkılar'],
    'admin-song-add'  => ['/admin-song-add', 'Şarkı Ekle'],
    'admin-playlist'  => ['/admin-playlist', 'Playlistler'],
    'admin-settings'  => ['/admin-settings', 'Ayarlar'],
];

// Navbar/footer — session kimliği (AdminAuthBridge MM_* anahtarları); figma
// metinleri korunur ("Bayram Ali - admin", "User Type : Administartor" yazım
// hataları dâhil — extracted-admin-1920.md §4/§8.6).
$username   = (string)($_SESSION['MM_Username'] ?? '');
$userLabel  = $username !== '' ? $username : 'admin';
$ipAddress  = (string)($_SERVER['REMOTE_ADDR'] ?? '');
$userRole   = (string)($_SESSION['MM_UserRole'] ?? 'Administartor');

$copyright  = 'Copyright © 2024-2025 Core Music for Ali Admin . All rights reserved.  Core Music';
?>
<div class="adm-page adm-page--layout">
    <div class="adm-bg" aria-hidden="true">
        <div class="adm-bg__image"></div>
        <div class="adm-bg__scrim"></div>
    </div>

    <!-- Auth paneli — layout ekranında gizli (p-admin-layout.css); boş shell korunur -->
    <div class="adm-auth-panel"></div>

    <nav class="adm-sidebar" aria-label="Yönetim menüsü">
        <p class="adm-sidebar__title">Menu</p>
        <div class="adm-sidebar__group">
            <?php foreach ($navItems as $key => [$href, $label]): ?>
                <a class="adm-sidebar__item<?= $activeNav === $key ? ' is-active' : '' ?>"
                   href="<?= $h($href) ?>"
                   <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                    <span class="adm-sidebar__label"><?= $h($label) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <div class="adm-navbar">
        <div class="adm-navbar__avatar" aria-hidden="true"></div>
        <span class="adm-navbar__name"><?= $h($userLabel) ?> - admin</span>
        <a class="adm-navbar__logout" href="<?= $h($authUrl) ?>/logout" data-no-spa aria-label="Çıkış">
            <span class="adm-u-sr-only">Çıkış</span>
        </a>
    </div>

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
