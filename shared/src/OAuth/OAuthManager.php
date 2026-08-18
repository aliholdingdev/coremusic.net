<?php declare(strict_types=1);

namespace CoreMusic\OAuth;

use CoreMusic\OAuth\Provider\IOAuthProvider;
use CoreMusic\OAuth\Provider\{
    PinterestOAuth,
    InstagramOAuth,
    TikTokOAuth,
    SnapchatOAuth,
    DiscordOAuth,
    RedditOAuth,
    XOAuth,
    LinkedInOAuth,
    YouTubeOAuth,
    FacebookOAuth
};

/**
 * OAuth Manager — Merkezi OAuth yönetimi.
 *
 * Cinsiyete göre platform listesi, token yönetimi, şifreleme.
 * ADR-088 + ADR-022 + ADR-044 compliant.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 * @see [[decisions/accepted/ADR-022-database-hardened-security]]
 * @see [[decisions/accepted/ADR-044-dynamic-user-theme-engine]]
 */
final class OAuthManager
{
    private \PDO $pdo;
    private array $platformConfig;
    private string $encryptionKey;

    public function __construct(\PDO $pdo, array $platformConfig, string $encryptionKey)
    {
        $this->pdo = $pdo;
        $this->platformConfig = $platformConfig;
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * Cinsiyete göre OAuth provider listesini döndür.
     *
     * @param string $gender 'female', 'male' veya 'neutral'
     * @return array<array-key, array{name: string, icon: string, color: string, provider: string}>
     */
    public function getPlatformsByGender(string $gender): array
    {
        $gender = in_array($gender, ['female', 'male']) ? $gender : 'neutral';
        $platforms = $this->platformConfig[$gender]['platforms'] ?? [];

        $result = [];
        foreach ($platforms as $provider => $config) {
            $result[] = [
                'provider' => $provider,
                'name' => $config['name'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'priority' => $config['priority'] ?? 99,
                'female_percent' => $config['female_percent'] ?? $config['male_percent'] ?? null,
            ];
        }

        // Priority'ye göre sırala
        usort($result, fn(array $a, array $b) => $a['priority'] <=> $b['priority']);

        return $result;
    }

    /**
     * Provider instance'ı oluştur.
     */
    public function createProvider(string $providerName, string $redirectUri): IOAuthProvider
    {
        $envKey = $this->getEnvKey($providerName);
        $clientId = getenv($envKey['client_id']) ?: '';
        $clientSecret = getenv($envKey['client_secret']) ?: '';

        if (empty($clientId) || empty($clientSecret)) {
            throw new \RuntimeException("OAuth credentials not configured for: {$providerName}");
        }

        // Config'den platform bilgilerini bul
        $config = $this->findPlatformConfig($providerName);
        if ($config === null) {
            throw new \RuntimeException("Unknown OAuth provider: {$providerName}");
        }

        return match ($providerName) {
            'pinterest' => new PinterestOAuth($clientId, $clientSecret, $redirectUri, $config),
            'instagram' => new InstagramOAuth($clientId, $clientSecret, $redirectUri, $config),
            'tiktok' => new TikTokOAuth($clientId, $clientSecret, $redirectUri, $config),
            'snapchat' => new SnapchatOAuth($clientId, $clientSecret, $redirectUri, $config),
            'discord' => new DiscordOAuth($clientId, $clientSecret, $redirectUri, $config),
            'reddit' => new RedditOAuth($clientId, $clientSecret, $redirectUri, $config),
            'x' => new XOAuth($clientId, $clientSecret, $redirectUri, $config),
            'linkedin' => new LinkedInOAuth($clientId, $clientSecret, $redirectUri, $config),
            'youtube' => new YouTubeOAuth($clientId, $clientSecret, $redirectUri, $config),
            'facebook' => new FacebookOAuth($clientId, $clientSecret, $redirectUri, $config),
            default => throw new \RuntimeException("Unknown provider: {$providerName}"),
        };
    }

    /**
     * OAuth bağlantısını kaydet.
     */
    public function saveConnection(
        int $userId,
        string $provider,
        array $tokenData,
        array $userData
    ): int {
        $accessTokenEncrypted = $this->encrypt($tokenData['access_token']);
        $refreshTokenEncrypted = isset($tokenData['refresh_token'])
            ? $this->encrypt($tokenData['refresh_token'])
            : null;

        $stmt = $this->pdo->prepare(
            'INSERT INTO oauth_connections
                (user_id, provider, provider_user_id, provider_username,
                 access_token_encrypted, refresh_token_encrypted,
                 token_expires_at, scopes, profile_data)
             VALUES
                (:user_id, :provider, :provider_user_id, :provider_username,
                 :access_token, :refresh_token,
                 :expires_at, :scopes, :profile_data)
             ON DUPLICATE KEY UPDATE
                 access_token_encrypted = VALUES(access_token_encrypted),
                 refresh_token_encrypted = VALUES(refresh_token_encrypted),
                 token_expires_at = VALUES(token_expires_at),
                 scopes = VALUES(scopes),
                 profile_data = VALUES(profile_data),
                 is_active = 1,
                 updated_at = CURRENT_TIMESTAMP'
        );

        $expiresAt = isset($tokenData['expires_in'])
            ? date('Y-m-d H:i:s', time() + $tokenData['expires_in'])
            : null;

        $stmt->execute([
            'user_id' => $userId,
            'provider' => $provider,
            'provider_user_id' => $userData['provider_user_id'],
            'provider_username' => $userData['username'] ?? null,
            'access_token' => $accessTokenEncrypted,
            'refresh_token' => $refreshTokenEncrypted,
            'expires_at' => $expiresAt,
            'scopes' => $tokenData['scope'] ?? null,
            'profile_data' => json_encode($userData['profile_data'] ?? []),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Kullanıcının tüm OAuth bağlantılarını getir.
     *
     * @return array<array-key, array>
     */
    public function getUserConnections(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, provider, provider_username, connected_at, last_used_at, is_active
             FROM oauth_connections
             WHERE user_id = :user_id AND is_deleted = 0'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * OAuth bağlantısını sil (soft delete).
     */
    public function disconnect(int $userId, string $provider): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE oauth_connections
             SET is_deleted = 1, is_active = 0
             WHERE user_id = :user_id AND provider = :provider'
        );

        return $stmt->execute([
            'user_id' => $userId,
            'provider' => $provider,
        ]);
    }

    /**
     * State token'ı kaydet (CSRF koruması).
     */
    public function saveState(string $state, string $provider, int $userId, ?string $codeVerifier = null): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO oauth_states (state_token, provider, user_id, code_verifier, expires_at)
             VALUES (:state, :provider, :user_id, :code_verifier, DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
        );

        $stmt->execute([
            'state' => hash('sha256', $state),
            'provider' => $provider,
            'user_id' => $userId,
            'code_verifier' => $codeVerifier,
        ]);
    }

    /**
     * State token'ı doğrula ve sil.
     */
    public function validateState(string $state, string $provider, int $userId): ?array
    {
        $hash = hash('sha256', $state);

        $stmt = $this->pdo->prepare(
            'SELECT code_verifier FROM oauth_states
             WHERE state_token = :state AND provider = :provider
               AND user_id = :user_id AND expires_at > NOW()'
        );
        $stmt->execute([
            'state' => $hash,
            'provider' => $provider,
            'user_id' => $userId,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        // State'i sil (tek kullanımlık)
        $deleteStmt = $this->pdo->prepare(
            'DELETE FROM oauth_states WHERE state_token = :state'
        );
        $deleteStmt->execute(['state' => $hash]);

        return ['code_verifier' => $row['code_verifier'] ?? null];
    }

    /**
     * Access token'ı AES-256-GCM ile şifrele.
     */
    private function encrypt(string $plaintext): string
    {
        $iv = random_bytes(12);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Şifreli token'ı çöz.
     */
    public function decrypt(string $encrypted): string
    {
        $decoded = base64_decode($encrypted, true);
        if ($decoded === false || strlen($decoded) < 29) {
            throw new \RuntimeException('Invalid encrypted token');
        }

        $iv = substr($decoded, 0, 12);
        $tag = substr($decoded, 12, 16);
        $ciphertext = substr($decoded, 28);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed');
        }

        return $plaintext;
    }

    /**
     * .env değişken adlarını döndür.
     */
    private function getEnvKey(string $provider): array
    {
        $mapping = $this->platformConfig['env_mapping'] ?? [];
        return $mapping[$provider] ?? [
            'client_id' => 'OAUTH_' . strtoupper($provider) . '_CLIENT_ID',
            'client_secret' => 'OAUTH_' . strtoupper($provider) . '_CLIENT_SECRET',
        ];
    }

    /**
     * Tüm gender'lardan provider config'ini bul.
     */
    private function findPlatformConfig(string $providerName): ?array
    {
        foreach (['female', 'male', 'neutral'] as $gender) {
            $platforms = $this->platformConfig[$gender]['platforms'] ?? [];
            if (isset($platforms[$providerName])) {
                return $platforms[$providerName];
            }
        }
        return null;
    }
}
