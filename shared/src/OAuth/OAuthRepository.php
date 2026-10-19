<?php declare(strict_types=1);

namespace CoreMusic\OAuth;

/**
 * OAuthRepository — oauth_connections + oauth_states veri erişimi.
 *
 * ADR-002: yalnız prepared statement; SQL string interpolation yok, SELECT * yok.
 * Bağlantı bu sınıf AÇMAZ — PDO dışarıdan enjekte edilir (OAuthManager constructor'ı).
 */
final class OAuthRepository
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    /**
     * Bağlantıyı kaydet (upsert — ON DUPLICATE KEY UPDATE).
     *
     * @param array{
     *     user_id: int, provider: string, provider_user_id: string,
     *     provider_username: string|null, access_token: string,
     *     refresh_token: string|null, expires_at: string|null,
     *     scopes: string|null, profile_data: string|null
     * } $params
     */
    public function upsertConnection(array $params): int
    {
        $sql = 'INSERT INTO oauth_connections
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
                     updated_at = CURRENT_TIMESTAMP';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$this->pdo->lastInsertId();
    }

    /** @return list<array<string, mixed>> */
    public function findUserConnections(int $userId): array
    {
        $sql = 'SELECT id, provider, provider_username, connected_at, last_used_at, is_active
                 FROM oauth_connections
                 WHERE user_id = :user_id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }

    /** Bağlantıyı sil (soft delete). */
    public function softDeleteConnection(int $userId, string $provider): bool
    {
        $sql = 'UPDATE oauth_connections
                 SET is_deleted = 1, is_active = 0
                 WHERE user_id = :user_id AND provider = :provider';

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'provider' => $provider,
        ]);
    }

    /**
     * State token'ı kaydet (CSRF koruması) — 10 dakika geçerlilik SQL'de.
     *
     * @param array{state: string, provider: string, user_id: int, code_verifier: string|null} $params
     */
    public function insertState(array $params): void
    {
        $sql = 'INSERT INTO oauth_states (state_token, provider, user_id, code_verifier, expires_at)
                 VALUES (:state, :provider, :user_id, :code_verifier, DATE_ADD(NOW(), INTERVAL 10 MINUTE))';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    /** Geçerli (süresi dolmamış) state'i bul. */
    public function findValidState(string $stateHash, string $provider, int $userId): ?array
    {
        $sql = 'SELECT code_verifier FROM oauth_states
                 WHERE state_token = :state AND provider = :provider
                   AND user_id = :user_id AND expires_at > NOW()';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'state' => $stateHash,
            'provider' => $provider,
            'user_id' => $userId,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /** State'i sil (tek kullanımlık). */
    public function deleteState(string $stateHash): bool
    {
        $sql = 'DELETE FROM oauth_states WHERE state_token = :state';

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['state' => $stateHash]);
    }
}
