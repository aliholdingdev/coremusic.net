---
title: ARCHITECTURE PATTERNS — SOLID, CLEAN, HEXAGONAL
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# ARCHITECTURE PATTERNS
# Prompt Maker v7.2.0 | 2026-06-11

## SOLID Principles

**S - Single Responsibility:** One class, one reason to change. User class handles only user logic.

**O - Open/Closed:** Open for extension, closed for modification. Use interfaces, not inheritance.

**L - Liskov Substitution:** Derived classes must be substitutable for base classes without breaking.

**I - Interface Segregation:** Many role-specific interfaces better than one fat interface.

**D - Dependency Inversion:** Depend on abstractions (interfaces), not concrete implementations.

## Clean Architecture (4-Layer L0-L3)

```
L3 Presentation (UI, main.js, pages)
  ↓ only L2
L2 Router/Application (Router.js, PageRouter.php, services)
  ↓ only L1
L1 Security (Middleware, auth, CSRF, rate-limit)
  ↓ only L0
L0 Infrastructure (FetchWrapper, Cache, Logger, DB)
```

**Rules:** No upward references. L0 cannot import L2 or L3.

## Hexagonal Architecture

Central domain (business logic) surrounded by ports (interfaces) and adapters (implementations).

**Ports:** Driven (DB, cache), Driver (API, CLI)
**Adapters:** Concrete implementations (MySQL, Redis, REST, CLI)
**Benefit:** Domain logic independent of frameworks

## Design Patterns (Gang of Four + Modern)

**Creational:** Factory, Abstract Factory, Singleton (use cautiously), Builder
**Structural:** Adapter, Facade, Proxy, Decorator, Bridge
**Behavioral:** Observer, Strategy, Command, State, Chain of Responsibility, Repository

## Repository Pattern

```php
interface IUserRepository {
    public function find(int $id): ?User;
    public function save(User $user): void;
}

class UserRepository implements IUserRepository {
    // DB-specific implementation
}
```

**Benefit:** Data access abstraction, easy to test (mock repository)

## Service Layer

```php
class AuthService {
    public function __construct(
        private IUserRepository $userRepository,
        private IPasswordHasher $hasher
    ) {}
    
    public function login(string $email, string $password): ?User {
        $user = $this->userRepository->findByEmail($email);
        if ($user && $this->hasher->verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
```

**Benefit:** Reusable business logic, testable without controllers

## DTO (Data Transfer Object) Pattern

```php
final readonly class CreateUserDTO {
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}

class UserController {
    public function create(CreateUserDTO $dto): Response {
        // Type-safe, immutable data transfer
    }
}
```

## Dependency Injection

```php
// CORRECT
class UserService {
    public function __construct(
        private IUserRepository $repo,
        private IMailer $mailer
    ) {}
}

// WRONG
class UserService {
    private $repo;
    
    public function __construct() {
        $this->repo = new UserRepository(); // Tight coupling
    }
}
```

## Domain-Driven Design (DDD)

- **Ubiquitous Language:** Shared terminology between business and developers
- **Entities:** Objects with identity (User with id=1)
- **Value Objects:** Objects without identity (Money, Address)
- **Aggregates:** Clusters of entities/value objects with root entity (Order with items)
- **Repositories:** Aggregate persistence abstraction
- **Services:** Stateless operations across aggregates

## Event Sourcing

- Store events (not state): `UserCreated`, `UserEmailChanged`
- Rebuild state by replaying events
- Benefit: complete audit trail, temporal queries

## CQRS (Command Query Responsibility Segregation)

- **Commands:** state-changing operations (Write model)
- **Queries:** read operations (Read model)
- **Benefit:** Scalability (independent scaling of read/write), complex queries

## Microservices Architecture

- **Per-service DB:** No shared database
- **Service boundaries:** Domain-driven (auth service, music service, payment service)
- **Communication:** REST/gRPC/async (message queue)
- **Resilience:** Circuit breaker, timeout, retry logic

## Multi-Project Orchestration (Ecosystem)

- **Services:** CoreMusic PHP, Assets JS, Download Node, Engine C++, Player C++
- **Communication:** WebSocket (9742, 9743, 3001), shared memory (ring buffer)
- **Discovery:** NevaEngineStatus (Windows named objects)
- **Health:** /health endpoints, liveness probes
- **Graceful degradation:** fallback when service down

## Anti-Patterns to Avoid

- **God Class:** Single class doing everything
- **Tight Coupling:** Direct dependencies between classes
- **Singletons:** Global mutable state, testing nightmare
- **Circular Dependencies:** Module A → B → A
- **Service Locator:** Global registry instead of DI
- **Feature Envy:** One class accessing another's data
- **Premature Optimization:** Sacrificing clarity for speed

## Quality Metrics

- **Cyclomatic Complexity:** < 10 per method
- **Method Length:** < 20 lines (average)
- **Class Size:** < 200 lines
- **Coupling:** low (few dependencies)
- **Cohesion:** high (related methods/fields)
- **Code Duplication:** < 5% (DRY)

---

Quality Score (2026-06-11): 98.7%
