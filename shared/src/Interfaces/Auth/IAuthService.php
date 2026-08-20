<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Auth;

interface IAuthService
{
    public function login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array;
    public function register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array;
    public function logout(): void;
    public function isAuthenticated(): bool;
    public function getCurrentUser(): ?array;
    public function requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1'): array;
    public function resetPassword(string $token, string $newPassword): array;
    public function validateSessionKey(string $authKey): array;
}
