<?php declare(strict_types=1);
/**
 * CoreMusic Home — Auth Callback
 *
 * Cross-domain session transfer (server-side):
 * 1. auth.coremusic.net'den auth_key al
 * 2. auth.coremusic.net/validate-key ile server-side doğrula
 * 3. Home'da session oluştur
 * 4. Return URL'e redirect et
 */

use CoreMusic\Home\Auth\HomeAuthBridge;

/** @var string $cspNonce */
/** @var string $csrfTokenEsc */
/** @var \DI\Container $homeContainer */

$authKeyRaw = (string)($_GET['auth_key'] ?? '');

// Auth key yoksa → login'e dön
if ($authKeyRaw === '') {
    header('Location: /login', true, 302);
    exit;
}

// Session name ve path ayarla (middleware ile tutarlı)
if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'COREMUSIC_SESS');
    $savePath = ini_get('session.save_path') ?: 'C:\temp';
    if (!is_dir($savePath)) {
        @mkdir($savePath, 0777, true);
    }
    session_save_path($savePath);
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '.coremusic.net',
        'secure'   => $isHttps,
        'httponly'  => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Server-side auth_key doğrulama + session oluşturma
global $homeContainer;
$authBridge = $homeContainer->get(HomeAuthBridge::class);
$result = $authBridge->validateAndCreateSession($authKeyRaw);

if ($result['success']) {
    // Session'un diske yazılmasını garantile
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    // Başarılı → home'a redirect
    header('Location: /home', true, 302);
    exit;
}

// Başarısız → hata mesajı göster
$errorMsg = match ($result['error'] ?? '') {
    'invalid_key' => 'Geçersiz veya süresi dolmuş oturum anahtarı.',
    'empty_key'   => 'Oturum anahtarı bulunamadı.',
    default       => 'Bilinmeyen hata oluştu.',
};
?>
<div class="page-callback" role="main">
    <div class="callback-error" role="alert">
        <p><?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="/login" class="callback-link">Tekrar Giriş Yap</a>
    </div>
</div>

<script nonce="<?= htmlspecialchars($cspNonce ?? '', ENT_QUOTES, 'UTF-8') ?>">
(function() {
    'use strict';
    setTimeout(function() {
        window.location.href = '/login?error=invalid_key';
    }, 3000);
})();
</script>
