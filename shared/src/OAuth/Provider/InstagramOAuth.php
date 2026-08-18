<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Instagram OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Meta Graph API OAuth flow.
 * Short-lived token: 1 saat → Long-lived token: 60 gün.
 * Requires Business/Creator account + Facebook Page.
 *
 * @see https://developers.facebook.com/docs/instagram-api/getting-started
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class InstagramOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'instagram';
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(',', $this->getScopes()),
            'state' => $state,
            'response_type' => 'code',
        ]);

        return $this->config['authorization_url'] . '?' . $params;
    }

    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array
    {
        // Short-lived token al
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'Instagram token exchange failed: ' . ($result['data']['error']['message'] ?? 'Unknown')
            );
        }

        $shortLivedToken = $result['data']['access_token'];

        // Long-lived token'a çevir (60 gün)
        $exchangeResult = $this->httpGet(
            $this->config['exchange_url'] . '?' . http_build_query([
                'grant_type' => 'fb_exchange_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'fb_exchange_token' => $shortLivedToken,
            ])
        );

        if ($exchangeResult['status'] === 200) {
            return [
                'access_token' => $exchangeResult['data']['access_token'],
                'expires_in' => 60 * 24 * 3600, // 60 gün
                'token_type' => 'bearer',
            ];
        }

        // Long-lived başarısızsa short-lived ile devam et
        return [
            'access_token' => $shortLivedToken,
            'expires_in' => 3600,
            'token_type' => 'bearer',
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        // Instagram'ın refresh token mekanizması yok, long-lived token kullanılır
        throw new \RuntimeException('Instagram does not support refresh tokens. Use long-lived token exchange.');
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/me?fields=id,username,name,profile_picture_url',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Instagram user info failed');
        }

        $user = $result['data'];

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['username'] ?? '',
            'display_name' => $user['name'] ?? '',
            'avatar' => $user['profile_picture_url'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        // Instagram token'ı iptal etme
        return true;
    }
}
