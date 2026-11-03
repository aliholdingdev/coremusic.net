<?php

declare(strict_types=1);

namespace Media\Http;

use CoreMusic\Config\EnvParser;
use CoreMusic\Database\ConnectionFactory;
use CoreMusic\Security\JwtService;
use Media\MediaEnv;
use Media\MediaHttpException;
use Media\MySqlMediaAccessRepository;

/**
 * media.coremusic.net — HTTP front controller (Faz 2, tasarım maddesi 1).
 *
 * Shared JSON Gateway YENİDEN KULLANILMAZ: binary gövde (audio/video) JSON-only
 * yanıt sözleşmesini ihlal eder. Bu front controller yalnız JSON hata zarfı üretir;
 * başarılı akış gövdesi `MediaStreamController` basar.
 *
 * Yollar:
 *   GET|HEAD /stream/{file_key} → akış (200 · 206 · 304 · 416 · 400/401/403/404)
 *   POST /upload                → üye medya yükleme (201 · 400/401/403/405/413/415/500/503)
 *   diğer yollar → 404 JSON · stream'de diğer methodlar → 405 + Allow ·
 *   /upload'da 405 → `Allow: POST`
 *
 * Hata sözleşmesi (api `$jsonResponse` deseni): `{error:{code,message}}` +
 * `X-Content-Type-Options: nosniff` + CSP `default-src 'none'; ...`.
 * `MediaHttpException` kendi durum/koduna çevirilir; diğer her şey 500
 * `INTERNAL_ERROR` (mesaj sızdırılmaz — asla `getMessage()` istemciye dönmez).
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

// .env yükleme (Faz 5 wiring): upload dalı DB çağrısı yapmadığı için
// ConnectionFactory ctor'ı (EnvParser) hiç tetiklenmez → MediaEnv::root()
// $_ENV'i boş görür ve MEDIA_ROOT tanımlı olsa bile 503 üretirdi.
// loadIntoEnv mevcut anahtarları EZMEZ (yalnız isset olmayanlar doldurulur);
// stream dalındaki ConnectionFactory da aynı dosyayı aynı şekilde okur.
EnvParser::loadIntoEnv(dirname(__DIR__) . '/../shared/config/.env');

/**
 * JSON hata zarfı + çıkış. Başarı gövdesi buradan ASLA üretilmez.
 *
 * @return never
 */
function mediaJsonError(int $status, string $code, string $message): never
{
    if (PHP_SAPI !== 'cli') {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8', true);
        header('X-Content-Type-Options: nosniff', true);
        header(
            "Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'",
            true
        );
        header('X-Media-Service: media.coremusic.net:' . MediaEnv::port(), true);
    }

    echo (string) json_encode(
        ['error' => ['code' => $code, 'message' => $message]],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

$yol = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');

// --- Yöntem kapısı (GET/HEAD dışında akış yok) -------------------------------
if (preg_match('#^/stream/([^/]+)$#', $yol, $eslesme) === 1) {
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? ''));
    if (!in_array($method, ['GET', 'HEAD'], true)) {
        if (PHP_SAPI !== 'cli') {
            header('Allow: GET, HEAD', true);
        }
        mediaJsonError(405, 'METHOD_NOT_ALLOWED', 'Yalniz GET/HEAD desteklenir.');
    }

    // Ham segment guard'a verilir; tek decode MediaAccessGuard::authorize'da yapılır
    // (çift decode `..` üretebilir).
    $fileKey = $eslesme[1];

    $repository = new MySqlMediaAccessRepository(
        static fn (string $db): \PDO => ConnectionFactory::getInstance()->get($db)
    );

    // JWT anahtarları — api `ApiAuthContainer::jwtService():108-123` deseni
    // (env → default `shared/config/jwt/`). Anahtar yoksa `validate`
    // RuntimeException fırlatır → index.php 500 (yapılandırma hatası, fail-closed).
    $jwtDir = dirname(__DIR__) . '/../shared/config/jwt';
    $jwt = new JwtService(
        privateKeyPath: (string) ((($_ENV['JWT_PRIVATE_KEY_PATH'] ?? '') !== '') ? $_ENV['JWT_PRIVATE_KEY_PATH'] : $jwtDir . '/private.pem'),
        publicKeyPath: (string) ((($_ENV['JWT_PUBLIC_KEY_PATH'] ?? '') !== '') ? $_ENV['JWT_PUBLIC_KEY_PATH'] : $jwtDir . '/public.pem'),
        issuer: (string) ((($_ENV['JWT_ISSUER'] ?? '') !== '') ? $_ENV['JWT_ISSUER'] : 'coremusic'),
        audience: (string) ((($_ENV['JWT_AUDIENCE'] ?? '') !== '') ? $_ENV['JWT_AUDIENCE'] : 'coremusic-api'),
        ttlSeconds: (int) ((($_ENV['JWT_TTL_SECONDS'] ?? '') !== '') ? $_ENV['JWT_TTL_SECONDS'] : 3600),
    );

    $controller = new MediaStreamController(new MediaAccessGuard($repository, $jwt));

    try {
        $controller->handle($fileKey);
        exit;
    } catch (MediaHttpException $e) {
        mediaJsonError($e->status, $e->errorCode, $e->getMessage());
    } catch (\Throwable) {
        // Dahili hata — mesaj asla istemciye dönmez (hata sızıntısı yasağı).
        mediaJsonError(500, 'INTERNAL_ERROR', 'Sunucu tarafinda beklenmeyen hata.');
    }
}

// --- POST /upload — üye medya yükleme (MediaUploadController kapıları) -------
if ($yol === '/upload') {
    $repository = new MySqlMediaAccessRepository(
        static fn (string $db): \PDO => ConnectionFactory::getInstance()->get($db)
    );

    // JWT anahtarları — stream dalı ile AYNI çözüm (env → default shared/config/jwt/).
    // (Dal tekrarı bilinçli: stream dalına dokunmadan eklenen ayrı route.)
    $jwtDir = dirname(__DIR__) . '/../shared/config/jwt';
    $jwt = new JwtService(
        privateKeyPath: (string) ((($_ENV['JWT_PRIVATE_KEY_PATH'] ?? '') !== '') ? $_ENV['JWT_PRIVATE_KEY_PATH'] : $jwtDir . '/private.pem'),
        publicKeyPath: (string) ((($_ENV['JWT_PUBLIC_KEY_PATH'] ?? '') !== '') ? $_ENV['JWT_PUBLIC_KEY_PATH'] : $jwtDir . '/public.pem'),
        issuer: (string) ((($_ENV['JWT_ISSUER'] ?? '') !== '') ? $_ENV['JWT_ISSUER'] : 'coremusic'),
        audience: (string) ((($_ENV['JWT_AUDIENCE'] ?? '') !== '') ? $_ENV['JWT_AUDIENCE'] : 'coremusic-api'),
        ttlSeconds: (int) ((($_ENV['JWT_TTL_SECONDS'] ?? '') !== '') ? $_ENV['JWT_TTL_SECONDS'] : 3600),
    );

    $controller = new MediaUploadController(new MediaAccessGuard($repository, $jwt));

    try {
        $controller->handle();
        exit;
    } catch (MediaHttpException $e) {
        // 405 → `Allow: POST` başlığı envelope ÖNCESİNDE (stream dalı deseni).
        if ($e->status === 405 && PHP_SAPI !== 'cli') {
            header('Allow: POST', true);
        }
        mediaJsonError($e->status, $e->errorCode, $e->getMessage());
    } catch (\Throwable) {
        // Dahili hata — mesaj asla istemciye dönmez (hata sızıntısı yasağı).
        mediaJsonError(500, 'INTERNAL_ERROR', 'Sunucu tarafinda beklenmeyen hata.');
    }
}

mediaJsonError(404, 'NOT_FOUND', 'Kayit bulunamadi.');