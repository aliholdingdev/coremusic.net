<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Çıkışı (Logout) — Light Theme
 *
 * Yer: auth.coremusic.net/pages/admin/logout.php
 * Tasarım: Açık Tema (Light Theme / html[data-mode="light"]) Glassmorphism
 * Token: a-color-mode-tokens.css + a-light-glass-tokens.css + a-admin-tokens.css
 */

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$nonce = (string)($_SESSION['csp_nonce'] ?? '');
$csrf  = (string)($_SESSION['csrf_token'] ?? '');

$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$adminUrl  = defined('ADMIN_URL') ? rtrim((string)ADMIN_URL, '/') : 'http://admin.coremusic.net:83';

$figmaNote = 'Yönetici oturumunuz ve aktif güvenlik anahtarlarınız başarıyla sonlandırılmıştır. Güvenliğiniz için tarayıcınızı kapatabilir veya yeniden giriş yapabilirsiniz.';
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
            <h1 class="adm-auth-brand__headline">Güvenli Çıkış<br><span>Tamamlandı</span></h1>
            <p class="adm-auth-brand__desc">Yönetim paneli erişiminiz güvenli biçimde sonlandırıldı.</p>
            
            <div class="adm-auth-note adm-auth-note--light">
                <div class="adm-auth-note__icon-wrap">
                    <img src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="32" height="32" class="adm-auth-note__icon">
                </div>
                <div class="adm-auth-note__content">
                    <strong>Güvenlik Protokolü</strong>
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
                <div class="adm-auth-card__success-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h2 class="adm-auth-card__title">Oturum Kapatıldı</h2>
                <p class="adm-auth-card__subtitle">Yönetici panelinden güvenle çıkış yaptınız</p>
            </div>

            <div class="adm-auth-logout-actions">
                <a href="/admin/login" class="adm-btn adm-btn--primary" data-no-spa style="text-decoration:none;display:inline-flex;justify-content:center;align-items:center;">
                    <span class="adm-btn__text">Yeniden Giriş Yap</span>
                </a>
            </div>

            <div class="adm-auth-card__footer">
                <span>CoreMusic Ana Sayfasına Dönmek İçin</span>
                <a href="http://home.coremusic.net:81" class="adm-form__link adm-form__link--bold" data-no-spa>Siteye Git</a>
            </div>
        </div>
    </main>
</div>
