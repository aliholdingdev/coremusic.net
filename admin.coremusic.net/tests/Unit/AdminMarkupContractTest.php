<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Görev D — Markup sözleşmesi testleri.
 *
 * Sayfalar standalone include edilir (bootstrap yalnız autoload yükler):
 * test kendisi sabitleri tanımlar, $_SESSION/$_SERVER tohumlar.
 *
 * Kapsam: figma sadakati (metinler yazım hatalarıyla birebir), sınıf
 * sözleşmesi (on-disk CSS seçicileri), nonce'lu script, çift <main> YOK,
 * "TODO UI" sızıntısı YOK, register CTA form bağlantısı, gizli gender.
 */
final class AdminMarkupContractTest extends TestCase
{
    /** Figma metinleri — extracted-admin-1920.md §5/§6 + ham JSON (birebir). */
    private const LOGIN_NOTE = 'Yönetici (Admin) girişi yapmak için lutfen yönetici iseniz giriş yapınız cunku sistem doğrulamsı vardır anında kaçak giriş & hacker tespiti yapılmaktadır';
    private const REGISTER_NOTE = 'Yönetici (Admin) olarak kayıt olmak için lutfen yönetici izini varsa sisteme kayıt olunuz cunku sistem & yönetici tarfından doğrulama için onay ve izin gerekecektir anında kaçak giriş & hacker tespiti yapılmaktadır izinsiz işlemler hacker olarak loglanacaktır ve kayıt edilecektir sistem yöneticisine bildirilecekitr';
    private const QR_TEXT = 'Telefon ile mobil uygulamamız yada qr kodu tarayarak da web den guvenli giriş yapabilirsiniz';

    private static bool $constantsDefined = false;

    public static function setUpBeforeClass(): void
    {
        if (self::$constantsDefined) {
            return;
        }
        self::$constantsDefined = true;

        // Sayfaların beklediği sabitler (config/bootstrap.php karşılığı).
        // DB_USER/DB_PASSWORD BİLEREK boş: Görev D kararı — dashboard, kimlik
        // bilgisi yokken ⚠️ VERIFICATION REQUIRED boş-durumuna düşer.
        $defs = [
            'ASSETS_URL'      => 'https://assets.coremusic.net',
            'AUTH_URL'        => 'https://auth.coremusic.net',
            'ADMIN_URL'       => 'https://admin.coremusic.net',
            'COREMUSIC_SCHEME' => 'https',
            'DB_HOST'         => 'localhost',
            'DB_ADMIN_NAME'   => 'coremusic_admin',
            'DB_USER'         => '',
            'DB_PASSWORD'     => '',
            'DB_PORT'         => 3306,
            'DB_CHARSET'      => 'utf8mb4',
        ];
        foreach ($defs as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }

    /** Sayfayı sabit oturum tohumuyla render eder. */
    private function render(string $page): string
    {
        $_SESSION = [
            'csp_nonce'   => 'test-nonce-123',
            'csrf_token'  => 'test-csrf-456',
            'MM_Username' => 'bayramali',
            'MM_UserRole' => 'Administartor',
            'cm_gender'   => 'female',
        ];
        $_SERVER['REMOTE_ADDR'] = '203.0.113.7';

        $file = dirname(__DIR__, 2) . '/pages/' . $page . '.php';
        self::assertFileExists($file);

        ob_start();
        try {
            include $file;
        } finally {
            $out = (string) ob_get_clean();
        }

        self::assertNotSame('', $out, $page . ' boş çıktı üretti');
        return $out;
    }

    public function testLoginPageMatchesFigmaContract(): void
    {
        $out = $this->render('admin-login');

        // Sayfa + panel sınıf sözleşmesi
        foreach ([
            'adm-page--login', 'adm-auth-panel', 'adm-auth__title', 'adm-auth__subtitle',
            'adm-auth__note', 'adm-auth__note-icon', 'adm-auth__note-text',
            'adm-auth__form', 'adm-form__label--l1', 'adm-form__label--l2',
            'adm-form__field--i1', 'adm-form__field--i2',
            'adm-form__field--icon', 'adm-form__field--pill', 'adm-form__field--password',
            'adm-form__remember', 'adm-form__check', 'adm-form__link', 'adm-form__message',
            'adm-btn', 'adm-btn__label',
            'adm-auth__qr-card', 'adm-auth__qr-frame', 'adm-auth__qr-text',
        ] as $class) {
            self::assertStringContainsString($class, $out, 'Eksik sınıf: ' . $class);
        }

        // Figma metinleri (yazım hataları/OEM karakterler dâhil birebir;
        // & işareti çıktıda htmlspecialchars ile &amp; olur)
        $esc = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        self::assertStringContainsString($esc(self::LOGIN_NOTE), $out);
        self::assertStringContainsString('Sisteme Giriş Yapın </p>', $out, 'alt başlık sondaki boşlukla korunmalı');
        self::assertStringContainsString(self::QR_TEXT, $out);
        self::assertStringContainsString('Kullanıcı adı yada Email', $out);
        self::assertStringContainsString('Şifremi Unuttum', $out);
        self::assertStringContainsString('placeholder="Bayram Ali"', $out);
        self::assertStringContainsString('placeholder="**********"', $out);

        // Transport: fetch endpoint + gizli redirect_uri + CSRF
        self::assertStringContainsString('data-endpoint="https://api.coremusic.net/v1/auth/login"', $out);
        self::assertStringContainsString('name="redirect_uri" value="https://admin.coremusic.net/auth/callback"', $out);
        self::assertStringContainsString('name="csrf_token" value="test-csrf-456"', $out);
        self::assertStringContainsString('name="client_id" value="coremusic-web"', $out);

        // nonce'lu external script (CSP strict-dynamic)
        self::assertStringContainsString('<script nonce="test-nonce-123"', $out);
        self::assertStringContainsString('/js/admin/admin-shell.js', $out);
        self::assertStringContainsString(' defer>', $out);

        // YASAKLAR
        self::assertStringNotContainsString('TODO UI', $out);
        self::assertStringNotContainsString('<main', $out, '<main yalnız HtmlShellRenderer\'da üretilir');
        self::assertStringNotContainsString('id="main-content"', $out);
        self::assertStringNotContainsString('style="', $out, 'inline style yasak (Guardrail #3)');
    }

    public function testRegisterPageMatchesFigmaContract(): void
    {
        $out = $this->render('admin-register');

        foreach ([
            'adm-page--register', 'adm-auth-panel', 'adm-auth__title', 'adm-auth__subtitle',
            'adm-auth__note', 'adm-auth__note-text', 'adm-auth__form',
            'adm-form__label--l1', 'adm-form__label--l2', 'adm-form__label--l3',
            'adm-form__label--l4', 'adm-form__label--l5',
            'adm-form__field--i1', 'adm-form__field--i2', 'adm-form__field--i3',
            'adm-form__field--i4', 'adm-form__field--i5',
            'adm-form__field--pill', 'adm-form__field--password',
            'adm-form__remember', 'adm-form__check', 'adm-form__link', 'adm-form__message',
            'adm-btn', 'adm-btn__label',
            'adm-auth__qr-card', 'adm-auth__qr-frame', 'adm-auth__qr-text',
        ] as $class) {
            self::assertStringContainsString($class, $out, 'Eksik sınıf: ' . $class);
        }

        // Figma metinleri
        $esc = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        self::assertStringContainsString($esc(self::REGISTER_NOTE), $out);
        self::assertStringContainsString(self::QR_TEXT, $out);
        self::assertStringContainsString('Adı Söyadı', $out);
        self::assertStringContainsString('Doğum Tarihi', $out);
        self::assertStringContainsString('placeholder="Tarih : Gun : 15  Ay : Eylul  Yıl: 2005"', $out);
        self::assertStringContainsString('Okudum Kabul Ediyorum', $out);

        // CTA formun DIŞINDA, form="..." ile bağlı (p-admin-register.css panel-rel)
        self::assertStringContainsString('form="adm-register-form"', $out);
        self::assertStringContainsString('id="adm-register-btn"', $out);

        // Gizli gender session/cookie'den, doğrulanmış aralıkta
        self::assertStringContainsString('name="gender" value="female"', $out);

        // Transport
        self::assertStringContainsString('data-endpoint="https://api.coremusic.net/v1/auth/register"', $out);
        self::assertStringContainsString('name="redirect_uri" value="https://admin.coremusic.net/auth/callback"', $out);
        self::assertStringContainsString('<script nonce="test-nonce-123"', $out);
        self::assertStringContainsString('/js/admin/admin-shell.js', $out);

        // YASAKLAR
        self::assertStringNotContainsString('TODO UI', $out);
        self::assertStringNotContainsString('<main', $out);
        self::assertStringNotContainsString('style="', $out);
    }

    public function testLayoutShellContractViaSongsPage(): void
    {
        $out = $this->render('admin-songs');

        foreach ([
            'adm-page--layout', 'adm-sidebar__title', 'adm-sidebar__group',
            'adm-sidebar__item', 'adm-sidebar__label',
            'adm-navbar__avatar', 'adm-navbar__name', 'adm-navbar__logout',
            'adm-content', 'adm-panel__title', 'adm-panel__breadcrumb',
            'adm-footer__meta--user', 'adm-footer__meta--ip', 'adm-footer__meta--role',
            'adm-copyright', 'adm-auth-panel',
        ] as $class) {
            self::assertStringContainsString($class, $out, 'Eksik sınıf: ' . $class);
        }

        // Aktif nav + figma chrome metinleri (yazım hatası korunur)
        self::assertStringContainsString('is-active', $out);
        self::assertStringContainsString('aria-current="page"', $out);
        self::assertStringContainsString('User Type : Administartor', $out);
        self::assertStringContainsString('Home / Şarkılar', $out);
        self::assertStringContainsString('bayramali - admin', $out);
        self::assertStringContainsString('IP : 203.0.113.7', $out);

        // Logout auth domainine gider (admin'de local logout rotası YOK)
        self::assertStringContainsString('https://auth.coremusic.net/logout', $out);

        // Çift landmark YOK
        self::assertStringNotContainsString('<main', $out);
        self::assertStringNotContainsString('TODO UI', $out);
    }

    public function testPlaceholderPagesShowYapimAsamasindaEmptyState(): void
    {
        foreach (['admin-songs', 'admin-song-add', 'admin-playlist', 'admin-settings'] as $page) {
            $out = $this->render($page);
            self::assertStringContainsString('is-empty', $out, $page . ' boş-durum işaretçisi taşımalı');
            self::assertStringContainsString('yapım aşamasındadır', $out, $page . ' yapım aşamasında metni taşımalı');
            self::assertStringNotContainsString('TODO UI', $out, $page . ' "TODO UI" sızdırmamalı');
        }
    }

    public function testDashboardWithoutDatabaseCredentialsShowsVerificationRequired(): void
    {
        // DB_USER/DB_PASSWORD boş (setUpBeforeClass) → gerçek sorgu denemesi
        // yapılamaz; uydurma sayım ÜRETİLMEZ, ⚠️ boş-durumu gösterilir.
        $out = $this->render('admin-dashboard');

        self::assertStringContainsString('adm-page--layout', $out);
        self::assertStringContainsString('is-empty', $out);
        self::assertStringContainsString('⚠️ VERIFICATION REQUIRED', $out);
        self::assertStringNotContainsString('Kayıtlı yönetici sayısı:', $out, 'doğrulanmamış sayı gösterilemez');
        self::assertStringNotContainsString('TODO UI', $out);
    }

    public function testAdminShellJsIsEs6AndCspSafe(): void
    {
        $file = dirname(__DIR__, 3) . '/assets.coremusic.net/js/admin/admin-shell.js';
        self::assertFileExists($file, 'admin-shell.js oluşturulmalı');

        $js = (string) file_get_contents($file);

        self::assertNotSame('', $js);
        self::assertDoesNotMatchRegularExpression('/\bvar\s+[A-Za-z_$]/', $js, 'var yasak (ES6 const/let)');
        self::assertStringNotContainsString('eval(', $js, 'eval yasak');
        self::assertStringNotContainsString('innerHTML', $js, 'innerHTML yasak (TrustedTypes)');
        self::assertStringNotContainsString('document.write', $js);

        // Çekirdek davranışlar mevcut (endpoint hardcoded değil — data-endpoint'ten okunur)
        self::assertStringContainsString("'use strict'", $js);
        self::assertStringContainsString('credentials: \'include\'', $js);
        self::assertStringContainsString('form.dataset.endpoint', $js);
        self::assertStringContainsString('agree_terms', $js);
        self::assertStringContainsString('is-loading', $js);
        self::assertStringContainsString('redirect_uri', $js);
    }
}
