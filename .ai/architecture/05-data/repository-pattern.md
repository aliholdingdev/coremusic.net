---
type: architecture
category: data-repository
title: "CoreMusic — Repository Pattern"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Repository Pattern

**See also:** [[architecture/05-data/database_master]] · [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-040-database-authority]]

---

## 1. Amaç

Repository Pattern, veri erişim mantığını iş mantığından ayıran bir tasarım kalıbıdır. CoreMusic'te ORM yasak olduğundan (ADR-002), repository'ler raw PDO kullanır.

---

## 2. Repository Hiyerarşisi

```
Domain Layer (L4)
    ↓ Interface
Repository Interface
    ↓ Implementation
Infrastructure Layer (L0)
    ↓
PDO MySQL 9 BCNF
```

---

## 3. Interface Tanımı

```php
// Domain Layer - Interface Only
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;
    public function delete(int $id): void;
    public function findActive(int $limit, int $offset): array;
}

interface DeviceRepositoryInterface
{
    public function findById(int $id): ?Device;
    public function findByUserId(int $userId): array;
    public function save(Device $device): void;
    public function findOffline(): array;
}
```

---

## 4. Implementation (PDO)

```php
// Infrastructure Layer - PDO Implementation
class PDOUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, name, created_at FROM users WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, name, password_hash) VALUES (:email, :name, :hash)
             ON DUPLICATE KEY UPDATE name = :name'
        );
        $stmt->execute([
            'email' => $user->email(),
            'name' => $user->name(),
            'hash' => $user->passwordHash(),
        ]);
    }
}
```

---

## 5. Yasak Kuralları

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| Eloquent ORM | Raw PDO | [[ADR-002]] |
| Doctrine ORM | Raw PDO | [[ADR-002]] |
| `SELECT *` | Explicit columns | [[ADR-002]] |
| Dynamic query building | Prepared statements | [[ADR-002]] |
| Lazy loading | Eager loading | — |
| Magic methods | Explicit methods | — |

---

## 6. BCNF Uyumluluk

| Kural | Açıklama |
|-------|----------|
| Prepared Statement | Zorunlu |
| Explicit Column List | `SELECT *` yasak |
| Soft Delete | `is_deleted = 0` |
| Snake Case | `user_id`, `created_at` |
| No Foreign Key ORM | Manuel JOIN |
| Transaction | Gerekirse `beginTransaction()` |

---

## 7. Cihaz Repository Örnekleri

```php
interface DeviceRepositoryInterface
{
    public function findById(int $id): ?Device;
    public function findByType(string $type): array;
    public function findOffline(): array;
    public function save(Device $device): void;
    public function updateFirmware(int $deviceId, string $version): void;
}

interface FirmwareRepositoryInterface
{
    public function findLatest(string $deviceType): ?Firmware;
    public function save(Firmware $firmware): void;
    public function findByDeviceId(int $deviceId): array;
}
```

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-002-pdo-mandatory-no-orm]] | ORM yasak, PDO zorunlu |
| [[ADR-040-database-authority]] | 9 BCNF DB |
| [[ADR-041-database-normalization-supplementary]] | Normalizasyon |

---

## 9. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Repository | [[architecture/l4-domain]] | Interface tanımı |
| Repository | [[architecture/l0-infrastructure]] | Implementation |
| Repository | [[electronic/index]] | Electronics entities |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
