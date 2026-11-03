<?php

declare(strict_types=1);

namespace Media\Test\Unit\Http;

use CoreMusic\Security\JwtService;
use Media\Http\MediaAccessGuard;
use Media\MediaHttpException;
use Media\Test\Fake\InMemoryMediaAccessRepository;
use PHPUnit\Framework\TestCase;

/**
 * MediaAccessGuardTest — fail-closed erişim denetimi (Faz 2 tasarım maddesi 3).
 *
 * Sıra: 400 sözdizimi (auth ÖNCESİ) → 401 kimlik → 403 izin → 404/403 varlık
 *      → 403 yol → 200 (ctx).
 *
 * Ortam: temp fixture kök + sahte repo (DB yok) + sahte oturum başlatıcı
 * (gerçek `session_start()` YOK). `MEDIA_ROOT` asla gerçek medya ağacına
 * (38 GB, gitignored) işaretlenmez.
 */
final class MediaAccessGuardTest extends TestCase
{
    private const GECERLI_KULANICI = '01930f2c-1111-7222-a333-444455556666';
    private const KULANICI_HEX = '01930f2c11117222a333444455556666';
    private const DOSYA_KEY = '01JBTESTKEY000000000000000';

    private string $kok;

    private InMemoryMediaAccessRepository $repo;

    /** @var array<string, mixed> kurtarilacak `$_SERVER`/`$_COOKIE`/`$_SESSION`/`$_ENV` durumu */
    private array $kurtar = [];

    private bool $oturumBaslatildi = false;

    /** @var array<int, string> temizlenecek jwt anahtar dizinleri */
    private array $geciciKlasorler = [];

    protected function setUp(): void
    {
        $this->kok = sys_get_temp_dir() . '/media_guard_' . bin2hex(random_bytes(6));
        $dizin = $this->kok . '/media/_inbox/2026-10-30';
        if (!mkdir($dizin, 0o777, true) && !is_dir($dizin)) {
            self::fail('fixture dizini olusturulamadi');
        }
        file_put_contents($dizin . '/audio.flac', 'FIXTURE-BYTE-ICERIGI');

        $_ENV['MEDIA_ROOT'] = $this->kok;
        $this->repo = new InMemoryMediaAccessRepository();
        $this->repo->asset = [
            'id' => self::DOSYA_KEY,
            'dosya_yolu' => 'media/_inbox/2026-10-30/audio.flac',
            'durum' => 'aktif',
            'dosya' => 'audio.flac',
            'format' => 'flac',
            'boyut_bayt' => 20,
            'sha256' => str_repeat('a', 64),
        ];

        foreach (['HTTP_AUTHORIZATION', 'HTTP_RANGE', 'HTTP_IF_NONE_MATCH', 'REQUEST_METHOD'] as $anahtar) {
            $this->kurtar['server:' . $anahtar] = $_SERVER[$anahtar] ?? null;
            unset($_SERVER[$anahtar]);
        }
        $this->kurtar['cookie'] = $_COOKIE;
        $this->kurtar['session'] = $_SESSION ?? null;
        $this->kurtar['env'] = $_ENV['MEDIA_ROOT'] ?? null;
        $_COOKIE = [];
        unset($_SESSION);

        $this->oturumBaslatildi = false;
        $this->geciciKlasorler = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->kurtar as $anahtar => $deger) {
            if (str_starts_with($anahtar, 'server:')) {
                $k = substr($anahtar, 7);
                if ($deger === null) {
                    unset($_SERVER[$k]);
                } else {
                    $_SERVER[$k] = $deger;
                }
            }
        }
        $_COOKIE = $this->kurtar['cookie'];
        if ($this->kurtar['session'] === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->kurtar['session'];
        }
        if ($this->kurtar['env'] === null) {
            unset($_ENV['MEDIA_ROOT']);
        } else {
            $_ENV['MEDIA_ROOT'] = $this->kurtar['env'];
        }

        $this->sil($this->kok);
    }

    /* ------------------------------------------------------------------ *
     * 1) Sözdizimi — auth ÖNCESİ 400
     * ------------------------------------------------------------------ */

    /**
     * @dataProvider gecersizKullanicilar
     */
    public function testGecersizFileKey400Verir(string $key): void
    {
        $hata = $this->authorizeHata($key);
        self::assertSame(400, $hata->status);
        self::assertSame('INVALID_FILE_KEY', $hata->errorCode);
    }

    /** @return array<string, array{0: string}> */
    public static function gecersizKullanicilar(): array
    {
        return [
            'bos' => [''],
            'ust dizin' => ['..%2F..%2Fetc%2Fpasswd'],
            'cift decode ile ..' => ['%2e%2e'],
            'bolum isareti' => ['01ABC/extra'],
            'ters bolu' => ['01ABC\\extra'],
            'boslukla baslar' => [' 01ABC'],
            'nul' => ["01ABC\0"],
            'uzun' => ['a' . str_repeat('b', 300)],
        ];
    }

    public function testGecersizAnahtarAuthCagirmaz(): void
    {
        // Sözdizimi reddi auth ÖNCESİ: oturum başlatıcı hiç çalışmamalı.
        try {
            $this->guard()->authorize('..%2F..%2Fetc%2Fpasswd');
            self::fail('MediaHttpException bekleniyordu');
        } catch (MediaHttpException $hata) {
            self::assertSame(400, $hata->status);
        }
        self::assertFalse($this->oturumBaslatildi, 'oturum auth oncesinde BASLATILMAMALI');
    }

    /* ------------------------------------------------------------------ *
     * 2) Kimlik — 401
     * ------------------------------------------------------------------ */

    public function testKimliksizIstek401Verir(): void
    {
        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(401, $hata->status);
        self::assertSame('UNAUTHORIZED', $hata->errorCode);
        self::assertFalse($this->oturumBaslatildi, 'cerez yokken oturum BASLATILMAZ');
    }

    public function testDesteklenmeyenSema401VeOturumYok(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic YWJjOmRlZg==';
        $_COOKIE['COREMUSIC_SESS'] = 'oturum-var-ama-sema-bilinmiyor';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(401, $hata->status);
        // Bearer disindaki sema dususle degistirilemez → oturuma GEÇİLMEZ.
        self::assertFalse($this->oturumBaslatildi);
    }

    public function testJwtServisiOlmadanBearer401(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . str_repeat('x', 40);

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(401, $hata->status);
    }

    public function testGecersizBearerToken401(): void
    {
        $jwt = $this->jwtServisi();
        $baskaCift = $this->jwtServisi(); // farkli RSA anahtar cifti → imza tutmaz
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $baskaCift->issue(self::KULANICI_HEX)['token'];

        $hata = $this->authorizeHata(self::DOSYA_KEY, $jwt);
        self::assertSame(401, $hata->status);
    }

    public function testGecerliBearerIleBaglamDoner(): void
    {
        $jwt = $this->jwtServisi();
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $jwt->issue(self::GECERLI_KULANICI)['token'];

        $ctx = $this->guard($jwt)->authorize(self::DOSYA_KEY);
        self::assertSame(self::KULANICI_HEX, $ctx['userId']);
        self::assertSame(realpath($this->kok . '/media/_inbox/2026-10-30/audio.flac'), $ctx['absolutePath']);
        self::assertFalse($this->oturumBaslatildi, 'Bearer istegi oturumu ASLA baslatmaz (ADR-010 §1)');
    }

    public function testOturumCereziIleBaglamDoner(): void
    {
        $_COOKIE['COREMUSIC_SESS'] = 'gercek-gibi-bir-sess-degeri';
        $ctx = $this->guard()->authorize(self::DOSYA_KEY);
        self::assertSame(self::KULANICI_HEX, $ctx['userId']);
        self::assertTrue($this->oturumBaslatildi);
    }

    public function testOturumdaUserIdAdayiKullanilir(): void
    {
        $_COOKIE['COREMUSIC_SESS'] = 'sess';
        $starter = function (): void {
            $this->oturumBaslatildi = true;
            $_SESSION = ['user' => ['id' => self::GECERLI_KULANICI]];
        };
        $guard = new MediaAccessGuard($this->repo, null, $starter);
        $ctx = $guard->authorize(self::DOSYA_KEY);
        self::assertSame(self::KULANICI_HEX, $ctx['userId']);
    }

    public function testOturumdakiGecersizKimlik401(): void
    {
        $_COOKIE['COREMUSIC_SESS'] = 'sess';
        $starter = function (): void {
            $this->oturumBaslatildi = true;
            $_SESSION = ['MM_UserID' => 'bozuk-kimlik'];
        };
        $guard = new MediaAccessGuard($this->repo, null, $starter);

        $hata = null;
        try {
            $guard->authorize(self::DOSYA_KEY);
        } catch (MediaHttpException $e) {
            $hata = $e;
        }
        self::assertNotNull($hata);
        self::assertSame(401, $hata->status);
    }

    /* ------------------------------------------------------------------ *
     * 3-4) İzin + varlık — 403 / 404
     * ------------------------------------------------------------------ */

    public function testIzinYok403(): void
    {
        $this->repo->izinVar = false;
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(403, $hata->status);
        self::assertSame('FORBIDDEN', $hata->errorCode);
    }

    public function testIzinSorgusuHataVersede403(): void
    {
        $this->repo->grantHatasi = new \RuntimeException('db baglantisi koptu');
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(403, $hata->status, 'DB hatasi fail-closed: doğrulanamayan izin = reddedilen');
    }

    public function testVarlikYok404(): void
    {
        $this->repo->asset = null;
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(404, $hata->status);
        self::assertSame('NOT_FOUND', $hata->errorCode);
    }

    public function testVarlikSorgusuHataVersede403(): void
    {
        $this->repo->assetHatasi = new \RuntimeException('katalog okunamadi');
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(403, $hata->status);
    }

    /* ------------------------------------------------------------------ *
     * 5) Yol — 403 PATH_DENIED
     * ------------------------------------------------------------------ */

    public function testMedyaKokuYoksa403(): void
    {
        unset($_ENV['MEDIA_ROOT']);
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(403, $hata->status);
        self::assertSame('PATH_DENIED', $hata->errorCode);
    }

    public function testKokDisiYol403(): void
    {
        $this->repo->asset['dosya_yolu'] = '../../windows/system.ini';
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(403, $hata->status);
        self::assertSame('PATH_DENIED', $hata->errorCode);
    }

    public function testDiskteOlmayanDosya403(): void
    {
        $this->repo->asset['dosya_yolu'] = 'media/_inbox/2026-10-30/olmayan.flac';
        $_COOKIE['COREMUSIC_SESS'] = 'sess';

        $hata = $this->authorizeHata(self::DOSYA_KEY);
        self::assertSame(403, $hata->status);
        self::assertSame('PATH_DENIED', $hata->errorCode);
    }

    /* ------------------------------------------------------------------ *
     * ETag + audit
     * ------------------------------------------------------------------ */

    public function testEtagKutuIcindeSha256UzerineKurulur(): void
    {
        $_COOKIE['COREMUSIC_SESS'] = 'sess';
        $ctx = $this->guard()->authorize(self::DOSYA_KEY);

        self::assertMatchesRegularExpression('/^"[0-9a-f]{64}"$/', $ctx['etag']);
        self::assertSame(20, $ctx['size']);
        self::assertGreaterThan(0, $ctx['mtime']);
    }

    public function testRecordAccessKaydiRepositoriyeGider(): void
    {
        $_COOKIE['COREMUSIC_SESS'] = 'sess';
        $ctx = $this->guard()->authorize(self::DOSYA_KEY);
        $this->guard()->recordAccess($ctx, 206);

        self::assertCount(1, $this->repo->auditKayitlari);
        $kayit = $this->repo->auditKayitlari[0];
        self::assertSame(self::KULANICI_HEX, $kayit['userId']);
        self::assertSame(self::DOSYA_KEY, $kayit['fileKey']);
        self::assertSame(206, $kayit['status']);
        self::assertSame(20, $kayit['size']);
    }

    public function testAuditHatasiYukariTasinir(): void
    {
        $this->repo->auditHatasi = new \RuntimeException('audit insert basarisiz');
        $_COOKIE['COREMUSIC_SESS'] = 'sess';
        $ctx = $this->guard()->authorize(self::DOSYA_KEY);

        $this->expectException(\RuntimeException::class);
        $this->guard()->recordAccess($ctx, 200); // fail-closed: kayit yoksa servis YOK
    }

    /* ------------------------------------------------------------------ *
     * Kurulum yardimcilari
     * ------------------------------------------------------------------ */

    private function guard(?JwtService $jwt = null): MediaAccessGuard
    {
        $starter = function (): void {
            $this->oturumBaslatildi = true;
            $_SESSION = ['MM_UserID' => self::GECERLI_KULANICI];
        };

        return new MediaAccessGuard($this->repo, $jwt, $starter);
    }

    private function authorizeHata(string $key, ?JwtService $jwt = null): MediaHttpException
    {
        try {
            $this->guard($jwt)->authorize($key);
        } catch (MediaHttpException $hata) {
            return $hata;
        }

        self::fail('MediaHttpException bekleniyordu (basari asla reddin yerine gecmez)');
    }

    /** @return array<string, mixed> */
    private function jwtServisi(): JwtService
    {
        $dizin = sys_get_temp_dir() . '/media_guard_jwt_' . bin2hex(random_bytes(6));
        if (!mkdir($dizin, 0o777, true) && !is_dir($dizin)) {
            self::fail('jwt dizini olusturulamadi');
        }
        $this->geciciKlasorler[] = $dizin;

        // Windows PHP 8.5: OPENSSL_CONF yokken `openssl_pkey_new` config dosyası
        // arar ve başarısız olur (ölçüm: error:80000003). Geçici config dosyası
        // üretilip `config` seçeneğiyle verilir — üretilen anahtar SADECE testtir.
        $cnf = $dizin . '/openssl.cnf';
        file_put_contents(
            $cnf,
            "[req]\ndefault_bits = 2048\nprompt = no\ndistinguished_name = dn\n[dn]\nCN = media-test\n"
        );
        $cift = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'config' => $cnf,
        ]);
        if ($cift === false) {
            self::fail('RSA anahtar cifti uretilemedi (ext openssl): ' . (string) openssl_error_string());
        }
        $ozel = '';
        $genel = '';
        // Export de config dosyası ister (ölçüm: configsiz boş PEM → 1. hatanın kökü).
        if (openssl_pkey_export($cift, $ozel, null, ['config' => $cnf]) === false || $ozel === '') {
            self::fail('RSA ozel anahtari export edilemedi: ' . (string) openssl_error_string());
        }
        $detay = openssl_pkey_get_details($cift);
        $genel = (string) ($detay['key'] ?? '');
        file_put_contents($dizin . '/private.pem', $ozel);
        file_put_contents($dizin . '/public.pem', $genel);

        return new JwtService($dizin . '/private.pem', $dizin . '/public.pem');
    }

    private function sil(string $dizin): void
    {
        foreach ($this->geciciKlasorler as $gecici) {
            if (is_dir($gecici)) {
                $this->silDizin($gecici);
            }
        }
        $this->geciciKlasorler = [];
        if (is_dir($dizin)) {
            $this->silDizin($dizin);
        }
    }

    private function silDizin(string $dizin): void
    {
        $girisler = scandir($dizin) ?: [];
        foreach ($girisler as $giris) {
            if ($giris === '.' || $giris === '..') {
                continue;
            }
            $yol = $dizin . '/' . $giris;
            if (is_dir($yol) && !is_link($yol)) {
                $this->silDizin($yol);
            } else {
                @unlink($yol);
            }
        }
        @rmdir($dizin);
    }
}