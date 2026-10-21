<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Yeni Şifre Belirleme — Light Theme
 *
 * Yer: auth.coremusic.net/pages/admin/reset-password.php
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
$token     = $_GET['token'] ?? '';

$figmaNote = 'Yeni şifreniz en az 8 karakter uzunluğunda olmalı; büyük-küçük harf, rakam ve özel karakter içermelidir.';
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
            <h1 class="adm-auth-brand__headline">Yeni Şifre<br><span>Belirleme</span></h1>
            <p class="adm-auth-brand__desc">Hesap güvenliğinizi sağlamak için güçlü ve benzersiz bir yönetici parolası belirleyin.</p>
            
            <div class="adm-auth-note adm-auth-note--light">
                <div class="adm-auth-note__icon-wrap">
                    <img src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="32" height="32" class="adm-auth-note__icon">
                </div>
                <div class="adm-auth-note__content">
                    <strong>Parola Güvenlik Standartları</strong>
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
                <h2 class="adm-auth-card__title">Yeni Şifre Oluştur</h2>
                <p class="adm-auth-card__subtitle">Lütfen yeni yönetici şifrenizi giriniz</p>
            </div>

            <div class="adm-auth-error" id="adm-rp-msg" role="alert" aria-live="polite" hidden></div>

            <form class="adm-form" id="adm-rp-form" method="post" action="/admin/reset-password" novalidate data-endpoint="<?= $h($apiBase) ?>/v1/auth/reset-password">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="token" value="<?= $h($token) ?>">

                <div class="adm-form__group">
                    <label class="adm-form__label" for="adm-rp-pw">Yeni Parola</label>
                    <div class="adm-form__input-wrapper">
                        <span class="adm-form__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="adm-form__input" type="password" id="adm-rp-pw" name="password" placeholder="En az 8 karakter" autocomplete="new-password" required minlength="8">
                        <button type="button" class="adm-form__eye" aria-label="Şifreyi Göster/Gizle" tabindex="-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11 7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="adm-rp-pw-confirm">Yeni Parola (Tekrar)</label>
                    <div class="adm-form__input-wrapper">
                        <span class="adm-form__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="adm-form__input" type="password" id="adm-rp-pw-confirm" name="password_confirm" placeholder="Parolayı tekrar girin" autocomplete="new-password" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="adm-btn adm-btn--primary" id="adm-rp-btn">
                    <span class="adm-btn__text">Şifremi Güncelle</span>
                    <span class="adm-btn__spinner" aria-hidden="true" hidden></span>
                </button>
            </form>

            <div class="adm-auth-card__footer">
                <span>Giriş ekranına dönmek için</span>
                <a href="/admin/login" class="adm-form__link adm-form__link--bold" data-no-spa>Yönetici Girişi</a>
            </div>
        </div>
    </main>
</div>
