<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * YouTube (Google) OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Google OAuth2 flow.
 * Access token: kısa süreli, Refresh token: offline access ile.
 *
 * @see https://developers.google.com/identity/protocols/oauth2/web-server
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class YouTubeOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'youtube';
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $this->getScopes()),
            'state' => $state,
            'response_type' => 'code',
            'access_type' => $this->config['access_type'] ?? 'offline',
            'prompt' => 'consent',
        ];

        return $this->config['authorization_url'] . '?' . http_build_query($params);
    }

    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'YouTube token exchange failed: ' . ($result['data']['error_description'] ?? 'Unknown')
            );
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? null,
            'expires_in' => $result['data']['expires_in'] ?? 3600,
            'token_type' => $result['data']['token_type'] ?? 'Bearer',
            'scope' => $result['data']['scope'] ?? '',
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('YouTube refresh failed');
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? $refreshToken,
            'expires_in' => $result['data']['expires_in'] ?? 3600,
        ];
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/channels?part=snippet&mine=true',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('YouTube user info failed');
        }

        $items = $result['data']['items'] ?? [];
        $channel = $items[0] ?? [];
        $snippet = $channel['snippet'] ?? [];

        return [
            'provider_user_id' => $channel['id'] ?? '',
            'username' => $snippet['title'] ?? '',
            'display_name' => $snippet['title'] ?? '',
            'avatar' => ($snippet['thumbnails']['default']['url'] ?? null),
            'profile_data' => $channel,
        ];
    }

    public function revokeToken(string $token): bool
    {
        $result = $this->httpGet(
            'https://oauth2.googleapis.com/revoke?token=' . $token
        );

        return $result['status'] === 200;
    }
}
