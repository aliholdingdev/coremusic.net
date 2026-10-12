#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * scan.php — media/coremusic.net tarama + katalog üretimi (ADR-092, F2.2).
 *
 * Medya agacini tarar, yan-JSON'lari dogrular (Validator), her dosyadan
 * katalog kaydi uretir:
 *   1) catalog/catalog.jsonl  (yeniden uretilebilir SSOT indeksi)
 *   2) MySQL media_catalog    (opsiyonel; yoksa tek uyari + JSONL ile devam)
 *
 * Kullanim: php bin/scan.php [--rebuild] [--dry-run] [--deep] [-v]
 *   --dry-run  JSONL'e ve MySQL'e yazar; yalnizca yazilacaklari stdout basar
 *   --rebuild  mevcut catalog.jsonl'i siler + MySQL varlik tablolarini bosaltir
 *   --deep     medya dosyasinin sha256'sini yeniden hesaplar ve yan-JSON'daki
 *              teknik.sha256 ile karsilastirir (fark → HASH-FARK, HATA)
 *   -v         ayrintili cikti
 *
 * Mod (ADR-092 §6.1 madde 5 — sha256 yalniz GIRISTE uretilir):
 *   hafif (varsayilan)  yeniden hash YOK — satir degeri JSON'dan okunur;
 *                       JSON'da yoksa sha256: null + HASH OLMAYAN sayaci
 *   --deep              yeniden hash + HASH-FARK karsilastirmasi
 *
 * Cikis: 0 = temiz, 1 = hata bulundu, 2 = kullanim/kok hatasi.
 *
 * UYARI: PHP calisma zamani bu ortamda YOKTUR — php -l + ornek veri ile
 * 8.4 ortaminda dogrulanmalidir (VERIFICATION REQUIRED).
 */

spl_autoload_register(static function (string $sinif): void {
    $onek = 'Media\\';
    if (!str_starts_with($sinif, $onek)) {
        return;
    }
    $dosya = __DIR__ . '/../src/Media/' . str_replace('\\', '/', substr($sinif, strlen($onek))) . '.php';
    if (is_file($dosya)) {
        require_once $dosya;
    }
});

use Media\CatalogWriter;
use Media\Ulid;
use Media\Validator;

/* ------------------------------------------------------------------ */
/* Kok + argumanlar                                                    */
/* ------------------------------------------------------------------ */
$kok = dirname(__DIR__);
if (realpath($kok) === false || !is_file($kok . '/config/media.schema.json')) {
    fwrite(STDERR, "HATA | kok | proje kokunde config/media.schema.json yok: {$kok}\n");
    exit(2);
}
/** Varsayilan: hafif mod — sha256 yeniden hesaplanmaz (ADR-092 §6.1 madde 5). */
$derin = false;
$rebuild = false;
$dryRun = false;
$verbose = false;
foreach (array_slice($argv, 1) as $arg) {
    match ($arg) {
        '--rebuild' => $rebuild = true,
        '--dry-run' => $dryRun = true,
        '--deep' => $derin = true,
        '-v', '--verbose' => $verbose = true,
        '-h', '--help' => (function (): void {
            fwrite(STDOUT, "Kullanim: php bin/scan.php [--rebuild] [--dry-run] [--deep] [-v]\n");
            fwrite(STDOUT, "  --deep  medya dosyasinin sha256'sini yeniden hesaplar ve ayni dizindeki\n");
            fwrite(STDOUT, "          yan-JSON'daki teknik.sha256 ile karsilastirir (fark → HASH-FARK, HATA).\n");
            fwrite(STDOUT, "          Varsayilan (hafif) modda yeniden hash YOKTUR (ADR-092 §6.1 madde 5).\n");
            exit(0);
        })(),
        default => (function (string $a): void {
            fwrite(STDERR, "HATA | arguman | bilinmeyen arguman: {$a}\n");
            fwrite(STDERR, "Kullanim: php bin/scan.php [--rebuild] [--dry-run] [--deep] [-v]\n");
            exit(2);
        })($arg),
    };
}

/* ------------------------------------------------------------------ */
/* Tarama                                                              */
/* ------------------------------------------------------------------ */
$yazici = CatalogWriter::fromProject($kok, $dryRun);
$validator = Validator::fromProject($kok);
if ($validator->yuklemeHatasi() !== null) {
    fwrite(STDERR, 'HATA | config/media.schema.json | json | ' . $validator->yuklemeHatasi() . "\n");
    exit(1);
}

$hata = 0;
$uyari = 0;
$dosyaSay = 0;
$jsonSay = 0;
/** sha256'si olmayan katalog satiri sayaci (ozette HASH OLMAYAN). */
$hashOlmayan = 0;
/** Dizin => (teknik.dosya => teknik.sha256): hafif mod kaynagi + --deep karsilastirmasi. */
$jsonTeknik = [];
/** @var list<array{tip: string, veri: array<string, mixed>, yol: string}> $hucreler */
$hucreler = [];

$yigin = [$kok . '/media'];
while (($dizin = array_pop($yigin)) !== null) {
    $girisler = @scandir($dizin);
    if ($girisler === false) {
        $hata++;
        fwrite(STDERR, 'HATA | ' . str_replace('\\', '/', substr($dizin, strlen($kok) + 1)) . " | dosya-var | dizin okunamadi\n");
        continue;
    }
    /* Yan-JSON teknik haritasi (dizin ici): teknik.dosya => teknik.sha256.
       Medya satirinin sha256 degeri hafif modda BURADAN okunur — yeniden hash yok
       (ADR-092 §6.1 madde 5). Taramadan once kurulur cunku JSON, medya dosyasindan
       SONRA da gelebilir (scandir alfabetik). Bozuk JSON'u ana dongu ayrica HATA raporlar. */
    foreach ($girisler as $giris) {
        if ($giris === '.' || $giris === '..' || strtolower(pathinfo($giris, PATHINFO_EXTENSION)) !== 'json') {
            continue;
        }
        $ham = @file_get_contents($dizin . '/' . $giris);
        $v = $ham === false ? null : json_decode($ham, true);
        if (!is_array($v)) {
            continue;
        }
        $tDosya = $v['teknik']['dosya'] ?? null;
        $tHash = $v['teknik']['sha256'] ?? null;
        if (is_string($tDosya) && $tDosya !== '' && is_string($tHash) && $tHash !== '') {
            $jsonTeknik[$dizin][$tDosya] = $tHash;
        }
    }
    foreach ($girisler as $giris) {
        if ($giris === '.' || $giris === '..') {
            continue;
        }
        $tam = $dizin . '/' . $giris;
        if (is_dir($tam)) {
            $yigin[] = $tam;
            continue;
        }
        $goreli = str_replace('\\', '/', substr($tam, strlen($kok) + 1));
        $uzanti = strtolower(pathinfo($giris, PATHINFO_EXTENSION));
        $ustDizin = basename($dizin);
        $dosyaSay++;

        /* Hash — ADR-092 §6.1 madde 5: sha256 yalniz GIRISTE uretilir, sonraki
           asamalarda yeniden hashleme YOK.
           Hafif (varsayilan): hash_file() CALISMAZ; deger yan-JSON'daki
           teknik.sha256'dan okunur, yoksa null (+ HASH OLMAYAN sayaci).
           --deep: yeniden hash + ayni dizindeki JSON kaydi ile karsilastirma
           (fark → HASH-FARK, HATA — audit.php ile ayni ayrim). */
        $sha = null;
        if ($uzanti !== 'json') {
            if (!is_readable($tam)) {
                $hata++;
                fwrite(STDERR, "HATA | {$goreli} | dosya-var | dosya okunamadi\n");
            } elseif ($derin) {
                $hash = @hash_file('sha256', $tam);
                if ($hash === false) {
                    $hata++;
                    fwrite(STDERR, "HATA | {$goreli} | dosya-var | hash hesaplanamadi (deep)\n");
                } else {
                    $sha = $hash;
                    $kayitli = $jsonTeknik[$dizin][$giris] ?? null;
                    if (is_string($kayitli) && $hash !== $kayitli) {
                        $hata++;
                        fwrite(STDERR, "HATA | {$goreli} | HASH-FARK | yeniden hash JSON teknik.sha256 ile farkli: kayitli "
                            . substr($kayitli, 0, 12) . '… ≠ gercek ' . substr($hash, 0, 12) . "…\n");
                    }
                }
            } else {
                $sha = $jsonTeknik[$dizin][$giris] ?? null;
            }
            if ($sha === null) {
                $hashOlmayan++;
            }
        }

        if ($uzanti === 'json') {
            /* Yan-JSON SSOT: dogrula + MySQL hucresi uret. */
            $ham = @file_get_contents($tam);
            $veri = $ham === false ? null : json_decode($ham, true);
            if (!is_array($veri)) {
                $hata++;
                fwrite(STDERR, 'HATA | ' . $goreli . ' | json | ' . ($ham === false ? 'okunamadi' : json_last_error_msg()) . "\n");
                continue;
            }
            $jsonSay++;
            foreach ($validator->validate($giris, $veri) as $mesaj) {
                $hata++;
                fwrite(STDERR, "HATA | {$goreli} | sema | {$mesaj}\n");
            }
            $tip = Validator::tipAnahtari($giris);
            if ($tip !== null) {
                $hucreler[] = ['tip' => $tip, 'veri' => $veri, 'yol' => $goreli];
            }
            /* sha256: JSON'dan okunur (hafif modda yeniden hash YOK);
               JSON'da teknik.sha256 yoksa null + HASH OLMAYAN sayaci. */
            $jsonSha = $veri['teknik']['sha256'] ?? null;
            if (!is_string($jsonSha) || $jsonSha === '') {
                $jsonSha = null;
                $hashOlmayan++;
            }
            $yazici->ekle([
                'tip' => $tip ?? 'bilinmeyen',
                'id' => (string) ($veri['id'] ?? ''),
                'slug' => (string) ($veri['kimlik']['slug'] ?? $veri['slug'] ?? ''),
                'yollar' => [$goreli],
                'baslik' => (string) ($veri['kimlik']['baslik'] ?? $veri['koleksiyon'] ?? ''),
                'sanatci' => $veri['kimlik']['sanatci'] ?? ($veri['ust']['sanatci'] ?? null),
                'album' => $veri['kimlik']['album'] ?? null,
                'yil' => $veri['tarih']['yil'] ?? ($veri['ust']['yil'] ?? null),
                'tag' => $veri['tag'] ?? null,
                'durum' => (string) ($veri['durum'] ?? ''),
                'sha256' => $jsonSha,
            ]);
            if ($verbose) {
                fwrite(STDOUT, "JSON | {$goreli} | {$tip}\n");
            }
            continue;
        }

        /* Medya dosyasi → katalog kaydi (tip = ust dizin: audio/video/_inbox...). */
        $yazici->ekle([
            'tip' => $ustDizin,
            'id' => $giris,
            'slug' => pathinfo($giris, PATHINFO_FILENAME),
            'yollar' => [$goreli],
            'baslik' => pathinfo($giris, PATHINFO_FILENAME),
            'sanatci' => null,
            'album' => null,
            'yil' => null,
            'tag' => null,
            'durum' => $ustDizin === '_inbox' ? 'inbox' : 'aktif',
            'sha256' => $sha,
        ]);
        if ($verbose) {
            fwrite(STDOUT, 'DOSYA | ' . $goreli . ' | ' . ($sha ?? '-') . "\n");
        }
    }
}

/* ------------------------------------------------------------------ */
/* Cikti: JSONL + MySQL                                                 */
/* ------------------------------------------------------------------ */
$sonuc = $yazici->yaz($rebuild);
if ($sonuc['hata'] !== null) {
    $hata++;
    fwrite(STDERR, 'HATA | ' . $yazici->yol() . ' | dosya-var | ' . $sonuc['hata'] . "\n");
}

$mysql = $dryRun
    ? ['baglandi' => false, 'yazildi' => 0, 'uyari' => ['dry-run: MySQL yazimi atladi']]
    : CatalogWriter::mysqlYaz($hucreler, $rebuild);

fwrite(STDOUT, 'MOD: ' . ($derin ? 'deep' : 'hafif (yeniden hash yok)') . "\n");
fwrite(STDOUT, "TOPLAM | {$dosyaSay} dosya | {$jsonSay} yan-JSON | {$hata} hata | {$uyari} uyari\n");
fwrite(STDOUT, "HASH OLMAYAN: {$hashOlmayan}\n");
fwrite(STDOUT, 'KATALOG | ' . $sonuc['yazildi'] . ' satir | ' . $sonuc['bayt'] . ' bayt | ' . $yazici->yol() . "\n");
fwrite(STDOUT, 'MYSQL | baglandi=' . ($mysql['baglandi'] ? 'evet' : 'hayir') . ' yazildi=' . $mysql['yazildi'] . "\n");
foreach ($mysql['uyari'] as $w) {
    fwrite(STDOUT, "UYARI | mysql | {$w}\n");
}
exit($hata > 0 ? 1 : 0);
