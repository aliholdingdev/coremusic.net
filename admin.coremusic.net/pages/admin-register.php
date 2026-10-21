<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Yönetici Kaydı (figma 2161:12529 "Admin - Register")
 *
 * Mockup: .ai/.png/admin-1920/Admin - Register.png · spec: screens/T17-monitor-22fhd/admin-register.md
 * Çıkarım SSOT: .ai/ui-design/reference/figma/extracted-admin-1920.md §6
 *
 * Transport: admin-login ile aynı — fetch POST API /v1/auth/register
 * (desen: auth.coremusic.net/pages/register.php).
 *
 * API sözleşmesi (api config/routes.php RegisterRequest): username/email/password/
 * gender zorunlu + agree_terms zorunlu bool. Figma'daki "Adı Söyadı" ve
 * "Doğum Tarihi" alanlarının API karşılığı YOKTUR (display_name username'den
 * türetilir) → sadakat için render edilir, isteğe gömülmez (rapor ⚠️).
 * gender API tarafından zorunlu, figma'da alan yok → session/cookie
 * cm_gender (auth register deseni), geçersizse 'neutral'.
 */

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$nonce = (string)($_SESSION['csp_nonce'] ?? '');
$csrf  = (string)($_SESSION['csrf_token'] ?? '');

$apiBase = ((defined('API_URL') && API_URL)
    ? (string)API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? (string)COREMUSIC_SCHEME : 'http') . '://api.coremusic.net');
$apiBase = rtrim($apiBase, '/');

$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$authUrl   = defined('AUTH_URL') ? rtrim((string)AUTH_URL, '/') : '';
$adminUrl  = defined('ADMIN_URL') ? rtrim((string)ADMIN_URL, '/') : '';
$callback  = $adminUrl . '/auth/callback';

$shellJs   = dirname(__DIR__, 2) . '/assets.coremusic.net/js/admin/admin-shell.js';
$shellJsV  = is_file($shellJs) ? (string)filemtime($shellJs) : '';
$shellJsQs = $shellJsV !== '' ? '?v=' . $shellJsV : '';

$gender = $_SESSION['cm_gender'] ?? $_COOKIE['cm_gender'] ?? 'neutral';
if (!in_array($gender, ['female', 'male', 'neutral'], true)) {
    $gender = 'neutral';
}

// Figma metinleri (extracted-admin-1920.md §6 + ham JSON characters) —
// yazım hataları/OEM karakterler korunur (kopya doğruluk).
$figmaNote = 'Yönetici (Admin) olarak kayıt olmak için lutfen yönetici izini varsa sisteme kayıt olunuz cunku sistem & yönetici tarfından doğrulama için onay ve izin gerekecektir anında kaçak giriş & hacker tespiti yapılmaktadır izinsiz işlemler hacker olarak loglanacaktır ve kayıt edilecektir sistem yöneticisine bildirilecekitr';
$figmaQr   = 'Telefon ile mobil uygulamamız yada qr kodu tarayarak da web den guvenli giriş yapabilirsiniz';
?>
<div class="adm-page adm-page--register">
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

        <form class="adm-auth__form" id="adm-register-form" method="post" action="/admin-register"
              novalidate aria-label="Yönetici kaydı"
              data-endpoint="<?= $h($apiBase) ?>/v1/auth/register">
            <div class="adm-form">
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <input type="hidden" name="client_id" value="coremusic-web">
                <input type="hidden" name="response_type" value="session">
                <input type="hidden" name="redirect_uri" value="<?= $h($callback) ?>">
                <input type="hidden" name="gender" value="<?= $h($gender) ?>">

                <label class="adm-form__label adm-form__label--l1" for="adm-reg-name">Adı Söyadı</label>
                <div class="adm-form__field adm-form__field--i1 adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-reg-name" name="name"
                           placeholder="Bayram Ali" autocomplete="name">
                </div>

                <label class="adm-form__label adm-form__label--l2" for="adm-reg-usr">Kullanıcı Adı</label>
                <div class="adm-form__field adm-form__field--i2 adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-reg-usr" name="username"
                           placeholder="aliadmin" autocomplete="username" required>
                </div>

                <label class="adm-form__label adm-form__label--l3" for="adm-reg-email">Email Adresi</label>
                <div class="adm-form__field adm-form__field--i3 adm-form__field--icon">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <input class="adm-form__input" type="email" id="adm-reg-email" name="email"
                           placeholder="bayramali@aliadmin.com" autocomplete="email" required>
                </div>

                <label class="adm-form__label adm-form__label--l4" for="adm-reg-pw">Parola</label>
                <div class="adm-form__field adm-form__field--i4 adm-form__field--icon adm-form__field--pill adm-form__field--password">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input class="adm-form__input" type="password" id="adm-reg-pw" name="password"
                           placeholder="**********" autocomplete="new-password" required>
                </div>

                <label class="adm-form__label adm-form__label--l5" for="adm-reg-birth">Doğum Tarihi</label>
                <div class="adm-form__field adm-form__field--i5 adm-form__field--icon adm-form__field--pill">
                    <span class="adm-form__icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <input class="adm-form__input" type="text" id="adm-reg-birth" name="birth_date"
                           placeholder="Tarih : Gun : 15  Ay : Eylul  Yıl: 2005" autocomplete="bday">
                </div>

                <div class="adm-form__actions">
                    <label class="adm-form__remember">
                        <input class="adm-form__check" type="checkbox" id="adm-reg-terms" name="agree_terms" value="1" required>
                        <span>Okudum Kabul Ediyorum</span>
                    </label>
                    <a class="adm-form__link" href="<?= $h($authUrl) ?>/forgot-password" data-no-spa>Şifremi Unuttum</a>
                </div>

                <p class="adm-form__message" id="adm-register-err" role="alert" aria-live="polite" hidden></p>
            </div>
        </form>

        <!-- CTA — figma'da form grubu DIŞINDA (panel-rel y864) → form="..." ile bağlanır
             (p-admin-register.css: .adm-btn panel-rel konumlanır) -->
        <button type="submit" form="adm-register-form" class="adm-btn" id="adm-register-btn">
            <span class="adm-btn__label">Sisteme Giriş Yap</span>
        </button>

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
