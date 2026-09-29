<?php declare(strict_types=1);

namespace CoreMusic\Home\Stream;

use CoreMusic\Home\Repository\MusicRepository;
use CoreMusic\PageRouter\PageRouterHelper;
use CoreMusic\Session\SessionBootstrapper;

/**
 * MusicStreamHandler — GET /stream/{musicId} (byte-range destekli ses akışı)
 *
 * Sözleşme:
 *   - Giriş ZORUNLU → değilse 302 /login (AuthGuard ile aynı session anahtarları)
 *   - Yalnız MUSIC_LIBRARY_PATH altında gerçek dosya servis edilir
 *     (`..` parçası, symlink ve kök-dışı realpath → 403)
 *   - HTTP Range: tek aralık → 206 Partial Content (seek için şart)
 *   - ETag + Last-Modified + Accept-Ranges: bytes
 *
 * Kurallar: ADR-002 (PDO prepared statements), SELECT * yok.
 */
final class MusicStreamHandler
{
    /** Uzantı → Content-Type */
    private const MIME_BY_EXT = [
        'mp3'  => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav'  => 'audio/wav',
        'm4a'  => 'audio/mp4',
        'ogg'  => 'audio/ogg',
        'aac'  => 'audio/aac',
        'dsd'  => 'audio/dsd',
        'mqa'  => 'audio/flac',
    ];

    /** file_format enum → Content-Type (uzantı yoksa fallback) */
    private const MIME_BY_FORMAT = [
        'mp3'  => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav'  => 'audio/wav',
        'aac'  => 'audio/mp4',
        'ogg'  => 'audio/ogg',
        'dsd'  => 'audio/dsd',
        'mqa'  => 'audio/flac',
    ];

    private const CHUNK_BYTES = 1048576; // 1 MiB

    private function __construct(
        private readonly string $libraryPath,
        private readonly MusicRepository $repository,
    ) {
    }

    /** bootstrap.php special-route girişi — yanıt gönderir ve çıkar. */
    public static function dispatch(string $musicIdHex): never
    {
        (new self(self::libraryPath(), MusicRepository::fromEnvironment()))->run(strtolower($musicIdHex));
    }

    private function run(string $musicIdHex): never
    {
        $this->authorize();

        $row = $this->findFile($musicIdHex);
        if ($row === null) {
            $this->fail(404, 'Kayıt bulunamadı');
        }

        $path = $this->resolvePath((string)$row['file_path']);
        $size = (int)$row['file_size'];
        if (is_file($path)) {
            $diskSize = (int)filesize($path);
            if ($diskSize > 0) {
                $size = $diskSize;
            }
        }

        $mime      = $this->contentType($path, (string)$row['file_format']);
        $mtime     = (int)(is_file($path) ? filemtime($path) : time());
        $etag      = '"' . md5($path . '|' . $size . '|' . $mtime) . '"';
        $lastMod   = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';

        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

        // Koşullu istekler
        if ($this->isNotModified($etag, $mtime)) {
            $this->emitNotModified($etag, $lastMod);
            exit;
        }

        $range = $this->parseRange((string)($_SERVER['HTTP_RANGE'] ?? ''), $size);

        $this->emitBaseHeaders($mime, $range['length'], $etag, $lastMod);

        if ($range['status'] === 416) {
            header('Content-Range: bytes */' . $size);
            http_response_code(416);
            exit;
        }

        if ($range['status'] === 206) {
            header('Content-Range: bytes ' . $range['start'] . '-' . $range['end'] . '/' . $size);
            http_response_code(206);
        } else {
            http_response_code(200);
        }

        if ($method === 'HEAD') {
            exit;
        }

        $this->sendBody($path, $range['start'], $range['length']);
    }

    /* ─────────────────────────── Auth ─────────────────────────── */

    /**
     * home'un auth guard'ı (PageRouterHelper = AuthGuard'ın baktığı session anahtarları).
     * ADR-008 bypass davranışı BypassAuthMiddleware ile birebir — fail-closed.
     */
    private function authorize(): void
    {
        SessionBootstrapper::ensureStarted();

        $helper = new PageRouterHelper();

        if (!$helper->checkAuthenticated() && self::bypassActive()) {
            $uuid = defined('BYPASS_USER_UUID') ? (string)BYPASS_USER_UUID : '';
            $role = defined('BYPASS_ROLE') ? (string)BYPASS_ROLE : '';
            $user = defined('BYPASS_USERNAME') ? (string)BYPASS_USERNAME : '';
            if ($uuid !== '' && $role !== '' && $user !== '') {
                $_SESSION['MM_UserID']      = $uuid;
                $_SESSION['MM_UserRole']    = $role;
                $_SESSION['MM_Username']    = $user;
                $_SESSION['MM_Permissions'] = [];
            }
        }

        if (!$helper->checkAuthenticated()) {
            SessionBootstrapper::writeClose();
            header('Location: /login', true, 302);
            exit;
        }

        // Streaming sırasında session kilidi tutulmasın
        SessionBootstrapper::writeClose();
    }

    private static function bypassActive(): bool
    {
        $force = defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS;
        $test  = defined('TEST_MODE') && TEST_MODE;
        return $force || $test;
    }

    /* ─────────────────────────── Veri ─────────────────────────── */

    /** @return array{file_path: string, file_format: string, file_size: int}|null */
    private function findFile(string $musicIdHex): ?array
    {
        return $this->repository->findPrimaryFile($musicIdHex);
    }

    /**
     * Dosya yolunu doğrular: MUSIC_LIBRARY_PATH altında olmalı.
     * `..` parçası, symlink ve realpath kaçışı → 403.
     */
    private function resolvePath(string $stored): string
    {
        $trimmed = trim($stored);
        if ($trimmed === '' || str_contains($trimmed, "\0")) {
            $this->fail(404, 'Dosya bulunamadı');
        }

        $segments = preg_split('#[/\\\\]+#', $trimmed) ?: [];
        if (in_array('..', $segments, true)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        if (is_link($trimmed)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        $real = realpath($trimmed);
        if ($real === false || !is_file($real)) {
            $this->fail(404, 'Dosya bulunamadı');
        }

        $root = realpath($this->libraryPath);
        if ($root === false) {
            $this->fail(404, 'Kütüphane bulunamadı');
        }

        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
        if (!str_starts_with($real, $prefix)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        if (!is_readable($real)) {
            $this->fail(403, 'Erişim reddedildi');
        }

        return $real;
    }

    private function contentType(string $path, string $format): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return self::MIME_BY_EXT[$ext] ?? self::MIME_BY_FORMAT[$format] ?? 'application/octet-stream';
    }

    /* ─────────────────────────── Range ─────────────────────────── */

    /**
     * Tek aralık destekli. Çoklu aralık yok sayılır (200 tam gövde).
     *
     * @return array{status: int, start: int, end: int, length: int}
     */
    private function parseRange(string $header, int $size): array
    {
        $full = ['status' => 200, 'start' => 0, 'end' => $size > 0 ? $size - 1 : 0, 'length' => $size];
        $header = trim($header);

        if ($header === '' || $size === 0 || str_contains($header, ',')) {
            return $full;
        }

        if (preg_match('/^bytes=(\d*)-(\d*)$/i', $header, $m) !== 1) {
            return $full; // çözümlenemeyen header yok sayılır (RFC 9110)
        }

        $startRaw = $m[1];
        $endRaw   = $m[2];

        if ($startRaw === '' && $endRaw === '') {
            return $full;
        }

        if ($startRaw === '') {
            // Suffix range: bytes=-N (son N bayt)
            $suffix = (int)$endRaw;
            if ($suffix <= 0) {
                return ['status' => 416, 'start' => 0, 'end' => 0, 'length' => 0];
            }
            $start = max(0, $size - $suffix);
            $end   = $size - 1;
        } else {
            $start = (int)$startRaw;
            $end   = $endRaw === '' ? $size - 1 : min((int)$endRaw, $size - 1);
        }

        if ($start >= $size || $start > $end) {
            return ['status' => 416, 'start' => 0, 'end' => 0, 'length' => 0];
        }

        return ['status' => 206, 'start' => $start, 'end' => $end, 'length' => $end - $start + 1];
    }

    private function isNotModified(string $etag, int $mtime): bool
    {
        $ifNoneMatch = trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
        if ($ifNoneMatch !== '') {
            if ($ifNoneMatch === '*') {
                return true;
            }
            foreach (explode(',', $ifNoneMatch) as $candidate) {
                $candidate = trim($candidate);
                $candidate = preg_replace('/^W\//', '', $candidate) ?? $candidate;
                if ($candidate === $etag) {
                    return true;
                }
            }
            return false;
        }

        $ifModifiedSince = (string)($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '');
        if ($ifModifiedSince !== '') {
            $since = strtotime($ifModifiedSince);
            if ($since !== false && $mtime <= $since) {
                return true;
            }
        }

        return false;
    }

    /* ─────────────────────────── Yanıt ─────────────────────────── */

    private function emitNotModified(string $etag, string $lastMod): never
    {
        if (function_exists('ini_set')) {
            @ini_set('zlib.output_compression', '0');
        }
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        header('ETag: ' . $etag);
        header('Last-Modified: ' . $lastMod);
        header('Cache-Control: private, max-age=0, must-revalidate');
        http_response_code(304);
        exit;
    }

    private function emitBaseHeaders(string $mime, int $length, string $etag, string $lastMod): void
    {
        if (function_exists('ini_set')) {
            @ini_set('zlib.output_compression', '0');
        }
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
        @ignore_user_abort(true);

        header_remove('Content-Type');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $length);
        header('ETag: ' . $etag);
        header('Last-Modified: ' . $lastMod);
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Content-Disposition: inline');
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . $mime);
    }

    private function sendBody(string $path, int $start, int $length): never
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            $this->fail(403, 'Erişim reddedildi');
        }

        if ($start > 0) {
            fseek($handle, $start);
        }

        $remaining = $length;
        while ($remaining > 0 && !feof($handle)) {
            $chunk = fread($handle, min(self::CHUNK_BYTES, $remaining));
            if ($chunk === false || $chunk === '') {
                break;
            }
            echo $chunk;
            $remaining -= strlen($chunk);
            flush();
        }

        fclose($handle);
        exit;
    }

    private function fail(int $status, string $message): never
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: text/plain; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-store');
        }
        echo $message;
        exit;
    }

    /* ─────────────────────────── Bağımlılık ─────────────────────────── */

    private static function libraryPath(): string
    {
        $path = defined('MUSIC_LIBRARY_PATH')
            ? (string)MUSIC_LIBRARY_PATH
            : (string)(($_ENV['MUSIC_LIBRARY_PATH'] ?? getenv('MUSIC_LIBRARY_PATH')) ?: '');

        return $path !== '' ? $path : 'C:\\Users\\Bayram Ali\\Music';
    }
}
