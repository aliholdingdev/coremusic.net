<?php declare(strict_types=1);

namespace CoreMusic\Auth\Repository;

use CoreMusic\Interfaces\Auth\IUserRepository;
use CoreMusic\Interfaces\Database\IDatabaseRegistry;
use CoreMusic\Security\UuidV7;

/**
 * User Repository — BCNF uyumlu, BINARY(16) UUID v7 destekli
 *
 * DB şeması: coremusic_auth (13 tablo)
 * - id: BINARY(16) UUID v7
     * - gender ENUM: male|female|neutral
 * - token_type ENUM: password_reset|email_verify|api_key|refresh|access
 */
final class UserRepository implements IUserRepository
{
    private const DB_KEY = 'auth';
    private const USER_COLUMNS = 'id, username, email, password_hash, display_name, gender, avatar_url, account_type, is_active, is_banned, last_login_at, created_at, updated_at';
    private const ACTIVE_USER_WHERE = 'AND is_active = 1 AND is_deleted = 0';

    public function __construct(
        private readonly IDatabaseRegistry $registry
    ) {}

    private function db(): \CoreMusic\Interfaces\Database\IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }

    public function findByCredential(string $identity): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE (email = :identity OR username = :identity2) ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['identity' => $identity, 'identity2' => $identity]
        );
        if (empty($rows)) {
            return null;
        }
        $user = $rows[0];
        $user['id'] = UuidV7::toHex($user['id']);
        return $user;
    }

    public function findByEmail(string $email): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE email = :email ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['email' => $email]
        );
        if (empty($rows)) {
            return null;
        }
        $user = $rows[0];
        $user['id'] = UuidV7::toHex($user['id']);
        return $user;
    }

    public function findByUsername(string $username): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE username = :username ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['username' => $username]
        );
        if (empty($rows)) {
            return null;
        }
        $user = $rows[0];
        $user['id'] = UuidV7::toHex($user['id']);
        return $user;
    }

    public function findById(int $userId): ?array
    {
        // UUID binary(16) destekli findById
        $binaryId = pack('H*', str_pad(dechex($userId), 32, '0', STR_PAD_LEFT));
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE id = :id ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['id' => $binaryId]
        );
        if (empty($rows)) {
            return null;
        }
        $user = $rows[0];
        $user['id'] = UuidV7::toHex($user['id']);
        return $user;
    }

    /**
     * UUID hex ile kullanıcı bul.
     */
    public function findByIdHex(string $uuidHex): ?array
    {
        $binaryId = UuidV7::toBinary($uuidHex);
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE id = :id ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['id' => $binaryId]
        );
        if (empty($rows)) {
            return null;
        }
        $user = $rows[0];
        $user['id'] = UuidV7::toHex($user['id']);
        return $user;
    }

    public function create(array $userData): array
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            $userId = UuidV7::generateBinary();

            $db->write(
                'INSERT INTO users (id, username, email, password_hash, display_name, gender, account_type, is_active, created_at, updated_at) VALUES (:id, :username, :email, :password_hash, :display_name, :gender, :account_type, :is_active, NOW(), NOW())',
                [
                    'id'             => $userId,
                    'username'       => $userData['username'],
                    'email'          => $userData['email'],
                    'password_hash'  => $userData['password_hash'],
                    'display_name'   => $userData['display_name'] ?? $userData['username'],
                    'gender'         => $userData['gender'] ?? 'neutral',
                    'account_type'   => $userData['account_type'] ?? 'free',
                    'is_active'      => 1,
                ]
            );

            // free_user rolünü ata
            $roleRow = $db->execute(
                "SELECT id FROM user_roles WHERE role_name = :role_name AND is_deleted = 0 LIMIT 1",
                ['role_name' => 'free_user']
            );

            if (!empty($roleRow)) {
                $assignedRoleId = UuidV7::generateBinary();
                $db->write(
                    'INSERT INTO user_assigned_roles (id, user_id, role_id, assigned_at, created_at, updated_at) VALUES (:id, UNHEX(:user_id), :role_id, NOW(), NOW(), NOW())',
                    [
                        'id'      => $assignedRoleId,
                        'user_id' => UuidV7::toHex($userId),
                        'role_id' => $roleRow[0]['id'],
                    ]
                );
            }

            $db->commit();
            return [
                'user_id'   => UuidV7::toHex($userId),
                'username'  => $userData['username'] ?? '',
                'email'     => $userData['email'] ?? '',
                'role_name' => 'free_user',
            ];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function updateLastLogin(string $userId): void
    {
        $this->db()->write('UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = UNHEX(:id)', ['id' => $userId]);
    }

    public function emailExists(string $email): bool
    {
        $rows = $this->db()->execute('SELECT id FROM users WHERE email = :email AND is_deleted = 0 LIMIT 1', ['email' => $email]);
        return !empty($rows);
    }

    public function usernameExists(string $username): bool
    {
        $rows = $this->db()->execute('SELECT id FROM users WHERE username = :username AND is_deleted = 0 LIMIT 1', ['username' => $username]);
        return !empty($rows);
    }

    public function saveResetToken(string $userId, string $tokenHash, string $expiresAt, ?string $clientIp = null): void
    {
        $tokenId = UuidV7::generateBinary();
        $this->db()->write(
            'INSERT INTO user_tokens (id, user_id, token_type, token_hash, expires_at, ip_address, created_at) VALUES (:id, UNHEX(:user_id), :token_type, :token_hash, :expires_at, :ip_address, NOW())',
            [
                'id'          => $tokenId,
                'user_id'     => $userId,
                'token_type'  => 'password_reset',
                'token_hash'  => $tokenHash,
                'expires_at'  => $expiresAt,
                'ip_address'  => $clientIp ?? '127.0.0.1',
            ]
        );
    }

    public function findValidResetToken(string $tokenHash): ?array
    {
        $rows = $this->db()->execute(
            "SELECT ut.id, ut.user_id, ut.token_hash, ut.expires_at, u.email, u.password_hash FROM user_tokens ut JOIN users u ON u.id = ut.user_id WHERE ut.token_hash = :token_hash AND ut.token_type = 'password_reset' AND ut.used_at IS NULL AND ut.expires_at > NOW() AND u.is_active = 1 AND u.is_deleted = 0 LIMIT 1",
            ['token_hash' => $tokenHash]
        );
        if (empty($rows)) {
            return null;
        }
        $row = $rows[0];
        $row['user_id'] = UuidV7::toHex($row['user_id']);
        $row['id'] = UuidV7::toHex($row['id']);
        return $row;
    }

    public function updatePassword(string $userId, string $newPasswordHash): void
    {
        $this->db()->write('UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = UNHEX(:id)', ['password_hash' => $newPasswordHash, 'id' => $userId]);
    }

    public function markResetTokenUsed(string $tokenId): void
    {
        $this->db()->write('UPDATE user_tokens SET used_at = NOW() WHERE id = UNHEX(:id)', ['id' => $tokenId]);
    }

    public function saveAuthKey(string $userId, string $authKey, string $expiresAt, ?string $clientIp = null): void
    {
        $tokenHash = hash('sha256', $authKey);
        $tokenId = UuidV7::generateBinary();
        $this->db()->write(
            'INSERT INTO user_tokens (id, user_id, token_type, token_hash, expires_at, ip_address, created_at) VALUES (:id, UNHEX(:user_id), :token_type, :token_hash, :expires_at, :ip_address, NOW())',
            [
                'id'          => $tokenId,
                'user_id'     => $userId,
                'token_type'  => 'api_key',
                'token_hash'  => $tokenHash,
                'expires_at'  => $expiresAt,
                'ip_address'  => $clientIp ?? '127.0.0.1',
            ]
        );
    }

    public function findValidAuthKey(string $authKey, bool $allowRecentlyUsed = false): ?array
    {
        $tokenHash = hash('sha256', $authKey);

        if ($allowRecentlyUsed) {
            // Grace window: used_at IS NULL VEYA (used_at < 30 saniye önce)
            $sql = "SELECT ut.id as token_id, ut.user_id, ut.used_at, u.username, u.email, u.display_name, u.gender, u.avatar_url, u.account_type FROM user_tokens ut JOIN users u ON u.id = ut.user_id WHERE ut.token_hash = :token_hash AND ut.token_type = 'api_key' AND (ut.used_at IS NULL OR ut.used_at > DATE_SUB(NOW(), INTERVAL 30 SECOND)) AND ut.expires_at > NOW() AND u.is_active = 1 AND u.is_deleted = 0 LIMIT 1";
        } else {
            $sql = "SELECT ut.id as token_id, ut.user_id, ut.used_at, u.username, u.email, u.display_name, u.gender, u.avatar_url, u.account_type FROM user_tokens ut JOIN users u ON u.id = ut.user_id WHERE ut.token_hash = :token_hash AND ut.token_type = 'api_key' AND ut.used_at IS NULL AND ut.expires_at > NOW() AND u.is_active = 1 AND u.is_deleted = 0 LIMIT 1";
        }

        $rows = $this->db()->execute($sql, ['token_hash' => $tokenHash]);
        if (empty($rows)) {
            return null;
        }
        $row = $rows[0];
        $row['user_id'] = UuidV7::toHex($row['user_id']);
        $row['token_id'] = UuidV7::toHex($row['token_id']);
        return $row;
    }

    public function markAuthKeyUsed(string $tokenId): void
    {
        $this->db()->write('UPDATE user_tokens SET used_at = NOW() WHERE id = UNHEX(:id)', ['id' => $tokenId]);
    }
}
