<?php

declare(strict_types=1);

/**
 * db-access-guard — DB erişim yüzeyi guardrail (CI statik gate).
 *
 * Kural (ADR-002 + kullanıcı kuralı "db'ye erişim yasak controller'dan ve index'ten"):
 *
 *  1) PREZENTASYON yuzeyleri (tum index.php girisleri — v2/ dahil ·
 *     Controller/ pages/ config/ alt dizinleri · bootstrap*.php · routes*.php)
 *     DB bağlantısı AÇAMAZ ve SQL çalıştıramaz.
 *        yasak: new DatabaseManager · new DatabaseConfig · new \PDO ·
 *               ConnectionFactory · DatabaseRegistry · IDatabaseManager ·
 *               getPdo( · ->prepare( · ->query( · ->execute( · beginTransaction(
 *
 *  2) REPOSITORY yuzeyleri (Repository/ alt dizinleri)
 *     DB bağlantısını KENDİSİ KURAMAZ.
 *        yasak: new DatabaseManager · new DatabaseConfig · new \PDO
 *        serbest (tek kaynak kurali): ConnectionFactory::getInstance()
 *          get-or-register — mevcut registry kaydini OKUR, yeni baglanti
 *          KURMAZ (onay 2026-10-24, home Music/PlaylistRepository ornegi).
 *        DI ile enjekte edilen IDatabaseManager tercih edilen yoldur.
 *
 * Kullanım:  php bin/db-access-guard.php
 * Çıkış:     0 = temiz (PASS) · 1 = ihlal var (BLOCK) · ihlaller dosya:satır listelenir.
 *
 * Yorum satirlari (yorum isaretli satirlar) taranmaz — yalniz kod satirlari.
 */

$kok = dirname(__DIR__);

/** Kapsamdaki backend subdomainleri (media CLI servisi bu guard'ın dışında — kendi veri katmanı). */
$subdomainler = [
    'auth.coremusic.net',
    'api.coremusic.net',
    'home.coremusic.net',
];

/** Prezentasyon yüzeyi desenleri (bu dosyalar DB'ye dokunamaz). */
$prezentasyonDesenleri = [
    'new\s+DatabaseManager\s*\(',
    'new\s+DatabaseConfig\s*\(',
    'new\s+\\\\?PDO\s*\(',
    'ConnectionFactory',
    'DatabaseRegistry',
    'IDatabaseManager',
    '->\s*getPdo\s*\(',
    '->\s*prepare\s*\(',
    '->\s*query\s*\(',
    '->\s*execute\s*\(',
    '->\s*beginTransaction\s*\(',
];

/** Repository yüzeyi desenleri (kendi bağlantısını kuramaz). */
$repositoryDesenleri = [
    'new\s+DatabaseManager\s*\(',
    'new\s+DatabaseConfig\s*\(',
    'new\s+\\\\?PDO\s*\(',
];

/**
 * Bir PHP dosyasının KOD satırlarını (yorumlar hariç) [satırNo => kod] döndürür.
 *
 * @return array<int, string>
 */
function kodSatirlari(string $dosya): array
{
    $icerik = file_get_contents($dosya);
    if ($icerik === false) {
        return [];
    }

    $satirlar = [];
    $blokYorum = false;
    foreach (explode("\n", $icerik) as $i => $ham) {
        $satir = rtrim($ham);
        $bosluk = ltrim($satir);

        if ($blokYorum) {
            if (str_contains($satir, '*/')) {
                $blokYorum = false;
            }
            continue;
        }
        if (str_starts_with($bosluk, '//') || str_starts_with($bosluk, '*') || str_starts_with($bosluk, '#')) {
            continue;
        }
        if (str_starts_with($bosluk, '/*')) {
            if (!str_contains($satir, '*/')) {
                $blokYorum = true;
            }
            continue;
        }

        // Satır-içi blok yorum başlangıcı varsa öncesini tara.
        $kod = $satir;
        $yorumPos = strpos($kod, '//');
        if ($yorumPos !== false) {
            $kod = substr($kod, 0, $yorumPos);
        }
        if (trim($kod) !== '') {
            $satirlar[$i + 1] = $kod;
        }
    }

    return $satirlar;
}

/** Bir dizindeki hedef yüzey dosyalarını toplar. */
function yuzeyDosyalari(string $dizin, string $yuzey): array
{
    if (!is_dir($dizin)) {
        return [];
    }

    $dosyalar = [];
    if ($yuzey === 'index') {
        // Tum giris noktalari: her index.php (v2/ dahil) + boot/route dosyalari
        // + config/ alt dizini. vendor/ haric tutulur (yeni genis tarama).
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dizin, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $yol = str_replace('\\', '/', $file->getPathname());
            if (str_contains($yol, '/vendor/') || str_contains($yol, '/node_modules/')) {
                continue;
            }
            $ad = $file->getFilename();
            if (
                $ad === 'index.php'
                || preg_match('/^(bootstrap|routes)[\w-]*\.php$/', $ad) === 1
                || str_contains($yol, '/config/')
            ) {
                $dosyalar[] = $file->getPathname();
            }
        }
        return $dosyalar;
    }

    $alt = $yuzey === 'controller' ? 'Controller' : 'pages';
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dizin, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $yol = str_replace('\\', '/', $file->getPathname());
            if (str_contains($yol, '/' . $alt . '/')) {
                $dosyalar[] = $file->getPathname();
            }
        }
    }
    return $dosyalar;
}

$ihlaller = [];
$taranan = 0;

foreach ($subdomainler as $sd) {
    $kokSD = $kok . '/' . $sd;
    if (!is_dir($kokSD)) {
        continue;
    }

    // 1) Prezentasyon yüzeyleri: index.php + Controller/ + pages/
    $prez = array_merge(
        yuzeyDosyalari($kokSD, 'index'),
        yuzeyDosyalari($kokSD, 'controller'),
        yuzeyDosyalari($kokSD, 'pages'),
    );
    foreach (array_unique($prez) as $dosya) {
        $taranan++;
        foreach (kodSatirlari($dosya) as $satirNo => $kod) {
            foreach ($prezentasyonDesenleri as $desen) {
                if (preg_match('/' . $desen . '/', $kod) === 1) {
                    $ihlaller[] = sprintf(
                        '[PREZENTASYON] %s:%d  ←  %s   (desen: %s)',
                        str_replace($kok . '/', '', $dosya),
                        $satirNo,
                        trim($kod),
                        $desen
                    );
                }
            }
        }
    }

    // 2) Repository yüzeyleri: kendi bağlantısını kuramaz
    $repoDir = $kokSD . '/include/Repository';
    if (!is_dir($repoDir)) {
        $repoDir = $kokSD . '/Repository';
    }
    if (is_dir($repoDir)) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($repoDir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $taranan++;
            foreach (kodSatirlari($file->getPathname()) as $satirNo => $kod) {
                foreach ($repositoryDesenleri as $desen) {
                    if (preg_match('/' . $desen . '/', $kod) === 1) {
                        $ihlaller[] = sprintf(
                            '[REPOSITORY]   %s:%d  ←  %s   (desen: %s)',
                            str_replace($kok . '/', '', $file->getPathname()),
                            $satirNo,
                            trim($kod),
                            $desen
                        );
                    }
                }
            }
        }
    }
}

echo "db-access-guard: {$taranan} dosya tarandi.\n";

if ($ihlaller === []) {
    echo "PASS — prezentasyon katmaninda DB erisimi yok; repository'ler kendi baglantisini kurmuyor.\n";
    exit(0);
}

echo 'BLOCK — ' . count($ihlaller) . " ihlal bulundu:\n";
foreach ($ihlaller as $ihlal) {
    echo '  ' . $ihlal . "\n";
}
exit(1);
