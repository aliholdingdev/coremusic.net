<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * LinkedIn OAuth 2.0 Provider — ADR-088 compliant.
 *
 * LinkedIn OAuth2 flow.
 * Access token: kısa süreli, Refresh token mevcut değil (çoğu app).
 *
 * @see https://learn.microsoft.com/en-us/linked-in/consumer/integrations/self-serve/sign-in-with-linkedin-v2
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class LinkedInOAuth extends BaseOAuthProvider
{
    public function getProviderName(): string
    {
        return 'linkedin';
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
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException(
                'LinkedIn token exchange failed: ' . ($result['data']['error_description'] ?? 'Unknown')
            );
        }

        return [
            'access_token' => $result['data']['access_token'] ?? '',
            'expires_in' => $result['data']['expires_in'] ?? 5184000,
            'token_type' => $result['data']['token_type'] ?? 'Bearer',
            'scope' => $result['data']['scope'] ?? '',
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        // LinkedIn refresh token çoğu uygulamada desteklenmez
        throw new \RuntimeException('LinkedIn does not support refresh tokens for most applications.');
    }

    public function getUserInfo(string $accessToken): array
    {
        $result = $this->httpGet(
            $this->config['api_base'] . '/me?projection=(id,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))',
            ['Authorization: Bearer ' . $accessToken]
        );

        if ($result['status'] !== 200) {
            throw new \RuntimeException('LinkedIn user info failed');
        }

        $user = $result['data'];

        // Email al
        $email = null;
        $emailResult = $this->httpGet(
            $this->config['api_base'] . '/emailAddress?q=members&projection=(elements*(handle~))',
            ['Authorization: Bearer ' . $accessToken]
        );
        if ($emailResult['status'] === 200) {
            $elements = $emailResult['data']['elements'] ?? [];
            if (!empty($elements[0]['handle~']['emailAddress'])) {
                $email = $elements[0]['handle~']['emailAddress'];
            }
        }

        $avatar = null;
        $profilePicture = $user['profilePicture'] ?? null;
        if ($profilePicture) {
            $displayImage = $profilePicture['displayImage~'] ?? null;
            if ($displayImage) {
                $streams = $displayImage['playableStreams'] ?? [];
                foreach ($streams as $stream) {
                    if (($stream['encodingFormat'] ?? '') === 'image/jpeg') {
                        $avatar = $stream['components'][0]['identifiers'][0]['identifier'] ?? null;
                        break;
                    }
                }
            }
        }

        return [
            'provider_user_id' => $user['id'] ?? '',
            'username' => $user['id'] ?? '',
            'display_name' => ($user['localizedFirstName'] ?? '') . ' ' . ($user['localizedLastName'] ?? ''),
            'avatar' => $avatar,
            'email' => $email,
            'profile_data' => $user,
        ];
    }

    public function revokeToken(string $token): bool
    {
        // LinkedIn revoke endpoint mevcut değil
        return true;
    }
}
