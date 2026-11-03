<?php

declare(strict_types=1);

namespace Media\Test\Unit\Http;

use Media\Http\MediaAccessGuard;
use Media\Http\MediaUploadController;
use Media\MediaHttpException;
use Media\Test\Fake\InMemoryMediaAccessRepository;
use Media\Ulid;
use PHPUnit\Framework\TestCase;

/**
 * MediaUploadTest — POST /upload kapı matrisi (Faz 2 uzantısı).
 *
 * Kapılar: 401 oturum yok · 403 allowlist dışı origin · 405 method (+ Allow) ·
 * 415 content-type · 415 uzantı · 413 boyut · 400 traversal · 503 MEDIA_ROOT yok ·
 * 400 boş gövde · 201 mutlu yol (file_key ULID, YOL SIZMAZ).
 *
 * Güvenlik: her test kendi temp fixture kökünü kurar/siler (`sys_get_temp_dir`);
 * gerçek medya ağacına (38 GB) asla dokunulmaz. Taşıma `copy` yapan enjekte
 * edilmiş mover ile simüle edilir (CLI'da `is_uploaded_file` hep false).
 */
final class MediaUploadTest extends TestCase
{
    private const KULANICI_HEX = '01930f2c11117222a333444455556666';

    /** Geçerli ses içeriği (küçük, tek dosya). */
    private const ICELIK = 'ID3fakeaudiocontent';

    private string $kok;

    /** MEDIA_ROOT disinda yukleme kaynagi (tmp_name). */
    private string $kaynak;

    private InMemoryMediaAccessRepository $repo;

    private MediaUploadController $controller;

    /** @var array<string, mixed> kurtarilacak istek durumu */
    private array $kurtar = [];

    protected function setUp(): void
    {
        $this->kok = sys_get_temp_dir() . '/media_upload_' . bin2hex(random_bytes(6));
        if (!mkdir($this->kok, 0o777, true) && !is_dir($this->kok)) {
            self::fail('fixture koku olusturulamadi');
        }

        $this->kaynak = sys_get_temp_dir() . '/media_upload_src_' . bin2hex(random_bytes(6)) . '.flac';
        file_put_contents($this->kaynak, self::ICELIK);

        $_ENV['MEDIA_ROOT'] = $this->kok;

        $this->repo = new InMemoryMediaAccessRepository();

        $anahtarlar = [
            'REQUEST_METHOD',
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'HTTP_ORIGIN',
            'HTTP_REFERER',
            'HTTP_AUTHORIZATION',
        ];
        foreach ($anahtarlar as $anahtar) {
            $this->kurtar[$anahtar] = $_SERVER[$anahtar] ?? null;
            unset($_SERVER[$anahtar]);
        }
        $this->kurtar['files'] = $_FILES;
        $this->kurtar['cookie'] = $_COOKIE;
        $this->kurtar['session'] = $_SESSION ?? null;
        $this->kurtar['env'] = $_ENV['MEDIA_ROOT'] ?? null;

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data; boundary=----cmupload';
        $_COOKIE = ['COREMUSIC_SESS' => 'fixture-oturum']; // oturum cerezi ZORUNLU (guard 401)
        unset($_SESSION);
        $this->dosyaAyarla('sarki.flac', strlen(self::ICELIK));

        $this->controller = $this->controllerKur();
    }

    protected function tearDown(): void
    {
        foreach ([
            'REQUEST_METHOD',
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'HTTP_ORIGIN',
            'HTTP_REFERER',
            'HTTP_AUTHORIZATION',
        ] as $anahtar) {
            if ($this->kurtar[$anahtar] === null) {
                unset($_SERVER[$anahtar]);
            } else {
                $_SERVER[$anahtar] = $this->kurtar[$anahtar];
            }
        }
        $_FILES = $this->kurtar['files'];
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
        @unlink($this->kaynak);
    }

    /* ------------------------------------------------------------------ *
     * 401 — oturum yok
     * ------------------------------------------------------------------ */

    public function testOturumYoksa401(): void
    {
        $_COOKIE = []; // cerez YOK
        unset($_SESSION);

        $hata = $this->hataCek();

        self::assertSame(401, $hata->status);
        self::assertSame('UNAUTHORIZED', $hata->errorCode);
    }

    /* ------------------------------------------------------------------ *
     * 403 — allowlist disi origin
     * ------------------------------------------------------------------ */

    public function testIzinVerilmeyenOrigin403(): void
    {
        $_SERVER['HTTP_ORIGIN'] = 'https://evil.example.com';

        $hata = $this->hataCek();

        self::assertSame(403, $hata->status);
        self::assertSame('FORBIDDEN', $hata->errorCode);
    }

    public function testRefererIzinVerilmeyenOrigin403(): void
    {
        // Origin yok AMA Referer var ve allowlist disi → 403 (ikinci kapida da)
        unset($_SERVER['HTTP_ORIGIN']);
        $_SERVER['HTTP_REFERER'] = 'https://evil.example.com/page';

        $hata = $this->hataCek();

        self::assertSame(403, $hata->status);
    }

    /* ------------------------------------------------------------------ *
     * 405 — method (Allow: POST)
     * ------------------------------------------------------------------ */

    public function testPostDisindaYontem405VeAllow(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $hata = $this->hataCek();

        self::assertSame(405, $hata->status);
        self::assertSame('METHOD_NOT_ALLOWED', $hata->errorCode);
        self::assertSame('POST', $this->controller->lastHeaders()['Allow'] ?? null);
    }

    /* ------------------------------------------------------------------ *
     * 415 — content-type multipart degil
     * ------------------------------------------------------------------ */

    public function testMultipartDegilse415(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $hata = $this->hataCek();

        self::assertSame(415, $hata->status);
        self::assertSame('UNSUPPORTED_MEDIA_TYPE', $hata->errorCode);
    }

    /* ------------------------------------------------------------------ *
     * 415 — uzantı allowlist disi
     * ------------------------------------------------------------------ */

    public function testIzinDisiUzanti415(): void
    {
        $this->dosyaAyarla('sarki.exe', strlen(self::ICELIK));

        $hata = $this->hataCek();

        self::assertSame(415, $hata->status);
        self::assertSame('UNSUPPORTED_EXTENSION', $hata->errorCode);
    }

    /* ------------------------------------------------------------------ *
     * 413 — boyut limiti
     * ------------------------------------------------------------------ */

    public function testBoyutLimitiAsarsa413(): void
    {
        // Content-Length erken kapisi: gercek dosya olusturmadan ust sinir uzerinden
        $_SERVER['CONTENT_LENGTH'] = (string) (MediaUploadController::MAX_UPLOAD_BYTES + 1);
        $this->dosyaAyarla('sarki.flac', MediaUploadController::MAX_UPLOAD_BYTES + 1);

        $hata = $this->hataCek();

        self::assertSame(413, $hata->status);
        self::assertSame('SIZE_LIMIT_EXCEEDED', $hata->errorCode);
    }

    /* ------------------------------------------------------------------ *
     * 400 — traversal (asla 2xx)
     * ------------------------------------------------------------------ */

    public function testTraversalDosyaAdi400(): void
    {
        $this->dosyaAyarla('../../evil.flac', strlen(self::ICELIK));

        $hata = $this->hataCek();

        self::assertSame(400, $hata->status, 'traversal ASLA 2xx');
        self::assertSame('PATH_TRAVERSAL', $hata->errorCode);
        // staging'e HIC dosya inmemis olmali
        self::assertSame([], $this->stagedDosyalar(), 'traversal redinde staging bos kalmali');
    }

    /* ------------------------------------------------------------------ *
     * 503 — MEDIA_ROOT yok (fail-closed, temp fallback YOK)
     * ------------------------------------------------------------------ */

    public function testMediaRootYoksa503(): void
    {
        unset($_ENV['MEDIA_ROOT']);
        putenv('MEDIA_ROOT'); // getenv kolu da temiz (fail-closed)

        $hata = $this->hataCek();

        self::assertSame(503, $hata->status);
        self::assertSame('MEDIA_NOT_CONFIGURED', $hata->errorCode);
        self::assertSame([], $this->stagedDosyalar(), '503 sirasinda staging yazimi YOK');
    }

    /* ------------------------------------------------------------------ *
     * 400 — bos yukleme
     * ------------------------------------------------------------------ */

    public function testBosYukleme400(): void
    {
        $this->dosyaAyarla('sarki.flac', 0);

        $hata = $this->hataCek();

        self::assertSame(400, $hata->status);
        self::assertSame('EMPTY_UPLOAD', $hata->errorCode);
    }

    /* ------------------------------------------------------------------ *
     * 201 — mutlu yol (file_key + guvenli metaveri, YOL SIZMAZ)
     * ------------------------------------------------------------------ */

    public function testMutluYol201VeYolSizmaz(): void
    {
        // coremusic origin allowlist'ten gecer (pozitif gecis de test edilir)
        $_SERVER['HTTP_ORIGIN'] = 'https://music.coremusic.net';

        [$durum, $govde] = $this->basari();

        self::assertSame(201, $durum);
        self::assertSame(201, $this->controller->lastStatus());

        $veri = json_decode($govde, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($veri);

        // 201 govdesi YALNIZ istenen alanlar
        self::assertSame(['file_key', 'ext', 'size_bytes', 'mime'], array_keys($veri));

        // file_key = ULID (26 Crockford), `/` icermemeli (path leak karsiti)
        self::assertTrue(Ulid::isValid($veri['file_key'] ?? null), 'file_key ULID olmali');
        self::assertStringNotContainsString('/', (string) ($veri['file_key'] ?? ''));

        self::assertSame('flac', $veri['ext']);
        self::assertSame(strlen(self::ICELIK), $veri['size_bytes']);
        self::assertSame('audio/flac', $veri['mime']);

        // YOL SIZMAZ: mutlak MEDIA_ROOT degeri govdede ASLA gecmemeli
        self::assertStringNotContainsString($this->kok, $govde, 'MEDIA_ROOT govdede ASLA olmamali');
        self::assertStringNotContainsString('_inbox', $govde, 'goreli staging yolu govdede ASLA olmamali');
        self::assertStringNotContainsString('media_upload_', $govde, 'temp kok govdede ASLA olmamali');

        // staging dosyasi gercekten olustu (ULID adli, dogru uzantili)
        $staged = $this->stagedDosyalar();
        self::assertCount(1, $staged);
        self::assertMatchesRegularExpression(
            '~/' . preg_quote(date('Y-m-d'), '~') . '/[0-9A-HJKMNP-TV-Z]{26}\.flac$~',
            str_replace('\\', '/', (string) $staged[0])
        );
        self::assertSame(self::ICELIK, (string) file_get_contents((string) $staged[0]));
    }

    /* ------------------------------------------------------------------ *
     * Kurulum yardimcilari
     * ------------------------------------------------------------------ */

    private function dosyaAyarla(string $ad, int $boyut): void
    {
        $_FILES = [
            'file' => [
                'name' => $ad,
                'type' => 'application/octet-stream',
                'tmp_name' => $this->kaynak,
                'error' => UPLOAD_ERR_OK,
                'size' => $boyut,
            ],
        ];
    }

    private function controllerKur(): MediaUploadController
    {
        $starter = static function (): void {
            $_SESSION = ['MM_UserID' => self::KULANICI_HEX];
        };
        // CLI'da is_uploaded_file/move_uploaded_file calismaz → test mover'i copy().
        $mover = static function (string $kaynak, string $hedef): bool {
            return @copy($kaynak, $hedef);
        };

        return new MediaUploadController(
            new MediaAccessGuard($this->repo, null, $starter),
            $mover
        );
    }

    /** @return list<string> MEDIA_ROOT/_inbox altindaki staged dosyalar */
    private function stagedDosyalar(): array
    {
        $dizin = $this->kok . '/_inbox';
        if (!is_dir($dizin)) {
            return [];
        }
        $sonuc = [];
        foreach ((array) glob($dizin . '/*/*') as $yol) {
            if (is_string($yol) && is_file($yol)) {
                $sonuc[] = $yol;
            }
        }

        return $sonuc;
    }

    /** Kapı çalıştırılır; MediaHttpException YOKSA test başarısız. */
    private function hataCek(): MediaHttpException
    {
        try {
            $this->controller->handle();
        } catch (MediaHttpException $hata) {
            return $hata;
        }

        self::fail('MediaHttpException bekleniyordu');
    }

    /**
     * Mutlu yol: durumu + govdeyi yakalar (govde basildiktan sonra).
     *
     * @return array{0: int, 1: string}
     */
    private function basari(): array
    {
        ob_start();
        $durum = 0;
        try {
            $durum = $this->controller->handle();
            $govde = (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return [$durum, $govde];
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
