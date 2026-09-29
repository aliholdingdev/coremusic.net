#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * audit.php — media/coremusic.net denetimi (ADR-092 §6.1/§7.1, F2.2).
 *
 * Salt okunur: medya agacinda hicbir yazma islemi yapmaz.
 * Ciktigi : "SEVIYE | yol | kural | neden" + TOPLAM ozeti.
 * Exit    : 0 = HATA yok · 1 = en az bir HATA · 2 = kullanim/kok hatasi.
 *
 * SSOT: config/media.schema.json (v2) · config/taxonomy.json ·
 *       config/mojibake-fix.json · docs/adlandirma.md (6 kural) ·
 *       docs/dizin-yapisi.md (agac + format §4) · ADR-092 §6.1/§7.1.
 *
 * Kural kimlikleri (docs/audits ile birebir):
 *   json · sema · addl-props · slug-regex · slug-max80 · slug-klasor ·
 *   sira-on-ek(UYARI) · ulid-regex · ulid-teklik · sha256-format ·
 *   sha256-tekrar(UYARI) · HASH-FARK(--deep) · dosya-desen · dosya-var ·
 *   varyant-var(UYARI) ·
 *   tag-enum · gorsel-var · gorsel-yok(UYARI) · mojibake ·
 *   hurda-aday(UYARI) · yol-260 · cesitli-az(UYARI)
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

use Media\Ulid;
use Media\Validator;

/* ------------------------------------------------------------------ */
/* Kok + arguman                                                       */
/* ------------------------------------------------------------------ */
$kok = dirname(__DIR__);
if (realpath($kok) === false || !is_file($kok . '/config/media.schema.json')) {
    fwrite(STDERR, "HATA | kok | proje kokunde config/media.schema.json yok\n");
    exit(2);
}
/** Varsayilan: hafif mod — sha256 yeniden hesaplanmaz (ADR-092 §6.1 madde 5). */
$derin = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '-h' || $arg === '--help') {
        fwrite(STDOUT, "Kullanim: php bin/audit.php [--deep]  (salt okunur — medya agacina hicbir sey yazmaz)\n");
        fwrite(STDOUT, "  --deep  medya dosyasinin sha256'sini yeniden hesaplar ve ayni dizindeki\n");
        fwrite(STDOUT, "          JSON'daki teknik.sha256 ile karsilastirir (fark → HASH-FARK, HATA).\n");
        fwrite(STDOUT, "          Varsayilan modda yeniden hash YOKTUR (1M+ varlikta CPU maliyeti).\n");
        exit(0);
    }
    if ($arg === '--deep') {
        $derin = true;
        continue;
    }
    fwrite(STDERR, "HATA | arguman | bilinmeyen arguman: {$arg}\n");
    exit(2);
}

/** @var int $hata */
$hata = 0;
/** @var int $uyari */
$uyari = 0;

/** SEVIYE | yol | kural | neden — hata STDERR'a, uyari STDOUT'a. */
$rapor = static function (string $seviye, string $yol, string $kural, string $neden) use (&$hata, &$uyari): void {
    if ($seviye === 'HATA') {
        $hata++;
        fwrite(STDERR, "HATA | {$yol} | {$kural} | {$neden}\n");
    } else {
        $uyari++;
        fwrite(STDOUT, "UYARI | {$yol} | {$kural} | {$neden}\n");
    }
};

/* ------------------------------------------------------------------ */
/* 1) Config dosyalari (kural: json)                                   */
/* ------------------------------------------------------------------ */
$config = [];
foreach (['media.schema.json', 'taxonomy.json', 'mojibake-fix.json'] as $ad) {
    $goreli = 'config/' . $ad;
    $ham = @file_get_contents($kok . '/' . $goreli);
    if ($ham === false) {
        $rapor('HATA', $goreli, 'json', 'dosya okunamadi');
        continue;
    }
    $v = json_decode($ham, true);
    if (!is_array($v)) {
        $rapor('HATA', $goreli, 'json', json_last_error_msg());
        continue;
    }
    $config[$ad] = $v;
}
$schema = $config['media.schema.json'] ?? [];
$taxonomy = $config['taxonomy.json'] ?? [];
$mojibakeHam = $config['mojibake-fix.json'] ?? [];

/* ------------------------------------------------------------------ */
/* 2) Sema yapisı (kural: sema + addl-props)                           */
/* ------------------------------------------------------------------ */
if ($schema !== []) {
    if (($schema['$schema'] ?? '') !== 'http://json-schema.org/draft-07/schema#'
        && !str_contains((string) ($schema['$schema'] ?? ''), 'json-schema.org')) {
        $rapor('HATA', 'config/media.schema.json', 'sema', '$schema anahtari taninmadi');
    }
    $semaDefs = is_array($schema['$defs'] ?? null) ? $schema['$defs'] : [];
    foreach (['artist', 'album', 'meta', 'koleksiyon'] as $anahtar) {
        if (!isset($semaDefs[$anahtar])) {
            $rapor('HATA', 'config/media.schema.json', 'sema', "\$defs.{$anahtar} yok");
            continue;
        }
        if (($semaDefs[$anahtar]['additionalProperties'] ?? null) !== false) {
            $rapor('HATA', 'config/media.schema.json', 'addl-props', "\$defs.{$anahtar}.additionalProperties false degil");
        }
    }
    /* Ek katmanı serbest (media.schema.json $defs.ek $comment) — bilinçli istisna. */
    if (isset($semaDefs['ek']) && ($semaDefs['ek']['additionalProperties'] ?? null) === false) {
        $rapor('HATA', 'config/media.schema.json', 'addl-props', '$defs.ek kilitlenmis (serbest olmali)');
    }
} else {
    $semaDefs = [];
}

/* ------------------------------------------------------------------ */
/* 3) Taksonomi (kural: tag-enum) + mojibake kataloğu (kural: mojibake) */
/* ------------------------------------------------------------------ */
$taxonomySetleri = [];
if ($taxonomy !== []) {
    $taxDefs = $taxonomy['definitions'] ?? [];
    if (!is_array($taxDefs) || $taxDefs === []) {
        $rapor('HATA', 'config/taxonomy.json', 'tag-enum', 'definitions bos');
    }
    foreach (is_array($taxDefs) ? $taxDefs : [] as $anahtar => $degerler) {
        if (!is_array($degerler) || $degerler === []) {
            $rapor('HATA', 'config/taxonomy.json', 'tag-enum', "{$anahtar} bos liste");
            continue;
        }
        $set = [];
        foreach ($degerler as $d) {
            if (!is_string($d)) {
                $rapor('HATA', 'config/taxonomy.json', 'tag-enum', "{$anahtar} icinde metin olmayan deger");
            } else {
                $set[$d] = true;
            }
        }
        $taxonomySetleri[$anahtar] = $set;
    }
    /* Şema enum ↔ taxonomy senkronu: $comment'inde "SYNC: taxonomy.json#<anahtar>"
       notu olan her $def, taxonomy deger listesi ile birebir ayni olmalidir. */
    $senkron = 0;
    foreach ($semaDefs as $ad => $def) {
        if (!is_array($def) || !isset($def['enum']) || !is_array($def['enum'])) {
            continue;
        }
        if (preg_match('/SYNC:\s*taxonomy\.json#([a-z0-9_]+)/', (string) ($def['$comment'] ?? ''), $e) !== 1) {
            continue;
        }
        $anahtar = $e[1];
        $senkron++;
        if (!isset($taxonomySetleri[$anahtar])) {
            $rapor('HATA', 'config/media.schema.json', 'tag-enum', "\$defs.{$ad} → taxonomy.json'de anahtar yok: {$anahtar}");
            continue;
        }
        $taxSet = $taxonomySetleri[$anahtar];
        foreach ($def['enum'] as $deger) {
            if (!is_string($deger) || !isset($taxSet[$deger])) {
                $rapor('HATA', 'config/media.schema.json', 'tag-enum', "\$defs.{$ad} enum disi taxonomy'de yok: " . (string) $deger);
            }
        }
        foreach (array_keys($taxSet) as $deger) {
            if (!in_array($deger, $def['enum'], true)) {
                $rapor('HATA', 'config/taxonomy.json', 'tag-enum', "{$anahtar} semada enum'da yok: {$deger}");
            }
        }
    }
    if ($senkron === 0) {
        $rapor('HATA', 'config/media.schema.json', 'tag-enum', 'SYNC notlu enum def\'i bulunamadi (senkron kontrolu kosulamadi)');
    }
}

$mojibakeAnahtar = [];
foreach ($mojibakeHam as $k => $v) {
    if (!is_string($k) || !is_string($v)) {
        $rapor('HATA', 'config/mojibake-fix.json', 'json', 'anahtar/deger metin degil');
        continue;
    }
    if (trim($k) === '') {
        continue; // bilinçli istisna: "Â " bosluk eslemesi (mojibake-fix.json)
    }
    $mojibakeAnahtar[$k] = true;
}

/* ------------------------------------------------------------------ */
/* 4) Medya agaci taramasi                                             */
/* ------------------------------------------------------------------ */
$dosyaAdDeseni = static function (string $ad): bool {
    /* docs/dizin-yapisi.md §4 format tablosu: sabit master adlari + varyantlar. */
    return preg_match(
        '/^(audio(\.[a-z0-9]+)?|audio-[0-9]+p?\.mp3|video(\.[a-z0-9]+)?|video-[0-9]+p?\.mp4'
        . '|cover\.(jpg|jpeg|png|webp)|poster\.(jpg|jpeg|png|webp)|avatar\.(jpg|jpeg|png|webp)'
        . '|artist\.json|album\.json|meta\.json)$/i',
        $ad
    ) === 1;
};

/* Yan dosya (altyazi/hane): ad serbest, yalnizca uzanti kontrol edilir. */
$yanDosya = ['lrc', 'srt', 'vtt', 'txt'];

$ulidSayi = [];
$shaSayi = [];
/** --deep: dizin => (teknik.dosya => teknik.sha256) karsilastirma referansi. */
$jsonTeknik = [];
/** --deep: dizin => (dosya adi => yeniden hesaplanan gercek hash|null). */
$derinHash = [];
$kayit = 0;
$siraOnekli = [];
/** master audio.{ext} bulunan klasorler → varyant-var denetimi (UYARI). */
$masterKlasorler = [];

/** Dizin adi denetimi: opsiyonel `NN-`/`YIL-`/`yil-yok-`/`YIL-klip-` onegi soyulur
 *  sonraki govde slug olmalidir (adlandirma §1/§2/§7 — kural slug-klasor). */
$klasorSlugDenetle = static function (string $klasorAd) use ($rapor): void {
    if (str_starts_with($klasorAd, '_')) {
        return; // _inbox/_cesitli/_hurda/_tekli sabitleri (istisna; _cesitli asagida ayrica)
    }
    if (mb_strlen($klasorAd, 'UTF-8') > 80) {
        $rapor('HATA', 'media/…/' . $klasorAd, 'slug-max80', mb_strlen($klasorAd, 'UTF-8') . ' karakter (max 80)');
    }
    /* On ekleri soyle: 01- | 1975- | yil-yok- | 2012-klip- */
    $govde = preg_replace('/^(\d{2}-|\d{4}-klip-|yil-yok-)/', '', $klasorAd);
    if (!is_string($govde) || $govde === '' || preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $govde) !== 1) {
        $rapor('HATA', 'media/…/' . $klasorAd, 'slug-klasor', "govde slug degil (onek sonrasi): '" . (string) $govde . "'");
    }
    if (preg_match('/[^a-z0-9-]/', $klasorAd) === 1) {
        $rapor('HATA', 'media/…/' . $klasorAd, 'slug-regex', 'klasor adinda ASCII disi/gecersiz karakter (fold kural 1)');
    }
};

$yigin = [$kok . '/media'];
while (($dizin = array_pop($yigin)) !== null) {
    $girisler = @scandir($dizin);
    if ($girisler === false) {
        $rapor('HATA', str_replace('\\', '/', substr($dizin, strlen($kok) + 1)), 'dosya-var', 'dizin okunamadi (izin/UTF-8?)');
        continue;
    }
    foreach ($girisler as $giris) {
        if ($giris === '.' || $giris === '..') {
            continue;
        }
        $tam = $dizin . '/' . $giris;
        $goreli = str_replace('\\', '/', substr($tam, strlen($kok) + 1));

        if (is_dir($tam)) {
            $yigin[] = $tam;
            $klasorSlugDenetle($giris);
            if ($giris === '_cesitli') {
                /* kural 3: _cesitli HER ZAMAN a-z bölmeli — altindaki ilk katman a-z tek harf/word. */
                $altlar = @scandir($tam) ?: [];
                foreach ($altlar as $alt) {
                    if ($alt === '.' || $alt === '..') {
                        continue;
                    }
                    if (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $alt) !== 1) {
                        $rapor('HATA', str_replace('\\', '/', substr($tam . '/' . $alt, strlen($kok) + 1)), 'cesitli-az', "_cesitli alt klasoru a-z degil: {$alt}");
                    }
                }
            }
            continue;
        }
        $kayit++;
        $uzanti = strtolower(pathinfo($giris, PATHINFO_EXTENSION));
        $ustDizin = basename($dizin);

        /* mojibake: dosya/klasor adi (UTF-8 bozuk dizi aranir). */
        foreach ([$giris, $ustDizin] as $ad) {
            foreach (array_keys($mojibakeAnahtar) as $kirik) {
                if ($kirik !== '' && str_contains($ad, $kirik)) {
                    $rapor('HATA', $goreli, 'mojibake', "'{$kirik}' adinda goruldu (ADR-092 §7.1/5)");
                    break 2;
                }
            }
        }

        /* hurda-aday (UYARI): kabul edilmeyen uzanti (dizin-yapisi §4). */
        $kabul = ['mp3', 'flac', 'wav', 'm4a', 'ogg', 'wma', 'mp4', 'mkv', 'avi', 'webm',
            'jpg', 'jpeg', 'png', 'webp', 'json', 'lrc', 'srt', 'vtt', 'txt'];
        if (!in_array($uzanti, $kabul, true)) {
            $rapor('UYARI', $goreli, 'hurda-aday', ".{$uzanti} format tablosunda yok → _hurda/rapor adayi");
            continue;
        }

        /* dosya-desen: format tablosu sabit adlari (§4).
           Istisnalar: yan dosyalar (serbest ad) · `_inbox`/`_hurda` altindakiler
           (islenmemis — ad henuz uretilmedi; ingest faz 2b'de adlandirir). */
        $yolIslenmemis = str_contains($goreli, '/_inbox/') || str_contains($goreli, '/_hurda/');
        if (!in_array($uzanti, $yanDosya, true)
            && !$yolIslenmemis
            && !$dosyaAdDeseni($giris)) {
            $rapor('HATA', $goreli, 'dosya-desen', "'{$giris}' format tablosu (§4) disinda ad");
        }

        /* yol-260: Windows MAX_PATH 260 (dizin-yapisi §6). */
        if (strlen($tam) > 260) {
            $rapor('HATA', $goreli, 'yol-260', strlen($tam) . ' bayt (max 260)');
        }

        /* JSON meta dosyalari → Validator (slug-regex/max80, ulid-regex, sha256-format, tag-enum...). */
        if ($uzanti === 'json') {
            $ham = @file_get_contents($tam);
            if ($ham === false) {
                $rapor('HATA', $goreli, 'dosya-var', 'JSON okunamadi');
                continue;
            }
            if (!mb_check_encoding($ham, 'UTF-8')) {
                $rapor('HATA', $goreli, 'mojibake', 'JSON UTF-8 degil');
                continue;
            }
            foreach (array_keys($mojibakeAnahtar) as $kirik) {
                if ($kirik !== '' && str_contains($ham, $kirik)) {
                    $rapor('HATA', $goreli, 'mojibake', "'{$kirik}' JSON iceriginde");
                    break;
                }
            }
            $veri = json_decode($ham, true);
            if (!is_array($veri)) {
                $rapor('HATA', $goreli, 'json', json_last_error_msg());
                continue;
            }
            /* validator mesajlari "yol => neden" bicimindedir; kural kimligine cevir. */
            $val = Validator::fromProject($kok);
            foreach ($val->validate($giris, $veri) as $mesaj) {
                $kural = match (true) {
                    str_contains($mesaj, 'enum disi') || str_contains($mesaj, 'taxonomy drift') => 'tag-enum',
                    str_contains($mesaj, 'pattern ihlali') && str_contains($mesaj, '[0-9A-HJKMNP') => 'ulid-regex',
                    str_contains($mesaj, 'pattern ihlali') && str_contains($mesaj, 'a-f0-9') => 'sha256-format',
                    str_contains($mesaj, 'maxLength') || str_contains($mesaj, 'minLength') => 'slug-max80',
                    str_contains($mesaj, 'additionalProperties') => 'addl-props',
                    str_contains($mesaj, 'zorunlu alan') || str_contains($mesaj, 'tip uyumsuz') => 'sema',
                    default => 'sema',
                };
                $rapor('HATA', $goreli, $kural, $mesaj);
            }
            /* ulid-teklik: meta/artist/album id alanlari. */
            $id = $veri['id'] ?? null;
            if (is_string($id) && $id !== '') {
                if (!Ulid::isValid($id)) {
                    $rapor('HATA', $goreli, 'ulid-regex', "id Crockford ULID degil: {$id}");
                } else {
                    $ulidSayi[$id] = ($ulidSayi[$id] ?? 0) + 1;
                }
            }
            /* sha256: teknik.sha256 → tekrar kümesi (format + küresel teklik:
               hafif modda yeniden hash yok; denetim yalnizca bu deger uzerinden). */
            $hash = $veri['teknik']['sha256'] ?? null;
            if (is_string($hash) && $hash !== '') {
                if (preg_match('/^[0-9a-f]{64}$/', $hash) !== 1) {
                    $rapor('HATA', $goreli, 'sha256-format', 'teknik.sha256 kucuk harf hex degil');
                } else {
                    $shaSayi[$hash] = ($shaSayi[$hash] ?? 0) + 1;
                    /* --deep karsilastirma referansi: ayni dizindeki medya dosyasi
                       (teknik.dosya) icin kayitli hash. */
                    $tDosya = $veri['teknik']['dosya'] ?? null;
                    if (is_string($tDosya) && $tDosya !== '') {
                        $jsonTeknik[$dizin][$tDosya] = $hash;
                    }
                }
            }
            /* gorsel-var / gorsel-yok: album.json cover alani. */
            if ($giris === 'album.json') {
                $cover = $veri['gorsel']['cover'] ?? '';
                if (!is_string($cover) || $cover === '') {
                    $rapor('UYARI', $goreli, 'gorsel-yok', 'album.json gorsel.cover bos');
                } elseif (!is_file(dirname($tam) . '/' . $cover)) {
                    $rapor('HATA', $goreli, 'gorsel-var', "cover dosyasi yok: {$cover}");
                }
            }
            continue;
        }

        /* Medya/gorsel dosyasi — §6.1 madde 5: sha256 yalniz GIRISTE uretilir,
           sonraki asamalarda yeniden hashleme YOK (1M+ varlikta CPU felaketi).
           Varsayilan (hafif) modda hash_file() CALISMAZ; yalnizca okunabilirlik
           denetlenir. Format (^[a-f0-9]{64}$) + kurel teklik kontrolleri JSON'daki
           teknik.sha256 uzerinden kosulur (sha256-format / sha256-tekrar).
           Yeniden hash yalnizca --deep bayraginda yapilir. */
        if (!is_readable($tam)) {
            $rapor('HATA', $goreli, 'dosya-var', 'dosya okunamadi');
        } elseif ($derin) {
            $gercek = @hash_file('sha256', $tam);
            if ($gercek === false) {
                $derinHash[$dizin][$giris] = null;
                $rapor('HATA', $goreli, 'dosya-var', 'dosya okunamadi (deep)');
            } else {
                $derinHash[$dizin][$giris] = $gercek;
            }
        }

        /* varyant-var (UYARI): klasorde yalniz `audio.mp3` varyanti var, master yok. */
        if (preg_match('/^audio\.[a-z0-9]+$/i', $giris) === 1) {
            $masterKlasorler[$dizin][] = $giris;
        }

        /* sira-on-ek (UYARI): `_inbox` icinde dosya adinda NN- onegi olmamali
           (adlandirma §2: sira onegi klasorundedir — inbox'ta henuz klasor yok,
           bu dosyalar islenmemistir). */
        if (str_contains($goreli, '/_inbox/') && preg_match('/^\d{2}-/', $giris) === 1) {
            $siraOnekli[$goreli] = true;
        }
    }
}

/* ------------------------------------------------------------------ */
/* 5) Turetilmis kontroller (teklik + tekrar)                          */
/* ------------------------------------------------------------------ */
foreach ($ulidSayi as $u => $n) {
    if ($n > 1) {
        $rapor('HATA', 'media/', 'ulid-teklik', "{$u} {$n} kez geciyor");
    }
}
foreach ($shaSayi as $h => $n) {
    if ($n > 1) {
        $ozet = substr($h, 0, 12) . '…';
        $rapor('UYARI', 'media/', 'sha256-tekrar', "{$ozet} ayni hash {$n} kez (kopya adayi)");
    }
}
/* --deep: yeniden hesaplanan gercek hash ↔ JSON teknik.sha256 karsilastirmasi.
   Taramadan SONRA kosulur (JSON, medya dosyasindan sonra da gelebilir).
   Karsilastirma referansi olmayan dosya (JSON'da teknik.dosya karsiligi yok) atlanir. */
if ($derin) {
    foreach ($derinHash as $dizinAd => $dosyalar) {
        foreach ($dosyalar as $ad => $gercek) {
            $kayitli = $jsonTeknik[$dizinAd][$ad] ?? null;
            if (!is_string($kayitli) || $gercek === null) {
                continue;
            }
            if ($gercek !== $kayitli) {
                $goreli = str_replace('\\', '/', substr($dizinAd . '/' . $ad, strlen($kok) + 1));
                $rapor('HATA', $goreli, 'HASH-FARK', 'yeniden hash JSON teknik.sha256 ile farkli: kayitli '
                    . substr($kayitli, 0, 12) . '… ≠ gercek ' . substr($gercek, 0, 12) . '…');
            }
        }
    }
}
foreach (array_keys($siraOnekli) as $yol) {
    $rapor('UYARI', $yol, 'sira-on-ek', 'dosya adinda sira onegi (adlandirma §2: onek klasorundedir)');
}
foreach ($masterKlasorler as $klasor => $adlar) {
    /* Yalniz `audio.mp3` (varyant) varsa master `audio.{orijinal}` yoktur. */
    $masterVar = false;
    foreach ($adlar as $a) {
        if (strcasecmp($a, 'audio.mp3') !== 0) {
            $masterVar = true;
            break;
        }
    }
    if (!$masterVar && count($adlar) > 0) {
        $goreliKlasor = str_replace('\\', '/', substr($klasor, strlen($kok) + 1));
        $rapor('UYARI', $goreliKlasor, 'varyant-var', 'audio.mp3 varyanti var ama master audio.{orijinal} yok (dizin-yapisi §3.1)');
    }
}

/* ------------------------------------------------------------------ */
/* 6) Manifest karsilastirmasi (opsiyonel — scan once kosulursa)       */
/* ------------------------------------------------------------------ */
$manifestYol = $kok . '/catalog/catalog.jsonl';
if (!is_file($manifestYol)) {
    $rapor('UYARI', 'catalog/catalog.jsonl', 'dosya-var', 'katalog yok; scan-onrasi kapsam; dosya-degistirme kontrolleri kismi kaldi');
} else {
    $satirlar = @file($manifestYol, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($satirlar as $satir) {
        $k = json_decode($satir, true);
        if (!is_array($k)) {
            $rapor('HATA', 'catalog/catalog.jsonl', 'json', 'gecersiz JSONL satiri');
            continue;
        }
        $yollar = is_array($k['yollar'] ?? null) ? $k['yollar'] : [];
        foreach ($yollar as $y) {
            if (!is_file($kok . '/' . $y)) {
                $rapor('HATA', (string) $y, 'dosya-var', 'katalogda var ama diskte yok');
            }
        }
    }
}

/* ------------------------------------------------------------------ */
/* Ozet                                                                */
/* ------------------------------------------------------------------ */
fwrite(STDOUT, 'MOD: ' . ($derin ? 'deep (yeniden hash + JSON karsilastirmasi)' : 'hafif (yeniden hash yok)') . "\n");
fwrite(STDOUT, "TOPLAM | {$hata} HATA | {$uyari} UYARI | {$kayit} dosya tarandi\n");
exit($hata > 0 ? 1 : 0);
