<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Girişi (Figma 2161:12506 "Admin - Login") — Light Theme
 *
 * Yer: auth.coremusic.net/pages/admin/login.php
 * Tasarım: Açık Tema (Light Theme / html[data-mode="light"]) Glassmorphism
 * Token: a-color-mode-tokens.css + a-light-glass-tokens.css + a-admin-tokens.css
 */

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$nonce = (string)($_SESSION['csp_nonce'] ?? '');
$csrf  = (string)($_SESSION['csrf_token'] ?? '');

$apiBase = ((defined('API_URL') && API_URL)
    ? (string)API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? (string)COREMUSIC_SCHEME : 'http') . '://api.coremusic.net');
$apiBase = rtrim($apiBase, '/');

$assetsUrl   = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$adminUrl    = defined('ADMIN_URL') ? rtrim((string)ADMIN_URL, '/') : 'http://admin.coremusic.net:83';
$redirectUri = $_GET['redirect_uri'] ?? ($adminUrl . '/auth/callback');
$clientId    = $_GET['client_id'] ?? 'coremusic-admin';
$responseType = $_GET['response_type'] ?? 'session';

$figmaNote = 'Yönetici (Admin) girişi yapmak için lütfen yönetici iseniz giriş yapınız çünkü sistem doğrulaması vardır, anında kaçak giriş & hacker tespiti yapılmaktadır.';
$figmaQr   = 'Telefon ile mobil uygulamamız ya da QR kodu tarayarak da webden güvenli giriş yapabilirsiniz.';
?>
<div class="adm-page adm-page--login adm-auth-wrapper" data-mode="light">
    <!-- Arka Plan Katmanları (Figma %74 Fotoğraf & Cam Dokusu) -->
    <div class="adm-bg adm-auth-bg" aria-hidden="true">
        <div class="adm-bg__image"></div>
        <div class="adm-bg__scrim"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--1"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--2"></div>
    </div>

    <!-- Sağ Auth Paneli (Figma %26 / 500px Cam Panel SSOT) -->
    <div class="adm-auth-panel adm-auth-card adm-auth-card--light">
        <h1 class="adm-auth__title adm-auth-card__title">Core Music for Ali Admin</h1>
        <p class="adm-auth__subtitle adm-auth-card__subtitle">Sisteme Giriş Yapın </p>

        <!-- Bilgi Notu Bandı (Figma y:261 h:136) -->
        <div class="adm-auth__note adm-auth-note adm-auth-note--light">
            <img class="adm-auth__note-icon" src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="50" height="50">
            <p class="adm-auth__note-text"><?= $h($figmaNote) ?></p>
        </div>

        <!-- Yönetici Giriş Formu (Figma y:507 w:390) -->
        <form class="adm-auth__form" id="adm-login-form" method="post" action="/admin/login"
              novalidate aria-label="Yönetici girişi"
              data-endpoint="<?= $h($apiBase) ?>/v1/auth/login">
            <div class="adm-form">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="client_id" value="<?= $h($clientId) ?>">
                <input type="hidden" name="response_type" value="<?= $h($responseType) ?>">
                <input type="hidden" name="redirect_uri" value="<?= $h($redirectUri) ?>">

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

                <div class="adm-form__actions adm-form__row">
                    <label class="adm-form__remember adm-form__checkbox-label">
                        <input class="adm-form__check adm-form__checkbox" type="checkbox" name="remember" value="1" checked>
                        <span>Beni Hatırla</span>
                    </label>
                    <a class="adm-form__link" href="/admin/forgot-password" data-no-spa>Şifremi Unuttum</a>
                </div>

                <p class="adm-form__message adm-auth-error" id="adm-login-err" role="alert" aria-live="polite" hidden></p>

                <button type="submit" class="adm-btn adm-btn--primary" id="adm-login-btn">
                    <span class="adm-btn__label adm-btn__text">Sisteme Giriş Yap</span>
                    <span class="adm-btn__spinner" aria-hidden="true" hidden></span>
                </button>
            </div>
        </form>

        <!-- QR Giriş Bloğu (Figma y:941 h:100) -->
        <div class="adm-auth__qr adm-auth-qr adm-auth-qr--light">
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

        <div class="adm-auth-card__footer">
            <span>Yeni bir yönetici hesabı mı gerekiyor?</span>
            <a href="/admin/register" class="adm-form__link adm-form__link--bold" data-no-spa>Yönetici Kaydı</a>
        </div>
    </div>
</div>
