<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

/**
 * AbstractController — tüm API controller'ların paylaştığı HTTP primitive'leri.
 *
 * Faz 3 (ADR-084 Contract First · ADR-083 L2 Routing):
 *   AuthController'dan behavior-preserving taşındı (sözleşme baytı değişmez):
 *     success()        → HTTP 200 + {success:true, data:{...}}
 *     error()          → HTTP kod + {error:{code,message}} (+ek alanlar)
 *     body()           → request['body'] (dizi/JSON) → form-urlencoded $_POST → JSON input
 *     clientIp()       → REMOTE_ADDR (orijinal davranış korunur — x-forwarded-for EKLENMEZ)
 *
 * Sözleşme (Faz 3a UI — DEĞİŞTİRİLEMEZ):
 *   başarı → {success:true, data:{...}}
 *   hata   → {error:{code,message}}  (422 → + error.fields — subclass ekler)
 */
abstract class AbstractController
{
    /**
     * 2xx + {success:true, data:{...}} (varsayılan 200 — davranış korunur).
     *
     * Adım 07: POST create 201 (Created) üretir; index.php 2xx status'u
     * korur (eski kod her <400 durumu 200'e zorlardı).
     */
    protected function success(array $data, int $status = 200): array
    {
        http_response_code($status);

        return ['success' => true, 'data' => $data];
    }

    /** HTTP kod + {error:{code,message}} (+ $extra: 422 error.fields vb.) */
    protected function error(string $code, string $message, int $status, array $extra = []): array
    {
        http_response_code($status);

        return ['error' => ['code' => $code, 'message' => $message] + $extra];
    }

    /**
     * Gövde çözümleme: request['body'] (dizi/JSON) → form-urlencoded $_POST → JSON input.
     * (AuthController orijinal gövdesiyle birebir — behavior-preserving taşıma.)
     *
     * @param array<string, mixed> $request
     * @return array<string, mixed>
     */
    protected function body(array $request): array
    {
        if (array_key_exists('body', $request)) {
            $body = $request['body'];
            if (is_array($body)) {
                return $body;
            }
            if (is_string($body)) {
                $decoded = json_decode($body, true);

                return is_array($decoded) ? $decoded : [];
            }

            return [];
        }

        $contentType = strtolower((string) ($request['server']['CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? ''));
        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return $_POST;
        }

        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * İstemci IP: REMOTE_ADDR (orijinal davranış — x-forwarded-for EKLENMEZ).
     *
     * @param array<string, mixed> $request
     */
    protected function clientIp(array $request): string
    {
        return (string) ($request['server']['REMOTE_ADDR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    }
}
