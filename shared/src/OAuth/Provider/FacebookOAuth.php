<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * Facebook OAuth 2.0 Provider — ADR-088 compliant.
 *
 * Meta Graph API OAuth flow.
 * Short-lived token: 1 saat → Long-lived token: 60 gün.
 *
 * @see https://developers.facebook.com/docs/.facebook-login/guides/access-tokens
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class FacebookOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'facebook';
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
        $result = $this->httpGet(
            $this->config['token_url'] . '?' . http_build_query([
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
            ])
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'Facebook token exchange failed: ' . ($result['data']['error']['message'] ?? 'Unknown')
            );
        }

        $shortLivedToken = $result['data']['access_token'];

        // Long-lived token'a çevir
        $exchangeResult = $this->httpGet(
            $this->config['api_base'] . '/oauth/access_token?' . http_build_query([
                'grant_type' => 'fb_exchange_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'fb_exchange_token' => $shortLivedToken,
            ])
        );

        if ($exchangeResult['status'] === 200) {
            return [
                'access_token' => $exchangeResult['data']['access_token'] ?? $shortLivedToken,
                'expires_in' => 60 * 24 * 3600,
                'token_type' => 'bearer',
            ];
        }

        return [
            'access_token' => $shortLivedToken,
            'expires_in' => 3600,
            'token_type' => 'bearer',
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        throw new \RuntimeException('Facebook does not support refresh tokens. Use long-lived token exchange.');
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/me?fields=id,name,email,picture.width(200).height(200)',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('Facebook user info failed');
        }

        $user = $result['data'];

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['name'] ?? '',
            'display_name' => $user['name'] ?? '',
            'avatar' => $user['picture']['data']['url'] ?? null,
            'email' => $user['email'] ?? null,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        // Facebook token'ı iptal etme
        return true;
    }
}
