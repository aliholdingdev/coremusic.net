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
 * Not: /login ve /register bu sayfaya TAKMA ADdır (G2, 2026-10-19) — bootstrap'te
 * kernel öncesi302 ile /admin-login'e yönlenir; auth domain redirect YOKTUR.
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
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-login-id" name="email"
                           placeholder="Bayram Ali" autocomplete="username" required>
                </div>

                <label class="adm-form__label adm-form__label--l2" for="adm-login-pw">Parola</label>
                <div class="adm-form__field adm-form__field--i2 adm-form__field--icon adm-form__field--pill adm-form__field--password">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input class="adm-form__input" type="password" id="adm-login-pw" name="password"
                           placeholder="**********" autocomplete="current-password" required>
                </div>

                <div class="adm-form__actions">
                    <label class="adm-form__remember">
                        <input class="adm-form__check" type="checkbox" name="remember" value="1" checked>
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
            <div class="adm-auth__qr-frame">
                <svg class="adm-auth__qr-phone-svg" viewBox="0 0 28 56" width="28" height="56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="27" height="55" rx="5.5" stroke="#B0B5C4" stroke-width="1.2" fill="rgba(35, 25, 45, 0.75)"/>
                    <rect x="2.5" y="4.5" width="23" height="45" rx="2" fill="#FFFFFF"/>
                    <rect x="11" y="2" width="6" height="1" rx="0.5" fill="#888C9B"/>
                    <g transform="translate(4, 15) scale(0.65)">
                        <rect x="0" y="0" width="8" height="8" fill="#120D1A"/>
                        <rect x="2" y="2" width="4" height="4" fill="#FFFFFF"/>
                        <rect x="18" y="0" width="8" height="8" fill="#120D1A"/>
                        <rect x="20" y="2" width="4" height="4" fill="#FFFFFF"/>
                        <rect x="0" y="18" width="8" height="8" fill="#120D1A"/>
                        <rect x="2" y="20" width="4" height="4" fill="#FFFFFF"/>
                        <rect x="10" y="2" width="4" height="4" fill="#120D1A"/>
                        <rect x="10" y="10" width="6" height="6" fill="#120D1A"/>
                        <rect x="2" y="10" width="4" height="4" fill="#120D1A"/>
                        <rect x="18" y="12" width="4" height="6" fill="#120D1A"/>
                        <rect x="12" y="20" width="8" height="4" fill="#120D1A"/>
                        <rect x="22" y="20" width="4" height="4" fill="#120D1A"/>
                    </g>
                    <rect x="10" y="52" width="8" height="1" rx="0.5" fill="#888C9B"/>
                </svg>
            </div>
            <p class="adm-auth__qr-text"><?= $h($figmaQr) ?></p>
        </div>
    </div>

    <script nonce="<?= $h($nonce) ?>" src="<?= $h($assetsUrl) ?>/js/admin/admin-shell.js<?= $shellJsQs ?>" defer></script>
</div>
