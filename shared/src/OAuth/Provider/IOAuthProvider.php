<?php declare(strict_types=1);

namespace CoreMusic\OAuth\Provider;

/**
 * OAuth Provider Interface — ADR-088 compliant.
 *
 * Tüm OAuth provider'lar bu interface'i implemente etmelidir.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
interface IOAuthProvider
{
    /**
     * Provider adını döndür.
     */
    public function getProviderName(): string;

    /**
     * Authorization URL oluştur.
     *
     * @param string $state CSRF koruması için state parametresi
     * @param string|null $codeVerifier PKCE için (sadece X/Twitter)
     * @return string Authorization URL
     */
    public function getAuthorizationUrl(string $state, ?string $codeVerifier = null): string;

    /**
     * Authorization code'u token ile değiştir.
     *
     * @param string $code Authorization code
     * @param string|null $codeVerifier PKCE için code verifier
     * @return array{access_token: string, refresh_token?: string, expires_in?: int, token_type?: string, scope?: string}
     * @throws \RuntimeException Token exchange başarısız olursa
     */
    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array;

    /**
     * Refresh token ile yeni access token al.
     *
     * @param string $refreshToken Refresh token
     * @return array{access_token: string, refresh_token?: string, expires_in?: int}
     * @throws \RuntimeException Refresh başarısız olursa
     */
    public function refreshToken(string $refreshToken): array;

    /**
     * Kullanıcı bilgilerini al.
     *
     * @param string $accessToken Access token
     * @return array{provider_user_id: string, username?: string, display_name?: string, avatar?: string, email?: string, profile_data?: array}
     * @throws \RuntimeException API çağrısı başarısız olursa
     */
    public function getUserInfo(string $accessToken): array;

    /**
     * Token'ı iptal et (revoke).
     *
     * @param string $token Access veya refresh token
     * @return bool Başarılı mı?
     */
    public function revokeToken(string $token): bool;

    /**
     * Provider'ın desteklediği scope'ları döndür.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * Provider'ın OAuth versiyonunu döndür.
     */
    public function getOAuthVersion(): string;

    /**
     * PKCE gereksinimi olup olmadığını döndür.
     */
    public function requiresPkce(): bool;
}
