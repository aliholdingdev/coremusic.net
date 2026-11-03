<?php

declare(strict_types=1);

namespace Media\Http;

use Media\MediaEnv;
use Media\MediaHttpException;
use Media\PathGuard;
use Media\Ulid;

/**
 * MediaUploadController — POST /upload üye medya yükleme yüzeyi (Faz 2 uzantısı).
 *
 * Kapı sırası (erken çıkış; her reddi `MediaHttpException` — asla 2xx):
 *   1. Kimlik                    → 401 UNAUTHORIZED (`MediaAccessGuard::requireIdentity`)
 *   2. Origin/Referer            → 403 FORBIDDEN (varsa allowlist dışı; yoksa geç)
 *   3. Yöntem                    → 405 METHOD_NOT_ALLOWED + `Allow: POST`
 *   4. Content-Type              → 415 UNSUPPORTED_MEDIA_TYPE (`multipart/form-data`)
 *   5. Erken boyut               → 413 SIZE_LIMIT_EXCEEDED (`Content-Length` limit üstü)
 *   6. Dosya yapısı / PHP hatası → 400 UPLOAD_REQUIRED · UPLOAD_FAILED (boyut 413)
 *   7. Boş gövde                 → 400 EMPTY_UPLOAD · limit üstü gerçek boyut → 413
 *   8. Uzantı allowlist          → 415 UNSUPPORTED_EXTENSION (mp3·flac·wav·m4a·aac·ogg·opus)
 *   9. İstemci adı kaçışı        → 400 PATH_TRAVERSAL (`PathGuard::isSafeRelative`)
 *  10. MEDIA_ROOT yok            → 503 MEDIA_NOT_CONFIGURED (sessiz temp fallback YOK)
 *  11. Staging dizini            → 503 STAGING_UNAVAILABLE
 *  12. Taşıma                    → 500 INTERNAL_ERROR (dosya staging'e ÇIKMAZ)
 *  13. Taşıma sonrası kök denetimi → 400 PATH_TRAVERSAL + unlink (asla 2xx)
 *  201 → yalnız `file_key` (ULID) + ext · size_bytes · mime —
 *        ASLA mutlak/göreli dosya yolu veya MEDIA_ROOT değeri (API yol sızdırma
 *        yasağı: "never a path in any response").
 *
 * Depolama adı istemci adından ÜRETİLMEZ: `Ulid::uret()` + doğrulanmış uzantı →
 * `MEDIA_ROOT/_inbox/{YYYY-MM-DD}/{ULID}.{ext}` (`bin/ingest.php` staging geleneği).
 * Taşıma `is_uploaded_file` + `move_uploaded_file` (CLI testinde enjekte edilir).
 *
 * DB yazımı YOK: `MediaAccessRepositoryInterface` insert yüzeyi içermez (yalnız
 * okuma izni / varlık / akış denetimi); 201, staging dosyası + file_key üzerinde
 * durur — env yoksa bile (görev sözleşmesi: "201 stands on staged file + file_key").
 */
final class MediaUploadController
{
    /** Boyut tavanı — 100 MiB (104857600 bayt); sabit kodda, env DEGIL. */
    public const MAX_UPLOAD_BYTES = 104857600;

    /** Ses öncelikli uzantı allowlist — `bin/ingest.php` `$sesUzanti` (wma hariç). */
    private const IZINLI_UZANTILAR = ['mp3', 'flac', 'wav', 'm4a', 'aac', 'ogg', 'opus'];

    /** Uzantı → MIME (`MediaStreamController::MIME_BY_EXT` ses alt kümesi). */
    private const MIME_BY_EXT = [
        'mp3' => 'audio/mpeg',
        'flac' => 'audio/flac',
        'wav' => 'audio/wav',
        'm4a' => 'audio/mp4',
        'aac' => 'audio/aac',
        'ogg' => 'audio/ogg',
        'opus' => 'audio/opus',
    ];

    /**
     * Origin allowlist — `shared/src/Middleware/OriginCheckMiddleware.php` geleneği:
     * tam host eşleşmesi (suffix bilinçli kapalı), `*.` yalnız açıkça yazılır,
     * apex ayrı satır. Origin/Referer YOKSA geç (same-origin olmayan tarayıcı
     * dışı istek — curl etc.).
     */
    private const ORIGIN_ALLOWLIST = [
        'localhost',
        '127.0.0.1',
        'coremusic.net',
        '*.coremusic.net',
    ];

    /** Taşıyıcı — üretim varsayılanı: `is_uploaded_file` + `move_uploaded_file`. */
    private readonly \Closure $mover;

    /** @var array<string, string> son gönderilen başlıklar (CLI'de `header()` yok → test kaynağı) */
    private array $lastHeaders = [];

    private int $lastStatus = 0;

    public function __construct(
        private readonly MediaAccessGuard $guard,
        ?\Closure $mover = null,
    ) {
        $this->mover = $mover ?? static function (string $kaynak, string $hedef): bool {
            return is_uploaded_file($kaynak) && move_uploaded_file($kaynak, $hedef);
        };
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

    /**
     * Kapı zincirini yürütür; başarıda JSON gövdeyi basar ve 201 döndürür.
     *
     * @throws MediaHttpException 400 · 401 · 403 · 405 · 413 · 415 · 500 · 503
     */
    public function handle(): int
    {
        // 1) Kimlik — varlık/izin/yol adımı YOK; upload yalnız kimlik ister (401)
        $this->guard->requireIdentity();

        // 2) Origin/Referer — yalnız VARSALLOWLIST DIŞI reddedilir (yoksa geç)
        $this->assertOriginAllowed();

        // 3) Yöntem — POST dışında yok (Allow başlığı controller'da kaydedilir,
        //    gerçek HTTP'te index.php 405 yakalarken gönderir)
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? ''));
        if ($method !== 'POST') {
            $this->hata(405, 'METHOD_NOT_ALLOWED', 'Yalniz POST desteklenir.', ['Allow' => 'POST']);
        }

        // 4) Content-Type — multipart/form-data (çoklu `;` parametreleri kırpılır)
        $contentType = strtolower(trim(explode(';', (string) ($_SERVER['CONTENT_TYPE'] ?? ''))[0]));
        if ($contentType !== 'multipart/form-data') {
            $this->hata(415, 'UNSUPPORTED_MEDIA_TYPE', 'multipart/form-data gerekli.');
        }

        // 5) Erken boyut — Content-Length limit üstüyse dosyaya hiç değinmeden dön
        $cl = trim((string) ($_SERVER['CONTENT_LENGTH'] ?? ''));
        if ($cl !== '' && ctype_digit($cl) && (int) $cl > self::MAX_UPLOAD_BYTES) {
            $this->hata(413, 'SIZE_LIMIT_EXCEEDED', 'Dosya boyutu izin verilen siniri asiyor.');
        }

        // 6) Dosya yapısı — `file` alanı yok/bozuk (çoklu `file[]` dizisi de reddi)
        $dosya = $_FILES['file'] ?? null;
        if (!is_array($dosya)) {
            $this->hata(400, 'UPLOAD_REQUIRED', 'Yuklenecek dosya bulunamadi.');
        }
        $istemciAdi = $dosya['name'] ?? null;
        $tmp = $dosya['tmp_name'] ?? null;
        $boyutHam = $dosya['size'] ?? null;
        if (!is_string($istemciAdi) || !is_string($tmp) || $tmp === '' || !is_int($boyutHam)) {
            $this->hata(400, 'UPLOAD_REQUIRED', 'Yukleme bicimi gecersiz.');
        }

        // 6b) PHP yükleme hatası — önce boyut hataları (INI/FORM) 413'e, boş/diğer 400'e
        $phpHata = (int) ($dosya['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($phpHata !== UPLOAD_ERR_OK) {
            if (in_array($phpHata, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                $this->hata(413, 'SIZE_LIMIT_EXCEEDED', 'Dosya boyutu izin verilen siniri asiyor.');
            }
            $this->hata(400, 'UPLOAD_FAILED', 'Dosya alinamadi.');
        }

        // 7) Gerçek boyut — boş → 400; tavan üstü → 413 (Content-Length yalan olsa da)
        if ($boyutHam <= 0) {
            $this->hata(400, 'EMPTY_UPLOAD', 'Dosya bos.');
        }
        if ($boyutHam > self::MAX_UPLOAD_BYTES) {
            $this->hata(413, 'SIZE_LIMIT_EXCEEDED', 'Dosya boyutu izin verilen siniri asiyor.');
        }

        // 8) Uzantı allowlist — istemci adından; depolama adı burada DEGIL (ULID)
        $uzanti = strtolower(pathinfo($istemciAdi, PATHINFO_EXTENSION));
        if (!in_array($uzanti, self::IZINLI_UZANTILAR, true)) {
            $this->hata(415, 'UNSUPPORTED_EXTENSION', 'Desteklenmeyen dosya uzantisi.');
        }

        // 9) İstemci adı kaçış denetimi — stoğa girmez AMA reddedilir (asla 2xx)
        if (!PathGuard::isSafeRelative($istemciAdi)) {
            $this->hata(400, 'PATH_TRAVERSAL', 'Gecersiz dosya adi.');
        }

        // 10) MEDIA_ROOT — yoksa 503 (fail-closed; sessiz temp dizin fallback YOK)
        $kok = MediaEnv::root();
        if ($kok === null) {
            $this->hata(503, 'MEDIA_NOT_CONFIGURED', 'Medya koku yapilandirilmamis.');
        }

        // 11) Staging: MEDIA_ROOT/_inbox/{YYYY-MM-DD}/{ULID}.{ext}
        $ulid = Ulid::uret();
        $goreli = '_inbox/' . date('Y-m-d') . '/' . $ulid . '.' . $uzanti;
        $hedefDizin = $kok . '/' . dirname($goreli);
        if (!is_dir($hedefDizin) && !@mkdir($hedefDizin, 0775, true) && !is_dir($hedefDizin)) {
            $this->hata(503, 'STAGING_UNAVAILABLE', 'Yukleme alani hazirlanamadi.');
        }
        $hedef = $kok . '/' . $goreli;

        // 12) Taşıma — üretimde `is_uploaded_file` + `move_uploaded_file` (CLI'da
        //     `is_uploaded_file` hep false → test enjekte eder). Başarısızlıkta
        //     dosya staging'e ÇIKMAZ (yarım kayıp temizlenir).
        $tasindi = (bool) ($this->mover)($tmp, $hedef);
        if (!$tasindi || !is_file($hedef)) {
            @unlink($hedef);
            $this->hata(500, 'INTERNAL_ERROR', 'Dosya yukleme alinamina tasinamadi.');
        }

        // 13) Taşıma sonrası kök containment — `PathGuard::resolve` fail-closed:
        //     çözülemez → dosya silinir + 400 (kaçış asla 2xx üretmez)
        if (PathGuard::resolve($kok, $goreli) === null) {
            @unlink($hedef);
            $this->hata(400, 'PATH_TRAVERSAL', 'Dosya yolu medya koku disina kacti.');
        }

        $gercekBoyut = @filesize($hedef);
        if ($gercekBoyut === false) {
            @unlink($hedef);
            $this->hata(500, 'INTERNAL_ERROR', 'Yuklenen dosya okunamadi.');
        }
        if ($gercekBoyut <= 0) {
            @unlink($hedef);
            $this->hata(400, 'EMPTY_UPLOAD', 'Dosya bos.');
        }

        // 201 — yalnız file_key + güvenli metaveri (YOL ASLA DEGIL)
        $govde = (string) json_encode([
            'file_key' => $ulid,
            'ext' => $uzanti,
            'size_bytes' => $gercekBoyut,
            'mime' => self::MIME_BY_EXT[$uzanti],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $this->send(201, [
            'Content-Type' => 'application/json; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'",
            'Cache-Control' => 'no-store',
        ]);
        echo $govde;

        return 201;
    }

    /* ------------------------------------------------------------------ *
     * Origin
     * ------------------------------------------------------------------ */

    /**
     * Origin/Referer yalnız ikisi de yoksa geçer; varsa allowlist'te tam host
     * eşleşmesi şart (OriginCheckMiddleware deseni — parse edilemeyen 403).
     */
    private function assertOriginAllowed(): void
    {
        $origin = trim((string) ($_SERVER['HTTP_ORIGIN'] ?? ''));
        if ($origin !== '') {
            if (!$this->hostAllowed($origin)) {
                $this->hata(403, 'FORBIDDEN', 'Izin verilmeyen origin.');
            }

            return;
        }

        $referer = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));
        if ($referer !== '' && !$this->hostAllowed($referer)) {
            $this->hata(403, 'FORBIDDEN', 'Izin verilmeyen origin.');
        }
    }

    /**
     * URL → host çözümü + allowlist eşleşmesi (scheme'li/ciplak girdi kabul;
     * `*.` alt alan adı, apex'i KAPSAMAZ — apex kendi satırındadır).
     */
    private function hostAllowed(string $url): bool
    {
        $parsed = parse_url($url);
        // isset sonrası `host` PHPDoc'u gereği string (phpstan L5: is_string tekrarı gereksiz).
        if ($parsed === false || !isset($parsed['host']) || $parsed['host'] === '') {
            return false;
        }
        $host = strtolower($parsed['host']);

        foreach (self::ORIGIN_ALLOWLIST as $kural) {
            if (str_starts_with($kural, '*.')) {
                $suffix = substr($kural, 1); // '.coremusic.net'
                $apex = substr($suffix, 1);  // 'coremusic.net'
                if (str_ends_with($host, $suffix) && $host !== $apex) {
                    return true;
                }

                continue;
            }
            if ($host === $kural) {
                return true;
            }
        }

        return false;
    }

    /* ------------------------------------------------------------------ *
     * Yanıt
     * ------------------------------------------------------------------ */

    /**
     * Reddi exception'a çevirir; durumu/ek başlıkları test kaynağına yazar
     * (CLI'de `header()` yok — `lastHeaders()` 405 `Allow` kanıtıdır).
     *
     * @param array<string, string> $ek
     *
     * @return never
     */
    private function hata(int $durum, string $kod, string $mesaj, array $ek = []): never
    {
        $this->lastStatus = $durum;
        $this->lastHeaders = $ek;

        throw new MediaHttpException($durum, $kod, $mesaj);
    }

    /**
     * Durumu + başlıkları uygular (MediaStreamController::send deseni — CLI'de
     * yalnız kaydır, cli-server/fpm'de gerçek başlık gönderir).
     *
     * @param array<string, string> $basliklar
     */
    private function send(int $durum, array $basliklar): void
    {
        $this->lastStatus = $durum;
        $this->lastHeaders = $basliklar;

        if (PHP_SAPI === 'cli') {
            return;
        }

        http_response_code($durum);
        foreach ($basliklar as $isim => $deger) {
            header($isim . ': ' . $deger, true);
        }
    }
}
