<?php

declare(strict_types=1);

namespace Media\Http;

use Media\MediaHttpException;

/**
 * MediaStreamController — Range / ETag / If-None-Match → 200 · 206 · 304 · 416.
 *
 * Desen kaynağı (OKU, kopya desen — taşıma değil):
 * `api.coremusic.net/include/Controller/StreamController.php` (MIME_BY_EXT,
 * parseRange, etagMatches, pump). Farklar: `exit` YOK (test edilebilirlik —
 * durum döner, gövde basılır), ETag kayıtlı `sha256` + disk boyu/mtime'dan
 * üretilir (dosya HASHLENMEZ), gövde göndermeden ÖNCE `media_audit` insert
 * edilir (kayıtlanmayan erişim servis edilmez — fail-closed).
 *
 * Durum matrisi:
 *   200 tam gövde · 206 parçalı (`Range`) · 304 koşullu önbellek (`If-None-Match`)
 *   416 `Range` geçersiz (`Content-Range` başlığında toplam bayt boyutu verilir)
 *   · 401/403/404 guard'dan
 *   500 audit/dosya hatası (exception → index.php JSON zarfı)
 *
 * Gövde yalnız istek `GET`'se basılır (`HEAD`: başlıklar + `Content-Length`,
 * gövde yok).
 */
final class MediaStreamController
{
    /** MIME haritası — StreamController.php:15-27 + `opus` (sip30/sip1023). */
    private const MIME_BY_EXT = [
        'mp3' => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav' => 'audio/wav',
        'm4a' => 'audio/mp4',
        'aac' => 'audio/aac',
        'ogg' => 'audio/ogg',
        'opus' => 'audio/opus',
        'wma' => 'audio/x-ms-wma',
        'mp4' => 'video/mp4',
        'mkv' => 'video/x-matroska',
        'webm' => 'video/webm',
        'avi' => 'video/x-msvideo',
    ];

    private const CHUNK_BYTES = 1048576; // 1 MiB — StreamController.php:29

    private const CACHE_CONTROL = 'private, max-age=86400';

    /** @var array<string, string> son gönderilen başlıklar (CLI'de `header()` yok → test kaynağı) */
    private array $lastHeaders = [];

    private int $lastStatus = 0;

    private bool $headersSentFlag = false;

    public function __construct(private readonly MediaAccessGuard $guard)
    {
    }

    /** @return array<string, string> */
    public function lastHeaders(): array
    {
        return $this->lastHeaders;
    }

    public function lastStatus(): int
    {
        return $this->lastStatus;
    }

    public function headersSent(): bool
    {
        return $this->headersSentFlag;
    }

    /**
     * Akışı yürütür ve HTTP durumunu döndürür; gövdeyi basar.
     *
     * @throws MediaHttpException Guard reddi (401/403/404) veya audit hatası (500)
     */
    public function handle(string $fileKey): int
    {
        $ctx = $this->guard->authorize($fileKey);

        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $size = (int) $ctx['size'];
        $mtime = (int) $ctx['mtime'];
        $etag = (string) $ctx['etag'];
        $lastMod = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';

        $headers = [
            'Content-Type' => $this->mime((string) $ctx['absolutePath'], (string) $ctx['relativePath']),
            'Accept-Ranges' => 'bytes',
            'ETag' => $etag,
            'Last-Modified' => $lastMod,
            'Cache-Control' => self::CACHE_CONTROL,
            'X-Content-Type-Options' => 'nosniff',
        ];

        // --- 304: If-None-Match eşleşti (RFC 9110 §13.1.2) -------------------
        $ifNoneMatch = trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
        if ($ifNoneMatch !== '' && $this->etagMatches($ifNoneMatch, $etag)) {
            $this->guard->recordAccess($ctx, 304);
            unset($headers['Content-Type'], $headers['Accept-Ranges']);
            $this->send(304, $headers);

            return 304;
        }

        // --- Range çözümü ------------------------------------------------------
        $range = $this->parseRange((string) ($_SERVER['HTTP_RANGE'] ?? ''), $size);

        if ($range['status'] === 416) {
            // 416 ≥ 400 → media_audit'e YAZILMAZ (yalnız başarılı erişimler kayda girer).
            $headers['Content-Range'] = 'bytes */' . $size;
            unset($headers['Content-Type'], $headers['Accept-Ranges']);
            $this->send(416, $headers);

            return 416;
        }

        // --- Dosya handle'ı BAŞLIKLARDAN ÖNCE (açılış hatası → gövde gönderilmeden) ---
        $handle = @fopen((string) $ctx['absolutePath'], 'rb');
        if ($handle === false) {
            throw new MediaHttpException(500, 'INTERNAL_ERROR', 'Dosya acilamadi.');
        }

        $status = $range['status'];
        $headers['Content-Length'] = (string) $range['length'];
        if ($status === 206) {
            $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $range['start'], $range['end'], $size);
        }

        // --- 5) media_audit insert: gövde ÖNCESİ (başarısızsa gövde YOK) -------
        try {
            $this->guard->recordAccess($ctx, $status);
        } catch (\Throwable $e) {
            fclose($handle);
            throw new MediaHttpException(500, 'INTERNAL_ERROR', 'Erisim kaydi yapilamadi.', $e);
        }

        $this->send($status, $headers);

        if ($method === 'HEAD') {
            fclose($handle);

            return $status;
        }

        $this->pump($handle, $range['start'], $range['length']);

        return $status;
    }

    /* ------------------------------------------------------------------ *
     * Gövde
     * ------------------------------------------------------------------ */

    /**
     * Byte akışı — StreamController::pump deseni (chunk döngüsü + flush),
     * ancak handle parametre olarak alınır (başlıklardan sonra açılmadıysa
     * buraya asla gelinmez).
     *
     * @param resource $handle
     */
    private function pump($handle, int $start, int $length): void
    {
        try {
            if ($length <= 0) {
                return;
            }
            if ($start > 0 && fseek($handle, $start, SEEK_SET) !== 0) {
                return; // başlıklar gönderildi → gövde kırpılır; exception YOK (artık JSON üretilemez)
            }

            $remaining = $length;
            while ($remaining > 0 && !feof($handle)) {
                $buffer = fread($handle, min(self::CHUNK_BYTES, $remaining));
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
        } finally {
            fclose($handle);
        }
    }

    /* ------------------------------------------------------------------ *
     * Başlık gönderimi
     * ------------------------------------------------------------------ */

    /**
     * Durumu + başlıkları uygular. CLI SAPI'de `header()`/`http_response_code()`
     * yoktur → testler `lastStatus()`/`lastHeaders()` okur; cli-server/fpm'de
     * gerçek başlık gönderilir.
     *
     * @param array<string, string> $headers
     */
    private function send(int $status, array $headers): void
    {
        $this->lastStatus = $status;
        $this->lastHeaders = $headers;
        $this->headersSentFlag = true;

        if (PHP_SAPI === 'cli') {
            return;
        }

        http_response_code($status);
        foreach ($headers as $isim => $deger) {
            header($isim . ': ' . $deger, true);
        }
    }

    /* ------------------------------------------------------------------ *
     * RFC 9110 yardımcıları (StreamController.php:176-241 deseni)
     * ------------------------------------------------------------------ */

    /**
     * If-None-Match karşılaştırması: `*`, çoklu değer (virgül), `W/` öneki,
     * tırnak farkı — hepsi eşleşir.
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

    /**
     * `Range` başlığı çözümü.
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
            return $full;
        }

        $startRaw = $m[1];
        $endRaw = $m[2];

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

        return ['status' => 206, 'start' => $start, 'end' => $end, 'length' => $end - $start + 1];
    }

    /**
     * MIME: gerçek dosya uzantısı → `format` kolonu → `application/octet-stream`.
     */
    private function mime(string $absolutePath, string $relativePath): string
    {
        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (isset(self::MIME_BY_EXT[$ext])) {
            return self::MIME_BY_EXT[$ext];
        }

        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        return self::MIME_BY_EXT[$ext] ?? 'application/octet-stream';
    }
}