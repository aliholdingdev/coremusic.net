<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Discord OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Discord OAuth2 flow.
 * Access token: kısa süreli, Refresh token mevcut.
 *
 * @see https://discord.com/developers/docs/topics/oauth2
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class DiscordOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'discord';
    }

    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $this->getScopes()),
            'state' => $state,
            'response_type' => 'code',
            'prompt' => 'consent',
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
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'Discord token exchange failed: ' . ($result['data']['error_description'] ?? 'Unknown')
            );
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? null,
            'expires_in' => $result['data']['expires_in'] ?? 604800,
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
            throw new \RuntimeException('Discord refresh failed');
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'refresh_token' => $result['data']['refresh_token'] ?? $refreshToken,
            'expires_in' => $result['data']['expires_in'] ?? 604800,
        ];
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/users/@me',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Discord user info failed');
        }

        $user = $result['data'];

        $avatar = null;
        if (!empty($user['avatar'])) {
            $ext = str_starts_with($user['avatar'], 'a_') ? 'gif' : 'png';
            $avatar = "https://cdn.discordapp.com/avatars/{$user['id']}/{$user['avatar']}.{$ext}";
        }

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['username'] ?? '',
            'display_name' => $user['global_name'] ?? $user['username'] ?? '',
            'avatar' => $avatar,
            'email' => $user['email'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        $result = $this->httpPost(
            $this->config['api_base'] . '/oauth2/token/revoke',
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'token' => $token,
                'token_type_hint' => 'access_token',
            ]
        );

        return $result['status'] === 204;
    }
}
