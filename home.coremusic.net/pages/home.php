<?php declare(strict_types=1);
/**
 * CoreMusic Home — Ana Sayfa
 * Auth state: Kullanıcı adını gösterir.
 */

$username = htmlspecialchars($_SESSION['MM_Username'] ?? '', ENT_QUOTES, 'UTF-8');
$email    = htmlspecialchars($_SESSION['MM_Email'] ?? '', ENT_QUOTES, 'UTF-8');
$gender   = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['MM_Gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$isAuth   = $username !== '';

require __DIR__ . '/../header.php';
?>
<div class="page-home" role="main" aria-label="Ana Sayfa">
    <?php if ($isAuth): ?>
        <h1>Hoş geldiniz, <?= $username ?>!</h1>
        <p>CoreMusic'e hoş geldiniz. Müzik keyfinize devam edin.</p>
        <div class="page-home__info">
            <p><strong>E-posta:</strong> <?= $email ?></p>
            <p><strong>Cinsiyet:</strong> <?= $gender ?></p>
        </div>
    <?php else: ?>
        <h1>CoreMusic</h1>
        <p>Müzik keyfinize devam etmek için giriş yapın.</p>
        <a href="/login" class="l-btn l-btn--primary">Giriş Yap</a>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../footer.php'; ?>
