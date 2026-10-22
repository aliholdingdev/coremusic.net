#!/usr/bin/env php
<?php declare(strict_types=1);

/**
 * duration-backfill — musics.duration_sec backfill CLI (ADR-002).
 *
 * SORUN
 *   coremusic_musics.musics.duration_sec import sırasında hiç yazılmadı
 *   (repo'da `INSERT INTO musics` yok). NULL süre → home UI'da 00:00:00
 *   (RecentTracksComponent::formatDuration NULL'u 00:00:00 yapar).
 *
 * ÇÖZÜM
 *   music_files.is_primary=1 dosyalarını okuyup süreyi hesaplar ve
 *   musics.duration_sec alanını doldurur.
 *
 * KULLANIM
 *   php bin/duration-backfill.php                  # dry-run (yazma yok)
 *   php bin/duration-backfill.php --commit         # DB'ye yazar
 *   php bin/duration-backfill.php --limit=500
 *   php bin/duration-backfill.php --format=csv     # rapor çıktısı
 *
 * FORMAT DESTEĞİ
 *   FLAC ......... STREAMINFO total_samples/sample_rate  → KESİN
 *   WAV/RIFF ..... fmt byte_rate + data chunk boyutu    → KESİN
 *   MP3 .......... Xing/VBRI frame count                → KESİN
 *                  yoksa CBR (dosya boyutu / bitrate)   → YAKLAŞIK
 *   M4A/OGG/AAC/DSD → ATLANIR (raporlanır; ffprobe gerekir)
 *
 * GÜVENLİK / GUARDRAIL
 *   - `.env` ASLA okunmaz/yazılmaz; DB bilgisi yalnız ortam değişkenlerinden.
 *   - SELECT * yok; PDO prepared statement (ADR-002).
 *   - Dosya I/O transaction dışında; yazma tek transaction'da (§4.3).
 *   - Varsayılan dry-run — DB'ye yazma için --commit ZORUNLU.
 *
 * ENV
 *   DB_HOST · DB_USER · DB_PASSWORD · DB_PORT · DB_CHARSET
 *   DB_MUSIC_NAME (varsayılan: coremusic_musics)
 *   MUSIC_LIBRARY_PATH (file_path göreli ise kök; yoksa C:\Users\Bayram Ali\Music)
 *
 * EXIT
 *   0 = başarılı · 1 = çalışma zamanı/DB hatası · 2 = kullanım hatası
 */

namespace {
    require_once dirname(__DIR__) . '/shared/vendor/autoload.php';
}

namespace CoreMusic\Console {

    use CoreMusic\Database\Config\DatabaseConfig;
    use CoreMusic\Database\DatabaseManager;

    final class UsageError extends \InvalidArgumentException {}

    /** Süre çözümleyici — format'a göre dosyadan saniye üretir. */
    final class DurationReader
    {
        /** @var list<int> MPEG1 Layer III bitrate (kbps), index 1..14 */
        private const BITRATE_V1 = [0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320];

        /** @var list<int> MPEG2/2.5 Layer III bitrate (kbps), index 1..14 */
        private const BITRATE_V2 = [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160];

        private const SAMPLE_RATE_V1 = [44100, 48000, 32000];
        private const SAMPLE_RATE_V2 = [22050, 24000, 16000];
        private const SAMPLE_RATE_V25 = [11025, 12000, 8000];

        /**
         * @return array{sec: int, exact: bool}|null null = format desteklenmiyor
         */
        public function read(string $path): ?array
        {
            $head = @file_get_contents($path, false, null, 0, 65536);
            if ($head === false || $head === '') {
                return null;
            }

            if (str_starts_with($head, 'fLaC')) {
                return $this->flac($head);
            }

            if (str_starts_with($head, 'RIFF') && str_starts_with(substr($head, 8, 4), 'WAVE')) {
                return $this->wav($path, $head);
            }

            if (str_starts_with($head, 'ID3') || $this->isMp3Frame($head, 0)) {
                $size = (int) @filesize($path);
                return $size > 0 ? $this->mp3($path, $head, $size) : null;
            }

            return null;
        }

        /** fLaC magic + STREAMINFO (blok tipi 0) → total_samples / sample_rate. */
        private function flac(string $head): ?array
        {
            if (strlen($head) < 42) {
                return null;
            }

            // Metadata block header: 1 byte (last<<7|type) + 3 byte uzunluk.
            $type   = ord($head[4]) & 0x7F;
            $length = (ord($head[5]) << 16) | (ord($head[6]) << 8) | ord($head[7]);
            if ($type !== 0 || $length < 34) {
                return null; // STREAMINFO ilk blok olmalı (spec zorunlu)
            }

            $w = unpack('N2', substr($head, 4 + 4 + 10, 8));
            if ($w === false) {
                return null;
            }

            $sampleRate   = ($w[1] >> 12) & 0xFFFFF;              // 20 bit
            $totalSamples = (($w[1] & 0xFFF) << 32) | $w[2];      // 36 bit
            if ($sampleRate <= 0 || $totalSamples <= 0) {
                return null;
            }

            return ['sec' => (int) round($totalSamples / $sampleRate), 'exact' => true];
        }

        /** RIFF/WAVE: fmt chunk byte_rate × data chunk süresi. */
        private function wav(string $path, string $head): ?array
        {
            $offset = 12;
            $byteRate = 0;
            $dataSize = 0;
            $len = strlen($head);

            while ($offset + 8 <= $len) {
                $id   = substr($head, $offset, 4);
                $size = unpack('V', substr($head, $offset + 4, 4))[1];

                if ($id === 'fmt ' && $size >= 16) {
                    // WAVE_FORMAT_EXTENSIBLE (0xFFFE) için alt format kontrolü yok —
                    // byte_rate her iki biçimde de geçerli.
                    $byteRate = unpack('V', substr($head, $offset + 8 + 8, 4))[1];
                } elseif ($id === 'data') {
                    $dataSize = $size;
                    if ($dataSize === 0) {
                        // Streaming WAV: gerçek boyutu diskten al.
                        $real = (int) @filesize($path);
                        $dataSize = $real > $offset + 8 ? $real - ($offset + 8) : 0;
                    }
                    break;
                }

                // Chunk'lar word-aligned (odd size → 1 byte padding).
                $offset += 8 + $size + ($size % 2);
            }

            if ($byteRate <= 0 || $dataSize <= 0) {
                return null;
            }

            return ['sec' => (int) round($dataSize / $byteRate), 'exact' => true];
        }

        /**
         * MP3: ID3v2 atlanır → Xing/VBRI varsa frame count (kesin),
         * yoksa CBR tahmini (dosya boyutu / bitrate).
         */
        private function mp3(string $path, string $head, int $fileSize): ?array
        {
            $audioStart = 0;

            // ID3v2: 'ID3' + version(2) + flags(1) + synchsafe size(4) = 10 byte başlık.
            if (str_starts_with($head, 'ID3') && strlen($head) >= 10) {
                $size = ((ord($head[6]) & 0x7F) << 21)
                    | ((ord($head[7]) & 0x7F) << 14)
                    | ((ord($head[8]) & 0x7F) << 7)
                    | (ord($head[9]) & 0x7F);
                $audioStart = 10 + $size;
            }

            if ($audioStart + 4 > strlen($head) || !$this->isMp3Frame($head, $audioStart)) {
                return null; // ilk kare senkronu bulunamadı (M4A/OGG vb.)
            }

            $b2 = ord($head[$audioStart + 1]);
            $b3 = ord($head[$audioStart + 2]);

            $versionBits = ($b2 >> 3) & 0x03;   // 0=2.5, 2=2, 3=1
            $layerBits   = ($b2 >> 1) & 0x03;   // 1=Layer III
            $brIndex     = ($b3 >> 4) & 0x0F;
            $srIndex     = ($b3 >> 2) & 0x03;
            $channelMode = (ord($head[$audioStart + 3]) >> 6) & 0x03;

            if ($layerBits !== 1 || $brIndex === 0 || $brIndex === 15 || $srIndex === 3) {
                return null; // Layer I/II ya da free/bad bitrate — desteklenmiyor
            }

            $isMpeg1 = $versionBits === 3;
            // $srIndex 0..2 (3 → return null), $brIndex 1..14 (0/15 → return null) garanti.
            $sampleRate = match ($versionBits) {
                3 => self::SAMPLE_RATE_V1[$srIndex],
                2 => self::SAMPLE_RATE_V2[$srIndex],
                0 => self::SAMPLE_RATE_V25[$srIndex],
                default => 0, // versionBits=1 (yedek) — desteklenmiyor
            };
            if ($sampleRate <= 0) {
                return null;
            }

            $bitrateKbps = $isMpeg1
                ? self::BITRATE_V1[$brIndex]
                : self::BITRATE_V2[$brIndex];

            $samplesPerFrame = $isMpeg1 ? 1152 : 576;
            $sideInfo = match (true) {
                $isMpeg1 && $channelMode === 3 => 17,
                $isMpeg1 => 32,
                $channelMode === 3 => 9,
                default => 17,
            };

            // Xing/Info (MP3) veya VBRI (Fraunhofer) — frame count'tan kesin süre.
            $xingOffset = $audioStart + 4 + $sideInfo;
            $tag = substr($head, $xingOffset, 4);
            if ($tag === 'Xing' || $tag === 'Info') {
                $flags = unpack('N', substr($head, $xingOffset + 4, 4))[1];
                if (($flags & 0x01) !== 0) { // frames flag
                    $frames = unpack('N', substr($head, $xingOffset + 8, 4))[1];
                    if ($frames > 0) {
                        return [
                            'sec' => (int) round($frames * $samplesPerFrame / $sampleRate),
                            'exact' => true,
                        ];
                    }
                }
            }

            $vbriOffset = $audioStart + 4 + 32; // VBRI her zaman sabit 32 byte sonra
            if (substr($head, $vbriOffset, 4) === 'VBRI') {
                $frames = unpack('N', substr($head, $vbriOffset + 14, 4))[1];
                if ($frames > 0) {
                    return [
                        'sec' => (int) round($frames * $samplesPerFrame / $sampleRate),
                        'exact' => true,
                    ];
                }
            }

            // CBR tahmini: ID3v1 (son 128 byte 'TAG') varsa byte'a dahil edilmez.
            $id3v1 = 0;
            if ($fileSize > $audioStart + 128) {
                $tail = @file_get_contents($path, false, null, $fileSize - 128, 128);
                if ($tail !== false && str_starts_with($tail, 'TAG')) {
                    $id3v1 = 128;
                }
            }

            $audioBytes = $fileSize - $audioStart - $id3v1;
            if ($audioBytes <= 0) {
                return null;
            }

            $seconds = (int) round($audioBytes * 8 / ($bitrateKbps * 1000));

            return ['sec' => max(1, $seconds), 'exact' => false];
        }

        private function isMp3Frame(string $head, int $offset): bool
        {
            if ($offset + 2 > strlen($head)) {
                return false;
            }

            return ord($head[$offset]) === 0xFF && (ord($head[$offset + 1]) & 0xE0) === 0xE0;
        }
    }

    /**
     * `duration:backfill` komutu.
     */
    final class DurationBackfillCommand
    {
        public const EXIT_OK    = 0;
        public const EXIT_ERROR = 1;
        public const EXIT_USAGE = 2;

        private const ALLOWED_OPTIONS = ['help', 'commit', 'limit', 'format'];

        private const USAGE = <<<TXT
        Kullanım:
          php bin/duration-backfill.php [--commit] [--limit=<n>] [--format=text|csv]

        Varsayılan DRY-RUN — DB'ye yazmak için --commit gereklidir.
        Format: FLAC/WAV kesin · MP3 (Xing/VBRI) kesin · MP3 CBR yaklaşık · M4A/OGG atlanır.
        Env   : DB_HOST · DB_USER · DB_PASSWORD · DB_PORT · DB_MUSIC_NAME · MUSIC_LIBRARY_PATH
        Exit  : 0 başarılı · 1 DB/çalışma zamanı · 2 kullanım hatası
        TXT;

        private const DRY_RUN_NOTICE = '*** DRY-RUN — hiçbir satır yazılmadı (yazmak için --commit) ***';

        /**
         * @param list<string> $argv
         */
        public static function main(array $argv): int
        {
            try {
                $opts = self::parse($argv);
            } catch (UsageError $e) {
                fwrite(STDERR, "HATA: {$e->getMessage()}\n\n" . self::USAGE . "\n");

                return self::EXIT_USAGE;
            }

            if (!empty($opts['help'])) {
                fwrite(STDOUT, self::USAGE . "\n");

                return self::EXIT_OK;
            }

            try {
                return self::run($opts);
            } catch (UsageError $e) {
                fwrite(STDERR, "HATA: {$e->getMessage()}\n\n" . self::USAGE . "\n");

                return self::EXIT_USAGE;
            } catch (\Throwable $e) {
                fwrite(STDERR, 'HATA: ' . $e::class . ': ' . $e->getMessage() . "\n");

                return self::EXIT_ERROR;
            }
        }

        /**
         * @param array<string, mixed> $opts
         */
        private static function run(array $opts): int
        {
            $commit  = !empty($opts['commit']);
            $format  = strtolower((string) ($opts['format'] ?? 'text'));
            $limit   = isset($opts['limit']) ? max(1, (int) $opts['limit']) : 0;

            if (!in_array($format, ['text', 'csv'], true)) {
                throw new UsageError('--format=text|csv olmalıdır');
            }

            $db = self::database();

            // 1) duration_sec NULL olan ve primary dosyası olan satırlar.
            $sql = 'SELECT HEX(m.id) AS music_hex, m.title AS title, '
                 . 'f.file_path AS file_path, f.file_format AS file_format '
                 . 'FROM musics m '
                 . 'INNER JOIN music_files f ON f.music_id = m.id '
                 . "WHERE m.duration_sec IS NULL AND m.is_deleted = 0 AND f.is_deleted = 0 "
                 . 'AND f.is_primary = 1 '
                 . 'ORDER BY m.created_at ASC, m.id ASC';
            if ($limit > 0) {
                $sql .= ' LIMIT ' . $limit;
            }

            $rows = $db->execute($sql);

            if ($rows === []) {
                fwrite(STDOUT, "duration_sec NULL olan satır yok — yapılacak iş yok.\n");

                return self::EXIT_OK;
            }

            $reader    = new DurationReader();
            $library   = self::libraryPath();
            $pending   = [];   // [music_hex => süre]
            $skipped   = [];
            $missing   = [];
            $exact     = 0;
            $approx    = 0;

            // 2) Dosya I/O — transaction DIŞINDA (skill §4.3).
            foreach ($rows as $row) {
                $path = self::resolvePath((string) $row['file_path'], $library);

                if ($path === null) {
                    $missing[] = [
                        'hex'   => (string) $row['music_hex'],
                        'title' => (string) $row['title'],
                        'path'  => (string) $row['file_path'],
                    ];
                    continue;
                }

                $result = $reader->read($path);
                if ($result === null) {
                    $skipped[] = [
                        'hex'    => (string) $row['music_hex'],
                        'title'  => (string) $row['title'],
                        'format' => (string) $row['file_format'],
                    ];
                    continue;
                }

                $pending[(string) $row['music_hex']] = $result['sec'];
                $result['exact'] ? $exact++ : $approx++;
            }

            // 3) Rapor.
            self::report($format, count($rows), $pending, $exact, $approx, $skipped, $missing);

            // 4) Yazma — tek transaction; hata → rollback.
            if (!$commit || $pending === []) {
                if ($commit && $pending === []) {
                    fwrite(STDOUT, "Yazılacak satır yok.\n");
                }

                return self::EXIT_OK;
            }

            $db->beginTransaction();
            try {
                foreach ($pending as $hex => $sec) {
                    $db->write(
                        'UPDATE musics SET duration_sec = :sec WHERE HEX(id) = :id AND is_deleted = 0',
                        ['sec' => $sec, 'id' => $hex]
                    );
                }
                $db->commit();
            } catch (\Throwable $e) {
                $db->rollBack();
                throw $e;
            }

            fwrite(STDOUT, 'Güncellendi: ' . count($pending) . " satır (duration_sec)\n");

            return self::EXIT_OK;
        }

        /**
         * @param array<string, int> $pending
         * @param list<array{hex: string, title: string, format: string}> $skipped
         * @param list<array{hex: string, title: string, path: string}> $missing
         */
        private static function report(
            string $format,
            int $total,
            array $pending,
            int $exact,
            int $approx,
            array $skipped,
            array $missing
        ): void {
            if ($format === 'csv') {
                fwrite(STDOUT, "music_hex,duration_sec,kaynak\n");
                foreach ($pending as $hex => $sec) {
                    fwrite(STDOUT, "{$hex},{$sec}\n");
                }

                return;
            }

            fwrite(STDOUT, "Toplam aday satır : {$total}\n");
            fwrite(STDOUT, "Hesaplandı        : " . count($pending)
                . " (kesin {$exact} · yaklaşık {$approx})\n");
            fwrite(STDOUT, "Dosya bulunamadı  : " . count($missing) . "\n");
            fwrite(STDOUT, "Format desteklenmiyor: " . count($skipped) . "\n");

            if ($skipped !== []) {
                fwrite(STDOUT, "\nAtlananlar (M4A/OGG/AAC/DSD — ffprobe gerekir):\n");
                foreach (array_slice($skipped, 0, 20) as $s) {
                    fwrite(STDOUT, "  - {$s['title']} [{$s['format']}]\n");
                }
                if (count($skipped) > 20) {
                    fwrite(STDOUT, '  ... ve ' . (count($skipped) - 20) . " satır daha\n");
                }
            }

            if ($missing !== []) {
                fwrite(STDOUT, "\nDosyası olmayanlar (file_path çözülemedi):\n");
                foreach (array_slice($missing, 0, 20) as $m) {
                    fwrite(STDOUT, "  - {$m['title']} → {$m['path']}\n");
                }
                if (count($missing) > 20) {
                    fwrite(STDOUT, '  ... ve ' . (count($missing) - 20) . " satır daha\n");
                }
            }

            fwrite(STDOUT, "\n" . self::DRY_RUN_NOTICE . "\n");
        }

        /**
         * file_path'i mutlak ya da MUSIC_LIBRARY_PATH kökünden dener.
         */
        private static function resolvePath(string $filePath, string $library): ?string
        {
            if ($filePath === '') {
                return null;
            }

            // 1) Mutlak yol.
            if (is_file($filePath)) {
                return $filePath;
            }

            // 2) Library kökü + göreli.
            if ($library !== '') {
                $joined = rtrim($library, "\\/") . DIRECTORY_SEPARATOR
                    . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath), DIRECTORY_SEPARATOR);
                if (is_file($joined)) {
                    return $joined;
                }
            }

            return null;
        }

        private static function env(string $key, string $default = ''): string
        {
            $value = getenv($key);

            return ($value === false || $value === '') ? $default : $value;
        }

        /** DB_MUSIC_NAME env'i — MusicRepository::fromEnvironment ile aynı sözleşme. */
        private static function database(): DatabaseManager
        {
            return new DatabaseManager(new DatabaseConfig(
                self::env('DB_HOST', 'localhost'),
                self::env('DB_MUSIC_NAME', 'coremusic_musics'),
                self::env('DB_USER', ''),
                self::env('DB_PASSWORD', ''),
                (int) self::env('DB_PORT', '3306'),
                self::env('DB_CHARSET', 'utf8mb4'),
            ));
        }

        private static function libraryPath(): string
        {
            $path = self::env('MUSIC_LIBRARY_PATH');

            return $path !== '' ? $path : 'C:\\Users\\Bayram Ali\\Music';
        }

        /**
         * @param list<string> $argv
         * @return array<string, mixed>
         */
        public static function parse(array $argv): array
        {
            $opts = [];
            $args = array_slice($argv, 1);

            for ($i = 0, $n = count($args); $i < $n; $i++) {
                $arg = (string) $args[$i];

                if ($arg === '--help' || $arg === '-h') {
                    $opts['help'] = true;
                    continue;
                }

                if (!str_starts_with($arg, '--')) {
                    throw new UsageError("Beklenmeyen argüman: {$arg}");
                }

                $body = substr($arg, 2);
                if (str_contains($body, '=')) {
                    [$key, $value] = explode('=', $body, 2);
                } else {
                    $key   = $body;
                    $value = true;
                    if (isset($args[$i + 1]) && !str_starts_with((string) $args[$i + 1], '--')) {
                        $value = (string) $args[++$i];
                    }
                }

                if ($key === '' || !in_array($key, self::ALLOWED_OPTIONS, true)) {
                    throw new UsageError("Bilinmeyen seçenek: --{$key}");
                }

                if ($value === true && $key !== 'help' && $key !== 'commit') {
                    throw new UsageError("--{$key} için değer gerekiyor");
                }

                $opts[$key] = $value;
            }

            return $opts;
        }
    }
}

namespace {
    // Yalnızca dosya doğrudan çalıştırıldığında (require edildiğinde DEĞİL).
    if (isset($_SERVER['SCRIPT_FILENAME'])
        && realpath((string) $_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)
    ) {
        exit(\CoreMusic\Console\DurationBackfillCommand::main($_SERVER['argv'] ?? []));
    }
}
