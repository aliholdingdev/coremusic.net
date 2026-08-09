---
type: architecture
category: auth
title: "Enterprise Auth — Application Layer (Use Cases)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Application Layer (Use Cases)

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Auth application katmanındaki use case'leri ve DTO'ları tanımlar. Application katmanı, Domain ile Infrastructure arasındaki köprüdür.

## 2. Use Case Tanımları

### 2.1 LoginUseCase

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application;

use CoreMusic\Auth\Domain\ValueObjects\Email;
use CoreMusic\Auth\Domain\ValueObjects\Password;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;
use CoreMusic\Auth\Application\DTO\LoginRequest;
use CoreMusic\Auth\Application\DTO\LoginResponse;

class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly SessionManagerInterface $sessionManager,
    ) {}

    public function execute(LoginRequest $request): LoginResponse
    {
        // 1. Find user by email
        $email = new Email($request->getEmail());
        $user = $this->userRepository->findByEmail($email);
        
        if ($user === null) {
            throw new \DomainException('Invalid credentials');
        }

        // 2. Check if user is active
        if (!$user->isActive()) {
            throw new \DomainException('Account is deactivated');
        }

        // 3. Verify password
        $password = Password::fromHash($user->getPassword()->getHash());
        if (!$password->verify($request->getPassword())) {
            throw new \DomainException('Invalid credentials');
        }

        // 4. Create session
        $session = $this->sessionManager->create($user);
        
        // 5. Return response
        return new LoginResponse(
            userId: $user->getId()->toString(),
            sessionId: $session->getId(),
            email: $user->getEmail()->toString(),
            roles: $user->getRoles(),
        );
    }
}
```

### 2.2 LogoutUseCase

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application;

use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;

class LogoutUseCase
{
    public function __construct(
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly SessionManagerInterface $sessionManager,
    ) {}

    public function execute(string $sessionId): void
    {
        $session = $this->sessionRepository->findById($sessionId);
        
        if ($session !== null) {
            $session->invalidate();
            $this->sessionRepository->save($session);
        }
        
        $this->sessionManager->destroy();
    }
}
```

### 2.3 RegisterUseCase

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application;

use CoreMusic\Auth\Domain\User;
use CoreMusic\Auth\Domain\ValueObjects\Email;
use CoreMusic\Auth\Domain\ValueObjects\Password;
use CoreMusic\Auth\Domain\ValueObjects\UserId;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Auth\Application\DTO\RegisterRequest;

class RegisterUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
    ) {}

    public function execute(RegisterRequest $request): void
    {
        // 1. Check if email already exists
        $email = new Email($request->getEmail());
        $existingUser = $this->userRepository->findByEmail($email);
        
        if ($existingUser !== null) {
            throw new \DomainException('Email already registered');
        }

        // 2. Create user
        $user = new User(
            id: UserId::generate(),
            email: $email,
            password: Password::fromPlain($request->getPassword()),
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
        );

        // 3. Save user
        $this->userRepository->save($user);
    }
}
```

### 2.4 ValidateSessionUseCase

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application;

use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Auth\Application\DTO\SessionDTO;

class ValidateSessionUseCase
{
    public function __construct(
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(string $sessionId): ?SessionDTO
    {
        $session = $this->sessionRepository->findById($sessionId);
        
        if ($session === null || !$session->isValid()) {
            return null;
        }
        
        if ($session->isExpired() || $session->isAbsoluteExpired()) {
            $session->invalidate();
            $this->sessionRepository->save($session);
            return null;
        }
        
        $user = $this->userRepository->findById($session->getUserId()->toString());
        
        if ($user === null || !$user->isActive()) {
            return null;
        }
        
        // Refresh session activity
        $session->refresh();
        $this->sessionRepository->save($session);
        
        return new SessionDTO(
            sessionId: $session->getId(),
            userId: $user->getId()->toString(),
            email: $user->getEmail()->toString(),
            roles: $user->getRoles(),
            isValid: true,
        );
    }
}
```

### 2.5 CheckPermissionUseCase

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application;

use CoreMusic\Auth\Domain\Repository\PermissionRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\RoleRepositoryInterface;

class CheckPermissionUseCase
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(array $userRoles, string $permission): bool
    {
        foreach ($userRoles as $roleName) {
            $role = $this->roleRepository->findByName($roleName);
            
            if ($role === null) {
                continue;
            }
            
            $permissions = $this->permissionRepository->findByRoleId($role->getId());
            
            foreach ($permissions as $perm) {
                if ($perm->getName() === $permission) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
```

## 3. DTO Tanımları

### 3.1 LoginRequest

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application\DTO;

class LoginRequest
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly ?string $redirectUrl = null,
    ) {}

    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRedirectUrl(): ?string { return $this->redirectUrl; }
}
```

### 3.2 LoginResponse

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application\DTO;

class LoginResponse
{
    public function __construct(
        private readonly string $userId,
        private readonly string $sessionId,
        private readonly string $email,
        private readonly array $roles,
    ) {}

    public function getUserId(): string { return $this->userId; }
    public function getSessionId(): string { return $this->sessionId; }
    public function getEmail(): string { return $this->email; }
    public function getRoles(): array { return $this->roles; }
}
```

### 3.3 SessionDTO

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Application\DTO;

class SessionDTO
{
    public function __construct(
        private readonly string $sessionId,
        private readonly string $userId,
        private readonly string $email,
        private readonly array $roles,
        private readonly bool $isValid,
    ) {}

    public function getSessionId(): string { return $this->sessionId; }
    public function getUserId(): string { return $this->userId; }
    public function getEmail(): string { return $this->email; }
    public function getRoles(): array { return $this->roles; }
    public function isValid(): bool { return $this->isValid; }
}
```

## 4. Application Rules

| Kural | Açıklama |
|-------|----------|
| Use Case bağımsız | Sadece Domain ve Interface'ler |
| DTO immutable | Değiştirilemez veri taşıyıcıları |
| Exception handling | Domain exception'ları fırlat |
| Single responsibility | Her use case tek bir iş yapar |

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Use Cases | 5 |
| DTOs | 3 |
| Clean Architecture | ✅ Application bağımsız |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
