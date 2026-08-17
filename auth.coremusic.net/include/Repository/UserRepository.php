<?php declare(strict_types=1);

namespace CoreMusic\Auth\Repository;

use CoreMusic\Interfaces\Auth\IUserRepository;
use CoreMusic\Interfaces\Database\IDatabaseRegistry;

final class UserRepository implements IUserRepository
{
    private const DB_KEY = 'auth';
    private const USER_COLUMNS = 'id, username, email, password_hash, display_name, gender, avatar_url, account_type, is_active, is_banned, last_login_at, created_at, updated_at';
    private const ACTIVE_USER_WHERE = 'AND is_active = 1 AND deleted_at IS NULL';

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
        return !empty($rows) ? $rows[0] : null;
    }

    public function findByEmail(string $email): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE email = :email ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['email' => $email]
        );
        return !empty($rows) ? $rows[0] : null;
    }

    public function findByUsername(string $username): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE username = :username ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['username' => $username]
        );
        return !empty($rows) ? $rows[0] : null;
    }

    public function findById(int $userId): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE id = :id ' . self::ACTIVE_USER_WHERE . ' LIMIT 1',
            ['id' => $userId]
        );
        return !empty($rows) ? $rows[0] : null;
    }

    public function create(array $userData): array
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            $db->write(
                'INSERT INTO users (username, email, password_hash, display_name, gender, account_type, is_active, created_at, updated_at) VALUES (:username, :email, :password_hash, :display_name, :gender, :account_type, :is_active, NOW(), NOW())',
                [
                    'username'      => $userData['username'],
                    'email'         => $userData['email'],
                    'password_hash' => $userData['password_hash'],
                    'display_name'  => $userData['display_name'] ?? $userData['username'],
                    'gender'        => $userData['gender'] ?? 'neutral',
                    'account_type'  => $userData['account_type'] ?? 'free',
                    'is_active'     => 1,
                ]
            );

            $userId = (int)$db->lastInsertId();

            $db->write(
                'INSERT INTO user_assigned_roles (user_id, role_id, assigned_at) VALUES (:user_id, (SELECT id FROM user_roles WHERE role_name = :role_name LIMIT 1), NOW())',
                ['user_id' => $userId, 'role_name' => 'free_user']
            );

            $db->commit();
            return ['user_id' => $userId, 'role_name' => 'free_user'];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function updateLastLogin(int $userId): void
    {
        $this->db()->write('UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id', ['id' => $userId]);
    }

    public function emailExists(string $email): bool
    {
        $rows = $this->db()->execute('SELECT id FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1', ['email' => $email]);
        return !empty($rows);
    }

    public function usernameExists(string $username): bool
    {
        $rows = $this->db()->execute('SELECT id FROM users WHERE username = :username AND deleted_at IS NULL LIMIT 1', ['username' => $username]);
        return !empty($rows);
    }

    public function saveResetToken(int $userId, string $tokenHash, string $expiresAt, ?string $clientIp = null): void
    {
        $this->db()->write(
            'INSERT INTO user_tokens (user_id, token_type, token_hash, expires_at, ip_address, created_at) VALUES (:user_id, :token_type, :token_hash, :expires_at, :ip_address, NOW())',
            ['user_id' => $userId, 'token_type' => 'password_reset', 'token_hash' => $tokenHash, 'expires_at' => $expiresAt, 'ip_address' => $clientIp ?? '127.0.0.1']
        );
    }

    public function findValidResetToken(string $tokenHash): ?array
    {
        $rows = $this->db()->execute(
            'SELECT ut.id, ut.user_id, ut.token_hash, ut.expires_at, u.email, u.password_hash FROM user_tokens ut JOIN users u ON u.id = ut.user_id WHERE ut.token_hash = :token_hash AND ut.token_type = :token_type AND ut.used_at IS NULL AND ut.expires_at > NOW() AND u.is_active = 1 AND u.deleted_at IS NULL LIMIT 1',
            ['token_hash' => $tokenHash, 'token_type' => 'password_reset']
        );
        return !empty($rows) ? $rows[0] : null;
    }

    public function updatePassword(int $userId, string $newPasswordHash): void
    {
        $this->db()->write('UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id', ['password_hash' => $newPasswordHash, 'id' => $userId]);
    }

    public function markResetTokenUsed(int $tokenId): void
    {
        $this->db()->write('UPDATE user_tokens SET used_at = NOW() WHERE id = :id', ['id' => $tokenId]);
    }

    public function saveAuthKey(int $userId, string $authKey, string $expiresAt, ?string $clientIp = null): void
    {
        $tokenHash = hash('sha256', $authKey);
        $this->db()->write(
            'INSERT INTO user_tokens (user_id, token_type, token_hash, expires_at, ip_address, created_at) VALUES (:user_id, :token_type, :token_hash, :expires_at, :ip_address, NOW())',
            ['user_id' => $userId, 'token_type' => 'api_token', 'token_hash' => $tokenHash, 'expires_at' => $expiresAt, 'ip_address' => $clientIp ?? '127.0.0.1']
        );
    }

    public function findValidAuthKey(string $authKey): ?array
    {
        $tokenHash = hash('sha256', $authKey);
        $rows = $this->db()->execute(
            'SELECT ut.id as token_id, ut.user_id, u.username, u.email, u.display_name, u.gender, u.avatar_url, u.account_type FROM user_tokens ut JOIN users u ON u.id = ut.user_id WHERE ut.token_hash = :token_hash AND ut.token_type = \'api_token\' AND ut.used_at IS NULL AND ut.expires_at > NOW() AND u.is_active = 1 AND u.deleted_at IS NULL LIMIT 1',
            ['token_hash' => $tokenHash]
        );
        return !empty($rows) ? $rows[0] : null;
    }

    public function markAuthKeyUsed(int $tokenId): void
    {
        $this->db()->write('UPDATE user_tokens SET used_at = NOW() WHERE id = :id', ['id' => $tokenId]);
    }
}
