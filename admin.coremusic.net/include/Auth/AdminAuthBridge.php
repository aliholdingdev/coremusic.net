<?php declare(strict_types=1);

namespace CoreMusic\Admin\Auth;

use CoreMusic\Session\SessionBootstrapper;

/**
 * Admin Auth Bridge
 *
 * Cross-domain session transfer:
 * auth.coremusic.net'den gelen auth_key'i doğrular
 * ve admin.coremusic.net'te session oluşturur.
 *
 * Desen: home.coremusic.net/include/Auth/HomeAuthBridge.php ile birebir aynı akış.
 * Session tarafı shared/src/Session/ (SessionBootstrapper) üzerinden yürür —
 * admin'e özel ayrı bir session manager sınıfı yoktur (YAGNI).
 *
 * Akış:
 * 1. /auth/callback auth_key alır
 * 2. Bu sınıf auth.coremusic.net/validate-key'e POST atar
 * 3. Başarılı → session oluşturur, user bilgilerini kaydeder
 * 4. Başarısız → false döner, callback login'e yönlendirir
 */
final class AdminAuthBridge
{
    private const VALIDATE_TIMEOUT = 5;
    private const MAX_RETRIES = 2;
    private const RETRY_DELAY_US = 200_000; // 200ms

    private string $authUrl;

    public function __construct(?string $authUrl = null)
    {
        $this->authUrl = $authUrl ?? (defined('AUTH_URL') ? AUTH_URL : '');
    }

    /**
     * auth_key'i auth.coremusic.net'te doğrula ve session oluştur.
     *
     * @return array{success: bool, user?: array, error?: string}
     */
    public function validateAndCreateSession(string $authKey): array
    {
        if ($authKey === '') {
            return ['success' => false, 'error' => 'empty_key'];
        }

        // auth.coremusic.net/validate-key'e POST at
        $userInfo = $this->callValidateKey($authKey);

        if ($userInfo === null) {
            return ['success' => false, 'error' => 'invalid_key'];
        }

        // Session oluştur (session fixation: doğrulama sonrası ID rotasyonu dahil)
        $this->establishSession($userInfo);

        return [
            'success' => true,
            'user'    => $userInfo,
        ];
    }

    /**
     * Doğrulanmış kullanıcı bilgisiyle admin session'ını kurar.
     *
     * Session fixation: setAuthUser'dan hemen sonra session_regenerate_id(true)
     * zorunlu — auth.coremusic.net'teki login/register deseniyle aynı.
     */
    public function establishSession(array $userInfo): void
    {
        SessionBootstrapper::ensureStarted();

        // UUID hex string olarak sakla — aynı MM_* anahtarları (cross-domain uyum)
        $_SESSION['MM_UserID']      = $userInfo['user_id'] ?? $userInfo['id'] ?? '';
        $_SESSION['MM_Username']    = $userInfo['username'] ?? '';
        $_SESSION['MM_Email']       = $userInfo['email'] ?? '';
        $_SESSION['MM_DisplayName'] = $userInfo['display_name'] ?? ($userInfo['username'] ?? '');
        $_SESSION['MM_AccountType'] = $userInfo['account_type'] ?? 'free';
        $_SESSION['MM_Image']       = $userInfo['avatar_url'] ?? '';
        $_SESSION['MM_Gender']      = $userInfo['gender'] ?? 'neutral';

        session_regenerate_id(true);

        // Lifecycle keys — SessionInitializer bunları bekler, yoksa session'ı "expired" sayar
        $now = time();
        if (!isset($_SESSION['_session_created_at'])) {
            $_SESSION['_session_created_at'] = $now;
        }
        $_SESSION['_session_last_active'] = $now;
        $_SESSION['_session_rotated_at']  = $now;
        $_SESSION['last_activity']        = $now;
    }

    /**
     * Mevcut session'da authenticated kullanıcı var mı?
     */
    public function isAuthenticated(): bool
    {
        $userId = $_SESSION['MM_UserID'] ?? null;
        return is_string($userId) && $userId !== '';
    }

    /**
     * Mevcut kullanıcı bilgisini döndür.
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id'           => $_SESSION['MM_UserID'],
            'username'     => $_SESSION['MM_Username'] ?? '',
            'email'        => $_SESSION['MM_Email'] ?? '',
            'display_name' => $_SESSION['MM_DisplayName'] ?? '',
            'account_type' => $_SESSION['MM_AccountType'] ?? 'free',
            'avatar_url'   => $_SESSION['MM_Image'] ?? '',
            'gender'       => $_SESSION['MM_Gender'] ?? 'neutral',
        ];
    }

    /**
     * auth.coremusic.net/validate-key endpoint'ine retry ile POST isteği gönder.
     */
    private function callValidateKey(string $authKey): ?array
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            $result = $this->doValidateKey($authKey);
            if ($result !== null) {
                return $result;
            }
            if ($attempt < self::MAX_RETRIES) {
                usleep(self::RETRY_DELAY_US);
            }
        }
        return null;
    }

    /**
     * Tek bir validate-key POST isteği gönder.
     */
    private function doValidateKey(string $authKey): ?array
    {
        $url = $this->authUrl . '/validate-key';

        $payload = json_encode(['auth_key' => $authKey], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        if ($ch === false) {
            error_log('[AdminAuthBridge] curl_init failed');
            return null;
        }

        $isLocal = str_contains($this->authUrl, 'localhost') || str_contains($this->authUrl, '127.0.0.1');

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => self::VALIDATE_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::VALIDATE_TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest',
            ],
            CURLOPT_SSL_VERIFYPEER => !$isLocal,
            CURLOPT_SSL_VERIFYHOST => $isLocal ? 0 : 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        // curl_close() PHP 8.0+'da etkisiz, 8.5'te deprecated — handle $ch
        // scope sonunda otomatik kapanır (GC). Bilinçli kaldırıldı (2026-10-19).

        if ($response === false || $error !== '') {
            error_log('[AdminAuthBridge] curl error: ' . ($error ?: 'unknown'));
            return null;
        }

        if ($httpCode !== 200) {
            error_log('[AdminAuthBridge] validate-key returned HTTP ' . $httpCode);
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            error_log('[AdminAuthBridge] validate-key: invalid JSON');
            return null;
        }

        // Response format: {httpStatus, type, body: {success, user}} veya {success, user}
        $body = $data['body'] ?? $data;
        if (empty($body['success']) || empty($body['user'])) {
            error_log('[AdminAuthBridge] validate-key: success=false or no user');
            return null;
        }

        return $body['user'];
    }
}
