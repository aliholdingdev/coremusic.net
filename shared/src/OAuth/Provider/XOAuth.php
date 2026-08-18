<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * X (Twitter) OAuth 2.0 Provider — ADR-088 compliant.
 *
 * X OAuth 2.0 with PKCE (zorunlu).
 * Access token: 2 saat, Refresh token: 60 gün.
 * JWT token formatı.
 *
 * @see https://developer.x.com/en/docs/authentication/oauth-2-0
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class XOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'x';
    }

    public function requiresPkce(): bool
    {
        return true;
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $this->getScopes()),
            'state' => $state,
            'response_type' => 'code',
            'code_challenge' => $this->generateCodeChallenge($codeVerifier ?? $this->generateCodeVerifier()),
            'code_challenge_method' => 'S256',
        ];

        return $this->config['authorization_url'] . '?' . http_build_query($params);
    }

    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array
    {
        $result = $this->httpPost(
            $this->config['token_url'],
            [
                'client_id' => $this->clientId,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'code_verifier' => $codeVerifier,
            ],
            ['Content-Type: application/x-www-form-urlencoded']
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'X token exchange failed: ' . ($result['data']['error_description'] ?? 'Unknown')
            );
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? null,
            'expires_in' => $result['data']['expires_in'] ?? 7200,
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
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
            ['Content-Type: application/x-www-form-urlencoded']
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('X refresh failed');
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? $refreshToken,
            'expires_in' => $result['data']['expires_in'] ?? 7200,
        ];
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/users/me?user.fields=profile_image_url,name,username',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('X user info failed');
        }

        $user = $result['data']['data'] ?? [];

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['username'] ?? '',
            'display_name' => $user['name'] ?? '',
            'avatar' => $user['profile_image_url'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        $result = $this->httpPost(
            $this->config['revoke_url'] ?? '',
            [
                'client_id' => $this->clientId,
                'token' => $token,
                'token_type_hint' => 'access_token',
            ]
        );

        return $result['status'] === 200;
    }
}
