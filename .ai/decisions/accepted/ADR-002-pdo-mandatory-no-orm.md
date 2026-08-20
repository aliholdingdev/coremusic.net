---
title: "ADR-002: PDO Mandatory, ORM Yasak"
status: frozen
date: 2026-01-20
tags: [database, pdo, orm, sql, security, frozen]
---

# ADR-002: PDO Mandatory, ORM Yasak

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic veritabanÄ± eriÅŸimi **PDO (PHP Data Objects)** ile yapÄ±lÄ±r. ORM kullanÄ±mÄ± (Doctrine, Eloquent, Propel) **kesinlikle yasaktÄ±r**. TÃ¼m SQL sorgularÄ± **prepared statement** ile Ã§alÄ±ÅŸtÄ±rÄ±lÄ±r. `SELECT *` kullanÄ±mÄ± yasaktÄ±r, **aÃ§Ä±k sÃ¼tun listesi** zorunludur.

### 1.2 Temel GerekÃ§e

ORM'ler:
- SQL injection riskini artÄ±rÄ±r (query builder aracÄ±lÄ±ÄŸÄ±yla)
- Performans overhead'i yaratÄ±r
- Debug zorluÄŸu yaratÄ±r
- Migration karmaÅŸÄ±klÄ±ÄŸÄ± yaratÄ±r
- ADR-001 (Vanilla JS) prensibiyle uyumlu deÄŸil

PDO:
- DoÄŸrudan SQL kontrolÃ¼
- Prepared statement ile SQL injection korumasÄ±
- DÃ¼ÅŸÃ¼k overhead
- Kolay debug
- PHP 8.4 native desteÄŸi

### 1.3 Beklenen SonuÃ§lar

- %100 PDO kullanÄ±mÄ±
- %0 ORM kullanÄ±mÄ±
- Prepared statement zorunlu
- Explicit column list zorunlu
- DÃ¼ÅŸÃ¼k veritabanÄ± overhead

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-01-20 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Data Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

ORM'lerin sakÄ±ncalarÄ±:
- **SQL Injection Risk:** Query builder'lar prepared statement kullanmayabilir
- **Performans Overhead:** ORM mapping maliyeti
- **Debug ZorluÄŸu:** ORM-generated SQL'i debug etmek zor
- **Migration KarmaÅŸÄ±klÄ±ÄŸÄ±:** ORM migration'larÄ± bazen uyumsuz
- **BaÄŸÄ±mlÄ±lÄ±k:** BÃ¼yÃ¼k framework baÄŸÄ±mlÄ±lÄ±ÄŸÄ±

### 3.2 YasaklÄ± ORM Listesi

| ORM | Durum | Neden Yasak |
|-----|-------|-------------|
| Doctrine ORM | âŒ Yasak | ADR-002 |
| Laravel Eloquent | âŒ Yasak | ADR-002 |
| Propel | âŒ Yasak | ADR-002 |
| RedBeanPHP | âŒ Yasak | ADR-002 |
| CakePHP ORM | âŒ Yasak | ADR-002 |

### 3.3 Ä°tici GÃ¼Ã§ler

| # | GÃ¼Ã§ | Kritiklik |
|---|-----|-----------|
| 1 | SQL injection korumasÄ± | Kritik |
| 2 | Performans | YÃ¼ksek |
| 3 | Debug kolaylÄ±ÄŸÄ± | YÃ¼ksek |
| 4 | Minimal baÄŸÄ±mlÄ±lÄ±k | Orta |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic'te veritabanÄ± eriÅŸimi sadece PDO ile yapÄ±lÄ±r. ORM kullanÄ±mÄ± kesinlikle yasaktÄ±r.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | PDO kullanÄ±mÄ± | âœ… Zorunlu |
| 2 | ORM kullanÄ±mÄ± | âŒ Yasak |
| 3 | Prepared statement | âœ… Zorunlu |
| 4 | `SELECT *` | âŒ Yasak |
| 5 | Explicit column list | âœ… Zorunlu |
| 6 | Error mode: EXCEPTION | âœ… Zorunlu |
| 7 | Emulate prepares: false | âœ… Zorunlu |
| 8 | Fetch mode: ASSOC | âœ… Zorunlu |

### 4.3 Kod Ã–rnekleri

#### 4.3.1 GÃ¼venli PDO BaÄŸlantÄ±sÄ±

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Infrastructure\Database;

/**
 * PDO Connection Factory
 *
 * ADR-002 uyumlu gÃ¼venli PDO baÄŸlantÄ±sÄ±.
 * Prepared statement zorunlu.
 * SELECT * yasak.
 */
final class PdoConnectionFactory
{
    /**
     * GÃ¼venli PDO baÄŸlantÄ±sÄ± oluÅŸturur.
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

            // ADR-002: GerÃ§ek prepared statement
            \PDO::ATTR_EMULATE_PREPARES => false,

            // Performans optimizasyonu
            \PDO::MYSQL_ATTR_FOUND_ROWS => false,

            // Persistent connection kapalÄ±
            \PDO::ATTR_PERSISTENT => false,
        ]);

        return $pdo;
    }
}
```

#### 4.3.2 GÃ¼venli Repository Ã–rneÄŸi

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
 * ADR-002 uyumlu veritabanÄ± eriÅŸimi.
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
     * ID ile kullanÄ±cÄ± bulur.
     *
     * âœ… DOÄRU â€” Explicit column list
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
     * E-posta ile kullanÄ±cÄ± bulur.
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
     * KullanÄ±cÄ± kaydeder.
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
     * KullanÄ±cÄ± gÃ¼nceller.
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

#### 4.3.3 YanlÄ±ÅŸ KullanÄ±m Ã–rnekleri

```php
// âŒ YANLIÅ â€” SELECT * yasak (ADR-002)
$stmt = $pdo->query('SELECT * FROM users WHERE id = 1');

// âœ… DOÄRU â€” Explicit column list
$stmt = $pdo->query('SELECT id, email, display_name FROM users WHERE id = 1');

// âŒ YANLIÅ â€” Prepared statement yok (ADR-002)
$stmt = $pdo->query("SELECT id, email FROM users WHERE email = '$email'");

// âœ… DOÄRU â€” Prepared statement
$stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);

// âŒ YANLIÅ â€” ORM kullanÄ±mÄ± (ADR-002)
$user = $entityManager->find(User::class, $id);

// âœ… DOÄRU â€” PDO prepared statement
$user = $repository->findById($id);
```

### 4.4 KonfigÃ¼rasyon

| Dosya | DeÄŸer |
|-------|-------|
| `shared/config/database.php` | PDO settings |
| `php.ini` | `pdo_mysql.default_charset=utf8mb4` |

---

## 5. Architecture

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚              Database Access Layer                â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Repository Pattern (Interface)            â”‚  â”‚
â”‚  â”‚  â€¢ UserRepositoryInterface                 â”‚  â”‚
â”‚  â”‚  â€¢ SessionRepositoryInterface              â”‚  â”‚
â”‚  â”‚  â€¢ TokenRepositoryInterface                â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                          â”‚                       â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  PDO Implementation                        â”‚  â”‚
â”‚  â”‚  â€¢ PdoUserRepository                       â”‚  â”‚
â”‚  â”‚  â€¢ PdoSessionRepository                    â”‚  â”‚
â”‚  â”‚  â€¢ PdoTokenRepository                      â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                          â”‚                       â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  PDO Connection Factory                    â”‚  â”‚
â”‚  â”‚  â€¢ ERRMODE_EXCEPTION                       â”‚  â”‚
â”‚  â”‚  â€¢ FETCH_ASSOC                             â”‚  â”‚
â”‚  â”‚  â€¢ EMULATE_PREPARES = false                â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
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

| Kriter | AÄŸÄ±rlÄ±k | PDO (seÃ§ilen) | Doctrine | Eloquent | mysqli |
|--------|---------|---------------|----------|----------|--------|
| GÃ¼venlik | %35 | â­â­â­â­â­ | â­â­â­ | â­â­â­ | â­â­â­ |
| Performans | %25 | â­â­â­â­â­ | â­â­â­ | â­â­â­ | â­â­â­â­ |
| Debug | %20 | â­â­â­â­â­ | â­â­ | â­â­ | â­â­â­ |
| BaÄŸÄ±mlÄ±lÄ±k | %10 | â­â­â­â­â­ | â­â­ | â­ | â­â­â­â­ |
| KolaylÄ±k | %10 | â­â­â­â­ | â­â­â­â­ | â­â­â­â­â­ | â­â­â­ |
| **TOPLAM** | %100 | **4.75** | **3.00** | **2.85** | **3.25** |

---

## 7. Consequences

### Olumlu
- SQL injection korumasÄ±
- DÃ¼ÅŸÃ¼k overhead
- Kolay debug
- Minimal baÄŸÄ±mlÄ±lÄ±k

### Olumsuz
- Manuel SQL yazma gereksinimi
- Schema deÄŸiÅŸikliklerinde manuel gÃ¼ncelleme

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

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-002: PDO Mandatory, ORM Yasak v2.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*