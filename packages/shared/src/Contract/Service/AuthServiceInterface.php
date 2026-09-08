<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Service;

use CoreMusic\Shared\DTO\Auth\LoginRequest;
use CoreMusic\Shared\DTO\Auth\LoginResponse;
use CoreMusic\Shared\ValueObject\UserId;

interface AuthServiceInterface
{
    public function login(LoginRequest $request): LoginResponse;

    public function logout(UserId $userId): void;

    public function validateToken(string $token): ?UserId;

    public function refreshToken(string $refreshToken): LoginResponse;
}
