<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Kaydı (Figma 2161:12529 "Admin - Register") — Light Theme SSOT
 *
 * Yer: auth.coremusic.net/pages/admin/register.php
 * Tasarım: Açık Kristal Cam (Light Theme Glassmorphism)
 * Otorite: .ai/.png/admin-1920/Admin - Register.png
 */

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$nonce = (string)($_SESSION['csp_nonce'] ?? '');
$csrf  = (string)($_SESSION['csrf_token'] ?? '');

$apiBase = ((defined('API_URL') && API_URL)
    ? (string)API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? (string)COREMUSIC_SCHEME : 'http') . '://api.coremusic.net');
$apiBase = rtrim($apiBase, '/');

$assetsUrl   = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$adminUrl    = defined('ADMIN_URL') ? rtrim((string)ADMIN_URL, '/') : 'http://admin.coremusic.net';
$redirectUri = $_GET['redirect_uri'] ?? ($adminUrl . '/auth/callback');
$clientId    = $_GET['client_id'] ?? 'coremusic-admin';
$responseType = $_GET['response_type'] ?? 'session';

$gender = $_SESSION['cm_gender'] ?? $_COOKIE['cm_gender'] ?? 'neutral';
if (!in_array($gender, ['female', 'male', 'neutral'], true)) {
    $gender = 'neutral';
}

$figmaNote = 'Yönetici (Admin) olarak kayıt olmak için lütfen yönetici izniniz varsa sisteme kayıt olunuz; çünkü sistem ve yönetici tarafından doğrulama için onay gerekecektir. İzinsiz işlemler güvenlik loglarına kaydedilmektedir.';
$figmaQr   = 'Telefon ile mobil uygulamamız ya da QR kodu tarayarak da webden güvenli kayıt ve doğrulama yapabilirsiniz.';
?>
<div class="adm-page adm-page--register adm-auth-wrapper" data-mode="light">
    <!-- Arka Plan Katmanları (Figma %74 Fotoğraf & Kristal Cam Dokusu) -->
    <div class="adm-bg adm-auth-bg" aria-hidden="true">
        <div class="adm-bg__image"></div>
        <div class="adm-bg__scrim"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--1"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--2"></div>
    </div>

    <!-- Sağ Auth Paneli (Figma %26 / 500px Cam Panel SSOT) -->
    <div class="adm-auth-panel adm-auth-card adm-auth-card--light">
        <h1 class="adm-auth__title adm-auth-card__title">Core Music for Ali Admin</h1>
        <p class="adm-auth__subtitle adm-auth-card__subtitle">Yönetici Başvurusu</p>

        <!-- Bilgi Notu Bandı -->
        <div class="adm-auth__note adm-auth-note adm-auth-note--light">
            <img class="adm-auth__note-icon" src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="50" height="50">
            <p class="adm-auth__note-text"><?= $h($figmaNote) ?></p>
        </div>

        <!-- Yönetici Kayıt Formu -->
        <form class="adm-auth__form" id="adm-register-form" method="post" action="/admin/register"
              novalidate aria-label="Yönetici kaydı"
              data-endpoint="<?= $h($apiBase) ?>/v1/auth/register">
            <div class="adm-form">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="client_id" value="<?= $h($clientId) ?>">
                <input type="hidden" name="response_type" value="<?= $h($responseType) ?>">
                <input type="hidden" name="redirect_uri" value="<?= $h($redirectUri) ?>">
                <input type="hidden" name="gender" value="<?= $h($gender) ?>">

                <label class="adm-form__label" for="adm-reg-name">Adı Soyadı</label>
                <div class="adm-form__field adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-reg-name" name="name"
                           placeholder="Bayram Ali" autocomplete="name">
                </div>

                <label class="adm-form__label" for="adm-reg-usr">Kullanıcı Adı</label>
                <div class="adm-form__field adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-reg-usr" name="username"
                           placeholder="aliadmin" autocomplete="username" required>
                </div>

                <label class="adm-form__label" for="adm-reg-email">Email Adresi</label>
                <div class="adm-form__field adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <input class="adm-form__input" type="email" id="adm-reg-email" name="email"
                           placeholder="bayramali@aliadmin.com" autocomplete="email" required>
                </div>

                <label class="adm-form__label" for="adm-reg-pw">Parola</label>
                <div class="adm-form__field adm-form__field--icon adm-form__field--pill adm-form__field--password">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input class="adm-form__input" type="password" id="adm-reg-pw" name="password"
                           placeholder="**********" autocomplete="new-password" required>
                </div>

                <label class="adm-form__label" for="adm-reg-birth">Doğum Tarihi</label>
                <div class="adm-form__field adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-reg-birth" name="birth_date"
                           placeholder="Tarih : Gun : 15  Ay : Eylul  Yıl: 2005" autocomplete="bday">
                </div>

                <div class="adm-form__actions adm-form__row">
                    <label class="adm-form__remember adm-form__checkbox-label">
                        <input class="adm-form__check adm-form__checkbox" type="checkbox" name="agree_terms" value="1" checked required>
                        <span>Okudum Kabul Ediyorum</span>
                    </label>
                    <a class="adm-form__link" href="/admin/forgot-password" data-no-spa>Şifremi Unuttum</a>
                </div>

                <p class="adm-form__message adm-auth-error" id="adm-register-err" role="alert" aria-live="polite" hidden></p>

                <button type="submit" class="adm-btn adm-btn--primary" id="adm-register-btn">
                    <span class="adm-btn__label adm-btn__text">Sisteme Giriş Yap</span>
                    <span class="adm-btn__spinner" aria-hidden="true" hidden></span>
                </button>
            </div>
        </form>

        <!-- QR Kayıt Bloğu -->
        <div class="adm-auth__qr adm-auth-qr adm-auth-qr--light">
            <div class="adm-auth__qr-card"></div>
            <div class="adm-auth__qr-frame">
                <svg class="adm-auth__qr-phone-svg" viewBox="0 0 28 56" width="28" height="56" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="27" height="55" rx="5.5" stroke="#B0B5C4" stroke-width="1.2" fill="rgba(35, 25, 45, 0.75)"/>
                    <rect x="3" y="6" width="22" height="40" rx="2" fill="#E8EDF5"/>
                    <circle cx="14" cy="50" r="2.5" fill="#B0B5C4"/>
                </svg>
            </div>
            <p class="adm-auth__qr-text"><?= $h($figmaQr) ?></p>
        </div>

        <!-- Alt Bağlantı -->
        <div class="adm-auth-card__footer">
            <span>Zaten yetkili hesabınız var mı?</span>
            <a href="/admin/login" class="adm-form__link adm-form__link--bold" data-no-spa>Yönetici Girişi</a>
        </div>
    </div>
</div>
