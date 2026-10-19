<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Layout (shared shell partial)
 *
 * Kullanım: sayfa önce kendi içeriğini üretir ($content), sonra bu dosyayı require eder.
 *
 *   ob_start(); ... içerik ...; $content = (string)ob_get_clean();
 *   require __DIR__ . '/layout.php';
 *
 * Değişkenler: $content (zorunlu), $pageTitle (opsiyonel), $activeNav (opsiyonel).
 * BEM: admin-layout · admin-nav · admin-main
 * TODO UI katmanı: mockup yok — iskelet görünüm, tasarım assets.coremusic.net'e ait.
 */

$pageTitle = $pageTitle ?? 'Kontrol Paneli';
$activeNav = $activeNav ?? '';
$content   = $content ?? '';

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$navItems = [
    'dashboard' => ['/dashboard', 'Kontrol Paneli'],
    'songs'     => ['/songs', 'Şarkılar'],
    'song-add'  => ['/song-add', 'Şarkı Ekle'],
    'playlist'  => ['/playlist', 'Playlistler'],
    'settings'  => ['/settings', 'Ayarlar'],
];
?>

<div class="admin-layout">
    <header class="admin-layout__header">
        <a class="admin-layout__brand" href="/dashboard">CoreMusic Admin</a>
        <nav class="admin-nav" aria-label="Yönetim menüsü">
            <?php foreach ($navItems as $key => [$href, $label]): ?>
                <a class="admin-nav__link<?= $activeNav === $key ? ' admin-nav__link--active' : '' ?>"
                   href="<?= $h($href) ?>"
                   <?= $activeNav === $key ? 'aria-current="page"' : '' ?>><?= $h($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="admin-layout__logout" href="/logout">Çıkış</a>
    </header>

    <main class="admin-main" id="main-content">
        <h1 class="admin-main__title"><?= $h($pageTitle) ?></h1>
        <?= $content ?>
    </main>

    <footer class="admin-layout__footer">
        <span>CoreMusic Admin — iskelet (TODO UI katmanı)</span>
    </footer>
</div>
