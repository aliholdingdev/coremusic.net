<?php declare(strict_types=1);

namespace CoreMusic\Contracts\Auth;

interface IUserRepository
{
    public function findByCredential(string $identity): ?array;
    public function findByEmail(string $email): ?array;
    public function findByUsername(string $username): ?array;
    public function findByIdHex(string $uuidHex): ?array;
    public function create(array $userData): array;
    public function updateLastLogin(string $userId): void;
    public function emailExists(string $email): bool;
    public function usernameExists(string $username): bool;
    public function saveResetToken(string $userId, string $tokenHash, string $expiresAt, ?string $clientIp = null): void;
    public function findValidResetToken(string $tokenHash): ?array;
    public function updatePassword(string $userId, string $newPasswordHash): void;
    public function markResetTokenUsed(string $tokenId): void;
    public function saveAuthKey(string $userId, string $authKey, string $expiresAt, ?string $clientIp = null): void;
    public function findValidAuthKey(string $authKey): ?array;
    public function markAuthKeyUsed(string $tokenId): void;

    /**
     * Kullanıcının kullanılmamış tüm auth_key'lerini (token_type=api_key) iptal eder — logout (B-F-20).
     */
    public function revokeAuthKeysForUser(string $userId): void;

    /**
     * Kullanıcının aktif RBAC rollerini (rol adı + izin listesi) döndürür.
     *
     * @return list<array{role_name: string, permissions: list<string>}>
     */
    public function findRolesForUser(string $userId): array;
}
