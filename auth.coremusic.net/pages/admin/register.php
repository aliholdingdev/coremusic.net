<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Kaydı (Figma 2161:12529 "Admin - Register") — Light Theme
 *
 * Yer: auth.coremusic.net/pages/admin/register.php
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

$gender = $_SESSION['cm_gender'] ?? $_COOKIE['cm_gender'] ?? 'neutral';
if (!in_array($gender, ['female', 'male', 'neutral'], true)) {
    $gender = 'neutral';
}

$figmaNote = 'Yönetici (Admin) olarak kayıt olmak için lütfen yönetici izniniz varsa sisteme kayıt olunuz; çünkü sistem ve yönetici tarafından doğrulama için onay gerekecektir. İzinsiz işlemler güvenlik loglarına kaydedilmektedir.';
$figmaQr   = 'Telefon ile mobil uygulamamız ya da QR kodu tarayarak da webden güvenli kayıt ve doğrulama yapabilirsiniz.';
?>
<div class="adm-auth-wrapper" data-mode="light">
    <!-- Light Theme Arka Plan Katmanları -->
    <div class="adm-auth-bg" aria-hidden="true">
        <div class="adm-auth-bg__glow adm-auth-bg__glow--1"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--2"></div>
        <div class="adm-auth-bg__pattern"></div>
    </div>

    <main class="adm-auth-container">
        <!-- Sol Alan: Marka & Bilgi Paneli -->
        <div class="adm-auth-brand">
            <div class="adm-auth-brand__header">
                <img src="<?= $h($assetsUrl) ?>/Image/res-pink/logo/logo-img.png" alt="" width="36" height="36" class="adm-auth-brand__logo">
                <span class="adm-auth-brand__name">CoreMusic <em>Admin</em></span>
            </div>
            <h1 class="adm-auth-brand__headline">Yeni Yönetici<br><span>Başvurusu</span></h1>
            <p class="adm-auth-brand__desc">Yetkili personel ve sistem yöneticileri için kayıt portalı. Tüm başvurular onay mekanizmasından geçer.</p>
            
            <div class="adm-auth-note adm-auth-note--light">
                <div class="adm-auth-note__icon-wrap">
                    <img src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="32" height="32" class="adm-auth-note__icon">
                </div>
                <div class="adm-auth-note__content">
                    <strong>Kayıt & Doğrulama Protokolü</strong>
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

        <!-- Sağ Alan: Açık Cam Kayıt Kartı -->
        <div class="adm-auth-card adm-auth-card--light">
            <div class="adm-auth-card__header">
                <h2 class="adm-auth-card__title">Core Music for Ali Admin</h2>
                <p class="adm-auth-card__subtitle">Yönetici Hesabı Oluşturun</p>
            </div>

            <div class="adm-auth-error" id="adm-register-err" role="alert" aria-live="polite" hidden></div>

            <form class="adm-form" id="adm-register-form" method="post" action="/admin/register" novalidate data-endpoint="<?= $h($apiBase) ?>/v1/auth/register">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="client_id" value="<?= $h($clientId) ?>">
                <input type="hidden" name="response_type" value="<?= $h($responseType) ?>">
                <input type="hidden" name="redirect_uri" value="<?= $h($redirectUri) ?>">
                <input type="hidden" name="gender" value="<?= $h($gender) ?>">

                <div class="adm-form__group">
                    <label class="adm-form__label" for="adm-reg-name">Ad Soyad</label>
                    <div class="adm-form__input-wrapper">
                        <span class="adm-form__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input class="adm-form__input" type="text" id="adm-reg-name" name="name" placeholder="Bayram Ali" autocomplete="name">
                    </div>
                </div>

                <div class="adm-form__row-2col">
                    <div class="adm-form__group">
                        <label class="adm-form__label" for="adm-reg-usr">Kullanıcı Adı</label>
                        <div class="adm-form__input-wrapper">
                            <span class="adm-form__icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            </span>
                            <input class="adm-form__input" type="text" id="adm-reg-usr" name="username" placeholder="aliadmin" autocomplete="username" required>
                        </div>
                    </div>

                    <div class="adm-form__group">
                        <label class="adm-form__label" for="adm-reg-birth">Doğum Tarihi</label>
                        <div class="adm-form__input-wrapper">
                            <span class="adm-form__icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </span>
                            <input class="adm-form__input" type="text" id="adm-reg-birth" name="birth_date" placeholder="GG.AA.YYYY" autocomplete="bday">
                        </div>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="adm-reg-email">E-posta Adresi</label>
                    <div class="adm-form__input-wrapper">
                        <span class="adm-form__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input class="adm-form__input" type="email" id="adm-reg-email" name="email" placeholder="bayramali@aliadmin.com" autocomplete="email" required>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="adm-reg-pw">Parola</label>
                    <div class="adm-form__input-wrapper">
                        <span class="adm-form__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="adm-form__input" type="password" id="adm-reg-pw" name="password" placeholder="••••••••••••" autocomplete="new-password" required>
                        <button type="button" class="adm-form__eye" aria-label="Şifreyi Göster/Gizle" tabindex="-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11 7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="adm-form__actions">
                    <label class="adm-form__checkbox-label">
                        <input type="checkbox" name="agree_terms" value="1" required class="adm-form__checkbox">
                        <span>Yönetici güvenlik sözleşmesini ve gizlilik koşullarını kabul ediyorum.</span>
                    </label>
                </div>

                <button type="submit" class="adm-btn adm-btn--primary" id="adm-register-btn">
                    <span class="adm-btn__text">Yönetici Başvurusu Yap</span>
                    <span class="adm-btn__spinner" aria-hidden="true" hidden></span>
                </button>
            </form>

            <div class="adm-auth-qr adm-auth-qr--light">
                <div class="adm-auth-qr__info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <p><?= $h($figmaQr) ?></p>
                </div>
            </div>

            <div class="adm-auth-card__footer">
                <span>Zaten yetkili hesabınız var mı?</span>
                <a href="/admin/login" class="adm-form__link adm-form__link--bold" data-no-spa>Yönetici Girişi</a>
            </div>
        </div>
    </main>
</div>
