<?php declare(strict_types=1);

/**
 * CoreMusic — OAuth Post Handler
 *
 * ADR-088 compliant — Gender-based social OAuth request handler.
 * auth.coremusic.net POST isteklerini işler.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */

declare(strict_types=1);

namespace CoreMusic\Auth\Handler;

use CoreMusic\OAuth\OAuthManager;

/**
 * OAuth POST handler — connect, disconnect, callback.
 */
final class OAuthPostHandler
{
    private OAuthManager $oauthManager;
    private string $baseUrl;

    public function __construct(OAuthManager $oauthManager, string $baseUrl = 'auth.coremusic.net')
    {
        $this->oauthManager = $oauthManager;
        $this->baseUrl = $baseUrl;
    }

    /**
     * OAuth bağlama işlemini başlat.
     *
     * @param string $provider Platform adı (instagram, pinterest, vb.)
     * @param int $userId Kullanıcı ID
     * @return array{success: bool, redirect?: string, error?: string}
     */
    public function handleConnect(string $provider, int $userId): array
    {
        // Provider'ı oluştur
        $redirectUri = "https://{$this->baseUrl}/oauth-callback?provider={$provider}";
        $providerInstance = $this->oauthManager->createProvider($provider, $redirectUri);

        // State üret (CSRF koruması)
        $state = bin2hex(random_bytes(16));

        // PKCE için code verifier (X/Twitter)
        $codeVerifier = null;
        if ($providerInstance->requiresPkce()) {
            $codeVerifier = bin2hex(random_bytes(32));
        }

        // State'i kaydet
        $this->oauthManager->saveState($state, $provider, $userId, $codeVerifier);

        // Authorization URL oluştur
        $authUrl = $providerInstance->getAuthorizationUrl($state, $codeVerifier);

        return [
            'success' => true,
            'redirect' => $authUrl,
        ];
    }

    /**
     * OAuth callback işlemini yönet.
     *
     * @param string $provider Platform adı
     * @param string $code Authorization code
     * @param string $state CSRF state token
     * @param int $userId Kullanıcı ID
     * @return array{success: bool, message: string, connection?: array}
     */
    public function handleCallback(
        string $provider,
        string $code,
        string $state,
        int $userId
    ): array {
        // State doğrula
        $stateData = $this->oauthManager->validateState($state, $provider, $userId);
        if ($stateData === null) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OAuth state. Please try again.',
            ];
        }

        try {
            // Provider'ı oluştur
            $redirectUri = "https://{$this->baseUrl}/oauth-callback?provider={$provider}";
            $providerInstance = $this->oauthManager->createProvider($provider, $redirectUri);

            // Code'u token ile değiştir
            $tokenData = $providerInstance->exchangeCodeForToken(
                $code,
                $stateData['code_verifier']
            );

            // Kullanıcı bilgilerini al
            $userData = $providerInstance->getUserInfo($tokenData['access_token']);

            // Veritabanına kaydet
            $connectionId = $this->oauthManager->saveConnection(
                $userId,
                $provider,
                $tokenData,
                $userData
            );

            return [
                'success' => true,
                'message' => "Successfully connected to {$provider}",
                'connection' => [
                    'id' => $connectionId,
                    'provider' => $provider,
                    'username' => $userData['username'] ?? '',
                    'display_name' => $userData['display_name'] ?? '',
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "OAuth connection failed: {$e->getMessage()}",
            ];
        }
    }

    /**
     * OAuth bağlantısını kes.
     */
    public function handleDisconnect(string $provider, int $userId): array
    {
        $success = $this->oauthManager->disconnect($userId, $provider);

        return [
            'success' => $success,
            'message' => $success
                ? "Disconnected from {$provider}"
                : "Failed to disconnect from {$provider}",
        ];
    }

    /**
     * Kullanıcının tüm OAuth bağlantılarını listele.
     */
    public function handleList(int $userId): array
    {
        $connections = $this->oauthManager->getUserConnections($userId);

        return [
            'success' => true,
            'connections' => $connections,
        ];
    }
}
