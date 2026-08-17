<?php declare(strict_types=1);
/**
 * CoreMusic Home — Auth Callback
 *
 * Cross-domain session transfer:
 * 1. auth.coremusic.net'den auth_key al
 * 2. auth.coremusic.net/validate-key ile doğrula
 * 3. Session oluştur
 * 4. Return URL'e redirect et
 */

/** @var string $cspNonce */
/** @var string $csrfTokenEsc */

$authKeyRaw = (string)($_GET['auth_key'] ?? '');
$authKey    = htmlspecialchars($authKeyRaw, ENT_QUOTES, 'UTF-8');
$error      = htmlspecialchars((string)($_GET['error'] ?? ''), ENT_QUOTES, 'UTF-8');
$authUrl    = defined('AUTH_URL') ? AUTH_URL : 'https://auth.coremusic.net';
$homeUrl    = defined('MUSIC_URL') ? MUSIC_URL : 'https://home.coremusic.net';

// Auth key yoksa → login'e dön
if ($authKeyRaw === '') {
    header('Location: /login', true, 302);
    exit;
}

// Hata varsa göster
$errorMsg = '';
if ($error !== '') {
    $errorMsg = match ($error) {
        'invalid_key' => 'Geçersiz veya süresi dolmuş oturum anahtarı.',
        'expired'     => 'Oturum anahtarı süresi dolmuş.',
        'denied'      => 'Erişim reddedildi.',
        default       => 'Bilinmeyen hata oluştu.',
    };
}
?>
<div class="page-callback" role="main">
    <?php if ($errorMsg !== ''): ?>
        <div class="callback-error" role="alert">
            <p><?= $errorMsg ?></p>
            <a href="/login" class="callback-link">Tekrar Giriş Yap</a>
        </div>
    <?php else: ?>
        <div class="callback-loading" aria-live="polite">
            <p>Oturum doğrulanıyor...</p>
        </div>
    <?php endif; ?>
</div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
(function() {
    'use strict';

    var AUTH_KEY = <?= json_encode($authKeyRaw) ?>;
    var VALIDATE_URL = '<?= $authUrl ?>/validate-key';
    var HOME_URL = '<?= $homeUrl ?>';
    var HAS_ERROR = <?= json_encode($errorMsg !== '') ?>;

    if (HAS_ERROR) {
        return;
    }

    fetch(VALIDATE_URL, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ auth_key: AUTH_KEY })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success && data.user) {
            try {
                sessionStorage.setItem('cm_user', JSON.stringify(data.user));
            } catch (e) { /* sessionStorage unavailable */ }

            window.location.href = HOME_URL + '/home';
        } else {
            window.location.href = '/login?error=invalid_key';
        }
    })
    .catch(function() {
        window.location.href = '/login?error=denied';
    });
})();
</script>
