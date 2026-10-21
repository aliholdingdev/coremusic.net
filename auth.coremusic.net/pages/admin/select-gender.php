<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Profil / Cinsiyet Seçimi — Light Theme
 *
 * Yer: auth.coremusic.net/pages/admin/select-gender.php
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

$figmaNote = 'Seçilen profil tercihi, yönetici paneli tema tonlamasını ve ses motoru arayüzünü kişiselleştirecektir.';
?>
<div class="adm-auth-wrapper" data-mode="light">
    <div class="adm-auth-bg" aria-hidden="true">
        <div class="adm-auth-bg__glow adm-auth-bg__glow--1"></div>
        <div class="adm-auth-bg__glow adm-auth-bg__glow--2"></div>
        <div class="adm-auth-bg__pattern"></div>
    </div>

    <main class="adm-auth-container">
        <!-- Sol Alan -->
        <div class="adm-auth-brand">
            <div class="adm-auth-brand__header">
                <img src="<?= $h($assetsUrl) ?>/Image/res-pink/logo/logo-img.png" alt="" width="36" height="36" class="adm-auth-brand__logo">
                <span class="adm-auth-brand__name">CoreMusic <em>Admin</em></span>
            </div>
            <h1 class="adm-auth-brand__headline">Yönetici Profil<br><span>Yapılandırması</span></h1>
            <p class="adm-auth-brand__desc">Yönetim panelindeki görsel deneyimi ve çalışma ortamınızı tercihinize göre özelleştirin.</p>
            
            <div class="adm-auth-note adm-auth-note--light">
                <div class="adm-auth-note__icon-wrap">
                    <img src="<?= $h($assetsUrl) ?>/Image/res-pink/info-mavi.png" alt="" width="32" height="32" class="adm-auth-note__icon">
                </div>
                <div class="adm-auth-note__content">
                    <strong>Kişiselleştirme Protokolü</strong>
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
                <h2 class="adm-auth-card__title">Profil Tercihi</h2>
                <p class="adm-auth-card__subtitle">Lütfen panel deneyiminiz için profil seçin</p>
            </div>

            <div class="adm-auth-error" id="adm-gender-err" role="alert" aria-live="polite" hidden></div>

            <form class="adm-form" id="adm-gender-form" method="post" action="/admin/set-gender" novalidate data-endpoint="<?= $h($apiBase) ?>/v1/auth/set-gender">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="client_id" value="<?= $h($clientId) ?>">
                <input type="hidden" name="response_type" value="<?= $h($responseType) ?>">
                <input type="hidden" name="redirect_uri" value="<?= $h($redirectUri) ?>">
                <input type="hidden" name="gender" id="adm-gender-input" value="<?= $h($gender) ?>">

                <div class="adm-gender-options">
                    <button type="button" class="adm-gender-card <?= $gender === 'female' ? 'adm-gender-card--active' : '' ?>" data-gender="female">
                        <div class="adm-gender-card__icon">
                            <img src="<?= $h($assetsUrl) ?>/Image/res-pink/kız-gender-select.png" alt="" width="32" height="32">
                        </div>
                        <div class="adm-gender-card__text">
                            <strong>Kadın / Soft</strong>
                            <small>Zarif tonlama ve pastel cam detayları</small>
                        </div>
                    </button>

                    <button type="button" class="adm-gender-card <?= $gender === 'male' ? 'adm-gender-card--active' : '' ?>" data-gender="male">
                        <div class="adm-gender-card__icon">
                            <img src="<?= $h($assetsUrl) ?>/Image/res-pink/erkek-gender-select.png" alt="" width="32" height="32">
                        </div>
                        <div class="adm-gender-card__text">
                            <strong>Erkek / Modern</strong>
                            <small>Dinamik koyu-açık kontrast ve mavi tonlar</small>
                        </div>
                    </button>

                    <button type="button" class="adm-gender-card <?= $gender === 'neutral' ? 'adm-gender-card--active' : '' ?>" data-gender="neutral">
                        <div class="adm-gender-card__icon">
                            <img src="<?= $h($assetsUrl) ?>/Image/res-pink/notur-gender-select.png" alt="" width="32" height="32">
                        </div>
                        <div class="adm-gender-card__text">
                            <strong>Belirtmek İstemiyorum / Standart</strong>
                            <small>Dengeli nötr açık tema ve sade cam yüzey</small>
                        </div>
                    </button>
                </div>

                <button type="submit" class="adm-btn adm-btn--primary" id="adm-gender-submit">
                    <span class="adm-btn__text">Seçimi Kaydet ve Devam Et</span>
                    <span class="adm-btn__spinner" aria-hidden="true" hidden></span>
                </button>
            </form>

            <div class="adm-auth-card__footer">
                <a href="/admin/login" class="adm-form__link" data-no-spa>Giriş Ekranına Geç</a>
            </div>
        </div>
    </main>
</div>
