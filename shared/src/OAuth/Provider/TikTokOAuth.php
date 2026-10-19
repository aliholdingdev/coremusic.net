<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * TikTok OAuth 2.0 Provider — ADR-088 compliant.
 *
 * TikTok Content Posting API OAuth flow.
 * Access token: 24 saat, Refresh token: 365 gün.
 * Refresh token rotasyonu zorunlu.
 *
 * @see https://developers.tiktok.com/doc/oauth-access-token
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class TikTokOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'tiktok';
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = http_build_query([
            'client_key' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(',', $this->getScopes()),
            'state' => $state,
            'response_type' => 'code',
        ]);

        return $this->config['authorization_url'] . '?' . $params;
    }

    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'client_key' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'TikTok token exchange failed: ' . ($result['data']['error']['message'] ?? 'Unknown')
            );
        }

        $data = $result['data']['data'] ?? $result['data'];

        return [
            'access_token' => $data['access_token'] ?? '',
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in' => $data['expires_in'] ?? 86400,
            'token_type' => 'bearer',
            'scope' => $data['scope'] ?? '',
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'client_key' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('TikTok refresh failed');
        }

        $data = $result['data']['data'] ?? $result['data'];

        return [
            'access_token' => $data['access_token'] ?? '',
            'refresh_token' => $data['refresh_token'] ?? $refreshToken,
            'expires_in' => $data['expires_in'] ?? 86400,
        ];
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpPost(
            $this->config['api_base'] . '/user/info/',
            [],
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('TikTok user info failed');
        }

        $user = $result['data']['data']['user'] ?? [];

        return [
            'provider_user_id' => (string)($user['uid'] ?? ''),
            'username' => $user['username'] ?? '',
            'display_name' => $user['nickname'] ?? '',
            'avatar' => $user['avatar_medium']['url_list'][0] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        // TikTok token revoke endpoint mevcut değil
        return true;
    }
}
