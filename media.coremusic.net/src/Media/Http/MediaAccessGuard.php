<?php

declare(strict_types=1);

namespace Media\Http;

use CoreMusic\Security\JwtService;
use CoreMusic\Session\SessionBootstrapper;
use Media\MediaAccessRepositoryInterface;
use Media\MediaEnv;
use Media\MediaHttpException;
use Media\PathGuard;

/**
 * MediaAccessGuard — fail-closed erişim denetimi (Faz 2, tasarım maddesi 3).
 *
 * Sıra (erken çıkış, asla 200 üretmez — her reddi exception):
 *   1. `file_key` sözdizimi    → 400 INVALID_FILE_KEY (auth ÖNCESİ: bozuk istek
 *      kimlik sorgusu olmadan reddedilir; hiçbir bilgi sızdırmaz)
 *   2. Kimlik                  → 401 UNAUTHORIZED
 *      a) `Authorization: Bearer <jwt>` → JwtService::validate (RS256, fail-closed)
 *      b) yoksa oturum çerezi           → SessionBootstrapper::ensureStarted()
 *      Bearer İSTEGI oturumu ASLA BAŞLATMAZ (ADR-010 §1) — bu yüzden sıra b'den önce a.
 *   3. `media_access` izni     → 403 FORBIDDEN   (DB hatası dahil: doğrulanamayan = reddedilen)
 *   4. `media_catalog.asset`   → 404 NOT_FOUND   (metadata hatası → 403)
 *   5. PathGuard + MEDIA_ROOT  → 403 PATH_DENIED (kaçış/symlink/kök yok → ASLA 200)
 *
 * Kimlik normalizasyonu: oturum `MM_UserID` (auth.coremusic.net/include/Service/
 * SessionManager.php:37) veya `user.id`; JWT `sub`. Hepsi tire temizlenip 32 hex'e
 * düşürülür (`UNHEX` binary(16) bunu ister); doğrulanamayan kimlik 401.
 *
 * ⚠️ VERIFICATION REQUIRED: jti iptal listesi (kullanıcı token'ları) Faz 2'de
 * uygulanmadı — `JwtService::validate` yalnız imza/exp/iss/aud denetler.
 */
final class MediaAccessGuard
{
    /**
     * `file_key` sözdizimi (decode SONRASI): harf/rakam/alt çizgi ile başlar,
     * ardından en fazla 254 `[A-Za-z0-9._-]`. `/`, `\`, NUL, `..` başlangıcı
     * ve boş değer ELENER (regex `.` içermez — `.` yalnız sonradan gelir).
     */
    private const FILE_KEY_DESEN = '/^[A-Za-z0-9_][A-Za-z0-9._-]{0,254}$/';

    /** Test için oturum başlatıcı — üretimde `SessionBootstrapper::ensureStarted`. */
    private readonly \Closure $sessionStarter;

    public function __construct(
        private readonly MediaAccessRepositoryInterface $repository,
        private readonly ?JwtService $jwt = null,
        ?\Closure $sessionStarter = null,
    ) {
        $this->sessionStarter = $sessionStarter ?? static function (): void {
            SessionBootstrapper::ensureStarted();
        };
    }

    /**
     * Kimlik + izin + varlık + yol denetimi; başarısızsa exception yükseltir.
     *
     * @return array{
     *     userId: string,
     *     fileKey: string,
     *     relativePath: string,
     *     absolutePath: string,
     *     size: int,
     *     mtime: int,
     *     etag: string
     * } Denetlenmiş, akışa hazır bağlam
     *
     * @throws MediaHttpException 400 · 401 · 403 · 404
     */
    public function authorize(string $fileKey): array
    {
        // 1) Sözdizimi — decode index.php'de bir kez yapılır; burada tekrar decode YOK
        //    (çift decode `..` üretebilir). Bozuk istek auth'dan ÖNCE reddedilir.
        $key = rawurldecode($fileKey);
        if (preg_match(self::FILE_KEY_DESEN, $key) !== 1) {
            throw new MediaHttpException(400, 'INVALID_FILE_KEY', 'Gecersiz dosya anahtari.');
        }

        // 2) Kimlik
        $userId = $this->authenticate();

        // 3) İzin (fail-closed: DB hatası dahil reddedilir)
        $granted = false;
        try {
            $granted = $this->repository->hasReadGrant($userId, $key);
        } catch (\Throwable) {
            $granted = false;
        }
        if (!$granted) {
            throw new MediaHttpException(403, 'FORBIDDEN', 'Bu dosya icin okuma izni yok.');
        }

        // 4) Katalog varlığı (fail-closed: metadata okunamıyorsa 403)
        try {
            $asset = $this->repository->findAsset($key);
        } catch (\Throwable) {
            throw new MediaHttpException(403, 'FORBIDDEN', 'Dosya bilgisi okunamadi.');
        }
        if ($asset === null) {
            throw new MediaHttpException(404, 'NOT_FOUND', 'Dosya kaydi bulunamadi.');
        }

        // 5) Yol çözümü (MEDIA_ROOT + PathGuard — kaçış/symlink ASLA 200)
        $root = MediaEnv::root();
        if ($root === null) {
            throw new MediaHttpException(403, 'PATH_DENIED', 'Medya koku yapilandirilmamis.');
        }
        $relative = (string) $asset['dosya_yolu'];
        $absolute = PathGuard::resolve($root, $relative);
        if ($absolute === null) {
            throw new MediaHttpException(403, 'PATH_DENIED', 'Dosya yolu medya koku disinda.');
        }

        $size = @filesize($absolute);
        $mtime = @filemtime($absolute);
        if ($size === false || $mtime === false) {
            throw new MediaHttpException(403, 'PATH_DENIED', 'Dosya okunamadi.');
        }

        // ETag: kayıtlı sha256 + disk boyu/mtimesı — dosyanın kendisi HASHLENMEZ
        // (ADR-092 §6.1 m.5: sha256 yalnız girişte; 38 GB ağaç tekrar okunmaz).
        $etag = '"' . hash(
            'sha256',
            $key . '|' . (string) $asset['sha256'] . '|' . $size . '|' . $mtime
        ) . '"';

        return [
            'userId' => $userId,
            'fileKey' => $key,
            'relativePath' => $relative,
            'absolutePath' => $absolute,
            'size' => (int) $size,
            'mtime' => (int) $mtime,
            'etag' => $etag,
        ];
    }

    /**
     * Erişim kaydı — controller gövdesi gönderilmeden ÖNCE çağırır.
     * Başarısızlık YUKARI taşınır (index.php → 500): kayıtlanmayan erişim servis
     * edilmez (fail-closed).
     *
     * @param array<string, mixed> $ctx authorize() bağlamı
     */
    public function recordAccess(array $ctx, int $status): void
    {
        $this->repository->recordAudit(
            (string) $ctx['userId'],
            (string) $ctx['fileKey'],
            (string) $ctx['relativePath'],
            (int) $ctx['size'],
            $status,
        );
    }

    /* ------------------------------------------------------------------ *
     * Kimlik
     * ------------------------------------------------------------------ */

    /**
     * Yalnız kimlik kapısı — `POST /upload` yüzeyi (varlık/izin/yol adımları YOK).
     *
     * `authorize()` zincirinin 2. adımıyla AYNI çözümü kullanır; 401 aynı
     * `authenticate()`'ten gelir (Bearer önce — Bearer isteği oturumu ASLA
     * başlatmaz, ADR-010 §1). Mevcut akış davranışı DEGISTIRILMEZ.
     *
     * @return string 32-hex userId (UNHEX binary(16) girişi)
     *
     * @throws MediaHttpException 401 UNAUTHORIZED
     */
    public function requireIdentity(): string
    {
        return $this->authenticate();
    }

    /**
     * Kimlik çözümü: Bearer (oturum BAŞLATMAZ) → oturum çerezi → 401.
     *
     * @throws MediaHttpException 401 UNAUTHORIZED
     */
    private function authenticate(): string
    {
        // a) Bearer — header varsa oturum hiç başlatılmaz (ADR-010 §1).
        $header = trim((string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        if ($header !== '') {
            if (!str_starts_with($header, 'Bearer ')) {
                // Desteklenmeyen şema — düşüşe izin VERİLMEZ (fail-closed).
                throw new MediaHttpException(401, 'UNAUTHORIZED', 'Kimlik dogrulanamadi.');
            }
            $token = trim(substr($header, 7));
            $claims = ($token !== '' && $this->jwt !== null) ? $this->jwt->validate($token) : null;
            $sub = is_array($claims) ? ($claims['sub'] ?? null) : null;
            if (is_string($sub)) {
                $hex = self::normalizeUserId($sub);
                if ($hex !== null) {
                    return $hex;
                }
            }

            throw new MediaHttpException(401, 'UNAUTHORIZED', 'Kimlik dogrulanamadi.');
        }

        // b) Oturum çerezi (session adı: SessionConfig::fromEnvironment() ile aynı çözüm).
        $sessionName = defined('SESSION_NAME') ? (string) SESSION_NAME : 'COREMUSIC_SESS';
        if (isset($_COOKIE[$sessionName])) {
            try {
                ($this->sessionStarter)();
            } catch (\Throwable) {
                // Oturum başlatılamadı → kullanıcı yok → 401 (fail-closed).
            }

            $session = $_SESSION ?? null;
            if (is_array($session)) {
                $adaylar = [$session['MM_UserID'] ?? null];
                if (is_array($session['user'] ?? null)) {
                    $adaylar[] = $session['user']['id'] ?? null;
                }
                foreach ($adaylar as $aday) {
                    if (is_string($aday)) {
                        $hex = self::normalizeUserId($aday);
                        if ($hex !== null) {
                            return $hex;
                        }
                    }
                }
            }
        }

        throw new MediaHttpException(401, 'UNAUTHORIZED', 'Oturum dogrulamasi gerekli.');
    }

    /**
     * UUIDv7 → 32 hex (tire yok); geçersizse `null` (→ 401).
     */
    private static function normalizeUserId(string $value): ?string
    {
        $temiz = str_replace('-', '', trim($value));
        if (strlen($temiz) !== 32 || !ctype_xdigit($temiz)) {
            return null;
        }

        return strtolower($temiz);
    }
}