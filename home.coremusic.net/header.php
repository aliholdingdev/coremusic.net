<?php declare(strict_types=1);
/**
 * CoreMusic Home — Header Partial
 * Auth state awareness: login/logout link gösterir.
 */

$currentUser = $_SESSION['MM_Username'] ?? null;
$isAuth = $currentUser !== null && $currentUser !== '';
?>
<header class="l-header" role="banner">
    <div class="l-header__brand">
        <a href="/home" class="l-header__logo" data-no-spa>CoreMusic</a>
    </div>
    <nav class="l-header__nav" role="navigation" aria-label="Ana menü">
        <a href="/home" class="l-header__link" data-no-spa>Ana Sayfa</a>
        <a href="/kesfet" class="l-header__link">Keşfet</a>
        <a href="/albumler" class="l-header__link">Albümler</a>
        <a href="/ayarlar" class="l-header__link">Ayarlar</a>
    </nav>
    <div class="l-header__user">
        <?php if ($isAuth): ?>
            <span class="l-header__username"><?= htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8') ?></span>
            <a href="/logout" class="l-header__link" data-no-spa>Çıkış</a>
        <?php else: ?>
            <a href="/login" class="l-header__link" data-no-spa>Giriş Yap</a>
        <?php endif; ?>
    </div>
</header>
