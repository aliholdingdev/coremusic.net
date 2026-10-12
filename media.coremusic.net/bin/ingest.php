#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * ingest.php — harici kaynak dizinden medya tasma (ADR-092, F2.2).
 *
 * GUVENLIK (ADR-092 §8 madde 11 — dry-run zorunlu):
 *   --commit YOKKEN medya agacina tek bayt bile kopyalama/yazma/silme YOKTUR.
 *   Dry-run'da yalnizca rapor dosyasi yazilir:
 *   reports/ingest-YYYYMMDD-HHMM.csv  (UTF-8 BOM, virgul, tum alanlar tirnakli)
 *
 * Kullanim:
 *   php bin/ingest.php --source "C:\Users\Bayram Ali\Music" [--commit] [-v]
 *   --source yoksa/gecmezse exit 2 (temiz hata cikisi, crash yok).
 *   --commit verilirse kopyalama onceden STDIN'den "evet" onayi ister.
 *
 * Cikis: 0 = temiz, 1 = hata, 2 = kullanim/kaynak/kok hatasi.
 *
 * UYARI: PHP calisma zamani bu ortamda YOKTUR — php -l + ornek kaynak ile
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
use Media\Slugger;
use Media\Ulid;

/* ------------------------------------------------------------------ */
/* 1) Kok + argumanlar                                                 */
/* ------------------------------------------------------------------ */
$kok = dirname(__DIR__);
if (realpath($kok) === false || !is_file($kok . '/config/mojibake-fix.json')) {
    fwrite(STDERR, "HATA | kok | proje kokunde config/mojibake-fix.json yok\n");
    exit(2);
}

$source = null;
$commit = false;
$verbose = false;
$args = array_slice($argv, 1);
for ($i = 0, $n = count($args); $i < $n; $i++) {
    $arg = $args[$i];
    if ($arg === '--source') {
        $source = $args[++$i] ?? '';
        if ($source === '') {
            fwrite(STDERR, "HATA | arguman | --source degeri bos\n");
            fwrite(STDERR, "Kullanim: php bin/ingest.php --source \"DIZIN\" [--commit] [-v]\n");
            exit(2);
        }
    } elseif ($arg === '--commit') {
        $commit = true;
    } elseif ($arg === '-v' || $arg === '--verbose') {
        $verbose = true;
    } elseif ($arg === '-h' || $arg === '--help') {
        fwrite(STDOUT, "Kullanim: php bin/ingest.php --source \"DIZIN\" [--commit] [-v]\n");
        exit(0);
    } else {
        fwrite(STDERR, "HATA | arguman | bilinmeyen arguman: {$arg}\n");
        fwrite(STDERR, "Kullanim: php bin/ingest.php --source \"DIZIN\" [--commit] [-v]\n");
        exit(2);
    }
}
if ($source === null) {
    fwrite(STDERR, "HATA | arguman | --source gerekli\n");
    fwrite(STDERR, "Kullanim: php bin/ingest.php --source \"DIZIN\" [--commit] [-v]\n");
    exit(2);
}
$sourceReal = realpath($source);
if ($sourceReal === false || !is_dir($sourceReal)) {
    fwrite(STDERR, "HATA | kaynak | dizin bulunamadi: {$source}\n");
    fwrite(STDERR, "NOT | hicbir dosya okunmadi/yazilmadi (dry-run)\n");
    exit(2);
}

/* ------------------------------------------------------------------ */
/* 2) Yardimcilar                                                      */
/* ------------------------------------------------------------------ */

/** mojibake-fix.json uzerunden ad normalizasyonu (slug URETMEDEN once). */
function adDuzelt(string $ad, array $mojibake): string
{
    if ($mojibake === []) {
        return $ad;
    }

    return str_replace(array_keys($mojibake), array_values($mojibake), $ad);
}

/** Yol sinirlari: hedef her zaman proje koku icinde (ADR-092 §8 madde 2). */
function kokIcindeMi(string $kok, string $goreliYol): bool
{
    $gercekKok = realpath($kok);
    if ($gercekKok === false) {
        return false;
    }
    $aday = $gercekKok . '/' . str_replace('\\', '/', $goreliYol);
    $dizin = dirname($aday);
    /* henuz olusturulmamis dizinlerde dirname uzerinden kontrol. */
    $gercekDizin = realpath($dizin);
    if ($gercekDizin === false) {
        /* kokun kendisine klasor acmak uzereyse kok kontrolu yeter. */
        return str_starts_with($dizin, $gercekKok);
    }

    return str_starts_with($gercekDizin, $gercekKok) || $gercekDizin === $gercekKok;
}

/* ------------------------------------------------------------------ */
/* 3) Yapilandirma                                                     */
/* ------------------------------------------------------------------ */
$mojibake = [];
$ham = @file_get_contents($kok . '/config/mojibake-fix.json');
$v = $ham === false ? null : json_decode($ham, true);
if (is_array($v)) {
    foreach ($v as $k => $deger) {
        if (is_string($k) && is_string($deger) && trim($k) !== '') {
            $mojibake[$k] = $deger;
        }
    }
}

/* ------------------------------------------------------------------ */
/* 4) Kaynagi tara (salt okunur)                                       */
/* ------------------------------------------------------------------ */
$sesUzanti = ['mp3', 'flac', 'wav', 'm4a', 'ogg', 'wma', 'opus', 'aac'];
$videoUzanti = ['mp4', 'mkv', 'avi', 'webm'];
$hurdaUzanti = ['ini', 'lnk', 'db', 'tmp', 'url'];
$playlistUzanti = ['m3u', 'm3u8', 'pls'];

/** @var list<array{durum: string, kaynak: string, hedef: string, slug: string, ulid: string, sha256: string, not: string}> $adaylar */
$adaylar = [];
$koleksiyonAday = 0;
$taramaHata = 0;

$yigin = [$sourceReal];
while (($dizin = array_pop($yigin)) !== null) {
    $girisler = @scandir($dizin);
    if ($girisler === false) {
        $taramaHata++;
        fwrite(STDERR, 'HATA | ' . $dizin . " | dosya-var | kaynak dizin okunamadi\n");
        continue;
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
        if (!is_file($tam)) {
            continue;
        }
        $uzanti = strtolower(pathinfo($giris, PATHINFO_EXTENSION));
        $ad = pathinfo($giris, PATHINFO_FILENAME);

        if (in_array($uzanti, $playlistUzanti, true)) {
            $koleksiyonAday++;
            $adaylar[] = ['durum' => 'atla', 'kaynak' => $tam, 'hedef' => '', 'slug' => '', 'ulid' => '', 'sha256' => '',
                'not' => 'koleksiyon-aday: playlist parse (faz 2b) → koleksiyon tanimi'];

            continue;
        }
        if (in_array($uzanti, $hurdaUzanti, true)) {
            $adaylar[] = ['durum' => 'atla', 'kaynak' => $tam, 'hedef' => '', 'slug' => '', 'ulid' => '', 'sha256' => '',
                'not' => 'hurda-aday: medya degil'];

            continue;
        }
        $tur = in_array($uzanti, $sesUzanti, true) ? 'ses'
            : (in_array($uzanti, $videoUzanti, true) ? 'video' : null);
        if ($tur === null) {
            $adaylar[] = ['durum' => 'atla', 'kaynak' => $tam, 'hedef' => '', 'slug' => '', 'ulid' => '', 'sha256' => '',
                'not' => "hurda-aday: .{$uzanti} format tablosunda yok"];

            continue;
        }

        /* Mojibake fix → slug (adlandirma §6: normalizasyon slug'dan ÖNCE). */
        $duzeltilmis = adDuzelt($ad, $mojibake);
        $slug = Slugger::slug($duzeltilmis);

        /* sha256: yalniz GIRISTE bir kez (ADR-092 §6.1 madde 5). */
        $sha = @hash_file('sha256', $tam);
        if ($sha === false) {
            $taramaHata++;
            $adaylar[] = ['durum' => 'hata', 'kaynak' => $tam, 'hedef' => '', 'slug' => '', 'ulid' => '', 'sha256' => '',
                'not' => 'dosya-var: hash hesaplanamadi'];

            continue;
        }

        $adaylar[] = [
            'durum' => 'yeni',
            'kaynak' => $tam,
            'hedef' => '', // yalniz rapor icin tahmin; gercek yazim --commit dalinda
            'slug' => $slug,
            'ulid' => Ulid::uret(),
            'sha256' => $sha,
            'not' => ($duzeltilmis !== $ad ? 'mojibake-duzeltildi' : '') . " | tur:{$tur}",
        ];
    }
}

/* ------------------------------------------------------------------ */
/* 5) Dedupe: katalog + parti ici (ADR-092 §8 madde 7)                 */
/* ------------------------------------------------------------------ */
$katalogHash = CatalogWriter::hashSetiOku($kok . '/catalog/catalog.jsonl');
$partiHash = [];
foreach ($adaylar as $i => $a) {
    if ($a['durum'] !== 'yeni') {
        continue;
    }
    if (isset($katalogHash[$a['sha256']])) {
        $adaylar[$i]['durum'] = 'tekrar';
        $adaylar[$i]['not'] .= ' | tekrar: katalogda var (' . $katalogHash[$a['sha256']] . ')';
    } elseif (isset($partiHash[$a['sha256']])) {
        $adaylar[$i]['durum'] = 'tekrar';
        $adaylar[$i]['not'] .= ' | tekrar: partide ' . $partiHash[$a['sha256']];
    } else {
        $partiHash[$a['sha256']] = $a['kaynak'];
    }
}

/* ------------------------------------------------------------------ */
/* 6) Hedef tahmini (rapor icin; gercek yazim yalniz --commit dalinda) */
/* ------------------------------------------------------------------ */
$bugun = date('Y-m-d');
$alinanSlug = []; // slug -> kullanim sayisi (adlandirma §3: -2, -3...)
foreach ($adaylar as $i => $a) {
    if ($a['durum'] !== 'yeni') {
        continue;
    }
    $say = ($alinanSlug[$a['slug']] ?? 0) + 1;
    $alinanSlug[$a['slug']] = $say;
    if ($say > 1) {
        /* Cakisma eki tam ad uzerinden (Slugger::slug dolu listesi). */
        $adaylar[$i]['slug'] = Slugger::slug($a['slug'], [$a['slug']]);
    }
    $uzanti = strtolower(pathinfo($a['kaynak'], PATHINFO_EXTENSION));
    /* Tahmini hedef: media/_inbox/{YYYY-AA-GG}/{slug}-{ULID}.{uzanti}
       (faz 2b siniflandirmasinda kesin klasor belirlenir). */
    $adaylar[$i]['hedef'] = "media/_inbox/{$bugun}/{$adaylar[$i]['slug']}-{$a['ulid']}.{$uzanti}";
}

/* ------------------------------------------------------------------ */
/* 7) Rapor yaz (dry-run'da da rapor yazilir — medya mutasyonu degil)   */
/* ------------------------------------------------------------------ */
$raporDir = $kok . '/reports';
if (!is_dir($raporDir) && !@mkdir($raporDir, 0775, true) && !is_dir($raporDir)) {
    fwrite(STDERR, "HATA | reports/ | dosya-var | klasor olusturulamadi\n");
    exit(1);
}
$raporYol = $raporDir . '/ingest-' . date('Ymd-His') . '.csv';
$hl = fopen($raporYol, 'wb');
if ($hl === false) {
    fwrite(STDERR, "HATA | {$raporYol} | dosya-var | rapor acilamadi\n");
    exit(1);
}
fwrite($hl, "\xEF\xBB\xBF"); // UTF-8 BOM (Excel)
fputcsv($hl, ['durum', 'kaynak', 'hedef', 'slug', 'ulid', 'sha256', 'not'], ',', '"', '\\');
foreach ($adaylar as $a) {
    fputcsv($hl, [$a['durum'], $a['kaynak'], $a['hedef'], $a['slug'], $a['ulid'], $a['sha256'], $a['not']], ',', '"', '\\');
}
fclose($hl);

/* ------------------------------------------------------------------ */
/* 8) Ozet                                                             */
/* ------------------------------------------------------------------ */
$adet = ['yeni' => 0, 'tekrar' => 0, 'atla' => 0, 'hata' => 0];
foreach ($adaylar as $a) {
    $adet[$a['durum']] = ($adet[$a['durum']] ?? 0) + 1;
}
fwrite(STDOUT, "TOPLAM | {$adet['yeni']} yeni | {$adet['tekrar']} tekrar | {$adet['atla']} atla | {$adet['hata']} hata | rapor: {$raporYol}\n");
if ($koleksiyonAday > 0) {
    fwrite(STDOUT, "NOT | koleksiyon-aday | {$koleksiyonAday} playlist dosyasi (faz 2b parse)\n");
}
if (!$commit) {
    fwrite(STDOUT, "DRY-RUN | medya agacina hicbir dosya kopyalanmadi/yazilmadi/silinmedi (tek bayt mutasyon yok)\n");
    exit(($adet['hata'] > 0 || $taramaHata > 0) ? 1 : 0);
}

/* ------------------------------------------------------------------ */
/* 9) --commit: onay + kopyalama (tek kopyalama noktasi)               */
/* ------------------------------------------------------------------ */
fwrite(STDOUT, "ONAY | {$adet['yeni']} dosya kopyalanacak, KAYNAKLARA DOKUNULMAYACAK. Onay icin 'evet': ");
$cevap = trim((string) (fgets(STDIN) ?: ''));
if (strtolower($cevap) !== 'evet') {
    fwrite(STDOUT, "IPTAL | onay alinmadi; hicbir dosya kopyalanmadi (0 bayt)\n");
    exit(0);
}

$kopyalanan = 0;
foreach ($adaylar as $a) {
    if ($a['durum'] !== 'yeni') {
        continue;
    }
    $hedefGoreli = $a['hedef'];
    /* Yol siniri: hedef proje koku icinde kalmalidir (kokIcindeMi). */
    if (!kokIcindeMi($kok, $hedefGoreli)) {
        $taramaHata++;
        fwrite(STDERR, "HATA | {$hedefGoreli} | yol-260 | hedef proje koku disina dusebilir, kopyalama iptal\n");

        continue;
    }
    $hedefTam = $kok . '/' . $hedefGoreli;
    $hedefDizin = dirname($hedefTam);
    if (!is_dir($hedefDizin) && !@mkdir($hedefDizin, 0775, true) && !is_dir($hedefDizin)) {
        $taramaHata++;
        fwrite(STDERR, "HATA | {$hedefGoreli} | dosya-var | hedef klasor olusturulamadi\n");

        continue;
    }
    /* copy(): tek kopyalama fonksiyonu — satir kaniti asagida. */
    if (!@copy($a['kaynak'], $hedefTam)) {
        $taramaHata++;
        fwrite(STDERR, "HATA | {$hedefGoreli} | dosya-var | kopyalanamadi (kaynak: {$a['kaynak']})\n");

        continue;
    }
    $kopyalanan++;
    /* Kopya sonrasi hash dogrulamasi (dokunulmazlik). */
    $yeniHash = @hash_file('sha256', $hedefTam);
    if ($yeniHash !== $a['sha256']) {
        $taramaHata++;
        fwrite(STDERR, "HATA | {$hedefGoreli} | sha256-format | kopya hashi kaynakla ayni degil\n");
    }
    if ($verbose) {
        fwrite(STDOUT, "KOPYA | {$a['kaynak']} -> {$hedefGoreli}\n");
    }
}
fwrite(STDOUT, "COMMIT | {$kopyalanan}/{$adet['yeni']} dosya kopyalandi | rapor: {$raporYol}\n");
exit(($adet['hata'] > 0 || $taramaHata > 0) ? 1 : 0);
