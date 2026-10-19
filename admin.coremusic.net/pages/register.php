<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Register (iskelet)
 *
 * Not: AuthGuard kayıt akışını auth.coremusic.net'e yönlendirir.
 * Bu sayfa yalnızca yedek/görsel iskelettir.
 * TODO UI katmanı
 */

$pageTitle = 'Kayıt';
$activeNav = '';
?>
<div class="admin-register">
    <p class="admin-register__text">Kayıt işlemi <strong>auth.coremusic.net</strong> üzerinden yapılır.</p>
    <a class="admin-register__link" href="/register">Kayda Yönlendir</a>
</div>
<?php require __DIR__ . '/layout.php';
