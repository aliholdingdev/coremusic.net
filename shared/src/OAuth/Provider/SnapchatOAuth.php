<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Snapchat OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Snapchat Login Kit OAuth flow.
 * Access token: 30 gün, Refresh token: 365 gün.
 *
 * @see https://developers.snap.com/api/login-kit
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class SnapchatOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'snapchat';
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $this->getScopes()),
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
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'Snapchat token exchange failed: ' . ($result['data']['error'] ?? 'Unknown')
            );
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? null,
            'expires_in' => $result['data']['expires_in'] ?? 2592000,
            'token_type' => $result['data']['token_type'] ?? 'bearer',
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
            throw new \RuntimeException('Snapchat refresh failed');
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? $refreshToken,
            'expires_in' => $result['data']['expires_in'] ?? 2592000,
        ];
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/users/me',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Snapchat user info failed');
        }

        $user = $result['data'] ?? [];

        return [
            'provider_user_id' => $user['sub'] ?? '',
            'username' => $user['preferred_username'] ?? '',
            'display_name' => $user['name'] ?? '',
            'avatar' => $user['bitmoji']['avatar_url'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        $result = $this->httpPost(
            $this->config['api_base'] . '/auth/revoke',
            ['token' => $token]
        );

        return $result['status'] === 200;
    }
}
