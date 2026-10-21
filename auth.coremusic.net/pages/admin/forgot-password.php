<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Şifre Sıfırlama — Light Theme
 *
 * Yer: auth.coremusic.net/pages/admin/forgot-password.php
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

$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$adminUrl  = defined('ADMIN_URL') ? rtrim((string)ADMIN_URL, '/') : 'http://admin.coremusic.net:83';

$figmaNote = 'Yönetici şifre sıfırlama işlemi kurumsal e-posta onayına tabidir. Doğrulama linki 15 dakika geçerlidir ve tek kullanımlıktır.';
?>
<div class="adm-auth-wrapper" data-mode="light">
    <div class="adm-auth-bg" aria-hidden="true">
        <div class="adm-auth-bg__glow adm-auth-bg__glow--1"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--2"></div>
        <div class="adm-auth-bg__pattern"></div>
    </div>

    <main class="adm-auth-container">
        <!-- Sol Bilgi Paneli -->
        <div class="adm-auth-brand">
            <div class="adm-auth-brand__header">
                <img src="<?= $h($assetsUrl) ?>/Image/res-pink/logo/logo-img.png" alt="" width="36" height="36" class="adm-auth-brand__logo">
                <span class="adm-auth-brand__name">CoreMusic <em>Admin</em></span>
            </div>
            <h1 class="adm-auth-brand__headline">Yönetici Şifre<br><span>Kurtarma</span></h1>
            <p class="adm-auth-brand__desc">Yetkili hesabınıza ait şifreyi güvenli e-posta doğrulama tokenı ile yenileyebilirsiniz.</p>
            
            <div class="adm-auth-note adm-auth-note--light">
                <div class="adm-auth-note__icon-wrap">
                    <img src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="32" height="32" class="adm-auth-note__icon">
                </div>
                <div class="adm-auth-note__content">
                    <strong>Güvenlik Bildirimi</strong>
                    <p><?= $h($figmaNote) ?></p>
                </div>
            </div>

            <div class="adm-auth-brand__footer">
                <a href="<?= $h($adminUrl) ?>/privacy" data-no-spa>Gizlilik</a>
                <span class="dot"></span>
                <a href="<?= $h($adminUrl) ?>/security" data-no-spa>Güvenlik</a>
                <span class="dot"></span>
                <span>&copy; <?= date('Y') ?> CoreMusic</span>
            </div>
        </div>

        <!-- Sağ Kart -->
        <div class="adm-auth-card adm-auth-card--light">
            <div class="adm-auth-card__header">
                <h2 class="adm-auth-card__title">Şifremi Unuttum</h2>
                <p class="adm-auth-card__subtitle">Kayıtlı yönetici e-posta adresinizi giriniz</p>
            </div>

            <div class="adm-auth-error" id="adm-fp-msg" role="alert" aria-live="polite" hidden></div>

            <form class="adm-form" id="adm-fp-form" method="post" action="/admin/forgot-password" novalidate data-endpoint="<?= $h($apiBase) ?>/v1/auth/forgot-password">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">

                <div class="adm-form__group">
                    <label class="adm-form__label" for="adm-fp-email">Yönetici E-posta Adresi</label>
                    <div class="adm-form__input-wrapper">
                        <span class="adm-form__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input class="adm-form__input" type="email" id="adm-fp-email" name="email" placeholder="admin@coremusic.net" autocomplete="email" required>
                    </div>
                </div>

                <button type="submit" class="adm-btn adm-btn--primary" id="adm-fp-btn">
                    <span class="adm-btn__text">Sıfırlama Bağlantısı Gönder</span>
                    <span class="adm-btn__spinner" aria-hidden="true" hidden></span>
                </button>
            </form>

            <div class="adm-auth-card__footer">
                <span>Şifrenizi hatırladınız mı?</span>
                <a href="/admin/login" class="adm-form__link adm-form__link--bold" data-no-spa>Giriş Yap'a Dön</a>
            </div>
        </div>
    </main>
</div>
