<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Login (iskelet)
 *
 * Not: AuthGuard, login/register rotalarını auth.coremusic.net'e yönlendirir
 * (AuthRouteConfig::isAuthRedirectRoute). Bu sayfa yalnızca yedek/görsel iskelettir.
 * TODO UI katmanı
 */

$pageTitle = 'Yönetici Girişi';
$activeNav = '';
?>
<div class="admin-login">
    <p class="admin-login__text">Yönetici girişi <strong>auth.coremusic.net</strong> üzerinden yapılır.</p>
    <a class="admin-login__link" href="/login">Girişe Yönlendir</a>
</div>
<?php require __DIR__ . '/layout.php';
