---
type: decision
id: "002"
title: "ADR-002: PDO Mandatory, ORM Yasak"
category: "database"
status: "frozen"
date: "2026-01-20"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [database, pdo, orm, sql, security, frozen]
risk-level: "critical"
owasp-top10: ["A03:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/l0-infrastructure]]"
---

# ADR-002: PDO Mandatory, ORM Yasak

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic veritabanı erişimi **PDO (PHP Data Objects)** ile yapılır. ORM kullanımı (Doctrine, Eloquent, Propel) **kesinlikle yasaktır**. Tüm SQL sorguları **prepared statement** ile çalıştırılır. `SELECT *` kullanımı yasaktır, **açık sütun listesi** zorunludur.

### 1.2 Temel Gerekçe

ORM'ler:
- SQL injection riskini artırır (query builder aracılığıyla)
- Performans overhead'i yaratır
- Debug zorluğu yaratır
- Migration karmaşıklığı yaratır
- ADR-001 (Vanilla JS) prensibiyle uyumlu değil

PDO:
- Doğrudan SQL kontrolü
- Prepared statement ile SQL injection koruması
- Düşük overhead
- Kolay debug
- PHP 8.4 native desteği

### 1.3 Beklenen Sonuçlar

- %100 PDO kullanımı
- %0 ORM kullanımı
- Prepared statement zorunlu
- Explicit column list zorunlu
- Düşük veritabanı overhead

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-01-20 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Data Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Problem Tanımı

ORM'lerin sakıncaları:
- **SQL Injection Risk:** Query builder'lar prepared statement kullanmayabilir
- **Performans Overhead:** ORM mapping maliyeti
- **Debug Zorluğu:** ORM-generated SQL'i debug etmek zor
- **Migration Karmaşıklığı:** ORM migration'ları bazen uyumsuz
- **Bağımlılık:** Büyük framework bağımlılığı

### 3.2 Yasaklı ORM Listesi

| ORM | Durum | Neden Yasak |
|-----|-------|-------------|
| Doctrine ORM | ❌ Yasak | ADR-002 |
| Laravel Eloquent | ❌ Yasak | ADR-002 |
| Propel | ❌ Yasak | ADR-002 |
| RedBeanPHP | ❌ Yasak | ADR-002 |
| CakePHP ORM | ❌ Yasak | ADR-002 |

### 3.3 İtici Güçler

| # | Güç | Kritiklik |
|---|-----|-----------|
| 1 | SQL injection koruması | Kritik |
| 2 | Performans | Yüksek |
| 3 | Debug kolaylığı | Yüksek |
| 4 | Minimal bağımlılık | Orta |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic'te veritabanı erişimi sadece PDO ile yapılır. ORM kullanımı kesinlikle yasaktır.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | PDO kullanımı | ✅ Zorunlu |
| 2 | ORM kullanımı | ❌ Yasak |
| 3 | Prepared statement | ✅ Zorunlu |
| 4 | `SELECT *` | ❌ Yasak |
| 5 | Explicit column list | ✅ Zorunlu |
| 6 | Error mode: EXCEPTION | ✅ Zorunlu |
| 7 | Emulate prepares: false | ✅ Zorunlu |
| 8 | Fetch mode: ASSOC | ✅ Zorunlu |

### 4.3 Kod Örnekleri

#### 4.3.1 Güvenli PDO Bağlantısı

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Infrastructure\Database;

/**
 * PDO Connection Factory
 *
 * ADR-002 uyumlu güvenli PDO bağlantısı.
 * Prepared statement zorunlu.
 * SELECT * yasak.
 */
final class PdoConnectionFactory
{
    /**
     * Güvenli PDO bağlantısı oluşturur.
     */
    public static function create(array $config): \PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        );

        $pdo = new \PDO($dsn, $config['username'], $config['password'], [
            // ADR-022: Exception modu zorunlu
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,

            // ADR-002:_ASSOC fetch modu
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,

            // ADR-002: Gerçek prepared statement
            \PDO::ATTR_EMULATE_PREPARES => false,

            // Performans optimizasyonu
            \PDO::MYSQL_ATTR_FOUND_ROWS => false,

            // Persistent connection kapalı
            \PDO::ATTR_PERSISTENT => false,
        ]);

        return $pdo;
    }
}
```

#### 4.3.2 Güvenli Repository Örneği

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Repository;

use CoreMusic\Auth\Domain\Entity\User;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Infrastructure\Database\PdoConnectionFactory;

/**
 * User Repository
 *
 * ADR-002 uyumlu veritabanı erişimi.
 * Prepared statement zorunlu.
 * SELECT * yasak.
 */
final class PdoUserRepository implements UserRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(array $dbConfig)
    {
        $this->pdo = PdoConnectionFactory::create($dbConfig);
    }

    /**
     * ID ile kullanıcı bulur.
     *
     * ✅ DOĞRU — Explicit column list
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, display_name, role, created_at, updated_at
             FROM users
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return User::fromArray($row);
    }

    /**
     * E-posta ile kullanıcı bulur.
     */
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, display_name, role, created_at, updated_at
             FROM users
             WHERE email = :email AND is_deleted = 0'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return User::fromArray($row);
    }

    /**
     * Kullanıcı kaydeder.
     */
    public function save(User $user): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, display_name, password_hash, role, created_at)
             VALUES (:email, :display_name, :password_hash, :role, NOW())'
        );

        return $stmt->execute([
            'email' => $user->getEmail(),
            'display_name' => $user->getDisplayName(),
            'password_hash' => $user->getPasswordHash(),
            'role' => $user->getRole(),
        ]);
    }

    /**
     * Kullanıcı günceller.
     */
    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET display_name = :display_name,
                 role = :role,
                 updated_at = NOW()
             WHERE id = :id AND is_deleted = 0'
        );

        return $stmt->execute([
            'display_name' => $user->getDisplayName(),
            'role' => $user->getRole(),
            'id' => $user->getId(),
        ]);
    }

    /**
     * Soft delete.
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET is_deleted = 1, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute(['id' => $id]);
    }
}
```

#### 4.3.3 Yanlış Kullanım Örnekleri

```php
// ❌ YANLIŞ — SELECT * yasak (ADR-002)
$stmt = $pdo->query('SELECT * FROM users WHERE id = 1');

// ✅ DOĞRU — Explicit column list
$stmt = $pdo->query('SELECT id, email, display_name FROM users WHERE id = 1');

// ❌ YANLIŞ — Prepared statement yok (ADR-002)
$stmt = $pdo->query("SELECT id, email FROM users WHERE email = '$email'");

// ✅ DOĞRU — Prepared statement
$stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);

// ❌ YANLIŞ — ORM kullanımı (ADR-002)
$user = $entityManager->find(User::class, $id);

// ✅ DOĞRU — PDO prepared statement
$user = $repository->findById($id);
```

### 4.4 Konfigürasyon

| Dosya | Değer |
|-------|-------|
| `shared/config/database.php` | PDO settings |
| `php.ini` | `pdo_mysql.default_charset=utf8mb4` |

---

## 5. Architecture

```
┌─────────────────────────────────────────────────┐
│              Database Access Layer                │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Repository Pattern (Interface)            │  │
│  │  • UserRepositoryInterface                 │  │
│  │  • SessionRepositoryInterface              │  │
│  │  • TokenRepositoryInterface                │  │
│  └───────────────────────┬────────────────────┘  │
│                          │                       │
│  ┌───────────────────────▼────────────────────┐  │
│  │  PDO Implementation                        │  │
│  │  • PdoUserRepository                       │  │
│  │  • PdoSessionRepository                    │  │
│  │  • PdoTokenRepository                      │  │
│  └───────────────────────┬────────────────────┘  │
│                          │                       │
│  ┌───────────────────────▼────────────────────┐  │
│  │  PDO Connection Factory                    │  │
│  │  • ERRMODE_EXCEPTION                       │  │
│  │  • FETCH_ASSOC                             │  │
│  │  • EMULATE_PREPARES = false                │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Doctrine ORM | ORM yasak (ADR-002) |
| Laravel Eloquent | ORM yasak (ADR-002) |
| Propel | ORM yasak (ADR-002) |
| mysql_* functions | Deprecated |
| mysqli_* | PDO tercih edilir |

### Karar Matrisi

| Kriter | Ağırlık | PDO (seçilen) | Doctrine | Eloquent | mysqli |
|--------|---------|---------------|----------|----------|--------|
| Güvenlik | %35 | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| Performans | %25 | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| Debug | %20 | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| Bağımlılık | %10 | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐ | ⭐⭐⭐⭐ |
| Kolaylık | %10 | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **TOPLAM** | %100 | **4.75** | **3.00** | **2.85** | **3.25** |

---

## 7. Consequences

### Olumlu
- SQL injection koruması
- Düşük overhead
- Kolay debug
- Minimal bağımlılık

### Olumsuz
- Manuel SQL yazma gereksinimi
- Schema değişikliklerinde manuel güncelleme

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Prepared statement | %100 |
| SELECT * engelleme | %100 |
| Error mode | %100 |
| Fetch mode | %100 |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-002: PDO Mandatory, ORM Yasak v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
