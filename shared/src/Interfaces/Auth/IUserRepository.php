<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Auth;

interface IUserRepository
{
    public function findByCredential(string $identity): ?array;
    public function findByEmail(string $email): ?array;
    public function findByUsername(string $username): ?array;
    public function findById(int $userId): ?array;
    public function create(array $userData): array;
    public function updateLastLogin(int $userId): void;
    public function emailExists(string $email): bool;
    public function usernameExists(string $username): bool;
    public function saveResetToken(int $userId, string $tokenHash, string $expiresAt, ?string $clientIp = null): void;
    public function findValidResetToken(string $tokenHash): ?array;
    public function updatePassword(int $userId, string $newPasswordHash): void;
    public function markResetTokenUsed(int $tokenId): void;
    public function saveAuthKey(int $userId, string $authKey, string $expiresAt, ?string $clientIp = null): void;
    public function findValidAuthKey(string $authKey): ?array;
    public function markAuthKeyUsed(int $tokenId): void;
}
