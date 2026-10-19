<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Reddit OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Reddit OAuth2 flow.
 * Access token: kısa süreli, Refresh token mevcut.
 * Auth style: HTTP Basic (client_id:client_secret).
 *
 * @see https://github.com/reddit-archive/reddit/wiki/OAuth2
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class RedditOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'reddit';
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'state' => $state,
            'redirect_uri' => $this->redirectUri,
            'duration' => 'permanent',
            'scope' => implode(' ', $this->getScopes()),
        ]);

        return $this->config['authorization_url'] . '?' . $params;
    }

    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ],
            ['Authorization: ' . $this->getBasicAuthHeader()]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'Reddit token exchange failed: ' . ($result['data']['error'] ?? 'Unknown')
            );
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? null,
            'expires_in' => $result['data']['expires_in'] ?? 3600,
            'token_type' => $result['data']['token_type'] ?? 'bearer',
            'scope' => $result['data']['scope'] ?? '',
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
            ['Authorization: ' . $this->getBasicAuthHeader()]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Reddit refresh failed');
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
            $this->config['api_base'] . '/me',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Reddit user info failed');
        }

        $user = $result['data'];

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['name'] ?? '',
            'display_name' => $user['name'] ?? '',
            'avatar' => $user['icon_img'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        $result = $this->httpPost(
            'https://www.reddit.com/api/v1/revoke_token',
            [
                'token' => $token,
                'token_type_hint' => 'access_token',
            ],
            ['Authorization: ' . $this->getBasicAuthHeader()]
        );

        return $result['status'] === 200;
    }
}
