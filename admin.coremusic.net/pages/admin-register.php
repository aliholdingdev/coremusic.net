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
                    <input class="adm-form__input" type="text" id="adm-reg-name" name="name"
                           placeholder="Bayram Ali" autocomplete="name">
                </div>

                <label class="adm-form__label adm-form__label--l2" for="adm-reg-usr">Kullanıcı Adı</label>
                <div class="adm-form__field adm-form__field--i2 adm-form__field--icon">
                    <input class="adm-form__input" type="text" id="adm-reg-usr" name="username"
                           placeholder="aliadmin" autocomplete="username" required>
                </div>

                <label class="adm-form__label adm-form__label--l3" for="adm-reg-email">Email Adresi</label>
                <div class="adm-form__field adm-form__field--i3 adm-form__field--icon">
                    <input class="adm-form__input" type="email" id="adm-reg-email" name="email"
                           placeholder="bayramali@aliadmin.com" autocomplete="email" required>
                </div>

                <label class="adm-form__label adm-form__label--l4" for="adm-reg-pw">Parola</label>
                <div class="adm-form__field adm-form__field--i4 adm-form__field--icon adm-form__field--pill adm-form__field--password">
                    <input class="adm-form__input" type="password" id="adm-reg-pw" name="password"
                           placeholder="**********" autocomplete="new-password" required>
                </div>

                <label class="adm-form__label adm-form__label--l5" for="adm-reg-birth">Doğum Tarihi</label>
                <div class="adm-form__field adm-form__field--i5 adm-form__field--pill">
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
            <div class="adm-auth__qr-frame"></div>
            <p class="adm-auth__qr-text"><?= $h($figmaQr) ?></p>
        </div>
    </div>

    <script nonce="<?= $h($nonce) ?>" src="<?= $h($assetsUrl) ?>/js/admin/admin-shell.js<?= $shellJsQs ?>" defer></script>
</div>
