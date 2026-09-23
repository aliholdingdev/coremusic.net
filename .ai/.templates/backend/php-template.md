---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — PHP Backend Development Template"
type: backend-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** PHP 8.4, strict_types=1
**Katman:** K8 (Servis) / K9 (API & Routing)
**Sorumlu Agent:** Backend Architect

---

## 1. Hard Guardrails (Kesinlikle Yasak)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `declare(strict_types=1)` zorunlu | Kod geçersiz |
| 2 | ORM yasak (ADR-002) — Sadece PDO | SQL injection riski |
| 3 | `SELECT *` yasak — Açık sütun listesi | SQL injection riski |
| 4 | Prepared statement zorunlu | SQL injection riski |
| 5 | CSRF token = `csrf_token` (ADR-010) | CSRF bozulması |
| 6 | Middleware sırası değişmez (ADR-010/011/012/013/022) | CSP/CSRF bozulması |
| 7 | Hardcoded secret yasak — `.env` / credential vault | Veri sızıntısı |
| 8 | PSR-12 kodlama standartları | Kod tutarsızlığı |

---

## 2. Dosya Yapısı

```
src/
├── Controller/
│   ├── {{MODULE}}Controller.php
│   └── AbstractController.php
├── Service/
│   ├── {{MODULE}}Service.php
│   └── Interface/
│       └── {{MODULE}}ServiceInterface.php
├── Repository/
│   ├── {{MODULE}}Repository.php
│   └── Interface/
│       └── {{MODULE}}RepositoryInterface.php
├── Model/
│   ├── {{MODULE}}.php
│   └── DTO/
│       └── {{MODULE}}DTO.php
├── Middleware/
│   └── {{MIDDLEWARE}}Middleware.php
├── Validation/
│   └── {{MODULE}}Validator.php
└── Config/
    └── {{MODULE}}.php
```

---

## 3. Controller Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Controller;

use CoreMusic\Service\Interface\{{MODULE}}ServiceInterface;
use CoreMusic\Validation\{{MODULE}}Validator;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

final class {{MODULE}}Controller
{
    public function __construct(
        private readonly {{MODULE}}ServiceInterface $service,
        private readonly {{MODULE}}Validator $validator,
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Request validation
        // 2. Service call
        // 3. Response formatting
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // 1. Validation
        // 2. Service call
        // 3. Response
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        // 1. Validation
        // 2. Service call
        // 3. Response (201 Created)
    }

    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $request->getParsedBody();
        // 1. Validation
        // 2. Service call
        // 3. Response
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // 1. Service call
        // 2. Response (204 No Content)
    }
}
```

---

## 4. Service Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Service;

use CoreMusic\Repository\Interface\{{MODULE}}RepositoryInterface;
use CoreMusic\Service\Interface\{{MODULE}}ServiceInterface;

final class {{MODULE}}Service implements {{MODULE}}ServiceInterface
{
    public function __construct(
        private readonly {{MODULE}}RepositoryInterface $repository,
    ) {}

    public function findAll(array $filters = []): array
    {
        return $this->repository->findAll($filters);
    }

    public function findById(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): array
    {
        // 1. Business logic validation
        // 2. Data transformation
        // 3. Repository call
        // 4. Return created entity
    }

    public function update(int $id, array $data): array
    {
        // 1. Existence check
        // 2. Business logic validation
        // 3. Repository call
        // 4. Return updated entity
    }

    public function delete(int $id): bool
    {
        // 1. Existence check
        // 2. Business logic validation
        // 3. Repository call
        // 4. Return success
    }
}
```

---

## 5. Repository Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Repository;

use CoreMusic\Repository\Interface\{{MODULE}}RepositoryInterface;
use PDO;

final class {{MODULE}}Repository implements {{MODULE}}RepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function findAll(array $filters = []): array
    {
        $sql = 'SELECT id, {{COLUMN_1}}, {{COLUMN_2}}, created_at
                FROM {{TABLE}}
                WHERE is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, {{COLUMN_1}}, {{COLUMN_2}}, created_at
                FROM {{TABLE}}
                WHERE id = :id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function insert(array $data): int
    {
        $sql = 'INSERT INTO {{TABLE}} ({{COLUMN_1}}, {{COLUMN_2}}, created_at)
                VALUES (:{{COLUMN_1}}, :{{COLUMN_2}}, NOW())';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE {{TABLE}}
                SET {{COLUMN_1}} = :{{COLUMN_1}}, {{COLUMN_2}} = :{{COLUMN_2}}, updated_at = NOW()
                WHERE id = :id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($data, ['id' => $id]));
        return $stmt->rowCount() > 0;
    }

    public function softDelete(int $id): bool
    {
        $sql = 'UPDATE {{TABLE}} SET is_deleted = 1, deleted_at = NOW()
                WHERE id = :id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
```

---

## 6. Middleware Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

final class {{MIDDLEWARE}}Middleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // 1. Pre-processing
        // 2. Call next handler
        // 3. Post-processing
        return $handler->handle($request);
    }
}
```

---

## 7. Validation Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Validation;

use Respect\Validation\{Validator, Exception};

final class {{MODULE}}Validator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        try {
            Validator::stringType()->length(1, 255)->assert($data['{{COLUMN_1}}'] ?? '');
        } catch (Exception\NestedValidationException $e) {
            $errors['{{COLUMN_1}}'] = $e->getMessages();
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        return $this->validateCreate($data);
    }
}
```

---

## 8. BCNF Veritabanı Kuralları

| Kural | Açıklama |
|-------|----------|
| BCNF zorunlu | 18 veritabanı BCNF kurallarına uymalıdır |
| Soft delete | `is_deleted = 0` koşulu her sorguda olmalı |
| Snake_case | Tablo ve sütun isimleri snake_case |
| Timestamp | `created_at`, `updated_at`, `deleted_at` zorunlu |
| Prepared statement | PDO prepared statement zorunlu |
| No ORM | Doctrine DBAL veya raw PDO |

---

## 9. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-002 | PDO mandatory, ORM yasak |
| ADR-010 | csrf_token key zorunlu |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-022 | AES-256-GCM, Argon2id |
| ADR-040 | 18 BCNF veritabanı otoritesi |

---

## 10. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Ana sözleşme |
| [[brain.md]] | Mimari kararlar |
| [[WORKFLOW.md]] | Süreçler |
| [[architecture/k8-servis/README.md]] | Servis mimarisi |

---

*PHP Backend Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
