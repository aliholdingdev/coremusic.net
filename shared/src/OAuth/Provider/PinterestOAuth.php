<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Pinterest OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Pinterest API v5 OAuth 2.0 flow.
 * Access token: 30 gün, Refresh token: 365 gün.
 * Auth style: HTTP Basic (client_id:client_secret).
 *
 * @see https://developers.pinterest.com/docs/oauth/
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class PinterestOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'pinterest';
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
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ],
            ['Authorization: ' . $this->getBasicAuthHeader()]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'Pinterest token exchange failed: ' . ($result['data']['message'] ?? 'Unknown error')
            );
        }

        return $result['data'];
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
            throw new \RuntimeException('Pinterest refresh failed');
        }

        return $result['data'];
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/user_account',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Pinterest user info failed');
        }

        $user = $result['data'];

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['username'] ?? '',
            'display_name' => $user['first_name'] . ' ' . $user['last_name'],
            'avatar' => $user['profile_image'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        // Pinterest revoke endpoint mevcut değil, token expiration ile yönetilir
        return true;
    }
}
