<?php declare(strict_types=1);

namespace CoreMusic\Home\Auth;

use CoreMusic\Home\Session\HomeSessionManager;

/**
 * Home Auth Bridge
 *
 * Cross-domain session transfer:
 * auth.coremusic.net'den gelen auth_key'i doğrular
 * ve home.coremusic.net'te session oluşturur.
 *
 * Akış:
 * 1. auth_callback.php auth_key alır
 * 2. Bu sınıf auth.coremusic.net/validate-key'e POST atar
 * 3. Başarılı → session oluşturur, user bilgilerini kaydeder
 * 4. Başarısız → false döner, auth_callback login'e yönlendirir
 */
final class HomeAuthBridge
{
    private const VALIDATE_TIMEOUT = 5;
    private const AUTH_KEY_TTL = 300;
    private const MAX_RETRIES = 2;
    private const RETRY_DELAY_US = 200_000; // 200ms

    private string $authUrl;

    public function __construct(
        private readonly HomeSessionManager $session,
        ?string $authUrl = null,
    ) {
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

        // Session oluştur
        $this->session->setAuthUser($userInfo);

        // Gender'ı session'a kaydet
        if (!empty($userInfo['gender'])) {
            $this->session->setGender($userInfo['gender']);
        }

        // Lifecycle keys — SessionInitializer bunları bekler, yoksa session'ı "expired" sayar
        $now = time();
        if (!isset($_SESSION['_session_created_at'])) {
            $_SESSION['_session_created_at'] = $now;
        }
        $_SESSION['_session_last_active'] = $now;
        $_SESSION['_session_rotated_at']  = $now;
        $_SESSION['last_activity']        = $now;

        return [
            'success' => true,
            'user'    => $userInfo,
        ];
    }

    /**
     * Mevcut session'da authenticated kullanıcı var mı?
     */
    public function isAuthenticated(): bool
    {
        return $this->session->isAuthenticated();
    }

    /**
     * Mevcut kullanıcı bilgisini döndür.
     */
    public function getCurrentUser(): ?array
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return null;
        }

        return [
            'id'           => $userId,
            'username'     => $this->session->get('MM_Username', ''),
            'email'        => $this->session->get('MM_Email', ''),
            'display_name' => $this->session->get('MM_DisplayName', ''),
            'account_type' => $this->session->get('MM_AccountType', 'free'),
            'avatar_url'   => $this->session->get('MM_Image', ''),
            'gender'       => $this->session->getGender(),
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
            error_log('[HomeAuthBridge] curl_init failed');
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
        curl_close($ch);

        if ($response === false || $error !== '') {
            error_log('[HomeAuthBridge] curl error: ' . ($error ?: 'unknown'));
            return null;
        }

        if ($httpCode !== 200) {
            error_log('[HomeAuthBridge] validate-key returned HTTP ' . $httpCode);
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            error_log('[HomeAuthBridge] validate-key: invalid JSON');
            return null;
        }

        // Response format: {httpStatus, type, body: {success, user}} veya {success, user}
        $body = $data['body'] ?? $data;
        if (empty($body['success']) || empty($body['user'])) {
            error_log('[HomeAuthBridge] validate-key: success=false or no user');
            return null;
        }

        return $body['user'];
    }
}
