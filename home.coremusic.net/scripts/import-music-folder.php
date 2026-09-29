<?php declare(strict_types=1);

/**
 * scripts/import-music-folder.php — Müzik klasörü importörü (CLI)
 *
 * Kullanım:
 *   php scripts/import-music-folder.php
 *
 * Kapsam:
 *   1) MUSIC_LIBRARY_PATH klasörünü recursive tarar (mp3/flac/wav/m4a/ogg/aac)
 *   2) artist = dosyanın bulunduğu klasör (kök klasör ise "Bilinmeyen Sanatçı")
 *   3) coremusic_musics.artists / musics / music_files tablolarına yazar
 *   4) Idempotent: aynı dosya yolu veya aynı (artist, title) tekrar işlenmez
 *
 * Kurallar: ADR-002 (PDO prepared statements yalnız), SELECT * yok, ORM yok.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Bu script sadece CLI ile çalıştırılabilir.\n");
}

require __DIR__ . '/../autoload.php';

use CoreMusic\Config\EnvParser;
use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Database\DatabaseManager;
use CoreMusic\Security\UuidV7;

/* ─── Ortam ─── */
$envFile = dirname(__DIR__) . '/config/.env';
if (is_file($envFile)) {
    EnvParser::loadIntoEnv($envFile);
}

$env = static fn (string $key, string $default = ''): string =>
    trim((string)(($_ENV[$key] ?? getenv($key)) ?: $default));

$dbHost     = $env('DB_HOST', 'localhost');
$dbPort     = (int)$env('DB_PORT', '3306');
$dbUser     = $env('DB_USER', '');
$dbPassword = $env('DB_PASSWORD', '');
$dbName     = $env('DB_MUSIC_NAME', 'coremusic_musics');
$dbCharset  = $env('DB_CHARSET', 'utf8mb4');

$libraryPath = $env('MUSIC_LIBRARY_PATH', 'C:\\Users\\Bayram Ali\\Music');
$batchSize   = 500;

/* ─── Biçim haritaları ─── */
$extensions      = ['mp3' => true, 'flac' => true, 'wav' => true, 'm4a' => true, 'ogg' => true, 'aac' => true];
$formatMap       = ['mp3' => 'mp3', 'flac' => 'flac', 'wav' => 'wav', 'm4a' => 'aac', 'ogg' => 'ogg', 'aac' => 'aac'];
$qualityMap      = ['flac' => 'lossless', 'wav' => 'lossless'];

$startedAt = microtime(true);

/**
 * ASCII slug üretir (Türkçe karakterleri sadeleştirir).
 */
$slugify = static function (string $value, int $maxLen): string {
    $value = strtr($value, [
        'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g',
        'ı' => 'i', 'İ' => 'i', 'ö' => 'o', 'Ö' => 'o',
        'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u',
    ]);
    $value = mb_strtolower($value, 'UTF-8');
    $translit = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if (is_string($translit) && $translit !== '') {
        $value = $translit;
    }
    $slug = (string)preg_replace('/[^a-z0-9]+/', '-', $value);
    $slug = trim($slug, '-');
    if ($slug === '') {
        $slug = 'kayit';
    }
    if (strlen($slug) > $maxLen) {
        $slug = rtrim(substr($slug, 0, $maxLen), '-');
    }
    return $slug !== '' ? $slug : 'kayit';
};

/** Benzersiz slug üretir: çakışmada -2, -3 ... ekler. */
$uniqueSlug = static function (string $base, array &$used): string {
    $candidate = $base;
    $n = 2;
    while (isset($used[$candidate])) {
        $candidate = $base . '-' . $n;
        $n++;
    }
    $used[$candidate] = true;
    return $candidate;
};

/* ─── Klasör taraması ─── */
if (!is_dir($libraryPath)) {
    fwrite(STDERR, "HATA: Klasör bulunamadı: {$libraryPath}\n");
    exit(1);
}

$rootReal = rtrim((string)realpath($libraryPath), '\\/');
$files    = [];
$skippedOutside = 0;
$skippedSymlink = 0;

$directory = new RecursiveDirectoryIterator($rootReal, FilesystemIterator::SKIP_DOTS);
$iterator  = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

/** @var SplFileInfo $file */
foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    // Symlink dosyalar reddedilir (path traversal koruması)
    if ($file->isLink()) {
        $skippedSymlink++;
        continue;
    }
    $ext = strtolower($file->getExtension());
    if (!isset($extensions[$ext])) {
        continue;
    }
    $fullPath = (string)$file->getPathname();
    // Path traversal: ".." yol parçası içeren dosyalar reddedilir
    $pathSegments = preg_split('#[/\\\\]+#', $fullPath) ?: [];
    if (in_array('..', $pathSegments, true)) {
        $skippedOutside++;
        continue;
    }
    $files[] = [
        'path' => $fullPath,
        'dir'  => dirname($fullPath),
        'name' => $file->getBasename('.' . $file->getExtension()),
        'ext'  => $ext,
        'size' => (int)$file->getSize(),
    ];
}

$scannedAt = microtime(true);
printf("Tarama: %d dosya (%.1f sn) — klasör: %s\n", count($files), $scannedAt - $startedAt, $rootReal);
if ($skippedSymlink > 0) {
    printf("Symlink reddedildi: %d\n", $skippedSymlink);
}
if ($skippedOutside > 0) {
    printf("Kök dışı reddedildi: %d\n", $skippedOutside);
}

if ($files === []) {
    fwrite(STDERR, "HATA: Desteklenen dosya bulunamadı.\n");
    exit(1);
}

/* ─── DB bağlantısı (ADR-002: yalnız DatabaseManager üzerinden, prepared statements) ─── */
try {
    $db = new DatabaseManager(new DatabaseConfig(
        $dbHost,
        $dbName,
        $dbUser,
        $dbPassword,
        $dbPort,
        $dbCharset,
    ));
} catch (Throwable $e) {
    fwrite(STDERR, 'HATA: DB bağlantı hatası: ' . $e->getMessage() . "\n");
    exit(1);
}

/* ─── Mevcut kayıtları yükle (idempotency) ─── */
$artistByName      = [];   // name => binary id
$artistSlugs       = [];   // slug => true
$musicKeys         = [];   // HEX(artist_id) . '|' . title => binary id
$musicSlugs        = [];   // slug => true
$knownPaths        = [];   // file_path => true

foreach ($db->execute('SELECT id, name, slug FROM artists WHERE is_deleted = 0') as $row) {
    $artistByName[(string)$row['name']] = (string)$row['id'];
    $artistSlugs[(string)$row['slug']]  = true;
}
foreach ($db->execute('SELECT id, artist_id, slug, title FROM musics WHERE is_deleted = 0') as $row) {
    $musicKeys[bin2hex((string)$row['artist_id']) . '|' . (string)$row['title']] = (string)$row['id'];
    $musicSlugs[(string)$row['slug']] = true;
}
foreach ($db->execute('SELECT file_path FROM music_files WHERE is_deleted = 0') as $row) {
    $knownPaths[(string)$row['file_path']] = true;
}

$existingArtists = count($artistByName);
$existingMusics  = count($musicKeys);

/* ─── Hazırlayıcılar ─── */
$artistInsertSql =
    'INSERT INTO artists (id, name, slug) VALUES (UNHEX(:id), :name, :slug)';

$musicInsertTpl  = 'INSERT INTO musics (id, title, slug, artist_id) VALUES ';
$fileInsertTpl   = 'INSERT INTO music_files (id, music_id, file_format, file_path, file_size, is_primary, quality_level) VALUES ';

/* ─── Ana döngü ─── */
$pendingMusics = [];
$pendingFiles  = [];
$insertedArtists = 0;
$createdArtists  = 0;
$insertedMusics  = 0;
$insertedFiles   = 0;
$skippedPath     = 0;
$skippedTitle    = 0;
$skippedLongPath = 0;
$unknownArtist   = 'Bilinmeyen Sanatçı';

$flush = static function () use (&$pendingMusics, &$pendingFiles, $db, $musicInsertTpl, $fileInsertTpl, &$insertedMusics, &$insertedFiles): void {
    if ($pendingMusics !== []) {
        $parts   = [];
        $params  = [];
        foreach ($pendingMusics as $i => $row) {
            $parts[] = sprintf('(UNHEX(:m%d), :t%d, :s%d, UNHEX(:a%d))', $i, $i, $i, $i);
            $params['m' . $i] = $row['id'];
            $params['t' . $i] = $row['title'];
            $params['s' . $i] = $row['slug'];
            $params['a' . $i] = $row['artist_id'];
        }
        $db->write($musicInsertTpl . implode(', ', $parts), $params);
        $insertedMusics += count($pendingMusics);
        $pendingMusics = [];
    }

    if ($pendingFiles !== []) {
        $parts  = [];
        $params = [];
        foreach ($pendingFiles as $i => $row) {
            $parts[] = sprintf('(UNHEX(:f%d), UNHEX(:m%d), :fmt%d, :p%d, :sz%d, 1, :q%d)', $i, $i, $i, $i, $i, $i);
            $params['f' . $i]   = $row['id'];
            $params['m' . $i]   = $row['music_id'];
            $params['fmt' . $i] = $row['format'];
            $params['p' . $i]   = $row['path'];
            $params['sz' . $i]  = $row['size'];
            $params['q' . $i]   = $row['quality'];
        }
        $db->write($fileInsertTpl . implode(', ', $parts), $params);
        $insertedFiles += count($pendingFiles);
        $pendingFiles = [];
    }
};

$db->beginTransaction();

try {
    foreach ($files as $file) {
        /* --- artist --- */
        $artistName = basename($file['dir']);
        // Dosya kök klasörde ise (üst klasör yok) → Bilinmeyen Sanatçı
        if ($file['dir'] === $rootReal || $artistName === '') {
            $artistName = $unknownArtist;
        }

        $artistId = $artistByName[$artistName] ?? null;
        if ($artistId === null) {
            $artistId = UuidV7::generateBinary();
            $artistSlug = $uniqueSlug($slugify($artistName, 180), $artistSlugs);
            $db->write($artistInsertSql, ['id' => bin2hex($artistId), 'name' => $artistName, 'slug' => $artistSlug]);
            $artistByName[$artistName] = $artistId;
            $insertedArtists++;
            $createdArtists++;
        }

        /* --- idempotency --- */
        if (isset($knownPaths[$file['path']])) {
            $skippedPath++;
            continue;
        }
        $musicKey = bin2hex($artistId) . '|' . $file['name'];
        if (isset($musicKeys[$musicKey])) {
            $skippedTitle++;
            continue;
        }
        if (strlen($file['path']) > 500) {
            $skippedLongPath++;
            continue;
        }

        /* --- music + file --- */
        $musicId   = UuidV7::generateBinary();
        $musicSlug = $uniqueSlug($slugify($file['name'], 450), $musicSlugs);

        $pendingMusics[] = [
            'id'        => bin2hex($musicId),
            'title'     => $file['name'],
            'slug'      => $musicSlug,
            'artist_id' => bin2hex($artistId),
        ];
        $knownPaths[$file['path']]   = true;
        $musicKeys[$musicKey]        = $musicId;

        $pendingFiles[] = [
            'id'         => bin2hex(UuidV7::generateBinary()),
            'music_id'   => bin2hex($musicId),
            'format'     => $formatMap[$file['ext']],
            'path'       => $file['path'],
            'size'       => $file['size'],
            'quality'    => $qualityMap[$file['ext']] ?? 'high_320',
        ];

        if (count($pendingMusics) >= $batchSize || count($pendingFiles) >= $batchSize) {
            $flush();
        }
    }

    $flush();
    $db->commit();
} catch (Throwable $e) {
    $db->rollBack();
    fwrite(STDERR, 'HATA: Import başarısız (transaction geri alındı): ' . $e->getMessage() . "\n");
    exit(1);
}

$finishedAt = microtime(true);

/* ─── Sonuç sayımı (DB kanıtı) ─── */
$artistCount = (int)$db->execute('SELECT COUNT(*) AS c FROM artists')[0]['c'];
$musicCount  = (int)$db->execute('SELECT COUNT(*) AS c FROM musics')[0]['c'];
$fileCount   = (int)$db->execute('SELECT COUNT(*) AS c FROM music_files')[0]['c'];

echo "─── IMPORT RAPOR ───\n";
printf("Süre              : %.1f sn (tarama %.1f sn)\n", $finishedAt - $startedAt, $scannedAt - $startedAt);
printf("Taranan dosya     : %d\n", count($files));
printf("artists           : +%d (önceden %d → toplam %d)\n", $insertedArtists, $existingArtists, $artistCount);
printf("musics            : +%d (önceden %d → toplam %d)\n", $insertedMusics, $existingMusics, $musicCount);
printf("music_files       : +%d (toplam %d)\n", $insertedFiles, $fileCount);
printf("Atlanan (yol)     : %d (idempotent rerun)\n", $skippedPath);
printf("Atlanan (başlık)  : %d (aynı artist+title)\n", $skippedTitle);
printf("Atlanan (uzun yol): %d\n", $skippedLongPath);
printf("Yeni sanatçı      : %d\n", $createdArtists);
