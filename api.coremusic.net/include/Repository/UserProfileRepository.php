<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;

/**
 * UserProfileRepository — coremusic_auth.users profil erişimi (IUserProfileRepository).
 *
 * Faz 4. Kullanıcı kimliği AuthenticationMiddleware'den gelir (`_auth_user.id`,
 * hex-string). ADR-002: prepared statement yalnız · SELECT * yok.
 * auth bağlantısı ApiAuthContainer::authRegistry() ile PAYLAŞILIR
 * (iki registry = iki bağlantı — UserRepository tekil kuralı).
 */
final class UserProfileRepository implements IUserProfileRepository
{
    private const DB_KEY = 'auth';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
    ) {}

    public function findByUserId(string $userId): ?array
    {
        if (strlen($userId) !== 32 || !ctype_xdigit($userId)) {
            return null;
        }

        $rows = $this->db()->execute(
            'SELECT HEX(u.id)          AS user_id_hex,
                    u.username         AS username,
                    u.email            AS email,
                    u.account_type     AS role,
                    u.display_name     AS display_name,
                    u.gender           AS gender,
                    u.avatar_url       AS avatar_url,
                    u.created_at       AS created_at
               FROM users u
              WHERE u.id = UNHEX(:id)
                AND u.is_active = 1
                AND u.is_deleted = 0
              LIMIT 1',
            ['id' => $userId]
        );

        return $rows === [] ? null : $rows[0];
    }

    public function updateProfile(string $userId, string $displayName, ?string $avatarUrl): bool
    {
        $this->db()->write(
            'UPDATE users
                SET display_name = :display_name,
                    avatar_url   = :avatar_url,
                    updated_at   = NOW()
              WHERE id = UNHEX(:id)
                AND is_active = 1
                AND is_deleted = 0',
            [
                'id'           => $userId,
                'display_name' => $displayName !== '' ? $displayName : null,
                'avatar_url'   => $avatarUrl !== '' ? $avatarUrl : null,
            ]
        );

        return true;
    }

    private function db(): IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }
}
