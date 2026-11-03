<?php

declare(strict_types=1);

namespace Media\Test\Unit;

use Media\PathGuard;
use PHPUnit\Framework\TestCase;

/**
 * PathTraversalTest — `PathGuard` kaçış/symlink/kök denetimi.
 *
 * Desen kaynağı: `bin/ingest.php:95-128` (`yoluNormallestir` + `kokIcindeMi`).
 * Tüm fixture'lar temp dizindedir; gerçek medya ağacına (38 GB) dokunulmaz.
 */
final class PathTraversalTest extends TestCase
{
    private string $kok;

    /** Kök DIŞINDA gerçek dosya içeren kardeş dizin (kaçış hedefi kanıtı). */
    private string $disariKok;

    /** @var array<int, string> temizlenecek dizinler */
    private array $gecici = [];

    protected function setUp(): void
    {
        $this->kok = sys_get_temp_dir() . '/media_pathguard_' . bin2hex(random_bytes(6));
        $this->olustur($this->kok . '/media/_inbox/2026-10-30');
        $this->gecici[] = $this->kok;
        // `disari` KÖK DIŞINDA ayrı temp dizin olmalı — kök içine konsaydı
        // resolve() meşru şekilde çözerdi (yanlış fixture, güvenlik testi anlamsızlaşırdı).
        $this->disariKok = sys_get_temp_dir() . '/media_pathguard_disari_' . bin2hex(random_bytes(6));
        $this->olustur($this->disariKok);
        $this->gecici[] = $this->disariKok;
        file_put_contents($this->disariKok . '/sarki.mp3', 'DISARIDAKI');
        file_put_contents($this->kok . '/media/_inbox/2026-10-30/audio.flac', 'ICERIDEKI-BYTE');
    }

    protected function tearDown(): void
    {
        foreach ($this->gecici as $dizin) {
            $this->sil($dizin);
        }
        $this->gecici = [];
    }

    /* ------------------------------------------------------------------ *
     * isSafeRelative — dosya sistemi ERİŞİMİ YOK (yalnız sözdizimi)
     * ------------------------------------------------------------------ */

    /**
     * @dataProvider guvenliYollar
     */
    public function testGuvenliGoreliYollarKabulEdilir(string $yol): void
    {
        self::assertTrue(PathGuard::isSafeRelative($yol), "kabul edilmeli: {$yol}");
    }

    /** @return array<string, array{0: string}> */
    public static function guvenliYollar(): array
    {
        return [
            'basit dosya' => ['audio.flac'],
            'alt klasor' => ['media/_inbox/2026-10-30/audio.flac'],
            'nokta ile baslamaz' => ['media/.gitignore-tarzi-ama-bu-bir-dosya'],
            'uzantili json' => ['catalog/catalog.jsonl'],
            'windows ayiraci' => ['media\\_inbox\\audio.mp3'],
            'sonda bolu (sozdizimsel guvenli; dosya-degil ayri kontrol)' => ['media/'],
        ];
    }

    /**
     * @dataProvider guvensizYollar
     */
    public function testGuvensizGoreliYollarReddedilir(string $yol): void
    {
        self::assertFalse(PathGuard::isSafeRelative($yol), "reddedilmeli: {$yol}");
    }

    /** @return array<string, array{0: string}> */
    public static function guvensizYollar(): array
    {
        return [
            'bos' => [''],
            'ust dizin' => ['../etc/passwd'],
            'gizli ust dizin' => ['media/../../secret'],
            'tekil nokta degil cift' => ['a/./b/../c'],
            'mutlak posix' => ['/etc/passwd'],
            'mutlak windows' => ['C:\\Windows\\system32'],
            'ters bolu mutlak' => ['\\\\sunucu\\paylasim'],
            'NUL bayti' => ["audio.flac\0.jpg"],
            'yalniz bosluk' => ['   '],
            'yalniz sekme' => ["\t"],
        ];
    }

    /* ------------------------------------------------------------------ *
     * resolve — gerçek dosya sistemi (temp fixture)
     * ------------------------------------------------------------------ */

    public function testKokIcindekiDosyaCozulur(): void
    {
        $sonuc = PathGuard::resolve($this->kok, 'media/_inbox/2026-10-30/audio.flac');
        self::assertNotNull($sonuc);
        self::assertSame(14, strlen((string) file_get_contents($sonuc)));
    }

    public function testKokDisinaCikisReddedilir(): void
    {
        // Kaçış hedefi GERÇEK ve kök DIŞINDA (yoksa test anlamsız).
        self::assertFileExists($this->disariKok . '/sarki.mp3');
        $kardes = '..' . '/' . basename($this->disariKok) . '/sarki.mp3';

        // `..` ile kök dışına çıkan her yol null (asla çözülmez).
        self::assertNull(PathGuard::resolve($this->kok, $kardes));
        self::assertNull(PathGuard::resolve($this->kok, '../disari/sarki.mp3'));
        self::assertNull(PathGuard::resolve($this->kok, 'media/../../disari/sarki.mp3'));
        // Kök içinde `disari/` diye bir dizin yok → dosya bulunamaz → null.
        self::assertNull(PathGuard::resolve($this->kok, 'disari/sarki.mp3'));
    }

    public function testVarOlmayanYolNullDoner(): void
    {
        self::assertNull(PathGuard::resolve($this->kok, 'media/yok/hiyle.mp3'));
    }

    public function testDizinNullDoner(): void
    {
        self::assertNull(PathGuard::resolve($this->kok, 'media/_inbox'));
    }

    public function testKokNullVeyaYoksaNullDoner(): void
    {
        self::assertNull(PathGuard::resolve(null, 'media/x.mp3'));
        self::assertNull(PathGuard::resolve('', 'media/x.mp3'));
        self::assertNull(PathGuard::resolve($this->kok . '/olmayan-kok', 'media/x.mp3'));
    }

    public function testSymlinkKokDisinaGotururseReddedilir(): void
    {
        // Kök DIŞINDA ayrı bir dizin (kok'un altinda degil) → symlink oraya gider.
        $gercekDisari = sys_get_temp_dir() . '/media_pathguard_disari_' . bin2hex(random_bytes(6));
        $this->olustur($gercekDisari);
        $this->gecici[] = $gercekDisari;
        file_put_contents($gercekDisari . '/sarki.mp3', 'DISARIDAKI');

        $bag = $this->kok . '/media/kacis-baglantisi';
        if (@symlink($gercekDisari, $bag) === false) {
            self::markTestSkipped('Bu ortamda symlink olusturulamiyor (Windows izin/Developer Mode) — GERCEK OLCUM YOK.');
        }

        // 1) symlink'in kendisi (son bilesen) → is_link reddi (realpath ONCESI).
        self::assertNull(PathGuard::resolve($this->kok, 'media/kacis-baglantisi'));
        // 2) symlink ICI dosya → dirname realpath'i koku tasir → kok-onek reddi.
        self::assertNull(PathGuard::resolve($this->kok, 'media/kacis-baglantisi/sarki.mp3'));
        // Kanit: hedef GERTEKTEN kok disinda ve mevcut (yoksa test anlamsiz).
        self::assertFileExists($gercekDisari . '/sarki.mp3');
    }

    /* ------------------------------------------------------------------ *
     * Yardimcilar
     * ------------------------------------------------------------------ */

    private function olustur(string $dizin): void
    {
        if (!is_dir($dizin) && !mkdir($dizin, 0o777, true) && !is_dir($dizin)) {
            self::fail("temp dizin olusturulamadi: {$dizin}");
        }
    }

    private function sil(string $dizin): void
    {
        if (is_link($dizin) || is_file($dizin)) {
            @unlink($dizin);

            return;
        }
        if (!is_dir($dizin)) {
            return;
        }
        $girisler = scandir($dizin) ?: [];
        foreach ($girisler as $giris) {
            if ($giris === '.' || $giris === '..') {
                continue;
            }
            $this->sil($dizin . '/' . $giris);
        }
        @rmdir($dizin);
    }
}