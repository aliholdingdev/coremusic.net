<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Girişi (figma 2161:12506 "Admin - Login")
 *
 * Mockup: .ai/.png/admin-1920/Admin - Login.png · spec: screens/T17-monitor-22fhd/admin-login.md
 * Çıkarım SSOT: .ai/ui-design/reference/figma/extracted-admin-1920.md §5
 *
 * Transport: admin CSP `form-action 'self'` cross-domain native POST'u engeller →
 * form submit'i admin-shell.js tarafından API'ye fetch POST ile iletilir
 * (desen: auth.coremusic.net/pages/login.php). Kernel'de POST handler YOKTUR.
 * Başarı → API `data.redirect` (admin /auth/callback?auth_key=...) → AdminAuthBridge.
 *
 * Not: auth.coremusic.net/pages/login.php GÖRÜNMEDEN kalır (routes.php 'login'
 * redirect_to meta'sı korunmuştur — RouteResolutionTest sözleşmesi).
 */

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$nonce = (string)($_SESSION['csp_nonce'] ?? '');
$csrf  = (string)($_SESSION['csrf_token'] ?? '');

// API tabanı — auth login.php ile aynı sözleşmeli desen (Faz 3a):
// API_URL tanımlı değilse COREMUSIC_SCHEME ile api.coremusic.net.
$apiBase = ((defined('API_URL') && API_URL)
    ? (string)API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? (string)COREMUSIC_SCHEME : 'http') . '://api.coremusic.net');
$apiBase = rtrim($apiBase, '/');

$assetsUrl  = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$authUrl    = defined('AUTH_URL') ? rtrim((string)AUTH_URL, '/') : '';
$adminUrl   = defined('ADMIN_URL') ? rtrim((string)ADMIN_URL, '/') : '';
$callback   = $adminUrl . '/auth/callback';

// Sayfa-bağımlı external JS (shell'de admin js mapi yok — Görev C yüzeyi):
// parser-inserted + nonce, strict-dynamic altında hem tam yükleme hem SPA'da çalışır.
$shellJs    = dirname(__DIR__, 2) . '/assets.coremusic.net/js/admin/admin-shell.js';
$shellJsV   = is_file($shellJs) ? (string)filemtime($shellJs) : '';
$shellJsQs  = $shellJsV !== '' ? '?v=' . $shellJsV : '';

// Figma metinleri (extracted-admin-1920.md §5 + ham JSON characters) —
// yazım hataları/OEM karakterler korunur (kopya doğruluk).
$figmaNote  = 'Yönetici (Admin) girişi yapmak için lutfen yönetici iseniz giriş yapınız cunku sistem doğrulamsı vardır anında kaçak giriş & hacker tespiti yapılmaktadır';
$figmaQr    = 'Telefon ile mobil uygulamamız yada qr kodu tarayarak da web den guvenli giriş yapabilirsiniz';
?>
<div class="adm-page adm-page--login">
    <div class="adm-bg" aria-hidden="true">
        <div class="adm-bg__image"></div>
        <div class="adm-bg__scrim"></div>
    </div>

    <div class="adm-auth-panel">
        <h1 class="adm-auth__title">Core Music for Ali Admin</h1>
        <p class="adm-auth__subtitle">Sisteme Giriş Yapın </p>

        <div class="adm-auth__note">
            <img class="adm-auth__note-icon" src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="50" height="50">
            <p class="adm-auth__note-text"><?= $h($figmaNote) ?></p>
        </div>

        <form class="adm-auth__form" id="adm-login-form" method="post" action="/admin-login"
              novalidate aria-label="Yönetici girişi"
              data-endpoint="<?= $h($apiBase) ?>/v1/auth/login">
            <div class="adm-form">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="client_id" value="coremusic-web">
                <input type="hidden" name="response_type" value="session">
                <input type="hidden" name="redirect_uri" value="<?= $h($callback) ?>">

                <label class="adm-form__label adm-form__label--l1" for="adm-login-id">Kullanıcı adı yada Email</label>
                <div class="adm-form__field adm-form__field--i1 adm-form__field--icon">
                    <input class="adm-form__input" type="text" id="adm-login-id" name="email"
                           placeholder="Bayram Ali" autocomplete="username" required>
                </div>

                <label class="adm-form__label adm-form__label--l2" for="adm-login-pw">Parola</label>
                <div class="adm-form__field adm-form__field--i2 adm-form__field--icon adm-form__field--pill adm-form__field--password">
                    <input class="adm-form__input" type="password" id="adm-login-pw" name="password"
                           placeholder="**********" autocomplete="current-password" required>
                </div>

                <div class="adm-form__actions">
                    <label class="adm-form__remember">
                        <input class="adm-form__check" type="checkbox" name="remember" value="1">
                        <span>Beni Hatırla</span>
                    </label>
                    <a class="adm-form__link" href="<?= $h($authUrl) ?>/forgot-password" data-no-spa>Şifremi Unuttum</a>
                </div>

                <p class="adm-form__message" id="adm-login-err" role="alert" aria-live="polite" hidden></p>

                <button type="submit" class="adm-btn" id="adm-login-btn">
                    <span class="adm-btn__label">Sisteme Giriş Yap</span>
                </button>
            </div>
        </form>

        <div class="adm-auth__qr">
            <div class="adm-auth__qr-card"></div>
            <div class="adm-auth__qr-frame"></div>
            <p class="adm-auth__qr-text"><?= $h($figmaQr) ?></p>
        </div>
    </div>

    <script nonce="<?= $h($nonce) ?>" src="<?= $h($assetsUrl) ?>/js/admin/admin-shell.js<?= $shellJsQs ?>" defer></script>
</div>
