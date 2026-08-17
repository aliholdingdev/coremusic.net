<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Auth;

interface ISessionManager
{
    public function setAuthUser(array $user): void;
    public function setRegisteredUser(array $created): void;
    public function getUserId(): ?int;
    public function isAuthenticated(): bool;
    public function destroy(): void;
    public function regenerateId(): void;
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function remove(string $key): void;
    public function setGender(string $gender): void;
    public function getGender(): string;
    public function setPendingRedirect(string $uri): void;
    public function consumePendingRedirect(): ?string;
    public function regenerateCspNonce(): string;
    public function getCspNonce(): string;
    public function isIdleExpired(int $timeoutSeconds): bool;
    public function touch(): void;
    public function rotateIfNeeded(int $intervalSeconds): bool;
    public function clearDisplayCookies(): void;
    public function getCookieParams(): array;
    public function all(): array;
}
