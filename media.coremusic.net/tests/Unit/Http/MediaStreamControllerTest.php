<?php

declare(strict_types=1);

namespace Media\Test\Unit\Http;

use Media\Http\MediaAccessGuard;
use Media\Http\MediaStreamController;
use Media\MediaHttpException;
use Media\Test\Fake\InMemoryMediaAccessRepository;
use PHPUnit\Framework\TestCase;

/**
 * MediaStreamControllerTest — 200 · 206 · 304 · 416 + audit önceliği.
 *
 * Durum matrisi gerçek byte üzerinde ölçülür (temp fixture dosyası);
 * başlıklar CLI SAPI'de `header()` olmadığından `lastHeaders()`/`lastStatus()`
 * üzerinden okunur. Gövde `ob_start()` ile yakalanır ve dosya içeriğiyle
 * birebir karşılaştırılır (sahte gövde YOK).
 */
final class MediaStreamControllerTest extends TestCase
{
    private const KULANICI_HEX = '01930f2c11117222a333444455556666';
    private const DOSYA_KEY = '01JBTESTKEY000000000000000';

    /** 20 bayt deterministik içerik — parçalı okumada birebir karşılaştırma. */
    private const ICELIK = '0123456789ABCDEFGHIJ';

    private string $kok;

    private InMemoryMediaAccessRepository $repo;

    private MediaStreamController $controller;

    /** @var array<string, mixed> kurtarilacak istek durumu */
    private array $kurtar = [];

    protected function setUp(): void
    {
        $this->kok = sys_get_temp_dir() . '/media_stream_' . bin2hex(random_bytes(6));
        $dizin = $this->kok . '/media/_inbox/2026-10-30';
        if (!mkdir($dizin, 0o777, true) && !is_dir($dizin)) {
            self::fail('fixture dizini olusturulamadi');
        }
        file_put_contents($dizin . '/audio.mp3', self::ICELIK);

        $_ENV['MEDIA_ROOT'] = $this->kok;

        $this->repo = new InMemoryMediaAccessRepository();
        $this->repo->asset = [
            'id' => self::DOSYA_KEY,
            'dosya_yolu' => 'media/_inbox/2026-10-30/audio.mp3',
            'durum' => 'aktif',
            'dosya' => 'audio.mp3',
            'format' => 'mp3',
            'boyut_bayt' => 20,
            'sha256' => str_repeat('b', 64),
        ];

        foreach (['REQUEST_METHOD', 'HTTP_RANGE', 'HTTP_IF_NONE_MATCH', 'HTTP_AUTHORIZATION'] as $anahtar) {
            $this->kurtar[$anahtar] = $_SERVER[$anahtar] ?? null;
            unset($_SERVER[$anahtar]);
        }
        $this->kurtar['cookie'] = $_COOKIE;
        $this->kurtar['session'] = $_SESSION ?? null;
        $this->kurtar['env'] = $_ENV['MEDIA_ROOT'];
        $_COOKIE = ['COREMUSIC_SESS' => 'fixture-oturum']; // oturum cerezi ZORUNLU (guard 401)
        unset($_SESSION);
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->controller = $this->controllerKur();
    }

    protected function tearDown(): void
    {
        foreach (['REQUEST_METHOD', 'HTTP_RANGE', 'HTTP_IF_NONE_MATCH', 'HTTP_AUTHORIZATION'] as $anahtar) {
            if ($this->kurtar[$anahtar] === null) {
                unset($_SERVER[$anahtar]);
            } else {
                $_SERVER[$anahtar] = $this->kurtar[$anahtar];
            }
        }
        $_COOKIE = $this->kurtar['cookie'];
        if ($this->kurtar['session'] === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->kurtar['session'];
        }
        $_ENV['MEDIA_ROOT'] = $this->kurtar['env'];

        $this->sil($this->kok);
    }

    /* ------------------------------------------------------------------ *
     * 200 — tam gövde
     * ------------------------------------------------------------------ */

    public function testTamIstek200VeGovdeBirebir(): void
    {
        $govde = $this->calistir();

        self::assertSame(200, $this->controller->lastStatus());
        self::assertSame(self::ICELIK, $govde);

        $baslik = $this->controller->lastHeaders();
        self::assertSame('20', $baslik['Content-Length']);
        self::assertSame('audio/mpeg', $baslik['Content-Type']);
        self::assertSame('bytes', $baslik['Accept-Ranges']);
        self::assertArrayNotHasKey('Content-Range', $baslik);
        self::assertMatchesRegularExpression('/^"[0-9a-f]{64}"$/', (string) $baslik['ETag']);
        self::assertSame('private, max-age=86400', $baslik['Cache-Control']);
        self::assertSame('nosniff', $baslik['X-Content-Type-Options']);

        // Başarılı erişim ÖNCE kayda girer: 1 audit insert, status 200.
        self::assertCount(1, $this->repo->auditKayitlari);
        self::assertSame(200, $this->repo->auditKayitlari[0]['status']);
        self::assertSame(self::KULANICI_HEX, $this->repo->auditKayitlari[0]['userId']);
    }

    /* ------------------------------------------------------------------ *
     * 304 — If-None-Match
     * ------------------------------------------------------------------ */

    public function testIfNoneMatchEslesirse304VeGovdeYok(): void
    {
        $ctx = $this->guard()->authorize(self::DOSYA_KEY);
        $_SERVER['HTTP_IF_NONE_MATCH'] = $ctx['etag'];

        $govde = $this->calistir();

        self::assertSame(304, $this->controller->lastStatus());
        self::assertSame('', $govde, '304 govde TASIYAMAZ');
        $baslik = $this->controller->lastHeaders();
        self::assertArrayNotHasKey('Content-Length', $baslik);
        self::assertArrayNotHasKey('Content-Type', $baslik);
        self::assertSame($ctx['etag'], $baslik['ETag']);
        // 304 de kayda girer (basarisiz degil).
        self::assertCount(1, $this->repo->auditKayitlari);
        self::assertSame(304, $this->repo->auditKayitlari[0]['status']);
    }

    public function testIfNoneMatchEslesmezse200(): void
    {
        $_SERVER['HTTP_IF_NONE_MATCH'] = '"baska-bir-etag"';
        $govde = $this->calistir();

        self::assertSame(200, $this->controller->lastStatus());
        self::assertSame(self::ICELIK, $govde);
    }

    public function testYildizVeWeakEtkiletilesi(): void
    {
        $_SERVER['HTTP_IF_NONE_MATCH'] = '*';
        $this->calistir();
        self::assertSame(304, $this->controller->lastStatus());

        $ctx = $this->guard()->authorize(self::DOSYA_KEY);
        $this->controller = $this->controllerKur(); // sayaci sifirla
        $_SERVER['HTTP_IF_NONE_MATCH'] = 'W/' . $ctx['etag'] . ', "gercek-degil"';
        $this->calistir();
        self::assertSame(304, $this->controller->lastStatus(), 'W/ oneki + virgul listesi eslesmeli');
    }

    /* ------------------------------------------------------------------ *
     * 206 — Range
     * ------------------------------------------------------------------ */

    public function testRangeIlkOnBayt206(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=0-9';
        $govde = $this->calistir();

        self::assertSame(206, $this->controller->lastStatus());
        self::assertSame('0123456789', $govde);
        $baslik = $this->controller->lastHeaders();
        self::assertSame('bytes 0-9/20', $baslik['Content-Range']);
        self::assertSame('10', $baslik['Content-Length']);
        self::assertCount(1, $this->repo->auditKayitlari);
        self::assertSame(206, $this->repo->auditKayitlari[0]['status']);
    }

    public function testRangeSonNBayt206(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=-5';
        $govde = $this->calistir();

        self::assertSame(206, $this->controller->lastStatus());
        self::assertSame('FGHIJ', $govde);
        self::assertSame('bytes 15-19/20', $this->controller->lastHeaders()['Content-Range']);
    }

    public function testRangeAcikUctan206(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=10-';
        $govde = $this->calistir();

        self::assertSame(206, $this->controller->lastStatus());
        self::assertSame('ABCDEFGHIJ', $govde);
        self::assertSame('bytes 10-19/20', $this->controller->lastHeaders()['Content-Range']);
    }

    /* ------------------------------------------------------------------ *
     * 416 — geçersiz Range (audit YOK)
     * ------------------------------------------------------------------ */

    public function testKapsamDisiRange416VeAuditYok(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=1000-2000';
        $govde = $this->calistir();

        self::assertSame(416, $this->controller->lastStatus());
        self::assertSame('', $govde, '416 govde TASIYAMAZ');
        self::assertSame('bytes */20', $this->controller->lastHeaders()['Content-Range']);
        self::assertSame([], $this->repo->auditKayitlari, '416 basarisizdir → media_audit e yazilmaz');
    }

    public function testTersAralik416(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=15-10';
        $this->calistir();
        self::assertSame(416, $this->controller->lastStatus());
    }

    /* ------------------------------------------------------------------ *
     * Bozuk / çoklu Range → 200'e düşer (sessizce tam gövde)
     * ------------------------------------------------------------------ */

    public function testBozukRange200Doner(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=abc';
        $govde = $this->calistir();

        self::assertSame(200, $this->controller->lastStatus());
        self::assertSame(self::ICELIK, $govde);
    }

    public function testCokluAralik200Doner(): void
    {
        $_SERVER['HTTP_RANGE'] = 'bytes=0-1,3-4';
        $govde = $this->calistir();

        self::assertSame(200, $this->controller->lastStatus(), 'coklu aralik desteklenmez → tam govde');
        self::assertSame(self::ICELIK, $govde);
    }

    /* ------------------------------------------------------------------ *
     * HEAD — başlık var, gövde yok
     * ------------------------------------------------------------------ */

    public function testHeadIsteGovdeYok(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $govde = $this->calistir();

        self::assertSame(200, $this->controller->lastStatus());
        self::assertSame('', $govde, 'HEAD govde TASIYAMAZ');
        self::assertSame('20', $this->controller->lastHeaders()['Content-Length']);
        // Audit yine girer (istek basarili okuma).
        self::assertCount(1, $this->repo->auditKayitlari);
    }

    /* ------------------------------------------------------------------ *
     * Fail-closed yolları
     * ------------------------------------------------------------------ */

    public function testKimliksizIstekGovdeBasilmas401(): void
    {
        $_COOKIE = []; // cerez YOK → 401 (basarisiz istekte govde basilmas)
        $hata = $this->authorizeHata();
        self::assertSame(401, $hata->status);
        self::assertFalse($this->controller->headersSent(), 'reddedilen istekte baslik bile gonderilmez');
        self::assertSame([], $this->repo->auditKayitlari);
    }

    public function testAuditHatasiGovdedenOnce500(): void
    {
        $this->repo->auditHatasi = new \RuntimeException('audit insert basarisiz');

        $govde = '';
        $hata = null;
        ob_start();
        try {
            $this->controller->handle(self::DOSYA_KEY);
        } catch (MediaHttpException $e) {
            $hata = $e;
        } finally {
            $govde = (string) ob_get_clean(); // tampon her yolda kapanir (risky yok)
        }

        self::assertNotNull($hata, 'audit hatasi MediaHttpException 500 uretmeli');
        self::assertSame(500, $hata->status);
        self::assertSame('INTERNAL_ERROR', $hata->errorCode);
        self::assertSame('', $govde, 'kayitlanmayan erişim ASLA servis edilmez');
        self::assertFalse($this->controller->headersSent(), 'basliklar audit SONRASINDA gonderilir');
    }

    public function testGecersizFileKey401Yerine400(): void
    {
        $hata = null;
        try {
            $this->controller->handle('..%2F..%2Fetc%2Fpasswd');
        } catch (MediaHttpException $e) {
            $hata = $e;
        }
        self::assertNotNull($hata);
        self::assertSame(400, $hata->status);
        self::assertFalse($this->controller->headersSent());
    }

    /* ------------------------------------------------------------------ *
     * Kurulum
     * ------------------------------------------------------------------ */

    private function controllerKur(): MediaStreamController
    {
        return new MediaStreamController($this->guard());
    }

    private function guard(): MediaAccessGuard
    {
        $starter = static function (): void {
            $_SESSION = ['MM_UserID' => self::KULANICI_HEX];
        };

        return new MediaAccessGuard($this->repo, null, $starter);
    }

    /**
     * Akışı çalıştırır, gövdeyi yakalar.
     *
     * `pump()` her chunk sonrası `ob_flush()` çağırır (üretim davranışı) — bu,
     * en içteki tamponu boşaltıp içeriği bir üst tampona taşır. Bu yüzden İKİ
     * katman açılır: iç (flush'ın hedefi) + dış (yakalama). İçerik hangisinde
     * kalırsa kalsın birleştirilir → gövde kaybı/uydurma YOK.
     */
    private function calistir(string $key = self::DOSYA_KEY): string
    {
        ob_start(); // dış yakalama
        ob_start(); // iç — pump ob_flush bunu bosaltir
        try {
            $this->controller->handle($key);
        } finally {
            $ic = (string) ob_get_clean();
            $dis = (string) ob_get_clean();
        }

        return $dis . $ic;
    }

    private function authorizeHata(): MediaHttpException
    {
        try {
            $this->controller->handle(self::DOSYA_KEY);
        } catch (MediaHttpException $hata) {
            return $hata;
        }

        self::fail('MediaHttpException bekleniyordu');
    }

    private function sil(string $dizin): void
    {
        if (!is_dir($dizin)) {
            return;
        }
        $girisler = scandir($dizin) ?: [];
        foreach ($girisler as $giris) {
            if ($giris === '.' || $giris === '..') {
                continue;
            }
            $yol = $dizin . '/' . $giris;
            if (is_dir($yol) && !is_link($yol)) {
                $this->sil($yol);
            } else {
                @unlink($yol);
            }
        }
        @rmdir($dizin);
    }
}