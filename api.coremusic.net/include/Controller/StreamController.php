<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Repository\IMusicRepository;

/**
 * StreamController — HTTP 206 Partial Content ses/video medya akışı.
 *
 * home.coremusic.net'deki MusicStreamHandler mantığını merkezi API Gateway'e taşır.
 * RFC 9110 Range isteklerini ve koşullu önbellek başlıklarını (ETag/Last-Modified) yönetir.
 */
final class StreamController
{
    private const MIME_BY_EXT = [
        'mp3'  => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav'  => 'audio/wav',
        'm4a'  => 'audio/mp4',
        'aac'  => 'audio/aac',
        'ogg'  => 'audio/ogg',
        'wma'  => 'audio/x-ms-wma',
        'mp4'  => 'video/mp4',
        'mkv'  => 'video/x-matroska',
        'webm' => 'video/webm',
        'avi'  => 'video/x-msvideo',
    ];

    private const CHUNK_BYTES = 1048576; // 1 MiB

    public function __construct(
        private readonly IMusicRepository $musicRepository,
        private readonly string $libraryPath = '',
    ) {}

    /**
     * Gateway dispatch arayüzü (ApiContainerBase::handle).
     *
     * F8 kaydı: routes.php'deki '/api/{version}/stream/{id}' kaydı contract-first
     * ilanıdır; gerçek istek index.php §1.1 byte-path'inde (Gateway ÖNCESİ)
     * yakalanır. Buradaki id çözümlemesi yalnız RouteTable üzerinden gelen
     * (literal '{id}' — exact-match olduğu için gerçek id bu satıra düşmez)
     * akış içindir. stream() asla normal dönüş yapmaz.
     *
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     * @return array<string, mixed>
     */
    public function handle(array $route, array $request): array
    {
        preg_match(
            '#(?:/api/v\d+)?/stream/([a-f0-9]{32})$#i',
            (string) ($request['uri'] ?? ''),
            $matches
        );
        $this->stream($matches[1] ?? '');
    }

    public function stream(string $musicIdHex): never
    {
        $musicIdHex = strtolower(trim($musicIdHex));
        if (strlen($musicIdHex) !== 32 || !ctype_xdigit($musicIdHex)) {
            $this->fail(400, 'STREAM_ID_INVALID', 'Geçersiz müzik ID biçimi');
        }

        $row = $this->musicRepository->findPrimaryFile($musicIdHex);
        if ($row === null) {
            $this->fail(404, 'STREAM_NOT_FOUND', 'Müzik kaydı bulunamadı');
        }

        // Dosya yolunu belirle
        $filePath = (string) ($row['file_path'] ?? '');
        $path = $this->resolvePath($filePath);
        $size = (int) ($row['file_size'] ?? 0);

        if (is_file($path)) {
            $diskSize = (int) filesize($path);
            if ($diskSize > 0) {
                $size = $diskSize;
            }
        }

        $format = (string) ($row['file_format'] ?? 'mp3');
        $mime = $this->contentType($path, $format);
        $mtime = (int) (is_file($path) ? filemtime($path) : time());
        $etag = '"' . md5($path . '|' . $size . '|' . $mtime) . '"';
        $lastMod = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';

        // Koşullu istek denetimi (If-None-Match) — RFC 9110: çoklu değer, W/ öneki, * kabul
        $ifNoneMatch = trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
        if ($ifNoneMatch !== '' && $this->etagMatches($ifNoneMatch, $etag)) {
            http_response_code(304);
            header('ETag: ' . $etag);
            header('Last-Modified: ' . $lastMod);
            header('Cache-Control: private, max-age=86400');
            exit;
        }

        $range = $this->parseRange((string) ($_SERVER['HTTP_RANGE'] ?? ''), $size);

        // Standart Başlıklar
        header('Content-Type: ' . $mime);
        header('Accept-Ranges: bytes');
        header('ETag: ' . $etag);
        header('Last-Modified: ' . $lastMod);
        header('Cache-Control: private, max-age=86400');
        header('X-Content-Type-Options: nosniff');

        if ($range['status'] === 416) {
            header('Content-Range: bytes */' . $size);
            http_response_code(416);
            exit;
        }

        if ($range['status'] === 206) {
            http_response_code(206);
            header(sprintf('Content-Range: bytes %d-%d/%d', $range['start'], $range['end'], $size));
            header('Content-Length: ' . (string) $range['length']);
        } else {
            http_response_code(200);
            header('Content-Length: ' . (string) $size);
        }

        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'HEAD') {
            exit;
        }

        $this->pump($path, $range['start'], $range['length']);
        exit;
    }

    private function pump(string $path, int $start, int $length): void
    {
        if ($length <= 0) {
            return;
        }

        $fp = @fopen($path, 'rb');
        if ($fp === false) {
            $this->fail(500, 'STREAM_READ_ERROR', 'Dosya okunamadı');
        }

        if ($start > 0 && fseek($fp, $start, SEEK_SET) !== 0) {
            fclose($fp);
            $this->fail(500, 'STREAM_SEEK_ERROR', 'Dosya konumu ayarlanamadı');
        }

        $remaining = $length;
        while ($remaining > 0 && !feof($fp)) {
            $readBytes = min(self::CHUNK_BYTES, $remaining);
            $buffer = fread($fp, $readBytes);
            if ($buffer === false || $buffer === '') {
                break;
            }

            echo $buffer;
            $remaining -= strlen($buffer);

            if (ob_get_level() > 0) {
                @ob_flush();
            }
            flush();

            if (connection_aborted() === 1) {
                break;
            }
        }

        fclose($fp);
    }

    /**
     * If-None-Match karşılaştırması (RFC 9110 §13.1.2):
     *   *, çoklu değer (virgül), W/ öneki, tırnak farkı — hepsi eşleşir.
     */
    private function etagMatches(string $headerValue, string $etag): bool
    {
        if ($headerValue === '*') {
            return true;
        }

        $bareEtag = preg_replace('/^W\//', '', $etag) ?? $etag;
        $bareEtag = trim($bareEtag, '"');

        foreach (explode(',', $headerValue) as $candidate) {
            $candidate = trim($candidate);
            $candidate = preg_replace('/^W\//', '', $candidate) ?? $candidate;
            $candidate = trim($candidate, '"');

            if ($candidate === $bareEtag) {
                return true;
            }
        }

        return false;
    }

    private function parseRange(string $header, int $size): array
    {
        $full = ['status' => 200, 'start' => 0, 'end' => $size > 0 ? $size - 1 : 0, 'length' => $size];
        $header = trim($header);

        if ($header === '' || $size === 0 || str_contains($header, ',')) {
            return $full;
        }

        if (preg_match('/^bytes=(\d*)-(\d*)$/i', $header, $m) !== 1) {
            return $full;
        }

        $startRaw = $m[1];
        $endRaw   = $m[2];

        if ($startRaw === '' && $endRaw === '') {
            return $full;
        }

        if ($startRaw === '') {
            $suffix = (int) $endRaw;
            if ($suffix <= 0) {
                return ['status' => 416, 'start' => 0, 'end' => 0, 'length' => 0];
            }
            $start = max(0, $size - $suffix);
            $end = $size - 1;
        } elseif ($endRaw === '') {
            $start = (int) $startRaw;
            $end = $size - 1;
        } else {
            $start = (int) $startRaw;
            $end = (int) $endRaw;
        }

        if ($start > $end || $start >= $size) {
            return ['status' => 416, 'start' => 0, 'end' => 0, 'length' => 0];
        }

        $end = min($end, $size - 1);
        $length = $end - $start + 1;

        return ['status' => 206, 'start' => $start, 'end' => $end, 'length' => $length];
    }

    private function resolvePath(string $stored): string
    {
        $trimmed = trim($stored);
        if ($trimmed === '' || str_contains($trimmed, "\0")) {
            $this->fail(404, 'STREAM_PATH_INVALID', 'Geçersiz dosya yolu');
        }

        if (str_contains($trimmed, '..') || is_link($trimmed)) {
            $this->fail(403, 'STREAM_FORBIDDEN', 'Erişim reddedildi');
        }

        // Eğer mutlak bir dosya yolu ise doğrudan kontrol et
        if (is_file($trimmed)) {
            return (string) realpath($trimmed);
        }

        // Kütüphane kök dizini altında ara
        $root = $this->libraryPath !== '' ? $this->libraryPath : dirname(__DIR__, 3);
        $candidate = rtrim($root, '\\/') . DIRECTORY_SEPARATOR . ltrim($trimmed, '\\/');

        if (is_file($candidate)) {
            return (string) realpath($candidate);
        }

        $this->fail(404, 'STREAM_FILE_NOT_FOUND', 'Fiziksel medya dosyası bulunamadı');
    }

    private function contentType(string $path, string $format): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return self::MIME_BY_EXT[$ext] ?? self::MIME_BY_EXT[$format] ?? 'application/octet-stream';
    }

    /**
     * F6: sözleşmeli hata gövdesi — {error:{code,message}}.
     *
     * Eski sürümde `code` HTTP durum numarasıydı (int) — API CLAUDE.md §4.2
     * hata sözleşmesi string enum kodu ister; 500 içi `STREAM_READ_ERROR` gibi
     * ayrıştırılabilir kodlar istemcinin dal/branch yapabilmesini sağlar.
     *
     * @param int $status HTTP durum kodu
     * @param string $errorCode Sözleşmeli enum kodu
     * @param string $message Kullanıcıya dönülebilir metin
     */
    private function fail(int $status, string $errorCode, string $message): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        echo json_encode(
            ['error' => ['code' => $errorCode, 'message' => $message]],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }
}
